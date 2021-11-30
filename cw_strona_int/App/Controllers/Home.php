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
class Home extends \Core\Controller
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
    public function indexAction()
    {
if(Auth::isloggedin()){
	
					
					$this->redirect('/Menu/loggedin');

	
}

else{
        View::renderTemplate('Home/index.html', [
		'user' => "unknown"
		
		]);
    }
	}
}
