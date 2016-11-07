<?php

namespace App\Controllers;

use App\Auth\Auth;
use App\Validation\Validator;
use Slim\Flash\Messages;
use Slim\Router;
use Slim\Views\Twig;

/**
 * @property Auth auth
 * @property Messages flash
 * @property Twig view
 * @property Validator validator
 * @property Router router
 */
class Controller
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __get($property)
    {
        if ($this->container->{$property}) {
            return $this->container->{$property};
        }
    }
}
