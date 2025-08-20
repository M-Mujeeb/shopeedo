<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:seller_payment_history'])->only('payment_histories');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     $payments = Payment::where('seller_id', Auth::user()->seller->id)->paginate(9);
    //     return view('seller.payment_history', compact('payments'));
    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function payment_histories(Request $request)
    {
        $payments = Payment::orderBy('created_at', 'desc')->paginate(15);
        return view('backend.sellers.payment_histories.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find(decrypt($id));
        $payments = Payment::where('seller_id', $user->id)->orderBy('created_at', 'desc')->get();
        if($payments->count() > 0){
            return view('backend.sellers.payment', compact('payments', 'user'));
        }
        flash(translate('No payment history available for this seller'))->warning();
        return back();
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function handleIPN(Request $request)
    {
        // Log the incoming request for debugging
        \Log::info('IPN Notification Received:', $request->all());

        // Validate the request
        $validatedData = $request->validate([
            'url' => 'required|url',
        ]);

        // Fetch transaction details from the provided URL
        $response = Http::get($validatedData['url']);

        if ($response->successful()) {
            $transactionDetails = $response->json();

            // Get the Combine Order ID from the response
            $combineOrderId = $transactionDetails['TransactionReferenceNumber'];
            $transactionStatus = $transactionDetails['TransactionStatus'];

            // Find all orders associated with the Combine Order ID
            $orders = Order::where('combine_order_id', $combineOrderId)->get();

            if ($orders->isEmpty()) {
                \Log::error('No orders found for the given CombineOrderId', ['combine_order_id' => $combineOrderId]);
                return response()->json(['message' => 'No orders found for the given CombineOrderId'], 404);
            }

            // Update payment status for each order
            foreach ($orders as $order) {
                if ($transactionStatus === 'Paid') {
                    $order->update(['payment_status' => 'Paid']);
                } elseif ($transactionStatus === 'Failed') {
                    $order->update(['payment_status' => 'Failed']);
                } else {
                    $order->update(['payment_status' => 'Pending']);
                }
            }

            \Log::info('Payment statuses updated successfully for CombineOrderId', ['combine_order_id' => $combineOrderId]);

            return response()->json(['message' => 'IPN processed successfully'], 200);
        }

        \Log::error('Failed to fetch transaction details from APG', ['url' => $validatedData['url']]);
        return response()->json(['message' => 'Failed to process IPN'], 500);
    }
}
