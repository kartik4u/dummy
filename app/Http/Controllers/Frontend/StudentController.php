<?php

/*
 * Controller Name  : StudentController
 * Author           : Prabhat
 * Author Contact   : prabhat.thakur@ignivasolutions.com
 * Created Date     : 23-03-2018
 * Description      : This controller use manage student
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
use File;
use Image;
use App\PageLog;

class StudentController extends Controller {

    /**
     *  @SWG\Get(
     *   path="/frontend/student/get-profile-details",
     *   summary="Get profile details",
     *   produces={"application/json"},
     *   tags={"Student"},
     *   @SWG\Parameter(
     *     name="token",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string"
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function getProfileDetails(Request $request) {
        $requested_data = $request->all();
        #get user details    

        $user_details = User::with(['userDetail' => function($query) {
                                $query->select(['id', 'user_id', 'dob', 'gender', 'certificates', 'achievements', 'qualification', 'profile_image']);
                            }])
                                ->where('id', $requested_data['data']['id'])->first(['id', 'email', 'name', 'role_id'])->toArray();
                if (!empty($user_details)) {
                    $response['status'] = 200;
                    $response['data'] = $user_details;
                } else {
                    $response = \Config::get('error.no_record_found');
                }
                return Response::json($response);
            }

            /**
             * @return \Illuminate\Http\JsonResponse
             *
             *  @SWG\Post(
             *   path="/frontend/student/save-profile-details",
             *   summary="Get profile details",
             *   produces={"application/json"},
             *   tags={"Student"},
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
             *     @SWG\Schema(ref="#/definitions/saveProfileDetails"),
             *   ),
             *   @SWG\Response(response=200, description="Success"),
             *   @SWG\Response(response=400, description="Failed"),
             * )
             * @SWG\Definition(
             *     definition="saveProfileDetails",
             *     allOf={
             *         @SWG\Schema(                
             *             @SWG\Property(
             *                 property="name",
             *                 type="string",
             *             ),
             *              @SWG\Property(
             *                 property="email",
             *                 type="string",
             *             )
             *         )
             *     }
             * ) 
             *
             */
            public function saveProfileDetails(Request $request) {
                $requested_data = $request->all();
                $rule = ['email' => 'required|email|unique:users,email,' . $requested_data['id'], 'name' => 'required'];
                $messages = [
                    'email.email' => 'Please enter a valid email id',
                    'email.unique' => 'Email is already in use. Please try another one'
                ];
                $validator = Validator::make($requested_data, $rule, $messages);  #Check validation       
                if ($validator->fails()) { #Check validation pass or fail
                    return Response::json($this->validateData($validator));
                }
                #save user data
                $saved_data = $this->saveUserData($requested_data);
                
                #profile update entry insertion
                $profile_update = PageLog::updateOrCreate(['title' => 'profile-update','user_id' => $requested_data['data']['id']], ['page_id' => 0, 'created_at' => time(), 'status' => 1, 'updated_at' => time()]);
                
                if ($saved_data['is_verification'] == 1) {
                    #send verification mail if email changed
                    $send_verification_mail = $this->sendVerificationMail($requested_data);
                    User::where('id', $requested_data['data']['id'])->update(['auth_token' => '']);
                    $response = \Config::get('success.success_email_changed');
                } else {
                    $response = \Config::get('success.success_profile_updated');
                }
                return Response::json($response);
            }

            /* save student data */

            private function saveUserData($data) {
                unset($data['user_detail']['profile_image']);
                $return = [];
                # check if email has been changed or not
                if (trim($data['email']) != $data['data']['email']) {
                    $data['verification_code'] = $this->getverificationCode();
                    $data['status'] = 0;
                }
                $data['updated_at'] = time();
                $user = $get_user = User::find($data['data']['id']);
                #save user table data
                $update_details = $get_user->update($data);
                #save user-detail table data
                $get_user_detail = UserDetail::where('user_id', $data['data']['id'])->first();
                $data['user_detail']['updated_at'] = time();
                if (!empty($get_user_detail)) {
                    $user->userDetail()->update($data['user_detail']);
                } else {
                    $data['user_detail']['id'] = $data['data']['id'];
                    $data['user_detail']['created_at'] = time();
                    $user->userDetail()->create($data['user_detail']);
                }
                $return['is_verification'] = trim($data['email']) != $data['data']['email'] ? 1 : 0;
                return $return;
            }

            /* function to send a verification email */

            private function sendVerificationMail($data) {
                $user = User::find($data['data']['id']);
                #data to send in email
                $email_array = array(
                    'server_url' => \Config::get('variable.SERVER_URL'),
                    'to' => trim($data["email"]),
                    'from' => \Config::get('variable.ADMIN_EMAIL'),
                    'from_name' => \Config::get('variable.MAIL_FROM_NAME'),
                    'subject' => \Config::get('variable.EMAIL_VERIFICATION'),
                    'view' => 'email.email_verify',
                    'verification_code' => $user->verification_code,
                    'name' => $user->name,
                    'type' => 'student',
                    'frontend_url' => \Config::get('variable.FRONTEND_URL')
                );
                #Send Email Verification Email
                return $this->sendEmail($email_array);
            }

            /**
             * @return \Illuminate\Http\JsonResponse
             *
             *  @SWG\Post(
             *   path="/frontend/student/change-profile-image",
             *   summary="Change Profile Image",
             *   produces={"application/json"},
             *   tags={"Student"},
             *   @SWG\Parameter(
             *     name="token",
             *     in="header",
             *     required=true,
             *     description = "Enter Token",
             *     type="string",
             *   ),
             *   @SWG\Parameter(
             *     name="profile_image",
             *     in="formData",
             *     required=true,
             *     description = "Choose Profile Picture",
             *     type="string",
             *   ),
             *   @SWG\Response(response=200, description="Success"),
             *   @SWG\Response(response=400, description="Failed"),
             * )
             */
            public function changeProfileImage(Request $request) {
                $requested_data = $request->all();
                #Validate data
                $rule = ['profile_image' => 'required|image|max:1000|mimes:jpeg,jpg,png'];
                $messages = [
                    'profile_image.max' => 'The image may not be greater than 1MB.',
                    'profile_image.image' => 'The image must be a image.',
                    'profile_image.mimes' => 'The image must be in jpg,jpeg,png format.'
                ];

                $validator = Validator::make($requested_data, $rule, $messages);  #Check validation       
                if ($validator->fails()) { #Check validation pass or fail
                    return Response::json($this->validateData($validator));
                }

                # get profile image of student
                $check_image = UserDetail::where('user_id', $requested_data['data']['id'])->first(['*', 'profile_image as profile_pic']);
                if (!empty($check_image)) {
                    #if profle image exists , then delete the previous image from folders
                    if (isset($check_image->profile_pic) && !empty($check_image->profile_pic)) {
                        $this->unlinkImage($check_image->profile_image);
                    }
                }

                $file = $requested_data['profile_image'];
                $main_dir = public_path() . '/images/student/profile_images';
                if (!file_exists(public_path() . '/images/student')) {
                    mkdir(public_path() . '/images/student', 0777);
                    chmod(public_path() . '/images/student', 0777);
                }
                if (!file_exists($main_dir)) {
                    mkdir($main_dir, 0777);
                    chmod($main_dir, 0777);
                }
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                if ($extension == 'jpg' || $extension == 'JPG' || $extension == 'png' || $extension == 'PNG' || $extension == 'JPEG' || $extension == 'jpeg' || $extension == 'GIF' || $extension == 'gif') {
                    $dynamic_name = $this->imageDynamicName();
                    $picture = time() . '-' . $dynamic_name . '.' . $extension;
                    $destinationPath = public_path() . '/images/student/profile_images';
                    #save new image to folder
                    $file->move($destinationPath, $picture);
                    if (file_exists($destinationPath . '/' . $picture)) {
                        @chmod($destinationPath . '/' . $picture, 0777);
                        # function for image conversion of student profile  images in thumb/medium
                        $this->productImageVersions($picture);
                        $picture_uploaded[] = $picture;
                        $is_image_uploaded = 1;
                    }
                }

                if (!empty($check_image)) {
                    #update data
                    $update_image = $check_image->update(['profile_image' => $picture, 'updated_at' => time()]);
                } else {
                    $create = [];
                    $create['user_id'] = $requested_data['data']['id'];
                    $create['profile_image'] = $picture;
                    $create['created_at'] = time();
                    $create['updated_at'] = time();
                    UserDetail::create($create);
                }
                $server_url = \Config::get('variable.SERVER_URL');
                $response = \Config::get('success.success_image_updated');
                $response['profile_image_path'] = $server_url . 'images/student/profile_images/medium/' . $picture;
                return Response::json($response);
            }

            # function for unlink student profile images in thumb/medium

            private function unlinkImage($image) {
                $mediaxy = $image;
                $tmp = explode('/', $mediaxy);
                $mediax = end($tmp);

                $main_dir = public_path() . '/images/student/profile_images';
                $thumb_dir = public_path() . '/images/student/profile_images/thumb';
                $medium_dir = public_path() . '/images/student/profile_images/medium';

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

                $file_path = public_path() . '/images/student/profile_images/' . $mediax;
                if (file_exists($file_path)) {

                    unlink($file_path);
                }

                $file_path_meidum = public_path() . '/images/student/profile_images/medium/' . $mediax;
                if (file_exists($file_path_meidum)) {
                    unlink($file_path_meidum);
                }

                $file_path_thumb = public_path() . '/images/student/profile_images/thumb/' . $mediax;
                if (file_exists($file_path_thumb)) {
                    unlink($file_path_thumb);
                }
            }

            # function for image conversion of user profile  images in thumb/medium

            public function productImageVersions($name) {
                $main_dir = public_path() . '/images/student/profile_images';
                $thumb_dir = public_path() . '/images/student/profile_images/thumb';
                $medium_dir = public_path() . '/images/student/profile_images/medium';
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
                        'width' => 530,
                        'height' => 350
                    ))->save($medium_dir . '/' . $name);
                    chmod($thumb_dir . '/' . $name, 0777);
                    chmod($medium_dir . '/' . $name, 0777);
                }
                return true;
            }

        }
        