<?php

/** @noinspection PhpUndefinedClassInspection */

namespace App\Http\Controllers\Api\V2;

use Hash;
use Mail;
use Socialite;
use App\Models\Cart;
use App\Models\Shop;
use App\Models\User;
use App\Models\Address;
use App\Rules\Recaptcha;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Validator;
use App\Mail\SecondEmailVerifyMailManager;
use App\Mail\GuestAccountOpeningMailManager;
use App\Http\Controllers\OTPVerificationController;
use App\Notifications\AppEmailVerificationNotification;
use Illuminate\Support\Str;
use App\Models\DeliveryBoy;
use App\Services\MailjetAuthMailer;

class AuthController extends Controller
{
    // public function signup(Request $request)
    // {

    //     $messages = array(
    //         'name.required' => translate('Name is required'),
    //         'email_or_phone.required' => $request->register_by == 'email' ? translate('Email is required') : translate('Phone is required'),
    //         'email_or_phone.email' => translate('Email must be a valid email address'),
    //         'email_or_phone.numeric' => translate('Phone must be a number.'),
    //         'email_or_phone.unique' => $request->register_by == 'email' ? translate('The email has already been taken') : translate('The phone has already been taken'),
    //         'password.required' => translate('Password is required'),
    //         'password.confirmed' => translate('Password confirmation does not match'),
    //         'password.min' => translate('Minimum 6 digits required for password')
    //     );

    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required',
    //         'password' => 'required|min:6|confirmed',
    //         'email_or_phone' => [
    //             'required',
    //             Rule::when($request->register_by === 'email', ['email', 'unique:users,email']),
    //             Rule::when($request->register_by === 'phone', ['numeric', 'unique:users,phone']),
    //         ],
    //         'g-recaptcha-response' => [
    //             Rule::when(get_setting('google_recaptcha') == 1, ['required', new Recaptcha()], ['sometimes'])
    //         ]
    //     ], $messages);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'result' => false,
    //             'message' => $validator->errors()->all()
    //         ]);
    //     }

    //     $user = new User();
    //     $user->name = $request->name;
    //     if ($request->register_by == 'email') {

    //         $user->email = $request->email_or_phone;
    //     }
    //     if ($request->register_by == 'phone') {
    //         $user->phone = $request->email_or_phone;
    //     }
    //     $user->password = bcrypt($request->password);
    //     $user->verification_code = rand(100000, 999999);
    //     $user->save();


    //     $user->email_verified_at = null;
    //     if ($user->email != null) {
    //         if (BusinessSetting::where('type', 'email_verification')->first()->value != 1) {
    //             $user->email_verified_at = date('Y-m-d H:m:s');
    //         }
    //     }

    //     if ($user->email_verified_at == null) {
    //         if ($request->register_by == 'email') {
    //             try {
    //                 $user->notify(new AppEmailVerificationNotification());
    //             } catch (\Exception $e) {
    //             }
    //         } else {
    //             $otpController = new OTPVerificationController();
    //             $otpController->send_code($user);
    //         }
    //     }

    //     $user->save();
    //     //create token
    //     $user->createToken('tokens')->plainTextToken;

    //     $tempUserId = $request->has('temp_user_id') ? $request->temp_user_id : null;
    //     return $this->loginSuccess($user, '', $tempUserId);
    // }

