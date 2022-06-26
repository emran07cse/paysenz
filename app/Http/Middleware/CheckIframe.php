<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Session;

class CheckIframe
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
        $iframe = ( isset($inputs['iframe']) && ($inputs['iframe'] == TRUE) ) ? TRUE : FALSE;
        if($iframe){
            $request->session()->put('iframe', TRUE);
        } else {
            $request->session()->remove('iframe');
        }

        return $next($request);
    }
}
