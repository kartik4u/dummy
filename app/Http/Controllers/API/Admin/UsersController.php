<?php
namespace App\Http\Controllers\API\Admin;

// Load Model
use App\Interfaces\AdminUserInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\Admin\User\UserChangePasswordRequest;
use App\Http\Requests\Admin\User\ChangeStatusRequest;
use App\Http\Requests\Admin\User\DeleteUserRequest;
use App\Http\Requests\UserResetPasswordRequest;
use App\Http\Requests\Admin\User\UserDetailRequest;
use App\Http\Traits\AdminTrait;
use App\Role;
use App\User;
use App\Models\Story;
use Carbon\Carbon;
use Config;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Image;
use Lcobucci\JWT\Parser;
use Mail;
use Response;
use DB;

class UsersController extends Controller implements AdminUserInterface
{   
    use AdminTrait;


    public function validateForgotPasswordExpiry(Request $request)
    {

        $data = [];
        $requested_data = $request->all();
        $response = User::where('forgot_password_token', $requested_data['token'])->first();
        if ($response && $response->forgot_token_expiry != '') {
            $from = Carbon::create(date('Y', $response->forgot_token_expiry), date('m', $response->forgot_token_expiry), date('d', $response->forgot_token_expiry), date('h', $response->forgot_token_expiry), date('i', $response->forgot_token_expiry), date('s', $response->forgot_token_expiry));
            $to = Carbon::now();
            if ($to->diffInSeconds($from) > 86400) {
                $data['message'] = 'This link has been expired';
                $data['status'] = 400;
            } else {
                $data['status'] = 200;
                $data['message'] = '';
            }
        } else {
            $data['message'] = 'This link has been expired';
            $data['status'] = 400;
        }
        return Response::json($data);
    }

