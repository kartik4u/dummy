<?php

#Controller Name: ReportController
#Developer      : Narinder
#Purpose        : Manage report


namespace App\Http\Controllers\API\Admin;

// Load Model
use App\Interfaces\AdminReportInterface;
use App\Http\Controllers\Controller;
use App\Http\Traits\AdminTrait;
use App\Role;
use App\Models\ReportedUser;
use App\User;
use Carbon\Carbon;
use Config;
use Hash;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\Report\GetReportDatailRequest;
use App\Http\Requests\Admin\Report\DeleteReportRequest;
use Illuminate\Support\Facades\Auth;
use Image;
use Lcobucci\JWT\Parser;
use Mail;
use DB;
use Response;

class ReportController extends Controller implements AdminReportInterface{

    public function __construct() {
        
    }
    use AdminTrait;

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/admin/reportsListing",
     *   summary="report listing",
     *   produces={"application/json"},
     *   tags={"ADMIN REPORTS APIS"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string"
     *   ),
     *  @SWG\Parameter(
     *     name="search",
     *     in="query",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function reportsListing(Request $request) {
        $requested_data = $request->all();
        $type = isset($requested_data['type'])?$requested_data['type']:0;
        $page_record = \Config::get('variable.page_per_record');
        $report_data = ReportedUser::with(
            [
            'reportedByUser'=> function($query)  {
                $query->select('id', 'name','role_id','status','email');
            },
            'reportedToUser'=> function($query)  {
                $query->select('id', 'name','role_id','status','email');
            }
        ]);
        
         #Check if search(name) with any keyword here

         if(isset($requested_data['search'])){
            $ids = User::where('name', 'LIKE', '%' . $requested_data['search'] . '%')->pluck('id');
            $report_data = $report_data->where(function($query) use ($ids) {
                return $query->whereIn('reported_by_user_id',$ids)
                    ->orWhereIn('reported_user_id',$ids);
            });     
         }

        #Filter by  report type

        if(isset($requested_data['report_type']) && empty(!$requested_data['report_type'])){
        $report_data = $report_data->where(function($query) use ($requested_data) {
            return $query->where('report_type',$requested_data['report_type']);
        });     
        }

         

         $report_data = $report_data->orderby('created_at','desc')->paginate($page_record)->toArray();

        #response
       // if (count($report_data['data'])) {
          $data = \Config::get('admin_success.record_found');     # success results
        // } else {
        //     $data = \Config::get('admin_error.no_record_found');      # no results
        // }
        $data['data'] = $report_data;
        $data['report_types'] = DB::table('report_types')->get();
        return Response::json($data);
    }

        /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/admin/getReportDatail",
     *   summary="single report data",
     *   produces={"application/json"},
     *   tags={"ADMIN REPORTS APIS"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string"
     *   ),
     *  @SWG\Parameter(
     *     name="report_id",
     *     in="query",
     *     required=true,
     *     type="number"
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function getReportDatail(GetReportDatailRequest $request) {
        $requested_data = $request->all();
        $report_data = ReportedUser::where('id',$requested_data['report_id'])->with(
            [
            'reportedByUser'=> function($query)  {
                $query->select('id', 'name','role_id','email');
            },
            'reportedToUser'=> function($query)  {
                $query->select('id', 'name','role_id','email');
            }
        ])->first();

        #Check Final data here and send 
       // if (isset($report_data) && !empty($report_data)) {
        $data = \Config::get('admin_success.record_found');     # success results
        
            // } else {
        //     $data = \Config::get('admin_error.no_record_found');      # no results
        // }
        $data['data'] = $report_data;
        return Response::json($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/admin/deleteReport",
     *   summary="Report deleted",
     *   produces={"application/json"},
     *   tags={"ADMIN REPORTS APIS"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string"
     *   ),
     *  @SWG\Parameter(
     *     name="report_id",
     *     in="formData",
     *     required=true,
     *     type="number"
     *   ),
     *  @SWG\Parameter(
     *     name="deactivate_action",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "0=>only report filed will b removed,1=>user will deactivated and filed fill be removed",
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
     * Function to delete report
     * @param request 
     * @return response (status, message, success/failure)
     */

    public function deleteReport(DeleteReportRequest $request) {
        $requested_data = $request->all();
         // deactivate user
        if($requested_data['deactivate_action']){
            $user_id= ReportedUser::where('id',$requested_data['report_id'])->first()->reported_user_id;
            $requested_data['id'] = $user_id;
            $requested_data['status'] = 2;
            $users =  $this->ActiveInactiveUser($requested_data);
         }
        ReportedUser::where('id',$requested_data['report_id'])->delete();
        $data = \Config::get('admin_success.delete_report');     # success results
        return Response::json($data);
    }

    // get all roles

    public function getReportTypes(Request $request)
    {
        $requested_data = $request->all();
        $data = \Config::get('admin_success.record_found');
        $data['data'] = DB::table('report_types')->get()->toArray();
        return Response::json($data);
    }

}
