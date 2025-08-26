<?php

namespace App\Utility;

use Mail;
use App\Models\User;
use App\Models\OrderDetail;
use App\Models\SmsTemplate;
use App\Models\CombinedOrder;
use App\Mail\InvoiceEmailManager;
use App\Models\FirebaseNotification;
use App\Notifications\OrderNotification;
use App\Mail\SecondEmailVerifyMailManager;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\OTPVerificationController;
use App\Models\BusinessSetting;
use App\Models\Product;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FCMNotification;

use Google\Client as GoogleClient;
use GuzzleHttp\Client as HttpClient;


class NotificationUtility
{
        public static function sendOrderPlacedNotification($order, $request = null)
        {
            $setting = BusinessSetting::where('type', 'platform_fee')->first();


            $combinedOrder = CombinedOrder::where('user_id', $order->user_id)->latest()->limit(1)->first();

            // dd($order->user->email);

            $array['view'] = 'emails.invoice';
            $array['subject'] = translate('A new order has been placed') . ' - ' . $order->code;
            $array['from'] = env('MAIL_FROM_ADDRESS');
            $array['order'] = $order;
            $array['setting'] = $setting->value;
            $array['combinedOrder'] = $combinedOrder->grand_total;

            // if ($order->user->email != null) {
            //     Mail::to($order->user->email)->queue(new InvoiceEmailManager($array));
            // }        
            try {
                if ($order->user->email != null) {
                    Mail::to($order->user->email)->queue(new InvoiceEmailManager($array));
                }
                Mail::to($order->orderDetails->first()->product->user->email)->queue(new InvoiceEmailManager($array));
         

            } catch (\Exception $e) {

        }


        //send email to seller as well

        $seller = User::find($order->seller_id);

        if ($seller) {
            $order_details = OrderDetail::where('order_id',$order->id)->first();
            $product = Product::where('id',$order_details->product_id)->first();
            $shippingAddress = json_decode($order->shipping_address);

            $array['view'] = 'emails.verification';
            $array['from'] = env('MAIL_FROM_ADDRESS');
            $array['subject'] = translate('Important Notice: Product Return Instructions ');

            $array['content'] = '
            Dear,<br><br>

            We hope this message finds you well. We are writing to provide important information regarding the delivery process for your recent sales on Shopeedo.<br><br>

            <strong>Order Details:</strong><br>
            ● Order Number: ' . $order->code . '<br>
            ● Product Name: ' . $product->name . '<br>
            ● Quantity: ' . $order_details->quantity . '<br>
            ● Shipping Address: ' . $shippingAddress->address . '<br><br>

            <strong>Delivery Instructions:</strong><br>
            ● Packaging: Ensure that your products are securely packaged to prevent any damage during transit.<br>
            ● Shipping Label: Attach the provided shipping label to the package. You can download it from your seller dashboard.<br>
            ● Shipping Carrier: Use the designated shipping carrier for this delivery. The carrier details can be found in your seller dashboard.<br>
            ● Dispatch: Schedule a pickup or drop off the package at the nearest shipping carrier location within the next ' . 2-3 . ' days.<br><br>

            <strong>Tracking Information:</strong><br>
            Once the package has been dispatched, please update the tracking information in your seller dashboard. This will allow customers to track their orders and ensure a smooth delivery process.<br><br>

            <strong>Customer Support:</strong><br>
            If you encounter any issues with the delivery process or have any questions, please contact our support team at [Support Email]. We are here to assist you and ensure a successful delivery.<br><br>

            <strong>Additional Notes:</strong><br>
            Timely and accurate delivery is crucial for maintaining a positive seller rating and customer satisfaction.<br>
            If there are any delays or issues with fulfilling this order, please inform us immediately.<br><br>

            Thank you for your attention to this matter and for your continued partnership with Shopeedo. We appreciate your commitment to providing excellent service to our customers.<br><br>

            Best regards,<br>
            The Shopeedo Team
        ';


        Mail::to($seller->email)->queue(new SecondEmailVerifyMailManager($array));

        if ($seller->device_token != null && get_setting('google_firebase') == 1) {
            $firebaseRequest = new \stdClass();
            $firebaseRequest->device_token = $seller->device_token;
            $firebaseRequest->title = "New Order Received!";
            $firebaseRequest->text = "You have received a new order {$order->code}. Please process it.";
            $firebaseRequest->type = "order";
            $firebaseRequest->id = $order->id;
            $firebaseRequest->user_id = $seller->id;

            self::sendFirebaseNotification($firebaseRequest);
        }
        }


        if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'order_placement')->first()->status == 1) {
            try {
                $otpController = new OTPVerificationController;
                $otpController->send_order_code($order);
            } catch (\Exception $e) {

            }
        }

        //sends Notifications to user
        self::sendNotification($order, 'placed');
        if ($request !=null && get_setting('google_firebase') == 1 && $order->user->device_token != null) {
            $request->device_token = $order->user->device_token;
            $request->title = "Order placed !";
            $request->text = "An order {$order->code} has been placed";

            $request->type = "order";
            $request->id = $order->id;
            $request->user_id = $order->user->id;

            self::sendFirebaseNotification($request);
        }
    }

    public static function sendNotification($order, $order_status)
    {
        $adminId = \App\Models\User::where('user_type', 'admin')->first()->id;
        $userIds = array($order->user->id, $order->seller_id);
        if ($order->seller_id != $adminId) {
            array_push($userIds, $adminId);
        }
        $users = User::findMany($userIds);

        $order_notification = array();
        $order_notification['order_id'] = $order->id;
        $order_notification['order_code'] = $order->code;
        $order_notification['user_id'] = $order->user_id;
        $order_notification['seller_id'] = $order->seller_id;
        $order_notification['status'] = $order_status;

        foreach($users as $user){
            $notificationType = get_notification_type('order_'.$order_status.'_'.$user->user_type, 'type');
            if($notificationType != null && $notificationType->status == 1){
                $order_notification['notification_type_id'] = $notificationType->id;
                Notification::send($user, new OrderNotification($order_notification));
            }
        }
    }

    // public static function sendFirebaseNotification($req)
    // {
    //     // $url = 'https://fcm.googleapis.com/fcm/send';

    //     // $fields = array
    //     // (
    //     //     'to' => $req->device_token,
    //     //     'notification' => [
    //     //         'body' => $req->text,
    //     //         'title' => $req->title,
    //     //         'sound' => 'default' /*Default sound*/
    //     //     ],
    //     //     'data' => [
    //     //         'item_type' => $req->type,
    //     //         'item_type_id' => $req->id,
    //     //         'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
    //     //     ]
    //     // );

    //     // //$fields = json_encode($arrayToSend);
    //     // $headers = array(
    //     //     'Authorization: key=' . env('FCM_SERVER_KEY'),
    //     //     'Content-Type: application/json'
    //     // );

    //     // $ch = curl_init();
    //     // curl_setopt($ch, CURLOPT_URL, $url);
    //     // curl_setopt($ch, CURLOPT_POST, true);
    //     // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //     // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    //     // $result = curl_exec($ch);
    //     // curl_close($ch);

    //     // $firebase_notification = new FirebaseNotification;
    //     // $firebase_notification->title = $req->title;
    //     // $firebase_notification->text = $req->text;
    //     // $firebase_notification->item_type = $req->type;
    //     // $firebase_notification->item_type_id = $req->id;
    //     // $firebase_notification->receiver_id = $req->user_id;

    //     // $firebase_notification->save();

    // $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
    // $messaging = $factory->createMessaging();

    // $message = CloudMessage::withTarget('token', $req->device_token)
    //     ->withNotification(FCMNotification::create($req->title, $req->text))
    //     ->withData([
    //         'item_type' => $req->type,
    //         'item_type_id' => $req->id,
    //         'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
    //     ]);

    // try {
    //     // Send the notification
    //    $messaging->send($message);

    //     // Save the notification to the database
    //     $firebase_notification = new FirebaseNotification;
    //     $firebase_notification->title = $req->title;
    //     $firebase_notification->text = $req->text;
    //     $firebase_notification->item_type = $req->type;
    //     $firebase_notification->item_type_id = $req->id;
    //     $firebase_notification->receiver_id = $req->user_id;
    //     $firebase_notification->save();

    //     return true;
    // } catch (\Exception $e) {
    //     \Log::info('Firebase notification error: ' . $e->getMessage());
    //     logger()->error('Firebase notification error: ' . $e->getMessage());
    //     return false;
    // }
    // }


