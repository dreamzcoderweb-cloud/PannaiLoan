<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    protected $fillable = ['branch_name', 'city_name', 'area_name'];
    
    public function customers()
    {
        return $this->hasMany(Customer::class, 'branch_id');
    }
}
