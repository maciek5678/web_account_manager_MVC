<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\User;
use \App\Models\Incomes;
use \App\Models\Expenses;
/**
 * Home controller
 *
 * PHP version 5.4
 */
class Balanceoverwiev extends \Core\Controller
{

    public function newAction()
    {
	if(Auth::isloggedin())
		{
			$user = new User();
			$incomes = new Incomes();
			$expenses = new Expenses();
			$data= $user->data('currentMonth');
			$list_incomes=$incomes->takeIncomesTable($_SESSION['user_id'],$data);
			$sum_incomes=$incomes->takeIncomesSum($_SESSION['user_id'],$data);
			$list_expenses=$expenses->takeExpensesTable($_SESSION['user_id'],$data);
			$sum_expenses=$expenses->takeExpensesSum($_SESSION['user_id'],$data);
 			$below0= $user->below0($sum_incomes [0] ['SUM(incomes.Amount)'] , $sum_expenses [0] ['SUM(expenses.Amount)']);	 
			$arg= $expenses->showCharDraft($_SESSION['user_id'], $data);
			View::renderTemplate('Balanceoverwiev/new.html',[	
			'list_incomes' => $list_incomes,
			'list_expenses' => $list_expenses,
			'sum_expenses' => $sum_expenses,
			'sum_incomes' => $sum_incomes,
			'below0' => $below0,
			'arg' => $arg
			]);
		} 
		else
		{
            $this->redirect('/');
		}
    }
	public function showAction()
    {
	if(Auth::isloggedin())
		{
		$user = new User();
		$incomes = new Incomes();
		$expenses = new Expenses();
		if($_POST['period'] !='unusual')
			{
				$data= $user->data($_POST['period']);
				$list_incomes=$incomes->takeIncomesTable($_SESSION['user_id'],$data);
				$sum_incomes=$incomes->takeIncomesSum($_SESSION['user_id'],$data);
				$list_expenses=$expenses->takeExpensesTable($_SESSION['user_id'],$data);
				$sum_expenses=$expenses->takeExpensesSum($_SESSION['user_id'],$data);
				$selectedCurrentMonth= $user->currentMonthPeriod($_POST['period']);
				$selectedPreviousMonth= $user->previousMonthPeriod($_POST['period']);
				$selectedCurrentYear= $user->currentYearPeriod($_POST['period']);
				$arg= $expenses->showCharDraft($_SESSION['user_id'], $data);
				$below0= $user->below0($sum_incomes [0] ['SUM(incomes.Amount)'] , $sum_expenses [0] ['SUM(expenses.Amount)']);	 
				View::renderTemplate('Balanceoverwiev/new.html',[
				'list_incomes' => $list_incomes,
				'list_expenses' => $list_expenses,
				'sum_expenses' => $sum_expenses,
				'sum_incomes' => $sum_incomes,
				'selectedCurrentMonth'=>$selectedCurrentMonth,
				'selectedPreviousMonth'=>$selectedPreviousMonth,
				'selectedCurrentYear'=>$selectedCurrentYear,
				'below0' => $below0,
				'arg' => $arg
				]);
				

			}
			else
			{
				$user = new User();
				$incomes = new Incomes();
				$expenses = new Expenses();

				$list_incomes=$incomes->takeIncomesTableUnusual($_SESSION['user_id'],$_POST['datapocz'],$_POST['datakonc']);
				$list_expenses=$expenses->takeExpensesTableUnusual($_SESSION['user_id'],$_POST['datapocz'],$_POST['datakonc']);
				$sum_incomes=$incomes->takeIncomesSumUnusual($_SESSION['user_id'],$_POST['datapocz'],$_POST['datakonc']);
				$sum_expenses=$expenses->takeExpensesSumUnusual($_SESSION['user_id'],$_POST['datapocz'],$_POST['datakonc']);
				$selectedCurrentMonth= $user->currentMonthPeriod($_POST['period']);
				$selectedPreviousMonth= $user->previousMonthPeriod($_POST['period']);
				$selectedCurrentYear= $user->currentYearPeriod($_POST['period']);
				$selectedUsusualPeriod= $user->unusualPeriod($_POST['period']);
				$arg= $expenses->showCharDraftUnusual($_SESSION['user_id'], $_POST['datapocz'],$_POST['datakonc']);
				$below0= $user->below0($sum_incomes [0] ['SUM(incomes.Amount)'] , $sum_expenses [0] ['SUM(expenses.Amount)']);
			
				View::renderTemplate('Balanceoverwiev/new.html',[
				'list_incomes' => $list_incomes,
				'list_expenses' => $list_expenses,
				'sum_expenses' => $sum_expenses,
				'sum_incomes' => $sum_incomes,
				'selectedCurrentMonth'=>$selectedCurrentMonth,
				'selectedPreviousMonth'=>$selectedPreviousMonth,
				'selectedCurrentYear'=>$selectedCurrentYear,	
				'selectedUsusualPeriod'=>$selectedUsusualPeriod,
				'below0' => $below0,
				'arg' => $arg,
				'datapocz' =>$_POST['datapocz'],
				'datakonc' =>$_POST['datakonc']
				]);


			}
		} 
		else
		{
			$this->redirect('/');
		
		}
	}

}