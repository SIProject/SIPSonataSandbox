<?php
/*
 * (c) Sukhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cms_domain")
 */
class Domain
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
    private $pattern;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Site", inversedBy="domains", cascade={"persist"})
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     */
    protected $site;


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
     * Set pattern
     *
     * @param string $pattern
     * @return Domain
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * Get pattern
     *
     * @return string 
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Domain
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
     * Set site
     *
     * @param \Armd\Bundle\CmsBundle\Entity\Site $site
     * @return Domain
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
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ? : '-';
    }
}