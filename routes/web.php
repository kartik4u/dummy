<?php

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

//use App\User;

header('Access-Control-Allow-Credentials', 'true');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization,token,Token,admintoken, studenttoken, universitytoken,searchcookie');
header('Access-Control-Expose-Headers: token,admintoken, universitytoken, studenttoken,searchcookie');
header("Content-Type: application/json");
header('Access-Control-Allow-Origin:  *');

Route::get('/', function () {
    return view('welcome');
});

#Admin urls below
Route::group([
    'prefix' => 'admin',
    'namespace' => 'Admin'
        ], function() {

    #Befor login urls
    Route::post('login', ['uses' => 'AdminController@loginAdmin']);
    Route::post('forgot-password', ['uses' => 'AdminController@forgotPasswordAdmin']);
    Route::post('reset-password', ['uses' => 'AdminController@resetPasswordAdmin']);    
    Route::group(['middleware' => 'jwt-auth'], function () {
        Route::post('logout', ['uses' => 'AdminController@logoutAdmin']);
        Route::delete('delete-user', ['uses' => 'AdminController@deleteUser']);
        
        Route::post('change-password', ['uses' => 'AdminController@changePasswordAdmin']);
        //students listing

        Route::get('get-students', ['uses' => 'StudentController@getStudents']);
        Route::get('student/download-student', ['uses' => 'StudentController@downloadStudent']);  
          
        
        Route::post('get-university', ['uses' => 'UniversityController@getUniversity']);
        Route::post('change-status', ['uses' => 'StudentController@changeStatus']);
        Route::post('change-university-status', ['uses' => 'UniversityController@changeUniversityStatus']);
        Route::post('reject-university', ['uses' => 'UniversityController@rejectUniversity']);
        Route::post('university-detail', ['uses' => 'UniversityController@universityDetail']);
        Route::post('save-university-detail', ['uses' => 'UniversityController@saveUniversityDetail']);
        #favourites
        Route::get('/favourite/get-my-favourites', ['uses' => 'FavouriteCoursesController@getMyFavourites']);
        Route::get('/favourite/get-course-detail', ['uses' => 'FavouriteCoursesController@getCourseDetail']);
        Route::get('/favourite/get-favourite-course-data', ['uses' => 'FavouriteCoursesController@getFavouriteCourseData']);
        Route::get('/favourite/download-favourite', ['uses' => 'FavouriteCoursesController@downloadFavourite']);
        
        
        # search
        Route::get('search/getSearchData', ['uses' => 'SearchController@getSearchData']);    
        Route::get('search/download-search', ['uses' => 'SearchController@downloadSearch']);   
        

        
        
        #manage pages
        Route::get('pages/page-listing', ['uses' => 'PageController@pageListing']);
        Route::get('pages/get-single-page', ['uses' => 'PageController@getSinglePage']);
        Route::post('pages/save-page-content', ['uses' => 'PageController@savePageContent']);

        Route::post('download-universities', ['uses' => 'UniversityController@downloadUniversities']);   
    });
});

#Frontend urls below
Route::group([
    'prefix' => 'frontend',
    'namespace' => 'Frontend'
        ], function() {

    #Befor login urls
    Route::post('/auth/register-data-check', ['uses' => 'AuthController@registerDataCheck']);
    Route::post('/auth/register', ['uses' => 'AuthController@register']);
    Route::post('/auth/verify/', ['uses' => 'AuthController@verify']);
    Route::post('/auth/forgot-password', ['uses' => 'AuthController@forgotPassword']);
    Route::post('/auth/reset-password', ['uses' => 'AuthController@resetPassword']);
    Route::post('/auth/login', ['uses' => 'AuthController@login']);
    Route::post('/auth/check-reset-password', ['uses' => 'AuthController@checkresetPassword']);

    Route::post('/pages/contact', ['uses' => 'PagesController@contact']);
    Route::get('/pages/get-terms-and-conditions', ['uses' => 'PagesController@getTermsAndConditions']);
    Route::get('/pages/get-privacy-policy', ['uses' => 'PagesController@getPrivacyPolicy']);

    /* search university */
    Route::get('/search/get-autocomplete-search-options', ['uses' => 'SearchController@getAutocompleteSearchOptions']);
    Route::post('/search/search-university', ['uses' => 'SearchController@searchUniversity']);//->middleware('sessions');
    Route::get('/search/search-and-get-university', ['uses' => 'SearchController@SearchAndGetUniversity']);
    Route::get('/search/search-and-get-course', ['uses' => 'SearchController@SearchAndGetCourse']);
    Route::get('/search/search-and-get-location', ['uses' => 'SearchController@SearchAndGetLocation']);
    Route::get('/search/search-and-get-visa', ['uses' => 'SearchController@SearchAndGetVisa']);
    Route::get('/search/search-and-get-entry-requirements', ['uses' => 'SearchController@SearchAndGetEntryRequirements']);
    Route::get('/university/get-degree-data', ['uses' => 'UniversityController@getDegreeData']);
    
    //Route::post('/search/save-search-data', ['uses' => 'SearchController@saveSearchData']);
    

    Route::get('/university/get-single-course-data', ['uses' => 'CourseController@getSingleCourseData']);
    Route::get('/university/get-quick-course-data', ['uses' => 'CourseController@getQuickCourseData']);
     
    Route::post('/user/accept-terms-privacy', ['uses' => 'UserController@acceptNewTermsPolicy']);
    
    Route::group(['middleware' => 'jwt-auth'], function () {

       /* favourite apis */
        Route::get('/favourite/get-my-favourites', ['uses' => 'FavouriteCoursesController@getMyFavourites']);
        Route::post('/favourite/favourite-unfavourite', ['uses' => 'FavouriteCoursesController@FavouriteUnfavourite']); 
        Route::get('/favourite/single-course-data', ['uses' => 'FavouriteCoursesController@singleCourseData']);
        // end
        
        Route::post('/auth/logout', ['uses' => 'AuthController@logout']);
        Route::post('/user/change-password', ['uses' => 'UserController@changePassword']);
        Route::delete('/user/delete-user', ['uses' => 'UserController@deleteUser']);
        Route::get('/user/get-activity-logs', ['uses' => 'UserController@getActivityLogs']);
        
        /* Start Ritu */
        Route::get('/address/get-addresses', ['uses' => 'AddressController@getAddresses']);
        Route::get('/address/get-single-addresses', ['uses' => 'AddressController@getSingleAddresses']);
        Route::post('/address/add-edit-addresses', ['uses' => 'AddressController@addEditAddresses']);
        Route::post('/address/delete-addresses', ['uses' => 'AddressController@deleteAddresses']);
        Route::post('/courses/add-edit-courses', ['uses' => 'CourseController@addEditCourses']);
        /* End Ritu */


        /* student apis */
        Route::get('/student/get-profile-details', ['uses' => 'StudentController@getProfileDetails']);
        Route::post('/student/save-profile-details', ['uses' => 'StudentController@saveProfileDetails']);
        Route::post('/student/change-profile-image', ['uses' => 'StudentController@changeProfileImage']);

        //*********university start*******
        Route::get('/university/get-university-profile', ['uses' => 'UniversityController@getUniversityProfile']);
        Route::post('/university/edit-university-profile', ['uses' => 'UniversityController@editUniversityProfile']);


        Route::get('/university/get-course-data', ['uses' => 'UniversityController@getCoursesData']);
        Route::post('/university/delete-course', ['uses' => 'UniversityController@deleteCourse']);
        
        


        //***********end university******

        /* common apis */
        Route::get('/user/get-user-details', ['uses' => 'UserController@getUserDetails']);

    });
   
});