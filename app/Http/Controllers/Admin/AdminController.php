<?php

#Controller Name: AdminController
#Developer      : Narinder Singh
#Purpose        : To perform user related tasks
#Tasks          : Login

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;
use Validator;
use Hash;
use Auth;
use JWTAuth;
use Session;
use App\Config;
use App\User;

class AdminController extends Controller {

    private $_api_context;
    public $table_user = 'users';

    public function __construct() {
        
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 
     * @SWG\Post(
     *   path="/admin/login",
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
        $rule = ['email' => 'required|email|exists:users,email,role_id,1,status,1', 'password' => 'required'];
        $messages = ['email.exists' => 'Invalid credentials. Please try again.'];
        #Check validation
        $validator = Validator::make($requested_data, $rule, $messages);  #Check validation       
        if ($validator->fails()) { #Check validation pass or fail
            return Response::json($this->validateData($validator));
        }

        #Check Login status of user
        $login_status = Auth::once(['email' => trim($requested_data['email']), 'password' => $requested_data['password']]);
        if ($login_status == true) {

            #After Success find user data
            $check_user_access = User::where(['email' => trim($requested_data['email']), 'role_id' => 1])->first();
            if (!empty($check_user_access)) {
                if ($check_user_access->status == 1) {
                    $auth_token = $this->loginAttempt(json_decode(json_encode($check_user_access), true));
                    if ($auth_token) {

                        #update user token
                        $update_user_token = $check_user_access->update(['auth_token' => $auth_token]);
                        if ($update_user_token) {
                            #Check latest data of the user
                            $latest_user_detail = User::where(['email' => trim($requested_data['email']), 'role_id' => 1])->first();
                            unset($latest_user_detail->password);
                            return Response::json(\Config::get('success.success_login'))->header('admintoken', $latest_user_detail->auth_token);
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
        return Response::json(\Config::get('error.wrong_credentials'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/admin/logout",
     *   summary="Logout",
     *   produces={"application/json"},
     *   tags={"Admin"},
     *   @SWG\Parameter(
     *     name="token",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string",
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     * )
     *
     */
    public function logoutAdmin(Request $request) {
        $requested_data = $request->all();
        #check if auth token matchs
        $check_user = User::where('auth_token', $requested_data['data']['auth_token'])->where('id', $requested_data['data']['id'])->where('role_id', 1)->first();
        if (!empty($check_user)) {
            #empty auth token
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

    /**
     * @return \Illuminate\Http\JsonResponse
     * 
     * @SWG\Post(
     *   path="/admin/forgot-password",
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
        $messages = ['email.exists' => 'Please enter valid email id.'];
        $validator = Validator::make($requested_data, $rule, $messages);  #Check validation       
        if ($validator->fails()) { #Check validation pass or fail
            return Response::json($this->validateData($validator));
        }

        #Check user based on email id
        $check_user_email = User::where('email', $requested_data['email'])->where('role_id', 1)->first();
        $requested_data['user_details'] = $check_user_email;
        $forgot_password_token = $this->updateResetPasswordToken();
        $requested_data['forgot_password_token'] = $forgot_password_token;
        #update forgot password code
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
            'view' => 'email.forgot_password',
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
     *   path="/admin/reset-password",
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
            'forgot_token.exists' => 'Link has been expired or invalid link.'
        ];
        $validator = Validator::make($requested_data, $rule, $messages);  #Check validation       
        if ($validator->fails()) { #Check validation pass or fail
            return Response::json($this->validateData($validator));
        }

        # get data of user from perticuller user
        $check_user_access = User::where(['forgot_password_code' => $requested_data['forgot_token']])->where('role_id', 1)->first();
        if (!empty($check_user_access)) {
            $password = bcrypt($requested_data['password']);
            # update data
            $update_user = $check_user_access->update(['password' => $password, 'auth_token' => '', 'forgot_password_code' => '']);

            if ($update_user) {
                return \Config::get('success.success_password_reset');
            } else {
                return \Config::get('error.failed_password_reset');
            }
        } else {
            return \Config::get('error.invalid_link');
        }
    }

    /*
     * Main Function to show reset password form
     * @param Request $request (token)
     * @return type (reset page)
     */

    public function showResetForm(Request $request, $token = null) {
        $tok = User::where('forgot_password_token', $token)->first();
        if (count($tok)) {
            return view('reset_admin')->with(
                            ['token' => $token, 'email' => $request->email]
            );
        } else {
            return view('link_expire')->with(
                            ['token' => $token, 'email' => $request->email]
            );
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *  @SWG\Post(
     *   path="/admin/change-password",
     *   summary="Change password",
     *   produces={"application/json"},
     *   tags={"Admin"},
     *   @SWG\Parameter(
     *     name="token",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "",
     *     @SWG\Schema(ref="#/definitions/changePasswordAdmin"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     * )
     * @SWG\Definition(
     *     definition="changePasswordAdmin",
     *     allOf={
     *         @SWG\Schema(                
     *             @SWG\Property(
     *                 property="old_password",
     *                 type="string",
     *             ),
     *              @SWG\Property(
     *                 property="new_password",
     *                 type="string",
     *             ),
     *              @SWG\Property(
     *                 property="confirm_password",
     *                 type="string",
     *             )
     *         )
     *     }
     * ) 
     *
     */
    public function changePasswordAdmin(Request $request) {
        $requested_data = $request->all();
        $user_data = User::find($requested_data['data']['id']);

        $rule = [
            'confirm_password' => 'required|same:new_password',
            'new_password' => ['required', 'between:8,20', 'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[~!^(){}<>$%@#&*?+=_-])[A-Za-z\d~!^(){}<>$%@#&*?+=_-]/'],
            'old_password' => 'required',
        ];
        $messages = [
            'confirm_password.same' => 'Password and Confirm password should match',
            'new_password.between' => 'Password must be between 8 to 20 characters including a number and special character',
            'old_password.regex' => 'Password must be between 8 to 20 characters including a number and special character',
        ];
        $validator = Validator::make($requested_data, $rule, $messages);  #Check validation       
        if ($validator->fails()) { #Check validation pass or fail
            return Response::json($this->validateData($validator));
        }

        # check user old password is correct or not
        if (Hash::check(trim($requested_data['old_password']), $user_data->password)) {
            # update user password
            $update_password = User::where('id', $requested_data['data']['id'])->update(['password' => bcrypt($requested_data['new_password'])]);
            if ($update_password) {
                return Response::json(\Config::get('success.success_password_update'));
            } else {
                return Response::json(\Config::get('error.failed_to_update_password'));
            }
        } else {
            return Response::json(\Config::get('error.old_password_incorrect'));
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Delete(
     *   path="/admin/delete-user",
     *   summary="User(student or university)",
     *   produces={"application/json"},
     *   tags={"User"},
     *   @SWG\Parameter(
     *     name="token",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="query",
     *     required=true,
     *     description = "user_id",
     *     type="string"
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function deleteUser(Request $request) {
        $requested_data = $request->all();
        $user_id = $requested_data['user_id'];
        # query to delete user permanently
        $user = User::find($user_id);
        if (!empty($user)) {
            $user_detail = $user;
            #delete user images
            $details = $user->userDetail()->select('id', 'user_id', 'profile_image as profile_pic', 'banner_image as banner_pic', 'logo as logo_pic')->first();

            if (!empty($details->profile_pic) && file_exists(public_path() . '/images/student/profile_images/medium/' . $details->profile_pic)) {
                unlink(public_path() . '/images/student/profile_images/thumb/' . $details->profile_pic);
                unlink(public_path() . '/images/student/profile_images/medium/' . $details->profile_pic);
                unlink(public_path() . '/images/student/profile_images/' . $details->profile_pic);
            }

            if ($details->banner_pic != '' && file_exists(public_path() . '/images/university/banner/medium/' . $details->banner_pic)) {
                unlink(public_path() . '/images/university/banner/thumb/' . $details->banner_pic);
                unlink(public_path() . '/images/university/banner/medium/' . $details->banner_pic);
                unlink(public_path() . '/images/university/banner/' . $details->banner_pic);
            }

            if ($details->logo_pic != '' && file_exists(public_path() . '/images/university/logo/medium/' . $details->logo_pic)) {
                unlink(public_path() . '/images/university/logo/thumb/' . $details->logo_pic);
                unlink(public_path() . '/images/university/logo/medium/' . $details->logo_pic);
                unlink(public_path() . '/images/university/logo/' . $details->logo_pic);
            }


            $user->userDetail()->delete();
            $user->address()->delete();
            $user->course()->delete();
            $user->searchedAnalytics()->delete();
            $user->favouriteCourses()->delete();
            $user->pageLogs()->delete();

            #send mail to user
            $send_delete_mail = $this->sendDeleteUserPermanentMail($user_detail);

            $user->delete();
            if ($user) {
                $response = \Config::get('success.user_deleted_successfully');
                if (!$send_delete_mail) {
                    $response = \Config::get('error.invalid_email_user_deleted');
                }
            } else {
                $response = \Config::get('error.failed_to_delete_user');
            }
            return Response::json($response);
        } else {
            return Response::json(\Config::get('error.invalid_user'));
        }
    }

    /* function to send email to user when admin deletes his/her account permanently */

    private function sendDeleteUserPermanentMail($data) {
        $user_detail = User::find($data->id);
        #data to send in email
        $email_array = array(
            'server_url' => \Config::get('variable.SERVER_URL'),
            'to' => $data->email,
            'from' => \Config::get('variable.ADMIN_EMAIL'),
            'from_name' => \Config::get('variable.MAIL_FROM_NAME'),
            'subject' => \Config::get('variable.ACCOUNT_DELETE_PERMANENT'),
            'view' => 'email.account_delete',
            'name' => $user_detail->name,
            'frontend_url' => \Config::get('variable.FRONTEND_URL')
        );
        #Send Verification Email
        return $this->sendEmail($email_array);  #Send Verification Email                
    }

}
