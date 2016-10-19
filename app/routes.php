<?php

/**
* Add your "extra" middleware
*/
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\AdminMiddleware;
use App\Middleware\SubscriberMiddleware;

$app->get('/', 'HomeController:index')->setName('home');

$app->get('/@{user_name}', 'ProfileController:getIndex'); //alpha & numeric

$app->group('', function () {
    $this->get('/signup', 'AuthController:getSignUp')->setName('auth.signup');
    $this->post('/signup', 'AuthController:postSignUp');

    $this->get('/signin', 'AuthController:getSignIn')->setName('auth.signin');
    $this->post('/signin', 'AuthController:postSignIn');
})->add(new GuestMiddleware($container));

$app->group('', function () {
    $this->get('/signout', 'AuthController:getSignOut')->setName('auth.signout');

    $this->get('/password/change', 'PasswordController:getChangePassword')->setName('auth.password.change');
    $this->post('/password/change', 'PasswordController:postChangePassword');

    $this->get('/dashboard', 'AuthController:dashboard')->setName('dashboard');
})->add(new AuthMiddleware($container));

$app->group('', function () {
    $this->get('/admin', 'AdminController:getIndex')->setName('admin.index');
    $this->post('/admin', 'AdminController:postIndex')->setName('admin.post');
})->add(new AdminMiddleware($container));

/* Subscription */
$app->get('/subscription', function ($request, $response) {
    return $this->view->render($response, 'subscriber/index.twig');
})->add(new SubscriberMiddleware($container));