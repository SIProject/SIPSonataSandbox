<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\NewsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Armd\ListBundle\Entity\BaseList;
use Armd\ContentAbstractBundle\Entity\BaseContent;

/**
 * @ORM\MappedSuperclass
 */
class BaseNews extends BaseContent
{
    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="text", nullable=true)
     */
    protected $announce;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="text", nullable=true)
     */
    protected $body;

    /**
     * @ORM\ManyToOne(targetEntity="\Armd\ContentAbstractBundle\Entity\Stream")
     */
    protected $stream;

    /**
     * @ORM\Column(type="datetime", name="date")
     */
    private $date;

    /**
     * Set title
     *
     * @param string $title
     * @return BaseList
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
     * Set announce
     *
     * @param string $announce
     * @return BaseList
     */
    public function setAnnounce($announce)
    {
        $this->announce = $announce;
        return $this;
    }

    /**
     * Get announce
     *
     * @return string
     */
    public function getAnnounce()
    {
        return $this->announce;
    }

    /**
     * Set body
     *
     * @param string $body
     * @return BaseList
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set stream
     *
     * @param \Armd\ContentAbstractBundle\Entity\Stream $stream
     * @return BaseList
     */
    public function setStream(\Armd\ContentAbstractBundle\Entity\Stream $stream = null)
    {
        $this->stream = $stream;
        return $this;
    }

    /**
     * Get stream
     *
     * @return \Armd\ContentAbstractBundle\Entity\Stream
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * Set date
     *
     * @param \datetime $date
     * @return BaseNews
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return \datetime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getTitle();
    }
}