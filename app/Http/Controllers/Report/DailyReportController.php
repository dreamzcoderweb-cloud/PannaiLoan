<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Emicollection;
use App\Models\EmicollectionDetail;
use App\Models\Expense;
use App\Models\LoanAssign;
use App\Models\CollectionSummary;
use App\Models\Branch;
use App\Models\Route;
class DailyReportController extends Controller
{
    public function daily_rep()
    {
        $today = now()->toDateString();
        $getbranches = Branch::all();
        $getroutes = Route::all();

        return view('Admin.Report.daily', compact('today', 'getbranches', 'getroutes'));
    }

    public function getRoutesByBranch($branchId)
    {
        $routes = Route::where('branch_id', $branchId)->get();
        return response()->json([
            'status' => true,
            'data' => $routes
        ]);
    }

    public function dailyFilter(Request $request)
    {
        $getbranches = Branch::all();
        $getroutes = Route::all();
        $branchId = $request->branch_id;
        //$routeId = $request->route_id;
        $today = Carbon::today()->format('Y-m-d');
        $selectedDate = $request->monthlydue_date ?? $today;
        $date = Carbon::parse($selectedDate);
        // Previous date variable
        $previousDate = Carbon::parse($date)->subDay();
        // Get EMI collections for the selected date (for individual records table)
        $emiCollections = Emicollection::with([
            'clientname','branch','routename',
            'loanassign.loan',
            'details' => function ($query) use ($date) {
                $query->whereDate('due_date', $date->toDateString())
                    ->orWhereDate('paid_date', $date->toDateString());
            }
        ])->when($branchId, function ($query) use ($branchId) {
            $query->whereHas('loanassign', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        })->whereHas('details', function ($query) use ($date) {
            $query->whereDate('due_date', $date->toDateString())
                ->orWhereDate('paid_date', $date->toDateString());
        })->get();


        //previous total paid amount
        $previous_emiCollections = Emicollection::with([
            'clientname',
            'loanassign.loan',
            'details' => function ($query) use ($previousDate) {

                $query->whereDate('due_date', $previousDate->format('Y-m-d'))
                    ->orWhereDate('paid_date', $previousDate->format('Y-m-d'));
            }
        ])->when($branchId, function ($query) use ($branchId) {
            $query->whereHas('loanassign', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        })->whereHas('details', function ($query) use ($previousDate) {

            $query->whereDate('due_date', $previousDate->format('Y-m-d'))
                ->orWhereDate('paid_date', $previousDate->format('Y-m-d'));
        })->get();
        // 1. Current Day Collection Amount Logic
        // Sum of all EMI payments made today
        $currentPayments = EmicollectionDetail::whereDate('paid_date', $date->toDateString())->sum('paid_amount');

        // Upfront Deductions (Interest/Fees kept by the system when giving loans today)
        $currentUpfrontDeductions = LoanAssign::whereDate('created_at', $date->toDateString())
        ->when($branchId, function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        })
        ->get()
        ->sum(function ($loan) {
        if ($loan->total_distribution > 0 && $loan->loan_amount > $loan->total_distribution) {
            return $loan->loan_amount - $loan->total_distribution;
        }

        return 0;
        });

        // Total Current Collection = EMI Payments + Upfront Deductions
        $currentCollection = $currentPayments + $currentUpfrontDeductions;

        // 2. Previous Collection Amount (Starting Balance)
        // Previous EMI Payments
        $prevPaymentsTotal = EmicollectionDetail::whereDate('paid_date', '<', $date->toDateString())->sum('paid_amount');

        // Previous Upfront Deductions
        $prevUpfrontTotal = LoanAssign::whereDate('created_at', '<', $date->toDateString())
        ->when($branchId, function ($query) use ($branchId) {
        $query->where('branch_id', $branchId);
        })->get()
            ->sum(function ($loan) {
                if ($loan->total_distribution > 0 && $loan->loan_amount > $loan->total_distribution) {
                    return $loan->loan_amount - $loan->total_distribution;
                }
                return 0;
            });
        $totalPrevIn = $prevPaymentsTotal + $prevUpfrontTotal;

        // Previous Loans (Gross Amount)
       $totalPrevLoans = LoanAssign::whereDate('created_at', '<', $date->toDateString())
    ->when($branchId, function ($query) use ($branchId) {
        $query->where('branch_id', $branchId);
    })
            ->get()
            ->sum(fn($loan) => $this->getLoanAmount($loan));
        // Previous Expenses
        $totalPrevExpenses = Expense::whereDate('expense_date', '<', $date->toDateString())->sum('expense_amount');

        // Previous Balance = (Total Money In) - (Total Money Out)
        $previousCollection = $totalPrevIn - $totalPrevLoans - $totalPrevExpenses;
        $previousDateLabel = $date->copy()->subDay()->format('d-m-Y');

        // 3. Total Amount (Carry Forward + Today's Collection)
        $totalAmount = $previousCollection + $currentCollection;

        // 4. Expenses Amount Current Day
        // First check expense table current date record
        $expenseExists = Expense::whereDate('expense_date', $date->toDateString())
            ->exists();

        if ($expenseExists) {

            // Get from expense table
            $totalExpenses = Expense::whereDate('expense_date', $date->toDateString())
                ->sum('expense_amount');

        } else {

            // If no expense record means get from collection summary table
            $totalExpenses = CollectionSummary::whereDate(
                'current_date',
                $date->toDateString()
            )
                ->value('expense_amount_currentdate') ?? 0;
        }

        // 5. Total Loan Amount (Gross) today
        // First check loan assign table current date record
        $loanExists = LoanAssign::whereDate('created_at', $date->toDateString())
            ->exists();

        if ($loanExists) {

            // Get from loan assign table
           $totalLoanAmountToday = LoanAssign::whereDate('created_at', $date->toDateString())
    ->when($branchId, function ($query) use ($branchId) {
        $query->where('branch_id', $branchId);
    })
                ->get()
                ->sum(fn($loan) => $this->getLoanAmount($loan));

            $totalLoansTodayCount = LoanAssign::whereDate('created_at', $date->toDateString())
    ->when($branchId, function ($query) use ($branchId) {
        $query->where('branch_id', $branchId);
    })
    ->count();

        } else {

            // If no loan records means get from collection summary table
            $summary = CollectionSummary::whereDate(
                'current_date',
                $date->toDateString()
            )->first();

            $totalLoanAmountToday = $summary->total_loanamount ?? 0;

            $totalLoansTodayCount = $summary->total_loan ?? 0;
        }

        // 6. Final Balance Amount
        // final balance = total amount - total loan amount - total expenses
        $finalBalance = $totalAmount - $totalLoanAmountToday - $totalExpenses;

        // For individual records total in footer
        $totalPaidAmount = 0;
        foreach ($emiCollections as $collection) {
            $totalPaidAmount += $collection->details->sum('paid_amount');
        }
        //Previous total paid amount
        $previous_totalPaidAmount = 0;
        foreach ($previous_emiCollections as $collection) {
            $previous_totalPaidAmount += $collection->details->sum('paid_amount');
        }
        //dd($previous_totalPaidAmount);
        $previousFinalBalance = CollectionSummary::whereDate('current_date', $previousDate->toDateString())
            ->value('final_balance_amount') ?? 0;
        // dd($previousFinalBalance);
        $existingSummary = CollectionSummary::whereDate(
            'current_date',
            $date->toDateString()
        )->first();
        $md_fund_in = $existingSummary->md_fund_in ?? 0;
        $md_fund_out = $existingSummary->md_fund_out ?? 0;
        /*
        |--------------------------------------------------------------------------
        | Previous Date Final Balance Amount
        |--------------------------------------------------------------------------
        */

        $previousFinalBalance = CollectionSummary::whereDate(
            'current_date',
            $previousDate->toDateString()
        )->value('final_balance_amount') ?? 0;

        /*
        |--------------------------------------------------------------------------
        | Current Date Total Paid Amount
        |--------------------------------------------------------------------------
        */

        // Always recompute from actual daily records; saved summary can become stale
        // if new payments are added after the summary was saved.
        $currentTotalPaidAmount = $totalPaidAmount;

        /*
        |--------------------------------------------------------------------------
        | Total Amount
        |--------------------------------------------------------------------------
        */

        $totalAmount = $previousFinalBalance + $currentTotalPaidAmount;

        /*
        |--------------------------------------------------------------------------
        | Final Balance Amount
        |--------------------------------------------------------------------------
        */

        $finalBalanceAmount = $totalAmount - $totalLoanAmountToday - $totalExpenses;

        /*
        |--------------------------------------------------------------------------
        | Form Array
        |--------------------------------------------------------------------------
        */

        $collectionSummaryForm = [

            'previous_date' => $previousDate->toDateString(),

            'previous_total_paidamount' => $previousFinalBalance,

            'current_date' => $date->toDateString(),

            'current_total_paidamount' => $currentTotalPaidAmount,

            'total_amount' => $totalAmount,

            'total_loan' => $totalLoansTodayCount,

            'total_loanamount' => $totalLoanAmountToday,

            'expense_amount_currentdate' => $totalExpenses,

            'final_balance_amount' => $finalBalanceAmount,
        ];


        return view('Admin.Report.daily', compact(
            'getbranches',
            'getroutes',
            'emiCollections',
            'selectedDate',
            'today',
            'md_fund_in',
            'md_fund_out',
            'previousCollection',
            'previousDateLabel',
            'currentCollection',
            'totalAmount',
            'totalExpenses',
            'totalLoanAmountToday',
            'totalLoansTodayCount',
            'totalPaidAmount',
            'previous_totalPaidAmount',
            'previousFinalBalance',
            'existingSummary',
            'branchId',
        ));
    }

    private function getLoanAmount($loan)
    {
        if ($loan->collection_type_id == 2) {
            return $loan->total_distribution;
        }

        if ($loan->collection_type_id == 3) {
            return $loan->loan_amount;
        }

        return 0;
    }

    public function loan_collection()
    {
        return $this->loanFilter(new Request());
    }

    public function loanFilter(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $query = LoanAssign::with(['client_name', 'loan', 'branches', 'routes', 'employee']);

        if ($from_date && $to_date) {
            $query->whereDate('created_at', '>=', $from_date)
                ->whereDate('created_at', '<=', $to_date);
        }

        $loanAssignments = $query->get();

        if ($request->ajax()) {
            return view('Admin.Report.partials.loan_table', compact('loanAssignments', 'from_date', 'to_date'))->render();
        }

        return view('Admin.Report.loan_collection', compact('loanAssignments', 'from_date', 'to_date'));
    }

    public function total()
    {
        return view('Admin.Report.total');
    }


public function Filter(Request $request)
{
    $fromDate = Carbon::parse($request->from_date)->format('Y-m-d');
    $toDate = Carbon::parse($request->to_date)->format('Y-m-d');

    $emiCollections = Emicollection::with([
        'clientname',
        'loanassign.loan',
        'details' => function ($query) use ($fromDate, $toDate) {
            $query->whereBetween('due_date', [$fromDate, $toDate])
                ->orWhereBetween('paid_date', [$fromDate, $toDate]);
        }
    ])->whereHas('details', function ($query) use ($fromDate, $toDate) {
        $query->whereBetween('due_date', [$fromDate, $toDate])
            ->orWhereBetween('paid_date', [$fromDate, $toDate]);
    })->get();

    $loans = LoanAssign::whereDate('created_at', '>=', $fromDate)
        ->whereDate('created_at', '<=', $toDate)
        ->get();

    $totalLoanCount = $loans->count();

    $totalLoanAmount = $loans->sum(
        fn($loan) => $this->getLoanAmount($loan)
    );

    $totalExpenseAmount = Expense::whereDate('expense_date', '>=', $fromDate)
        ->whereDate('expense_date', '<=', $toDate)
        ->sum('expense_amount');

    $CtotalPaidAmount = 0;

    foreach ($emiCollections as $collection) {
        $CtotalPaidAmount += $collection->details->sum('paid_amount');
    }

    CollectionSummary::updateOrCreate(
        [
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'report_type' => 'total',
        ],
        [
            'total_loan' => $totalLoanCount,
            'total_loanamount' => $totalLoanAmount,
            'current_total_paidamount' => $CtotalPaidAmount,
        ]
    );

    $collections = CollectionSummary::whereDate('from_date', '>=', $fromDate)
        ->whereDate('to_date', '<=', $toDate)
        ->first();

    return view(
        'Admin.Report.total',
        compact(
            'emiCollections',
            'fromDate',
            'toDate',
            'totalLoanCount',
            'totalLoanAmount',
            'CtotalPaidAmount',
            'totalExpenseAmount',
            'collections'
        )
    );
}

}
