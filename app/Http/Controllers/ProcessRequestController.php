<?php

namespace App\Http\Controllers;

use App\AppClient;
use App\CityBankRequest;
use App\BkashBankRequest;
use App\DutchBanglaBankRequest;
use App\EblBankRequest;
use App\surecashBankRequest;
use App\PaymentOption;
use App\PaymentOptionRate;
use App\PaymentRequest;
use App\Banks\CityBank;
use App\Banks\BkashBank;
use App\Banks\DutchBanglaBank;
use App\Banks\surecash;
use App\Banks\EBLBank;
use App\Mail\InvoiceEmail;
use Illuminate\Http\Request;
use GuzzleHttp;
use Illuminate\Support\Facades\Mail;
use phpDocumentor\Reflection\Types\Integer;
use PDF;

class ProcessRequestController extends Controller {

    public function index($txnId) {
        if (!empty($txnId)) {
            $paymentRequest = PaymentRequest::where(['txnid' => $txnId])->first();
            if ($paymentRequest !== null) {
                if ($paymentRequest && $paymentRequest->isReadyForPayment()) {
                    $paymentOptionRates = AppClient::find($paymentRequest->client_id)->paymentOptionRatesActive;

                    // Group by Cards and mobile banking
                    $paymentOptionRatesCards = [];
                    $paymentOptionRatesMobile = [];
                    if (count($paymentOptionRates)) {
                        foreach ($paymentOptionRates as $_paymentOptionRate) {
                            if ($_paymentOptionRate->paymentOption->type == 'Card') {
                                $paymentOptionRatesCards[] = $_paymentOptionRate;
                            }
                            if ($_paymentOptionRate->paymentOption->type == 'Mobile') {
                                $paymentOptionRatesMobile[] = $_paymentOptionRate;
                            }
                        }
                    }


                    return view('processRequest.index')->with(array(
                                'paymentRequest' => $paymentRequest,
                                'currency' => PaymentRequest::convertCurrency($paymentRequest->currency, $paymentRequest->currency_of_transaction, null),
                                'paymentOptionRates' => $paymentOptionRates,
                                'paymentOptionRatesCards' => $paymentOptionRatesCards,
                                'paymentOptionRatesMobile' => $paymentOptionRatesMobile,
                    ));
                } else {
                    return "REQUEST IS ALREADY PROCESSED";
                }
            } else {
                return "REQUEST NOT FOUND";
            }
        } else {
            return "INVALID REQUEST";
        }
    }

