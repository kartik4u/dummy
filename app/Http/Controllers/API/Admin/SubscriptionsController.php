<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Subscription\CreatePlanRequest;
use App\Http\Requests\Admin\Subscription\EditPlanRequest;
use App\Http\Requests\Admin\Subscription\ViewPlanRequest;
use App\Http\Requests\Admin\Subscription\DeletePlanRequest;
use App\Interfaces\AdminSubscriptionsInterface;
use App\Models\Plan;
use Config;
use Illuminate\Http\Request;
use Response;

class SubscriptionsController extends Controller implements AdminSubscriptionsInterface
{

    
     /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/admin/getPlans",
     *   summary="get all plans data",
     *   produces={"application/json"},
     *   tags={"ADMIN Subscription APIS"},
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
     * Function : function to get all Plans
     * Input:
     * Output: success, error
     */
    public function getPlans(Request $request)
    {
        $requested_data = $request->all();
        $response = Plan::select(['id', 'name', 'amount', 'status', 'updated_at', 'status'])
            ->orderby('updated_at', 'desc')
            ->paginate(config('variable.page_per_record'));
        if ($response) {
            $data = \Config::get('admin_success.record_found');
            $data['data'] = $response;
        } else {
            $data = \Config::get('admin_error.no_record_found');
        }
        return Response::json($data);
    }


     /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/admin/viewPlan",
     *   summary="get single plan data",
     *   produces={"application/json"},
     *   tags={"ADMIN Subscription APIS"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *  @SWG\Parameter(
     *     name="id",
     *     in="query",
     *     required=true,
     *     type="number",
     *     description="plan id"
     *   ), 
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )  
     *
     */

    /*
     * Function : function to get single plan
     * Input: id
     * Output: success, error
     */
    public function viewPlan(ViewPlanRequest $request)
    {
        $requested_data = $request->all();
        $response = Plan::where(['id' => $requested_data['id']])->select(['id', 'name', 'amount', 'status', 'updated_at', 'status'])->first();
        $data = \Config::get('admin_success.record_found');
        $data['data'] = $response;
        return Response::json($data);
    }


   
     /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/admin/createPlan",
     *   summary="create Plan",
     *   produces={"application/json"},
     *   tags={"ADMIN Subscription APIS"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "createPlan",
     *     @SWG\Schema(ref="#/definitions/createPlan"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="createPlan",
     *     allOf={
     *         @SWG\Schema(
     *            @SWG\Property(
     *                 property="name",
     *                 type="string",
     *             ),
     *            @SWG\Property(
     *                 property="amount",
     *                 type="number",
     *             )
     *         )
     *     }
     * )
     *
     */
    
    /*
     * Function : function to Create/Add Plan
     * Input: question,answer
     * Output: success, error
     */

    public function createPlan(CreatePlanRequest $request)
    {
        $requested_data = $request->all();
        $created = Plan::create([
            'name' => $request->name,
            'amount' => $request->amount,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        if ($created) {
            $data = \Config::get('admin_success.plan_success_created');
        } else {
            $data = \Config::get('admin_error.error');
        }
        return Response::json($data);
    }


   
     /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/admin/editPlan",
     *   summary="edit Plan",
     *   produces={"application/json"},
     *   tags={"ADMIN Subscription APIS"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "editPlan",
     *     @SWG\Schema(ref="#/definitions/editPlan"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="editPlan",
     *     allOf={
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="id",
     *                 type="number",
     *             ),
     *            @SWG\Property(
     *                 property="name",
     *                 type="string",
     *             ),
     *            @SWG\Property(
     *                 property="amount",
     *                 type="number",
     *             )
     *         )
     *     }
     * )
     *
     */
    
     /*
     /*
     * Function : function to edit Subscription
     * Input: id,name,price
     * Output: success, error
     */

    public function editPlan(EditPlanRequest $request)
    {
        $requested_data = $request->all();
        $updated = Plan::where('id', $requested_data['id'])->update([
            'name' => $request->name,
            'amount' => $request->amount,
            'updated_at' => time(),
            'created_at' => time(),
        ]);
        $data = \Config::get('admin_success.plan_success_updated');
        return Response::json($data);
    }


  
     /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/admin/deletePlan",
     *   summary="delete Plan",
     *   produces={"application/json"},
     *   tags={"ADMIN Subscription APIS"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "deletePlan",
     *     @SWG\Schema(ref="#/definitions/deletePlan"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="deletePlan",
     *     allOf={
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
     * Function : function to delete Plan
     * Input: id
     * Output: success, error
     */
    public function deletePlan(DeletePlanRequest $request)
    {
        $requested_data = $request->all();
        $response = Plan::where('id', $requested_data['id'])->delete();
        if ($response) {
            $data = \Config::get('admin_success.plan_success_deleted');
        } else {
            $data = \Config::get('admin_error.error');
        }
        return Response::json($data);
    }
}
