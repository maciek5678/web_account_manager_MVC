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
class Changelogin extends \Core\Controller
{

    /**
     * Before filter
     *
     * @return void
     */
    protected function before()
    {
        //echo "(before) ";
        //return false;
    }

    /**
     * After filter
     *
     * @return void
     */
    protected function after()
    {
        //echo " (after)";
    }

    /**
     * Show the index page
     *
     * @return void
     */
    public function newAction()
    {
if(Auth::isloggedin()){
	        View::renderTemplate('Changelogin/new.html');
					
	

	
}

else{
 				$this->redirect('/');
    }
	}
	    public function changeAction()
    {
if(Auth::isloggedin()){
	$user= new User($_POST);
	if($user->changelogin())
	{
	
	        View::renderTemplate('Changelogin/success.html');
					
	

	}
	else
	{
		View::renderTemplate('Changelogin/new.html',[
		 'user' => $user
		
		]);
	}
}

else{
 				$this->redirect('/');
    }
	}
}
