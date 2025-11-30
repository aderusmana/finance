<?php

namespace App\Http\Controllers\BG;

use App\Http\Controllers\Controller;
use App\Models\BG\LampiranD;
use App\Models\BG\LampiranDVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class LampiranDController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = LampiranD::with('submission');
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-warning btn-edit-lampiran-d" data-id="' . $row->id . '">
                                <i class="fa-solid fa-pencil text-white"></i>
                            </button>
                            <form action="' . route('lampiran-d.destroy', $row->id) . '" method="POST" style="display:inline;">'
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

        return view('page.bg.lampiran_d.index');
    }

    public function show(LampiranD $lampiranD)
    {
        return $lampiranD->load(['versions','activeVersion']);
    }

    public function store(Request $request)
    {
        $l = LampiranD::create($request->all());
        return response()->json(['success' => true, 'message' => 'Lampiran D created successfully!', 'data' => $l], 201);
    }

    public function update(Request $request, LampiranD $lampiranD)
    {
        // Update lampiran_d and create a new version snapshot
        $lampiranD->update($request->all());

        $nextVersion = ($lampiranD->version_latest ?? 0) + 1;
        $version = LampiranDVersion::create([
            'lampiran_d_id' => $lampiranD->id,
            'version_no' => $nextVersion,
            'data_snapshot' => $request->all(),
            'file_path' => $request->input('file_path'),
            'generated_by' => Auth::id(),
            'generated_at' => now(),
            'remarks' => $request->input('remarks')
        ]);

        $lampiranD->version_latest = $version->version_no;
        $lampiranD->active_version_id = $version->id;
        $lampiranD->save();

        return response()->json(['success' => true, 'message' => 'Lampiran D updated and version created successfully!', 'data' => $lampiranD, 'version' => $version]);
    }

    public function destroy(LampiranD $lampiranD)
    {
        $lampiranD->delete();
        return response()->json(['success' => true, 'message' => 'Lampiran D deleted successfully!']);
    }
}
