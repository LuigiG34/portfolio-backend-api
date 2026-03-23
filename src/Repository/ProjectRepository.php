<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 * */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    /** @return Project[] */
    public function findAllWithTechnologies(): array
    {
        /** @var Project[] $result */
        $result = $this->createQueryBuilder('p')
            ->addSelect('t', 'i')
            ->leftJoin('p.technologies', 't')
            ->leftJoin('p.image', 'i')
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();

        return $result;
    }
}
