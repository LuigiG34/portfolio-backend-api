<?php

namespace App\EventSubscriber;

use App\Entity\Contact;
use App\Message\SendContactEmail;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;

class MessengerFailureSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ContactRepository $contactRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageFailedEvent::class => 'onMessageFailed',
        ];
    }

    public function onMessageFailed(WorkerMessageFailedEvent $event): void
    {
        if ($event->willRetry()) {
            return;
        }

        $message = $event->getEnvelope()->getMessage();

        if (!$message instanceof SendContactEmail) {
            return;
        }

        $contact = $this->contactRepository->find($message->getContactId());
        if ($contact instanceof Contact) {
            $contact->setStatus('failed');
            $this->entityManager->flush();
        }
    }
}
