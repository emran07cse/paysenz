<?php

namespace App\Http\Controllers;

use App\AppClient;
use App\Store;
use Illuminate\Http\Request;
use App\PaymentRequest;
use Illuminate\Support\Facades\Validator;

class RequestController extends Controller
{

    //WILL BE POPULATED LATER
    protected $merchantRequest = null;

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'client_id'                     => 'required|Integer', // @TODO : check for valid client_id
            'order_id_of_merchant'          => 'required|String',
            'amount'                        => 'required|String',
            'currency_of_transaction'       => 'required|String',
            'buyer_name'                    => 'required|String',
            'buyer_email'                   => 'required|String',
            'buyer_address'                 => 'required|String',
            'buyer_contact_number'          => 'required|String',
            /*'ship_to'                       => 'required|String',
            'shipping_email'                => 'required|String',
            'shipping_address'              => 'required|String',
            'shipping_contact_number'       => 'required|String',*/
            'order_details'                 => 'required|String',
            'callback_success_url'          => 'required|String',
            'callback_fail_url'             => 'required|String',
            'callback_cancel_url'           => 'required|String',
            'expected_response_type'        => 'required|String',
        ]);
    }

    public function index(Request $request)
    {
        $inputs = $request->input();
        $validator = $this->validator($inputs);

        //input validation
        if($validator->fails()){
            return [
                'errorCode' => 'PSZ0001',
                'errorMessage' => makeInvalidParameterDetails($validator->errors())
            ];
        }

        //Currency Validation
        if(!in_array($inputs['currency_of_transaction'], config('app.currencies'))){
            return collect(["currency_of_transaction not recognized or supported"]);
        }

        //UNIQUE order_id_of_merchant check
        if(PaymentRequest::where('client_id', $inputs['client_id'])
                ->where('order_id_of_merchant', $inputs['order_id_of_merchant'])->count() > 0){
            return collect(["duplicate order_id_of_merchant identified"]);
        }

        // Convert Currency to BDT
        if(!empty($inputs['currency']) && ($inputs['currency'] != 'BDT') ){
        	$amountBDT = PaymentRequest::convertCurrency($inputs['currency'], PaymentRequest::CURRENCY_DEFAULT, $inputs['amount']);
        	$inputs['amount'] = $amountBDT > 0 ? $amountBDT : $inputs['amount'];
        }
        
        $paymentRequest = PaymentRequest::create($inputs);

        return [
            'request_id' => $paymentRequest->id,
            'expected_response' => route('process', [ 'txnId' => $paymentRequest->txnid ]),
            'error_code' => '00',
            'error_message' => ''
        ];
    }

    public function status(){
        if(request()->has('request_id')){
            $paymentRequest = PaymentRequest::find(request()->get('request_id'));

            if(!empty($paymentRequest)){
                return [
                    'payment_status' => $paymentRequest->status,
                    'payment_amount' => $paymentRequest->amount,
                    'error_code' => '00',
                    'error_message' => ''
                ];
            }
        }

        return [
            'payment_status' => "",
            'payment_amount' => "",
            'error_code' => 'PSZ2001',
            'error_message' => 'Invalid request_id'
        ];
    }
    
    public function verify(){
        $vefiried = false;
        $inputs = request()->input();
        $paymentRequest = PaymentRequest::where(['txnid' => $inputs['psz_txnid']])->first();

        if(!empty($paymentRequest)){
            if( ($paymentRequest->status == $inputs['payment_status']) && ($paymentRequest->amount == $inputs['amount']) ) {
                $vefiried = true;
            }
            return [
                'status' => $vefiried,
            ];
        }
        

        return [
            'status' => $vefiried,
            'error_code' => 'PSZ2002',
            'error_message' => 'Invalid payment request'
        ];
    }
    
    public function getClientInfo(Request $request){
        $inputs = request()->input();
        $client = AppClient::find($inputs['client_id']);
        
        return array(
            'id' => $client->id,
            'name' => $client->name,
            'email' => $client->user->email,
            'phone' => $client->user->phone
            );
    }
    

    /**
     * @param $from
     * @return int|string
     */
    private function convertionRatio($from){
        $amount = 1;
        $to = 'BDT';
        if($from == $to) return 1;
        $data = file_get_contents("https://www.google.com/finance/converter?a=$amount&from=$from&to=$to");
        preg_match("/<span class=bld>(.*)</span>/",$data, $converted);
        $converted = preg_replace("/[^0-9.]/", "", $converted[1]);
        return fn(round($converted, 3));
    }
}
