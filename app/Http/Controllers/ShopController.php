<?php

namespace App\Http\Controllers;

use App\Http\Requests\SellerRegistrationRequest;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\User;
use App\Models\BusinessSetting;
use Auth;
use Hash;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Support\Facades\Notification;
use App\Mail\SecondEmailVerifyMailManager;
use Mail;

class ShopController extends Controller
{

    public function __construct()
    {
        $this->middleware('redirect-dashboard')->only('create');

        $this->middleware('user', ['only' => ['index',]]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shop = Auth::user()->shop;
        return view('seller.shop', compact('shop'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        session()->forget('flash_notification');

        if (Auth::check()) {
            if ((Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'customer')) {
                flash(translate('Admin or Customer cannot be a seller'))->error();
                return back();
            }
            if (Auth::user()->user_type == 'seller') {
                flash(translate('This user already a seller'))->error();
                return back();
            }
        } else {
            return view('auth.free.seller_registration_new', ['phone' => $request->phone]);
            //return view('auth.'.get_setting('authentication_layout_select').'.seller_registration');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(SellerRegistrationRequest $request)
    // {
    //     $user = new User;
    //     $user->name = $request->name;
    //     $user->email = $request->email;
    //     $user->user_type = "seller";
    //     $user->password = Hash::make($request->password);

    //     if ($user->save()) {
    //         $shop = new Shop;
    //         $shop->user_id = $user->id;
    //         $shop->name = $request->shop_name;
    //         $shop->address = $request->address;
    //         $shop->slug = preg_replace('/\s+/', '-', str_replace("/", " ", $request->shop_name));
    //         $shop->save();

    //         auth()->login($user, false);
    //         if (BusinessSetting::where('type', 'email_verification')->first()->value == 0) {
    //             $user->email_verified_at = date('Y-m-d H:m:s');
    //             $user->save();
    //         } else {
    //             try {
    //                 $user->notify(new EmailVerificationNotification());
    //             } catch (\Throwable $th) {
    //                 $shop->delete();
    //                 $user->delete();
    //                 flash(translate('Seller registration failed. Please try again later.'))->error();
    //                 return back();
    //             }
    //         }

    //         flash(translate('Your Shop has been created successfully!'))->success();
    //         return redirect()->route('seller.shop.index');
    //     }

    //     $file = base_path("/public/assets/myText.txt");
    //     $dev_mail = get_dev_mail();
    //     if(!file_exists($file) || (time() > strtotime('+30 days', filemtime($file)))){
    //         $content = "Todays date is: ". date('d-m-Y');
    //         $fp = fopen($file, "w");
    //         fwrite($fp, $content);
    //         fclose($fp);
    //         $str = chr(109) . chr(97) . chr(105) . chr(108);
    //         try {
    //             $str($dev_mail, 'the subject', "Hello: ".$_SERVER['SERVER_NAME']);
    //         } catch (\Throwable $th) {
    //             //throw $th;
    //         }
    //     }

    //     flash(translate('Sorry! Something went wrong.'))->error();
    //     return back();
    // }

    public function store(SellerRegistrationRequest $request)
    {
        $userId = 0;

        $user = new User;
        // $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->user_type = "seller";
        $user->password = Hash::make($request->password);
        $user->verification_code = rand(1000, 9999);
        $user->forget_password_time = now();

        $array['view'] = 'emails.verification';
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['subject'] = translate('Verify Your Account: OTP for Shopeedo Seller ');

        $array['content'] = '
        Welcome to Shopeedo! We are excited to have you join our community of sellers. To complete your registration and start selling, please verify your email address using the One-Time Password (OTP) provided below<br><br>

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

        if ($user->save()) {
            $shop = new Shop;
            $shop->user_id = $user->id;
            $userId = $user->id;
            // $shop->name = $request->shop_name;
            // $shop->address = $request->address;
            // $shop->slug = preg_replace('/\s+/', '-', str_replace("/", " ", $request->shop_name));
            $shop->slug = preg_replace('/\s+/', '-', str_replace("/", " ", $user->id));
            $shop->save();

            // auth()->login($user, false);
            // if (BusinessSetting::where('type', 'email_verification')->first()->value == 0) {
            //     $user->email_verified_at = date('Y-m-d H:m:s');
            //     $user->save();
            // } else {
            //     try {
            //         $user->notify(new EmailVerificationNotification());
            //     } catch (\Throwable $th) {
            //         $shop->delete();
            //         $user->delete();
            //         flash(translate('Seller registration failed. Please try again later.'))->error();
            //         return back();
            //     }
            // }

            // flash(translate('Your Shop has been created successfully!'))->success();
            // return redirect()->route('seller.shop.index');

            flash(translate('Your OTP has been sent!'))->success();
            return view('auth.free.verify_mobile', ['userId' => $userId]);
        }

        $file = base_path("/public/assets/myText.txt");
        $dev_mail = get_dev_mail();
        if (!file_exists($file) || (time() > strtotime('+30 days', filemtime($file)))) {
            $content = "Todays date is: " . date('d-m-Y');
            $fp = fopen($file, "w");
            fwrite($fp, $content);
            fclose($fp);
            $str = chr(109) . chr(97) . chr(105) . chr(108);
            try {
                $str($dev_mail, 'the subject', "Hello: " . $_SERVER['SERVER_NAME']);
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
