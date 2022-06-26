<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBkashBankRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bkash_bank_requests', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('payment_request_id')->unsigned();
            $table->foreign('payment_request_id')->references('id')->on('payment_requests');

            $table->integer('payment_option_rate_id')->unsigned();
            $table->foreign('payment_option_rate_id')->references('id')->on('payment_option_rates');

            $table->string('trxStatus')->nullable();
            $table->string('msisdn')->nullable();
            $table->string('amount')->nullable();
            $table->string('trxid')->nullable();
            $table->string('counter')->nullable();
            $table->string('reference')->nullable();
            $table->string('reversed')->nullable();
            $table->string('sender')->nullable();
            $table->string('service')->nullable();
            $table->string('currency')->nullable();
            $table->string('receiver')->nullable();
            $table->string('trxTimestamp')->nullable();

            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('payment_description')->nullable();
            $table->string('request_state')->nullable();
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
        Schema::dropIfExists('bkash_bank_requests');
    }
}
