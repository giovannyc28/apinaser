<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    use HasFactory;
    protected $fillable = [
        'strAgreement',
        'strMobilePhone',
        'strType',
        'strName',
        'strNumber',
        'intExpMonth',
        'intExpYear',
        'strCCV',
        'strFirstName',
        'strLastName',
        'strStreet1',
        'strCity',
        'strState',
        'strzip',
        'strCountry',
        'strPhoneNumber',
        'strEmailAddress'
    ];
}
