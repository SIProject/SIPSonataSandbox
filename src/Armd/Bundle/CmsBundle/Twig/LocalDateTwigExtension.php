<?php
/*
 * (c) Stepanov Andrey <isteep@gmail.com>
 */
namespace Armd\Bundle\CmsBundle\Twig;

use Twig_Extension;
use Twig_Filter_Method;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

class LocalDateTwigExtension extends Twig_Extension
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Translation\Translator
     */
    protected $translator;

    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function getFilters()
    {
        return array(
            'localDate' => new Twig_Filter_Method($this, 'localDateFilter'),
        );
    }

    public function localDateFilter($datetime, $format)
    {
        /** @var \DateTime $datetime */
        if (!$datetime instanceof \DateTime) {
            return '';
        }

        if (strpos($format, 'F') !== false) {
            $monthLabel = $datetime->format('F');
            if (strpos($format, 'd') !== false || strpos($format, 'j') !== false) {
                $monthLabel = 'p_' . $datetime->format('F');
            }
            $month = $this->translator->trans($monthLabel, array(), 'localdate');
            $format = str_replace('F', $month, $format);
        }

        if (strpos($format, 'l') !== false) {
            $weekDay = $this->translator->trans($datetime->format('l'), array(), 'localdate');
            $format = str_replace('l', $weekDay, $format);
        }

        return $datetime->format($format);
    }

    public function getName()
    {
        return 'armd_cms_rudate_twig_extension';
    }
}
