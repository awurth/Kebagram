<?php

namespace App\Controllers;

use Slim\Views\Twig as View;

class HomeController extends Controller
{
    public function index($request, $response)
    {
        if ($this->auth->user()->user_id == NULL){
            return $this->view->render($response, 'homeNotSigned.twig');
        }
        return $this->view->render($response, 'home.twig');
    }
}
