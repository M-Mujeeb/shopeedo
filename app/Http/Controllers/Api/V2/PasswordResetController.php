<?php

namespace App\Http\Controllers\Api\V2;

use App\Notifications\AppEmailVerificationNotification;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordReset;
use App\Notifications\PasswordResetRequest;
use Illuminate\Support\Str;
use App\Http\Controllers\OTPVerificationController;
use App\Services\MailjetAuthMailer;
use Hash;

class PasswordResetController extends Controller
{
    public function forgetRequest(Request $request)
    {
        if ($request->send_code_by == 'email') {
            $user = User::where('email', $request->email_or_phone)->first();
        } else {
            $user = User::where('phone', $request->email_or_phone)->first();
        }


        if (!$user) {
            return response()->json([
                'result' => false,
                'message' => translate('User not found')
            ], 404);
        }

        if ($user) {
            $user->verification_code = rand(1000, 9999);
            $user->forget_password_time = now();
            $user->save();
            if ($request->send_code_by == 'phone') {
                $otpController = new OTPVerificationController();
                $otpController->send_code($user);
            } else {
               try {
        $mailjet = new MailjetAuthMailer();

        $templateId = match ($user->user_type) {
            'customer' => env('MAILJET_TEMPLATE_CUSTOMER'),
            'seller' => env('MAILJET_TEMPLATE_SELLER'),
            'delivery_boy' => env('MAILJET_TEMPLATE_DELIVERY_BOY'),
            default => null
        };


        $name = $user->name ?? ucfirst($user->user_type);
        $otp = $user->verification_code;

        $array = [
            'to' => $user->email,
            'subject' => "Forgot Password OTP for Shopeedo " . ucfirst($user->user_type),
            'template_id' => $templateId,
            'variables' => [
                'customer_name' => $name,
                'seller_name' => $name,
                'delivery_name' => $name,
                'otp_code' => $otp
            ],
            'view' => 'emails.verification',
            'content' => "
                You requested a new OTP code to Forgot your Shopeedo account as a $user->user_type.<br><br>

                <div style='text-align:center;'>
                    <span style='font-size:35px !important; font-weight:700;letter-spacing: 16px'>$otp</span>
                </div>
                <br><br>
                <strong style='color:#7D9A40'>Please do not share this code with anyone for your security.</strong><br><br>
                If you didn’t request this, you may safely ignore it.<br><br>
                Best regards,<br>
                The Shopeedo Team
            "
        ];

        $response = $mailjet->send($array);

        if (!$response->success()) {
            \Log::error('Mailjet failed (resend): ' . $response->getReasonPhrase());
            return response()->json([
                'result' => false,
                'message' => translate('Failed to resend verification email.')
            ]);
        }

    } catch (\Exception $e) {
        \Log::error('Mailjet resendCode exception: ' . $e->getMessage());
        return response()->json([
            'result' => false,
            'message' => translate('Failed to resend verification email.')
        ]);
    }
            }
        }

        return response()->json([
            'result' => true,
            'message' => translate('A code is sent'),
            'user_id'=>$user->id
        ], 200);
    }

    public function confirmReset(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();

        if ($user != null) {
            $user->verification_code = null;
            $user->password = Hash::make($request->new_password);
            $user->save();
            return response()->json([
                'result' => true,
                'message' => translate('Your password is reset. Please login'),
            ], 200);
        } else {
            return response()->json([
                'result' => false,
                'message' => translate('User not found'),
            ], 200);
        }
    }

    public function resendCode(Request $request)
    {

        if ($request->verify_by == 'email') {
            $user = User::where('email', $request->email_or_phone)->first();
        } else {
            $user = User::where('phone', $request->email_or_phone)->first();
        }


        if (!$user) {
            return response()->json([
                'result' => false,
                'message' => translate('User is not found')
            ], 404);
        }

        $user->verification_code = rand(100000, 999999);
        $user->save();

        if ($request->verify_by == 'email') {
            $user->notify(new AppEmailVerificationNotification());
        } else {
            $otpController = new OTPVerificationController();
            $otpController->send_code($user);
        }



        return response()->json([
            'result' => true,
            'message' => translate('A code is sent again'),
        ], 200);
    }
}