//     public static function sendFirebaseNotification($req)
// {
//     try {
//         $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
//         $messaging = $factory->createMessaging();

//         // Create the notification
//         $message = CloudMessage::withTarget('token', $req->device_token)
//             ->withNotification(FCMNotification::create($req->title, $req->text))
//             ->withData([
//                 'item_type' => $req->type,
//                 'item_type_id' => (string)$req->id, 
//                 'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
//             ]);

//         // Send the notification
//         $result = $messaging->send($message);

//         // Save the notification to the database
//         $firebase_notification = new FirebaseNotification;
//         $firebase_notification->title = $req->title;
//         $firebase_notification->text = $req->text;
//         $firebase_notification->item_type = $req->type;
//         $firebase_notification->item_type_id = $req->id;
//         $firebase_notification->receiver_id = $req->user_id;
//         $firebase_notification->save();

//         \Log::info("Firebase notification successfully sent to device: {$req->device_token}");
//         return true;
//     } catch (\Exception $e) {
//         \Log::error('Firebase notification error: ' . $e->getMessage());
//         \Log::error('Error trace: ' . $e->getTraceAsString());
//         return false;
//     }
// }

public static function sendFirebaseNotification($req)
    {
        $forType = isset($req->for_type) ? $req->for_type : '';
        try {
            $credentialsPath = storage_path(
            $forType === 'seller'
                ? 'app/firebase/seller_credentials.json'
                : 'app/firebase/firebase_credentials.json'
        );
            $accessToken = self::getGoogleAccessToken($credentialsPath);
            
            if (!$accessToken) {
                \Log::error('Failed to get Google access token');
                return false;
            }

            $message = [
    'message' => [
        'token' => $req->device_token,
        'notification' => [
            'title' => $req->title,
            'body' => $req->text,
        ],
        'data' => [
            'item_type' => $req->type,
            'item_type_id' => (string)$req->id,
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        ],
        'android' => [
            'priority' => 'high',
        ],
        'apns' => [
            'headers' => [
                'apns-priority' => '10', // High priority for iOS
            ],
            'payload' => [
                'aps' => [
                    'content-available' => 1,
                    'mutable-content' => 1,
                    'alert' => [
                        'title' => $req->title,
                        'body' => $req->text,
                    ],
                    'sound' => 'default', // optional, but helps deliver faster
                ],
            ],
        ],
    ],
];


            $client = new HttpClient();
            $response = $client->post('https://fcm.googleapis.com/v1/projects/shopeedo-app/messages:send', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $message,
            ]);

            $firebase_notification = new FirebaseNotification;
            $firebase_notification->title = $req->title;
            $firebase_notification->text = $req->text;
            $firebase_notification->item_type = $req->type;
            $firebase_notification->item_type_id = $req->id;
            $firebase_notification->receiver_id = $req->user_id;
            $firebase_notification->save();

            \Log::info("Firebase notification successfully sent to device: {$req->device_token}");
            return true;
        } catch (\Exception $e) {
            \Log::error('Firebase notification error: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());
            return false;
        }
    }

      private static function getGoogleAccessToken($credentialsPath)
    {
        try {
        
            $credentials = json_decode(file_get_contents($credentialsPath), true);

            $client = new GoogleClient();
            $client->setAuthConfig($credentials);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

            $client->fetchAccessTokenWithAssertion();
            $token = $client->getAccessToken();

            return $token['access_token'] ?? null;
        } catch (\Exception $e) {
            \Log::error('Failed to get Google access token: ' . $e->getMessage());
            return null;
        }
    }

}
