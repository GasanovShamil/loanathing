<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * LoanRepository
 */
class LoanRepository extends EntityRepository
{
    public function findLoansByOwner($owner, $status)
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.announce', 'a')
            ->leftJoin('a.owner', 'u')
            ->where('u.id = :owner')
            ->andWhere('l.status = :status')
            ->setParameter('owner', $owner)
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }

    public function findLoansByApplicant($applicant, $status)
    {
        return $this->createQueryBuilder('l')
            ->where('l.applicant = :applicant')
            ->andWhere('l.status = :status')
            ->setParameter('applicant', $applicant)
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }
}