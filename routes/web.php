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
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Rota principal redireciona para a página de produtos
Route::get('/', function () {
    return redirect()->route('products');
});

// Redireciona /login para o login do cliente
Route::get('/login', function () {
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
});


// Rotas do Revendedor
// Rotas públicas
Route::get('/produtos', [ProductShowcaseController::class, 'index'])->name('products');

// Rotas do carrinho
Route::middleware('auth:customer')->group(function () {
    Route::get('/carrinho', [CartController::class, 'index'])->name('customer.cart');
    Route::post('/cart/add/{product}', [CartController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart/count', [CartController::class, 'getCartCount'])->name('cart.count');
    Route::put('/cart/items/{cartItem}/quantity', [CartController::class, 'updateQuantity'])->name('cart.update.quantity');
    Route::delete('/cart/items/{cartItem}', [CartController::class, 'removeItem'])->name('cart.remove');
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
    Route::get('/perfil', [CustomerAuthController::class, 'profile'])->name('customer.profile');
    Route::put('/perfil', [CustomerAuthController::class, 'updateProfile'])->name('customer.profile.update');
    
    // Rotas do carrinho
    Route::get('/carrinho', [CustomerAuthController::class, 'cart'])->name('cart');
    
    // Rotas de pedidos
    Route::get('/pedidos', [CustomerAuthController::class, 'orders'])->name('customer.orders');
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



require __DIR__.'/auth.php';
