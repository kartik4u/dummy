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
use DB;
use JWTAuth;
use Session;
use App\Config;
use App\User;
use App\UserAddress;
use App\Course;
use App\CourseDetail;
use App\CourseProspect;
use App\Address;

class CourseController extends Controller {

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/frontend/courses/add-edit-courses",
     *   summary="Add courses and its details",
     *   produces={"application/json"},
     *   tags={"Courses"},
     *   @SWG\Parameter(
     *     name="token",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/addEditCourses"),
     *   ),
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "id => add course id here to edit course data",
     *     @SWG\Schema(ref="#/definitions/addEditCourses"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="addEditCourses",
     *     allOf={
     *         @SWG\Schema(
     *           @SWG\Property(
     *                 property="id",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="course_name",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="duration_of_course",
     *                 type="integer",
     *             ),
     *             @SWG\Property(
     *                 property="city",
     *                 type="string",
     *             ) ,
     *             @SWG\Property(
     *                 property="degree_level",
     *                 type="integer",
     *             ) ,
     *             @SWG\Property(
     *                 property="visa_type",
     *                 type="integer",
     *             ) ,
     *             @SWG\Property(
     *                 property="fee",
     *                 type="integer",
     *             ),
     *              @SWG\Property(
     *                 property="average_income",
     *                 type="integer",
     *             ),
     *              @SWG\Property(
     *                 property="drop_out_rate",
     *                 type="string",
     *             ),
     *              @SWG\Property(
     *                 property="next_intake",
     *                 type="string",
     *             ),
     *              @SWG\Property(
     *                 property="description",
     *                 type="string",
     *             ),
     *              @SWG\Property(
     *                 property="status",
     *                 type="integer",
     *             ),
     *            @SWG\Property(
     *              property="course_details",
     *              type="array",
     *              @SWG\Items(
     *              type="object",
     *              @SWG\Property(property="degree", type="string"),
     *              @SWG\Property(property="degree_description", type="string"),
     *              @SWG\Property(property="status", type="integer"),
     *             ),
     *              ),
     *            @SWG\Property(
     *              property="course_prospects",
     *              type="array",
     *              @SWG\Items(
     *              type="object",
     *              @SWG\Property(property="name", type="string"),
     *              @SWG\Property(property="status", type="integer"),
     *             ),
     *              ),
     *         )
     *     }
     * )
     *
     */
    public function addEditCourses(Request $request) {

        $requested_data = $request->all();
        //return Response::json($requested_data);
        //unique:products,product_id,' . $requested_data['id'].',id,user_id,'.$requested_data['user_id']]
        if (isset($requested_data['id']) && !empty($requested_data['id'])) {
            $rule = ['course_name' => 'required|unique:courses,course_name,' . trim($requested_data['course_name']) . ',id,user_id,' . $requested_data['id'],
                'duration_of_course' => 'required', 'degree_level' => 'required',
                'fee' => 'required', 'average_income' => 'required', 'next_intake' => 'required', 'description' => 'required',
            ];
        } else {
            $rule = ['course_name' => 'required|unique:courses,course_name,' . trim($requested_data['course_name']) . ',id,user_id,' . $requested_data['data']['id'],
                'duration_of_course' => 'required', 'degree_level' => 'required',
                'fee' => 'required', 'average_income' => 'required', 'next_intake' => 'required', 'description' => 'required',
            ];
        }
        $messages = ['required' => 'please enter :attribute'];

        $validator = Validator::make($requested_data, $rule, $messages);  #Check validation
        if ($validator->fails()) { #Check validation pass or fail
            return Response::json((new Controller)->validateData($validator));
        }
        #check if course name already exist for that user then return error
        $check_existance = Course::where('user_id', $requested_data['data']['id'])->whereRaw('LOWER(course_name) = "' . strtolower(trim($requested_data['course_name'])) . '"');
        if (isset($requested_data['id']) && !empty($requested_data['id'])) {
            $check_existance = $check_existance->where('id', '!=', $requested_data['id']);
        }
        $check_existance = $check_existance->count();
        if ($check_existance > 0) {
            return Response::json(\Config::get('error.course_name_aleready_taken')); #error
        }
        if (isset($requested_data['id'])) { #edit course address
            $addcourse_satus = $this->AddEditCourseFn($requested_data, $requested_data['id']); #call private function to edit course
            if ($addcourse_satus == 1) {
                return Response::json(\Config::get('success.edit_courses')); #success
            } else {
                return Response::json(\Config::get('error.incorrect_id')); #error
            }
        } else { #add course
            $addcourse_satus = $this->AddEditCourseFn($requested_data); #call private function to add course
            return Response::json(\Config::get('success.add_courses')); #success
        }
    }

    /* Private function to add edit course */

