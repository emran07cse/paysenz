<?php

namespace App\Http\Middleware;

use Closure;
use App\AppClient;

class CheckClient
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //client validation
        $inputs = $request->input();
        $client = isset($inputs['client']) ? $inputs['client'] : null;
        $secret = isset($inputs['secret']) ? $inputs['secret'] : null;

        if(AppClient::where(['name' => $client, 'secret' => $secret])->count() == 0){
            die('Invalid client credentials!!');
        }

        return $next($request);
    }
}
