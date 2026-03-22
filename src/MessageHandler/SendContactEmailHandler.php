<?php

namespace App\MessageHandler;

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
    ) {}

    public function __invoke(SendContactEmail $message): void
    {
        $email = (new Email())
            ->from($message->getEmail())
            ->to($_ENV['EMAIL_TO'])
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
        if ($contact) {
            $contact->setStatus('sent');
            $this->entityManager->flush();
        }
    }
}