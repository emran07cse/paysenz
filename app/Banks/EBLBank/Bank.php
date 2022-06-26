<?php
/**
 * Created by PhpStorm.
 * User: Shaiful Islam
 * Time: 8:27 PM
 */

namespace App\Banks\EBLBank;

use GuzzleHttp;
use Mockery\Exception;
use DateTime;
use Illuminate\Http\Request;
use App\Banks\EBLBank\Skypay;
use App\EblBankRequest;
use App\PaymentRequest;
use App\PaymentOptionRate;

class Bank
{
    protected $mode = '0'; // 0 - Sandbox/Test, 1 - Live

    public function __construct($mode = 0)
    {
        $this->mode = $mode;
    }
    
    /**
     * @desc Generate Bank request
     *
     * @param $paymentRequest
     * @param $paymentOptionRate
     * @return mixed
     */
    public function createBankRequest($paymentRequest, $paymentOptionRate, $response){
        // Check for existing bank record, if not found create record
        $bankRequest = EblBankRequest::where(['payment_request_id' => $paymentRequest->id])->first();
        if($bankRequest === null) {
            $bankRequest = EblBankRequest::create([
                'payment_request_id' => $paymentRequest->id,
                'payment_option_rate_id' => $paymentOptionRate->id,
                'order_id' => $response['data'] ? $response['data']['order']['id'] : null,
                'session_id' => $response['eblData']? $response['eblData']['session.id'] : null,
                'successIndicator' => $response['eblData']? $response['eblData']['successIndicator'] : null,
                'request_state' => 'Redirected to Bank',
                'ip_address' => request()->getClientIp() ?? request_ip_address(),
                'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null
            ]);
        } else {
            $bankRequest->order_id = $response['data'] ? $response['data']['order']['id'] : null;
            $bankRequest->session_id = $response['eblData']? $response['eblData']['session.id'] : null;
            $bankRequest->successIndicator = $response['eblData']? $response['eblData']['successIndicator'] : null;
            $bankRequest->payment_option_rate_id = $paymentOptionRate->id;
            $bankRequest->request_state = 'Redirected to Bank';
            $bankRequest->ip_address = request()->getClientIp() ?? request_ip_address();
            $bankRequest->user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
            $bankRequest->save();
        }

        return $bankRequest;
    }

    
    public function callAndGetGatewayUrl($data, $subMerchantInfo = array()) : array {
        if(!empty($data['order']['id']) && !empty($data['order']['amount'])) {
            $configArray = $this->getConfiguration($subMerchantInfo);
            $skypay = new Skypay($configArray);
            $eblData = $skypay->Checkout($data);
        }
        
        if(empty($eblData) || count($eblData) == 0) return null;
        else{
            return [
                'url' => $eblData['redirect_url'],
                'data' => $data,
                'eblData' => $eblData,
            ];
        }
    }
    
    

    /**
     * Used for confirmation if callback is done from TCB end
     * @param $subMerchantId
     * @param $orderID
     * @param $sessionID
     * @return array
     */
    public function getTransactionConfirmationData($orderID, $sessionID, $subMerchantInfo) : array {
        if(empty($orderID) || empty($sessionID)){
            die('EBL Verify: Invalid Order information');
        }
        $configArray = $this->getConfiguration($subMerchantInfo);
        $skypay = new Skypay($configArray);
        $responseArray = $skypay->RetrieveOrder($orderID);
        
        return [
            'responseData' => $responseArray,
            'OrderStatus'=> $responseArray["result"],
            'paymentStatus'=> ($responseArray["amount"] == $responseArray["totalAuthorizedAmount"]) && ($responseArray["amount"] == $responseArray["totalCapturedAmount"]) ? TRUE : FALSE,
            'amount' => $responseArray["amount"],
            'createDate' => $responseArray["creationTime"]
        ];
    }
    
