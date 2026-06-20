<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['group_name','total_loan_amount','total_remaining_amount'];

    public function loanAssigns()
    {
        return $this->belongsToMany(LoanAssign::class, 'group_loans', 'group_id', 'loanassign_id');
    }
}
