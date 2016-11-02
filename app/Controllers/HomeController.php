<?php

namespace App\Controllers;

use Illuminate\Database\Capsule\Manager as DB;

class HomeController extends Controller
{
    public function index($request, $response)
    {
        if (!$this->auth->check()) {
            return $this->view->render($response, 'homeNotSigned.twig');
        }

        $posts = DB::table('picture')
            ->select(['picture.id', 'description', 'picture.created_at', 'picture.updated_at', 'user_name', 'user_slug'])
            ->leftJoin('users', 'picture.user_id', '=', 'users.user_id')
            ->leftJoin('subscription', 'users.user_id', '=', 'subscription.followed_id')
            ->where('subscription.follower_id', $this->auth->user()->user_id)
            ->orderBy('picture.created_at', 'desc')
            ->get();

        return $this->view->render($response, 'feed.twig', [
            'posts' => $posts
        ]);
    }
}
