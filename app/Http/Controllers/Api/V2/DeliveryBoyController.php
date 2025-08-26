<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\DeliveryBoyBonusHistory;
use Carbon\Carbon;
use App\Models\Shop;
use App\Models\User;
use App\Models\Order;
use App\Models\Address;
use App\Models\DeliveryBoy;
use App\Models\OrderDetail;
use App\Models\SmsTemplate;
use App\Utility\SmsUtility;
use Illuminate\Http\Request;
use App\Models\CombinedOrder;
use App\Models\BusinessSetting;
use App\Models\DeliveryHistory;
use App\Models\DeliveryBoyPayment;
use App\Utility\NotificationUtility;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\V2\DeliveryHistoryCollection;
use App\Http\Resources\V2\PurchaseHistoryCollection;
use App\Http\Resources\V2\PurchaseHistoryMiniCollection;
use App\Http\Resources\V2\PurchaseHistoryItemsCollection;
use App\Http\Resources\V2\PurchaseHistoryDeliveryCollection;
use App\Http\Resources\V2\DeliveryBoyPendingHistoryMiniCollection;
use App\Http\Resources\V2\DeliveryBoyPurchaseHistoryMiniCollection;
use App\Models\DeliveryBoyShiftHistory;
use App\Models\DeliveryBoyCollection;

class DeliveryBoyController extends Controller
{

