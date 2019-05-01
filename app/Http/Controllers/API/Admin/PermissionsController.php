<?php

namespace App\Http\Controllers\API\Admin;

use Spatie\Permission\Models\Permission;
use App\Interfaces\AdminPermissionsInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Lcobucci\JWT\Parser;
use Mail;
use Response;
use App\Http\Requests\Admin\Permission\CreatePermissionsRequest;
use App\Http\Requests\Admin\Permission\UpdatePermissionsRequest;
use App\Http\Requests\Admin\Permission\DeletePermissionsRequest;
use App\Http\Requests\Admin\Permission\ViewPermissionsRequest;
use DB;
use App\Quotation;


class PermissionsController extends Controller implements AdminPermissionsInterface
{

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/admin/getAllPermissions",
     *   summary="get all permissions",
     *   produces={"application/json"},
     *   tags={"ADMIN PERMISSIONS APIS"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *  @SWG\Parameter(
     *     name="search",
     *     in="query",
     *     required=false,
     *     type="string",
     *     description="search by name"
     *   ), 
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )  
     *
     */

    /**
     * Display a listing of Permission.
     *
     * @return \Illuminate\Http\Response
     */
     
    public function getAllPermissions(Request $request)
    {
      $requested_data = $request->all();
      $permissions = DB::table('permissions')->select('id','name','created_at','updated_at');
       // seaching if required
        if (isset($requested_data['search']) && !empty($requested_data['search'])) {
            $permissions = $permissions->where(function($q) use($requested_data) {
                $q->whereRaw("( REPLACE(name,' ','')  LIKE '%" . str_replace(' ', '', $requested_data['search']) . "%')");
            });
        }
        $permissions =$permissions->orderBy('created_at', 'desc')->paginate(\Config::get('variable.page_per_record'))->toArray();
      
      if ($permissions) {
            $data = \Config::get('admin_success.record_found');
            $data['data'] = $permissions;
        } else {
            $data = \Config::get('admin_error.no_record_found');
            $data['data'] = [];
        }
        return Response::json($data);
    }

     /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\POST(
   *   path="/admin/createPermissions",
     *   summary="create permissions",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *   tags={"ADMIN PERMISSIONS APIS"},
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "",
     *     @SWG\Schema(ref="#/definitions/createPermissions"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="createPermissions",
     *     allOf={
     *         @SWG\Schema(
   *            @SWG\Property(
     *               property="permissions",
     *               type="array",
     *              @SWG\Items(type="string")
     *            ),
     *      ),
     *     }
     * )
     *
     */

    /**
     * Function to create permissions
     *
     * @param  \App\Http\Requests\StorePermissionsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function createPermissions(CreatePermissionsRequest $request)
    {
        $data = \Config::get('admin_success.permission_created');
        // if(count($request->permissions)){
        //     foreach ($request->permissions as $key => $value) {
                $key=0;
                $input[$key]['name'] = $request->name;
                $input[$key]['created_at'] = time();
                $input[$key]['updated_at'] = time();
//            }
        //}
        Permission::insert($input);
        return Response::json($data);
    }

     /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
   *   path="/admin/updatePermissions",
     *   summary="update permissions",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *   tags={"ADMIN PERMISSIONS APIS"},
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "",
     *     @SWG\Schema(ref="#/definitions/update"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="update",
     *     allOf={
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="name",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="permission_id",
     *                 type="number",
     *             ),
     *         ),
     *     }
     * )
     *
     */

    /**
     * function to update permission.
     *
     * @param  \App\Http\Requests\UpdatePermissionsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePermissions(UpdatePermissionsRequest $request)
    {
        $permission = Permission::findOrFail($request->permission_id);
        $permission->update((['name'=>$request->name,'updated_at'=>time()]));
        $data = \Config::get('admin_success.permission_updated');
        return Response::json($data);
    }



     /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
   *   path="/admin/deletePermissions",
     *   summary="delete permissions",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *   tags={"ADMIN PERMISSIONS APIS"},
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "",
     *     @SWG\Schema(ref="#/definitions/deletePermissions"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="deletePermissions",
     *     allOf={
     *         @SWG\Schema(
  *            @SWG\Property(
     *            property="permission_ids",
     *            type="array",
     *            @SWG\Items(type="number")
     *            ),
     *         ),
     *     }
     * )
     *
     */

    /**
     * function to update permission.
     *
     * @param  \App\Http\Requests\UpdatePermissionsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deletePermissions(DeletePermissionsRequest $request)
    {
        $permission = Permission::where('id',$request->id);

   //     $permission = Permission::whereIn('id',$request->permission_ids);
        $permission->delete();
        $data = \Config::get('admin_success.permission_deleted');
        return Response::json($data);
    }



    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/admin/viewPermission",
     *   summary="get single permission",
     *   produces={"application/json"},
     *   tags={"ADMIN PERMISSIONS APIS"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *  @SWG\Parameter(
     *     name="permission_id",
     *     in="query",
     *     required=true,
     *     type="number",
     *     description="permission id"
     *   ), 
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )  
     *
     */

    /**
     * Display a listing of Permission.
     *
     * @return \Illuminate\Http\Response
     */

     
    public function viewPermission(ViewPermissionsRequest $request)
    {
      $permissions = Permission::where('id',$request->permission_id)->first();
      $data = \Config::get('admin_success.record_found');
      $data['data'] = $permissions;
      return Response::json($data);
    }


    // get permission lists        
    public function getPermissions(Request $request)
    {
        $permissions = Permission::select('id','name','created_at','updated_at')->get();
        $data = \Config::get('admin_success.record_found');
        $data['data'] = $permissions;
        return Response::json($data);
    }

}
