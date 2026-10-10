<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminActivityLogController;
use App\Http\Controllers\Admin\AdminArchiveController;

/*
|--------------------------------------------------------------------------
| OWNER
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Owner\DashboardController;
use App\Http\Controllers\Owner\CategoryController;
use App\Http\Controllers\Owner\SalesInventoryController;
use App\Http\Controllers\Owner\SupplierController;
use App\Http\Controllers\Owner\PurchaseOrderController;
use App\Http\Controllers\Owner\ReportController;
use App\Http\Controllers\Owner\ArchiveController;

/*
|--------------------------------------------------------------------------
| SECRETARY
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Secretary\SecretaryDashboardController;
use App\Http\Controllers\Secretary\SecretaryProductController;
use App\Http\Controllers\Secretary\SecretaryInventoryController;
use App\Http\Controllers\Secretary\SecretaryCustomerOrderController;
use App\Http\Controllers\Secretary\SecretaryStockCardController;
use App\Http\Controllers\Secretary\SecretaryReportController;
use App\Http\Controllers\Secretary\SecretaryArchiveController;

/*
|--------------------------------------------------------------------------
| CASHIER
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Cashier\CashierDashboardController;
use App\Http\Controllers\Cashier\CashierCustomerOrderController;
use App\Http\Controllers\Cashier\CashierPaymentController;
use App\Http\Controllers\Cashier\CashierLedgerController;
use App\Http\Controllers\Cashier\CashierReportController;
use App\Http\Controllers\Cashier\CashierArchiveController;

use App\Http\Middleware\EnsureUserIsActive;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| GUEST
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [
        LoginController::class,
        'showLogin',
    ])->name('login');

    Route::post('/login', [
        LoginController::class,
        'login',
    ])->name('login.submit');
});

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

Route::post('/logout', [
    LoginController::class,
    'logout',
])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USERS
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | GENERAL DASHBOARD REDIRECT
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', function () {

        return match (auth()->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'owner' => redirect()->route('owner.dashboard'),
            'secretary' => redirect()->route('secretary.dashboard'),
            'cashier' => redirect()->route('cashier.dashboard'),
            default => abort(403, 'Unauthorized role.'),
        };

    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    */

    Route::middleware([
        EnsureUserIsActive::class,
        'role:admin',
    ])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get(
                '/dashboard',
                [AdminDashboardController::class, 'index']
            )->name('dashboard');

            Route::get(
                '/users',
                [AdminUserController::class, 'index']
            )->name('users');

            Route::post(
                '/users',
                [AdminUserController::class, 'store']
            )->name('users.store');

            Route::put(
                '/users/{user}',
                [AdminUserController::class, 'update']
            )->name('users.update');

            Route::patch(
                '/users/{user}/activate',
                [AdminUserController::class, 'activate']
            )->name('users.activate');

            Route::patch(
                '/users/{user}/deactivate',
                [AdminUserController::class, 'deactivate']
            )->name('users.deactivate');

            Route::get(
                '/activity-logs',
                [AdminActivityLogController::class, 'index']
            )->name('activity-logs');

            Route::get(
                '/archive',
                [AdminArchiveController::class, 'index']
            )->name('archive');
        });

    /*
    |--------------------------------------------------------------------------
    | OWNER
    |--------------------------------------------------------------------------
    |
    | Owner has complete access to the operational system.
    |
    | Sales & Inventory combines:
    | - customer orders
    | - sales
    | - cashiering
    | - inventory
    | - product management (Add Product, opening stock)
    | - stock movement
    | - payments
    | - stock receiving
    | - adjustments
    | - customer returns
    | - damaged stock
    |
    */

    Route::middleware([
        EnsureUserIsActive::class,
        'role:owner',
    ])->group(function () {

        Route::get(
            '/owner/dashboard',
            [DashboardController::class, 'index']
        )->name('owner.dashboard');

        /*
        |--------------------------------------------------------------------------
        | SALES & INVENTORY
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/owner/sales-inventory',
            [SalesInventoryController::class, 'index']
        )->name('owner.sales-inventory');

        /*
        |--------------------------------------------------------------------------
        | ADD PRODUCT (product master + suppliers + opening stock / batch)
        |--------------------------------------------------------------------------
        |
        | There is no separate Owner Products page. Products are created
        | from the Inventory tab of Sales & Inventory.
        |
        */

        Route::post(
            '/owner/sales-inventory/products',
            [SalesInventoryController::class, 'productStore']
        )->name('owner.sales-inventory.product.store');

        Route::get(
            '/owner/sales-inventory/products/{product}',
            [SalesInventoryController::class, 'productInfo']
        )->name('owner.sales-inventory.product-info');

        Route::post(
            '/owner/sales-inventory/checkout',
            [SalesInventoryController::class, 'checkout']
        )->name('owner.sales-inventory.checkout');

        Route::post(
            '/owner/sales-inventory/orders',
            [SalesInventoryController::class, 'storeOrder']
        )->name('owner.sales-inventory.order');

        Route::post(
            '/owner/sales-inventory/orders/{customerOrder}/release',
            [SalesInventoryController::class, 'release']
        )->name('owner.sales-inventory.release');

        Route::post(
            '/owner/sales-inventory/orders/{customerOrder}/payment',
            [SalesInventoryController::class, 'payment']
        )->name('owner.sales-inventory.payment');

        Route::post(
            '/owner/sales-inventory/orders/{customerOrder}/recheck',
            [SalesInventoryController::class, 'recheck']
        )->name('owner.sales-inventory.recheck');

        Route::post(
            '/owner/sales-inventory/orders/{customerOrder}/confirm',
            [SalesInventoryController::class, 'confirm']
        )->name('owner.sales-inventory.confirm');

        Route::post(
            '/owner/sales-inventory/orders/{customerOrder}/cancel',
            [SalesInventoryController::class, 'cancel']
        )->name('owner.sales-inventory.cancel');

        Route::post(
            '/owner/sales-inventory/orders/{customerOrder}/void',
            [SalesInventoryController::class, 'voidOrder']
        )->name('owner.sales-inventory.void');

        /*
        |--------------------------------------------------------------------------
        | SUPPLIER DELIVERY / STOCK IN
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/owner/sales-inventory/stock-in',
            [SalesInventoryController::class, 'stockIn']
        )->name('owner.sales-inventory.stock-in');

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER RETURN
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/owner/sales-inventory/return',
            [SalesInventoryController::class, 'returnStock']
        )->name('owner.sales-inventory.return');

        /*
        |--------------------------------------------------------------------------
        | DAMAGED STOCK
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/owner/sales-inventory/damage',
            [SalesInventoryController::class, 'damageStock']
        )->name('owner.sales-inventory.damage');

        /*
        |--------------------------------------------------------------------------
        | PHYSICAL STOCK ADJUSTMENT
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/owner/sales-inventory/adjust',
            [SalesInventoryController::class, 'adjust']
        )->name('owner.sales-inventory.adjust');

        /*
        |--------------------------------------------------------------------------
        | OLD OWNER PAGES
        |--------------------------------------------------------------------------
        |
        | These remain as redirects so older links/bookmarks do not break.
        |
        */

        Route::get(
            '/owner/inventory',
            fn () => redirect()->route(
                'owner.sales-inventory',
                ['tab' => 'inventory']
            )
        )->name('owner.inventory');

        Route::get(
            '/owner/customer-orders',
            fn () => redirect()->route(
                'owner.sales-inventory',
                ['tab' => 'orders']
            )
        )->name('owner.customer-orders');

        Route::get(
            '/owner/stock-card',
            fn () => redirect()->route(
                'owner.sales-inventory',
                ['tab' => 'history']
            )
        )->name('owner.stock-card');

        /*
        |--------------------------------------------------------------------------
        | CATEGORIES
        |--------------------------------------------------------------------------
        |
        | Used by "+ Add Category" in the Add Product form.
        | Redirects back to Sales & Inventory (Inventory tab).
        |
        */

        Route::post(
            '/owner/categories',
            [CategoryController::class, 'store']
        )->name('owner.categories.store');

        /*
        |--------------------------------------------------------------------------
        | SUPPLIERS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/owner/suppliers',
            [SupplierController::class, 'index']
        )->name('owner.suppliers');

        Route::post(
            '/owner/suppliers',
            [SupplierController::class, 'store']
        )->name('owner.suppliers.store');

        Route::put(
            '/owner/suppliers/{supplier}',
            [SupplierController::class, 'update']
        )->name('owner.suppliers.update');

        Route::patch(
            '/owner/suppliers/{supplier}/archive',
            [SupplierController::class, 'archive']
        )->name('owner.suppliers.archive');

        /*
        |--------------------------------------------------------------------------
        | PURCHASE ORDERS
        |--------------------------------------------------------------------------
        |
        | Workflow:
        |   Draft   -> submit  -> Pending
        |   Pending -> approve -> Approved   (does NOT change inventory)
        |   Draft / Pending -> cancel -> Cancelled
        |   Inventory increases only through
        |   owner.sales-inventory.stock-in (actual supplier delivery).
        |
        | {purchaseOrder} is the numeric database id (route-model binding),
        | not the PO number (e.g. PO-2026-0007).
        |
        */

        Route::get(
            '/owner/purchase-orders',
            [PurchaseOrderController::class, 'index']
        )->name('owner.purchase-orders');

        Route::post(
            '/owner/purchase-orders',
            [PurchaseOrderController::class, 'store']
        )->name('owner.purchase-orders.store');

        Route::patch(
            '/owner/purchase-orders/{purchaseOrder}/submit',
            [PurchaseOrderController::class, 'submit']
        )->name('owner.purchase-orders.submit');

        Route::patch(
            '/owner/purchase-orders/{purchaseOrder}/approve',
            [PurchaseOrderController::class, 'approve']
        )->name('owner.purchase-orders.approve');

        Route::patch(
            '/owner/purchase-orders/{purchaseOrder}/cancel',
            [PurchaseOrderController::class, 'cancel']
        )->name('owner.purchase-orders.cancel');

        /*
        |--------------------------------------------------------------------------
        | REPORTS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/owner/reports',
            [ReportController::class, 'index']
        )->name('owner.reports');

        /*
        |--------------------------------------------------------------------------
        | ARCHIVE
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/owner/archive',
            [ArchiveController::class, 'index']
        )->name('owner.archive');
    });

    /*
    |--------------------------------------------------------------------------
    | SECRETARY
    |--------------------------------------------------------------------------
    |
    | Secretary retains operational inventory/order access.
    | Owner does not depend on Secretary because Owner has the complete
    | Sales & Inventory module above.
    |
    */

    Route::middleware([
        EnsureUserIsActive::class,
        'role:secretary',
    ])->prefix('secretary')->name('secretary.')->group(function () {

        Route::get(
            '/dashboard',
            [SecretaryDashboardController::class, 'index']
        )->name('dashboard');

        Route::get(
            '/products',
            [SecretaryProductController::class, 'index']
        )->name('products');

        Route::get(
            '/inventory',
            [SecretaryInventoryController::class, 'index']
        )->name('inventory');

        Route::post(
            '/inventory/stock-in',
            [SecretaryInventoryController::class, 'stockIn']
        )->name('inventory.stock-in');

        Route::post(
            '/inventory/stock-out',
            [SecretaryInventoryController::class, 'stockOut']
        )->name('inventory.stock-out');

        Route::post(
            '/inventory/adjust',
            [SecretaryInventoryController::class, 'adjust']
        )->name('inventory.adjust');

        Route::get(
            '/customer-orders',
            [SecretaryCustomerOrderController::class, 'index']
        )->name('customer-orders');

        Route::patch(
            '/customer-orders/{customerOrder}/check-inventory',
            [SecretaryCustomerOrderController::class, 'checkInventory']
        )->name('customer-orders.check-inventory');

        Route::get(
            '/stock-card',
            [SecretaryStockCardController::class, 'index']
        )->name('stock-card');

        Route::get(
            '/reports',
            [SecretaryReportController::class, 'index']
        )->name('reports');

        Route::get(
            '/archive',
            [SecretaryArchiveController::class, 'index']
        )->name('archive');
    });

    /*
    |--------------------------------------------------------------------------
    | CASHIER
    |--------------------------------------------------------------------------
    |
    | Cashier remains available for later payment/ledger work.
    |
    */

    Route::middleware([
        EnsureUserIsActive::class,
        'role:cashier',
    ])
        ->prefix('cashier')
        ->name('cashier.')
        ->group(function () {

            Route::get(
                '/dashboard',
                [CashierDashboardController::class, 'index']
            )->name('dashboard');

            Route::get(
                '/customer-orders',
                [CashierCustomerOrderController::class, 'index']
            )->name('customer-orders');

            Route::get(
                '/customer-orders/{customerOrder}',
                [CashierCustomerOrderController::class, 'show']
            )->name('customer-orders.show');

            Route::get(
                '/payments',
                [CashierPaymentController::class, 'index']
            )->name('payments');

            Route::post(
                '/payments',
                [CashierPaymentController::class, 'store']
            )->name('payments.store');

            Route::get(
                '/ledger',
                [CashierLedgerController::class, 'index']
            )->name('ledger');

            Route::get(
                '/ledger/{customerOrder}',
                [CashierLedgerController::class, 'show']
            )->name('ledger.show');

            Route::get(
                '/reports',
                [CashierReportController::class, 'index']
            )->name('reports');

            Route::get(
                '/archive',
                [CashierArchiveController::class, 'index']
            )->name('archive');
        });
});