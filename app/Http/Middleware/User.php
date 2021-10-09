<?php

namespace App\Http\Middleware;

use Closure;

class User
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
        if(empty(session('user'))){
            return redirect()->route('login.form');
        }
        else if($request->user()->role !='user')
        {
            request()->session()->flash('error','You do not have any permission to access this page');
            return redirect()->back();
        }
        else{
            return $next($request);
        }
    }
}
