<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserStatusController;
use App\Http\Controllers\BatchOperationController;

// Public routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Admin-only registration routes
    Route::get('/admin/register', [RegisterController::class, 'showRegistrationForm'])->name('admin.register');
    Route::post('/admin/register', [RegisterController::class, 'register'])->name('admin.register.submit');

    // Categories
    Route::resource('categories', CategoryController::class);

    // Suppliers
    Route::resource('suppliers', SupplierController::class);

    // Products
    Route::resource('products', ProductController::class);

    // Inventory
    Route::resource('inventory', InventoryController::class);

    // Purchase Orders
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::patch('/purchase-orders/{purchase_order}/mark-as-ordered', [PurchaseOrderController::class, 'markAsOrdered'])->name('purchase-orders.mark-as-ordered');
    Route::get('/purchase-orders/{purchase_order}/receive', [PurchaseOrderController::class, 'showReceiveForm'])->name('purchase-orders.receive-form');
    Route::patch('/purchase-orders/{purchase_order}/receive', [PurchaseOrderController::class, 'receiveOrder'])->name('purchase-orders.receive');
    Route::patch('/purchase-orders/{purchase_order}/cancel', [PurchaseOrderController::class, 'cancelOrder'])->name('purchase-orders.cancel');

    // Orders
    Route::resource('orders', OrderController::class);
    Route::patch('/orders/{order}/process', [OrderController::class, 'processOrder'])->name('orders.process');
    Route::patch('/orders/{order}/complete', [OrderController::class, 'completeOrder'])->name('orders.complete');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancelOrder'])->name('orders.cancel');

    // Users (admin check is done in the controller)
    Route::resource('users', UserController::class);
    Route::patch('/users/{user}/toggle-status', [UserStatusController::class, 'toggleStatus'])->name('users.toggle-status');

    // Roles (admin check is done in the controller)
    Route::resource('roles', RoleController::class);

    // Reports
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/inventory', [ReportsController::class, 'inventory'])->name('reports.inventory');
    Route::get('/reports/sales', [ReportsController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/purchases', [ReportsController::class, 'purchases'])->name('reports.purchases');
    Route::get('/reports/profit-loss', [ReportsController::class, 'profitLoss'])->name('reports.profit-loss');
    Route::get('/reports/export-csv', [ReportsController::class, 'exportCsv'])->name('reports.export-csv');
    Route::get('/export/products', [ReportsController::class, 'exportProducts'])->name('export.products');
    Route::get('/export/suppliers', [ReportsController::class, 'exportSuppliers'])->name('export.suppliers');
    Route::get('/export/inventory', [ReportsController::class, 'exportInventory'])->name('export.inventory');
    Route::get('/export/purchase-orders', [ReportsController::class, 'exportPurchaseOrders'])->name('export.purchase-orders');
    Route::get('/export/orders', [ReportsController::class, 'exportOrders'])->name('export.orders');
    Route::get('/export/categories', [ReportsController::class, 'exportCategories'])->name('export.categories');

    // User Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Batch Operations
    Route::get('/batch/inventory', [BatchOperationController::class, 'showBatchInventoryForm'])->name('batch.inventory');
    Route::post('/batch/inventory', [BatchOperationController::class, 'processBatchInventory'])->name('batch.inventory.process');
    Route::get('/batch/prices', [BatchOperationController::class, 'showBatchPriceForm'])->name('batch.prices');
    Route::post('/batch/prices', [BatchOperationController::class, 'processBatchPrice'])->name('batch.prices.process');
});