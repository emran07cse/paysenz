<?php

namespace App\Providers;

use App\CityBankRequest;
use App\DutchBanglaBankRequest;
use App\Merchant;
use App\Rate;
use App\Store;
use Illuminate\Support\ServiceProvider;
use App\User;
use App\PaymentRequest;
use App\Withdraw;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(User::class, function () {
            return User::model();
        });

        $this->app->bind(Merchant::class, function () {
            return Merchant::model();
        });

        $this->app->bind(Store::class, function () {
            return Store::model();
        });

        $this->app->bind(Rate::class, function () {
            return Rate::model();
        });

        //Requests
        $this->app->bind(PaymentRequest::class, function () {
            return PaymentRequest::model();
        });

        $this->app->bind(CityBankRequest::class, function () {
            return CityBankRequest::model();
        });

        $this->app->bind(DutchBanglaBankRequest::class, function () {
            return DutchBanglaBankRequest::model();
        });
        
        $this->app->bind(Withdraw::class, function () {
            return Withdraw::model();
        });
    }
}
