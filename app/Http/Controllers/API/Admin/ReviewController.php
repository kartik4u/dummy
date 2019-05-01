<?php

#Controller Name: ReviewController
#Developer      : Narinder
#Purpose        : Manage reviews


namespace App\Http\Controllers\API\Admin;

// Load Model
use App\Interfaces\AdminReviewInterface;
use App\Http\Controllers\Controller;
use App\Http\Traits\AdminTrait;
use App\Role;
use App\Models\ReportedUser;
use App\User;
use App\Models\Review;
use Carbon\Carbon;
use Config;
use Hash;
use App\Models\RatingSubject;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\Review\PostReviewRequest;
use App\Http\Requests\Admin\Review\ReviewDetailRequest;
use Illuminate\Support\Facades\Auth;
use Image;
use Lcobucci\JWT\Parser;
use Mail;
use Response;


class ReviewController extends Controller implements AdminReviewInterface{

    use AdminTrait;

        /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/admin/reviewListing",
     *   summary="review listing",
     *   produces={"application/json"},
     *   tags={"ADMIN REVIEW APIS"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string"
     *   ),
     *  @SWG\Parameter(
     *     name="search",
     *     in="query",
     *     required=false,
     *     type="string",
     *     description = "search by user",
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    /*
     * Function to get reviews data
     * @param request 
     * @return response (status, message, success/failure)
     */

    public function reviewListing(Request $request) {
        $requested_data = $request->all();
        $page_record = \Config::get('variable.page_per_record');
        $reviews = Review::where('status',0)->with(['toUser' => function($query) {
                $query->select('id', 'name', 'role_id');
            },
            'fromUser' => function($query) {
                $query->select('id', 'name', 'role_id');
            }
        ]);

        #Check if search(name) with any keyword here
        if (isset($requested_data['search'])) {
            $ids = User::where('name', 'LIKE', '%' . $requested_data['search'] . '%')->pluck('id');
            $reviews = $reviews->where(function($query) use ($ids) {
                return $query->whereIn('review_by', $ids)
                                ->orWhereIn('review_to', $ids);
            });
        }

        // date filter
        // if (isset($requested_data['start_date']) && empty(!$requested_data['end_date'])) {
        //     $reviews = $reviews->where('created_at', '>=', $requested_data['start_date'])->where('created_at', '<=', $requested_data['end_date']);
        // }
        $reviews = $reviews->orderby('created_at','desc')->paginate($page_record)->toArray();

         #response
         //if (count($reviews['data'])) {
            $data = \Config::get('admin_success.record_found');     # success results
        // } else {
        //     $data = \Config::get('admin_error.no_record_found');      # no results
        // }
        $data['data'] = $reviews;
        return Response::json($data);
    }



        /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/admin/reviewDetail",
     *   summary="review detail",
     *   produces={"application/json"},
     *   tags={"ADMIN REVIEW APIS"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string"
     *   ),
     *  @SWG\Parameter(
     *     name="review_id",
     *     in="query",
     *     required=true,
     *     type="number"
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */

    /*
     * Function to get single review data
     * @param request 
     * @return response (status, message, success/failure)
     */

    public function reviewDetail(ReviewDetailRequest $request) {
        #Validations
        $requested_data = $request->all();

        $review = Review::where('id', $requested_data['review_id'])->with(['toUser' => function($query) {
                        $query->select('id', 'name');
                    },
                    'fromUser' => function($query) {
                        $query->select('id', 'name');
                    }
                ])->first();
        #Check Final data here and send 
        if (isset($review) && !empty($review)) {
            $data = \Config::get('admin_success.record_found');     # success results
        } else {
            $data = \Config::get('admin_error.no_record_found');      # no results
        }
        $data['data'] = $review;
        return Response::json($data);
    }



    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/admin/postReview",
     *   summary="Post review",
     *   produces={"application/json"},
     *   tags={"ADMIN REVIEW APIS"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string"
     *   ),
     *  @SWG\Parameter(
     *     name="review_id",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description="review id"
     *   ),
     *  @SWG\Parameter(
     *     name="status",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description="status(1=>approved,2=>declined)"
     *   ),
     *  @SWG\Parameter(
     *     name="reason",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description="if rating declined ,then this filed is required"
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */

    /*
    /*
     * Function to post review
     * @param request 
     * @return response (status, message, success/failure)
     */

    public function postReview(PostReviewRequest $request) {
        #Validations
        $requested_data = $request->all();
        $requested_data['reason'] = isset($requested_data['reason'])?$requested_data['reason']:'test'; 
        $reviews = Review::where('id', $requested_data['review_id'])->update(['status' => $requested_data['status']]);
        // post review
        if ($requested_data['status'] == 1) {
            $res = \Config::get('admin_success.review_approved');     # success results
        } else {
            // declined review by email to user
            $this->reviewRejected($requested_data);    
            $res = \Config::get('admin_success.review_declined');     # success results
        }

        return Response::json($res);
    }

}
