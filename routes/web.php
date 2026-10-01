<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| ADMIN CONTROLLERS
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminActivityLogController;
use App\Http\Controllers\Admin\AdminArchiveController;

/*
|--------------------------------------------------------------------------
| OWNER CONTROLLERS
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Owner\DashboardController;
use App\Http\Controllers\Owner\ProductController;
use App\Http\Controllers\Owner\CategoryController;
use App\Http\Controllers\Owner\InventoryController;
use App\Http\Controllers\Owner\SupplierController;
use App\Http\Controllers\Owner\CustomerOrderController;
use App\Http\Controllers\Owner\PurchaseOrderController;
use App\Http\Controllers\Owner\StockCardController;
use App\Http\Controllers\Owner\ReportController;
use App\Http\Controllers\Owner\ArchiveController;

/*
|--------------------------------------------------------------------------
| SECRETARY CONTROLLERS
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
| CASHIER CONTROLLERS
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

    Route::get(
        '/login',
        [LoginController::class, 'showLogin']
    )->name('login');

    Route::post(
        '/login',
        [LoginController::class, 'login']
    )->name('login.submit');

});


/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

Route::post(
    '/logout',
    [LoginController::class, 'logout']
)->middleware('auth')
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

            'admin' =>
                redirect()->route('admin.dashboard'),

            'owner' =>
                redirect()->route('owner.dashboard'),

            'secretary' =>
                redirect()->route('secretary.dashboard'),

            'cashier' =>
                redirect()->route('cashier.dashboard'),

            default =>
                abort(403, 'Unauthorized role.'),

        };

    })->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    |
    | Admin responsibilities:
    |
    | - Monitor system/account information
    | - Manage user accounts
    | - Assign system roles
    | - Activate/deactivate users
    | - View activity logs
    | - View historical/inactive records
    |
    | Admin does NOT manage the operational inventory workflow.
    |
    */

    Route::middleware([
        EnsureUserIsActive::class,
        'role:admin',
    ])->prefix('admin')->name('admin.')->group(function () {


        /*
        |--------------------------------------------------------------------------
        | ADMIN - DASHBOARD
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [AdminDashboardController::class, 'index']
        )->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | ADMIN - USER MANAGEMENT
        |--------------------------------------------------------------------------
        |
        | Functions:
        |
        | - View users
        | - Search users
        | - Filter users
        | - Create user
        | - Edit user
        | - Change role
        | - Activate account
        | - Deactivate account
        | - Change/reset password
        |
        */

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


        /*
        |--------------------------------------------------------------------------
        | ADMIN - ACTIVITY LOGS
        |--------------------------------------------------------------------------
        |
        | Read-only system activity monitoring.
        |
        | Records may include:
        |
        | - Login/logout
        | - User management
        | - Inventory actions
        | - Customer order actions
        | - Purchase order actions
        | - Payment actions
        | - Other system actions
        |
        */

        Route::get(
            '/activity-logs',
            [AdminActivityLogController::class, 'index']
        )->name('activity-logs');


        /*
        |--------------------------------------------------------------------------
        | ADMIN - ARCHIVE
        |--------------------------------------------------------------------------
        |
        | Read-only historical records.
        |
        | Includes:
        |
        | - Inactive user accounts
        | - Historical activity records
        |
        | No delete operation is provided.
        |
        */

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
    | Owner has full business authority.
    |
    | Owner responsibilities:
    |
    | - Products
    | - Inventory
    | - Suppliers
    | - Customer Orders
    | - Purchase Orders
    | - Stock Card
    | - Reports
    | - Archive
    |
    */


    /*
    |--------------------------------------------------------------------------
    | OWNER - DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/owner/dashboard',
        [DashboardController::class, 'index']
    )->name('owner.dashboard');


    /*
    |--------------------------------------------------------------------------
    | OWNER - PRODUCTS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/owner/products',
        [ProductController::class, 'index']
    )->name('owner.products');

    Route::post(
        '/owner/products',
        [ProductController::class, 'store']
    )->name('owner.products.store');

    Route::put(
        '/owner/products/{product}',
        [ProductController::class, 'update']
    )->name('owner.products.update');

    Route::patch(
        '/owner/products/{product}/archive',
        [ProductController::class, 'archive']
    )->name('owner.products.archive');


    /*
    |--------------------------------------------------------------------------
    | OWNER - CATEGORIES
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/owner/categories',
        [CategoryController::class, 'store']
    )->name('owner.categories.store');


    /*
    |--------------------------------------------------------------------------
    | OWNER - INVENTORY
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/owner/inventory',
        [InventoryController::class, 'index']
    )->name('owner.inventory');

    Route::post(
        '/owner/inventory/stock-in',
        [InventoryController::class, 'stockIn']
    )->name('owner.inventory.stock-in');

    Route::post(
        '/owner/inventory/stock-out',
        [InventoryController::class, 'stockOut']
    )->name('owner.inventory.stock-out');

    Route::post(
        '/owner/inventory/adjust',
        [InventoryController::class, 'adjust']
    )->name('owner.inventory.adjust');


    /*
    |--------------------------------------------------------------------------
    | OWNER - SUPPLIERS
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
    | OWNER - CUSTOMER ORDERS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/owner/customer-orders',
        [CustomerOrderController::class, 'index']
    )->name('owner.customer-orders');

    Route::post(
        '/owner/customer-orders',
        [CustomerOrderController::class, 'store']
    )->name('owner.customer-orders.store');

    Route::get(
        '/owner/customer-orders/{customerOrder}',
        [CustomerOrderController::class, 'show']
    )->name('owner.customer-orders.show');

    Route::patch(
        '/owner/customer-orders/{customerOrder}/check-inventory',
        [CustomerOrderController::class, 'checkInventory']
    )->name('owner.customer-orders.check-inventory');

    Route::patch(
        '/owner/customer-orders/{customerOrder}/decision',
        [CustomerOrderController::class, 'ownerDecision']
    )->name('owner.customer-orders.decision');


    /*
    |--------------------------------------------------------------------------
    | OWNER - PURCHASE ORDERS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/owner/purchase-orders',
        [PurchaseOrderController::class, 'index']
    )->name('owner.purchase-orders');

    Route::post(
        '/owner/purchase-orders',
        [PurchaseOrderController::class, 'store']
    )->name('owner.purchase-orders.store');

    Route::get(
        '/owner/purchase-orders/{purchaseOrder}',
        [PurchaseOrderController::class, 'show']
    )->name('owner.purchase-orders.show');

    Route::put(
        '/owner/purchase-orders/{purchaseOrder}',
        [PurchaseOrderController::class, 'update']
    )->name('owner.purchase-orders.update');

    Route::patch(
        '/owner/purchase-orders/{purchaseOrder}/approve',
        [PurchaseOrderController::class, 'approve']
    )->name('owner.purchase-orders.approve');

    Route::patch(
        '/owner/purchase-orders/{purchaseOrder}/cancel',
        [PurchaseOrderController::class, 'cancel']
    )->name('owner.purchase-orders.cancel');

    Route::patch(
        '/owner/purchase-orders/{purchaseOrder}/received',
        [PurchaseOrderController::class, 'markReceived']
    )->name('owner.purchase-orders.received');


    /*
    |--------------------------------------------------------------------------
    | OWNER - STOCK CARD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/owner/stock-card',
        [StockCardController::class, 'index']
    )->name('owner.stock-card');


    /*
    |--------------------------------------------------------------------------
    | OWNER - REPORTS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/owner/reports',
        [ReportController::class, 'index']
    )->name('owner.reports');


    /*
    |--------------------------------------------------------------------------
    | OWNER - ARCHIVE
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/owner/archive',
        [ArchiveController::class, 'index']
    )->name('owner.archive');


    /*
    |--------------------------------------------------------------------------
    | SECRETARY
    |--------------------------------------------------------------------------
    |
    | Secretary responsibilities:
    |
    | - Monitor inventory
    | - View products
    | - Perform inventory transactions
    | - Check customer order availability
    | - Monitor low/out-of-stock products
    | - Report reorder needs to Owner
    |
    | Secretary does NOT:
    |
    | - Manage suppliers
    | - Create Purchase Orders
    | - Approve Purchase Orders
    |
    */


    /*
    |--------------------------------------------------------------------------
    | SECRETARY - DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/secretary/dashboard',
        [SecretaryDashboardController::class, 'index']
    )->name('secretary.dashboard');


    /*
    |--------------------------------------------------------------------------
    | SECRETARY - PRODUCTS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/secretary/products',
        [SecretaryProductController::class, 'index']
    )->name('secretary.products');


    /*
    |--------------------------------------------------------------------------
    | SECRETARY - INVENTORY
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/secretary/inventory',
        [SecretaryInventoryController::class, 'index']
    )->name('secretary.inventory');

    Route::post(
        '/secretary/inventory/stock-in',
        [SecretaryInventoryController::class, 'stockIn']
    )->name('secretary.inventory.stock-in');

    Route::post(
        '/secretary/inventory/stock-out',
        [SecretaryInventoryController::class, 'stockOut']
    )->name('secretary.inventory.stock-out');

    Route::post(
        '/secretary/inventory/adjust',
        [SecretaryInventoryController::class, 'adjust']
    )->name('secretary.inventory.adjust');


    /*
    |--------------------------------------------------------------------------
    | SECRETARY - CUSTOMER ORDERS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/secretary/customer-orders',
        [SecretaryCustomerOrderController::class, 'index']
    )->name('secretary.customer-orders');

    Route::patch(
        '/secretary/customer-orders/{customerOrder}/check-inventory',
        [SecretaryCustomerOrderController::class, 'checkInventory']
    )->name('secretary.customer-orders.check-inventory');


    /*
    |--------------------------------------------------------------------------
    | SECRETARY - STOCK CARD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/secretary/stock-card',
        [SecretaryStockCardController::class, 'index']
    )->name('secretary.stock-card');


    /*
    |--------------------------------------------------------------------------
    | SECRETARY - REPORTS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/secretary/reports',
        [SecretaryReportController::class, 'index']
    )->name('secretary.reports');


    /*
    |--------------------------------------------------------------------------
    | SECRETARY - ARCHIVE
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/secretary/archive',
        [SecretaryArchiveController::class, 'index']
    )->name('secretary.archive');


    /*
    |--------------------------------------------------------------------------
    | CASHIER
    |--------------------------------------------------------------------------
    |
    | Cashier / Accounting Staff responsibilities:
    |
    | - View relevant customer orders
    | - Record payments and receipts
    | - Maintain accounts receivable / ledger
    | - Monitor credit / utang
    | - Monitor checks and PDC
    | - Prepare accounting-related reports
    | - View historical accounting records
    |
    | Cashier does NOT:
    |
    | - Manage inventory
    | - Manage suppliers
    | - Create Purchase Orders
    | - Approve Purchase Orders
    | - Perform Stock In
    | - Perform Stock Out
    | - Perform inventory adjustments
    |
    */

    Route::middleware([
        EnsureUserIsActive::class,
        'role:cashier',
    ])->prefix('cashier')->name('cashier.')->group(function () {


        /*
        |--------------------------------------------------------------------------
        | CASHIER - DASHBOARD
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [CashierDashboardController::class, 'index']
        )->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | CASHIER - CUSTOMER ORDERS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/customer-orders',
            [CashierCustomerOrderController::class, 'index']
        )->name('customer-orders');

        Route::get(
            '/customer-orders/{customerOrder}',
            [CashierCustomerOrderController::class, 'show']
        )->name('customer-orders.show');


        /*
        |--------------------------------------------------------------------------
        | CASHIER - PAYMENTS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/payments',
            [CashierPaymentController::class, 'index']
        )->name('payments');

        Route::post(
            '/payments',
            [CashierPaymentController::class, 'store']
        )->name('payments.store');


        /*
        |--------------------------------------------------------------------------
        | CASHIER - ACCOUNTS RECEIVABLE / LEDGER
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/ledger',
            [CashierLedgerController::class, 'index']
        )->name('ledger');

        Route::get(
            '/ledger/{customerOrder}',
            [CashierLedgerController::class, 'show']
        )->name('ledger.show');


        /*
        |--------------------------------------------------------------------------
        | CASHIER - REPORTS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/reports',
            [CashierReportController::class, 'index']
        )->name('reports');


        /*
        |--------------------------------------------------------------------------
        | CASHIER - ARCHIVE
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/archive',
            [CashierArchiveController::class, 'index']
        )->name('archive');

    });

});