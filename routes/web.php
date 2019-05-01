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
use App\Models\Page;
Route::get('/phpinfo', function () {
    return phpinfo();
});

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::namespace ('FRONTEND')->group(function () {
    Route::get('verification/{key}', ['uses' => 'UsersController@verifyUser']); # verify user on click email template
    Route::get('/reset-password/{token}', ['uses' => 'UsersController@showResetForm']); #show reset form on web
    Route::post('resetPassword', ['uses' => 'UsersController@resetPassword']); # user reset password
    Route::get('share/{id}', 'UsersController@shareJob');
    Route::get('download-resume/{id}', 'UsersController@downloadResume');

    Route::get('testing',function(){
        //return view('reset_success');
        // return view('verify');
        //     return view('error_verify');
            //   return view('reset_success');
               // return view('verify');
               //     return view('error_verify');
               // return view('reset')->with(
               //     ['token' => $token, 'email' => $request->email]
               // );
            //    return view('reset')->with(
            //          ['token' => 'asd', 'email' => 'fsdf']
            //      );

            $page_data = Page::where('meta_key','term')->orderBy('version','desc')->first();
            //$user['page_data'] = $page_data;

            
            //3
            $user = DB::table('users')->first();
           return view('mail.page_email',['data'=>$page_data ]);
          //2
           return view('mail.contact');

            //1
            $user['name'] ='asdfsd' ;
            $user['forgot_password'] ='asdfsd' ;
            return view('emails.users.forgot_password',['data'=>$user]);

    }
    
);



});


Route::get('test', function () {
    return 'Post is working 1';
});

