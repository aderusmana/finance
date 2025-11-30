<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $customers = Customer::query();

            return DataTables::of($customers)
                ->addIndexColumn()
                ->addColumn('action', function ($customer) {
                    return '
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-warning btn-edit-customer"
                                data-id="' . $customer->id . '"
                                data-name="' . e($customer->name) . '"
                                data-address="' . e($customer->address) . '"
                            >
                                <i class="fa-solid fa-pencil text-white"></i>
                            </button>
                            <form action="' . route('customers.destroy', $customer->id) . '" method="POST" class="delete-form delete-customer-btn" style="display:inline;">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash-alt text-white"></i>
                                </button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('page.master.customers.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:customers,name',
            'address' => 'nullable|string|max:500',
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'address' => $request->address,
            'slug' => Str::slug($request->name),
        ]);

        activity()
           ->causedBy(Auth::user())
           ->performedOn($customer)
           ->event('customers')
           ->log('Created a new customer');

        return response()->json(['success' => true, 'message' => 'Customer created successfully!']);
    }

    public function update(Request $request, customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:customers,name,' . $customer->id,
            'address' => 'nullable|string|max:500',
        ]);

        $oldData = $customer->getOriginal();

        $customer->update([
            'name' => $request->name,
            'address' => $request->address,
            'slug' => Str::slug($request->name),
        ]);

        activity()
           ->causedBy(Auth::user())
           ->performedOn($customer)
           ->event('customers')
           ->withProperties(['old' => $oldData, 'new' => $customer->getChanges()])
           ->log('Updated customer data');

        return response()->json(['success' => true, 'message' => 'Customer updated successfully!']);
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $oldData = $customer->toArray();
        $customer->delete();

        activity()
           ->causedBy(Auth::user())
           ->event('customers')
           ->withProperties(['deleted_data' => $oldData])
           ->log('Deleted a customer');

        return response()->json(['success' => true, 'message' => 'Customer deleted successfully!']);
    }
}
