<?php

use App\Http\Controllers\InventoryActivityLogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\SalesOrderController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\InventoryRawMatsController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\PurchaseOrderController;
use App\Http\Controllers\Api\PurchaseOrderItemController;
use App\Http\Controllers\Api\ForecastController;
use App\Http\Controllers\Api\ProductionOutputController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\FileUploadController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\SupplierOfferController;
use App\Http\Controllers\Api\RawMaterialSupplierController;
use App\Http\Controllers\Api\ReturnToVendorController;
use App\Http\Controllers\DisposalController;
use App\Http\Controllers\DisposalProductController;
use App\Http\Controllers\DisposalRawMatController;
use App\Http\Controllers\Api\AnalyticsController;

// ============================
// AUTHENTICATION ROUTES
// ============================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// ============================
// SALES ORDERS
// ============================
Route::prefix('sales-orders')->group(function () {
    Route::get('cso-count', [SalesOrderController::class, 'getCsoCount']);
    Route::get('rtv-count', [SalesOrderController::class, 'getRtvCount']);
    Route::get('disposal-count', [SalesOrderController::class, 'getDisposalCount']);
    Route::get('count', [SalesOrderController::class, 'totalCount']);

    Route::get('most-selling', [SalesOrderController::class, 'mostSelling']);
    Route::get('top-products', [SalesOrderController::class, 'topProducts']); 

    Route::get('/sales-orders-by-year', [SalesOrderController::class, 'getSalesByYear']);
    Route::get('/', [SalesOrderController::class, 'index']);
    Route::post('/', [SalesOrderController::class, 'store']);
    Route::get('{id}', [SalesOrderController::class, 'show']);
    Route::put('{id}', [SalesOrderController::class, 'update']);
    Route::delete('{id}', [SalesOrderController::class, 'destroy']);
    Route::delete('/', [SalesOrderController::class, 'destroy']);
    Route::get('{id}/pdf', [SalesOrderController::class, 'generatePdf']);
    Route::put('{id}/status', [SalesOrderController::class, 'updateStatus']);
    Route::put('{id}/mark-delivered', [SalesOrderController::class, 'markDelivered']);
    Route::get('/sales-orders', [SalesOrderController::class, 'index']);

});

// ============================
// INVENTORY
// ============================
Route::prefix('inventories')->group(function () {
    Route::get('/inventories-by-year', [InventoryController::class, 'inventoryByYear']);
    Route::get('/', [InventoryController::class, 'index']);
    Route::post('/', [InventoryController::class, 'store']);
    Route::put('{id}', [InventoryController::class, 'update']);
    Route::post('{id}/add', [InventoryController::class, 'addQuantity']);
    Route::post('deduct', [InventoryController::class, 'deduct']);
    Route::post('receive', [InventoryController::class, 'receiveItem']);
    Route::put('{id}/update-alert', [InventoryController::class, 'updateAlert']);
    Route::put('{id}/update-price', [InventoryController::class, 'updatePrice']);
});

Route::prefix('inventory_rawmats')->group(function () {
    Route::get('/', [InventoryRawMatsController::class, 'index']);
    Route::post('/', [InventoryRawMatsController::class, 'store']);
    Route::put('{id}', [InventoryRawMatsController::class, 'update']);
    Route::post('{id}/add', [InventoryRawMatsController::class, 'addQuantity']);
    Route::post('{id}/deduct', [InventoryRawMatsController::class, 'deduct']);
    Route::post('receive', [InventoryRawMatsController::class, 'receiveItem']);
    Route::put('{id}/update-alert', [InventoryRawMatsController::class, 'updateAlert']);
    Route::get('{id}/suppliers', [InventoryRawMatsController::class, 'getSuppliers']);
});

// ============================
// CUSTOMERS
// ============================
Route::prefix('customers')->group(function () {
    Route::get('/', [CustomerController::class, 'index']);
    Route::post('/', [CustomerController::class, 'store']);
    Route::get('{id}', [CustomerController::class, 'show']);
    Route::put('{id}', [CustomerController::class, 'update']);
    Route::patch('{id}', [CustomerController::class, 'update']);
    Route::delete('{id}', [CustomerController::class, 'destroy']);
    Route::delete('bulk-delete', [CustomerController::class, 'bulkDelete']);
});

// ============================
// ITEMS
// ============================
Route::apiResource('items', ItemController::class);

// ============================
// PURCHASE ORDERS
// ============================
Route::prefix('purchase-orders')->group(function () {
    Route::get('/', [PurchaseOrderController::class, 'index']);
    Route::post('/', [PurchaseOrderController::class, 'store']);
    Route::put('{id}', [PurchaseOrderController::class, 'update']);
    Route::delete('{id}', [PurchaseOrderController::class, 'destroy']);
    Route::post('{id}/receive', [PurchaseOrderController::class, 'receiveItems']);
    Route::get('{id}/delivery-note', [PurchaseOrderController::class, 'generateDeliveryNote'])->name('purchase_orders.delivery_note');

    Route::get('pending-count', [PurchaseOrderController::class, 'getPendingCount']);
    Route::get('partial-count', [PurchaseOrderController::class, 'getPartialCount']);
    Route::get('completed-count', [PurchaseOrderController::class, 'getCompletedCount']);
});

