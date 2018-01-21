<?php
/**
 * Created by PhpStorm.
 * User: shgas
 * Date: 24/11/2017
 * Time: 12:55
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @ORM\Table(name="Announce")
 * @Vich\Uploadable
 */
class Announce {

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

    //region Owner
    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="announces")
     * @ORM\JoinColumn(name="owner", referencedColumnName="id")
     */
    private $owner;

    /**
     * @return int
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param int $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    //endregion

    //region Type
    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Type")
     * @ORM\JoinColumn(name="type", referencedColumnName="id")
     */
    private $type;

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
    //endregion

    //region Tag
    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tag")
     * @ORM\JoinColumn(name="tag", referencedColumnName="id")
     */
    private $tag;

    /**
     * @return ArrayCollection
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param ArrayCollection $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }
    //endregion

    //region Name
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     *
     * @Assert\NotBlank(message="Le titre est obligatoire")
     */
    private $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    //endregion

    //region Description
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string")
     *
     * @Assert\NotBlank(message="La description est obligatoire")
     */
    private $description;

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
    //endregion

    //region File
    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="announce_image", fileNameProperty="image")
     */
    private $file;

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param File $image
     */
    public function setFile($file)
    {
        $this->file = $file;
    }
    //endregion

    //region Image
    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string")
     *
     * @Assert\File(mimeTypes={ "image/png" }, mimeTypesMessage="Le fichier doit être au format PNG.")
     */
    private $image;

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }
    //endregion

    //region StartDate
    /**
     * @var string
     *
     * @ORM\Column(name="startDate", type="string")
     *
     * @Assert\NotBlank(message="La date de début est obligatoire")
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
     *
     * @Assert\NotBlank(message="La date de fin est obligatoire")
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

    //region Loans
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Loan", mappedBy="announce")
     */
    private $loans;

    /**
     * @return ArrayCollection
     */
    public function getLoans()
    {
        return $this->loans;
    }

    /**
     * @param ArrayCollection $loans
     */
    public function setLoans($loans)
    {
        $this->loans = $loans;
    }
    //endregion

    public function __construct() {
        $this->id = 0;
        $this->owner = null;
        $this->type = null;
        $this->tag = null;
        $this->name = '';
        $this->description = '';
        $this->image = '';
        $this->startDate = '';
        $this->endDate = '';
        $this->loans = new ArrayCollection();
    }
}