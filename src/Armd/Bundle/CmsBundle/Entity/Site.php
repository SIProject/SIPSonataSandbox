<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Genemu\Bundle\FormBundle\Gd\File\Image;
use Armd\ContentAbstractBundle\Model\ResultCacheableInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="cms_site")
 * @ORM\HasLifecycleCallbacks
 */
class Site implements ResultCacheableInterface
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
     * @ORM\OneToMany(targetEntity="Domain", mappedBy="site")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $domains;

    /**
     * @ORM\OneToMany(targetEntity="Page", mappedBy="site")
     */
    private $pages;

    /**
     * @var array $parameters
     *
     * @ORM\Column(type="array", nullable=true)
     */
    private $parameters = array();

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
        $this->domains = new \Doctrine\Common\Collections\ArrayCollection();
        $this->pages = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Site
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
     * Add domains
     *
     * @param \Armd\Bundle\CmsBundle\Entity\Domain $domains
     * @return Site
     */
    public function addDomain(\Armd\Bundle\CmsBundle\Entity\Domain $domains)
    {
        $this->domains[] = $domains;
        return $this;
    }

    /**
     * Get domains
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDomains()
    {
        return $this->domains;
    }

    /**
     * Add pages
     *
     * @param \Armd\Bundle\CmsBundle\Entity\Page $pages
     * @return Site
     */
    public function addPage(\Armd\Bundle\CmsBundle\Entity\Page $pages)
    {
        $this->pages[] = $pages;
        return $this;
    }

    /**
     * Get pages
     *
     * @return \Armd\Bundle\CmsBundle\Entity\Page[]
     */
    public function getPages()
    {
        return $this->pages;
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
        if ($this->logo && $this->oldFile) {
            $oldImage = new Image($this->oldFile);

            if ($oldImage->getRealPath() == $this->logo->getRealPath()) {
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
        $oldImage = new Image($this->logo);
        /** @var Image $thumbnail */
        foreach ($oldImage->getThumbnails() as $thumbnail) {
            @unlink($thumbnail->getRealPath());
        }
        @unlink($oldImage->getRealPath());
    }

    /**
     * @return array
     */
    public function getCacheKeys()
    {
        $cacheKeys = array();
        foreach ($this->getPages() as $page) {
            $cacheKeys[] = '_cms_page_' . $page->getId();
        }

        return $cacheKeys;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle() ? (string)$this->getTitle(): '-';
    }
}