<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCityBankRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_bank_requests', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('payment_request_id')->unsigned();
            $table->foreign('payment_request_id')->references('id')->on('payment_requests');

            $table->integer('payment_option_rate_id')->unsigned();
            $table->foreign('payment_option_rate_id')->references('id')->on('payment_option_rates');

            $table->string('OrderID')->nullable();
            $table->string('SessionID')->nullable();

            $table->string('OrderStatusScr')->nullable();
            $table->string('ResponseCode')->nullable();
            $table->string('PAN')->nullable();
            $table->string('PurchaseAmountScr')->nullable();
            $table->string('OrderDescription')->nullable();
            $table->string('Name')->nullable();
            $table->string('request_state');
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
        Schema::dropIfExists('city_bank_requests');
    }
}
