<?php

namespace App\MessageHandler;

use App\Message\BrevoEventMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsMessageHandler]
final class BrevoEventMessageHandler
{
    private $client;

    public function __construct(
        HttpClientInterface $client,
    ) {
        $this->client = $client;
    }

    public function __invoke(BrevoEventMessage $message): void
    {
        $token = $message->getToken();
        $event = $message->getEvent();
        $email = $message->getEmail();
        $eventProperties = $message->getEventProperties() ?? false;
        $contactProperties = $message->getContactProperties() ?? false;

        $json = [
            'event_name' => $event,
            'identifiers' => [
                'email_id' => $email,
            ]
        ];

        if ($eventProperties) {
            $json['event_properties'] = $eventProperties;
        }

        if ($contactProperties) {
            $json['contact_properties'] = $contactProperties;
        }

        /* Send Brevo event */
        $this->client->request(
            'POST',
            "https://api.brevo.com/v3/events",
            [
                'headers' => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    "api-key: $token",
                ],
                'json' => $json
            ]
        );
    }
}
