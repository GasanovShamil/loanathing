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
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FeedbackRepository")
 * @ORM\Table(name="Feedback")
 */
class Feedback {

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

    //region Author
    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="author", referencedColumnName="id")
     */
    private $author;

    /**
     * @return int
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param int $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }
    //endregion

    //region Target
    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="feedbacks")
     * @ORM\JoinColumn(name="target", referencedColumnName="id")
     */
    private $target;
    /**
     * @return int
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param int $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    //endregion

    //region Loan
    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Loan", inversedBy="feedbacks")
     * @ORM\JoinColumn(name="loan", referencedColumnName="id")
     */
    private $loan;

    /**
     * @return int
     */
    public function getLoan()
    {
        return $this->loan;
    }

    /**
     * @param int $loan
     */
    public function setLoan($loan)
    {
        $this->loan = $loan;
    }
    //endregion

    //region Grade
    /**
     * @var float
     *
     * @ORM\Column(name="grade", type="decimal")
     */
    private $grade;
    /**
     * @return float
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * @param float $grade
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;
    }
    //endregion

    //region Comment
    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string")
     */
    private $comment;

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }
    //endregion

    //region PostDate
    /**
     * @var string
     *
     * @ORM\Column(name="postDate", type="string")
     */
    private $postDate;

    /**
     * @return string
     */
    public function getPostDate()
    {
        return $this->postDate;
    }

    /**
     * @param string $postDate
     */
    public function setPostDate($postDate)
    {
        $this->postDate = $postDate;
    }
    //endregion

    public function __construct() {
        $this->id = 0;
        $this->author = null;
        $this->target = null;
        $this->loan = null;
        $this->grade = null;
        $this->comment = '';
        $this->comment = '';
    }
}