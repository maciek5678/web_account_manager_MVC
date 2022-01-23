<?php

namespace App\Controllers;

use \Core\View;

use \App\Models\Incomes;
use \App\Auth;
use \App\Flash;

/**
 * Home controller
 *
 * PHP version 5.4
 */
class Addincome extends \Core\Controller
{

    public function newAction()
    {
		if(Auth::isloggedin())
		{
			$incomes = new Incomes($_POST);
			$dataIncomes= $incomes ->takeIncomesCategories($_SESSION['user_id']);
			View::renderTemplate('Addincome/new.html',[	
			'dataIncomes' => $dataIncomes
			]);

		} 
		else
		{
            $this->redirect('/');
		}
    }
	
	public function addAction()
    {
		if(Auth::isloggedin())
		{
         $incomes = new Incomes($_POST);
		if($incomes->save($_SESSION['user_id']))
			{
			Flash::addMessage('Dodanie zakończone pomyślnie');
			$dataIncomes= $incomes ->takeIncomesCategories($_SESSION['user_id']);
			View::renderTemplate('Addincome/new.html',[	
			'dataIncomes' => $dataIncomes
			]);
			}
			else
			{
				Flash::addMessage('Wystąpił błąd', Flash::WARNING);
				$dataIncomes= $incomes ->takeIncomesCategories($_SESSION['user_id']);
		        View::renderTemplate('Addincome/new.html', [
				'incomes' => $incomes,
				'dataIncomes' => $dataIncomes
				]);
			}
		} 
		else
		{
            $this->redirect('/');
	
		}
    }
	
	
	

}
