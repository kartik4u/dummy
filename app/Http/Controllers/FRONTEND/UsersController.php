<?php

namespace App\Http\Controllers\FRONTEND;

use App\Http\Controllers\Controller;
use App\Interfaces\Frontend\UserInterface;
use App\Models\ActivityLog;
use App\User;
use Config;
use Illuminate\Http\Request;
use Redirect;
use Validator;
use PDF;
use App\Jobs\SignupJob;
use App\Models\CvInterest;
use App\Models\ViewProfile;
use App\Models\UserActivity;
use App\Models\ReportedUser;
use App\Models\ActivityLike;
use App\Models\CvLike;
use App\Models\UserCv;
use App\Models\MyFeed;
use App\Models\Page;
use App\Models\Follower;
use App\Models\CommentTag;
use App\Models\UserJob;
use App\Models\JobTag;
use App\Models\CvComment;
use App\Models\BlockedUser;
use App\Models\EducationDetail;
use App\Models\ActivityComment;
use App\Models\Notification;

class UsersController extends Controller implements UserInterface
{

    public function verifyUser($key)
    {
       $user = User::where('verify_token', $key)->first();


       if($user)
        {
            if($user->secondary_email == ''){
                $user->update([
                    'status' => '1',
                    'verify_token' => '',
                ]);
                $verification_activity_log = ActivityLog::insert(array(
                    array('user_id' => $user->id,
                        'meta_key' => 'verification',
                        'meta_value' => time(),
                        'status' => 1,
                        'created_at' => time(),
                        'updated_at' => time(),
                    )));
                    return view('verify');
            }
            else{
                $user->update([
                    'status' => '1',
                    'verify_token' => '',
                    'email' => $user->secondary_email,
                    'secondary_email' => '',
                    
                ]);
                return view('verify');
                }
        } 
            else 
            {
                return view('error_verify');
            }
    }
    

    /*
     * Main Function to show reset password form
     * @param Request $request (token)
     * @return type (reset page)
     */

    public function showResetForm(Request $request, $token = null)
    {
        $tok = User::where('forgot_password_token', $token)->first();

        if ($tok) {
            return view('reset')->with(
                ['token' => $token, 'email' => $request->email]
            );

        } else {
            return view('token_expire')->with(
                ['token' => $token, 'email' => $request->email]
            );
        }
    }

    public function resetPassword(Request $request)
    {
        $requested_data = $request->all();
        #Validate Data
        $rule = [
            'password' => 'required|min:6|confirmed',
        ];

        $messages = ['password.confirmed' => 'Password and Confirm password should match'];
        #Check validation
        $validator = Validator::make($requested_data, $rule, $messages);
        #Array for send data in response
        $data_error = array();
        #Check validation pass or fail
        if ($validator->fails()) {
            #if validation fail print error messages
            $error = $validator->errors()->all();
            foreach ($error as $key => $errors):
                $data_error['status'] = '400';
                $data_error['message'] = $errors;
                $data_error['data'] = (object) [];
            endforeach;

            #Return data in json
            $website_url = \Config::get('variable.Website_url');
            $error = $data_error['message'];

            return Redirect::to($website_url . 'reset-password/' . $requested_data['forgot_password_token'] . '?x_section=519M26519M26149964074149519M26149964074964074&error=' . $error);
        } else {
            # get data of user from perticuller user
            $check_user_access = User::where(['forgot_password_token' => $requested_data['forgot_password_token']])->first();
            $password = bcrypt($requested_data['password']);
            # update data
            $update_user = User::where('id', $check_user_access->id)
                ->update(['password' => $password, 'verify_token' => '', 'forgot_password_token' => '']);

            return view('reset_success');

        }
    }

          
    /*
    * Function : share job
    */
    public function shareJob($id = null){
        if(!empty($id)){
            return view('sharelinks',['id' => $id]);
        }        
       
    }

    /*
    * Function : download resume
    */
    public function downloadResume($id = null){
        $data = User::select('id','role_id', 'name', 'email','gender','dob', 'city', 'description', 'profile_image','slug', 'termsandcondition_version', 'phone', 'privacy_version', 'device_type', 'device_id' ,'current_login','last_login', 'status', 'push_notification_status','created_at', 'updated_at')->where('id',$id);
       
       $data = $data->with(['skills' => function ($q) {
             $q;
        },'educationDetail' => function ($q) {
            $q;
        }
        ,'employmentDetail' => function ($q) {
                $q;
        }
        ])->first();
        // echo '<pre>';
        // dd($data); 
       // return  view('resume',['userdata' => $data]);
        $name = $data->name.'_resume.pdf';
        $pdf = PDF::loadView('resume',['userdata' => $data]);
        return $pdf->download($name);
    }



}
