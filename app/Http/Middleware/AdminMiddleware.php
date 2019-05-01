<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Redirect;

class AdminMiddleware
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
       
        if ($this->auth->getUser()->role_id == 1) {
             return $next($request);
           
        }
             $request->session()->flash('message.level', 'danger');
             $request->session()->flash('message.content', 'You are unauthrize user.');
   
                      
                 return Redirect::route('login');
       
    }
}