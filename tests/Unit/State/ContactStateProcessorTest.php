<?php

namespace App\Tests\Unit\State;

use ApiPlatform\Metadata\Post;
use App\Entity\Contact;
use App\Message\SendContactEmail;
use App\State\ContactStateProcessor;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class ContactStateProcessorTest extends TestCase
{
    private function createContact(): Contact
    {
        $contact = new Contact();
        $contact->setName('John Doe');
        $contact->setEmail('john@example.com');
        $contact->setMessage('Hello!');

        // Force an id since we're not persisting to a real DB
        $reflection = new \ReflectionClass($contact);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($contact, 42);

        return $contact;
    }

    public function testProcessPersistsContactAndDispatchesMessage(): void
    {
        $contact = $this->createContact();

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())->method('persist')->with($contact);
        $entityManager->expects($this->once())->method('flush');

        $bus = $this->createMock(MessageBusInterface::class);
        $bus->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(SendContactEmail::class))
            ->willReturn(new Envelope(new SendContactEmail('John Doe', 'john@example.com', 'Hello!', 0)));

        $processor = new ContactStateProcessor($entityManager, $bus);
        $result = $processor->process($contact, new Post());

        $this->assertSame($contact, $result);
    }

    public function testProcessReturnsContactEntity(): void
    {
        $contact = $this->createContact();

        $entityManager = $this->createMock(EntityManagerInterface::class);

        $bus = $this->createMock(MessageBusInterface::class);
        $bus->method('dispatch')
            ->willReturn(new Envelope(new SendContactEmail('John Doe', 'john@example.com', 'Hello!', 0)));

        $processor = new ContactStateProcessor($entityManager, $bus);
        $result = $processor->process($contact, new Post());

        $this->assertInstanceOf(Contact::class, $result);
    }
}
