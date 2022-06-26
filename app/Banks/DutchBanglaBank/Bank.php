<?php
/**
 * Created by PhpStorm.
 * User: Tareq Mahbub
 * Date: 12-Aug-17
 * Time: 1:40 PM
 */


namespace App\Banks\DutchBanglaBank;

use Illuminate\Http\Request;
use App\DutchBanglaBankRequest;
use App\PaymentRequest;
use App\PaymentOptionRate;
use Illuminate\Support\Facades\Log;
use SoapClient;
class Bank
{
    protected $submerchantInfos = null;
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
    public function createBankRequest($paymentRequest, $paymentOptionRate, $transactionId, $cardTypeId){
        // Check for existing DutchBangla bank record, if not found create record
        $bankRequest = DutchBanglaBankRequest::where(['payment_request_id' => $paymentRequest->id])->first();
        if($bankRequest === null) {
            $bankRequest = DutchBanglaBankRequest::create([
                'payment_request_id' => $paymentRequest->id,
                'payment_option_rate_id' => $paymentOptionRate->id,
                'trans_id' => $transactionId,
                'card_type' => $cardTypeId,
                'request_state' => 'Redirected to Payment Gateway',
                'ip_address' => request_ip_address(),
                'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null
            ]);
        } else {
            $bankRequest->trans_id = $transactionId;
            $bankRequest->payment_option_rate_id = $paymentOptionRate->id;
            $bankRequest->card_type = $cardTypeId;
            $bankRequest->ip_address = request_ip_address();
            $bankRequest->user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
            $bankRequest->save();
        }

        return $bankRequest;
    }

    public function getGatewayUrlData($cardType, $amount, $details, $subMerchantId, $subMerchantTerminalId, $subMerchantName) : array{
        //$dbblData = $this->callAndGetDBBLTransactionId($amount, $details, $subMerchantId, $subMerchantTerminalId, $subMerchantName);
        
        $context='';
		//$wsdlUrl = 'https://ecom.dutchbanglabank.com/ecomws/dbblecomtxn?wsdl';
		$wsdlUrl = 'http://ecomtest.dutchbanglabank.com:8080/ecomws/dbblecomtxn?wsdl';
		/*$soapClientOptions = array(
			'stream_context' => $context,
			'cache_wsdl' => WSDL_CACHE_NONE
		);*/
        $soapClientOptions = array(
            'soap_version'=>SOAP_1_2,
            'exceptions'=>true,
            'trace'=>1,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'trace' => 1,
            'stream_context' => stream_context_create(array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
        )));

		/*$dbblparentmerchantId = '000599991040000'; 
		$pwd = 'enQ6coCN70ICQ';
		$merchant_name = 'PAYSZ-Mudishop';
		$submerchant_id = '000599001080001';
		$terminal_id = '59901579';*/
		
	    $dbblparentmerchantId = '000599990830000'; 
    	$pwd = 'enJZfD1.06xmg';
    	$merchant_name = 'FOSTER-X-press';
    	$submerchant_id = '000599000830029';
    	$terminal_id = '59901203';
		
		$clientIp = $_SERVER['REMOTE_ADDR'] ;
		$cParameters = array(
			'userid' => $dbblparentmerchantId,
			'pwd' => $pwd,
			'submername' => $merchant_name,
			'submerid' => $submerchant_id,
			'terminalid' => $terminal_id,
			'amount' => $amount*100,
			'cardtype' => $cardType,
			'txnrefnum' => 'kjjkjlkj',
			'clientip' => $clientIp
		);

		Log::debug("cParameters is=" . json_encode($cParameters));
		
		try {
		    libxml_disable_entity_loader(false);
		    //$client = new SoapClient($wsdlUrl, $soapClientOptions);
		    $dbblData=$this->_soapClient = new SoapClient($wsdlUrl, array('exceptions'=>true, 'trace' => true));
        } 
        catch (Exception $e) {
            echo 'Catch it!';
        }
        
