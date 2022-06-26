<?php

namespace App\Providers;

use App\Bank;
use App\PaymentOption;
use App\PaymentOptionRate;
use App\Policies\BankPolicy;
use App\Policies\PaymentOptionPolicy;
use App\Policies\PaymentOptionRatePolicy;
use App\Policies\UserPolicy;
use App\User;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        User::class => UserPolicy::class,
        Bank::class => BankPolicy::class,
        PaymentOption::class => PaymentOptionPolicy::class,
        PaymentOptionRate::class => PaymentOptionRatePolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        Passport::tokensExpireIn(now()->addDays(365)); // Expires in 12 months

        Passport::refreshTokensExpireIn(now()->addDays(365)); // Refresh token for additional 12 months
    }
}
