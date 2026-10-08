<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreDistributorListRequest;
use App\Http\Requests\StoreDistributorDocumentRequest;
use App\Models\Customer\Distributor;
use App\Models\Customer\DistributorDocumentAttachment;
use App\Services\Distributor\DistributorDocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Yajra\DataTables\Facades\DataTables;

class DistributorDocumentController extends Controller
{
    public function __construct(
        protected DistributorDocumentService $documentService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $year = (int) $request->input('year', date('Y'));

        if ($request->ajax()) {
            $query = $this->documentService->getMonitoringDistributorsQuery($year);

            return DataTables::eloquent($query)
                ->addColumn('code', function ($distributor) {
                    return '<span class="distributor-code-badge">' . e($distributor->code) . '</span>';
                })
                ->addColumn('name', function ($distributor) {
                    $output = '<div class="fw-bold text-dark">' . e($distributor->name) . '</div>';
                    $bupotEmails = $distributor->bupot_email_list;
                    $output .= '<div class="small mt-1">';
                    if (!empty($bupotEmails)) {
                        $output .= '<div class="d-flex flex-wrap gap-1 align-items-center">';
                        foreach ($bupotEmails as $bEmail) {
                            $output .= '<span class="badge bg-light text-dark border fw-normal text-truncate" style="font-size: 0.75rem; max-width: 250px;" title="' . e($bEmail) . '">';
                            $output .= '<i class="iconoir-mail me-1 text-primary"></i>' . e($bEmail);
                            $output .= '</span>';
                        }
                        $output .= '</div>';
                    } else {
                        $output .= '<span class="text-muted fst-italic">-</span>';
                    }
                    $output .= '</div>';

                    return $output;
                })
                ->filterColumn('code', function ($query, $keyword) {
                    $query->where('distributors.code', 'like', "%{$keyword}%");
                })
                ->filterColumn('name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('distributors.name', 'like', "%{$keyword}%")
                            ->orWhere('distributors.code', 'like', "%{$keyword}%")
                            ->orWhere('distributors.bupot_email', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('monthly_progress', function ($distributor) use ($year) {
                    $monthNames = [
                        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
                        7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
                    ];
                    $monthlySummary = $distributor->getMonthlyDocumentSummary($year);

                    $html = '<div class="month-matrix-container justify-content-center">';
                    for ($m = 1; $m <= 12; $m++) {
                        $summary = $monthlySummary[$m] ?? ['status' => 'empty', 'total' => 0, 'has_bupot' => false, 'has_top_insentif' => false];
                        $status = $summary['status'];
                        $tooltip = $monthNames[$m] . ': ' . ($status === 'complete' ? 'Lengkap (BuPot & TOP)' : ($status === 'partial' ? 'Sebagian (' . ($summary['has_bupot'] ? 'BuPot' : 'TOP') . ')' : 'Belum Ada Dokumen'));
                        $icon = ($status === 'complete') ? '✓' : (($status === 'partial') ? '!' : '•');

                        $html .= '<button type="button" class="month-matrix-pill ' . $status . '" title="' . e($tooltip) . '" data-bs-toggle="tooltip" aria-label="Bulan ' . e($monthNames[$m]) . ': ' . e($tooltip) . '" onclick="openDistributorDetailModal(' . $distributor->id . ', ' . $year . ', \'monthly\', ' . $m . ')">';
                        $html .= '<span class="month-num">' . $m . '</span>';
                        $html .= '<span class="month-icon">' . $icon . '</span>';
                        $html .= '</button>';
                    }
                    $html .= '</div>';

                    return $html;
                })
                ->addColumn('transfer_running', function ($distributor) use ($year) {
                    if ($distributor->transfer_documents_count > 0) {
                        $transferYears = $distributor->transferDocuments->map(function ($doc) {
                            return $doc->transaction_date
                                ? (int) $doc->transaction_date->format('Y')
                                : (int) $doc->created_at->format('Y');
                        })->filter()->unique()->sort()->values();

                        $rangeHtml = '';
                        if ($transferYears->isNotEmpty()) {
                            $minY = $transferYears->first();
                            $maxY = $transferYears->last();
                            $range = $minY === $maxY ? (string) $minY : "{$minY}-{$maxY}";
                            $rangeHtml = '<div class="small text-muted mt-1" style="font-size: 0.72rem;">(' . e($range) . ')</div>';
                        }

                        return '<button type="button" class="btn btn-sm btn-link text-decoration-none p-0" onclick="openDistributorDetailModal(' . $distributor->id . ', ' . $year . ', \'transfer\')">'
                            . '<span class="badge bg-light text-dark border px-2 py-1 fw-bold">'
                            . $distributor->transfer_documents_count . ' File'
                            . '</span>'
                            . $rangeHtml
                            . '</button>';
                    }

                    return '<span class="text-muted small">0 File</span>';
                })
                ->addColumn('action', function ($distributor) use ($year) {
                    return '<button type="button" class="btn btn-sm btn-outline-primary text-nowrap" onclick="openDistributorDetailModal(' . $distributor->id . ', ' . $year . ')">'
                        . '<i class="iconoir-folder me-1"></i> Kelola File'
                        . '</button>';
                })
                ->rawColumns(['code', 'name', 'monthly_progress', 'transfer_running', 'action'])
                ->make(true);
        }

        // Get all distributors for the Add Distributor modal dropdown option
        $availableDistributors = Distributor::orderBy('code', 'asc')->get(['id', 'code', 'name', 'email', 'bupot_email']);

        return view('page.distributor_documents.index', compact('year', 'availableDistributors'));
    }

    public function detailView(Request $request, $distributorId): View
    {
        $distributor = Distributor::with('customer')->findOrFail($distributorId);
        $year = (int) $request->input('year', date('Y'));
        $transferYear = $request->input('transfer_year', 'all');
        $tab = $request->input('tab', 'monthly');

        $documentData = $this->documentService->getDetailData($distributor, $year, $tab, $transferYear);

        $viewData = array_merge([
            'distributor' => $distributor,
            'year' => $year,
        ], $documentData);

        // Return partial view if requested via AJAX (Modal Detail)
        if ($request->ajax() || $request->has('ajax')) {
            return view('page.distributor_documents.partials.detail-content', $viewData);
        }

        return view('page.distributor_documents.detail', $viewData);
    }

    public function storeUpload(StoreDistributorDocumentRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();
        $distributor = Distributor::findOrFail($validated['distributor_id']);

        $attachment = $this->documentService->uploadDocument(
            $distributor,
            $validated,
            $request->file('file'),
            auth()->id()
        );

        $isTransfer = $attachment->doc_type === 'transfer';
        $year = $attachment->year ?? (int) date('Y');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diunggah.',
                'distributor_id' => $distributor->id,
                'year' => $year,
                'tab' => $isTransfer ? 'transfer' : 'monthly',
            ]);
        }