  public function signup(Request $request)
{
    $existingUser = User::where('email', $request->email)->first();
    $existingPhoneUser = User::where('phone', $request->phone)->first();

    // If email exists and is already verified, block
    if ($existingUser && $existingUser->email_verified_at) {
        return response()->json([
            'result' => false,
            'user_id' => 0,
            'message' => [translate('The email has already been taken')]
        ]);
    }

    // If phone exists and is already verified, block
    if ($existingPhoneUser && $existingPhoneUser->email_verified_at) {
        return response()->json([
            'result' => false,
            'user_id' => 0,
            'message' => [translate('The phone has already been taken')]
        ]);
    }

    $emailRule = $existingUser && !$existingUser->email_verified_at
    ? ['email']
    : ['email', 'unique:users,email'];

   $phoneRule = $existingPhoneUser && !$existingPhoneUser->email_verified_at
    ? ['numeric']
    : ['numeric', 'unique:users,phone'];

    $messages = [
        'email.required' => translate('Email is required'),
        'phone.required' => translate('Phone is required'),
        'user_type.required' => translate('User type is required'),
        'email.email' => translate('Email must be a valid email address'),
        'phone.numeric' => translate('Phone must be a number.'),
        'email.unique' => translate('The email has already been taken'),
        'phone.unique' => translate('The phone has already been taken'),
        'password.required' => translate('Password is required'),
        'password.min' => translate('Minimum 6 digits required for password')
    ];

    $validator = Validator::make($request->all(), [
    'password' => 'required|min:6',
    'email' => array_merge(['required'], $emailRule),
    'phone' => array_merge(['required'], $phoneRule),
    'user_type' => 'required',
    'g-recaptcha-response' => Rule::when(get_setting('google_recaptcha') == 1, ['required', new Recaptcha()], ['sometimes'])
], $messages);


    if ($validator->fails()) {
        return response()->json([
            'result' => false,
            'message' => $validator->errors()->all()
        ]);
    }

    // Use existing unverified user or create new
    if ($existingUser && !$existingUser->email_verified_at) {
        $user = $existingUser;
    } elseif ($existingPhoneUser && !$existingPhoneUser->email_verified_at) {
        $user = $existingPhoneUser;
    } else {
        $user = new User();
    }

    $user->name = $request->name ?? '';
    $user->email = $request->email;
    $user->user_type = $request->user_type;
    $user->phone = $request->phone;
    $user->password = bcrypt($request->password);
    $user->verification_code = rand(1000, 9999);
    $user->forget_password_time = now();
    $user->email_verified_at = null;
    $user->save();

    // Create Shop if seller and no existing shop
    if ($request->user_type == 'seller') {
        $shopExists = Shop::where('user_id', $user->id)->exists();
        if (!$shopExists) {
            $shop = new Shop();
            $shop->user_id = $user->id;
            $shop->delivery_pickup_latitude = 31.582045;
            $shop->delivery_pickup_longitude = 74.329376;
            $shop->slug = preg_replace('/\s+/', '-', str_replace("/", " ", $user->id));
            $shop->save();
        }
    }

    // Send OTP Email via Mailjet
    try {
        $mailjet = new MailjetAuthMailer();

        $templateId = match ($request->user_type) {
            'customer' => env('MAILJET_TEMPLATE_CUSTOMER'),
            'seller' => env('MAILJET_TEMPLATE_SELLER'),
            'delivery_boy' => env('MAILJET_TEMPLATE_DELIVERY_BOY'),
            default => null
        };
        $name = $user->name ?? ucfirst($request->user_type);
        $otp = $user->verification_code;

        $array = [
            'to' => $user->email,
            'subject' => "Verify Your Account: OTP for Shopeedo " . ucfirst($request->user_type),
            'template_id' => $templateId,
            'variables' => [
                'customer_name' => $name,
                'seller_name' => $name,
                'delivery_name' => $name,
                'otp_code' => $otp
            ],
            'view' => 'emails.verification',
            'content' => "
                Welcome to Shopeedo! We are excited to have you join our community of $request->user_type. To complete your registration and start using the platform, please verify your email address using the OTP below:<br><br>

                <div style='text-align:center;'>
                    <span style='font-size:35px !important; font-weight:700;letter-spacing: 16px'>$otp</span>
                </div>
                <br><br>
                <strong style='color:#7D9A40'>For your security, do not share this code with anyone.</strong><br><br>
                If you did not request this code, you can safely ignore this email.<br><br>
                Best regards,<br>The Shopeedo Team
            "
        ];

        $response = $mailjet->send($array);

        if (!$response->success()) {
            \Log::error('Mailjet failed: ' . $response->getReasonPhrase());
            return response()->json([
                'result' => false,
                'message' => translate('Failed to send verification email.')
            ]);
        }

    } catch (\Exception $e) {
        \Log::error('Mailjet exception: ' . $e->getMessage());
        return response()->json([
            'result' => false,
            'message' => translate('Failed to send verification email.')
        ]);
    }

    return response()->json([
        'result' => true,
        'message' => translate('The OTP has been sent to your email.'),
        'user_id' => $user->id,
        'user_type' => $user->user_type
    ]);
}


