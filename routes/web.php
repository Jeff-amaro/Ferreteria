<?php

use App\Http\Controllers\AlertController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientLookupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryCountController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReplenishmentController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WholesaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('catalogo', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('catalogo/{product:slug}', [CatalogController::class, 'show'])->name('catalog.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/perfil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::middleware('role:administrador')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);

        Route::post('products/{product}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::resource('products', ProductController::class);
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('clients', ClientController::class);
        Route::resource('suppliers', SupplierController::class);
    });

    Route::get('productos/{product}/stock', [PosController::class, 'stock'])->name('products.stock');

    Route::middleware('role:administrador,vendedor')->group(function () {
        Route::get('pos', [PosController::class, 'index'])->name('pos.index');
        Route::post('pos', [PosController::class, 'store'])->name('pos.store');

        Route::get('clientes/buscar', [ClientLookupController::class, 'search'])->name('clients.search');
        Route::post('clientes/rapido', [ClientLookupController::class, 'quickStore'])->name('clients.quick');

        Route::get('cotizaciones', [QuotationController::class, 'index'])->name('quotations.index');
        Route::get('cotizaciones/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
        Route::post('cotizaciones/{quotation}/emitir', [QuotationController::class, 'emit'])->name('quotations.emit');
        Route::post('cotizaciones/{quotation}/estado', [QuotationController::class, 'status'])->name('quotations.status');
        Route::post('cotizaciones/{quotation}/convertir', [QuotationController::class, 'convert'])->name('quotations.convert');
        Route::post('cotizaciones/{quotation}/mayorista', [QuotationController::class, 'wholesale'])->name('quotations.wholesale');
        Route::get('cotizaciones/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('quotations.pdf');

        Route::get('solicitudes-mayoristas', [WholesaleController::class, 'index'])->name('wholesale.index');

        Route::get('campanas', [CampaignController::class, 'index'])->name('campaigns.index');
        Route::post('campanas', [CampaignController::class, 'store'])->name('campaigns.store');
    });

    Route::post('solicitudes-mayoristas/{wholesaleRequest}/evaluar', [WholesaleController::class, 'evaluate'])
        ->middleware('role:administrador')
        ->name('wholesale.evaluate');

    Route::middleware('role:administrador,vendedor,almacenero')->group(function () {
        Route::get('ordenes', [SalesOrderController::class, 'index'])->name('orders.index');
        Route::get('ordenes/{order}', [SalesOrderController::class, 'show'])->name('orders.show');
        Route::post('ordenes/{order}/estado', [SalesOrderController::class, 'status'])->name('orders.status');
        Route::get('ordenes/{order}/pago', [SalesOrderController::class, 'payment'])->name('orders.payment');
        Route::post('ordenes/{order}/pago', [SalesOrderController::class, 'paymentStore'])->name('orders.payment.store');
        Route::get('ordenes/{order}/entrega', [SalesOrderController::class, 'delivery'])->name('orders.delivery');
        Route::post('ordenes/{order}/entrega', [SalesOrderController::class, 'deliveryStore'])->name('orders.delivery.store');

        Route::get('comprobantes/{receipt}', [ReceiptController::class, 'show'])->name('receipts.show');
        Route::get('comprobantes/{receipt}/pdf', [ReceiptController::class, 'pdf'])->name('receipts.pdf');
    });

    Route::middleware('role:administrador,almacenero')->group(function () {
        Route::get('inventario', [InventoryCountController::class, 'index'])->name('inventory.index');
        Route::get('inventario/nuevo', [InventoryCountController::class, 'create'])->name('inventory.create');
        Route::post('inventario', [InventoryCountController::class, 'store'])->name('inventory.store');
        Route::get('inventario/{inventoryCount}', [InventoryCountController::class, 'show'])->name('inventory.show');
        Route::get('inventario/{inventoryCount}/pdf', [InventoryCountController::class, 'pdf'])->name('inventory.pdf');

        Route::get('auditorias', [AuditController::class, 'index'])->name('audits.index');
        Route::post('auditorias/{audit}/resolver', [AuditController::class, 'resolve'])->name('audits.resolve');

        Route::get('reposicion', [ReplenishmentController::class, 'index'])->name('replenishment.index');
        Route::get('reposicion/pdf', [ReplenishmentController::class, 'pdf'])->name('replenishment.pdf');

        Route::get('compras', [PurchaseOrderController::class, 'index'])->name('purchases.index');
        Route::get('compras/nueva', [PurchaseOrderController::class, 'create'])->name('purchases.create');
        Route::post('compras', [PurchaseOrderController::class, 'store'])->name('purchases.store');
        Route::get('compras/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchases.show');
        Route::post('compras/{purchaseOrder}/recibir', [PurchaseOrderController::class, 'receive'])->name('purchases.receive');
    });

    Route::middleware('role:administrador,vendedor,almacenero')->group(function () {
        Route::get('alertas', [AlertController::class, 'index'])->name('alerts.index');
        Route::post('alertas/leer-todas', [AlertController::class, 'readAll'])->name('alerts.readAll');
        Route::post('alertas/{alert}/leer', [AlertController::class, 'read'])->name('alerts.read');
    });
});
