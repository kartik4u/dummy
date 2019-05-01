<?php

#Controller Name: AdminController
#Developer      : Narinder Singh
#Purpose        : To perform student related tasks
#Tasks          : get student,active inactive etc

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

class StudentController extends Controller {

    public function __construct() {
        
    }


        /**
     * @SWG\Get(
     *   path="/admin/get-students",
     *   summary="student data",
     *   produces={"application/json"},
     *   tags={"Admin"},
     *    @SWG\Parameter(
     *     name="token",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="search",
     *     in="query",
     *     required=false,
     *     description = "search",
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="start_date",
     *     in="query",
     *     required=false,
     *     description = "start date",
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="end_date",
     *     in="query",
     *     required=false,
     *     description = "end date",
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     required=false,
     *     description = "page number",
     *     type="number",
     *     default=1
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")  
     *  )
     *
     */

    /*
     * Main Function to get student listing
     * @param : status, search keyword, page no
     * @return (status, success with data/error)
     */

    public function getStudents(Request $request) {
        $requested_data = $request->all();
        $excel_limit = \Config::get('variable.excel_limit_per_file');
        $keyword = isset($requested_data['search']) ? $requested_data['search'] : '';
        $keyword = trim($keyword);
        $student = User::select('id', 'name','email', 'role_id', 'status', 'last_logged', 'last_searched','created_at')->whereIn('status', [1, 2])->where('role_id', 3);
        if (!empty($keyword)) {
            $student = $student->where(function($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%');
            });
        }
        // filter by start and end date
        if (isset($requested_data['start_date']) && empty(!$requested_data['start_date']) && isset($requested_data['end_date']) && empty(!$requested_data['end_date'])) {
            $start_date = $requested_data['start_date'];
            $end_date = $requested_data['end_date'];
            $student = $student->whereRaw("DATE_FORMAT(FROM_UNIXTIME(created_at), '%Y-%m-%d') >= '".date('Y-m-d',$start_date)."'")
                    ->whereRaw("DATE_FORMAT(FROM_UNIXTIME(created_at), '%Y-%m-%d') <= '".date('Y-m-d',$end_date)."'");
        }

        $student = $student->orderBy('created_at', 'desc');
        $count = $student;
        $student = $student->paginate(\Config::get('variable.page_per_record'))->toArray();
        $count = $count->count(); #get total count
        
        if($count>0){
            $total_links = $count >= $excel_limit ? ceil($count / $excel_limit) : 1; #total download links to generate
        } else{
            $total_links=0;
        }
        $response['total_links'] = $total_links;

        if (isset($student) && !empty($student)) {
            $response["status"] = 200;
            $response["message"] = 'Students listing data';
            $response["data"] = $student;
        } else {
            $response = \Config::get('error.profile_not_fetched');
        }
        return Response::json($response);
    }

    /*
     * Main Function to get activate & deactivate user
     * @param :
     * @return (status, success with data/error)
     */

    public function changeStatus(Request $request) {
        // #Set common variable for all requests
        $requested_data = $request->all();
        //
        // #Check if requested data empty send error msg with status else send data in private functions
        $rule = ['status' => 'required', 'user_id' => 'required'];
        $validator = Validator::make($requested_data, $rule);  #Check validation

        $data_error = array();   #Array for send data in response
        if ($validator->fails()) { #Check validation pass or fail
            return Response::json($this->validateData($validator));
        }
        User::where('id', $requested_data['user_id'])->update(array('status' => $requested_data['status']));
        $data['status'] = 200;
        $data['user_status'] = $requested_data["status"];
        $data['class'] = 'status_' . $requested_data['user_id'];
        if ($requested_data["status"] == 1) {
            $data['description'] = 'Student resumed successfully';
        } else if ($requested_data["status"] == 2) {
            $data['description'] = 'Student revoked successfully';
        } else {
            $error = \Config::get('error.error_user_status');
            return Response::json($error);
        }
        return Response::json($data);
    }