    public function selectbank($txnId, $optionId) {

        if (!empty($txnId) && (int) $optionId) {
            $paymentRequest = PaymentRequest::where(['txnid' => $txnId])->first();
            $paymentOptionRate = PaymentOptionRate::find($optionId);


            if ($paymentRequest && $paymentRequest->isReadyForPayment()) {
                $client = AppClient::find($paymentRequest->client_id);
                switch ($paymentOptionRate->paymentOption->bank->short_code) {
                    case 'TCB':
                        $amount = $paymentRequest->amount;
                        $details = $paymentRequest->order_details;
                        $bank = new CityBank\Bank($paymentOptionRate->is_live);
                        $merchantId = $client->user->tcb_id;


                        if (empty($merchantId)) {
                            die('CityBank MerchantId not found for this Merchant/Client.');
                        }
                        $response = $bank->callAndGetGatewayUrlAndData($amount, $details, $merchantId);
                        if ($response) {

                            $bankRequest = $bank->createBankRequest(
                                    $paymentRequest, $paymentOptionRate, $response
                            );


                            return redirect()->away($response['url']);
                        } else {
                            $errmsg = 'System Error. Unble to generate CityBank OrderID';
                            echo $errmsg;
                            exit;
                        }

                        return redirect()->away($response['url']);
                        break;
                    case 'DBBL':
                        $amount = (int) $paymentRequest->amount;
                        $details = $paymentRequest->order_details;
                        $bank = new DutchBanglaBank\Bank($paymentOptionRate->is_live);

                        // Get SubMerchant Informations
                        $subMerchantId = $client->user->dbbl_id;
                        $subMerchantTerminalId = $client->user->dbbl_terminal_id;
                        $subMerchantName = $client->user->dbbl_name;
                        $subMerchantFullName = $client->user->dbbl_fullname;

                        $cardTypeId = $paymentOptionRate->paymentOption->param_1;
                        $details = $cardTypeId . '^' . $paymentRequest->order_id_of_merchant;
                        $gatewayData = $bank->getGatewayUrlData($cardTypeId, $amount, $details, $subMerchantId, $subMerchantTerminalId, $subMerchantName);

                        // Check for existing DutchBangla bank record, if not found create record
                        if ($gatewayData && isset($gatewayData['transactionId']) && isset($gatewayData['url'])) {
                            $bankRequest = $bank->createBankRequest(
                                    $paymentRequest, $paymentOptionRate, $gatewayData['transactionId'], $cardTypeId
                            );

                            return redirect()->away($gatewayData['url']);
                        } else {
                            $errmsg = 'System Error. Unble to generate DBBL transaction_id';
                            echo $errmsg;
                            exit;
                        }
                        break;

                    case 'bkash':
                        // Check for existing bKash bank record, if not found create record
                        $bankRequest = BkashBankRequest::where(['payment_request_id' => $paymentRequest->id])->first();
                        if ($bankRequest === null) {
                            BkashBankRequest::create([
                                'payment_request_id' => $paymentRequest->id,
                                'payment_option_rate_id' => $paymentOptionRate->id,
                                'request_state' => 'Redirected to Bkash Form'
                            ]);
                        }
                        return redirect()->route('process.bkash', ['txnId' => $txnId]);
                    case 'surecash':
                        // Check for existing surecash bank record, if not found create record
                        /*$bankRequest = surecashBankRequest::where(['payment_request_id' => $paymentRequest->id])->first();
                        if ($bankRequest === null) {
                            surecashBankRequest::create([
                                'payment_request_id' => $paymentRequest->id,
                                'payment_option_rate_id' => $paymentOptionRate->id,
                                'request_state' => 'Redirected to surecash Form'
                            ]);
                        }*/
                        return redirect()->route('process.surecash', ['txnId' => $txnId]);

                    case 'EBL':
                        $amount = $paymentRequest->amount;
                        $details = $paymentRequest->order_details;
                        $bank = new EBLBank\Bank($paymentOptionRate->is_live);

                        $subMerchantInfo = array(
                            'subMerchantId' => $client->user->ebl_id,
                            'subMerchantPassword' => $client->user->ebl_password
                        );


                        $orderId = 'EBL' . $paymentRequest->client_id . $paymentRequest->txnid; // Join clientId and Txnid for unique orderId
                        $data = array(
                            'order' => array(
                                'amount' => $amount,
                                'id' => $orderId,
                                'description' => $details,
                                'currency' => 'BDT',
                            ),
                            'interaction' => array(
                                'cancelUrl' => route("callback.eblbank.cancel", ['txnId' => $txnId]),
                                'returnUrl' => route("callback.eblbank.success"),
                                'merchant' => array(
                                    'name' => 'Paysenz',
                                    'logo' => 'https://www.paysenz.com/images/logo.png'
                                ),
                                'displayControl' => array(
                                    'billingAddress' => 'HIDE',
                                    'orderSummary' => 'HIDE'
                                )
                            )
                        );
                        $response = $bank->callAndGetGatewayUrl($data, $subMerchantInfo);
                        if ($response) {
                            $bankRequest = $bank->createBankRequest(
                                    $paymentRequest, $paymentOptionRate, $response
                            );

                            return redirect()->away($response['url']);
                        } else {
                            $errmsg = 'System Error. Unble to generate CityBank OrderID';
                            echo $errmsg;
                            exit;
                        }

                        if (isset($response['url'])) {
                            return redirect()->away($response['url']);
                        } else {
                            die('EBL: Invaid configuration');
                        }

                        break;
                    case 'bkash_api':
                        return redirect()->route('process.bkashCheckout', ['txnId' => $txnId]);

                    default:
                        return "CANNOT RECOGNIZE THE BANK";
                }
            } else {
                return "REQUEST IS ALREADY PROCESSED";
            }
        } else {
            return "INVALID REQUEST";
        }
    }

