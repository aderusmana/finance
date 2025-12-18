<?php

namespace App\Http\Controllers\BG;

use App\Http\Controllers\Controller;
use App\Models\BG\BgRecommendation;
use App\Models\Customer\Customer;
use App\Models\BG\Tax;
use App\Models\BG\BgLimitRule;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomerFillFormNotification;
use Carbon\Carbon;

class BgRecommendationController extends Controller
{
    private function getLimitRulePercent($customer) {
        if (!$customer || !$customer->join_date) {
            return 0;
        }

        $joinDate = Carbon::parse($customer->join_date);
        $years    = (int) abs($joinDate->diffInYears(Carbon::now())); 

        $rule = BgLimitRule::where('min_year', '<=', $years)
                        ->where('max_year', '>=', $years)
                        ->orderBy('min_year', 'desc')
                        ->first();

        // dd([
        //     'customer' => $customer->name,
        //     'join_date' => $customer->join_date,
        //     'years_calculated' => $years,
        //     'rule_found' => $rule
        // ]);

        return $rule ? (float)$rule->percentage : 0;
    }

    public function index(Request $request)
    {
        $taxConfig = Tax::first(); 
        if(!$taxConfig) {
            $taxConfig = (object)['id' => null, 'value' => 0.11]; 
        }

        if ($request->ajax()) {
            if ($request->has('type') && $request->type == 'expiring') {
                
                $query = BgRecommendation::with(['customer'])
                    ->where('bg_recommendations.status', '=', 'pending'); 

                return DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('customer_name', fn($row) => $row->customer->name ?? '-')
                    ->editColumn('current_bg', fn($row) => 'Rp ' . number_format($row->current_bg, 0, ',', '.'))
                    ->addColumn('action', function ($row) {
                        return '<button type="button" class="btn btn-sm btn-success btn-process text-white"
                            data-id="'.$row->id.'" title="Process"><i class="ph-bold ph-play"></i> Process</button>';
                    })
                    ->rawColumns(['action'])->make(true);
            }

            if ($request->has('type') && $request->type == 'history') {
                
                $query = BgRecommendation::with('customer')
                    ->where('bg_recommendations.status', '!=', 'pending'); 

                return DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('customer_name', fn($row) => $row->customer->name ?? '-')
                    ->editColumn('average', fn($row) => 'Rp ' . number_format($row->average, 0, ',', '.'))
                    ->editColumn('recommended_credit_limit', fn($row) => 'Rp ' . number_format($row->recommended_credit_limit, 0, ',', '.'))
                    ->editColumn('set_bg', fn($row) => 'Rp ' . number_format($row->set_bg, 0, ',', '.'))
                    
                    ->editColumn('status', function($row){
                        $color = $row->status == 'completed' ? 'success' : 'primary';
                        return '<span class="badge bg-'.$color.'">'.ucfirst(str_replace('_', ' ', $row->status)).'</span>';
                    })
                    
                    ->addColumn('action', function($row){
                        return '<button class="btn btn-sm btn-warning btn-edit-rec text-white" data-id="'.$row->id.'"><i class="ph-bold ph-pencil-simple"></i></button>';
                    })
                    
                    ->rawColumns(['status', 'action']) 
                    ->make(true);
            }
        }

        $customers = Customer::orderBy('name')->get();
        return view('page.bg.bg_recommendations.index', compact('taxConfig', 'customers'));
    }

    public function show($id) {
        $rec = BgRecommendation::with(['customer', 'tax'])->findOrFail($id);
        
        if ($rec->top == 0 && $rec->customer) {
            $rec->top = $rec->customer->term_of_payment;
        }
        if ($rec->lead_time == 0 && $rec->customer) {
            $rec->lead_time = $rec->customer->lead_time;
        }

        $data = $rec->toArray();
        $data['calculated_rule_percent'] = $this->getLimitRulePercent($rec->customer);
        $data['tax_value'] = $rec->tax ? $rec->tax->value : 0.11;
        
        return response()->json($data);
    }

    // Fungsi Update untuk Kalkulasi Final & Simpan
    public function update(Request $request, $id)
    {
        $request->validate([
            'average' => 'required|numeric',
            'set_bg'  => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            $rec = BgRecommendation::findOrFail($id);
            
            $top        = (float)$rec->top;
            if($top == 0 && $rec->customer) $top = $rec->customer->term_of_payment;

            $leadTime   = (float)$rec->lead_time;
            if($leadTime == 0 && $rec->customer) $leadTime = $rec->customer->lead_time;

            $inflation  = 130; // Data Paten 130%
            $avg        = (float)$request->average;
            $setBg      = (float)$request->set_bg;
            $taxRate    = $rec->tax ? $rec->tax->value : 0.11; // 0.11
            $rulePercent = $this->getLimitRulePercent($rec->customer);

            // Rumus: Avg * (PPN/100) -> Avg * 0.11
            $estPpnValue = $avg * $taxRate;
            
            // Rumus: "(top+lead time)/top x 130%" 
            $timeFactor = $top > 0 ? ($top + $leadTime) / $top : 1;
            $inflationFactor = $inflation / 100; // 1.3
            
            // Total = (Avg * 11%) * ((TOP+Lead)/TOP) * 1.3
            $recLimit = $estPpnValue * $timeFactor * $inflationFactor;

            // Rumus: RecLimit * (Rule/100)
            $fkLimit = $recLimit * ($rulePercent / 100);

            // D. Rounded (Jutaan)
            $rounded = round($fkLimit, -6);

            // Rumus: Set BG / (Rule/100)
            if ($rulePercent > 0) {
                 $limitUpdated = $setBg / ($rulePercent / 100);
            } else {
                 $limitUpdated = $setBg;
            }

            // Update DB
            $rec->update([
                'average'                   => $avg,
                'top'                       => $top,      // Update jika sebelumnya 0
                'lead_time'                 => $leadTime, // Update jika sebelumnya 0
                'recommended_credit_limit'  => $recLimit,
                'fk_with_limit'             => $fkLimit,
                'rounded_credit_limit'      => $rounded,
                'set_bg'                    => $setBg,
                'credit_limit_updated'      => $limitUpdated,
                'status'                    => 'process',
                'notes'                     => $request->notes
            ]);

            if ($rec->customer && $rec->customer->email) {
                Mail::to($rec->customer->email)->send(new CustomerFillFormNotification($rec));
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Rekomendasi berhasil diproses!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        BgRecommendation::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Data deleted']);
    }
}   