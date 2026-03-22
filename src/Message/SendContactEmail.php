<?php

namespace App\Message;

final class SendContactEmail
{
    public function __construct(
        private readonly string $name,
        private readonly string $email,
        private readonly string $message,
        private readonly int $contactId,
    ) {}

    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function getMessage(): string { return $this->message; }
    public function getContactId(): int { return $this->contactId; }
}