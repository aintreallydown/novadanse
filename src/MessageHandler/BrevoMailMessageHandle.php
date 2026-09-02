<?php

namespace App\MessageHandler;

use App\Message\BrevoMailMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsMessageHandler]
final class BrevoMailMessageHandler
{

    public function __construct(
        private HttpClientInterface $client,
    ) {}

    public function __invoke(BrevoMailMessage $message): void
    {
        $token = $message->getToken();
        $templateId = $message->getTemplateId();
        $from = $message->getFrom();
        $to = $message->getTo();
        $params = $message->getParams() ?? null;

        $json = [
            'sender' => [
                'name' => 'Nøva Danse',
                'email' => $from,
            ],
            'to' => $to,
            'templateId' => $templateId
        ];

        if ($params) {
            $json['params'] = $params;
        }

        /* Send Brevo event */
        $this->client->request(
            'POST',
            "https://api.brevo.com/v3/smtp/email",
            [
                'headers' => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    "api-key: {$token}",
                ],
                'json' => $json
            ]
        );
    }
}
