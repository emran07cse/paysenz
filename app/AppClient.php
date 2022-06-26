<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppClient extends Model
{
    protected $table = 'oauth_clients';

    private static $instance = null;

    public static function model() : AppClient{
        if(empty(AppClient::$instance)) AppClient::$instance = new AppClient();
        return AppClient::$instance;
    }

    /**
     * Get the  role of the User
     */
    public function user(){
        return $this->belongsTo('App\User');
    }

    public function paymentOptionRates(){
        return $this->hasMany('App\PaymentOptionRate', 'client_id');
    }
    
    public function paymentOptionRatesActive(){
        return $this->hasMany('App\PaymentOptionRate', 'client_id')->active();
    }

    public function paymentRequests(){
        return $this->hasMany('App\PaymentRequest', 'client_id');
    }
}
