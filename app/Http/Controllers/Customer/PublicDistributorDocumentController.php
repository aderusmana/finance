<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\RequestDistributorOtpRequest;
use App\Http\Requests\Customer\VerifyDistributorOtpRequest;
use App\Services\Distributor\DistributorDocumentService;
use App\Services\Distributor\DistributorPortalAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PublicDistributorDocumentController extends Controller
{
    public function __construct(
        protected DistributorDocumentService $documentService,
        protected DistributorPortalAuthService $authService
    ) {}

    /**
     * Request OTP for external distributor using code and registered bupot_email.
     */
    public function requestOtp(RequestDistributorOtpRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->requestOtp(
                $request->input('code'),
                $request->input('email'),
                $request->ip()
            );

            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => collect($e->errors())->flatten()->first() ?? $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Verify OTP and return document data for portal display.
     */
    public function verifyOtp(VerifyDistributorOtpRequest $request): JsonResponse
    {
        try {
            $distributor = $this->authService->verifyOtp(
                $request->input('code'),
                $request->input('email'),
                $request->input('otp')
            );

            $payload = $this->documentService->getPortalVerificationPayload($distributor);

            return response()->json($payload);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => collect($e->errors())->flatten()->first() ?? $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Fetch documents dynamically via AJAX (e.g. when changing year or tab inside modal).
     */
    public function getDocuments(Request $request): JsonResponse
    {
        $distributorId = (int) $request->get('distributor_id');
        $this->authService->authorizePortalAccess($distributorId);

        $transferYear = $request->get('transfer_year', 'all');

        // Return JSON response for portal transfer running table
        if ($request->has('draw') || $request->input('table') === 'transfer') {
            return $this->documentService->getTransferDataTable(
                $distributorId,
                $transferYear,
                isPortal: true,
                canManage: false
            );
        }

        $year = (int) ($request->get('year') ?: now()->year);
        $tab = $request->get('tab', 'monthly');

        $data = $this->documentService->getPortalDocumentData($distributorId, $year, $tab, $transferYear);

        return response()->json($data);
    }

    /**
     * Preview single file PDF with session ownership validation.
     */
    public function previewFile($id): BinaryFileResponse
    {
        $document = $this->documentService->findAttachment((int) $id);
        $this->authService->authorizePortalAccess($document->distributor_id);

        return $this->documentService->previewResponse($document);
    }

    /**
     * Download single file while recording audit trail.
     */
    public function downloadFile(Request $request, $id): BinaryFileResponse
    {
        $document = $this->documentService->findAttachment((int) $id);
        $this->authService->authorizePortalAccess($document->distributor_id);

        return $this->documentService->downloadResponse($document, 'guest_portal', $request);
    }

    /**
     * Bundling download documents to zip file with session validation.
     */
    public function downloadAllZip(Request $request)
    {
        $request->validate([
            'distributor_id' => ['required', 'exists:distributors,id'],
            'year' => ['required', 'integer'],
        ]);

        $distributorId = (int) $request->distributor_id;
        $this->authService->authorizePortalAccess($distributorId);

        try {
            return $this->documentService->downloadZipByDistributorId(
                $distributorId,
                (int) $request->year,
                'guest_portal',
                $request
            );
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            return redirect()->back()->with('error', 'Tidak ada dokumen yang dapat diunduh.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Terminate portal guest session.
     */
    public function portalLogout(Request $request): JsonResponse
    {
        $this->authService->logout();

        return response()->json(['success' => true, 'message' => 'Sesi portal telah ditutup.']);
    }
}
