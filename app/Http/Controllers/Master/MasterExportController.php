<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer\Customer;
use App\Models\Customer\Distributor;
use App\Models\Customer\DistributorCustomer;
use App\Exports\CustomerExport;
use App\Exports\LogisticFeeExport;
use App\Exports\DistributorExport;
use Maatwebsite\Excel\Facades\Excel;

class MasterExportController extends Controller
{
    public function index()
    {
        $stats = [
            'customers' => [
                'total'    => Customer::count(),
                'active'   => Customer::where('status', 'Active')->count(),
                'inactive' => Customer::where('status', '!=', 'Active')->count(),
            ],
            'logistic_fees' => [
                'total'    => DistributorCustomer::count(),
                'approved' => DistributorCustomer::where('status', 'Approved')->count(),
                'pending'  => DistributorCustomer::where('status', 'Pending')->count(),
            ],
            'distributors' => [
                'total'  => Distributor::count(),
                'linked' => Distributor::whereNotNull('customer_id')->count(),
                'manual' => Distributor::whereNull('customer_id')->count(),
            ],
        ];

        return view('page.master.export.index', compact('stats'));
    }

    public function exportCustomer(Request $request)
    {
        $status = $request->get('status', 'all');
        $filename = 'export_customers_' . ($status !== 'all' ? $status . '_' : '') . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new CustomerExport($status), $filename);
    }

    public function exportLogisticFee(Request $request)
    {
        $status = $request->get('status', 'all');
        $filename = 'export_logistic_fees_' . ($status !== 'all' ? strtolower($status) . '_' : '') . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new LogisticFeeExport($status), $filename);
    }

    public function exportDistributor()
    {
        $filename = 'export_distributors_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new DistributorExport(), $filename);
    }
}
