<?php

namespace App\Exports;

use App\Models\LoanAssign;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LoanAssignExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    protected $searchTerm;

    public function __construct($searchTerm = null)
    {
        $this->searchTerm = $searchTerm;
    }

    public function query()
    {
        $query = LoanAssign::with('int', 'loan', 'branches', 'routes', 'client_name', 'latestEmiCollection.latestDetail');

        if ($this->searchTerm) {
            $query->where(function ($q) {
                $searchTerm = $this->searchTerm;
                $q->where('phone', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('client_name', function ($q2) use ($searchTerm) {
                        $q2->where('name', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('loan', function ($q3) use ($searchTerm) {
                        $q3->where('loan_name', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('branches', function ($q4) use ($searchTerm) {
                        $q4->where('branch_name', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('routes', function ($q5) use ($searchTerm) {
                        $q5->where('route_name', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        return $query;
    }

    public function map($loanAssign): array
    {
        // Determine collection type text
        $collectionType = match ($loanAssign->collection_type_id) {
            1 => 'Daily',
            2 => 'Weekly',
            3 => 'Monthly',
            default => '---'
        };

        // Determine EMI amount based on collection type
        $emiAmount = match ($loanAssign->collection_type_id) {
            1 => $loanAssign->daily_emi ?? '---',
            2 => $loanAssign->weekly_emi ?? '---',
            3 => $loanAssign->monthly_emi ?? '---',
            default => '---'
        };

        // Get remaining payable amount
        $remainingPayable = '----';
        if ($loanAssign->latestEmiCollection && $loanAssign->latestEmiCollection->latestDetail) {
            $remainingPayable = $loanAssign->latestEmiCollection->latestDetail->remaining_payable_amount ?? '----';
        }

        return [
            $loanAssign->id,
            $loanAssign->client_name->name ?? '---',
            $loanAssign->phone,
            $loanAssign->loan->loan_name ?? '---',
            $loanAssign->int->interest_id ?? '---',
            $collectionType,
            $emiAmount,
            $loanAssign->total_payableamt ?? '---',
            $remainingPayable,
            $loanAssign->branches->branch_name ?? '---',
            $loanAssign->routes->route_name ?? '---',
            $loanAssign->loan_amount,

        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Customer Name',
            'Phone',
            'Loan Name',
            'Loan Interest',
            'Loan Collection Type',
            'EMI Amount',
            'Total Payable Amount',
            'Remaining Amount',
            'Branch',
            'Route',
            'Loan Amount',
        ];
    }
}
