<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Closure;
use App\Models\Page;

class CheckLoginStatus {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (Auth::user()) {
            if (Auth::user()->status != 1) {
                Auth::logout();
                $request->session()->flash('message.deactivate', 'danger');
                $request->session()->flash('message.content', 'Your Account is Deactivated by admin, please contact admin at ' . \Config::get('variable.ADMIN_EMAIL'));
                return Redirect::route('login');
            }
        }
        return $next($request);
    }

}
