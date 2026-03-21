<?php

namespace App\Repository;

use App\Entity\Experience;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ExperienceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Experience::class);
    }

    public function findAllWithTasks(): array
    {
        return $this->createQueryBuilder('e')
            ->addSelect('t')
            ->leftJoin('e.tasks', 't')
            ->orderBy('e.startedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}