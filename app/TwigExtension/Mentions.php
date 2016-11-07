<?php

namespace App\TwigExtension;

use Slim\Interfaces\RouterInterface;

class Mentions extends \Twig_Extension
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function getName()
    {
        return 'mentions';
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('mentions', array($this, 'mentions'), array('is_safe' => array('html')))
        ];
    }

    public function mentions($string)
    {
        return preg_replace('/(@(\w+))/', '<a href="' . $this->router->pathFor('home') . 'user/$2">$1</a>', $string);
    }
}