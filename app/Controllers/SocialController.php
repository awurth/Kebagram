<?php

namespace App\Controllers;

use App\Models\Comment;
use App\Models\Picture;
use App\Models\User;
use Slim\Exception\NotFoundException;
use Illuminate\Database\Capsule\Manager as DB;
use Respect\Validation\Validator as v;

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

        $subscription = DB::table('subscription')
            ->where('follower_id', $currentUser->user_id)
            ->where('followed_id', $user->user_id)
            ->first();

        if ($subscription) {
            return $response->withRedirect($this->router->pathFor('user.profile', ['slug' => $user->user_slug]));
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

    public function postComment($request, $response, $args)
    {
        if (!v::notBlank()->validate($request->getParam('content'))) {
            $this->flash->addMessage('error', 'Your comment cannot be empty!');
            return $response->withRedirect($this->router->pathFor('home'));
        }

        $picture = Picture::find($args['id']);

        if (!$picture) {
            throw new NotFoundException($request, $response);
        }

        $comment = new Comment();
        $comment->content = $request->getParam('content');
        $comment->user()->associate($this->auth->user());
        $comment->picture()->associate($picture);
        $comment->save();

        $this->flash->addMessage('success', 'Comment added successfully!');
        return $response->withRedirect($this->router->pathFor('home'));
    }
}
