<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Response;
use JWTAuth;
use Mail;
use App\User;

class Controller extends BaseController {

    /**
     * @SWG\Swagger(
     *     schemes={"http"},
     *     host="server.edseen.com",
     *     basePath="",
     *     @SWG\Info(
     *         version="1.0.0",
     *         title="edseen",
     *         description="",
     *     )
     * )
     */
    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

    /* @Check requried data send or not here */

    public function validateData($validator) {
        $error = $validator->errors()->all(); #if validation fail print error messages
        $data_error = array();
        foreach ($error as $key => $errors):
            $data_error['status'] = 400;
            $data_error['description'] = $errors;
            //  $error_code['errorCode'] = 400;
            //  $error_code['errorDescription'] = $errors;
            //  $data_error['code'] = $error_code;
        endforeach;
        return $data_error; #Return data in json
    }

    /* @get Verication code */

    public function getVerificationCode($length = 12) {
        $str = "";
        $characters = array_merge(range('A', 'Z'), range('0', '9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }

    /** Private Function for update reset password token */
    public function updateResetPasswordToken() {
        #Available alpha caracters
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pin = mt_rand(1000000, 9999999)
                . $characters[rand(0, 8)];
        $string = str_shuffle($pin);
        return $string;
    }

    /* function to get the header token */

    public function loginAttempt($requested_data = NULL) {
        $user = $this->chkUserExistance($requested_data['email']);
        $token = null;
        try {
            if (!$token = JWTAuth::fromUser($user)) { #JWT Login Status
                return \Config::get('error.wrong_credentials');
            }
        } catch (JWTAuthException $e) {
            return \Config::get('error.failed_to_create_token');
        }
        if (empty($token)) {
            return \Config::get('error.empty_token');
        } else {
            return $token;
        }
    }

    /* function to check if email exists or not */

    public function checkEmailExistance($email) {
        $exist = User::where('email', $email)->first();
        if ($exist) {
            return true;
        }
        return false;
    }

    /** Send Email */
    public function sendEmail($data) {
        try {
            Mail::send($data['view'], $data, function ($message) use ($data) {
                $message->to($data['to'])->from($data['from'], $data['from_name'])->subject($data['subject']);
            });
        } catch (Exception $ex) {
            return false;
        }
        if (count(Mail::failures()) > 0) {
            return false;
        } else {
            return true;
        }
    }

    /* function to get the user existance */

    public function chkUserExistance($email = NULL) {
        #Check user based on email or username
        $check_user = User::where('email', $email)->first();
        return $check_user;
    }

    /*
     * Public Function for image dynamic name
     */

    public function imageDynamicName() {
        #Available alpha caracters
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pin = mt_rand(1000000, 9999999)
                . $characters[rand(0, 5)];
        $string = str_shuffle($pin);
        return $string;
}

 /*
    * Function to check token
   */

   function checkToken($request) {
        $parsed_token = $request->headers->all();
        if (isset($parsed_token['x-token'][0])) {
        return $token = $parsed_token['x-token'][0];
        } else if(isset($parsed_token['token'][0])) {
           return $token = $parsed_token['token'][0];
        }
    }

}
