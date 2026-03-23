<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Contact;
use App\Message\SendContactEmail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @implements ProcessorInterface<Contact, Contact>
 */
final class ContactStateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageBusInterface $bus,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Contact
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();

        $name = $data->getName() ?? throw new \InvalidArgumentException('Contact name is required.');
        $email = $data->getEmail() ?? throw new \InvalidArgumentException('Contact email is required.');
        $message = $data->getMessage() ?? throw new \InvalidArgumentException('Contact message is required.');
        $id = $data->getId() ?? throw new \InvalidArgumentException('Contact id is missing.');

        $this->bus->dispatch(new SendContactEmail($name, $email, $message, $id));

        return $data;
    }
}
