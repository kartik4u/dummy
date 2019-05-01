<?php

#Controller Name: PageController
#Developer      : Prabhat
#Purpose        : Manage pages content

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;
use Validator;
use Hash;
use Auth;
use JWTAuth;
use App\Config;
use App\Page;

class PageController extends Controller {

    public function __construct() {
        
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/admin/pages/page-listing",
     *   summary="Page listing",
     *   produces={"application/json"},
     *   tags={"Admin-Pages"},
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
    public function pageListing(Request $request) {
        $requested_data = $request->all();
        $page_record = \Config::get('variable.page_per_record');
        $pages = Page::select(['id', 'slug', 'title', 'status', 'created_at'])->orderBy('id', 'DESC')->paginate($page_record);
        #Check Final data here and send 
        if (isset($pages) && !empty($pages)) {
            $data = \Config::get('success.success_data');     # success results
        } else {
            $data = \Config::get('success.no_record');      # no results
        }
        $data['data'] = $pages;
        return Response::json($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/admin/pages/get-single-page",
     *   summary="Page listing",
     *   produces={"application/json"},
     *   tags={"Admin-Pages"},
     *   @SWG\Parameter(
     *     name="token",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string"
     *   ),
     *  @SWG\Parameter(
     *     name="slug",
     *     in="query",
     *     required=true,
     *     type="integer"
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function getSinglePage(Request $request) {
        $requested_data = $request->all();
        $page = Page::where('status', 1)->where('slug', $requested_data['slug'])->orderBy('id', 'DESC')->first();
        #Check Final data here and send 
        if (isset($page) && !empty($page)) {
            $data = \Config::get('success.success_data');     # success results
        } else {
            $data = \Config::get('error.no_record_found');      # no results
        }
        $data['data'] = $page;
        return Response::json($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/admin/pages/save-page-content",
     *   summary="save page data",
     *   produces={"application/json"},
     *   tags={"Admin-Pages"},
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
     *     @SWG\Schema(ref="#/definitions/savePageContent"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     * )
     * @SWG\Definition(
     *     definition="savePageContent",
     *     allOf={
     *         @SWG\Schema(
     *             required={"slug","title","content"},
     *             @SWG\Property(property="slug", type="string"),
     *              @SWG\Property(property="content", type="string")
     *         )
     *     }
     * )
     *
     */
    public function savePageContent(Request $request) {
        $requested_data = $request->all();
        $rule = ['slug' => 'required|exists:pages,slug', 'content' => 'required'];
        #check validation
        $validator = Validator::make($requested_data, $rule);
        if ($validator->fails()) {
            return Response::json($this->validateData($validator));
        }
        $pages = Page::where('slug', $requested_data['slug'])->first();
        # update data
        if (!empty($pages)) {
            #find the last version
            $page = Page::where('status', 1)->where('slug', $requested_data['slug'])->orderBy('id', 'DESC')->first();
            if (!empty($page)) {
                $explode_version = explode('v', $page->version);
                $version = $explode_version[1] + 1;
            } else {
                $version = 1;
            }
            #update status for older entries
            $update = Page::where('slug', $requested_data['slug'])->update(['status' => 0, 'updated_at' => time()]);

            $insert = Page::create(['content' => $request->content, 'slug' => $requested_data['slug'], 'title' => $pages->title,
                        'version' => "v" . $version, 'created_at' => time(), 'updated_at' => time()]);
            if ($insert) {
                return Response::json(\Config::get('success.success_page_updated'));
            }
        }
        return Response::json(\Config::get('error.failed_to_update'));
    }

}
