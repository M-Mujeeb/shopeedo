<?php

namespace App\Http\Controllers\Auth;

use Nexmo;
use Cookie;
use Session;
use App\Models\Cart;
use App\Models\User;
use Twilio\Rest\Client;

use App\Rules\Recaptcha;
use Illuminate\Validation\Rule;

use App\Models\Customer;
use App\OtpConfiguration;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Http\Controllers\OTPVerificationController;
use App\Notifications\EmailVerificationNotification;
use App\Mail\SecondEmailVerifyMailManager;
use Mail;

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
    protected $redirectTo = '/';

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
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'g-recaptcha-response' => [
                Rule::when(get_setting('google_recaptcha') == 1, ['required', new Recaptcha()], ['sometimes'])
            ]
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // dd('j');
        if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'verification_code' => rand(1000, 9999),
                'forget_password_time' => now(),

            ]);
            $array['view'] = 'emails.verification';
            $array['from'] = env('MAIL_FROM_ADDRESS');
            $array['subject'] = translate('Verify Your Account: OTP for Shopeedo Customer ');

            $array['content'] = '
                Welcome to Shopeedo! We are excited to have you join our community of customer. To complete your registration and start buying, please verify your email address using the One-Time Password (OTP) provided below<br><br>

                 <div style="text-align:center;">
                     <span style="font-size:35px !important; font-weight:700;letter-spacing: 16px">' . $user->verification_code . '</span>
                 </div>
                 <br><br>

                 <strong style="color:#7D9A40">For your security, do not share this code with anyone under any circumstances.</strong>
                 <br><br>
                 If you did not request this code, you can safely ignore this email.<br><br>
                 Need assistance? Visit our Help Center.<br><br>
                 Best regards,<br>
                 The Shopeedo Team
             ';


            Mail::to($user->email)->queue(new SecondEmailVerifyMailManager($array));
        }
        //  else {
        // if (addon_is_activated('otp_system')) {
        //     $user = User::create([
        //         'name' => $data['name'],
        //         'phone' => '+' . $data['country_code'] . $data['phone'],
        //         'password' => Hash::make($data['password']),
        //         'verification_code' => rand(100000, 999999)
        //     ]);

        //     $otpController = new OTPVerificationController;
        //     $otpController->send_code($user);
        // }
        // }

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

        if (Cookie::has('referral_code')) {
            $referral_code = Cookie::get('referral_code');
            $referred_by_user = User::where('referral_code', $referral_code)->first();
            if ($referred_by_user != null) {
                $user->referred_by = $referred_by_user->id;
                $user->save();
            }
        }



        return $user;
    }

    public function register(Request $request)
    {
        // dd($request->all());
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            if (User::where('email', $request->email)->first() != null) {
                flash(translate('Email or Phone already exists.'));
                return back();
            }
        } elseif (User::where('phone', '+' . $request->country_code . $request->phone)->first() != null) {
            flash(translate('Phone already exists.'));
            return back();
        }

        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        flash(translate('4 digit code has been sent on your email.'))->success();

        return view('auth.free.user_registeration_no_otp', ['userId' => $user->id]);




        // $this->guard()->login($user);

        // if ($user->email != null) {
        //     if (BusinessSetting::where('type', 'email_verification')->first()->value != 1) {
        //         $user->email_verified_at = date('Y-m-d H:m:s');
        //         $user->save();
        //         offerUserWelcomeCoupon();
        //         flash(translate('Registration successful.'))->success();
        //     } else {
        //         try {
        //             $user->sendEmailVerificationNotification();
        //             flash(translate('Registration successful. Please verify your email.'))->success();
        //         } catch (\Throwable $th) {
        //             $user->delete();
        //             flash(translate('Registration failed. Please try again later.'))->error();
        //         }
        //     }
        // }

        // return $this->registered($request, $user)
        //     ?: redirect($this->redirectPath());
    }

    protected function registered(Request $request, $user)
    {
        if ($user->email == null) {
            return redirect()->route('verification');
        } elseif (session('link') != null) {
            return redirect(session('link'));
        } else {
            return redirect()->route('home');
        }
    }
}
