<?php

/*
 * Controller Name  : FavouriteController
 * Author           : Narinder
 * Author Contact   : narinder.singh@ignivasolutions.com
 * Created Date     : 18-04-2018
 * Description      : This controller perform favourite related tasks
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
use App\Course;
use App\FavouriteCourse;


class  FavouriteCoursesController extends Controller {


       /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     * @SWG\Post(
     * path="/frontend/favourite/favourite-unfavourite",
     * summary="(NS)favorite-unfavorite Course:api to favourite-unfavourite  course by hitting api , on first hit video will be marked as favourite and on second hit course will be marked as unfavourite",
     * produces={"application/json"},
     * tags={"favourite"},
     * @SWG\Parameter(
     * name="token",
     * in="header",
     * required=true,
     * description = "Enter Token",
     * type="string"
     * ),
     * @SWG\Parameter(
     * name="course_id",
     * in="formData",
     * required=true,
     * default=2,
     * description="course id like 2",
     * type="integer"
     * ),
     * @SWG\Response(response=200, description="Success"),
     * @SWG\Response(response=400, description="Failed"),
     * @SWG\Response(response=405, description="Undocumented data"),
     * @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    
    public function favouriteUnfavourite(Request $request) {
        $requested_data = $request->all();

        #code to check validation
        $rule = ['course_id' => 'required | numeric | exists:courses,id']; #Check validation
        $messages = ['required' => 'Please enter :attribute', 'numeric' => 'Please enter only numeric value', 'coures_id.exists' => 'Please enter valid course id'];
        $validator = Validator::make($requested_data, $rule, $messages);
        if ($validator->fails()) { #Check validation pass or fail
            return Response::json((new Controller)->validatedata($validator));
        }

        $logged_in_user_id = $requested_data['data']['id'];
        $course_id = $requested_data['course_id'];

        // get favorite course
        $favorite_data =  FavouriteCourse::where('user_id', $logged_in_user_id)->where('course_id', $course_id)->count();

        $data['user_id'] = $logged_in_user_id;
        $data['course_id'] = $course_id;
        $data['created_at'] = time();

        if ($favorite_data) {
            FavouriteCourse::where('user_id', $logged_in_user_id)->where('course_id', $course_id)->delete();
            $data = \Config::get('success.success_course_unfavorite');
        } else {
            # insert favorite data
            FavouriteCourse::create($data);
            $data = \Config::get('success.success_course_favorite');
        }
        return Response::json($data);
    }


        /*
     * Function: function to get favourites
     * Input:  user_id
     * Output: success/failed
     */

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *  path="/frontend/favourite/get-my-favourites",
     *  summary="(NS)Favourites:api to get favourites",
     *  produces={"application/json"},
     *  tags={"favourite"},
     *   @SWG\Parameter(
     *     name="token",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     required=true,
     *     default=1,
     *     type="integer",
     *   ),
     * @SWG\Response(response=200, description="Success"),
     * @SWG\Response(response=400, description="Failed"),
     * @SWG\Response(response=405, description="Undocumented data"),
     * @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    
    function getMyFavourites(request $request) {
        $requested_data = $request->all();
        #code to check validation        
        $requested_data['page'] = isset($requested_data['page']) ? $requested_data['page'] : 1;
        $page_record = \Config::get('variable.page_per_record');
        $course_ids =  FavouriteCourse::where('user_id', $requested_data['data']['id'])->pluck('course_id')->toArray();       
        $user_ids =    Course::whereIn('id', $course_ids)->groupby('user_id')->pluck('user_id')->toArray();       

        $page_record = \Config::get('variable.page_per_record');
        # query to get course  data

        $course_data = User::select('id','name','is_sponsor')->whereIn('id',$user_ids)->with([
                    'course.getCourseDetail' => function($q) {
                        $q->select('id','course_id', 'degree', 'grades', 'status')->where('status', 1);
                    },
                    'getProfile' => function($q) {
                        $q->select('id','user_id','logo')->where('status', 1);
                    },
                    'singleAddress' => function($q) {
                        $q->select('user_id', 'campus_name', 'postal_code', 'city', 'country','latitude','longitude', 'address','address2','status', 'created_at', 'updated_at')->where('status', 1);
                    },
                    'course' => function($q) use ($course_ids) {
                        $q->select('id','course_name','user_id','degree_level','next_intake as intake','fee','next_intake as intake')->where('status', 1)->whereIn('id',$course_ids);
                    },
                    'course.getDegreeLevels' => function($q) {
                        $q->select('id','name');
                    }])
                    // ->whereHas('course', function($query) use ($course_ids){
                    //     $query->whereIn('id',$course_ids)})
                //     ->whereHas('course', function($query) use ($course_ids){
                //         $q->whereIn('id',$course_ids);
                //   })
                ->paginate($page_record)->toArray();
       
        if (count($course_data['data']) > 0) {
            $data = \Config::get('success.success_data_fetcheted_successfully');
            $data['data'] = $course_data;
        } else {
            $data = \Config::get('success.no_record_found');
            $data['data'] = $course_data;
        }
        return Response::json($data);
    }

    /*
     * Function: function to get all the university data
     */

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/frontend/favourite/single-course-data",
     *   summary="get single course  Data",
     *   produces={"application/json"},
     *   tags={"favourite"},
     *   @SWG\Parameter(
     *     name="id",
     *     in="query",
     *     required=true,
     *     description="Course ID",
     *     type="integer",
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function singleCourseData(Request $request) {
        $requested_data = $request->all();
        $page_record = \Config::get('variable.page_per_record');
        # query to get single course  data

        $course_data = Course ::select('*', 'next_intake as intake')->where('id', $requested_data['id'])
                ->with(['getCourseDetail' => function($q) {
                        $q->where('status', 1);
                    }, 'getCourseProspectives', 'getProfile' => function($q) {
                        $q->where('status', 1);
                    }, 'address' => function($q) {
                        $q->where('status', 1);
                    }, 'user' => function($q)use($requested_data) {
                        $q->where('status', 1);
                    }])
                ->whereHas('user', function($q1)use($requested_data) {
                    $q1->where('status', 1);
                })->whereHas('getProfile', function($q2) {
                    $q2->where('status', 1);
                })->whereHas('getCourseDetail', function($q3) {
                    $q3->where('status', 1);
                })->whereHas('address', function($q4) {
                    $q4->where('status', 1);
                })
                ->first();

        if (!empty($course_data)) {
            $data = \Config::get('success.success_data');     # success  results
            $data['data'] = $course_data;
            return Response::json($data);
        } else {
            $data = \Config::get('error.no_record_found');      # no results
            $data['data'] = $course_data;
            return Response::json($data);
        }
    }

}