    /**
     * @desc Return payment response again for successfull transaction
     */
    public function retry(Request $request) {
        // Initialize default response
        $response = array('success' => false, 'data' => null);
        $bankData = array(
            'amount' => '', 'paysenz_charge_percentage' => '', 'pay_time' => '', 'card_no' => '', 'remarks' => ''
        );

        $inputs = $request->input();
        $orderId = $inputs['order_id'];
        $clientId = $inputs['client_id'];
        $paymentRequest = PaymentRequest::where(['client_id' => $clientId, 'order_id_of_merchant' => $orderId])->first();

        // send response
        if ($paymentRequest) {
            $bankRequest = BkashBankRequest::where(['payment_request_id' => $paymentRequest->id])->first();
            if ($bankRequest) {
                $paymentOptionRate = PaymentOptionRate::find($bankRequest->payment_option_rate_id);
                $bankData = array(
                    'amount' => $paymentRequest->amount,
                    'paysenz_charge_percentage' => $paymentOptionRate ? $paymentOptionRate->paysenz_charge_percentage : '',
                    'pay_time' => $bankRequest->payment_time,
                    'card_no' => isset($bankRequest->sender) ? $bankRequest->sender : '',
                    'remarks' => $bankRequest->payment_description
                );
            }

            $dataResponse = $this->_preparePaymentResponse($paymentRequest, $bankData);
            $response['success'] = true;
            $response['data'] = $dataResponse;
        }

        return array($response);
    }

    public function dbblSuccess(Request $request) {
        return $this->_processDbblBankCallback();
    }

    public function dbblFail(Request $request) {
        return $this->_processDbblBankCallback();
    }

    public function _processDbblBankCallback() {
        $inputs = request()->input();

        $bankRequest = DutchBanglaBankRequest::where(['trans_id' => $inputs['trans_id']])->first();
        if ($bankRequest) {
            $paymentOptionRate = PaymentOptionRate::find($bankRequest->payment_option_rate_id);
            $bank = new DutchBanglaBank\Bank($paymentOptionRate->is_live);
            $confirmationData = $bank->getTransactionConfirmationData($bankRequest->trans_id);
            if ($confirmationData) {
                $responseArray = $bank->processTransactionConfirmationData($confirmationData, $bankRequest);
                if ($responseArray) {
                    $responseArray['bankData']['paysenz_charge_percentage'] = $paymentOptionRate->paysenz_charge_percentage;
                    echo "<pre>DBBL Confirmation Data: ";
                    print_r($confirmationData);
                    echo "</pre>";
                    return $this->_goToCallBackPage($responseArray['paymentRequest'], $responseArray['bankData']);
                }
            }
        }

        exit;
    }

    public function cityBankSuccess(Request $request) {
        return $this->_processCityBankCallback();
    }

    public function cityBankCancel(Request $request) {
        return $this->_processCityBankCallback();
    }

    public function cityBankFail(Request $request) {

        return $this->_processCityBankCallback();
    }