    /**
     * @SWG\Get(
     *   path="/admin/student/download-student",
     *   summary=" download student data",
     *   produces={"application/json"},
     *   tags={"Admin"},
     *    @SWG\Parameter(
     *     name="token",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="search",
     *     in="query",
     *     required=false,
     *     description = "search",
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="start_date",
     *     in="query",
     *     required=false,
     *     description = "start date",
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="end_date",
     *     in="query",
     *     required=false,
     *     description = "end date",
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     required=false,
     *     description = "page number",
     *     type="number",
     *     default=1
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")  
     *  )
     *
     */



    public function downloadStudent(Request $request) {
        $requested_data = $request->all();
        $server_url = \Config::get('variable.SERVER_URL');
        $excel_limit = \Config::get('variable.excel_limit_per_file');
        
        $keyword = isset($requested_data['search']) ? $requested_data['search'] : '';
        $keyword = trim($keyword);
         $student = User::select('id', 'name', 'email','role_id', 'status', 'last_logged', 'last_searched')->whereIn('status', [1, 2])->where('role_id', 3)
        ->with([
            'userDetail'=> function($q) {
                $q->select('user_id','qualification','certificates','achievements');
            }
        ]);

        if (!empty($keyword)) {
            $student = $student->where(function($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%');
            });
        }
        // filter by start and end date
        if (isset($requested_data['start_date']) && empty(!$requested_data['start_date']) && isset($requested_data['end_date']) && empty(!$requested_data['end_date'])) {
            $start_date = $requested_data['start_date'];
            $end_date = $requested_data['end_date'];
            $student = $student->whereRaw("DATE_FORMAT(FROM_UNIXTIME(created_at), '%Y-%m-%d') >= '".date('Y-m-d',$start_date)."'")
                    ->whereRaw("DATE_FORMAT(FROM_UNIXTIME(created_at), '%Y-%m-%d') <= '".date('Y-m-d',$end_date)."'");
            //$student = $student->whereBetween('created_at', [$start_date, $end_date]);
        }

        $student = $student->orderBy('created_at', 'desc');
        $student = $student->paginate($excel_limit)->toArray();
        
        #Check university not empty here and greater then 0
        if(isset($student['data']) && !empty($student['data'])){
            #Set path here and define file name
            $public_path = public_path() . "/csv/student/";

            #Remove old from directory
            $files = glob($public_path . '/*.csv');
            if (!empty($files)) {
                foreach ($files as $file) {
                    unlink($file);
                }
            }
            
            #Set file name here
            $file = strtotime(date('Y-m-d H:i:s')) . "_student.csv";
            $filename = $public_path . $file;
            $handle = fopen($filename, 'w+');
            #Set all heading here for file
            fputcsv($handle, array('Sr. no.','ID','name','email','Last logged', 'Last Searched','Qualification','Certificates','Achievements'));
                #Set data here 
                foreach ($student['data'] as $key => $row) {
                    $noNo = $key+1;
                    $id = isset($row['id']) && !empty($row['id']) ? $row['id'] : '';
                    $name = isset($row['name']) && !empty($row['name']) ? $row['name'] : '';
                    $email = isset($row['email']) && !empty($row['email']) ? $row['email'] : '';
                    $last_logged=$row['last_logged'];
                    $last_searched = $row['last_searched'];
                    $qualification = $row['user_detail']['qualification'];
                    $certificates = $row['user_detail']['certificates'];
                    $achievements = $row['user_detail']['achievements'];
                    fputcsv($handle, array(
                        $noNo,
                        $id,
                        $name,
                        $email,
                        $last_logged,
                        $last_searched,
                        $qualification,
                        $certificates,
                        $achievements
                    ));
                }
                #return file name here
                fclose($handle);
                    $headers = array(
                        'Content-Type' => 'text/csv',
                    );
                    $data['status'] = 200;
                    $data['file'] = $server_url.'csv/student/'.$file;
                    return response()->json($data);

        }else{
            return Response::json(array('status' => 400, 'description' => 'student data not found, Please try again.')); 
        }
    }


}
