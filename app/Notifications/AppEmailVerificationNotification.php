<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use App\Mail\EmailManager;
use Auth;
use App\Mail\SecondEmailVerifyMailManager;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class AppEmailVerificationNotification extends Notification
{
    use Queueable;

    public function __construct()
    {
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // $array['view'] = 'emails.app_verification';
        // $array['subject'] = translate('Email Verification');
        // $array['content'] = translate('Please enter the code:').' '.$notifiable->verification_code;
        $array['view'] = 'emails.verification';
        $array['subject'] = translate('Password Reset');
        $array['content'] = '
    <strong style="color:#7D9A40">Important: Keep this information private, even from shopeedo!</strong>
    <br>We received a request to reset your shopeedo password. If this was you, please enter the 4-digit code on the email verification page:<br><br>

    <div style="text-align:center;">
        <span style="font-size:35px !important; font-weight:700;letter-spacing: 16px">' .$notifiable->verification_code . '</span>
    </div>
    <br><br>

    <strong style="color:#7D9A40">For your security, do not share this code with anyone under any circumstances.</strong>
    <br><br>
    If you did not request a password reset, you can safely ignore this email.<br><br>
    Need assistance? Visit our Help Center.<br><br>
    Best regards,<br>
    The shopeedo Team
';
    // return Mail::to($notifiable->email)->queue(new SecondEmailVerifyMailManager($array));

        return (new MailMessage)
            ->view('emails.verification', ['array' => $array])
            ->subject(translate('Email Verification - ').env('APP_NAME'));
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
