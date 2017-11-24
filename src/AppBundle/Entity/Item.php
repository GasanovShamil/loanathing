<?php
/**
 * Created by PhpStorm.
 * User: shgas
 * Date: 24/11/2017
 * Time: 12:55
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Items")
 */
class Item {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct() {
        parent::__construct();
    }
}