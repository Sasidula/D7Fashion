<?php

use App\Http\Controllers\EmployeeBonusAdjustmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InternalProductController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MaterialStockController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PageController;

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


/* Dashboard route with auth and restrict.employee middleware
Route::get('/dashboard/{page?}', function ($page = 'home') {
    if (!view()->exists("pages.$page")) {
        abort(404);
    }

    return view('dashboard', ['page' => $page]);
})->middleware(['auth', 'restrict.employee'])->name('dashboard');
*/

// =================== NON-MODULAR DASHBOARD ROUTES =================== //

Route::get('/dashboard', function () {
    return redirect('/dashboard/home');
});


Route::middleware(['auth', 'restrict.employee'])->group(function () {

    //to dashboard
    Route::get('/dashboard', function () {
        return redirect('/dashboard/home');
    });

    // Dashboard Home
    Route::get('/dashboard/home', function () {
        return view('pages.home');
    })->name('dashboard');

    //counter
    Route::get('/dashboard/counter', function () {
        return view('pages.counter');
    })->name('counter');

    // Attendance
    Route::get('/dashboard/attendance', function () {
        return view('pages.attendance');
    })->name('attendance');

    // Petty Cash
    Route::get('/dashboard/petty-cash', function () {
        return view('pages.petty-cash');
    })->name('petty-cash');

    // Accounts
    Route::get('/dashboard/accounts', function () {
        return view('pages.accounts');
    })->name('accounts');

    // Reports
    Route::get('/dashboard/reports', function () {
        return view('pages.reports');
    })->name('reports');

    // Settings
    Route::get('/dashboard/settings', function () {
        return view('pages.settings');
    })->name('settings');

    // Internal Product
    Route::get('/dashboard/add-internal-product', function () {
        return view('pages.add-internal-product');
    })->name('products.add.internal');
    Route::get('/dashboard/create-internal-product', function () {
        return view('pages.create-internal-product');
    })->name('products.create.internal');

    // External Product
    Route::get('/dashboard/add-external-product', function () {
        return view('pages.add-external-product');
    })->name('products.add.external');
    Route::get('/dashboard/create-external-product', function () {
        return view('pages.create-external-product');
    })->name('products.create.external');

    // Manage Products
    Route::get('/dashboard/manage-product', function () {
        return view('pages.manage-product');
    })->name('products.manage');


    // Assignments
    Route::get('/dashboard/add-assignment', function () {
        return view('pages.add-assignment');
    })->name('assignments.add');
    Route::get('/dashboard/accept-assignment', function () {
        return view('pages.accept-assignment');
    })->name('assignments.accept');
    Route::get('/dashboard/manage-assignment', function () {
        return view('pages.manage-assignment');
    })->name('assignments.manage');
});

// Authenticated user profile routes
Route::middleware('auth')->group(function () {
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

// Load additional auth routes (login, register, reset, etc.)
require __DIR__.'/auth.php';
