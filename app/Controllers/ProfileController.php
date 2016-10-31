<?php

namespace App\Controllers;

use App\Models\User;
use Slim\Views\Twig as View;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;

class ProfileController extends Controller
{
	/**
	* Render the users profile.
        * Have a look at [Laravel Eloquent Ignore Casing](http://stackoverflow.com/q/21213490/4892914)
	*
	* @param $args['user_name']
	*
	* @return mixed
	*/
    public function getIndex($request, $response, $args)
    {
        if(User::where('user_name', 'like', $args['user_name'])->first()){
            return $this->view->render($response, 'profiles/index.twig', ['user' => User::where('user_name', $args['user_name'])->first()]);
        }else{
            // If no user found, throw and show 404
            // return $response->withStatus(404)->withHeader('Content-Type', 'text/html')->write('Page not found');
            throw new \Slim\Exception\NotFoundException($request, $response);
        }
    }


    public function editAccount($request, $response) {
        $id = $this->auth->user()->user_id;
        $edit = NULL;
        if (isset($_GET)) { $edit = $_GET['what']; }
        return $this->view->render($response, 'profiles/editaccount.twig',["user" => User::find($id),"edit" => $edit]);
    }

    private function passwordMatches($p,$p2){
        return ( ($p == $p2) && strlen($p) > 6);
    }

    private function usernameAvailable($username)
    {
        if (!(ctype_space($username))) {
            if ( User::where('user_name',$username)->first == NULL ) {
                $this->flash->addMessage('info', 'Your username has changed');
                return true;
            }else{
                $this->flash->addMessage('error', 'Error, this username is already taken');
            }
        }else{
            $this->flash->addMessage('error', 'Error, this username contains illegal characters');
        }
        return false;
    }


    private function emailAvailable($email)
    {
        if (!(ctype_space($email))) {
            if ( User::where('user_email',$email)->first == NULL ) {
                $this->flash->addMessage('info', 'Your email address has changed');
                return true;
            }else{
                $this->flash->addMessage('error', 'Error, this address email is unavailable');
            }
        }else{
            $this->flash->addMessage('error', 'Error, this email address contains illegal characters');
        }
        return false;
    }

    private function me(){
        return User::find($this->auth->user()->user_id);
    }

    public function saveEdit($request,$response)
    {
        if (isset($_POST)) {
            switch( $request->getParam('what') ) {
                case "password" :
                    $mdp = $request->getParam('password');
                    $mdp2 = $request->getParam('password2');
                    if ($this->passwordMatches($mdp,$mdp2)) {
                        $this->flash->addMessage('info', 'Your password has changed');
                        $user = $this->me();
                        $user->user_password_hash = password_hash($mdp, PASSWORD_DEFAULT);
                        $user->save();
                    }else{
                        $this->flash->addMessage('error', 'Error, your password needs at least 6 characters');
                    }
                    return $response->withRedirect($this->router->pathFor("edit.account"));
                    break;

               case "username":
                        if ( $this->usernameAvailable($request->getParam('username')) ) {
                            $user = $this->me();
                            $user->user_name = $request->getParam('username');
                            $user->user_slug = strtolower($user->user_name);
                            $user->save();
                        }
                        return $response->withRedirect($this->router->pathFor("edit.account"));
                break;

                case "email":
                    if ( $this->emailAvailable($request->getParam('email')) ) {
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
        return $response->withRedirect($this->router->pathFor("home"));
    }





}
