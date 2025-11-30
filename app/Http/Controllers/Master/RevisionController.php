<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Revision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RevisionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if(!$user->can('view revision')){
            abort(403);
        }
        return view('page.master.revision.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:revisions,id',
            'revision_number' => 'required|string',
            'revision_count' => 'required|integer',
            'revision_date' => 'required|date',
        ]);

        try {
            $revision = Revision::findOrFail($request->id);
            $revision->update([
                'revision_number' => $request->revision_number,
                'revision_count' => $request->revision_count,
                'revision_date' => $request->revision_date,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Revision updated successfully',
                'data' => $revision
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update revision: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getrevisiondata()
    {
        $data = Revision::orderBy('created_at', 'desc')->get();
        return response()->json(['data' => $data]);
    }

}
