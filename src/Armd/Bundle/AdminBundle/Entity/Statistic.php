<?php

namespace Armd\Bundle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\ContentAbstractBundle\Entity\BaseContent;
use Armd\ContentAbstractBundle\Model\ResultCacheableInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="cms_statistic")
 */
class Statistic extends BaseContent implements ResultCacheableInterface
{
    /**
     * @ORM\Column(type="string")
     */
    private $counterId;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $gaCounterId;

    /**
     * @ORM\Column(type="string")
     */
    private $providerClass;
    
    /**
     * @ORM\Column(type="string")
     */
    private $appId;
    
    /**
     * @ORM\Column(type="string")
     */
    private $appSecret;
    
    /**
     * @ORM\Column(type="string")
     */
    private $userLogin;
    
    /**
     * @ORM\Column(type="string")
     */
    private $userPassword;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * Set counterId
     *
     * @param string $counterId
     */
    public function setCounterId($counterId)
    {
        $this->counterId = $counterId;
    }

    /**
     * Get counterId
     *
     * @return string 
     */
    public function getCounterId()
    {
        return $this->counterId;
    }
    
    /**
     * Set providerClass
     *
     * @param string $providerClass
     */
    public function setProviderClass($providerClass)
    {
        $this->providerClass = $providerClass;
    }

    /**
     * Get providerClass
     *
     * @return string 
     */
    public function getProviderClass()
    {
        return $this->providerClass;
    }
    
    /**
     * Set appId
     *
     * @param string $appId
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    /**
     * Get appId
     *
     * @return string 
     */
    public function getAppId()
    {
        return $this->appId;
    }
    
    /**
     * Set appSecret
     *
     * @param string $appSecret
     */
    public function setAppSecret($appSecret)
    {
        $this->appSecret = $appSecret;
    }

    /**
     * Get appSecret
     *
     * @return string 
     */
    public function getAppSecret()
    {
        return $this->appSecret;
    }
    
    /**
     * Set userLogin
     *
     * @param string $userLogin
     */
    public function setUserLogin($userLogin)
    {
        $this->userLogin = $userLogin;
    }

    /**
     * Get userLogin
     *
     * @return string 
     */
    public function getUserLogin()
    {
        return $this->userLogin;
    }
    
    /**
     * Set userPassword
     *
     * @param string $userPassword
     */
    public function setUserPassword($userPassword)
    {
        $this->userPassword = $userPassword;
    }

    /**
     * Get userPassword
     *
     * @return string 
     */
    public function getUserPassword()
    {
        return $this->userPassword;
    }

    /**
     * @var datetime $createdAt
     */
    protected $createdAt;

    /**
     * @var datetime $updatedAt
     */
    protected $updatedAt;

    /**
     * @var datetime $deletedAt
     */
    protected $deletedAt;

    /**
     * @var datetime $publishedAt
     */
    protected $publishedAt;

    /**
     * Set isActive
     *
     * @param integer $isActive
     * @return Statistic
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * Get isActive
     *
     * @return integer 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set createdAt
     *
     * @param \Datetime $createdAt
     * @return Statistic
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \Datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \Datetime $updatedAt
     * @return Statistic
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \Datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set deletedAt
     *
     * @param \Datetime $deletedAt
     * @return Statistic
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \Datetime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Set publishedAt
     *
     * @param \Datetime $publishedAt
     * @return Statistic
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }

    /**
     * Get publishedAt
     *
     * @return \Datetime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }



    /**
     * Set gaAccountId
     *
     * @param string $gaAccountId
     * @return Statistic
     */
    public function setGaCounterId($gaAccountId)
    {
        $this->gaCounterId = $gaAccountId;
        return $this;
    }

    /**
     * Get gaAccountId
     *
     * @return string
     */
    public function getGaCounterId()
    {
        return $this->gaCounterId;
    }

    /**
     * @return array
     */
    public function getCacheKeys()
    {
        return array('_statistic_list');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getProviderClass() . '- ' . $this->getId();
    }
}