    protected function _processCityBankCallback() {
        $inputs = request()->input();


        if (!empty($inputs['xmlmsg'])) {


            $callbackData = CityBank\Bank::decodeXmlResponse($inputs['xmlmsg']);

            if ($callbackData) {

                if (isset($callbackData['SessionID'])) {

                    // For Visa and MasterCard
                    $bankRequest = CityBankRequest::where(['OrderID' => $callbackData['OrderID'], 'SessionID' => $callbackData['SessionID']])->first();
                    // var_dump($bankRequest);
                    // exit();
                } else {
                    // For Amex Card (CityBank Response does not send 'SessionID')
                    $bankRequest = CityBankRequest::where(['OrderID' => $callbackData['OrderID']])->first();
                    //   var_dump($bankRequest);
                    // exit();
                }
                //  exit();
                if ($bankRequest) {
                    //  var_dump($bankRequest);
                    //  exit();
                    $paymentOptionRate = PaymentOptionRate::find($bankRequest->payment_option_rate_id);
                    $bank = new CityBank\Bank($paymentOptionRate->is_live);
                    // Check if the Callback is valid callback from CityBank.
                    $paymentRequest = PaymentRequest::where(['id' => $bankRequest->payment_request_id])->first();
                    $client = AppClient::find($paymentRequest->client_id);
                    $merchantId = $client->user->tcb_id;
                    $transConfirmData = $bank->getTransactionConfirmationData($bankRequest->OrderID, $bankRequest->SessionID, $merchantId);

                    $callbackAmountdevided = $callbackData['PurchaseAmount'];

                    if (($callbackData['OrderStatus'] == $transConfirmData['Orderstatus']) && ($callbackAmountdevided == $transConfirmData['Amount'])) {
                        $responseArray = $bank->processTransactionConfirmationData($callbackData, $bankRequest);
                        //   var_dump($bankRequest);
                        //  var_dump($transConfirmData['Amount']);
                        //  exit();
                        if ($responseArray) {
                            $responseArray['bankData']['paysenz_charge_percentage'] = $paymentOptionRate->paysenz_charge_percentage;

                            return $this->_goToCallBackPage($responseArray['paymentRequest'], $responseArray['bankData']);
                        }
                    } else {
                        echo "Invalid CityBank Callback data.";
                        exit;
                    }
                }
            }
        }
        exit;
    }

    public function eblBankSuccess(Request $request) {
        return $this->_processEBLBankCallback();
    }

    public function eblBankCancel(Request $request) {
        $inputs = request()->input();
        $txnId = $inputs['txnId'];

        return redirect()->route('process', ['txnId' => $txnId]);
    }

    public function eblBankFail(Request $request) {

        return $this->_processEBLBankCallback();
    }

    protected function _processEBLBankCallback() {
        $inputs = request()->input();
        $resultIndicator = (isset($inputs['resultIndicator'])) ? $inputs['resultIndicator'] : "";
        $eblskypay = request()->session()->get('eblskypay');

        $result = "Payment Falied";

        if (!empty($eblskypay['successIndicator']) && ($eblskypay['successIndicator'] == $resultIndicator)) {
            $bankRequest = EblBankRequest::where(['successIndicator' => $eblskypay['successIndicator'], 'session_id' => $eblskypay['session.id']])->first();
            if ($bankRequest) {

                // remove session data
                request()->session()->forget('eblskypay');

                $paymentOptionRate = PaymentOptionRate::find($bankRequest->payment_option_rate_id);
                $bank = new EBLBank\Bank($paymentOptionRate->is_live);
                $paymentRequest = PaymentRequest::where(['id' => $bankRequest->payment_request_id])->first();
                $client = AppClient::find($paymentRequest->client_id);
                $subMerchantInfo = array(
                    'subMerchantId' => $client->user->ebl_id,
                    'subMerchantPassword' => $client->user->ebl_password
                );
                $transConfirmData = $bank->getTransactionConfirmationData($bankRequest->order_id, $bankRequest->session_id, $subMerchantInfo);
                if ($transConfirmData['paymentStatus'] == TRUE) {
                    $responseArray = $bank->processTransactionConfirmationData($transConfirmData['responseData'], $bankRequest);
                    if ($responseArray) {
                        $responseArray['bankData']['paysenz_charge_percentage'] = $paymentOptionRate->paysenz_charge_percentage;
                        return $this->_goToCallBackPage($responseArray['paymentRequest'], $responseArray['bankData']);
                    }
                } else {
                    echo "Invalid EBLBank Callback data.";
                    exit;
                }
            } else {
                die('EBL: Bank data not found.');
            }
        }

        exit;
    }

