<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\CommonTrait;
use App\Http\Traits\UserTrait;
use App\Interfaces\StoryInterface;
use App\Jobs\SignupJob;
use App\Models\ViewProfile;
use App\Models\Page;
use App\Models\Genre;
use App\Models\Story;
use App\Models\UserGenre;
use App\Models\StoryGenre;
use App\Models\ViewedStory;
use App\Models\SavedStory;
use App\Models\CommingSoon;
use App\Models\Follower;
use App\Models\AutherView;
use App\Models\Download;
use App\Models\Notification;
use App\Models\AdditionalUserInfo;
use App\Models\Favourite;
use App\Models\Episode;
use App\Models\Comment;
use App\Role;
use App\User;
use Config;
use DB;
use Hash;
use App\Http\Requests\Stories\ViewStoryRequest;
use App\Http\Requests\Stories\SaveAdditionalInfoRequest;
use App\Http\Requests\Stories\AddStoryRequest;
use App\Http\Requests\Stories\AddEpisodeRequest;
use App\Http\Requests\Stories\StoryReportRequest;
use App\Http\Requests\CommonStoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Image;
use Lcobucci\JWT\Parser;
use Mail;
use Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Routing\Route;


class StoriesController extends Controller implements StoryInterface
{
    use CommonTrait, UserTrait;


function __construct(request $request,route $route){
     $method = $route->getActionName();
     $method= explode('@', $method);
     $action  = $method[1];
     $headers = apache_request_headers();
     if($action=='getHomePage'){
         if(isset($headers['Authorization'])){
            $this->middleware(['auth:api','user_data'])->only('getHomePage');
         } else{
         }
     }
}

    
    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *   path="/story/getAllGenres",
     *   summary="getAllGenres",
     *   produces={"application/json"},
     *   tags={"Stories"},
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

    function getAllGenres(Request $request){
        $requested_data = $request->all();
        $info = Genre::select('id','name')->get();
        $data = \Config::get('success.get');
        $data['data'] = $info;
        return Response::json($data);
    }



    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *   path="/story/getMyGenres",
     *   summary="getMyGenres",
     *   produces={"application/json"},
     *   tags={"Stories"},
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

    function getMyGenres(Request $request){
        $requested_data = $request->all();
  
        $info= UserGenre::select('id','user_id','genre_id')->where('user_id',$requested_data['data']['id'])->with(['getMyGenre'])
       ->get();
       
       $data = \Config::get('success.get');
       $data['data'] = empty($info)?(object) []:$info;
       return Response::json($data);
    }




