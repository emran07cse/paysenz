<?php
/**
 * Created by PhpStorm.
 * User: Shaiful Islam
 * Time: 8:27 PM
 */

namespace App\Banks\CityBank;

use GuzzleHttp;
use Mockery\Exception;
use DateTime;
use App\CityBankRequest;
use App\PaymentRequest;
use App\PaymentOptionRate;
use Illuminate\Support\Facades\Log;
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
        // Check for existing DutchBangla bank record, if not found create record
        $bankRequest = CityBankRequest::where(['payment_request_id' => $paymentRequest->id])->first();
        if($bankRequest === null) {
            $bankRequest = CityBankRequest::create([
                'payment_request_id' => $paymentRequest->id,
                'payment_option_rate_id' => $paymentOptionRate->id,
                'OrderID' => $response['data'] ? $response['data']['OrderID'] : null,
                'SessionID' => $response['data']? $response['data']['SessionID'] : null,
                'request_state' => 'Redirected to Bank',
                'ip_address' => request_ip_address(),
                'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null
            ]);
        } else {
            $bankRequest->OrderID = $response['data'] ? $response['data']['OrderID'] : null;
            $bankRequest->SessionID = $response['data']? $response['data']['SessionID'] : null;
            $bankRequest->payment_option_rate_id = $paymentOptionRate->id;
            $bankRequest->request_state = 'Redirected to Bank';
            $bankRequest->ip_address = request_ip_address();
            $bankRequest->user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
            $bankRequest->save();
        }

        return $bankRequest;
    }

    
    public function callAndGetGatewayUrlAndData($amountInBDT, $description, $merchantId) : array {
        $tcbData = $this->sendRequestAndGetResponseData($amountInBDT, $description, $merchantId);

        if(empty($tcbData) || count($tcbData) == 0) return null;
        else{
            return [
                'url' => $tcbData['URL'] . "?ORDERID=" . $tcbData['OrderID']. "&SESSIONID=" . $tcbData['SessionID'] . "",
                'data' => $tcbData,
            ];
        }
    }
    
    private function sendRequestAndGetResponseData($amountInBDT, $description, $merchantId) : array
    {
        $sendingXML = "<?xml version='1.0'?>
                        <TKKPG>
                          <Request>
                           <Operation>CreateOrder</Operation> 
                           <Language>EN</Language>
                           <Order>
                             <OrderType>Purchase</OrderType>
                             <Merchant>" . $merchantId . "</Merchant>
                             <Amount>" . $amountInBDT * 100 . "</Amount>
                             <Currency>050</Currency>
                             <Description>" . $description . "</Description>
                             <ApproveURL>". route("callback.citybank.success") ."</ApproveURL>
                             <CancelURL>". route("callback.citybank.cancel") ."</CancelURL>
                             <DeclineURL>". route("callback.citybank.fail") ."</DeclineURL>
                            </Order>
                          </Request>
                         </TKKPG>";
        Log::debug("createPaymentRequest headers=minutes".$sendingXML);
        $xml = $this->postQW($sendingXML);
        Log::debug("createPaymentRequest headers=minutes".$xml);
        return [
            'Status' => (string) $xml->Response->Status,
            'OrderID' => (string) $xml->Response->Order->OrderID,
            'SessionID' => (string) $xml->Response->Order->SessionID,
            'URL' => (string) $xml->Response->Order->URL
        ];
    }

    /**
     * Post can be placed to a separate function that uses sockets (the third-party libraries are not required)
     * The function returns a simplexml object containing a parsed xml.
     *
     * @param $data
     * @return \SimpleXMLElement
     */
    public function postQW($data): \SimpleXMLElement
    {

        //$hostname = $this->getHostname(); // Address of the server with servlet used to work with orders
        //$port = $this->getPort(); // Port
        
        $hostname = '69.16.200.167';
        $port = '943';
        
        $path = '/Exec';
        $content = '';

        // Establish a connection to the $hostname server
        $fp = fsockopen($hostname, $port, $errno, $errstr, 30);

        // Check if the connection is successfully established
        if (!$fp) die('<p>' . $errstr . ' (' . $errno . ')</p>');

        // HTTP request header
        $headers = 'POST ' . $path . " HTTP/1.0\r\n";
        $headers .= 'Host: ' . $hostname . "\r\n";
        //$headers .= "Content-type: application/x-www-form-urlencoded\r\n";
        $headers .= "Content-type: text/xml\r\n";
        $headers .= 'Content-Length: ' . strlen($data) . "\r\n\r\n";
        Log::debug("data:".$data);
        Log::debug("headers:".$headers);
        // Send HTTP request to the server
        fwrite($fp, $headers . $data);

        // Receive response
    	/*while ( !feof($fp) ){
    		$inStr= fgets($fp, 1024);
    		$content .= $inStr;
    	}
    	fclose($fp);
    	// Cut the HTTP response headers. The string can be commented out if it is necessary to parse the header
    	// In this case it is necessary to cut the response
    	$content = substr($content, strpos($content, "<TKKPG>"));*/
    	
    	while (!feof($fp)) {
            $inStr = fgets($fp, 1024);
            if (substr($inStr, 0, 7) !== "<TKKPG>")
                continue;
            // Disconnect
            $content .= $inStr;
        }
        fclose($fp);

        Log::debug("content:".$content);
        // To parse the response, use the simplexml library
        // Documentation on simplexml - http://us3.php.net/manual/ru/book.simplexml.php
        $xml = simplexml_load_string($content); // Load data from the string
        Log::debug("xml:".$xml);
        return ($xml);
    }

    /**
     * Used for confirmation if callback is done from TCB end
     * @param $subMerchantId
     * @param $orderID
     * @param $sessionID
     * @return array
     */
    public function getTransactionConfirmationData($orderID, $sessionID, $merchantId) : array {
        $PAN = "";
       
        // Call GetOrderInformation Operation for getting Order details
		$data='<?xml version="1.0" encoding="UTF-8"?>';
		$data.="<TKKPG>";
		$data.="<Request>";
		$data.="<Operation>GetOrderInformation</Operation>";
		$data.="<Language>EN</Language>";
		$data.="<Order>";
		$data.="<Merchant>".$merchantId."</Merchant>";
		$data.="<OrderID>".$orderID."</OrderID>";
		$data.="</Order>";
		$data.="<SessionID>".$sessionID."</SessionID>";
		$data.="<ShowParams>true</ShowParams>";
		$data.="<ShowOperations>false</ShowOperations>";
		$data.="<ClassicView>true</ClassicView>";
		$data.="</Request></TKKPG>";

        $xml=$this->postQW($data);

        //Extract additional parameters for verification
        foreach($xml->Response->Order->row->OrderParams->row as $item)
        {
            if($item->PARAMNAME == "PAN")
                $PAN = $item->VAL;
        }

        return [
            'PAN' => (string) $PAN,
            'Orderstatus'=> (string) $xml->Response->Order->row->Orderstatus,
            'Amount' => (string) $xml->Response->Order->row->Amount,
            'createDate' => (string) $xml->Response->Order->row->createDate
        ];
    }
    
    public function processTransactionConfirmationData($confirmationData, $bankRequest){
        $paymentVerification = false;
        
        if(is_array($confirmationData)) {
            
            // Save Citybank response data to table
            $bankRequest->TransactionType = isset($confirmationData['TransactionType']) && !is_array($confirmationData['TransactionType']) ? $confirmationData['TransactionType'] : null;
            $bankRequest->RRN = isset($confirmationData['RRN']) && !is_array($confirmationData['RRN']) ? $confirmationData['RRN'] : null;
            $bankRequest->PAN = isset($confirmationData['PAN']) && !is_array($confirmationData['PAN']) ? $confirmationData['PAN'] : null;
            $bankRequest->PurchaseAmount = isset($confirmationData['PurchaseAmount']) && !is_array($confirmationData['PurchaseAmount']) ? $confirmationData['PurchaseAmount'] : null;
            $bankRequest->Currency = isset($confirmationData['Currency']) && !is_array($confirmationData['Currency']) ? $confirmationData['Currency'] : null;
            $bankRequest->TranDateTime = isset($confirmationData['TranDateTime']) && !is_array($confirmationData['TranDateTime']) ? $confirmationData['TranDateTime'] : null;
            $bankRequest->ResponseCode = isset($confirmationData['ResponseCode']) && !is_array($confirmationData['ResponseCode']) ? $confirmationData['ResponseCode'] : null;
            $bankRequest->ResponseDescription = isset($confirmationData['ResponseDescription']) && !is_array($confirmationData['ResponseDescription']) ? $confirmationData['ResponseDescription'] : null;
            $bankRequest->CardHolderName = isset($confirmationData['CardHolderName']) && !is_array($confirmationData['CardHolderName']) ? $confirmationData['CardHolderName'] : null;
            $bankRequest->Brand = isset($confirmationData['Brand']) && !is_array($confirmationData['Brand']) ? $confirmationData['Brand'] : null;
            $bankRequest->OrderStatus = isset($confirmationData['OrderStatus']) && !is_array($confirmationData['OrderStatus']) ? $confirmationData['OrderStatus'] : null;
            $bankRequest->ApprovalCode = isset($confirmationData['ApprovalCode']) && !is_array($confirmationData['ApprovalCode']) ? $confirmationData['ApprovalCode'] : null;
            $bankRequest->AcqFee = isset($confirmationData['AcqFee']) && !is_array($confirmationData['AcqFee']) ? $confirmationData['AcqFee'] : null;
            $bankRequest->MerchantTranID = isset($confirmationData['MerchantTranID']) && !is_array($confirmationData['MerchantTranID']) ? $confirmationData['MerchantTranID'] : null;
            $bankRequest->OrderDescription = isset($confirmationData['OrderDescription']) && !is_array($confirmationData['OrderDescription']) ? $confirmationData['OrderDescription'] : null;
            $bankRequest->ApprovalCodeScr = isset($confirmationData['ApprovalCodeScr']) && !is_array($confirmationData['ApprovalCodeScr']) ? $confirmationData['ApprovalCodeScr'] : null;
            $bankRequest->PurchaseAmountScr = isset($confirmationData['PurchaseAmountScr']) && !is_array($confirmationData['PurchaseAmountScr']) ? $confirmationData['PurchaseAmountScr'] : null;
            $bankRequest->CurrencyScr = isset($confirmationData['CurrencyScr']) && !is_array($confirmationData['CurrencyScr']) ? $confirmationData['CurrencyScr'] : null;
            $bankRequest->OrderStatusScr = isset($confirmationData['OrderStatusScr']) && !is_array($confirmationData['OrderStatusScr']) ? $confirmationData['OrderStatusScr'] : null;
            $bankRequest->Name = isset($confirmationData['Name']) && !is_array($confirmationData['Name']) ? $confirmationData['Name'] : null;
            $bankRequest->ThreeDSVerificaion = isset($confirmationData['ThreeDSVerificaion']) && !is_array($confirmationData['ThreeDSVerificaion']) ? $confirmationData['ThreeDSVerificaion'] : null;
            $bankRequest->ThreeDSStatus = isset($confirmationData['ThreeDSStatus']) && !is_array($confirmationData['ThreeDSStatus']) ? $confirmationData['ThreeDSStatus'] : null;

            // Fillup Common Bank fields
            $bankRequest->gateway_response = json_encode($confirmationData);
            $bankRequest->card_no = isset($confirmationData['PAN']) && !is_array($confirmationData['PAN']) ? $confirmationData['PAN'] : null;
            $bankRequest->card_holder_name = isset($confirmationData['CardHolderName']) && !is_array($confirmationData['CardHolderName']) ? $confirmationData['CardHolderName'] : null;
            $bankRequest->card_type = isset($confirmationData['Brand']) && !is_array($confirmationData['Brand']) ? $confirmationData['Brand'] : null;

            $bankRequest->amount = isset($confirmationData['PurchaseAmount']) && !is_array($confirmationData['PurchaseAmount']) ? $confirmationData['PurchaseAmount'] : null;
            $bankRequest->amount = (float) ($bankRequest->amount / 100);

            // Create MySQL format payment_time from `TranDateTime` data
            if(isset($confirmationData['TranDateTime'])){
                $tempArray = explode(' ', $confirmationData['TranDateTime']);
                $dateTran = $tempArray[0];
                $timeTran = $tempArray[1];
                if(!empty($dateTran)){
                    $myDateTime = DateTime::createFromFormat('d/m/Y', $dateTran);
                    $newDateString = $myDateTime->format('Y-m-d') . ' ' . $timeTran;
                    $bankRequest->payment_time = date('Y-m-d H:i:s', strtotime($newDateString));    
                }
            }
            
            $paymentRequest = PaymentRequest::where(['id' => $bankRequest->payment_request_id])->first();
            // $bankAmount = (float) ($bankRequest->amount / 100);
             $bankAmount = (float) ($bankRequest->amount);
            $requestAmount = (float) $paymentRequest->amount;
            $amountDiff = ($requestAmount - $bankAmount);
            // var_dump($amountDiff);
            //   var_dump($bankRequest->amount);
            // exit();

            if($confirmationData['OrderStatus'] == 'APPROVED') {
                // Check if the Order amount and bKash amount is same.
                if($amountDiff == 0){
                    echo "CityBank payment verified successfully!!";
                    $bankRequest->payment_description = "CityBank  payment verified successfully!!";
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
                    $bankRequest->payment_description = "Error: CityBank payment verified but Payment amount mismatch. Order amount: {$requestAmount}, Bkash amount: {$bankAmount}";
                    $bankRequest->request_state = 'Verification failed';
                }
            } else {
                $bankRequest->payment_description = "CityBank Payment failed";
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
    
    public static function decodeXmlResponse($xmlmsg){
        $xmlResponse = simplexml_load_string($xmlmsg);
		$json = json_encode($xmlResponse);
		return json_decode($json, TRUE);
    }
    
    /*protected function getMerchantId(){
        return $this->mode === 1 ? config('Banks.CityBank.Bank.merchatId') : config('Banks.CityBank.Bank.merchantId_sandbox') ;
    }*/
    
    protected function getHostname(){
        return $this->mode === 1 ? config('Banks.CityBank.Bank.hostname') : config('Banks.CityBank.Bank.hostname_sandbox') ;
    }
    
    protected function getPort(){
        return $this->mode === 1 ? config('Banks.CityBank.Bank.port') : config('Banks.CityBank.Bank.port_sandbox') ;
    }

    
}
