<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\ContentAbstractBundle\Model\ResultCacheableInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="cms_page_type")
 */
class PageType implements ResultCacheableInterface
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
    private $title;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $layout;

    /**
     * @ORM\OneToMany(targetEntity="Page", mappedBy="pageType")
     */
    private $page;

    /**
     * @ORM\ManyToMany(targetEntity="Container", inversedBy="pageType")
     * @ORM\JoinTable(name="cms_container_page_type")
     */
    protected $containers;

    public function __construct()
    {
        $this->page = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return PageType
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
     * Set layout
     *
     * @param string $layout
     * @return PageType
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * Get layout
     *
     * @return string 
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Add page
     *
     * @param \Armd\Bundle\CmsBundle\Entity\Page $page
     * @return PageType
     */
    public function addPage(\Armd\Bundle\CmsBundle\Entity\Page $page)
    {
        $this->page[] = $page;
        return $this;
    }

    /**
     * Get page
     *
     * @return \Armd\Bundle\CmsBundle\Entity\Page[]
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Add containers
     *
     * @param \Armd\Bundle\CmsBundle\Entity\Container $containers
     * @return PageType
     */
    public function addContainer(\Armd\Bundle\CmsBundle\Entity\Container $containers)
    {
        $this->containers[] = $containers;
        return $this;
    }

    /**
     * Remove container
     *
     * @param \Armd\Bundle\CmsBundle\Entity\Container $container
     */
    public function removeContainer(\Armd\Bundle\CmsBundle\Entity\Container $container)
    {
        $this->containers->removeElement($container);
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
     * @return array
     */
    public function getCacheKeys()
    {
        $cacheKeys = array();
        foreach ($this->getPage() as $page) {
            $cacheKeys[] = '_cms_page_' . $page->getId();
        }

        return $cacheKeys;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle() ?: '-';
    }

    public function __clone() {
        $this->id         = null;
        $this->title      = null;
        $containers = $this->getContainers();
        $this->containers = new \Doctrine\Common\Collections\ArrayCollection();
        if ($containers) {
            foreach ($containers as $container) {
                $this->addContainer($container);
            }
        }
    }
}