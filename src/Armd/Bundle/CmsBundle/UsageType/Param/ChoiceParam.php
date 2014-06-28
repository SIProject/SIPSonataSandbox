<?php
/*
 * (c) Isuhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\UsageType\Param;

class ChoiceParam extends BaseParam
{
    /**
     * @var array
     */
    protected $choiceList;

    /**
     * @var bool
     */
    protected $multiple;

    /**
     * @var array
     */
    protected $allowFields = array('choiceList', 'multiple');

    /**
     * @return bool
     */
    public function getMultiple()
    {
        return $this->multiple;
    }

    /**
     * @param $multiple
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;
    }

    /**
     * @return array
     */
    public function getChoiceList()
    {
        return $this->choiceList;
    }

    /**
     * @param $choiceList
     */
    public function setChoiceList($choiceList)
    {
        $this->choiceList = $choiceList;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        if (is_null($this->value)) {
            return $this->getDefault();
        }

        if ($this->getMultiple() && !is_array($this->value)) {
            return array();
        }

        return $this->value;
    }

    /**
     * @param null $paramValue
     * @return array
     */
    public function getParamData($paramValue = null)
    {
        return array(
            $this->getName(),
            'genemu_jqueryselect2_choice',
            array(
                'data' => $this->getValue(),
                'choices'  => $this->getChoiceList(),
                'label' => $this->getTitle(),
                'multiple' => $this->getMultiple(),
                'required' => $this->isRequirements(),
                'configs' => array('allowClear' => $this->isRequirements()?false: true)
            )
        );
    }
}