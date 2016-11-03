<?php

namespace App\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function search($request, $response)
    {
        $users = User::where('user_name', 'like', '%' . $request->getParam('q') . '%')->get();

        return $this->view->render($response, 'user/search.twig', [
            'users' => $users
        ]);
    }
}
