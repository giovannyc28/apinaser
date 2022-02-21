<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeneficiariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->string('strAgreement');
            $table->string('strRelationship');
            $table->string('strBeneficiaryFirstName');
            $table->string('strBeneficiaryMiddleName');
            $table->string('strBeneficiaryLastName');
            $table->string('strBeneficiaryBusinessPhone');
            $table->string('strBeneficiaryMobilePhone');
            $table->string('strBeneficiaryEmail');
            $table->string('strBeneficiaryAddress1Street1');
            $table->string('strBeneficiaryAddress1Street2');
            $table->string('strBeneficiaryAddress1City');
            $table->string('strBeneficiaryAddress1StateOrProvince');
            $table->string('strBeneficiaryAddress1ZIPOrPostalCode');
            $table->string('strBeneficiaryAddress1CountrOrRegion');
            $table->string('strLocation');
            $table->string('strCountryofResidence');
            $table->string('dtDateofbirth');
            $table->string('strCompanyName');
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
        Schema::dropIfExists('beneficiaries');
    }
}
