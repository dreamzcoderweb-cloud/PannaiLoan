<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class MobileEmployee extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'employees';
    
    protected $fillable = [
        'name',
        'phone',
        'password',
        'branch_id',
        'route_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }

    public function employeeIDproof_file()
    {
        return $this->hasMany(EmployeeIdProof::class, 'employee_id');
    }
}
