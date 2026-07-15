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
use App\Http\Controllers\Master\LogisticFeeController;
use App\Http\Controllers\BG\CustomerBgPortalController;
use App\Http\Controllers\BG\ApprovalProcessController;
use App\Http\Controllers\BG\BgApprovalInboxController;
use App\Http\Controllers\BG\BgReportController;
use App\Http\Controllers\BG\LampiranDController;
use App\Http\Controllers\LogisticOrder\LogisticOrderController;
use App\Http\Controllers\Master\CustomerShipToController;
use App\Http\Controllers\Master\DistributorController;
use App\Http\Controllers\Master\SystemLogController;
use Illuminate\Support\Facades\Route;

// Redirect root to the named login route (actual login routes are defined in routes/auth.php)
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/customer', [DashboardController::class, 'customerIndex'])->name('dashboard.customer');
    Route::get('/dashboard/bank-garansi', [DashboardController::class, 'bgIndex'])->name('dashboard.bg');
    Route::get('/dashboard/logistic', [DashboardController::class, 'logisticIndex'])->name('dashboard.logistic');
});

Route::get('/tes-404', function () {
    abort(404); // Menampilkan halaman resources/views/errors/404.blade.php
});

Route::get('/tes-500', function () {
    abort(500); // Menampilkan halaman resources/views/errors/500.blade.php
});

