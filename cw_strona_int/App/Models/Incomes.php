<?php


namespace App\Models;

use PDO;
use \Core\View;

class Incomes extends \Core\Model
{
	public function __construct($data = [])
    {
        foreach ($data as $key => $value) 
		{
            $this->$key = $value;
        };
    }
	
	public function save($id)
	{
		$this->validate();
		if (empty($this->errors_kwota) & empty($this->errors_data)) 
			{

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
		while($r = $query1->fetch(PDO::FETCH_ASSOC))
		{
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
        if ($this->kwota == '') 
		{
            $this->errors_kwota[] = 'Wpisz kwotę';
        }
		
		if ($this->kwota == '0.00') 
		{
            $this->errors_kwota[] = 'Wpisz kwotę';
        }

		if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $this->data) == 0) 
		{
			$this->errors_data[] = 'Niewłaściwy format daty';
        }
    }
	
	public function takeIncomesTable($id,$data)
	{
		$db = static::getDB();
		$usersQuery = $db->prepare("SELECT  incomes.date_of_income, incomes.Amount, incomes_category_assigned_to_users.name, incomes_category_assigned_to_users.amount_limit FROM incomes_category_assigned_to_users, incomes WHERE incomes_category_assigned_to_users.id=incomes.income_category_assigned_to_user_id AND incomes.user_id=:id AND incomes.date_of_income LIKE :data");
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
	
	public function takeIncomesCategoriesAndLimit($id)
	{
		$db = static::getDB();
		$usersQuery = $db->prepare("SELECT name, amount_limit, id   FROM incomes_category_assigned_to_users WHERE user_id=:id ");
		$usersQuery->bindValue(':id', $id, PDO::PARAM_INT);

		$usersQuery->execute();
		return	$users = $usersQuery-> fetchAll();
	}

	
	public function insertIncomeCategoryAssignedToUser($id)
	{
		$db = static::getDB();
		$usersQuery = $db->prepare("INSERT INTO  incomes_category_assigned_to_users (id, user_id, name, amount_limit) VALUES (NULL,:id,  :name, :limit) ");
		$usersQuery->bindValue(':id', $id, PDO::PARAM_INT);
		$usersQuery->bindValue(':name', $this->categoryname, PDO::PARAM_STR);
		
		if(!empty($this->limitNew)){
			$usersQuery->bindValue(':limit', $this->limitNew, PDO::PARAM_STR);
		
		}
		else
		{
			$usersQuery->bindValue(':limit', NULL, PDO::PARAM_INT);
		}
		$usersQuery->execute();
		return	$users = $usersQuery-> fetchAll();
	}
	
	
			public function updateIncomeCategoryAssignedToUserLimit($id, $salaryLimit, $catName)
	{
		$db = static::getDB();
		$usersQuery = $db->prepare("UPDATE incomes_category_assigned_to_users SET amount_limit=:amount_limit WHERE user_id=:id AND name=:name");
		$usersQuery->bindValue(':id', $id, PDO::PARAM_INT);
		$usersQuery->bindValue(':name', $catName, PDO::PARAM_STR);		
		$usersQuery->bindValue(':amount_limit', $salaryLimit, PDO::PARAM_STR);
		$usersQuery->execute();
		return	$users = $usersQuery-> fetchAll();

	}
	public function deleteIncomeCategoryAssignedToUserLimit($id, $catName)
	{
		$db = static::getDB();
		$usersQuery = $db->prepare("UPDATE incomes_category_assigned_to_users SET amount_limit=:amount_limit WHERE user_id=:id AND name=:name");
		$usersQuery->bindValue(':id', $id, PDO::PARAM_INT);
		$usersQuery->bindValue(':name', $catName, PDO::PARAM_STR);		
		$usersQuery->bindValue(':amount_limit', NULL, PDO::PARAM_STR);
		$usersQuery->execute();
		return	$users = $usersQuery-> fetchAll();

	}
	
	public function deleteIncomeCategoryAssignedToUser ($userId, $catId)
	{
		$db = static::getDB();
		$usersQuery = $db->prepare("DELETE FROM incomes_category_assigned_to_users  WHERE user_id=:id AND id=:catId");
		$usersQuery->bindValue(':id', $userId, PDO::PARAM_INT);
		$usersQuery->bindValue(':catId', $catId, PDO::PARAM_INT);		
		$usersQuery->execute();
		return	$users = $usersQuery-> fetchAll();

	}
	
	public function deleteIncomes ($userId, $catId)
	{
		$db = static::getDB();
		$usersQuery = $db->prepare("DELETE FROM incomes  WHERE user_id=:id AND income_category_assigned_to_user_id=:catId");
		$usersQuery->bindValue(':id', $userId, PDO::PARAM_INT);
		$usersQuery->bindValue(':catId', $catId, PDO::PARAM_INT);		
		$usersQuery->execute();
		return	$users = $usersQuery-> fetchAll();

	}
	
	public function getData($data)
	{
		$data=substr($data, 0, -3);
		return	$data."%";

	}
	
	public function getIncomesByCategoryAndUserAndDate($data, $category)
	{
		$db = static::getDB();
		$usersQuery = $db->prepare("SELECT SUM(incomes.amount),  incomes_category_assigned_to_users.amount_limit FROM incomes, incomes_category_assigned_to_users WHERE incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id AND name=:category AND incomes.date_of_income LIKE :data");
		$usersQuery->bindValue(':category', $category, PDO::PARAM_STR);		
		$usersQuery->bindValue(':data', $data, PDO::PARAM_STR);		
		$usersQuery->execute();
		return	$users = $usersQuery-> fetchAll();

	}
	
	public function sumIncomesAndCurrentIncome($sum,$kwota)
	{
		return $sum + $kwota;
	}
	
	public function difference($limit,$kwota)
	{
		return $limit - $kwota;
	}
	
	public function color($limit,$sum)
	{
		if(( $limit - $sum)<0)
			
		return "orange";
		else
			return "green";
		
	}
	
	
}