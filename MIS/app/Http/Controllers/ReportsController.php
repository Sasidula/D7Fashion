<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ExternalProductItem;
use App\Models\MaterialStock;
use App\Models\MonthlyExpensesRecord;
use App\Models\PettyCash;
use App\Models\ProductSale;
use App\Models\ProductSalesItem;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ReportsController extends Controller
{

    public function index(Request $request)
    {
        $month = $request->monthx ?? now()->month;
        $year  = $request->yearx ?? now()->year;

        $employees = User::all();

        // Existing queries
        $Expense = MonthlyExpensesRecord::with(['expense' => fn($q) => $q->withTrashed()]);
        $Attendance = Attendance::with(['user' => fn($q) => $q->withTrashed()]);
        $PettyCash = PettyCash::query();
        $Sales = ProductSalesItem::with([
            'sale',
            'internalProductItem.internalProduct',
            'externalProductItem.external_product'
        ]);

        // Load users with attendances and bonusAdjustments for the given month and year
        $query = User::with([
            'attendances' => function($q) use ($month, $year) {
                $q->whereMonth('date', $month)->whereYear('date', $year);
            },
            'bonusAdjustments' => function($q) use ($month, $year) {
                $q->whereMonth('created_at', $month)->whereYear('created_at', $year);
            }
        ])->withTrashed();

        if ($request->filled('user_id')) {
            $query->where('id', $request->user_id);
        }

        $users = $query->get();

        if ($request->filled('monthx')) {
            $Expense = $Expense->whereMonth('created_at', $request->monthx);
            $Attendance = $Attendance->whereMonth('created_at', $request->monthx);
            $PettyCash = $PettyCash->whereMonth('created_at', $request->monthx);
            $Sales = $Sales->whereMonth('created_at', $request->monthx);
        }

        if ($request->filled('yearx')) {
            $Expense = $Expense->whereYear('created_at', $request->yearx);
            $Attendance = $Attendance->whereYear('created_at', $request->yearx);
            $PettyCash = $PettyCash->whereYear('created_at', $request->yearx);
            $Sales = $Sales->whereYear('created_at', $request->yearx);
        }

        // Salary calculation including bonus adjustments
        $salaryData = $users->map(function($user) use ($month) {
            $workedHours = 0;

            foreach ($user->attendances as $att) {

                if ($att->check_in && $att->check_out) {
                    $checkIn  = Carbon::parse($att->check_in);
                    $checkOut = Carbon::parse($att->check_out);
                    $workedHours += $checkOut->diffInMinutes($checkIn,true) / 60; // more accurate
                }
            }

            // Calculate base salary
            if ($user->salary_type === 'monthly') {
                $baseSalary = $user->salary_amount;
            } elseif ($user->salary_type === 'hourly') {
                $baseSalary = $workedHours * $user->salary_amount;
            } else {
                $baseSalary = 0;
            }

            // Sum bonus adjustments for this month
            $totalBonusAdds = $user->bonusAdjustments
                ->where('action', 'add')
                ->sum('amount');

            $totalBonusRemoves = $user->bonusAdjustments
                ->where('action', 'remove')
                ->sum('amount');

            // Final salary = base + adds - removes
            $finalSalary = $baseSalary + $totalBonusAdds - $totalBonusRemoves;

            return [
                'month'            => Carbon::create(null, $month)->format('F'),
                'name'             => $user->name,
                'worked_hours'     => round($workedHours, 2),
                'salary_type'      => ucfirst($user->salary_type),
                'rate'             => $user->salary_amount,
                'base_salary'      => round($baseSalary, 2),
                'bonus_adds'       => round($totalBonusAdds, 2),
                'bonus_removes'    => round($totalBonusRemoves, 2),
                'calculatedSalary' => round($finalSalary, 2),
            ];
        });

        // ---------------- PROFIT CALCULATION ----------------

        $totalSalaries = $salaryData->sum('calculatedSalary');

        // For Total Income
        $salesRevenue = ProductSale::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get()
            ->sum('price');

        // For Total Expenses
        $MaterialCosts = MaterialStock::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->with('material')
            ->get()
            ->sum(fn($s) => $s->material->price);
        $ExternalCosts = ExternalProductItem::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->with('external_product')
            ->get()
            ->sum(fn($s) => $s->external_product->bought_price);


        // Expenses & Incomes
        $totalExpense = $Expense->clone()->where('type','expense')->sum('amount');
        $totalIncome  = $Expense->clone()->where('type','income')->sum('amount');

        $pettyExpense = $PettyCash->clone()->where('type','expense')->sum('amount');
        $pettyIncome  = $PettyCash->clone()->where('type','income')->sum('amount');

        // Net Profit = (Sales + incomes) - (salaries + expenses + material costs)
        $totalProfit = ($salesRevenue + $pettyIncome + $totalIncome)
            - ($totalSalaries + $MaterialCosts + $ExternalCosts + $pettyExpense + $totalExpense);

        $netProfit = [
            'profit' => round($totalProfit, 2),
            'sales'  => round($salesRevenue, 2),
            'incomes' => round($pettyIncome + $totalIncome, 2),
            'expenses' => round($totalExpense + $pettyExpense, 2),
            'salaries' => round($totalSalaries, 2),
            'material_costs' => round($MaterialCosts, 2),
            'external_costs' => round($ExternalCosts, 2),
            'month' => $month,
            'year'  => $year,
        ];

        // -----------------------------------------------------

        // Grouping for existing data
        $attendanceGrouped = $Attendance->get()->groupBy('user_id');
        $salesGrouped = $Sales->get()->groupBy('product_sales_id');

        return view('pages.reports', [
            'expenses'     => $Expense->get(),
            'attendance'   => $attendanceGrouped,
            'pettyCash'    => $PettyCash->get(),
            'sales'        => $salesGrouped,
            'salaryReport' => $salaryData,
            'netProfit'    => $netProfit,
            'employee'     => $employees,
            'user_id'      => $request->user_id ?? '',
            'month'        => $request->monthx ?? '',
            'year'         => $request->yearx ?? '',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reportType' => 'required|in:main,expenses,employee,petty,sales,salary',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $type = $validator->validated()['reportType'];

        $month = $request->monthx ?? now()->month;
        $year  = $request->yearx ?? now()->year;

        // Existing queries
        $Expense = MonthlyExpensesRecord::with(['expense' => fn($q) => $q->withTrashed()]);
        $Attendance = Attendance::with(['user' => fn($q) => $q->withTrashed()]);
        $PettyCash = PettyCash::query();
        $Sales = ProductSalesItem::with([
            'sale',
            'internalProductItem.internalProduct',
            'externalProductItem.external_product'
        ]);

        // Load users with attendances and bonusAdjustments for the given month and year
        $query = User::with([
            'attendances' => function($q) use ($month, $year) {
                $q->whereMonth('date', $month)->whereYear('date', $year);
            },
            'bonusAdjustments' => function($q) use ($month, $year) {
                $q->whereMonth('created_at', $month)->whereYear('created_at', $year);
            }
        ])->withTrashed();

        if ($request->filled('user_id')) {
            $query->where('id', $request->user_id);
        }

        $users = $query->get();

        if ($request->filled('monthx')) {
            $Expense = $Expense->whereMonth('created_at', $request->monthx);
            $Attendance = $Attendance->whereMonth('created_at', $request->monthx);
            $PettyCash = $PettyCash->whereMonth('created_at', $request->monthx);
            $Sales = $Sales->whereMonth('created_at', $request->monthx);
        }

        if ($request->filled('yearx')) {
            $Expense = $Expense->whereYear('created_at', $request->yearx);
            $Attendance = $Attendance->whereYear('created_at', $request->yearx);
            $PettyCash = $PettyCash->whereYear('created_at', $request->yearx);
            $Sales = $Sales->whereYear('created_at', $request->yearx);
        }

        // Salary calculation including bonus adjustments
        $salaryData = $users->map(function($user) use ($month) {
            $workedHours = 0;

            foreach ($user->attendances as $att) {

                if ($att->check_in && $att->check_out) {
                    $checkIn  = Carbon::parse($att->check_in);
                    $checkOut = Carbon::parse($att->check_out);
                    $workedHours += $checkOut->diffInMinutes($checkIn,true) / 60; // more accurate
                }
            }

            // Calculate base salary
            if ($user->salary_type === 'monthly') {
                $baseSalary = $user->salary_amount;
            } elseif ($user->salary_type === 'hourly') {
                $baseSalary = $workedHours * $user->salary_amount;
            } else {
                $baseSalary = 0;
            }

            // Sum bonus adjustments for this month
            $totalBonusAdds = $user->bonusAdjustments
                ->where('action', 'add')
                ->sum('amount');

            $totalBonusRemoves = $user->bonusAdjustments
                ->where('action', 'remove')
                ->sum('amount');

            // Final salary = base + adds - removes
            $finalSalary = $baseSalary + $totalBonusAdds - $totalBonusRemoves;

            return [
                'month'            => Carbon::create(null, $month)->format('F'),
                'name'             => $user->name,
                'worked_hours'     => round($workedHours, 2),
                'salary_type'      => ucfirst($user->salary_type),
                'rate'             => $user->salary_amount,
                'base_salary'      => round($baseSalary, 2),
                'bonus_adds'       => round($totalBonusAdds, 2),
                'bonus_removes'    => round($totalBonusRemoves, 2),
                'calculatedSalary' => round($finalSalary, 2),
            ];
        });

        // ---------------- PROFIT CALCULATION ----------------

        $totalSalaries = $salaryData->sum('calculatedSalary');

        // For Total Income
        $salesRevenue = ProductSale::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get()
            ->sum('price');

        // For Total Expenses
        $MaterialCosts = MaterialStock::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->with('material')
            ->get()
            ->sum(fn($s) => $s->material->price);
        $ExternalCosts = ExternalProductItem::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->with('external_product')
            ->get()
            ->sum(fn($s) => $s->external_product->bought_price);


        // Expenses & Incomes
        $totalExpense = $Expense->clone()->where('type','expense')->sum('amount');
        $totalIncome  = $Expense->clone()->where('type','income')->sum('amount');

        $pettyExpense = $PettyCash->clone()->where('type','expense')->sum('amount');
        $pettyIncome  = $PettyCash->clone()->where('type','income')->sum('amount');

        // Net Profit = (Sales + incomes) - (salaries + expenses + material costs)
        $totalProfit = ($salesRevenue + $pettyIncome + $totalIncome)
            - ($totalSalaries + $MaterialCosts + $ExternalCosts + $pettyExpense + $totalExpense);

        $netProfit = [
            'profit' => round($totalProfit, 2),
            'sales'  => round($salesRevenue, 2),
            'incomes' => round($pettyIncome + $totalIncome, 2),
            'expenses' => round($totalExpense + $pettyExpense, 2),
            'salaries' => round($totalSalaries, 2),
            'material_costs' => round($MaterialCosts, 2),
            'external_costs' => round($ExternalCosts, 2),
            'month' => $month,
            'year'  => $year,
        ];

        // -----------------------------------------------------


        // Grouping for existing data
        $attendanceGrouped = $Attendance->get()->groupBy('user_id');
        $attendance = $attendanceGrouped;
        $salesGrouped = $Sales->get()->groupBy('product_sales_id');
        $sales = $salesGrouped;
        $expenses = $Expense->get();
        $pettyCash = $PettyCash->get();
        $salaryReport = $salaryData;

        $monthy = $request->monthx ?? '';
        $yeary = $request->yearx ?? '';

        switch ($type) {
            case 'main':
                $pdf = PDF::loadView('reports.monthly_report_pdf', compact('netProfit', 'monthy', 'yeary'));
                return $pdf->download('monthly_report.pdf');
            case 'expenses':
                $pdf = PDF::loadView('reports.monthly_expenses_pdf', compact('expenses', 'monthy', 'yeary'));
                return $pdf->download('monthly_expenses_report.pdf');
            case 'employee':
                $pdf = PDF::loadView('reports.attendence_pdf', compact('attendance', 'monthy', 'yeary'));
                return $pdf->download('attendence_report.pdf');
            case 'petty':
                $pdf = PDF::loadView('reports.petty_cash_pdf', compact('pettyCash', 'monthy', 'yeary'));
                return $pdf->download('petty_cash_report.pdf');
            case 'sales':
                $pdf = PDF::loadView('reports.sales_pdf', compact('sales', 'monthy', 'yeary'));
                return $pdf->download('sales_report.pdf');
            case 'salary':
                $pdf = PDF::loadView('reports.salary_pdf', compact('salaryReport', 'monthy', 'yeary'));
                return $pdf->download('salary_report.pdf');
        }

        return redirect()->back()->with('error', 'Invalid report type.');
    }

    public function PrintPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reportType' => 'required|in:main,expenses,employee,petty,sales,salary',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $type = $validator->validated()['reportType'];

        $month = $request->monthx ?? now()->month;
        $year  = $request->yearx ?? now()->year;

        // Existing queries
        $Expense = MonthlyExpensesRecord::with(['expense' => fn($q) => $q->withTrashed()]);
        $Attendance = Attendance::with(['user' => fn($q) => $q->withTrashed()]);
        $PettyCash = PettyCash::query();
        $Sales = ProductSalesItem::with([
            'sale',
            'internalProductItem.internalProduct',
            'externalProductItem.external_product'
        ]);

        // Load users with attendances and bonusAdjustments for the given month and year
        $query = User::with([
            'attendances' => function($q) use ($month, $year) {
                $q->whereMonth('date', $month)->whereYear('date', $year);
            },
            'bonusAdjustments' => function($q) use ($month, $year) {
                $q->whereMonth('created_at', $month)->whereYear('created_at', $year);
            }
        ])->withTrashed();

        if ($request->filled('user_id')) {
            $query->where('id', $request->user_id);
        }

        $users = $query->get();

        if ($request->filled('monthx')) {
            $Expense = $Expense->whereMonth('created_at', $request->monthx);
            $Attendance = $Attendance->whereMonth('created_at', $request->monthx);
            $PettyCash = $PettyCash->whereMonth('created_at', $request->monthx);
            $Sales = $Sales->whereMonth('created_at', $request->monthx);
        }

        if ($request->filled('yearx')) {
            $Expense = $Expense->whereYear('created_at', $request->yearx);
            $Attendance = $Attendance->whereYear('created_at', $request->yearx);
            $PettyCash = $PettyCash->whereYear('created_at', $request->yearx);
            $Sales = $Sales->whereYear('created_at', $request->yearx);
        }

        // Salary calculation including bonus adjustments
        $salaryData = $users->map(function($user) use ($month) {
            $workedHours = 0;

            foreach ($user->attendances as $att) {

                if ($att->check_in && $att->check_out) {
                    $checkIn  = Carbon::parse($att->check_in);
                    $checkOut = Carbon::parse($att->check_out);
                    $workedHours += $checkOut->diffInMinutes($checkIn,true) / 60; // more accurate
                }
            }

            // Calculate base salary
            if ($user->salary_type === 'monthly') {
                $baseSalary = $user->salary_amount;
            } elseif ($user->salary_type === 'hourly') {
                $baseSalary = $workedHours * $user->salary_amount;
            } else {
                $baseSalary = 0;
            }

            // Sum bonus adjustments for this month
            $totalBonusAdds = $user->bonusAdjustments
                ->where('action', 'add')
                ->sum('amount');

            $totalBonusRemoves = $user->bonusAdjustments
                ->where('action', 'remove')
                ->sum('amount');

            // Final salary = base + adds - removes
            $finalSalary = $baseSalary + $totalBonusAdds - $totalBonusRemoves;

            return [
                'month'            => Carbon::create(null, $month)->format('F'),
                'name'             => $user->name,
                'worked_hours'     => round($workedHours, 2),
                'salary_type'      => ucfirst($user->salary_type),
                'rate'             => $user->salary_amount,
                'base_salary'      => round($baseSalary, 2),
                'bonus_adds'       => round($totalBonusAdds, 2),
                'bonus_removes'    => round($totalBonusRemoves, 2),
                'calculatedSalary' => round($finalSalary, 2),
            ];
        });

        // ---------------- PROFIT CALCULATION ----------------

        $totalSalaries = $salaryData->sum('calculatedSalary');

        // For Total Income
        $salesRevenue = ProductSale::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get()
            ->sum('price');

        // For Total Expenses
        $MaterialCosts = MaterialStock::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->with('material')
            ->get()
            ->sum(fn($s) => $s->material->price);
        $ExternalCosts = ExternalProductItem::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->with('external_product')
            ->get()
            ->sum(fn($s) => $s->external_product->bought_price);


        // Expenses & Incomes
        $totalExpense = $Expense->clone()->where('type','expense')->sum('amount');
        $totalIncome  = $Expense->clone()->where('type','income')->sum('amount');

        $pettyExpense = $PettyCash->clone()->where('type','expense')->sum('amount');
        $pettyIncome  = $PettyCash->clone()->where('type','income')->sum('amount');

        // Net Profit = (Sales + incomes) - (salaries + expenses + material costs)
        $totalProfit = ($salesRevenue + $pettyIncome + $totalIncome)
            - ($totalSalaries + $MaterialCosts + $ExternalCosts + $pettyExpense + $totalExpense);

        $netProfit = [
            'profit' => round($totalProfit, 2),
            'sales'  => round($salesRevenue, 2),
            'incomes' => round($pettyIncome + $totalIncome, 2),
            'expenses' => round($totalExpense + $pettyExpense, 2),
            'salaries' => round($totalSalaries, 2),
            'material_costs' => round($MaterialCosts, 2),
            'external_costs' => round($ExternalCosts, 2),
            'month' => $month,
            'year'  => $year,
        ];

        // -----------------------------------------------------

        // Grouping for existing data
        $attendanceGrouped = $Attendance->get()->groupBy('user_id');
        $attendance = $attendanceGrouped;
        $salesGrouped = $Sales->get()->groupBy('product_sales_id');
        $sales = $salesGrouped;
        $expenses = $Expense->get();
        $pettyCash = $PettyCash->get();
        $salaryReport = $salaryData;

        $monthy = $request->monthx ?? '';
        $yeary = $request->yearx ?? '';

        switch ($type) {
            case 'main':
                $pdf = PDF::loadView('reports.monthly_report_pdf', compact('netProfit', 'monthy', 'yeary'));
                return $pdf->stream('monthly_report.pdf');
            case 'expenses':
                $pdf = PDF::loadView('reports.monthly_expenses_pdf', compact('expenses', 'monthy', 'yeary'));
                return $pdf->stream('monthly_expenses_report.pdf');
            case 'employee':
                $pdf = PDF::loadView('reports.attendence_pdf', compact('attendance', 'monthy', 'yeary'));
                return $pdf->stream('attendence_report.pdf');
            case 'petty':
                $pdf = PDF::loadView('reports.petty_cash_pdf', compact('pettyCash', 'monthy', 'yeary'));
                return $pdf->stream('petty_cash_report.pdf');
            case 'sales':
                $pdf = PDF::loadView('reports.sales_pdf', compact('sales', 'monthy', 'yeary'));
                return $pdf->stream('sales_report.pdf');
            case 'salary':
                $pdf = PDF::loadView('reports.salary_pdf', compact('salaryReport', 'monthy', 'yeary'));
                return $pdf->stream('salary_report.pdf');
        }

        return redirect()->back()->with('error', 'Invalid report type.');
    }



















    public function salaryIndex(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
        ]);

        $employees = User::all();

        $month = $request->monthx ?? now()->month;
        $year  = $request->yearx ?? now()->year;

        $salaryData = collect();

        if($request->filled('user_id')) {
            // Load users with attendances and bonusAdjustments for the given month and year
            $users = User::with([
                'attendances' => function($q) use ($month, $year) {
                    $q->whereMonth('date', $month)->whereYear('date', $year);
                },
                'bonusAdjustments' => function($q) use ($month, $year) {
                    $q->whereMonth('created_at', $month)->whereYear('created_at', $year);
                }
            ])->where('id', $request->user_id)->withTrashed()->get();

            // Salary calculation including bonus adjustments
            $salaryData = $users->map(function($user) use ($month) {
                $workedHours = 0;

                foreach ($user->attendances as $att) {

                    if ($att->check_in && $att->check_out) {
                        $checkIn  = Carbon::parse($att->check_in);
                        $checkOut = Carbon::parse($att->check_out);
                        $workedHours += $checkOut->diffInMinutes($checkIn,true) / 60; // more accurate
                    }
                }

                // Calculate base salary
                if ($user->salary_type === 'monthly') {
                    $baseSalary = $user->salary_amount;
                } elseif ($user->salary_type === 'hourly') {
                    $baseSalary = $workedHours * $user->salary_amount;
                } else {
                    $baseSalary = 0;
                }

                // Sum bonus adjustments for this month
                $totalBonusAdds = $user->bonusAdjustments
                    ->where('action', 'add')
                    ->sum('amount');

                $totalBonusRemoves = $user->bonusAdjustments
                    ->where('action', 'remove')
                    ->sum('amount');

                // Final salary = base + adds - removes
                $finalSalary = $baseSalary + $totalBonusAdds - $totalBonusRemoves;

                return [
                    'month'            => Carbon::create(null, $month)->format('F'),
                    'name'             => $user->name,
                    'worked_hours'     => round($workedHours, 2),
                    'salary_type'      => ucfirst($user->salary_type),
                    'rate'             => $user->salary_amount,
                    'base_salary'      => round($baseSalary, 2),
                    'bonus_adds'       => round($totalBonusAdds, 2),
                    'bonus_removes'    => round($totalBonusRemoves, 2),
                    'calculatedSalary' => round($finalSalary, 2),
                ];
            });
        }

        return view('pages.salary', [
            'employees'    => $employees,
            'user'         => $request->user_id ?? '',
            'salaryReport' => $salaryData,
            'month'        => $request->monthx ?? '',
            'year'         => $request->yearx ?? '',
        ]);
    }

    public function salaryExport(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
        ]);

        $month = $request->monthx ?? now()->month;
        $year  = $request->yearx ?? now()->year;

        $salaryData = collect();

        if($request->filled('user_id')) {
            // Load users with attendances and bonusAdjustments for the given month and year
            $users = User::with([
                'attendances' => function($q) use ($month, $year) {
                    $q->whereMonth('date', $month)->whereYear('date', $year);
                },
                'bonusAdjustments' => function($q) use ($month, $year) {
                    $q->whereMonth('created_at', $month)->whereYear('created_at', $year);
                }
            ])->where('id', $request->user_id)->withTrashed()->get();

            // Salary calculation including bonus adjustments
            $salaryData = $users->map(function($user) use ($month) {
                $workedHours = 0;

                foreach ($user->attendances as $att) {

                    if ($att->check_in && $att->check_out) {
                        $checkIn  = Carbon::parse($att->check_in);
                        $checkOut = Carbon::parse($att->check_out);
                        $workedHours += $checkOut->diffInMinutes($checkIn,true) / 60; // more accurate
                    }
                }

                // Calculate base salary
                if ($user->salary_type === 'monthly') {
                    $baseSalary = $user->salary_amount;
                } elseif ($user->salary_type === 'hourly') {
                    $baseSalary = $workedHours * $user->salary_amount;
                } else {
                    $baseSalary = 0;
                }

                // Sum bonus adjustments for this month
                $totalBonusAdds = $user->bonusAdjustments
                    ->where('action', 'add')
                    ->sum('amount');

                $totalBonusRemoves = $user->bonusAdjustments
                    ->where('action', 'remove')
                    ->sum('amount');

                // Final salary = base + adds - removes
                $finalSalary = $baseSalary + $totalBonusAdds - $totalBonusRemoves;

                return [
                    'month'            => Carbon::create(null, $month)->format('F'),
                    'name'             => $user->name,
                    'worked_hours'     => round($workedHours, 2),
                    'salary_type'      => ucfirst($user->salary_type),
                    'rate'             => $user->salary_amount,
                    'base_salary'      => round($baseSalary, 2),
                    'bonus_adds'       => round($totalBonusAdds, 2),
                    'bonus_removes'    => round($totalBonusRemoves, 2),
                    'calculatedSalary' => round($finalSalary, 2),
                ];
            });
        }

        $user = User::find($request->user_id);
        $salaryReport = $salaryData;
        $month = $request->monthx ?? '';
        $year = $request->yearx ?? '';

        $pdf = PDF::loadView('reports.salary_slip_pdf', compact('salaryReport', 'user', 'month', 'year'));
        return $pdf->download('salary_slip.pdf');
    }

    public function salaryPrint(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
        ]);

        $month = $request->monthx ?? now()->month;
        $year  = $request->yearx ?? now()->year;

        $salaryData = collect();

        if($request->filled('user_id')) {
            // Load users with attendances and bonusAdjustments for the given month and year
            $users = User::with([
                'attendances' => function($q) use ($month, $year) {
                    $q->whereMonth('date', $month)->whereYear('date', $year);
                },
                'bonusAdjustments' => function($q) use ($month, $year) {
                    $q->whereMonth('created_at', $month)->whereYear('created_at', $year);
                }
            ])->where('id', $request->user_id)->withTrashed()->get();

            // Salary calculation including bonus adjustments
            $salaryData = $users->map(function($user) use ($month) {
                $workedHours = 0;

                foreach ($user->attendances as $att) {

                    if ($att->check_in && $att->check_out) {
                        $checkIn  = Carbon::parse($att->check_in);
                        $checkOut = Carbon::parse($att->check_out);
                        $workedHours += $checkOut->diffInMinutes($checkIn,true) / 60; // more accurate
                    }
                }

                // Calculate base salary
                if ($user->salary_type === 'monthly') {
                    $baseSalary = $user->salary_amount;
                } elseif ($user->salary_type === 'hourly') {
                    $baseSalary = $workedHours * $user->salary_amount;
                } else {
                    $baseSalary = 0;
                }

                // Sum bonus adjustments for this month
                $totalBonusAdds = $user->bonusAdjustments
                    ->where('action', 'add')
                    ->sum('amount');

                $totalBonusRemoves = $user->bonusAdjustments
                    ->where('action', 'remove')
                    ->sum('amount');

                // Final salary = base + adds - removes
                $finalSalary = $baseSalary + $totalBonusAdds - $totalBonusRemoves;

                return [
                    'month'            => Carbon::create(null, $month)->format('F'),
                    'name'             => $user->name,
                    'worked_hours'     => round($workedHours, 2),
                    'salary_type'      => ucfirst($user->salary_type),
                    'rate'             => $user->salary_amount,
                    'base_salary'      => round($baseSalary, 2),
                    'bonus_adds'       => round($totalBonusAdds, 2),
                    'bonus_removes'    => round($totalBonusRemoves, 2),
                    'calculatedSalary' => round($finalSalary, 2),
                ];
            });
        }

        $user = User::find($request->user_id);
        $salaryReport = $salaryData;
        $month = $request->monthx ?? '';
        $year = $request->yearx ?? '';

        $pdf = PDF::loadView('reports.salary_slip_pdf', compact('salaryReport', 'user', 'month', 'year'));
        return $pdf->stream('salary_slip.pdf');
    }

}
