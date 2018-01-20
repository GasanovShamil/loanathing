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
 * @ORM\Table(name="Notification")
 */
class Notification {
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="notifications")
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     */
    private $user;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isNew", type="boolean")
     */
    private $isNew;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string")
     */
    private $content;

    public function __construct() {
        $this->id = 0;
        $this->user = null;
        $this->isNew = true;
        $this->content = '';
    }
}