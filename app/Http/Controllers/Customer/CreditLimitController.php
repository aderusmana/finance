<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\CreditLimit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CreditLimitController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = CreditLimit::with(['customer','bankGaransi','recommendation']);
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-warning btn-edit-credit-limit" data-id="' . $row->id . '">
                                <i class="fa-solid fa-pencil text-white"></i>
                            </button>
                            <form action="' . route('credit-limits.destroy', $row->id) . '" method="POST" style="display:inline;">'
                            . csrf_field() . method_field('DELETE') . '
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

        return view('page.master.credit_limits.index');
    }

    public function show(CreditLimit $creditLimit)
    {
        return $creditLimit;
    }

    public function store(Request $request)
    {
        $cl = CreditLimit::create($request->all());
        return response()->json(['success' => true, 'message' => 'Credit limit request created successfully!', 'data' => $cl], 201);
    }

    public function update(Request $request, CreditLimit $creditLimit)
    {
        $creditLimit->update($request->all());
        return response()->json(['success' => true, 'message' => 'Credit limit updated successfully!', 'data' => $creditLimit]);
    }

    public function destroy(CreditLimit $creditLimit)
    {
        $creditLimit->delete();
        return response()->json(['success' => true, 'message' => 'Credit limit deleted successfully!']);
    }
}
