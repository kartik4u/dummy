<?php

/*
 * Controller Name  : UniversityController
 * Author           : Inder
 * Created Date     : 23-04-2018
 * Description      : This controller perform university related oprations (get university,update university data..)
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin;
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

class UniversityController extends Controller {

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/admin/university/get-university",
     *   summary="University listing",
     *   produces={"application/json"},
     *   tags={"Admin-University"},
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
     *  )
     *
     */
    public function getUniversity(Request $request) {
        $requested_data = $request->all();
        $page_record = \Config::get('variable.page_per_record');
        $excel_limit = \Config::get('variable.excel_limit_per_file');

        #Validation 
        $rule = ['type' => 'required'];
        $validator = Validator::make($requested_data, $rule);
        if ($validator->fails()) {
            return Response::json($this->validateData($validator));
        }

        #Query to get university data
        $university_data = User::where('role_id', 2);

        #Get Active and Deactivated universities
        if (isset($requested_data['type']) && $requested_data['type'] == 'list') {
            $university_data->where('status', '!=', 0)->where('status', '!=', 3);
            #Get Rejected universities
        } else if (isset($requested_data['type']) && $requested_data['type'] == 'pending_list') {
            $university_data->where('status', 3);
            #If not verified
        } else if (isset($requested_data['type']) && $requested_data['type'] == 'unverified') {
            $university_data->where('status', 0)->orderBy('updated_at', 'DESC');
            #If not valid data type
        } else {
            return Response::json(array('status' => 400, 'description' => 'Please send valid data type.'));
        }

        #Add Search Filter here
        if (isset($requested_data['search']) && !empty($requested_data['search'])) {
            $university_data->where(function ($query) use($requested_data) {
                $query->where('name', 'LIKE', '%' . $requested_data['search'] . '%')
                        ->orWhere('email', 'LIKE', '%' . $requested_data['search'] . '%');
            });
        }

        #Add Date Filter here
        if (isset($requested_data['start_date']) && !empty($requested_data['start_date']) && isset($requested_data['end_date']) && !empty($requested_data['end_date'])) {
            //$university_data->where('created_at', '>=', $requested_data['start_date'])->where('created_at', '<=', $requested_data['end_date']);
            $university_data->whereRaw("DATE_FORMAT(FROM_UNIXTIME(created_at), '%Y-%m-%d') >= '".date('Y-m-d',$requested_data['start_date'])."'")
                    ->whereRaw("DATE_FORMAT(FROM_UNIXTIME(created_at), '%Y-%m-%d') <= '".date('Y-m-d',$requested_data['end_date'])."'");
            
        }

        #Add pagination here        
        $university_list = $university_data->orderBy('created_at', 'DESC')->paginate($page_record)->toArray();

        #Check university not empty here and greater then 0
        if (!empty($university_list['total']) && $university_list['total'] > 0) {
            if ($university_list['total'] >= $excel_limit) {
                $total_files = ceil($university_list['total'] / $excel_limit);
            } else {
                $total_files = 1;
            }
        } else {
            $total_files = 0;
        }

        #Check Final data here and send 
        if (isset($university_list['data']) && !empty($university_list['data'])) {
            $data = \Config::get('success.success_data');     # success  results
            $data['total_files'] = $total_files;
            $data['data'] = $university_list;
            return Response::json($data);
        } else {
            $data = \Config::get('success.no_record');      # no results
            $data['data'] = $university_list;
            $data['total_files'] = $total_files;
            return Response::json($data);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/admin/university/change-university-status",
     *   summary="Change University Status",
     *   produces={"application/json"},
     *   tags={"Admin-University"},
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
     *   )
     *   @SWG\Definition(
     *     definition="changeUniversityStatus",
     *     allOf={
     *         @SWG\Schema(
     *              @SWG\Property(property="user_id", type="integer"),
     *              @SWG\Property(property="status", type="integer")
     *         )
     *     }
     * )
     *
     */
    public function changeUniversityStatus(Request $request) {
        $requested_data = $request->all();
        $page_record = \Config::get('variable.page_per_record');

        #Validation 
        $rule = ['user_id' => 'required|exists:users,id,role_id,2'];
        $validator = Validator::make($requested_data, $rule);
        if ($validator->fails()) {
            return Response::json($this->validateData($validator));
        }

        #Check Status(approved,active,deactivate) here 
        if (isset($requested_data['status']) && $requested_data['status'] == 1) { #Activate user here
            $response = User::where('id', $requested_data['user_id'])->update(['status' => $requested_data['status']]);

            #Send response here 
            if ($response) {
                return Response::json(\Config::get('success.university_activate'));
            } else {
                return Response::json(\Config::get('error.university_not_activate'));
            }
        } elseif (isset($requested_data['status']) && $requested_data['status'] == 2) { #Deactivate user here
            $response = User::where('id', $requested_data['user_id'])->update(['status' => $requested_data['status']]);

            #Send response here 
            if ($response) {
                return Response::json(\Config::get('success.university_deactivate'));
            } else {
                return Response::json(\Config::get('error.university_not_deactivate'));
            }
        } elseif (isset($requested_data['status']) && $requested_data['status'] == 3) { #Approved user and send confirmation email to user here
            $response = User::where('id', $requested_data['user_id'])->update(['status' => 1]);

            #Send Approved mail
            $this->sendApprovedMail($requested_data['user_id']);

            #Send response here 
            if ($response) {
                return Response::json(\Config::get('success.university_approved'));
            } else {
                return Response::json(\Config::get('error.university_not_approved'));
            }
        } else { #If status is invalid or empty then return below error message
            return Response::json(array('status' => 400, 'description' => 'Please send valid status.'));
        }
    }

    /* function to send a Approved email */

    private function sendApprovedMail($id) {
        $user_detail = User::find($id);

        #data to send in email
        $email_array = array(
            'server_url' => \Config::get('variable.SERVER_URL'),
            'to' => $user_detail->email,
            'from' => \Config::get('variable.ADMIN_EMAIL'),
            'from_name' => \Config::get('variable.MAIL_FROM_NAME'),
            'subject' => 'Account Approval Confirmation',
            'view' => 'email.approved_university',
            'name' => $user_detail->name,
            'frontend_url' => \Config::get('variable.FRONTEND_URL')
        );
        #Send Verification Email
        return $this->sendEmail($email_array);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/admin/university/reject-university",
     *   summary="Reject University",
     *   produces={"application/json"},
     *   tags={"Admin-University"},
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
     *   )
     * )
     *
     */
    public function rejectUniversity(Request $request) {
        $requested_data = $request->all();
        $page_record = \Config::get('variable.page_per_record');

        #Validation 
        $rule = ['user_id' => 'required|exists:users,id,role_id,2', 'reason' => 'required'];
        $validator = Validator::make($requested_data, $rule);
        if ($validator->fails()) {
            return Response::json($this->validateData($validator));
        }

        #Send Rejected mail
        $this->sendRejectMail($requested_data['user_id'], $requested_data['reason']);

        #Reject user here
        $response = User::where('id', $requested_data['user_id'])->delete();

        #Send response here 
        if ($response) {
            return Response::json(\Config::get('success.university_rejected'));
        } else {
            return Response::json(\Config::get('error.university_not_rejected'));
        }
    }

    /* function to send a Reject email */

    private function sendRejectMail($id, $reason) {
        $user_detail = User::find($id);

        #data to send in email
        $email_array = array(
            'server_url' => \Config::get('variable.SERVER_URL'),
            'to' => $user_detail->email,
            'from' => \Config::get('variable.ADMIN_EMAIL'),
            'from_name' => \Config::get('variable.MAIL_FROM_NAME'),
            'subject' => 'Account Rejected',
            'view' => 'email.rejected_university',
            'name' => $user_detail->name,
            'reason' => !empty($reason) ? $reason : 'You are rejected by admin.',
            'frontend_url' => \Config::get('variable.FRONTEND_URL')
        );
        #Send Verification Email
        return $this->sendEmail($email_array);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/admin/university/university-detail",
     *   summary="University Detail",
     *   produces={"application/json"},
     *   tags={"University"},
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
     *   )
     *   @SWG\Definition(
     *     definition="universityDetail",
     *     allOf={
     *         @SWG\Schema(
     *              @SWG\Property(property="user_id", type="integer")
     *         )
     *     }
     * )
     *
     */
    public function universityDetail(Request $request) {
        $requested_data = $request->all();

        #Validation 
        $rule = ['user_id' => 'required|exists:users,id,role_id,2'];
        $validator = Validator::make($requested_data, $rule);
        if ($validator->fails()) {
            return Response::json($this->validateData($validator));
        }

        # query to get university profile data
        $university_data = User ::where('id', $requested_data['user_id'])->with(['getProfile'])->first();
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
     *  @SWG\Get(
     *   path="/admin/university/save-university-detaill",
     *   summary="Save University Detail",
     *   produces={"application/json"},
     *   tags={"University"},
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
     *   )
     *   @SWG\Definition(
     *     definition="saveUniversityDetail",
     *     allOf={
     *         @SWG\Schema(
     *              @SWG\Property(property="user_id", type="integer"),
     *              @SWG\Property(property="is_sponsor", type="integer"),
     *              @SWG\Property(property="ranking", type="integer")
     *         )
     *     }
     * )
     *
     */
    public function saveUniversityDetail(Request $request) {
        $requested_data = $request->all();

        #Validation 
        $rule = ['user_id' => 'required|exists:users,id,role_id,2'];
        $validator = Validator::make($requested_data, $rule);
        if ($validator->fails()) {
            return Response::json($this->validateData($validator));
        }

        #Check is_sponsor value and update
        if (isset($requested_data['is_sponsor']) && $requested_data['is_sponsor'] != '') {
            User::where('id', $requested_data['user_id'])->update(['is_sponsor' => 1]);
        } else {
            User::where('id', $requested_data['user_id'])->update(['is_sponsor' => 0]);
        }

        #Check ranking value and update
        //if (isset($requested_data['ranking']) && !empty($requested_data['ranking'])) {
            $userDetail = UserDetail::where('user_id', $requested_data['user_id'])->first();
            if (!empty($userDetail)) {
                UserDetail::where('user_id', $requested_data['user_id'])->update(['ranking' => $requested_data['ranking']]);
            } else {
                $data = array();
                $data['user_id'] = $requested_data['user_id'];
                $data['ranking'] = $requested_data['ranking'];
                $data['created_at'] = time();
                $data['updated_at'] = time();
                UserDetail::create($data);
            }
        //}
        #Send response here               
        return Response::json(\Config::get('success.save_university_data'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/admin/university/download-universities",
     *   summary="Download University",
     *   produces={"application/json"},
     *   tags={"University"},
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
     *   @SWG\Definition(
     *     definition="downloadUniversities",
     *     allOf={
     *         @SWG\Schema(           
     *              @SWG\Property(property="start_date", type="integer"),
     *              @SWG\Property(property="end_date", type="integer"),
     *              @SWG\Property(property="search", type="string")
     *         )
     *     }
     * )
     *
     */
    public function downloadUniversities(Request $request) {
        $requested_data = $request->all();
        $server_url = \Config::get('variable.SERVER_URL');
        $excel_limit = \Config::get('variable.excel_limit_per_file');

        #Get all university(role_id->2) with status 1,2
        $university_data = User::where('role_id', 2)->where('status', '!=', 3)->where('status', '!=', 0);

        #Add Search Filter here
        if (isset($requested_data['search']) && !empty($requested_data['search'])) {
            $university_data->where(function ($query) use($requested_data) {
                $query->where('name', 'LIKE', '%' . $requested_data['search'] . '%')
                        ->orWhere('email', 'LIKE', '%' . $requested_data['search'] . '%');
            });
        }

        #Add Date Filter here
        if (isset($requested_data['start_date']) && !empty($requested_data['start_date']) && isset($requested_data['end_date']) && !empty($requested_data['end_date'])) {
            //$university_data->where('created_at', '>=', $requested_data['start_date'])->Where('created_at', '<=', $requested_data['end_date']);
        
            $university_data->whereRaw("DATE_FORMAT(FROM_UNIXTIME(created_at), '%Y-%m-%d') >= '".date('Y-m-d',$requested_data['start_date'])."'")
                    ->whereRaw("DATE_FORMAT(FROM_UNIXTIME(created_at), '%Y-%m-%d') <= '".date('Y-m-d',$requested_data['end_date'])."'");
            
        }
        $university_list = $university_data->with(['getProfile'])->paginate($excel_limit)->toArray();

        #Check university not empty here and greater then 0
        if (isset($university_list['data']) && !empty($university_list['data'])) {
            #Set path here and define file name
            $public_path = public_path() . "/csv/university/";

            #Remove old from directory
            $files = glob($public_path . '/*.csv');
            if (!empty($files)) {
                foreach ($files as $file) {
                    unlink($file);
                }
            }

            #Set file name here
            $file = strtotime(date('Y-m-d H:i:s')) . "_universities.csv";
            $filename = $public_path . $file;
            $handle = fopen($filename, 'w+');
            #Set all heading here for file
            fputcsv($handle, array('ID', 'Name', 'Email', 'Sponsored', 'Created At',
                'Phone Number', 'Ranking', 'Website', 'Date of Origin',
                'About University', 'About Scholarship'));
            #Set data here 
            foreach ($university_list['data'] as $key => $row) {
                
                $id = isset($row['id']) && !empty($row['id']) ? $row['id'] : '';
                $name = isset($row['name']) && !empty($row['name']) ? $row['name'] : '';
                $email = isset($row['email']) && !empty($row['email']) ? $row['email'] : '';
                $sponsored = isset($row['is_sponsor']) && !empty($row['is_sponsor']) && $row['is_sponsor'] == 1 ? 'yes' : 'no';
                $created_at = isset($row['created_at']) && !empty($row['created_at']) && $row['created_at'] > 0 ? date('d-m-Y', $row['created_at']) : '';
                $phone_number = isset($row['get_profile']['phone_number']) && !empty($row['get_profile']['phone_number']) ? $row['get_profile']['phone_number'] : '';
                $ranking = isset($row['get_profile']['ranking']) && !empty($row['get_profile']['ranking']) ? $row['get_profile']['ranking'] : '';
                $website = isset($row['get_profile']['website']) && !empty($row['get_profile']['website']) ? $row['get_profile']['website'] : '';
                $date_of_origin = isset($row['get_profile']['date_of_origin']) && !empty($row['get_profile']['date_of_origin']) && $row['get_profile']['date_of_origin'] > 0 ? date('d-m-Y', $row['get_profile']['date_of_origin']) : '';
                $about_university = isset($row['get_profile']['about_university']) && !empty($row['get_profile']['about_university']) ? $row['get_profile']['about_university'] : '';
                $about_scholarship = isset($row['get_profile']['about_scholarship']) && !empty($row['get_profile']['about_scholarship']) ? $row['get_profile']['about_scholarship'] : '';


                fputcsv($handle, array($id,
                    $name,
                    $email,
                    $sponsored,
                    $created_at,
                    $phone_number,
                    $ranking,
                    $website,
                    $date_of_origin,
                    $about_university,
                    $about_scholarship
                ));
            }
            #return file name here
            fclose($handle);
            $headers = array(
                'Content-Type' => 'text/csv',
            );
            $data['status'] = 200;
            $data['file'] = $server_url . 'csv/university/' . $file;
            return response()->json($data);
        } else {
            return Response::json(array('status' => 400, 'description' => 'University data not found, Please try again.'));
        }
    }

}
