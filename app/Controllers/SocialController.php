<?php

namespace App\Controllers;

use App\Models\User;
use Slim\Exception\NotFoundException;
use Illuminate\Database\Capsule\Manager as DB;

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
        return $response->withRedirect($this->router->pathFor('user.profile', ['slug' => $user->user_slug]));
    }

    public function unfollow($request, $response, $args)
    {
        $user = User::where('user_slug', $args['slug'])->first();

        if (!$user) {
            throw new NotFoundException($request, $response);
        }

        $currentUser = $this->auth->user();

        if ($user == $currentUser) {
            return $response->withRedirect($this->router->pathFor('user.profile', ['slug' => $user->user_slug]));
        }

        $subscription = DB::table('subscription')
                        ->where('follower_id', $currentUser->user_id)
                        ->where('followed_id', $user->user_id)
                        ->first();

        if (!$subscription) {
            return $response->withRedirect($this->router->pathFor('user.profile', ['slug' => $user->user_slug]));
        }

        $currentUser->following()->detach($user->user_id);

        $this->flash->addMessage('success', 'You have unfollowed ' . $user->user_name . '!');
        return $response->withRedirect($this->router->pathFor('user.profile', ['slug' => $user->user_slug]));
    }
}
