<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreDistributorListRequest;
use App\Http\Requests\StoreDistributorDocumentRequest;
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

        if ($request->ajax()) {
            return $this->documentService->getMonitoringDataTable($year);
        }

        return view('page.distributor_documents.index', [
            'year' => $year,
            'availableDistributors' => $this->documentService->getAvailableDistributors(),
        ]);
    }

    public function detailView(Request $request, $distributorId): View|JsonResponse
    {
        $transferYear = $request->input('transfer_year', 'all');

        // Return JSON response for transfer running table
        if ($request->has('draw') || $request->input('table') === 'transfer') {
            return $this->documentService->getTransferDataTable(
                (int) $distributorId,
                $transferYear,
                isPortal: false,
                canManage: auth()->user()?->can('manage-distributor-docs') ?? false
            );
        }

        $year = (int) $request->input('year', date('Y'));
        $tab = $request->input('tab', 'monthly');

        $viewData = $this->documentService->getDetailViewData((int) $distributorId, $year, $tab, $transferYear);

        // Return partial view if requested via AJAX (Modal Detail)
        if ($request->ajax() || $request->has('ajax')) {
            return view('page.distributor_documents.partials.detail-content', $viewData);
        }

        return view('page.distributor_documents.detail', $viewData);
    }

    public function storeUpload(StoreDistributorDocumentRequest $request): JsonResponse|RedirectResponse
    {
        $result = $this->documentService->uploadDocumentFromRequest(
            $request->validated(),
            $request->file('file'),
            auth()->id()
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'distributor_id' => $result['distributor_id'],
                'year' => $result['year'],
                'tab' => $result['tab'],
            ]);
        }

        return redirect()->route('distributor.documents.detail', [
            'distributorId' => $result['distributor_id'],
            'year' => $result['year'],
            'tab' => $result['tab'],
        ])->with('success', $result['message']);
    }

    public function destroy(Request $request, $id): JsonResponse|RedirectResponse
    {
        $result = $this->documentService->deleteAttachmentById((int) $id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'distributor_id' => $result['distributor_id'],
                'year' => $result['year'],
                'tab' => $result['tab'],
            ]);
        }

        return redirect()->route('distributor.documents.detail', [
            'distributorId' => $result['distributor_id'],
            'year' => $result['year'],
            'tab' => $result['tab'],
        ])->with('success', $result['message']);
    }

    public function previewFile($id): BinaryFileResponse
    {
        return $this->documentService->previewAttachment((int) $id);
    }

    public function downloadFile(Request $request, $id): BinaryFileResponse
    {
        return $this->documentService->downloadAttachment((int) $id, 'internal', $request);
    }

    public function downloadAllZip(Request $request): BinaryFileResponse|RedirectResponse
    {
        $request->validate([
            'distributor_id' => ['required', 'exists:distributors,id'],
            'year' => ['required', 'integer'],
        ]);

        try {
            return $this->documentService->downloadZipByDistributorId(
                (int) $request->distributor_id,
                (int) $request->year,
                'internal',
                $request
            );
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            return redirect()->back()->with('error', 'Tidak ada dokumen yang dapat diunduh.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function storeDistributorList(StoreDistributorListRequest $request): JsonResponse
    {
        try {
            $result = $this->documentService->registerDistributor(
                (int) $request->distributor_id,
                (int) $request->year,
                $request->boolean('with_upload'),
                $request->boolean('with_upload') ? $request->validated() : null,
                $request->boolean('with_upload') ? $request->file('file') : null,
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'year' => $result['year'],
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
