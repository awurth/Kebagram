<?php

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;

$config = require __DIR__ . '/../bootstrap/settings.php';

$capsule = new Manager();
$capsule->addConnection($config['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

Manager::schema()->dropIfExists('hashtag_picture');
Manager::schema()->dropIfExists('hashtag');
Manager::schema()->dropIfExists('picture_rating');
Manager::schema()->dropIfExists('picture');
Manager::schema()->dropIfExists('users');
Manager::schema()->dropIfExists('subscription');
Manager::schema()->dropIfExists('comment');

Manager::schema()->create('users', function (Blueprint $table) {
    $table->increments('user_id');
    $table->string('session_id', 48)->nullable();
    $table->string('user_name', 64)->unique();
    $table->string('user_slug', 64)->unique();
    $table->string('user_password_hash');
    $table->string('user_email')->unique();
    $table->boolean('user_active')->default(0);
    $table->boolean('user_deleted')->default(0);
    $table->boolean('user_account_type')->default(1);
    $table->boolean('user_has_avatar')->default(0);
    $table->string('user_remember_me_token', 64)->nullable();
    $table->bigInteger('user_suspension_timestamp')->nullable();
    $table->bigInteger('user_last_login_timestamp')->nullable();
    $table->boolean('user_failed_logins')->default(0);
    $table->integer('user_last_failed_login')->nullable();
    $table->string('user_activation_hash')->nullable();
    $table->boolean('user_profile')->default(1);
    $table->string('user_password_reset_hash')->nullable();
    $table->bigInteger('user_password_reset_timestamp')->nullable();
    $table->timestamps();
});

Manager::schema()->create('picture', function (Blueprint $table) {
    $table->increments('id');
    $table->text('description');
    $table->text('url');
    $table->timestamps();
    $table->integer('user_id')->unsigned();
    $table->foreign('user_id')->references('user_id')->on('users');
});

Manager::schema()->create('picture_rating', function (Blueprint $table) {
    $table->increments('id');
    $table->boolean('rate');
    $table->integer('user_id')->unsigned();
    $table->integer('picture_id')->unsigned();
    $table->foreign('user_id')->references('user_id')->on('users');
    $table->foreign('picture_id')->references('id')->on('picture');
});

Manager::schema()->create('subscription', function (Blueprint $table) {
    $table->integer('follower_id')->unsigned();
    $table->integer('followed_id')->unsigned();
    $table->dateTime('created_at');
    $table->primary(['follower_id', 'followed_id']);
    $table->foreign('follower_id')->references('user_id')->on('users');
    $table->foreign('followed_id')->references('user_id')->on('users');
});

Manager::schema()->create('comment', function (Blueprint $table) {
    $table->increments('id');
    $table->text('content');
    $table->integer('user_id')->unsigned();
    $table->integer('picture_id')->unsigned();
    $table->timestamps();
    $table->foreign('user_id')->references('user_id')->on('users');
    $table->foreign('picture_id')->references('id')->on('picture');
});

Manager::schema()->create('hashtag', function (Blueprint $table) {
    $table->increments('id');
    $table->string('name');
    $table->timestamps();
});

Manager::schema()->create('hashtag_picture', function (Blueprint $table) {
    $table->integer('hashtag_id')->unsigned();
    $table->integer('picture_id')->unsigned();
    $table->primary(['hashtag_id', 'picture_id']);
    $table->foreign('hashtag_id')->references('id')->on('hashtag');
    $table->foreign('picture_id')->references('id')->on('picture');
});