    public function bkashForm($txnId) {
        if (!empty($txnId)) {
            $paymentRequest = PaymentRequest::where(['txnid' => $txnId])->first();
            if ($paymentRequest && $paymentRequest->isReadyForPayment()) {
                // Check for existing bKash bank record, if not found create record
                $bankRequest = BkashBankRequest::where(['payment_request_id' => $paymentRequest->id])->first();
                if (!$bankRequest) {
                    foreach (AppClient::find($paymentRequest->client_id)->paymentOptionRates as $paymentOptionRate) {
                        if ($paymentOptionRate->paymentOption->bank->short_code == 'bkash') {
                            BkashBankRequest::create([
                                'payment_request_id' => $paymentRequest->id,
                                'payment_option_rate_id' => $paymentOptionRate->id,
                                'request_state' => 'Redirected to Bkash Form'
                            ]);
                            break;
                        }
                    }
                } // End bKash bank record.
                return view('processRequest.bkashForm')->with(array('paymentRequest' => $paymentRequest));
            } else {
                return "PAYMENT IS ALREADY PROCESSED FOR THIS REQUEST";
            }
        }
    }

    public function bkashFormSubmit($txnId, Request $request) {
        $inputs = request()->input();
        $trxidBkash = trim($inputs['trxid']);

        if (!empty($txnId)) {
            if (empty($trxidBkash)) {
                return "Bkash TrxID is empty. Please enter a valid Bkash TrxID";
            }
            $paymentRequest = PaymentRequest::where(['txnid' => $txnId])->first();

            if ($paymentRequest) {
                // Check if the bKash trxId is already used before.
                $bkashBankRequestUsed = BkashBankRequest::where('trxId', '=', $trxidBkash)
                        ->where('payment_request_id', '!=', $paymentRequest->id)
                        ->first();
                if ($bkashBankRequestUsed === null) {
                    // Get the details of the user provided `trxid`
                    $bankRequest = BkashBankRequest::where(['payment_request_id' => $paymentRequest->id])->first();
                    if ($bankRequest) {
                        $paymentOptionRate = PaymentOptionRate::find($bankRequest->payment_option_rate_id);
                        $bank = new BkashBank\Bank($paymentOptionRate->is_live);
                        $bkashResponse = $bank->validateByTraxId($trxidBkash, $bankRequest->amount);
                        if ($bkashResponse && $bkashResponse['transaction']) {
                            $responseArray = $bank->processTransactionConfirmationData($bkashResponse, $bankRequest);
                            if ($responseArray) {
                                $responseArray['bankData']['paysenz_charge_percentage'] = $paymentOptionRate->paysenz_charge_percentage;
                                if ($bankRequest->status == 1) {
                                    return $this->_goToCallBackPage($responseArray['paymentRequest'], $responseArray['bankData']);
                                } else {
                                    return redirect()->route('process.bkash', ['txnId' => $txnId])->with('status', $bankRequest->payment_description);
                                }
                            }
                        }
                    }
                } else {
                    $status = '';
                    if ($bkashBankRequestUsed->status == 1) {
                        $bankRequest = BkashBankRequest::where(['payment_request_id' => $paymentRequest->id])->first();
                        if ($bankRequest) {
                            $paymentOptionRate = PaymentOptionRate::find($bankRequest->payment_option_rate_id);
                            $bank = new BkashBank\Bank($paymentOptionRate->is_live);
                            $bkash = $bank->validateResponse(4001);
                            if ($bkash)
                                $status = $bkash->message . '. ' . $bkash->interpretation;
                        }
                    }
                    return redirect()->route('process.bkash', ['txnId' => $txnId])->with('status', $status);
                    //return "THIS `trxId` Is already used before";
                }
            }
        }
    }
    public function surecashForm($txnId) {
        if (!empty($txnId)) {
            $paymentRequest = PaymentRequest::where(['txnid' => $txnId])->first();
			return view('processRequest.surecashForm')->with(array('paymentRequest' => $paymentRequest));							
        }
		else {
			return "PAYMENT REQUEST INVALID";
		}
    }

