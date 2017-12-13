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
 * @ORM\Table(name="Feedbacks")
 */
class Feedback {
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="author", referencedColumnName="id")
     */
    private $author;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="target", referencedColumnName="id")
     */
    private $target;

    /**
     * @var float
     *
     * @ORM\Column(name="grade", type="decimal")
     */
    private $grade;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string")
     */
    private $comment;

    public function __construct() {
        $this->id = 0;
        $this->author = null;
        $this->target = null;
        $this->grade = null;
        $this->comment = '';
    }
}