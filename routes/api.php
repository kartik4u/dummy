<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "API" middleware group. Enjoy building your API!
|
 */

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
return $request->user();
});*/
Route::get('test', function () {
    return 'Post is working';
});

Route::group(['middleware' => 'cors'], function () {



Route::group([
    'namespace' => 'API'
        ], function() {
            
    // users
    Route::post('user/login', 'UsersController@login');
    Route::post('user/sociallogin',['uses'=>'UsersController@socialLogin']);
    Route::post('user/signUp', 'UsersController@signUp');
    Route::post('user/resendVerification', 'UsersController@resendVerification');
    Route::post('user/forgotPassword', 'UsersController@forgotPassword');

    // pages
    Route::post('/pages/contact', ['uses' => 'PagesController@contact']);
    Route::get('/pages/get-terms-and-conditions', ['uses' => 'PagesController@getTermsAndConditions']);
    Route::get('/pages/get-privacy-policy', ['uses' => 'PagesController@getPrivacyPolicy']);
    Route::get('/pages/aboutus', ['uses' => 'PagesController@getAboutus']);

    // stories
    Route::get('story/getHomePage', 'StoriesController@getHomePage');
    Route::get('story/weeklyPayment', 'StoriesController@weeklyPayment');


    // share story and episode   //       http://server.localepisodic.com/api/story/share/2/1
    Route::get('story/share/{story_id}/{episode_id}', function(){
       return  view('share');
    });
    // payment
    Route::group(['prefix' => 'payment'], function () {
        Route::post('generateToken', 'PaymentController@generateToken');
    });

    

    // Routing for frontend user
    Route::group(['middleware' => [
        'auth:api',
        'user_data',
    ]], function () {
        // patment
        Route::group(['prefix' => 'payment'], function () {
            Route::post('subcribePayment', 'PaymentController@subcribePayment');
        });

        // pages

        Route::post('/pages/sendPageMail', ['uses' => 'PagesController@sendPageMail']);
        Route::get('/pages/getDonation', ['uses' => 'PagesController@getDonation']);

        // users
        Route::post('user/logout', 'UsersController@logout');
        Route::post('user/changePassword', 'UsersController@changePassword');
        Route::get('user/getProfile', 'UsersController@getProfile');
        Route::get('user/getMyActivities', 'UsersController@getMyActivities');
        Route::get('user/getMyFollowers', 'UsersController@getMyFollowers');
        Route::post('user/uploadImage', 'UsersController@uploadImage');
        Route::post('user/saveRating', 'UsersController@saveRating');
        Route::get('user/getComments', 'UsersController@getComments');
        Route::post('user/postComment', 'UsersController@postComment');
        Route::post('user/favouriteUnfavourite', 'UsersController@favouriteUnfavourite');
        Route::post('user/switchAccount', 'UsersController@switchAccount');
        Route::get('user/getSubscriptionPlans', 'UsersController@getSubscriptionPlans');
        Route::post('user/pushNotification', 'UsersController@pushNotification');
        Route::get('user/getNotification', 'UsersController@getNotification');
        Route::post('user/deleteUser', 'UsersController@deleteUser');
        Route::post('user/editProfile', 'UsersController@editProfile');
        Route::post('user/saveStripe', 'UsersController@saveStripe');
        Route::post('user/deleteUser', 'UsersController@deleteUser');
        Route::post('user/share', 'UsersController@share');
        Route::post('user/followUnfollow', 'UsersController@followUnfollow');
        Route::post('user/addCard', 'UsersController@addCard');
        Route::get('user/getMyCards', 'UsersController@getMyCards');


        //Story
    
        Route::post('story/saveAdditionalInfo', 'StoriesController@saveAdditionalInfo');
        Route::get('story/getAllGenres', 'StoriesController@getAllGenres');
        Route::get('story/getMyGenres', 'StoriesController@getMyGenres');
        Route::post('story/saveGenres', 'StoriesController@saveGenres');
        Route::get('story/getStories', 'StoriesController@getStories');
        Route::post('story/saveStory', 'StoriesController@saveStory');
        Route::get('story/getWritters', 'StoriesController@getWritters');
        Route::post('story/viewStory', 'StoriesController@viewStory');
        Route::get('story/getStoryDetail', 'StoriesController@getStoryDetail');
        Route::post('story/addOrdeleteDownload', 'StoriesController@addOrdeleteDownload');
        Route::post('story/addStory', 'StoriesController@addStory');
        Route::post('story/addEpisode', 'StoriesController@addEpisode');
        Route::get('story/storyReport', 'StoriesController@storyReport');
        Route::post('story/deleteStory', 'StoriesController@deleteStory');
        Route::post('story/approve', 'StoriesController@approve');

        
        # Chat
        Route::get('chat/getPersonalChat', 'ChatsController@getPersonalChat');
        Route::get('chat/getInboxChat', 'ChatsController@getInboxChat');
        Route::post('chat/deleteSpecificMessage', 'ChatsController@deleteSpecificMessage');
        Route::post('chat/deleteChat', 'ChatsController@deleteChat');
        Route::post('chat/sendMessage', 'ChatsController@sendMessage');
        
    });





Route::group(['prefix' => 'admin','namespace' => 'Admin'], function () {

    // Routes For Admin  Without Login
    Route::post('login', 'UsersController@login');
    Route::post('forgotPassword', 'UsersController@forgotPassword');
    Route::post('resetPassword', 'UsersController@resetPassword');
    Route::post('validateForgotExpiry', 'UsersController@validateForgotPasswordExpiry');
    
    // after login
    Route::group(['middleware' => [
        'auth:api',
        'user_data'
    ]], function () {
        Route::get('downloadExcel', 'UsersController@downloadUsers');

        // users routes
        Route::get('logout', 'UsersController@logout');
        Route::put('changePassword', 'UsersController@changePassword');
        Route::get('getUsers', 'UsersController@getUsers');
        Route::post('changeStatus', 'UsersController@changeStatus');
        Route::post('deleteUser', 'UsersController@deleteUser');
        Route::get('userDetail', 'UsersController@userDetail');
        Route::get('dashboard', 'UsersController@dashboard');
        Route::get('getAllRoles', 'UsersController@getAllRoles');

        
        // report routes
        Route::get('reportsListing', 'ReportController@reportsListing');
        Route::post('deleteReport', 'ReportController@deleteReport');
        Route::get('getReportDatail', 'ReportController@getReportDatail');
        Route::get('getReportTypes', 'ReportController@getReportTypes');


        // review routes
        Route::get('reviewListing', 'ReviewController@reviewListing');
        Route::post('postReview', 'ReviewController@postReview');
        Route::get('reviewDetail', 'ReviewController@reviewDetail');

            
    
        // page routes
        Route::get('getPages', 'PagesController@getPages');
        Route::get('getPage', 'PagesController@getPage');
        Route::post('updatePage', 'PagesController@updatePage');
        
        // faq
        Route::get('getFaqs', 'PagesController@getFaqs');
        Route::post('createFaq', 'PagesController@createFaq');
        Route::post('editFaq', 'PagesController@editFaq');
        Route::post('deleteFaq', 'PagesController@deleteFaq');
        Route::get('viewFaq', 'PagesController@viewFaq');


        // subscription
        Route::get('getPlans', 'SubscriptionsController@getPlans');
        Route::post('createPlan', 'SubscriptionsController@createPlan');
        Route::post('editPlan', 'SubscriptionsController@editPlan');
        Route::post('deletePlan', 'SubscriptionsController@deletePlan');
        Route::get('viewPlan', 'SubscriptionsController@viewPlan');

        // permissions
        Route::get('getAllPermissions', 'PermissionsController@getAllPermissions');
        Route::post('createPermissions', 'PermissionsController@createPermissions');
        Route::post('updatePermissions', 'PermissionsController@updatePermissions');
        Route::post('deletePermissions', 'PermissionsController@deletePermissions');
        Route::get('viewPermission', 'PermissionsController@viewPermission');
        Route::get('getPermissions', 'PermissionsController@getPermissions');


        // manage Roles
        Route::get('getAllRolesData', 'RolesController@getAllRoles');
        Route::post('createRoles', 'RolesController@createRoles');
        Route::post('updateRoles', 'RolesController@updateRoles');
        Route::post('deleteRoles', 'RolesController@deleteRoles');
        Route::get('viewRole', 'RolesController@viewRole');


        // transactions
        Route::get('getAllTransactions', 'TransactionsController@getAllTransactions');

    });
});




});
});
