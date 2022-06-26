<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

use App\CityBankRequest;
use App\BkashBankRequest;
use App\DutchBanglaBankRequest;
use App\EblBankRequest;

class PaymentRequest extends Model
{
    private static $instance = null;

    // Status variables
    public const STATUS_SUCCESS = 'Successful';
    public const STATUS_FAILED = 'Failed';
    public const STATUS_INITIATED = 'Initiated';
    public const STATUS_ONHOLD = 'OnHold';
    public const STATUS_REFUND = 'Refund';

    // Bank shortcodes
    public const BANK_SHORTCODE_BKASH = 'bkash';
    public const BANK_SHORTCODE_CITYBANK = 'TCB';
    public const BANK_SHORTCODE_DBBL = 'DBBL';
    public const BANK_SHORTCODE_EBL = 'EBL';


    //
    const DIR_INVOICE_PDF = '/uploads/invoice/';
    
    // DEFAULT Currency
    const CURRENCY_DEFAULT = 'BDT';


    public static function model() : PaymentRequest{
        if(empty(PaymentRequest::$instance)) PaymentRequest::$instance = new PaymentRequest();
        return PaymentRequest::$instance;
    }

    /* 
    Payment status Lists: 
    1. Initiated: A new transaction has been intiated.
    2. Canceled_Reversal: A reversal has been canceled. For example, you won a dispute with the customer, and the funds for the transaction that was reversed have been returned to you.
    3. Completed: The payment has been completed, and the funds have been added successfully to your account balance.
    4. Denied/Fraud: The payment was denied. This happens only if the payment was previously pending because their is a possibility of fraud.
    5. Expired: This authorization has expired and cannot be captured.
    6. Failed: The payment has failed. This happens only if the payment was made from your customer's bank account.
    7. Pending: The payment is pending. See pending_reason for more information.
    8. Refunded: You refunded the payment.
    9. Reversed: A payment was reversed due to a chargeback or other type of reversal. The funds have been removed from your account balance and returned to the buyer. The reason for the reversal is specified in the ReasonCode element.
    10. Processed: A payment has been accepted.
    11. Voided: This authorization has been voided.
    */

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'order_id_of_merchant',
        'amount',
        'currency_of_transaction',
        'currency',
        'buyer_name',
        'buyer_email',
        'buyer_address',
        'buyer_address2',
        'buyer_city',
        'buyer_state',
        'buyer_zipcode',
        'buyer_country',
        'buyer_contact_number',
        'ship_to',
        'shipping_email',
        'shipping_address',
        'shipping_address2',
        'shipping_city',
        'shipping_state',
        'shipping_zipcode',
        'shipping_country',
        'shipping_contact_number',
        'order_details',
        'callback_success_url',
        'callback_fail_url',
        'callback_cancel_url',
        'callback_ipn_url',
        'custom_1',
        'custom_2',
        'custom_3',
        'custom_4',
        'expected_response_type',
    ];

    /**
     * Get the  role of the User
     */
    public function appClient(){
        return $this->belongsTo('App\AppClient', 'client_id');
    }

    /**
     * Get the Payment option rate
     */
    public function paymentOptionRate(){
        return $this->belongsTo('App\PaymentOptionRate', 'payment_option_rate_id');
    }

    /**
     * Get the Payment bKash
     */
    public function paymentBkash(){
        return $this->hasOne('App\BkashBankRequest', 'payment_request_id');
    }
    /**
     * Get the Payment CityBank
     */
    public function paymentCityBank(){
        return $this->hasOne('App\CityBankRequest', 'payment_request_id');
    }
    /**
     * Get the Payment DBBLBank
     */
    public function paymentDbblBank(){
        return $this->hasOne('App\DutchBanglaBankRequest', 'payment_request_id');
    }
    /**
     * Get the Payment EBLBank
     */
    public function paymentEblBank(){
        return $this->hasOne('App\EblBankRequest', 'payment_request_id');
    }

    /**
     * @desc Check if the Request is already processed previously.
     * @return bool
     */
    public function isReadyForPayment() {
        return $this->status != 'Successful' ? true : false;
    }

    /**
     * @desc Get amount
     * @return mixed
     */
    public function getAmount() {
        return $this->amount;
    }

    /**
     * @desc Get amount
     * @return mixed
     */
    public function getStoreAmount() {
        return (float) ($this->amount - $this->store_service_charge);
    }

    public function getGatewayBankName(){
        if(!empty($this->paymentOptionRate)
            && !empty($this->paymentOptionRate->paymentOption)
            && !empty($this->paymentOptionRate->paymentOption->bank)) {
            return $this->paymentOptionRate->paymentOption->bank->name;
        }

        return '';
    }

    /**
     *  @desc Get full Payment Gateway name.
     */
    public function getPaymentTypeName($fullname = false){
        $payment_type = '';
        if(!empty($this->paymentOptionRate)
            && !empty($this->paymentOptionRate->paymentOption)
            && !empty($this->paymentOptionRate->paymentOption->bank)) {
            $payment_type = $this->paymentOptionRate->paymentOption->name . ' ('.$this->paymentOptionRate->paymentOption->bank->short_code.') ';
        }

        return $payment_type;
    }

    public function getPaymentBank(){
        if(!empty($this->paymentOptionRate)
            && !empty($this->paymentOptionRate->paymentOption)
            && !empty($this->paymentOptionRate->paymentOption->bank)) {
            $bankShortCode = $this->paymentOptionRate->paymentOption->bank->short_code;
            if(strtolower($bankShortCode) == strtolower(self::BANK_SHORTCODE_BKASH)){
                return BkashBankRequest::where(['payment_request_id' => $this->id])->first();
            } else if(strtolower($bankShortCode) == strtolower(self::BANK_SHORTCODE_DBBL)){
                return DutchBanglaBankRequest::where(['payment_request_id' => $this->id])->first();
            } else if(strtolower($bankShortCode) == strtolower(self::BANK_SHORTCODE_CITYBANK)){
                return CityBankRequest::where(['payment_request_id' => $this->id])->first();
            } else if(strtolower($bankShortCode) == strtolower(self::BANK_SHORTCODE_EBL)){
                return EblBankRequest::where(['payment_request_id' => $this->id])->first();
            }
        }

        return null;
    }

    public function getPaymentDetails(){
        $bankRequest = $this->getPaymentBank();
        if($bankRequest){
            return $bankRequest->paymentDetails();
        }

        return array();
    }

    public function getPaymentEmailDetails(){
        $bankRequest = $this->getPaymentBank();
        $data = array(
            'pay_status' => $this->status,
            'pay_amount' => number_format((float)$this->getAmount(), 2),
            'store_amount' => number_format($this->getStoreAmount(), 2),
            'service_charge' => number_format($this->store_service_charge, 2),
            'paysenz_transactionid' => $this->txnid,
            'merchant_order_id'=> $this->order_id_of_merchant,
            'currency' => !empty($bankRequest) ? $bankRequest->getCurrency() : '',
            'convertion_rate' => '0.00',
            'store_id' => $this->appClient->name,
            'card_type' => !empty($bankRequest) ? $bankRequest->getCardType() : '',
            'card_number' => !empty($bankRequest) ? $bankRequest->getCardNo() : '',
            'card_holder_name' => !empty($bankRequest) ? $bankRequest->card_holder_name : '',
            'gateway_bank' => $this->getGatewayBankName(),
            'bank_transaction_id' => !empty($bankRequest) ? $bankRequest->getBankTransactionId() : '',
            'payment_datetime' => !empty($bankRequest) ? date('Y:m:d H:i:s', strtotime($bankRequest->payment_time)) : '',
            'ip_address' => !empty($bankRequest) ? $bankRequest->ip_address : '',
            'customer_name' => $this->buyer_name,
            'customer_email' => $this->buyer_email,
            'customer_address' => $this->buyer_address,
            'customer_contact_number' => $this->buyer_contact_number,
            'order_description' => $this->order_details,
        );

        return $data;
    }

    public function getBillingName(){
        return !empty($this->ship_to) ? $this->ship_to : $this->buyer_name;
    }

    public function getBillingEmail(){
        return !empty($this->shipping_email) ? $this->shipping_email : $this->buyer_email;
    }

    public function getBillingAddress(){
        return !empty($this->shipping_address) ? $this->shipping_address : $this->buyer_address;
    }

    public function getBillingPhone(){
        return !empty($this->shipping_contact_number) ? $this->shipping_contact_number : $this->buyer_contact_number;
    }

    public function getStatusTableColor(){
        $tableClass = 'table-light';
        if($this->status == self::STATUS_SUCCESS){
            $tableClass = 'table-success';
        } else if($this->status == self::STATUS_FAILED){
            $tableClass = 'table-danger';
        } else if($this->status == self::STATUS_REFUND){
            $tableClass = 'table-secondary';
        } else if($this->status == self::STATUS_ONHOLD){
            $tableClass = 'table-warning';
        }

        return $tableClass;
    }

    public static function boot(){
        parent::boot();

        self::creating(function($model){
            // Generate unique Txnid ID
            $model->txnid = $model->client_id. date('ymdhis') . rand(1, 100);
        });
    }

    /**
     * @desc Get all the Payment staus dropdown options list
     */
    public static function getStatusOptions(){
        return array(
            self::STATUS_INITIATED => self::STATUS_INITIATED,
            self::STATUS_SUCCESS => self::STATUS_SUCCESS,
            self::STATUS_FAILED => self::STATUS_FAILED,
            self::STATUS_ONHOLD => self::STATUS_ONHOLD,
            self::STATUS_REFUND => self::STATUS_REFUND,
        );
    }

    public static function getWithdrawReport($client_id = null) {
        $whereCondition = '';
        if((int) $client_id > 0){
            $whereCondition .= " AND pr.`client_id` = {$client_id} ";
        }
        $sql = "SELECT
                COUNT(pr.`txnid`) AS total_txn,    
                oc.`name` AS client_name,
                SUM(pr.`amount`) AS total_amount,
                SUM(
                    pr.`amount` - pr.`store_service_charge`
                ) AS total_merchant_amount,
                SUM(pr.`store_service_charge`) AS total_paysenz_amount,
                SUM(pr.`bank_service_charge`) AS total_bank_amount,    
                SUM(
                   pr.`store_service_charge` - pr.`bank_service_charge`
                ) AS total_paysenz_profit_amount,
                CONCAT(b.name, ': ', po.name) AS payment_gateway
            FROM
                `payment_requests` pr
            JOIN `oauth_clients` oc ON
                oc.id = pr.`client_id`    
            JOIN `payment_option_rates` por ON
                por.id = pr.`payment_option_rate_id`
            JOIN `payment_options` po ON
                po.id = por.`payment_option_id`
            JOIN `banks` b ON
                b.id = po.`bank_id`    
            WHERE
                pr.status = 'Successful'
            {$whereCondition}
            GROUP BY
                oc.`name`,
                payment_gateway
            ORDER BY
                pr.ID
            DESC";

        $data = DB::select( DB::raw($sql) );
        return $data;
    }
    
    /**
     * @desc Convert currency function
     * @param $from
     * @param $to
     * @param $amount
     * @return float|int
     */
    public static function convertCurrency($from, $to, $amount){
        $url = 'https://free.currencyconverterapi.com/api/v5/convert?q=' . $from . '_' . $to . '&compact=ultra';
        $cSession = curl_init();

        curl_setopt($cSession, CURLOPT_URL, $url);
        curl_setopt($cSession, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cSession, CURLOPT_SSL_VERIFYPEER, false);

        $buffer = curl_exec($cSession);
        curl_close($cSession);

        $json = json_decode($buffer, true);
        $rate = implode(" ",$json);
        if($amount != null)
        {
            $total = $rate * $amount;
            $rounded = round($total); //optional, rounds to a whole number
            return $rounded; //or return $rounded if you kept the rounding bit from above
        }else{
            return $rate;
        }

    }
}
