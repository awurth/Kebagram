<?php

namespace App\Controllers;

use App\Models\Picture;
use App\Models\User;
use Slim\Exception\NotFoundException;

class ProfileController extends Controller
{
    public function view($request, $response, $args)
    {
        $user = User::where('user_slug', $args['slug'])->first();

        if (!$user) {
            throw new NotFoundException($request, $response);
        }

        $page = $request->getParam('page') ? (int) $request->getParam('page') : 1;

        $builder = Picture::where('user_id', $user->user_id);
        $count = $builder->count();

        $builder->orderBy('created_at', 'desc')->take(9);

        if ($page > 1) {
            $builder->skip(9 * ($page - 1));
        }

        $pictures = $builder->get();

        return $this->view->render($response, 'profiles/view.twig', [
            'user' => $user,
            'pictures' => $pictures,
            'count' => $count,
            'pages' => ceil($count / 9),
            'page' => $page
        ]);
    }

    public function editAccount($request, $response)
    {
        $edit = NULL;
        if (isset($_GET['what'])) {
            $edit = $_GET['what'];
        }
        return $this->view->render($response, 'profiles/editaccount.twig',["edit" => $edit]);
    }

    private function passwordMatches($p, $p2)
    {
        return (($p == $p2) && strlen($p) > 6);
    }

    private function usernameAvailable($username)
    {
        if (!(ctype_space($username))) {
            if (User::where('user_name', $username)->first() == NULL) {
                $this->flash->addMessage('info', 'Your username has changed');
                return true;
            } else {
                $this->flash->addMessage('error', 'Error, this username is already taken');
            }
        } else {
            $this->flash->addMessage('error', 'Error, this username contains illegal characters');
        }
        return false;
    }

    private function emailAvailable($email)
    {
        if (!(ctype_space($email))) {
            if ( User::where('user_email',$email)->first() == NULL ) {
                $this->flash->addMessage('info', 'Your email address has changed');
                return true;
            } else {
                $this->flash->addMessage('error', 'Error, this address email is unavailable');
            }
        } else {
            $this->flash->addMessage('error', 'Error, this email address contains illegal characters');
        }
        return false;
    }

    private function me()
    {
        return $this->auth->user();
    }

    public function saveEdit($request, $response)
    {
        if (isset($_POST)) {
            switch ($request->getParam('what')) {
                case "password" :
                    $mdp = $request->getParam('password');
                    $mdp2 = $request->getParam('password2');
                    if ($this->passwordMatches($mdp, $mdp2)) {
                        $this->flash->addMessage('info', 'Your password has changed');
                        $user = $this->me();
                        $user->user_password_hash = password_hash($mdp, PASSWORD_DEFAULT);
                        $user->save();
                    } else {
                        $this->flash->addMessage('error', 'Error, your password needs at least 6 characters');
                    }
                    return $response->withRedirect($this->router->pathFor("edit.account"));
                    break;

                case "username":
                    if ($this->usernameAvailable($request->getParam('username'))) {
                        $user = $this->me();
                        $user->user_name = $request->getParam('username');
                        $user->user_slug = strtolower($user->user_name);
                        $user->save();
                    }
                    return $response->withRedirect($this->router->pathFor("edit.account"));
                    break;

                case "email":
                    if ($this->emailAvailable($request->getParam('email'))) {
                        $user = $this->me();
                        $user->user_email = $request->getParam('email');
                        $user->save();
                    }
                    return $response->withRedirect($this->router->pathFor("edit.account"));
                    break;

                case "remove":
                    $user = $this->me();
                    $user->delete();
                    $this->flash->addMessage('info', 'Your account has been successfully deleted');
                    break;
                // Pas besoin de break puisque le cas trivial (?) est de retourner à la page d'accueil
            }
        }

        return $response->withRedirect($this->router->pathFor('home'));
    }
}
