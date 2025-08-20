<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Utility\NotificationUtility;


class CashOnDeliveryController extends Controller
{
    public function pay()
    {
        flash(translate("Your order has been placed successfully"))->success();

      
        $order = Order::findOrFail(session()->get('order_id'));

        // dd($order);

        // foreach ($combined_order->orders as $order) {
          if($order->notified == 0) {
              NotificationUtility::sendOrderPlacedNotification($order);
              $order->notified = 1;
              if($order->type == 'Taiz'){
                  $order->delivery_status = 'confirmed';
              }else{
                  $order->delivery_status = 'pending';
              }

              $order->save();
          }
      // }
          session()->put('thankYou', true);
        // flash(translate('Your order has been placed successfully.'))->success();
                return redirect()->route('purchase_history.details', encrypt(session()->get('order_id')));
        // return redirect()->route('order_confirmed');
    }
}
