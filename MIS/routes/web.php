<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EmployeeBonusAdjustmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExternalProductController;
use App\Http\Controllers\ExternalProductItemController;
use App\Http\Controllers\InternalProductController;
use App\Http\Controllers\InternalProductItemController;
use App\Http\Controllers\MaterialAssignmentController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MaterialStockController;
use App\Http\Controllers\MonthlyExpensesListController;
use App\Http\Controllers\MonthlyExpensesRecordController;
use App\Http\Controllers\PettyCashController;
use App\Http\Controllers\ProductSaleController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Redirect root to /login (custom login view)
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return view('login'); // Custom login page
});


// Popup routes
Route::get('/popup/{view}', function ($view) {
    $blade = "popups.$view";
    if (!view()->exists($blade)){
        abort(404);
    }

    $user = Auth::user(); // ✅ Get the logged-in user

    return view($blade, compact('user')); // ✅ Pass $user to the Blade view
})->middleware('auth', 'restrict.employee'); // ✅ Protect with auth


// =================== DASHBOARD ROUTE =================== //
Route::middleware(['auth', 'restrict.employee'])->group(function () {

    //to dashboard
    Route::get('/dashboard', function () {
        return redirect('/dashboard/home');
    });

    // Dashboard Home
    Route::get('/dashboard/home', function () {
        return view('pages.home');
    })->name('dashboard');

});

// Authenticated user profile routes
Route::middleware(['auth', 'restrict.employee'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile', [ProfileController::class, 'store'])->name('profile.store');
});

