<?php

namespace App\Models;

use PDO;
use \Core\View;

class User extends \Core\Model
{
	public $errors_mail = [];
	public $errors_password= [];
	public $errors_login= [];
	public $errors_loggingin= [];
	public $errors_newlogin=[];
	public $errors_newpassword=[];
	public $errors_newmail = [];
	public function __construct($data = [])
    {
        foreach ($data as $key => $value) 
		{
            $this->$key = $value;
        };
    }
	
	public function save()
    {
        $this->validate();

        if (empty($this->errors_mail) & empty($this->errors_login) & empty($this->errors_password)) 
		{

            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
            $sql = 'INSERT INTO users (username, password, email)
			VALUES (:name, :password_hash, :email)';
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':name', $this->login, PDO::PARAM_STR);
			$stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            return $stmt->execute();			
        }
		return false;
	}
    public function validate()
    {
        // Name
        if ($this->login == '') 
		{
            $this->errors_login[] = 'Wymagany login';
        }
		 if (static::loginExists($this->login, $this->id ?? null)) 
		{
            $this->errors_login[] = 'Istnieje już użytkownik o takim loginie';
        }

        // email address
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) 
		{
            $this->errors_mail[] = 'Niepoprawny adres e-mail';
        }
        if (static::emailExists($this->email, $this->id ?? null)) 
		{
            $this->errors_mail[] = 'Istnieje już użytkownik o takim adresie e-mail';
        }

        if (isset($this->password)) 
		{

            if (strlen($this->password) < 6) 
			{
                $this->errors_password[] = 'Hasło musi mieć co najmniej 6 znaków';
            }
			
			if ($this->password != $this->password2)  
			{
                $this->errors_password[] = 'Hasła nie są indentyczne';
            }


            if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) 
			{
                $this->errors_password[] = 'Hasło musi posiadać co najmniej  jedną literę';
            }

            if (preg_match('/.*\d+.*/i', $this->password) == 0) 
			{
                $this->errors_password[] = 'Hasło musi posiadać co najmniej  jedną cyfrę';
            }
        }
    }
	

	    public static function emailExists($email, $ignore_id = null)
    {
        $user = static::findByEmail($email);

        if ($user) {
            if ($user->id != $ignore_id) {
                return true;
            }
        }               

        return false;
    }
	
	public static function loginExists($login, $ignore_id = null)
    {
        $user = static::findByUsername($login);
        if ($user) 
		{
            if ($user->username != $ignore_id) 
			{
				return true;
            }
        }               

        return false;
    }
	
	public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }
	
    public static  function authenticate($email, $password)
    {
        $user = static::findByUsername($email);
		if ($user ) 
		{
            if (password_verify($password, $user->password)) 
			{
                return $user;
				
            }
				
        }

        return false;
    }
	
		    public static function findByUsername($username)
    {
        $sql = 'SELECT * FROM users WHERE username = :username';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }
	
	public static function findByID()
    {
        $sql = 'SELECT * FROM users WHERE id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $_SESSION['user_id'] , PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }
	
	public function getUserId($username)
    {
		$db = static::getDB();
 		$registrated_id=$db->prepare("SELECT id FROM users WHERE username=:username ");
		$registrated_id->bindValue(':username', $username, PDO::PARAM_STR);
		$registrated_id->execute();
		
		while($row = $registrated_id->fetch(PDO::FETCH_ASSOC))
		{
			return  $id = $row['id'];
		}
	
	}

	public function copyCategory($id)
    {
		$db = static::getDB();
		$query = $db->prepare('INSERT INTO expenses_category_assigned_to_users (user_id,name) SELECT :id, name FROM  expenses_category_default');
		$query->bindValue(':id', $id, PDO::PARAM_INT);
		$query->execute();
	}
	
	public function copyPayment($id)
    
	{
		$db = static::getDB();
		$query = $db->prepare('INSERT INTO payment_methods_assigned_to_users (user_id,name) SELECT :id, name FROM  payment_methods_default');
		$query->bindValue(':id', $id, PDO::PARAM_INT);
		$query->execute();
	}
	
	public function copyCategoryInc($id)
    {
		 $db = static::getDB();
		$query= $db->prepare('INSERT INTO incomes_category_assigned_to_users (user_id,name) SELECT :id, name FROM  incomes_category_default');
		$query->bindValue(':id', $id, PDO::PARAM_INT);
		$query->execute();
	}
	
	public function data($period)
	{
		if (!isset($period))
		{
			$data=date("Y-m").'%';

		}
		
		else if ($period=="currentMonth")
		{
			$data=date("Y-m").'%';
			
		} 
		else if ($period=="previousMonth")
		{
			$rok=date("Y");
			$miesiac=date("n");
			if($miesiac>1)
			{
				$miesiac=$miesiac-1;
			}
			else
			{
				$miesiac=12;

				$rok=$rok-1;
			}
			if($miesiac<10)
			{
				$miesiac="0".$miesiac;
			}
			$data=$rok."-".$miesiac."%";

		}
			
		else if ($period=="currentYear")
		{
			
			$data=date("Y").'%';
			
		}
		return $data;
	}
	
	public function currentMonthPeriod($period)
	{
		if($period=="currentMonth")
		{
			return 1;
		}
	}
	public function currentYearPeriod($period)
	{
		if($period=="currentYear")
		{
			return 1;
	
		}
	}
	
	public function previousMonthPeriod($period)
	{
		if($period=="previousMonth")
		{
			return 1;
	
		}
	}
	
	public function unusualPeriod($period)
	{
		if($period=="unusual")
		{
			return 1;
	
		}
	}
	public function below0($sum_incomes, $sum_expenses)
	{
		if ($sum_incomes>=$sum_expenses)
		{
			return 'Gratulacje. Świetnie zarządzasz finansami!';
		}
		else 
		{
			return 'Uważaj. Wpadasz w długi';
		}
		
		
	}

	public function changelogin()
	{		
		$this->validatelogin();
		if (empty($this->errors_newlogin) & empty($this->errors_login)) 
		{

			$this->insertNewLogin();

			return true;
		}
				
		return false;
	}
	public function validatelogin()
	{
		if ($this->newusername != $this->newusername2)  
		{
				$this->errors_newlogin[] = 'Loginy nie są identyczne';
		}
		if (static::loginExists($this->newusername, $this->id ?? null)) 
		{
			$this->errors_newlogin[] = 'Istnieje już użytkownik o takim loginie';
		}
			
		if (!static::loginExists($this->username)) 
		{
			$this->errors_login[] = 'Login niepoprawny';
		}

	}
		    public function insertNewLogin()
    {
			 $db = static::getDB();
		$query = $db->prepare('UPDATE users SET username=:newusername WHERE id=:id ');
		$query->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
		$query->bindValue(':newusername', $this->newusername, PDO::PARAM_STR);
		$query->execute();
	}


	public function changepassword()
	{
	
		$this->validatepassword();
		if (empty($this->errors_password) & empty($this->errors_newpassword)) 
		{

			$this->insertNewPassword();

			return true;
		}
			
		return false;
	}
	
	public function insertNewPassword()
    {
		$db = static::getDB();
		$newpassword_hash = password_hash($this->newpassword, PASSWORD_DEFAULT);
		$query = $db->prepare('UPDATE users SET password=:newpassword WHERE id=:id ');
		$query->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);		
		$query->bindValue(':newpassword', $newpassword_hash, PDO::PARAM_STR);
		$query->execute();
	}
	
	public function validatepassword()
	{
		if ($this->newpassword != $this->newpassword2)  
		{
             $this->errors_newpassword[] = 'Hasła nie są identyczne';
        }
		
        if (strlen($this->newpassword) < 6) 
		{
			$this->errors_newpassword[] = 'Hasło musi mieć co najmniej 6 znaków';
		}

		if (preg_match('/.*[a-z]+.*/i', $this->newpassword) == 0) 
		{
                $this->errors_newpassword[] = 'Hasło musi posiadać co najmniej  jedną literę';
		}

		if (preg_match('/.*\d+.*/i', $this->newpassword) == 0) 
		{
			$this->errors_newpassword[] = 'Hasło musi posiadać co najmniej  jedną cyfrę';
		}
		if (!password_verify($this->password, $this->getpassword()))   
		{
			$this->errors_password[] = 'Niepoprawne hasło';
        }
			
	}
	
	public function getpassword()
	{
		$db = static::getDB();
		$query = $db->prepare('SELECT password FROM users WHERE id=:id ');
		$query->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
		$query->execute();
		
		while($row = $query->fetch(PDO::FETCH_ASSOC))
		{
			return  $id = $row['password'];
		}
	}
