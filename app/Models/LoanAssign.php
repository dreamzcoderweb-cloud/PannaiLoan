<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterByEmployeeRoute;

class LoanAssign extends Model
{
    use HasFactory, FilterByEmployeeRoute;
    protected $fillable = [
        'loanAssign_id',
        'client_id',
        'client_type',
        'address',
        'phone',
        'city',
        'pincode',
        'loan_type_id',
        'document_type_id',
        'interest_id',
        'collection_type_id',
        'branch_id',
        'route_id',
        'loan_amount',
        'daily_duedays_id',
        'week_duedays_id',
        'weeklyemi_date',
        'monthlydue_date',
        'loanassign_date',
        'loan_tenure',
        'monthly_emi',
        'daily_emi',
        'weekly_emi',
        'total_distribution',
        'total_interest',
        'total_payableamt',
    ];

    protected $casts = [
        'document_type_id' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function int()
    {
        return $this->belongsTo(Interest::class, 'interest_id');
    }
    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_type_id');
    }

    public function branches()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function routes()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }
    public function client_name()
    {
        return $this->belongsTo(Customer::class, 'client_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'route_id', 'route_id');
    }


    public function documents()
    {
        return $this->belongsToMany(Document::class, 'id');
    }

    public function emiCollections()
    {
        return $this->hasMany(Emicollection::class, 'loan_assign_id');
    }
    //  Get latest EMI collection (optional helper)
    public function latestEmiCollection()
    {
        return $this->hasOne(Emicollection::class, 'loan_assign_id')->latestOfMany();
    }
}
