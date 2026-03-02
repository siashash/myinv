<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookReportController;
use App\Http\Controllers\AcHeadController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchasePaymentController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\SalesReceiptController;
use App\Http\Controllers\SalesReturnController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\StockReportController;
use App\Http\Controllers\SundryDebtorReportController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('managed.auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/subcategories', [SubCategoryController::class, 'index'])->name('subcategories.index');
    Route::post('/subcategories', [SubCategoryController::class, 'store'])->name('subcategories.store');
    Route::get('/subcategories/{subcategory}/edit', [SubCategoryController::class, 'edit'])->name('subcategories.edit');
    Route::put('/subcategories/{subcategory}', [SubCategoryController::class, 'update'])->name('subcategories.update');
    Route::delete('/subcategories/{subcategory}', [SubCategoryController::class, 'destroy'])->name('subcategories.destroy');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('/purchases/{purchase}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
    Route::put('/purchases/{purchase}', [PurchaseController::class, 'update'])->name('purchases.update');
    Route::delete('/purchases/{purchase}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');
    Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
    Route::post('/sales', [SalesController::class, 'store'])->name('sales.store');
    Route::get('/sales/{sale}/edit', [SalesController::class, 'edit'])->name('sales.edit');
    Route::put('/sales/{sale}', [SalesController::class, 'update'])->name('sales.update');
    Route::delete('/sales/{sale}', [SalesController::class, 'destroy'])->name('sales.destroy');
    Route::get('/purchases-details', [PurchaseController::class, 'details'])->name('purchases.details');
    Route::get('/reports/stock', [StockReportController::class, 'index'])->name('reports.stock');
    Route::get('/reports/sales', [SalesReportController::class, 'index'])->name('reports.sales');
    Route::get('/reports/account-book', [BookReportController::class, 'account'])->name('reports.account-book');
    Route::get('/reports/cash-book', [BookReportController::class, 'cash'])->name('reports.cash-book');
    Route::get('/reports/bank-book', [BookReportController::class, 'bank'])->name('reports.bank-book');
    Route::get('/reports/sundry-debtors', [SundryDebtorReportController::class, 'index'])->name('reports.sundry-debtors');
    Route::get('/reports/sundry-creditors', [SundryDebtorReportController::class, 'index'])->name('reports.sundry-creditors');

    Route::get('/purchase-payments', [PurchasePaymentController::class, 'index'])->name('purchase-payments.index');
    Route::post('/purchase-payments', [PurchasePaymentController::class, 'store'])->name('purchase-payments.store');
    Route::delete('/purchase-payments/{payment}', [PurchasePaymentController::class, 'cancel'])->name('purchase-payments.cancel');
    Route::get('/sales-receipts', [SalesReceiptController::class, 'index'])->name('sales-receipts.index');
    Route::get('/purchase-returns', [PurchaseReturnController::class, 'index'])->name('purchase-returns.index');
    Route::post('/purchase-returns', [PurchaseReturnController::class, 'store'])->name('purchase-returns.store');
    Route::get('/sales-returns', [SalesReturnController::class, 'index'])->name('sales-returns.index');
    Route::post('/sales-returns', [SalesReturnController::class, 'store'])->name('sales-returns.store');

    Route::get('/units', [UnitController::class, 'index'])->name('units.index');
    Route::post('/units', [UnitController::class, 'store'])->name('units.store');
    Route::get('/units/{unit}/edit', [UnitController::class, 'edit'])->name('units.edit');
    Route::put('/units/{unit}', [UnitController::class, 'update'])->name('units.update');
    Route::delete('/units/{unit}', [UnitController::class, 'destroy'])->name('units.destroy');

    Route::get('/ajax/categories/{category}/subcategories', [ProductController::class, 'subCategories'])->name('categories.subcategories');

    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    Route::get('/ac-heads', [AcHeadController::class, 'index'])->name('ac_heads.index');
    Route::post('/ac-heads', [AcHeadController::class, 'store'])->name('ac_heads.store');
    Route::get('/ac-heads/{acHead}/edit', [AcHeadController::class, 'edit'])->name('ac_heads.edit');
    Route::put('/ac-heads/{acHead}', [AcHeadController::class, 'update'])->name('ac_heads.update');
    Route::delete('/ac-heads/{acHead}', [AcHeadController::class, 'destroy'])->name('ac_heads.destroy');

    Route::prefix('user-management')->name('um.')->middleware('managed.admin')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::get('/users/{managedUser}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{managedUser}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{managedUser}', [UserManagementController::class, 'destroy'])->name('users.destroy');

        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
        Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
        Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

        Route::get('/role-permissions', [RolePermissionController::class, 'index'])->name('role_permissions.index');
        Route::post('/role-permissions', [RolePermissionController::class, 'store'])->name('role_permissions.store');
        Route::get('/role-permissions/{roleId}/{permissionId}/edit', [RolePermissionController::class, 'edit'])->name('role_permissions.edit');
        Route::put('/role-permissions/{roleId}/{permissionId}', [RolePermissionController::class, 'update'])->name('role_permissions.update');
        Route::delete('/role-permissions/{roleId}/{permissionId}', [RolePermissionController::class, 'destroy'])->name('role_permissions.destroy');
    });
});
