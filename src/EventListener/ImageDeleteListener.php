<?php

namespace App\EventListener;

use App\Entity\Image;
use App\Service\ImageUploadService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preRemove, entity: Image::class)]
class ImageDeleteListener
{
    public function __construct(
        private readonly ImageUploadService $imageUploadService,
    ) {}

    public function preRemove(Image $image): void
    {
        $this->imageUploadService->delete($image);
    }
}