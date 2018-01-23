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
            ->getQuery()
            ->getResult();
    }
}