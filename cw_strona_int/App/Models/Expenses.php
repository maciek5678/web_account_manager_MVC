<?php


namespace App\Models;

use PDO;
use \Core\View;

class Expenses extends \Core\Model
{
	
		    public $errors_kwota = [];
		 public $errors_data= [];
	
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

			$exp_cat_id= $this->expensesCatUser($id);
			$pay_cat_id = $this->expensesPayUser($id);

			$this->expensesAdd($id, $exp_cat_id,$pay_cat_id);
		
			return true;
		}
		return false;
	}
	public function expensesCatUser($id)
	{
		$db = static::getDB();
		$query1 = $db->prepare("SELECT id FROM expenses_category_assigned_to_users WHERE user_id=:id AND name=:category");
		$query1->bindValue(':category', $this->category, PDO::PARAM_STR);
		$query1->bindValue(':id', $id, PDO::PARAM_INT);
		$query1->execute();
		
		
		while($row = $query1->fetch(PDO::FETCH_ASSOC))
		{
           return $exp_id = $row['id'];
		}

	}	
	public function expensesPayUser($id)
	{
		$db = static::getDB();
		$query3 = $db->prepare("SELECT id FROM payment_methods_assigned_to_users WHERE user_id=:id AND name=:platnosc");
		$query3->bindValue(':platnosc', $this->platnosc, PDO::PARAM_STR);
		$query3->bindValue(':id', $id, PDO::PARAM_INT);
		$query3->execute();
		
			while($row = $query3->fetch(PDO::FETCH_ASSOC))
			{
				return $pay_id = $row['id'];
            }
	}

	public function expensesAdd($id, $exp_cat_id,$pay_cat_id)
	{
		$db = static::getDB();
		$query5=$db->prepare("INSERT INTO expenses VALUES (NULL,:id,:exp_cat_id,:pay_cat_id,:kwota,:data,:komentarz)");
		$query5->bindValue(':id', $id, PDO::PARAM_INT);
		$query5->bindValue(':exp_cat_id', $exp_cat_id, PDO::PARAM_INT);

		$query5->bindValue(':pay_cat_id',$pay_cat_id, PDO::PARAM_INT);
		$query5->bindValue(':kwota', $this->kwota, PDO::PARAM_LOB);		
		$query5->bindValue(':data', $this->data, PDO::PARAM_STR);
		$query5->bindValue(':komentarz', $this->komentarz, PDO::PARAM_STR);
		$query5->execute();
	

	}

	public function validate()
    {

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
	
	public function takeExpensesTable($id,$data)
	{
		$db = static::getDB();
		$usersQuery = $db->prepare("SELECT  expenses.date_of_expense, expenses.Amount, expenses_category_assigned_to_users.name FROM expenses_category_assigned_to_users, expenses WHERE expenses_category_assigned_to_users.id=expenses.expense_category_assigned_to_user_id AND expenses.user_id=:id AND expenses.date_of_expense LIKE :data");
		$usersQuery->bindValue(':id', $id, PDO::PARAM_INT);
		$usersQuery->bindValue(':data', $data, PDO::PARAM_STR);
		$usersQuery->execute();
		return	$users_data = $usersQuery-> fetchAll();
					   
	}

	public function takeExpensesSum($id,$data)
	{
		$db = static::getDB();
		$usersQuery = $db->prepare("SELECT SUM(expenses.Amount),COUNT(expenses.id) FROM expenses_category_assigned_to_users, expenses WHERE expenses_category_assigned_to_users.id=expenses.expense_category_assigned_to_user_id AND expenses.user_id=:id AND expenses.date_of_expense LIKE :data");

		$usersQuery->bindValue(':id', $id, PDO::PARAM_INT);
		$usersQuery->bindValue(':data', $data, PDO::PARAM_STR);
		$usersQuery->execute();

		$users_data = $usersQuery-> fetchAll();
	
		return	$users_data;
					   
	}
				    
					
	public function takeExpensesTableUnusual($id,$datapocz, $datakonc)
	{
		$db = static::getDB();
		$usersQuery2 = $db->prepare("SELECT expenses.date_of_expense, expenses.Amount, expenses_category_assigned_to_users.name FROM expenses_category_assigned_to_users, expenses WHERE expenses_category_assigned_to_users.id=expenses.expense_category_assigned_to_user_id AND expenses.user_id=:id AND expenses.date_of_expense BETWEEN :datapocz AND :datakonc");
		$usersQuery2->bindValue(':id', $id, PDO::PARAM_INT);
		$usersQuery2->bindValue(':datapocz', $datapocz, PDO::PARAM_STR);
		$usersQuery2->bindValue(':datakonc', $datakonc, PDO::PARAM_STR);
		$usersQuery2->execute();
		return $users2 = $usersQuery2-> fetchAll();
	}
				   
	public function takeExpensesSumUnusual($id,$datapocz, $datakonc)
	{
		$db = static::getDB();
		$usersQuery2 = $db->prepare("SELECT  SUM(expenses.Amount), COUNT(expenses.id) FROM expenses_category_assigned_to_users, expenses WHERE expenses_category_assigned_to_users.id=expenses.expense_category_assigned_to_user_id AND expenses.user_id=:id AND expenses.date_of_expense BETWEEN :datapocz AND :datakonc");
		$usersQuery2->bindValue(':id', $id, PDO::PARAM_INT);
		$usersQuery2->bindValue(':datapocz', $datapocz, PDO::PARAM_STR);
		$usersQuery2->bindValue(':datakonc', $datakonc, PDO::PARAM_STR);
		$usersQuery2->execute();
		return $users2 = $usersQuery2-> fetchAll();
	}
				   
	public function takeExpensesSumCategoryUnusual($id,$datapocz, $datakonc)
	{
		$db = static::getDB();
		$usersQuery2 = $db->prepare("SELECT  SUM(expenses.Amount), expenses_category_assigned_to_users.name FROM expenses_category_assigned_to_users, expenses WHERE expenses_category_assigned_to_users.id=expenses.expense_category_assigned_to_user_id AND expenses.user_id=id AND expenses.date_of_expense BETWEEN :datapocz AND :datakonc GROUP  BY expenses_category_assigned_to_users.name");
		$usersQuery2->bindValue(':id', $id, PDO::PARAM_INT);
		$usersQuery2->bindValue(':datapocz', $datapocz, PDO::PARAM_STR);
		$usersQuery2->bindValue(':datakonc', $datakonc, PDO::PARAM_STR);
		$usersQuery2->execute();
		return $users2 = $usersQuery2-> fetchAll();
	}
				   
	public function takeExpensesSumCategory($id,$data)
	{
		$db = static::getDB();
		$usersQuery2 = $db->prepare("SELECT  SUM(expenses.Amount), expenses_category_assigned_to_users.name FROM expenses_category_assigned_to_users, expenses WHERE expenses_category_assigned_to_users.id=expenses.expense_category_assigned_to_user_id AND expenses.user_id=id AND expenses.date_of_expense LIKE :data GROUP  BY expenses_category_assigned_to_users.name");
		$usersQuery2->bindValue(':id', $id, PDO::PARAM_INT);
		$usersQuery2->bindValue(':datapocz', $datapocz, PDO::PARAM_STR);
		$usersQuery2->bindValue(':data', $data, PDO::PARAM_STR);
		$usersQuery2->execute();
		return $users2 = $usersQuery2-> fetchAll();
	}
				   
				   
				   
	public function showCharDraftUnusual($id,$datapocz,$datakonc)
	{
		$db = static::getDB();
		$stmt = $db->prepare( "SELECT ec.name, SUM(e.amount) FROM expenses AS e, expenses_category_assigned_to_users AS ec 
        WHERE e.user_id=:id AND ec.user_id=:id AND ec.id=e.expense_category_assigned_to_user_id AND e.date_of_expense 
        BETWEEN :datapocz AND :datakonc GROUP BY expense_category_assigned_to_user_id");
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		$stmt->bindValue(':datapocz', $datapocz, PDO::PARAM_STR);
		$stmt->bindValue(':datakonc', $datakonc, PDO::PARAM_STR);
        $stmt->execute();
        $expensePie = array();
        $expenseResult = $stmt->fetchAll(\PDO::FETCH_OBJ);
        $expensesArray = json_decode(json_encode($expenseResult), True);
		foreach ($expensesArray as $expenseChart) 
			{
				array_push($expensePie, array("label"=>$expenseChart['name'], "y"=>$expenseChart['SUM(e.amount)']));
			}
        json_encode($expensePie, JSON_NUMERIC_CHECK);
        return $expensesArray;
    }
	public function showCharDraft($id,$data)
	{
		$db = static::getDB();
		$stmt = $db->prepare( "SELECT ec.name, SUM(e.amount) FROM expenses AS e, expenses_category_assigned_to_users AS ec 
        WHERE e.user_id=:id AND ec.user_id=:id AND ec.id=e.expense_category_assigned_to_user_id AND e.date_of_expense 
        LIKE :data GROUP BY expense_category_assigned_to_user_id");
		
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		$stmt->bindValue(':data', $data, PDO::PARAM_STR);
        $stmt->execute();
        $expensePie = array();
        $expenseResult = $stmt->fetchAll(\PDO::FETCH_OBJ);
        $expensesArray = json_decode(json_encode($expenseResult), True);
		 foreach ($expensesArray as $expenseChart) 
		{
            array_push($expensePie, array("label"=>$expenseChart['name'], "y"=>$expenseChart['SUM(e.amount)']));
        }
        json_encode($expensePie, JSON_NUMERIC_CHECK);
        return $expensesArray;


    }			   
					   
	public function takeExpensesCategoriesAndLimit($id)
	{
		$db = static::getDB();
		$usersQuery = $db->prepare("SELECT name, amount_limit, id   FROM expenses_category_assigned_to_users WHERE user_id=:id ");
		$usersQuery->bindValue(':id', $id, PDO::PARAM_INT);

		$usersQuery->execute();
		return	$users = $usersQuery-> fetchAll();
	}			 


	public function insertExpenseCategoryAssignedToUser($id)
	{
		$db = static::getDB();
		$usersQuery = $db->prepare("INSERT INTO  expenses_category_assigned_to_users (id, user_id, name, amount_limit) VALUES (NULL,:id,  :name, :limit) ");
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
	
	
	public function updateExpenseCategoryAssignedToUserLimit($id, $salaryLimit, $catName)
	{
		$db = static::getDB();
		$usersQuery = $db->prepare("UPDATE expenses_category_assigned_to_users SET amount_limit=:amount_limit WHERE user_id=:id AND name=:name");
		$usersQuery->bindValue(':id', $id, PDO::PARAM_INT);
		$usersQuery->bindValue(':name', $catName, PDO::PARAM_STR);		
		$usersQuery->bindValue(':amount_limit', $salaryLimit, PDO::PARAM_STR);
		$usersQuery->execute();
		return	$users = $usersQuery-> fetchAll();

	}
	public function deleteExpenseCategoryAssignedToUserLimit($id, $catName)
	{
		$db = static::getDB();
		$usersQuery = $db->prepare("UPDATE expenses_category_assigned_to_users SET amount_limit=:amount_limit WHERE user_id=:id AND name=:name");
		$usersQuery->bindValue(':id', $id, PDO::PARAM_INT);
		$usersQuery->bindValue(':name', $catName, PDO::PARAM_STR);		
		$usersQuery->bindValue(':amount_limit', NULL, PDO::PARAM_STR);
		$usersQuery->execute();
	return	$users = $usersQuery-> fetchAll();

	}
	
	public function deleteExpenseCategoryAssignedToUser ($userId, $catId)
	{
		$db = static::getDB();
		$usersQuery = $db->prepare("DELETE FROM expenses_category_assigned_to_users  WHERE user_id=:id AND id=:catId");
		$usersQuery->bindValue(':id', $userId, PDO::PARAM_INT);
		$usersQuery->bindValue(':catId', $catId, PDO::PARAM_INT);		
		$usersQuery->execute();
		return	$users = $usersQuery-> fetchAll();

	}
	
	public function deleteExpenses($userId, $catId)
	{
		$db = static::getDB();
		$usersQuery = $db->prepare("DELETE FROM expenses  WHERE user_id=:id AND expense_category_assigned_to_user_id=:catId");
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
	
	public function getExpensesByCategoryAndUserAndDate($data, $category, $id)
	{
		$db = static::getDB();
		$usersQuery = $db->prepare("SELECT SUM(expenses.amount),  expenses_category_assigned_to_users.amount_limit FROM expenses, expenses_category_assigned_to_users WHERE expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id AND name=:category AND expenses.date_of_expense LIKE :data AND expenses.user_id=:id");
		$usersQuery->bindValue(':category', $category, PDO::PARAM_STR);		
		$usersQuery->bindValue(':data', $data, PDO::PARAM_STR);
		$usersQuery->bindValue(':id', $id, PDO::PARAM_INT);
		$usersQuery->execute();
		return	$users = $usersQuery-> fetchAll();

	}
	public function sumExpensesAndCurrentIncome($sum,$kwota)
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
				   



 




 
					   
				   
				   
