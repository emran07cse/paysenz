<?php

namespace App\Http\Controllers;

use App\GatewayInfo;
use App\MerchantRequest;
use App\StatusCodeInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\BankRequest;
use App\Rate;
use App\Store;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class TransactionViewController extends Controller
{
    protected $merchantRequests;
    protected $bankRequests;
    protected $stores;
    protected $rates;

    public function __construct(MerchantRequest $merchantRequests, BankRequest $bankRequests , Store $stores, Rate $rates)
    {
        $this->requestStartTime = microtime(true);
        $this->bankRequests = $bankRequests;
        $this->stores = $stores;
        $this->rates = $rates;
    }

    protected $inputKeyToDBFieldNameAndComparisonOperatorMaps = [
        'storeId' => [ 'fieldName' => 'm_transaction.store_id', 'operator' => '=' ],
        'transactionId' => [ 'fieldName' => 'm_transaction.lid', 'operator' => '=' ],
        'orderId' => [ 'fieldName' => 'm_transaction.mid', 'operator' => '=' ],
        'status' => [ 'fieldName' => 'm_transaction.pay_status', 'operator' => '=' ],
        'max' => [ 'fieldName' => 'm_transaction.amount_bdt', 'operator' => '<=' ],
        'min' => [ 'fieldName' => 'm_transaction.amount_bdt', 'operator' => '>=' ],
        'email' => [ 'fieldName' => 'm_transaction.invoice_email', 'operator' => '=' ],
        'phone' => [ 'fieldName' => 'm_transaction.cus_phone', 'operator' => '=' ],
        'startDate' => [ 'fieldName' => 'm_transaction.t_date', 'operator' => '>=' ],
        'endDate' => [ 'fieldName' => 'm_transaction.t_date', 'operator' => '<=' ],
        'docReceived' => [ 'fieldName' => 'm_transaction.doc_recived', 'operator' => '=' ],
        'cardCountry' => [ 'fieldName' => 'm_transaction.bin_country', 'operator' => '=' ],
        'riskStatus' => [ 'fieldName' => 'm_transaction.risk_level', 'operator' => '=' ],
        'currency' => [ 'fieldName' => 'm_transaction.currency_merchant', 'operator' => '=' ],
        'bankId' => [ 'fieldName' => 't_transaction.bid', 'operator' => '=' ],
    ];

    public function index()
    {
        $this->merchantRequests = DB::table('m_transaction')
            ->join('t_transaction', 'm_transaction.lid','=','t_transaction.lid');
        $inputs = request()->input();

        $this->addFilters($inputs);

        $time1 = microtime(true);
        $entries = $this->merchantRequests
            ->select(
                'm_transaction.Id',
                'm_transaction.store_id',
                'm_transaction.lid',
                'm_transaction.t_date',
                'm_transaction.mid',
                'm_transaction.invoice_to',
                't_transaction.card_holder',
                'm_transaction.invoice_email',
                'm_transaction.cus_phone',
                'm_transaction.cus_country',
                'm_transaction.ip',
                'm_transaction.ip_country',
                't_transaction.card_number',
                'm_transaction.bin_cardtype',
                'm_transaction.bin_cardcategory',
                'm_transaction.bin_country',
                'm_transaction.service_type',
                'm_transaction.doc_recived',
                'm_transaction.verify_status',
                'm_transaction.report_id',
                'm_transaction.amount_bdt',
                'm_transaction.currency',
                'm_transaction.amount_currency',
                'm_transaction.currency_merchant',
                'm_transaction.convertion_rate',
                'm_transaction.pay_status',
                't_transaction.bid',
                't_transaction.back_dbbl_id',
                't_transaction.bank_uid',
                't_transaction.bank_txn',
                'm_transaction.ipn_alert',
                'm_transaction.final_reason')
            ->orderByDesc('m_transaction.Id')->take(10)->get();
            //->paginate(request()->get('itemsPerPage') ?? 10);
        $time2 = microtime(true);
        //$paginatedEntries = new Paginator($entries->get(),10,1, [ 'path' => route('transactions') ]);
        //dd($paginatedEntries);

        $this->merchantRequests = DB::table('m_transaction')
            ->join('t_transaction', 'm_transaction.lid','=','t_transaction.lid');
        $this->addFilters($inputs);

        //dd(DB::table('m_transaction')->where('t_date','>=', '2017-09-12')->count('Id'), $time2-$time1, microtime(true)-$time2);

        $report = $this->merchantRequests
            ->where('t_transaction.pay_status','=', 2)
            ->select('m_transaction.pay_status' . ' as pivot',
                DB::raw('count(t_transaction.Id) as totalCount'),
                DB::raw('sum(t_transaction.pay_amount) as totalAmount'),
                DB::raw('sum(m_transaction.rec_amount) as totalReceivableAmount'),
                DB::raw('sum(m_transaction.charge) as totalCharge'),
                DB::raw('sum(bank_charge) as totalBankCharge'))
            ->groupBy('m_transaction.pay_status')
            ->get()->first();
        $time3 = microtime(true);

        //dd($entries, $report, $time2-$time1, $time3-$time2);



        $paginatedEntries = new LengthAwarePaginator($entries,$report->totalCount,10,request()->has('page') ? request()->get('page') : 1, [ 'path' => route('transactions') ]);

        //dd($paginatedEntries,($time2 - $time1), ($time3 - $time2));

        return view('transactions.list',
            [
                'requestStartTime' => $this->requestStartTime,
                'requests' => $paginatedEntries,
                'totalSuccessful' => empty($report) ? 0 : $report->totalCount,//$totalSuccessful,
                'totalAmount' => empty($report) ? 0 : $report->totalAmount,//fn($totalAmount),
                'totalReceivableAmount' => empty($report) ? 0 : $report->totalReceivableAmount,//fn($totalReceivableAmount),
                'totalServiceCharge' => empty($report) ? 0 : $report->totalCharge,//fn($totalAmount - $totalReceivableAmount),
                'totalBankCharges' => empty($report) ? 0 : $report->totalBankCharge,//fn($totalBankCharges),
                'stores' => cache()->remember('stores', 1440, function() {
                    $items = DB::select('SELECT storeid, title FROM `m_store` as s
                                      WHERE s.storeid in (SELECT DISTINCT store_id FROM `m_transaction`) ORDER BY title');

                    return collect($items)->mapWithKeys(function ($item){
                        return [
                            $item->storeid => $item->title
                        ];
                    })->toArray();
                }),
            ]);
    }

    public function ajaxIndex()
    {
        return view(
            'transactions.ajaxIndex',
            [
                'stores' => cache()->remember('stores', 1440, function() {
                    $items = DB::select('SELECT storeid, title FROM `m_store` as s
                                      WHERE s.storeid in (SELECT DISTINCT store_id FROM `m_transaction`) ORDER BY title');

                    return collect($items)->mapWithKeys(function ($item){
                        return [
                            $item->storeid => $item->title
                        ];
                    })->toArray();
                }),
            ]);
    }

    public function list()
    {
        $this->merchantRequests = DB::table('m_transaction')
            ->join('t_transaction', 'm_transaction.lid','=','t_transaction.lid');
        $inputs = request()->input();

        $this->addFilters($inputs);

        return $this->merchantRequests
            ->select(
                'm_transaction.Id',
                'm_transaction.store_id',
                'm_transaction.lid',
                'm_transaction.t_date',
                'm_transaction.mid',
                'm_transaction.invoice_to',
                't_transaction.card_holder',
                'm_transaction.invoice_email',
                'm_transaction.cus_phone',
                'm_transaction.cus_country',
                'm_transaction.ip',
                'm_transaction.ip_country',
                't_transaction.card_number',
                'm_transaction.bin_cardtype',
                'm_transaction.bin_cardcategory',
                'm_transaction.bin_country',
                'm_transaction.service_type',
                'm_transaction.doc_recived',
                'm_transaction.verify_status',
                'm_transaction.report_id',
                'm_transaction.amount_bdt',
                'm_transaction.currency',
                'm_transaction.amount_currency',
                'm_transaction.currency_merchant',
                'm_transaction.convertion_rate',
                'm_transaction.pay_status',
                't_transaction.bid',
                't_transaction.back_dbbl_id',
                't_transaction.bank_uid',
                't_transaction.bank_txn',
                'm_transaction.ipn_alert',
                'm_transaction.final_reason')
            ->orderByDesc('m_transaction.Id')
            ->forPage(
                request()->has('page') ? request()->get('page') : 1,
                request()->has('perPage') ? request()->get('perPage') : 10
            )
            ->get();
    }

    /**
     * @return array
     */
    public function reports()
    {
        $inputs = request()->input();
        $this->merchantRequests = DB::table('m_transaction')
            ->join('t_transaction', 'm_transaction.lid','=','t_transaction.lid');

        $this->addFilters($inputs);

        $collection = $this->merchantRequests
            ->select('m_transaction.pay_status' . ' as pivot',
                DB::raw('count(t_transaction.Id) as totalCount'),
                DB::raw('sum(t_transaction.pay_amount) as totalAmount'),
                DB::raw('sum(m_transaction.rec_amount) as totalReceivableAmount'),
                DB::raw('sum(m_transaction.charge) as totalCharge'),
                DB::raw('sum(bank_charge) as totalBankCharge'))
            ->groupBy('m_transaction.pay_status')
            ->get();

        $array = (array) $collection->where('pivot', 'Successful')->first();
        $array['cumulativeCount'] = $collection->sum('totalCount');
        return $array;
    }

    public function storeOptions(){
        return cache()->remember('stores', 1440, function() {
            $items = DB::select('SELECT storeid as "value", title as "text" FROM `m_store` as s
                                      WHERE s.storeid in (SELECT DISTINCT store_id FROM `m_transaction`) ORDER BY title');

            return $items;
        });
    }

    private function addFilters($inputs){
        foreach($inputs as $inputKey => $value){
            if(!empty($value))
                $this->addFilterOnRequestsByInputKey($inputKey, $value);
        }

        if(auth()->user()->isMerchant() || auth()->user()->isStoreManager()){
            $store = auth()->user()->store();
            if(empty($store)) return "NO STORE FOUND FOR YOU";

            $this->addFilterOnRequestsByDBFieldName('store_id', $store->storeid, '=');
        }
    }

    private function addFilterOnRequestsByInputKey($inputKey, $value){
        if(!empty(trim($value)) && array_key_exists($inputKey, $this->inputKeyToDBFieldNameAndComparisonOperatorMaps)){
            $fieldAndOperator = $this->inputKeyToDBFieldNameAndComparisonOperatorMaps[$inputKey];

            switch ($fieldAndOperator['fieldName']){
                case 'm_transaction.store_id':
                    if(auth()->user()->isAdmin() || auth()->user()->isManager()){
                        $this->addFilterOnRequestsByDBFieldName($fieldAndOperator['fieldName'], $value, $fieldAndOperator['operator']);
                    }
                    //if merchant/store manager
                    break;
                case 'm_transaction.pay_status':
                    if(auth()->user()->isAdmin() || auth()->user()->isManager()){
                        if($value == 'Initiated'){
                            $this->addFilterOnRequestsByDBFieldName('status', 0, '=');
                        }else{
                            $this->addFilterOnRequestsByDBFieldName($fieldAndOperator['fieldName'], $value, $fieldAndOperator['operator']);
                        }
                    }
                    //if merchant/store manager
                    break;
                case 'm_transaction.t_date':
                    $date = Carbon::createFromFormat('d-m-Y - g:i:s a', $value . ' - 12:00:00 am');
                    $this->merchantRequests = $this->merchantRequests->whereDate($fieldAndOperator['fieldName'], $fieldAndOperator['operator'], $date->toDateTimeString());
                    break;
                default:
                    $this->addFilterOnRequestsByDBFieldName($fieldAndOperator['fieldName'], $value, $fieldAndOperator['operator']);
                    break;
            }
        }
    }

    private function addFilterOnRequestsByDBFieldName($fieldName, $value, $condition = null){
        $this->merchantRequests
            = $this
            ->merchantRequests
            ->where($fieldName, empty($condition) ? '=' : $condition, $value);
    }
}
