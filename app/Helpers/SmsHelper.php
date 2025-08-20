<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class SmsHelper
{
    public static function sendSms($mobileNumber, $message)
    {
        $apiUrl = 'http://bsms.ufone.com/bsms_v8_api/sendapi-0.3.jsp';
        
        $params = [
            'id' => config('sms.ufone.account_id'),
            'message' => $message,
            'shortcode' => config('sms.ufone.shortcode'),
            'lang' => 'English',
            'mobilenum' => $mobileNumber,
            'password' => config('sms.ufone.api_password'),
            'messagetype' => 'Nontransactional'
        ];

        $response = Http::get($apiUrl, $params);

        $xml = simplexml_load_string($response->body());

        if ((string)$xml->response_id === '0') {
            return true;
        } else {
            \Log::error('SMS sending failed: ' . (string)$xml->response_text);
            return false;
        }
    }
}