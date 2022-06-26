<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 9/10/2018
 * Time: 5:17 PM
 */

namespace App\Helpers;


use App\PaymentOptionRate;

class AppHelpers
{
    public static function getPaymentOptionInfo($payment_option_id,$client_id)
    {
        if (!empty($payment_option_id ))
        {
            $option = PaymentOptionRate::select('id','paysenz_charge_percentage','bank_charge_percentage','is_live','status')
                ->where('payment_option_id',$payment_option_id)
                ->where('client_id',$client_id)
                ->first();

                return $option;
        }else{
            return '';
        }

    }

}