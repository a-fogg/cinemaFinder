<?php
require_once 'system_files/core/init.php';
if(input::exists()) {
	if(token::checkreg(input::get('tokenreg'))){
		
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'username' => array(
				'required' => true,
				'min' => 2,
				'max' => 20,
				//'unique' => 'users'
			),
			'password' => array(
				'required' => true,
				'min' => 6
				),
			'password_again' => array(
				'required' => true,
				'matches' => 'password'
				)
			));
	if($validation->passed()) {
		$user = new User();
		$salt = Hash::salt(32);
		 
		try { 
			$user->create(array(
				'username' => Input::get('username'),
				'password' => hash::make(input::get('password'), $salt),
				'salt' 	   => $salt,
				'name'     => Input::get('name'),
				'joined'   => date('Y-m-d H:i:s'),
				'group'    => 1,
				));
			Session::flash('home', '<div class="alert alert-success"><h4>Success!</h4>You have registered and can now login!.</div>');
			//Redirect::to('index.php');
		} catch(Exception $e) {
			die($e->getMessage());
		}

	} else {
        $errors = "<div class='alert alert-danger'><h4>Warning!</h4>Please enter the correct information!";
		foreach($validation->errors() as $error) {
            $errors .= "<p class='alert alert-danger'><h5>Warning!</h5>" . $error . "</p>";
            
        }
        $errors .= "</div>";
    
			Session::flash('home', $errors);
        
	}
}
}

Redirect::to('default.php');
?>
