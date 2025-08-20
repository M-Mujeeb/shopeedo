<?php

namespace App\Http\Controllers\Api\V2;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function cashOnDelivery(Request $request)
    {
        $order = new OrderController;
        return $order->store($request);
    }

    public function manualPayment(Request $request)
    {
        $order = new OrderController;
        return $order->store($request);
    }

    public function processAlfalahPayment(Request $request)
{
    // Validate request payload
    // $validated = $request->validate([
    //     'card_number' => 'required|string',
    //     'card_expiry' => 'required|string', 
    //     'card_cvv' => 'required|string',
    //     'amount' => 'required|numeric',
    //     'currency' => 'required|string',
    //     'description' => 'required|string',
    //     'customer_email' => 'required|email',
    //     'customer_phone' => 'required|string',
    //     // Add any additional fields required for order storage
    //     // 'products' => 'required|array', // Example for order details
    // ]);

   
    // $payload = [
    //     'merchant_id' => config('services.bank_alfalah.merchant_id'),
    //     'password' => config('services.bank_alfalah.password'),
    //     'amount' => $validated['amount'],
    //     'currency' => $validated['currency'],
    //     'card_number' => $validated['card_number'],
    //     'card_expiry' => $validated['card_expiry'],
    //     'card_cvv' => $validated['card_cvv'],
    //     'description' => $validated['description'],
    //     'customer_email' => $validated['customer_email'],
    //     'customer_phone' => $validated['customer_phone'],
    // ];

    // try {
       
        // $response = Http::post(config('services.bank_alfalah.api_url'), $payload);

        // if ($response->successful()) {
        
            $order = new OrderController;
           
            return $order->store($request);
            // $storeResponse = $order->store($request);

            // return response()->json([
            //     'status' => 'success',
            //     'message' => 'Payment processed and order placed successfully',
            //     'payment_data' => $response->json(),
            //     'order_data' => $storeResponse,
            // ]);
        // }

        // return response()->json([
        //     'status' => 'error',
        //     'message' => 'Payment failed',
        //     'error' => $response->json(),
        // ], 400);
    // } catch (\Exception $e) {
    //     return response()->json([
    //         'status' => 'error',
    //         'message' => 'An error occurred while processing the payment',
    //         'error' => $e->getMessage(),
    //     ], 500);
    // }
}


}
