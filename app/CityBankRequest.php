<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CityBankRequest extends Model
{
    private static $instance = null;

    public static function model() : CityBankRequest{
        if(empty(CityBankRequest::$instance)) CityBankRequest::$instance = new CityBankRequest();
        return CityBankRequest::$instance;
    }

    protected $fillable = [
        'payment_request_id', 'request_state', 'payment_option_rate_id', 'OrderID', 'SessionID'
    ];

    /**
     * Get the  role of the User
     */
    public function paymentOptionRate(){
        return $this->belongsTo('App\PaymentOptionRate');
    }


    public function getAmount(){
        return (float) ($this->amount);
    }

    public function getCurrency(){
        return $this->Currency;
    }

    public function getCardType(){
        return $this->card_type;
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
            'CardHolderName' => $this->card_holder_name,
            'RRN' => isset($gatewayResponse->RRN) ? $gatewayResponse->RRN : null,
            'PAN' => isset($gatewayResponse->PAN) ? $gatewayResponse->PAN : null,
            'Approved Code' => isset($gatewayResponse->ApprovalCodeScr) ? $gatewayResponse->ApprovalCodeScr : null,
            'MerchantTranID' => isset($gatewayResponse->MerchantTranID) ? $gatewayResponse->MerchantTranID : null,
            'Description' => isset($gatewayResponse->ResponseDescription) ? $gatewayResponse->ResponseDescription : null,
            'IP Address' => $this->ip_address,
            'Payment Date' => $this->payment_time,
            'Payment Status' => isset($gatewayResponse->OrderStatus) ? $gatewayResponse->OrderStatus : null,
        );
    }
}