   public function resendCode(Request $request)
{
    if (!$request->user_id) {
        return response()->json([
            'result' => false,
            'message' => translate('User not found'),
        ], 200);
    }

    $user = User::find($request->user_id);

    if (!$user) {
        return response()->json([
            'result' => false,
            'message' => translate('User not found'),
        ], 200);
    }

    $user->verification_code = rand(1000, 9999);
    $user->forget_password_time = now();
    $user->save();

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
            'subject' => "Resend OTP for Shopeedo " . ucfirst($user->user_type),
            'template_id' => $templateId,
            'variables' => [
                'customer_name' => $name,
                'seller_name' => $name,
                'delivery_name' => $name,
                'otp_code' => $otp
            ],
            'view' => 'emails.verification',
            'content' => "
                You requested a new OTP code to verify your Shopeedo account as a $user->user_type.<br><br>

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

    return response()->json([
        'result' => true,
        'message' => translate('Verification code is sent again'),
    ], 200);
}

    //     public function q    (Request $request)
    // {
    //     $user = User::where('id', $request->user_id)->first();
    //     $shopInfo = Shop::where('user_id', $user->id)->first();

    //     if ($request->verify_by == "phone") {
    //         if ($user->verification_code == $request->verification_code) {
    //             $user->first_login = 0;
    //             $user->save();
    //             return $this->loginSuccess($user, '', null);
    //         } else {
    //             return response()->json([
    //                 'result' => false,
    //                 'message' => translate('Invalid OTP Code '),
    //             ], 200);
    //         }
    //     } else { // Email verification scenario
    //         if ($user->verification_code == $request->verification_code) {
    //             $user->email_verified_at = now();
    //             $user->verification_code = null;
    //             $user->save();

    //             if ($request->verify_from == "signup") {

    //                 $array['view'] = 'emails.verification';
    //                 $array['from'] = env('MAIL_FROM_ADDRESS');
    //                 $array['subject'] = translate('Welcome to Shopeedo - Your Seller Account is Successfully Created');
    //                 $array['content'] = "
    //                     Dear {$user->username},

    //                     Congratulations and welcome to Shopeedo!

    //                     [Welcome message content]

    //                     Best regards,
    //                     The Shopeedo Team
    //                 ";

    //                 Mail::to($user->email)->queue(new SecondEmailVerifyMailManager($array));
    //             }

    //             return $this->loginSuccess($user, '', null, $shopInfo);
    //         } else {
    //             return response()->json([
    //                 'result' => false,
    //                 'message' => translate('Code does not match, you can request for resending the code'),
    //             ], 200);
    //         }
    //     }
    // }


   public function confirmCode(Request $request)
{
    $user = User::find($request->user_id);
    if (!$user) {
        return response()->json([
            'result' => false,
            'message' => translate('User not found'),
        ], 200);
    }

    $shopInfo = Shop::where('user_id', $user->id)->first();
    $otpExpirationTime = 2 * 60; // 2 minutes

    // Check OTP expiration
    if (now()->diffInSeconds($user->forget_password_time) > $otpExpirationTime) {
        return response()->json([
            'result' => false,
            'message' => translate('OTP is expired, please request again.'),
        ], 200);
    }

    // Phone verification
    if ($request->verify_by === "phone") {
        if ($user->verification_code == $request->verification_code) {
            $user->first_login = 0;
            $user->save();
            return $this->loginSuccess($user, '', null);
        } else {
            return response()->json([
                'result' => false,
                'message' => translate('Invalid OTP Code'),
            ], 200);
        }
    }

    // Email verification
    if ($user->verification_code == $request->verification_code) {
        $user->email_verified_at = now();
        $user->verification_code = null;
        $user->save();

        if ($request->verify_from == "signup") {
            try {
                $mailjet = new MailjetAuthMailer();
                 $templateId = match ($user->user_type) {
                    'customer' => env('MAILJET_TEMPLATE_CUSTOMER_WELLCOME'),
                    'seller' => env('MAILJET_TEMPLATE_SELLER_WELLCOME'),
                    'delivery_boy' => env('MAILJET_TEMPLATE_DELIVERY_BOY_WELLCOME'),
                    default => null
                };

                $array = [
                    'to' => $user->email,
                    'subject' => translate('Welcome to the No. 1 platform Shopeedo! Your Account is Now Registered'),
                    'template_id' => $templateId, 
                    'view' => 'emails.verification',
                     'variables' => [
                'email' => $user->email,
            ],
                    'content' => '
                        <h4>A New Adventure Begins</h4>
                        <h5 style="font-style: italic">YOU ARE ONE STEP AWAY FROM BUYING OR SELLING</h5>
                        <p>Here’s a sneak peek of what awaits you:</p>
                        <ul>
                            <li><strong>Exclusive Deals:</strong> Get access to members-only promotions and discounts.</li>
                            <li><strong>Swift Checkout:</strong> Save your details for a seamless shopping experience.</li>
                            <li><strong>Order Updates:</strong> Stay in the loop with real-time tracking and notifications.</li>
                        </ul>
                        <h4>Need Assistance:</h4>
                        <p>Our support team is here for you from 09:00 AM to 06:00 PM, every day of the week.</p>
                        <h4>Contact Us</h4>
                        <ul>
                            <li>Phone: +971 58 5567542</li>
                            <li>Email: info@shopeedo.com</li>
                        </ul>
                        <p>You’re receiving this email because you (or someone else) confirmed this email address for a Shopeedo account. If you didn’t do this, please ignore this email.</p>
                        <hr>
                    '
                ];

                $response = $mailjet->send($array);

                if (!$response->success()) {
                    \Log::error('Mailjet failed (welcome email): ' . $response->getReasonPhrase());
                }

            } catch (\Exception $e) {
                \Log::error('Mailjet exception (welcome email): ' . $e->getMessage());
            }
        }

        return $this->loginSuccess($user, '', null, $shopInfo);
    } else {
        return response()->json([
            'result' => false,
            'message' => translate('Invalid OTP'),
        ], 200);
    }
}

