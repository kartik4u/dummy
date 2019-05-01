<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Requests\Payment\MakePaymentRequest;
use App\Http\Controllers\Controller;
use App\Interfaces\PaymentInterface;
use Response;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Plan;
use Stripe\Stripe;
use Stripe\Subscription;
use \Stripe\Token;
use App\User;
use App\Models\SubscriptionPlan;
use App\Models\SubscribeUser;

use DB;


class PaymentController extends Controller implements PaymentInterface
{
    	/**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/payment/generateToken",
     *   summary="subcribePayment",
     *   produces={"application/json"},
     *   tags={"subcribePayment"},  
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */

	public function generateToken(Request $request){

		Stripe::setApiKey('sk_test_nP29UEka0qkehcuCJEiKgDSw00VuX4rHaE');
		$token = \Stripe\Token::create([
		  'card' => [
		    'number' => '4242424242424242',
		    'exp_month' => 4,
		    'exp_year' => 2020,
		    'cvc' => '314'
		  ]
		]);

		$data['token'] = $token->id;
		return Response::json($data);
	}


	/**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/payment/subcribePayment",
     *   summary="subcribePayment",
     *   produces={"application/json"},
     *   tags={"subcribePayment"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="token",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "Enter stripe token",
     *   ),   
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */

	public function subcribePayment(MakePaymentRequest $request){
        $requested_data = $request->all();
		$subscription_plan_id = User::where('id',$requested_data['data']['id'])->first()->subscription_plan_id;
        $plan_id = DB::table('subscription_plans')->where('id',$subscription_plan_id)->first()->plan_id;
		try {
			Stripe::setApiKey('sk_test_nP29UEka0qkehcuCJEiKgDSw00VuX4rHaE');
			$customer = \Stripe\Customer::create(array(
	    	  "source" => $request->token, // The token submitted from Checkout
            ));
           
	    	$subcriptions = \Stripe\Subscription::create(array(
	    	  "customer" => $customer->id,
	    	  "items" => array(
	    		array( 
	    		  "plan" => trim($plan_id)
	    		),
	    	  ),
            ));
            
    	  	if ($subcriptions->id) {
    	  		$subscriptiondata['user_id'] = $request['data']['id'];
    	  		$subscriptiondata['plan_id'] = $subcriptions->plan->id;
    	  		$subscriptiondata['charge_id'] = $subcriptions->customer;
    	  		$subscriptiondata['current_period_start'] = $subcriptions->current_period_start;
				$subscriptiondata['current_period_end'] = $subcriptions->current_period_end;
				$subscriptiondata['canceled_at'] = $subcriptions->canceled_at;
				$subscriptiondata['cancel_at_period_end'] = $subcriptions->cancel_at_period_end;
				$subscriptiondata['subcribe_id'] = $subcriptions->id;
				$subscriptiondata['amount'] = $subcriptions->plan->amount;
				$subscriptiondata['created_at'] = time();
				$subscriptiondata['status'] = $subcriptions->status == 'active' ? '1' : '0';
				SubscribeUser::create($subscriptiondata);
                $data['message'] = 'Your payment has been completed successfully';
                $data['status'] =  \Config::get('success.code');
                return Response::json($data);
            }
		}
		catch (\Stripe\Error\Card $e) {
            $body = $e->getJsonBody(); // Since it's a decline, \Stripe\Error\Card will be caught
            $err = $body['error'];
            $data["status"] =   \Config::get('error.code');
            $data["message"] = $err['message'];
            return Response::json($data);
        } catch (\Stripe\Error\InvalidRequest $e) {
            // Invalid parameters were supplied to Stripe's API
            $body = $e->getJsonBody();
            $err = $body['error'];
            $data["status"] =  \Config::get('error.code');
            $data["message"] =$err['message'];
            return Response::json($data);
        } catch (Exception $e) {
            $data["status"] =   \Config::get('error.code');
            $data["message"] =$e->getMessage();
            return Response::json($data);
        }
		
		
	}


	/*private function createCustomer($requested_data)
    {
        Stripe::setApiKey('sk_test_cnh5NIVfv4P24BjFcryX09w7');
        $user = User::where('id', $requested_data["data"]["id"])->first();
        if ($user->stripe_id && $user->stripe_id != '') {
            return $user->stripe_id;
        } else {
            $customer = Customer::create(array(
                'email' => $user->email,
                'source' => $requested_data['token'],
            ));
            User::where('id', $requested_data["data"]["id"])->update(["stripe_id" => $customer->id]);
            return $customer->id;
        }
    }
*/






}