    /*
     * Function : function to login admin
     * Input: email,password
     * Output: success, error
     */
    public function login(UserLoginRequest $request)
    {
         $requested_data = $request->all();
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')], request('remember_me'))) {
            $user = Auth::user();
           // return $user->role_id;//  Role::where('name', 'admin')->first()->id;
            if ($user->role_id != Role::where('name', 'admin')->first()->id) {
                Auth::logout();
                $data =  \Config::get('admin_error.unauthorised');
                return Response::json($data);
            }
            switch ($user->status) {
                case 0:
                    Auth::logout();
                    $data = \Config::get('admin_error.not_varified');
                    $data['user_unverified'] = true;
                    $data['email'] = request('email');
                    return Response::json($data);
                    break;
            }
            $remember_me = isset($requested_data['remember_me']) ? $requested_data['remember_me'] : false;
            return Response::json([
                'status' => 200,
                'role_id' => $user->role_id,
                'profile_status' => $user->status,
                'remember_me' => $remember_me,
                'name' => $user->name,
            ])->header('admin_access_token', $user->createToken(env("APP_NAME"))->accessToken);
        } else {
            $data['message'] = 'Invalid email/password';
            $data['status'] = 400;
            return Response::json($data);
        }
    }    


    /*
     * Function : function to logout admin
     * Input: 
     * Output: success, error
     */

    public function logout(Request $request)
    {
        $value = $request->bearerToken();
        $id = (new Parser())->parse($value)->getHeader('jti');
        $token = $request->user()->tokens->find($id);
        if ($token->revoke()) {
            $data = \Config::get('admin_success.logout');
        } else {
            $data = \Config::get('admin_error.error');
        }
        return Response::json($data);
    }



    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $requested_data = $request->all();
        $forgot_password_code = $this->getverificationCode();

        $check_user = User::where(['email' => $requested_data['email'], 'role_id' => Role::where('name', 'admin')->first()->id])->first();

        if (!empty($check_user)) {
            User::where('email', $requested_data['email'])->update(array('forgot_password_token' => $forgot_password_code, 'forgot_token_expiry' => time() + 86400)); // update the record in the DB.
           $email = $requested_data['email'];
            $admin_email = Config::get('variable.ADMIN_EMAIL');
            $frontend_url = Config::get('variable.ADMIN_URL');
            
            Mail::send('emails.admin.forgot_password', [
                "data" => array("name" =>  $check_user->name,
                    "frontend_url" => $frontend_url,
                    "forgot_password" => $forgot_password_code,
                    "email" => $email,
                )], function ($message) use ($email, $admin_email) {
                $message->from($admin_email, config('variable.SITE_NAME'));
                $message->to($email, config('variable.SITE_NAME'))->subject(config('variable.SITE_NAME') . ' : Forgot password');
            });
 
            if (count(Mail::failures()) > 0) {
                $data = \Config::get('admin_error.mail_error');
                return Response::json($data);
            } else {
                $data = \Config::get('admin_success.send_forgot_password_link');
                return Response::json($data);
            }
        } else {
            $data=  \Config::get('admin_error.unauthorised');
            return Response::json($data);
        }
    }


    /*
     * Function: function to reset password
     * Input:forgot_password_token,password
     * Output:success/fail
     */

    public function resetPassword(UserResetPasswordRequest $request)
    {
        $requested_data = $request->all();
        $updated = User::where('forgot_password_token', $requested_data['forgot_password_token'])
            ->update(['password' => bcrypt($requested_data['password']), 'forgot_password_token' => ""]);
        if ($updated) {
            return Response::json(\Config::get('admin_success.reset_password'));
        } else {
            return Response::json(\Config::get('admin_error.password_update_error'));
        }
    }




    /*
     * Function: function to change login user password
     * Input: old_password , new_password , new_password_confirmation
     * Output:success/fail
     */

    public function changePassword(UserChangePasswordRequest $request)
    {
        $requested_data = $request->all();
        $old_password = $requested_data['old_password'];
        $hashedPassword = Auth::user()->password;

        if (Hash::check($old_password, $hashedPassword)) {
            $updated = User::find($requested_data['data']["id"])->update(['password' => Hash::make($requested_data['password'])]);
            if ($updated) {
                return Response::json(\Config::get('admin_success.update_password'));
            } else {
                return Response::json(\Config::get('admin_error.password_update_error'));
            }
        } else {
            return Response::json(\Config::get('admin_error.invalid_old_password'));
        }
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/admin/getUsers",
     *   summary="get user data",
     *   produces={"application/json"},
     *   tags={"ADMIN USER APIS"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *  @SWG\Parameter(
     *     name="role_id",
     *     in="query",
     *     required=false,
     *     type="number",
     *     description="user role (0 for all users)"
     *   ), 
     *  @SWG\Parameter(
     *     name="search",
     *     in="query",
     *     required=false,
     *     type="string",
     *     description="search by name and email"
     *   ), 
     *  @SWG\Parameter(
     *     name="user_type",
     *     in="query",
     *     required=false,
     *     type="number",
     *     description="user type (status field in table(active,inactive,not varified))"
     *   ), 
     *  @SWG\Parameter(
     *     name="start_date",
     *     in="query",
     *     required=false,
     *     type="number",
     *     description="filter by start date in timestemp"
     *   ), 
     *  @SWG\Parameter(
     *     name="end_date",
     *     in="query",
     *     required=false,
     *     type="number",
     *     description="filter by end date in timestemp"
     *   ), 
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )  
     *
     */

    /*
     * Function : function to get user data
     * Input: id
     * Output: success, error
     */

    public function getUsers(Request $request)
    { 
        $requested_data = $request->all();
        
        $requested_data['role_id'] = isset($requested_data['role_id'])?$requested_data['role_id']:0;

        // check user role
        if(empty(!$requested_data['role_id'])){
            $query = User::where('role_id', '=',$requested_data['role_id']);
        } else{
            $query = User::where('role_id','!=',Role::where('name', 'admin')->first()->id)->select('name','created_at')->where('role_id', '!=','');
        }

        // check user status
        if($requested_data['user_status']>=0){
            
            $query = $query->where('status',$requested_data['user_status']);
        } 

        // seaching if required
        if (isset($requested_data['search']) && !empty($requested_data['search'])) {
            $query = $query->where(function($q) use($requested_data) {
                $q->whereRaw("( REPLACE(name,' ','')  LIKE '%" . str_replace(' ', '', $requested_data['search']) . "%')")
                ->orWhereRaw("( REPLACE(email,' ','')  LIKE '%" . str_replace(' ', '', $requested_data['search']) . "%')");
            });
        }

         // date filter
        if (isset($requested_data['start_date']) && empty(!$requested_data['end_date'])) { 
            $query = $query->where('created_at', '>=', $requested_data['start_date'])->where('created_at', '<=', $requested_data['end_date']+86399);
        }


         // other filter (active/inactive/unvarified)
         if (isset($requested_data['user_type']) && empty(!$requested_data['user_type'])) { 
            $query = $query->where('status', $requested_data['user_type']);
        }

        $query = $query->select('id', 'name', 'email', 'status', 'created_at','role_id')
            ->orderBy('created_at', 'desc')->paginate(config('variable.page_per_record'))->toArray();
        $data = \Config::get('admin_success.record_found');
        $data['data'] = $query;
       // $data['user_roles'] = Role::where('name','!=','admin')->get();
   
        return Response::json($data);
    }

    // get all roles
    
    public function getAllRoles(Request $request)
    {
        $requested_data = $request->all();
        $data = \Config::get('admin_success.record_found');
        $data['data'] = Role::where('name','!=','admin')->paginate(config('variable.page_per_record'))->toArray();
        return Response::json($data);
    }


     /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
   *   path="/admin/changeStatus",
     *   summary="change user status (active inactive)",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *   tags={"ADMIN USER APIS"},
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "change active inactive status,status=>(1->active,2->inactive),id=>(user_id)",
     *     @SWG\Schema(ref="#/definitions/changeStatus"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="changeStatus",
     *     allOf={
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="status",
     *                 type="number",
     *             )
     *         ),
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="id",
     *                 type="number",
     *             )
     *         )
     *     }
     * )
     *
     */

    /*
     * Function: function to change user status
     * Input: status,user_id
     * Output:success/fail
     */



    public function changeStatus(ChangeStatusRequest $request)
    {
        $data = [];
        $requested_data = $request->all();
          $users =  $this->ActiveInactiveUser($requested_data);
        if ($users) {
            if ($requested_data['status'] == 1) {
                $data = \Config::get('admin_success.activate_user');
            } else {
                $data = \Config::get('admin_success.deactivate_user');
            }
        } else {
            $data = \Config::get('admin_error.error');
        }
        return Response::json($data);
    }



    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\POST(
     *   path="/admin/deleteUser",
     *   summary="deleteUser",
     *   produces={"application/json"},
     *   tags={"ADMIN USER APIS"},
     *   @SWG\Parameter(
     *    name="Authorization",
     *    in="header",
     *    required=true,
     *    description = "Enter Token",
     *    type="string"
     *   ),
     *   tags={"ADMIN USER APIS"},
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "id=> deleted user id",
     *     @SWG\Schema(ref="#/definitions/deleteUser"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="deleteUser",
     *     allOf={ 
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="user_id",
     *                 type="number",
     *             )
     *         )
     *     }
     * )
     *
     */

     /*
     * Function: function to delete user
     * Input:user_id
     * Output:success/fail
     */

    public function deleteUser(DeleteUserRequest $request){
        $requested_data = $request->all();
        $user = storage_path() . '/app/public/users/'.$requested_data['user_id'];
        if (file_exists(storage_path() . '/app/public/users/'.$requested_data['user_id'])) {
            rmdir($user);
        }
        User::where('id',$requested_data['user_id'])->delete();
        $data = \Config::get('admin_success.user_deleted');
        $data['data'] = (object) [];
        return Response::json($data);
    }



    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/admin/userDetail",
     *   summary="get single user detail",
     *   produces={"application/json"},
     *   tags={"ADMIN USER APIS"},
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
     *     required=true,
     *     type="number",
     *     description="user id"
     *   ), 
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )  
     *
     */



   /*
     * Function: function to get user detail
     * Input: user_id
     * Output:
     */

    public function userDetail(UserDetailRequest $request) {
        #Validations
        $requested_data = $request->all(); 
        
        # ger user detail 
        $requested_data['user_id'] = isset($requested_data['user_id'])?$requested_data['user_id']:$requested_data['data']['id'];
        $usersData = User::where('id',$requested_data['user_id'])->select('id','role_id', 'name', 'email', 'password', 'dob', 'address', 'city', 'description','postal_code', 'profile_image', 'profile_submited_at','slug', 'termsandcondition_version', 'phone', 'privacy_version', 'forgot_password_token',  'device_type', 'device_id','current_login','last_login','verify_token' ,'status', 'push_notification_status','created_at', 'updated_at','remember_token','profile_image as admin_image');
        $usersData = $usersData->first();

        #Send response here
        if ($usersData) {
            $data = \Config::get('admin_success.record_found');
            $data['data'] = $usersData;
            return Response::json($data);               
        } else {
            return Response::json(\Config::get('admin_error.error'));            
        }
    }



    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/admin/dashboard",
     *   summary="get dashboard",
     *   produces={"application/json"},
     *   tags={"ADMIN USER APIS"},
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
     *
     */



   /*
     * Function: function to get dashboard
     * Input: 
     * Output:
     */

    public function dashboard(Request $request) {
        #Validations
        $requested_data = $request->all(); 
        $usersData['total_authers'] = User::where('role_id','=',3)->count();
        $usersData['total_readers'] = User::where('role_id','=',2)->count();
        $usersData['total_panding_stories'] = Story::where('status','=',0)->count();
        $usersData['total_stories'] = Story::where('status','=',1)->count();
        $usersData['total_revenue'] = User::sum('total_revenue');
        #Send response here
        $data = \Config::get('admin_success.record_found');
        $data['data'] = $usersData;
        return Response::json($data);               
    }





    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/admin/download-users",
     *   summary="Download Users",
     *   produces={"application/json"},
     *   tags={"Admin Download Users"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string"
     *   ),
     *  @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     required=false,
     *     type="number",
     *     description="page"
     *   ), 
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * )
     *
     */
    public function downloadUsers(Request $request) {
        $requested_data = $request->all();
        $server_url = \Config::get('variable.SERVER_URL');
        $excel_limit = \Config::get('variable.excel_limit_per_file');
        
      
        $requested_data['role_id'] = isset($requested_data['role_id'])?$requested_data['role_id']:0;

        // check user role
        if(empty(!$requested_data['role_id'])){
            $query = User::where('role_id', '=',$requested_data['role_id']);
        } else{
            $query = User::where('role_id','!=',Role::where('name', 'admin')->first()->id)->select('name','created_at')->where('role_id', '!=','');
        }

        // check user status
        if($requested_data['user_status']>=0){
            
            $query = $query->where('status',$requested_data['user_status']);
        } 

        // seaching if required
        if (isset($requested_data['search']) && !empty($requested_data['search'])) {
            $query = $query->where(function($q) use($requested_data) {
                $q->whereRaw("( REPLACE(name,' ','')  LIKE '%" . str_replace(' ', '', $requested_data['search']) . "%')")
                ->orWhereRaw("( REPLACE(email,' ','')  LIKE '%" . str_replace(' ', '', $requested_data['search']) . "%')");
            });
        }

         // date filter
        if (isset($requested_data['start_date']) && empty(!$requested_data['end_date'])) { 
            $query = $query->where('created_at', '>=', $requested_data['start_date'])->where('created_at', '<=', $requested_data['end_date']+86399);
        }


         // other filter (active/inactive/unvarified)
         if (isset($requested_data['user_type']) && empty(!$requested_data['user_type'])) { 
            $query = $query->where('status', $requested_data['user_type']);
        }

        $user_data = $query->select('id', 'name', 'email', 'status', 'created_at','role_id')
            ->orderBy('created_at', 'asc')->paginate($excel_limit)->toArray();
      
       // $user_data = $user_data->paginate($excel_limit)->toArray();
        #Check university not empty here and greater then 0
        if(isset($user_data['data']) && !empty($user_data['data'])){
            #Set path here and define file name
            $public_path = public_path() . "/csv/users/";

            #Remove old from directory
            $files = glob($public_path . '/*.csv');
            if (!empty($files)) {
                foreach ($files as $file) {
                    unlink($file);
                }
            }
            
            #Set file name here
            $file = strtotime(date('Y-m-d H:i:s')) . "_users.csv";
            $filename = $public_path . $file;
            $handle = fopen($filename, 'w+');
            #Set all heading here for file
            fputcsv($handle, array('ID', 'Name', 'Email', 'Type','Created At',
                'Phone Number', 'Country', 'City', 'Date of birth',
                'Description'));
                #Set data here 
                foreach ($user_data['data'] as $key => $row) {
                    $id = @$row['id'];
                    $name = @$row['name'];
                    $email = @$row['email'];
                    $type = @$row['role_id'] ==3 ? 'employer':'employee';
                    $created_at = date('d-m-Y',@$row['created_at']);
                    $phone_number = @$row['phone_number'] ? $row['phone_number'] :'';
                    $country = @$row['country'] ? $row['country']: '' ;
                    $city = @$row['city'] ? $row['city'] : '' ;
                    $date_of_birth = date('d-m-Y',@$row['dob']);
                    $about = @$row['description']? @$row['description']:'';

                    fputcsv($handle, array($id,
                        $name,
                        $email,
                        $type,
                        $created_at,
                        $phone_number,
                        $country,
                        $city,
                        $date_of_birth,
                        $about
                    ));
                }
                #return file name here
                fclose($handle);
                    $headers = array(
                        'Content-Type' => 'text/csv',
                    );
                    $data['status'] = 200;
                    $data['file'] = $server_url.'/csv/users/'.$file;
                    $data['per_page'] = $excel_limit ;
                    $data['total'] =$user_data['total'];
                    $data['current'] =$user_data['current_page'];
                    return response()->json($data);

        }else{
            $data['status'] = 200;
            $data['file'] = '';
            $data['per_page'] = $excel_limit ;
            $data['total'] =0;
            $data['current'] =1;
            return Response::json($data); 
        }
    }














}
