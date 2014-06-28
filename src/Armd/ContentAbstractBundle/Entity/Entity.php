<?php

namespace Armd\ContentAbstractBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cms_entity")
 */
class Entity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $class;

    /**
     * @ORM\Column(type="string")
     */
    private $service;

    /**
     * @ORM\ManyToMany(targetEntity="Stream", mappedBy="entity")
     */
    private $streams;
    
    public function __toString()
    {
        return $this->getName();
    }
    
    public function __construct()
    {
        $this->streams = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
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
     * @return Entity
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
     * Set class
     *
     * @param string $class
     * @return Entity
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * Get class
     *
     * @return string 
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set service
     *
     * @param string $service
     * @return Entity
     */
    public function setService($service)
    {
        $this->service = $service;
        return $this;
    }

    /**
     * Get service
     *
     * @return string 
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Add streams
     *
     * @param \Armd\ContentAbstractBundle\Entity\Stream $streams
     * @return Entity
     */
    public function addStream(\Armd\ContentAbstractBundle\Entity\Stream $streams)
    {
        $this->streams[] = $streams;
        return $this;
    }

    /**
     * Get streams
     *
     * @return \Armd\ContentAbstractBundle\Entity\Stream[]
     */
    public function getStreams()
    {
        return $this->streams;
    }
}