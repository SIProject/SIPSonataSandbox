<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Entity;

/**
 * Armd\Bundle\CmsBundle\Entity\BaseContainer
 */
abstract class BaseContainer implements ContainerIntrface
{
    /**
     * @var array $settings
     */
    protected $settings;

    /**
     * Check is UsageType has settings for this Container
     *
     * @param $name
     * @param string $name
     * @return bool
     */
    public function hasSetting($name)
    {
        return isset($this->settings[$name]);
    }

    /**
     * Return settings for some UsageType
     *
     * @param string $name
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getSetting($name)
    {
        if ( !$this->hasSetting($name) ) {
            return null;
        }
        return $this->settings[$name];
    }

    /**
     * @param array $settings
     */
    public function copySettings($settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param array $settings
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
    }
}