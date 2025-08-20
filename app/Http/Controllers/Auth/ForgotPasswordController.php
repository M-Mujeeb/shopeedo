<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use App\Mail\SecondEmailVerifyMailManager;
use App\Utility\SmsUtility;
use Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

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
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            Session::flash('error', 'Account does not exist');
            return back();
        }

        $user->verification_code = rand(1000, 9999);
        $user->forget_password_time = now();

        $array['view'] = 'emails.verification';
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['subject'] = translate('Password Reset');
        $array['content'] = '
    <strong style="color:#7D9A40">Important: Keep this information private, even from shopeedo!</strong>
    <br>We received a request to reset your shopeedo password. If this was you, please enter the 4-digit code on the email verification page:<br><br>

    <div style="text-align:center;">
        <span style="font-size:35px !important; font-weight:700;letter-spacing: 16px">' . $user->verification_code . '</span>
    </div>
    <br><br>

    <strong style="color:#7D9A40">For your security, do not share this code with anyone under any circumstances.</strong>
    <br><br>
    If you did not request a password reset, you can safely ignore this email.<br><br>
    Need assistance? Visit our Help Center.<br><br>
    Best regards,<br>
    The shopeedo Team
';



        Mail::to($user->email)->queue(new SecondEmailVerifyMailManager($array));

        $user->save();

        flash(translate('Your OTP has been sent!'))->success();

        return view('auth.' . get_setting('authentication_layout_select') . '.reset_password', ['user_id' => $user->id]);

        // $phone = "+{$request['country_code']}{$request['phone']}";
        // if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
        //     $user = User::where('email', $request->email)->first();
        //     if ($user != null) {
        //         $user->verification_code = rand(100000,999999);
        //         $user->save();

        //         $array['view'] = 'emails.verification';
        //         $array['from'] = env('MAIL_FROM_ADDRESS');
        //         $array['subject'] = translate('Password Reset');
        //         $array['content'] = translate('Verification Code is').': '. $user->verification_code;

        //         Mail::to($user->email)->queue(new SecondEmailVerifyMailManager($array));

        //         return view('auth.'.get_setting('authentication_layout_select').'.reset_password');
        //     }
        //     else {
        //         flash(translate('No account exists with this email'))->error();
        //         return back();
        //     }
        // }
        // else{
        //     $user = User::where('phone', $phone)->first();
        //     if ($user != null) {
        //         $user->verification_code = rand(100000,999999);
        //         $user->save();
        //         SmsUtility::password_reset($user);
        //         return view('otp_systems.frontend.auth.'.get_setting('authentication_layout_select').'.reset_with_phone');
        //     }
        //     else {
        //         flash(translate('No account exists with this phone number'))->error();
        //         return back();
        //     }
        // }
    }
}