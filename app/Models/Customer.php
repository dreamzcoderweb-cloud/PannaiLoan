<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterByEmployeeRoute;

class Customer extends Model
{
    use HasFactory, FilterByEmployeeRoute;
    protected $hidden = [
        'password',
    ];  
    protected $fillable = [
        'name',
        'phone',
        'password',
        'branch_id',
        'route_id',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }
    public function loanAssign()
    {
        return $this->hasOne(LoanAssign::class, 'client_id', 'id');
    }
    
    public function customers()
    {
      return $this->hasMany(Customer::class, 'branch_id');
    }
}