// Purchase Order Items
Route::prefix('purchase-order-items')->group(function () {
    Route::post('/', [PurchaseOrderItemController::class, 'store']);
    Route::put('{id}', [PurchaseOrderItemController::class, 'update']);
    Route::get('{purchaseOrderId}', [PurchaseOrderItemController::class, 'getByPurchaseOrder']);
});

// ============================
// PURCHASE RECEIPTS (only via PurchaseOrderController)
// ============================
Route::get('/purchase-receipts', [PurchaseOrderController::class, 'getAllReceivedItems']);

// ============================
// USERS
// ============================
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('{employeeID}', [UserController::class, 'showByEmployeeID']);
    Route::post('/', [UserController::class, 'store']);
    Route::put('{id}', [UserController::class, 'update']);
    Route::delete('{id}', [UserController::class, 'destroy']);
});

// ============================
// FORECAST
// ============================
Route::get('historical-sales', [ForecastController::class, 'historicalSales']);
Route::get('forecast', [ForecastController::class, 'forecast']);

// ============================
// PRODUCTION OUTPUT
// ============================
Route::prefix('production-output')->group(function () {
    Route::get('/', [ProductionOutputController::class, 'index']);
    Route::post('/', [ProductionOutputController::class, 'store']);
    Route::post('add', [ProductionOutputController::class, 'store']);
    Route::delete('/', [ProductionOutputController::class, 'destroyMany']);
    Route::get('raw-materials/{product}', [ProductionOutputController::class, 'getRawMaterialsByProduct']);
    Route::get('details/{batch_number}', [ProductionOutputController::class, 'showDetails']);
});

// ============================
// RETURN TO VENDOR
// ============================
Route::prefix('return-to-vendor')->group(function () {
    Route::get('/', [ReturnToVendorController::class, 'index']);
    Route::post('/', [ReturnToVendorController::class, 'store']);
    Route::put('{id}', [ReturnToVendorController::class, 'update']);
    Route::delete('/', [ReturnToVendorController::class, 'destroy']);
    Route::get('count', [ReturnToVendorController::class, 'count']);
});

// ============================
// DISPOSALS
// ============================
Route::prefix('disposals')->group(function () {
    Route::get('pdf', [ReportController::class, 'disposalReportPDF'])->name('disposals.pdf');
    Route::get('/', [DisposalController::class, 'index']);
    Route::post('/', [DisposalController::class, 'store']);
    Route::delete('/', [DisposalController::class, 'destroy']);
    Route::post('{id}/dispose', [DisposalController::class, 'markDisposed']);
    Route::get('{id}', [DisposalController::class, 'show']);
});

Route::prefix('disposal-products')->group(function () {
    Route::get('/', [DisposalProductController::class, 'index']);
    Route::get('{id}', [DisposalProductController::class, 'show']);
    Route::post('/', [DisposalProductController::class, 'store']);
    Route::put('{id}', [DisposalProductController::class, 'update']);
    Route::delete('{id}', [DisposalProductController::class, 'destroy']);
    Route::delete('/', [DisposalProductController::class, 'destroyMultiple']);
});

Route::prefix('disposal-rawmats')->group(function () {
    Route::get('/', [DisposalRawMatController::class, 'index']);
    Route::get('{id}', [DisposalRawMatController::class, 'show']);
    Route::post('/', [DisposalRawMatController::class, 'store']);
    Route::put('{id}', [DisposalRawMatController::class, 'update']);
    Route::delete('{id}', [DisposalRawMatController::class, 'destroy']);
    Route::delete('/', [DisposalRawMatController::class, 'destroyMultiple']);
});

// ============================
// REPORTS
// ============================
Route::get('reports/sales', [ReportController::class, 'salesReport']);
Route::get('sales-report/pdf', [ReportController::class, 'salesReportPDF']);
Route::get('inventory-report-pdf', [ReportController::class, 'inventoryReportPDF']);
Route::get('disposals/pdf', [ReportController::class, 'disposalReportPDF'])->name('disposals.pdf');
Route::get('/return-to-vendor/pdf', [ReportController::class, 'returnToVendorReportPDF']);
Route::get('purchase-order-report-pdf', [ReportController::class, 'purchaseOrderReportPDF']);

// ============================
// FILE UPLOAD
// ============================
Route::post('upload', [FileUploadController::class, 'upload']);

// ============================
// SUPPLIERS & OFFERS
// ============================
Route::get('suppliers', [SupplierController::class, 'index']);
Route::post('suppliers', [SupplierController::class, 'store']);
Route::get('suppliers/{id}/offers', [SupplierController::class, 'getOffers']);
Route::get('supplier-offers', [SupplierOfferController::class, 'index']);
Route::post('suppliers/{supplier}/offers', [SupplierOfferController::class, 'store']);
Route::put('/suppliers/{supplier}/offers/{rawmat}', [SupplierOfferController::class, 'update']);

// ============================
// RAW MATERIAL SUPPLIERS
// ============================
Route::get('raw-materials/by-product/{product}', [RawMaterialSupplierController::class, 'getByProduct']);
Route::get('inventory-activity-logs', [InventoryActivityLogController::class, 'index']);

Route::get('analytics/inventory', [AnalyticsController::class, 'inventoryTrends']);