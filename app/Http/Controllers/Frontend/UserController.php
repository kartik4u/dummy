<?php

/*
 * Controller Name  : UserController
 * Author           : Prabhat
 * Author Contact   : prabhat.thakur@ignivasolutions.com
 * Created Date     : 23-03-2018
 * Description      : This controller use manage static pages
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
use App\Page;
use App\PageLog;

class UserController extends Controller {

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *  @SWG\Post(
     *   path="/frontend/user/change-password",
     *   summary="Change Password",
     *   produces={"application/json"},
     *   tags={"University/Student"},
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
     *     @SWG\Schema(ref="#/definitions/changePassword"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     * )
     * @SWG\Definition(
     *     definition="changePassword",
     *     allOf={
     *         @SWG\Schema(                
     *             @SWG\Property(
     *                 property="oldPassword",
     *                 type="string",
     *             ),
     *              @SWG\Property(
     *                 property="newPassword",
     *                 type="string",
     *             ),
     *              @SWG\Property(
     *                 property="confirmPassword",
     *                 type="string",
     *             )
     *         )
     *     }
     * ) 
     *
     */
    public function changePassword(Request $request) {
        $requested_data = $request->all();
        $user_data = User::find($requested_data['data']['id']);

        $rule = [
            'confirmPassword' => 'required|same:newPassword',
            'newPassword' => ['required', 'between:8,15', 'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[~!^(){}<>$%@#&*?+=_-])[A-Za-z\d~!^(){}<>$%@#&*?+=_-]/'],
            'oldPassword' => 'required',
        ];
        $messages = [
            'confirmPassword.same' => 'Password and Confirm password should match',
            'newPassword.between' => 'Password must be between 8 to 15 characters including a number and special character',
            'newPassword.regex' => 'Password must be between 8 to 15 characters including a number and special character',
        ];
        $validator = Validator::make($requested_data, $rule, $messages);  #Check validation       
        if ($validator->fails()) { #Check validation pass or fail
            return Response::json($this->validateData($validator));
        }

        # check user old password is correct or not
        if (Hash::check($requested_data['oldPassword'], $user_data->password)) {
            # update user password
            $update_password = User::where('id', $requested_data['data']['id'])->update(['password' => bcrypt($requested_data['newPassword'])]);
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
     *  @SWG\Get(
     *   path="/frontend/user/get-user-details",
     *   summary="User",
     *   produces={"application/json"},
     *   tags={"User"},
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
    public function getUserDetails(Request $request) {
        $requested_data = $request->all();
        $user_id = $requested_data['data']['id'];

        # query to get user data
        $getDetails = User::with('userDetail')->where('id', $user_id)->first();
        if (!empty($getDetails)) {
            unset($getDetails->password);
            $data = \Config::get('success.success_record_found');     # success results
            $data['data'] = $getDetails;
            #get current terms & conditions
            $terms = Page::where('status', 1)->where('slug', 'terms-and-conditions')->orderBy('id', 'DESC')->first();
            #get current privacy policy
            $policy = Page::where('status', 1)->where('slug', 'privacy-policy')->orderBy('id', 'DESC')->first();
            $data['data']['terms'] = $data['data']['policy'] = 0;
            if (!empty($terms) && !empty($policy)) {
                #get user latest accepted terms & conditions
                $user_terms = PageLog::where('user_id', $user_id)->where('title', 'terms-and-conditions')->orderBy('id', 'DESC')->first();
                #get user latest accepted privacy policy
                $user_policy = PageLog::where('user_id', $user_id)->where('title', 'privacy-policy')->orderBy('id', 'DESC')->first();
                #check if user policies match to current policies
                if (!empty($user_terms) && !empty($user_policy)) {
                    $data['data']['terms'] = !empty($user_terms) && $terms->id != $user_terms->page_id ? 1 : 0;
                    $data['data']['policy'] = !empty($user_policy) && $policy->id != $user_policy->page_id ? 1 : 0;
                } else {
                    $data['data']['terms'] = 1;
                    $data['data']['policy'] = 1;
                }
            }

            return Response::json($data);
        } else {
            $data = \Config::get('success.no_record_found');      # no results
            $data['data'] = $university_data;
            return Response::json($data);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Delete(
     *   path="/frontend/user/delete-user",
     *   summary="User",
     *   produces={"application/json"},
     *   tags={"User"},
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
    public function deleteUser(Request $request) {
        $requested_data = $request->all();
        $user_id = $requested_data['data']['id'];
        # query to delete user permanently
        $user = User::find($user_id);
        if (!empty($user)) {
            #delete user images
            $details = $user->userDetail()->select('id','user_id','profile_image as profile_pic','banner_image as banner_pic','logo as logo_pic')->first();
            
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
            $user->delete();
            if ($user) {
                return Response::json(\Config::get('success.user_deleted_successfully'));
            } else {
                return Response::json(\Config::get('error.failed_to_delete_user'));
            }
        } else {
            return Response::json(\Config::get('error.invalid_user'));
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/frontend/user/get-activity-logs",
     *   summary="User",
     *   produces={"application/json"},
     *   tags={"User"},
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
    public function getActivityLogs(Request $request) {
        $requested_data = $request->all();
        $user_id = $requested_data['data']['id'];
        $user = User::where('id', $user_id)->select(['id', 'last_logged as last_login', 'created_at'])->first();
        $account_verification = PageLog::where('user_id', $user_id)->where('title', 'account-verification')->first();
        $profile_update = PageLog::where('user_id', $user_id)->where('title', 'profile-update')->first();
        $term_conditions = PageLog::has('page')->with(['page' => function($q) {
                                $q->select('id', 'version');
                            }])
                        ->where('user_id', $user_id)->where('title', 'terms-and-conditions')->get()->toArray();
        $privacy_policy = PageLog::has('page')->with(['page' => function($q) {
                                $q->select('id', 'version');
                            }])
                        ->where('user_id', $user_id)->where('title', 'privacy-policy')->get()->toArray();

        if (!empty($user)) {
            $user_activities = [];
            $user_activities['signup'] = $user->created_at;
            $user_activities['profile_update'] = !empty($profile_update) ? $profile_update->created_at : '';
            $user_activities['last_login'] = $user->last_login;
            $user_activities['account_verification'] = !empty($account_verification) ? $account_verification->created_at : '';
            $user_activities['terms_conditions'] = $term_conditions;
            $user_activities['privacy_policy'] = $privacy_policy;

            $data = \Config::get('success.success_record_found');     # success results
            $data['data'] = $user_activities;
            return Response::json($data);
        } else {
            return Response::json(\Config::get('error.invalid_user'));
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *  @SWG\Post(
     *   path="/frontend/user/accept-terms-privacy",
     *   summary="Accept new terms and condtions & privacy policy",
     *   produces={"application/json"},
     *   tags={"University/Student"},
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
     *     @SWG\Schema(ref="#/definitions/acceptNewTermsPolicy"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     * )
     * @SWG\Definition(
     *     definition="acceptNewTermsPolicy",
     *     allOf={
     *         @SWG\Schema(   
     *             @SWG\Property(
     *                 property="user_id",
     *                 type="integer",
     *             ),             
     *             @SWG\Property(
     *                 property="terms",
     *                 type="integer",
     *             ),
     *              @SWG\Property(
     *                 property="privacy",
     *                 type="integer",
     *             ),
     *              @SWG\Property(
     *                 property="terms_page_id",
     *                 type="integer",
     *             ),
     *              @SWG\Property(
     *                 property="privacy_page_id",
     *                 type="integer",
     *             )
     *         )
     *     }
     * ) 
     *
     */
    public function acceptNewTermsPolicy(Request $request) {
        $requested_data = $request->all();
        $terms_page = Page::where('id', trim($requested_data['terms_page_id']))->first();
        $privacy_page = Page::where('id', trim($requested_data['privacy_page_id']))->first();

        if (!empty($terms_page) && !empty($privacy_page)) {
            $insert_array = [];
            if ($requested_data['terms'] == 1) {
                $insert_array[] = ['user_id' => $requested_data['user_id'], 'title' => $terms_page->slug, 'page_id' => $terms_page->id, 'created_at' => time(), 'updated_at' => time()];
            }
            if ($requested_data['privacy'] == 1) {
                $insert_array[] = ['user_id' => $requested_data['user_id'], 'title' => $privacy_page->slug, 'page_id' => $privacy_page->id, 'created_at' => time(), 'updated_at' => time()];
            }
            if (!empty($insert_array)) {
                $insert = PageLog::insert($insert_array);
            } else {
                return Response::json(\Config::get('error.terms_privacy_error'));
            }
            return Response::json(\Config::get('success.terms_privacy_accept_success'));
        } else {
            return Response::json(\Config::get('error.terms_privacy_error'));
        }
    }

}
