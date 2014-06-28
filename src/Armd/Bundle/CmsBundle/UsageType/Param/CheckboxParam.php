<?php
/*
 * (c) Sukhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\UsageType\Param;

class CheckboxParam extends BaseParam
{
    /**
     * @param null $paramValue
     * @return array
     */
    public function getParamData()
    {
        return array($this->getName(), 'checkbox', array('data' => $this->getValue(),
                                                         'label' => $this->getTitle(),
                                                         'required' => $this->isRequirements()));
    }
}
