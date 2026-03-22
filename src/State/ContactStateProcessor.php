<?php

namespace App\State;

use App\Entity\Contact;
use App\Message\SendContactEmail;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class ContactStateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageBusInterface $bus,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Contact
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();

        $this->bus->dispatch(new SendContactEmail(
            $data->getName(),
            $data->getEmail(),
            $data->getMessage(),
            $data->getId(),
        ));

        return $data;
    }
}