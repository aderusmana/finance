<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer\CustomerClass;

class CustomerClassController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $customerClasses = CustomerClass::query();

            return \Yajra\DataTables\Facades\DataTables::of($customerClasses)
                ->addIndexColumn()
                ->addColumn('action', function ($customerClass) {
                    return '
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-warning btn-edit-customer-class"
                                data-id="' . $customerClass->id . '"
                                data-name_class="' . e($customerClass->name_class) . '"
                            >
                                <i class="fa-solid fa-pencil text-white"></i>
                            </button>
                            <form action="' . route('customer-classes.destroy', $customerClass->id) . '" method="POST" class="delete-form delete-customer-class-btn" style="display:inline;">
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

        return view('page.master.customer-classes.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_class' => 'required|string|max:255|unique:customer_classes,name_class',
        ]);

        CustomerClass::create([
            'name_class' => $request->name_class,
        ]);

        return response()->json(['success' => true, 'message' => 'Customer class created successfully!']);
    }

    public function update(Request $request, CustomerClass $customerClass)
    {
        $request->validate([
            'name_class' => 'required|string|max:255|unique:customer_classes,name_class,' . $customerClass->id,
        ]);

        $customerClass->update([
            'name_class' => $request->name_class,
        ]);

        return response()->json(['success' => true, 'message' => 'Customer class updated successfully!']);
    }

    public function destroy($id)
    {
        $customerClass = CustomerClass::findOrFail($id);
        $customerClass->delete();

        return response()->json(['success' => true, 'message' => 'Customer class deleted successfully!']);
    }
}
