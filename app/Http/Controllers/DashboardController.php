<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BG\BankGaransi;
use App\Models\Customer\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    /**
     * Helper: Parse Date Range (YYYY-MM-DD to YYYY-MM-DD)
     */
    private function parseDateRange($dateString)
    {
        // JIKA KOSONG -> DEFAULT KE TAHUN INI SAMPAI HARI INI (REALTIME)
        if (empty($dateString)) {
            return [
                Carbon::now()->startOfYear(), // 1 Jan Tahun ini
                Carbon::now()->endOfDay()     // Hari ini (Detik ini)
            ];
        }

        if (str_contains($dateString, ' to ')) {
            $parts = explode(' to ', $dateString);
            return [
                Carbon::parse($parts[0])->startOfDay(),
                Carbon::parse($parts[1])->endOfDay()
            ];
        }

        // Single Date
        return [
            Carbon::parse($dateString)->startOfDay(),
            Carbon::parse($dateString)->endOfDay()
        ];
    }

    /**
     * Helper: Get Available Years (untuk dropdown filter)
     */
    public function getAvailableYearsApi()
    {
        $years = BankGaransi::select(DB::raw('YEAR(created_at) as year'))
            ->whereNotNull('created_at')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($years)) $years = [now()->year];

        return response()->json($years);
    }

    /**
     * STAT 1: Main Chart Data (Monthly)
     */
    public function getMonthlyStats(Request $request)
    {
        $type = $request->input('type', 'bg');
        // Parse tanggal akan otomatis handle default jika frontend belum kirim tanggal
        [$startDate, $endDate] = $this->parseDateRange($request->input('date_range'));

        $query = BankGaransi::query()->whereBetween('created_at', [$startDate, $endDate]);

        $data = $query->select('status', DB::raw('MONTH(created_at) as month'))->get();

        $created = array_fill(0, 12, 0);
        $approved = array_fill(0, 12, 0);
        $pending  = array_fill(0, 12, 0);
        $rejected = array_fill(0, 12, 0);

        foreach ($data as $row) {
            $idx = $row->month - 1;
            $created[$idx]++;

            if (in_array($row->status, ['approved', 'completed', 'active'])) {
                $approved[$idx]++;
            } elseif (in_array($row->status, ['rejected', 'expired', 'returned'])) {
                $rejected[$idx]++;
            } else {
                $pending[$idx]++;
            }
        }

        return response()->json([
            'created' => $created, 'approved' => $approved,
            'pending' => $pending, 'rejected' => $rejected
        ]);
    }

    /**
     * STAT 2: Advanced Stats (Donut Chart, Largest BG, Longest Cust)
     */
    public function getAdvancedStats(Request $request)
    {
        [$startDate, $endDate] = $this->parseDateRange($request->input('date_range'));

        // 1. BG Composition
        $bgTypes = BankGaransi::select('bg_type', DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('bg_type')
            ->pluck('total', 'bg_type')->toArray();

        // 2. Largest Active BG (Snapshot saat ini dalam periode terpilih)
        $largestBg = BankGaransi::with('customer')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotIn('status', ['expired', 'rejected', 'draft'])
            ->orderBy('bg_nominal', 'desc')
            ->first();

        // 3. Customer Growth
        $totalNewCust = Customer::whereBetween('created_at', [$startDate, $endDate])->count();

        // 4. Longest Customer (Global fact)
        $oldestCustomer = Customer::orderBy('join_date', 'asc')->first()
                          ?? Customer::orderBy('created_at', 'asc')->first();

        return response()->json([
            'bg_composition' => [
                'new' => $bgTypes['new'] ?? 0,
                'extension' => $bgTypes['extension'] ?? 0,
                'existing' => $bgTypes['existing'] ?? 0,
            ],
            'largest_bg' => [
                'nominal' => $largestBg ? $largestBg->bg_nominal : 0,
                'customer' => $largestBg && $largestBg->customer ? $largestBg->customer->name : '-',
                'number' => $largestBg ? $largestBg->bg_number : '-'
            ],
            'longest_customer' => [
                'name' => $oldestCustomer ? $oldestCustomer->name : '-',
                'year' => $oldestCustomer ? date('Y', strtotime($oldestCustomer->join_date ?? $oldestCustomer->created_at)) : '-'
            ],
            'cust_growth' => $totalNewCust,
        ]);
    }

    /**
     * STAT 3: BG Metrics Card (Total Value & Expiring)
     */
    public function bgMetrics(Request $request)
    {
        [$startDate, $endDate] = $this->parseDateRange($request->input('date_range'));

        $query = BankGaransi::whereBetween('created_at', [$startDate, $endDate]);

        // Expiring selalu melihat masa depan (H+60 hari), tidak terpengaruh filter tanggal "created_at"
        $expiringCount = BankGaransi::whereBetween('exp_date', [now(), now()->addDays(60)])
                                    ->whereNotIn('status', ['expired', 'returned'])
                                    ->count();

        return response()->json([
            'total_value' => $query->sum('bg_nominal'),
            'expiring' => $expiringCount,
        ]);
    }

    /**
     * STAT 4: Customer Metrics Card (Total & Overlimit)
     */
    public function customerMetrics()
    {
        // Total Customer Global
        $total = Customer::count();

        // Credit Exceeded (Global Check)
        $creditExceeded = 0;
        // Optimization: Gunakan raw query atau chunk jika data customer ribuan
        $customers = Customer::whereNotNull('credit_limit')->where('credit_limit', '>', 0)->get(['id','credit_limit']);

        foreach ($customers as $c) {
            $sumBg = BankGaransi::where('customer_id', $c->id)
                                ->where('status', 'approved') // Hanya BG aktif
                                ->sum('bg_nominal');
            if ($sumBg > $c->credit_limit) {
                $creditExceeded++;
            }
        }

        return response()->json([
            'total' => $total,
            'credit_exceeded' => $creditExceeded,
        ]);
    }

    /**
     * LIST 1: Top Customers (By Count OR Value)
     */
    public function topCustomersByBg(Request $request)
    {
        $metric = $request->input('metric', 'bg_count'); // 'bg_count' or 'value'
        $user = Auth::user();

        $query = Customer::leftJoin('bank_garansi', 'customers.id', '=', 'bank_garansi.customer_id');

        if ($metric === 'value') {
            $query->select('customers.id', 'customers.name', DB::raw('SUM(bank_garansi.bg_nominal) as bg_value'))
                  ->orderByDesc('bg_value');
        } else {
            $query->select('customers.id', 'customers.name', DB::raw('COUNT(bank_garansi.id) as bg_count'))
                  ->orderByDesc('bg_count');
        }

        $query->groupBy('customers.id', 'customers.name');

        if (!$user->hasRole('super-admin')) {
            $query->where('bank_garansi.created_by', $user->id);
        }

        $list = $query->limit(5)->get();

        return response()->json($list);
    }

    /**
     * LIST 2: Recent Activities (Updated from BG table)
     */
    public function getRecentActivities()
    {
        $user = Auth::user();
        $query = BankGaransi::with('customer')->orderBy('updated_at', 'desc');

        if (!$user->hasRole('super-admin')) {
            $query->where('created_by', $user->id);
        }

        $recent = $query->limit(5)->get()->map(function ($bg) {
            return [
                'srs_number' => $bg->bg_number,
                'requester_name' => optional($bg->customer)->name ?? 'N/A',
                'category' => 'BG',
                'status' => $bg->status,
                'timestamp' => $bg->updated_at->diffForHumans(),
            ];
        });

        return response()->json($recent);
    }

    /**
     * LIST 3: My Actions (Notifications)
     */
    public function getMyActions()
    {
        $user = Auth::user();
        $notifications = $user->unreadNotifications()->limit(5)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'message' => $n->data['message'] ?? 'Notification',
                'url' => $n->data['url'] ?? '#',
                'timestamp' => $n->created_at->diffForHumans(),
                'causer_name' => $n->data['causer_name'] ?? 'System',
            ];
        });

        return response()->json([
            'count' => $user->unreadNotifications->count(),
            'notifications' => $notifications
        ]);
    }
}
