<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Entity;

use Armd\Bundle\CmsBundle\Validator\Constraints\ContainerUnique;
use Armd\Bundle\CmsBundle\Validator\Constraints\ContainerMain;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Armd\ContentAbstractBundle\Model\ResultCacheableInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="cms_page_type_container")
 * @ContainerUnique
 * @ContainerMain
 */
class Container extends BaseContainer implements ResultCacheableInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="array")
     */
    protected $settings;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $usageService;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $usageType;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_main;

    /**
     * @ORM\OneToMany(targetEntity="PageContainer", mappedBy="container", cascade={"remove"})
     */
    private $pageContainer;

    /**
     * @ORM\ManyToOne(targetEntity="Area", inversedBy="containers")
     * @ORM\JoinColumn(name="area_id", referencedColumnName="id")
     */
    private $area;

    /**
     * @ORM\ManyToMany(targetEntity="PageType", mappedBy="containers")
     */
    protected $pageType;

    /**
     * @var string
     */
    protected $toString;

    public function __construct()
    {
        $this->pageContainer = new \Doctrine\Common\Collections\ArrayCollection();
        $this->pageType = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get settings
     *
     * @return array 
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Set usageService
     *
     * @param string $usageService
     * @return Container
     */
    public function setUsageService($usageService)
    {
        $this->usageService = $usageService;
        return $this;
    }

    /**
     * Get usageService
     *
     * @return string 
     */
    public function getUsageService()
    {
        return $this->usageService;
    }

    /**
     * Set usageType
     *
     * @param string $usageType
     * @return Container
     */
    public function setUsageType($usageType)
    {
        $this->usageType = $usageType;
        return $this;
    }

    /**
     * Get usageType
     *
     * @return string 
     */
    public function getUsageType()
    {
        return $this->usageType;
    }

    /**
     * Set is_main
     *
     * @param boolean $isMain
     * @return Container
     */
    public function setIsMain($isMain)
    {
        $this->is_main = $isMain;
        return $this;
    }

    /**
     * Get is_main
     *
     * @return boolean 
     */
    public function getIsMain()
    {
        return $this->is_main;
    }

    /**
     * Add pageContainer
     *
     * @param \Armd\Bundle\CmsBundle\Entity\PageContainer $pageContainer
     * @return Container
     */
    public function addPageContainer(\Armd\Bundle\CmsBundle\Entity\PageContainer $pageContainer)
    {
        $this->pageContainer[] = $pageContainer;
        return $this;
    }

    /**
     * Get pageContainer
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPageContainer()
    {
        return $this->pageContainer;
    }

    /**
     * Set area
     *
     * @param \Armd\Bundle\CmsBundle\Entity\Area $area
     * @return Container
     */
    public function setArea(\Armd\Bundle\CmsBundle\Entity\Area $area = null)
    {
        $this->area = $area;
        return $this;
    }

    /**
     * Get area
     *
     * @return \Armd\Bundle\CmsBundle\Entity\Area
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Container
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
     * Remove pageContainer
     *
     * @param \Armd\Bundle\CmsBundle\Entity\PageContainer $pageContainer
     */
    public function removePageContainer(\Armd\Bundle\CmsBundle\Entity\PageContainer $pageContainer)
    {
        $this->pageContainer->removeElement($pageContainer);
    }

    /**
     * Add pageType
     *
     * @param \Armd\Bundle\CmsBundle\Entity\PageType $pageType
     * @return Container
     */
    public function addPageType(\Armd\Bundle\CmsBundle\Entity\PageType $pageType)
    {
        $pageType->addContainer($this);
        $this->pageType[] = $pageType;

        return $this;
    }

    /**
     * Remove pageType
     *
     * @param \Armd\Bundle\CmsBundle\Entity\PageType $pageType
     */
    public function removePageType(\Armd\Bundle\CmsBundle\Entity\PageType $pageType)
    {
        $pageType->removeContainer($this);
        $this->pageType->removeElement($pageType);
    }

    /**
     * Get pageType
     *
     * @return  \Armd\Bundle\CmsBundle\Entity\PageType[]
     */
    public function getPageType()
    {
        return $this->pageType;
    }

    /**
     * @return array
     */
    public function getCacheKeys()
    {
        $cacheKeys = array();
        foreach ($this->getPageType() as $pageType) {
            foreach ($pageType->getPage() as $page) {
                $cacheKeys[] = '_cms_page_' . $page->getId();
            }
        }

        return $cacheKeys;
    }

    /**
     * @return string
     */
    public function getToString()
    {
        if (!$this->toString) {
            $this->toString = $this->__toString();
        }

        return $this->toString;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->getTitle()) {
            return $this->getTitle();
        }

        return ($this->getIsMain()? '*': '') .
            ((string)$this->getArea() .
                '(' . ($this->getUsageService()? $this->getUsageService(): '').
                ($this->getUsageType()? ':' . $this->getUsageType() . ' ': '').
                (string)$this->getId()) . ')';
    }
}