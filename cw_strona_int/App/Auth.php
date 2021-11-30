<?php

namespace App;
use App\Models\User;

class Auth
{
	    public static function login($user)
    {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user->id;

 
    }
	
	    public static function logout()
    {
        // Unset all of the session variables
        $_SESSION = [];
	}
	
	public static function isloggedin()
	{
		if(isset($_SESSION['user_id'] ))
			return true;
		else
			return false;
		
	}
}