<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTarifasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tarifas', function (Blueprint $table) {
            $table->id();
            $table->string('plan_type')->unique();
            $table->decimal('subscription_fee', $precision = 8, $scale = 2);
            $table->decimal('yearly_fee', $precision = 8, $scale = 2);
            $table->decimal('halfyear_fee', $precision = 8, $scale = 2);
            $table->decimal('quarterly_fee', $precision = 8, $scale = 2);
            $table->decimal('monthly_fee', $precision = 8, $scale = 2);
            $table->integer('anualmultiple_pays')->nullable();
            $table->integer('cant_person')->nullable();
            $table->decimal('value_kid' , $precision = 8, $scale = 2)->nullable();
            $table->decimal('value_adult', $precision = 8, $scale = 2)->nullable();
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
        Schema::dropIfExists('tarifas');
    }
}