		//$client = new SoapClient($wsdlUrl, $soapClientOptions);		
        $dbblData = $client->getsubmertransid($cParameters);
		Log::debug("cParameters is=" . json_encode($dbblData));
		foreach ($dbblData as $value){
			$arr = explode(":",$value);
			$part1 = $arr[0];
			$part2 = $arr[1];
			if ($part1 == "TRANSACTION_ID") {$transactionId = $part2;}
			else{$transactionId="";}
		}
        if(empty($dbblData) || count($dbblData) == 0 ||$transactionId=='') return array();
        else{
			/*return [
                'url' => $this->getPaymentUrl() . "?card_type=$cardType&trans_id=" . urlencode($dbblData['transactionId']),
                'transactionId' => $dbblData['transactionId'],
            ];*/
            return [
                'url' => $this->getPaymentUrl() . "?card_type=$cardType&trans_id=$transactionId",
                'transactionId' => $transactionId,
            ];
        }
    }

    /**
     * @param $store_id
     * @param $amount
     * @param $details
     * @return array|null
     */
    public function callAndGetDBBLTransactionId($amount, $details, $subMerchantId, $subMerchantTerminalId, $subMerchantName) : array
    {
        $ip = request()->getClientIp() ?? request_ip_address();
        $outputArray = [];
        
        // Generate command based on Live-mode or Sandbox-mode, Live will have sub-merchant informations.
        $str = $this->mode === 1 
            ? $this->createDBBLRequestStringWithSubmerchant($amount*100, $ip, $details, $subMerchantId, $subMerchantTerminalId, $subMerchantName) 
            : $this->createDBBLRequestStringWithoutSubmerchant($amount*100, $ip, $details);
        //echo "DBBL Transaction generation command: " .  $str;
        exec($str, $outputArray);
        if(!empty($outputArray)){
            $final = end($outputArray);
            if(strlen(trim($final)) > 0){
                return [
                    'transactionId' => substr($final, 16, 40),
                    'queryResponse' => json_encode($outputArray),
                ];
            }else{
                return null;
            }
        }else{
            return null;
        }
    }
    
    /**
     * @param $dbblTransactionId
     * @return mixed
     */
    public function getTransactionConfirmationData($dbblTransactionId) : array
    {
        $ip = request()->getClientIp() ?? request_ip_address();
        $str = $this->getJavaPath() . " -jar " . $this->getJarPath() . " " . $this->getMerchantPropertyPath() . " -c $dbblTransactionId $ip";

        exec($str, $outputArray);
        $dbbl_return_data = [];
        foreach ($outputArray as $name => $value) {
            $dbbl = explode(":", $value);
            $keyto = $dbbl[0];
            if ($keyto != 'CARDNAME') {
                $dbbl_return_data[$keyto] = str_replace(' ', '', $dbbl[1]);
            } else {
                $dbbl_return_data[$keyto] = $dbbl[1];
            }
        }

        return $dbbl_return_data;
    }
    
    public function processTransactionConfirmationData($confirmationData, $bankRequest){
        $paymentVerification = false;
        
        if(is_array($confirmationData)) {
            foreach($confirmationData as $key => $value){
                if ($key == "RESULT") { // FAILED|OK
                    $bankRequest->RESULT = $value;
                } else if ($key == "RESULT_CODE") { // 911: Error, 000: Success
                    $bankRequest->RESULT_CODE = $value;
                } else if ($key == "RRN") {
                    $bankRequest->RRN = $value;
                } else if ($key  == "APPROVAL_CODE") {
                    $APPROVAL_CODE = $value;
                } else if ($key == "CARD_NUMBER") {
                    $bankRequest->CARD_NUMBER = $value;
                } else if ($key == "AMOUNT") {
                    $bankRequest->AMOUNT = $value;
                } else if ($key == "TRANS_DATE") {
                    date_default_timezone_set('Etc/GMT-6');
                    $bankRequest->TRANS_DATE = date('Y-m-d H:i:s', $value/1000);
                } else if ($key == "CARDNAME") {
                    $bankRequest->CARDNAME = $value;
                } else if ($key == "DESCRIPTION") {
                    $bankRequest->DESCRIPTION = $value;
                } else {
                    // Unknown value;
                }
            }

            // Fillup Common Bank fields
            $bankRequest->gateway_response = json_encode($confirmationData);
            $bankRequest->card_no = isset($confirmationData['CARD_NUMBER']) && !is_array($confirmationData['CARD_NUMBER']) ? $confirmationData['CARD_NUMBER'] : null;
            $bankRequest->card_holder_name = isset($confirmationData['CARDNAME']) && !is_array($confirmationData['CARDNAME']) ? $confirmationData['CARDNAME'] : null;
            date_default_timezone_set('Etc/GMT-6');
            $bankRequest->payment_time = isset($confirmationData['TRANS_DATE']) && !is_array($confirmationData['TRANS_DATE']) ? date('Y-m-d H:i:s', $confirmationData['TRANS_DATE']/1000) : null;
            
            $paymentRequest = PaymentRequest::where(['id' => $bankRequest->payment_request_id])->first();
            $bankAmount = (float) ($bankRequest->AMOUNT / 100);
            $requestAmount = (float) $paymentRequest->amount;
            $amountDiff = ($requestAmount - $bankAmount);
            if($bankRequest->RESULT == 'OK') {
                // Check if the Order amount and bKash amount is same.
                if($amountDiff == 0){
                    echo "DBBL payment verified successfully!!";
                    $bankRequest->payment_description = "DBBL  payment verified successfully!!";
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
                    $bankRequest->payment_description = "Error: DBBL payment verified but Payment amount mismatch. Order amount: {$requestAmount}, Bkash amount: {$bankAmount}";
                    $bankRequest->request_state = 'Verification failed';
                }
            } else {
                $bankRequest->payment_description = "DBBL Payment failed";
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
                'card_type' => self::getCardBrandName($bankRequest->card_type),
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



    /**
     * @param $amount
     * @param $ip
     * @param $details
     * @param $merchantName
     * @param $subMerchantId
     * @param $terminalId
     * @return string
     */
    public function createDBBLRequestStringWithSubmerchant($amount, $ip, $details, $subMerchantId, $subMerchantTerminalId, $subMerchantName) : string {
        if(empty($subMerchantId) || empty($subMerchantTerminalId) || empty($subMerchantName)) {
            die('DBBL SubMerchantId/TerminalId/SubMerchantName not found for this Merchant.');
        }
        
        return "{$this->getJavaPath()} -jar {$this->getJarPath()} {$this->getMerchantPropertyPath()}  -v {$amount} 050 {$ip} {$details} --merchant_name={$subMerchantName} --etid=Y --bank_comm=0 --msp_comm=0 --vat_comm=0 --submerchant_id={$subMerchantId} --terminal_id={$subMerchantTerminalId} --dbblpan={$this->getDbblpanLive()}";
    }

    public static function getCardBrandName($card_type){
        $brandName = '';
        if($card_type == 1){
            $brandName = 'DBBL NEXUS';
        } elseif($card_type == 2){
            $brandName = 'MasterDebit';
        } elseif($card_type == 3){
            $brandName = 'VisaDebit';
        } elseif($card_type == 4){
            $brandName = 'VISA';
        } elseif($card_type == 5){
            $brandName = 'MasterCard';
        } elseif($card_type == 6){
            $brandName = 'ROCKET';
        }

        return $brandName;
    }

    /**
     * @param $amount
     * @param $ip
     * @param $details
     * @return string
     */
    public function createDBBLRequestStringWithoutSubmerchant($amount, $ip, $details) : string {
        return $this->getJavaPath() . " -jar " . $this->getJarPath() . " " . $this->getMerchantPropertyPath() . " -v $amount 050 $ip $details";
    }

    protected function getJavaPath(){
        return config('Banks.DutchBanglaBank.Bank.javaPath');
    }

    protected function getJarPath(){
        return $this->mode === 1
            ? config('Banks.DutchBanglaBank.Bank.live.ecomPath') . '/' . config('Banks.DutchBanglaBank.Bank.dbblJarPath')
            : config('Banks.DutchBanglaBank.Bank.sandbox.ecomPath') . '/' . config('Banks.DutchBanglaBank.Bank.dbblJarPath');
    }

    protected function getMerchantPropertyPath(){
        return $this->mode === 1
            ? config('Banks.DutchBanglaBank.Bank.live.ecomPath') . '/' . config('Banks.DutchBanglaBank.Bank.dbblPropertyFilePath')
            : config('Banks.DutchBanglaBank.Bank.sandbox.ecomPath') . '/' . config('Banks.DutchBanglaBank.Bank.dbblPropertyFilePath');
    }
    
    protected function getPaymentUrl(){
        return $this->mode === 1 ? config('Banks.DutchBanglaBank.Bank.live.url') : config('Banks.DutchBanglaBank.Bank.sandbox.url') ;
    }
    
    // For Live only
    protected function getMerchantName(){
        return config('Banks.DutchBanglaBank.Bank.live.ecomPath');
    }
    
    // For Live only
    protected function getSubMerchantId(){
        return config('Banks.DutchBanglaBank.Bank.live.submerchantId');
    }
    
    // For Live only
    protected function getSubTernimalId(){
        return config('Banks.DutchBanglaBank.Bank.live.terminalId');
    }
    
    // For Live only
    protected function getDbblpanLive(){
        return config('Banks.DutchBanglaBank.Bank.live.dbblpan');
    }
    
}
