<?php

namespace App\Services;

use \Mailjet\Resources;
use Illuminate\Support\Facades\View;

class MailjetAuthMailer
{
    protected $mj;
    protected $fromEmail;
    protected $fromName;

    public function __construct()
    {
        $this->mj = new \Mailjet\Client(env('MAILJET_APIKEY'), env('MAILJET_APISECRET'), true, ['version' => 'v3.1']);
        $this->fromEmail = env('MAIL_FROM_ADDRESS');
        $this->fromName = env('MAIL_FROM_NAME', 'Shopeedo');
    }

    public function send(array $array)
    {
        $to = $array['to'];
        $subject = $array['subject'] ?? 'Shopeedo Notification';
        $templateId = $array['template_id'] ?? null;
        $variables = $array['variables'] ?? [];

        if ($templateId) {
        $message = [
            'From' => [
                'Email' => $this->fromEmail,
                'Name' => $this->fromName
            ],
            'To' => [[ 'Email' => $to ]],
            'TemplateID' => (int) $templateId,
            'TemplateLanguage' => true,
            'Subject' => $subject,
        ];

        // Only add Variables if not empty
        if (!empty($variables)) {
            $message['Variables'] = $variables;
        }

        $body = ['Messages' => [ $message ]];
    } else {
            // Render Blade view to HTMLPart
            $html = View::make($array['view'], [
                'array' => [
                    'subject' => $subject,
                    'content' => $array['content'],
                    'link' => $array['link'] ?? null,
                ]
            ])->render();

            $body = [
                'Messages' => [
                    [
                        'From' => [
                            'Email' => $this->fromEmail,
                            'Name' => $this->fromName
                        ],
                        'To' => [[ 'Email' => $to ]],
                        'Subject' => $subject,
                        'HTMLPart' => $html
                    ]
                ]
            ];
        }

        return $this->mj->post(Resources::$Email, ['body' => $body]);
    }
}
