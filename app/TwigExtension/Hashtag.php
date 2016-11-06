<?php

namespace App\TwigExtension;

use Slim\Interfaces\RouterInterface;

class Hashtag extends \Twig_Extension
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function getName()
    {
        return 'hashtag';
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('hashtag', array($this, 'hashtag'), array('is_safe' => array('html')))
        ];
    }

    public function hashtag($string)
    {
        return preg_replace('/(#(\w+))/', '<a href="' . $this->router->pathFor('search') . '?q=%23$2">$1</a>', $string);
    }
}