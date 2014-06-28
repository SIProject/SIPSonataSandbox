<?php
/*
 * (c) Sukhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\ContentAbstractBundle\Model\ResultCacheableInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="cms_area")
 */
class Area implements ResultCacheableInterface
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
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;

    /**
     * @ORM\OneToMany(targetEntity="Container", mappedBy="area")
     */
    protected $containers;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->containers = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Area
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
     * Add containers
     *
     * @param \Armd\Bundle\CmsBundle\Entity\Container $containers
     * @return Area
     */
    public function addContainer(\Armd\Bundle\CmsBundle\Entity\Container $containers)
    {
        $this->containers[] = $containers;
    
        return $this;
    }

    /**
     * Remove containers
     *
     * @param \Armd\Bundle\CmsBundle\Entity\Container $containers
     */
    public function removeContainer(\Armd\Bundle\CmsBundle\Entity\Container $containers)
    {
        $this->containers->removeElement($containers);
    }

    /**
     * Get containers
     *
     * @return \Armd\Bundle\CmsBundle\Entity\Container[]
     */
    public function getContainers()
    {
        return $this->containers;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Area
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
     * @return array
     */
    public function getCacheKeys()
    {
        $cacheKeys = array();

        foreach ($this->getContainers() as $container) {
            $cacheKeys = array_merge($cacheKeys, $container->getCacheKeys());
        }

        return $cacheKeys;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle()? (string)$this->getTitle(): (string)$this->getName();
    }
}