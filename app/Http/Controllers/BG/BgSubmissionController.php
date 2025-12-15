<?php

namespace App\Http\Controllers\BG;

use App\Http\Controllers\Controller;
use App\Models\BG\BgSubmission;
use App\Models\BG\BgRecommendation;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BgSubmissionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = BgSubmission::with(['recommendation.customer']);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_name', function($row){
                    return $row->recommendation && $row->recommendation->customer
                        ? $row->recommendation->customer->name : '-';
                })
                ->editColumn('total_nominal', function($row){
                    return 'Rp ' . number_format($row->total_nominal, 0, ',', '.');
                })
                ->addColumn('file', function($row){
                    if($row->signed_document_path) {
                        return '<a href="'.asset($row->signed_document_path).'" target="_blank" class="btn btn-xs btn-info"><i class="ph-bold ph-file"></i> View</a>';
                    }
                    return '<span class="text-muted">No File</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-warning btn-edit" data-id="'.$row->id.'"><i class="ph-bold ph-pencil-simple"></i></button>';
                })
                ->rawColumns(['file', 'action'])
                ->make(true);
        }

        // Ambil list rekomendasi untuk dropdown (menampilkan nama customer)
        $recommendations = BgRecommendation::with('customer')
            ->whereHas('customer') // Pastikan ada customernya
            ->get();

        return view('page.bg.bg_submissions.index', compact('recommendations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bg_recommendation_id' => 'required',
            'form_code' => 'required|unique:bg_submissions,form_code',
        ]);

        $data = $request->all();

        // Handle File Upload
        if ($request->hasFile('signed_document')) {
            $file = $request->file('signed_document');
            $path = $file->store('bg_documents', 'public'); // pastikan symlink storage sudah jalan
            $data['signed_document_path'] = 'storage/' . $path;
        }

        BgSubmission::create($data);
        return response()->json(['success' => true, 'message' => 'Submission created!']);
    }

    // Update & Show & Destroy methods similar structure...
    public function update(Request $request, $id) {
         // Logic update file handling sama seperti store
         $sub = BgSubmission::findOrFail($id);
         $data = $request->all();
         if ($request->hasFile('signed_document')) {
            $data['signed_document_path'] = 'storage/' . $request->file('signed_document')->store('bg_documents', 'public');
         }
         $sub->update($data);
         return response()->json(['success' => true, 'message' => 'Updated!']);
    }

    public function show($id) { return response()->json(BgSubmission::findOrFail($id)); }
}