    public function surecashFormSubmit($txnId, Request $request) {
        $inputs = request()->input();
        $customersurecashacount = trim($inputs['trxid']);

        if (!empty($txnId)) {
            if (empty($customersurecashacount)) {
                return "Account is empty. Please enter a valid Surecash Account";
            }
			
            $paymentRequest = PaymentRequest::where(['txnid' => $txnId])->first();
			$processId=date("ymd") . date("Hisu");
			$bankRequest = surecashBankRequest::where(['processId' => $processId])->first();
			if (!$bankRequest) {
				foreach (AppClient::find($paymentRequest->client_id)->paymentOptionRates as $paymentOptionRate) {
					if ($paymentOptionRate->paymentOption->bank->short_code == 'surecash') {
						surecashBankRequest::create([
							'payment_request_id' => $paymentRequest->id,
							'payment_option_rate_id' => $paymentOptionRate->id,
							'processId' => $processId,
							'surecashAccountNo'=>$customersurecashacount,
							'RequestAmount'=> $paymentRequest->amount,
							'request_state' => 'Redirected to SureCash Bank for check'
						]);
						break;
					}
				}
				$bkashBankRequestUsed = surecashBankRequest::where('payment_request_id', '=', $paymentRequest->id)
                        ->where('processId', '!=', $processId)
                        ->first();
                if ($bkashBankRequestUsed === null) {
                    // Get the details of the user provided `trxid`
                    $bankRequest = surecashBankRequest::where(['processId' => $processId])->first();
                    if ($bankRequest) {
                        $paymentOptionRate = PaymentOptionRate::find($bankRequest->payment_option_rate_id);
                        $bank = new surecash\Bank($paymentOptionRate->is_live);
                        $bkashResponse = $bank->validateByTraxId($processId, $bankRequest->RequestAmount, $customersurecashacount,$bankRequest->id,$bankRequest->payment_request_id);
                        if ($bkashResponse) {
                            $responseArray = $bank->processTransactionConfirmationData($bkashResponse, $bankRequest);
                            if ($responseArray) {
                                $responseArray['bankData']['paysenz_charge_percentage'] = $paymentOptionRate->paysenz_charge_percentage;
                                if ($bankRequest->status == 1) {
                                    return $this->_goToCallBackPage($responseArray['paymentRequest'], $responseArray['bankData']);
                                } else {
                                    return redirect()->route('process.bkash', ['txnId' => $txnId])->with('status', $bankRequest->payment_description);
                                }
                            }
                        }
                    }
                } else {
                    $status = '';
                    if ($bkashBankRequestUsed->status == 1) {
                        $bankRequest = BkashBankRequest::where(['payment_request_id' => $paymentRequest->id])->first();
                        if ($bankRequest) {
                            $paymentOptionRate = PaymentOptionRate::find($bankRequest->payment_option_rate_id);
                            $bank = new BkashBank\Bank($paymentOptionRate->is_live);
                            $bkash = $bank->validateResponse(4001);
                            if ($bkash)
                                $status = $bkash->message . '. ' . $bkash->interpretation;
                        }
                    }
                    return redirect()->route('process.bkash', ['txnId' => $txnId])->with('status', $status);
                }
			}
			else {
                // Check if the payment_request_id  is already used before.
            }
        }
    }

