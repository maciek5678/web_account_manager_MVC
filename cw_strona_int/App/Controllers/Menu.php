<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
/**
 * Home controller
 *
 * PHP version 5.4
 */
class Menu extends \Core\Controller
{

    public function loggedinAction()
    {

		
	if(isset($_POST['username']) && isset($_POST['pass']))
		{
		$user = User::authenticate($_POST['username'], $_POST['pass']);
        


			if ($user) 
			{
				Auth::login($user);
 
				View::renderTemplate('Menu/loggedin.html', [
                'user' => $user

				]);

			} else 
			{



				View::renderTemplate('Home/index.html', [
			     'user' => $user
				]);
			}
		}
		else
		{
			
			if(!Auth::isloggedin())
			{
            $this->redirect('/');
		

	
			}
			else
			{
	
		    $user = User::findByID();
			View::renderTemplate('Menu/loggedin.html', [
			'user' => $user

            ]);
	
			}
			
			
		}
	}
	public function loggedoutAction()
	{
		Auth::logout();
		View::renderTemplate('Home/index.html', [
		'user' => "unknown"
         ]);
	}
}
