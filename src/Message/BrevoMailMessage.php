<?php

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
final class BrevoMailMessage
{
    public function __construct(
        public readonly string $token,
        public readonly int $templateId,
        public readonly ?string $from,
        public readonly ?array $to,
        public readonly ?array $params,
    ) {}

    public function getToken(): string
    {
        return $this->token;
    }

    public function getTemplateId(): int
    {
        return $this->templateId;
    }

    public function getFrom(): ?string
    {
        return $this->from;
    }

    public function getTo(): ?array
    {
        return $this->to;
    }

    public function getParams(): ?array
    {
        return $this->params;
    }
}
