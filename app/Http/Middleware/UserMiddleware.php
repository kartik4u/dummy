<?php

namespace App\Http\Middleware;

use App\User;
use App\UserRole;
use App\Models\UserChallengeCategory;
use Auth;
use Closure;

class UserMiddleware
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
        $request['data'] = User::where('id', Auth::user()->id)
        ->select('id','name','email','role_id','status','push_notification_status','profile_image','current_login','profile_image as profile_image_real','synopsis','synopsis as synopsis_full','dob','description','about_writer','phone')
           ->with(['userRole' => function ($q) {
                $q->select('id', 'name');
            }
            ])->first();

        return $next($request);
    }
}
