<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * LoanRepository
 */
class LoanRepository extends EntityRepository
{
    public function findLoansByOwner($owner)
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.announce', 'a')
            ->leftJoin('a.owner', 'u')
            ->where('u.id = :id')
            ->andWhere('l.ownerCode =\'\'')
            ->andWhere('l.applicantCode = \'\'')
            ->setParameter('id', $owner)
            ->getQuery()
            ->getResult();
    }
}