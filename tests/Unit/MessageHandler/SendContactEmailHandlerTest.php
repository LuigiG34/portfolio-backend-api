<?php

namespace App\Tests\Unit\MessageHandler;

use App\Entity\Contact;
use App\Message\SendContactEmail;
use App\MessageHandler\SendContactEmailHandler;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;

class SendContactEmailHandlerTest extends TestCase
{
    public function testHandlerSendsEmailAndUpdatesStatusToSent(): void
    {
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects($this->once())->method('send');

        $contact = new Contact();
        $contact->setName('John Doe');
        $contact->setEmail('john@example.com');
        $contact->setMessage('Hello!');

        $contactRepository = $this->createMock(ContactRepository::class);
        $contactRepository
            ->expects($this->once())
            ->method('find')
            ->with(42)
            ->willReturn($contact);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())->method('flush');

        $handler = new SendContactEmailHandler($mailer, $contactRepository, $entityManager);
        $handler(new SendContactEmail('John Doe', 'john@example.com', 'Hello!', 42));

        $this->assertSame('sent', $contact->getStatus());
    }

    public function testHandlerDoesNotFailIfContactNotFound(): void
    {
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects($this->once())->method('send');

        $contactRepository = $this->createMock(ContactRepository::class);
        $contactRepository->method('find')->willReturn(null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->never())->method('flush');

        $handler = new SendContactEmailHandler($mailer, $contactRepository, $entityManager);
        $handler(new SendContactEmail('John Doe', 'john@example.com', 'Hello!', 99));

        $this->assertTrue(true);
    }
}
