<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class SystemLogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Do not eager-load `subject` because some activity rows may reference
            // classes that no longer exist (would trigger MorphTo exceptions).
            $query = Activity::with('causer')->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d M Y H:i:s');
                })
                ->addColumn('causer_name', function ($row) {
                    if (!$row->causer) return '<span class="badge bg-dark">System / Bot</span>';
                    return $row->causer->name ?? 'Unknown User';
                })
                ->editColumn('description', function ($row) {
                    $desc = strtolower($row->description);
                    $color = 'secondary';
                    $icon = 'ph-info';

                    if (str_contains($desc, 'create') || str_contains($desc, 'upload')) { $color = 'success'; $icon = 'ph-plus-circle'; }
                    if (str_contains($desc, 'update') || str_contains($desc, 'edit')) { $color = 'warning'; $icon = 'ph-pencil'; }
                    if (str_contains($desc, 'delete')) { $color = 'danger'; $icon = 'ph-trash'; }
                    if (str_contains($desc, 'approve')) { $color = 'primary'; $icon = 'ph-check-circle'; }
                    if (str_contains($desc, 'reject')) { $color = 'danger'; $icon = 'ph-x-circle'; }
                    if (str_contains($desc, 'expired')) { $color = 'danger'; $icon = 'ph-clock-warning'; }

                    return '<span class="badge bg-'.$color.'"><i class="ph-bold '.$icon.' me-1"></i>'.ucfirst($row->description).'</span>';
                })
                ->addColumn('subject_description', function ($row) {
                    $subjectType = $row->subject_type ?? null;
                    $class = $subjectType ? class_basename($subjectType) : 'N/A';

                    // If the subject class doesn't exist anymore, avoid accessing ->subject
                    if (empty($subjectType) || !class_exists($subjectType)) {
                        $ref = $row->properties['attributes']['form_code'] ?? '-';
                        return '<small class="text-muted">'.e($class).'</small><br><strong>'.e($ref).'</strong>';
                    }

                    // Safe to attempt to access the relation (may still be null)
                    $subject = $row->subject;
                    $ref = $row->properties['attributes']['form_code'] ?? ($subject->id ?? '-');
                    if(isset($subject->bg_number)) $ref = $subject->bg_number;
                    if(isset($subject->name)) $ref = $subject->name;

                    return '<small class="text-muted">'.e($class).'</small><br><strong>'.e($ref).'</strong>';
                })
                ->addColumn('properties', function ($row) {
                    return '<button class="btn btn-xs btn-outline-info btn-view-json" data-json=\''.json_encode($row->properties).'\'><i class="ph-bold ph-code"></i> Details</button>';
                })
                ->rawColumns(['causer_name', 'description', 'subject_description', 'properties'])
                ->make(true);
        }

        return view('page.master.system_log.index');
    }
}
