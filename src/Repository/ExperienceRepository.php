<?php

namespace App\Repository;

use App\Entity\Experience;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** 
 * @extends ServiceEntityRepository<Experience> 
 * */
class ExperienceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Experience::class);
    }

    /** @return Experience[] */
    public function findAllWithTasks(): array
    {
        /** @var Experience[] $result */
        $result = $this->createQueryBuilder('e')
            ->addSelect('t')
            ->leftJoin('e.tasks', 't')
            ->orderBy('e.startedAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $result;
    }
}
