<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    private static $instance = null;

    const DIR_LOGO = '/uploads/merchants/logo/';

    public static function model() : User{
        if(empty(User::$instance)) User::$instance = new User();
        return User::$instance;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'role_id', 
        'tcb_id','dbbl_id', 'dbbl_terminal_id', 'dbbl_name', 'dbbl_fullname',
        'ebl_id', 'ebl_password', 'logo', 'invoice_address','invoice_item', 'invoice_email'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the  role of the User
     */
    public function role(){
        return $this->belongsTo('App\Role');
    }

    public function isAdmin(){
        return $this->role_id == 1;
    }

    public function isMerchant(){
        return $this->role_id == 2;
    }

    public function getLogo(){
        return $this->logo;
    }
}
