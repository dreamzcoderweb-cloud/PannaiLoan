<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmicollectionDetail extends Model
{
    use HasFactory;

    protected $table = 'emicollection_details';

    protected $fillable = [

        'emi_collection_id',
        'loan_type_id',
        'daily_duedays_id',
        'week_duedays_id',
        'installment_no',
        'installment_date',
        'due_date',
        'emi_amount',
        'remaining_payable_amount',
        'status',
        'paid_amount',
        'paid_date',
        'discount',
    ];
    public function emiCollection()
    {
        return $this->belongsTo(Emicollection::class,  'emi_collection_id');
    }
}
