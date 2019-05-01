<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chats\GetPersonalChatRequest;
use App\Http\Requests\Chats\SendMessageRequest;
use App\Http\Requests\CommonUserRequest;
use App\Http\Traits\CommonTrait;
use App\Interfaces\ChatInterface;
use App\Models\Message;
use App\Models\Friend;
use App\User;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Input;
use DB;

class ChatsController extends Controller implements ChatInterface
{
    use CommonTrait;



        /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *   path="/chat/getPersonalChat",
     *   summary="getPersonalChat",
     *   produces={"application/json"},
     *   tags={"chat"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *  @SWG\Parameter(
     *     name="user_id",
     *     in="query",
     *     required=true,
     *     type="number",
     *     description="user id"
     *   ), 
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
       * )
     *
     */


    /**
     *  Function :  Get personal chat
     * @queryParam friend_id integer required id of friend.
     * @return response (status, message, success/failure)
     */

    public function getPersonalChat(GetPersonalChatRequest $request){
        $requested_data = $request->all();
    	$messages = Message::where(function ($query) use($request) {
                $query->where('sender_id',$request->user_id)
               ->where('receiver_id', Auth::user()->id);
            })->orWhere(function ($query) use($request){
                $query->where('receiver_id', $request->user_id)
               ->where('sender_id', Auth::user()->id);
            })
            ->with(['senderData','receiverData'])
            ->orderBy('created_at','desc')
            ->paginate(50);
            $data = \Config::get('success.get');     # success results
            $data['data'] = $messages;
            $data['logged_user_id'] = $requested_data['data']['id'];
            return Response::json($data);        
    }


            /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *   path="/chat/getInboxChat",
     *   summary="getInboxChat",
     *   produces={"application/json"},
     *   tags={"chat"},
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
    
    /**
     *  Function :  Get inbox chat
     * @return response (status, message, success/failure)
     */

    public function getInboxChat(Request $request)
    {

      $id = Auth::user()->id;
      $deleted = ",".Auth::user()->id." ";

      $messages = Message::where(function ($query){
                $query->where('receiver_id',  Auth::user()->id)
                      ->orWhere('sender_id', Auth::user()->id);
            })
            ->where('last',1)
            ->with(['senderData','receiverData'])
            ->orderBy('created_at','desc')
            ->where('deleted', 'NOT LIKE', '%'.$deleted.'%')
            ->paginate(\Config::get('variable.page_per_record'));
            $data = \Config::get('success.get');     # success results
            $data['logged_user_id'] = $id ;
            $data['data'] = $messages;
            return Response::json($data);
    }



     /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/chat/sendMessage",
     *   summary="sendMessage",
     *   produces={"application/json"},
     *   tags={"chat"},
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
     *     description = "sendMessage",
     *     @SWG\Schema(ref="#/definitions/sendMessage"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="sendMessage",
     *     allOf={
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="user_id",
     *                 type="number",
     *             )
     *         ),
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="message",
     *                 type="string",
     *             )
     *         )
     *     }
     * )
     *
     */

    
    /**
     *  Function :  send message to specific user
     *  @bodyParam 
     * @return response (status, message, success/failure)
     */

    public function sendMessage(SendMessageRequest $request)
    {
        $requested_data = $request->all();
        $id = $requested_data['data']['id'];
        Message::Create(['sender_id'=>$id,'receiver_id'=>$requested_data['user_id'],'created_at'=>time()]);
        $requested_data['type'] = 5;
        $requested_data['story_id']=0;
        $this->sendNotification($requested_data,array(),$requested_data['user_id']);
        $data = \Config::get('success.message_sent');     # success results
        #return response
        return Response::json($data);
    }

    
}
