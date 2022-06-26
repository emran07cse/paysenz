<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EblBankRequest extends Model
{
    private static $instance = null;

    public static function model() : EBLBankRequest{
        if(empty(EBLBankRequest::$instance)) EBLBankRequest::$instance = new EBLBankRequest();
        return EBLBankRequest::$instance;
    }

    protected $fillable = [
        'payment_request_id', 'request_state', 'payment_option_rate_id', 
        'order_id', 'session_id', 'successIndicator', 'gateway_response'
    ];

    /**
     * Get the  role of the User
     */
    public function paymentOptionRate(){
        return $this->belongsTo('App\PaymentOptionRate');
    }

    public function getAmount(){
        return (float) $this->amount;
    }

    public function getCurrency(){
        return $this->currency;
    }

    public function getCardType(){
        return $this->card_type;
    }

    public function getCardNo(){
        return $this->card_no;
    }

    public function getBankTransactionId(){
        $gatewayResponse = !empty($this->gateway_response) ? json_decode($this->gateway_response) : null;

        return isset($gatewayResponse->id) ? $gatewayResponse->id : null;
    }

    /**
     * @desc Returns summery of the Bank payment
     * @return array
     */
    public function paymentDetails(){
        $gatewayResponse = !empty($this->gateway_response) ? json_decode($this->gateway_response) : null;

        return array(
            'Transaction Type' => 'E-commerce',
            'Card Type' => $this->getCardType(),
            'Card Number' => $this->getCardNo(),
            'Currency' => $this->getCurrency(),
            'amount' => 'BDT ' . $this->getAmount(),
            'CardHolderName' => $this->card_holder_name,
            'MerchantTranID' => isset($gatewayResponse->merchant) ? $gatewayResponse->merchant : null,
            'IP Address' => $this->ip_address,
            'Payment Date' => $this->payment_time,
            'Payment Status' => isset($gatewayResponse->result) ? $gatewayResponse->result : null,
        );
    }
}
