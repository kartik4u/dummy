<?php

/*
 * Controller Name  : UniversityController
 * Author           : Narinder
 * Created Date     : 3-04-2018
 * Description      : This controller ferform university related oprations (get university,update university data..)
 */

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\UserDetail;
use Illuminate\Http\Request;
use Response;
use Validator;
use Hash;
use Auth;
use JWTAuth;
use Session;
use App\Config;
use App\User;
use File;
use Image;
use DB;
use App\Course;
use App\CourseDetail;
use App\CourseProspect;
use App\PageLog;

class UniversityController extends Controller {

    public $table_user = 'users';

    /*
     * Function: function to get all the university data
     */

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/frontend/university/get-university-profile",
     *   summary="University Profile",
     *   produces={"application/json"},
     *   tags={"University"},
     *   @SWG\Parameter(
     *     name="token",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
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
    public function getUniversityProfile(Request $request) {
        $requested_data = $request->all();
        $user_id = $requested_data['data']['id'];

        # query to get university profile data
        $university_data = User ::where('id', $user_id)->with(['getProfile' => function($q) {
                        $q;
                    },
                    'address' => function($q) {
                        $q;
                    },
                ])
                ->first();

        if ($university_data) {
            $data = \Config::get('success.success_data');     # success  results
            $data['data'] = $university_data;
            return Response::json($data);
        } else {
            $data = \Config::get('success.no_record');      # no results
            $data['data'] = $university_data;
            return Response::json($data);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/frontend/university/edit-university-profile",
     *   summary="edit and update university profile",
     *   produces={"application/json"},
     *   tags={"University"},
     *   @SWG\Parameter(
     *     name="token",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="universityname",
     *     in="formData",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="about_university",
     *     in="formData",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="gender",
     *     in="formData",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="date_of_origin",
     *     in="formData",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="about_scholarships",
     *     in="formData",
     *     required=true,
     *     description = "Scholarships..",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="phone",
     *     in="formData",
     *     required=false,
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="website",
     *     in="formData",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="logo",
     *     in="formData",
     *     required=false,
     *     type="file"
     *   ),
     *   @SWG\Parameter(
     *     name="banner",
     *     in="formData",
     *     required=false,
     *     type="file"
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function editUniversityProfile(Request $request) {
        $requested_data = $request->all();

        $rule = ['about_scholarships' => 'max:2000', 'about_university' => 'max:2000', 'universityname' => ['required', 'max:50'], 'date_of_origin' => ['required'], 'website' => ['required'], 'logo' => 'image|mimes:jpeg,jpg,png|max:1048', 'banner' => 'image|mimes:jpeg,jpg,png|max:1048'
        ];
        $messages = [
            'required' => 'Please enter  :attribute.',
            'universityname.max' => ':attribute must not exceed 50 characters.',
            'about_scholarships.max' => ':attribute must not exceed 2000 characters.',
            'about_university.max' => ':attribute must not exceed 2000 characters.',
            'logo.mimes' => 'Only JPEG and PNG  files are allowed.',
            'logo.max' => 'image size should not be more then 1048kb.',
            'banner.mimes' => 'Only JPEG and PNG  files are allowed.',
            'banner.max' => 'image size should not be more then 1048kb.'
        ];

        $validator = Validator::make($requested_data, $rule, $messages);  #Check validation
        $data_error = array();   #Array for send data in response
        if ($validator->fails()) { #Check validation pass or fail
            return Response::json((new Controller)->validatedata($validator));
        }  #Request Parameters in single array


        if ($requested_data['data']['name'] != $requested_data['universityname']) {
            //check if university already exists
            $check_email_used = User::where('id', '!=', $requested_data['data']['id'])->where('name', '=', $requested_data['universityname'])->count();
            if ($check_email_used) {
                return Response::json(\Config::get('error.error_alerady_exists_university'));
            }
        }

        // upload logo
        if ($request->file('logo')) {
            $logo_name = $this->uploadLogo($request);
            $requested_data['logo'] = $logo_name;
        }

        // upload banner
        if ($request->file('banner')) {
            $banner_name = $this->uploadBanner($request);
            $requested_data['banner'] = $banner_name;
        }

        // check email has changed
        if ($requested_data['data']['email'] != $requested_data['email']) {

            //check if changed email is already used by another user
            $check_email_used = User::where('id', '!=', $requested_data['data']['id'])->where('email', '=', $requested_data['email'])->count();
            if ($check_email_used) {
                return Response::json(\Config::get('error.error_alerady_exists_user_email'));
            }

            // send varification mail
            $this->sendVerificationMail($requested_data);
            $this->logout($requested_data);
        }
        // update profile
        $response = $this->updateProfile($requested_data);
        return $response;
    }

    // upload logo

    function uploadLogo($request) {
        $requested_data = $request->all();
        // get user data
        $user_data = UserDetail::where('user_id', $requested_data['data']['id'])->select('id', 'user_id', 'logo')->first();
        // delete logo
        if ($user_data) {
            if (!empty($user_data->logo)) {
                if (file_exists(public_path('/images/university/logo/') . $user_data->logo)) {
                    unlink('./images/university/logo/' . $user_data->logo);
                }
            }
        }
        $image = $request->file('logo');
        $dynamic_name = $result = (new Controller)->imageDynamicName();
        $input['imagename'] = time() . '-' . $dynamic_name . '.' . $image->getClientOriginalExtension();  #Image Dynamic Name
        $destinationPath = public_path('/images/university/logo');      #Image Path
        $image->move($destinationPath, $input['imagename']);  #Move file into folder
        $data['file_name'] = $input['imagename'];
        $this->userImageVersions($data['file_name'], 'logo');
        return $data['file_name'];
        #success message when logo image uploaded successfully
    }

    // upload banner

    function uploadBanner($request) {
        $requested_data = $request->all();
        // get user data
        $user_data = UserDetail::where('user_id', $requested_data['data']['id'])->select('id', 'user_id', 'logo', 'banner_image')->first();
        // delete logo
        if ($user_data) {
            if (!empty($user_data->banner_image)) {
                if (file_exists(public_path('/images/university/banner/') . $user_data->banner_image)) {
                    unlink('./images/university/banner/' . $user_data->banner_image);
                }
            }
        }
        $image = $request->file('banner');
        $dynamic_name = $result = (new Controller)->imageDynamicName();
        $input['imagename'] = time() . '-' . $dynamic_name . '.' . $image->getClientOriginalExtension();  #Image Dynamic Name
        $destinationPath = public_path('/images/university/banner');      #Image Path
        $image->move($destinationPath, $input['imagename']);  #Move file into folder
        $data['file_name'] = $input['imagename'];
        $this->userImageVersions($data['file_name'], 'banner');
        return $data['file_name'];
        #success message when banner image uploaded successfully
    }

    /*
     * Function: function to  update profile
     * Input:
     * Output: success/failed
     */

    function updateProfile($requested_data) {
        // data will  insert into user table
        $profile_input['name'] = isset($requested_data['universityname']) ? $requested_data['universityname'] : '';

        // data will insert into userdetail table
        $input['about_scholarship'] = isset($requested_data['about_scholarships']) ? $requested_data['about_scholarships'] : '';
        $input['about_university'] = isset($requested_data['about_university']) ? $requested_data['about_university'] : '';
        $input['phone_number'] = isset($requested_data['phone']) ? $requested_data['phone'] : NULL;
        $input['website'] = isset($requested_data['website']) ? $requested_data['website'] : '';
        $input['date_of_origin'] = isset($requested_data['date_of_origin']) ? $requested_data['date_of_origin'] : '';

        // if banner is uploading
        if (isset($requested_data['banner'])) {
            $input['banner_image'] = $requested_data['banner'];
        }

        // if logo is uploading
        if (isset($requested_data['logo'])) {
            $input['logo'] = $requested_data['logo'];
        }

        $user_profile = UserDetail::updateOrCreate(['user_id' => $requested_data['data']['id']]);
        $profile_input['email'] = $requested_data['email'];
        $profile_input['updated_at'] = time();
        $update = $user_profile->update($input);
        User::where('id', $requested_data['data']['id'])->update($profile_input);
        #profile update entry insertion
        $profile_update = PageLog::updateOrCreate(['title' => 'profile-update', 'user_id' => $requested_data['data']['id']], ['page_id' => 0, 'created_at' => time(), 'status' => 1, 'updated_at' => time()]);

        if (trim($requested_data['email']) != $requested_data['data']['email']) {
            return Response::json(\Config::get('success.success_email_changed'));
        } else {
            return Response::json(\Config::get('success.success_update_university'));
        }
    }

    /* function to send a verification email */

    private function sendVerificationMail($data) {
        $user_detail = User::find($data['data']['id']);

        // get varification code
        $varification_code = $this->getverificationCode();
        //update verification code

        $profile_input['updated_at'] = time();
        $profile_input['status'] = 0;
        $profile_input['verification_code'] = $varification_code;
        User::where('id', $data['data']['id'])->update($profile_input);

        // update university data
        $response = $this->updateProfile($data);

        //update user data
        //User::where('id',$data['data']['id'])->update(['verification_code'=>$varification_code,'status'=>0]);
        #data to send in email
        $email_array = array(
            'server_url' => \Config::get('variable.SERVER_URL'),
            'to' => $data["email"],
            'from' => \Config::get('variable.ADMIN_EMAIL'),
            'from_name' => \Config::get('variable.MAIL_FROM_NAME'),
            'subject' => 'Change Email : Verification',
            'view' => 'email.change_email',
            'verification_code' => $varification_code,
            'name' => $user_detail->name,
            'type' => $user_detail->type == 3 ? 'student' : 'university',
            'frontend_url' => \Config::get('variable.FRONTEND_URL')
        );
        #Send Verification Email
        return $this->sendEmail($email_array);  #Send Verification Email
    }

    private function logout($requested_data) {
        $check_user = User::where('auth_token', $requested_data['data']['auth_token'])->where('id', $requested_data['data']['id'])->where('role_id', $requested_data['data']['role_id'])->first();
        if (!empty($check_user)) {
            $logout_user = $check_user->update(['auth_token' => '']);
        }
    }

    # function for image conversion of user profile  images in thumb/medium

    public function userImageVersions($name, $type) {
        if ($type == 'logo') {
            $main_dir = public_path() . '/images/university/logo';
            $thumb_dir = public_path() . '/images/university/logo/thumb';
            $medium_dir = public_path() . '/images/university/logo/medium';
        } else {
            $main_dir = public_path() . '/images/university/banner';
            $thumb_dir = public_path() . '/images/university/banner/thumb';
            $medium_dir = public_path() . '/images/university/banner/medium';
        }
        if (!file_exists($main_dir)) {
            mkdir($main_dir, 0777);
            chmod($main_dir, 0777);
        }
        if (!file_exists($thumb_dir)) {
            mkdir($thumb_dir, 0777);
            chmod($thumb_dir, 0777);
        }
        if (!file_exists($medium_dir)) {
            mkdir($medium_dir, 0777);
            chmod($medium_dir, 0777);
        }

        if (file_exists($main_dir . '/' . $name)) {
            Image::make($main_dir . '/' . $name, array(
                'width' => 150,
                'height' => 100,
                'crop' => true,
                'grayscale' => false
            ))->save($thumb_dir . '/' . $name);

            Image::make($main_dir . '/' . $name, array(
                'width' => 260,
                'height' => 200
            ))->save($medium_dir . '/' . $name);
            chmod($thumb_dir . '/' . $name, 0777);
            chmod($medium_dir . '/' . $name, 0777);
        }
        return true;
    }

    /*
     * Function: function to get all the university data
     */

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/frontend/university/get-course-data",
     *   summary="Cource Data",
     *   produces={"application/json"},
     *   tags={"University"},
     *   @SWG\Parameter(
     *     name="token",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
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
    public function getCoursesData(Request $request) {
        $requested_data = $request->all();
        $user_id = $requested_data['data']['id'];
        $requested_data['page'] = isset($requested_data['page']) ? $requested_data['page'] : 1;
        $page_record = \Config::get('variable.page_per_record');
        # query to get courses data
        $courses_data = Course::select('id', 'course_name', 'duration_of_course', 'city', 'degree_level', 'visa_type', 'fee', 'average_income', 'drop_out_rate', 'description', 'next_intake as intake')->with(['getDegreeLevels' => function($q) {
                                $q;
                            }])
                        ->where('user_id', $requested_data['data']['id'])->where('status', 1)
                        ->orderBy('created_at', 'desc')->paginate($page_record)->toArray();

        if (count($courses_data['data'])) {
            $data = \Config::get('success.success_data');     # success  results
            $data['data'] = $courses_data;
            return Response::json($data);
        } else {
            $data = \Config::get('success.no_record');      # no results
            $data['data'] = $courses_data;
            return Response::json($data);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *  @SWG\Post(
     *   path="/frontend/university/delete-course",
     *   summary="delete Course",
     *   produces={"application/json"},
     *   tags={"University"},
     *   @SWG\Parameter(
     *     name="token",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="id",
     *     in="formData",
     *     required=true,
     *     description="Enter course id here to delete specific course",
     *     type="integer",
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     * )
     *
     */
    public function deleteCourse(Request $request) {

        $requested_data = $request->all();

        $rule = ['id' => 'required'];
        $messages = ['required' => 'Please enter address :attribute'];

        $validator = Validator::make($requested_data, $rule, $messages);  #Check validation
        if ($validator->fails()) { #Check validation pass or fail
            return Response::json((new Controller)->validatedata($validator));
        }

        if (isset($requested_data['id'])) { #delete university course
            $result = DB::table('courses')->where('id', $requested_data['id'])->delete();
            DB::table('course_details')->where('course_id', $requested_data['id'])->delete();
            DB::table('course_prospects')->where('course_id', $requested_data['id'])->delete();
            if ($result == 1) {
                return Response::json(\Config::get('success.delete_course'));
            } else {
                return Response::json(\Config::get('error.incorrect_id'));
            }
        }
    }

    /*
     * Function: function to get all the university data
     */

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/frontend/university/get-degree-data",
     *   summary="Degree Data",
     *   produces={"application/json"},
     *   tags={"University"},
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function getDegreeData(Request $request) {
        $requested_data = $request->all();
        $page_record = \Config::get('variable.page_per_record');
        # query to get degree data
        $degree_data = DB::table('degree_levels')->get()->toArray();
        if (count($degree_data)) {
            $data = \Config::get('success.success_data');     # success  results
            $data['data'] = $degree_data;
            return Response::json($data);
        } else {
            $data = \Config::get('success.no_record');      # no results
            $data['data'] = $degree_data;
            return Response::json($data);
        }
    }

}
