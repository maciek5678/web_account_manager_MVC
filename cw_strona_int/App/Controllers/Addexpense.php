<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Expenses;
/**
 * Home controller
 *
 * PHP version 5.4
 */
class Addexpense extends \Core\Controller
{

    public function newAction()
    {
		if(Auth::isloggedin())
		{
			View::renderTemplate('Addexpense/new.html');
		} else{
            $this->redirect('/');
	
		}
    }
	
	public function addAction()
    {
		if(Auth::isloggedin())
		{
			$expenses = new Expenses($_POST);
			if($expenses->save($_SESSION['user_id']))
			{

				View::renderTemplate('Addexpense/new.html');
			}
			else{
				View::renderTemplate('Addexpense/new.html', [
				'expenses' => $expenses
				]);
	
				}
		} 
		else
		{
            $this->redirect('/');
	
		}
    }
}
