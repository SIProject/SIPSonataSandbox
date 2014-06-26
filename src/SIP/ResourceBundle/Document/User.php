<?php
/*
 * (c) Suhinin Ilja <iljasuhinin@gmail.com>
 */
namespace SIP\ResourceBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Sonata\UserBundle\Document\BaseUser as BaseUser;
use Sonata\UserBundle\Model\UserInterface;

/**
 * @MongoDB\Document(collection="user")
 */
class User extends BaseUser
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\ReferenceMany(targetDocument="SIP\ResourceBundle\Document\Group")
     */
    protected $groups;

    /**
     * @MongoDB\String
     */
    protected $username;

    /**
     * @MongoDB\String
     * @MongoDB\UniqueIndex(order="asc")
     */
    protected $usernameCanonical;

    /**
     * @MongoDB\String
     */
    protected $email;

    /**
     * @MongoDB\String
     * @MongoDB\UniqueIndex(order="asc")
     */
    protected $emailCanonical;

    /**
     * @MongoDB\Boolean
     */
    protected $enabled;

    /**
     * @MongoDB\String
     */
    protected $salt;

    /**
     * @MongoDB\String
     */
    protected $password;

    /**
     * @MongoDB\Date
     */
    protected $lastLogin;

    /**
     * @MongoDB\Boolean
     */
    protected $locked;

    /**
     * @MongoDB\Boolean
     */
    protected $expired;

    /**
     * @MongoDB\Date
     */
    protected $expiresAt;

    /**
     * @MongoDB\String
     */
    protected $firstname;

    /**
     * @MongoDB\String
     */
    protected $lastname;

    /**
     * @MongoDB\String
     */
    protected $website;

    /**
     * @MongoDB\String
     */
    protected $biography;

    /**
     * @MongoDB\String
     */
    protected $phone;

    /**
     * @MongoDB\String
     */
    protected $facebookUid;

    /**
     * @MongoDB\String
     */
    protected $facebookName;

    /**
     * @MongoDB\hash
     */
    protected $facebookData;

    /**
     * @MongoDB\String
     */
    protected $twitterUid;

    /**
     * @MongoDB\String
     */
    protected $twitterName;

    /**
     * @MongoDB\hash
     */
    protected $twitterData;

    /**
     * @MongoDB\String
     */
    protected $gplusUid;

    /**
     * @MongoDB\String
     */
    protected $gplusName;

    /**
     * @MongoDB\hash
     */
    protected $gplusData;

    /**
     * @MongoDB\String
     */
    protected $confirmationToken;

    /**
     * @MongoDB\Date
     */
    protected $passwordRequestedAt;

    /**
     * @MongoDB\Hash
     */
    protected $roles;

    /**
     * @MongoDB\String
     */
    protected $token;

    /**
     * @MongoDB\Boolean
     */
    protected $credentialsExpired;

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
     * Returns the gender list
     *
     * @return array
     */
    public static function getGenderList()
    {
        return array(
            UserInterface::GENDER_UNKNOWN => 'gender_unknown',
            UserInterface::GENDER_FEMALE  => 'gender_female',
            UserInterface::GENDER_MALE    => 'gender_male',
        );
    }
}