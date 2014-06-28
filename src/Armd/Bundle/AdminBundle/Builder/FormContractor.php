<?php

namespace Armd\Bundle\AdminBundle\Builder;

use Sonata\DoctrineORMAdminBundle\Builder\FormContractor as BaseFormContractor;
use Sonata\AdminBundle\Admin\FieldDescriptionInterface;

class FormContractor extends BaseFormContractor
{
    const CLASS_NAME    = 'datepicker';
    const WIDGET_NAME   = 'single_text';
    const DATE_FORMAT   = 'dd.MM.yyyy';


    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions($type, FieldDescriptionInterface $fieldDescription)
    {
        return array_replace_recursive(parent::getDefaultOptions($type, $fieldDescription),
            self::getDefaultOptionsByType($fieldDescription->getType()));
    }

    /**
     * @param string $type
     * @return array
     */
    public function getDefaultOptionsByType($type)
    {
        $types = array(
            'date'      => self::getDateOptions(),
            'time'      => self::getTimeOptions(),
            'datetime'  => self::getDateTimeOptions(),
            'birthday'  => self::getDateOptions(),
        );

        return isset($types[$type]) ? $types[$type] : array();
    }

    /**
     * @return array
     */
    public static function getDateTimeOptions()
    {
        return array(
            'date_widget'   => self::WIDGET_NAME,
            'time_widget'   => self::WIDGET_NAME,
            'date_format'   => self::DATE_FORMAT,
            'attr'          => array('class' => self::CLASS_NAME),
        );
    }

    /**
     * @return array
     */
    public static function getDateOptions()
    {
        return array(
            'widget'   => self::WIDGET_NAME,
            'format'   => self::DATE_FORMAT,
            'attr'     => array('class' => self::CLASS_NAME),
        );
    }

    /**
     * @return array
     */
    public static function getTimeOptions()
    {
        return array(
            'widget'   => 'single_text',
        );
    }
}