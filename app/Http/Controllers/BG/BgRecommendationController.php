<?php

namespace App\Http\Controllers\BG;

use App\Http\Controllers\Controller;
use App\Models\BG\BgPeriod;
use App\Models\BG\BgRecommendation;
use App\Models\BG\BgSubmission;
use App\Models\BG\BankGaransi;
use App\Models\Customer\Customer;
use App\Models\BG\Tax;
use App\Models\BG\BgLimitRule;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
                    ->addColumn('bg_number', function($row) {
                        $bg = BankGaransi::where('customer_id', $row->customer_id)
                                ->latest()
                                ->first();

                        return $bg ? $bg->bg_number : '-';
                    })
                    ->addColumn('customer_name', fn($row) => $row->customer->name ?? '-')
                    ->editColumn('current_bg', fn($row) => 'Rp ' . number_format($row->current_bg, 0, ',', '.'))
                    ->addColumn('action', function ($row) {
                        return '<button type="button" class="btn btn-sm btn-warning btn-process text-dark"
                            data-id="'.$row->id.'" title="Process"><i class="ph-bold ph-note-pencil"></i></button>';
                    })
                    ->rawColumns(['action'])->make(true);
            }

            if ($request->has('type') && $request->type == 'history') {
                $query = BgRecommendation::with('customer')
                    ->where('bg_recommendations.status', '!=', 'pending');

                return DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('bg_number', function($row) {
                        $bg = BankGaransi::where('customer_id', $row->customer_id)
                                ->latest()
                                ->first();

                        return $bg ? $bg->bg_number : '-';
                    })
                    ->addColumn('customer_name', fn($row) => $row->customer->name ?? '-')
                    ->editColumn('average', fn($row) => 'Rp ' . number_format($row->average, 0, ',', '.'))
                    ->editColumn('recommended_credit_limit', fn($row) => 'Rp ' . number_format($row->recommended_credit_limit, 0, ',', '.'))
                    ->editColumn('set_bg', fn($row) => 'Rp ' . number_format($row->set_bg, 0, ',', '.'))
                    ->editColumn('status', function($row){
                        $color = $row->status == 'completed' ? 'success' : 'primary';
                        return '<span class="badge bg-'.$color.' status-badge-lg">'.ucfirst(str_replace('_', ' ', $row->status)).'</span>';
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
        $rec = BgRecommendation::with(['customer', 'tax', 'periods'])->findOrFail($id);

        if ($rec->top == 0 && $rec->customer) {
            $rec->top = $rec->customer->term_of_payment;
        }
        if ($rec->lead_time == 0 && $rec->customer) {
            $rec->lead_time = $rec->customer->lead_time;
        }

        $data = $rec->toArray();
        $data['calculated_rule_percent'] = $this->getLimitRulePercent($rec->customer);
        $data['tax_value'] = $rec->tax ? $rec->tax->value : 0.11;
        $data['raw_current_bg'] = $rec->current_bg;

        return response()->json($data);
    }

    public function savePeriods(Request $request, $id)
    {
        $request->validate([
            'periods' => 'required|array',
            'periods.*.date' => 'required|date',
            'periods.*.amount' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            // Hapus periode lama untuk ID rekomendasi ini
            BgPeriod::where('bg_recommendation_id', $id)->delete();

            $totalAmount = 0;
            $periodsData = [];

            // Siapkan data untuk bulk insert (lebih efisien)
            foreach ($request->periods as $period) {
                $periodsData[] = [
                    'bg_recommendation_id' => $id,
                    'period_date'          => $period['date'], // Format Y-m-d
                    'amount'               => $period['amount'],
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ];
                $totalAmount += $period['amount'];
            }

            BgPeriod::insert($periodsData);

            // Update average sementara di tabel parent agar sinkron
            $rec = BgRecommendation::findOrFail($id);
            $rec->fill(['average' => $totalAmount])->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rincian periode berhasil disimpan.',
                'total_average' => $totalAmount // Kembalikan nilai total untuk JS
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'average' => 'required|numeric',
            'set_bg'  => 'required|numeric',
            'credit_limit_updated' => 'nullable|numeric',
        ]);

        DB::beginTransaction();
        try {
            $rec = BgRecommendation::with(['customer', 'tax'])->findOrFail($id);

            $top        = (float)$rec->top;
            if($top == 0 && $rec->customer) $top = $rec->customer->term_of_payment;

            $leadTime   = (float)$rec->lead_time;
            if($leadTime == 0 && $rec->customer) $leadTime = $rec->customer->lead_time;

            $inflation  = 130;
            $avg        = (float)$request->average;
            $setBg      = (float)$request->set_bg;
            $taxRate    = $rec->tax ? ($rec->tax->value / 100) : 0.11;
            if ($rec->tax && $rec->tax->value >= 1) {
                 $taxRate = $rec->tax->value / 100;
            } elseif ($rec->tax) {
                 $taxRate = $rec->tax->value;
            }
            $rulePercent = $this->getLimitRulePercent($rec->customer);

            // Perhitungan Ulang di Backend (Validasi)
            $estPpnValue = $avg * $taxRate;
            $timeFactor = $top > 0 ? ($top + $leadTime) / $top : 1;
            $inflationFactor = $inflation / 100;
            $recLimit = $estPpnValue * $timeFactor * $inflationFactor;
            $fkLimit = $recLimit * ($rulePercent / 100);
            $rounded = round($fkLimit, -6);

            if ($request->filled('credit_limit_updated')) {
                $limitUpdated = (float) $request->credit_limit_updated;
            } else {
                if ($rulePercent > 0) {
                     $limitUpdated = $setBg / ($rulePercent / 100);
                } else {
                     $limitUpdated = $setBg;
                }
            }

            $notes = $request->notes;
            if (empty($notes)) {
                $notes = "Auto-generated on: " . Carbon::now()->format('d M Y');
            }

            $token = Str::random(64);

            $rec->update([
                'average'                   => $avg,
                'top'                       => $top,
                'lead_time'                 => $leadTime,
                'recommended_credit_limit'  => $recLimit,
                'fk_with_limit'             => $fkLimit,
                'rounded_credit_limit'      => $rounded,
                'set_bg'                    => $setBg,
                'credit_limit_updated'      => $limitUpdated,
                'status'                    => 'process',
                'notes'                     => $notes,
                'token'                     => $token,
            ]);

            BgSubmission::firstOrCreate(
                ['bg_recommendation_id' => $rec->id],
                [
                    'form_code' => 'SUB-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
                    'status'    => 'pending_print',
                    'token'     => Str::random(60),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $recForMail = BgRecommendation::with(['customer', 'periods', 'tax'])->findOrFail($id);
            if ($recForMail->customer && $recForMail->customer->email) {
                Mail::to($recForMail->customer->email)
                    ->queue(new CustomerFillFormNotification($recForMail)); // Kirim objek yg lengkap
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Rekomendasi diproses!']);

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
