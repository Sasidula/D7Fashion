<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
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
        $users = User::with([
            'attendances' => function($q) use ($month, $year) {
                $q->whereMonth('date', $month)->whereYear('date', $year);
            },
            'bonusAdjustments' => function($q) use ($month, $year) {
                $q->whereMonth('created_at', $month)->whereYear('created_at', $year);
            }
        ])->withTrashed()->get();

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

        // Grouping for existing data
        $attendanceGrouped = $Attendance->get()->groupBy('user_id');
        $salesGrouped = $Sales->get()->groupBy('product_sales_id');

        return view('pages.reports', [
            'expenses'     => $Expense->get(),
            'attendance'   => $attendanceGrouped,
            'pettyCash'    => $PettyCash->get(),
            'sales'        => $salesGrouped,
            'salaryReport' => $salaryData,
            'month'        => $request->monthx ?? '',
            'year'         => $request->yearx ?? '',
        ]);
    }



    public function exportPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reportType' => 'required|in:expenses,employee,petty,sales,salary',
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
        $users = User::with([
            'attendances' => function($q) use ($month, $year) {
                $q->whereMonth('date', $month)->whereYear('date', $year);
            },
            'bonusAdjustments' => function($q) use ($month, $year) {
                $q->whereMonth('created_at', $month)->whereYear('created_at', $year);
            }
        ])->withTrashed()->get();

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
}
