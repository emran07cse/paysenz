<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentOptionRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_option_rates', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('oauth_clients');
            $table->integer('payment_option_id')->unsigned();
            $table->foreign('payment_option_id')->references('id')->on('payment_options');
            $table->decimal('paysenz_charge_percentage');
            $table->decimal('bank_charge_percentage')->nullable();
            $table->boolean('is_live')->nullable();
            $table->boolean('status')->nullable()->default(0);
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
        Schema::dropIfExists('payment_option_rates');
    }
}
