<?php

namespace App\Http\Controllers;

use App\Bank;
use App\PaymentOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentOptionController extends Controller
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
            'name' => 'required|string|max:100',
            'bank_id' => 'required|integer',
            'type' => 'required|string',
            'min_required_amount' => 'required|numeric',
            'icon_url' => 'required|string',
            'bank_charge_percentage' => 'required|numeric'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('paymentOptions.create', [ 'banks' => Bank::all() ]);
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
        return redirect()
            ->route('paymentOptions.show', PaymentOption::create($inputs))
            
            ->with("status", $this->savedMessage);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('paymentOptions.list', [ 'paymentOptions' => PaymentOption::paginate(10)]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PaymentOption  $paymentOption
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentOption $paymentOption)
    {
        return view('paymentOptions.show', compact('paymentOption'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PaymentOption  $paymentOption
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentOption $paymentOption)
    {
        return view('paymentOptions.edit',['paymentOption' => $paymentOption, 'banks' => Bank::all()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PaymentOption  $paymentOption
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentOption $paymentOption)
    {
        $this->validator(request()->input())->validate();
        $paymentOption->update(request()->input());
        return redirect()->route('paymentOptions.show', $paymentOption)
            
            ->with("status", $this->updatedMessage);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PaymentOption  $paymentOption
     * @return \Illuminate\Http\Response
     */
    public function delete(PaymentOption $paymentOption)
    {
        return view('paymentOptions.delete', compact('paymentOption'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PaymentOption  $paymentOption
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentOption $paymentOption)
    {
        $paymentOption->delete();
        return redirect()
            ->route('paymentOptions.index')
            
            ->with("status", $this->deletedMessage);
    }
}