   public function login(Request $request)
{
    $messages = [
        'email.required' => $request->login_by == 'email' ? translate('Email is required') : translate('Phone is required'),
        'email.email' => translate('Email must be a valid email address'),
        'email.numeric' => translate('Phone must be a number.'),
        'password.required' => translate('Password is required'),
    ];

    $validator = Validator::make($request->all(), [
        'password' => 'required',
        'login_by' => 'required',
        'email' => [
            'required',
            Rule::when($request->login_by === 'email', ['email']),
            Rule::when($request->login_by === 'phone', ['numeric']),
        ],
    ], $messages);

    if ($validator->fails()) {
        return response()->json([
            'result' => false,
            'message' => $validator->errors()->all()
        ]);
    }

    $req_email = $request->email;
    $device_token = $request->device_token;
    $userType = $request->user_type ?? 'customer';

    $user = User::where('user_type', $userType)
        ->where(function ($q) use ($req_email) {
            $q->where('email', $req_email)->orWhere('phone', $req_email);
        })->first();

    if (!$user) {
        return response()->json(['result' => false, 'message' => translate('User not found'), 'user' => null], 401);
    }

    if ($device_token) {
        $user->device_token = $device_token;
        $user->save();
    }

    $shopInfo = Shop::where('user_id', $user->id)->first();

    if ($user->banned) {
        return response()->json(['result' => false, 'message' => translate('User is banned'), 'user' => null], 401);
    }

    if (!Hash::check($request->password, $user->password)) {
        return response()->json(['result' => false, 'message' => translate('Invalid Credentials'), 'user' => null], 401);
    }

    // Email verification
    if ($request->login_by === 'email') {
        if ($user->email_verified_at !== null) {
            return $this->loginSuccess($user, '', null, $shopInfo);
        }

        // Send OTP via email
        $user->verification_code = rand(1000, 9999);
        $user->forget_password_time = now();
        $user->save();

        try {
            $mailjet = new MailjetAuthMailer();
            $templateId = match ($user->user_type) {
                'customer' => env('MAILJET_TEMPLATE_CUSTOMER'),
                'seller' => env('MAILJET_TEMPLATE_SELLER'),
                'delivery_boy' => env('MAILJET_TEMPLATE_DELIVERY_BOY'),
                default => null
            };

            $array = [
                'to' => $user->email,
                'subject' => translate('Verify Your Shopeedo Account'),
                'template_id' => $templateId,
                'variables' => [
                    'customer_name' => $user->name,
                    'seller_name' => $user->name,
                    'delivery_name' => $user->name,
                    'otp_code' => $user->verification_code
                ],
                'view' => 'emails.verification',
                'content' => "
                    Welcome to Shopeedo! Please verify your account using the OTP below:<br><br>
                    <div style='text-align:center;'>
                        <span style='font-size:35px !important; font-weight:700;letter-spacing: 16px'>{$user->verification_code}</span>
                    </div><br><br>
                    <strong style='color:#7D9A40'>Do not share this code with anyone.</strong><br><br>
                    If you didn't request this, ignore it.
                "
            ];

            $response = $mailjet->send($array);

            if (!$response->success()) {
                \Log::error('Mailjet failed during login: ' . $response->getReasonPhrase());
                return response()->json(['result' => false, 'message' => translate('Failed to send verification email.')]);
            }

        } catch (\Exception $e) {
            Log::error('Email send exception (login): ' . $e->getMessage());
            return response()->json(['result' => false, 'message' => translate('Failed to send verification email.')]);
        }

        return response()->json([
            'result' => false,
            'message' => translate('Your account is not verified. Please check your email for OTP code'),
            'user_id' => $user->id,
            'user' => $user
        ], 403);
    }

    // Phone login & first time
    if ($user->first_login === 1) {
        $user->verification_code = rand(1000, 9999);
        $user->forget_password_time = now();
        $user->save();

        try {
            $mailjet = new MailjetAuthMailer();
            $templateId = match ($user->user_type) {
                'customer' => env('MAILJET_TEMPLATE_CUSTOMER'),
                'seller' => env('MAILJET_TEMPLATE_SELLER'),
                'delivery_boy' => env('MAILJET_TEMPLATE_DELIVERY_BOY'),
                default => null
            };

            $array = [
                'to' => $user->email,
                'subject' => translate('Verify Your Shopeedo Account'),
                'template_id' => $templateId,
                'variables' => [
                    'customer_name' => $user->name,
                    'seller_name' => $user->name,
                    'delivery_name' => $user->name,
                    'otp_code' => $user->verification_code
                ],
                'view' => 'emails.verification',
                'content' => "
                    Please verify your account using this OTP:<br><br>
                    <div style='text-align:center;'>
                        <span style='font-size:35px !important; font-weight:700;letter-spacing: 16px'>{$user->verification_code}</span>
                    </div><br><br>
                    This step is required for your first login.
                "
            ];

            $response = $mailjet->send($array);

            if (!$response->success()) {
                \Log::error('Mailjet failed (phone login first time): ' . $response->getReasonPhrase());
                return response()->json(['result' => false, 'message' => translate('Failed to send verification OTP.')]);
            }

        } catch (\Exception $e) {
            Log::error('OTP send exception (phone login): ' . $e->getMessage());
            return response()->json(['result' => false, 'message' => translate('Failed to send verification OTP.')]);
        }

        return response()->json([
            'result' => true,
            'message' => translate('OTP is sent to your mobile number. Please verify to continue'),
            'user' => [
                'id' => $user->id,
                'type' => $user->user_type,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'avatar_original' => uploaded_asset($user->avatar_original),
                'phone' => $user->phone,
                'email_verified' => $user->email_verified_at != null,
                'first_login_phone' => $user->first_login
            ]
        ]);
    }

    // Normal login
    return $this->loginSuccess($user, '', null);
}

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {

        $user = request()->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

        if($user->user_type == 'delivery_boy') {
            $delivery_boy = DeliveryBoy::where('user_id', $user->id)->first();
            $delivery_boy->status = 0;
            $delivery_boy->save();
        }

        return response()->json([
            'result' => true,
            'message' => translate('Successfully logged out')
        ]);
    }

