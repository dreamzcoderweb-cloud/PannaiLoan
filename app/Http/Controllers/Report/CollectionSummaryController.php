<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\CollectionSummary;
use Illuminate\Http\Request;

class CollectionSummaryController extends Controller
{
    public function saveFromDailyReport(Request $request)
    {

        $report_type = $request->input("report_type");
        if ($report_type == "daily") {
            $data = $request->validate([
                'previous_date' => ['nullable', 'date'],
                'previous_total_paidamount' => ['required', 'numeric', 'min:0'],
                'current_date' => ['required', 'date'],
                'current_total_paidamount' => ['required', 'numeric', 'min:0'],
                'total_amount' => ['required', 'numeric', 'min:0'],
                'total_loan' => ['required', 'integer', 'min:0'],
                'total_loanamount' => ['required', 'numeric', 'min:0'],
                'expense_amount_currentdate' => ['required', 'numeric', 'min:0'],
                'final_balance_amount' => ['required', 'numeric'],
                'md_fund_in' => ['nullable', 'numeric'],
                'md_fund_out' => ['nullable', 'numeric'],
            ]);
            CollectionSummary::updateOrCreate(
                ['current_date' => $data['current_date']],
                $data
            );
        } else {
            $data = $request->validate([
                'from_date' => ['required', 'date'],
                'to_date' => ['required', 'date'],
                'current_total_paidamount' => ['required', 'numeric', 'min:0'],
                'md_fund_in' => ['nullable', 'numeric'],
                'md_fund_out' => ['nullable', 'numeric'],
                'total_amount' => ['required', 'numeric', 'min:0'],
                'total_loan' => ['required', 'integer', 'min:0'],
                'total_loanamount' => ['required', 'numeric', 'min:0'],
                'expense_amount_currentdate' => ['required', 'numeric', 'min:0'],
                'final_balance_amount' => ['required', 'numeric'],
            ]);

            CollectionSummary::updateOrCreate(
                [
                    'from_date' => $data['from_date'],
                    'to_date' => $data['to_date']
                ],
                $data
            );
        }


        return back()->with('success', 'Collection summary saved successfully.');
    }
}
