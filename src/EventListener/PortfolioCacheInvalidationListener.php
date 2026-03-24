<?php

namespace App\EventListener;

use App\Entity\Degree;
use App\Entity\Experience;
use App\Entity\Project;
use App\Entity\Skill;
use App\Entity\Technology;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[AsEntityListener(event: Events::postPersist, entity: Project::class)]
#[AsEntityListener(event: Events::postUpdate, entity: Project::class)]
#[AsEntityListener(event: Events::postRemove, entity: Project::class)]
#[AsEntityListener(event: Events::postPersist, entity: Experience::class)]
#[AsEntityListener(event: Events::postUpdate, entity: Experience::class)]
#[AsEntityListener(event: Events::postRemove, entity: Experience::class)]
#[AsEntityListener(event: Events::postPersist, entity: Skill::class)]
#[AsEntityListener(event: Events::postUpdate, entity: Skill::class)]
#[AsEntityListener(event: Events::postRemove, entity: Skill::class)]
#[AsEntityListener(event: Events::postPersist, entity: Degree::class)]
#[AsEntityListener(event: Events::postUpdate, entity: Degree::class)]
#[AsEntityListener(event: Events::postRemove, entity: Degree::class)]
#[AsEntityListener(event: Events::postPersist, entity: Technology::class)]
#[AsEntityListener(event: Events::postUpdate, entity: Technology::class)]
#[AsEntityListener(event: Events::postRemove, entity: Technology::class)]
class PortfolioCacheInvalidationListener
{
    public function __construct(
        private readonly TagAwareCacheInterface $portfolioCache,
    ) {
    }

    public function postPersist(): void
    {
        $this->invalidate();
    }

    public function postUpdate(): void
    {
        $this->invalidate();
    }

    public function postRemove(): void
    {
        $this->invalidate();
    }

    private function invalidate(): void
    {
        $this->portfolioCache->invalidateTags(['portfolio']);
    }
}
