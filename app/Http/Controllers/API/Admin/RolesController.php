<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\AdminRolesInterface;
use App\Http\Requests\Admin\Role\CreateRolesRequest;
use App\Http\Requests\Admin\Role\UpdateRolesRequest;
use App\Http\Requests\Admin\Role\DeleteRolesRequest;
use App\Http\Requests\Admin\Role\ViewRolesRequest;
use App\User;
use App\RoleHasPermission;
use DB;
use Illuminate\Http\Request;
use Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role ;
//use App\Role;
//use App\Models\Permission;

class RolesController extends Controller implements AdminRolesInterface
{

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/admin/getAllRoles",
     *   summary="get all roles",
     *   produces={"application/json"},
     *   tags={"ADMIN ROLES APIS"},
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
     * Display a listing of Roles.
     *
     * @return \Illuminate\Http\Response
     */

    public function getAllRoles(Request $request)
    {      
        $requested_data= $request->all();
        $roles = Role::select('id','name','created_at','updated_at');
       // seaching if required
        if (isset($requested_data['search']) && !empty($requested_data['search'])) {
            
            $roles = $roles->where(function($q) use($requested_data) {
                $q->whereRaw("( REPLACE(name,' ','')  LIKE '%" . str_replace(' ', '', $requested_data['search']) . "%')");
            });
        }
        $roles = $roles->orderBy('created_at', 'desc')->paginate(config('variable.page_per_record'))->toArray();

        //if (count($roles['data'])) {
            $data = \Config::get('admin_success.record_found');
            $data['data'] = $roles;
        //}
        //  else {
        //     $data = \Config::get('admin_error.no_record_found');
        //     $data['data'] = [];
        // }
        return Response::json($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\POST(
     *   path="/admin/createRoles",
     *   summary="create roles",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *   tags={"ADMIN ROLES APIS"},
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "",
     *     @SWG\Schema(ref="#/definitions/createRoles"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="createRoles",
     *     allOf={
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="name",
     *                 type="string",
     *             ),
     *            @SWG\Property(
     *               property="permissions",
     *               type="array",
     *              @SWG\Items(type="string")
     *            ),
     *         ),
     *     }
     * )
     *
     */

    /**
     * Function to create role
     *
     * @param  \App\Http\Requests\CreateDeleteRolesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function createRoles(CreateRolesRequest $request)
    {
        $data = \Config::get('admin_success.role_created');
        DB::table('roles')->insert(['name'=>$request->name,'created_at'=>time(),'updated_at'=>time()]);
        $roles = Role::where('name', $request->name)->first();
        $roles->givePermissionTo($request->permissions);
        return Response::json($data);
    }

     /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\POST(
     *   path="/admin/updateRoles",
     *   summary="update roles",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *   tags={"ADMIN ROLES APIS"},
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "",
     *     @SWG\Schema(ref="#/definitions/updateRoles"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="updateRoles",
     *     allOf={
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="name",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="role_id",
     *                 type="number",
     *             ),
     *            @SWG\Property(
     *            property="permissions",
     *            type="array",
     *            @SWG\Items(type="string")
     *            ),
     *         ),
     *     }
     * )
     *
     */

    /**
     * function to update roles.
     *
     * @param  \App\Http\Requests\UpdateRolessRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateRoles(UpdateRolesRequest $request)
    {
        $role = Role::findOrFail($request->role_id);
        $role->update(['name'=>$request->name]);
        $role->syncPermissions($request->permissions);
        $data = \Config::get('admin_success.role_updated');
        return Response::json($data);
    }



         /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
   *   path="/admin/deleteRoles",
     *   summary="delete roles",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *   tags={"ADMIN ROLES APIS"},
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "",
     *     @SWG\Schema(ref="#/definitions/deleteRoles"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="deleteRoles",
     *     allOf={
     *         @SWG\Schema(
  *            @SWG\Property(
     *            property="role_ids",
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
     * @param  \App\Http\Requests\DeleteRolesRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteRoles(DeleteRolesRequest $request)
    {  
        Role::whereIn('id',$request->role_ids)->delete();
        $data = \Config::get('admin_success.role_deleted');
        return Response::json($data);
    }



    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/admin/viewRole",
     *   summary="get single permission",
     *   produces={"application/json"},
     *   tags={"ADMIN ROLES APIS"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *  @SWG\Parameter(
     *     name="role_id",
     *     in="query",
     *     required=true,
     *     type="number",
     *     description="role id"
     *   ), 
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )  
     *
     */

    /**
     * Display a single role.
     *
     * @return \Illuminate\Http\Response
     */

     
    public function viewRole(viewRolesRequest $request)
    {
        $role = Role::where('id',$request->role_id)->with(['rolePermission.permissions'])
        ->first();
        $permission_ids= RoleHasPermission::where('role_id',$request->role_id)->pluck('permission_id');
        // $role = Role::findByName('string');
      //    if($role->hasPermissionTo('string')){
      //     echo '2';
      //    } else{
      //        echo '1';
      //    }
      //    die;
  
      $permissions = DB::table('permissions')->whereNotIn('id',$permission_ids)->select('id','name','created_at','updated_at')->get();
  
        $data = \Config::get('admin_success.record_found');
        $data['data'] = $role ;
        $data['permissions'] = $permissions ;
        return Response::json($data);
    }


}
