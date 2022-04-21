<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    use HasFactory;

    protected $fillable = [
        'strAgreement',
        'strSupervisor',
        'strSalesRep',
        'strCustomerCareRep',
        'strAHFirstName',
        'strAHLastName',
        'strAHBusinessPhone',
        'strAHMobilePhone',
        'strAHEmail',
        'strAHAddress1Street1',
        'strAHAddress1City',
        'strAHAddress1StateOrProvince',
        'strAHAddress1ZIPOrPostalCode',
        'strAHAddress1CountrOrRegion',
        'strECFirstName',
        'strECLastName',
        'strECBusinessPhone',
        'strECMobilePhone',
        'strECEmail',
        'strECAddress1Street1',
        'strECAddress1City',
        'strECAddress1StateOrProvince',
        'strECAddress1ZIPOrPostalCode',
        'strECAddress1CountrOrRegion',
        'strAgreementType',
        'strAgent',
        'strCompanySponsor',
        'dtDateofbirth',
        'strMaritalStatus',
        'strPlaceofbirth',
        'strCountryofResidence',
        'strPaymentTerms',
        'dtDateofpayment',
        'blnRecurringPayment',
        'strCompanyName',
        'strAgentcontactEmail',
        'strObservacion',
        'user_id'
    ];
}