        return redirect()->route('distributor.documents.detail', [
            'distributorId' => $distributor->id,
            'year' => $year,
            'tab' => $isTransfer ? 'transfer' : 'monthly',
        ])->with('success', 'Dokumen berhasil diunggah.');
    }

    public function destroy(Request $request, $id): JsonResponse|RedirectResponse
    {
        $attachment = DistributorDocumentAttachment::with('document')->findOrFail($id);
        $distributorId = $attachment->distributor_id;
        $year = $attachment->year ?? (int) date('Y');
        $docType = $attachment->doc_type;

        $this->documentService->deleteAttachment($attachment);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus.',
                'distributor_id' => $distributorId,
                'year' => $year,
                'tab' => $docType === 'transfer' ? 'transfer' : 'monthly',
            ]);
        }

        return redirect()->route('distributor.documents.detail', [
            'distributorId' => $distributorId,
            'year' => $year,
            'tab' => $docType === 'transfer' ? 'transfer' : 'monthly',
        ])->with('success', 'Dokumen berhasil dihapus.');
    }

    public function previewFile($id): BinaryFileResponse
    {
        $attachment = DistributorDocumentAttachment::findOrFail($id);

        return $this->documentService->previewResponse($attachment);
    }

    public function downloadFile(Request $request, $id): BinaryFileResponse
    {
        $attachment = DistributorDocumentAttachment::findOrFail($id);

        return $this->documentService->downloadResponse($attachment, 'internal', $request);
    }

    public function downloadAllZip(Request $request): BinaryFileResponse|RedirectResponse
    {
        $request->validate([
            'distributor_id' => ['required', 'exists:distributors,id'],
            'year' => ['required', 'integer'],
        ]);

        $distributor = Distributor::findOrFail($request->distributor_id);
        $year = (int) $request->year;

        try {
            return $this->documentService->downloadZipResponse($distributor, $year, 'internal', $request);
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            return redirect()->back()->with('error', 'Tidak ada dokumen yang dapat diunduh.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function storeDistributorList(StoreDistributorListRequest $request): JsonResponse
    {
        $distributor = Distributor::findOrFail($request->distributor_id);
        $year = (int) $request->year;
        $withUpload = $request->boolean('with_upload');

        try {
            $this->documentService->registerDistributorToYear(
                $distributor,
                $year,
                $withUpload,
                $withUpload ? $request->validated() : null,
                $withUpload ? $request->file('file') : null,
                auth()->id()
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $message = $withUpload
            ? "Distributor '{$distributor->name}' berhasil ditambahkan ke list tahun {$year} dan dokumen berhasil diunggah."
            : "Distributor '{$distributor->name}' berhasil ditambahkan ke list dokumen tahun {$year}.";

        return response()->json([
            'success' => true,
            'message' => $message,
            'year' => $year,
        ]);
    }
}
