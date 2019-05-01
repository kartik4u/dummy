<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Faq\CreateFaqRequest;
use App\Http\Requests\Admin\Faq\EditFaqRequest;
use App\Http\Requests\Admin\Page\GetPageRequest;
use App\Http\Requests\Admin\Page\UpdatePageRequest;
use App\Http\Requests\Admin\Faq\ViewFaqRequest;
use App\Http\Requests\Admin\Faq\DeleteFaqRequest;
use App\Interfaces\AdminPagesInterface;
use App\Models\Page;
use App\Models\Faq;
use App\User;
use App\Role;
use Config;
use Illuminate\Http\Request;
use Response;

class PagesController extends Controller implements AdminPagesInterface
{

     /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/admin/getFaqs",
     *   summary="get all FAQ data",
     *   produces={"application/json"},
     *   tags={"ADMIN PAGE APIS"},
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
     * Function : function to get all FAQs
     * Input:
     * Output: success, error
     */
    public function getFaqs(Request $request)
    {
        $requested_data = $request->all();
        $response = Faq::where('status', 1)->select(['id', 'slug', 'question', 'answer', 'status', 'updated_at', 'status'])
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
     *   path="/admin/viewFaq",
     *   summary="get single FAQ data",
     *   produces={"application/json"},
     *   tags={"ADMIN PAGE APIS"},
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
     *     description="faq id"
     *   ), 
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )  
     *
     */

    /*
     * Function : function to get single FAQ
     * Input: id
     * Output: success, error
     */
    public function viewFaq(ViewFaqRequest $request)
    {
        $requested_data = $request->all();
        $response = Faq::where(['id' => $requested_data['id']])->select(['id', 'slug', 'question', 'answer', 'status', 'updated_at', 'status'])->first();
        $data = \Config::get('admin_success.record_found');
        $data['data'] = $response;
        return Response::json($data);
    }


   
     /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/admin/createFaq",
     *   summary="create faq",
     *   produces={"application/json"},
     *   tags={"ADMIN PAGE APIS"},
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
     *     description = "createFaq",
     *     @SWG\Schema(ref="#/definitions/createFaq"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="createFaq",
     *     allOf={
     *         @SWG\Schema(
     *            @SWG\Property(
     *                 property="question",
     *                 type="string",
     *             ),
     *            @SWG\Property(
     *                 property="answer",
     *                 type="string",
     *             )
     *         )
     *     }
     * )
     *
     */
    
    /*
     * Function : function to Create/Add FAQ
     * Input: question,answer
     * Output: success, error
     */

    public function createFaq(CreateFaqRequest $request)
    {
        $requested_data = $request->all();
        $created = Faq::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        if ($created) {
            $data = \Config::get('admin_success.faq_success_created');
        } else {
            $data = \Config::get('admin_error.not_created');
        }
        return Response::json($data);
    }


   
     /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/admin/editFaq",
     *   summary="edit faq",
     *   produces={"application/json"},
     *   tags={"ADMIN PAGE APIS"},
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
     *     description = "editFaq",
     *     @SWG\Schema(ref="#/definitions/editFaq"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="editFaq",
     *     allOf={
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="id",
     *                 type="number",
     *             ),
     *            @SWG\Property(
     *                 property="question",
     *                 type="string",
     *             ),
     *            @SWG\Property(
     *                 property="answer",
     *                 type="string",
     *             )
     *         )
     *     }
     * )
     *
     */
    
     /*
     /*
     * Function : function to edit FAQ
     * Input: question,answer
     * Output: success, error
     */

    public function editFaq(EditFaqRequest $request)
    {
        $requested_data = $request->all();
        $updated = Faq::where('id', $requested_data['id'])->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'updated_at' => time(),
        ]);
        $data = \Config::get('admin_success.faq_success_updated');
        return Response::json($data);
    }


  
     /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/admin/deleteFaq",
     *   summary="delete faq",
     *   produces={"application/json"},
     *   tags={"ADMIN PAGE APIS"},
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
     *     description = "deleteFaq",
     *     @SWG\Schema(ref="#/definitions/deleteFaq"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="deleteFaq",
     *     allOf={
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="id",
     *                 type="string",
     *             )
     *         )
     *     }
     * )
     *
     */

    /*
     * Function : function to delete FAQ
     * Input: id
     * Output: success, error
     */
    public function deleteFaq(DeleteFaqRequest $request)
    {
        $requested_data = $request->all();
        $response = Faq::where('id', $requested_data['id'])->delete();
        if ($response) {
            $data = \Config::get('admin_success.faq_success_deleted');
        } else {
            $data = \Config::get('admin_error.not_deleted');
        }
        return Response::json($data);
    }



    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/admin/getPage",
     *   summary="get single page",
     *   produces={"application/json"},
     *   tags={"ADMIN PAGE APIS"},
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
     *     description="faq id"
     *   ), 
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )  
     *
     */

     /*
     * Function : function to get single page
     * Input: id
     * Output: success, error
     */
    public function getPage(GetPageRequest $request)
    {
        $data = [];
        $requested_data = $request->all();
        $response = Page::where(['id' => $requested_data['id']])->first();
        $data = \Config::get('admin_success.record_found');
        $data['data'] = $response;
        return Response::json($data);
    }

     /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/admin/getPages",
     *   summary="get all static pages",
     *   produces={"application/json"},
     *   tags={"ADMIN PAGE APIS"},
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
     * Function : function to get all static pages
     * Input: 
     * Output: success, error
     */
    public function getPages(Request $request)
    {
        $data = [];
        $requested_data = $request->all();
        $response = Page::orderby('status','desc')->paginate(config('variable.page_per_record'));
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
     * @SWG\Post(
     *   path="/admin/updatePage",
     *   summary="admin update page",
     *   produces={"application/json"},
     *   tags={"ADMIN PAGE APIS"},
     *    @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "updatePage ,id:->page id,meta_value:->Page content",
     *     @SWG\Schema(ref="#/definitions/updatePage"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="updatePage",
     *     allOf={
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="id",
     *                 type="number",
     *             ),
     *             @SWG\Property(
     *                 property="meta_value",
     *                 type="string",
     *             )
     *         )
     *     }
     * )
     *
     */
    
     /*
     * Function : function to update specific page
     * Input: id (page id)
     * Output: success, error
     */
    public function updatePage(UpdatePageRequest $request)
    {
        $data = [];
        $requested_data = $request->all();

        $pages = Page::find($requested_data['id']);
        $created_at = time();
        $version = (int) $pages->version;
        $new_version = ++$version;
        if ($request->meta_key == 'privacy-policy') {
            $name = 'Privacy Policy';
        } elseif ($request->meta_key == 'term') {
            $name = 'Terms & Conditions';
        } elseif ($request->meta_key == 'about-us') {
            $name = 'About Us';
            $response = Page::where(['id' => $requested_data['id']])->update(['meta_value' => $requested_data['meta_value'], 'updated_at' => time()]);
            $data = \Config::get('admin_success.page_success_updated');
            return Response::json($data);
        } elseif ($request->meta_key == 'donation') {
            $name = 'Donation Detail';
            $response = Page::where(['id' => $requested_data['id']])->update(['meta_value' => $requested_data['meta_value'], 'updated_at' => time()]);
            $data = \Config::get('admin_success.page_success_updated');
            return Response::json($data);
        }
        
        $pages = Page::Create([
            'version' => $new_version,
            'meta_key' => $request->meta_key,
            'name' => $name,
            'meta_value' => $request->meta_value,
            'status' => 1,
            'created_at' => $created_at,
            'updated_at' => $created_at
        ]);
        Page::where('id','!=',$pages->id)->where('meta_key','=',$request->meta_key)->update(['status' =>0]);
        
        if ($request->meta_key == 'privacy_policy') {
            User::where('role_id', '!=', Role::where('name', 'admin')->first()->id)->update([
                'privacy_version' => '',
            ]);
        } elseif ($request->meta_key == 'term') {
            User::where('role_id', '!=', Role::where('name', 'admin')->first()->id)->update([
                'termsandcondition_version' => '',
            ]);
        }

        //$response = Page::where(['id' => $requested_data['id']])->update(['meta_value' => $requested_data['meta_value'], 'updated_at' => time()]);
      //  $response = Page::Create(['meta_value' => $requested_data['meta_value'], 'updated_at' => time()]);

       // if ($response) {
            $data = \Config::get('admin_success.page_success_updated');
       // } else {
          //  $data = \Config::get('admin_error.not_updated');
       // }
        return Response::json($data);
    }

}
