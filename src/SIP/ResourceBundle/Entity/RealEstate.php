<?php
/*
 * (c) Suhinin Ilja <iljasuhinin@gmail.com>
 */
namespace SIP\ResourceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sip_real_estate")
 * @ORM\HasLifecycleCallbacks
 */
class RealEstate
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(type="string")
     */
    protected $location;


    /**
     * @ORM\Column(type="string")
     */
    protected $price;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $currency;

    /**
     * @ORM\Column(type="string", name="home_area", nullable=true)
     */
    protected $homeArea;

    /**
     * @ORM\Column(type="string", name="piece_area", nullable=true)
     */
    protected $pieceArea;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $distance;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $image;

    protected $uploadedImage;

    /**
     * @ORM\Column(type="string")
     */
    protected $link;

    /**
     * @ORM\Column(type="datetime", name="date_upload")
     */
    protected $dateUpload;

    /**
     * @ORM\Column(type="datetime", name="date_update", nullable=true)
     */
    protected $dateUpdate;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $city;

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
     * Set title
     *
     * @param string $title
     * @return RealEstate
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return RealEstate
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set price
     *
     * @param integer $price
     * @return RealEstate
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return integer 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set currency
     *
     * @param integer $currency
     * @return RealEstate
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return integer 
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set homeArea
     *
     * @param float $homeArea
     * @return RealEstate
     */
    public function setHomeArea($homeArea)
    {
        $this->homeArea = $homeArea;

        return $this;
    }

    /**
     * Get homeArea
     *
     * @return float 
     */
    public function getHomeArea()
    {
        return $this->homeArea;
    }

    /**
     * Set pieceArea
     *
     * @param float $pieceArea
     * @return RealEstate
     */
    public function setPieceArea($pieceArea)
    {
        $this->pieceArea = $pieceArea;

        return $this;
    }

    /**
     * Get pieceArea
     *
     * @return float 
     */
    public function getPieceArea()
    {
        return $this->pieceArea;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return RealEstate
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set distance
     *
     * @param string $distance
     * @return RealEstate
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return string 
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return RealEstate
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return RealEstate
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set uploadedImage
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedImage
     * @return RealEstate
     */
    public function setUploadedImage(\Symfony\Component\HttpFoundation\File\UploadedFile $uploadedImage)
    {
        $this->uploadedImage = $uploadedImage;

        $this->preUpload();
        return $this;
    }

    /**
     * Get uploadedImage
     *
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function getUploadedImage()
    {
        return $this->uploadedImage;
    }

    /**
     * @return null|string
     */
    public function getAbsolutePath()
    {
        return null === $this->image? null: $this->getUploadRootDir().'/'.$this->image;
    }

    /**
     * @return null|string
     */
    public function getWebPath()
    {
        return null === $this->image? null: $this->getUploadDir().'/'.$this->image;
    }

    /**
     * @return string
     */
    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    /**
     * @return string
     */
    protected function getUploadDir()
    {
        return 'uploads/real_estate';
    }

    public function preUpload()
    {
        if (null !== $this->getUploadedImage()) {
            $filename = sha1(uniqid(mt_rand(), true));
            $this->image = $filename.'.'.$this->getUploadedImage()->guessExtension();
        }
    }

    /**
     * @ORM\PrePersist()
     */
    public function update()
    {
        $this->setDateUpdate(new \DateTime());
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getUploadedImage()) return;
        $this->getUploadedImage()->move($this->getUploadRootDir(), $this->image);
        $this->uploadedImage = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            @unlink($file);
        }
    }

    /**
     * @return null|string
     */
    public function getImage()
    {
        if (strpos($this->image, 'http') === 0) {
            return $this->image;
        }
        return $this->getWebPath();
    }

    /**
     * @param \DateTime $dateUpload
     * @return $this
     */
    public function setDateUpload(\DateTime $dateUpload = null)
    {
        $this->dateUpload = $dateUpload;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateUpload()
    {
        return $this->dateUpload;
    }

    /**
     * @param \DateTime $dateUpload
     * @return $this
     */
    public function setDateUpdate(\DateTime $dateUpdate = null)
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateUpdate()
    {
        return $this->dateUpdate;
    }

    public function __toString()
    {
        return (string) $this->getTitle();
    }
}
