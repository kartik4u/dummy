<?php

/*
 * Controller Name  : AuthController
 * Author           : Prabhat
 * Author Contact   : prabhat.thakur@ignivasolutions.com
 * Created Date     : 23-03-2018
 * Description      : This controller use for Auth use for before login activites (register,login,logout,resetPassword,forgotPassword)
 */

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Frontend;
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
use App\UserDetail;
use App\Page;
use App\PageLog;

class AuthController extends Controller {

    public $table_user = 'users';

    /**
     *  @SWG\Post(
     *   path="/frontend/auth/register-data-check",
     *   summary="Register Data Check",
     *   produces={"application/json"},
     *   tags={"University/Student"},
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     @SWG\Schema(ref="#/definitions/registerDataCheck"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="registerDataCheck",
     *     allOf={
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="name",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="email",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="password",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="type",
     *                 type="string",
     *             ),
     *         )
     *     }
     * )
     *
     */
    public function registerDataCheck(Request $request) {
        $requested_data = $request->all();
        $rule = [
            'type' => 'required|in:student,university',
            'password' => ['required', 'between:8,15', 'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[~!^(){}<>$%@#&*?+=_-])[A-Za-z\d~!^(){}<>$%@#&*?+=_-]/'],
            'email' => 'required|email|unique:users'
                //'name' => 'required',
        ];
        $rule['name'] = $requested_data['type'] == 'student' ? 'required' : 'required|unique:users';

        $messages = [
            'type.in' => 'Type must be either university or student',
            'email.email' => 'Invalid email',
            'password.between' => 'Password must be between 8 to 15 characters including a number and special character',
            'password.regex' => 'Password must be between 8 to 15 characters including a number and special character',
        ];
        $validator = Validator::make($requested_data, $rule, $messages);  #Check validation

        if ($validator->fails()) { #Check validation pass or fail
            return Response::json((new Controller)->validateData($validator));
        } else {
            #check if email already exists
            $email_exists = $this->checkEmailExistance($requested_data);
            if ($email_exists) {
                return Response::json(\Config::get('error.email_already_exists'));
            } else {
                return Response::json(['status' => 200]);
            }
        }
    }

    /**
     *  @SWG\Post(
     *   path="/frontend/auth/register",
     *   summary="Register",
     *   produces={"application/json"},
     *   tags={"University/Student"},
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     @SWG\Schema(ref="#/definitions/register"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="register",
     *     allOf={
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="name",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="email",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="password",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="type",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="terms",
     *                 type="boolean",
     *             )
     *         )
     *     }
     * )
     *
     */
    public function register(Request $request) {
        $requested_data = $request->all();
        $rule = [
            'type' => 'required|in:student,university',
            'password' => ['required', 'between:8,15', 'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[~!^(){}<>$%@#&*?+=_-])[A-Za-z\d~!^(){}<>$%@#&*?+=_-]/'],
            'email' => 'required|email|unique:users','terms' => 'required'
                //'name' => 'required',
        ];
        $rule['name'] = $requested_data['type'] == 'student' ? 'required' : 'required|unique:users';

        $messages = [
            'type.in' => 'Type must be either university or student',
            'email.email' => 'Invalid email',
            'password.between' => 'Password must be between 8 to 15 characters including a number and special character',
            'password.regex' => 'Password must be between 8 to 15 characters including a number and special character',
        ];
        $validator = Validator::make($requested_data, $rule, $messages);  #Check validation

        if ($validator->fails()) { #Check validation pass or fail
            return Response::json((new Controller)->validateData($validator));
        } else {
            #check if email already exists
            $email_exists = $this->checkEmailExistance($requested_data);
            if ($email_exists) {
                return Response::json(\Config::get('error.email_already_exists'));
            } 
            #terms privacy policy accetance error
            if($requested_data['terms'] != 1){
                return Response::json(\Config::get('error.privacy_terms_error'));
            }
            #save data
            $saved_data = $this->saveRegistrationData($requested_data);
            if ($saved_data) {
                #send verification mail
                $send_verification_mail = $this->sendVerificationMail($saved_data);
                if ($send_verification_mail) {
                    $response = \Config::get('success.success_user_created');
                } else {
                    User::where('id', $saved_data['id'])->delete();
                    $response = \Config::get('error.invalid_email');
                }
            } else {
                $response = \Config::get('error.failed_to_save_details'); #Send error message for required data
            }
            return Response::json($response);
        }
    }

