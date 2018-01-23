<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * NotificationRepository
 */
class NotificationRepository extends EntityRepository
{
    public function findNotificationsByUser($user)
    {
        return $this->createQueryBuilder('n')
            ->leftJoin('n.user', 'u')
            ->where('u.id = :user')
            ->setParameter('user', $user)
            ->orderBy('n.id', 'DESC')
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