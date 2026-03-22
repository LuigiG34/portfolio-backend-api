<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function findAllWithTechnologies(): array
    {
        return $this->createQueryBuilder('p')
            ->addSelect('t', 'i')
            ->leftJoin('p.technologies', 't')
            ->leftJoin('p.image', 'i')
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
