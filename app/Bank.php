<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'short_code', 'details'
    ];

    public function paymentOptions(){
        return $this->hasMany('App\PaymentOption');
    }
}