Route::get('/tes-403', function () {
    abort(403, 'Akses Ditolak'); // Menampilkan halaman 403 dengan pesan kustom
});



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
    Route::get('/system-logs', [SystemLogController::class, 'index'])->name('system-logs.index');
    Route::get('/approval/path', [ApprovalPathController::class, 'index'])->name('approval.path');
    Route::get('/getapproverlist', [ApprovalPathController::class, 'approverList'])->name('get.approverlist');
    Route::resource('/approvers', ApprovalPathController::class);
    Route::get('/categories', [ApprovalPathController::class, 'categories'])->name('get.categories');
    Route::get('/approver-name', [ApprovalPathController::class, 'approverName'])->name('get.approver.name');

    Route::get('/master/revision', [RevisionController::class, 'index'])->name('master.revision.index');
    Route::post('/master/revision/update', [RevisionController::class, 'update'])->name('master.revision.update');
    Route::get('/master/revision/getdata', [RevisionController::class, 'getrevisiondata'])->name('master.revision.getdata');
    Route::prefix('dashboard/data')->name('dashboard.data.')->group(function () {
        Route::get('/available-years', [DashboardController::class, 'getAvailableYearsApi'])->name('available-years');
        Route::get('/monthly-stats', [DashboardController::class, 'getMonthlyStats'])->name('monthly-stats');
        Route::get('/advanced-stats', [DashboardController::class, 'getAdvancedStats'])->name('advanced-stats');
        Route::get('/bg-metrics', [DashboardController::class, 'bgMetrics'])->name('bg-metrics');
        Route::get('/customer-metrics', [DashboardController::class, 'customerMetrics'])->name('customer-metrics');
        Route::get('/top-customers-bg', [DashboardController::class, 'topCustomersByBg'])->name('top-customers-bg');
        Route::get('/recent-activities', [DashboardController::class, 'getRecentActivities'])->name('recent-activities');
        Route::get('/my-actions', [DashboardController::class, 'getMyActions'])->name('my-actions');
        Route::get('/logistic-stats', [DashboardController::class, 'getLogisticStats'])->name('logistic-stats');
        Route::get('/customer-classes', [DashboardController::class, 'getCustomerClassesData'])->name('customer-classes');
        Route::get('/class-stats-chart', [DashboardController::class, 'getClassStatsChart'])->name('class-stats-chart');
    });

    Route::post('/customers/{customer}/recall', [CustomerController::class, 'recall'])->name('customers.recall');
    Route::get('customers-template/download', [CustomerController::class, 'downloadTemplate'])->name('customers.template');
    Route::post('customers/import', [CustomerController::class, 'import'])->name('customers.import');
    Route::get('/customers/approval', [CustomerController::class, 'approvalPage'])->name('customers.approval');
    Route::get('/customers-approval/data', [CustomerController::class, 'getApprovalData'])->name('customers.approval.data');
    Route::post('/approvals/resend/{token}', [CustomerController::class, 'resendApprovalEmail'])->name('approvals.resend');

    Route::get('/customers/log', [CustomerController::class, 'logPage'])->name('customers.log');
    Route::get('/customers-log/data', [CustomerController::class, 'getLogData'])->name('customers.log.data');

    Route::get('/customers/reports', [CustomerController::class, 'reportsPage'])->name('customers.reports');
    Route::post('/customers/reports/print', [CustomerController::class, 'printMultipleReport'])->name('customers.reports.print');
    Route::get('/customers-reports/data', [CustomerController::class, 'getReportsData'])->name('customers.reports.data');

    Route::resource('customers', CustomerController::class);

    Route::get('/logistic-orders/export-pdf', [LogisticOrderController::class, 'exportDeliveryNotesPdf'])->name('logistic-orders.export-pdf');
    Route::resource('logistic-orders', LogisticOrderController::class);
    Route::get('/logistic-orders/export/delivery-notes', [LogisticOrderController::class, 'exportDeliveryNotes'])->name('logistic-orders.export-dn');
    Route::get('/logistic-orders/customer-dependencies/{customer}', [LogisticOrderController::class, 'getCustomerDependencies']);
    Route::get('/logistic-orders/fee/{distributor}/{customer}', [LogisticOrderController::class, 'getLogisticFee']);
    Route::post('/logistic-orders/{id}/cancel', [LogisticOrderController::class, 'cancel']);
    Route::put('/logistic-orders/{id}', [LogisticOrderController::class, 'update']);

    Route::resource('revision', RevisionController::class);
    Route::resource('account-groups', AccountGroupController::class);
    Route::resource('regions', RegionsController::class);
    Route::resource('branches', BranchController::class);
    Route::resource('sales', SalesController::class);
    Route::resource('tops', TOPController::class);
    Route::resource('customer-classes', CustomerClassController::class);
    Route::resource('tax', BgTaxController::class);
    Route::resource('limit-rules', BgLimitRuleController::class);
    Route::resource('logistic-fees', LogisticFeeController::class);
    Route::resource('distributors', DistributorController::class);
    Route::resource('customer-ship-tos', CustomerShipToController::class);

    Route::get('/logistic-fees-approval', [LogisticFeeController::class, 'approvalList'])->name('logistic-fees.approval.list');
    Route::get('/logistic-fees-approval/{id}', [LogisticFeeController::class, 'approvalDetail']);
    Route::post('/logistic-fees-approval/process/{id}', [LogisticFeeController::class, 'systemProcessApproval']);
    Route::post('/logistic-fees-approval/resend/{id}', [LogisticFeeController::class, 'resendEmail']);
    Route::get('/logistic-fees-log', [LogisticFeeController::class, 'logList'])->name('logistic-fees.log');

    Route::get('/get-customers-by-distributor/{distributor_id}', [DistributorController::class, 'getCustomersByDistributor']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ROUTE NOTIFIKASI
    Route::get('/notifications/count', [NotificationController::class, 'count'])->name('notifications.count');
    Route::get('/notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
    Route::post('/notifications/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read.all');
    Route::delete('/notifications/delete-all', [NotificationController::class, 'deleteAll'])->name('notifications.delete.all');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');


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
    Route::resource('permissions', PermissionController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('positions', PositionController::class);
    Route::get('roles/{roleId}/give-permissions', [RoleController::class, 'addPermissionToRole'])->name('roles.give-permissions');
    Route::post('roles/{roleId}/give-permissions', [RoleController::class, 'givePermissionToRole'])->name('roles.give-permission');
});


// Route untuk halaman tanpa autentikasi, seperti halaman login, register, dll. (Jika menggunakan Laravel Breeze atau Jetstream, biasanya sudah otomatis mengatur ini)
Route::get('/logistic-fees/approval/form/{token}/{action}', [LogisticFeeController::class, 'showApprovalForm'])->name('logistic-fees.approval.form');
Route::post('/logistic-fees/approval/process/{token}/{action}', [LogisticFeeController::class, 'processApproval'])->name('logistic-fees.approval.process');

Route::get('/public/logistic-order/{id}/detail', [LogisticOrderController::class, 'publicDetail'])->name('public.lo.detail')->middleware('signed');
Route::get('/public/logistic-order/{id}/download/{fromEmail?}', [LogisticOrderController::class, 'publicDownload'])->name('public.lo.download')->middleware('signed');

Route::get('/customers/approval/{token}', [CustomerController::class, 'viewApprovalPage'])->name('customers.view_approval');
Route::post('/customers/{customer}/approval-action', [CustomerController::class, 'approvalAction'])->name('customers.approval_action');
Route::get('/approval/process/{token}/{action}', [ApprovalProcessController::class, 'process'])->name('approval.process');
Route::get('/approval/form/{token}/{action}', [ApprovalProcessController::class, 'showForm'])->name('approval.form');
Route::post('/approval/submit/{token}', [ApprovalProcessController::class, 'submit'])->name('approval.submit');
Route::get('/public/download-doc/{bg_id}/{type}', [CustomerBgPortalController::class, 'downloadExpiringPdf'])->name('public.bg.download')->middleware('signed');


require __DIR__ . '/auth.php';
