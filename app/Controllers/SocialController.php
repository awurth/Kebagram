<?php

namespace App\Controllers;

use App\Models\User;
use Slim\Exception\NotFoundException;

class SocialController extends Controller
{
    public function follow($request, $response, $args)
    {
        $user = User::where('user_slug', $args['slug'])->first();

        if (!$user) {
            throw new NotFoundException($request, $response);
        }

        $currentUser = $this->auth->user();

        if ($user == $currentUser) {
            throw new \Exception('You cannot follow yourself!');
        }

        $currentUser->following()->attach($user->user_id, ['created_at' => new \DateTime()]);

        $this->flash->addMessage('success', 'You are now following ' . $user->user_name . '!');
        return $response->withRedirect($this->router->pathFor('home'));
    }
}
