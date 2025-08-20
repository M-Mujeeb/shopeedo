<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\SmsHelper;


use Auth;
use Session;
use Storage;
use Socialite;
use Carbon\Carbon;
use App\Models\Cart;
use App\Models\User;
use GuzzleHttp\Client;
use App\Models\Customer;
use Illuminate\Support\Str;
use CoreComponentRepository;
use Illuminate\Http\Request;
use App\Services\SocialRevoke;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use GeneaLabs\LaravelSocialiter\Facades\Socialiter;
use App\Mail\SecondEmailVerifyMailManager;
use Illuminate\Support\Facades\Mail;
use Hash;

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
    /*protected $redirectTo = '/';*/


    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($provider)
    {
        if (request()->get('query') == 'mobile_app') {
            request()->session()->put('login_from', 'mobile_app');
        }
        if ($provider == 'apple') {
            return Socialite::driver("sign-in-with-apple")
                ->scopes(["name", "email"])
                ->redirect();
        }
        return Socialite::driver($provider)->redirect();
    }

    public function handleAppleCallback(Request $request)
    {
        try {
            $user = Socialite::driver("sign-in-with-apple")->user();
        } catch (\Exception $e) {
            flash(translate("Something Went wrong. Please try again."))->error();
            return redirect()->route('user.login');
        }
        //check if provider_id exist
        $existingUserByProviderId = User::where('provider_id', $user->id)->first();

        if ($existingUserByProviderId) {
            $existingUserByProviderId->access_token = $user->token;
            $existingUserByProviderId->refresh_token = $user->refreshToken;
            if (!isset($user->user['is_private_email'])) {
                $existingUserByProviderId->email = $user->email;
            }
            $existingUserByProviderId->save();
            //proceed to login
            auth()->login($existingUserByProviderId, true);
        } else {
            //check if email exist
            $existing_or_new_user = User::firstOrNew([
                'email' => $user->email
            ]);
            $existing_or_new_user->provider_id = $user->id;
            $existing_or_new_user->access_token = $user->token;
            $existing_or_new_user->refresh_token = $user->refreshToken;
            $existing_or_new_user->provider = 'apple';
            if (!$existing_or_new_user->exists) {
                $existing_or_new_user->name = 'Apple User';
                if ($user->name) {
                    $existing_or_new_user->name = $user->name;
                }
                $existing_or_new_user->email = $user->email;
                $existing_or_new_user->email_verified_at = date('Y-m-d H:m:s');
            }
            $existing_or_new_user->save();

            auth()->login($existing_or_new_user, true);
        }

        if (session('temp_user_id') != null) {
            Cart::where('user_id', auth()->user()->id)->delete(); // If previous data is available for this user, delete first
            Cart::where('temp_user_id', session('temp_user_id'))
                ->update([
                    'user_id' => auth()->user()->id,
                    'temp_user_id' => null
                ]);

            Session::forget('temp_user_id');
        }

        if (session('link') != null) {
            return redirect(session('link'));
        } else {
            if (auth()->user()->user_type == 'seller') {
                return redirect()->route('seller.dashboard');
            }
            return redirect()->route('dashboard');
        }
    }
    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback(Request $request, $provider)
    {

        if (session('login_from') == 'mobile_app') {
            return $this->mobileHandleProviderCallback($request, $provider);
        }
        try {
            if ($provider == 'twitter') {
                $user = Socialite::driver('twitter')->user();
            } else {
                $user = Socialite::driver($provider)->stateless()->user();
            }
        } catch (\Exception $e) {
            flash(translate("Something Went wrong. Please try again."))->error();
            return redirect()->route('user.login');
        }

        //check if provider_id exist
        $existingUserByProviderId = User::where('provider_id', $user->id)->first();

        if ($existingUserByProviderId) {
            $existingUserByProviderId->access_token = $user->token;
            $existingUserByProviderId->save();
            //proceed to login


            auth()->login($existingUserByProviderId, true);
        } else {
            //check if email exist
            $existingUser = User::where('email', '!=', null)->where('email', $user->email)->first();

            if ($existingUser) {
                //update provider_id
                $existing_User = $existingUser;
                $existing_User->provider_id = $user->id;
                $existing_User->provider = $provider;
                $existing_User->access_token = $user->token;
                $existing_User->save();

                //proceed to login
                // auth()->login($existing_User, true);
            } else {
                //create a new user
                $newUser = new User;
                $newUser->name = $user->name;
                $newUser->email = $user->email;
                $newUser->email_verified_at = date('Y-m-d Hms');
                $newUser->provider_id = $user->id;
                $newUser->provider = $provider;
                $newUser->access_token = $user->token;
                $newUser->save();
                //proceed to login

                auth()->login($newUser, true);
            }
        }

        if (session('temp_user_id') != null) {
            // Deleting cart data if the user has already cart data.
            Cart::where('user_id', auth()->user()->id)->delete();

            Cart::where('temp_user_id', session('temp_user_id'))
                ->update([
                    'user_id' => auth()->user()->id,
                    'temp_user_id' => null
                ]);

            Session::forget('temp_user_id');
        }

        if (session('link') != null) {
            return redirect(session('link'));
        } else {
            if (auth()->user()->user_type == 'seller') {
                return redirect()->route('seller.dashboard');
            }
            return redirect()->route('dashboard');
        }
    }

    public function mobileHandleProviderCallback($request, $provider)
    {
        $return_provider = '';
        $result = false;
        if ($provider) {
            $return_provider = $provider;
            $result = true;
        }
        return response()->json([
            'result' => $result,
            'provider' => $return_provider
        ]);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        // dd($request);
        $request->validate([
            'email'    => 'required_without:phone',
            'phone'    => 'required_without:email',
            'password' => 'required|string',
        ]);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request): array
    {
        // dd($request);

        if ($request->get('phone') != null) {
            return ['phone' => "{$request['phone']}", 'password' => $request->get('password')];
        } elseif ($request->get('email') != null) {

            return $request->only($this->username(), 'password');
        }
    }


    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $email = $request->email;
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->credentials($request);

        // dd($request);
        // return $request->email;

        // $phone = User::where('phone', $request->phone)->first();
        // if ($phone == null) {
        //     return $this->sendFailedLoginResponse($request);
        // }

        // // Check if user exists and email is verified
        // if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        //     flash('Invalid email address.');
        //     return back();
        //     // return response()->json(['error' => 'Invalid email address.'], 400);
        // }

        $user = User::where('email', $email)->first();
        // dd($user);
        if ($user != null && $user->user_type == 'customer' && $request->urlrequest == 'seller') {
            flash('Invalid Credentials for Seller');
            return back();
        } elseif ($user != null && $user->user_type == 'seller' && $request->urlrequest == 'customer') {
            flash('Invalid Credentials for Customer');
            return back();
        } else {

            if ($user != null && $user->email_verified_at == null) {

                $user->verification_code = rand(1000, 9999);
                $user->forget_password_time = now();
                $user->save();
                $array['view'] = 'emails.verification';
                $array['from'] = env('MAIL_FROM_ADDRESS');
                $array['subject'] = translate('Seller Account Verification');
                $array['content'] = translate('Verification Code is') . ': ' . $user->verification_code;
                Mail::to($user->email)->queue(new SecondEmailVerifyMailManager($array));
                flash(translate('Your OTP has been sent!'))->success();
                return view('auth.free.verify_login_unverified_email', ['userId' => $user->id]);
            }
            // dd($request->all());
            if ($this->attemptLogin($request)) {
                if ($request->hasSession()) {
                    $request->session()->put('auth.password_confirmed_at', time());
                }

                return $this->sendLoginResponse($request);
            }

            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            $this->incrementLoginAttempts($request);

            return $this->sendFailedLoginResponse($request);
        }
    }


    /**
     * Check user's role and redirect user based on their role
     * @return
     */
    public function authenticated()
    {

        // dd(auth()->check());


        if (session('temp_user_id') != null) {
            if (auth()->user()->user_type == 'customer') {
                Cart::where('temp_user_id', session('temp_user_id'))
                    ->update(
                        [
                            'user_id' => auth()->user()->id,
                            'temp_user_id' => null
                        ]
                    );
            } else {
                Cart::where('temp_user_id', session('temp_user_id'))->delete();
            }
            Session::forget('temp_user_id');
        }

        if (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff') {
            CoreComponentRepository::instantiateShopRepository();
            return redirect()->route('admin.dashboard');
        } elseif (auth()->user()->user_type == 'seller') {

            return redirect()->route('seller.dashboard');
        } else {

            if (session('link') != null) {
                return redirect(session('link'));
            } else {
                return redirect()->route('home');
                // below is commit due to rabel
                // return redirect()->route('dashboard');
            }
        }
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        flash(translate('Invalid login credentials'))->error();
        return back();
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        if (auth()->user() != null && (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff')) {
            $redirect_route = 'login';
        } else {
            $redirect_route = 'home';
        }

        //User's Cart Delete
        // if (auth()->user()) {
        //     Cart::where('user_id', auth()->user()->id)->delete();
        // }

        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect()->route($redirect_route);
    }

    public function account_deletion(Request $request)
    {

        $redirect_route = 'home';

        if (auth()->user()) {
            Cart::where('user_id', auth()->user()->id)->delete();
        }

        // if (auth()->user()->provider) {
        //     $social_revoke =  new SocialRevoke;
        //     $revoke_output = $social_revoke->apply(auth()->user()->provider);

        //     if ($revoke_output) {
        //     }
        // }

        $auth_user = auth()->user();

        // user images delete from database and file storage
        $uploads = $auth_user->uploads;
        if ($uploads) {
            foreach ($uploads as $upload) {
                if (env('FILESYSTEM_DRIVER') == 's3') {
                    Storage::disk('s3')->delete($upload->file_name);
                    if (file_exists(public_path() . '/' . $upload->file_name)) {
                        unlink(public_path() . '/' . $upload->file_name);
                        $upload->delete();
                    }
                } else {
                    unlink(public_path() . '/' . $upload->file_name);
                    $upload->delete();
                }
            }
        }

        $auth_user->customer_products()->delete();

        User::destroy(auth()->user()->id);

        auth()->guard()->logout();
        $request->session()->invalidate();

        flash(translate("Your account deletion successfully done."))->success();
        return redirect()->route($redirect_route);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'account_deletion']);
    }

    public function handle_demo_login()
    {
        return view('frontend.handle_demo_login');
    }

    public function checkMobileOtpStatus(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:11',
            'password' => 'required',
        ]);
        $user = User::where('phone', $request['phone'])->first();

        // dd(Hash::check($user->password, $request->password));
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                if ($user->first_login == 0) {
                    Auth()->login($user);
                    return redirect()->route('seller.dashboard');
                } else {
                    return redirect()->route('sendOtp')->with('phone', $request->phone);
                }
            } else {
                flash(translate('invalid credentials'))->error();
                // ->with('error', 'invalid credentials')
                return redirect()->back();
            }
        } else {
            flash(translate('user not found!'))->error();
            return redirect()->back()->with('error', 'user not found');
        }
    }
    // public function sendOtp(Request $request)
    // {
    //     $phone = session('phone');

    //     // $phone = "+{$request->input('country_code')}{$request->input('phone')}";
    //     $userId = 0;

    //     // dd($phone);

    //     $user = User::where('phone', $phone)->first();
    //     // if ($user->is_phone_verified == 1) {
    //     //     return Http::withoutVerifying()->withHeaders([
    //     //         'X-CSRF-TOKEN' => csrf_token(),
    //     //     ])->post(route('login'), [
    //     //         'phone' => $request->phone,
    //     //         'password' => $request->password,
    //     //     ]);
    //     // }

    //     // if ($user->is_phone_verified == 1) {
    //     //     return redirect()->route('login')->withInput($request->only($request->phone, $request->password));
    //     // }
    //     // dd($user);
    //     if ($user) {
    //         $userId = $user['id'];
    //         $user->verification_code = rand(1000, 9999);
    //         $user->forget_password_time = now();
    //         $user->save();
    //         session(['phone' => $phone]);

    //         flash(translate('Your OTP has been sent!'))->success();


    //         return view('auth.free.seller_login_no_otp', ['userId' => $userId]);
    //     } else {
    //         flash(translate('Account does not exist'))->error();
    //         return back();
    //     }
    // }


    public function sendOtp(Request $request)
    {
        $phone = session('phone');
        $userId = 0;
        $user = User::where('phone', $phone)->first();

        if ($user) {
            $userId = $user['id'];
            $otp = rand(1000, 9999);
            $user->verification_code = $otp;
            $user->forget_password_time = now();
            $user->save();
            session(['phone' => $phone]);

            // Send OTP via SMS
            $message = "Your OTP is: " . $otp;
            $smsResult = SmsHelper::sendSms($phone, $message);

            if ($smsResult) {
                flash(translate('Your OTP has been sent!'))->success();
            } else {
                flash(translate('Failed to send OTP. Please try again.'))->error();
            }

            return view('auth.free.seller_login_no_otp', ['userId' => $userId]);
        } else {
            flash(translate('Account does not exist'))->error();
            return back();
        }
    }
    public function verifyOtp(Request $request)
    {
        $otp_code = $request->otp_1 . $request->otp_2 . $request->otp_3 . $request->otp_4;
        $user = User::where('id', $request->user_id)
            ->where('verification_code', $otp_code)
            ->first();

        if ($user) {
            $currentDateTime = now();
            $forgetPasswordTime = Carbon::parse($user->forget_password_time);

            $secondsDifference = $forgetPasswordTime->diffInSeconds($currentDateTime);
            if ($secondsDifference < 120) {
                auth()->login($user);
                auth()->user()->first_login = 0;
                auth()->user()->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'OTP verified successfully'
                ]);
            } else {
                return response()->json([
                    'status' => 'time',
                    'message' => 'Please request a new code'
                ]);
            }
        } else {
            return response()->json([
                'status' => 'mismatch',
                'message' => 'Invalid OTP'
            ]);
        }
    }
}
