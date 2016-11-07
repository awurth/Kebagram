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
$app->get('/search', 'HomeController:search')->setName('search');

$app->group('', function () {
    $this->get('/signup', 'AuthController:getSignUp')->setName('auth.signup');
    $this->post('/signup', 'AuthController:postSignUp');
    $this->get('/signin', 'AuthController:getSignIn')->setName('auth.signin');
    $this->post('/signin', 'AuthController:postSignIn');
    $this->get('/forgotpassword','AuthController:forgotPassword')->setName('auth.forgotpassword');
    $this->post('/forgotpassword', 'AuthController:changeForgotPassword');
})->add(new GuestMiddleware($container));

$app->group('', function () {

    $this->get('/signout', 'AuthController:getSignOut')->setName('auth.signout');
    $this->get('/me', 'ProfileController:editAccount')->setName('edit.account');

    $this->post('/saveEdit', 'ProfileController:saveEdit')->setName('saveEdit.account');

    $this->get('/password/change', 'PasswordController:getChangePassword')->setName('auth.password.change');
    $this->post('/password/change', 'PasswordController:postChangePassword');

    $this->get('/pic/add', 'PictureController:getAdd')->setName('picture.add');
    $this->post('/pic/add', 'PictureController:postAdd');
    $this->post('/pic/relooking', 'PictureController:changeProfilePicture')->setName('profilePicture.add');
    $this->get('/pic/{id}/edit', 'PictureController:getEdit')->setName('picture.edit');
    $this->post('/pic/{id}/edit', 'PictureController:postEdit');
    $this->get('/pic/{id}/delete', 'PictureController:delete')->setName('picture.delete');

    $this->post('/liker', 'PictureController:likeDispatcher')->setName('photo.like');
    $this->get('/user/{slug}/follow', 'SocialController:follow')->setName('user.follow');
    $this->get('/user/{slug}/unfollow', 'SocialController:unfollow')->setName('user.unfollow');

    $this->post('/pic/{id}/comment', 'SocialController:postComment')->setName('comment.add');
    $this->get('/pic/{id}/comments', 'SocialController:getComments')->setName('comment.get');
    $this->get('/comment/{id}/delete', 'SocialController:deleteComment')->setName('comment.delete');
})->add(new AuthMiddleware($container));

$app->group('', function () {
    $this->get('/admin', 'AdminController:getIndex')->setName('admin.index');
    $this->post('/admin', 'AdminController:postIndex')->setName('admin.post');
})->add(new AdminMiddleware($container));