    public function testCityBankSocket() {
        $MerchantID = config('Banks.CityBank.Bank.merchantId_sandbox');
        $amount = 5;
        $currency = 'BDT';

        // Create Xml order to describe the order parameters:
        $data = '<?xml version="1.0" encoding="UTF-8"?>';
        $data .= "<TKKPG>";
        $data .= "<Request>";
        $data .= "<Operation>CreateOrder</Operation>";
        $data .= "<Language>EN</Language>";
        $data .= "<Order>";
        $data .= "<OrderType>Purchase</OrderType>";
        $data .= "<Merchant>" . $MerchantID . "</Merchant>";
        $data .= "<Amount>" . $amount * 100 . "</Amount>";
        $data .= "<Currency>" . $currency . "</Currency>";
        $data .= "<Description>Test ORder</Description>";
        $data .= "<ApproveURL>http://paysenz.com/callback/citybank/success</ApproveURL>";
        $data .= "<CancelURL>http://paysenz.com/callback/citybank/cancel</CancelURL>";
        $data .= "<DeclineURL>http://paysenz.com/callback/citybank/failed</DeclineURL>";
        $data .= "</Order></Request></TKKPG>";


        // Information on the result of the order creation in the Response object
        // Examples of obtaining required fields:
        $bank = new CityBank\Bank(0);
        $xml = $bank->postQW($data);
        $OrderID = $xml->Response->Order->OrderID;
        $SessionID = $xml->Response->Order->SessionID;
        $URL = $xml->Response->Order->URL;
        echo "<br />OrderID: {$OrderID}";
        echo "<br />SessionID: {$SessionID}";
        exit;
        // Request for payment page
        if ($OrderID != "" and $SessionID != "") {
            //Update existing Order XML by Create Order Status
            $xml = new DOMDocument('1.0', 'utf-8');
            $xml->formatOutput = true;
            $xml->preserveWhiteSpace = false;
            $xml->load('Order.xml');
            //Get item element
            $element = $xml->getElementsByTagName('Order')->item(0);
            //Load child elements
            $oID = $element->getElementsByTagName('OrderID')->item(0);
            $sID = $element->getElementsByTagName('SessionID')->item(0);
            $PurchaseAmount = $element->getElementsByTagName('PurchaseAmount')->item(0);
            $Currency = $element->getElementsByTagName('Currency')->item(0);
            $Description = $element->getElementsByTagName('Description')->item(0);
            $PAN = $element->getElementsByTagName('PAN')->item(0);
            $oStatus = $element->getElementsByTagName('Status')->item(0);
            //Replace old elements with new
            $element->replaceChild($oID, $oID);
            $element->replaceChild($sID, $sID);
            $element->replaceChild($PurchaseAmount, $PurchaseAmount);
            $element->replaceChild($Currency, $Currency);
            $element->replaceChild($Description, $Description);
            $element->replaceChild($PAN, $PAN);
            $element->replaceChild($oStatus, $oStatus);
            //Assign elements with new value
            $oID->nodeValue = $OrderID;
            $sID->nodeValue = $SessionID;
            $PurchaseAmount->nodeValue = $_POST['Amount'] * 100;
            $Currency->nodeValue = $_POST['Currency'];
            $Description->nodeValue = $_POST['Description'];
            $PAN->nodeValue = '';
            $oStatus->nodeValue = 'Created';
            $xml->save('Order.xml');
            // Add codes for saving the Order ID and Session ID in Merchant DB for future uses.
            header("Location: " . $URL . "?ORDERID=" . $OrderID . "&SESSIONID=" . $SessionID . "");
            exit();
        }
    }

    public function _preparePaymentResponse($paymentRequest, $bankData) {
        // get payment_type info
        $payment_type = '';
        if (!empty($paymentRequest->paymentOptionRate) && !empty($paymentRequest->paymentOptionRate->paymentOption) && !empty($paymentRequest->paymentOptionRate->paymentOption->bank)) {
            $payment_type = $paymentRequest->paymentOptionRate->paymentOption->bank->name . ': ' . $paymentRequest->paymentOptionRate->paymentOption->name;
        }

        // Make callback request to Marchent URL
        $data = [
            'payment_status' => $paymentRequest->status,
            'amount' => (float) $bankData['amount'],
            'store_amount' => (float) $paymentRequest->amount, // REMOVE IT (DUPLICATE)
            'psz_fee' => $bankData['paysenz_charge_percentage'] ? $bankData['paysenz_charge_percentage'] : 0,
            'psz_txnid' => $paymentRequest->txnid,
            'mer_txnid' => $paymentRequest->order_id_of_merchant, // REMOVE IT (DUPLICATE)
            'merchant_amount' => (float) $paymentRequest->amount,
            'merchant_amount_deducted' => (float) ($paymentRequest->amount - $paymentRequest->store_service_charge),
            'merchant_currency' => $paymentRequest->currency_of_transaction,
            'merchant_client_id' => $paymentRequest->client_id,
            'merchant_txnid' => $paymentRequest->order_id_of_merchant,
            'payment_time' => $bankData['pay_time'],
            'remarks' => $bankData['remarks'],
            'custom_1' => $paymentRequest->custom_1,
            'custom_2' => $paymentRequest->custom_2,
            'custom_3' => $paymentRequest->custom_3,
            'custom_4' => $paymentRequest->custom_4,
            'payment_type' => $payment_type,
            'card_no' => isset($bankData['card_no']) ? $bankData['card_no'] : null,
        ];

        return $data;
    }

