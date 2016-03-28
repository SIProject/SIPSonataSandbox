<?php
/*
 * (c) Suhinin Ilja <iljasuhinin@gmail.com>
 */
namespace SIP\ResourceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_meta", uniqueConstraints={ @ORM\UniqueConstraint(name="key_idx", columns={"meta_key"}) } )
 * @ORM\HasLifecycleCallbacks
 */
class MetaData
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="title")
     */
    protected $title;

    /**
     * @ORM\Column(type="string", name="meta_key")
     */
    protected $metaKey;

    /**
     * @ORM\Column(type="string", name="meta_title", nullable=true)
     */
    protected $metaTitle;

    /**
     * @ORM\Column(type="text", name="meta_keywords", nullable=true)
     */
    protected $metaKeywords;

    /**
     * @ORM\Column(type="text", name="meta_description", nullable=true)
     */
    protected $metaDescription;

    /**
     * Constructor
     */
    public function __construct() {}

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set id
     *
     * @return MetaData
     */
    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return MetaData
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set metaKey
     *
     * @param string $metaKey
     * @return MetaData
     */
    public function setMetaKey($metaKey) {
        $this->metaKey = $metaKey;

        return $this;
    }

    /**
     * Get metaKey
     *
     * @return string
     */
    public function getMetaKey() {
        return $this->metaKey;
    }

    /**
     * Set metaTitle
     *
     * @param string $metaTitle
     * @return MetaData
     */
    public function setMetaTitle($metaTitle) {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * Get metaTitle
     *
     * @return string
     */
    public function getMetaTitle() {
        return $this->metaTitle;
    }

    /**
     * Set metaKeywords
     *
     * @param string $metaKeywords
     * @return MetaData
     */
    public function setMetaKeywords($metaKeywords) {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    /**
     * Get metaKeywords
     *
     * @return string
     */
    public function getMetaKeywords() {
        return $this->metaKeywords;
    }

    /**
     * Set metaDescription
     *
     * @param string $metaDescription
     * @return MetaData
     */
    public function setMetaDescription($metaDescription) {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * Get metaDescription
     *
     * @return string
     */
    public function getMetaDescription() {
        return $this->metaDescription;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getTitle();
    }
}
