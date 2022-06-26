<?php
/**
 * Created by PhpStorm.
 * User: Md. Shaiful Islam
 */

namespace App\Banks\BkashBank;

use GuzzleHttp;
use Mockery\Exception;
use App\PaymentRequest;
use App\PaymentOptionRate;

class Bank
{
    protected $mode = '0'; // 0 - Sandbox/Test, 1 - Live
    
    public function __construct($mode = 0)
    {
        $this->mode = $mode;
    }
    
    public function validateByTraxId($trxid, $amount){
        try{
            $data = [
                'user' => config('Banks.BkashBank.Bank.user'),
                'pass' => config('Banks.BkashBank.Bank.pass'),
                'msisdn' => config('Banks.BkashBank.Bank.msisdn'),
                'trxid' => $trxid
            ];
            return $this->callApi(config('Banks.BkashBank.Bank.api_url'), $data);

        }catch (Exception $e){
            dd($e->getMessage());
            return false;
        }

    }

    /**
     * @param array $data
     */
    protected function callApi($url, $data){
        $client = new GuzzleHttp\Client(['headers' => [ 'Content-Type' => 'application/json' ]]);
        $response = $client->post($url,
            [
                'body' => json_encode($data)
            ]);

        return json_decode((string) $response->getBody()->getContents(), true);
    }
    
    public function processTransactionConfirmationData($bkashResponse, $bankRequest){
        $bkashPaymentVerification = false;
        
        // Update the bKash Bank Request row with the response
        $bankRequest->trxStatus = isset($bkashResponse['transaction']['trxStatus']) ? $bkashResponse['transaction']['trxStatus'] : null;
        $bankRequest->counter = isset($bkashResponse['transaction']['counter']) ? $bkashResponse['transaction']['counter'] : null;
        $bankRequest->reference = isset($bkashResponse['transaction']['reference']) ? $bkashResponse['transaction']['reference'] : null;
        $bankRequest->reversed = isset($bkashResponse['transaction']['reversed']) ? $bkashResponse['transaction']['reversed'] : null;
        $bankRequest->sender = isset($bkashResponse['transaction']['sender']) ? $bkashResponse['transaction']['sender'] : null;
        $bankRequest->service = isset($bkashResponse['transaction']['service']) ? $bkashResponse['transaction']['service'] : null;
        $bankRequest->currency = isset($bkashResponse['transaction']['currency']) ? $bkashResponse['transaction']['currency'] : null;
        $bankRequest->receiver = isset($bkashResponse['transaction']['receiver']) ? $bkashResponse['transaction']['receiver'] : null;
        $bankRequest->trxTimestamp = isset($bkashResponse['transaction']['trxTimestamp']) ? $bkashResponse['transaction']['trxTimestamp'] : null;
        $bankRequest->ip_address = request()->getClientIp() ?? request_ip_address();
        $bankRequest->user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        // Fillup Common Bank fields
        $bankRequest->gateway_response = json_encode($bkashResponse);
        $bankRequest->amount = isset($bkashResponse['transaction']['amount']) ? (float) $bkashResponse['transaction']['amount'] : null;
        $bankRequest->payment_time = isset($bkashResponse['transaction']['trxTimestamp']) ? date('Y-m-d H:i:s', strtotime($bkashResponse['transaction']['trxTimestamp'])) : null;
        
        $paymentRequest = PaymentRequest::where(['id' => $bankRequest->payment_request_id])->first();

        // check if the `trxid` is valid
        $transactionResponse = $this->validateResponse($bkashResponse['transaction']['trxStatus']);
        if($transactionResponse->status == true){
            // Store the user provided TrxId for after validation
            $bankRequest->trxId = isset($bkashResponse['transaction']['trxId']) ? $bkashResponse['transaction']['trxId'] : null;
            $bankRequest->card_no = $bankRequest->trxId;
            $bankRequest->card_holder_name = isset($bkashResponse['transaction']['sender']) ? $bkashResponse['transaction']['sender'] : null;
            $bankRequest->card_type = 'BKASH';

            // Check if the Order amount and bKash amount is same.
            $amount = $paymentRequest->amount;
            if($bkashResponse['transaction']['amount'] >= $amount){
                //echo "Bkash payment verified successfully!!";
                $bankRequest->payment_description = "Bkash payment verified successfully!!";
                $bankRequest->request_state = 'Finished and Confirmed';
                $bankRequest->status = 1;
                $bkashPaymentVerification = true;
                
                // Update bank and store charge
                $paymentOptionRate = PaymentOptionRate::find($bankRequest->payment_option_rate_id);
                if($paymentOptionRate){
                    $store_charge_amount = ($paymentOptionRate->paysenz_charge_percentage > 0) ? ($bkashResponse['transaction']['amount'] * ($paymentOptionRate->paysenz_charge_percentage / 100)) : 0;
                    $bank_charge_amount = ($paymentOptionRate->bank_charge_percentage > 0) ? ($bkashResponse['transaction']['amount'] * ($paymentOptionRate->bank_charge_percentage / 100)) : 0; 
                    $paymentRequest->store_service_charge = $store_charge_amount;
                    $paymentRequest->bank_service_charge = $bank_charge_amount;
                }
                
            } else {
                $bankRequest->status = 0;
                $bankRequest->payment_description = "Error: Bkash payment verified but Payment amount mismatch. Order amount: {$amount}, Bkash amount: {$bkashResponse['transaction']['amount']}";
                $bankRequest->request_state = 'Verification failed';
            }
        } else {
            //echo "Bkash Error code {$transactionResponse->trxStatus}, {$transactionResponse->message}";
			//$bankRequest->payment_description = "Bkash Error code {$transactionResponse->trxStatus}, {$transactionResponse->message}";
            $bankRequest->payment_description = "{$transactionResponse->message} {$transactionResponse->interpretation}";            
            $bankRequest->request_state = 'Verification failed';
        }

        $bankRequest->save();

        // Update the Payment Request
        if( $bkashPaymentVerification == true ) {
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
            'amount' => $paymentRequest->amount,
            'pay_time' => $bankRequest->payment_time,
            'card_no' => $bankRequest->sender,
            'remarks' => json_encode($paymentDescription)
            );
        return array('paymentRequest' => $paymentRequest, 'bankData' => $bankData);
    }

