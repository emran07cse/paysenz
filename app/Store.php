<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    private static $instance = null;

    public static function model() : Store{
        if(empty(Store::$instance)) Store::$instance = new Store();
        return Store::$instance;
    }
}
