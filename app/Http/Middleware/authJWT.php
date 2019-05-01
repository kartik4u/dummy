<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Response;
use App\User;
use App\Race;

class authJWT {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $token = $parsed_token = '';
        $parsed_token = $request->headers->all();
        if (isset($parsed_token['admintoken'][0])) {
            $token = $parsed_token['admintoken'][0];
        } else if (isset($parsed_token['studenttoken'][0])) {
            $token = $parsed_token['studenttoken'][0];
        } else if (isset($parsed_token['universitytoken'][0])) {
            $token = $parsed_token['universitytoken'][0];
        }else if (isset($parsed_token['token'][0])) {
            $token = $parsed_token['token'][0];
        } else {
            return response()->json(['status' => 1000, 'description' => 'Please send required token.']);
        }

        try {
            $user = JWTAuth::toUser($token);                                   
            if (!empty($user) &&  $user->auth_token == "" && $user->auth_token != $token) {                        
                return response()->json(['status' => 1000, 'message' => 'Your session is expired.', 'description' => 'Your session is expired.']);
            }
               
//            if (!empty($user) && $user->status == "2") {                   
//                $user = JWTAuth::toUser($token);
//                $user->update([
//                    'auth_token' => '',
//                    ]);
//                return response()->json(['status' => 1000, 'message' => 'Your account has been deactivated by the admin.']);
//            }        
                                                    
            $check_token_database = User::where(['auth_token' => $token])->first();
            if ($check_token_database) {
                $request['data'] = $check_token_database->toArray();
                return $next($request);
            } else {                            
                return response()->json(['status' => 1000, 'description' => 'Your session is expired.']);
            }
           
        } catch (JWTException $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['status' => 1000, 'description' => 'Your session is expired.']);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['status' => 1000,'description' => 'Your session is expired.']);
            } else {
                return response()->json(['status' => 1000, 'description' => 'Your session is expired.']);
            }
        }
    }

}
