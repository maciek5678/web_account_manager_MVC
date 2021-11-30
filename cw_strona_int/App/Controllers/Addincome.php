<?php

namespace App\Controllers;

use \Core\View;

use \App\Models\Incomes;
use \App\Auth;

/**
 * Home controller
 *
 * PHP version 5.4
 */
class Addincome extends \Core\Controller
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
        View::renderTemplate('Addincome/new.html');
} else{
            $this->redirect('/');
	
}
    }
	
	    public function addAction()
    {
if(Auth::isloggedin()){
         $incomes = new Incomes($_POST);
if($incomes->save($_SESSION['user_id']))
{

        View::renderTemplate('Addincome/new.html');
}else{
		        View::renderTemplate('Addincome/new.html', [
			 'incomes' => $incomes
			
			
			
			]);
}
} else{
            $this->redirect('/');
	
}
    }
}