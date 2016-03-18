<?php
/*
 * (c) Suhinin Ilja <iljasuhinin@gmail.com>
 */
namespace SIP\ResourceBundle\Entity\User;

use Doctrine\ORM\Mapping as ORM;

use Sonata\UserBundle\Entity\BaseGroup as BaseGroup;

/**
 * @ORM\Entity
 * @ORM\Table(name="sip_user_group")
 */
class Group extends BaseGroup
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="SIP\ResourceBundle\Entity\User\User", mappedBy="groups")
     */
    protected $users;


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
     * Add user
     *
     * @param \SIP\ResourceBundle\Entity\User\User $user
     *
     * @return Group
     */
    public function addUser(\SIP\ResourceBundle\Entity\User\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \SIP\ResourceBundle\Entity\User\User $user
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeUser(\SIP\ResourceBundle\Entity\User\User $user)
    {
        return $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \SIP\ResourceBundle\Entity\User\User[]
     */
    public function getUsers()
    {
        return $this->users;
    }
}
