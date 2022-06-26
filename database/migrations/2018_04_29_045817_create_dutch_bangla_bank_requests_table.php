<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDutchBanglaBankRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dutch_bangla_bank_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payment_request_id')->unsigned();
            $table->foreign('payment_request_id')->references('id')->on('payment_requests');
            $table->integer('payment_option_rate_id')->unsigned();
            $table->foreign('payment_option_rate_id')->references('id')->on('payment_option_rates');
            $table->string('trans_id')->nullable();
            $table->integer('Ucaf_Cardholder_Confirm')->nullable();
            $table->integer('card_type')->nullable();
            $table->string('RESULT')->nullable();
            $table->string('RESULT_PS')->nullable();
            $table->string('RESULT_CODE')->nullable();
            $table->string('RRN')->nullable();
            $table->string('APPROVAL_CODE')->nullable();
            $table->string('CARD_NUMBER')->nullable();
            $table->string('AMOUNT')->nullable();
            $table->string('TRANS_DATE')->nullable();
            $table->string('CARDNAME')->nullable();
            $table->string('DESCRIPTION')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('payment_description')->nullable();
            $table->string('request_state');
            $table->boolean('status');
            $table->dateTime('payment_time')->nullable();
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
        Schema::dropIfExists('dutch_bangla_bank_requests');
    }
}