    public function _goToCallBackPage($paymentRequest, $bankData) {
        // Get response array
        $data = $this->_preparePaymentResponse($paymentRequest, $bankData);

        // check if valid callback url is set
        if (empty($paymentRequest->callback_success_url) && $paymentRequest->callback_fail_url) {
            echo "<pre>";
            print_r('No callback URL found');
            echo "</pre>";
            echo "<pre>Payment Information: ";
            print_r($data);
            echo "</pre>";
            exit;
        }

        // Send notification emails
        if ($paymentRequest->status == PaymentRequest::STATUS_SUCCESS) {
            $this->sendInvoiceEmail($paymentRequest);
        }

        $url = $paymentRequest->status == 'Successful' ? $paymentRequest->callback_success_url : $paymentRequest->callback_fail_url;
        return view('processRequest.callbackPageForm')->with(array('data' => $data, 'url' => $url));
    }

    /**
     * @desc Send notification Invoice email to Admin, Client
     * and Customer for successful Payment.
     * @param $paymentRequest
     */
    public function sendInvoiceEmail($paymentRequest) {
        try {
            if ($paymentRequest) {
                $toArray = array();
                $adminEmailsString = env('MAIL_ADMIN_INVOICE');
                $adminEmails = explode(',', $adminEmailsString);
                $toArray = array_merge($toArray, $adminEmails);

                // Send email to merchant when flag is set to TRUE
                if ($paymentRequest->appClient->user->invoice_email == 1) {
                    $toArray[] = $paymentRequest->appClient->user ? $paymentRequest->appClient->user->email : '';
                }
                $toArray[] = $paymentRequest->buyer_email;

                $data = array();
                $data['invoice'] = $paymentRequest->getPaymentEmailDetails();
                $data['invoice_no'] = $paymentRequest->txnid;
                $data['message'] = $paymentRequest->status . ' Transaction Notification';
                $data['subject'] = '(' . $paymentRequest->appClient->name . ') ' . $paymentRequest->status . ' Transaction Notification: ' . $data['invoice_no'];

                // Set invoice email attachment PDF for successful Transaction
                if ($paymentRequest->status == PaymentRequest::STATUS_SUCCESS) {
                    $attachmentPdf = $this->generateInvoicePDF($paymentRequest);
                    $data['attachment'] = array(
                        'path' => $attachmentPdf,
                        'display_name' => 'invoice#' . $paymentRequest->txnid . '.pdf',
                        'mime' => 'application/pdf',
                    );
                }
                foreach ($toArray as $toEmail) {
                    if (!empty($toEmail)) {
                        Mail::to($toEmail)->send(new InvoiceEmail($data));
                    }
                }
            }
        } catch (Exception $e) {
            echo "Email Error: " . $e->getMessage();
        }
    }
    /**
     * @desc Generates Invoice PDF
     * @param $paymentRequest
     * @return string
     */
    public function generateInvoicePDF($paymentRequest) {
        $data = array();
        $data['invoice'] = $paymentRequest->getPaymentEmailDetails();
        $data['invoice_no'] = $paymentRequest->txnid;

        $path = public_path(PaymentRequest::DIR_INVOICE_PDF);
        $pdf_name = $paymentRequest->txnid . '.pdf';
        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
                ->loadView('pdfs.invoice', array('data' => $data, 'paymentRequest' => $paymentRequest));
        $pdf->save($path . $pdf_name);

        return $path . $pdf_name;
    }

}
