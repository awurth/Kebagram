<?php

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;

$config = require __DIR__ . '/../bootstrap/settings.php';

$capsule = new Manager();
$capsule->addConnection($config['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

Manager::schema()->drop('photos');
Manager::schema()->drop('users');

Manager::schema()->create('users', function (Blueprint $table) {
    $table->increments('id');
    $table->string('username')->unique();
    $table->string('slug')->unique();
    $table->string('email')->unique();
    $table->string('password');
    $table->string('name');
    $table->string('description');
    $table->boolean('active');
    $table->boolean('deleted');
    $table->boolean('type');
    $table->boolean('has_avatar');
    $table->string('remember_me_token');
    $table->dateTime('last_login');
    $table->timestamps();
});

Manager::schema()->create('photos', function (Blueprint $table) {
    $table->increments('id');
    $table->text('description');
    $table->text('url');
    $table->timestamps();
    $table->integer('user_id')->unsigned();
    $table->foreign('user_id')->references('id')->on('users');
});
