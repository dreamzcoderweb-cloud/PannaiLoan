<?php

namespace App\Exports;

use App\Models\Loan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LoansExport implements FromCollection, WithHeadings, WithMapping
{
    protected $searchTerm;

    /**
     * Constructor to receive search term
     */
    public function __construct($searchTerm = '')
    {
        $this->searchTerm = $searchTerm;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Loan::query();

        // Apply search if term exists
        if (!empty($this->searchTerm)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('loan_number', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('status', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('type', 'like', '%' . $this->searchTerm . '%');
                // Add more fields as needed
            });
        }

        return $query->get();
    }

    /**
     * Define headings
     */
    public function headings(): array
    {
        return [
            'ID',
            'Loan Name',
            'Created At',
        ];
    }

    /**
     * Map data for export
     */
    public function map($loan): array
    {
        return [
            $loan->id,
            $loan->loan_name ?? 'N/A',
            $loan->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
