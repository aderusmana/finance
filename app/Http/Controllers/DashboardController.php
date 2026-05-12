<?php

namespace App\Http\Controllers;

use App\Models\Customer\LogisticOrder;
use App\Models\Customer\DeliveryOrderNote;
use App\Models\Customer\DistributorCustomer;
use App\Models\Master\LogisticFeeLog;
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
        $user = Auth::user();

        // if ($user->hasRole('super-admin','manager-finance', 'head-finance')) {
        //     return view('dashboard');
        // }

        // if ($user->hasRole(['staff-finance']) || $user->can('view bg dashboard')) {
        //     return view('dashboard.bg');
        // }

        // if ($user->hasRole(['staff-sales', 'head-SNM', 'atasan']) || $user->can('view customer dashboard')) {
        //     return view('dashboard.customer');
        // }
        // if ($user->hasRole(['staff-sales', 'head-SNM', 'atasan']) || $user->can('view customer dashboard')) {
        //     return view('dashboard.customer');
        // }

        return view('dashboard');
    }

    public function customerIndex()
    {
        $user = Auth::user();
        
        // if (!$user->hasRole(['super-admin', 'staff-sales', 'head-SNM', 'atasan'])) {
        //     abort(403, 'Anda tidak memiliki akses ke Dashboard Customer');
        // }

        return view('dashboard.customer');
    }

    public function bgIndex()
    {
        $user = Auth::user();
        
        // if (!$user->hasRole(['super-admin', 'staff-finance', 'manager-finance', 'head-finance'])) {
        //     abort(403, 'Anda tidak memiliki akses ke Dashboard Bank Garansi');
        // }

        return view('dashboard.bg');
    }

    /**
     * Helper: Parse Date Range (YYYY-MM-DD to YYYY-MM-DD)
     */
    private function parseDateRange($dateString)
    {
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
        // Parse tanggal
        [$startDate, $endDate] = $this->parseDateRange($request->input('date_range'));

        // --- PERBAIKAN DI SINI: Switch Query Berdasarkan Tipe ---
        if ($type === 'customer') {
            $query = Customer::query()->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            $query = BankGaransi::query()->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Ambil data status dan bulan
        // Pastikan model Customer memiliki kolom 'status' (active/pending/dll).
        // Jika tidak ada, kode ini tetap menghitung 'Created' dengan benar,
        // tapi Approved/Pending mungkin 0 jika nama statusnya beda.
        $data = $query->select('status', DB::raw('MONTH(created_at) as month'))->get();

        $created = array_fill(0, 12, 0);
        $approved = array_fill(0, 12, 0);
        $pending  = array_fill(0, 12, 0);
        $rejected = array_fill(0, 12, 0);

        foreach ($data as $row) {
            $idx = $row->month - 1;

            // 1. Hitung Created (Semua data yang masuk query = Created)
            $created[$idx]++;

            // 2. Mapping Status (Sesuaikan dengan value di database Anda)
            // Lowercase agar case-insensitive
            $st = strtolower($row->status ?? '');

            // Logic untuk Bank Garansi & Customer
            if (in_array($st, ['approved', 'completed', 'active', 'verified'])) {
                $approved[$idx]++;
            } elseif (in_array($st, ['rejected', 'expired', 'returned', 'inactive'])) {
                $rejected[$idx]++;
            } elseif (in_array($st, ['draft', 'pending', 'process', 'new', 'waiting_approval'])) {
                $pending[$idx]++;
            } else {
                // Jika status kosong atau null, anggap sebagai Pending atau biarkan hanya masuk Created
                if($type === 'customer' && $st == '') {
                    // Opsional: Jika customer tidak punya kolom status, bisa dianggap Approved otomatis
                    // $approved[$idx]++;
                } else {
                    $pending[$idx]++;
                }
            }
        }

        return response()->json([
            'created' => $created,
            'approved' => $approved,
            'pending' => $pending,
            'rejected' => $rejected
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

        $totalValue = (clone $query)->whereIn('status', ['approved', 'active'])->sum('bg_nominal');
        $activeCount = (clone $query)->whereIn('status', ['approved', 'active'])->count();
        $expiringCount = BankGaransi::whereBetween('exp_date', [now(), now()->addDays(60)])
                                    ->whereNotIn('status', ['expired', 'returned', 'rejected'])
                                    ->count();

        $largestBg = (clone $query)->whereIn('status', ['approved', 'active'])
                                   ->orderBy('bg_nominal', 'desc')
                                   ->first();

        return response()->json([
            'total_value' => $totalValue,
            'active_count' => $activeCount,
            'expiring' => $expiringCount,
            'largest_bg_nominal' => $largestBg ? $largestBg->bg_nominal : 0,
            'largest_bg_customer' => $largestBg && $largestBg->customer ? $largestBg->customer->name : '-',
        ]);
    }

    /**
     * STAT 4: Customer Metrics Card (Total & Overlimit)
     */
    public function customerMetrics(Request $request)
    {
        $total = Customer::count();

        $withBg = BankGaransi::whereIn('status', ['approved', 'active', 'completed'])
                    ->distinct('customer_id')
                    ->count('customer_id');

        $withoutBg = $total - $withBg;

        $creditExceeded = 0;
        $customers = Customer::whereNotNull('credit_limit')->where('credit_limit', '>', 0)->get(['id','credit_limit']);
        foreach ($customers as $c) {
            $sumBg = BankGaransi::where('customer_id', $c->id)
                                ->whereIn('status', ['approved', 'active'])
                                ->sum('bg_nominal');
            if ($sumBg > $c->credit_limit) {
                $creditExceeded++;
            }
        }

        $highestLimit = Customer::orderBy('credit_limit', 'desc')->first(['name', 'credit_limit']);

        $longestJoined = Customer::orderBy('join_date', 'asc')->first() ?? Customer::orderBy('created_at', 'asc')->first();

        return response()->json([
            'total' => $total,
            'with_bg' => $withBg,
            'without_bg' => $withoutBg,
            'credit_exceeded' => $creditExceeded,
            'highest_limit_name' => $highestLimit ? $highestLimit->name : '-',
            'highest_limit_amount' => $highestLimit ? $highestLimit->credit_limit : 0,
            'longest_joined_name' => $longestJoined ? $longestJoined->name : '-',
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

    public function logisticIndex()
    {
        $user = Auth::user();
        // Beri proteksi (opsional, sesuaikan dengan role kamu)
        if (!$user->hasRole(['super-admin', 'staff-sales', 'head-SNM', 'atasan'])) {
            // abort(403, 'Anda tidak memiliki akses ke Dashboard Logistic');
        }

        return view('dashboard.logistic');
    }

    /**
     * API Data untuk Dashboard Logistic (Statistik, Chart, dan Tabel)
     */
    public function getLogisticStats(Request $request)
    {
        // 1. TOP CARDS (Summary)
        $totalOrders = LogisticOrder::count();
        $pendingDownloads = DeliveryOrderNote::where('status', 'Pending Download')->count();
        
        $activeFees = DistributorCustomer::where('status', 'Approved')->count();
        $pendingFees = DistributorCustomer::where('status', 'Pending')->count();

        // 2. CHART DATA (Logistic Orders 6 Bulan Terakhir)
        $chartLabels = [];
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $chartLabels[] = $month->translatedFormat('M Y');
            $chartData[] = LogisticOrder::whereMonth('created_at', $month->month)
                                        ->whereYear('created_at', $month->year)
                                        ->count();
        }

        // 3. RECENT LOGISTIC ORDERS
        $recentOrders = LogisticOrder::with(['distributor', 'customer', 'note'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($order) {
                return [
                    'lo_no' => 'LO-' . str_pad($order->id, 4, '0', STR_PAD_LEFT),
                    'do_no' => $order->note->delivery_order_no ?? '-',
                    'customer' => $order->customer->name ?? 'N/A',
                    'distributor' => $order->distributor->name ?? 'N/A',
                    'status' => $order->note->status ?? 'Pending',
                    'date' => $order->created_at->format('d M Y')
                ];
            });

        // 4. RECENT FEE LOGS
        $recentFeeLogs = LogisticFeeLog::with(['distributorCustomer.customer', 'distributorCustomer.distributor', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($log) {
                return [
                    'customer' => $log->distributorCustomer->customer->name ?? 'N/A',
                    'distributor' => $log->distributorCustomer->distributor->name ?? 'N/A',
                    'new_fee' => 'Rp ' . number_format($log->new_fee, 0, ',', '.'),
                    'status' => $log->status,
                    'action_by' => $log->user->name ?? $log->action_by ?? 'System',
                    'time' => $log->created_at->diffForHumans()
                ];
            });

        return response()->json([
            'summary' => [
                'total_orders' => $totalOrders,
                'pending_downloads' => $pendingDownloads,
                'active_fees' => $activeFees,
                'pending_fees' => $pendingFees,
            ],
            'chart' => [
                'labels' => $chartLabels,
                'data' => $chartData
            ],
            'recent_orders' => $recentOrders,
            'recent_fee_logs' => $recentFeeLogs
        ]);
    }
}
