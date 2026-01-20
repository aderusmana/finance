<?php

use App\Http\Controllers\Customer\AccountGroupController;
use App\Http\Controllers\Master\ApprovalPathController;
use App\Http\Controllers\Customer\BranchController;
use App\Http\Controllers\Customer\CustomerClassController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Master\DepartmentController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\BG\BankGaransiController;
use App\Http\Controllers\BG\BgHistoryController;
use App\Http\Controllers\BG\BgRecommendationController;
use App\Http\Controllers\BG\BgSubmissionController;
use App\Http\Controllers\Customer\RegionsController;
use App\Http\Controllers\Customer\SalesController;
use App\Http\Controllers\Customer\TOPController;
use App\Http\Controllers\Master\PermissionController;
use App\Http\Controllers\Master\RevisionController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Master\PositionController;
use App\Http\Controllers\Master\BgTaxController;
use App\Http\Controllers\Master\BgLimitRuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Requisition\ItemController;
use App\Http\Controllers\BG\CustomerBgPortalController;
use App\Http\Controllers\BG\ApprovalProcessController;
use App\Http\Controllers\BG\BgApprovalInboxController;
use App\Http\Controllers\BG\BgReportController;
use App\Http\Controllers\BG\LampiranDController;
use App\Http\Controllers\Master\SystemLogController;
use Illuminate\Support\Facades\Route;

// Redirect root to the named login route (actual login routes are defined in routes/auth.php)
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/tes-404', function () {
    abort(404); // Menampilkan halaman resources/views/errors/404.blade.php
});

Route::get('/tes-500', function () {
    abort(500); // Menampilkan halaman resources/views/errors/500.blade.php
});

Route::get('/tes-403', function () {
    abort(403, 'Akses Ditolak'); // Menampilkan halaman 403 dengan pesan kustom
});

Route::get('/customers/approval/{token}', [CustomerController::class, 'viewApprovalPage'])->name('customers.view_approval');
Route::post('/customers/{customer}/approval-action', [CustomerController::class, 'approvalAction'])->name('customers.approval_action');
Route::get('/approval/process/{token}/{action}', [ApprovalProcessController::class, 'process'])->name('approval.process');
Route::get('/approval/form/{token}/{action}', [ApprovalProcessController::class, 'showForm'])->name('approval.form');
Route::post('/approval/submit/{token}', [ApprovalProcessController::class, 'submit'])->name('approval.submit');
Route::get('/public/download-doc/{bg_id}/{type}', [CustomerBgPortalController::class, 'downloadExpiringPdf'])->name('public.bg.download')->middleware('signed');

Route::prefix('customer-portal')->name('customer.portal.')->group(function () {
    Route::get('/form/{token}', [CustomerBgPortalController::class, 'showInputForm'])->name('input-form');
    Route::post('/form/{token}', [CustomerBgPortalController::class, 'storeInputData'])->name('store-input');
    Route::get('/upload/{token}', [CustomerBgPortalController::class, 'showUploadForm'])->name('upload-form');
    Route::post('/upload/{token}', [CustomerBgPortalController::class, 'storeUploadData'])->name('store-upload');
    Route::get('/download/{token}', [CustomerBgPortalController::class, 'downloadPdf'])->name('download-pdf');
    Route::get('/download-lampiran-d/{token}', [CustomerBgPortalController::class, 'downloadLampiranD'])->name('download-lampiran-d');
});

