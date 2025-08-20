<?php

namespace App\Http\Controllers\Api\V2\Seller;

use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Services\OrderService ;
use App\Http\Resources\V2\Seller\OrderCollection;
use App\Http\Resources\V2\Seller\OrderItemResource;
use App\Http\Resources\V2\Seller\OrderDetailResource;
use Illuminate\Support\Facades\Config;
use PDF;

use Session;
use App\Models\Currency;
use App\Models\Language;

class OrderController extends Controller
{
    public function getOrderList(Request $request)
{
    $order_query = Order::query();
    if ($request->payment_status != "" || $request->payment_status != null) {
        $order_query->where('payment_status', $request->payment_status);
    }
    if ($request->delivery_status != "" || $request->delivery_status != null) {
        $delivery_status = $request->delivery_status;
        $order_query->whereIn("id", function ($query) use ($delivery_status) {
            $query->select('order_id')
                ->from('order_details')
                ->where('delivery_status', $delivery_status);
        });
    }

    $orders = $order_query->where('seller_id', auth()->user()->id)->with('combinedOrder')->latest()->paginate(10);
    return new OrderCollection($orders);
}

public function getOrderDetails($id)
{
    $order_detail = Order::where('id', $id)
        ->where('seller_id', auth()->user()->id)
        ->with('combinedOrder') // Eager load combined_order relationship
        ->get();
    
    $business_settings = BusinessSetting::where('type', 'platform_fee')->first();
    
    $order_detail->map(function ($order) use ($business_settings) {
        $order->platform_fee = $business_settings ? $business_settings->value : 0; 
        return $order;
    });

    return OrderDetailResource::collection($order_detail);
}


    public function getOrderItems($id)
    {
        $order_id = Order::select('id')->where('id', $id)->where('seller_id', auth()->user()->id)->first();
        $order_query = OrderDetail::where('order_id', $order_id->id);
        


        return  OrderItemResource::collection($order_query->get());
    }

    public function update_delivery_status(Request $request) {
        (new OrderService)->handle_delivery_status($request);
        return $this->success(translate('Delivery status has been changed successfully'));
    }

    public function update_payment_status(Request $request) {
        (new OrderService)->handle_payment_status($request);
        return $this->success(translate('Payment status has been changed successfully'));
    }

    public function getPOSInvoice($order_id)
    {
        $order = Order::findOrFail($order_id);

        $currency_code = Session::get('currency_code',
                              Currency::findOrFail(
                                get_setting('system_default_currency')
                              )->code
                            );
        $language_code = Session::get('locale', Config::get('app.locale'));
        $rtl = Language::where('code', $language_code)->first()->rtl;
        $direction     = $rtl ? 'rtl' : 'ltr';
        $text_align    = $rtl ? 'right' : 'left';
        $not_text_align= $rtl ? 'left'  : 'right';

        $font_family = $this->resolveFontFamily($currency_code, $language_code);

        $config = [
            'mode'        => 'utf-8',
            'format'      => [80, 200],  
            'orientation' => 'P',
        ];
        $pdf = PDF::loadView('backend.invoices.invoice', [
                    'order'          => $order,
                    'font_family'    => $font_family,
                    'direction'      => $direction,
                    'text_align'     => $text_align,
                    'not_text_align' => $not_text_align,
                ], [], $config);

                $filename = 'order-' . $order->code . '.pdf';
                $publicDir = public_path('invoices');
            
                if (! is_dir($publicDir)) {
                    mkdir($publicDir, 0755, true);
                }
            
                file_put_contents($publicDir . '/' . $filename, $pdf->output());
            
                $url = url("public/invoices/{$filename}");

        return response()->json([
            'status'     => true,
            'order_id'   => $order->id,
            'invoice_url'=> $url,
        ], 200);
    }

    /**
     * Move your font‐selection logic here for clarity
     */
    private function resolveFontFamily($currency_code, $language_code)
    {
        // … paste your if/elseif chain …
        return "freeserif"; // fallback
    }
}
