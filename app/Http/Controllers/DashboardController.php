<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BG\BankGaransi;
use App\Models\Customer\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama.
     */
    public function index()
    {
        // Get available years from requisition table
        $availableYears = $this->getAvailableYears();

        return view('dashboard', compact('availableYears'));
    }

    /**
     * Mendapatkan daftar tahun yang tersedia dari data requisition.
     */
    public function getAvailableYears()
    {
        // Use BankGaransi created year as available years for dashboard
        $years = BankGaransi::select(DB::raw('YEAR(created_at) as year'))
            ->whereNotNull('created_at')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($years)) {
            $years = [now()->year];
        }

        return $years;
    }

    /**
     * Menyediakan data agregat untuk kartu metrik di dashboard.
     */
    public function getMetricCounts()
    {
        // Provide BG/Customer based metrics mapped to the legacy keys used by the view
        $user = Auth::user();

        $bgQuery = BankGaransi::query();
        if (!$user->hasRole('super-admin')) {
            $bgQuery->where('created_by', $user->id);
        }

        $totalBgOpen = $bgQuery->whereNotIn('status', ['expired'])->count();
        $totalBgExpiring = (clone $bgQuery)->whereBetween('exp_date', [now()->startOfDay(), now()->addDays(60)->endOfDay()])->count();
        $totalBgValue = (clone $bgQuery)->whereNotIn('status', ['expired'])->sum('bg_nominal');
        $totalCustomers = Customer::count();
        $customersWithBg = Customer::whereHas('bankGaransis', function ($q) use ($user) {
            if (!$user->hasRole('super-admin')) {
                $q->where('created_by', $user->id);
            }
            $q->whereNotIn('status', ['expired']);
        })->count();

        // Map to legacy keys so frontend doesn't need immediate changes
        $counts = [
            'sample_fg' => (int)$totalBgOpen,
            'sample_pkg' => (int)$totalBgExpiring,
            'sample_so' => (float)$totalBgValue,
            'complain' => (int)$totalCustomers,
            'free_goods' => (int)$customersWithBg,
        ];

        return response()->json($counts);
    }

    /**
     * Menyediakan data untuk chart statistik per bulan.
     */
    public function getMonthlyStats(Request $request)
    {
        // Monthly stats derived from BankGaransi created_at and statuses.
        $user = Auth::user();
        $year = $request->input('year', now()->year);

        $query = BankGaransi::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(id) as created'),
            DB::raw("SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved"),
            DB::raw("SUM(CASE WHEN status = 'submitted' THEN 1 ELSE 0 END) as pending"),
            DB::raw("SUM(CASE WHEN status = 'reviewed' THEN 1 ELSE 0 END) as in_progress"),
            DB::raw("SUM(CASE WHEN status = 'expired' THEN 1 ELSE 0 END) as expired"),
            DB::raw("SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft")
        );

        if (!$user->hasRole('super-admin')) {
            $query->where('created_by', $user->id);
        }

        $stats = $query->whereYear('created_at', $year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'), 'ASC')
            ->get();

        $chartData = [
            'created'     => array_fill(0, 12, 0),
            'approved'    => array_fill(0, 12, 0),
            'pending'     => array_fill(0, 12, 0),
            'in_progress' => array_fill(0, 12, 0),
            'completed'   => array_fill(0, 12, 0),
            'rejected'    => array_fill(0, 12, 0),
            'recalled'    => array_fill(0, 12, 0),
        ];

        foreach ($stats as $stat) {
            $monthIndex = $stat->month - 1;
            $chartData['created'][$monthIndex]     = (int)$stat->created;
            $chartData['approved'][$monthIndex]    = (int)$stat->approved;
            $chartData['pending'][$monthIndex]     = (int)$stat->pending;
            $chartData['in_progress'][$monthIndex] = (int)$stat->in_progress;
            $chartData['completed'][$monthIndex]   = (int)$stat->approved; // map approved to completed for compatibility
            $chartData['rejected'][$monthIndex]    = (int)$stat->expired; // map expired to rejected
            $chartData['recalled'][$monthIndex]    = (int)$stat->draft; // map draft to recalled placeholder
        }

        return response()->json($chartData);
    }

    /**
     * Menyediakan data Top 5 Item yang paling sering direquest.
     */
    public function getTopItems(Request $request)
    {
        // Return empty or top customers by BG as a replacement for items
        return $this->topCustomersByBg($request);
    }

    /**
     * Menyediakan data Top 5 Customer yang paling sering melakukan request.
     */
    public function getTopCustomers(Request $request)
    {
        return $this->topCustomersByBg($request);
    }

    /**
     * Mengambil aktivitas requisition terbaru.
     */
    public function getRecentActivities()
    {
        // Replace recent activities with recent BG entries
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
     * Mengambil notifikasi atau tindakan yang perlu dilakukan user.
     */
    public function getMyActions()
    {
        // Fungsi ini sudah spesifik per user, tidak perlu diubah
        $user = Auth::user();
        $notifications = $user->unreadNotifications()->limit(5)->get()->map(function ($notification) {
            return [
                'id' => $notification->id,
                'message' => $notification->data['message'],
                'url' => $notification->data['url'] ?? '#',
                'timestamp' => $notification->created_at->diffForHumans(),
                'causer_name' => $notification->data['causer_name'] ?? 'System',
            ];
        });

        return response()->json([
            'count' => $user->unreadNotifications->count(),
            'notifications' => $notifications
        ]);
    }

    /**
     * API endpoint untuk mendapatkan daftar tahun yang tersedia.
     */
    public function getAvailableYearsApi()
    {
        $years = $this->getAvailableYears();
        return response()->json($years);
    }

    /**
     * BG specific metrics: open count, expiring within 60 days, total value, pending approvals.
     */
    public function bgMetrics()
    {
        $user = Auth::user();

        $query = BankGaransi::query();

        if (!$user->hasRole('super-admin')) {
            $query->where('created_by', $user->id);
        }

        $today = now()->startOfDay();
        $threshold = now()->addDays(60)->endOfDay();

        $openCount = (clone $query)->whereNotIn('status', ['expired'])->count();
        $expiringCount = (clone $query)->whereBetween('exp_date', [$today, $threshold])->count();
        $totalValue = (clone $query)->whereNotIn('status', ['expired'])->sum('bg_nominal');
        // pending approvals: consider statuses 'submitted' or 'reviewed' as awaiting approval
        $pendingApprovals = (clone $query)->whereIn('status', ['submitted', 'reviewed'])->count();

        return response()->json([
            'open' => (int)$openCount,
            'expiring' => (int)$expiringCount,
            'total_value' => (float)$totalValue,
            'pending_approvals' => (int)$pendingApprovals,
        ]);
    }

    /**
     * Customer metrics: total customers, credit exceeded, customers with active BG.
     */
    public function customerMetrics()
    {
        $user = Auth::user();

        // total customers (no role restriction here)
        $total = Customer::count();

        // customers with active BG (status not expired)
        $withBg = Customer::whereHas('bankGaransis', function ($q) use ($user) {
            if (!$user->hasRole('super-admin')) {
                $q->where('created_by', $user->id);
            }
            $q->whereNotIn('status', ['expired']);
        })->count();

        // credit exceeded: customers where credit_limit < sum of active BG nominal
        $creditExceeded = 0;
        $customers = Customer::whereNotNull('credit_limit')->get(['id','credit_limit']);
        foreach ($customers as $c) {
            $sumBg = BankGaransi::where('customer_id', $c->id)->whereNotIn('status', ['expired'])->sum('bg_nominal');
            if ($c->credit_limit < $sumBg) {
                $creditExceeded++;
            }
        }

        return response()->json([
            'total' => (int)$total,
            'with_bg' => (int)$withBg,
            'credit_exceeded' => (int)$creditExceeded,
        ]);
    }

    /**
     * Recent Bank Garansi entries (latest 5)
     */
    public function recentBgs()
    {
        $user = Auth::user();
        $query = BankGaransi::with('customer')->orderBy('created_at', 'desc');
        if (!$user->hasRole('super-admin')) {
            $query->where('created_by', $user->id);
        }

        $bgs = $query->limit(5)->get()->map(function ($bg) {
            return [
                'id' => $bg->id,
                'bg_number' => $bg->bg_number,
                'customer_name' => optional($bg->customer)->name ?? null,
                'status' => $bg->status,
                'exp_date' => $bg->exp_date ? $bg->exp_date->toDateString() : null,
            ];
        });

        return response()->json($bgs);
    }

    /**
     * Top customers by BG count.
     */
    public function topCustomersByBg()
    {
        $user = Auth::user();

        $query = Customer::select('customers.id', 'customers.name', DB::raw('COUNT(bank_garansi.id) as bg_count'))
            ->leftJoin('bank_garansi', 'customers.id', '=', 'bank_garansi.customer_id')
            ->groupBy('customers.id', 'customers.name');

        if (!$user->hasRole('super-admin')) {
            $query->where(function($q) use ($user) {
                $q->whereNotNull('bank_garansi.created_by')
                  ->where('bank_garansi.created_by', $user->id);
            });
        }

        $list = $query->orderByDesc('bg_count')->limit(5)->get()->map(function ($row) {
            return [
                'id' => $row->id,
                'name' => $row->name,
                'code' => $row->id,
                'bg_count' => (int)$row->bg_count,
            ];
        });

        return response()->json($list);
    }
}