    public function processTransactionConfirmationData($confirmationData, $bankRequest){
        $paymentVerification = false;
                
        if(is_array($confirmationData)) {
            
            // Save EBLbank response data to table
            $bankRequest->gateway_response = json_encode($confirmationData);
            $bankRequest->card_no = isset($confirmationData['sourceOfFunds.provided.card.number']) && !is_array($confirmationData['sourceOfFunds.provided.card.number']) ? $confirmationData['sourceOfFunds.provided.card.number'] : null;
            $bankRequest->card_holder_name = isset($confirmationData['sourceOfFunds.provided.card.nameOnCard']) && !is_array($confirmationData['sourceOfFunds.provided.card.nameOnCard']) ? $confirmationData['sourceOfFunds.provided.card.nameOnCard'] : null;
            $bankRequest->card_type = isset($confirmationData['sourceOfFunds.provided.card.brand']) && !is_array($confirmationData['sourceOfFunds.provided.card.brand']) ? $confirmationData['sourceOfFunds.provided.card.brand'] : null;
            $bankRequest->amount = $confirmationData['amount'];
            $bankRequest->currency = $confirmationData['currency'];
            
            // Create MySQL forcard_nomat payment_time from `TranDateTime` data
            if(isset($confirmationData['creationTime']) && !empty($confirmationData['creationTime'])){
                $datetime = new DateTime($confirmationData['creationTime']);
                $bankRequest->payment_time = $datetime->format('Y-m-d H:i:s');
            }
            
            
            $paymentRequest = PaymentRequest::where(['id' => $bankRequest->payment_request_id])->first();
            $bankAmount = (float) ($bankRequest->amount);
            $requestAmount = (float) $paymentRequest->amount;
            $amountDiff = ($requestAmount - $bankAmount);
            if($confirmationData['result'] == 'SUCCESS') {
                // Check if the Order amount and bank amount is same.
                if($amountDiff == 0){
                    echo "EBLBank payment verified successfully!!";
                    $bankRequest->payment_description = "EBLBank  payment verified successfully!!";
                    $bankRequest->request_state = 'Finished and Confirmed';
                    $bankRequest->status = 1;
                    $paymentVerification = true;
                    
                    // Update bank and store charge
                    $paymentOptionRate = PaymentOptionRate::find($bankRequest->payment_option_rate_id);
                    if($paymentOptionRate){
                        $store_charge_amount = ($paymentOptionRate->paysenz_charge_percentage > 0) ? ($bankAmount * ($paymentOptionRate->paysenz_charge_percentage / 100)) : 0;
                        $bank_charge_amount = ($paymentOptionRate->bank_charge_percentage > 0) ? ($bankAmount * ($paymentOptionRate->bank_charge_percentage / 100)) : 0; 
                        $paymentRequest->store_service_charge = $store_charge_amount;
                        $paymentRequest->bank_service_charge = $bank_charge_amount;
                    }
                } else {
                    $bankRequest->payment_description = "Error: EBLBank payment verified but Payment amount mismatch. Order amount: {$requestAmount}, Bank amount: {$bankAmount}";
                    $bankRequest->request_state = 'Verification failed';
                }
            } else {
                $bankRequest->payment_description = "EBLBank Payment failed";
                $bankRequest->request_state = 'Verification failed';
            }
            
            // Update the bank request
            $bankRequest->save();
            
             // Update the Payment Request
            if( $paymentVerification == true ) {
                $paymentRequest->status = PaymentRequest::STATUS_SUCCESS;
            } else {
                $paymentRequest->status = PaymentRequest::STATUS_FAILED;
            }

            $paymentRequest->payment_option_rate_id = $bankRequest->payment_option_rate_id;
            $paymentDescription = array(
                'payment_status' => $paymentRequest->status,
                'card_no' => $bankRequest->card_no,
                'card_holder_name' => $bankRequest->card_holder_name,
                'card_type' => $bankRequest->card_type,
                'payment_type' => $paymentRequest->getPaymentTypeName()
            );

            $paymentRequest->description = json_encode($paymentDescription);
            $paymentRequest->save();
            
            $bankData = array(
                'amount' => $requestAmount,
                'pay_time' => $bankRequest->payment_time,
                'remarks' => json_encode($paymentDescription)
                );
                
            return array('paymentRequest' => $paymentRequest, 'bankData' => $bankData);
        }
    }
    
    protected function getConfiguration($subMerchantInfo){
        $configArray = array();
        if($this->mode === 1) {
            // Live mode
            $configArray = config('Banks.EBLBank.Bank.live') ;  
        }  else {
            // Sandbox mode, Test merchant credential from config will be used.
            $configArray = config('Banks.EBLBank.Bank.sandbox') ;  
        } 
        
        return $configArray;
    }
    
}
