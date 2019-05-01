<?php

/*
 * Controller Name  : UserController
 * Author           : Ritu
 * Author Contact   : Ritu@ignivasolutions.com
 * Created Date     : 4-04-2018
 * Description      : This controller manage address of university
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
use App\UserAddress;

class AddressController extends Controller {

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *  @SWG\Get(
     *   path="/frontend/address/get-addresses",
     *   summary="Get Addresses",
     *   produces={"application/json"},
     *   tags={"University/Address"},
     *   @SWG\Parameter(
     *     name="token",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     description="Page number 1/2/3 default is 1",
     *     required=true,
     *     type="number"
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     * )
     *
     */
    public function getAddresses(Request $request) {

        $requested_data = $request->all();
        $page_record = \Config::get('variable.page_per_record');
        #Get university address
        $address = UserAddress::where('user_id', $requested_data['data']['id'])->orderBy('id')->paginate($page_record)->toArray();
        
        if (isset($address['data']) && !empty($address['data'])) {
            $success = \Config::get('success.record_found');
            $success['data'] = $address;
            return Response::json($success);
        } else {
            return Response::json(\Config::get('error.no_record_found'));
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *  @SWG\Get(
     *   path="/frontend/address/get-single-addresses",
     *   summary="Get Addresses",
     *   produces={"application/json"},
     *   tags={"University/Address"},
     *   @SWG\Parameter(
     *     name="token",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="id",
     *     in="query",
     *     required=true,
     *     description = "Enter address id here",
     *     type="string",
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     * )
     *
     */
    public function getSingleAddresses(Request $request) {

        $requested_data = $request->all();
        #Get university address
        $result = UserAddress::where('id', $requested_data['id'])->where('user_id', $requested_data['data']['id'])->first(); #get single result from university address

        if (count($result) > 0) {
            $success = \Config::get('success.record_found');
            $success['data'] = $result;
            return Response::json($success);
        } else {
            return Response::json(\Config::get('error.no_record_found'));
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *  @SWG\Post(
     *   path="/frontend/address/add-edit-addresses",
     *   summary="add Addresses",
     *   produces={"application/json"},
     *   tags={"University/Address"},
     *   @SWG\Parameter(
     *     name="token",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="id",
     *     in="formData",
     *     required=false,
     *     description="Enter address id here to edit specific address",
     *     type="integer",
     *   ),
     *   @SWG\Parameter(
     *     name="campus_name",
     *     in="formData",
     *     required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="postal_code",
     *     in="formData",
     *     required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="city",
     *     in="formData",
     *     required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="country",
     *     in="formData",
     *     required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="latitude",
     *     in="formData",
     *     required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="longitude",
     *     in="formData",
     *     required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="address",
     *     in="formData",
     *     required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="address2",
     *     in="formData",
     *     required=true,
     *     type="string",
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     * )
     *
     */
    public function addEditAddresses(Request $request) {

        $requested_data = $request->all();

        $rule = ['campus_name' => 'required', 'postal_code' => ['required'], 'city' => 'required', 'country' => 'required', 'latitude' => 'required',
            'longitude' => 'required', 'address' => 'required'];
        $messages = ['longitude.required' => 'Please choose address from suggestions',
            'latitude.required' => 'Please choose address from suggestions'
            , 'required' => 'Please enter :attribute'];

        $validator = Validator::make($requested_data, $rule, $messages);  #Check validation
        if ($validator->fails()) { #Check validation pass or fail
            return Response::json((new Controller)->validatedata($validator));
        }

        $requested_data['user_id'] = $requested_data['data']['id'];
        $requested_data['updated_at'] = time();
        unset($requested_data['data']);

        if (isset($requested_data['id'])) { #edit university address
            $result = UserAddress::where('id', $requested_data['id'])->update($requested_data);
            if ($result == 1) {
                return Response::json(\Config::get('success.edit_address'));
            } else {
                return Response::json(\Config::get('error.incorrect_id'));
            }
        } else { #add university address
            $requested_data['created_at'] = time();
            $result = UserAddress::create($requested_data);
            return Response::json(\Config::get('success.add_address'));
        }
    }



        /**
     * @return \Illuminate\Http\JsonResponse
     *
     *  @SWG\Post(
     *   path="/frontend/address/delete-addresses",
     *   summary="delete Addresses",
     *   produces={"application/json"},
     *   tags={"University/Address"},
     *   @SWG\Parameter(
     *     name="token",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="id",
     *     in="formData",
     *     required=true,
     *     description="Enter address id here to delete specific address",
     *     type="integer",
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     * )
     *
     */

    public function deleteAddresses(Request $request) {

        $requested_data = $request->all();

        $rule = ['id' => 'required'];
        $messages = ['required' => 'Please enter address :attribute'];

        $validator = Validator::make($requested_data, $rule, $messages);  #Check validation
        if ($validator->fails()) { #Check validation pass or fail
            return Response::json((new Controller)->validatedata($validator));
        }
        if (isset($requested_data['id'])) { #delete university address
            $result = UserAddress::where('id', $requested_data['id'])->delete();
            if ($result == 1) {
                return Response::json(\Config::get('success.delete_address'));
            } else {
                return Response::json(\Config::get('error.incorrect_id'));
            }
        }

    }

}
