<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommonStoryRequest;
use App\Http\Requests\deleteUserRequest;
use App\Http\Requests\EditProfileRequest;
use App\Http\Requests\SaveStripeRequest;
use App\Http\Requests\FavouriteUnfavouriteRequest;
use App\Http\Requests\PostCommentRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ReportUserRequest;
use App\Http\Requests\SocialLoginRequest;
use App\Http\Requests\UserChangePasswordRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserProfileImageRequest;
use App\Http\Requests\UserProfileRequest;
use App\Http\Requests\UserResendVerifyRequest;
use App\Http\Requests\UserSignupRequest;
use App\Http\Requests\GetCommentsRequest;
use App\Http\Requests\FollowUnfollowRequest;
use App\Http\Requests\ViewProfileRequest;
use App\Http\Requests\GetUserViewRequest;
use App\Http\Requests\SaveRatingRequest;
use App\Http\Requests\AddCardRequest;
use App\Http\Traits\CommonTrait;
use App\Http\Traits\UserTrait;
use App\Interfaces\UserInterface;
use App\Jobs\SignupJob;
use App\Models\ActivityLog;
use App\Models\AdditionalUserInfo;
use App\Models\UserGenre;
use App\Models\ViewProfile;
use App\Models\Page;
use App\Models\Follower;
use App\Models\Notification;
use App\Models\Favourite;
use App\Models\Rating;
use App\Models\Episode;
use App\Models\Story;
use App\Models\Comment;
use App\Models\BankDetail;
use App\Role;
use App\User;
use Config;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Image;
use Lcobucci\JWT\Parser;
use Mail;
use Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class UsersController extends Controller implements UserInterface
{
    use CommonTrait, UserTrait;

    
    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/user/signUp",
     *   summary="user signup",
     *   produces={"application/json"},
     *   tags={"COMMON USER APIS"},
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "user_type = writer or reader, dob = timestemp",
     *     @SWG\Schema(ref="#/definitions/signup"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="signup",
     *     allOf={
     *         @SWG\Schema(   
     *             @SWG\Property(
     *                 property="email",
     *                 type="string",
     *                 default="test@yopmail.com",
     *             ),
     *             @SWG\Property(
     *                 property="user_type",
     *                 type="string",
     *                 default="reader",
     *             ) ,
     *             @SWG\Property(
     *                 property="password",
     *                 type="string",
     *                 default="test@123",    
     *             ) ,
     *             @SWG\Property(
     *                 property="password_confirmation",
     *                 type="string",
     *                 default="test@123",    
     *             ) ,
     *             @SWG\Property(
     *                 property="dob",
     *                 type="number",
     *                 default="test@123",    
     *             ) ,
     *             @SWG\Property(
     *                 property="username",
     *                 type="string",
     *                 default="test",    
     *             ) ,
     *             @SWG\Property(
     *                 property="country",
     *                 type="string",
     *                 default="india",    
     *             ) ,
     *         )
     *     }
     * )
     *
     */

    public function signUp(UserSignupRequest $request)
    {
        $requested_data = $request->all();
        $array['role_id'] = Role::where('name', $requested_data['user_type'])->first()->id;
        $array['password'] = bcrypt($requested_data['password']);
        $array['email'] = $requested_data['email'];
        $array['username'] = isset($requested_data['username'])?$requested_data['username']:'';
        $array['dob'] = isset($requested_data['dob'])?$requested_data['dob']:'';
        $array['country'] = isset($requested_data['country'])?$requested_data['country']:'';
        $array['verify_token'] = $this->getverificationCode();
        $array['created_at'] = time();
        $array['updated_at'] = time();
        $user = User::create($array);
        if ($user) {
            SignupJob::dispatch($user)->delay(now()->addSeconds(3));
            
            // Manage activity log
            $term = Page::where('meta_key', '=', 'term')->orderBy('created_at', 'desc')->select('id')->first();
            $privacy_policy = Page::where('meta_key', '=', 'privacy-policy')->orderBy('created_at', 'desc')->select('id')->first();
            $sign_up_activity_log = ActivityLog::insert(array(
                array('user_id' => $user->id,
                    'meta_key' => 'sign_up',
                    'meta_value' => time(),
                    'status' => 1,
                    'created_at' => time(),
                    'updated_at' => time(),
                ),
                array('user_id' => $user->id,
                    'meta_key' => 'term_condition',
                    'meta_value' => $term->id,
                    'status' => 1,
                    'created_at' => time(),
                    'updated_at' => time(),
                ),
                array('user_id' => $user->id,
                    'meta_key' => 'privacy_policy',
                    'meta_value' => $privacy_policy->id,
                    'status' => 1,
                    'created_at' => time(),
                    'updated_at' => time(),
                ),
            )
            );
           
            // create user folder
            if (file_exists(storage_path() . '/app/public/users')) {
                mkdir(storage_path() . '/app/public/users/'.$user->id.'', 0777, true);
            }

            $data = \Config::get('success.user_created');
            $data['data'] = (object) [];
            return Response::json($data);

        } else {
            $data = \Config::get('error.user_created');
            $data['data'] = (object) [];
            return Response::json($data);
        }
    }


     /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/user/login",
     *   summary="user login",
     *   produces={"application/json"},
     *   tags={"COMMON USER APIS"},
     *   description = "device_type = IOS or ANDROID,user_type = writer or reader",
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "login ",
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
     *             @SWG\Property(
     *                 property="username",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="password",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="device_id",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="device_type",
     *                 type="string",
     *                 default="IOS",
     *                 description="IOS or ANDROID"
     *             ),
     *             @SWG\Property(
     *                 property="user_type",
     *                 type="string",
     *                 default="reader",
     *                 description="writer or reader"
     *             ),
     *         )
     *     }
     * )
     *
     */
    /*
     * Function : function for login
     * Input:
     * Output: success, error
     */

    public function login(UserLoginRequest $request)
    {
        $requested_data = $request->all();
        
        if (Auth::attempt(['username' => request('username'), 'password' => request('password')], request('remember_me'))) {
            $user = Auth::user();
            // check multiple signup
            if($user->is_writer_reader==0){
                if(Role::where('name', $requested_data['user_type'])->first()->id!=$user->role_id){
                    Auth::logout();
                    $data['status'] = \Config::get('error.code');
                    $data['message'] = 'Unautharised';
                    $data['data'] = (object) [];
                    return Response::json($data);
                }
            }

            $user = $user->update([
                'device_id' => isset($requested_data['device_id']) ? $requested_data['device_id'] : '',
                'device_type' => isset($requested_data['device_type']) ? $requested_data['device_type'] : 'IOS',
                'updated_at' => time(),
            ]);

            $user = Auth::user();
            switch ($user->status) {
                case 0:
                    Auth::logout();
                    $data = \Config::get('error.account_not_verified');
                    $data['user_unverified'] = true;
                    $data['username'] = request('username');
                    $data['data'] = (object) [];
                    return Response::json($data);
                    break;
                case 2:
                    Auth::logout();
                    $data = \Config::get('error.account_blocked_admin');
                    $data['data'] = (object) [];
                    return Response::json($data);
                    break;
            }

            $remember_me = isset($requested_data['remember_me']) ? $requested_data['remember_me'] : false;
            $token= $user->createToken(env("APP_NAME"))->accessToken;

            $user_last_login = User::where('id', $user->id)
            ->update(['current_login' => time(), 'last_login' => $user->current_login,'auth_token' => 'Bearer'. ' '.$token]);
            $user_activity_login = ActivityLog::updateOrCreate(
                ['user_id' => $user->id, 'meta_key' => 'last_login'],
                ['user_id' => $user->id, 'meta_key' => 'last_login',
                    'meta_value' => $user->current_login, 'status' => 1,
                    'created_at' => time(), 'updated_at' => time()]
            );

            return Response::json([
                'status' => 200,
                'user_id' => $user->id,
                'role_id' => $user->role_id,
                'profile_status' => $user->status,
                'is_writer_reader' => $user->is_writer_reader,
                'is_servay_filled' => AdditionalUserInfo::where('user_id',$user->id)->count(),
                'is_genre_filled' => UserGenre::where('user_id',$user->id)->count()
            ])->header('access_token','Bearer'. ' '. $user->createToken(env("APP_NAME"))->accessToken);
        } else {
            $data = \Config::get('error.invalid_email_password');
            $data['data'] = (object) [];
            return Response::json($data);
        }
    }


     /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/user/sociallogin",
     *   summary="social login",
     *   produces={"application/json"},
     *   tags={"COMMON USER APIS"},
     *   @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     required=true,
     *     type="string"
     *   ),
     *  @SWG\Parameter(
     *     name="name",
     *     in="formData",
     *     required=false,
     *     type="string"
     *   ),
     *  @SWG\Parameter(
     *     name="device_type",
     *     in="formData",
     *     required=true,
     *     type="string"
     *   ),
     *  @SWG\Parameter(
     *     name="device_id",
     *     in="formData",
     *     required=true,
     *     type="string"
     *   ),
     *  @SWG\Parameter(
     *     name="signup_type",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "Please enter (facebook / gmail).",
     *   ),
     *  @SWG\Parameter(
     *     name="social_id",
     *     in="formData",
     *     required=true,
     *     type="string"
     *   ),
     *
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function socialLogin(SocialLoginRequest $request)
    {
        $requested_data = $request->all();
        $email = isset($requested_data['email'])?$requested_data['email']:0;
        $user = User::where('email', '=', $email)->orWhere('gmail_id', '=', request('social_id'))->orWhere('fb_id', '=', request('social_id'))->first();
        //Now log in the user if exists
        if ($user != null) {
            switch ($user->status) { # if blocked by admin
            case 2:
                    Auth::logout();
                    $data = \Config::get('error.account_blocked_admin');
                    $data['data'] = (object) [];
                    return Response::json($data);
                    break;
            }
            DB::table('oauth_access_tokens')->where('user_id', $user->id)->update(['revoked' => true]); # logout
            Auth::loginUsingId($user->id); # login
            $remember_me = isset($requested_data['remember_me']) ? $requested_data['remember_me'] : false;

            if(isset($requested_data['email'])){
                if(!User::where('id', $user->id)->where('email','!=',NULL)->count()){
                    User::where('id', $user->id)->update(['email'=>$requested_data['email']]);
                }
            }

            if(isset($requested_data['name'])){
                User::where('id', $user->id)->update(['name'=>$requested_data['name']]);
            }
            User::where('id', $user->id)->update(['signup_type'=>$requested_data['signup_type']]);

            if($requested_data['signup_type']=="facebook"){
                User::where('id', $user->id)->update(['fb_id'=>$requested_data['social_id']]);
            } else{
                User::where('id', $user->id)->update(['gmail_id'=>$requested_data['social_id']]);
            }


            return Response::json([
                'status' => 200,
                'user_id' => $user->id,
                'role_id' => $user->role_id,
                'profile_status' => $user->status,
                'remember_me' => $remember_me,
            ])->header('access_token','Bearer'. ' '. $user->createToken(env("APP_NAME"))->accessToken);
        } else {
            $array = [];
            $array['role_id'] = Role::where('name', 'reader')->first()->id;

            if(isset($requested_data['name'])){
                $array['name'] = $requested_data['name'];
            }

            if(isset($requested_data['email'])){
                $array['email'] = $requested_data['email'];
            }
            $array['device_type'] = $requested_data['device_type'];
            $array['signup_type'] = $requested_data['signup_type'];
            $array['device_id'] = $requested_data['device_id'];
            $array['status'] = 1;
            $array['created_at'] = time();
            $array['updated_at'] = time();
            $user = User::create($array);

            if($requested_data['signup_type']=="facebook"){
                User::where('id', $user->id)->update(['fb_id'=>$requested_data['social_id']]);
            } else{
                User::where('id', $user->id)->update(['gmail_id'=>$requested_data['social_id']]);
            }

            // Auth::loginUsingId($user->id);
            // /*create norification on signup*/
            // $type = 1; // 1=> sign up
            // $notification = $this->notifications($requested_data, $user->id, $user->id, $type, '');
            $remember_me = isset($requested_data['remember_me']) ? $requested_data['remember_me'] : false;
            return Response::json([
                'status' => 200,
                'user_id' => $user->id,
                'role_id' => $user->role_id,
                'profile_status' => $user->status,
                'remember_me' => $remember_me,
            ])->header('access_token','Bearer'. ' '. $user->createToken(env("APP_NAME"))->accessToken);
        }
    }





    

        /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/user/logout",
     *   summary="logout",
     *   produces={"application/json"},
     *   tags={"COMMON USER APIS"},
     *   @SWG\Parameter(
     *    name="Authorization",
     *    in="header",
     *    required=true,
     *    description = "Enter Token",
     *    type="string"
     *   ),
     *
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    /*
     * Main Function for logout
     * @param Request $request (data_type)
     * @return type (status, success/error)
     */
    public function logout(Request $request)
    {
        $value = $request->bearerToken();
        $id = (new Parser())->parse($value)->getHeader('jti');
        $token = $request->user()->tokens->find($id);

        if ($token->revoke()) {
            $data = \Config::get('success.logout');
        } else {
            $data = \Config::get('error.error');
        }
        return Response::json($data);
    }



     /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/user/forgotPassword",
     *   summary="user forgotPassword",
     *   produces={"application/json"},
     *   tags={"COMMON USER APIS"},
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "forgotpassword",
     *     @SWG\Schema(ref="#/definitions/forgotpassword"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="forgotpassword",
     *     allOf={
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="email",
     *                 type="string",
     *             )
     *         )
     *     }
     * )
     *
     */
    /*
     * Function : function for forgot password
     * Input:
     * Output: success, error
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $requested_data = $request->all();
        $forgot_password_code = $this->getverificationCode();
        $check_user = User::where(['email' => $requested_data['email']])->first();

        if (!empty($check_user)) {
            User::where('email', $requested_data['email'])->update(array('forgot_password_token' => $forgot_password_code)); // update the record in the DB.

            $email = $requested_data['email'];
            $admin_email = Config::get('variable.ADMIN_EMAIL');
            $frontend_url = Config::get('variable.FRONTEND_URL');
            $name = $check_user->name;
            Mail::send('emails.users.forgot_password', [
                "data" => array("name" => $name,
                    "frontend_url" => $frontend_url,
                    "forgot_password" => $forgot_password_code,
                    "email" => $email,
                )], function ($message) use ($email, $admin_email) {
                $message->from($admin_email, config('variable.SITE_NAME'));
                $message->to(trim($email), config('variable.SITE_NAME'))->subject(config('variable.SITE_NAME') . ' : Forgot password');
            });
            if (count(Mail::failures()) > 0) {
                $data = \Config::get('error.error_sending_email');
                $data['data'] = (object) [];
                return Response::json($data);
            } else {

                $data = \Config::get('success.send_forgot_password_link');
                $data['data'] = (object) [];
                return Response::json($data);

            }
        } else {
            $data = \Config::get('error.send_forgot_password_link');
            $data['data'] = (object) [];
            return Response::json($data);
        }
    }



     /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
   *   path="/user/changePassword",
     *   summary="Change Password",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *   tags={"COMMON USER APIS"},
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "changepassword",
     *     @SWG\Schema(ref="#/definitions/changepassword"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="changepassword",
     *     allOf={
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="old_password",
     *                 type="string",
     *             )
     *         ),
     *        @SWG\Schema(
     *             @SWG\Property(
     *                 property="new_password",
     *                 type="string",
     *             )
     *         ),
     *        @SWG\Schema(
     *             @SWG\Property(
     *                 property="new_password_confirmation",
     *                 type="string",
     *             )
     *         )
     *     }
     * )
     *
     */
    /*
     * Function : function for forgot password
     * Input:
     * Output: success, error
     */

    public function changePassword(UserChangePasswordRequest $request)
    {
        $oldPassword = $request->old_password;
        $newPassword = $request->new_password;
        $hashedPassword = Auth::user()->password;
        if (Hash::check($oldPassword, $hashedPassword)) {
            $user = User::find(Auth::user()->id)
                ->update(
                    ['password' => Hash::make($newPassword)]
                );
            if ($user) {
                $data = \Config::get('success.update_password');
                $data['data'] = (object) [];
                return Response::json($data);
            } else {
                $data = \Config::get('error.update_password');
                $data['data'] = (object) [];
                return Response::json($data);

            }
        } else {
            $data = \Config::get('error.wrong_old_password');
            $data['data'] = (object) [];
            return Response::json($data);

        }
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *   path="/user/getProfile",
     *   summary="getProfile",
     *   produces={"application/json"},
     *   tags={"Users"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *  @SWG\Parameter(
     *     name="user_id",
     *     in="query",
     *     required=false,
     *     type="string",
     *     description="user id"
     *   ), 
     *  @SWG\Parameter(
     *     name="only_user",
     *     in="query",
     *     required=false,
     *     type="number",
     *     description="if you want only uer data"
     *   ), 
     * 
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
       * )
     *
     */

    function getProfile(Request $request){
        $requested_data = $request->all();
        if(isset($requested_data['user_id'])){
            $user_id  =  $requested_data['user_id'];
        } else{
            $user_id  = $requested_data['data']['id'];
        }
        $data = User::select('id','role_id', 'name','gender', 'email', 'dob', 'city', 'description', 'profile_image','slug', 'termsandcondition_version', 'phone', 'privacy_version', 'device_type', 'device_id' ,'current_login','last_login', 'status', 'push_notification_status','created_at', 'updated_at','synopsis','synopsis as synopsis_full','dob','description','about_writer','followers_count','favourite_count','total_revenue','subscription_plan_id','stories_count','avg_rating','monthly_revenue')->where('id',$user_id);
        
        if(isset($requested_data['only_user'])){
        } else{
            $data = $data->with([
                'getUserGenre.getGenre' => function ($q) {
                    $q;
                },
                'getStories.getEpisodes',
                'getFavAuthers.getUser'  => function ($q) {
                    $q->select('name','id','profile_image','avg_rating');
                },      
                ]);
        }
       $data = $data->first();

       $res = \Config::get('success.get');
       $res['unread_notification_count'] = Notification::where('receiver_id',$requested_data['data']['id'])->where('is_read',0)->count();
       if(isset($requested_data['user_id'])){
            $res['is_following'] = Follower::where('followed_by',$requested_data['data']['id'])->where('followed_to',$requested_data['user_id'])->count();
            $res['is_fav'] = Favourite::where('favourite_by',$requested_data['data']['id'])->where('user_id',$requested_data['user_id'])->count();
        } 
      $res['story_approved_status'] = Story::where('user_id',$user_id )->where('status',1)->count();
      $res['episode_approved_status'] = Episode::where('user_id',$user_id )->where('status',1)->count();
      $res['followers_count'] = Follower::where('followed_to',$user_id )->count();
      $res['data'] = empty(!$data)?$data:(object) [];
      return Response::json($res);
    }

    

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *   path="/user/getComments",
     *   summary="Users",
     *   produces={"application/json"},
     *   tags={"Users"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *  @SWG\Parameter(
     *     name="story_id",
     *     in="query",
     *     required=true,
     *     type="string",
     *     default="1",
     *     description="story id"
     *   ),  
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
       * )
     *
     */

    function getComments(GetCommentsRequest $request){
        $requested_data = $request->all();
        $data =Comment::where(['story_id'=>$requested_data['story_id']])->with(
            ['getStory' => function ($q) use($requested_data) {
            $q;
        },
        'getUser'=> function ($q) use($requested_data) {
            $q->select('name','id','profile_image','role_id','is_writer_reader');
            },
        ]);

         $info  = $data->orderBy('created_at', 'desc')->paginate(\Config::get('variable.page_per_record'))->toArray();
        $res['status'] = \Config::get('success.code');
        $res['message']=$info['total']>0?"success.":"No Record Found.";
        $res['data'] =$info['total']>0?$info:(object) [];
         return Response::json($res);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/user/postComment",
     *   summary="postComment",
     *   produces={"application/json"},
     *   tags={"Users"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/postComment"),
     *   ),
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "story_id",
     *     @SWG\Schema(ref="#/definitions/postComment"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="postComment",
     *     allOf={
     *         @SWG\Schema(
     *            @SWG\Property(
     *                 property="id",
     *                 type="number",
     *             ),
     *            @SWG\Property(
     *                 property="comment",
     *                 type="string",
     *             ),
     *         )
     *     }
     * )
     *
     */




    public function postComment(postCommentRequest $request)
    {
       $requested_data = $request->all();

       $id = $requested_data['data']['id'];
       // save comments
        Comment::Create(['story_id'=>$requested_data['id'],'commented_by'=>$requested_data['data']['id'],'created_at'=>time(),'comment'=>$requested_data['comment']]);
        
        
        // 4. send notification to commented users
        $requested_data['story_id'] = $requested_data['id'];
        $user_ids = Comment::where('story_id',$requested_data['id'])->pluck('commented_by')->toArray();
        
        $to_remove = array($id);

        // send notification 
        if(count($to_remove)){
            $user_ids = array_diff($user_ids, $to_remove);
        }
       
        
        $requested_data['type'] = 4;
        $this->sendNotification($requested_data,$user_ids);
        
        
        $data['status'] = \Config::get('success.code');
        $data['message']="Comment posted successfully.";
        $data['data'] = (object) [];
        return Response::json($data);
    }
       
    
  
 /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/user/followUnfollow",
     *   summary="followUnfollow",
     *   produces={"application/json"},
     *   tags={"Users"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/followUnfollow"),
     *   ),
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = " ",
     *     @SWG\Schema(ref="#/definitions/followUnfollow"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="followUnfollow",
     *     allOf={
     *         @SWG\Schema(
     *            @SWG\Property(
     *                 property="user_id",
     *                 type="number",
     *             ),
     *         )
     *     }
     * )
     *
     */


    public function followUnfollow(FollowUnfollowRequest $request)
    {
       $requested_data = $request->all();       
         // if already follow
        $check = Follower::where('followed_to',$requested_data['user_id'])->where('followed_by',$requested_data['data']['id'])->count();
        if(!$check){
            $msg= 'User followed successfully.';
            Follower::Create(['followed_to'=>$requested_data['user_id'],'followed_by'=>$requested_data['data']['id'],'created_at'=>time()]); 
            User::where('id',$requested_data['data']['id'])->increment('followers_count');
        } else {
            $msg= 'User Unfollowed successfully.';
            Follower::where(['followed_to'=>$requested_data['user_id'],'followed_by'=>$requested_data['data']['id']])->delete();
            User::where('id',$requested_data['data']['id'])->decrement('followers_count');
        }
        $data['status'] = \Config::get('success.code');
        $data['message']=$msg;
        $data['data'] = (object) [];
        return Response::json($data);
    }


        /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\GET(
     *   path="/user/getMyFollowers",
     *   summary="getMyFollowers",
     *   produces={"application/json"},
     *   tags={"Users"},
     *   @SWG\Parameter(
     *    name="Authorization",
     *    in="header",
     *    required=true,
     *    description = "Enter Token",
     *    type="string"
     *   ),
     *
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */

     /**
     *  Function : Get followers
     *  Output : Success 
     */

    public function getMyFollowers(Request $request) {
        $requested_data = $request->all();
        #Get User data 
        $my_follwers = Follower::where('followed_to', $requested_data['data']['id'])
        ->with([
            'getUser' => function($query) {
                $query->select('name','id','profile_image');
              }])
        ->orderBy('created_at', 'desc')->paginate(\Config::get('variable.page_per_record'))->toArray();
        
        #Send response here
        $res['status'] = \Config::get('success.code');
        $res['message']=$my_follwers['total']>0?"success.":"No Record Found.";
        $res['data'] =$my_follwers['total']>0?$my_follwers:(object) [];
        return Response::json($res);
    }



      
 /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/user/favouriteUnfavourite",
     *   summary="favouriteUnfavourite",
     *   produces={"application/json"},
     *   tags={"Users"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/favouriteUnfavourite"),
     *   ),
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "type :1=>user, 2=story ",
     *     @SWG\Schema(ref="#/definitions/favouriteUnfavourite"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="favouriteUnfavourite",
     *     allOf={
     *         @SWG\Schema(
     *            @SWG\Property(
     *                 property="id",
     *                 type="number",
     *             ),
     *         ),
     *         @SWG\Schema(
     *            @SWG\Property(
     *                 property="type",
     *                 type="number",
     *             ),
     *         )
     *     }
     * )
     *
     */


    public function favouriteUnfavourite(FavouriteUnfavouriteRequest $request)
    {
       $requested_data = $request->all();       
         // if already favourite
         if($requested_data['type']==1){
                $check = Favourite::where('user_id',$requested_data['id'])->where('favourite_by',$requested_data['data']['id'])->where('type',1)->count();
                if(!$check){
                    $msg= 'User marked as favourite.';
                    Favourite::Create(['user_id'=>$requested_data['id'],'favourite_by'=>$requested_data['data']['id'],'created_at'=>time(),'type'=>1]); 
                    User::where('id',$requested_data['data']['id'])->increment('favourite_count');
                } else {
                    $msg= 'User marked as Unfavourite.';
                    Favourite::where(['user_id'=>$requested_data['id'],'type'=>1])->delete();
                    User::where('id',$requested_data['data']['id'])->decrement('favourite_count');
                }
        } else{
            $check = Favourite::where('story_id',$requested_data['id'])->where('favourite_by',$requested_data['data']['id'])->where('type',2)->count();
                if(!$check){
                    $msg= 'Story marked as favourite.';
                    Favourite::Create(['story_id'=>$requested_data['id'],'favourite_by'=>$requested_data['data']['id'],'created_at'=>time(),'type'=>2]); 
                    Story::where('id',$requested_data['id'])->increment('favourite_count');
                } else {
                    $msg= 'Story marked as Unfavourite.';
                    Favourite::where(['story_id'=>$requested_data['id'],'type'=>2])->delete();
                    Story::where('id',$requested_data['id'])->decrement('favourite_count');
                }
        }
        $data['status'] = \Config::get('success.code');
        $data['message']=$msg;
        $data['data'] = (object) [];
        return Response::json($data);
    }



    

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *   path="/user/getNotification",
     *   summary="getNotification",
     *   produces={"application/json"},
     *   tags={"Users"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *  @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     required=true,
     *     type="string",
     *     default="1",
     *     description="page number"
     *   ), 
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
       * )
     *
     */


    function getNotification(Request $request){
        $requested_data = $request->all();
        $data  =Notification::select('sender_id', 'receiver_id', 'type','created_at','type as msg','story_id')->where('receiver_id',$requested_data['data']['id']);
        $data->with([
            'sender' => function ($q) {
            $q->select('name','id','email');
        },
        'receiver' => function ($q) {
            $q->select('name','id','email');
        },
        'story' => function ($q) {
            $q->select('name','id');
        }
         ]);
        $data  = $data->orderBy('created_at', 'desc')->paginate(\Config::get('variable.page_per_record'))->toArray();
        $res['message']=count($data['data'])?"success":"No Record Found";
        $res['status'] =\Config::get('success.code');
        $res['data'] = count($data['data'])?$data:(object) [];
        return Response::json($res);
    }



        /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *   path="/user/getSubscriptionPlans",
     *   summary="getSubscriptionPlans",
     *   produces={"application/json"},
     *   tags={"Users"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
       * )
     *
     */

    function getSubscriptionPlans(Request $request){
        $requested_data = $request->all();
        $data = \Config::get('success.get');     # success results
        $data['my_plan'] = $requested_data['data']['subscription_plan_id'];
        $data['new_plan'] = $requested_data['data']['new_subscription_plan'];
        $data['data']  = DB::table('subscription_plans')->orderBy('created_at', 'desc')->paginate(\Config::get('variable.page_per_record'))->toArray();
        return Response::json($data);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/user/pushNotification",
     *   summary="pushNotification",
     *   produces={"application/json"},
     *   tags={"COMMON USER APIS"},
     *   @SWG\Parameter(
     *    name="Authorization",
     *    in="header",
     *    required=true,
     *    description = "Enter Token",
     *    type="string"
     *   ),
     *
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    /*
     * Main Function for on , off pushNotification
     * @param Request 
     * @return type (status, success/error)
     */
    public function pushNotification(Request $request)
    {
        $requested_data = $request->all();
        $status = $requested_data['data']['push_notification_status']==1?0:1;
        User::where('id',$requested_data['data']['id'])->update(['push_notification_status'=>$status]);
        $data['message'] = $status==1?'Push Notification ON successfully.':'Push Notification OFF successfully.';
        $data['status'] =\Config::get('success.code');
        $data['data'] = (object) [];
        return Response::json($data);
    }

    


    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\GET(
     *   path="/user/getMyActivities",
     *   summary="getMyActivities",
     *   produces={"application/json"},
     *   tags={"COMMON USER APIS"},
     *   @SWG\Parameter(
     *    name="Authorization",
     *    in="header",
     *    required=true,
     *    description = "Enter Token",
     *    type="string"
     *   ),
     *
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */

     /**
     *  Function : Get activity logs
     *  Output : Success 
     */

    public function getMyActivities(Request $request) {
        $requested_data = $request->all();
        #Get User data 
        $registered_on = ActivityLog::where('user_id', $requested_data['data']['id'])->where('meta_key', '=', 'sign_up')->orderBy('created_at', 'desc')->where('status', '1')
                        ->get()->toArray();
        $last_login = ActivityLog::where('user_id', $requested_data['data']['id'])->where('status', '1')->where('meta_key', 'last_login')
                        ->get()->toArray();
        $last_profile = ActivityLog::where('user_id',$requested_data['data']['id'])->where('status', '1')->where('meta_key', 'Profile')
                        ->get()->toArray();
        $profile_completion = ActivityLog::where('user_id', $requested_data['data']['id'])->where('status', '1')->where('meta_key', 'profile_completion')
                        ->get()->toArray();                
        $privacyData = Page::where('status', '1')->select('id','version','meta_key','name','created_at')->where('meta_key', 'privacy_policy')
                        ->get()->toArray();
        $verify = ActivityLog::where('user_id', $requested_data['data']['id'])->where('status', '1')->where('meta_key', 'verification')
                        ->get()->toArray();
        $termsData = Page::where('status', '1')->where('meta_key', 'term')
                        ->select('id','version','meta_key','name','created_at')
                        ->get()->toArray();
        
        #Send response here
        $data = \Config::get('success.success_record_found');     # success results
        $data['registered_on'] = $registered_on;
        $data['last_login'] = $last_login;
        $data['last_profile'] = $last_profile;
        $data['verify'] = $verify;
        $data['privacy_data'] = $privacyData;
        $data['terms_data'] = $termsData;
        $data['profile_completion'] = $profile_completion;
        $data['user_data'] =$requested_data['data'];
        return Response::json($data);
    }


   
    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\POST(
     *   path="/user/deleteUser",
     *   summary="deleteUser",
     *   produces={"application/json"},
     *   tags={"COMMON USER APIS"},
     *   @SWG\Parameter(
     *    name="Authorization",
     *    in="header",
     *    required=true,
     *    description = "Enter Token",
     *    type="string"
     *   ),
     *  @SWG\Parameter(
     *     name="message",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description="message"
     *   ), 
     *
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */

    public function deleteUser(deleteUserRequest $request){
        $requested_data = $request->all();
        $user = storage_path() . '/app/public/users/'.$requested_data['data']['id'];
        if (file_exists(storage_path() . '/app/public/users/'.$requested_data['data']['id'])) {
            rmdir($user);
        }
        $requested_data['data']['message'] = $requested_data['message'];
        $requested_data['user_data'] = $requested_data['data'];
        #send mail 
        $this->sendMailDeleteUser($requested_data);
        User::where('id',$requested_data['data']['id'])->delete();
        $data['status'] =\Config::get('success.code');
        $data['data'] = (object) [];
        $data['message']='User deleted successfully.';
        return Response::json($data);
    }


  /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/user/uploadImage",
     *   summary="uploadImage",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Users"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *  @SWG\Parameter(
     *     name="image",
     *     in="formData",
     *     required=true,
     *     type="file"
     *   ),  
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */


    public function uploadImage(Request $request)
    {
        $requested_data = $request->all();
        // check file extension
        $image_url=  $requested_data['data']['profile_image_real'];
        $allowed = ['jpeg', 'png', 'jpg'];
        if(isset($_FILES['image']['name'])){
            $filename = $_FILES['image']['name'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                if (!in_array($ext, $allowed)) {
                    $data = \Config::get('error.invalid_file_format');
                    $data['data'] = (object) [];
                    return Response::json($data);
                }
                // check file size
                if ($_FILES['image']['size'] > 2097152) {
                    $data = \Config::get('error.file_too_large');
                    $data['data'] = (object) [];
                    return Response::json($data);
                }
                //upload file
                $del_file = $image_url;
                $dynamic_name = time() . '-' . $this->imageDynamicName() . '.' . $ext;
                $image = $request->file('image')->storeAs('public/users/'.$requested_data['data']['id'].'', $dynamic_name);
                if ($image) {
                    $image_name = explode('/', $image);
                    $image_url = $this->userImageVersions($image_name[3],$requested_data);
                    //if ($saved_Image) {
                } 

                // unlink image
                if(empty(!$del_file)) {
                    $main_image = storage_path() . '/app/public/users/'.$requested_data['data']['id'].''.'/'.$del_file;
                    //delete existing image
                    if (file_exists($main_image)) {
                        unlink($main_image);
                    }
                }
        }
        unset($requested_data['image']);
        User::where('id',$requested_data['data']['id'])->update(['profile_image'=>$image_url]);
        $data['status'] = \Config::get('success.code');
        $data['message'] = 'saved';
        $data['data'] = (object) [];
        return Response::json($data);

    }

    // help $res['status'] =\Config::get('success.code');
   // $res['data'] = count($data['data'])?$data:(object) [];
        /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/user/saveRating",
     *   summary="saveRating",
     *   produces={"application/json"},
     *   tags={"Users"},
     *   @SWG\Parameter(
     *    name="Authorization",
     *    in="header",
     *    required=true,
     *    description = "Enter Token",
     *    type="string"
     *   ),
     *  @SWG\Parameter(
     *     name="rating",
     *     in="query",
     *     required=true,
     *     type="number",
     *     default="1",
     *     description="rating"
     *   ), 
     *  @SWG\Parameter(
     *     name="type",
     *     in="query",
     *     required=true,
     *     type="string",
     *     default="1",
     *     description="rating type:1=>user,2=>episode"
     *   ), 
     *  @SWG\Parameter(
     *     name="id",
     *     in="query",
     *     required=true,
     *     type="string",
     *     default="1",
     *     description="episode_id or user_id"
     *   ), 
     *  @SWG\Parameter(
     *     name="story_id",
     *     in="query",
     *     required=false,
     *     type="string",
     *     default="1",
     *     description="story_id is rquired in case of rate to episode"
     *   ), 
     *
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    /*
     * Main Function for save rating
     * @param Request 
     * @return type (status, success/error)
     */
    public function saveRating(SaveRatingRequest $request)
    {
        $requested_data = $request->all();
        if($requested_data['type']==1){
            Rating::Create(['type'=>$requested_data['type'],'by_user_id'=>$requested_data['data']['id'],'to_user_id'=>$requested_data['id'],'rating'=>$requested_data['rating'],'created_at'=>time()]);
            $users = Rating::where('to_user_id',$requested_data['id'])->where('type',1)->count();
            $rating = Rating::where('to_user_id',$requested_data['id'])->where('type',1)->sum('rating');
            $total_rate = $users*5;
            $rating = ($rating/$total_rate)*5;
            User::where('id',$requested_data['id'])->update(['avg_rating'=>$rating]);
        } else{
            Rating::Create(['type'=>$requested_data['type'],'story_id'=>$requested_data['story_id'],'episode_id'=>$requested_data['id'],'by_user_id'=>$requested_data['data']['id'],'rating'=>$requested_data['rating']]);
            
            // save episode rating
            $story = Rating::where('episode_id',$requested_data['id'])->where('type',2)->count();
            $rating = Rating::where('episode_id',$requested_data['id'])->where('type',2)->sum('rating');
            $total_rate = $story *5;
            $rating = ($rating/$total_rate)*5;
            Episode::where('id',$requested_data['id'])->update(['rating'=>$rating]);
            
            // save story rating

            $story = Rating::where('story_id',$requested_data['story_id'])->where('type',2)->count();
            $rating = Rating::where('story_id',$requested_data['story_id'])->where('type',2)->sum('rating');
            $total_rate = $story *5;
            $rating = ($rating/$total_rate)*5;

            Story::where('id',$requested_data['story_id'])->update(['rating'=>$rating]);
        }
        $data['status'] = \Config::get('success.code');
        $data['messsge'] = 'Rating saved successfully.';
        $data['data'] =(object) [];
        return Response::json($data);
    }






    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/user/switchAccount",
     *   summary="switchAccount",
     *   produces={"application/json"},
     *   tags={"Users"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string",
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */




    public function switchAccount(Request $request)
    {
       $requested_data = $request->all();
        User::where('id',$requested_data['data']['id'])->update(['is_writer_reader'=>1]);
        $data = \Config::get('success.account_switch');     # success results
        return Response::json($data);
    }




    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/user/editProfile",
     *   summary="edit profile",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Users"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *  @SWG\Parameter(
     *     name="image",
     *     in="formData",
     *     required=false,
     *     type="file"
     *   ),  
     *  @SWG\Parameter(
     *     name="synopsis",
     *     in="formData",
     *     required=false,
     *     type="file"
     *   ),    
     * @SWG\Parameter(
     *     name="name",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "name",
     *   ), 
     * @SWG\Parameter(
     *     name="dob",
     *     in="formData",
     *     required=false,
     *     type="number",
     *     description = "date of birth in timestemp",
     *   ), 
     *   @SWG\Parameter(
     *     name="about",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "about",
     *   ),  
     *   @SWG\Parameter(
     *     name="about_writer",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "about writer",
     *   ), 
     *   @SWG\Parameter(
     *     name="gender",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "gender",
     *   ), 
     * @SWG\Parameter(
     *     name="phone",
     *     in="formData",
     *     required=false,
     *     type="number",
     *     description = "phone number",
     *   ),   
     * @SWG\Parameter(
     *     name="user_ids",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "faviourite artist ids",
     *   ),  
     * @SWG\Parameter(
     *     name="genre_ids",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "genres ids",
     *   ),  
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */

    public function editProfile(EditProfileRequest $request)
    {
       $requested_data = $request->all();
       $id = $requested_data['data']['id'];
       $requested_data['updated_at'] = time();
       $profile_image = $requested_data['data']['profile_image_real'];
       $synopsis = $requested_data['data']['synopsis'];

       $about = isset($requested_data['about'])?$requested_data['about']:$requested_data['data']['about'];
       $phone = isset($requested_data['phone'])?$requested_data['phone']:$requested_data['data']['phone'];
       $requested_data['dob']= isset($requested_data['dob'])?$requested_data['dob']:$requested_data['data']['dob'];
       $requested_data['gender']= isset($requested_data['gender'])?$requested_data['gender']:$requested_data['data']['gender'];
       $requested_data['about_writer']= isset($requested_data['about_writer'])?$requested_data['data']['about_writer']:'';


        // check file extension
        $allowed = ['jpeg', 'png', 'jpg'];
        if(isset($_FILES['image']['name'])){
                 $filename = $_FILES['image']['name'];
                 $ext = pathinfo($filename, PATHINFO_EXTENSION);
                if (!in_array($ext, $allowed)) {
                    $data['message'] = 'Please upload a file in valid video format.';
                    $data['status'] = \Config::get('error.code');;
                    $data['data'] = (object) [];
                    return Response::json($data);
                }
                // check file size
                if ($_FILES['image']['size'] > 2097152) {
                    $data['message'] = 'Image size must be less then 2 MB.';
                    $data['status'] = \Config::get('error.code');;
                    $data['data'] = (object) [];
                    return Response::json($data);
                }
                
                //upload file
                $dynamic_name = time() . '-' . $this->imageDynamicName() . '.' . $ext;
                $image = $request->file('image')->storeAs('public/users/'.$requested_data['data']['id'].'', $dynamic_name);
                $del_image = $profile_image;
                if ($image) {
                    $image_name = explode('/', $image);
                    $profile_image = $this->userImageVersions($image_name[3],$requested_data);
                    //if ($saved_Image) {
                } 

                    // unlink image
                if(empty(!$profile_image)) {
                    $main_image = storage_path() . '/app/public/users/'.$requested_data['data']['id'].''.'/'.$del_image;
                    $thumb_image = storage_path() . '/apppublic/users/'.$requested_data['data']['id'].'/thumb/'.$del_image;
                    //delete existing image
                    if (file_exists($main_image)) {
                        unlink($main_image);
                    }
                    if (file_exists($thumb_image )) {
                        unlink($thumb_image);
                    }
                } 
        }
         


        // upload synopsis
        if(isset($_FILES['synopsis']['name'])){
            $allowed = ['pdf'];
            if(isset($_FILES['synopsis']['name'])){
                    $filename = $_FILES['synopsis']['name'];
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                if (!in_array($ext, $allowed)) {
                    $data['message'] = 'Please upload a file in valid text format.';
                    $data['status'] = \Config::get('error.code');;
                    $data['data'] = (object) [];
                    return Response::json($data);
                }
            }
            $del_file = $synopsis;
            $path='app/public/synopsis/user';
            $synopsis =$this->uploadFile($requested_data,$path);
            // unlink file
            if(empty(!$del_file)) {
                $main = storage_path() . '/app/public/synopsis/user'.$del_file;
                //delete existing image
                if (file_exists($main)) {
                    unlink($main);
                }
            } 
          }


        User::where('id',$id)->update(['name'=>$requested_data['name'],'profile_image'=>$profile_image,'phone'=>$phone,'description'=>$about,'dob'=>$requested_data['dob'],'gender'=>$requested_data['gender'],'synopsis'=>$synopsis,'about_writer'=>$requested_data['about_writer']]);
        
        // if genre exists 
        if(isset($requested_data['genre_ids'])){
            $this->addGenres($requested_data);
        }

        // if user ids exists 
        if(isset($requested_data['user_ids'])){
            $this->addFavAuther($requested_data);
        }

        $profile_update_activity_log = ActivityLog::insert(array(
            array('user_id' => $requested_data['data']['id'],
                'meta_key' => 'profile_update',
                'meta_value' => time(),
                'status' => 1,
                'created_at' => time(),
                'updated_at' => time(),
            )));
    
        $data['status'] = \Config::get('success.code');;
        $data['message'] = 'Profile updated successfully.';
        $data['data'] = (object) [];
        return Response::json($data);
    }




    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/user/saveStripe",
     *   summary="saveStripe",
     *   produces={"application/json"},
     *   tags={"Users"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string",
     *   ),
     * @SWG\Parameter(
     *     name="stripe_id",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "stripe_id",
     *   ), 
     * @SWG\Parameter(
     *     name="plan_id",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "plan_id",
     *   ), 
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */




    public function saveStripe(SaveStripeRequest $request)
    {
       $requested_data = $request->all();
       $insert=[];

        if(isset($requested_data['plan_id']) && empty(!$requested_data['plan_id'])){
                $insert = ['subscription_plan_id'=>$requested_data['plan_id']];
        }

        if(isset($requested_data['stripe_id']) && empty(!$requested_data['stripe_id'])){
            $insert['stripe_id']=$requested_data['stripe_id'];
        }
        User::where('id',$requested_data['data']['id'])->update();
        $data['status'] = \Config::get('success.code');     # success results
        $data['message'] = 'Detail saved successfully.';     # success results
        return Response::json($data);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/user/share",
     *   summary="share episode and story",
     *   produces={"application/json"},
     *   tags={"Users"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string",
     *   ),
     * @SWG\Parameter(
     *     name="story_id",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "story_id",
     *   ), 
     * @SWG\Parameter(
     *     name="episode_id",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "episode_id",
     *   ), 
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */




    public function share(CommonStoryRequest $request) {
       $requested_data = $request->all();
        if(isset($requested_data['episode_id']) && empty(!$requested_data['episode_id'])){
            Episode::where(['story_id'=>$requested_data['story_id'],'id'=>$requested_data['episode_id']])->increment('share_count');
            $msg="Episode shared successfully.";
        } else{
            $msg="Story shared successfully.";
            Story::where(['id'=>$requested_data['story_id']])->increment('share_count');
        }
        $data['status'] = \Config::get('success.code');     # success results
        $data['message'] = $msg;
        return Response::json($data);
    }




    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/user/addCard",
     *   summary="add card",
     *   produces={"application/json"},
     *   tags={"Users"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string",
     *   ),
     * @SWG\Parameter(
     *     name="card_no",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "card_no",
     *   ), 
     * @SWG\Parameter(
     *     name="card_holder_name",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "card_holder_name",
     *   ), 
     * @SWG\Parameter(
     *     name="month",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "month",
     *   ), 
     * @SWG\Parameter(
     *     name="year",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "year",
     *   ), 
     * @SWG\Parameter(
     *     name="cvv",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "cvv",
     *   ), 
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */


    public function addCard(AddCardRequest $request) {
         $requested_data = $request->all();
         $requested_data['user_id'] = $requested_data['data']['id']; 
         $requested_data['created_at'] = time(); 
         unset($requested_data['data']);
         BankDetail::Create($requested_data);
         $data['status'] = \Config::get('success.code');     # success results
         $data['message'] = 'Card saved Successfully.';
         return Response::json($data);
     }


    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\GET(
     *   path="/user/getMyCards",
     *   summary="getMyCards",
     *   produces={"application/json"},
     *   tags={"Users"},
     *   @SWG\Parameter(
     *    name="Authorization",
     *    in="header",
     *    required=true,
     *    description = "Enter Token",
     *    type="string"
     *   ),
     *
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */

     /**
     *  Function : Get my card
     *  Output : Success 
     */

    public function getMyCards(Request $request) {
        $requested_data = $request->all();
        #Get User data 
        $cards = BankDetail::where('user_id', $requested_data['data']['id'])->orderBy('created_at', 'desc')
                        ->get()->toArray();
        #Send response here
        $data['status'] = \Config::get('success.code');     # success results
        $data['message'] =  count($cards)>0?'Card fetched Successfully.':"No Record Found.";
        $data['data'] = $cards;
        return Response::json($data);
    }


 


}
