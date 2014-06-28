<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Model;

class Layout
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $sourceBundleName;

    /**
     * @var bool
     */
    protected $default = false;

    public function __construct($name, $sourceBundleName, array $layoutDefinition)
    {
        $this->setName($name);
        $this->setSourceBundleName($sourceBundleName);

        if (isset($layoutDefinition['title'])) {
            $this->setTitle($layoutDefinition['title']);
        }

        if (isset($layoutDefinition['default'])) {
            $this->setDefault($layoutDefinition['default']);
        }
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param boolean $default
     */
    public function setDefault($default)
    {
        $this->default = (bool)$default;
    }

    /**
     * @return boolean
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $sourceBundleName
     */
    public function setSourceBundleName($sourceBundleName)
    {
        $this->sourceBundleName = $sourceBundleName;
    }

    /**
     * @return string
     */
    public function getSourceBundleName()
    {
        return $this->sourceBundleName;
    }

    /**
     * @param string $layoutName
     * @return string
     */
    public function getLayoutPath()
    {
        return $this->sourceBundleName . ":layout/" . $this->getName() . ":index.html.twig";
    }

    /**
     * @param $layoutName
     * @return string
     */
    public function getBlocksGridPath()
    {
        return $this->sourceBundleName . ":layout/" . $this->getName() . ":blocks.html.twig";
    }
}
