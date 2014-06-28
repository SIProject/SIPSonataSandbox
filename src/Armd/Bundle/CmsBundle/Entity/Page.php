<?php
/*
 * (c) Sukhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

use Genemu\Bundle\FormBundle\Gd\File\Image;

use Armd\Bundle\CmsBundle\Validator\Constraints\PageUrlUnique;
use Armd\ContentAbstractBundle\Model\ResultCacheableInterface;
use Armd\Bundle\CmsBundle\Model\CloneInterface;

/**
 * @ORM\Entity(repositoryClass="Armd\Bundle\CmsBundle\Entity\PageRepository")
 * @PageUrlUnique
 * @ORM\Table(name="cms_page")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\Tree(type="nested")
 */
class Page implements ResultCacheableInterface, CloneInterface
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
     * @ORM\Column(type="string", unique=true)
     */
    private $url;

    /**
     * @ORM\Column(type="string")
     */
    private $slug;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(type="integer", nullable=true)
     */
    private $root;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(type="integer")
     */
    private $lvl;

    /**
     * @ORM\OneToMany(targetEntity="Page", mappedBy="parent", cascade={"remove", "persist"})
     */
    private $children;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

    /**
     * @ORM\ManyToOne(targetEntity="PageType", inversedBy="page")
     * @ORM\JoinColumn(name="page_type_id", referencedColumnName="id")
     * @Assert\NotNull
     */
    private $pageType;

    /**
     * @ORM\OneToMany(targetEntity="PageContainer", mappedBy="page", cascade={"remove", "persist"})
     */
    private $pageContainers;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $menuEnabled = false;

    /**
     * @ORM\ManyToOne(targetEntity="Site", inversedBy="pages")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     * @Assert\NotNull
     */
    private $site;

    /**
     * @var array $parameters
     *
     * @ORM\Column(type="array", nullable=true)
     */
    private $parameters = array();

     /**
      * @ORM\Column(type="boolean", nullable=true)
      */
    protected $toFirstChild = false;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $logo;

    /**
     * @var string
     */
    private $oldFile;

    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->pageContainers = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set id
     *
     * @return integer
     */
    public function setId($id)
    {
        return $this->id = $id;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
     * Set url
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set lft
     *
     * @param integer $lft
     */
    public function setLft($lft)
    {
        $this->lft = $lft;
    }

    /**
     * Get lft
     *
     * @return integer 
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set rgt
     *
     * @param integer $rgt
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;
    }

    /**
     * Get rgt
     *
     * @return integer 
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set root
     *
     * @param integer $root
     */
    public function setRoot($root)
    {
        $this->root = $root;
    }

    /**
     * Get root
     *
     * @return integer 
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Set lvl
     *
     * @param integer $lvl
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;
    }

    /**
     * Get lvl
     *
     * @return integer 
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Add children
     *
     * @param \Armd\Bundle\CmsBundle\Entity\Page $children
     */
    public function addPage(\Armd\Bundle\CmsBundle\Entity\Page $children)
    {
        $this->children[] = $children;
    }

    /**
     * Get children
     *
     * @return \Armd\Bundle\CmsBundle\Entity\Page[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \Armd\Bundle\CmsBundle\Entity\Page $parent
     */
    public function setParent(\Armd\Bundle\CmsBundle\Entity\Page $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return \Armd\Bundle\CmsBundle\Entity\Page
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set pageType
     *
     * @param \Armd\Bundle\CmsBundle\Entity\PageType $pageType
     */
    public function setPageType(\Armd\Bundle\CmsBundle\Entity\PageType $pageType)
    {
        $this->pageType = $pageType;
    }

    /**
     * Get pageType
     *
     * @return \Armd\Bundle\CmsBundle\Entity\PageType
     */
    public function getPageType()
    {
        return $this->pageType;
    }

    /**
     * Add pageContainers
     *
     * @param \Armd\Bundle\CmsBundle\Entity\PageContainer $pageContainers
     */
    public function addPageContainer(\Armd\Bundle\CmsBundle\Entity\PageContainer $pageContainers)
    {
        $this->pageContainers[] = $pageContainers;
    }

    /**
     * Get pageContainers
     *
     * @return \Armd\Bundle\CmsBundle\Entity\PageContainer[]
     */
    public function getPageContainers()
    {
        return $this->pageContainers;
    }

    /**
     * Set site
     *
     * @param \Armd\Bundle\CmsBundle\Entity\Site $site
     * @return Page
     */
    public function setSite(\Armd\Bundle\CmsBundle\Entity\Site $site = null)
    {
        $this->site = $site;
        return $this;
    }

    /**
     * Get site
     *
     * @return \Armd\Bundle\CmsBundle\Entity\Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Get menuEnabled
     *
     * @return string
     */
    public function getMenuEnabled()
    {
        return $this->menuEnabled;
    }

    /**
     * Set menuEnabled
     *
     * @param integer $menuEnabled
     */
    public function setMenuEnabled($menuEnabled)
    {
        $this->menuEnabled = $menuEnabled;
    }

    /**
     * Get toFirstChild
     *
     * @return string
     */
    public function getToFirstChild()
    {
        return $this->toFirstChild;
    }

    /**
     * Set toFirstChild
     *
     * @param integer $toFirstChild
     */
    public function setToFirstChild($toFirstChild)
    {
        $this->toFirstChild = $toFirstChild;
    }

    /**
     * Set parameters
     *
     * @param string $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Get parameters
     *
     * @return string
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set logo
     *
     * @param $logo
     */
    public function setLogo($logo = null)
    {
        $this->oldFile = $this->logo;
        $this->logo = $logo;
    }

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->getWebPath($this->logo);
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getWebPath($path)
    {
        $pathArray = explode('web', $path);

        if (isset($pathArray[1])) {
            return str_replace('//', '/', $pathArray[1]);
        }

        return $path;
    }

    /**
     * @ORM\PostUpdate()
     */
    public function postUpdate()
    {
        if ($this->oldFile) {
            $oldImage = new Image($this->oldFile);

            if ($this->logo && $oldImage->getRealPath() == $this->logo->getRealPath()) {
                return;
            }

            /** @var Image $thumbnail */
            foreach ($oldImage->getThumbnails() as $thumbnail) {
                @unlink($thumbnail->getRealPath());
            }
            @unlink($oldImage->getRealPath());
        }
    }

    /**
     * @ORM\PostRemove()
     */
    public function postRemove()
    {
        if ($this->logo) {
            $oldImage = new Image($this->logo);
            /** @var Image $thumbnail */
            foreach ($oldImage->getThumbnails() as $thumbnail) {
                @unlink($thumbnail->getRealPath());
            }
            @unlink($oldImage->getRealPath());
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle() . ($this->getParent()? ('(' . $this->getParent()->getTitle() . ')'): (''));
    }

    /**
     * @return array
     */
    public function getCacheKeys()
    {
        return array('_page_' . $this->getId(), '_cms_page_' . $this->getId());
    }

    public function __clone() {
        $this->id      = null;
        $this->title   = null;
        $this->slug    = null;
    }

    public function cloneAssociation()
    {
        if ($this->getPageContainers()) {
            foreach ($this->getPageContainers() as $pageContainer) {
                $newPageContainer = clone $pageContainer;
                $newPageContainer->setPage($this);
                $this->addPageContainer($newPageContainer);
            }
        }

        if ($this->logo) {
            $fileArray = pathinfo($this->logo);
            $name = uniqid();
            $newFilePath = str_replace($fileArray['filename'], $name, $this->logo);
            copy($this->logo, $newFilePath);
            $logo = new Image($this->logo);
            foreach ($logo->getThumbnails() as $thumbnail) {
                copy($thumbnail->getRealPath(), str_replace($fileArray['filename'], $name, $thumbnail->getRealPath()));
            }

            $this->logo = $newFilePath;
        }
    }
}