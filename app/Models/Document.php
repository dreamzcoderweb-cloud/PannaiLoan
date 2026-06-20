<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;
    protected $fillable = [
        'document_name',
    ];
    public function document_name()
    {
        return $this->belongsToMany(LoanAssign::class, 'document_type_id');
    }
}
