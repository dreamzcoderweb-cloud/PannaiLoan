<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterByEmployeeRoute;


class Route extends Model
{
    use HasFactory;
    use FilterByEmployeeRoute;
    protected $fillable = ['route_name', 'branch_id'];
    protected $table = 'routes';
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    
    public function customers()
    {
     return $this->hasMany(Customer::class, 'route_id');
    }
}
