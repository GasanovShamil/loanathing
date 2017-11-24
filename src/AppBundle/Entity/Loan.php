<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 24/11/2017
 * Time: 14:58
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Loans")
 */
class Loan {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="applicant", referencedColumnName="id")
     */
    private $applicant;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Announce")
     * @ORM\JoinColumn(name="announce", referencedColumnName="id")
     */
    private $announce;

    /**
     * @ORM\Column(name="startDate", type="datetime")
     */
    private $startDate;

    /**
     * @ORM\Column(name="endDate", type="datetime")
     */
    private $endDate;

    /**
     * @ORM\Column(name="transferCode", type="string")
     */
    private $transferCode;

    /**
     * @ORM\Column(name="returnCode", type="string")
     */
    private $returnCode;

    public function __construct() {
        $this->id = 0;
        $this->applicant = null;
        $this->announce = null;
        $this->startDate = new \DateTime();
        $this->endDate = new \DateTime();
        $this->transferCode = null;
        $this->returnCode = null;
    }
}