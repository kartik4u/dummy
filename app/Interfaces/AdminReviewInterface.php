<?php

namespace App\Interfaces;
use App\Http\Requests\Admin\Review\PostReviewRequest;
use App\Http\Requests\Admin\Review\ReviewDetailRequest;

use Illuminate\Http\Request;

interface AdminReviewInterface {
    public function postReview(PostReviewRequest $request);
    public function reviewListing(Request $request);
    public function reviewDetail(ReviewDetailRequest $request);   
}

