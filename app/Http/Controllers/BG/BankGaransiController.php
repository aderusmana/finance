<?php

namespace App\Http\Controllers\BG;

use App\Http\Controllers\Controller;
use App\Models\BG\BankGaransi;
use App\Models\BG\BgDetail;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BankGaransiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = BankGaransi::with(['customer','details']);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-warning btn-edit-bg" data-id="' . $row->id . '">
                                <i class="fa-solid fa-pencil text-white"></i>
                            </button>
                            <form action="' . route('bank-garansi.destroy', $row->id) . '" method="POST" style="display:inline;">'
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

        return view('page.bg.bank_garansi.index');
    }

    public function show(BankGaransi $bankGaransi)
    {
        return $bankGaransi->load(['details','histories','creator']);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $details = $data['details'] ?? [];
        unset($data['details']);

        $bg = BankGaransi::create($data);

        // create related details if provided
        if (is_array($details)) {
            foreach ($details as $d) {
                $bg->details()->create($d);
            }
        }

        return response()->json(['success' => true, 'message' => 'Bank Garansi created successfully!', 'data' => $bg->load('details')], 201);
    }

    public function update(Request $request, BankGaransi $bankGaransi)
    {
        $data = $request->all();
        $details = $data['details'] ?? null;
        $deletedDetailIds = $data['deleted_detail_ids'] ?? null;
        unset($data['details']);
        unset($data['deleted_detail_ids']);

        $bankGaransi->update($data);

        // handle deleted details
        if (is_array($deletedDetailIds)) {
            BgDetail::whereIn('id', $deletedDetailIds)->where('bank_garansi_id', $bankGaransi->id)->delete();
        }

        // handle upsert of details
        if (is_array($details)) {
            foreach ($details as $d) {
                if (!empty($d['id'])) {
                    $detail = BgDetail::where('bank_garansi_id', $bankGaransi->id)->find($d['id']);
                    if ($detail) {
                        $detail->update($d);
                    }
                } else {
                    $bankGaransi->details()->create($d);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Bank Garansi updated successfully!', 'data' => $bankGaransi->load('details')]);
    }

    public function destroy(BankGaransi $bankGaransi)
    {
        // delete related details explicitly for safety (migration also had cascade)
        $bankGaransi->details()->delete();
        $bankGaransi->delete();
        return response()->json(['success' => true, 'message' => 'Bank Garansi and related details deleted successfully!']);
    }

}
