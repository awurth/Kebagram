<?php

/**
* Add your "extra" middleware
*/
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\AdminMiddleware;
use App\Middleware\SubscriberMiddleware;

$app->get('/', 'HomeController:index')->setName('home');

$app->get('/user/{slug}', 'ProfileController:view')->setName('user.profile');

$app->group('', function () {
    $this->get('/signup', 'AuthController:getSignUp')->setName('auth.signup');
    $this->post('/signup', 'AuthController:postSignUp');

    $this->get('/signin', 'AuthController:getSignIn')->setName('auth.signin');
    $this->post('/signin', 'AuthController:postSignIn');
})->add(new GuestMiddleware($container));

$app->group('', function () {
    $this->get('/signout', 'AuthController:getSignOut')->setName('auth.signout');

    $this->get('/me', 'ProfileController:editAccount')->setName('edit.account');

    $this->post('/saveEdit', 'ProfileController:saveEdit')->setName('saveEdit.account');

    $this->get('/password/change', 'PasswordController:getChangePassword')->setName('auth.password.change');
    $this->post('/password/change', 'PasswordController:postChangePassword');

    $this->get('/dashboard', 'AuthController:dashboard')->setName('dashboard');

    $this->get('/pic/add', 'PictureController:getAdd')->setName('picture.add');
    $this->post('/pic/add', 'PictureController:postAdd');

    $this->get('/user/{slug}/follow', 'SocialController:follow')->setName('user.follow');
    $this->get('/user/{slug}/unfollow', 'SocialController:unfollow')->setName('user.unfollow');
})->add(new AuthMiddleware($container));

$app->group('', function () {
    $this->get('/admin', 'AdminController:getIndex')->setName('admin.index');
    $this->post('/admin', 'AdminController:postIndex')->setName('admin.post');
})->add(new AdminMiddleware($container));

/* Subscription */
$app->get('/subscription', function ($request, $response) {
    return $this->view->render($response, 'subscriber/index.twig');
})->add(new SubscriberMiddleware($container));
