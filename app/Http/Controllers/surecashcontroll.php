<?php
/**
 * Created by PhpStorm.
 * User: Amin
 * Date: 2/27/2019
 * Time: 12:54 AM
 */

namespace App\Http\Controllers;


use App\BkashCheckoutTxnModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\PaymentRequest;
use App\AppClient;
use App\surecashBankRequest;
use App\PaymentOptionRate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class surecashcontroll extends ProcessRequestController
{
    private $bkashUsername = "PAYSENZLTD";
    private $bkashPassword = "p@A1Y7s4dlM";
    private $bkashAppKey = "4g5su531jdpbaur8d9rb7uhu0m";
    private $bkashAppSecret = "199jnni8ht50a667lp5kvo202bf2oc85ckl0gjbsrtisdl1tdo3d";
    private $bkashBaseUrl = "https://checkout.pay.bka.sh/v1.2.0-beta";
    
    private $bkashAccessTokenUrl = "/checkout/token/grant";
    private $bkashPaymentCreateUrl = "/checkout/payment/create";
    private $bkashPaymentExecuteUrl = "/checkout/payment/execute/";
    private $bkashPaymentQueryUrl = "/checkout/payment/query/";


    private function getBkashToken()
    {

        $header = array(
            'Content-Type: application/json',
            'username:' . $this->bkashUsername,
            'password:' . $this->bkashPassword
        );
        $params = array(
            'app_key' => $this->bkashAppKey,
            'app_secret' => $this->bkashAppSecret
        );
        $url = $this->bkashBaseUrl . $this->bkashAccessTokenUrl;
        $data = $this->callApiPost($url, $header, json_encode($params));
        if ($data['status'] > 199 && $data['status'] < 300) {
            return json_decode($data['response'], true);
        } else {
            return null;
        }
    }

    private function callApiPost($url, $headers, $params, $method='POST')
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $data['status'] = $http_code;
        if ($err) {
            $data['error'] = $err;
        } else {
            $data['response'] = $response;
        }

        Log::debug(" data" .json_encode($data));

        return $data;

    }

    public function createPaymentRequest(Request $request)
    {
        $validatorResponse = $this->validateRequest($request, [
            'amount' => 'required',
            'txnid' => 'required'
        ]);

        if ($validatorResponse !== true) {
            return $this->sendErrorResponse($validatorResponse);
        }
        $inputs = $request->all();
        $paymentRequest = PaymentRequest::where(['txnid' => $inputs['txnid']])->first();
        $clientInfo = AppClient::with('user')->where('id', $paymentRequest->client_id)->first();
        
        $oauth_clients=DB::table('oauth_clients')->where('id', $paymentRequest->client_id)->first();
        $MerchantName = str_replace(' ', '_', $oauth_clients->name);
        
        if($inputs['amount']==''||$inputs['txnid']==''){return $this->formatError('200', 'Your Session Out. please try again.');}
        
        $amount=$paymentRequest->amount;
        
        if($inputs['amount'] > $amount || $inputs['amount']< $amount ){return $this->formatError('200', 'Your payment amount mismatch.Please Try Again.');}
        
        $createdate=$paymentRequest->created_at;
        $minutes = abs(strtotime($createdate) - time()) / 60;
        if($minutes>14){return $this->formatError('200', 'Your Payment Request Time Out. please try again.');}
        
        $midLength = strlen($MerchantName);
        if($midLength <10){
            $midLength = "0".$midLength;
        }
        $rfLength=strlen($inputs['txnid']);
        if($rfLength <10){
            $rfLength = "0".$rfLength;
        }

        $merchantAssociationInfo="MI".$midLength.$MerchantName."RF".$rfLength.$inputs['txnid'];
        $params = array(
            'amount' =>$amount,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => $inputs['txnid'],
            'merchantAssociationInfo' => $merchantAssociationInfo
        );

        $tokenData = $this->getBkashToken();
        $headers = array(
            'Content-Type: application/json',
            'authorization:' . $tokenData['id_token'],
            'x-app-key:' . $this->bkashAppKey);

        $url = $this->bkashBaseUrl . $this->bkashPaymentCreateUrl;
        $bankRequest = BkashCheckoutTxnModel::where(['OrderNo' => $inputs['txnid']])->first();

        $data = $this->callApiPost($url, $headers, json_encode($params));
        Log::debug("createPaymentRequest headers=minutes".round($minutes) .json_encode($headers). " params is=" . json_encode($params));
        if ($data['status'] > 199 && $data['status'] < 300) {
            $bkashResponse = json_decode($data['response'], true);
            BkashCheckoutTxnModel::where(['OrderNo' => $inputs['txnid']])->update(
                array(
                    "paymentID" => $bkashResponse['paymentID'],
                    "transactionStatus" => $bkashResponse['transactionStatus'],
                )
            );
            Log::debug("createPaymentRequest response=". $data['response']);
            return new JsonResponse($bkashResponse);
        } else {
            return $this->formatError($data['status'], $data['error']);
        }
    }

    public function executePaymentRequest(Request $request)
    {
        $validatorResponse = $this->validateRequest($request, [
            'paymentID' => 'required'
        ]);

        if ($validatorResponse !== true) {
            return $this->sendErrorResponse($validatorResponse);
        }
        $inputs = $request->all();
        $paymentID = $inputs['paymentID'];
        /*if ($request->session()->exists('bKashTokenData')) {
            $tokenData = json_decode($request->session()->get('key', 'default'));
        } else{
            $tokenData = $this->getBkashToken();
            $request->session()->put('bKashTokenData', json_encode($tokenData));
        }*/
        $tokenData = $this->getBkashToken();
        $headers = array(
            'Content-Type:application/json',
            'authorization:' . $tokenData['id_token'],
            'x-app-key:' . $this->bkashAppKey);

        $url = $this->bkashBaseUrl . $this->bkashPaymentExecuteUrl . $paymentID;
        $data = $this->callApiPost($url, $headers, null);
        Log::debug("executePaymentRequest headers=" .json_encode($headers). " url is=" . json_encode($url));
        if ($data['status'] > 199 && $data['status'] < 300) {
            $bkashResponse = json_decode($data['response'], true);
            Log::debug("executePaymentRequest response=". $data['response']);
            $errorCode = isset($bkashResponse['errorCode']) ? $bkashResponse['errorCode'] : 100;
            $errorMsg= isset($bkashResponse['errorMessage']) ? $bkashResponse['errorMessage'] : "";
            if ($errorCode == 100) {
                BkashCheckoutTxnModel::where(['paymentID' => $paymentID])->update(
                    array(
                        "paymentID" => $bkashResponse['paymentID'],
                        "transactionStatus" => $bkashResponse['transactionStatus'],
                        "response" => $data['response'],
                        "TxnId" => $bkashResponse['trxID'],
                        "createTime" => $bkashResponse['createTime'],
                        "updateTime" => $bkashResponse['updateTime'],
                        "error_code"=>$errorCode,
                        "error_msg"=>$errorMsg,
                    )
                );
            } else{
                BkashCheckoutTxnModel::where(['paymentID' => $paymentID])->update(
                    array(
                        "error_code"=>$errorCode,
                        "error_msg"=>$errorMsg,
                    )
                );
            }

            return new JsonResponse($bkashResponse);
        } else {
            return $this->formatError($data['status'], $data['error']);
        }
    }

    public function queryPaymentRequest($paymentID)
    {
        $tokenData = $this->getBkashToken();
        $headers = array(
            'Content-Type:application/json',
            'authorization:' . $tokenData['id_token'],
            'x-app-key:' . $this->bkashAppKey);

        $url = $this->bkashBaseUrl . $this->bkashPaymentQueryUrl . $paymentID;

        return $this->callApiPost($url, $headers, null, "GET");
    }

    public function process($txnId)
    {
        if (!empty($txnId)) {
            $paymentRequest = PaymentRequest::where(['txnid' => $txnId])->first();
            if ($paymentRequest && $paymentRequest->isReadyForPayment()) {
                // Check for existing bKash bank record, if not found create record
                $bankRequest = BkashCheckoutTxnModel::where(['OrderNo' => $paymentRequest->txnid])->first();
                if (!$bankRequest) {
                    foreach (AppClient::find($paymentRequest->client_id)->paymentOptionRates as $paymentOptionRate) {
                        if ($paymentOptionRate->paymentOption->bank->short_code == 'bkash') {
                            $bankRequest = BkashCheckoutTxnModel::create([
                                'OrderNo' => $paymentRequest->txnid,
                                'RequestAmount' => $paymentRequest->amount,
                                'Txnamount' => $paymentRequest->amount,
                                'payment_option_rate_id' => $paymentOptionRate->id,
                                'status' => 0,
                                'createTime' => date('Y-m-d H:i:s'),
                                'updateTime' => date('Y-m-d H:i:s'),
                                'currency' => "BDT",
                                'intent' => "sale",
                                'transactionStatus' => "INITIATED",
                            ]);
                            BkashBankRequest::create([
                                'payment_request_id' => $paymentRequest->id,
                                'payment_option_rate_id' => $paymentOptionRate->id,
                                'request_state' => 'Redirected to Bkash Checkout'
                            ]);
                            break;
                        }
                    }
                } // End bKash bank record.

                return view('processRequest.bkashcheckoutprocess')->with(array('paymentRequest' => $paymentRequest));
            } else {
                return "PAYMENT IS ALREADY PROCESSED FOR THIS REQUEST";
            }
        } else {
            return "Invalid Payment ID";
        }
    }

    public function completePayment($txnId)
    {
        $paymentRequest = PaymentRequest::where(['txnid' => $txnId])->first();
        $bkashTxnModel = BkashCheckoutTxnModel::where(['OrderNo' => $paymentRequest->txnid])->first();
        $data = $this->queryPaymentRequest($bkashTxnModel->paymentID);
        if($bkashTxnModel->error_code != 100){
            return redirect()->route('process.bkashCheckout', ['txnId' => $txnId])->with('status', "Payment Failed. Reason: ".$bkashTxnModel->error_msg);
        }

        if ($data['status'] > 199 && $data['status'] < 300) {
            $bkashResponse = json_decode($data['response']);
            Log::debug("queryPaymentRequest response=". $data['response']);
            $errorCode = isset($bkashResponse->errorCode) ? $bkashResponse->errorCode : 100;
            if ($errorCode != 100) {
                return redirect()->route('process.bkashCheckout', ['txnId' => $txnId])->with('status', "Payment Failed. Reason: ".$bkashResponse->errorMessage);
            }
        } else {
            return redirect()->route('process.bkashCheckout', ['txnId' => $txnId])->with('status', "Unable to verify. Please contact support.");
        }

        //$bkashResponse = json_decode($bkashTxnModel->response);
        $bankRequest = BkashBankRequest::where(['payment_request_id' => $paymentRequest->id])->first();
        $responseArray = $this->processTransactionConfirmationData($bkashResponse, $bankRequest);
        $paymentOptionRate = PaymentOptionRate::find($bankRequest->payment_option_rate_id);
        if ($responseArray) {
            $responseArray['bankData']['paysenz_charge_percentage'] = $paymentOptionRate->paysenz_charge_percentage;
            if ($bankRequest->status == 1) {
                return $this->_goToCallBackPage($responseArray['paymentRequest'], $responseArray['bankData']);
            } else {
                return redirect()->route('process.bkash', ['txnId' => $txnId])->with('status', $bankRequest->payment_description);
            }

        }
    }


    private function processTransactionConfirmationData($bkashResponse, $bankRequest)
    {
        $bkashPaymentVerification = false;

        // Update the bKash Bank Request row with the response
        $bankRequest->trxStatus = isset($bkashResponse->transactionStatus) ? $bkashResponse->transactionStatus : null;
        $bankRequest->counter = null;
        $bankRequest->reference = null;
        $bankRequest->reversed = null;
        $bankRequest->sender = null;
        $bankRequest->service = isset($bkashResponse->intent) ? $bkashResponse->intent : null;
        $bankRequest->currency = isset($bkashResponse->currency) ? $bkashResponse->currency : null;
        $bankRequest->receiver = null;
        $bankRequest->trxTimestamp = isset($bkashResponse->updateTime) ? $bkashResponse->updateTime : null;
        $bankRequest->ip_address = request()->getClientIp() ?? request_ip_address();
        $bankRequest->user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        // Fillup Common Bank fields
        $bankRequest->gateway_response = json_encode($bkashResponse);
        $bankRequest->amount = isset($bkashResponse->amount) ? $bkashResponse->amount : null;
        $bankRequest->payment_time = isset($bkashResponse->updateTime) ? date('Y-m-d H:i:s', strtotime($bkashResponse->updateTime)) : null;

        $paymentRequest = PaymentRequest::where(['id' => $bankRequest->payment_request_id])->first();


        if ($bkashResponse->transactionStatus == "Completed") {
            // Store the user provided TrxId for after validation
            $bankRequest->trxId = isset($bkashResponse->trxID) ? $bkashResponse->trxID : null;
            $bankRequest->card_no = $bkashResponse->trxID;
            $bankRequest->card_holder_name = null;
            $bankRequest->card_type = 'BKASH';

            // Check if the Order amount and bKash amount is same.
            $amount = $paymentRequest->amount;
            if ($bkashResponse->amount >= $amount) {
                //echo "Bkash payment verified successfully!!";
                $bankRequest->payment_description = "Bkash payment verified successfully!!";
                $bankRequest->request_state = 'Finished and Confirmed';
                $bankRequest->status = 1;
                $bkashPaymentVerification = true;

                // Update bank and store charge
                $paymentOptionRate = PaymentOptionRate::find($bankRequest->payment_option_rate_id);
                if ($paymentOptionRate) {
                    $store_charge_amount = ($paymentOptionRate->paysenz_charge_percentage > 0) ? ($bkashResponse->amount * ($paymentOptionRate->paysenz_charge_percentage / 100)) : 0;
                    $bank_charge_amount = ($paymentOptionRate->bank_charge_percentage > 0) ? ($bkashResponse->amount * ($paymentOptionRate->bank_charge_percentage / 100)) : 0;
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
        if ($bkashPaymentVerification == true) {
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


}