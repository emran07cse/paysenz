<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'payment', '/payment/status', '/payment/verify', '/payment/retry', 
        'api/*','callback/*','/process/dbbl/success','/process/dbbl/fail'
    ];
}
