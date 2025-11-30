<?php

namespace App\Http\Controllers\BG;

use App\Http\Controllers\Controller;
use App\Models\BG\BgSubmission;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BgSubmissionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = BgSubmission::with('recommendation');
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-warning btn-edit-bg-submission" data-id="' . $row->id . '">
                                <i class="fa-solid fa-pencil text-white"></i>
                            </button>
                            <form action="' . route('bg-submissions.destroy', $row->id) . '" method="POST" style="display:inline;">'
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

        return view('page.bg.bg_submissions.index');
    }

    public function show(BgSubmission $bgSubmission)
    {
        return $bgSubmission->load(['recommendation','lampiranD']);
    }

    public function store(Request $request)
    {
        $sub = BgSubmission::create($request->all());
        return response()->json(['success' => true, 'message' => 'Submission created successfully!', 'data' => $sub], 201);
    }

    public function update(Request $request, BgSubmission $bgSubmission)
    {
        $bgSubmission->update($request->all());
        return response()->json(['success' => true, 'message' => 'Submission updated successfully!', 'data' => $bgSubmission]);
    }

    public function destroy(BgSubmission $bgSubmission)
    {
        $bgSubmission->delete();
        return response()->json(['success' => true, 'message' => 'Submission deleted successfully!']);
    }
}
