<?php

namespace App\Tests\Unit\EventSubscriber;

use App\Entity\Contact;
use App\EventSubscriber\MessengerFailureSubscriber;
use App\Message\SendContactEmail;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;

class MessengerFailureSubscriberTest extends TestCase
{
    public function testStatusUpdatedToFailedWhenNoMoreRetries(): void
    {
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

        $subscriber = new MessengerFailureSubscriber($contactRepository, $entityManager);

        $message = new SendContactEmail('John Doe', 'john@example.com', 'Hello!', 42);
        $envelope = new Envelope($message);
        $event = new WorkerMessageFailedEvent($envelope, 'async', new \Exception('fail'));

        $subscriber->onMessageFailed($event);

        $this->assertSame('failed', $contact->getStatus());
    }

    public function testStatusNotUpdatedWhenWillRetry(): void
    {
        $contactRepository = $this->createMock(ContactRepository::class);
        $contactRepository->expects($this->never())->method('find');

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->never())->method('flush');

        $subscriber = new MessengerFailureSubscriber($contactRepository, $entityManager);

        $message = new SendContactEmail('John Doe', 'john@example.com', 'Hello!', 42);
        $envelope = new Envelope($message);
        $event = new WorkerMessageFailedEvent($envelope, 'async', new \Exception('fail'));
        $event->setForRetry(); // will retry, should not update status

        $subscriber->onMessageFailed($event);

        $this->assertTrue(true);
    }

    public function testIgnoresNonContactMessages(): void
    {
        $contactRepository = $this->createMock(ContactRepository::class);
        $contactRepository->expects($this->never())->method('find');

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->never())->method('flush');

        $subscriber = new MessengerFailureSubscriber($contactRepository, $entityManager);

        $envelope = new Envelope(new \stdClass());
        $event = new WorkerMessageFailedEvent($envelope, 'async', new \Exception('fail'));

        $subscriber->onMessageFailed($event);

        $this->assertTrue(true);
    }

    public function testDoesNotFailIfContactNotFound(): void
    {
        $contactRepository = $this->createMock(ContactRepository::class);
        $contactRepository->method('find')->willReturn(null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->never())->method('flush');

        $subscriber = new MessengerFailureSubscriber($contactRepository, $entityManager);

        $message = new SendContactEmail('John Doe', 'john@example.com', 'Hello!', 99);
        $envelope = new Envelope($message);
        $event = new WorkerMessageFailedEvent($envelope, 'async', new \Exception('fail'));

        $subscriber->onMessageFailed($event);

        $this->assertTrue(true);
    }
}