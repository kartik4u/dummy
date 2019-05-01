<?php

/*
 * Controller Name  : SearchController
 * Author           : Prabhat
 * Created Date     : 9-04-2018
 * Description      : This controller manage search courses functionality
 */

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;
use Validator;
use Auth;
use JWTAuth;
use App\Config;
use \Illuminate\Pagination\Paginator;
use App\User;
use App\UserAddress;
use App\Course;
use App\CourseDetail;
use App\UserDetail;
use Illuminate\Routing\Route;
use Illuminate\Pagination\LengthAwarePaginator;
use App\SearchAnalytic;
use DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class SearchController extends Controller {

    function __construct(request $request, route $route) {
        $parsed_token = $request->headers->all();


        $token = (new Controller)->checkToken($request);

        if ($token) {
            $method = $route->getActionName();

            $method = explode('@', $method);
            $action = $method[1];
            if ($action == 'searchUniversity') {
                $this->middleware('jwt-auth')->only('searchUniversity');
            }
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *   path="/frontend/search/get-autocomplete-search-options",
     *   summary="Get autocomplete search options",
     *   produces={"application/json"},
     *   tags={"Search University"},
     *   @SWG\Parameter(
     *     name="keyword",
     *     in="query",
     *     description="keyword to search (university,location or city)",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     */
    public function getAutocompleteSearchOptions(Request $request) {
        $requested_data = $request->all();
        $search = strtolower(trim($requested_data['keyword']));
        if (isset($search) && !empty($search)) {
            #get matched top 5 universities data
            $users = User::whereRaw('LOWER(name) like "%' . $search . '%"')->where('role_id', 2)->where('status', 1)
                            ->has('course')->has('singleAddress')->has('userDetail')
                            ->groupBy('name')->orderBy('id', 'desc')->take(5)->get(['id', 'name'])->toArray();
            //dd($users);
            #get matched top 5 courses data
            $courses = Course::whereRaw('LOWER(course_name) like "%' . $search . '%"')->where('status', 1)
                            ->whereHas('user', function($q1) {
                                $q1->where('status', 1);
                            })
                            ->has('user.singleAddress')->has('user.userDetail')
                            ->groupBy('course_name')->orderBy('id', 'desc')->take(5)->get(['id', 'course_name'])->toArray();
            //dd($courses);
            #get matched top 5 addresses data
            $addresses1 = UserAddress::where(function($query) use($search) {
                                $query//->whereRaw('LOWER(postal_code) like "%' . $search . '%"')
                                ->orWhereRaw('LOWER(city) like "%' . $search . '%"');
                                //->orWhereRaw('LOWER(country) like "%' . $search . '%"')
                                //->orWhereRaw('LOWER(address) like "%' . $search . '%"')
                                //->orWhereRaw('LOWER(address2) like "%' . $search . '%"');
                            })
                            ->whereHas('user', function($q1) {
                                $q1->where('status', 1);
                            })
                            ->has('user.course')->has('user.userDetail')
                            ->where('status', 1)->groupBy('country')->orderBy('id', 'desc')->take(5)->get(['id', 'city'])->toArray();
            $addresses2 = UserAddress::where(function($query) use($search) {
                                $query->orWhereRaw('LOWER(country) like "%' . $search . '%"');
                            })
                            ->whereHas('user', function($q1) {
                                $q1->where('status', 1);
                            })
                            ->has('user.course')->has('user.userDetail')
                            ->where('status', 1)->groupBy('country')->orderBy('id', 'desc')->take(5)->get(['id', 'country'])->toArray();

            $city = !empty($addresses1) ? array_column($addresses1, 'city', 'id') : [];
            $country = !empty($addresses2) ? array_column($addresses2, 'country', 'id') : [];
            $addresses = !empty($addresses1) || !empty($addresses2) ? array_unique(array_merge($city, $country), SORT_REGULAR) : [];
            if (empty($users) && empty($courses) && empty($addresses)) {
                return Response::json(\Config::get('error.no_record_found'));
            } else {
                $response = \Config::get('success.success_record_found');
                $response['data'] = [];
                if (!empty($users)) {
                    $response['data'][] = ['id' => 'Universities', 'type' => 'main', 'name' => 'Universities'];
                    foreach ($users as $k => $v) {
                        $v['type'] = 'sub';
                        $v['data_type'] = 1;
                        $response['data'][] = $v;
                    }
                }
                if (!empty($courses)) {
                    $response['data'][] = ['id' => 'Courses', 'type' => 'main', 'name' => 'Courses'];
                    foreach ($courses as $k => $v) {
                        $v['type'] = 'sub';
                        $v['name'] = $v['course_name'];
                        $v['data_type'] = 2;
                        unset($v['course_name']);
                        $response['data'][] = $v;
                    }
                }
                if (!empty($addresses)) {

                    $response['data'][] = ['id' => 'Addresses', 'type' => 'main', 'name' => 'Location'];
                    $arr = [];
                    foreach ($addresses as $k => $v) {
                        $arr['id'] = array_search($v, $city) == false ? array_search($v, $country) : array_search($v, $city);
                        $arr['type'] = 'sub';
                        $arr['name'] = $v;
                        $arr['data_type'] = 3;
                        unset($v);
                        $response['data'][] = $arr;
                    }
                }
                return Response::json($response);
            }
        }
    }

    /**
     *  @SWG\Post(
     *   path="/frontend/search/search-university",
     *   summary="Search university, course, location",
     *   produces={"application/json"},
     *   tags={"Search University"},
     *   @SWG\Parameter(
     *     name="token",
     *     in="header",
     *     required=false,
     *     description = "Enter Token",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     @SWG\Schema(ref="#/definitions/searchUniversity"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="searchUniversity",
     *     allOf={
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="university_name",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="degree_level",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="course_name",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="visa_type",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="location",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="entry_requirement",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="ranking",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="fees",
     *                 type="integer",
     *             )
     *         )
     *     }
     * )
     *
     */
    public function searchUniversity(Request $request) {
        $requested_data = $request->all();
        //$sponsored_page = isset($requested_data['sponsored_page']) && !empty($requested_data['sponsored_page']) ? $requested_data['sponsored_page'] : 1;
        $sponsored_uni_array = isset($requested_data['sponsored_uni_array']) && !empty($requested_data['sponsored_uni_array']) ? $requested_data['sponsored_uni_array'] : [];
        $page = isset($requested_data['page']) && !empty($requested_data['page']) ? trim($requested_data['page']) : 1;
        //$page_record = \Config::get('variable.page_per_record');
        $main_array = [];
        $page_record = 5;
        $page_record1 = 1;
        $page_record2 = 4;
        // update last searched
        if (isset($requested_data['data'])) {
            User::where('id', $requested_data['data']['id'])->update(['last_searched' => time()]);
        }
        $save_serach = $this->saveSearchData($requested_data, $request);

        //$get_sponsored_count = $this->getSponsoredUniveristies($requested_data, []); #get total sponsored count
        $get_sponsored_count = $this->getSponsoredUniveristiesCount($requested_data); #get total sponsored count
        if ($get_sponsored_count) {
            $main_array['total'] = $get_sponsored_count;
            //$main_array['total'] = $get_sponsored_count['total'];
        }
        #sponsored search results
        $sponsored_array = $this->getSponsoredUniveristies($requested_data, $sponsored_uni_array);
        if ($sponsored_array) {
            //$main_array['current_sponsored_page'] = $sponsored_page;
            $get_sponsor_ids1 = array_column($sponsored_array['data'], 'id');
            $sponsored_uni_array = isset($sponsored_uni_array) && !empty($sponsored_uni_array) ? array_merge($sponsored_uni_array, $get_sponsor_ids1) : $get_sponsor_ids1;
            $main_array['sponsored_uni_array'] = $sponsored_uni_array;
            $main_array['data'] = $sponsored_array['data'];
            $page_record2 = $page_record - $page_record1;
            //dd($sponsored_array);//dd($page_record2);
        }
        //dd($main_array);
        #unsponsored search results
        $unsponsored_array = $this->getUnsponsoredUniveristies($requested_data, $page_record2, $page);
        //dd($unsponsored_array);
        if ($unsponsored_array) {
            $main_array['data'] = isset($main_array['data']) ? array_merge($main_array['data'], $unsponsored_array['data']) : $unsponsored_array['data'];
            //dd($unsponsored_array);//dd($page_record2);
            //echo count($unsponsored_array['data'])." -- ".$page_record2;
            $unsponsored_count = count($unsponsored_array['data']); #get total unsponsored universities count
            $main_array['total'] = isset($main_array['total']) ? $main_array['total'] + $unsponsored_array['total'] : $unsponsored_array['total'];
            while ($unsponsored_count < $page_record2) {
                $unsponsored_count++;
                //echo "here" . $unsponsored_count . "<br>";
                $add_sponsored_array = $this->getSponsoredUniveristies($requested_data, $sponsored_uni_array);
                if ($add_sponsored_array) {
                    $get_sponsor_ids2 = array_column($add_sponsored_array['data'], 'id');
                    $sponsored_uni_array = isset($sponsored_uni_array) && !empty($sponsored_uni_array) ? array_merge($sponsored_uni_array, $get_sponsor_ids2) : $get_sponsor_ids2;
                    $main_array['sponsored_uni_array'] = $sponsored_uni_array;
                    //$main_array['current_sponsored_page'] = $sponsored_page;
                    $main_array['data'] = isset($main_array['data']) ? array_merge($main_array['data'], $add_sponsored_array['data']) : $add_sponsored_array['data'];
                }
            }
            //dd($main_array);
        } else {
            $get_unsponsored_count = $this->getUnsponsoredUniveristies($requested_data, $page_record2, 1); #get total unsponsored count
            if ($get_unsponsored_count) {
                $main_array['total'] = isset($main_array['total']) ? $main_array['total'] + $get_unsponsored_count['total'] : $get_unsponsored_count['total'];
            }
            //echo $page_record2;//dd($main_array);
            $count = $page_record2 - 1;
            $start = 0;
            //dd($count);
            while ($start < $count) {
                //$sponsored_page++;
                $start++;
                $add_sponsored_array = $this->getSponsoredUniveristies($requested_data, $sponsored_uni_array);
                //dd($add_sponsored_array);
                if ($add_sponsored_array) {
                    $get_sponsor_ids2 = array_column($add_sponsored_array['data'], 'id');
                    $sponsored_uni_array = isset($sponsored_uni_array) && !empty($sponsored_uni_array) ? array_merge($sponsored_uni_array, $get_sponsor_ids2) : $get_sponsor_ids2;
                    $main_array['sponsored_uni_array'] = $sponsored_uni_array;
                    //$main_array['current_sponsored_page'] = $sponsored_page;
                    $main_array['data'] = isset($main_array['data']) ? array_merge($main_array['data'], $add_sponsored_array['data']) : $add_sponsored_array['data'];
                    //dd($main_array);
                }
            }
        }
        //dd($main_array);

        if (isset($main_array['data']) && !empty($main_array['data'])) {
            $main_array['current_page'] = (int) $page;
            $main_array['last_page'] = ceil($main_array['total'] / $page_record);
            $response = \Config::get('success.success_record_found');
            $response['data'] = $main_array;
            $response['set_search_cookie'] = $save_serach;
            return Response::json($response);
        } else {
            $response = \Config::get('error.no_record_found');
            $response['set_search_cookie'] = $save_serach;
            return Response::json($response);
        }
    }

    private function getSponsoredUniveristiesCount($requested_data) {
        $sponsored_search = User::has('course')->has('course.courseDetail')->has('singleAddress')->has('userDetail')
                        ->where('is_sponsor', 1)->where('status', 1)->where('role_id', 2)->count();
        return $sponsored_search;
    }

    private function getSponsoredUniveristies($requested_data, $sponsored_arr = null) {
        $sponsored_search = User::with(['course' => function($q1) use($requested_data) {
                        $q1->with('getDegreeLevels');
                        if (isset($requested_data['data']['id']) && !empty($requested_data['data']['id'])) {
                            $q1->with(['favouriteCourse' => function($q3)use($requested_data) {
                                    $q3->select(['id', 'course_id'])->where('status', 1)->where('user_id', $requested_data['data']['id']);
                                }]);
                                }
                                        $q1->select(['id', 'user_id', 'course_name', 'slug', 'degree_level', 'fee', 'next_intake as intake', 'visa_type'])->where('status', 1)->inRandomOrder()->limit(4); //->take(4)->get()->random();///->take(1);
                            }, 'singleAddress' => function($q8) {
                                $q8->select(['id', 'user_id', 'address', 'latitude', 'longitude'])->where('status', 1);
                            }, 'userDetail' => function($q12) {
                                $q12->select(['id', 'user_id', 'logo', 'website', 'about_university', 'ranking'])->where('status', 1);
                            }, 'course.courseDetail' => function($q1) {
                                $q1->select(['id', 'course_id', 'degree'])->where('status', 1);
                                    }])
                                        ->has('course')->has('course.courseDetail')->has('singleAddress')->has('userDetail')
                                        ->select(['id', 'name', 'slug', 'email', 'role_id', 'status', 'is_sponsor'])
                                        ->where('is_sponsor', 1)->where('status', 1)->where('role_id', 2);
                        if (isset($sponsored_arr) && !empty($sponsored_arr)) {
                            $sponsored_search = $sponsored_search->whereNotIn('id', $sponsored_arr);
                        }
                        $sponsored_search = $sponsored_search->inRandomOrder()
                                        ->paginate(1, ['*'], 'page', 1)->toArray();
                        if (isset($sponsored_search['data']) && !empty($sponsored_search['data'])) {
                            return $sponsored_search;
                        }
                        return false;
                    }

                    private function getUnsponsoredUniveristies($requested_data, $per_page_record, $page) {
                        $search = User::with(['course' => function($q1) use($requested_data) {
                                        $q1->with('getDegreeLevels');
                                        #if logged in student, then get favourite course
                                        if (isset($requested_data['data']['id']) && !empty($requested_data['data']['id'])) {
                                            $q1->with(['favouriteCourse' => function($q3)use($requested_data) {
                                                    $q3->select(['id', 'course_id'])->where('status', 1)->where('user_id', $requested_data['data']['id']);
                                                }]);
                                                }
                                                #if course name is selected
                                                if (isset($requested_data['course_name']) && !empty($requested_data['course_name'])) {
                                                    $q1->whereRaw('LOWER(course_name) like "%' . strtolower(trim($requested_data['course_name']) . '%"'));
                                                }

                                                
                                                        #if degree level is selected
                                                        if (isset($requested_data['degree_level']) && !empty($requested_data['degree_level'])) {
                                                            $q1->where('degree_level', trim($requested_data['degree_level']))->where('status', 1);
                                                        }
                                                        #if visa type is selected
                                                        if (isset($requested_data['visa_type']) && !empty($requested_data['visa_type'])) {
                                                            $q1->whereRaw('LOWER(visa_type) like "%' . strtolower(trim($requested_data['visa_type']) . '%"'))->where('status', 1);
                                                        }
                                                        #if entry requirement is selected
                                                        if (isset($requested_data['entry_requirement']) && !empty($requested_data['entry_requirement'])) {
                                                            $q1->whereHas('courseDetail', function($q2) use($requested_data) {
                                                                $q2->whereRaw('LOWER(degree) like "%' . strtolower(trim($requested_data['entry_requirement'])) . '%"')->where('status', 1);
                                                            });
                                                        }
                                                        #if annual fee is selected
                                                        if (isset($requested_data['fees']) && !empty($requested_data['fees']) && $requested_data['fees'] > 0) {
                                                            $q1->where('fee', '<=', trim($requested_data['fees']))->where('status', 1);
                                                        }
                                                        $q1->select(['id', 'user_id', 'course_name', 'slug', 'degree_level', 'fee', 'next_intake as intake', 'visa_type'])->where('status', 1); //->take(4)->get()->random();//->inRandomOrder()->limit(4); //->orderByRaw('LOWER(course_name) asc');
                                            }, 'singleAddress' => function($q8) {
                                                $q8->select(['id', 'user_id', 'address', 'latitude', 'longitude'])->where('status', 1);
                                            }, 'userDetail' => function($q12) {
                                                $q12->select(['id', 'user_id', 'logo', 'website', 'about_university', 'ranking'])->where('status', 1);
                                            }, 'course.courseDetail' => function($q1) {
                                                $q1->select(['id', 'course_id', 'degree'])->where('status', 1);
                                                    }])
                                                        ->has('course')->has('course.courseDetail')->has('singleAddress')->has('userDetail');

                                        if ((isset($requested_data['course_name']) && !empty($requested_data['course_name'])) ||
                                                (isset($requested_data['degree_level']) && !empty($requested_data['degree_level'])) ||
                                                (isset($requested_data['visa_type']) && !empty($requested_data['visa_type'])) ||
                                                (isset($requested_data['entry_requirement']) && !empty($requested_data['entry_requirement'])) ||
                                                (isset($requested_data['fees']) && !empty($requested_data['fees']))) {

                                            $search = $search->whereHas('course', function($q1) use($requested_data) {
                                                #if course name is selected
                                                if (isset($requested_data['course_name']) && !empty($requested_data['course_name'])) {
                                                    $q1->whereRaw('LOWER(course_name) like "%' . strtolower(trim($requested_data['course_name']) . '%"'))->where('status', 1);
                                                }
                                                #if degree level is selected
                                                if (isset($requested_data['degree_level']) && !empty($requested_data['degree_level'])) {
                                                    $q1->where('degree_level', trim($requested_data['degree_level']))->where('status', 1);
                                                }
                                                #if visa type is selected
                                                if (isset($requested_data['visa_type']) && !empty($requested_data['visa_type'])) {
                                                    $q1->whereRaw('LOWER(visa_type) like "%' . strtolower(trim($requested_data['visa_type']) . '%"'))->where('status', 1);
                                                }
                                                #if entry requirement is selected
                                                if (isset($requested_data['entry_requirement']) && !empty($requested_data['entry_requirement'])) {
                                                    $q1->whereHas('courseDetail', function($q2) use($requested_data) {
                                                        $q2->whereRaw('LOWER(degree) like "%' . strtolower(trim($requested_data['entry_requirement'])) . '%"')->where('status', 1);
                                                    });
                                                }
                                                #if annual fee is selected
                                                if (isset($requested_data['fees']) && !empty($requested_data['fees']) && $requested_data['fees'] > 0) {
                                                    $q1->where('fee', '<=', trim($requested_data['fees']))->where('status', 1);
                                                }
                                            });
                                        };

                                        #if location is selected
                                        if (isset($requested_data['location']) && !empty($requested_data['location'])) {
                                            $search = $search->whereHas('address', function($q9) use($requested_data) {
                                                $search = strtolower(trim($requested_data['location']));
                                                $q9->where(function($q10) use($search) {
                                                    $q10->whereRaw('LOWER(postal_code) like "%' . $search . '%"')
                                                            ->orWhereRaw('LOWER(city) like "%' . $search . '%"')
                                                            ->orWhereRaw('LOWER(country) like "%' . $search . '%"')
                                                            ->orWhereRaw('LOWER(address) like "%' . $search . '%"')
                                                            ->orWhereRaw('LOWER(address2) like "%' . $search . '%"');
                                                })->where('status', 1);
                                            });
                                        };

                                        #if university name is selected
                                        if (isset($requested_data['university_name']) && !empty($requested_data['university_name'])) {
                                            $search = $search->whereRaw('LOWER(name) like "%' . strtolower(trim($requested_data['university_name'])) . '%"');
                                        };
                                         #search by keyword
                                         if (isset($requested_data['keyword']) && !empty($requested_data['keyword'])) {
                                            $keyword = $requested_data['keyword'];
                                            $search = $search->where('name','like', "%{$keyword}%");
                                           // $search = $search->whereRaw('LOWER(name) like "%' . strtolower(trim($requested_data['keyword'])) . '%"');
                                        };

                                        #if ranking is selected
                                        if (isset($requested_data['ranking']) && !empty($requested_data['ranking']) && ($requested_data['ranking'] > 0)) {
                                            $search = $search->where('status', 1)->where('role_id', 2);
                                            if ((isset($requested_data['university_name']) && !empty($requested_data['university_name'])) ||
                                                    (isset($requested_data['course_name']) && !empty($requested_data['course_name'])) ||
                                                    (isset($requested_data['degree_level']) && !empty($requested_data['degree_level'])) ||
                                                    (isset($requested_data['visa_type']) && !empty($requested_data['visa_type'])) ||
                                                    (isset($requested_data['entry_requirement']) && !empty($requested_data['entry_requirement'])) ||
                                                    (isset($requested_data['fees']) && !empty($requested_data['fees'])) ||
                                                    (isset($requested_data['location']) && !empty($requested_data['location']))) {
                                                //$search = $search->whereIn('is_sponsor', [0, 1]);
                                            } else {
                                                $search = $search->where('is_sponsor', 0);
                                            }


                                            $query = $get_ids = $search;
                                            $count = $query->count(); #total count after filteration except ranking
                                            //print_r($count);die;
                                            if ($count > 0) {
                                                $ranking_percentage = trim($requested_data['ranking']);
                                                $total = floor($count * ($ranking_percentage / 100)); #total count after filteration with ranking
                                                //echo $count . "--" . $ranking_percentage . "--" . $total;die;
                                                if ($total > 0) {
                                                    $ids = $get_ids->pluck('id')->toArray(); #get all university ids after filteration
                                                    if (!empty($ids)) {
                                                        $order_by_ids = UserDetail::whereIn('user_details.user_id', $ids)
                                                                        ->join('users', function($join) {
                                                                            $join->on('user_details.user_id', '=', 'users.id');
                                                                        })->orderBy('user_details.ranking', 'DESC')->orderBy('users.name', 'ASC')
                                                                        ->take($total)->pluck('user_id')->toArray(); #order by ranking

                                                        $search = $search->select(['id', 'name', 'slug', 'email', 'role_id', 'status', 'is_sponsor'])->whereIn('id', $order_by_ids)
                                                                        ->orderByRaw(\DB::raw("FIELD(id, " . implode(",", $order_by_ids) . ")"))
                                                                        ->paginate($per_page_record, ['*'], 'page', $page)->toArray();
                                                        if (isset($search['data']) && !empty($search['data'])) {
                                                            array_walk_recursive($search['data'], function (&$item, $key) {
                                                                if ($key == 'is_sponsor') {
                                                                    $item = 0;
                                                                }
                                                            });
                                                            return $search;
                                                        }
                                                    }
                                                }
                                            }
                                        } else {
                                            $search = $search->select(['id', 'name', 'slug', 'email', 'role_id', 'status', 'is_sponsor']);
                                            if ((isset($requested_data['university_name']) && !empty($requested_data['university_name'])) ||
                                                    (isset($requested_data['course_name']) && !empty($requested_data['course_name'])) ||
                                                    (isset($requested_data['degree_level']) && !empty($requested_data['degree_level'])) ||
                                                    (isset($requested_data['visa_type']) && !empty($requested_data['visa_type'])) ||
                                                    (isset($requested_data['entry_requirement']) && !empty($requested_data['entry_requirement'])) ||
                                                    (isset($requested_data['fees']) && !empty($requested_data['fees'])) ||
                                                    (isset($requested_data['location']) && !empty($requested_data['location']))) {
                                                //$search = $search->whereIn('is_sponsor', [0, 1]);
                                            } else {
                                                $search = $search->where('is_sponsor', 0);
                                            }
                                            $search = $search->where('status', 1)->where('role_id', 2)->orderBy('name', 'ASC')
                                                            ->paginate($per_page_record, ['*'], 'page', $page)->toArray();
                                            if (isset($search['data']) && !empty($search['data'])) {
                                                array_walk_recursive($search['data'], function (&$item, $key) {
                                                    if ($key == 'is_sponsor') {
                                                        $item = 0;
                                                    }
                                                });
                                                return $search;
                                            }
                                        }
                                        return false;
                                    }

                                    /**
                                     * @return \Illuminate\Http\JsonResponse
                                     *
                                     * @SWG\Get(
                                     *   path="/frontend/search/search-and-get-university",
                                     *   summary="Search and get university",
                                     *   produces={"application/json"},
                                     *   tags={"Search University"},
                                     *   @SWG\Parameter(
                                     *     name="keyword",
                                     *     in="query",
                                     *     description="keyword to search (university)",
                                     *     required=true,
                                     *     type="string"
                                     *   ),
                                     *   @SWG\Response(response=200, description="Success"),
                                     *   @SWG\Response(response=400, description="Failed"),
                                     *   @SWG\Response(response=405, description="Undocumented data"),
                                     *   @SWG\Response(response=500, description="Internal server error")
                                     * )
                                     */
                                    public function SearchAndGetUniversity(Request $request) {
                                        $requested_data = $request->all();

                                        // update last searched
                                        if (isset($requested_data['data'])) {
                                            User::where('id', $requested_data['data']['id'])->update(['last_searched' => time()]);
                                        }
                                        $search = strtolower(trim($requested_data['keyword']));
                                        if (isset($search) && !empty($search)) {
                                            $users = User::whereRaw('LOWER(name) like "%' . $search . '%"')
                                                            ->where('role_id', 2)->where('status', 1)
                                                            ->has('course')->has('singleAddress')->has('userDetail')
                                                            ->groupBy('name')->orderBy('id', 'desc')->take(5)->get(['id', 'name'])->toArray();

                                            if (empty($users)) {
                                                return Response::json(\Config::get('error.no_record_found'));
                                            } else {
                                                $response = \Config::get('success.success_record_found');
                                                $response['data'] = $users;

                                                return Response::json($response);
                                            }
                                        }
                                    }

                                    /**
                                     * @return \Illuminate\Http\JsonResponse
                                     *
                                     * @SWG\Get(
                                     *   path="/frontend/search/search-and-get-course",
                                     *   summary="Search and get course",
                                     *   produces={"application/json"},
                                     *   tags={"Search University"},
                                     *   @SWG\Parameter(
                                     *     name="keyword",
                                     *     in="query",
                                     *     description="keyword to search (course)",
                                     *     required=true,
                                     *     type="string"
                                     *   ),
                                     *   @SWG\Response(response=200, description="Success"),
                                     *   @SWG\Response(response=400, description="Failed"),
                                     *   @SWG\Response(response=405, description="Undocumented data"),
                                     *   @SWG\Response(response=500, description="Internal server error")
                                     * )
                                     */
                                    public function SearchAndGetCourse(Request $request) {
                                        $requested_data = $request->all();
                                        $search = strtolower(trim($requested_data['keyword']));
                                        if (isset($search) && !empty($search)) {

                                            $courses = Course::whereRaw('LOWER(course_name) like "%' . $search . '%"')->where('status', 1)
                                                            ->whereHas('user', function($q1) {
                                                                $q1->where('status', 1);
                                                            })
                                                            ->has('user.singleAddress')->has('user.userDetail')
                                                            ->groupBy('course_name')->orderBy('id', 'desc')->take(5)->get(['id', 'course_name'])->toArray();

                                            if (empty($courses)) {
                                                return Response::json(\Config::get('error.no_record_found'));
                                            } else {
                                                $response = \Config::get('success.success_record_found');
                                                $response['data'] = $courses;
                                                return Response::json($response);
                                            }
                                        }
                                    }

                                    /**
                                     * @return \Illuminate\Http\JsonResponse
                                     *
                                     * @SWG\Get(
                                     *   path="/frontend/search/search-and-get-location",
                                     *   summary="Search and get course",
                                     *   produces={"application/json"},
                                     *   tags={"Search University"},
                                     *   @SWG\Parameter(
                                     *     name="keyword",
                                     *     in="query",
                                     *     description="keyword to search (location)",
                                     *     required=true,
                                     *     type="string"
                                     *   ),
                                     *   @SWG\Response(response=200, description="Success"),
                                     *   @SWG\Response(response=400, description="Failed"),
                                     *   @SWG\Response(response=405, description="Undocumented data"),
                                     *   @SWG\Response(response=500, description="Internal server error")
                                     * )
                                     */
                                    public function SearchAndGetLocation(Request $request) {
                                        $requested_data = $request->all();
                                        $search = strtolower(trim($requested_data['keyword']));
                                        if (isset($search) && !empty($search)) {
                                            $addresses1 = UserAddress::where(function($query) use($search) {
                                                                $query//->whereRaw('LOWER(postal_code) like "%' . $search . '%"')
                                                                ->orWhereRaw('LOWER(city) like "%' . $search . '%"');
                                                                //->orWhereRaw('LOWER(country) like "%' . $search . '%"')
                                                                //->orWhereRaw('LOWER(address) like "%' . $search . '%"')
                                                                //->orWhereRaw('LOWER(address2) like "%' . $search . '%"');
                                                            })
                                                            ->whereHas('user', function($q1) {
                                                                $q1->where('status', 1);
                                                            })
                                                            ->has('user.course')->has('user.userDetail')
                                                            ->where('status', 1)->groupBy('country')->orderBy('id', 'desc')->take(5)->get(['id', 'city'])->toArray();

                                            $addresses2 = UserAddress::where(function($query) use($search) {
                                                                $query->orWhereRaw('LOWER(country) like "%' . $search . '%"');
                                                            })
                                                            ->whereHas('user', function($q1) {
                                                                $q1->where('status', 1);
                                                            })
                                                            ->has('user.course')->has('user.userDetail')
                                                            ->where('status', 1)->groupBy('country')->orderBy('id', 'desc')->take(5)->get(['id', 'country'])->toArray();

                                            $city = !empty($addresses1) ? array_column($addresses1, 'city', 'id') : [];
                                            $country = !empty($addresses2) ? array_column($addresses2, 'country', 'id') : [];
                                            $addresses = !empty($addresses1) || !empty($addresses2) ? array_unique(array_merge($city, $country), SORT_REGULAR) : [];

                                            if (empty($addresses)) {
                                                return Response::json(\Config::get('error.no_record_found'));
                                            } else {
                                                $response = \Config::get('success.success_record_found');
                                                $arr = $location_arr = [];
                                                foreach ($addresses as $k => $v) {
                                                    $arr['id'] = array_search($v, $city) == false ? array_search($v, $country) : array_search($v, $city);
                                                    $arr['address'] = $v;
                                                    $location_arr[] = $arr;
                                                }
                                                $response['data'] = $location_arr;
                                                return Response::json($response);
                                            }
                                        }
                                    }

                                    /**
                                     * @return \Illuminate\Http\JsonResponse
                                     *
                                     * @SWG\Get(
                                     *   path="/frontend/search/search-and-get-visa",
                                     *   summary="Search and get visa type",
                                     *   produces={"application/json"},
                                     *   tags={"Search University"},
                                     *   @SWG\Parameter(
                                     *     name="keyword",
                                     *     in="query",
                                     *     description="keyword to search (visa type)",
                                     *     required=true,
                                     *     type="string"
                                     *   ),
                                     *   @SWG\Response(response=200, description="Success"),
                                     *   @SWG\Response(response=400, description="Failed"),
                                     *   @SWG\Response(response=405, description="Undocumented data"),
                                     *   @SWG\Response(response=500, description="Internal server error")
                                     * )
                                     */
                                    public function SearchAndGetVisa(Request $request) {
                                        $requested_data = $request->all();
                                        $search = strtolower(trim($requested_data['keyword']));
                                        if (isset($search) && !empty($search)) {

                                            $visa_type = Course::whereRaw('LOWER(visa_type) like "%' . $search . '%"')->where('status', 1)
                                                            ->whereHas('user', function($q1) {
                                                                $q1->where('status', 1);
                                                            })
                                                            ->has('user.singleAddress')->has('user.userDetail')
                                                            ->groupBy('visa_type')->orderBy('id', 'desc')->take(5)->get(['id', 'visa_type']);
                                            if (count($visa_type)) {
                                                $response = \Config::get('success.success_record_found');
                                                $response['data'] = $visa_type;
                                                return Response::json($response);
                                            } else {
                                                return Response::json(\Config::get('error.no_record_found'));
                                            }
                                        }
                                    }

                                    /**
                                     * @return \Illuminate\Http\JsonResponse
                                     *
                                     * @SWG\Get(
                                     *   path="/frontend/search/search-and-get-entry-requirements",
                                     *   summary="Search and get entry-requirements",
                                     *   produces={"application/json"},
                                     *   tags={"Search University"},
                                     *   @SWG\Parameter(
                                     *     name="keyword",
                                     *     in="query",
                                     *     description="keyword to search (entry-requirements)",
                                     *     required=true,
                                     *     type="string"
                                     *   ),
                                     *   @SWG\Response(response=200, description="Success"),
                                     *   @SWG\Response(response=400, description="Failed"),
                                     *   @SWG\Response(response=405, description="Undocumented data"),
                                     *   @SWG\Response(response=500, description="Internal server error")
                                     * )
                                     */
                                    public function SearchAndGetEntryRequirements(Request $request) {
                                        $requested_data = $request->all();
                                        $search = strtolower(trim($requested_data['keyword']));
                                        if (isset($search) && !empty($search)) {

                                            $entry_requirements = CourseDetail::whereRaw('LOWER(degree) like "%' . $search . '%"')->where('status', 1)
                                                            ->whereHas('course', function($q1) {
                                                                $q1->where('status', 1);
                                                            })
                                                            ->whereHas('course.user', function($q1) {
                                                                $q1->where('status', 1);
                                                            })
                                                            ->has('course.user.singleAddress')->has('course.user.userDetail')
                                                            ->groupBy('degree')->orderBy('id', 'desc')->take(5)->get(['id', 'degree']);
                                            //dd($entry_requirements);

                                            if (empty($entry_requirements)) {
                                                return Response::json(\Config::get('error.no_record_found'));
                                            } else {
                                                $response = \Config::get('success.success_record_found');
                                                $response['data'] = $entry_requirements;
                                                return Response::json($response);
                                            }
                                        }
                                    }

                                    /* Save search records */

                                    private function saveSearchData($data, $request) {
                                        if ((isset($data['university_name']) && !empty($data['university_name'])) || (isset($data['course_name']) && !empty($data['course_name'])) || (isset($data['degree_level']) && !empty($data['degree_level'])) || (isset($data['location']) && !empty($data['location'])) || (isset($data['visa_type']) && !empty($data['visa_type'])) || (isset($data['entry_requirement']) && !empty($data['entry_requirement'])) || (isset($data['ranking']) && !empty($data['ranking']) && $data['ranking'] > 0) || (isset($data['fees']) && !empty($data['fees']) && $data['fees'] > 0)) {
                                            #get public ip address 
                                            $ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
                                            $ip_origin = $this->getLocationInfoByIp($ip); #get location by ip
                                            $insert = [];
                                            if (!isset($data['data']['id'])) {
                                                $headers = $request->headers->all();
                                                if (isset($headers['searchcookie'][0]) && !empty($headers['searchcookie'][0])) {
                                                    $searchcookie = $headers['searchcookie'][0];
                                                    $expired_date = time() - 86400;
                                                    $chk_already_exitance = SearchAnalytic::where('cache_id', $searchcookie)->where('status', 1)->orderBy('id', 'DESC')->first();
                                                    if ((isset($chk_already_exitance->updated_at) && $chk_already_exitance->updated_at > $expired_date) || empty($chk_already_exitance)) {
                                                        $value = $searchcookie;
                                                    } else {
                                                        $value = $this->getVerificationCode();
                                                    }
                                                } else {
                                                    $value = $this->getVerificationCode();
                                                }
                                                $insert['cache_id'] = isset($value) && !empty($value) ? $value : '';
                                            }

                                            $insert['ip_address'] = isset($ip) && !empty($ip) ? trim($ip) : '';
                                            $insert['ip_origin'] = isset($ip_origin) && !empty($ip_origin) ? trim($ip_origin) : '';
                                            $insert['user_id'] = isset($data['data']['id']) && !empty($data['data']['id']) ? trim($data['data']['id']) : 0;
                                            $insert['university'] = isset($data['university_name']) && !empty($data['university_name']) ? trim($data['university_name']) : '';
                                            $insert['course'] = isset($data['course_name']) && !empty($data['course_name']) ? trim($data['course_name']) : '';
                                            $insert['degree_level'] = isset($data['degree_level']) && !empty($data['degree_level']) ? trim($data['degree_level']) : '';
                                            $insert['location'] = isset($data['location']) && !empty($data['location']) ? trim($data['location']) : '';
                                            $insert['visa'] = isset($data['visa_type']) && !empty($data['visa_type']) ? trim($data['visa_type']) : '';
                                            $insert['entry_requirement'] = isset($data['entry_requirement']) && !empty($data['entry_requirement']) ? trim($data['entry_requirement']) : '';
                                            $insert['ranking'] = isset($data['ranking']) && !empty($data['ranking']) && $data['ranking'] > 0 ? trim($data['ranking']) : '';
                                            $insert['annual_fee'] = isset($data['fees']) && !empty($data['fees']) && $data['fees'] > 0 ? trim($data['fees']) : '';
                                            $insert['status'] = 1;
                                            $insert['created_at'] = time();
                                            $insert['updated_at'] = time();
                                            $create = SearchAnalytic::create($insert);
                                            return $create->cache_id;
                                        }
                                    }

                                    #get location by ip

                                    function getLocationInfoByIp($remote) {
                                        $ip = $remote;
                                        //$ip='182.156.229.242';
                                        $ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
                                        $country = $ip_data && $ip_data->geoplugin_countryName != null ? $ip_data->geoplugin_countryName : '';
                                        return $country;
                                    }

                                }
                                
