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
class Changepassword extends \Core\Controller
{

    public function newAction()
    {
	if(Auth::isloggedin())
		{
					View::renderTemplate('Changepassword/new.html');
							
			
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
		if($user->changepassword())
			{
	
				View::renderTemplate('Changepassword/success.html');
					
	

			}
			else
			{
				
				
			View::renderTemplate('Changepassword/new.html',[
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
