<?php

/*
 * Controller Name  : FavouriteController
 * Author           : Narinder
 * Author Contact   : narinder.singh@ignivasolutions.com
 * Created Date     : 18-04-2018
 * Description      : This controller perform favourite related tasks
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin;
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

class FavouriteCoursesController extends Controller {
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
     *  path="/admin/favourite/get-my-favourites",
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
     *     name="user_id",
     *     in="query",
     *     required=true,
     *     default=1,
     *     type="integer",
     *   ),
     *   @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     required=true,
     *     default=1,
     *     type="integer",
     *   ),
     *  @SWG\Parameter(
     *     name="filter_type",
     *     in="query",
     *     required=true,
     *     default=1,
     *     type="integer",
     *     description="Type should be either 1 or 2 (1-Courses, 2-Accomodations)"
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
        $rule = ['user_id' => 'required|exists:users,id,role_id,3', 'filter_type' => 'required|in:1,2'];
        $validator = Validator::make($requested_data, $rule);  #Check validation
        if ($validator->fails()) { #Check validation pass or fail
            return Response::json($this->validateData($validator));
        }

        $requested_data['page'] = isset($requested_data['page']) ? $requested_data['page'] : 1; # page no
        $page_record = \Config::get('variable.page_per_record'); #per_page record
        #get user details
        $user = User::with(['userDetail' => function($q1) {
                        $q1->select(['id', 'user_id', 'dob', 'gender']);
                    }])->where('id', trim($requested_data['user_id']))->first(['id', 'name', 'email']);
                //dd($user);
                $response['data']['student_detail'] = $user;
                $response['data']['favourites'] = [];
                if ($requested_data['filter_type'] == 1) {
                    $course_ids = FavouriteCourse::where('user_id', $requested_data['user_id'])->pluck('course_id')->toArray();
                    $user_ids = Course::whereIn('id', $course_ids)->groupBy('user_id')->pluck('user_id')->toArray();
                    #query to get course data
                    $universities = User::select('id', 'name', 'is_sponsor')->whereIn('id', $user_ids)->with([
                                'course.getCourseDetail' => function($q) {
                                    $q;
                                },
                                'getProfile' => function($q) {
                                    $q->select('id', 'user_id', 'logo');
                                },
                                'singleAddress' => function($q) {
                                    $q->select(['id', 'user_id', 'address']);
                                },
                                        'course' => function($q) use ($course_ids) {
                                    $q->select('id', 'course_name', 'user_id', 'degree_level', 'next_intake', 'fee', 'next_intake as intake')->whereIn('id', $course_ids);
                                },
                                        'course.getDegreeLevels' => function($q) {
                                    $q->select('id', 'name');
                                },
                                    ])
                                    ->paginate($page_record);
                            if (!empty($universities->items())) {
                                $response['data']['favourites'] = $universities;
                            }
                        } else {
                            $response['data']['favourites']['data'] = [];
                            $response['data']['favourites']['total'] = 0;
                            $response['data']['favourites']['per_page'] = $page_record;
                        }
                        $response['status'] = 200;
                        return Response::json($response);
                    }

                    /**
                     * @return \Illuminate\Http\JsonResponse
                     *
                     *
                     *  @SWG\Get(
                     *  path="/admin/favourite/get-favourite-course-data",
                     *  summary="get favourite data for CSV",
                     *  produces={"application/json"},
                     *  tags={"favourite"},
                     *  @SWG\Parameter(
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
                     *   @SWG\Response(response=200, description="Success"),
                     *   @SWG\Response(response=400, description="Failed"),
                     *   @SWG\Response(response=405, description="Undocumented data"),
                     *   @SWG\Response(response=500, description="Internal server error")
                     * )
                     *
                     */
                    public function getFavouriteCourseData(Request $request) {
                        $requested_data = $request->all();
                        $page_record = \Config::get('variable.page_per_record'); #per_page record
                        $excel_limit = \Config::get('variable.excel_limit_per_file');
                        # query to get course  data

                        $course_data = Course::select(['id', 'course_name', 'user_id'])
                                ->with(['user' => function($q1) {
                                        $q1->select(['id', 'name']);
                                    }])->has('favouriteCourse')->withCount('favouriteCourse')
                                        ->paginate($page_record)->toArray();
                                
                                if (isset($course_data['data']) && !empty($course_data['data'])) {
                                    // manage download limit
                                    if ($course_data['total'] > 0) {
                                        $total_links = $course_data['total'] >= $excel_limit ? ceil($course_data['total'] / $excel_limit) : 1; #total download links to generate
                                    } else {
                                        $total_links = 1;
                                    }
                                    $data = \Config::get('success.success_data');     # success results
                                    $data['data'] = $course_data;
                                    $data['total_links'] = $total_links;
                                    return Response::json($data);
                                } else {
                                    $data = \Config::get('success.no_record');      # no results
                                    $data['total_links'] = 0;
                                    $data['data'] = $course_data;
                                    return Response::json($data);
                                }
                            }

                            /**
                             * @SWG\Get(
                             *   path="/admin/favourite/download-favourite",
                             *   summary=" download favourite data",
                             *   produces={"application/json"},
                             *   tags={"favourite"},
                             *    @SWG\Parameter(
                             *     name="token",
                             *     in="header",
                             *     required=true,
                             *     description = "Enter Token",
                             *     type="string"
                             *   ),
                             *     @SWG\Parameter(
                             *     name="page",
                             *     in="query",
                             *     required=false,
                             *     description = "page number",
                             *     type="number",
                             *     default=1
                             *   ),
                             *   @SWG\Response(response=200, description="Success"),
                             *   @SWG\Response(response=400, description="Failed"),
                             *   @SWG\Response(response=405, description="Undocumented data"),
                             *   @SWG\Response(response=500, description="Internal server error")  
                             *  )
                             *
                             */
                            public function downloadFavourite(Request $request) {
                                $requested_data = $request->all();
                                $page_record = \Config::get('variable.page_per_record'); #per_page record
                                $server_url = \Config::get('variable.SERVER_URL');
                                $excel_limit = \Config::get('variable.excel_limit_per_file');

                                $course_data = Course::select(['id', 'course_name', 'user_id'])
                                                ->with(['user' => function($q1) {
                                                        $q1->select(['id', 'name']);
                                                    }])->has('favouriteCourse')->withCount('favouriteCourse')
                                                        ->paginate($page_record)->toArray();
                                        #Check university not empty here and greater then 0
                                        if (isset($course_data['data']) && !empty($course_data['data'])) {
                                            #Set path here and define file name
                                            $public_path = public_path() . "/csv/favourite/";

                                            #Remove old from directory
                                            $files = glob($public_path . '/*.csv');
                                            if (!empty($files)) {
                                                foreach ($files as $file) {
                                                    unlink($file);
                                                }
                                            }

                                            #Set file name here
                                            $file = strtotime(date('Y-m-d H:i:s')) . "_favourite.csv";
                                            $filename = $public_path . $file;
                                            $handle = fopen($filename, 'w+');
                                            #Set all heading here for file
                                            fputcsv($handle, array('Sr. no.', 'University name', 'Course name', 'Total favourite count'));
                                            #Set data here 
                                            foreach ($course_data['data'] as $key => $row) {
                                                $noNo = $key + 1;
                                                $course_name = @$row['course_name'];
                                                $university = @$row['user']['name'];
                                                $fav_count = @$row['favourite_course_count'];
                                                fputcsv($handle, array($noNo,$university,$course_name,$fav_count));
                                            }
                                            #return file name here
                                            fclose($handle);
                                            $headers = array(
                                                'Content-Type' => 'text/csv',
                                            );
                                            $data['status'] = 200;
                                            $data['file'] = $server_url . 'csv/favourite/' . $file;
                                            return response()->json($data);
                                        } else {
                                            return Response::json(array('status' => 400, 'description' => 'favourite data not found, Please try again.'));
                                        }
                                    }
                                    
                                    /**
                     * @return \Illuminate\Http\JsonResponse
                     *
                     *
                     *  @SWG\Get(
                     *  path="/admin/favourite/get-course-detail",
                     *  summary="Favourites:get favourite course detail",
                     *  produces={"application/json"},
                     *  tags={"favourite"},
                     *  @SWG\Parameter(
                     *     name="token",
                     *     in="header",
                     *     required=true,
                     *     description = "Enter Token",
                     *     type="string"
                     *   ),
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
                    public function getCourseDetail(Request $request) {
                        $requested_data = $request->all();
                        $course_id = $requested_data['id'];
                        # query to get single course  data

                        $course_data = Course::select('*', 'next_intake as intake')
                                ->where('id',$course_id)->where('status',1)
                                ->with(['getCourseDetail' => function($q) {
                                        $q->where('status', 1);
                                    }, 'getCourseProspectives' => function($q) {
                                        $q->where('status', 1);
                                    }, 'getProfile' => function($q) {
                                        $q->where('status', 1);
                                    }, 'address' => function($q) {
                                        $q->where('status', 1);
                                    }, 'user' => function($q) {
                                        $q->where('role_id', 2);
                                    }
                                ])->first();

                        if (!empty($course_data)) {
                            $data = \Config::get('success.success_data');     # success results
                            $data['data'] = $course_data;
                            return Response::json($data);
                        } else {
                            $data = \Config::get('success.no_record');      # no results
                            $data['data'] = $course_data;
                            return Response::json($data);
                        }
                    }

                                }
                                