<?php

namespace App\Http\Controllers;

use App\AppClient;
use App\CityBankRequest;
use App\PaymentRequest;
use App\PaymentOption;
use Illuminate\Http\Request;

use PDF;

class HomeController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function appClients(){
        return view('appClients', [ 'clients' => auth()->user()->isAdmin() ? AppClient::paginate(50) : [] ]);
    }

    public function updateAppClients(){
        if(!auth()->user()->isAdmin()) return "NOT ALLOWED";
        $client = AppClient::find(request()->get('id'));
        $client->password_client = !$client->password_client;
        $client->save();
        return redirect()->route('app.clients')->with('status',"Successfully Updated the Status.");
    }

    public function transactions(){

        $filters = request()->input() ? request()->input() : null;
        if(auth()->user()->isAdmin()){
            $transactions = PaymentRequest::with('appClient', 'paymentOptionRate', 'paymentBkash', 'paymentCityBank', 'paymentDbblBank', 'paymentEblBank');
        }else{
            $client_ids = AppClient::select('id')->where('user_id', auth()->user()->id)->get()->map(function ($item, $key) {
                return $item->id;
            })->toArray();
            $transactions = PaymentRequest::with('appClient', 'paymentOptionRate', 'paymentBkash', 'paymentCityBank', 'paymentDbblBank', 'paymentEblBank')->whereIn('client_id', $client_ids);

        }

        //Add filter Conditions

        if($filters){
            // Filter by Keyword
            if(isset($filters['keyword']) && !is_null($filters['keyword'])) {
                $transactions->where('txnid', 'like', '%'.$filters['keyword'].'%')
                    ->orWhere('order_id_of_merchant', 'like', '%'.$filters['keyword'].'%')
                    ->orWhere('amount', 'like', '%'.$filters['keyword'].'%')

                    // Buyer information
                    ->orWhere('buyer_name', 'like', '%'.$filters['keyword'].'%')
                    ->orWhere('buyer_email', 'like', '%'.$filters['keyword'].'%')
                    ->orWhere('buyer_address', 'like', '%'.$filters['keyword'].'%')
                    ->orWhere('buyer_contact_number', 'like', '%'.$filters['keyword'].'%')
                    ->orWhere('description', 'like', '%'.$filters['keyword'].'%')

                    // Custom client fields
                    ->orWhere('custom_1', 'like', '%'.$filters['keyword'].'%')
                    ->orWhere('custom_2', 'like', '%'.$filters['keyword'].'%')
                    ->orWhere('custom_3', 'like', '%'.$filters['keyword'].'%')
                    ->orWhere('custom_4', 'like', '%'.$filters['keyword'].'%')

                    // Search card_no in bank tables
                    ->orwhereHas("paymentBkash", function($q) use ($filters) {
                        $q->where("card_no", 'like', '%'.$filters['keyword'].'%');
                    })
                    ->orwhereHas("paymentCityBank", function($q) use ($filters) {
                        $q->where("card_no", 'like', '%'.$filters['keyword'].'%');
                    })
                    ->orwhereHas("paymentDbblBank", function($q) use ($filters) {
                        $q->where("card_no", 'like', '%'.$filters['keyword'].'%');
                    })
                    ->orwhereHas("paymentEblBank", function($q) use ($filters) {
                        $q->where("card_no", 'like', '%'.$filters['keyword'].'%');
                    })
                ;
            }
            // Filter by Client
            if(isset($filters['client_id']) && !is_null($filters['client_id'])) {
                $transactions->where('client_id','=',$filters['client_id']);
            }

            // Filter by Payment Type
            if(isset($filters['payment_option_id']) && !is_null($filters['payment_option_id'])) {
                $transactions->whereHas("paymentOptionRate", function($q) use ($filters) {
                    $q->where("payment_option_id", $filters['payment_option_id']);
                });
            }

            // Filter by Status
            if(isset($filters['status']) && !is_null($filters['status'])) {
                $transactions->where('status','=',$filters['status']);
            }

            // Filter by Date
            if(isset($filters['date_from']) && !is_null($filters['date_from']) && !isset($filters['date_to'])) {
                $fromDate = $filters['date_from'];
                $toDate   = $filters['date_from'];
                $transactions->whereRaw("created_at >= ? AND created_at <= ?",
                    array($fromDate." 00:00:00", $toDate." 23:59:59")
                );
            } else if(isset($filters['date_from']) && !is_null($filters['date_from']) && isset($filters['date_to']) && !is_null($filters['date_to']) ) {
                $fromDate = $filters['date_from'];
                $toDate   = $filters['date_to'];
                $transactions->whereRaw("created_at >= ? AND created_at <= ?",
                    array($fromDate." 00:00:00", $toDate." 23:59:59")
                );
            }
        }
        
        // Sort
        $transactions->orderByDesc('id');

        return view('transactions', [
            'transactions' => $transactions->paginate(20),
            'filters' => $filters,
            'clients' => AppClient::all(),
            'paymentOptions' => $paymentOptions = PaymentOption::leftJoin('banks', 'banks.id', '=', 'payment_options.bank_id')
                ->select('payment_options.*')
                ->orderBy('banks.short_code')
                ->get(),
            'statuses' => PaymentRequest::getStatusOptions()
        ]);
    }

    public function merchantRequestDetails(){
        return view('merchantRequestDetails', [ 'paymentRequest' => PaymentRequest::find(request()->get('id')) ]);
    }

    public function bankRequests(){
        $id = request()->get('id');
        $bankRequests = CityBankRequest::where('payment_request_id',$id)->paginate(20);
        return view('bankRequests', [ 'bankRequests' => $bankRequests]);
    }

    public function editTransactions(){
        if(auth()->user()->isAdmin()){
            $id = request()->get('id');
            $transaction = PaymentRequest::find($id);
            if($transaction){
                $paymentOptions = PaymentRequest::getStatusOptions();

                return view('transactions.edit', [ 'transaction' => $transaction, 'paymentOptions' => $paymentOptions]);
            }

        } else{
            die('Invalid permission to perform this action.');
        }
    }

    public function updateTransactions(){
        if(auth()->user()->isAdmin()) {
            $data = request()->input();

            $transaction = PaymentRequest::find($data['id']);
            if($transaction) {
                $transaction->status = $data['status'];
                $transaction->save();

                return redirect()->route('transactions')->with('status',"Success: Transaction successfully Updated.");
            }
        } else{
            die('Invalid permission to perform this action.');
        }
    }

    public function getTransactionsPDF(PaymentRequest $id)
    {
        $data = array();
        $data['invoice'] = $id->getPaymentEmailDetails();
        $data['invoice_no'] = $id->txnid;

        $path = public_path(PaymentRequest::DIR_INVOICE_PDF);
        $pdf_name = $id->txnid.'.pdf';
        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
            ->loadView('pdfs.invoice', array('data' => $data, 'paymentRequest' => $id));
//        $pdf->save($path.$pdf_name);

        return $pdf->stream($path.$pdf_name);
//        return $TxnID;

    }
}
