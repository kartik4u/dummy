<?php

namespace App\Interfaces;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\UserChangePasswordRequest;
use App\Http\Requests\UserLoginRequest;
//use App\Http\Requests\UserProfileImageRequest;
//use App\Http\Requests\UserProfileRequest;
//use App\Http\Requests\UserResendVerifyRequest;
use App\Http\Requests\UserSignupRequest;
use App\Http\Requests\GetCommentsRequest;
use App\Http\Requests\DeleteUserRequest;
use App\Http\Requests\EditProfileRequest;
use App\Http\Requests\SaveStripeRequest;
use App\Http\Requests\FollowUnfollowRequest;
use App\Http\Requests\PostCommentRequest;
use App\Http\Requests\ViewProfileRequest;
use App\Http\Requests\SaveRatingRequest;
use App\Http\Requests\FavouriteUnfavouriteRequest;
use App\Http\Requests\CommonStoryRequest;
use App\Http\Requests\AddCardRequest;



use Illuminate\Http\Request;

interface UserInterface
{
    public function signUp(UserSignupRequest $request);
    public function login(UserLoginRequest $request);
    public function logout(Request $request);
    public function forgotPassword(ForgotPasswordRequest $request);
    public function changePassword(UserChangePasswordRequest $request);
    public function getNotification(Request $request);
    public function followUnfollow(FollowUnfollowRequest $request);
    public function getComments(GetCommentsRequest $request);
    public function postComment(PostCommentRequest $request);
    public function getProfile(Request $request);
    public function pushNotification(Request $request);
    public function saveRating(SaveRatingRequest $request);
    public function switchAccount(Request $request);
    public function getSubscriptionPlans(Request $request);
    public function favouriteUnfavourite(FavouriteUnfavouriteRequest $request);
    public function deleteUser(DeleteUserRequest $request);
    public function editProfile(EditProfileRequest $request);
    public function getMyActivities(Request $request);
    public function getMyFollowers(Request $request);
    public function uploadImage(Request $request);
    public function saveStripe(SaveStripeRequest $request);
    public function share(CommonStoryRequest $request);
    public function addCard(AddCardRequest $request);
    public function getMyCards(Request $request);

}
