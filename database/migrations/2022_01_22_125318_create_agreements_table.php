<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agreements', function (Blueprint $table) {
            $table->id();
            $table->string('strAgreement');
            $table->string('strSupervisor');
            $table->string('strSalesRep');
            $table->string('strCustomerCareRep');
            $table->string('strAHFirstName');
            $table->string('strAHMiddleName');
            $table->string('strAHLastName');
            $table->string('strAHBusinessPhone');
            $table->string('strAHMobilePhone');
            $table->string('strAHEmail');
            $table->string('strAHAddress1Street1');
            $table->string('strAHAddress1Street2');
            $table->string('strAHAddress1City');
            $table->string('strAHAddress1StateOrProvince');
            $table->string('strAHAddress1ZIPOrPostalCode');
            $table->string('strAHAddress1CountrOrRegion');
            $table->string('strECFirstName');
            $table->string('strECMiddleName');
            $table->string('strECLastName');
            $table->string('strECBusinessPhone');
            $table->string('strECMobilePhone');
            $table->string('strECEmail');
            $table->string('strECAddress1Street1');
            $table->string('strECAddress1Street2');
            $table->string('strECAddress1City');
            $table->string('strECAddress1StateOrProvince');
            $table->string('strECAddress1ZIPOrPostalCode');
            $table->string('strECAddress1CountrOrRegion');
            $table->string('strAgreementType');
            $table->string('strAgreementStatus');
            $table->string('dtEffectiveDate');
            $table->string('dtExpirationDate');
            $table->string('strAgent');
            $table->string('strCompanySponsor');
            $table->string('strLanguage');
            $table->string('dtDateofbirth');
            $table->string('strGender');
            $table->string('strMaritalStatus');
            $table->string('strPlaceofbirth');
            $table->string('strCountryofResidence');
            $table->string('strPaymentTerms');
            $table->string('dtDateofpayment');
            $table->string('blnRecurringPayment');
            $table->string('strCompanyName');
            $table->string('strAgentcontactEmail');
            $table->string('strUser');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agreements');
    }
}
