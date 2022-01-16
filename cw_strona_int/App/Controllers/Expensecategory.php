<?php

namespace App\Controllers;

use \App\Models\Expenses;
use \Core\View;
use \App\Auth;

/**
 * Home controller
 *
 * PHP version 5.4
 */
class Expensecategory extends \Core\Controller
{
    public function newAction()
    {
	if(Auth::isloggedin())
		{
			$expenses = new Expenses();
	        $dataExpenses= $expenses ->takeExpensesCategoriesAndLimit($_SESSION['user_id']);
			View::renderTemplate('Expensecategory/new.html',[	
			'dataExpenses' => $dataExpenses
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
			

					
			$expenses = new Expenses($_POST);
			$expenses ->insertExpenseCategoryAssignedToUser($_SESSION['user_id']);
	       $dataExpenses= $expenses ->takeExpensesCategoriesAndLimit($_SESSION['user_id']);

			View::renderTemplate('Expensecategory/new.html',[	
			'dataExpenses' => $dataExpenses
			]);

	
		}

		else
		{
 				$this->redirect('/');
		}
	}
	
	public function saveAction()
    {
		$expenses  = new Expenses();
		$expenses ->updateExpenseCategoryAssignedToUserLimit($_SESSION['user_id'], $_POST['salaryLimit'] , $_POST['catName'] );
	}
	public function deleteAction()
	
    {
		$expenses  = new Expenses();
		$expenses ->deleteExpenseCategoryAssignedToUserLimit($_SESSION['user_id'], $_POST['catName'] );

	}
	
	public function deletecategoryAction()	
    {
		$expenses = new Expenses();
		$expenses ->deleteExpenseCategoryAssignedToUser($_SESSION['user_id'], $_POST['limit']);
				$expenses ->deleteExpenses($_SESSION['user_id'], $_POST['limit']);
			       $dataExpenses= $expenses ->takeExpensesCategoriesAndLimit($_SESSION['user_id']);


			View::renderTemplate('Expensecategory/new.html',[	
			'dataExpenses' => $dataExpenses
			]);

	}

}
