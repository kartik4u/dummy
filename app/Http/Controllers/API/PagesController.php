<?php

/*
 * Controller Name  : PagesController
 * Author           : narinder
 * Author Contact   : narinder.sing@ignivasolutions.com
 * Created Date     : 2-04-2019
 * Description      : This controller use manage static pages
 */

namespace App\Http\Controllers\API;

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
use App\Models\Page;
use App\ActivityLog;
use App\Jobs\ContactusJob;
use App\Interfaces\PageInterface;
use App\Http\Requests\Pages\ContactRequest;
use App\Http\Requests\Pages\SendPageMailRequest;
use App\Http\Traits\CommonTrait;
use App\Http\Traits\UserTrait;




class PagesController extends Controller  implements PageInterface
{
    use CommonTrait, UserTrait;
    /**
     * @SWG\Post(
     *   path="/pages/contact",
     *   summary="send contact us",
     *   produces={"application/json"},
     *   tags={"Pages"},
     *  @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "",
     *     @SWG\Schema(ref="#/definitions/contact"),
     *   ),  
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )  
     * @SWG\Definition(
     *     definition="contact",
     *     allOf={
     *         @SWG\Schema(
     *              required={"name","email","query","subject","message"},
     *              @SWG\Property(property="name", type="string"),
     *              @SWG\Property(property="email", type="string"),
     *              @SWG\Property(property="message", type="string"),
     *              @SWG\Property(property="subject", type="string")
     *         )
     *     }
     * )
     */
    public function contact(ContactRequest $request) {
        #Validations
        $requested_data = $request->all();
       
        $email_array = array(
            'server_url' => \Config::get('variable.SERVER_URL'),
            'to' =>trim($requested_data["email"]),
            'from' =>\Config::get('variable.ADMIN_EMAIL'),
            'from_name' => 'Yourfut',
            'subject' =>  trim($requested_data["subject"]),
            'view' => 'mail.contact',
            'name' => trim($requested_data["name"]),
            'msg' => trim($requested_data["message"])
        );
        $requested_data['email_array'] = $email_array;
        #send contact us email
        ContactusJob::dispatch($requested_data)->delay(now()->addSeconds(3));
        $data = \Config::get('success.mail_sent');     # success results
        return Response::json($data);
    }

       /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/pages/get-terms-and-conditions",
     *   summary="get Terms and condition data",
     *   produces={"application/json"},
     *   tags={"Pages"},
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )  
     *
     */
    public function getTermsAndConditions(Request $request) {
        $pages = Page::where('meta_key', 'term')->where('status',1)->orderby('created_at','desc')->first();
        $data = \Config::get('success.get');     # success results
        $data['data'] =$pages; 
        #return response
        return Response::json($data);
    }



       /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/pages/getDonation",
     *   summary="get Donation",
     *   produces={"application/json"},
     *   tags={"Pages"},
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
     * )
     *
     */
    public function getDonation(Request $request) {
        $pages = Page::where('meta_key', 'donation')->where('status',1)->orderby('created_at','desc')->first();
        $data = \Config::get('success.get');     # success results
        $data['data'] =$pages; 
        #return response
        return Response::json($data);
    }


            /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/pages/aboutus",
     *   summary="about us",
     *   produces={"application/json"},
     *   tags={"Pages"},
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )  
     *
     */
    public function getAboutus(Request $request) {
        $pages = Page::where('meta_key', 'about-us')->where('status',1)->first();
        $data = \Config::get('success.get');     # success results
        $data['data'] =$pages; 
        #return response
        return Response::json($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/pages/get-privacy-policy",
     *   summary="get Privacy Ploicy data",
     *   produces={"application/json"},
     *   tags={"Pages"},
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )  
     *
     */
    public function getPrivacyPolicy(Request $request) {
        $pages = Page::where('meta_key', 'privacy-policy')->where('status',1)->orderby('created_at','desc')->first();
        $data = \Config::get('success.get');     # success results
        $data['data'] =$pages; 
        #return response
        return Response::json($data);
    }



        /**
     * @SWG\Post(
     *   path="/pages/sendPageMail",
     *   summary="send page mail",
     *   produces={"application/json"},
     *   tags={"Pages"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string",
     *   ),
     *  @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "meta_key:- term,about-us,donation,privacy-policy",
     *     @SWG\Schema(ref="#/definitions/sendPageMail"),
     *   ),  
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )  
     * @SWG\Definition(
     *     definition="sendPageMail",
     *     allOf={
     *         @SWG\Schema(
     *              required={"meta_key"},
     *              @SWG\Property(property="meta_key", type="string"),
     *         )
     *     }
     * )
     */
    public function sendPageMail(SendPageMailRequest $request) {
        $requested_data =  $request->all();
        $page_data = Page::where('meta_key',$requested_data['meta_key'])->orderBy('version','desc')->first();
        $requested_data['page_data'] = $page_data;
        #send mail 
        $this->sendMail($requested_data);
        $data = \Config::get('success.mail_sent');     # success results
        return Response::json($data);
    }

}
