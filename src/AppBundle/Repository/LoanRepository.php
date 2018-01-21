<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * LoanRepository
 */
class LoanRepository extends EntityRepository
{
    public function findLoansForAnnounces($owner)
    {
//        $announcesIds = array();
//        foreach ($announces as $announce) {
//            $announcesIds[] = $announce->getId();
//        }
//
//        return $this->createQueryBuilder('l')
//            ->select('l')
//            ->where('l.announce IN (:announces)')
//            ->andWhere('l.ownerCode <> \'\'')
//            ->andWhere('l.applicantCode <> \'\'')
//            ->setParameter('announces', array_values($announcesIds))
//            ->getQuery();

        return $this->createQueryBuilder('l')
            ->select('l')
            ->where('l.announce IN (SELECT id FROM announce WHERE owner = :owner)')
            ->andWhere('l.ownerCode <> \'\'')
            ->andWhere('l.applicantCode <> \'\'')
            ->setParameter('owner', $owner)
            ->getQuery()
            ->getResult();
    }
}