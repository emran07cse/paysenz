<?php

namespace App\Http\Controllers;

use App\PaymentRequest;
use App\Withdraw;
use App\AppClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WithdrawController extends Controller
{
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $validator =  Validator::make($data, [
            'client_id' => 'required|Integer',
            'bank_details' => 'required|string',
            'payment_date' => 'required|string',
        ]);
        
        $validator->after(function ($validator) {
            $inputs = request()->input();
            $clientWithdrawData = Withdraw::getClientWithdrawInfo($inputs['client_id']);
        
            if($inputs['amount'] > $clientWithdrawData['availableWithdrawBalance']){
                $validator->errors()->add('amount', 'Withdraw amount is greater than available balance!');
            }
        });
        
        
        return $validator;
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('withdraws.create', [
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
        
        return redirect()
            ->route('withdraws.index', Withdraw::create($inputs))
            ->with("status", $this->savedMessage);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $filters = request()->input() ? request()->input() : null;

        if(auth()->user()->isAdmin()){
            $withdraws = Withdraw::with('appClient');
        }else{
            $client_ids = AppClient::select('id')->where('user_id', auth()->user()->id)->get()->map(function ($item, $key) {
                return $item->id;
            })->toArray();
            $withdraws = Withdraw::with('appClient')->whereIn('client_id', $client_ids);
        }

        //Add filter Conditions
        if($filters){
            // Filter by Client
            if(isset($filters['client_id']) && !is_null($filters['client_id'])) {
                $withdraws->where('client_id','=',$filters['client_id']);
            }

            // Filter by Payment Date
            if(isset($filters['withdraw_date']) && !is_null($filters['withdraw_date'])) {
                $fromDate = $filters['withdraw_date'];
                $toDate   = $filters['withdraw_date'];
                $withdraws->whereRaw("created_at >= ? AND created_at <= ?",
                    array($fromDate." 00:00:00", $toDate." 23:59:59")
                );
            }

            // Filter by Payment Date
            if(isset($filters['payment_date']) && !is_null($filters['payment_date'])) {
                $fromDate = $filters['payment_date'];
                $toDate   = $filters['payment_date'];
                $withdraws->whereRaw("payment_date >= ? AND payment_date <= ?",
                    array($fromDate." 00:00:00", $toDate." 23:59:59")
                );
            }
        }

        // Sort
        $withdraws->orderByDesc('id');

        return view('withdraws.list', [
            'withdraws' => $withdraws->paginate(20),
            'clients' => AppClient::all(),
            'filters' => $filters,
        ]);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Withdraw  $withdraw
     * @return \Illuminate\Http\Response
     */
    public function show(Withdraw $withdraw)
    {
        return view('withdraws.show', compact('withdraw'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Withdraw  $withdraw
     * @return \Illuminate\Http\Response
     */
    public function edit(Withdraw $withdraw)
    {
        return view('withdraws.edit',compact('withdraw'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Bank  $withdraw
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Withdraw $withdraw)
    {
        $this->validator(request()->input())->validate();
        $withdraw->update(request()->input());
        return redirect()->route('banks.show', $withdraw)
            ->with("status", $this->updatedMessage);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Withdraw  $withdraw
     * @return \Illuminate\Http\Response
     */
    public function delete(Withdraw $withdraw)
    {
        return view('withdraws.delete', compact('bank'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Withdraw  $withdraw
     * @return \Illuminate\Http\Response
     */
    public function destroy(Withdraw $withdraw)
    {
        $withdraw->delete();
        return redirect()
            ->route('withdraws.index')
            ->with("status", $this->deletedMessage);
    }
    
    /**
     * Show the application ajax-withdraw-data.
     *
     * @return \Illuminate\Http\Response
     */
    public function ajaxWithdrawData(Request $request)
    {
    	if($request->ajax() && (int)$request->client_id > 0){
	        $clientWithdrawData = Withdraw::getClientWithdrawInfo($request->client_id);
	        
    		return response()->json([
    		    'totalAmount'=> $clientWithdrawData['totalAmount'],
                'totalOnHoldAmount'=> $clientWithdrawData['totalOnHoldAmount'],
                'totalRefundAmount'=> $clientWithdrawData['totalRefundAmount'],
    		    'totalWithdraw' => $clientWithdrawData['totalWithdraw'],
    		    'availableWithdrawBalance' => $clientWithdrawData['availableWithdrawBalance']
    		    ]);
    	    	    
    	}
    }
    
}
