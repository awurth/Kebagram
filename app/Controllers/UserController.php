<?php

namespace App\Controllers;

use App\Models\User;
use Slim\Exception\NotFoundException;

class UserController extends Controller
{
    public function search($request, $response)
    {
        if (!$request->getParam('q')) {
            throw new NotFoundException($request, $response);
        }

        $users = User::where('user_name', 'like', '%' . $request->getParam('q') . '%')->get();

        return $this->view->render($response, 'user/search.twig', [
            'users' => $users,
            'q' => $request->getParam('q')
        ]);
    }
}
