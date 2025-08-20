<?php

namespace App\Http\Controllers\Seller;

use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Upload;
use App\Models\User;
use App\Notifications\ShopVerificationNotification;
use Auth;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rules\Dimensions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class ShopController extends Controller
{
    public function index()
    {
        $shop = Auth::user()->shop;
        return view('seller.shop', compact('shop'));
    }

    public function update(Request $request)
    {
        // dd($request->all());
        if ($request->logo != null) {
            $logo = Upload::where('id', $request->logo)->first();

            $filePath = Storage::path($logo->file_name); // Get the full path to the file

            $file = new \Illuminate\Http\File($filePath);

            // Manually create a validator for the file
            $validator = Validator::make(
                ['logo' => $file],
                ['logo' => 'dimensions:max_width=500,max_height=500'],
                ['logo.dimensions' => 'The logo must not be greater than 500x500 pixels.']
            );

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }
        if ($request->top_banner != null) {
            // Retrieve the banner upload by ID
            $top_banner = Upload::where('id', $request->top_banner)->first();

            // Get the file path of the uploaded banner
            $filePath = Storage::path($top_banner->file_name);

            // Create a new Illuminate File instance from the file path
            $file = new \Illuminate\Http\File($filePath);

            // Validate the dimensions of the banner
            $validator = Validator::make(
                ['top_banner' => $file],
                ['top_banner' => 'dimensions:min_width=1440,min_height=375'],
                ['top_banner.dimensions' => 'The banner must be at least 1440x375 pixels in size.']
            );

            // If validation fails, return with error message
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }
        if ($request->sliders != null) {
            $sliderIds = explode(',', $request->sliders);
            // If it's not an array, convert it to an array (this handles the case where a single ID is passed)
            if (!is_array($sliderIds)) {
                $sliderIds = [$sliderIds];  // Convert single ID into an array
            }

            foreach ($sliderIds as $sliderId) {
                $slider = Upload::where('id', $sliderId)->first();
                $filePath = Storage::path($slider->file_name);
                $file = new \Illuminate\Http\File($filePath);

                // Validate each slider file
                $validator = Validator::make(
                    ['slider' => $file],
                    ['slider' => 'dimensions:min_width=1440,min_height=375'],
                    ['slider.dimensions' => 'Each slider must be at least 1440x375 pixels in size.']
                );

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
            }
        }


        $shop = Shop::find($request->shop_id);

        // if ($request->has('name') && $request->has('address')) {
        if ($request->has('shipping_cost')) {
            $shop->shipping_cost = $request->shipping_cost;
        }





        $shop->name             = $request->name;
        $shop->address          = $request->address;
        $shop->phone            = $request->phone;
        $shop->slug             = preg_replace('/\s+/', '-', $request->name) . '-' . $shop->id;
        $shop->meta_title       = $request->meta_title;
        $shop->meta_description = $request->meta_description;
        if ($request->logo != null) {
            $shop->logo             = $request->logo;
        }



        // if ($request->has('delivery_pickup_longitude') && $request->has('delivery_pickup_latitude')) {

        $shop->delivery_pickup_longitude    = $request->delivery_pickup_longitude;
        $shop->delivery_pickup_latitude     = $request->delivery_pickup_latitude;
        // } elseif (
        //     $request->has('facebook') ||
        //     $request->has('google') ||
        //     $request->has('twitter') ||
        //     $request->has('youtube') ||
        //     $request->has('instagram')
        // ) {
        $shop->facebook = $request->facebook;
        $shop->instagram = $request->instagram;
        $shop->google = $request->google;
        $shop->twitter = $request->twitter;
        $shop->youtube = $request->youtube;
        // } elseif (
        //     $request->has('top_banner') ||
        //     $request->has('sliders') ||
        //     $request->has('banner_full_width_1') ||
        //     $request->has('banners_half_width') ||
        //     $request->has('banner_full_width_2')
        // ) {
        // if ($request->top_banner != null) {
        $shop->top_banner = $request->top_banner;
        // }
        // if ($request->sliders != null) {
        $shop->sliders = $request->sliders;
        // }
        // $shop->banner_full_width_1 = $request->banner_full_width_1;
        // $shop->banners_half_width = $request->banners_half_width;
        // $shop->banner_full_width_2 = $request->banner_full_width_2;
        // }

        if ($shop->save()) {
            flash(translate('Your Shop has been updated successfully!'))->success();
            return back();
        }

        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }

    public function verify_form()
    {
        if (Auth::user()->shop->verification_info == null) {
            $shop = Auth::user()->shop;
            return view('seller.verify_form', compact('shop'));
        } else {
            flash(translate('Sorry! You have sent verification request already.'))->error();
            return back();
        }
    }

    public function verify_form_store(Request $request)
{
    $data = array();
    $i = 0;

    $form_structure = json_decode(BusinessSetting::where('type', 'verification_form')->first()->value);

    foreach ($form_structure as $key => $element) {
        $item = array();
        $fieldKey = 'element_' . $i;

        $item['type'] = $element->type;
        $item['label'] = $element->label;

        if (in_array($element->type, ['text', 'select', 'radio'])) {
            $item['value'] = $request->input($fieldKey, null);
        } elseif ($element->type == 'multi_select') {
            $item['value'] = !empty($request->$fieldKey) ? json_encode($request->$fieldKey) : null;
        } elseif ($element->type == 'file') {
            if ($request->hasFile($fieldKey) && $request->file($fieldKey)->isValid()) {
                $item['value'] = $request->file($fieldKey)->store('uploads/verification_form');
            } else {
                $item['value'] = null;
            }
        }

        $data[] = $item;
        $i++;
    }

    $shop = Auth::user()->shop;
    $shop->verification_info = json_encode($data);

    if ($shop->save()) {
        $adminId = User::where('user_type', 'admin')->value('id');
        $users = User::findMany([$adminId]);

        Notification::send($users, new ShopVerificationNotification([
            'shop' => $shop,
            'status' => 'submitted',
            'notification_type_id' => get_notification_type('shop_verify_request_submitted', 'type')->id,
        ]));

        flash(translate('Your shop verification request has been submitted successfully!'))->success();
        return redirect()->route('seller.dashboard');
    }

    flash(translate('Sorry! Something went wrong.'))->error();
    return back();
}


    public function shop_status(Request $request)
    {

        $user = User::findOrFail($request->user_id);
        $user->shop->shop_status = $request->status;

        $user->shop->save();

        return response()->json(['status' => 'Shop Status update succesffully', 'message'=> true], 200);

    }
    public function show() {}
}
