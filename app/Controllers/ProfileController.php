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
        return ( ($p == $p2) && size($p) > 6);
    }

    public function saveEdit($request,$response) {

        if (isset($_POST)) {

            switch( $request->getParam('what') ) {

                case "password" :

                    $mdp = $request->getParam('password');
                    $mdp2 = $request->getParam('password2');

                    if ($this->passwordMatches($mdp,$mdp2)) {
                        $user = User::find($this->auth->user()->user_id);
                        $user->user_password_hash = password_hash($mdp, PASSWORD_DEFAULT);
                        $user->save();
                        $this->flash->addMessage('info', 'Your password was changed');
                    }else{
                        $this->flash->addMessage('error', 'Error, your password needs at least 6 characters');
                    }

                    return $response->withRedirect($this->router->pathFor("edit.account"));
                break;

                default:

                    return $response->withRedirect($this->router->pathFor("home"));

            }


        }




    }



}