    public function socialLogin(Request $request)
    {
        if (!$request->provider) {
            return response()->json([
                'result' => false,
                'message' => translate('User not found'),
                'user' => null
            ]);
        }

        switch ($request->social_provider) {
            case 'facebook':
                $social_user = Socialite::driver('facebook')->fields([
                    'name',
                    'first_name',
                    'last_name',
                    'email'
                ]);
                break;
            case 'google':
                $social_user = Socialite::driver('google')
                    ->scopes(['profile', 'email']);
                break;
            case 'twitter':
                $social_user = Socialite::driver('twitter');
                break;
            case 'apple':
                $social_user = Socialite::driver('sign-in-with-apple')
                    ->scopes(['name', 'email']);
                break;
            default:
                $social_user = null;
        }
        if ($social_user == null) {
            return response()->json(['result' => false, 'message' => translate('No social provider matches'), 'user' => null]);
        }

        if ($request->social_provider == 'twitter') {
            $social_user_details = $social_user->userFromTokenAndSecret($request->access_token, $request->secret_token);
        } else {
            $social_user_details = $social_user->userFromToken($request->access_token);
        }

        if ($social_user_details == null) {
            return response()->json(['result' => false, 'message' => translate('No social account matches'), 'user' => null]);
        }

        $existingUserByProviderId = User::where('provider_id', $request->provider)->first();

        if ($existingUserByProviderId) {
            $existingUserByProviderId->access_token = $social_user_details->token;
            if ($request->social_provider == 'apple') {
                $existingUserByProviderId->refresh_token = $social_user_details->refreshToken;
                if (!isset($social_user->user['is_private_email'])) {
                    $existingUserByProviderId->email = $social_user_details->email;
                }
            }
            $existingUserByProviderId->save();
            return $this->loginSuccess($existingUserByProviderId);
        } else {
            $existing_or_new_user = User::firstOrNew(
                [['email', '!=', null], 'email' => $social_user_details->email]
            );

            // $existing_or_new_user->user_type = 'customer';
            $existing_or_new_user->provider_id = $social_user_details->id;

            if (!$existing_or_new_user->exists) {
                if ($request->social_provider == 'apple') {
                    if ($request->name) {
                        $existing_or_new_user->name = $request->name;
                    } else {
                        $existing_or_new_user->name = 'Apple User';
                    }
                } else {
                    $existing_or_new_user->name = $social_user_details->name;
                }
                $existing_or_new_user->email = $social_user_details->email;
                $existing_or_new_user->email_verified_at = date('Y-m-d H:m:s');
            }

            $existing_or_new_user->save();

            return $this->loginSuccess($existing_or_new_user);
        }
    }

