<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ResellerController;
use App\Http\Controllers\ResellerAuthController;
use App\Http\Controllers\ResellerDashboardController;
use App\Http\Controllers\ProductShowcaseController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ShippingMethodController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\SalesController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Rota principal - redireciona para dashboard do cliente ou login
Route::get('/', function () {
    if (Auth::guard('customer')->check()) {
        return redirect()->route('customer.dashboard');
    }
    return redirect()->route('customer.login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rotas de Clientes
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::post('/{customer}/add-balance', [CustomerController::class, 'addBalance'])->name('add-balance');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
    });


    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/departments', [DepartmentController::class, 'index'])->name('departments');
    Route::get('/departments/create', [DepartmentController::class, 'create'])->name('departments.create');
    Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
    Route::get('/departments/{department}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');
    Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

    // Product Routes
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::post('/{product}/images', [ProductController::class, 'updateImages'])->name('update.images');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
    });

    // Reseller Routes
    Route::prefix('resellers')->name('resellers.')->group(function () {
        Route::get('/', [ResellerController::class, 'index'])->name('index');
        Route::get('/create', [ResellerController::class, 'create'])->name('create');
        Route::post('/', [ResellerController::class, 'store'])->name('store');
        Route::get('/{reseller}/edit', [ResellerController::class, 'edit'])->name('edit');
        Route::put('/{reseller}', [ResellerController::class, 'update'])->name('update');
        Route::delete('/{reseller}', [ResellerController::class, 'destroy'])->name('destroy');
    });

    // Shipping Method Routes
    Route::get('/shipping-methods', [ShippingMethodController::class, 'index'])->name('shipping-methods.index');
    Route::get('/shipping-methods/create', [ShippingMethodController::class, 'create'])->name('shipping-methods.create');
    Route::post('/shipping-methods', [ShippingMethodController::class, 'store'])->name('shipping-methods.store');
    Route::get('/shipping-methods/{shippingMethod}/edit', [ShippingMethodController::class, 'edit'])->name('shipping-methods.edit');
    Route::put('/shipping-methods/{shippingMethod}', [ShippingMethodController::class, 'update'])->name('shipping-methods.update');
    Route::delete('/shipping-methods/{shippingMethod}', [ShippingMethodController::class, 'destroy'])->name('shipping-methods.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rotas de Vendas
    Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
    Route::get('/sales/reports', [SalesController::class, 'reports'])->name('sales.reports');
    Route::get('/sales/{order}', [SalesController::class, 'show'])->name('sales.show');
});


// Rotas do Revendedor

// Rotas do carrinho
Route::middleware('auth:customer')->group(function () {
    Route::get('/carrinho', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart/count', [CartController::class, 'getCartCount'])->name('cart.count');
    Route::put('/cart/items/{cartItem}/quantity', [CartController::class, 'updateQuantity'])->name('cart.update.quantity');
    Route::delete('/cart/items/{cartItem}', [CartController::class, 'removeFromCart'])->name('cart.remove');

    // Rotas de pagamento
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/success', [OrderController::class, 'success'])->name('orders.success');
    Route::get('/orders/{order}/pix', [OrderController::class, 'pixPayment'])->name('orders.pix');
    Route::get('/orders/{order}/payment-error', [OrderController::class, 'paymentError'])->name('orders.payment-error');
    Route::post('/orders/process-credit-card', [OrderController::class, 'processCreditCardPayment'])->name('orders.process-credit-card');
    Route::post('/orders/process-pix', [OrderController::class, 'processPixPayment'])->name('orders.process-pix');
});

// Rotas de autenticação do cliente
Route::middleware('guest:customer')->prefix('cliente')->group(function () {
    Route::get('/entrar', [CustomerAuthController::class, 'showLogin'])->name('customer.login');
    Route::post('/entrar', [CustomerAuthController::class, 'login']);
    Route::get('/registro', [CustomerAuthController::class, 'showRegister'])->name('customer.register');
    Route::post('/registro', [CustomerAuthController::class, 'register']);
    
    // Rotas de redefinição de senha
    Route::get('/esqueci-senha', [CustomerAuthController::class, 'showForgotPassword'])
        ->name('customer.password.request');
    Route::post('/esqueci-senha', [CustomerAuthController::class, 'sendResetLinkEmail'])
        ->name('customer.password.email');
    Route::get('/redefinir-senha/{token}', [CustomerAuthController::class, 'showResetPassword'])
        ->name('customer.password.reset');
    Route::post('/redefinir-senha', [CustomerAuthController::class, 'resetPassword'])
        ->name('customer.password.update');
});

// Rotas protegidas do cliente
Route::middleware('auth:customer')->prefix('cliente')->group(function () {
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');
    Route::get('/minha-conta', [CustomerAuthController::class, 'dashboard'])->name('customer.dashboard');
    Route::post('/adicionar-saldo', [CustomerAuthController::class, 'addWalletValue'])->name('customer.wallet.add');
    Route::get('/perfil', [CustomerAuthController::class, 'profile'])->name('customer.profile');
    Route::put('/perfil', [CustomerAuthController::class, 'updateProfile'])->name('customer.profile.update');
    Route::post('/buscar-imovel', [CustomerAuthController::class, 'buscarImovel'])->name('customer.buscar-imovel');
    Route::post('/buscar-por-ni', [CustomerAuthController::class, 'buscarPorNI'])->name('customer.buscar-por-ni');
    Route::get('/historico-imovel', [CustomerAuthController::class, 'historicoImovel'])->name('customer.historico-imovel');
    Route::get('/historico-por-ni', [CustomerAuthController::class, 'historicoPorNI'])->name('customer.historico-por-ni');
    Route::get('/historico-por-codigo', [CustomerAuthController::class, 'historicoPorCodigo'])->name('customer.historico-por-codigo');
    Route::get('/exportar-historico-imovel-pdf', [CustomerAuthController::class, 'exportarHistoricoImovelPdf'])->name('customer.exportar-historico-imovel-pdf');
    Route::get('/exportar-consulta-imovel-pdf/{id}', [CustomerAuthController::class, 'exportarConsultaImovelPdf'])->name('customer.exportar-consulta-imovel-pdf');
    
    // Rotas de pedidos
    Route::get('/pedidos', [CustomerAuthController::class, 'orders'])->name('customer.orders');
    Route::get('/pedidos/count', [CustomerAuthController::class, 'ordersCount'])->name('customer.orders.count');
});

Route::middleware('web')->group(function () {
    Route::get('revendedor/login', [ResellerAuthController::class, 'showLogin'])->name('reseller.login');
    Route::post('revendedor/login', [ResellerAuthController::class, 'login']);
});

Route::middleware(['web', 'auth:reseller'])->prefix('revendedor')->group(function () {
    Route::post('logout', [ResellerAuthController::class, 'logout'])->name('reseller.logout');
    Route::get('dashboard', [ResellerDashboardController::class, 'index'])->name('reseller.dashboard');
    Route::get('perfil', [ResellerDashboardController::class, 'profile'])->name('reseller.profile');
});

// Webhook do PagSeguro (público)
    // ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Rotas de autenticação Admin (movidas de auth.php para controle explícito)
// require __DIR__.'/auth.php';

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
