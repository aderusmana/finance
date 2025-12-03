<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Customer\AccountGroupController;
use App\Http\Controllers\Master\ApprovalPathController;
use App\Http\Controllers\Customer\BranchController;
use App\Http\Controllers\Customer\CustomerClassController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Master\DepartmentController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Customer\RegionsController;
use App\Http\Controllers\Customer\SalesController;
use App\Http\Controllers\Customer\TOPController;
use App\Http\Controllers\Master\PermissionController;
use App\Http\Controllers\Master\RevisionController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Master\PositionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Requisition\ComplainApprovalController;
use App\Http\Controllers\Requisition\ComplainController;
use App\Http\Controllers\Requisition\ComplainLogController;
use App\Http\Controllers\Requisition\FreeGoodsController;
use App\Http\Controllers\Requisition\ItemController;
use App\Http\Controllers\Requisition\SampleController;
use Carbon\Carbon;
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

// --- mailing dan approval proses complain ---
Route::get('/approval', [ComplainController::class, 'processApproval'])->name('approval.process');
Route::get('/complain/approval-direct', [ComplainController::class, 'processApproval'])->name('approval.process.direct');
Route::get('/complain/approval/review', [ComplainController::class, 'showReviewPage'])->name('complain.approval.review');
Route::post('/complain/approval/process', [ComplainController::class, 'processApproval'])->name('complain.approval.process');
Route::get('/complain/warehouse/approval', [ComplainController::class, 'processWarehouseApproval'])->name('complain.warehouse.approval');
Route::get('/comclaim/warehouse/review', [ComplainController::class, 'showWarehouseReviewPage'])->name('complain.warehouse.review');
Route::post('/comclaim/warehouse/process', [ComplainController::class, 'processWarehouseApproval'])->name('complain.warehouse.process');

// Approval Link dari Email (Sample Requisition)
Route::get('/approval/response/{token}', [SampleController::class, 'showResponseForm'])->name('approval.response');
Route::post('/approvals/resend/{token}', [SampleController::class, 'resendApprovalEmail'])->name('approvals.resend');
Route::post('/approval/process', [SampleController::class, 'processApproval'])->name('approval-sample.process-form');
Route::get('/approval/success', [SampleController::class, 'showSuccessPage'])->name('approval.success');

// Approval Link dari Email (Free Goods Requisition)
Route::get('/fg-approval/response/{token}', [FreeGoodsController::class, 'showResponseForm'])->name('fg.approval.response');
Route::post('/fg-approval/process', [FreeGoodsController::class, 'processApproval'])->name('fg.approval.process');
Route::get('/fg-approval/success', [FreeGoodsController::class, 'showSuccessPage'])->name('fg.approval.success');

