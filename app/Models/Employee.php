<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'password',
        'branch_id',
        'route_id',
    ];

    public function idProofs()
    {
        return $this->hasMany(EmployeeIdProof::class, 'employee_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }
}