public function changeemail()
{
	
	$this->validateemail();
	if (empty($this->errors_mail) & empty($this->errors_newmail)) 
	{
		$this->insertNewEmail();
		return true;
	}
			
		return false;
}
		
	public function insertNewEmail()
    {
		 $db = static::getDB();
		$query = $db->prepare('UPDATE users SET email=:email WHERE id=:id ');
		$query->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
		$query->bindValue(':email', $this->newemail, PDO::PARAM_STR);
		$query->execute();
	}
	
	public function validateemail()
	{
		if ($this->newemail != $this->newemail2)  
		{
            $this->errors_newmail[] = 'E-maile nie są identyczne';
        }
      
		if ($this->email !=$this->getemail())   
		{
			$this->errors_mail[] = 'Niepoprawne e-mail';
		}
			
		if (static::emailExists($this->newemail, $this->id ?? null)) 
		{
			$this->errors_newmail[] = 'Istnieje już użytkownik o takim adresie e-mail';
		}
	}

	public function getemail()
	{
		
		$db = static::getDB();
		$query = $db->prepare('SELECT email FROM users WHERE id=:id ');
		$query->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
		$query->execute();
		
		while($row = $query->fetch(PDO::FETCH_ASSOC))
		{
			return  $id = $row['email'];
		}
	}
}	

	