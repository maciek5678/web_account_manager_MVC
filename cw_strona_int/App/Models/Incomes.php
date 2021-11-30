<?php


namespace App\Models;

use PDO;
use \Core\View;

class Incomes extends \Core\Model
{
			    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }
	
	public function save($id)
	{
				$this->validate();
		if (empty($this->errors_kwota) & empty($this->errors_data)) {

		$inc_cat_id= $this->incomesCatUser($id);


		$this->incomesAdd($id, $inc_cat_id);
		
		return true;
	}
	return false;
	}	
	
						    public function incomesCatUser($id)
    {
					 $db = static::getDB();
				$query1=$db->prepare("SELECT id FROM incomes_category_assigned_to_users WHERE user_id=:id AND name=:przychod");			
			$query1->bindValue(':przychod', $this->przychod, PDO::PARAM_STR);
			$query1->bindValue(':id', $id, PDO::PARAM_INT);
			$query1->execute();
					while($r = $query1->fetch(PDO::FETCH_ASSOC)){
            return $inc_id = $r['id'];
            }
	}
								    public function incomesAdd($id, $inc_cat_id)
    {
					 $db = static::getDB();
								  $query=$db->prepare("INSERT INTO incomes VALUES (NULL,:id,:inc_cat_id,:kwota,:data,:komentarz)");
$query->bindValue(':id', $id, PDO::PARAM_INT);
		  		$query->bindValue(':inc_cat_id', $inc_cat_id, PDO::PARAM_INT);

		$query->bindValue(':kwota', $this->kwota, PDO::PARAM_LOB);		
		$query->bindValue(':data', $this->data, PDO::PARAM_STR);
		$query->bindValue(':komentarz', $this->komentarz, PDO::PARAM_STR);
$query->execute();
}
			   public function validate()
    {
        // Name
        if ($this->kwota == '') {
            $this->errors_kwota[] = 'Wpisz kwotę';
        }
		        if ($this->kwota == '0.00') {
            $this->errors_kwota[] = 'Wpisz kwotę';
        }

		            if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $this->data) == 0) {
                $this->errors_data[] = 'Niewłaściwy format daty';
            }
	


    }
	
				   public function takeIncomesTable($id,$data)
				   {
						$db = static::getDB();
$usersQuery = $db->prepare("SELECT  incomes.date_of_income, incomes.Amount, incomes_category_assigned_to_users.name FROM incomes_category_assigned_to_users, incomes WHERE incomes_category_assigned_to_users.id=incomes.income_category_assigned_to_user_id AND incomes.user_id=:id AND incomes.date_of_income LIKE :data");

$usersQuery->bindValue(':id', $id, PDO::PARAM_INT);
$usersQuery->bindValue(':data', $data, PDO::PARAM_STR);
$usersQuery->execute();

	return	$users_data_incomes = $usersQuery-> fetchAll();
					   
				   }
				   public function takeIncomesSum($id,$data)
				   {
						$db = static::getDB();
$usersQuery = $db->prepare("SELECT  SUM(incomes.Amount), COUNT(incomes.id) FROM incomes_category_assigned_to_users, incomes WHERE incomes_category_assigned_to_users.id=incomes.income_category_assigned_to_user_id AND incomes.user_id=:id AND incomes.date_of_income LIKE :data");

$usersQuery->bindValue(':id', $id, PDO::PARAM_INT);
$usersQuery->bindValue(':data', $data, PDO::PARAM_STR);
$usersQuery->execute();

	return	$users_data_incomes = $usersQuery-> fetchAll();

					   
				   }
				   public function takeIncomesTableUnusual($id,$datapocz, $datakonc)
				   {
					   $db = static::getDB();
	$usersQuery = $db->prepare("SELECT incomes.date_of_income, incomes.Amount, incomes_category_assigned_to_users.name FROM incomes_category_assigned_to_users, incomes WHERE incomes_category_assigned_to_users.id=incomes.income_category_assigned_to_user_id AND incomes.user_id=:id AND incomes.date_of_income BETWEEN :datapocz AND :datakonc");

$usersQuery->bindValue(':id', $id, PDO::PARAM_INT);
$usersQuery->bindValue(':datapocz', $datapocz, PDO::PARAM_STR);
$usersQuery->bindValue(':datakonc', $datakonc, PDO::PARAM_STR);
$usersQuery->execute();

	return	$users = $usersQuery-> fetchAll();
				   }
				   
				   				   public function takeIncomesSumUnusual($id,$datapocz, $datakonc)
				   {
					   $db = static::getDB();
	$usersQuery = $db->prepare("SELECT SUM(incomes.Amount), COUNT(incomes.id) FROM incomes_category_assigned_to_users, incomes WHERE incomes_category_assigned_to_users.id=incomes.income_category_assigned_to_user_id AND incomes.user_id=:id AND incomes.date_of_income BETWEEN :datapocz AND :datakonc");

$usersQuery->bindValue(':id', $id, PDO::PARAM_INT);
$usersQuery->bindValue(':datapocz', $datapocz, PDO::PARAM_STR);
$usersQuery->bindValue(':datakonc', $datakonc, PDO::PARAM_STR);
$usersQuery->execute();

	return	$users = $usersQuery-> fetchAll();
				   }
}