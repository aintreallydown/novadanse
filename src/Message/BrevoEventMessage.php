<?php

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
final class BrevoEventMessage
{
    public function __construct(
        public readonly string $token,
        public readonly string $event,
        public readonly string $email,
        public readonly ?array $eventProperties,
        public readonly ?array $contactProperties
    ) {}

    public function getToken(): string
    {
        return $this->token;
    }

    public function getEvent(): string
    {
        return $this->event;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getEventProperties(): ?array
    {
        return $this->eventProperties;
    }

    public function getContactProperties(): ?array
    {
        return $this->contactProperties;
    }
}
