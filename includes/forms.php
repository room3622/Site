<?php

class Forms {

	public static function register_user($require_filds, $POST) {
		/*
		 * $error if the for eturn 0
		 * if the form is not valid return 1
		 */
		$error = 0;
		$v="";
		foreach ($POST as $key => $value) {

			if (empty($value) && in_array($key, $require_filds)) {
				// if any fild in $require_filds is empety error
				$error = 1;
				break 1;
			} else {
				//at this point the form is valid 
				$v = Forms::isValidData_register($POST);
				
			}

		}
		
		

		return array($error, $v);
	}

	public static function isValidData_register($data) {
		//cheking post data for validation 	
		$username 	= $data['username'];
		$email 		= $data['email'];
		$pwd		= $data['pwd'];
		$pwd_c 		= $data['pwd_confirm'];
		
		$user_error=0;
		$email_error=0;
		$pwd_error=0;
		$mail_check_error=0;
		
		//check if username exists if it dos 
		//send error 
		$post_username = User::check_if_user_existes("username", $username);
		if($post_username){
			//if this post user existes sent error ==1
			$user_error=1;
		}
		
		$mail_check = filter_var( $email, FILTER_VALIDATE_EMAIL );
		if($mail_check){
		$post_mail = User::check_if_user_existes("email", $email);
			//check is the post email existes in database if is tose 
			//dont create me user recover the password
			if($post_mail){
			//if this email existes 
			// send error 
			$email_error=1;	
			}
		
		}else{
			//if the post email is not vaild dont chek db send error
			$mail_check_error=1;
		}
		
		//check if the password mache 
		//if it dont mach send error 
		
		if($pwd != $pwd_c){
			$pwd_error=1;
		}
		
		
		
		
		
		
		return array($user_error,$email_error,$pwd_error,$mail_check_error);
	}

}
?>