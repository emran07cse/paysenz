<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    private static $instance = null;

    public static function model() : Merchant{
        if(empty(Merchant::$instance)) Merchant::$instance = new Merchant();
        return Merchant::$instance;
    }
}