Route::middleware('auth')->group(function () {
    Route::post('/customers/generate-pkd-preview', [CustomerController::class, 'generatePkdPreview'])->name('customers.generate-pkd-preview');
        Route::prefix('dashboard/data')->name('dashboard.data.')->group(function () {
            Route::get('/metric-counts', [DashboardController::class, 'getMetricCounts'])->name('metric-counts');
            Route::get('/monthly-stats', [DashboardController::class, 'getMonthlyStats'])->name('monthly-stats');
            Route::get('/top-items', [DashboardController::class, 'getTopItems'])->name('top-items');
            Route::get('/top-customers', [DashboardController::class, 'getTopCustomers'])->name('top-customers');
            Route::get('/recent-activities', [DashboardController::class, 'getRecentActivities'])->name('recent-activities');
            Route::get('/my-actions', [DashboardController::class, 'getMyActions'])->name('my-actions');
            Route::get('/available-years', [DashboardController::class, 'getAvailableYearsApi'])->name('available-years');
            Route::get('/bg-metrics', [DashboardController::class, 'bgMetrics'])->name('bg-metrics');
            Route::get('/customer-metrics', [DashboardController::class, 'customerMetrics'])->name('customer-metrics');
            Route::get('/recent-bgs', [DashboardController::class, 'recentBgs'])->name('recent-bgs');
            Route::get('/top-customers-bg', [DashboardController::class, 'topCustomersByBg'])->name('top-customers-bg');
        });

    Route::get('/customers/approval', [CustomerController::class, 'approvalPage'])->name('customers.approval');
    Route::get('/customers-approval/data', [CustomerController::class, 'getApprovalData'])->name('customers.approval.data');
    Route::post('/approvals/resend/{token}', [CustomerController::class, 'resendApprovalEmail'])->name('approvals.resend');

    Route::get('/customers/log', [CustomerController::class, 'logPage'])->name('customers.log');
    Route::get('/customers-log/data', [CustomerController::class, 'getLogData'])->name('customers.log.data');

    Route::resource('revision', RevisionController::class);
    Route::resource('account-groups', AccountGroupController::class);
    Route::resource('regions', RegionsController::class);
    Route::resource('branches', BranchController::class);
    Route::resource('sales', SalesController::class);
    Route::resource('tops', TOPController::class);
    Route::resource('customer-classes', CustomerClassController::class);
    Route::resource('tax', BgTaxController::class);
    Route::resource('limit-rules', BgLimitRuleController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ROUTE NOTIFIKASI
    Route::get('/notifications/count', [NotificationController::class, 'count'])->name('notifications.count');
    Route::get('/notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
    Route::post('/notifications/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read.all');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // --- MASTER DATA ROUTES ---
    Route::prefix('master')->group(function () {
        Route::get('items', [ItemController::class, 'indexMaster'])->name('items.indexMaster');
        Route::post('items', [ItemController::class, 'store'])->name('items.store');
        Route::get('items/{id}/edit', [ItemController::class, 'edit'])->name('items.edit');
        Route::put('items/{id}', [ItemController::class, 'update'])->name('items.update');
        Route::delete('items/{id}', [ItemController::class, 'destroy'])->name('items.destroy');
        Route::get('items/details', [ItemController::class, 'detailsAll'])->name('items.details.all');
        Route::get('items/select2', [ItemController::class, 'select2'])->name('items.select2');
        Route::post('items/details', [ItemController::class, 'storeDetailAll'])->name('items.details.storeAll');
        Route::get('items/details/{id}/edit', [ItemController::class, 'editDetailAll'])->name('items.details.editAll');
        Route::put('items/details/{id}', [ItemController::class, 'updateDetailAll'])->name('items.details.updateAll');
        Route::delete('items/details/{id}', [ItemController::class, 'destroyDetailAll'])->name('items.details.destroyAll');

        Route::get('items/{item_id}/details', [ItemController::class, 'detailsIndex'])->name('items.details.index');
        Route::post('items/{item_id}/details', [ItemController::class, 'storeDetail'])->name('items.details.store');
        Route::get('items/{item_id}/details/{id}/edit', [ItemController::class, 'editDetail'])->name('items.details.edit');
        Route::put('items/{item_id}/details/{id}', [ItemController::class, 'updateDetail'])->name('items.details.update');
        Route::delete('items/{item_id}/details/{id}', [ItemController::class, 'destroyDetail'])->name('items.details.destroy');
    });

    Route::group(['prefix' => 'bg'], function () {
        Route::resource('bg-list', BankGaransiController::class);
        Route::get('generate-number', [BankGaransiController::class, 'generateNumber'])->name('bg.generate-number');
        Route::resource('bg-recommendations', BgRecommendationController::class);
        Route::resource('bg-submissions', BgSubmissionController::class);
        Route::get('bg-histories/export', [BgHistoryController::class, 'export'])->name('bg-histories.export');
        Route::resource('bg-histories', BgHistoryController::class)->only(['index', 'destroy']);
        Route::post('bg-recommendations/{id}/periods', [BgRecommendationController::class, 'savePeriods'])->name('bg-recommendations.save-periods');
        Route::get('customer/bg-form/{id}', [BgRecommendationController::class, 'showForm'])->name('customer.bg.form');
        Route::post('customer/bg-form/{id}', [BgRecommendationController::class, 'submitDetails'])->name('customer.bg.submit');
        Route::get('bg-submissions/{id}/edit-data', [BgSubmissionController::class, 'getEditData'])->name('bg-submissions.get-edit-data');
        Route::post('bg-submissions/{id}/process-review', [BgSubmissionController::class, 'processReview'])->name('bg-submissions.process-review');
        Route::resource('lampiran-d', LampiranDController::class);
        Route::get('lampiran-d/{id}/versions', [LampiranDController::class, 'versions'])->name('lampiran-d.versions');
        Route::get('lampiran-d/version/{versionId}', [LampiranDController::class, 'showVersionDetail'])->name('lampiran-d.version.show');
        Route::get('approvals/inbox', [BgApprovalInboxController::class, 'index'])->name('bg-approvals.index');
        Route::get('approvals/modal-data/{id}', [BgApprovalInboxController::class, 'getModalData']);
        Route::post('approvals/process', [BgApprovalInboxController::class, 'process'])->name('bg-approvals.process');
        Route::post('approvals/resend/{id}', [BgApprovalInboxController::class, 'resendEmail']);
        Route::get('reports', [BgReportController::class, 'index'])->name('bg-reports.index');
        Route::post('reports/bulk-download', [BgReportController::class, 'bulkDownload'])->name('bg-reports.bulk-download');
        Route::get('reports/download/{id}/{doc_type}', [BgReportController::class, 'downloadDoc'])->name('bg-reports.download');
        Route::get('reports/letters/{id}/{letter_type}', [BgReportController::class, 'downloadLetters'])->name('bg-reports.download-letters');
        Route::post('bg/request-existing/{id}', [BankGaransiController::class, 'requestExisting'])->name('bg.request.existing');
        Route::post('bg/request-extension', [BankGaransiController::class, 'requestExtension'])->name('bg.request.extension');
    });
});

Route::group(['middleware' => ['role:super-admin|admin']], function () {
    Route::resource('users', UserController::class);
    Route::get('/users-data', [UserController::class, 'getData'])->name('users.data');
    Route::resource('departments', DepartmentController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('positions', PositionController::class);
    Route::get('/system-logs', [SystemLogController::class, 'index'])->name('system-logs.index');
    Route::get('roles/{roleId}/give-permissions', [RoleController::class, 'addPermissionToRole'])->name('roles.give-permissions');
    Route::post('roles/{roleId}/give-permissions', [RoleController::class, 'givePermissionToRole'])->name('roles.give-permission');

    // --- Requisition Path (Approvers) ---
    Route::get('/approval/path', [ApprovalPathController::class, 'index'])->name('approval.path');
    Route::get('/getapproverlist', [ApprovalPathController::class, 'approverList'])->name('get.approverlist');
    Route::resource('/approvers', ApprovalPathController::class);
    Route::get('/categories', [ApprovalPathController::class, 'categories'])->name('get.categories');
    Route::get('/approver-name', [ApprovalPathController::class, 'approverName'])->name('get.approver.name');

    Route::get('/master/revision', [RevisionController::class, 'index'])->name('master.revision.index');
    Route::post('/master/revision/update', [RevisionController::class, 'update'])->name('master.revision.update');
    Route::get('/master/revision/getdata', [RevisionController::class, 'getrevisiondata'])->name('master.revision.getdata');
});


require __DIR__ . '/auth.php';
