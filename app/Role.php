<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    private static $instance = null;

    public static function model() : Role{
        if(empty(Role::$instance)) Role::$instance = new Role();
        return Role::$instance;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description',
    ];

    public function users(){
        return $this->hasMany('App\User');
    }
}
