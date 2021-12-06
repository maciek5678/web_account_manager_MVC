<?php

namespace App\Controllers;

use \App\Models\User;
use \Core\View;
use \App\Auth;

/**
 * Home controller
 *
 * PHP version 5.4
 */
class Changeemail extends \Core\Controller
{

	public function newAction()
    {
		if(Auth::isloggedin())
		{
	        
			
			View::renderTemplate('Changeemail/new.html');
						
		}

		else
		{
 				$this->redirect('/');
		}
	}

	public function changeAction()
    {
		if(Auth::isloggedin())
		{
			$user= new User($_POST);
			if($user->changeemail())
			{
	
				View::renderTemplate('Changeemail/success.html');
					
			}
			else
			{
			View::renderTemplate('Changeemail/new.html',[
			'user' => $user
			]);
			}
		}
		else
		{
 			$this->redirect('/');
		}
	}
}
