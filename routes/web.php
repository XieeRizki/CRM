<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController; 
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController; 
use App\Http\Controllers\MenuController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\CompanyChartController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SalesVisitController;
use App\Http\Controllers\PicController;
use App\Http\Controllers\ProfileController;



// ==========================
// Public Routes (Login / Logout / Password Reset)
// ==========================
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
    
    // Password Reset - Pakai PasswordResetController
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPasswordForm'])
        ->name('password.request');
    
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])
        ->name('password.email');
    
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetPasswordForm'])
        ->name('password.reset');
    
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
        ->name('password.update');
});

// Logout (requires authentication)
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

    //public
Route::get('/aboutus', [ProfileController::class, 'aboutUs'])->name('aboutus');




// ==========================
// CASCADE DROPDOWN ROUTES
// ==========================
Route::middleware('auth')->group(function () {
    Route::get('/get-regencies/{provinceId}', [UserController::class, 'getRegencies']);
    Route::get('/get-districts/{regencyId}', [UserController::class, 'getDistricts']);
    Route::get('/get-villages/{districtId}', [UserController::class, 'getVillages']);
});

// ==========================
// Protected Routes (Login + Permission)
// ==========================
Route::middleware(['auth', 'permission'])->group(function () {

    // ==========================
    // Dashboard
    // ==========================
    Route::get('/dashboard', [CompanyChartController::class, 'index'])->name('dashboard');

    // ==========================
    // Company Management
    // ==========================
    Route::get('/company', [CompanyController::class, 'index'])->name('company');
    Route::post('/company', [CompanyController::class, 'store'])->name('company.store');
    Route::put('/company/{id}', [CompanyController::class, 'update'])->name('company.update');
    Route::delete('/company/{id}', [CompanyController::class, 'destroy'])->name('company.destroy');
    Route::get('/company/search', [CompanyController::class, 'search'])->name('company.search');
    Route::get('/company/get-companies-dropdown', [CompanyController::class, 'getCompaniesForDropdown']);
    Route::post('/company/store-company-ajax', [CompanyController::class, 'storeCompanyAjax']);
    // 🔥 Show company detail with PICs
    Route::get('/company/{id}', [CompanyController::class, 'show'])->name('company.show');
    
    // ==========================
    // Customer Management
    // ==========================
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers');
    Route::get('/customers/list', [CustomerController::class, 'customers'])->name('customers.list');
    Route::get('/customers/export/csv', [CustomerController::class, 'export'])->name('customers.export');
    Route::post('/customers/import', [CustomerController::class, 'import'])->name('customers.import');
    Route::post('/customers/bulk-delete', [CustomerController::class, 'bulkDelete'])->name('customers.bulkDelete');
    Route::get('/customers/get-regencies/{provinceId}', [CustomerController::class, 'getRegencies']);
    Route::get('/customers/get-districts/{regencyId}', [CustomerController::class, 'getDistricts']);
    Route::get('/customers/get-villages/{districtId}', [CustomerController::class, 'getVillages']);
    Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::put('/customers/{id}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');

    // ==========================
    // SALES VISIT ROUTES 
    // ==========================
    Route::get('/salesvisit', [SalesVisitController::class, 'index'])->name('salesvisit');
    Route::get('/salesvisit/get-sales', [SalesVisitController::class, 'getSalesUsers'])->name('salesvisit.sales');
    Route::post('/salesvisit', [SalesVisitController::class, 'store'])->name('salesvisit.store');
    Route::get('/salesvisit/{id}/edit', [SalesVisitController::class, 'edit'])->name('salesvisit.edit');
    // 🔥 NEW: Show visit detail with PIC
    Route::get('/salesvisit/{id}', [SalesVisitController::class, 'show'])->name('salesvisit.show');
    Route::put('/salesvisit/{id}', [SalesVisitController::class, 'update'])->name('salesvisit.update');
    Route::delete('/salesvisit/{id}', [SalesVisitController::class, 'destroy'])->name('salesvisit.destroy');
    Route::get('/salesvisit/search', [SalesVisitController::class, 'search'])->name('salesvisit.search');
    Route::get('/salesvisit/get-provinces', [SalesVisitController::class, 'getProvinces'])->name('salesvisit.provinces.list');
    Route::get('/salesvisit/regencies/{provinceId}', [SalesVisitController::class, 'getRegencies'])->name('salesvisit.regencies');
    Route::get('/salesvisit/districts/{regencyId}', [SalesVisitController::class, 'getDistricts'])->name('salesvisit.districts');
    Route::get('/salesvisit/villages/{districtId}', [SalesVisitController::class, 'getVillages'])->name('salesvisit.villages');
    Route::get('/salesvisit/export', [SalesVisitController::class, 'export'])->name('salesvisit.export');
    Route::post('/salesvisit/import', [SalesVisitController::class, 'import'])->name('salesvisit.import');
    Route::get('/salesvisit/get-companies', [CompanyController::class, 'getCompaniesForDropdown']);
    Route::post('/salesvisit/store-company', [CompanyController::class, 'storeCompanyAjax']);
    Route::post('/sales-visit/pic', [SalesVisitController::class, 'storePic'])->name('salesvisit.pic.store');

    // ==========================
    // PIC Management
    // ==========================
    Route::get('/pic', [PicController::class, 'index'])->name('pic');
    Route::post('/pic', [PicController::class, 'store'])->name('pics.store');
    Route::put('/pic/{id}', [PicController::class, 'update'])->name('pics.update');
    Route::delete('/pic/{id}', [PicController::class, 'destroy'])->name('pics.destroy');
    Route::get('/pic/search', [PicController::class, 'search'])->name('pics.search');
    Route::get('/pics/by-company/{companyId}', [PicController::class, 'getPICsByCompany']);
    Route::post('/pics/store-pic-ajax', [PicController::class, 'storePICAjax']);

    // ==========================
    // Calendar Page (React)
    // ==========================
    Route::get('/calendar', fn() => view('layout.react'))->name('calendar');

    

    // ==========================
    // Calendar API Routes
    // ==========================
    Route::prefix('api/calendar')->name('calendar.events.')->group(function () {
        Route::get('/events', [CalendarController::class, 'index'])->name('index');
        Route::post('/events', [CalendarController::class, 'store'])->name('store');
        Route::put('/events/{id}', [CalendarController::class, 'update'])->name('update');
        Route::delete('/events/{id}', [CalendarController::class, 'destroy'])->name('destroy');
    });

    // ==========================
    // Search APIs (AJAX)
    // ==========================
    Route::get('/users/search', [UserController::class, 'search'])->name('users.search');
    Route::get('/roles/search', [RoleController::class, 'search'])->name('roles.search');
    Route::get('/menus/search', [MenuController::class, 'search'])->name('menus.search');

    // ==========================
// Profile Management API
// ==========================
Route::prefix('profiles')->name('profiles.')->group(function () {
    Route::get('/', [ProfileController::class, 'getProfiles'])->name('index');
    Route::get('/api/profile/latest', [ProfileController::class, 'getLatest']);
    Route::post('/', [ProfileController::class, 'store'])->name('store');
    Route::get('/{id}', [ProfileController::class, 'show'])->name('show');
    Route::put('/{id}', [ProfileController::class, 'update'])->name('update');
    Route::delete('/{id}', [ProfileController::class, 'destroy'])->name('destroy');
    Route::post('/reset', [ProfileController::class, 'reset'])->name('reset');
    Route::get('/stats', [ProfileController::class, 'stats'])->name('stats');
});

    // ==========================
    // Settings Pages
    // ==========================
    Route::get('/user', [UserController::class, 'index'])->name('user');
    Route::get('/role', [RoleController::class, 'index'])->name('role'); 
    Route::get('/menu', [MenuController::class, 'index'])->name('menu');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');


    // ==========================
    // CRUD - Users
    // ==========================
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // ==========================
    // CRUD - Roles
    // ==========================
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::put('/roles/{id}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::post('/roles/{id}/assign-menu', [RoleController::class, 'assignMenu'])->name('roles.assignMenu');

    // ==========================
    // CRUD - Menus
    // ==========================
    Route::post('/menus', [MenuController::class, 'store'])->name('menus.store');
    Route::put('/menus/{id}', [MenuController::class, 'update'])->name('menus.update');
    Route::delete('/menus/{id}', [MenuController::class, 'destroy'])->name('menus.destroy');

    // ==========================
    // Marketing (Sales)
    // ==========================
    Route::get('/marketing', [SalesController::class, 'index'])->name('marketing');
    Route::get('/marketing/search', [SalesController::class, 'search'])->name('marketing.search');
    Route::post('/marketing/sales', [SalesController::class, 'store'])->name('marketing.sales.store');
    Route::put('/marketing/sales/{id}', [SalesController::class, 'update'])->name('marketing.sales.update');
    Route::delete('/marketing/sales/{id}', [SalesController::class, 'destroy'])->name('marketing.sales.destroy');
    Route::get('/marketing/sales/{id}', [SalesController::class, 'show'])->name('marketing.sales.show');

    // ==========================
    // BAR CHART ROUTES
    // ==========================
    Route::prefix('api/geographic/bar')->group(function () {
        Route::get('/distribution', [CompanyChartController::class, 'getGeoDistributionBar']);
        Route::get('/tier/{tier}', [CompanyChartController::class, 'getTierDetailBar']);
        Route::get('/export', [CompanyChartController::class, 'exportGeoDataBar']);
    });

    // ==========================
    // PIE CHART ROUTES
    // ==========================
    Route::prefix('api/geographic/pie')->group(function () {
        Route::get('/distribution', [CompanyChartController::class, 'getGeoDistributionPie']);
        Route::get('/tier/{tier}', [CompanyChartController::class, 'getTierDetailPie']);
        Route::get('/export', [CompanyChartController::class, 'exportGeoDataPie']);
    });
});