<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->increments('id');

            $table->string('txnid')->unique();
            $table->integer('client_id');
            $table->string('order_id_of_merchant');
            $table->decimal('amount');
            $table->decimal('store_service_charge')->nullable()->default(0);
            $table->decimal('bank_service_charge')->nullable()->default(0);
            $table->string('currency_of_transaction');
            $table->string('buyer_name');
            $table->string('buyer_email');
            $table->string('buyer_address');
            $table->string('buyer_contact_number');
            $table->string('ship_to');
            $table->string('shipping_email');
            $table->string('shipping_address');
            $table->string('shipping_contact_number');
            $table->string('order_details');
            $table->string('callback_url')->nullable();

            $table->string('callback_success_url');
            $table->string('callback_fail_url')->nullable();
            $table->string('callback_cancel_url')->nullable();
            $table->string('callback_ipn_url')->nullable();
            $table->string('opt_a')->nullable();
            $table->string('opt_b')->nullable();
            $table->string('opt_c')->nullable();
            $table->string('opt_d')->nullable();

            $table->text('description')->nullable();
            $table->string('expected_response_type');
            
            $table->integer('payment_option_rate_id')->unsigned()->nullable();
            $table->foreign('payment_option_rate_id')->references('id')->on('payment_option_rates');

            $table->string('status')->default('Initiated');

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
        Schema::dropIfExists('payment_requests');
    }
}
