<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Subscription;
use App\Models\UserDeleteLog;
use App\User;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application show login method.
     *
     * @return \Illuminate\Http\Response
     */
    public function loginMethod()
    {
        return view('auth.login-method');
    }

    /**
     * Show the application normal login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showNormalLoginForm()
    {
        return view('auth.noramal-user-login');
    }

    /**
     * Show the application assitant login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAssistantLoginForm()
    {
        return view('auth.assitant-user-login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        //$this->validateLogin($request);
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        /** @var User $user */
        $user = Auth::once(['email' => $request->email, 'password' => $request->password]);

        if ($user) {
            $user = User::where(['email' => $request->email])->first();
            if ($user->status == 0) {

                $request->session()->flash('message.notverify', 'danger');
                $request->session()->flash('message.content', 'Please verify your account, check email for verification code.');
                return Redirect::back()->withInput($request->only($this->username(), 'remember'));
            }

            if ($user->status == 2) {

                $request->session()->flash('message.deactivate', 'danger');
                $request->session()->flash('message.content', 'Your Account is Deactivated by admin, please contact admin at ' . \Config::get('variable.ADMIN_EMAIL'));
                return Redirect::back()->withInput($request->only($this->username(), 'remember'));
            }
        }
        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request), $request->has('remember')
        );
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user()) ?: redirect()->intended($this->redirectPath());
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if (Auth::check()) {
            if ($user->role_id == 1) {
                return redirect('/admin');
            } else {
                $user_delete = UserDeleteLog::where('role_id', $user->role_id)->where('email', $user->email)->count();
                $user_subscription = Subscription::where('user_id', $user->id)->first();
                $user_last_login = User::where('id', $user->id)
                    ->update(['current_login' => time(), 'last_login' => $user->current_login]);
                $user_activity_login = ActivityLog::updateOrCreate(
                    ['user_id' => $user->id, 'meta_key' => 'last_login'],
                    ['user_id' => $user->id, 'meta_key' => 'last_login', 'meta_value' => $user->current_login, 'status' => 1, 'created_at' => time(), 'updated_at' => time()]
                );

                if ($user_subscription == null) {

                    if ($user->role_id == 2) {
                        if ($user_delete) {

                        } else {
                            $subscription_data = array('plan_id' => 0, 'user_id' => $user->id, 'subscription_id' => 'sub_trial', 'plan_amount' => 0, 'plan_type' => 'trial', 'plan_start_date' => time(), 'plan_end_date' => strtotime(date('Y-m-d', strtotime("+30 days"))), 'status' => 1, 'created_at' => time(), 'updated_at' => time());
                            $subscription_saved_data = Subscription::create($subscription_data);
                        }
                    }
                    return redirect('/home');
                } else {
                    return redirect('/home');
                }
            }
        }
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [$this->username() => trans('auth.failed')];

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect('/');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

}