    /**
     * @param array $response
     * @throws Exception
     */
    public function validateResponse($trxStatus)
    {
        $response = new \stdClass();
        $response->status = false;
        $response->message = '';
        $response->trxStatus = $trxStatus;

        switch ($trxStatus) {
            case '0010':
            case '0011':
                $response->message = 'TrxID is valid but transaction is in pending state.';
                $response->interpretation = 'Transaction Pending';
                break;
            case '0100':
                $response->message = 'TrxID is valid but transaction has been reversed.';
                $response->interpretation = 'Transaction Reversed';
                break;
            case '0111':
                $response->message = 'TrxID is valid but transaction has failed.';
                $response->interpretation = 'Transaction Failure';
                break;
            case '1001':
                $response->message = 'Invalid MSISDN input. Try with correct mobile no.';
                $response->interpretation = 'Format Error';
                break;
            case '1002':
                $response->message = 'Invalid trxID, it does not exist.';
                $response->interpretation = 'Invalid Reference';
                break;
            case '1003':
                $response->message = 'Access denied. Username or Password is incorrect.';
                $response->interpretation = 'Authorization Error';
                break;
            case '1004':
                $response->message = 'Access denied. TrxID is not related to this username.';
                $response->interpretation = 'Authorization Error';
                break;
            case '2000':
                $response->message = 'Access denied. User does not have access to this module.';
                $response->interpretation = 'Authorization Error';
                break;
            case '2001':
                $response->message = 'Access denied. User date time request is exceeded of the defined limit.';
                $response->interpretation = 'Date time limit Error';
                break;
            case '3000':
                $response->message = 'Missing required mandatory fields for this module.';
                $response->interpretation = 'Missing fields Error';
                break;
            case '9999':
                $response->message = 'Could not process request.';
                $response->interpretation = 'System Error';
                break;
            case '4001':
                $response->message = 'Duplicate request done with same information (e.g. same transaction id)';
                $response->interpretation = 'Duplicate request';
                break;
            case '0000':
                $response->status = true;
                $response->message = 'TrxID is valid and transaction is successful.';
                $response->interpretation = 'Transaction Successful';
                break;
        }

        return $response;
    }

}
