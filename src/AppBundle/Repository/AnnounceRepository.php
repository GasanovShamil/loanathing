<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * AnnounceRepository
 */
class AnnounceRepository extends EntityRepository
{
    public function findAnnouncesByOwner($owner, $status)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.loans', 'l')
            ->where('a.owner = :owner')
            ->andWhere('l.status = :status')
            ->setParameter('owner', $owner)
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }

    public function findAnnouncesByApplicant($applicant, $status)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.loans', 'l')
            ->leftJoin('l.applicant', 'u')
            ->where('u.id = :applicant')
            ->andWhere('l.status = :status')
            ->setParameter('applicant', $applicant)
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }
}