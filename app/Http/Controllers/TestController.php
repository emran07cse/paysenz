<?php

namespace App\Http\Controllers;

use App\AppClient;
use Illuminate\Http\Request;
use App\Mail\InvoiceEmail;

use App\PaymentRequest;
use PDF;

class TestController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function testEmail(){
        $id = 948; //Bkash
        //$id = 773; // DBBL
        $paymentRequest = PaymentRequest::find($id);

        $toArray = array();
        $adminEmailsString = env('MAIL_ADMIN_INVOICE');
        $adminEmails = explode(',' , $adminEmailsString);
        $toArray = array_merge($toArray, $adminEmails);
        
        // Send email to merchant when flag is set to TRUE
        if($paymentRequest->appClient->user->invoice_email == 1){
            $toArray[] = $paymentRequest->appClient->user ? $paymentRequest->appClient->user->email : '';    
        }
        
        $toArray[] = $paymentRequest->appClient->user ? $paymentRequest->appClient->user->email : '';
        $toArray[] = $paymentRequest->buyer_email;

        $data = array();
        $data['invoice'] = $paymentRequest->getPaymentEmailDetails();
        $data['invoice_no'] = $paymentRequest->txnid;

        return view('pdfs.invoice')->with(array('data' => $data, 'paymentRequest' => $paymentRequest));

        $attachmentPdf = $this->generateInvoicePDF($paymentRequest);
        //return view('pdfs.invoice')->with(array('data' => $data, 'paymentRequest' => $paymentRequest));

        $data['message'] = $paymentRequest->status . ' Transaction Notification';
        $data['subject'] = $paymentRequest->status . ' Transaction Notification: ' . $data['invoice_no'];

        $data['attachment'] = array(
            'path' => $attachmentPdf,
            'display_name' => 'invoice#' .$paymentRequest->txnid.'.pdf',
            'mime' => 'application/pdf',
        );

        \Mail::to('bappybd@gmail.com')->send(new InvoiceEmail($data));

        /*foreach($toArray as $toEmail) {
            if(!empty($toEmail)) {
                \Mail::to($toEmail)->send( new InvoiceEmail($data));
            }
        }*/
    }

    public function generateInvoicePDF($paymentRequest){
        $data = array();
        $data['invoice'] = $paymentRequest->getPaymentEmailDetails();
        $data['invoice_no'] = $paymentRequest->txnid;

        $path = public_path(PaymentRequest::DIR_INVOICE_PDF);
        $pdf_name = $paymentRequest->txnid.'.pdf';
        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
                ->loadView('pdfs.invoice', array('data' => $data, 'paymentRequest' => $paymentRequest));
        $pdf->save($path.$pdf_name);

        return $path.$pdf_name;
    }
}
