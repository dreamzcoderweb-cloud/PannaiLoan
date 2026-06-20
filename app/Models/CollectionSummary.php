<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionSummary extends Model
{
    protected $table = 'collection_summary';
    protected $primaryKey = 'collection_sum_id';

    protected $fillable = [
        'previous_date',
        'previous_total_paidamount',
        'current_date',
        'current_total_paidamount',
        'total_amount',
        'total_loan',
        'total_loanamount',
        'expense_amount_currentdate',
        'final_balance_amount',
        'report_type',
        'from_date',
        'to_date',
        'md_fund_in',
        'md_fund_out',
    ];

    protected $casts = [
        'previous_date' => 'date',
        'current_date' => 'date',
        'previous_total_paidamount' => 'decimal:2',
        'current_total_paidamount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'total_loanamount' => 'decimal:2',
        'expense_amount_currentdate' => 'decimal:2',
        'final_balance_amount' => 'decimal:2',
    ];
}

