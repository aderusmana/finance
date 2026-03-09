<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SystemLogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Activity::with('causer')->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    // Format tanggal lebih compact: "12 Feb 2024" dan jam dibawahnya
                    $date = Carbon::parse($row->created_at);
                    return '<div style="line-height:1.2;">
                                <span style="font-weight:600; color:#343a40;">'.$date->format('d M Y').'</span><br>
                                <small style="color:#6c757d; font-size:0.75rem;">'.$date->format('H:i:s').'</small>
                            </div>';
                })
                ->addColumn('causer_name', function ($row) {
                    $name = $row->causer->name ?? 'System';

                    // Safely determine role name without assuming relations exist
                    $role = 'Bot';
                    if ($row->causer) {
                        if (method_exists($row->causer, 'getRoleNames')) {
                            $roles = $row->causer->getRoleNames();
                            $role = $roles->isNotEmpty() ? $roles->first() : 'User';
                        } else {
                            $role = optional(optional($row->causer->roles)->first())->name ?? 'User';
                        }
                    }

                    // Buat Avatar Inisial Sederhana dengan Inline CSS
                    $initial = strtoupper(substr($name, 0, 1));
                    $bgColors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'];
                    $color = $bgColors[rand(0, 4)]; // Random color agar tidak monoton
                    
                    if(!$row->causer) $color = '#858796'; // Abu-abu untuk System

                    return '
                    <div class="d-flex align-items-center">
                        <div style="width: 35px; height: 35px; background-color: '.$color.'; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; margin-right: 10px;">
                            '.$initial.'
                        </div>
                        <div style="line-height:1.2;">
                            <span style="font-weight:600; font-size:0.9rem; color:#2e384d;">'.e($name).'</span><br>
                            <small style="font-size:0.7rem; color:#8898aa; text-transform:uppercase;">'.e($role).'</small>
                        </div>
                    </div>';
                })
                ->editColumn('description', function ($row) {
                    $desc = $row->description; // Ambil teks asli
                    $event = $row->event; // create, update, delete
                    
                    // Tentukan warna badge event
                    $badgeStyle = "padding: 3px 8px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;";
                    
                    if ($event == 'created') { $badgeStyle .= " background:#d1e7dd; color:#0f5132;"; }
                    elseif ($event == 'updated') { $badgeStyle .= " background:#fff3cd; color:#856404;"; }
                    elseif ($event == 'deleted') { $badgeStyle .= " background:#f8d7da; color:#842029;"; }
                    else { $badgeStyle .= " background:#e2e3e5; color:#383d41;"; }

                    // Nama Log (misal: "Customer", "User")
                    $logName = ucfirst($row->log_name ?? 'System');

                    return '
                    <div>
                        <div style="margin-bottom: 4px;">
                            <span style="'.$badgeStyle.'">'.strtoupper($event).'</span>
                            <span style="font-size: 0.75rem; color: #6c757d; margin-left: 5px; font-weight: 600;">• '.e($logName).'</span>
                        </div>
                        <div style="font-size: 0.9rem; color: #343a40; font-weight: 500;">
                            '.e(Str::limit($desc, 60)).'
                        </div>
                    </div>';
                })
                ->addColumn('subject_description', function ($row) {
                    $subjectType = $row->subject_type;
                    
                    // Ambil ID atau Ref Code
                    $ref = '#'.$row->subject_id;
                    if(isset($row->properties['attributes']['code'])) $ref = $row->properties['attributes']['code'];
                    if(isset($row->properties['attributes']['name'])) $ref = $row->properties['attributes']['name'];

                    // Bersihkan Namespace Class (App\Models\Customer -> Customer)
                    $cleanSubject = class_basename($subjectType ?? 'General');

                    return '
                    <div style="line-height:1.2;">
                        <span style="font-weight:600; font-size:0.85rem; color:#4e73df;">'.e(Str::limit($ref, 25)).'</span><br>
                        <small style="color:#adb5bd; font-size:0.7rem;">Target: '.e($cleanSubject).'</small>
                    </div>';
                })
                ->addColumn('properties', function ($row) {
                    
                    // 1. CLEAN SUBJECT TYPE (Contoh: 'Customer')
                    $subjectTypeClean = '-';
                    if ($row->subject_type) {
                        $subjectTypeClean = class_basename($row->subject_type);
                        $subjectTypeClean = preg_replace('/(?<!\ )[A-Z]/', ' $0', $subjectTypeClean);
                    }

                    // 2. CLEAN CAUSER TYPE / ROLE (Contoh: 'Manager Finance')
                    $causerTypeClean = 'System / Bot';
                    if ($row->causer) {
                        if (method_exists($row->causer, 'getRoleNames')) {
                            $roles = $row->causer->getRoleNames();
                            $roleName = $roles->isNotEmpty() ? $roles->first() : 'User';
                            $causerTypeClean = ucwords(str_replace(['-', '_'], ' ', $roleName));
                        } else {
                            $causerTypeClean = class_basename($row->causer_type);
                        }
                    }

                    // 3. GET SUBJECT NAME (Nama Data Target)
                    // Cari field 'name', 'code', atau 'title' sebagai representasi nama
                    $subjectName = '-';
                    if ($row->subject) {
                        $subjectName = $row->subject->name ?? $row->subject->code ?? $row->subject->title ?? $row->subject->id;
                    } else {
                        // Jika data sudah dihapus, coba ambil dari log properties (attributes)
                        $attrs = $row->properties['attributes'] ?? [];
                        $subjectName = $attrs['name'] ?? $attrs['code'] ?? $attrs['title'] ?? 'Deleted Data';
                    }

                    // PAYLOAD LENGKAP
                    $fullData = [
                        'id' => $row->id,
                        'log_name' => $row->log_name,
                        'description' => $row->description,
                        'event' => $row->event,
                        
                        // Data yang sudah dibersihkan
                        'subject_type' => $subjectTypeClean, 
                        'subject_name' => $subjectName, // <--- INI DATA BARU (NAMA)
                        
                        'causer_type' => $causerTypeClean,
                        'causer_name' => $row->causer->name ?? 'System',
                        
                        'properties' => $row->properties, 
                        'created_at' => Carbon::parse($row->created_at)->format('d M Y, H:i:s'),
                    ];

                    $jsonData = htmlspecialchars(json_encode($fullData), ENT_QUOTES, 'UTF-8');

                    return '<button class="btn btn-sm btn-light border border-secondary fw-bold btn-view-json" 
                            style="font-size: 0.75rem; color: #4e73df;" 
                            data-row="'.$jsonData.'">
                                <i class="ph-bold ph-eye me-1"></i> View
                            </button>';
                })
                ->rawColumns(['causer_name', 'created_at', 'description', 'subject_description', 'properties'])
                ->make(true);
        }

        // --- STATISTIK UNTUK WIDGET ATAS ---
        $lastActivity = Activity::latest()->first();
        $stats = [
            'total_logs' => Activity::count(),
            'today_logs' => Activity::whereDate('created_at', Carbon::today())->count(),
            'unique_users' => Activity::distinct('causer_id')->count('causer_id'),
            'last_activity' => $lastActivity ? $lastActivity->created_at->diffForHumans() : '-'
        ];

        return view('page.master.system_log.index', compact('stats'));
    }
}