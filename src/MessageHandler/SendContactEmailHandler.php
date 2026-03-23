<?php

namespace App\MessageHandler;

use App\Entity\Contact;
use App\Message\SendContactEmail;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
final class SendContactEmailHandler
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly ContactRepository $contactRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly string $emailTo,
    ) {
    }

    public function __invoke(SendContactEmail $message): void
    {
        $email = (new Email())
            ->from($message->getEmail())
            ->to($this->emailTo)
            ->subject("Portfolio contact from {$message->getName()}")
            ->html("
                <h2>New contact from your portfolio</h2>
                <p><strong>Name:</strong> {$message->getName()}</p>
                <p><strong>Email:</strong> {$message->getEmail()}</p>
                <p><strong>Message:</strong></p>
                <p>{$message->getMessage()}</p>
            ");

        $this->mailer->send($email);

        $contact = $this->contactRepository->find($message->getContactId());
        if ($contact instanceof Contact) {
            $contact->setStatus('sent');
            $this->entityManager->flush();
        }
    }
}
