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
use App\Http\Controllers\Requisition\ItemController;
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

    Route::get('/customers/approval/{token}', [CustomerController::class, 'viewApprovalPage'])->name('customers.view_approval');
    Route::post('/customers/{customer}/approval-action', [CustomerController::class, 'approvalAction'])->name('customers.approval_action');

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