Route::middleware('auth')->group(function () {
    Route::prefix('dashboard/data')->name('dashboard.data.')->group(function () {
        // Legacy endpoints repurposed for BG/Customer data
        Route::get('/metric-counts', [DashboardController::class, 'getMetricCounts'])->name('metric-counts');
        Route::get('/monthly-stats', [DashboardController::class, 'getMonthlyStats'])->name('monthly-stats');
        Route::get('/top-items', [DashboardController::class, 'getTopItems'])->name('top-items');
        Route::get('/top-customers', [DashboardController::class, 'getTopCustomers'])->name('top-customers');
        Route::get('/recent-activities', [DashboardController::class, 'getRecentActivities'])->name('recent-activities');
        Route::get('/my-actions', [DashboardController::class, 'getMyActions'])->name('my-actions');
        Route::get('/available-years', [DashboardController::class, 'getAvailableYearsApi'])->name('available-years');

        // New BG/Customer specific endpoints used by updated dashboard view
        Route::get('/bg-metrics', [DashboardController::class, 'bgMetrics'])->name('bg-metrics');
        Route::get('/customer-metrics', [DashboardController::class, 'customerMetrics'])->name('customer-metrics');
        Route::get('/recent-bgs', [DashboardController::class, 'recentBgs'])->name('recent-bgs');
        Route::get('/top-customers-bg', [DashboardController::class, 'topCustomersByBg'])->name('top-customers-bg');
    });

    Route::resource('revision', RevisionController::class);
    Route::resource('account-groups', AccountGroupController::class);
    Route::resource('regions', RegionsController::class);
    Route::resource('branches', BranchController::class);
    Route::resource('sales', SalesController::class);
    Route::resource('tops', TOPController::class);
    Route::resource('customer-classes', CustomerClassController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ROUTE NOTIFIKASI
    Route::get('/notifications/count', [NotificationController::class, 'count'])->name('notifications.count');
    Route::get('/notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
    Route::post('/notifications/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read.all');


    // --- SAMPLE REQUISITION ROUTES ---
    Route::get('/sample-form/approval', [SampleController::class, 'approvalPage'])->name('sample-form.approval');
    Route::get('/sample-approval/data', [SampleController::class, 'getApprovalData'])->name('sample.approval.data');

    Route::get('/sample-form-reports', [SampleController::class, 'reportsPage'])->name('sample-form.reports');
    Route::get('/sample-reports/data', [SampleController::class, 'getReportsData'])->name('sample.reports.data');
    Route::post('/sample-report/print', [SampleController::class, 'printMultipleReport'])->name('report_sample.print');
    Route::get('/sample-report/{id}', [SampleController::class, 'printReport'])->name('sample.report');

    Route::get('/sample-form/log', [SampleController::class, 'logPage'])->name('sample-form.log');
    Route::get('/sample-log/data', [SampleController::class, 'getLogData'])->name('sample.log.data');

    Route::resource('sample-form', SampleController::class);

    Route::get('/sample-data', [SampleController::class, 'getData'])->name('sample.data');
    Route::post('/sample-form/{id}/recall', [SampleController::class, 'recallRequisition'])->name('sample.recall');
    Route::post('/get-products-by-material-types', [SampleController::class, 'getProductsByMaterialTypes'])->name('sample.getProductsByMaterialTypes');
    Route::post('/get-item-details-by-products', [SampleController::class, 'getItemDetailsByProducts'])->name('sample.getItemDetailsByProducts');
    Route::get('/get-all-item-masters', [SampleController::class, 'getAllItemMasters'])->name('sample.getAllItemMasters');


    // --- FREE GOODS REQUISITION ROUTES ---
    Route::prefix('freegoods-form')->name('freegoods-form.')->group(function () {
        Route::get('/', [FreeGoodsController::class, 'index'])->name('index');
        Route::get('/fg-approval', [FreeGoodsController::class, 'approvalPage'])->name('approval');
        Route::get('/fg-reports', [FreeGoodsController::class, 'reports'])->name('reports');
        Route::get('/fg-log', [FreeGoodsController::class, 'log'])->name('log');

        // [FIX] Menambahkan route untuk recall yang hilang
        Route::post('/{id}/recall', [FreeGoodsController::class, 'recallRequisition'])->name('recall');

        Route::resource('/', FreeGoodsController::class)->except(['index'])->parameters(['' => 'id']);
    });

    Route::name('freegoods.')->group(function () {
        Route::get('/freegoods-data', [FreeGoodsController::class, 'getData'])->name('data');
        Route::get('/freegoods-approval/data', [FreeGoodsController::class, 'getApprovalData'])->name('approval.data');
        Route::get('/freegoods-reports/data', [FreeGoodsController::class, 'getReportData'])->name('reports.data');
        Route::get('/freegoods-log/data', [FreeGoodsController::class, 'getLogData'])->name('log.data');
        Route::get('/freegoods/get-next-number', [FreeGoodsController::class, 'getNextFgNumber'])->name('get-next-number');
        Route::get('/get-all-item-masters-fg', [FreeGoodsController::class, 'getAllItemMasters'])->name('getAllItemMasters');
        Route::post('/freegoods-form/reports/print-batch', [FreeGoodsController::class, 'printBatch'])->name('report.print.batch');
    });


    // ! complain routes
    Route::prefix('complain')->group(function () {
        // * complain form
        Route::resource('complain-form', ComplainController::class);

        // * complain data
        Route::get('/getSerial', [ComplainController::class, 'getSerial'])->name('get.serial');
        Route::get('/getComplainData', [ComplainController::class, 'getData'])->name('get.complain.data');
        Route::get('/getProductList', [ComplainController::class, 'getProductList'])->name('get.product.list');
        Route::get('/getCostumerList', [ComplainController::class, 'getCustomerList'])->name('customers.list');
        Route::get('/getformdetail/{id}', [ComplainController::class, 'getFormDetail'])->name('get.form.detail');
        Route::post('/upload-payment-proof', [ComplainController::class, 'uploadPaymentProof'])->name('upload.payment.proof');

        // * warehouse approval
        Route::get('/test-warehouse/{id}', [ComplainController::class, 'testWarehouseTracking'])->name('complain.test.warehouse');

        // * complain approval
        Route::get('/complain-approval', [ComplainApprovalController::class, 'index'])->name('complain.approval');
        Route::get('/getapproverdata/{id?}', [ComplainApprovalController::class, 'getData'])->name('get.approver.data');
        Route::post('/approval/resend/{token}', [ComplainApprovalController::class, 'resendApprovalEmail'])->name('complain.approval.resend');

        // * complain reports
        Route::get('/complain-reports', [ComplainController::class, 'reports'])->name('complain.reports');
        Route::post('/report/print-bulk', [ComplainController::class, 'printBulkReport'])->name('report.print.bulk');

        // * complain log
        Route::get('/complain-log', [ComplainLogController::class, 'index'])->name('complain.log');
        Route::get('/log.data', [ComplainLogController::class, 'getData'])->name('complain.log.data');
    });

    // --- MASTER DATA ROUTES ---
    Route::prefix('master')->group(function () {
        Route::get('items', [ItemController::class, 'indexMaster'])->name('items.indexMaster');
        Route::post('items', [ItemController::class, 'store'])->name('items.store');
        Route::get('items/{id}/edit', [ItemController::class, 'edit'])->name('items.edit');
        Route::put('items/{id}', [ItemController::class, 'update'])->name('items.update');
        Route::delete('items/{id}', [ItemController::class, 'destroy'])->name('items.destroy');
        // Item details endpoints handled by ItemController
        // All item details (no specific item)
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
});


Route::group(['middleware' => ['role:super-admin|admin']], function () {

    Route::resource('users', UserController::class);
    Route::get('/users-data', [UserController::class, 'getData'])->name('users.data');
    Route::resource('departments', DepartmentController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('positions', PositionController::class);

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
