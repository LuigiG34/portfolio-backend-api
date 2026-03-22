<?php

namespace App\Tests\Unit\Message;

use App\Message\SendContactEmail;
use PHPUnit\Framework\TestCase;

class SendContactEmailTest extends TestCase
{
    public function testMessageStoresDataCorrectly(): void
    {
        $message = new SendContactEmail(
            name: 'John Doe',
            email: 'john@example.com',
            message: 'Hello!',
            contactId: 42,
        );

        $this->assertSame('John Doe', $message->getName());
        $this->assertSame('john@example.com', $message->getEmail());
        $this->assertSame('Hello!', $message->getMessage());
        $this->assertSame(42, $message->getContactId());
    }
}