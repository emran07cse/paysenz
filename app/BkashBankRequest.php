<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BkashBankRequest extends Model
{
    private static $instance = null;

    public static function model() : BkashBankRequest{
        if(empty(BkashBankRequest::$instance)) BkashBankRequest::$instance = new BkashBankRequest();
        return BkashBankRequest::$instance;
    }

    protected $fillable = [
        'payment_request_id', 'request_state', 'payment_option_rate_id'
    ];

    /**
     * Get the  role of the User
     */
    public function paymentOptionRate(){
        return $this->belongsTo('App\PaymentOptionRate');
    }

    public function getAmount(){
        return $this->amount;
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
        return $this->card_no;
    }

    /**
     * @desc Returns summery of the Bank payment
     * @return array
     */
    public function paymentDetails(){
        return array(
            'Transaction Type' => 'E-commerce',
            'Card Type' => $this->getCardType(),
            'TrxId' => $this->getCardNo(),
            'Sender' => $this->sender,
            'Reference' => $this->reference,
            'Counter' => $this->counter,
            'Receiver' => $this->receiver,
            'Currency' => $this->getCurrency(),
            'amount' => $this->getAmount(),
            'IP Address' => $this->ip_address,
            'Payment Date' => $this->payment_time,
            'Payment Status' => $this->status == TRUE ? 'Successful' : 'Failed',
        );
    }
}
