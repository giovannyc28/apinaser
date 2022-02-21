<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_cards', function (Blueprint $table) {
            $table->id();
            $table->string('strAgreement');
            $table->string('strContactLastName');
            $table->string('strMobilePhone');
            $table->string('strType');
            $table->string('strName');
            $table->string('strNumber');
            $table->string('intExpMonth');
            $table->string('intExpYear');
            $table->string('strCCV');
            $table->string('strPaymentGateway');
            $table->string('strEncryptedToken');
            $table->string('strCreditCardStatus');
            $table->string('strFirstName');
            $table->string('strLastName');
            $table->string('strBillToName');
            $table->string('strStreet1');
            $table->string('strStreet2');
            $table->string('strCity');
            $table->string('strState');
            $table->string('strzip');
            $table->string('strCountry');
            $table->string('strPhoneNumber');
            $table->string('strEmailAddress');
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
        Schema::dropIfExists('credit_cards');
    }
}
