<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    use HasFactory;
    protected $fillable = [
        'strAgreement',
        'strRelationship',
        'strBeneficiaryFirstName',
        'strBeneficiaryLastName',
        'strBeneficiaryAddress1City',
        'strBeneficiaryAddress1StateOrProvince',
        'strBeneficiaryAddress1CountrOrRegion',
        'strCountryofResidence',
        'dtDateofbirth',
        'strCompanyName',
        'strEdad',
        'idAgreement'
    ];
}
