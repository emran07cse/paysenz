<?php

namespace App\Http\Controllers;

use App\Withdraw;
use App\PaymentRequest;
use App\AppClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $inputs = request()->input();
        $client_id = $inputs && $inputs['client_id'] ? $inputs['client_id'] : null;
        if(auth()->user()->isAdmin()){
            $data = PaymentRequest::getWithdrawReport($client_id);
            
            return view('reports.list', [ 'reports' => $data, 'clients' => AppClient::all(), 'client_id' => $client_id]);
        }else{
            /*$client_ids = AppClient::select('id')->where('user_id', auth()->user()->id)->get()->map(function ($item, $key) {
                return $item->id;
            })->toArray();
            $withdraws = Withdraw::whereIn('client_id', $client_ids)->orderByDesc('id')->paginate(20);
            return view('withdraws', [ 'withdraws' => $withdraws ]);*/
            
            $data = PaymentRequest::getWithdrawReport($client_id);
            
            return view('reports.list', [ 'reports' => $data, 'clients' => AppClient::all(), 'client_id' => $client_id]);
        }
        
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
}