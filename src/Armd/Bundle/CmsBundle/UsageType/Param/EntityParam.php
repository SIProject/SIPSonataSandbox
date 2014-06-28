<?php
/*
 * (c) Isuhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\UsageType\Param;

use Doctrine\Bundle\DoctrineBundle\Registry;

class EntityParam extends BaseParam
{
    protected $entity;

    protected $addField;

    protected $viewField;

    protected $filterField;

    protected $filterValue;

    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected $doctrine;

    /**
     * @var bool
     */
    protected $multiple;

    /**
     * @var array
     */
    protected $allowFields = array('entity', 'addField', 'viewField', 'filterField', 'filterValue', 'multiple');

    /**
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

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
     * @param $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param $addField
     */
    public function setAddField($addField)
    {
        $this->addField = $addField;
    }

    public function getAddField()
    {
        return $this->addField;
    }

    /**
     * @param $viewField
     */
    public function setViewField($viewField)
    {
        $this->viewField = $viewField;
    }

    public function getViewField()
    {
        return $this->viewField;
    }

    /**
     * @param $viewField
     */
    public function setFilterField($filterField)
    {
        $this->filterField = $filterField;
    }

    public function getFilterField()
    {
        return $this->filterField;
    }

    /**
     * @param $viewField
     */
    public function setFilterValue($filterValue)
    {
        $this->filterValue = $filterValue;
    }

    public function getFilterValue()
    {
        return $this->filterValue;
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
                'label' => $this->getTitle(),
                'choices'  => $this->getChoiceList(),
                'multiple' => $this->getMultiple(),
                'required' => $this->isRequirements(),
                'configs' => array('allowClear' => $this->isRequirements()?false: true)
            )
        );
    }

    /**
     * @param \Armd\Bundle\CmsBundle\UsageType\Param\EntityParam $param
     * @return array
     */
    public function getChoiceList()
    {
        $choices = array();
        if ($this->getFilterField()) {
            $findMethod = "findBy" . $this->getFilterField();
            $entityes = $this->doctrine->getRepository($this->getEntity())
                ->$findMethod($this->getFilterValue(),
                array(strtolower($this->getAddField()) => 'ASC'));
        } else {
            $entityes = $this->doctrine->getRepository($this->getEntity())
                ->findBy(array(), array(strtolower($this->getAddField()) => 'ASC'));
        }

        $viewField = 'get' . $this->getViewField();
        $addField = 'get' . $this->getAddField();
        if ($entityes) {
            foreach ($entityes as $entity) {
                $choices[$entity->$addField()] = $entity->$viewField();
            }
        }
        return $choices;
    }
}