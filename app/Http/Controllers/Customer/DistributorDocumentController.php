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

class DistributorDocumentController extends Controller
{
    public function __construct(
        protected DistributorDocumentService $documentService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $year = (int) $request->input('year', date('Y'));
        $search = $request->input('search');

        $distributors = $this->documentService->getMonitoringDistributors($year, $search, 15);

        if ($request->ajax()) {
            return view('finance.distributor_documents.partials.table', compact('distributors', 'year', 'search'));
        }

        // Get all distributors for the Add Distributor modal dropdown option
        $availableDistributors = Distributor::orderBy('code', 'asc')->get(['id', 'code', 'name', 'email', 'bupot_email']);

        return view('finance.distributor_documents.index', compact('distributors', 'year', 'search', 'availableDistributors'));
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
            return view('finance.distributor_documents.partials.detail-content', $viewData);
        }

        return view('finance.distributor_documents.detail', $viewData);
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
