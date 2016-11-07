<?php

namespace App\Controllers;

use App\Models\User;
use Illuminate\Database\Capsule\Manager as DB;
use Slim\Exception\NotFoundException;

class HomeController extends Controller
{
    public function index($request, $response)
    {
        if (!$this->auth->check()) {
            return $this->view->render($response, 'homeNotSigned.twig');
        }

        $posts = DB::table('picture')
            ->select(['picture.id', 'picture.description', 'picture.created_at', 'picture.updated_at', 'user_name', 'user_slug'])
            ->leftJoin('users', 'picture.user_id', '=', 'users.user_id')
            ->leftJoin('subscription', 'users.user_id', '=', 'subscription.followed_id')
            ->where('subscription.follower_id', $this->auth->user()->user_id)
            ->orderBy('picture.created_at', 'desc')
            ->get();

        return $this->view->render($response, 'feed.twig', [
            'posts' => $posts
        ]);
    }

    public function search($request, $response)
    {
        $query = $request->getParam('q');

        if (!$query) {
            throw new NotFoundException($request, $response);
        }

        $users = null;
        $posts = null;

        if (starts_with($query, '#')) {
            $posts = $this->getPostsWithHashtag(substr($query, 1));
        } else {
            $users = User::where('user_name', 'like', '%' . $request->getParam('q') . '%')->get();
        }

        return $this->view->render($response, 'search.twig', [
            'users' => $users,
            'posts' => $posts,
            'q' => $request->getParam('q')
        ]);
    }

    private function getPostsWithHashtag($hashtag)
    {
        $posts = DB::table('picture')
            ->select(['picture.id', 'picture.description', 'picture.created_at', 'picture.updated_at', 'user_name', 'user_slug'])
            ->leftJoin('users', 'picture.user_id', '=', 'users.user_id')
            ->where('tags', 'like', '%' . $hashtag . '%')
            ->orderBy('picture.created_at', 'desc')
            ->get();

        return $posts;
    }
}