     /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\POST(
     *   path="/story/saveGenres",
     *   summary="save genres",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *   tags={"Stories"},
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "",
     *     @SWG\Schema(ref="#/definitions/saveGenres"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="saveGenres",
     *     allOf={
     *         @SWG\Schema(
     *            @SWG\Property(
     *               property="genre_ids",
     *               type="array",
     *              @SWG\Items(type="number")
     *            ),
     *         ),
     *     }
     * )
     *
     */


    function saveGenres(Request $request){
        $requested_data = $request->all();
        UserGenre::where('user_id',$requested_data['data']['id'])->delete();
        $this->saveUserGenres($requested_data['data']['id'],$requested_data['genre_ids']);
        $data['status'] = \Config::get('success.code');
        $data['data'] =(object) [];
        return Response::json($data);
    }




    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *   path="/story/getHomePage",
     *   summary="getHomePage",
     *   produces={"application/json"},
     *   tags={"Stories"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=false,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *  @SWG\Parameter(
     *     name="search",
     *     in="query",
     *     required=false,
     *     type="string",
     *     default="search",
     *     description="search"
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
       * )
     *
     */

    function getHomePage(Request $request){
        $requested_data = $request->all();
        $requested_data['data']['id'] =  isset($requested_data['data'])?$requested_data['data']['id']:0;
        $saved_story_ids = SavedStory::where('user_id',$requested_data['data']['id'])->pluck('story_id')->toArray();
        $viewed_story_ids = ViewedStory::where('user_id',$requested_data['data']['id'])->where('type',1)->pluck('story_id')->toArray();
        $data = \Config::get('success.get');
        $data['saved_stories'] = Story::select('id','name','url','rating','created_at')->whereIn('id',$saved_story_ids)
        ->take(10)->get();

        $data['viewd_stories'] = Story::select('id','name','url','rating','created_at','chapters_count','share_count','share_count as avg')->whereIn('id',$viewed_story_ids)->with(['getViewStory'=>function($q) use($requested_data){
            $q->where('user_id',$requested_data['data']['id'])->groupBy('story_id');
        },
        'getViewStory.getEpisodeView'
        ])
        ->take(10)->where('status',1)->get();

        $data['best_rated_writers'] = User::select('id','role_id', 'name','gender', 'email', 'dob', 'city', 'description', 'profile_image','slug', 'termsandcondition_version', 'phone', 'privacy_version', 'device_type', 'device_id' ,'current_login','last_login', 'status', 'push_notification_status','created_at', 'updated_at','avg_rating','stories_count')->where('role_id',Role::where('name','writer')->first()->id)
        ->take(10)->where('auther_status',1)->orderBy('avg_rating', 'desc')->get();

        $data['comming_soon'] = CommingSoon::with(['getUser'])->
        take(10)->get();
       
        $data['best_rated_stories'] = Story::select('id','name','url','rating','created_at','chapters_count','share_count')->where('status',1)
        ->take(10)->orderBy('rating', 'desc')->get();

        $data['recently_lunched'] = Story::select('id','name','url','rating','created_at')->where('status',1)
        ->orderBy('created_at', 'desc')->first();

        $data['auther_views'] = AutherView::select('id','message','created_at','user_id')->with(['getUser'])
        ->orderBy('created_at', 'desc')->first();

        // if searching
        if(isset($requested_data['search'])){
            $data_search = \Config::get('success.get');
            $search = $requested_data['search'];

            $data_search['stories'] = Story::select('id','name','url','rating','created_at')->where('name', 'LIKE', "%$search%")->where('status',1)
        ->take(10)->get();

          $data_search['writers'] = User::select('id','role_id', 'name','gender', 'email', 'dob', 'city', 'description', 'profile_image','slug', 'termsandcondition_version', 'phone', 'privacy_version', 'device_type', 'device_id' ,'current_login','last_login', 'status', 'push_notification_status','created_at', 'updated_at','avg_rating','stories_count')->where('name', 'LIKE', "%$search%")->where('role_id',Role::where('name','writer')->first()->id)->where('auther_status',1)
            ->take(10)->orderBy('avg_rating', 'desc')->get();
            return Response::json($data_search);
        }
        
       return Response::json($data);
    }


     /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\POST(
     *   path="/story/viewStory",
     *   summary="view Story",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *   tags={"Stories"},
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "episode_id only in case of watch episode,type=>1:story,2:episdoe",
     *     @SWG\Schema(ref="#/definitions/viewStory"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="viewStory",
     *     allOf={
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="story_id",
     *                 type="number",
     *         )
     *     ),
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="episode_id",
     *                 type="number",
     *      )
     *         ),
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="type",
     *                 type="number",
     *         )
     * ),
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="is_full_watched",
     *                 type="number",
     *         ),
     * 
     * ),
     *     
     *     }
     * )
     *
     */

    function viewStory(ViewStoryRequest $request){
        $requested_data = $request->all();
        $amount =0.02;
        if($requested_data['type']==1){
            $check = ['user_id'=>$requested_data['data']['id'],'story_id'=>$requested_data['story_id'],'type'=>$requested_data['type']];
            $insert = ['user_id'=>$requested_data['data']['id'],'story_id'=>$requested_data['story_id'],'type'=>$requested_data['type'],'created_at'=>time(),'updated_at'=>time()];
        } else{

            $check = ['user_id'=>$requested_data['data']['id'],'story_id'=>$requested_data['story_id'],'episode_id'=>$requested_data['episode_id'],'type'=>$requested_data['type']];
            $insert = ['user_id'=>$requested_data['data']['id'],'story_id'=>$requested_data['story_id'],'episode_id'=>$requested_data['episode_id'],'created_at'=>time(),'type'=>$requested_data['type']];
             // if watch full episode
            if($requested_data['is_full_watched']==1){
                $amount=0.03;
                $insert['is_full_watched']=1;
                $insert['updated_at']=time();
                $check['is_full_watched']=1;
            }
        }
        $check_status = ViewedStory::where($check)->first();
        $res = ViewedStory::updateOrCreate($check ,$insert);
        if(empty($check_status)){
            $data = \Config::get('success.story_saved');

            if(empty(!$requested_data['episode_id'])){

                if($requested_data['is_full_watched']!=1){
                    Episode::where('story_id',$requested_data['story_id'])->where('id',$requested_data['episode_id'])->increment('revenue',$amount);
                }else{
                    Episode::where('story_id',$requested_data['story_id'])->where('id',$requested_data['episode_id'])->increment('revenue_full_read',$amount);
                }
                $user_id = Story::where('id',$requested_data['story_id'])->first()->user_id;
                User::where('id',$user_id)->increment('total_revenue',$amount);
                User::where('id',$user_id)->increment('monthly_revenue',$amount);
                Story::where('id',$requested_data['story_id'])->increment('total_revenue',$amount);
            } 
            //else{
                //Story::where('id',$requested_data['story_id'])->increment('total_revenue',$amount);
            //}
        } else{
            $data = \Config::get('error.error');
        }

        $data['data'] =(object) [];
        return Response::json($data);
    }



     /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\POST(
     *   path="/story/saveStory",
     *   summary="save Story",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *   tags={"Stories"},
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "",
     *     @SWG\Schema(ref="#/definitions/saveStory"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="saveStory",
     *     allOf={
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="story_id",
     *                 type="number",
     *         )
     *     )
     *     }
     * )
     *
     */

    function saveStory(CommonStoryRequest $request){
        $requested_data = $request->all();
        $check = ['user_id'=>$requested_data['data']['id'],'story_id'=>$requested_data['story_id']];
        $insert = ['user_id'=>$requested_data['data']['id'],'story_id'=>$requested_data['story_id'],'created_at'=>time()];
        SavedStory::updateOrCreate($check ,$insert);
        $data = \Config::get('success.story_saved');
        $data['data'] =(object) [];
        return Response::json($data);
    }


        /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *   path="/story/getStories",
     *   summary="getStories",
     *   produces={"application/json"},
     *   tags={"Stories"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *  @SWG\Parameter(
     *     name="story_ids",
     *     in="query",
     *     required=false,
     *     type="string",
     *     default="1,2,3",
     *     description="story_ids"
     *   ), 
     *  @SWG\Parameter(
     *     name="search",
     *     in="query",
     *     required=false,
     *     type="string",
     *     default="",
     *     description="search"
     *   ), 
     *  @SWG\Parameter(
     *     name="type",
     *     in="query",
     *     required=false,
     *     type="string",
     *     default="saved",
     *     description="e.g 1,countinue_reading,2 saved, 3, best_rated, 4 ,comming_soon,5=favourites,5=downloads,6=mystories"
     *   ), 
     *  @SWG\Parameter(
     *     name="status",
     *     in="query",
     *     required=true,
     *     type="number",
     *     default="1",
     *     description="e.g 1:-approved,2:-declined,0:-pending"
     *   ), 
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
       * )
     *
     */

    function getStories(Request $request){
        $requested_data = $request->all();
  
        $data = Story::select('id','total_revenue','favourite_count','name','rating','chapters_count','description','duration','url','user_id','created_at','share_count','status');       
        
        if(isset($requested_data['story_ids'])){
            if(!is_array($requested_data['story_ids'])){
                $requested_data['story_ids'] = explode(",",$requested_data['story_ids']);
            }
            $data  = $data->whereIn('id',$requested_data['story_ids']);
        }


        $saved_story_ids = SavedStory::where('user_id',$requested_data['data']['id'])->pluck('story_id')->toArray();
        $viewed_story_ids = ViewedStory::where('user_id',$requested_data['data']['id'])->pluck('story_id')->toArray();
        $fav_story_ids = Favourite::where('favourite_by',$requested_data['data']['id'])->pluck('story_id')->toArray();
        $downloaded_story_ids = Download::where('user_id',$requested_data['data']['id'])->pluck('story_id')->toArray();


        if(isset($requested_data['type'])){
            if($requested_data['type']=='saved'){
                $data  = $data->whereIn('id',$saved_story_ids);
            } else if($requested_data['type']=='countinue_reading'){
                $data  = $data->whereIn('id',$viewed_story_ids);
            } else if($requested_data['type']=='favourites'){
                $data  = $data->whereIn('id',$fav_story_ids);
            } else if($requested_data['type']=='download'){
                $data  = $data->whereIn('id',$downloaded_story_ids);
            } else if($requested_data['type']=='mystories'){

                $data  = $data->where('user_id',$requested_data['data']['id']);
                $data = $data->where('status',$requested_data['status']);

            } else if($requested_data['type']=='comming_soon'){
                $data = CommingSoon::with(['getUser'])->paginate(\Config::get('variable.page_per_record'))->toArray();
                $res = \Config::get('success.get');
                $res['data'] = $data;
                return Response::json($res);
            } 
        }
        
         // story status
        if(isset($requested_data['status'])){
            $data = $data->where('status',$requested_data['status']);
        }       

        // searching exists
        if(isset($requested_data['search'])){
            $search = $requested_data['search'];
            $data = $data->where('name', 'LIKE', "%$search%");
        }

        $data  = $data->with(['getSavedStory','getViewStory'=>function($q) use($requested_data){
            $q->where('user_id',$requested_data['data']['id'])->groupBy('story_id');
        },
        'getViewStory.getEpisodeView',
        'getStoryGenre.getGenre','getViewStory',
        'myFav'=>function($q) use($requested_data){
            $q->where('favourite_by',$requested_data['data']['id']);
        }
        ])
        ->orderBy('rating', 'desc')->paginate(\Config::get('variable.page_per_record'))->toArray();
        $res = \Config::get('success.get');
        $res['data'] = $data;
        return Response::json($res);
    }



            /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *   path="/story/getStoryDetail",
     *   summary="getStoryDetail",
     *   produces={"application/json"},
     *   tags={"Stories"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *  @SWG\Parameter(
     *     name="story_id",
     *     in="query",
     *     required=true,
     *     type="string",
     *     default="1",
     *     description="story_id"
     *   ), 
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
       * )
     *
     */

    function getStoryDetail(CommonStoryRequest $request){
        $requested_data = $request->all();
        $data = Story::select('id','total_revenue','favourite_count','name','rating','chapters_count','description','duration','url','user_id','created_at','share_count')->
        with(['getUserDetail'=>function($q){
            $q->select('id','name','avg_rating','profile_image');
        },
        'getUserDetail.totalFollowers'=>function($q){
            $q;
        },
        'getStoryGenre.getGenre'=>function($q){
            $q;
        },'getEpisodes'
        ])->
        first();       
        $res = \Config::get('success.get');
        $res['data'] = $data;
        return Response::json($res);
    }



        /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *   path="/story/getWritters",
     *   summary="getWritters",
     *   produces={"application/json"},
     *   tags={"Stories"},
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
     *     default="search",
     *     description="search"
     *   ), 
     *  @SWG\Parameter(
     *     name="is_fav",
     *     in="query",
     *     required=false,
     *     type="string",
     *     default="0",
     *     description="0=no,1=yes"
     *   ), 
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
       * )
     *
     */

    function getWritters(Request $request){
        $requested_data = $request->all();

        $fav_user_ids = Favourite::where('favourite_by',$requested_data['data']['id'])->pluck('user_id')->toArray();

        $data = User::select('id','role_id', 'name','gender', 'email', 'dob', 'city', 'description', 'profile_image','slug', 'termsandcondition_version', 'phone', 'privacy_version', 'device_type', 'device_id' ,'current_login','last_login', 'status', 'push_notification_status','created_at', 'updated_at','avg_rating','stories_count')->where('role_id',Role::where('name','writer')->first()->id)->where('status',1);
        
        // searching exists
        if(isset($requested_data['search'])){
            $search = $requested_data['search'];
            $data = $data->where('name', 'LIKE', "%$search%");
        }

        // favourite data
        if($requested_data['is_fav']){
            $data = $data->whereIn('id',$fav_user_ids);
        }

        $data  = $data->with([
            'CheckFollower' => function ($q) {
            $q;
        },
        'getUserGenre.getGenre' => function ($q) {
            $q;
        },
        
         ])->orderBy('avg_rating', 'desc')->paginate(\Config::get('variable.page_per_record'))->toArray();
        $res = \Config::get('success.get');
        $res['data'] = $data;
        return Response::json($res);
    }

     /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/story/addOrdeleteDownload",
     *   summary="addOrdeleteDownload",
     *   produces={"application/json"},
     *   tags={"Stories"},
     *   @SWG\Parameter(
     *    name="Authorization",
     *    in="header",
     *    required=true,
     *    description = "Enter Token",
     *    type="string"
     *   ),
     *  @SWG\Parameter(
     *     name="story_id",
     *     in="query",
     *     required=true,
     *     type="number",
     *     default="1",
     *     description="story id"
     *   ), 
     *
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    /*
     * Main Function to delte download
     * @param Request 
     * @return type (status, success/error)
     */
    public function addOrdeleteDownload(CommonStoryRequest $request)
    {
        $requested_data = $request->all();
        $check = Download::where(['story_id'=>$requested_data['story_id'],'user_id'=>$requested_data['data']['id']])->count();
        if($check){
            Download::where(['story_id'=>$requested_data['story_id'],'user_id'=>$requested_data['data']['id']])->delete();
            $msg = 'Download deleted successfully.';
        } else{
            Download::Create(['story_id'=>$requested_data['story_id'],'user_id'=>$requested_data['data']['id'],'created_at'=>time()]);
            $msg = 'Story downloaded successfully.';
        }
        $data['status'] =  \Config::get('success.code');;
        $data['message']=$msg;
        $data['data'] = (object) [];
        return Response::json($data);
    }




         /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/story/deleteStory",
     *   summary="deleteStory",
     *   produces={"application/json"},
     *   tags={"Stories"},
     *   @SWG\Parameter(
     *    name="Authorization",
     *    in="header",
     *    required=true,
     *    description = "Enter Token",
     *    type="string"
     *   ),
     *  @SWG\Parameter(
     *     name="story_id",
     *     in="query",
     *     required=true,
     *     type="number",
     *     default="1",
     *     description="story id"
     *   ), 
     *
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    /*
     * Main Function to delte story
     * @param Request 
     * @return type (status, success/error)
     */
    public function deleteStory(CommonStoryRequest $request)
    {
        $requested_data = $request->all();
        // Episode::where(['id'=>$requested_data['story_id']])->delete();
        // Comment::where(['id'=>$requested_data['story_id']])->delete();
        // StoryGenre::where(['id'=>$requested_data['story_id']])->delete();
        // SavedStory::where(['id'=>$requested_data['story_id']])->delete();
        // Favourite::where(['id'=>$requested_data['story_id']])->delete();
        Story::where(['id'=>$requested_data['story_id']])->delete();
        $data['status'] =  \Config::get('success.code');        
        $data['message']="Story deleted successfully.";
        $data['data'] = (object) [];
        return Response::json($data);
    }



    




     /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\POST(
     *   path="/story/saveAdditionalInfo",
     *   summary="save genres",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *   tags={"Stories"},
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "",
     *     @SWG\Schema(ref="#/definitions/saveAdditionalInfo"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="saveAdditionalInfo",
     *     allOf={
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="what_do_you_read",
     *                 type="string",
     *         )
     *     ),
     *        @SWG\Schema(
     *             @SWG\Property(
     *                 property="where_do_you_read",
     *                 type="string",
     *         )
     *     ),
     *        @SWG\Schema(
     *             @SWG\Property(
     *                 property="read_time",
     *                 type="number",
     *         )
     *     )
     *     }
     * )
     *
     */

    function saveAdditionalInfo(SaveAdditionalInfoRequest $request){
        $requested_data = $request->all();
        $requested_data['user_id'] = $requested_data['data']['id'];
        $requested_data['created_at'] = time();
        unset($requested_data['data']);
        $info = ['user_id'=>$requested_data['user_id']];
        AdditionalUserInfo::updateOrCreate($info,$requested_data);
        $data = \Config::get('success.saved');
        $data['data'] = (object) [];
        return Response::json($data);
    }
    



        /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/story/addStory",
     *   summary="add Story",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Stories"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *  @SWG\Parameter(
     *     name="image",
     *     in="formData",
     *     required=false,
     *     type="file"
     *   ),   
     *  @SWG\Parameter(
     *     name="synopsis",
     *     in="formData",
     *     required=false,
     *     type="file"
     *   ),    
     * @SWG\Parameter(
     *     name="name",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "name of story",
     *   ), 
     * @SWG\Parameter(
     *     name="story_id",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "used only on edit story",
     *   ), 
     *   @SWG\Parameter(
     *     name="about",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "description",
     *   ),   
     *   @SWG\Parameter(
     *     name="query_letter",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "query letter",
     *   ), 
     * @SWG\Parameter(
     *     name="genre_ids",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "genres ids",
     *   ),  
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */

    public function addStory(AddStoryRequest $request)
    {
        $requested_data = $request->all();
        $id = $requested_data['data']['id'];
        $requested_data['updated_at'] = time();
        $synopsis = '';
 
        $about = isset($requested_data['about'])?$requested_data['about']:'';
        $query_latter = isset($requested_data['query_letter'])?$requested_data['query_letter']:'';
        $cover_image='';
        
        if(isset($requested_data['story_id'])){
            $story_data= Story::where('id',$requested_data['story_id'])->first();
            $cover_image = $story_data->url;
            $synopsis = $story_data->synops;
        }
 
         // check file extension
         $allowed = ['jpeg', 'png', 'jpg'];
         if(isset($_FILES['image']['name'])){
                  $filename = $_FILES['image']['name'];
                  $ext = pathinfo($filename, PATHINFO_EXTENSION);
                 if (!in_array($ext, $allowed)) {
                     $data['message'] = 'Please upload a file in valid video format.';
                     $data['status'] = 400;
                     $data['data'] = (object) [];
                     return Response::json($data);
                 }

                 // check file size
                 if ($_FILES['image']['size'] > 2097152) {
                     $data['message'] = 'Image size must be less then 2 MB.';
                     $data['status'] = 400;
                     $data['data'] = (object) [];
                     return Response::json($data);
                 }

                 //upload file
                 $del_file = $cover_image; 
                 $dynamic_name = time() . '-' . $this->imageDynamicName() . '.' . $ext;
                 $image = $request->file('image')->storeAs('public/users/'.$requested_data['data']['id'], $dynamic_name);
                 if ($image) {
                     $image_name = explode('/', $image);
                     $cover_image = $image_name[3];//$this->userImageVersions($image_name[3]);
                     //if ($saved_Image) {
                 } 

                  // unlink image
                if(empty(!$del_file)) {
                    $main_image = storage_path() . '/app/public/users/'.$requested_data['data']['id'].'/'.$del_file;
                    //delete existing image
                    if (file_exists($main_image)) {
                        unlink($main_image);
                    }
                } 
         }
         
       
         // upload synopsis
         if(isset($_FILES['synopsis']['name'])){
             $allowed = ['pdf'];
             if(isset($_FILES['synopsis']['name'])){
                     $filename = $_FILES['synopsis']['name'];
                     $ext = pathinfo($filename, PATHINFO_EXTENSION);
                 if (!in_array($ext, $allowed)) {
                     $data['message'] = 'Please upload a file in valid text format.';
                     $data['status'] = 400;
                     $data['data'] = (object) [];
                     return Response::json($data);
                 }
             }
             $del_file = $synopsis; 
             $path = 'app/public/synopsis/story';
             $synopsis =$this->uploadFile($requested_data,$path);
             // unlink cv
             if(empty(!$del_file)) {
                 $main = storage_path() . '/app/public/users/'.$requested_data['data']['id'].'/'.$del_file;
                 //delete existing image
                 if (file_exists($main)) {
                     unlink($main);
                 }
             } 
           }
          
            // if on edit time 
            if(isset($requested_data['story_id'])){
                $msg= 'Story updated successfully.';
                Story::where('id',$requested_data['story_id'])->Update(['user_id'=>$requested_data['data']['id'],'name'=>$requested_data['name'],'synops'=>$synopsis,'url'=>$cover_image,'query_letter'=>$query_latter,'description'=>$about]);
            } else{
                //1. send notification to fav. users user
                $msg= 'Story saved successfully.';
                $res = Story::Create(['user_id'=>$requested_data['data']['id'],'name'=>$requested_data['name'],'synops'=>$synopsis,'url'=>$cover_image,'query_letter'=>$query_latter,'description'=>$about,'created_at'=>time()]);
                $requested_data['story_id'] = $res->id;
            }
               // if genre exists 
           if(isset($requested_data['genre_ids'])){
                $this->addGenres($requested_data,'story');
           }
 
         $data['status'] = \Config::get('success.code');
         $data['message'] = $msg;
         $data['data'] = (object) [];
         return Response::json($data);
    }




    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/story/addEpisode",
     *   summary="add Story",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Stories"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),   
     *  @SWG\Parameter(
     *     name="synopsis",
     *     in="formData",
     *     required=false,
     *     type="file"
     *   ),    
     * @SWG\Parameter(
     *     name="name",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "episode of story",
     *   ), 
     *   @SWG\Parameter(
     *     name="about",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "description",
     *   ),   
     * @SWG\Parameter(
     *     name="story_id",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "story id",
     *   ), 
     * @SWG\Parameter(
     *     name="episode_id",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "used only on edit episode id",
     *   ), 
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */

    public function addEpisode(AddEpisodeRequest $request)
    {
        $requested_data = $request->all();
        $id = $requested_data['data']['id'];
        $synopsis = '';

        $about = isset($requested_data['about'])?$requested_data['about']:'';

        if(isset($requested_data['episode_id'])){
            $spisode_data= Episode::where('story_id',$requested_data['story_id'])->where('id',$requested_data['episode_id'])->first();
            $synopsis = $spisode_data->synops;
        }
 
         // upload synopsis
         if(isset($_FILES['synopsis']['name'])){
             $allowed = ['pdf'];
             if(isset($_FILES['synopsis']['name'])){
                     $filename = $_FILES['synopsis']['name'];
                     $ext = pathinfo($filename, PATHINFO_EXTENSION);
                 if (!in_array($ext, $allowed)) {
                     $data['message'] = 'Please upload a file in valid text format.';
                     $data['status'] = 400;
                     $data['data'] = (object) [];
                     return Response::json($data);
                 }
             }
             $path='app/public/synopsis/story';
             $del_story = $synopsis;
             $synopsis =$this->uploadFile($requested_data,$path);
             // unlink cv
             if(empty(!$del_story)) {
                 $main = storage_path() . '/app/public/users/'.$requested_data['data']['id'].'/'.$del_story;
                 //delete existing image
                 if (file_exists($main)) {
                     unlink($main);
                 }
             } 
           }
           // if edit episode
         if(isset($requested_data['episode_id'])){
            $msg='Episode updated successfully.';
            Episode::where('id',$requested_data['episode_id'])->update(['name'=>$requested_data['name'],'user_id'=>$requested_data['data']['id'],'story_id'=>$requested_data['story_id'],'synops'=>$synopsis,'description'=>$about]);
         } else{
            $msg='Episode saved successfully.';
            Episode::Create(['name'=>$requested_data['name'],'user_id'=>$requested_data['data']['id'],'story_id'=>$requested_data['story_id'],'synops'=>$synopsis,'description'=>$about,'created_at'=>time()]);
        }
         $data['status'] =  \Config::get('success.code');;
         $data['message'] =$msg;
         $data['data'] = (object) [];
         return Response::json($data);
    }


     /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\POST(
     *   path="/story/approve",
     *   summary="approve story and episode",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *   tags={"Stories"},
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "status=,1=>approved,2decline",
     *     @SWG\Schema(ref="#/definitions/approve"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="approve",
     *     allOf={
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="story_id",
     *                 type="number",
     *         )
     *     ),
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="episode_id",
     *                 type="number",
     *      )
     *         ),
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="status",
     *                 type="number",
    *         )
     *     )
     *     }
     * )
     *
     */

    function approve(CommonStoryRequest $request){
        $requested_data = $request->all();

        if(empty(!$requested_data['episode_id'])){
             Episode::where('story_id',$requested_data['story_id'])->where('id',$requested_data['episode_id'])->update(['status'=>$requested_data['status']]);
            if($requested_data['status']==1){

                //2. new chapter for reading story
               $user_ids = ViewedStory::where('story_id',$requested_data['story_id'])->where('type',1)->pluck('user_id')->toArray();
               $requested_data['type'] = 2;
               $this->sendNotification($requested_data,$user_ids,0,$requested_data['episode_id']);


               //3. send notification to fav. story chapter
               $user_ids = Favourite::where('story_id',$requested_data['story_id'])->where('type',2)->pluck('favourite_by')->toArray();
               $requested_data['type'] = 3;
               $this->sendNotification($requested_data,$user_ids);


                Story::where('id',$requested_data['story_id'])->increment('chapters_count');
            }
        
        } else{
            Story::where('id',$requested_data['story_id'])->Update(['status'=>$requested_data['status']]);
            
            if($requested_data['status']==1){
                $id = Story::where('id',$requested_data['story_id'])->first()->user_id;

                //1. send notification to fav. users user
               $user_ids = Favourite::where('user_id',$id)->where('type',1)->pluck('favourite_by')->toArray();
               $requested_data['type'] = 1;
               $this->sendNotification($requested_data,$user_ids);




                User::where('id',$id)->increment('stories_count');
            }
        }
        $data = \Config::get('success.success');
        $data['data'] =(object) [];
        return Response::json($data);
    }





    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *   path="/story/storyReport",
     *   summary="storyReport",
     *   produces={"application/json"},
     *   tags={"Stories"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=false,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *  @SWG\Parameter(
     *     name="start_date",
     *     in="query",
     *     required=true,
     *     type="string",
     *     default="",
     *     description="start date"
     *   ),
     *  @SWG\Parameter(
     *     name="end_date",
     *     in="query",
     *     required=true,
     *     type="string",
     *     default="",
     *     description="start date"
     *   ),
     *  @SWG\Parameter(
     *     name="year",
     *     in="query",
     *     required=false,
     *     type="string",
     *     default="",
     *     description="year"
     *   ),
     *  @SWG\Parameter(
     *     name="story_id",
     *     in="query",
     *     required=true,
     *     type="string",
     *     default="",
     *     description="story id"
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
       * )
     *
     */

    function storyReport(StoryReportRequest $request){
        $requested_data = $request->all();
        $report = [];
        $start_date = strtotime(date("Y-m-d 00:00:00", $requested_data['start_date']));
        $end_date = strtotime(date("Y-m-d 00:00:00", $requested_data['end_date']));
        $i = 1;  
        $add_amount = 86400;
        $days=1;
        if(isset($requested_data['year'])){
            $start_date = strtotime(date("Y-1-1 00:00:00", $requested_data['start_date']));
            $end_date = strtotime(date("Y-12-31 00:00:00", $requested_data['start_date']));
        }

    
        while ($start_date < $end_date) {
            if(isset($requested_data['year'])){
                $days = date('t',$start_date);
            }
            $next_date =$start_date+($add_amount*$days);
            $perv_amount = ViewedStory::whereBetween('created_at', array($start_date,$next_date))->where('type',2)->where('type',2)->sum('perview_amount');
            $full_amount= ViewedStory::whereBetween('updated_at', array($start_date,$next_date))->where('type',2)->sum('full_amount');
            $total = $perv_amount+$full_amount;
            $report[$i]['amount'] = $total;
            $report[$i]['start_date_timestemp'] = $start_date;
            $report[$i]['start_date'] = date("Y-m-d",$start_date);
            $report[$i]['end_date_timestemp'] = $end_date;
            $report[$i]['end_date'] = date("Y-m-d",$end_date);
            $report[$i]['next_date'] = date("Y-m-d",$next_date);
            $report[$i]['next_date_timestemp'] = $next_date;
            $report[$i]['days'] = $days;
            $start_date = $next_date;
            $i++;
        }
        //$data = \Config::get('success.get');
        $data['status'] =\Config::get('success.code');
        $data['message'] =count($report)?'success':'No Record Found.';
        $data['data'] =count($report)?$report:(object) [];
        return Response::json($data);        
    }



    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *   path="/story/weeklyPayment",
     *   summary="weeklyPayment",
     *   produces={"application/json"},
     *   tags={"Stories"},
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
       * )
     *
     */

    function weeklyPayment(Request $request){
        $requested_data = $request->all();
        $info = User::select('id','name','monthly_revenue')->where('monthly_revenue','!=',0)->get()->toArray();
        $data = \Config::get('success.get');
        $data['data'] = $info;
        return Response::json($data);
    }



}
