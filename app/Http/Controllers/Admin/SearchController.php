<?php

/*
 * Controller Name  : SearchController
 * Author           : Narinder
 * Created Date     : 27-04-2018
 * Description      : This controller manage search courses functionality
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;
use Validator;
use Auth;
use JWTAuth;
use App\Config;
use \Illuminate\Pagination\Paginator;
use App\SearchAnalytic;

class SearchController extends Controller {

    /**
     * @SWG\Get(
     *   path="/admin/search/getSearchData",
     *   summary="Search data",
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
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")  
     *  )
     *
     */
    /*
     * Main Function to get search listing
     * @param : status, search keyword, page no
     * @return (status, success with data/error)
     */

    public function getSearchData(Request $request) {
        $requested_data = $request->all();
        $page_record = \Config::get('variable.page_per_record');

        $search = SearchAnalytic::select('*')->with(['degreeLevel']);

        // filter by start and end date
        if (isset($requested_data['start_date']) && empty(!$requested_data['start_date']) && isset($requested_data['end_date']) && empty(!$requested_data['end_date'])) {
            $start_date = $requested_data['start_date'];
            $end_date = $requested_data['end_date'];
            $search = $search->whereRaw("DATE_FORMAT(FROM_UNIXTIME(created_at), '%Y-%m-%d') >= '" . date('Y-m-d', $start_date) . "'")
                    ->whereRaw("DATE_FORMAT(FROM_UNIXTIME(created_at), '%Y-%m-%d') <= '" . date('Y-m-d', $end_date) . "'");
            //$search = $search->where('created_at', '>=', $start_date)->where('created_at', '<=', $end_date);
            //->whereBetween('created_at', [$start_date, $end_date]);
        }

        $excel_limit = \Config::get('variable.excel_limit_per_file');

        $search_count1 = $search;
        $search = $search->orderBy('created_at', 'desc')->paginate(\Config::get('variable.page_per_record'))->toArray();
        #Check search  not empty here and greater then 0
        if (!empty($search['total']) && $search['total'] > 0) {
            if ($search['total'] > $excel_limit) {
                $total_files = ceil($search['total'] / $excel_limit);
            } else {
                $total_files = 1;
            }
        } else {
            $total_files = 0;
        }

        $check_count = SearchAnalytic::where('status', 1);
        if (isset($requested_data['start_date']) && empty(!$requested_data['start_date']) && isset($requested_data['end_date']) && empty(!$requested_data['end_date'])) {
            $start_date = $requested_data['start_date'];
            $end_date = $requested_data['end_date'];
            $check_count = $check_count->whereRaw("DATE_FORMAT(FROM_UNIXTIME(created_at), '%Y-%m-%d') >= '" . date('Y-m-d', $start_date) . "'")
                    ->whereRaw("DATE_FORMAT(FROM_UNIXTIME(created_at), '%Y-%m-%d') <= '" . date('Y-m-d', $end_date) . "'");
        }
        $loggedin_search_user = $browser_search_user = 0;
        $search_count2 = $check_count;
        $get_browser_search_users = $search_count2->where('user_id', 0)->where('cache_id', '!=', '')->groupBy('cache_id')->get();
        if (!empty($get_browser_search_users)) {
            $browser_search_user = count($get_browser_search_users);
        }
        $loggedin_search_user = $search_count1->where('user_id', '!=', 0)->groupBy('user_id')->count();
        $total_records_by_ip = $loggedin_search_user + $browser_search_user;

        if (isset($search) && !empty($search)) {
            $response["status"] = 200;
            $response["message"] = 'Search listing data';
            $response["data"] = $search;
            $response["total_records_by_ip"] = $total_records_by_ip;
            $response['total_files'] = $total_files;
        } else {
            $response = \Config::get('success.no_record');
            $response["total_records_by_ip"] = $total_records_by_ip;
            $response['total_files'] = 0;
        }
        return Response::json($response);
    }

    /**
     * @SWG\Get(
     *   path="/admin/search/download-search",
     *   summary=" download Search data",
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
    public function downloadSearch(Request $request) {
        $requested_data = $request->all();
        $server_url = \Config::get('variable.SERVER_URL');
        $excel_limit = \Config::get('variable.excel_limit_per_file');

        #Get all search data
        $search_data = SearchAnalytic::select('*')->with(['degreeLevel']);


        #Add Date Filter here
        if (isset($requested_data['start_date']) && !empty($requested_data['start_date']) && isset($requested_data['end_date']) && !empty($requested_data['end_date'])) {
            //$search_data->where('created_at', '>=', $requested_data['start_date'])->Where('created_at', '<=', $requested_data['end_date']);
            $search_data->whereRaw("DATE_FORMAT(FROM_UNIXTIME(created_at), '%Y-%m-%d') >= '" . date('Y-m-d', $requested_data['start_date']) . "'")
                    ->whereRaw("DATE_FORMAT(FROM_UNIXTIME(created_at), '%Y-%m-%d') <= '" . date('Y-m-d', $requested_data['end_date']) . "'");
        }
        $search_data = $search_data->orderBy('created_at', 'desc')->paginate($excel_limit)->toArray();

        #Check university not empty here and greater then 0
        if (isset($search_data['data']) && !empty($search_data['data'])) {
            #Set path here and define file name
            $public_path = public_path() . "/csv/search/";

            #Remove old from directory
            $files = glob($public_path . '/*.csv');
            if (!empty($files)) {
                foreach ($files as $file) {
                    unlink($file);
                }
            }

            #Set file name here
            $file = strtotime(date('Y-m-d H:i:s')) . "_search.csv";
            $filename = $public_path . $file;
            $handle = fopen($filename, 'w+');
            #Set all heading here for file
            fputcsv($handle, array('Sr. no.', 'ID', 'IP Address', 'location', 'university', 'course',
                'degree_level', 'visa', 'entry requirement', 'annual fee',
                'ranking', 'created at'));
            #Set data here 
            foreach ($search_data['data'] as $key => $row) {
                $noNo = $key + 1;
                $id = @$row['id'];
                $ip_origin = @$row['ip_origin'];
                $location = @$row['location'];
                $university = @$row['university'];
                $course = @$row['course'];
                $degree_level = @$row['degree_level']['name'];
                $visa = @$row['visa'];
                $entry_requirement = @$row['entry_requirement'];
                $fee = @$row['annual_fee'];
                $ranking = @$row['ranking'] ? $row['ranking'] : '';
                $created_at = date('d-m-Y', @$row['created_at']);

                fputcsv($handle, array(
                    $noNo,
                    $id,
                    $ip_origin,
                    $location,
                    $university,
                    $course,
                    $degree_level,
                    $visa,
                    $entry_requirement,
                    $fee,
                    $ranking,
                    $created_at
                ));
            }
            #return file name here
            fclose($handle);
            $headers = array(
                'Content-Type' => 'text/csv',
            );
            $data['status'] = 200;
            $data['file'] = $server_url . 'csv/search/' . $file;
            return response()->json($data);
        } else {
            return Response::json(array('status' => 400, 'description' => 'Search data not found. Please try again.'));
        }
    }

}
