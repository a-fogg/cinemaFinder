<?php
//class to create tokens for forms
	class token {
		//function generate a token from the session array in the init.php config with the correct token name which has been set
			public static function generate() {
				//retrurns by putting the token which has been created and hashed with a unique id into the current user session
			return Session::put(Config::get('session/token_name'), md5(uniqid()));
		}

		//check if the token exists
		public static function check($token){
			//gets the token name from the config array
			$tokenName = Config::get('session/token_name');
			//if the tokenname exists and the token is equal to the token in the session then delete the session by the token name
			if(Session::exists($tokenName) && $token === Session::get($tokenName)) {
				Session::delete($tokenName);
				return true;
			}
			return false;
		}

		//repeated functions run the same as the other functions but multiple token names were needed.

		public static function generatelogin() {
			return Session::put(Config::get('session/token_login'), md5(uniqid()));
		}

		
			public static function generateloginhor() {
			return Session::put(Config::get('session/token_loginhor'), md5(uniqid()));
		}

		public static function checkloginhor($token){
			$tokenName = Config::get('session/token_loginhor');
			if(Session::exists($tokenName) && $token === Session::get($tokenName)) {
				Session::delete($tokenName);
				return true;
			}
			return false;
		}
		public static function generatereg() {
			return Session::put(Config::get('session/token_register'), md5(uniqid()));
		}

		public static function checkreg($token){
			$tokenName = Config::get('session/token_register');
			if(Session::exists($tokenName) && $token === Session::get($tokenName)) {
				Session::delete($tokenName);
				return true;
			}
			return false;
		}
	}