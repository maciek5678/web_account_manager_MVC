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
class Registration extends \Core\Controller
{

    public function newAction()
    {
	   if(Auth::isloggedin())
	   {
		
			$this->redirect('/Menu/loggedin');
		
		}
		else
		{
			View::renderTemplate('Registration/new.html');
		}
    }
	
	public function addAction()
    {
         $user = new User($_POST);

		if ($user->save()) 
		{
			$id=$user->getUserId($_POST['login']);
			$user->copyCategory($id);
			$user->copyPayment($id);
			$user->copyCategoryInc($id);

            $this->redirect('/Registration/success');

		} 
		else 
		{

				View::renderTemplate('Registration/new.html', [
                'user' => $user
				]);

		}
    }
	public function successAction()
    {
   
        View::renderTemplate('Registration/success.html');
    }
}