//Employee routes
Route::middleware(['auth', 'restrict.employee'])->group(function () {
    //add employee
    Route::get('/dashboard/add-employee', function () {
        return view('pages.add-employee');
    })->name('employees.create');
    Route::post('/dashboard/add-employee', [EmployeeController::class, 'store'])->name('employees.store');

    //manage employee
    Route::get('/dashboard/manage-employee', [EmployeeController::class, 'index'])->name('employees.index');
    Route::patch('/dashboard/manage-employee', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/dashboard/manage-employee', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::put('/dashboard/manage-employee', [EmployeeController::class, 'updatePassword'])->name('employees.updatePassword');

    //employee bonus
    Route::get('/dashboard/add-bonus-deduction', [EmployeeBonusAdjustmentController::class, 'index'])
        ->name('employees.bonus');
    Route::post('/dashboard/add-bonus-deductions', [EmployeeBonusAdjustmentController::class, 'store'])
        ->name('employee_bonus_adjustments.store');
});

//Material routes
Route::middleware(['auth', 'restrict.employee'])->group(function () {
    //create material
    Route::get('/dashboard/create-stocks', function () {
        return view('pages.create-stocks');
    })->name('stocks.create');
    Route::post('/dashboard/create-stocks', [MaterialController::class, 'store'])->name('stockscreate.create');

    //add material
    Route::get('/dashboard/add-stocks', [MaterialController::class, 'index'])->name('stocks.index');
    Route::post('/dashboard/add-stocks', [MaterialStockController::class, 'store'])->name('stocks.store');

    //manage material
    Route::get('/dashboard/manage-stocks', [MaterialStockController::class, 'index'])->name('stocks.manage');
    Route::patch('/dashboard/manage-stocks/update', [MaterialController::class, 'update'])->name('stocks.update');
    Route::patch('/dashboard/manage-stocks/adjust', [MaterialStockController::class, 'adjustStock'])->name('stocks.adjust');
    Route::delete('/dashboard/manage-stocks', [MaterialStockController::class, 'softDeleteMaterial'])->name('materials.softDelete');
});

//External Product routes
Route::middleware(['auth', 'restrict.employee'])->group(function () {
    //create external product
    Route::get('/dashboard/create-external-product', function () {
        return view('pages.create-external-product');
    })->name('products.create.external');
    Route::post('/dashboard/create-external-product', [ExternalProductController::class, 'store'])->name('ExternalProducts.store');

    //add external product
    Route::get('/dashboard/add-external-product', [ExternalProductController::class, 'index'])->name('products.add.external');
    Route::post('/dashboard/add-external-product', [ExternalProductItemController::class, 'store'])->name('ExternalProducts.add');
});

//Internal Product routes
Route::middleware(['auth', 'restrict.employee'])->group(function () {
    //create internal product
    Route::get('/dashboard/create-internal-product', function () {
        return view('pages.create-internal-product');
    })->name('products.create.internal');
    Route::post('/dashboard/create-internal-product', [InternalProductController::class, 'store'])->name('InternalProducts.store');

    //add internal product
    Route::get('/dashboard/add-internal-product', [InternalProductController::class, 'index'])->name('products.add.internal');
    Route::post('/dashboard/add-internal-product', [InternalProductItemController::class, 'store'])->name('InternalProducts.add');
});

//manage products routes
Route::middleware(['auth', 'restrict.employee'])->group(function () {
    //get products
    Route::get('/dashboard/manage-product', [InternalProductController::class, 'allProducts'])->name('products.manage');

    //update Internal products
    Route::patch('/dashboard/manage-product/internal/update', [InternalProductController::class, 'update'])->name('internalProducts.update');
    Route::patch('/dashboard/manage-product/internal/adjust', [InternalProductItemController::class, 'adjustStock'])->name('internalProducts.adjust');
    Route::delete('/dashboard/manage-product/internal', [InternalProductItemController::class, 'softDelete'])->name('internalProducts.softDelete');

    //update External products
    Route::patch('/dashboard/manage-product/external/update', [ExternalProductController::class, 'update'])->name('externalProducts.update');
    Route::patch('/dashboard/manage-product/external/adjust', [ExternalProductItemController::class, 'adjustStock'])->name('externalProducts.adjust');
    Route::delete('/dashboard/manage-product/external', [ExternalProductItemController::class, 'softDeleteExternalProduct'])->name('externalProducts.softDelete');
});

//Material Assignment routes
Route::middleware(['auth', 'restrict.employee'])->group(function () {
    //create material assignment
    Route::get('/dashboard/add-assignment', [MaterialAssignmentController::class, 'createindex'])->name('page.assignments.create');
    Route::post('/dashboard/add-assignment', [MaterialAssignmentController::class, 'store'])->name('assignments.store');

    //accept material assignment
    Route::get('/dashboard/accept-assignment', [MaterialAssignmentController::class, 'index'])->name('page.assignments.accept');
    Route::post('/dashboard/accept-assignment', [MaterialAssignmentController::class, 'tobecompletedindex'])->name('assignments.get');
    Route::post('/dashboard/accept-assignment/complete', [MaterialAssignmentController::class, 'complete'])->name('assignments.complete');
    Route::patch('/dashboard/accept-assignment/update', [MaterialAssignmentController::class, 'updateassignment'])->name('assignments.update');


    //manage material assignment
    Route::get('/dashboard/manage-assignment', [MaterialAssignmentController::class, 'reviewIndex'])->name('page.assignments.manage');
    Route::patch('/dashboard/manage-assignment/review', [MaterialAssignmentController::class, 'review'])->name('assignments.review');
    Route::patch('/dashboard/manage-assignment/revieweach', [MaterialAssignmentController::class, 'revieweach'])->name('assignments.revieweach');
});

//counter routes
Route::middleware(['auth', 'restrict.employee'])->group(function () {
    //counter
    Route::get('/dashboard/counter',[ProductSaleController::class, 'products'])->name('page.counter');
    Route::post('/dashboard/counter', [ProductSaleController::class, 'store'])->name('counter.store');
});

//attendance routes
Route::middleware(['auth', 'restrict.employee'])->group(function () {
    //counter
    Route::get('/dashboard/attendance',[AttendanceController::class, 'index'])->name('page.attendance');
    Route::post('/dashboard/attendance', [AttendanceController::class, 'check'])->name('attendance.check');
    Route::patch('/dashboard/attendance', [AttendanceController::class, 'mark'])->name('attendance.mark');
});

//petty cash routes
Route::middleware(['auth', 'restrict.employee'])->group(function () {
    Route::get('/dashboard/petty-cash',[PettyCashController::class, 'index'])->name('page.pettyCash');
    Route::post('/dashboard/petty-cash', [PettyCashController::class, 'store'])->name('pettyCash.store');
    Route::delete('/dashboard/petty-cash', [PettyCashController::class, 'destroy'])->name('pettyCash.destroy');
});

//accounts routes
Route::middleware(['auth', 'restrict.employee'])->group(function () {
    Route::get('/dashboard/accounts',[MonthlyExpensesRecordController::class, 'index'])->name('page.accounts');

    Route::post('/dashboard/accounts', [MonthlyExpensesRecordController::class, 'store'])->name('expense.store');
    Route::delete('/dashboard/accounts', [MonthlyExpensesRecordController::class, 'destroy'])->name('expense.destroy');

    Route::post('/dashboard/expense/list', [MonthlyExpensesListController::class, 'store'])->name('title.store');
    Route::delete('/dashboard/expense/list', [MonthlyExpensesListController::class, 'destroy'])->name('title.destroy');
});

//salary routes
Route::middleware(['auth', 'restrict.employee'])->group(function () {
    Route::get('/dashboard/salary',[ReportsController::class, 'SalaryIndex'])->name('page.salary');

    Route::post('/dashboard/salary',[ReportsController::class, 'salaryExport'])->name('salary.export');
    Route::post('/dashboard/salary/print',[ReportsController::class, 'salaryPrint'])->name('salary.print');
});

//reports routes
Route::middleware(['auth', 'restrict.employee'])->group(function () {
    Route::get('/dashboard/reports',[ReportsController::class, 'index'])->name('page.reports');

    Route::post('/dashboard/reports', [ReportsController::class, 'exportPdf'])->name('report.export');
    Route::post('/dashboard/reports/print', [ReportsController::class, 'PrintPdf'])->name('report.print');
});

//settings routes
Route::middleware(['auth', 'restrict.employee'])->group(function () {
    Route::get('/dashboard/settings', function () {
        return view('pages.settings');
    })->name('page.settings');

    Route::post('/dashboard/settings/material/deleted', [SettingController::class, 'destroyDeletedMaterial'])->name('page.settings.material.deleted');
    Route::post('/dashboard/settings/Internal-product/deleted', [SettingController::class, 'destroyDeletedInternalProduct'])->name('page.settings.internalProduct.deleted');
    Route::post('/dashboard/settings/external-product/deleted', [SettingController::class, 'destroyDeletedExternalProduct'])->name('page.settings.externalProduct.deleted');

    Route::post('/dashboard/settings/material/unavailable', [SettingController::class, 'destroyUnavailableMaterial'])->name('page.settings.material.unavailable');
    Route::post('/dashboard/settings/Internal-product/sold', [SettingController::class, 'destroySoldInternalProduct'])->name('page.settings.internalProduct.sold');
    Route::post('/dashboard/settings/external-product/sold', [SettingController::class, 'destroySoldExternalProduct'])->name('page.settings.externalProduct.sold');
});

// Load additional auth routes (login, register, reset, etc.)
require __DIR__.'/auth.php';


/* Dashboard route with auth and restrict.employee middleware
Route::get('/dashboard/{page?}', function ($page = 'home') {
    if (!view()->exists("pages.$page")) {
        abort(404);
    }

    return view('dashboard', ['page' => $page]);
})->middleware(['auth', 'restrict.employee'])->name('dashboard');
*/