    /* save university/student data */

    private function saveRegistrationData($data) {
        $insert_array = [];
        $insert_array['slug'] = str_slug($data['name']);
        $insert_array['name'] = trim($data['name']);
        $insert_array['email'] = trim($data['email']);
        $insert_array['password'] = Hash::make(trim($data['password']));
        $insert_array['verification_code'] = $this->getverificationCode();
        $insert_array['role_id'] = $data['type'] == 'student' ? 3 : 2;
        $insert_array['status'] = 0;
        $insert_array['created_at'] = time();
        $insert_array['updated_at'] = time();
        #save university data in table
        $create_university = User::create($insert_array);

        if ($create_university) {
            $return['id'] = $create_university->id;
            UserDetail::create(['user_id' => $return['id'], 'created_at' => time(), 'updated_at' => time()]);
            $terms = Page::where('slug', 'terms-and-conditions')->where('status', 1)->select('id')->orderBy('id', 'desc')->first();
            $privacy = Page::where('slug', 'privacy-policy')->where('status', 1)->select('id')->orderBy('id', 'desc')->first();
            PageLog::insert([
                ['user_id' => $return['id'], 'title' => 'terms-and-conditions', 'page_id' => $terms->id, 'created_at' => time(), 'updated_at' => time()],
                ['user_id' => $return['id'], 'title' => 'privacy-policy', 'page_id' => $privacy->id, 'created_at' => time(), 'updated_at' => time()]
            ]);

            $return['verification_code'] = $insert_array['verification_code'];
            $return['email'] = trim($data['email']);
            $return['type'] = trim($data['type']);
            return $return;
        }
        return false;
    }

    /* function to send a verification email */

    private function sendVerificationMail($data) {
        $user_detail = User::find($data['id']);
        #data to send in email
        $email_array = array(
            'server_url' => \Config::get('variable.SERVER_URL'),
            'to' => $data["email"],
            'from' => \Config::get('variable.ADMIN_EMAIL'),
            'from_name' => \Config::get('variable.MAIL_FROM_NAME'),
            'subject' => \Config::get('variable.REGISTER_EMAIL_SUBJECT'),
            'view' => 'email.register',
            'verification_code' => $data["verification_code"],
            'name' => $user_detail->name,
            'type' => $data['type'],
            'frontend_url' => \Config::get('variable.FRONTEND_URL')
        );
        #Send Verification Email
        return $this->sendEmail($email_array);  #Send Verification Email                
    }

    /*
     * Function for verify email
     * @param request parameters (verification code)
     * @return response (status, message, success/failure)
     */

