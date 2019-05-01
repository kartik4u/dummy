<?php

/*
 * Controller Name  : AuthController
 * Author           : Prabhat
 * Author Contact   : prabhat.thakur@ignivasolutions.com
 * Created Date     : 23-03-2018
 * Description      : This controller use manage static pages
 */

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Frontend;
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
use App\Page;

class PagesController extends Controller {

    /**
     * @SWG\Post(
     *   path="/pages/contact",
     *   summary="send contact us",
     *   produces={"application/json"},
     *   tags={"Home - Contact"},
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
     *              @SWG\Property(property="message", type="string")
     *         )
     *     }
     * )
     */
    public function contact(Request $request) {
        #Validations
        $requested_data = $request->all();
        $rule = ['name' => 'required', 'email' => 'required|email', 'message' => 'required'];
        $validator = Validator::make($requested_data, $rule);
        if ($validator->fails()) {
            return $this->validateData($validator);
        }
        #send contact us email
        $send_contact_mail = $this->sendContactUsMail($requested_data);
        if ($send_contact_mail) {
            #send contact us confirmation email
            $send_confirmation_mail = $this->sendConfirmationMail($requested_data);
            $response = \Config::get('success.success_contact_mail');
        } else {
            $response = \Config::get('error.failed_to_send_email');
        }
        return Response::json($response);
    }

    /* function to send a contact us email to admin */

    private function sendContactUsMail($data) {
        #data to send in email
        $email_array = array(
            'server_url' => \Config::get('variable.SERVER_URL'),
            'to' => \Config::get('variable.ADMIN_EMAIL'),
            'from' => trim($data["email"]),
            'from_name' => trim($data["name"]),
            //'subject' => \Config::get('variable.CONTACT_EMAIL_SUBJECT'),
            'subject' => "Contact Us : " . trim($data["subject"]),
            'sub' => trim($data["subject"]),
            'view' => 'email.contact',
            'name' => trim($data["name"]),
            'msg' => trim($data["message"])
        );
        #Send Verification Email
        return $this->sendEmail($email_array);  #Send Verification Email                
    }

    /* function to send a contact us email to admin */

    private function sendConfirmationMail($data) {
        #data to send in email
        $email_array = array(
            'server_url' => \Config::get('variable.SERVER_URL'),
            'to' => trim($data["email"]),
            'from' => \Config::get('variable.ADMIN_EMAIL'),
            'from_name' => 'Edseen',
            'subject' => \Config::get('variable.CONTACT_EMAIL_SUBJECT'),
            'view' => 'email.contact_confirmation',
            'name' => trim($data["name"])
        );
        #Send Verification Email
        return $this->sendEmail($email_array);  #Send Verification Email                
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/pages/get-terms-and-conditions",
     *   summary="get Terms and condition data",
     *   produces={"application/json"},
     *   tags={"Home - Pages"},
     *   @SWG\Parameter(
     *     name="version",
     *     in="formData",
     *     required=false,
     *     description = "Version",
     *     type="integer"
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )  
     *
     */
    public function getTermsAndConditions(Request $request) {
        $requested_data = $request->all();
        if (isset($requested_data['version']) && !empty($requested_data['version'])) {
            $pages = Page::where('version', trim($requested_data['version']))->where('slug', 'terms-and-conditions')->first();
        } else {
            $pages = Page::where('slug', 'terms-and-conditions')->where('status', 1)->orderBy('id', "DESC")->first();
        }
        if (!empty($pages)) {
            $data['data'] = $pages;
            $data['status'] = 200;
        } else {
            $data['status'] = 400;
            $data['description'] = 'Data not found';
        }

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
     *   tags={"Home - Pages"},
     *   @SWG\Parameter(
     *     name="version",
     *     in="formData",
     *     required=false,
     *     description = "Version",
     *     type="integer"
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )  
     *
     */
    public function getPrivacyPolicy(Request $request) {
        $requested_data = $request->all();
        if (isset($requested_data['version']) && !empty($requested_data['version'])) {
            $pages = Page::where('version', trim($requested_data['version']))->where('slug', 'privacy-policy')->first();
        } else {
            $pages = Page::where('slug', 'privacy-policy')->where('status', 1)->orderBy('id', "DESC")->first();
        }
        
        if (!empty($pages)) {
            $data['data'] = $pages;
            $data['status'] = 200;
        } else {
            $data['status'] = 400;
            $data['description'] = 'Data not found';
        }

        #return response
        return Response::json($data);
    }

}
