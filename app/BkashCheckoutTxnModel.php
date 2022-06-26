<?php
/**
 * Created by PhpStorm.
 * User: Amin
 * Date: 3/4/2019
 * Time: 1:44 AM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class BkashCheckoutTxnModel extends Model
{
    protected $table = "bkash_transaction";
    protected $fillable = ['OrderNo', 'RequestAmount', 'Txnamount', 'InsDate', 'status', 'paymentID', 'createTime', 'updateTime', 'TxnId', 'transactionStatus', 'bankamount', 'currency', 'intent', 'token', 'created_at', 'updated_at','payment_option_rate_id','response'];
}