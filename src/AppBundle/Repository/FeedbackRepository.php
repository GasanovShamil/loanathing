<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * FeedbackRepository
 */
class FeedbackRepository extends EntityRepository
{
    public function findFeedbacksByUser($user)
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.target', 'u')
            ->where('u.id = :user')
            ->setParameter('user', $user)
            ->orderBy('f.id', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    public function findAverageGradeByUser($user)
    {
        return $this->createQueryBuilder('f')
            ->select('avg(f.grade)')
            ->leftJoin('f.target', 'u')
            ->where('u.id = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
}