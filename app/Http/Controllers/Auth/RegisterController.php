<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Page;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Mail;
use Session;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
     */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        if ($data['role_id'] == 3) {
            $messages = array('terms_conditions.required' => 'Please check the terms and conditions.', 'policy.required' => 'Please check the privacy policy.');
            return Validator::make($data, [
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'email' => 'required|string|email|max:255|unique:users,email',
                'unique_id' => 'required|max:255|unique:users,unique_id',
                'password' => 'min:6|required_with:confirm_password|same:confirm_password',
                'confirm_password' => 'required',
                //'gender' => 'required',
                'university_name' => 'required|string|max:255',
                //'phone_no' => 'required|min:8|max:15',
                'terms_conditions' => 'required',
                'policy' => 'required',
            ], $messages);
        } else {
            $messages = array('terms_conditions.required' => 'Please check the terms and conditions.', 'policy.required' => 'Please check the privacy policy.');
            return Validator::make($data, [
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'email' => 'required|string|email|max:255|unique:users,email',
                //'unique_id' => 'required|max:255|unique:users,unique_id',
                'password' => 'min:6|required_with:confirm_password|same:confirm_password',
                'confirm_password' => 'required',
                //'gender' => 'required',
                // 'university_name' => 'required|string|max:255',
                //'phone_no' => 'required|min:8|max:15',
                'terms_conditions' => 'required',
                'policy' => 'required',
            ], $messages);

        }
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone_no' => @$data['phone_no'] ? $data['phone_no'] : '123',
            'unique_id' => @$data['unique_id'],
            'university_name' => @$data['university_name'],
            'password' => \Hash::make($data['password']),
            //generates a random string that is 20 characters long
            'verification_code' => str_random(20),
            'remember_token' => str_random(20),
            'subscribed_term_condition' => 1,
            'subscribed_privacy_policy' => 1,
            'role_id' => $data['role_id'],
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $sign_up_activity_log = ActivityLog::insert(array(
            array('user_id' => $user->id,
                'meta_key' => 'sign_up',
                'meta_value' => time(),
                'status' => 1,
                'created_at' => time(),
                'updated_at' => time(),
            ),
            array('user_id' => $user->id,
                'meta_key' => 'term_condition',
                'meta_value' => $data['terms_conditions'],
                'status' => 1,
                'created_at' => time(),
                'updated_at' => time(),
            ),
            array('user_id' => $user->id,
                'meta_key' => 'privacy_policy',
                'meta_value' => $data['policy'],
                'status' => 1,
                'created_at' => time(),
                'updated_at' => time(),
            ),
        )
        );

        //send verification mail to user
        //---------------------------------------------------------
        $data['verification_code'] = $user->verification_code;
        $data['name'] = $user->first_name . " " . $user->last_name;

        Mail::send('emails.confirm', $data, function ($message) use ($data) {
            $message->from('no-reply@Lifewill.com', "Lifewill");
            $message->subject("Welcome to Lifewill");
            $message->to($data['email']);
        });

        return $user;
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        //$this->guard()->login($user);
        //        return $this->registered($request, $user)
        //                        ?: redirect($this->redirectPath());

        $request->session()->flash('message.level', 'success');
        $request->session()->flash('message.content', 'A verification link has been sent to your email. Please verify your account.');

        return Redirect::route('login');
    }

    /**
     * Show the application show signup method.
     *
     * @return \Illuminate\Http\Response
     */
    public function signUpMethod()
    {
        return view('auth.signup-method');
    }

    /**
     * Show the application normal registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showNormalRegistrationForm()
    {
        $data = array();
        $data['first_name'] = \Config::get('variable.NORMAL_F_NAME');
        $data['last_name'] = \Config::get('variable.NORMAL_L_NAME');
        $data['gender'] = \Config::get('variable.NORMAL_GENDER');
        $data['phone_no'] = \Config::get('variable.NORMAL_PHONE_NO');
        $data['email'] = \Config::get('variable.NORMAL_EMAIL');
        $data['password'] = \Config::get('variable.NORMAL_PASSWORD');
        $data['confirm_password'] = \Config::get('variable.NORMAL_C_PASSWORD');
        $data['term_condition'] = \Config::get('variable.NORMAL_TERM_CONDITION');
        $data['privacy_policy'] = \Config::get('variable.NORMAL_PRIVACY_POLICY');

        $term_conditions = Page::where('meta_key', 'term')->latest()->first();
        $policy = Page::where('meta_key', 'privacy_policy')->latest()->first();

        return view('auth.noramal-user-signup', array('term' => $term_conditions, 'policy' => $policy, 'input_titles' => $data));
    }

    /**
     * Show the application assitant registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAssistantRegistrationForm()
    {
        $data = array();
        $data['first_name'] = \Config::get('variable.ASSISTANT_F_NAME');
        $data['last_name'] = \Config::get('variable.ASSISTANT_L_NAME');
        $data['gender'] = \Config::get('variable.ASSISTANT_GENDER');
        $data['university_name'] = \Config::get('variable.ASSISTANT_UNIVERSITY_NAME');
        $data['phone_no'] = \Config::get('variable.ASSISTANT_PHONE_NO');
        $data['unique_id'] = \Config::get('variable.ASSISTANT_UNIQUE_ID');
        $data['email'] = \Config::get('variable.ASSISTANT_EMAIL');
        $data['password'] = \Config::get('variable.ASSISTANT_PASSWORD');
        $data['confirm_password'] = \Config::get('variable.ASSISTANT_C_PASSWORD');
        $data['term_condition'] = \Config::get('variable.ASSISTANT_TERM_CONDITION');
        $data['privacy_policy'] = \Config::get('variable.ASSISTANT_PRIVACY_POLICY');

        $term_conditions = Page::where('meta_key', 'term')->latest()->first();
        $policy = Page::where('meta_key', 'privacy_policy')->latest()->first();

        return view('auth.assitant-user-signup', array('term' => $term_conditions, 'policy' => $policy, 'input_titles' => $data));
    }

    /*
     * Main Function for resend user verification link to user
     * @param Request $request (email)
     * @return type (status, success/error)
     */

    public function resendUserVerification($email, Request $request)
    {

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->verify_token = str_random(20);

            $user->save();

            //send verification mail to user
            //---------------------------------------------------------
            $data['verify_token'] = $user->verify_token;
            $data['name'] = $user->first_name . " " . $user->last_name;
            $data['email'] = $email;
            Mail::send('emails.confirm', $data, function ($message) use ($data) {
                $message->from('no-reply@Lifewill.com', "Lifewill");
                $message->subject("Welcome to Lifewill");
                $message->to($data['email']);
            });

            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', 'A verification link has been sent to your email. Please verify your account.');
            return Redirect::route('login');
        } else {
            $request->session()->flash('message.level', 'danger');
            $request->session()->flash('message.content', 'Your email id is not registered with us.');

            return Redirect::route('login');
        }
    }

}
