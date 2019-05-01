<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class AdminCheck
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
        
        $user_details = Auth::user();
       
        # check is user logged in or not 
        # if not will redirect on login screen
        if (empty($user_details)) {
            return redirect('/admin/login');
            exit;
        }
        # check is user logged in as a smarter or a broker
        # if not will redirect on login screen
        if(isset($user_details) && $user_details->role_id != 1){
           Auth::logout(); 
           return redirect('/admin/login');
           exit;
        }
        
        return $next($request);
    }
}
