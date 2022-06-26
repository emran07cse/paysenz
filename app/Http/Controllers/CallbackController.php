<?php

namespace App\Http\Controllers;

use App\Banks\CityBank;
use App\CityBankRequest;
use App\PaymentRequest;
use Illuminate\Http\Request;

class CallbackController extends Controller
{
    public function index($bid, $bank_request_id){
        $inputs = request()->input();
        switch (strtoupper($bid)){
            case "TCB":
                $cityBankRequest = CityBankRequest::find($bank_request_id);
                $paymentRequest = PaymentRequest::find($cityBankRequest->payment_request_id);
                if($paymentRequest->status == "Successful") return "PAYMENT ALREADY SUCCESSFUL";

                $cityBankRequest->OrderStatusScr = $inputs['OrderStatusScr'];
                $cityBankRequest->ResponseCode = $inputs['ResponseCode'];
                $cityBankRequest->PAN = $inputs['PAN'];
                $cityBankRequest->PurchaseAmountScr = $inputs['PurchaseAmountScr'];
                $cityBankRequest->OrderDescription = $inputs['OrderDescription'];
                $cityBankRequest->Name = $inputs['Name'];
                $cityBankRequest->request_state = 'Finished but Unconfirmed';
                $cityBankRequest->save();

                $bank = new CityBank\Bank();
                $confirmedData = $bank->getTransactionConfirmationData($paymentRequest->appClient->user->tcb_id, $cityBankRequest->OrderID, $cityBankRequest->SessionID);

                if($confirmedData['PAN'] == $cityBankRequest->PAN && $confirmedData['Orderstatus'] == $cityBankRequest->OrderStatusScr){
                    $cityBankRequest->request_state = 'Finished and Confirmed';

                    if($cityBankRequest->OrderStatusScr == 'APPROVED' && $cityBankRequest->ResponseCode == '001'){
                        $paymentRequest->status = 'Successful';
                    }else{
                        if($cityBankRequest->OrderStatusScr == 'CANCELED')
                            $paymentRequest->status = 'Canceled';
                        else
                            $paymentRequest->status = 'Failed';
                    }
                    $paymentRequest->save();
                    $cityBankRequest->save();

                    $url = $paymentRequest->callback_url . (str_contains($paymentRequest->callback_url, "?") ? "&request_id=" . $paymentRequest->id : "?request_id=" . $paymentRequest->id);
                    return redirect()->away($url);
                }
                break;
        }
        return "BANK NOT RECOGNIZED";
    }
}
