<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Armd\ContentAbstractBundle\Model\ResultCacheableInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="cms_page_container",
 *            uniqueConstraints={@ORM\UniqueConstraint(name="page_container_unique_idx", columns={
 *              "page_id", "container_id"
 *            })}
 * )
 */
class PageContainer extends BaseContainer implements ResultCacheableInterface
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
    private $usageService;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $usageType;

    /**
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="pageContainers")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id")
     * @Assert\NotNull
     */
    private $page;

    /**
     * @ORM\ManyToOne(targetEntity="Container", inversedBy="pageContainer")
     * @ORM\JoinColumn(name="container_id", referencedColumnName="id")
     * @Assert\NotNull
     */
    private $container;


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
     */
    public function setUsageService($usageService)
    {
        $this->usageService = $usageService;
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
     */
    public function setUsageType($usageType)
    {
        $this->usageType = $usageType;
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
     * Set page
     *
     * @param \Armd\Bundle\CmsBundle\Entity\Page $page
     */
    public function setPage(\Armd\Bundle\CmsBundle\Entity\Page $page)
    {
        $this->page = $page;
    }

    /**
     * Get page
     *
     * @return \Armd\Bundle\CmsBundle\Entity\Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set container
     *
     * @param \Armd\Bundle\CmsBundle\Entity\Container $container
     */
    public function setContainer(\Armd\Bundle\CmsBundle\Entity\Container $container)
    {
        $this->container = $container;
    }

    /**
     * Get container
     *
     * @return \Armd\Bundle\CmsBundle\Entity\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return array
     */
    public function getCacheKeys()
    {
        return array('_cms_page_' . $this->getPage()->getId());
    }

    /**
     * @return int
     */
    public function __toString()
    {
        return (string)$this->getId();
    }

    public function __clone() {
        $this->id = null;
    }
}