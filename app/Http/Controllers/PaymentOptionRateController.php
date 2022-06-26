<?php

namespace App\Http\Controllers;

use App\AppClient;
use App\Helpers\AppHelpers;
use App\PaymentOption;
use App\PaymentOptionRate;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentOptionRateController extends Controller
{

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'client_id' => 'required|numeric',
            'payment_option_id' => 'required|numeric',
            'paysenz_charge_percentage' => 'required|numeric',
            'bank_charge_percentage' => 'required|numeric',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('paymentOptionRates.create', [
            'paymentOptionRate' => new PaymentOptionRate(),
            'paymentOptions' => PaymentOption::all(),
            'clients' => AppClient::all() ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $inputs = request()->input();
        $this->validator($inputs)->validate();
        unset($inputs['_token']);
        if(PaymentOptionRate::create($inputs))
        {
            return 1;
        }else{
            return 0;
        }


//        $data = ['client_id','payment_option_id','paysenz_charge_percentage','bank_charge_percentage','is_live','status','created_at','updated_at'];
//        $client_id = $request->client_id;
//        $payment_option = [];
//
//        $payment_option_id = $request->payment_option_id;
//        $paysenz_charge_percentage = $request->paysenz_charge_percentage;
//        $bank_charge_percentage = $request->bank_charge_percentage;
//        $is_live = $request->is_live;
//        $status = $request->status;
//
//        if($payment_option_id != null)
//        {
//            for($i = 0, $l = count($payment_option_id); $i < $l; ++$i) {
//                $age=array($client_id,$payment_option_id[$i],$paysenz_charge_percentage[$i],$bank_charge_percentage[$i],$is_live[$i],$status[$i],Carbon::now(),Carbon::now());
//                $combine=array_combine($data,$age);
//                array_push($payment_option,$combine);
//            }
//
//            foreach ($payment_option as $payments)
//            {
//                $option = array($payments);
//                foreach ($option as $item)
//                {
//                    $payment_id = $item['payment_option_id'];
//                    $qry = PaymentOptionRate::where('payment_option_id',$payment_id)
//                        ->where('client_id',$client_id)->get();
//                    if(count($qry)!= '')
//                    {
//                        return redirect()
//                            ->route('paymentOptionRates.show',['client_id'=>$client_id])
//                            ->with("error", 'Your payment option already exist.');
//                    }else{
//                        PaymentOptionRate::insert($option);
//                    }
//                }
//            }
//        }else{
//            return redirect()
//                ->route('paymentOptionRates.show',['client_id'=>$client_id])
//                ->with("error", 'Please select payment Option.');
//        }
//
//
//        return redirect()
//            ->route('paymentOptionRates.show',['client_id'=>$client_id])
//            ->with("status", $this->savedMessage);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('paymentOptionRates.list',[ 'clients' => auth()->user()->isAdmin() ? AppClient::paginate(50) : [] ,
            'paymentOptionRates' => PaymentOptionRate::orderBy('client_id')->orderBy('payment_option_id')->paginate(10)]);
//        return view('paymentOptionRates.list', [ 'paymentOptionRates' => PaymentOptionRate::orderBy('client_id')->orderBy('payment_option_id')->paginate(10)]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PaymentOptionRate  $paymentOptionRate
     * @return \Illuminate\Http\Response
     */
    public function show(AppClient $client_id)
    {
//        dd(PaymentOption::paymentOption());
        $test = PaymentOptionRate::where('client_id',$client_id->id)->get();
//        dd($test);
//        return view('paymentOptionRates.show', compact('paymentOptionRate'));
        return view('paymentOptionRates.details', [
            'client' => $client_id,
            'paymentOptions' => PaymentOption::all(),
            'PaymentOptionRates' => PaymentOptionRate::where('client_id',$client_id->id)->get()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PaymentOptionRate  $paymentOptionRate
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentOptionRate $paymentOptionRate)
    {
        return view('paymentOptionRates.edit',[
            'paymentOptionRate' => $paymentOptionRate,
            'clients' => AppClient::all(),
            'paymentOptions' => PaymentOption::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PaymentOptionRate  $paymentOptionRate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentOptionRate $paymentOptionRate)
    {
//        return $paymentOptionRate;
        $this->validator(request()->input())->validate();
        if ($paymentOptionRate->update(request()->input()))
        {
            return 1;
        }else{
            return 0;
        }

//        return redirect()->route('paymentOptionRates.show', $paymentOptionRate)
//            ->with("status", $this->updatedMessage);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PaymentOptionRate  $paymentOptionRate
     * @return \Illuminate\Http\Response
     */
    public function delete(PaymentOptionRate $paymentOptionRate)
    {
        return view('paymentOptionRates.delete', compact('paymentOptionRate'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PaymentOptionRate  $paymentOptionRate
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentOptionRate $paymentOptionRate)
    {
//        return $paymentOptionRate;
        if ($paymentOptionRate->delete())
        {
            return 1;
        }else{
            return 0;
        }
//        return redirect()
//            ->route('paymentOptionRates.index')
//
//            ->with("status", $this->deletedMessage);
    }
}