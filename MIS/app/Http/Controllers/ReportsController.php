<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ExternalProductItem;
use App\Models\MaterialStock;
use App\Models\MonthlyExpensesList;
use App\Models\MonthlyExpensesRecord;
use App\Models\PettyCash;
use App\Models\ProductSale;
use App\Models\ProductSalesItem;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $ExpenseList = MonthlyExpensesList::withTrashed()->get();
        $Expense = MonthlyExpensesRecord::with(['expense' => fn($q) => $q->withTrashed()]);

        if ($request->filled('expense_type')) {
            $Expense = $Expense->where('type', $request->expense_type);
        }

        if ($request->filled('expense_id')) {
            $Expense = $Expense->where('expense_id', $request->expense_id);
        }

        $Attendance = Attendance::with(['user' => fn($q) => $q->withTrashed()])
            ->get();

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
            $Attendance = $Attendance->whereMonth('date', $request->monthx);
            $PettyCash = $PettyCash->whereMonth('created_at', $request->monthx);
            $Sales = $Sales->whereMonth('created_at', $request->monthx);
        }

        if ($request->filled('yearx')) {
            $Expense = $Expense->whereYear('created_at', $request->yearx);
            $Attendance = $Attendance->whereYear('date', $request->yearx);
            $PettyCash = $PettyCash->whereYear('created_at', $request->yearx);
            $Sales = $Sales->whereYear('created_at', $request->yearx);
        }

        if ($request->filled('dayx')) {
            $Expense = $Expense->whereDay('created_at', $request->dayx);
            $Attendance = $Attendance->whereDay('date', $request->dayx);
            $PettyCash = $PettyCash->whereDay('created_at', $request->dayx);
            $Sales = $Sales->whereDay('created_at', $request->dayx);
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

        $records = MonthlyExpensesRecord::with(['expense' => fn($q) => $q->withTrashed()])
            ->get()
            ->groupBy('type');

        // Expenses grouped by title with total
        $expenseRecord = $records->get('expense', collect())
            ->groupBy('expense.title')
            ->map(fn($items) => [
                'title' => $items->first()->expense->title,
                'total' => $items->sum('amount'),
            ])
            ->values();

        // Incomes grouped by title with total
        $incomeRecord = $records->get('income', collect())
            ->groupBy('expense.title')
            ->map(fn($items) => [
                'title' => $items->first()->expense->title,
                'total' => $items->sum('amount'),
            ])
            ->values();

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
            'petty_expenses' => round($pettyExpense, 2),
            'petty_incomes' => round($pettyIncome, 2),
            'total_incomes' => round($totalIncome, 2),
            'total_expenses' => round($totalExpense, 2),
            'salaries' => round($totalSalaries, 2),
            'material_costs' => round($MaterialCosts, 2),
            'external_costs' => round($ExternalCosts, 2),
            'expense_record' => $expenseRecord,
            'income_record' => $incomeRecord,
            'month' => $month,
            'year'  => $year,
        ];

        // -----------------------------------------------------

        // Purchase Report
        // -----------------------------------------------------

        // Materials
        $materials = DB::table('material_stocks')
            ->join('materials', 'material_stocks.material_id', '=', 'materials.id')
            ->selectRaw("
                DATE(material_stocks.created_at) as date,
                materials.name as item_name,
                COUNT(material_stocks.id) as quantity,
                'material' as type,
                SUM(materials.price) as total_price
            ")
            ->groupBy('date', 'materials.name');

        // External Products
        $externalProducts = DB::table('external_product_items')
            ->join('external_products', 'external_product_items.external_product_id', '=', 'external_products.id')
            ->selectRaw("
                DATE(external_product_items.created_at) as date,
                external_products.name as item_name,
                COUNT(external_product_items.id) as quantity,
                'external_item' as type,
                SUM(external_products.bought_price) as total_price
            ")
            ->groupBy('date', 'external_products.name');

        // Merge both queries with UNION
        $purchases = $materials->unionAll($externalProducts);

        // Wrap in subquery
        $query = DB::table(DB::raw("({$purchases->toSql()}) as purchases"))
            ->mergeBindings($materials) // merge bindings for Laravel
            ->orderBy('date', 'desc');

        // Apply filters
        if ($request->monthx) {
            $query->whereMonth('date', $request->monthx);
        }

        if ($request->yearx) {
            $query->whereYear('date', $request->yearx);
        }

        if ($request->dayx) {
            $query->whereDay('date', $request->dayx);
        }

        // Now execute
        $purchases = $query->get();

        // -----------------------------------------------------


        // Grouping for existing data
        $attendanceGrouped = $Attendance->groupBy('user_id')->map(function ($attendances) {
            return $attendances->map(function ($attendance) {
                // If checkout exists and is after checkin
                if ($attendance->check_in && $attendance->check_out) {
                    $checkIn = Carbon::parse($attendance->check_in);
                    $checkOut = Carbon::parse($attendance->check_out);

                    $attendance->hours_worked = round($checkOut->diffInMinutes($checkIn,true) / 60,2);
                } else {
                    $attendance->hours_worked = 0;
                }
                return $attendance;
            });
        });
        if ($request->user_id) {
            $attendanceGrouped = $attendanceGrouped->only([$request->user_id]);
        }


        Log::info($attendanceGrouped);

        $salesGrouped = $Sales
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('product_sales_id');

        return view('pages.reports', [
            'expenses'     => $Expense->get(),
            'attendance'   => $attendanceGrouped,
            'pettyCash'    => $PettyCash->orderBy('created_at', 'desc')->get(),
            'sales'        => $salesGrouped,
            'salaryReport' => $salaryData,
            'netProfit'    => $netProfit,
            'purchases'    => $purchases,
            'expenseList'  => $ExpenseList,
            'expense_id'   => $request->expense_id ?? '',
            'expense_type' => $request->expense_type ?? '',
            'employee'     => $employees,
            'user_id'      => $request->user_id ?? '',
            'month'        => $request->monthx ?? '',
            'year'         => $request->yearx ?? '',
            'day'          => $request->dayx ?? '',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reportType' => 'required|in:main,expenses,employee,petty,sales,salary,purchase',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $type = $validator->validated()['reportType'];

        $month = $request->monthx ?? now()->month;
        $year  = $request->yearx ?? now()->year;

        // Existing queries
        $Expense = MonthlyExpensesRecord::with(['expense' => fn($q) => $q->withTrashed()]);

        if ($request->filled('expense_type')) {
            $Expense = $Expense->where('type', $request->expense_type);
        }

        if ($request->filled('expense_id')) {
            $Expense = $Expense->where('expense_id', $request->expense_id);
        }

        $Attendance = Attendance::with(['user' => fn($q) => $q->withTrashed()])
            ->get();

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

        if ($request->filled('dayx')) {
            $Expense = $Expense->whereDay('created_at', $request->dayx);
            $Attendance = $Attendance->whereDay('created_at', $request->dayx);
            $PettyCash = $PettyCash->whereDay('created_at', $request->dayx);
            $Sales = $Sales->whereDay('created_at', $request->dayx);
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

        $records = MonthlyExpensesRecord::with(['expense' => fn($q) => $q->withTrashed()])
            ->get()
            ->groupBy('type');

        // Expenses grouped by title with total
        $expenseRecord = $records->get('expense', collect())
            ->groupBy('expense.title')
            ->map(fn($items) => [
                'title' => $items->first()->expense->title,
                'total' => $items->sum('amount'),
            ])
            ->values();

        // Incomes grouped by title with total
        $incomeRecord = $records->get('income', collect())
            ->groupBy('expense.title')
            ->map(fn($items) => [
                'title' => $items->first()->expense->title,
                'total' => $items->sum('amount'),
            ])
            ->values();


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
            'petty_expenses' => round($pettyExpense, 2),
            'petty_incomes' => round($pettyIncome, 2),
            'total_incomes' => round($totalIncome, 2),
            'total_expenses' => round($totalExpense, 2),
            'salaries' => round($totalSalaries, 2),
            'material_costs' => round($MaterialCosts, 2),
            'external_costs' => round($ExternalCosts, 2),
            'expense_record' => $expenseRecord,
            'income_record' => $incomeRecord,
            'month' => $month,
            'year'  => $year,
        ];

        // -----------------------------------------------------

        // Purchase Report
        // -----------------------------------------------------

        // Materials
        $materials = DB::table('material_stocks')
            ->join('materials', 'material_stocks.material_id', '=', 'materials.id')
            ->selectRaw("
                DATE(material_stocks.created_at) as date,
                materials.name as item_name,
                COUNT(material_stocks.id) as quantity,
                'material' as type,
                SUM(materials.price) as total_price
            ")
            ->groupBy('date', 'materials.name');

        // External Products
        $externalProducts = DB::table('external_product_items')
            ->join('external_products', 'external_product_items.external_product_id', '=', 'external_products.id')
            ->selectRaw("
                DATE(external_product_items.created_at) as date,
                external_products.name as item_name,
                COUNT(external_product_items.id) as quantity,
                'external_item' as type,
                SUM(external_products.bought_price) as total_price
            ")
            ->groupBy('date', 'external_products.name');

        // Merge both queries with UNION
        $purchases = $materials->unionAll($externalProducts);

        // Wrap in subquery
        $query = DB::table(DB::raw("({$purchases->toSql()}) as purchases"))
            ->mergeBindings($materials) // merge bindings for Laravel
            ->orderBy('date', 'desc');

        // Apply filters
        if ($request->monthx) {
            $query->whereMonth('date', $request->monthx);
        }

        if ($request->yearx) {
            $query->whereYear('date', $request->yearx);
        }

        if ($request->dayx) {
            $query->whereDay('date', $request->dayx);
        }

        // Now execute
        $purchases = $query->get();

        // -----------------------------------------------------


        // Grouping for existing data
        $attendanceGrouped = $Attendance->groupBy('user_id')->map(function ($attendances) {
            return $attendances->map(function ($attendance) {
                // If checkout exists and is after checkin
                if ($attendance->check_in && $attendance->check_out) {
                    $checkIn = Carbon::parse($attendance->check_in);
                    $checkOut = Carbon::parse($attendance->check_out);

                    $attendance->hours_worked = round($checkOut->diffInMinutes($checkIn,true) / 60,2);
                } else {
                    $attendance->hours_worked = 0;
                }
                return $attendance;
            });
        });
        if ($request->user_id) {
            $attendanceGrouped = $attendanceGrouped->only([$request->user_id]);
        }
        $attendance = $attendanceGrouped;

        $salesGrouped = $Sales
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('product_sales_id');
        $sales = $salesGrouped;
        $expenses = $Expense->get();
        $pettyCash = $PettyCash->orderBy('created_at', 'desc')->get();
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
            case 'purchase':
                $pdf = PDF::loadView('reports.purchase_pdf', compact('purchases', 'monthy', 'yeary'));
                return $pdf->download('purchase_report.pdf');
        }

        return redirect()->back()->with('error', 'Invalid report type.');
    }

    public function PrintPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reportType' => 'required|in:main,expenses,employee,petty,sales,salary,purchase',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $type = $validator->validated()['reportType'];

        $month = $request->monthx ?? now()->month;
        $year  = $request->yearx ?? now()->year;

        // Existing queries
        $Expense = MonthlyExpensesRecord::with(['expense' => fn($q) => $q->withTrashed()]);

        if ($request->filled('expense_type')) {
            $Expense = $Expense->where('type', $request->expense_type);
        }

        if ($request->filled('expense_id')) {
            $Expense = $Expense->where('expense_id', $request->expense_id);
        }

        $Attendance = Attendance::with(['user' => fn($q) => $q->withTrashed()])
            ->get();

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

        if ($request->filled('dayx')){
            $Expense = $Expense->whereDay('created_at', $request->dayx);
            $Attendance = $Attendance->whereDay('created_at', $request->dayx);
            $PettyCash = $PettyCash->whereDay('created_at', $request->dayx);
            $Sales = $Sales->whereDay('created_at', $request->dayx);
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

        $records = MonthlyExpensesRecord::with(['expense' => fn($q) => $q->withTrashed()])
            ->get()
            ->groupBy('type');

        // Expenses grouped by title with total
        $expenseRecord = $records->get('expense', collect())
            ->groupBy('expense.title')
            ->map(fn($items) => [
                'title' => $items->first()->expense->title,
                'total' => $items->sum('amount'),
            ])
            ->values();

        // Incomes grouped by title with total
        $incomeRecord = $records->get('income', collect())
            ->groupBy('expense.title')
            ->map(fn($items) => [
                'title' => $items->first()->expense->title,
                'total' => $items->sum('amount'),
            ])
            ->values();


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
            'petty_expenses' => round($pettyExpense, 2),
            'petty_incomes' => round($pettyIncome, 2),
            'total_incomes' => round($totalIncome, 2),
            'total_expenses' => round($totalExpense, 2),
            'salaries' => round($totalSalaries, 2),
            'material_costs' => round($MaterialCosts, 2),
            'external_costs' => round($ExternalCosts, 2),
            'expense_record' => $expenseRecord,
            'income_record' => $incomeRecord,
            'month' => $month,
            'year'  => $year,
        ];

        // -----------------------------------------------------

        // Purchase Report
        // -----------------------------------------------------

        // Materials
        $materials = DB::table('material_stocks')
            ->join('materials', 'material_stocks.material_id', '=', 'materials.id')
            ->selectRaw("
                DATE(material_stocks.created_at) as date,
                materials.name as item_name,
                COUNT(material_stocks.id) as quantity,
                'material' as type,
                SUM(materials.price) as total_price
            ")
            ->groupBy('date', 'materials.name');

        // External Products
        $externalProducts = DB::table('external_product_items')
            ->join('external_products', 'external_product_items.external_product_id', '=', 'external_products.id')
            ->selectRaw("
                DATE(external_product_items.created_at) as date,
                external_products.name as item_name,
                COUNT(external_product_items.id) as quantity,
                'external_item' as type,
                SUM(external_products.bought_price) as total_price
            ")
            ->groupBy('date', 'external_products.name');

        // Merge both queries with UNION
        $purchases = $materials->unionAll($externalProducts);

        // Wrap in subquery
        $query = DB::table(DB::raw("({$purchases->toSql()}) as purchases"))
            ->mergeBindings($materials) // merge bindings for Laravel
            ->orderBy('date', 'desc');

        // Apply filters
        if ($request->monthx) {
            $query->whereMonth('date', $request->monthx);
        }

        if ($request->yearx) {
            $query->whereYear('date', $request->yearx);
        }

        if ($request->dayx) {
            $query->whereDay('date', $request->dayx);
        }

        // Now execute
        $purchases = $query->get();

        // -----------------------------------------------------

        // Grouping for existing data
        $attendanceGrouped = $Attendance->groupBy('user_id')->map(function ($attendances) {
            return $attendances->map(function ($attendance) {
                // If checkout exists and is after checkin
                if ($attendance->check_in && $attendance->check_out) {
                    $checkIn = Carbon::parse($attendance->check_in);
                    $checkOut = Carbon::parse($attendance->check_out);

                    $attendance->hours_worked = round($checkOut->diffInMinutes($checkIn,true) / 60,2);
                } else {
                    $attendance->hours_worked = 0;
                }
                return $attendance;
            });
        });
        if ($request->user_id) {
            $attendanceGrouped = $attendanceGrouped->only([$request->user_id]);
        }
        $attendance = $attendanceGrouped;

        $salesGrouped = $Sales
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('product_sales_id');
        $sales = $salesGrouped;
        $expenses = $Expense->get();
        $pettyCash = $PettyCash->orderBy('created_at', 'desc')->get();
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
            case 'purchase':
                $pdf = PDF::loadView('reports.purchase_pdf', compact('purchases', 'monthy', 'yeary'));
                return $pdf->stream('purchase_report.pdf');
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

        $attendanceGrouped = collect();

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

            //attendance list
            $Attendance = Attendance::with(['user' => fn($q) => $q->withTrashed()])
                ->where('user_id', $request->user_id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->get();

            $attendanceGrouped = $Attendance->groupBy('user_id')->map(function ($attendances) {
                return $attendances->map(function ($attendance) {
                    // If checkout exists and is after checkin
                    if ($attendance->check_in && $attendance->check_out) {
                        $checkIn = Carbon::parse($attendance->check_in);
                        $checkOut = Carbon::parse($attendance->check_out);

                        $attendance->hours_worked = round($checkOut->diffInMinutes($checkIn,true) / 60,2);
                    } else {
                        $attendance->hours_worked = 0;
                    }
                    return $attendance;
                });
            });

            // Salary calculation including bonus adjustments
            $salaryData = $users->map(function($user) use ($year, $month) {
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
            'attendance'   => $attendanceGrouped,
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
