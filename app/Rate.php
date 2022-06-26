<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    private static $instance = null;

    public static function model() : Rate{
        if(empty(Rate::$instance)) Rate::$instance = new Rate();
        return Rate::$instance;
    }
}
