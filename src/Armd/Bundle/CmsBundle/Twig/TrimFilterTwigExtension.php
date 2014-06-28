<?php

namespace Armd\Bundle\CmsBundle\Twig;
use Twig_Extension;
use Twig_Filter_Function;

class TrimFilterTwigExtension extends Twig_Extension
{
    public function getFilters() {
        return array(
            'trim'  => new Twig_Filter_Function('trim')
        );
    }

    public function getName()
    {
        return 'armd_cms_trim_filter_twig_extension';
    }
}