    // Guest user Account Create
    public function guestUserAccountCreate(Request $request)
    {
        $success = 1;
        $password = substr(hash('sha512', rand()), 0, 8);
        $isEmailVerificationEnabled = get_setting('email_verification');

        // User Create
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = addon_is_activated('otp_system') ? $request->phone : null;
        $user->password = Hash::make($password);
        $user->email_verified_at = $isEmailVerificationEnabled != 1 ? date('Y-m-d H:m:s') : null;
        $user->save();

        // Account Opening and verification(if activated) eamil send
        $array['email'] = $user->email;
        $array['password'] = $password;
        $array['subject'] = translate('Account Opening Email');
        $array['from'] = env('MAIL_FROM_ADDRESS');

        try {
            Mail::to($user->email)->queue(new GuestAccountOpeningMailManager($array));
            if ($isEmailVerificationEnabled == 1) {
                $user->notify(new AppEmailVerificationNotification());
            }
        } catch (\Exception $e) {
            $success = 0;
            $user->delete();
        }

        if ($success == 0) {
            return response()->json([
                'result' => false,
                'message' => translate('Something went wrong!')
            ]);
        }

        // User Address Create
        $address = new Address();
        $address->user_id = $user->id;
        $address->address = $request->address;
        $address->country_id = $request->country_id;
        $address->state_id = $request->state_id;
        $address->city_id = $request->city_id;
        $address->postal_code = $request->postal_code;
        $address->phone = $request->phone;
        $address->longitude = $request->longitude;
        $address->latitude = $request->latitude;
        $address->save();

        Cart::where('temp_user_id', $request->temp_user_id)
            ->update([
                'user_id' => $user->id,
                'temp_user_id' => null,
                'address_id' => $address->id
            ]);



        return $this->loginSuccess($user);
    }

    public function loginSuccess($user, $token = null, $tempUserId = null, $shopInfo = null)
    {

        if (!$token) {
            $token = $user->createToken('API Token')->plainTextToken;
        }

        if ($tempUserId != null) {
            Cart::where('temp_user_id', $tempUserId)
                ->update([
                    'user_id' => $user->id,
                    'temp_user_id' => null
                ]);
        }

        return response()->json([
            'result' => true,
            'message' => translate('Successfully logged in'),
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => null,
            'user' => [
                'id' => $user->id,
                'type' => $user->user_type,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'avatar_original' => uploaded_asset($user->avatar_original),
                'phone' => $user->phone,
                'email_verified' => $user->email_verified_at != null,
                'first_login_phone' => $user->first_login
            ],
            'shopInfo' => $shopInfo
        ]);
    }