    public function verify(request $request) {
        $requested_data = $request->all();

        if (isset($requested_data['verify_code']) && !empty($requested_data['verify_code']) && isset($requested_data['type']) && !empty($requested_data['type'])) {
            $verify_code = trim($requested_data['verify_code']);
            $type = trim($requested_data['type']);
            $role_id = $type == 'student' ? 3 : 2;
            $expired_date = time() - 86400;
            $check_link_expiration = User::where('verification_code', $verify_code)->where('role_id', $role_id)->where('updated_at', '>', $expired_date)->first();
            if ($check_link_expiration) {
                $update = User::where('verification_code', $verify_code);
                if ($role_id == 3) {
                    $update = $update->update(array('verification_code' => '', 'status' => '1'));
                } else {
                    $update = $update->update(array('verification_code' => '', 'status' => '3'));
                }
                if ($update) {
                    #Account verificattion entry insertion
                    $account_verification = PageLog::updateOrCreate(['title' => 'account-verification', 'user_id' => $check_link_expiration->id], ['page_id' => 0, 'created_at' => time(), 'status' => 1, 'updated_at' => time()]);

                    if ($role_id == 3) {
                        $response = \Config::get('success.success_email_verified_student');
                    } else {
                        $response = \Config::get('success.success_email_verified');
                    }
                } else {
                    $response = \Config::get('error.fail_to_verify');
                }
            } else {
                $response = \Config::get('error.link_expired');
            }
        } else {
            $response = \Config::get('error.invalid_link');
        }
        return Response::json($response);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 
     * @SWG\Post(
     *   path="/frontend/auth/forgot-password",
     *   summary="Forgot password",
     *   produces={"application/json"},
     *   tags={"University/Student"},
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "",
     *     @SWG\Schema(ref="#/definitions/forgotPassword"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="forgotPassword",
     *     allOf={
     *         @SWG\Schema(
     *             required={"name"},
     *             @SWG\Property(
     *                 property="email",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="type",
     *                 type="string",
     *             )
     *         )
     *     }
     * ) 
     *
     */
    public function forgotPassword(Request $request) {
        #Set common variable for all requests
        $requested_data = $request->all();
        #Validate Data
        $rule = ['type' => 'required|in:student,university', 'email' => 'required|email|exists:users,email'];
        $messages = ['type.in' => 'Type must be either university or student'];
        $validator = Validator::make($requested_data, $rule, $messages);  #Check validation       
        if ($validator->fails()) { #Check validation pass or fail
            return Response::json((new Controller)->validateData($validator));
        }

        $role_id = $requested_data['type'] == 'student' ? 3 : 2;
        #Check user based on email id
        $check_user_email = User::where('email', $requested_data['email'])->where('role_id', $role_id)->first();
        if (!empty($check_user_email)) {
            $requested_data['user_details'] = $check_user_email;
            $forgot_password_token = $this->updateResetPasswordToken();
            $requested_data['forgot_password_token'] = $forgot_password_token;
            $updateUser = $check_user_email->update(['forgot_password_code' => $forgot_password_token]);

            #send forgot password mail
            $send_mail = $this->sendForgotPasswordMail($requested_data);
            if ($send_mail) {
                return Response::json(\Config::get('success.success_forgot_password'));
            } else {
                return Response::json(\Config::get('error.invalid_email'));
            }
        } else {
            return Response::json(\Config::get('error.invalid_user'));
        }
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
            'type' => $data['type'],
            'frontend_url' => \Config::get('variable.FRONTEND_URL')
        );
        #Send Forgot Password Email
        return $this->sendEmail($email_array);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 
     * @SWG\Post(
     *   path="/frontend/auth/reset-password",
     *   summary="Reset password",
     *   produces={"application/json"},
     *   tags={"University/Student"},
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "",
     *     @SWG\Schema(ref="#/definitions/resetPassword"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="resetPassword",
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
     *                 property="type",
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
    public function resetPassword(Request $request) {

        $requested_data = $request->all();
        #Validate Data
        $rule = ['type' => 'required|in:student,university',
            'confirm_password' => 'required|same:password',
            'password' => 'required|between:8,15|regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[~!^(){}<>$%@#&*?+=_-])[A-Za-z\d~!^(){}<>$%@#&*?+=_-]/',
            'forgot_token' => 'required|exists:users,forgot_password_code'];
        $messages = [
            'type.in' => 'Type must be either university or student',
            'confirm_password.same' => 'Password and Confirm password should match',
            'password.between' => 'Password must be between 8 to 15 characters including a number and special character',
            'password.regex' => 'Password must be between 8 to 15 characters including a number and special character',
            'forgot_token.required' => 'Link has been expired or invalid link',
            'forgot_token.exists' => 'Link has been expired or invalid link',
        ];
        $validator = Validator::make($requested_data, $rule, $messages);  #Check validation       
        if ($validator->fails()) { #Check validation pass or fail
            return Response::json($this->validateData($validator));
        }
        $role_id = $requested_data['type'] == 'student' ? 3 : 2;
        # get data of user from perticuller user
        $check_user_access = User::where(['forgot_password_code' => $requested_data['forgot_token']])->where('role_id', $role_id)->first();
        if (!empty($check_user_access)) {
            $password = bcrypt($requested_data['password']);
            # update data
            $update_user = $check_user_access->update(['password' => $password, 'auth_token' => '', 'forgot_password_code' => '']);
            return Response::json(\Config::get('success.success_password_reset'));
        } else {
            return Response::json(\Config::get('error.invalid_link'));
        }
    }

    public function checkresetPassword(Request $request) {
        $requested_data = $request->all();
        $role_id = $requested_data['type'] == 'student' ? 3 : 2;
        # get data of user from perticuller user
        $check_user_access = User::where(['forgot_password_code' => $requested_data['forgot_token']])->where('role_id', $role_id)->first();
        if (isset($check_user_access) && empty(!$check_user_access)) {
            return Response::json(\Config::get('success.success_page_show_reset_password'));
        } else {
            return Response::json(\Config::get('error.invalid_link'));
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 
     * @SWG\Post(
     *   path="/frontend/auth/login",
     *   summary="Login",
     *   produces={"application/json"},
     *   tags={"University/Student"},
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "",
     *     @SWG\Schema(ref="#/definitions/login"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="login",
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
     *             ),
     *             @SWG\Property(
     *                 property="type",
     *                 type="string",
     *             )
     *         )
     *     }
     * ) 
     *
     */
    public function login(Request $request) {

        $requested_data = $request->all();
        $rule = ['email' => 'required|email|exists:users,email', 'password' => 'required', 'type' => 'required|in:student,university',]; //Validate Data
        $messages = ['type.in' => 'Type must be either university or student', 'email.exists' => 'Please enter valid credentials'];
        $validator = Validator::make($requested_data, $rule, $messages);  #Check validation       
        if ($validator->fails()) { #Check validation pass or fail
            return Response::json($this->validateData($validator));
        }
        $role_id = $requested_data['type'] == 'student' ? 3 : 2;
        #Check Login status of user
        $login_status = Auth::once(['email' => $requested_data['email'], 'password' => $requested_data['password']]);
        if ($login_status == true) {
            $check_user_access = User::where(['email' => $requested_data['email'], 'role_id' => $role_id])->first();
            if (!empty($check_user_access)) {
                if ($check_user_access->status == 1) {
                    $auth_token = $this->loginAttempt(json_decode(json_encode($check_user_access), true));
                    if ($auth_token) {
                        #update user token
                        $update_user_token = $check_user_access->update(['auth_token' => $auth_token, 'last_logged' => time()]);
                        if ($update_user_token) {
                            #Check latest data of the user
                            $latest_user_detail = User::where(['email' => $requested_data['email'], 'role_id' => $role_id])->first();
                            $response = \Config::get('success.success_login');
                            unset($latest_user_detail->password);
                            $response['data'] = $latest_user_detail;
                            if ($role_id == 3) {
                                return Response::json($response)->header('studenttoken', $latest_user_detail->auth_token);
                            } else {
                                return Response::json($response)->header('universitytoken', $latest_user_detail->auth_token);
                            }
                        } else {
                            return Response::json(\Config::get('error.failed_to_update_token'));
                        }
                    } else {
                        return Response::json(\Config::get('error.failed_to_create_token'));
                    }
                } else if ($check_user_access->status == 3 && $role_id == 2) {
                    return Response::json(\Config::get('error.not_verified_by_admin'));
                } else if ($check_user_access->status == 2) {
                    return Response::json(\Config::get('error.deactivated_by_admin'));
                } else {
                    return Response::json(\Config::get('error.account_not_activate'));
                }
            }
        }
        return Response::json(\Config::get('error.valid_credentials'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/frontend/auth/logout",
     *   summary="Logout",
     *   produces={"application/json"},
     *   tags={"University/Student"},
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
     */
    public function logout(Request $request) {
        $requested_data = $request->all();
        $check_user = User::where('auth_token', $requested_data['data']['auth_token'])->where('id', $requested_data['data']['id'])->where('role_id', $requested_data['data']['role_id'])->first();
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
