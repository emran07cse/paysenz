<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Banks\DutchBanglaBank;

class DutchBanglaBankRequest extends Model
{
    private static $instance = null;

    public static function model() : DutchBanglaBankRequest{
        if(empty(DutchBanglaBankRequest::$instance)) DutchBanglaBankRequest::$instance = new DutchBanglaBankRequest();
        return DutchBanglaBankRequest::$instance;
    }

    protected $fillable = [
        'payment_request_id', 'trans_id', 'card_type', 'request_state', 'payment_option_rate_id'
    ];

    /**
     * Get the  role of the User
     */
    public function paymentOptionRate(){
        return $this->belongsTo('App\PaymentOptionRate');
    }

    public function getAmount(){
        return (float) ($this->AMOUNT / 100);
    }

    public function getCurrency(){
        return 'BDT';
    }

    public function getCardType(){
        return DutchBanglaBank\Bank::getCardBrandName($this->card_type);
    }

    public function getCardNo(){
        return $this->card_no;
    }

    public function getBankTransactionId(){
        return $this->RRN;
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
            'RRN' => isset($gatewayResponse->RRN) ? $gatewayResponse->RRN : null,
            'Approved Code' => isset($gatewayResponse->APPROVAL_CODE) ? $gatewayResponse->APPROVAL_CODE : null,
            'Description' => isset($gatewayResponse->DESCRIPTION) ? $gatewayResponse->DESCRIPTION : null,
            'IP Address' => $this->ip_address,
            'Payment Date' => $this->payment_time,
            'Payment Status' => isset($gatewayResponse->RESULT) ? $gatewayResponse->RESULT : null,
        );
    }
}
