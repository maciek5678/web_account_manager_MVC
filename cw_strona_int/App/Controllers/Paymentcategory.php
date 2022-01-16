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
class Paymentcategory extends \Core\Controller
{
    public function newAction()
    {
		if(Auth::isloggedin())
		{
			$payments= new User();
	         $dataPayments= $payments ->takePaymentsCategories($_SESSION['user_id']);
			View::renderTemplate('Paymentcategory/new.html',[	
			'dataPayments' => $dataPayments
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
			$payments= new User($_POST);
			$payments->insertPaymentCategoryAssignedToUser($_SESSION['user_id']);
	        $dataPayments= $payments ->takePaymentsCategories($_SESSION['user_id']);
			View::renderTemplate('Paymentcategory/new.html',[
			'dataPayments' => $dataPayments
			]);
		}

		else
		{
 			$this->redirect('/');
		}
	}
	
	public function deletemethodAction()
    {
		$payments= new User();
		$payments ->deletePaymentCategoryAssignedToUser($_SESSION['user_id'], $_POST['limit']);
		$payments ->deleteExpenses($_SESSION['user_id'], $_POST['limit']);
		$dataPayments= $payments->takePaymentsCategories($_SESSION['user_id']);
		View::renderTemplate('Paymentcategory/new.html',[
		'dataPayments' => $dataPayments
		]);
	}
}
