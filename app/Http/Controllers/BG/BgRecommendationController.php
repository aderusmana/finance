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
                        $bg = BankGaransi::where('customer_id', $row->customer_id)->latest()->first();
                        $num = $bg ? $bg->bg_number : '-';
                        return '<div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 36px; height: 36px;">
                                        <i class="ph-bold ph-file-text fs-5"></i>
                                    </div>
                                    <span class="fw-bold text-dark fs-6">'.$num.'</span>
                                </div>';
                    })
                    ->addColumn('customer_name', function($row) {
                        $name = $row->customer->name ?? '-';
                        $initial = substr($name, 0, 1);
                        return '<div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center me-3 fw-bold border border-warning border-opacity-25 shadow-sm" style="width: 36px; height: 36px; font-size: 15px;">'.$initial.'</div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark">'.$name.'</h6>
                                    </div>
                                </div>';
                    })
                    ->editColumn('current_bg', function($row) {
                        return '<div class="d-flex align-items-center">
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 shadow-sm" style="font-size: 0.9rem;">
                                        <i class="ph-bold ph-wallet me-1"></i> Rp ' . number_format($row->current_bg, 0, ',', '.') . '
                                    </span>
                                </div>';
                    })
                    ->addColumn('action', function ($row) {
                        return '<div class="action-btn-group"><button type="button" class="btn btn-secondary action-btn-hover btn-process" data-id="'.$row->id.'" data-tooltip="Process Recommendation"><i class="ph-bold ph-magic-wand"></i></button></div>';
                    })
                    ->rawColumns(['bg_number', 'customer_name', 'current_bg', 'action'])->make(true);
            }

            if ($request->has('type') && $request->type == 'history') {
                $query = BgRecommendation::with('customer')
                    ->where('bg_recommendations.status', '!=', 'pending');

                return DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('bg_number', function($row) {
                        $bg = BankGaransi::where('customer_id', $row->customer_id)->latest()->first();
                        $num = $bg ? $bg->bg_number : '-';
                        return '<div class="d-flex align-items-center">
                                    <div class="bg-secondary bg-opacity-10 text-secondary rounded-3 d-flex align-items-center justify-content-center me-2 shadow-sm" style="width: 32px; height: 32px;">
                                        <i class="ph-bold ph-file-text"></i>
                                    </div>
                                    <span class="fw-bold text-dark">'.$num.'</span>
                                </div>';
                    })
                    ->addColumn('customer_name', function($row) {
                        $name = $row->customer->name ?? '-';
                        $initial = substr($name, 0, 1);
                        return '<div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center me-2 fw-bold border border-info border-opacity-25 shadow-sm" style="width: 32px; height: 32px; font-size: 13px;">'.$initial.'</div>
                                    <span class="fw-bold text-dark">'.$name.'</span>
                                </div>';
                    })
                    ->editColumn('average', fn($row) => '<span class="text-muted fw-bold">Rp ' . number_format($row->average, 0, ',', '.') . '</span>')
                    ->editColumn('recommended_credit_limit', fn($row) => '<span class="text-dark fw-bold">Rp ' . number_format($row->recommended_credit_limit, 0, ',', '.') . '</span>')
                    ->editColumn('set_bg', fn($row) => '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1"><i class="ph-bold ph-check-circle me-1"></i> Rp ' . number_format($row->set_bg, 0, ',', '.') . '</span>')
                    ->editColumn('status', function($row){
                        $color = $row->status == 'completed' ? 'success' : 'primary';
                        $icon = $row->status == 'completed' ? 'ph-check-circle' : 'ph-spinner-gap';
                        return '<span class="badge bg-'.$color.' bg-opacity-10 text-'.$color.' border border-'.$color.' border-opacity-25 px-3 py-1 rounded-pill"><i class="ph-bold '.$icon.' me-1"></i>'.ucfirst(str_replace('_', ' ', $row->status)).'</span>';
                    })
                    ->addColumn('action', function($row){
                        return '<div class="action-btn-group"><button class="btn btn-secondary action-btn-hover btn-edit-rec" data-id="'.$row->id.'" data-tooltip="Edit Data"><i class="ph-bold ph-pencil"></i></button></div>';
                    })
                    ->rawColumns(['bg_number', 'customer_name', 'average', 'recommended_credit_limit', 'set_bg', 'status', 'action'])
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
                'message' => 'Period details have been successfully saved.',
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
                'inflation'                 => $inflation,
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

            $recForMail = BgRecommendation::with(['customer', 'periods', 'tax'])->findOrFail($id);
            activity()
                ->causedBy(auth()->user())
                ->performedOn($recForMail)
                ->useLog('bg_recommendation')
                ->event('submit_credit_limit')
                ->withProperties([
                    'customer' => $recForMail->customer->name,
                    'calculated_limit' => $rounded,
                    'final_credit_limit' => $limitUpdated,
                    'set_bg_nominal' => $setBg,
                    'inflation_rate' => $inflation . '%'
                ])
                ->log("Admin set new Credit Limit: Rp " . number_format($limitUpdated, 0, ',', '.') . " (BG: Rp " . number_format($setBg, 0, ',', '.') . ")");

            if ($recForMail->customer && $recForMail->customer->email) {
                Mail::to($recForMail->customer->email)
                    ->queue(new CustomerFillFormNotification($recForMail)); // Kirim objek yg lengkap
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Recommendation has been processed!']);

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
