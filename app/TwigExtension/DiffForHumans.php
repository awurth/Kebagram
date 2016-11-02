<?php

namespace App\TwigExtension;

use App\Helper;

class DiffForHumans extends \Twig_Extension
{
    public function getName()
    {
        return 'diffForHumans';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('diffForHumans', array($this, 'diffForHumans'))
        ];
    }

    public function diffForHumans($datetime)
    {
        return Helper::diffForHumans($datetime);
    }
}