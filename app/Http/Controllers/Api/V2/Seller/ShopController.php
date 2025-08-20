<?php

namespace App\Http\Controllers\Api\V2\Seller;

use App\Http\Controllers\Api\V2\AuthController;
use App\Http\Requests\SellerRegistrationRequest;
use App\Http\Resources\V2\Seller\ProductCollection;
use App\Http\Resources\V2\Seller\CommissionHistoryResource;
use App\Http\Resources\V2\Seller\SellerPaymentResource;
use App\Http\Resources\V2\ShopCollection;
use App\Http\Resources\V2\ShopDetailsCollection;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\CommissionHistory;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Notifications\AppEmailVerificationNotification;
use Illuminate\Http\Request;
use App\Utility\SearchUtility;
use Carbon\Carbon;
use DB;
use Hash;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ShopVerificationNotification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Upload;
use Mail;
use App\Mail\SecondEmailVerifyMailManager;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $shop_query = Shop::query();

        if ($request->name != null && $request->name != "") {
            $shop_query->where("name", 'like', "%{$request->name}%");
            SearchUtility::store($request->name);
        }
        return new ShopCollection($shop_query->whereIn('user_id', verified_sellers_id())->paginate(10));
    }



    public function update(Request $request)
    {
        $shop = Shop::where('user_id', auth()->user()->id)->first();
        $successMessage = 'Shop info updated successfully';
        $failedMessage = 'Shop info updated failed';
        $dimensionMessage = 'Shop Logo should be 500x500 pixels';
        $sliderMessage = 'Shop Sliders should be 1440x375 pixels';


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
                return $this->failed(translate($dimensionMessage));
            }
        }
        if ($request->sliders != null) {
            $sliderIds = explode(',', $request->sliders);

            if (!is_array($sliderIds)) {
                $sliderIds = [$sliderIds];
            }

            foreach ($sliderIds as $index => $sliderId) {
                $slider = Upload::where('id', $sliderId)->first();

                if (!$slider) {
                    return $this->failed("Slider with ID {$sliderId} not found.");
                }

                $filePath = Storage::path($slider->file_name);
                $file = new \Illuminate\Http\File($filePath);

                $validator = Validator::make(
                    ['slider' => $file],
                    ['slider' => 'dimensions:min_width=1440,min_height=375'],
                    ['slider.dimensions' => "Slider image at position " . ($index + 1) . " must be at least 1440 x 375 pixels in size."]
                );

                if ($validator->fails()) {
                    return $this->failed(translate($validator->errors()->first()));  // Retrieve the specific error message
                }
            }
        }


        if ($request->has('name') && $request->has('address')) {
            if ($request->has('shipping_cost')) {
                $shop->shipping_cost = $request->shipping_cost;
            }
            $shop->name             = $request->name;
            $shop->address          = $request->address;
            $shop->phone            = $request->phone;
            $shop->slug             = preg_replace('/\s+/', '-', $request->name) . '-' . $shop->id;
            $shop->meta_title       = $request->meta_title;
            $shop->meta_description = $request->meta_description;
            $shop->logo             = $request->logo;
        }

        if ($request->has('delivery_pickup_longitude') && $request->has('delivery_pickup_latitude')) {

            $shop->delivery_pickup_longitude    = $request->delivery_pickup_longitude;
            $shop->delivery_pickup_latitude     = $request->delivery_pickup_latitude;
        } elseif (
            $request->has('facebook') ||
            $request->has('google') ||
            $request->has('twitter') ||
            $request->has('youtube') ||
            $request->has('instagram')
        ) {
            $shop->facebook = $request->facebook;
            $shop->instagram = $request->instagram;
            $shop->google = $request->google;
            $shop->twitter = $request->twitter;
            $shop->youtube = $request->youtube;
        } elseif (
            $request->has('cash_on_delivery_status') ||
            $request->has('bank_payment_status') ||
            $request->has('bank_name') ||
            $request->has('bank_acc_name') ||
            $request->has('bank_acc_no') ||
            $request->has('bank_routing_no')
        ) {

            $shop->cash_on_delivery_status = $request->cash_on_delivery_status;
            $shop->bank_payment_status = $request->bank_payment_status;
            $shop->bank_name = $request->bank_name;
            $shop->bank_acc_name = $request->bank_acc_name;
            $shop->bank_acc_no = $request->bank_acc_no;
            $shop->bank_routing_no = $request->bank_routing_no;

            $successMessage = 'Payment info updated successfully';
        } else {

            $shop->sliders = $request->sliders;
        }

        if ($shop->save()) {
            return $this->success(translate($successMessage));
        }

        return $this->failed(translate($failedMessage));
    }


    public function sales_stat()
    {
        $data = Order::where('created_at', '>=', Carbon::now()->subDays(7))
            ->where('seller_id', '=', auth()->user()->id)
            ->where('delivery_status', '=', 'delivered')
            ->select(DB::raw("sum(grand_total) as total, DATE_FORMAT(created_at, '%b-%d') as date"))
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"))
            ->get()->toArray();

        $sales_array = [];
        for ($i = 1; $i < 8; $i++) {
            $new_date = date("M-d", strtotime(($i - 1) . " days ago"));

            $sales_array[$i]['date'] = $new_date;
            $sales_array[$i]['total'] = 0;

            if (!empty($data)) {
                $key = array_search($new_date, array_column($data, 'date'));
                if (is_numeric($key)) {
                    $sales_array[$i]['total'] = $data[$key]['total'];
                }
            }
        }

        return Response()->json($sales_array);
    }

    public function category_wise_products()
    {
        $category_wise_product = [];
        $new_array = [];
        foreach (Category::all() as $key => $category) {
            if (count($category->products->where('user_id', auth()->user()->id)) > 0) {
                $category_wise_product['name'] = $category->getTranslation('name');
                $category_wise_product['banner'] = uploaded_asset($category->banner);
                $category_wise_product['cnt_product'] = count($category->products->where('user_id', auth()->user()->id));

                $new_array[] = $category_wise_product;
            }
        }

        return Response()->json($new_array);
    }

    public function top_12_products()
    {
        $products = filter_products(Product::where('user_id',  auth()->user()->id)
            ->orderBy('num_of_sale', 'desc'))
            ->limit(12)
            ->get();



        return new ProductCollection($products);
    }

    public function info()
    {
        return new ShopDetailsCollection(auth()->user()->shop);
    }

    public function pacakge()
    {
        $shop = auth()->user()->shop;

        return response()->json([
            'result' => true,
            'id' => $shop->id,
            'package_name' => $shop->seller_package->name,
            'package_img' => uploaded_asset($shop->seller_package->logo)

        ]);
    }

    public function profile()
    {
        $user = auth()->user();


        return response()->json([
            'result' => true,
            'id' => $user->id,
            'type' => $user->user_type,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
            'avatar_original' =>  $user->avatar_original ? uploaded_asset($user->avatar_original) : "",
            'phone' => $user->phone,
            'ntn' => $user->ntn,
            'cnic_no' => $user->cnic_no,
            'date_of_issue' => $user->date_of_issue,
            'date_of_expiry' => $user->date_of_expiry,
            'front_side_picture' => uploaded_asset($user->front_side_picture) ,
            'back_side_picture' => uploaded_asset( $user->back_side_picture)

        ]);
    }

    public function payment_histories()
    {
        $payments = Payment::where('seller_id', auth()->user()->id)->paginate(10);
        return SellerPaymentResource::collection($payments);
    }

    public function collection_histories()
    {
        $commission_history = CommissionHistory::where('seller_id', auth()->user()->id)->orderBy('created_at', 'desc')->paginate(10);
        return CommissionHistoryResource::collection($commission_history);
    }

    public function store(SellerRegistrationRequest $request)
    {
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->user_type = "seller";
        $user->password = Hash::make($request->password);
        $user->verification_code = rand(100000, 999999);

        if ($user->save()) {
            $shop = new Shop;
            $shop->user_id = $user->id;
            $shop->name = $request->shop_name;
            $shop->address = $request->address;
            $shop->slug = preg_replace('/\s+/', '-', str_replace("/", " ", $request->shop_name));
            $shop->save();

            if (BusinessSetting::where('type', 'email_verification')->first()->value != 1) {
                $user->email_verified_at = date('Y-m-d H:m:s');
                $user->save();
            } else {

                try {
                    $user->notify(new AppEmailVerificationNotification());
                } catch (\Exception $e) {
                    $shop->delete();
                    $user->delete();
                    return $this->failed(translate('Something Went Wrong!'));
                }
            }
            $authController = new AuthController();
            return $authController->loginSuccess($user);
        }

        return $this->failed(translate('Something Went Wrong!'));
    }


    public function getVerifyForm()
    {
        $forms = BusinessSetting::where('type', 'verification_form')->first();
        return response()->json(json_decode($forms->value));
    }

    public function store_verify_info(Request $request)
{
    $data = array();
    $i = 0;
    foreach (json_decode(BusinessSetting::where('type', 'verification_form')->first()->value) as $key => $element) {
        $item = array();
        if ($element->type == 'text') {
            $item['type'] = 'text';
            $item['label'] = $element->label;
            $item['value'] = $request['element_' . $i] ?? null;
        } elseif ($element->type == 'select' || $element->type == 'radio') {
            $item['type'] = 'select';
            $item['label'] = $element->label;
            $item['value'] = $request['element_' . $i] ?? null;
        } elseif ($element->type == 'multi_select') {
            $item['type'] = 'multi_select';
            $item['label'] = $element->label;
            $item['value'] = !empty($request['element_' . $i]) ? json_encode($request['element_' . $i]) : null;
        } elseif ($element->type == 'file') {
            $item['type'] = 'file';
            $item['label'] = $element->label;
            // Check if the element is a file upload and is not empty
            $item['value'] = ($request->hasFile('element_' . $i) && $request->file('element_' . $i)->isValid())
                ? $request->file('element_' . $i)->store('uploads/verification_form')
                : null;
        }
        array_push($data, $item);
        $i++;
    }

    $shop = auth()->user()->shop;
    $shop->verification_info = json_encode($data);

    if ($shop->save()) {
        $users = User::findMany([User::where('user_type', 'admin')->first()->id]);
        $data = array();
        $data['shop'] = $shop;
        $data['status'] = 'submitted';
        $data['notification_type_id'] = get_notification_type('shop_verify_request_submitted', 'type')->id;
        Notification::send($users, new ShopVerificationNotification($data));

        return $this->success(translate('Your shop verification request has been submitted successfully!'));
    }

        return $this->failed(translate('Something Went Wrong!'));
    }

    public function shop_status(Request $request)
    {

        $request->validate([
            'status' => 'required|boolean', // Ensure the status is a boolean (1 or 0, true or false)
        ]);

        // Get the authenticated user
        $user = auth()->user();

        // Check if the user has a shop
        if (!$user->shop) {
        // return 1;

            return response()->json([
                'status'  => 'error',
                'message' => 'Shop not found for the authenticated user',
            ], 404);
        }

        // Update the shop status
        $user->shop->shop_status = $request->status;
        $user->shop->save();

        // Return success response
        return response()->json([
            'status'  => 'success',
            'message' => 'Shop status updated successfully',

        ], 200);
    }

    public function get_shop_status(Request $request){

         $user = auth()->user();
           if (!$user->shop) {
        // return 1;

            return response()->json([
                'status'  => 'error',
                'message' => 'Shop not found for the authenticated user',
            ], 404);
        }

        if($user->shop->shop_status == 1){
             return response()->json([
                'status'  => 'true',
                'message' => 'Shop is Online',
            ], 404);
        }else{
              return response()->json([
                'status'  => 'false',
                'message' => 'Shop is Offline',
            ], 404);
        }

    }
}
