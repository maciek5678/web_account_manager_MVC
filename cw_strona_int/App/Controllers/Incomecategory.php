<?php

namespace App\Controllers;

use \App\Models\User;
use \Core\View;
use \App\Auth;
use \App\Models\Incomes;
/**
 * Home controller
 *
 * PHP version 5.4
 */
class Incomecategory extends \Core\Controller
{
    public function newAction()
    {
	if(Auth::isloggedin())
		{
			$incomes = new Incomes();
	       $dataIncomes= $incomes ->takeIncomesCategoriesAndLimit($_SESSION['user_id']);
			View::renderTemplate('Incomecategory/new.html',[	
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
			$incomes ->insertIncomeCategoryAssignedToUser($_SESSION['user_id']);
			$dataIncomes= $incomes ->takeIncomesCategoriesAndLimit($_SESSION['user_id']);
			View::renderTemplate('Incomecategory/new.html',[	
			'dataIncomes' => $dataIncomes
			]);
		}
		else
		{
 			$this->redirect('/');
		}
	}
	
	public function saveAction()
    {
		$incomes = new Incomes();
		
		$incomes ->updateIncomeCategoryAssignedToUserLimit($_SESSION['user_id'], $_POST['salaryLimit'] , $_POST['catName'] );	
		
	}
	
	public function deleteAction()	
    {
		$incomes = new Incomes();		
		$incomes ->deleteIncomeCategoryAssignedToUserLimit($_SESSION['user_id'], $_POST['catName'] );
	}
	
	public function deletecategoryAction()	
    {
		$incomes = new Incomes();
		$incomes ->deleteIncomeCategoryAssignedToUser($_SESSION['user_id'], $_POST['limit']);
		$incomes ->deleteIncomes($_SESSION['user_id'], $_POST['limit']);
		$dataIncomes= $incomes ->takeIncomesCategoriesAndLimit($_SESSION['user_id']);
		View::renderTemplate('Incomecategory/new.html',[	
		'dataIncomes' => $dataIncomes
		]);
	}
}
