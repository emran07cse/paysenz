<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentOptionRate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id', 'payment_option_id', 'paysenz_charge_percentage', 'bank_charge_percentage', 'is_live', 'status'
    ];

    /**
     * Get the  role of the User
     */
    public function appClient(){
        return $this->belongsTo('App\AppClient', 'client_id');
    }

    /**
     * Get the  role of the User
     */
    public function paymentOption(){
        return $this->belongsTo('App\PaymentOption');
    }
    
    /**
     * @desc Return only Active rows
     *
     * @param $query
     * @return mixed
     */
    public function scopeActive($query){
        return $query->where('status', 1);
    }
}