    /**
     * Show the list of assigned delivery by the admin.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    // public function dashboard_summary($id)
    // {
    //     $order_query = Order::query();
    //     $order_query->where('assign_delivery_boy', $id);

    //     $delivery_boy = DeliveryBoy::where('user_id', $id)->first();

    //     return response()->json([
    //         'completed_delivery' => Order::where('assign_delivery_boy', $id)->where('delivery_status', 'delivered')->count(),
    //         // 'pending_delivery' => Order::where('assign_delivery_boy', $id)->where('delivery_status', '!=', 'delivered')->where('delivery_status', '!=', 'cancelled')->where('cancel_request', '0')->count(),
    //         'pending_delivery' =>Order::whereJsonContains('assignment_candidates', $id)
    //         ->whereNull('assign_delivery_boy')
    //         // ->where('offer_expiry_time', '>', now())
    //         ->where(function ($query) {
    //             $query->where('delivery_status', 'pending')
    //                 ->orWhere('delivery_status', 'confirmed');
    //         })->where('cancel_request', '0')
    //         ->count(),
    //         'total_collection' => format_price($delivery_boy->total_collection),
    //         'total_earning' => format_price($delivery_boy->total_earning),
    //         'cancelled' => Order::where('assign_delivery_boy', $id)->where('delivery_status', 'cancelled')->count(),
    //         'on_the_way' => Order::where('assign_delivery_boy', $id)->where('delivery_status', 'on_the_way')->where('cancel_request', '0')->count(),
    //         'picked' => Order::where('assign_delivery_boy', $id)->where('delivery_status', 'picked_up')->where('cancel_request', '0')->count(),
    //         'assigned' => Order::where('assign_delivery_boy', $id)
    //         ->where(function ($query) {
    //             $query->where('delivery_status', 'pending')
    //                 ->orWhere('delivery_status', 'confirmed');
    //         })
    //         ->where('cancel_request', '0')
    //         ->count(),

    //     ]);
    // }

  public function dashboard_summary($id): \Illuminate\Http\JsonResponse
    {
    
        $delivery_boy = DeliveryBoy::where('user_id', $id)->first();
    
        if (!$delivery_boy) {
            return response()->json(['error' => 'Delivery boy not found'], 404);
        }
    
        $pendingOffers = Order::whereRaw("JSON_CONTAINS(assignment_candidates, ?)", [json_encode((int)$id)])
            ->whereNull('assign_delivery_boy')
            ->where(function ($query) {
                $query->where('delivery_status', 'pending')
                    ->orWhere('delivery_status', 'confirmed');
            })
            ->where('cancel_request', '0')
            ->count();

        $bonus_due = DeliveryBoyBonusHistory::where('user_id', $id)->where('is_paid', 0)
                    ->sum('bonus_amount');
        
    
        $cod_amount_limit = BusinessSetting::where('type', 'max_cod_amount')->first()->value ?? 0;
    
        return response()->json([
            'completed_delivery' => Order::where('assign_delivery_boy', $id)
                ->where('delivery_status', 'delivered')
                ->count(),
            'pending_delivery' => $pendingOffers,
            'total_collection' => format_price($delivery_boy->total_collection),
            'total_earning' => format_price($delivery_boy->total_earning),
            'cancelled' => Order::where('assign_delivery_boy', $id)
                ->where('delivery_status', 'cancelled')
                ->count(),
            'bonus_due' => format_price($bonus_due),
            'payment_due' => format_price($delivery_boy->total_earning),
            'cod_status' => $delivery_boy->total_collection <= $cod_amount_limit,
            'max_cod_amount' => $cod_amount_limit
        ]);
    }
    public function assigned_delivery($id)
    {
        $order_query = Order::query();
        $order_query->where('assign_delivery_boy', $id);
        $order_query->where(function ($query) {
            $query->where(function ($q) {
                $q->where('delivery_status', 'pending')
                    ->where('cancel_request', '0');
            })->orWhere(function ($q) {
                $q->where('delivery_status', 'confirmed')
                    ->where('cancel_request', '0');
            });
        });
        return new DeliveryBoyPurchaseHistoryMiniCollection($order_query->latest('delivery_history_date')->paginate(10));
    }

    /**
     * Show the list of pickup delivery by the delivery boy.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function picked_up_delivery($id)
    {
        $order_query = Order::query();
        $order_query->where('delivery_status', 'picked_up');
        $order_query->where('cancel_request', '0');
        return new DeliveryBoyPurchaseHistoryMiniCollection($order_query->where('assign_delivery_boy', $id)->latest('delivery_history_date')->paginate(10));
    }

     public function on_going_delivery($id)
    {
        $order_query = Order::query();

        $order_query->where(function ($query) {
            $query->whereIn('delivery_status', ['picked_up', 'on_the_way'])
                ->orWhere(function ($q) {
                    $q->where(function ($subQ) {
                        $subQ->where('delivery_status', 'pending')
                            ->where('cancel_request', '0');
                    })->orWhere(function ($subQ) {
                        $subQ->where('delivery_status', 'confirmed')
                            ->where('cancel_request', '0');
                    });
                });
        });

        $order_query->where('assign_delivery_boy', $id);

        return new DeliveryBoyPurchaseHistoryMiniCollection(
            $order_query->latest('delivery_history_date')->paginate(10)
        );
    }

    /**
     * Show the list of pickup delivery by the delivery boy.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function on_the_way_delivery($id)
    {
        $order_query = Order::query();
        $order_query->where('delivery_status', 'on_the_way');
        $order_query->where('cancel_request', '0');

        return new DeliveryBoyPurchaseHistoryMiniCollection($order_query->where('assign_delivery_boy', $id)->latest('delivery_history_date')->paginate(10));
    }

    /**
     * Show the list of completed delivery by the delivery boy.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function completed_delivery($id)
    {
        $order_query = Order::query();
        $order_query->where('delivery_status', 'delivered');

        if (request()->has('date_range') && request()->date_range != null &&  request()->date_range != "") {
            $max_date = date('Y-m-d H:i:s');
            $min_date = date('Y-m-d 00:00:00');
            if (request()->date_range == "today") {
                $min_date = date('Y-m-d 00:00:00');
            } else if (request()->date_range == "this_week") {
                $min_date = date('Y-m-d 00:00:00', strtotime("-7 days"));
            } else if (request()->date_range == "this_month") {
                $min_date = date('Y-m-d 00:00:00', strtotime("-30 days"));
            }
            $order_query->where('delivery_history_date','>=',$min_date)->where('delivery_history_date','<=',$max_date);
        }
        if (request()->has('payment_type') && request()->payment_type != null &&  request()->payment_type != "") {
            if (request()->payment_type == "cod") {
                $order_query->where('payment_type','=','cash_on_delivery');
            } else if (request()->payment_type == "non-cod") {
                $order_query->where('payment_type','!=','cash_on_delivery');
            }
        }
        return new DeliveryBoyPurchaseHistoryMiniCollection($order_query->where('assign_delivery_boy', $id)->latest('delivery_history_date')->paginate(10));
    }

    public function deliveryHistories($id)
{
    $orders = Order::with([
        'orderDetails.product:id,name',
        'combinedOrder:id,shipping_cost' // whichever column exists
    ])
    ->where('delivery_status', 'delivered')
    ->where('assign_delivery_boy', $id)
    ->latest()
    ->paginate(3);

    return new PurchaseHistoryMiniCollection($orders);
}

    /**
     * Show the list of pending delivery by the delivery boy.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function pending_delivery($id)
    {
        $order_query = Order::query();
        $order_query->where('delivery_status', '!=', 'delivered');
        $order_query->where('delivery_status', '!=', 'cancelled');
        $order_query->where('cancel_request', '0');

        return new DeliveryBoyPurchaseHistoryMiniCollection($order_query->where('assign_delivery_boy', $id)->latest('delivery_history_date')->paginate(10));
    }

    /**
     * Show the list of cancelled delivery by the delivery boy.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function cancelled_delivery($id)
    {
        $order_query = Order::query();
        $order_query->where('delivery_status', 'cancelled');

        if (request()->has('date_range') && request()->date_range != null &&  request()->date_range != "") {
            $max_date = date('Y-m-d H:i:s');
            $min_date = date('Y-m-d 00:00:00');
            if (request()->date_range == "today") {
                $min_date = date('Y-m-d 00:00:00');
            } else if (request()->date_range == "this_week") {
                $min_date = date('Y-m-d 00:00:00', strtotime("-7 days"));
            } else if (request()->date_range == "this_month") {
                $min_date = date('Y-m-d 00:00:00', strtotime("-30 days"));
            }
            $order_query->where('delivery_history_date','>=',$min_date)->where('delivery_history_date','<=',$max_date);
        }

        if (request()->has('payment_type') && request()->payment_type != null &&  request()->payment_type != "") {

            if (request()->payment_type == "cod") {
                $order_query->where('payment_type','==','cash_on_delivery');
            } else if (request()->payment_type == "non-cod") {
                $order_query->where('payment_type','!=','cash_on_delivery');
            }
        }
        return new PurchaseHistoryMiniCollection($order_query->where('assign_delivery_boy', $id)->latest()->paginate(10));
    }

    /**
     * Show the list of today's collection by the delivery boy.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function collection($id)
    {
        $collection_query = DeliveryHistory::query();
        $collection_query->where('delivery_status', 'delivered');
        $collection_query->where('payment_type', 'cash_on_delivery');

        return new DeliveryHistoryCollection($collection_query->where('delivery_boy_id', $id)->latest()->paginate(10));
    }

    public function earning($id)
    {
        $collection_query = DeliveryHistory::query();
        $collection_query->where('delivery_status', 'delivered');

        return new DeliveryHistoryCollection($collection_query->where('delivery_boy_id', $id)->latest()->paginate(10));
    }

    public function deliveryBoyDetails($id)
    {
        $deliveryBoy = DeliveryBoy::where('user_id', $id)->first();
        $order_count = Order::where('delivery_status', 'delivered')->where('assign_delivery_boy', $id)->count();

        return response()->json([
            'result' => true,
            'order_delivered' => $order_count,
            'average_rating' => number_format((float) $deliveryBoy->rating, 1, '.', ''),
            'total_earning' =>$deliveryBoy->total_earning
        ]);
    }

    public function collection_summary($id)
    {
        $collection_query = DeliveryHistory::query();
        $collection_query->where('delivery_status', 'delivered');
        $collection_query->where('payment_type', 'cash_on_delivery');


        $today_date = date('Y-m-d');
        $yesterday_date = date('Y-m-d', strtotime("-1 day"));
        $today_date_formatted = date('d M, Y');
        $yesterday_date_formatted = date('d M,Y', strtotime("-1 day"));


        $today_collection = DeliveryHistory::where('delivery_status', 'delivered')
            ->where('payment_type', 'cash_on_delivery')
            ->where('delivery_boy_id', $id)
            ->where('created_at','like',"%$today_date%")
            ->sum('collection');

        $yesterday_collection = DeliveryHistory::where('delivery_status', 'delivered')
            ->where('payment_type', 'cash_on_delivery')
            ->where('delivery_boy_id', $id)
            ->where('created_at','like',"%$yesterday_date%")
            ->sum('collection');


        return response()->json([
            'today_date' => $today_date_formatted,
            'today_collection' => format_price($today_collection) ,
            'yesterday_date' => $yesterday_date_formatted,
            'yesterday_collection' => format_price($yesterday_collection) ,

        ]);
    }

    public function earning_summary($id)
    {
        $collection_query = DeliveryHistory::query();
        $collection_query->where('delivery_status', 'delivered');

        $today_date = date('Y-m-d');
        $yesterday_date = date('Y-m-d', strtotime("-1 day"));
        $today_date_formatted = date('d M, Y');
        $yesterday_date_formatted = date('d M,Y', strtotime("-1 day"));


        $today_collection = DeliveryHistory::where('delivery_status', 'delivered')
            ->where('delivery_boy_id', $id)
            ->where('created_at','like',"%$today_date%")
            ->sum('earning');

        $yesterday_collection = DeliveryHistory::where('delivery_status', 'delivered')
            ->where('delivery_boy_id', $id)
            ->where('created_at','like',"%$yesterday_date%")
            ->sum('earning');


        return response()->json([
            'today_date' => $today_date_formatted,
            'today_earning' => format_price($today_collection) ,
            'yesterday_date' => $yesterday_date_formatted,
            'yesterday_earning' => format_price($yesterday_collection) ,

        ]);
    }

    /**
     * For only delivery boy while changing delivery status.
     * Call from order controller
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function change_delivery_status(Request $request) {

        $delivery_status = DeliveryBoy::where('user_id', $request->delivery_boy_id)->first();

        if($delivery_status->status == 0){
            return response()->json([
                'result' => false,
                'message' => "You are not allowed to change delivery status"
            ]);
        }
        $order = Order::find($request->order_id);
        $order->delivery_viewed = '0';
        $order->delivery_status = $request->status;
        $order->save();

        $delivery_history = new DeliveryHistory;

        $delivery_history->order_id         = $order->id;
        $delivery_history->delivery_boy_id  = $request->delivery_boy_id;
        $delivery_history->delivery_status  = $order->delivery_status;
        $delivery_history->payment_type     = $order->payment_type;

        if($order->delivery_status == 'delivered') {
            foreach ($order->orderDetails as $key => $orderDetail) {
                if (addon_is_activated('affiliate_system')) {
                    if ($orderDetail->product_referral_code) {
                        $no_of_delivered = 0;
                        $no_of_canceled = 0;

                        if($request->status == 'delivered') {
                            $no_of_delivered = $orderDetail->quantity;
                        }
                        if($request->status == 'cancelled') {
                            $no_of_canceled = $orderDetail->quantity;
                        }

                        $referred_by_user = User::where('referral_code', $orderDetail->product_referral_code)->first();

                        $affiliateController = new AffiliateController;
                        $affiliateController->processAffiliateStats($referred_by_user->id, 0, 0, $no_of_delivered, $no_of_canceled);
                    }
                }
            }
            $delivery_boy = DeliveryBoy::where('user_id', $request->delivery_boy_id)->first();

            if (get_setting('delivery_boy_payment_type') == 'commission') {
                if(get_setting('delivery_boy_commission_type') == 'flat'){
                    $delivery_history->earning = get_setting('delivery_boy_commission');
                    $delivery_boy->total_earning += get_setting('delivery_boy_commission');
                }else if(get_setting('delivery_boy_commission_type') == 'percentage'){
                    $shipping_cost = optional(CombinedOrder::where('id', $order->combined_order_id)->first())->shipping_cost ?? 0;
                    $delivery_history->earning = ($shipping_cost * get_setting('delivery_boy_commission')) / 100;
                    $delivery_boy->total_earning += ($shipping_cost * get_setting('delivery_boy_commission')) / 100;
                }

            }
            if ($order->payment_type == 'cash_on_delivery') {
                $delivery_history->collection = $order->grand_total;
                $delivery_boy->total_collection += $order->grand_total;

                $order->payment_status = 'paid';
                if ($order->commission_calculated == 0) {
                    calculateCommissionAffilationClubPoint($order);
                    $order->commission_calculated = 1;
                }

            }


            $delivery_boy->save();
        }
        $order->delivery_history_date = date("Y-m-d H:i:s");

        $order->save();
        $delivery_history->save();

        if (addon_is_activated('otp_system') && SmsTemplate::where('identifier','delivery_status_change')->first()->status == 1){
            try {
                SmsUtility::delivery_status_change($order->user->phone, $order);
            } catch (\Exception $e) {

            }
        }

        return response()->json([
            'result' => true,
            'message' => translate('Delivery status changed to ') . ' ' . ucwords(str_replace('_', ' ', $request->status))
        ]);
    }

    public function cancel_request(Request $request, $id)
{
    $request->validate([
        'cancel_reason' => 'nullable|string|max:500',
    ]);

    $order = Order::with('orderDetails')->find($id);

    if (!$order) {
        return response()->json([
            'result'  => false,
            'message' => 'Order not found',
        ], 404);
    }

    // Idempotent: already cancelled
    if ($order->delivery_status === 'cancelled') {
        return response()->json([
            'result'  => false,
            'message' => 'Order already cancelled',
        ], 200);
    }

    \DB::transaction(function () use ($order, $request) {
        $now = Carbon::now('UTC');

        $order->delivery_status    = 'cancelled';
        $order->cancel_request_at  = $now;
        if ($request->filled('cancel_request')) {
            $order->cancel_reason = $request->cancel_request;
        }
        $order->updated_at = $now;
        $order->cancel_by = 'delivery_boy';
        $order->save();

        $order->orderDetails()->update([
            'delivery_status' => 'cancelled',
            'updated_at'      => $now,
        ]);
    });

    return response()->json([
        'result'  => true,
        'message' => translate('Order cancelled successfully'),
    ], 200);
}

    public function details($id)
    {
        $order_detail = Order::with('combinedOrder')
    ->where('id', $id)
    ->where('assign_delivery_boy', auth()->user()->id)
    ->get();
        return new PurchaseHistoryDeliveryCollection($order_detail);
    }

    public function items($id)
    {
        $order_id = Order::select('id')->where('id', $id)->where('assign_delivery_boy', auth()->user()->id)->first();
        $order_query = OrderDetail::where('order_id', $order_id->id);
        return new PurchaseHistoryItemsCollection($order_query->get());
    }

  public function delivery_boy_status(Request $request)
{
    $request->validate(['status' => 'required|boolean']); // 1=online, 0=offline

    $user = $request->user();
    $deliveryBoy = $user->deliveryBoy;
    if (!$deliveryBoy) {
        return response()->json(['status' => 'error','message' => 'Delivery boy not found'], 404);
    }

    if ((int)$deliveryBoy->status === (int)$request->status) {
        return response()->json([
            'status'  => 'failed',
            'message' => 'Delivery boy is already ' . ($deliveryBoy->status ? 'online' : 'offline'),
        ], 200);
    }

    $latitude  = $deliveryBoy->lat ?? null;
    $longitude = $deliveryBoy->lng ?? null;
    $isOnline  = $request->boolean('status');

    // Store UTC explicitly
    $deliveryBoy->status       = $isOnline ? 1 : 0;
    $deliveryBoy->online_since = $isOnline ? Carbon::now('UTC') : null;
    $deliveryBoy->save();

    \DB::transaction(function () use ($user, $isOnline, $latitude, $longitude) {
        $open = DeliveryBoyShiftHistory::where('user_id', $user->id)
            ->whereNull('end_at')
            ->lockForUpdate()
            ->first();

        if ($isOnline) {
            if (!$open) {
                DeliveryBoyShiftHistory::create([
                    'user_id'   => $user->id,
                    'start_at'  => Carbon::now('UTC'), // UTC
                    'start_lat' => $latitude,
                    'start_lng' => $longitude,
                ]);
            }
        } else {
            if ($open) {
                $end = Carbon::now('UTC');            // UTC
                $open->end_at           = $end;
                $open->duration_seconds = $end->diffInSeconds($open->start_at);
                $open->end_lat          = $latitude;
                $open->end_lng          = $longitude;
                $open->save();
            }
        }
    });

    return response()->json(['status' => 'success','message' => 'Delivery boy status updated successfully'], 200);
}

public function delivery_boy_get_status(Request $request)
{
    $user = $request->user();
    $deliveryBoy = $user->deliveryBoy;

    if (!$deliveryBoy) {
        return response()->json([
            'status' => false,
            'message' => 'Delivery boy not found',
            'last_shift_on' => null,
        ], 404);
    }

    $isOnline = (bool) $deliveryBoy->status;
    $tz = 'Asia/Karachi';

    // online_since is stored as UTC—convert for display
    $lastShiftOn = ($isOnline && $deliveryBoy->online_since)
        ? Carbon::parse($deliveryBoy->online_since, 'UTC')->setTimezone($tz)->format('Y-m-d H:i:s')
        : null;

    return response()->json([
        'status'        => $isOnline,
        'message'       => 'Delivery Boy is ' . ($isOnline ? 'online' : 'offline'),
        'last_shift_on' => $lastShiftOn,
    ], 200);
}



public function storeDeviceInfo(Request $request) {


    $request->validate([
        'latitude' => 'required|numeric|between:-90,90',
        'longitude' => 'required|numeric|between:-180,180',
        'device_token' => 'required|string',
    ]);

    // Get the authenticated user
    $user = Auth::user();

    // Update the user's location and device token
    $user->update([
        'device_token' => $request->device_token,
    ]);

    $user->deliveryBoy->update([
        'lat' => $request->latitude,
        'lng' => $request->longitude,
    ]);



    // Return a success response
    return response()->json([
        'message' => 'Device information stored successfully',
        // 'user' => $user,
    ], 200);


}

public function welcomebonus($id){

        $delivery_boy = DeliveryBoy::where('user_id', $id)->first();
        $completed_rides = $delivery_boy->completed_rides;

        $created_date = $delivery_boy->created_at;

        // Get the current date
        $current_date = Carbon::now();

        // Calculate the difference in days
        $difference_in_days = $created_date->diffInDays($current_date);

        // Check if the difference is not more than 30 days
        $is_within_30_days = $difference_in_days <= 30 ? true : false;

        if(!$is_within_30_days) {
            if($completed_rides == 75){
                return 2;
            }
            return  $completed_rides;
        }

        return response()->json([
           "data" =>  $is_within_30_days,
        ], 200);
}

// public function respondToOrderOffer(Request $request)
// {
//     $request->validate([
//         'order_id' => 'required|exists:orders,id',
//         'response' => 'required|in:accept,reject',
//     ]);

//     $order = Order::find($request->order_id);
//     $deliveryBoyId = $request->user()->id;

//     if (!in_array($deliveryBoyId, json_decode($order->assignment_candidates, true))) {
//         return response()->json(['message' => 'You are not assigned to this order.'], 403);
//     }

//     if ($order->assign_delivery_boy) {
//         return response()->json(['message' => 'Order already assigned.'], 400);
//     }

//     if ($request->response === 'accept') {
//         $order->assign_delivery_boy = $deliveryBoyId;
//         $order->offer_expiry_time = null;
//         $order->save();

//         return response()->json(['message' => 'Order accepted successfully.']);
//     }

//     // Remove this delivery boy from the candidate list
//     $candidates = array_values(array_diff(json_decode($order->assignment_candidates, true), [$deliveryBoyId]));
//     $order->assignment_candidates = json_encode($candidates);
//     $order->save();

//     // Move to next available delivery boy
//     if (!empty($candidates)) {
//         $order->offer_expiry_time = now()->addMinutes(5);
//         $order->save();
//         $this->sendOrderOfferToDeliveryBoy($order, $candidates[0]);
//     }

//     return response()->json(['message' => 'Order offer rejected.']);
// }


// public function getPendingOrderOffers(Request $request)
// {
//     $deliveryBoyId = $request->user()->id;

//     $orders = Order::whereJsonContains('assignment_candidates', $deliveryBoyId)
//         ->whereNull('assign_delivery_boy')
//         // ->where('offer_expiry_time', '>', now())
//         ->paginate(10);

//     return new DeliveryBoyPendingHistoryMiniCollection($orders);
// }


public function respondToOrderOffer(Request $request)
{
    $request->validate([
        'order_id' => 'required|exists:orders,id',
        'response' => 'required|in:accept,reject',
    ]);

    $order = Order::find($request->order_id);
    $deliveryBoyId = $request->user()->id;
    $delivery_boy_status = DeliveryBoy::where('user_id', $deliveryBoyId)->first();

    if ($delivery_boy_status->status == 0) {
        return response()->json(['message' => 'You are not allowed to accept this order'], 403);
    }

    $candidates = json_decode($order->assignment_candidates, true) ?: [];

    if (!in_array($deliveryBoyId, $candidates)) {
        return response()->json(['message' => 'You are not assigned to this order.'], 403);
    }

    if ($order->assign_delivery_boy) {
        return response()->json(['message' => 'Order already assigned to another delivery boy.'], 400);
    }

    if ($request->response === 'accept') {
        $order->assign_delivery_boy = $deliveryBoyId;
        $order->offer_expiry_time = null;
        $order->assignment_candidates = null;
        $order->save();

        return response()->json(['message' => 'Order accepted successfully.']);
    }

    // response is reject
    $candidates = array_values(array_diff($candidates, [$deliveryBoyId]));
    $order->assignment_candidates = !empty($candidates) ? json_encode($candidates) : null;

    if (!empty($candidates)) {
        $order->offer_expiry_time = now()->addMinutes(1);
        $order->save();
        $this->sendOrderOfferToDeliveryBoy($order, $candidates[0]);
    } else {
        $order->offer_expiry_time = null; // Clear the expiry time if no candidates left
        $order->save();
        \Log::info("No more delivery candidates available for order ID: {$order->id}");
    }

    return response()->json(['message' => 'Order offer rejected.']);
}




public function getPendingOrderOffers(Request $request)
{
    $deliveryBoyId = $request->user()->id;

    $orders = Order::whereRaw("JSON_CONTAINS(assignment_candidates, ?)", [json_encode($deliveryBoyId)])
        ->whereNull('assign_delivery_boy')
        // ->where('offer_expiry_time', '>', now())  // Uncomment if you want to enforce expiry
        ->paginate(10);

    return new DeliveryBoyPendingHistoryMiniCollection($orders);
}


private function sendOrderOfferToDeliveryBoy(Order $order, $deliveryBoyId)
{
    $deliveryBoy = User::find($deliveryBoyId);

    if ($deliveryBoy) {
        if (get_setting('google_firebase') == 1 && $deliveryBoy->device_token != null) {
            $notificationData = [
                'device_token' => $deliveryBoy->device_token,
                'title' => 'Order Updated!',
                'text' => "New order Available",
                'type' => 'order',
                'id' => $order->id,
                'user_id' => $deliveryBoy->id,
            ];
            NotificationUtility::sendFirebaseNotification($notificationData);
        }
    }
}


private function formatShippingAddress($shipping_address)
    {
        if (!$shipping_address) return '';

        $addressArray = json_decode($shipping_address, true);

        if (is_array($addressArray)) {
            $formattedAddress = array_filter([
                $addressArray['address'] ?? null,
                $addressArray['city'] ?? null,
                $addressArray['state'] ?? null,
                $addressArray['country'] ?? null
            ]);

            return implode(' ', $formattedAddress);
        }

        return '';
    }

public function assignedOrderDetails($id){
    $order_detail = Order::with('combinedOrder')
        ->where('id', $id)
        ->where('assign_delivery_boy', auth()->id())
        ->whereNotIn('delivery_status', ['delivered'])
        ->first();

    if (!$order_detail) {
        return response()->json(['message' => 'Order not found'], 404);
    }

    $order_detail_count = OrderDetail::where('order_id', $id)->count();
    
        $shipping_address = json_decode($order_detail->shipping_address, true);
        $lat = 90.99;
        $lang = 180.99;

        if (isset($shipping_address['lat_lang'])) {
            $location_available = true;
            $exploded_lat_lang = explode(',', $shipping_address['lat_lang']);
            $lat = floatval($exploded_lat_lang[0]);
            $lang = floatval($exploded_lat_lang[1]);
        }


    $seller = Shop::where('user_id', $order_detail->seller_id)->first();
    $customer = Address::where('user_id', $order_detail->user_id)->first();
    
    return response()->json([
        'id' =>$order_detail->id,
        'code' => $order_detail->code,
        'is_quick' => (bool) $order_detail->type == 'Taiz' ? true : false,
        'delivery_status' => $order_detail->delivery_status,
        'shipping_address' => $this->formatShippingAddress($order_detail->shipping_address),
        'shipping_latitude' => $lat,
        'shipping_longitude' => $lang,
        'shop_address' => $seller->address,
        'shop_latitude' => $seller->delivery_pickup_latitude != null ? $seller->delivery_pickup_latitude : '',
        'shop_longitude' => $seller->delivery_pickup_longitude != null ? $seller->delivery_pickup_longitude : '',
        'items_count'=>$order_detail_count,
        'shipping_cost'=> $order_detail->combinedOrder->shipping_cost,
        'grand_total' => $order_detail->grand_total,
        'created_at' => $order_detail->delivery_history_date
    ]);
}

public function deliveryBoyBonuses(){
    $deliveryBoyId = auth()->id();
    $totalBonus = DeliveryBoyBonusHistory::where('user_id', $deliveryBoyId)
        ->sum('bonus_amount');

    return response()->json([
        'total_bonus' => $totalBonus ? $totalBonus : 0
    ]);
}

public function earning_details_graph(Request $request)
{
    // Validate request parameters
    $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ]);

    $deliveryBoyId = auth()->id();
    $startDate = Carbon::parse($request->start_date)->startOfDay();
    $endDate = Carbon::parse($request->end_date)->endOfDay();

    // Fetch earnings grouped by day
    $earnings = DeliveryHistory::where('delivery_boy_id', $deliveryBoyId)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy(\DB::raw('DATE(created_at)'))
        ->select(
            \DB::raw('DATE(created_at) as date'),
            \DB::raw('SUM(earning) as total_earning'),
            \DB::raw('SUM(collection) as total_collection')
        )
        ->orderBy('date', 'ASC')
        ->get();

    $totalEarningWeek = $earnings->sum('total_earning');

    // Generate daily earnings report
    $response = [];
    $currentDate = clone $startDate;

    while ($currentDate->lte($endDate)) {
        $date = $currentDate->toDateString();
        $earningData = $earnings->firstWhere('date', $date);

        $response[] = [
            'date' => $date,
            'total_earning' => $earningData->total_earning ?? 0,

        ];

        $currentDate->addDay();
    }

    return response()->json([
        'total_earning_week' => $totalEarningWeek,
        'data' => $response
    ]);
}

public function delivery_boy_banner()
{
    $banners = BusinessSetting::where('type', 'delivery_boy_banner')->value('value');

    $banners = $banners ? explode(',', $banners) : [];

    $deliveryBoyBanners = array_map('uploaded_asset', $banners);

    return response()->json([
        'banners' => $deliveryBoyBanners
    ]);
}

 public function paymentBonusesHistory(Request $request)
{
    $userId = $request->user()->id; 

    $payments = \DB::table('delivery_boy_payments')
        ->where('user_id', $userId)
        ->orderByDesc('created_at')
        ->get(['created_at', 'payment', 'payment_type']);

    $paymentList = $payments->filter(function ($row) {
        return $row->payment_type === 'earning';
    })->map(function ($row) {
        return [
            'date_time' => Carbon::parse($row->created_at)->format('d-m-Y h:i A'),
            'amount'    => (float) $row->payment,
        ];
    })->values();

    $bonusList = $payments->filter(function ($row) {
        return $row->payment_type === 'bonus';
    })->map(function ($row) {
        return [
            'date_time' => Carbon::parse($row->created_at)->format('d-m-Y h:i A'),
            'amount'    => (float) $row->payment,
        ];
    })->values();

    return response()->json([
        'status'  => true,
        'message' => 'Payment & bonus history fetched successfully',
        'data'    => [
            'payment'       => $paymentList,
            'bonus'         => $bonusList,
            'total_payment' => round($payments->where('payment_type', 'earning')->sum('payment'), 2),
            'total_bonus'   => round($payments->where('payment_type', 'bonus')->sum('payment'), 2),
        ],
    ], 200);
}


public function timesheet(Request $request)
{
    $user = $request->user();
    $deliveryBoy = $user->deliveryBoy;

    if (!$deliveryBoy) {
        return response()->json([
            'status'  => 'failed',
            'message' => 'Delivery boy not found',
        ], 404);
    }

    $tz = 'Asia/Karachi';

    // Local range (default: current month → today)
    $fromLocal = $request->query('from')
        ? Carbon::parse($request->query('from').' 00:00:00', $tz)
        : Carbon::now($tz)->startOfMonth();

    $toLocal = $request->query('to')
        ? Carbon::parse($request->query('to').' 23:59:59', $tz)
        : Carbon::now($tz)->endOfDay();

    // Convert to UTC for querying (stored in UTC)
    $fromUtc = $fromLocal->copy()->utc();
    $toUtc   = $toLocal->copy()->utc();

    // Closed sessions only and exclude sessions ending at midnight
    $sessions = DeliveryBoyShiftHistory::where('user_id', $user->id)
        ->whereNotNull('end_at')
        ->whereTime('end_at', '!=', '00:00:00')
        ->where(function ($q) use ($fromUtc, $toUtc) {
            $q->whereBetween('start_at', [$fromUtc, $toUtc])
              ->orWhereBetween('end_at',   [$fromUtc, $toUtc])
              ->orWhere(function ($q2) use ($fromUtc, $toUtc) {
                  $q2->where('start_at', '<=', $fromUtc)
                     ->where('end_at',   '>=', $toUtc);
              });
        })
        ->orderBy('start_at', 'desc')
        ->get(['id','user_id','start_at','end_at','duration_seconds']);

    $timesheet = [];
    $totalSeconds = 0;

    foreach ($sessions as $s) {
        // Parse as UTC using raw DB strings (avoid casting surprises)
        $startUtc = Carbon::createFromFormat('Y-m-d H:i:s', $s->getRawOriginal('start_at'), 'UTC');
        $endUtc   = Carbon::createFromFormat('Y-m-d H:i:s', $s->getRawOriginal('end_at'),   'UTC');

        // Recompute duration
        $durSecs = $endUtc->diffInSeconds($startUtc);
        $totalSeconds += $durSecs;

        // Convert to local for output
        $startLocal = $startUtc->copy()->setTimezone($tz);
        $endLocal   = $endUtc->copy()->setTimezone($tz);

        $dateKey = $startLocal->format('Y-m-d');
        $timesheet[$dateKey][] = [
            'start_time' => $startLocal->format('H:i:s'),
            'end_time'   => $endLocal->format('H:i:s'),
            'total_time' => $durSecs,
        ];
    }

    // Sort only if we have data
    if (!empty($timesheet)) {
        // Sort entries within each date: latest → oldest by start_time
        foreach ($timesheet as &$entries) {
            usort($entries, function ($a, $b) {
                return strcmp($b['start_time'], $a['start_time']); // desc
            });
        }
        unset($entries);

        // Sort dates: latest → oldest
        krsort($timesheet, SORT_STRING);
    }

    // If no timesheet data, return {} instead of []
    $timesheetPayload = !empty($timesheet) ? $timesheet : (object)[];

    return response()->json([
        'status'  => 'success',
        'message' => 'Timesheet (local time) from '.$fromLocal->format('Y-m-d').' to '.$toLocal->format('Y-m-d'),
        'data'    => [
            'timezone'    => $tz,
            'total_days'  => count($timesheet),
            'total_time'  => $totalSeconds,
            'timesheet'   => $timesheetPayload,
        ],
    ], 200);
}


public function collectionHistory(Request $request)
{
    $user = $request->user();
    $deliveryBoy = $user->deliveryBoy;

    if (!$deliveryBoy) {
        return response()->json([
            'status'  => 'failed',
            'message' => 'Delivery boy not found',
        ], 404);
    }

    $collectionHistory = DeliveryBoyCollection::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->get();

    $historyData = [];

    foreach ($collectionHistory as $historyItem) {
        $historyData[] = [
            'amount' => format_price(convert_price($historyItem->collection_amount)),
            
            'date_time' => $historyItem->created_at->toDateTimeString(), 
        ];
    }

    return response()->json([
        'status'  => 'success',
        'message' => 'Delivery Boy Collection History',
        'data'    => $historyData, 
    ], 200);
}



}
