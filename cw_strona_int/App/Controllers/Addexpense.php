<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Expenses;
use \App\Models\User;
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
			$payments= new User();
			$expenses = new Expenses();
			$dataPayments= $payments ->takePaymentsCategories($_SESSION['user_id']);
			$dataExpenses= $expenses ->takeExpensesCategoriesAndLimit($_SESSION['user_id']);
			
			View::renderTemplate('Addexpense/new.html',[
			'dataPayments' => $dataPayments,
			'dataExpenses' => $dataExpenses
			]);
		} else
		{
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

				$payments= new User();
				
				$dataPayments= $payments ->takePaymentsCategories($_SESSION['user_id']);
				$dataExpenses= $expenses ->takeExpensesCategoriesAndLimit($_SESSION['user_id']);
				Flash::addMessage('Dodanie zakończone pomyślnie');
				
				View::renderTemplate('Addexpense/new.html',[
				'dataPayments' => $dataPayments,
				'dataExpenses' => $dataExpenses
				]);
			}
			else
			{
				$payments= new User();
				$dataPayments= $payments ->takePaymentsCategories($_SESSION['user_id']);
				$dataExpenses= $expenses ->takeExpensesCategoriesAndLimit($_SESSION['user_id']);
				Flash::addMessage('Wystąpił błąd', Flash::WARNING);
				View::renderTemplate('Addexpense/new.html', [
				'expenses' => $expenses,
				'dataPayments' => $dataPayments,
				'dataExpenses' => $dataExpenses
				]);
	
			}
		} 
		else
		{
            $this->redirect('/');
	
		}
    }
	
	public function verifyAction()
	{
		$expenses = new Expenses();
		$data=$expenses->getData($_POST['data']);
		$sum_expenses=$expenses->getExpensesByCategoryAndUserAndDate($data, $_POST['category'], $_SESSION['user_id']);
	
		if(!empty( $_POST['limit']))
		{
			if(!empty( $sum_expenses [0] ['SUM(expenses.amount)']))
			{
				$difference= $expenses->difference($_POST['limit'], $sum_expenses [0] ['SUM(expenses.amount)'] );
				$sum_balance= $expenses->sumExpensesAndCurrentIncome($sum_expenses [0] ['SUM(expenses.amount)'],$_POST['kwota'] );
				$color=$expenses->color($_POST['limit'],$sum_balance);
		
				View::renderTemplate('Addexpense/change.html', [
				'limit' => $_POST['limit'],
				'spended' => $sum_expenses [0] ['SUM(expenses.amount)'],
				'diff' => $difference,
				'sum' => $sum_balance,
				'color' => $color
				]);
			} 
	
			else

			{
	
				$color=$expenses->color($_POST['limit'],$_POST['kwota']);
				View::renderTemplate('Addexpense/change.html', [
				'limit' => $_POST['limit'],
				'spended' => '0,00',
				'diff' => $_POST['limit'],
				'sum' => $_POST['kwota'],
				'color' => $color
				]);	
			}		


	}

	
	}
	
}
