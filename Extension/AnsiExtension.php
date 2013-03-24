<?php
namespace Lsw\AutomaticUpdateBundle\Extension;

/**
 * ANSI extention
 *
 */
class AnsiExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('ansi2html', 'Lsw\AutomaticUpdateBundle\Extension\Ansi::ansi2html'),
        );
    }

    public function getName()
    {
        return 'lsw_ansi_extension';
    }

}

