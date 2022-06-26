<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentOption extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bank_id', 'type', 'name', 'description', 'min_required_amount', 'icon_url', 'bank_charge_percentage', 'param_1', 'param_2'
    ];

    /**
     * Get the  role of the User
     */
    public function bank(){
        return $this->belongsTo('App\Bank');
    }

    public function paymentOptionRates(){
        return $this->hasMany('App\PaymentOptionRate');
    }
}
