<?php

namespace Armd\ContentAbstractBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Armd\ContentAbstractBundle\Repository\StreamRepository")
 * @ORM\Table(name="cms_stream")
 */
class Stream
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
     * @ORM\Column(type="string", name="sys_name", nullable=true)
     */
    private $sysName;

    /**
     * @ORM\ManyToOne(targetEntity="Entity", inversedBy="streams")
     */
    /**
     * @ORM\ManyToMany(targetEntity="Entity", inversedBy="streams")
     * @ORM\JoinTable(name="stream_entity",
     *      joinColumns={@ORM\JoinColumn(name="stream_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="entity_id", referencedColumnName="id")}
     *      )
     */
    private $entity;

    public function __construct()
    {
        $this->entity = new ArrayCollection();
    }

    public function __toString()
    {
        return (string)$this->getName();
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
     * @return Stream
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
     * Set sysName
     *
     * @param string $sysName
     * @return Stream
     */
    public function setSysName($sysName)
    {
        $this->sysName = $sysName;
        return $this;
    }

    /**
     * Get sysName
     *
     * @return string
     */
    public function getSysName()
    {
        return $this->sysName;
    }

    /**
     * Get entity
     *
     * @return \Armd\ContentAbstractBundle\Entity\Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Add eventGroups
     *
     * @param Entity $eventGroups
     * @return Stream
     */
    public function addEntity(Entity $entity)
    {
        $this->entity[] = $entity;

        return $this;
    }

    /**
     * Remove eventGroups
     *
     * @param Entity $eventGroups
     */
    public function removeEntity(Entity $entity)
    {
        $this->entity->removeElement($entity);
    }

}