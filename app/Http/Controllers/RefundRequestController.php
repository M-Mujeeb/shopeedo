<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Models\ClubPoint;
use App\Models\RefundRequest;
use App\Models\OrderDetail;
use App\Models\Shop;
use App\Models\Wallet;
use App\Models\User;
use Artisan;
use Auth;
use App\Mail\SecondEmailVerifyMailManager;
use App\Models\Order;
use App\Models\Product;
use App\Models\Seller;
use Mail;

class RefundRequestController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:view_refund_requests'])->only('admin_index');
        $this->middleware(['permission:view_approved_refund_requests'])->only('paid_index');
        $this->middleware(['permission:view_rejected_refund_requests'])->only('rejected_index');
        $this->middleware(['permission:refund_request_configuration'])->only('refund_config');
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    //Store Customer Refund Request
    public function request_store(Request $request, $id)
    {
         $validated = $request->validate([
        'reason'      => ['required','string','min:3'],
        'attachments' => ['nullable','string'],
    ]);
        $order_detail = OrderDetail::where('id', $id)->first();
        $product = Product::where('id', $order_detail->product_id)->first();
        $order = Order::where('id', $order_detail->order_id)->first();
        $customer = User::where('id', $order->user_id)->first();
        $seller = User::where('id',$order_detail->seller_id)->first();
        $refund = new RefundRequest;
        $refund->user_id = Auth::user()->id;
        $refund->order_id = $order_detail->order_id;
        $refund->order_detail_id = $order_detail->id;
        $refund->seller_id = $order_detail->seller_id;
        $refund->seller_approval = 1;
        $refund->reason =  $validated['reason'];
        $refund->attachments = $validated['attachments'] ?? '';
        $refund->admin_approval = 0;
        $refund->admin_seen = 0;
        $refund->refund_amount = $order_detail->price + $order_detail->tax;
        $refund->refund_status = 0;
        if ($refund->save()) {

            $array['view'] = 'emails.verification';
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['subject'] = translate('Important Notice: Product Return Instructions ');

        $array['content'] = '
    Dear Seller,<br><br>
    
    We hope this message finds you well. We are writing to inform you about a product return request from a recent order on Shopeedo. Please review the details below and take the necessary steps to process the return.<br><br>
    
    <strong>Return Details:</strong><br>
    ● Order Number: ' . $order->code. '<br>
    ● Product Name: ' .  $product->name . '<br>
    ● Quantity: ' . $order_detail->quantity . '<br>
    ● Return Reason: ' . $refund->reason . '<br>
    ● Customer\'s Name: ' . $customer->name . '<br><br>
    
    <strong>Return Instructions:</strong><br>
    1. Contact the Customer: Reach out to the customer to acknowledge the return request and provide any necessary assistance or clarification.<br>
    2. Return Shipping Label: Generate and provide a return shipping label to the customer. This can be done through your seller dashboard.<br>
    3. Inspect the Returned Product: Once you receive the returned product, inspect it to ensure it meets the return policy criteria.<br>
    4. Refund or Replacement: Based on your return policy, process a refund or send a replacement product to the customer.<br><br>
    
    <strong>Important Notes:</strong><br>
    ● Ensure the return process is handled promptly to maintain customer satisfaction.<br>
    ● Update the return status in your seller dashboard to keep track of the return process.<br>
    ● If there are any discrepancies or issues with the returned product, contact our support team immediately.<br><br>
    
    <strong>Customer Support:</strong><br>
    If you have any questions or need assistance with the return process, please contact our support team at [Support Email]. We are here to help you manage returns efficiently and effectively.<br><br>
    
    Thank you for your attention to this matter and for your continued partnership with Shopeedo. We appreciate your dedication to providing excellent service to our customers.<br><br>
    
    Best regards,<br>
    The Shopeedo Team
';

     

        // Mail::to($seller->email)->queue(new SecondEmailVerifyMailManager($array));
            flash( translate("Refund Request has been sent successfully") )->success();
            return redirect()->route('purchase_history.index');
        }
        else {
            flash( translate("Something went wrong") )->error();
            return back();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function vendor_index()
    {
        $refunds = RefundRequest::where('seller_id', Auth::user()->id)->latest()->paginate(10);
        
        return view('refund_request.frontend.recieved_refund_request.index', compact('refunds'));
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function customer_index()
    {
        $refunds = RefundRequest::where('user_id', Auth::user()->id)->latest()->paginate(10);
        return view('refund_request.frontend.refund_request.index', compact('refunds'));
    }

    //Set the Refund configuration
    public function refund_config()
    {
        return view('refund_request.config');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function refund_time_update(Request $request)
    {
        $business_settings = BusinessSetting::where('type', $request->type)->first();
        if ($business_settings != null) {
            $business_settings->value = $request->value;
            $business_settings->save();
        }
        else {
            $business_settings = new BusinessSetting;
            $business_settings->type = $request->type;
            $business_settings->value = $request->value;
            $business_settings->save();
        }
        Artisan::call('cache:clear');
        flash( translate("Refund Request sending time has been updated successfully") )->success();
        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function refund_sticker_update(Request $request)
    {
        $business_settings = BusinessSetting::where('type', $request->type)->first();
        if ($business_settings != null) {
            $business_settings->value = $request->logo;
            $business_settings->save();
        }
        else {
            $business_settings = new BusinessSetting;
            $business_settings->type = $request->type;
            $business_settings->value = $request->logo;
            $business_settings->save();
        }
        Artisan::call('cache:clear');
        flash( translate("Refund Sticker has been updated successfully"))->success();
        return back();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_index()
    {
        $refunds = RefundRequest::where('refund_status', 0)->latest()->paginate(15);
        return view('refund_request.index', compact('refunds'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function paid_index()
    {
        $refunds = RefundRequest::where('refund_status', 1)->latest()->paginate(15);
        return view('refund_request.paid_refund', compact('refunds'));
    }

    public function rejected_index()
    {
        $refunds = RefundRequest::where('refund_status', 2)->latest()->paginate(15);
        return view('refund_request.rejected_refund', compact('refunds'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function request_approval_vendor(Request $request)
    {
        $refund = RefundRequest::findOrFail($request->el);
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            $refund->seller_approval = 1;
            $refund->admin_approval = 1;
        }
        else {
            $refund->seller_approval = 1;
        }

        if ($refund->save()) {
            return 1;
        }
        else {
            return 0;
        }
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function refund_pay(Request $request)
    {
        $refund = RefundRequest::findOrFail($request->refund_id);
        if ($refund->seller_approval == 1) {
            $seller = Shop::where('user_id', $refund->seller_id)->first();
            if ($seller != null) {
                $seller->admin_to_pay -= $request->refund_amount;
            }
            $seller->save();
        }
        $refund->refund_amount = $request->refund_amount;
        $refund->save();

        $refund_amount = $request->refund_amount;

        // Club Point conversion check
        if (addon_is_activated('club_point')) {
            $club_point = ClubPoint::where('order_id', $refund->order_id)->first();
            if($club_point != null){
                $club_point_details = $club_point->club_point_details->where('product_id',$refund->orderDetail->product->id)->first();
                
                if($club_point->convert_status == 1 ){
                    $refund_amount -= $club_point_details->converted_amount;  
                }
                else{
                    $club_point_details->refunded = 1;
                    $club_point_details->save();
                }
            }
        }

        $wallet = new Wallet;
        $wallet->user_id = $refund->user_id;
        $wallet->amount = $refund_amount;
        $wallet->payment_method = 'Refund';
        $wallet->payment_details = 'Product Money Refund';
        $wallet->save();
        $user = User::findOrFail($refund->user_id);
        $user->balance += $refund_amount;
        $user->save();
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            $refund->admin_approval = 1;
            $refund->refund_status = 1;
        }

        if ($refund->save()) {
            flash(translate('Refund has been sent successfully.'))->success();
        }
        else {
            flash(translate('Something went wrong.'))->error();
        }
        return back();
    }

    public function reject_refund_request(Request $request){
      $refund = RefundRequest::findOrFail($request->refund_id);
      if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
          $refund->admin_approval = 2;
          $refund->refund_status  = 2;
          $refund->reject_reason  = $request->reject_reason;
      }
      else{
          $refund->seller_approval = 2;
          $refund->reject_reason  = $request->reject_reason;
      }
      
      if ($refund->save()) {
          flash(translate('Refund request rejected successfully.'))->success();
          return back();
      }
      else {
          return back();
      }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function refund_request_send_page($id)
    {
        $order_detail = OrderDetail::findOrFail($id);
        if ($order_detail->product != null && $order_detail->product->refundable == 1) {
            return view('refund_request.frontend.refund_request.create', compact('order_detail'));
        }
        else {
            return back();
        }
    }

    /**
     * Show the form for view the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //Shows the refund reason
    public function reason_view($id)
    {
        $refund = RefundRequest::findOrFail($id);
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            if ($refund->orderDetail != null) {
                $refund->admin_seen = 1;
                $refund->save();
                return view('refund_request.reason', compact('refund'));
            }
        }
        else {
            return view('refund_request.frontend.refund_request.reason', compact('refund'));
        }
    }

    public function reject_reason_view($id)
    {
        $refund = RefundRequest::findOrFail($id);
        return $refund->reject_reason;
    }

}
