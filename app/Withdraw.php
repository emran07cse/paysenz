<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\PaymentRequest;

class Withdraw extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id', 'amount', 'bank_details', 'payment_date'
    ];
    
    /**
     * Get the  role of the User
     */
    public function appClient(){
        return $this->belongsTo('App\AppClient', 'client_id');
    }
    
    public static function getClientWithdrawInfo($client_id){
        if((int) $client_id > 0){
            $resutlTotalAmount = DB::select( DB::raw("SELECT SUM( (`amount` - `store_service_charge`)) as store_amount FROM `payment_requests` WHERE `client_id` = {$client_id} AND `status` = '".PaymentRequest::STATUS_SUCCESS."'") );
            $resutlOnHoldAmount = DB::select( DB::raw("SELECT SUM( (`amount` - `store_service_charge`)) as onhold_amount FROM `payment_requests` WHERE `client_id` = {$client_id} AND `status` = '".PaymentRequest::STATUS_ONHOLD."'") );
            $resutlRefundAmount = DB::select( DB::raw("SELECT SUM( (`amount` - `store_service_charge`)) as refund_amount FROM `payment_requests` WHERE `client_id` = {$client_id} AND `status` = '".PaymentRequest::STATUS_REFUND."'") );
            $resultTotalWithdraw = DB::select( DB::raw("SELECT SUM(`amount`) as withdraw_amount FROM `withdraws` WHERE `client_id` = {$client_id}") );
            
            $totalAmount = $resutlTotalAmount ? (float) $resutlTotalAmount[0]->store_amount : null;
            $totalOnHoldAmount = $resutlOnHoldAmount ? (float) $resutlOnHoldAmount[0]->onhold_amount : null;
            $totalRefundAmount = $resutlRefundAmount ? (float) $resutlRefundAmount[0]->refund_amount : null;

            $totalWithdraw = $resultTotalWithdraw ? (float) $resultTotalWithdraw[0]->withdraw_amount : null;
    	    $availableWithdrawBalance = (float) ($totalAmount - $totalWithdraw);
    	    
    	    return ['totalAmount'=> number_format($totalAmount, 2, '.', ''),
                    'totalOnHoldAmount'=> number_format($totalOnHoldAmount, 2, '.', ''),
                    'totalRefundAmount'=> number_format($totalRefundAmount, 2, '.', ''),
            		'totalWithdraw' => number_format($totalWithdraw, 2, '.', ''),
            		'availableWithdrawBalance' => number_format($availableWithdrawBalance, 2, '.', '')
            	];
        }
        
        return array();
    }
}
