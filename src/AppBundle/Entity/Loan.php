<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 24/11/2017
 * Time: 14:58
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LoanRepository")
 * @ORM\Table(name="Loan")
 */
class Loan {

    //region Id
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    //endregion

    //region Applicant
    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="applicant", referencedColumnName="id")
     */
    private $applicant;

    /**
     * @return int
     */
    public function getApplicant()
    {
        return $this->applicant;
    }

    /**
     * @param int $applicant
     */
    public function setApplicant($applicant)
    {
        $this->applicant = $applicant;
    }
    //endregion

    //region Announce
    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Announce", inversedBy="loans")
     * @ORM\JoinColumn(name="announce", referencedColumnName="id")
     */
    private $announce;

    /**
     * @return int
     */
    public function getAnnounce()
    {
        return $this->announce;
    }

    /**
     * @param int $announce
     */
    public function setAnnounce($announce)
    {
        $this->announce = $announce;
    }
    //endregion

    //region StartDate
    /**
     * @var string
     *
     * @ORM\Column(name="startDate", type="string")
     */
    private $startDate;

    /**
     * @return string
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param string $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }
    //endregion

    //region EndDate
    /**
     * @var string
     *
     * @ORM\Column(name="endDate", type="string")
     */
    private $endDate;

    /**
     * @return string
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param string $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }
    //endregion

    //region Status
    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param integer $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
    //endregion

    //region OwnerCode
    /**
     * @var string
     *
     * @ORM\Column(name="ownerCode", type="string")
     */
    private $ownerCode;

    /**
     * @return string
     */
    public function getOwnerCode()
    {
        return $this->ownerCode;
    }

    /**
     * @param string $ownerCode
     */
    public function setOwnerCode($ownerCode)
    {
        $this->ownerCode = $ownerCode;
        if ($this->ownerCode == 'OK' && $this->applicantCode == 'OK') $this->status = 2;
    }
    //endregion

    //region ApplicantCode
    /**
     * @var string
     *
     * @ORM\Column(name="applicantCode", type="string")
     */
    private $applicantCode;

    /**
     * @return string
     */
    public function getApplicantCode()
    {
        return $this->applicantCode;
    }

    /**
     * @param string $applicantCode
     */
    public function setApplicantCode($applicantCode)
    {
        $this->applicantCode = $applicantCode;
        if ($this->ownerCode == 'OK' && $this->applicantCode == 'OK') $this->status = 2;
    }
    //endregion

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Feedback", mappedBy="loan")
     */
    private $feedbacks;

    public function __construct() {
        $this->id = 0;
        $this->applicant = null;
        $this->announce = null;
        $this->startDate = '';
        $this->endDate = '';
        $this->status = 0;
        $this->ownerCode = '';
        $this->applicantCode = '';
        $this->feedbacks = new ArrayCollection();
    }
}