    protected function loginFailed()
    {

        return response()->json([
            'result' => false,
            'message' => translate('Login Failed'),
            'access_token' => '',
            'token_type' => '',
            'expires_at' => null,
            'user' => [
                'id' => 0,
                'type' => '',
                'name' => '',
                'email' => '',
                'avatar' => '',
                'avatar_original' => '',
                'phone' => ''
            ]
        ]);
    }


    public function account_deletion()
    {
        if (auth()->user()) {
            Cart::where('user_id', auth()->user()->id)->delete();
        }
        $auth_user = auth()->user();
        $auth_user->tokens()->where('id', $auth_user->currentAccessToken()->id)->delete();
        $auth_user->customer_products()->delete();

        User::destroy(auth()->user()->id);

        return response()->json([
            "result" => true,
            "message" => translate('Your account deletion successfully done')
        ]);
    }

    public function getUserInfoByAccessToken(Request $request)
    {
        $token = PersonalAccessToken::findToken($request->access_token);
        if (!$token) {
            return $this->loginFailed();
        }
        $user = $token->tokenable;

        if ($user == null) {
            return $this->loginFailed();
        }

        return $this->loginSuccess($user, $request->access_token);
    }



   public function send_email_change_verification_mail(Request $request)
{
    $response['status'] = 0;
    $response['message'] = 'Unknown';

    $user = User::find($request->user_id);
    if (!$user) {
        return response()->json(['result' => false, 'message' => translate('User not found')], 404);
    }

    $userExist = User::where('email', $request->email)->first();
    if ($userExist) {
        return response()->json(['result' => false, 'message' => translate('Email Already Exist')], 401);
    }

    $verification_code = Str::random(32);

    $verifyLink = route('email_change.callback') . '?new_email_verificiation_code=' . $verification_code . '&email=' . urlencode($request->email);

    $user->new_email_verificiation_code = $verification_code;
    $user->email = $request->email;
    $user->save();

    try {
        $mailjet = new MailjetAuthMailer();

        $array = [
            'to' => $request->email,
            'subject' => translate('Email Verification'),
            'template_id' => null, // No template; use Blade
            'view' => 'emails.verification',
            'content' => "
                <p>Dear {$user->name},</p>
                <p>Please click the button below to verify your new email address:</p>
                <div style='text-align:center; margin: 20px 0;'>
                    <a href='$verifyLink' style='background: #28a745; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Verify Email</a>
                </div>
                <p>If you didn’t request this, you can safely ignore it.</p>
                <p>Regards,<br>Shopeedo Team</p>
            ",
            'link' => $verifyLink,
        ];

        $responseMail = $mailjet->send($array);

        if ($responseMail->success()) {
            $response['status'] = 1;
            $response['message'] = translate("Your verification mail has been sent to your email.");
        } else {
            \Log::error('Mailjet failed (email change): ' . $responseMail->getReasonPhrase());
            $response['status'] = 0;
            $response['message'] = translate("Failed to send verification email.");
        }

    } catch (\Exception $e) {
        \Log::error('Exception in email change verification: ' . $e->getMessage());
        $response['status'] = 0;
        $response['message'] = $e->getMessage();
    }

    return response()->json($response);
}

    public function email_change_callback(Request $request)
    {
        if ($request->has('new_email_verificiation_code') && $request->has('email')) {
            $verification_code_of_url_param = $request->input('new_email_verificiation_code');
            $user = User::where('new_email_verificiation_code', $verification_code_of_url_param)->first();

            if ($user != null) {

                $user->email = $request->input('email');
                $user->new_email_verificiation_code = null;
                $user->save();

                auth()->login($user, true);

                flash(translate('Email Changed successfully'))->success();
                if ($user->user_type == 'seller') {
                    return redirect()->route('seller.dashboard');
                }
                return redirect()->route('dashboard');
            }
        }

        flash(translate('Email was not verified. Please resend your mail!'))->error();
        return redirect()->route('dashboard');
    }
}
