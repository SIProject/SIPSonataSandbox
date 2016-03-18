<?php
/*
 * (c) Suhinin Ilja <iljasuhinin@gmail.com>
 */
namespace SIP\ResourceBundle\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseUser as BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="sip_user_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="SIP\ResourceBundle\Entity\User\Group", inversedBy="users")
     * @ORM\JoinTable(name="sip_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $about;

    /**
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $address;

    /**
     * @ORM\ManyToOne(targetEntity="SIP\ResourceBundle\Entity\Media\Media",cascade={"persist"})
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    protected $image;

    protected $showImage;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return User
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set image
     *
     * @param \SIP\ResourceBundle\Entity\Media\Media $image
     *
     * @return User
     */
    public function setImage(\SIP\ResourceBundle\Entity\Media\Media $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \SIP\ResourceBundle\Entity\Media\Media
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return string
     */
    public function getFIO()
    {
        if ($this->getName())
            return $this->getName();

        return $this->getUsername();
    }

    public function setEmail($email)
    {
        $this->setUsername($email);
        $this->setUsernameCanonical($email);
        return parent::setEmail($email);
    }

    public function getShowImage()
    {
        return $this->image;
    }

    public function setShowImage($showImage)
    {
        return $this;
    }

    /**
     * Set about
     *
     * @param string $about
     *
     * @return User
     */
    public function setAbout($about)
    {
        $this->about = $about;

        return $this;
    }

    /**
     * Get about
     *
     * @return string
     */
    public function getAbout()
    {
        return $this->about;
    }

    public function __toString()
    {
        if ($this->getName())
            return $this->getName();

        return (string)$this->getUsername();
    }
}
