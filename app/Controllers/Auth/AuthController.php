<?php

namespace App\Controllers\Auth;

use App\Auth\Auth;
use App\Controllers\ProfileController;
use App\Models\User;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;

class AuthController extends Controller
{


    /**
    * Render sign-in page
    */
    public function getSignIn($request, $response)
    {
        return $this->view->render($response, 'signin.twig');
    }

    /**
    * Log out
    */
        public function getSignOut($request, $response)
    {
        $this->auth->logout();

        return $response->withRedirect($this->router->pathFor('home'));
    }

    /**
    * Sign the user in with the provided credentials.
    *
    * @param string $user_email
    * @param string $user_password
    * @param string reg
    *
    * @return bool
    */
    public function postSignIn($request, $response)
    {
        /**
        * Check if the fields are valied. op is a hidden field. To prevent bots
        */
        $validation = $this->validator->validate($request, [
            'op' => v::equals('reg'),
        ]);

        /**
        * If the fields fail, then redirect back to signup
        */
        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        $auth = $this->auth->attempt(
            $request->getParam('user_email'),
            $request->getParam('user_password')
        );

        if (!$auth) {
            $this->flash->addMessage('error', 'Could not sign you in with those details.');
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        // If Auth successfull, then redirect to choosen location
        return $response->withRedirect($this->router->pathFor('dashboard'));
    }

    /**
    * Render sign-up page
    */
    public function getSignUp($request, $response)
    {
        return $this->view->render($response, 'signup.twig');
    }


    /**
    * Register a new user
    *
    * @param string $user_name
    * @param string $user_email
    * @param string $user_password
    * @param string reg
    *
    * @return bool
    */
    public function postSignUp($request, $response)
    {
        /**
        * Check if the fields are valied. op is a hidden field, to prevent bots
        */
        v::with('App\\Validation\\Rules\\');
        
        $validation = $this->validator->validate($request, [
            'user_email' => v::noWhitespace()->notEmpty()->email()->emailAvailable(),
            'user_name' => v::noWhitespace()->notEmpty()->alpha(),
            'user_password' => v::noWhitespace()->notEmpty()->length(6),
            'user_password_confirm' => v::equals($request->getParam('user_password')),
            'op' => v::equals('reg'),
        ]);

        /**
        * If the fields fail, then redirect back to signup
        */
        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('auth.signup'));
        }

        /**
        * If validation is OK, then continue with registration.
        */
        $user = User::create([
            'user_email' => $request->getParam('user_email'),
            'user_name' => $request->getParam('user_name'),
            'user_slug' => $this->generateSlug($request->getParam('user_name')),
            'user_password_hash' => password_hash($request->getParam('user_password'), PASSWORD_DEFAULT),
        ]);

        if($this->auth->attempt($user->user_email, $request->getParam('user_password'))){
            /** Add a flas message that everything went ok **/
            $this->flash->addMessage('success', 'You have been signed up!');

            /** On success registration, redirect to dashboard */
            return $response->withRedirect($this->router->pathFor('home'));
        }
        return false;
    }

    /**
    * Render dashboard
    */
    public function dashboard($request, $response)
    {
        return $this->view->render($response, 'auth/dashboard/dashboard.twig');
    }


    public function generateSlug($text) {
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = trim($text, '-');
        $text = preg_replace('~[^A-Za-z0-9.]+~', '-', $text);
        return strtolower($text);
    }

    public function forgotPassword($request, $response)
    {
        return $this->view->render($response, 'auth/password/forgot.twig');
    }

    public function changeForgotPassword($request, $response)
    {
        if (isset($_POST)) {
            if($request->getParam('username') && $request->getParam('email')
                && $request->getParam('password') && $request->getParam('password2')) {

                $username = $request->getParam('username');
                $email = $request->getParam('email');
                $mdp = $request->getParam('password');
                $mdp2 = $request->getParam('password2');
                $user = User::where("user_name","=",$username)->first();
                if($user){
                    if($user->user_email == $email){
                        if($mdp == $mdp2){
                            $user->user_password_hash = password_hash($mdp, PASSWORD_DEFAULT);
                            $user->save();
                            $this->flash->addMessage('success', 'Success');
                            return $response->withRedirect($this->router->pathFor('auth.signin'));
                        }else{
                            $this->flash->addMessage('error', 'Error, Passwords do not match');
                            return $response->withRedirect($this->router->pathFor('auth.forgotpassword'));
                        }
                    }
                }
                $this->flash->addMessage('error', 'Error, The user does not exist');
                return $response->withRedirect($this->router->pathFor('auth.forgotpassword'));
            }else{
                $this->flash->addMessage('error', 'Error, Tout les champs doivent etre remplis');
                return $response->withRedirect($this->router->pathFor('auth.forgotpassword'));
            }

        }
        return $this->view->render($response, 'auth/password/forgot.twig');
    }


}