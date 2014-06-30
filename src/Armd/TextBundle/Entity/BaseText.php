<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\TextBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\ContentAbstractBundle\Entity\BaseContent;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\MappedSuperclass
 */
class BaseText extends BaseContent
{
    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="text")
     */
    protected $body;

    /**
     * Set title
     *
     * @param string $title
     * @return BaseText
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
     * Set body
     *
     * @param BaseText $body
     * @return BaseText
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Get body
     *
     * @return BaseText
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getTitle();
    }
}