<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupLoan extends Model
{
    use HasFactory;

    protected $fillable = ['group_id','loanassign_id', 'client_id','loan_amount','remaining_amount'];
}
