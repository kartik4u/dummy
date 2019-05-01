<?php

/*
 * Controller Name  : AuthController
 * Author           : Prabhat
 * Author Contact   : prabhat.thakur@ignivasolutions.com
 * Created Date     : 23-03-2018
 * Description      : This controller use for Auth use for before login activites (register,login,logout,resetPassword,forgotPassword)
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;
use Validator;
use Hash;
use Auth;
use Session;
use App\Config;
use App\User;

class AuthController extends Controller {

    public $table_user = 'users';

    /**
     * @return \Illuminate\Http\JsonResponse
     * 
     * @SWG\Post(
     *   path="/admin/auth/forgot-password",
     *   summary="Forgot password",
     *   produces={"application/json"},
     *   tags={"Admin"},
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "",
     *     @SWG\Schema(ref="#/definitions/forgotPasswordAdmin"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="forgotPasswordAdmin",
     *     allOf={
     *         @SWG\Schema(
     *             required={"name"},
     *             @SWG\Property(
     *                 property="email",
     *                 type="string",
     *             )
     *         )
     *     }
     * ) 
     *
     */
    public function forgotPasswordAdmin(Request $request) {
        #Set common variable for all requests
        $requested_data = $request->all();
        #Validate Data
        $rule = ['email' => 'required|email|exists:users,email,role_id,1'];
        $validator = Validator::make($requested_data, $rule);  #Check validation       
        if ($validator->fails()) { #Check validation pass or fail
            return Response::json((new Controller)->validateData($validator));
        }

        #Check user based on email id
        $check_user_email = User::where('email', $requested_data['email'])->where('role_id', 1)->first();
        $requested_data['user_details'] = $check_user_email;
        $forgot_password_token = $this->updateResetPasswordToken();
        $requested_data['forgot_password_token'] = $forgot_password_token;
        $updateUser = $check_user_email->update(['forgot_password_code' => $forgot_password_token]);

        #send forgot password mail
        $send_mail = $this->sendForgotPasswordMail($requested_data);
        if ($send_mail) {
            $response = \Config::get('success.success_forgot_password');
        } else {
            $response = \Config::get('error.invalid_email');
        }
        return Response::json($response);
    }

    /* function to send forgot password email */

    private function sendForgotPasswordMail($data) {
        #data to send in email
        $email_array = array(
            'server_url' => \Config::get('variable.SERVER_URL'),
            'to' => $data['user_details']['email'],
            'from' => \Config::get('variable.ADMIN_EMAIL'),
            'from_name' => \Config::get('variable.MAIL_FROM_NAME'),
            'subject' => \Config::get('variable.FORGOT_EMAIL_SUBJECT'),
            'view' => 'email.admin_forgot_password',
            'id' => $data['user_details']['id'],
            'forgot_password_token' => $data['forgot_password_token'],
            'name' => $data['user_details']['name'],
            'admin_url' => \Config::get('variable.ADMIN_URL')
        );
        #Send Forgot Password Email
        return $this->sendEmail($email_array);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 
     * @SWG\Post(
     *   path="/admin/auth/reset-password",
     *   summary="Reset password",
     *   produces={"application/json"},
     *   tags={"Admin"},
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "",
     *     @SWG\Schema(ref="#/definitions/resetPasswordAdmin"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="resetPasswordAdmin",
     *     allOf={
     *         @SWG\Schema(
     *             required={"name"},
     *             @SWG\Property(
     *                 property="confirm_password",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="password",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="forgot_token",
     *                 type="string",
     *             )
     *         )
     *     }
     * ) 
     *
     */
    public function resetPasswordAdmin(Request $request) {

        $requested_data = $request->all();
        #Validate Data
        $rule = ['confirm_password' => 'required|same:password',
            'password' => 'required|between:8,15|regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[~!^(){}<>$%@#&*?+=_-])[A-Za-z\d~!^(){}<>$%@#&*?+=_-]/',
            'forgot_token' => 'required|exists:users,forgot_password_code'];
        $messages = [
            'confirm_password.same' => 'Password and Confirm password should match',
            'password.between' => 'Password must be between 8 to 15 characters including a number and special character',
            'password.regex' => 'Password must be between 8 to 15 characters including a number and special character',
        ];
        $validator = Validator::make($requested_data, $rule, $messages);  #Check validation       
        if ($validator->fails()) { #Check validation pass or fail
            return Response::json((new Controller)->validatedata($validator));
        }

        # get data of user from perticuller user
        $check_user_access = User::where(['forgot_password_code' => $requested_data['forgot_token']])->where('role_id', 1)->first();
        if (!empty($check_user_access)) {
            $password = bcrypt($requested_data['password']);
            # update data
            $update_user = $check_user_access->update(['password' => $password, 'auth_token' => '', 'forgot_password_code' => '']);
            return \Config::get('success.success_password_reset');
        } else {
            return \Config::get('error.invalid_link');
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 
     * @SWG\Post(
     *   path="/admin/auth/login",
     *   summary="Login",
     *   produces={"application/json"},
     *   tags={"Admin"},
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "",
     *     @SWG\Schema(ref="#/definitions/loginAdmin"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="loginAdmin",
     *     allOf={
     *         @SWG\Schema(
     *             required={"name"},
     *             @SWG\Property(
     *                 property="email",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="password",
     *                 type="string",
     *             )
     *         )
     *     }
     * ) 
     *
     */
    public function loginAdmin(Request $request) {
        $requested_data = $request->all();
        #Validate Data
        $rule = ['email' => 'required|email|exists:users,email', 'password' => 'required'];
        #Check validation
        $validator = Validator::make($requested_data, $rule);  #Check validation       
        if ($validator->fails()) { #Check validation pass or fail
            return Response::json((new Controller)->validatedata($validator));
        }
        #Check Login status of user
        $login_status = Auth::once(['email' => $requested_data['email'], 'password' => $requested_data['password']]);
        if ($login_status == true) {
            #After Success find user data
            $check_user_access = User::where(['email' => $requested_data['email'], 'role_id' => 1])->first();
            if (!empty($check_user_access)) {
                if ($check_user_access->status == 1) {
                    $auth_token = $this->loginAttempt(json_decode(json_encode($check_user_access), true));
                    if ($auth_token) {
                        #update user token
                        $update_user_token = $check_user_access->update(['auth_token' => $auth_token]);
                        if ($update_user_token) {
                            #Check latest data of the user
                            $latest_user_detail = User::where(['email' => $requested_data['email'], 'role_id' => 1])->first();
                            return Response::json(\Config::get('success.success_login'))->header('x-admintoken', $latest_user_detail->auth_token);
                        } else {
                            return Response::json(\Config::get('error.failed_to_update_token'));
                        }
                    } else {
                        return Response::json(\Config::get('error.failed_to_create_token'));
                    }
                } else {
                    return Response::json(\Config::get('error.account_not_activate'));
                }
            }
        }
        return Response::json(\Config::get('error.invalid_email_password'));
    }
    
    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/admin/auth/logout",
     *   summary="Logout",
     *   produces={"application/json"},
     *   tags={"Admin"},
     *   @SWG\Parameter(
     *     name="x-admintoken",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "",
     *     @SWG\Schema(ref="#/definitions/logoutAdmin"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     * )
     * @SWG\Definition(
     *     definition="logoutAdmin",
     *     allOf={
     *         @SWG\Schema(
     *              required={"user_id"},
     *              @SWG\Property(
     *                  property="user_id",
     *                  type="integer"
     *              ),
     *          
     *         )
     *     }
     * )
     *
     */
    public function logoutAdmin(Request $request) {
        $requested_data = $request->all();
        $rule = ['user_id' => 'required|exists:users,id'];    #Validate Data
        $validator = Validator::make($requested_data, $rule);  #Check validation
        if ($validator->fails()) { #Check validation pass or fail
            return $this->validateData($validator);
        } else {
            $check_user = User::where('auth_token', $requested_data['data']['auth_token'])->where('id', $requested_data['user_id'])->where('role_id', 1)->first();
            if (!empty($check_user)) {
                $logout_user = $check_user->update(['auth_token' => '']);
                if ($logout_user) {
                    return Response::json(\Config::get('success.success_logout'));
                } else {
                    return Response::json(\Config::get('error.logout_failed'));
                }
            } else {
                return Response::json(\Config::get('error.invalid_request'));
            }
        }
    }

}