    function AddEditCourseFn($requested_data, $id = Null) {

        $input['user_id'] = $requested_data['data']['id'];
        if (isset($id) && !empty($id)) {
            
        } else {
            $input['slug'] = str_slug($requested_data['course_name']);
        }
        $input['course_name'] = $requested_data['course_name'];
        $input['duration_of_course'] = $requested_data['duration_of_course'];
        $input['degree_level'] = $requested_data['degree_level'];
        $input['visa_type'] = isset($requested_data['visa_type']) ? $requested_data['visa_type'] : '';
        $input['fee'] = $requested_data['fee'];
        $input['average_income'] = $requested_data['average_income'];
        $input['drop_out_rate'] = isset($requested_data['drop_out_rate']) ? $requested_data['drop_out_rate'] : '';
        $input['next_intake'] = $requested_data['next_intake'];
        $input['description'] = $requested_data['description'];
        // $input['status'] = $requested_data['status'];
        $input['updated_at'] = time();

        $course_id = $id;
        $edit = 0;
        if (isset($course_id)) { #edit drop_out_ratecourse from here
            $result = Course::where('id', $course_id)->update($input);
            if ($result == 0) {
                return 0; #error
            }
            $edit = 1;
        } else { #insert course from here
            $input['created_at'] = time();
            $result = Course::create($input); #insert course in course table
            $course_id = $result->id; #get last insert course id
        }

        $coursedt_status = $this->CourseDetails($requested_data, $course_id, $edit); #add/edit course details from this function
        $courseProspects_status = $this->CourseProspects($requested_data, $course_id, $edit); #add/edit Course prospects from this function
        return 1;
    }

    /* Private function to save/edit course details */

    function CourseDetails($requested_data, $course_id, $edit) {
        if ($edit == 1) { #delete exiting single/multiple course details
            CourseDetail::where('course_id', $course_id)->delete();
        }
        $course_details = $requested_data['course_details'];
        $coursdt_array = [];
        foreach ($course_details as $key => $course_detail) {
            $coursdt_array[$key]['course_id'] = $course_id;
            $coursdt_array[$key]['degree'] = $course_detail['degree'];
            $coursdt_array[$key]['grades'] = $course_detail['grades'];
            $coursdt_array[$key]['status'] = $course_detail['status'] = 1;
            $coursdt_array[$key]['created_at'] = time();
            $coursdt_array[$key]['updated_at'] = time();
        }
        CourseDetail::insert($coursdt_array); #insert course details
        return 1;
    }

    /* Private function to save/edit course prospects */

    function CourseProspects($requested_data, $course_id, $edit) {

        if ($edit == 1) { #delete exiting single/multiple course prospects
            CourseProspect::where('course_id', $course_id)->delete();
        }

        $course_prospects = $requested_data['course_prospects'];
        $coursdt_array = [];
        foreach ($course_prospects as $key => $course_prospect) {
            $coursdt_array[$key]['course_id'] = $course_id;
            $coursdt_array[$key]['name'] = $course_prospect['name'];
            $coursdt_array[$key]['status'] = 1; // $course_prospect['status'];
            $coursdt_array[$key]['created_at'] = time();
            $coursdt_array[$key]['updated_at'] = time();
        }

        CourseProspect::insert($coursdt_array); #insert course prospect
        return 1;
    }

    /*
     * Function: function to get all the university data
     */

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/frontend/university/get-single-course-data",
     *   summary="get single couurse  Data",
     *   produces={"application/json"},
     *   tags={"University"},
     *   @SWG\Parameter(
     *     name="university_slug",
     *     in="query",
     *     required=true,
     *     description="Enter university slug",
     *     type="integer",
     *   ),
     *   @SWG\Parameter(
     *     name="course_slug",
     *     in="query",
     *     required=true,
     *     description="Enter course slugs",
     *     type="integer",
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function getSingleCourseData(Request $request) {
        $requested_data = $request->all();
        $page_record = \Config::get('variable.page_per_record');
        # query to get single course  data

        $course_data = Course ::select('*', 'next_intake as intake')->where('slug', $requested_data['course_slug'])
                ->with(['getCourseDetail' => function($q) {
                        $q->where('status', 1);
                    }, 'getCourseProspectives', 'getProfile' => function($q) {
                        $q->where('status', 1);
                    }, 'address' => function($q) {
                        $q->where('status', 1);
                    }, 'user' => function($q)use($requested_data) {
                        $q->where('status', 1)->where('slug', $requested_data['university_slug']);
                    }])
                ->whereHas('user', function($q1)use($requested_data) {
                    $q1->where('status', 1)->where('slug', $requested_data['university_slug']);
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

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/frontend/favourite/get-quick-course-data",
     *   summary="get quick details course",
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
    public function getQuickCourseData(Request $request) {
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
