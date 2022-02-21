<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preexistance extends Model
{
    use HasFactory;

    protected $fillable = [
        'strAgreement',
        'idQuestion',
        'idBeneficiary'
    ];
}
