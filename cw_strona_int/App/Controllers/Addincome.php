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

    public function newAction()
    {
		if(Auth::isloggedin())
		{
			$incomes = new Incomes($_POST);
			$dataIncomes= $incomes ->takeIncomesCategoriesAndLimit($_SESSION['user_id']);
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

			$dataIncomes= $incomes ->takeIncomesCategoriesAndLimit($_SESSION['user_id']);
			View::renderTemplate('Addincome/new.html',[	
			'dataIncomes' => $dataIncomes
			]);
			}
			else
			{
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
	
	
	
		public function verifyAction()
	{
		$incomes = new Incomes();
		$data=$incomes->getData($_POST['data']);		
		$sum_incomes=$incomes->getIncomesByCategoryAndUserAndDate($data, $_POST['category']);
		if(!empty( $_POST['limit']))
		{
			if(!empty( $sum_incomes [0] ['SUM(incomes.amount)']))
			{
				$difference= $incomes->difference($_POST['limit'], $sum_incomes [0] ['SUM(incomes.amount)'] );
				$sum_balance=$incomes->sumIncomesAndCurrentIncome($sum_incomes [0] ['SUM(incomes.amount)'],$_POST['kwota'] );
				$color=$incomes->color($_POST['limit'],$sum_balance);
				View::renderTemplate('Addincome/change.html', [
				'limit' => $_POST['limit'],
				'spended' => $sum_incomes [0] ['SUM(incomes.amount)'],
				'diff' => $difference,
				'sum' => $sum_balance,
				'color' => $color
				]);
			} 
	
			else

			{
	
				$color=$incomes->color($_POST['limit'],$_POST['kwota']);
				View::renderTemplate('Addincome/change.html', [
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
