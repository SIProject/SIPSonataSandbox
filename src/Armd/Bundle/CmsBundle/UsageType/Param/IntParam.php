<?php
/*
 * (c) Stepanov Andrey <isteep@gmail.com>
 */
namespace Armd\Bundle\CmsBundle\UsageType\Param;

use Symfony\Component\Validator\Constraints\Type;

class IntParam extends BaseParam
{
    /**
     * @param null $paramValue
     * @return array
     */
    public function getParamData()
    {
        return array(
            $this->getName(),
            'integer',
            array(
                'data' => (int)$this->getValue(),
                'label' => $this->getTitle(),
                'required' => $this->isRequirements(),
                'constraints' => array(
                    new Type(array('type' => 'numeric'))
                )
            )
        );
    }
}
