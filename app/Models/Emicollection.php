<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emicollection extends Model
{
    use HasFactory;
    protected $table = 'emicollections';
    protected $fillable = [
        'loan_assign_id',
        'client_id',
        'collection_type_id',
        'total_payable_amount',
        'total_collected',
        'total_remaining',
        'status',
        'discount',
        'Collect_by',
        'emp_id',
    ];
    public function details()
    {
        return $this->hasMany(EmicollectionDetail::class, 'emi_collection_id', 'id');
    }

    public function getCollectionTypeAttribute()
    {
    return match ($this->collection_type_id) {
        1 => 'Daily',
        2 => 'Weekly',
        3 => 'Monthly',
        default => 'Unknown',
    };
    }

    protected $appends = ['collection_type'];

    //  Get latest remaining amount
    public function latestDetail()
    {
        return $this->hasOne(EmicollectionDetail::class, 'emi_collection_id')->latestOfMany('id');
    }

    public function clientname()
    {
        return $this->belongsTo(Customer::class, 'client_id');
    }

    public function loanassign()
    {
        return $this->belongsTo(LoanAssign::class, 'loan_assign_id');
    }

    public function loan()
    {
        return $this->hasOneThrough(
            Loan::class,
            LoanAssign::class,
            'id',          // LoanAssign.id
            'id',          // Loan.id
            'loan_assign_id', // Emicollection.loan_assign_id
            'loan_type_id' // LoanAssign.loan_type_id
        );
    }
    public function branch()
    {
        return $this->hasOneThrough(
            Branch::class,
            LoanAssign::class,
            'id',          // LoanAssign.id
            'id',          // Branch.id
            'loan_assign_id', // Emicollection.loan_assign_id
            'branch_id' // LoanAssign.branch_id
        );
    }
    public function routename()
    {
        return $this->hasOneThrough(
            Route::class,
            LoanAssign::class,
            'id',          // LoanAssign.id
            'id',          // Route.id
            'loan_assign_id', // Emicollection.loan_assign_id
            'route_id' // LoanAssign.route_id
        );
    }   
}
