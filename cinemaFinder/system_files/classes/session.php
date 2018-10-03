<?php
//handles user sessions
	class session {
		//check if the user session currently exists and returns true or false
		public static function exists($name) {
			return (isset($_SESSION[$name])) ? true : false;
		}
		//puts the user data into a session
		public static function put($name, $value) {
			return $_SESSION[$name] = $value;
		}
		//gets a user session
		public static function get($name){
			return $_SESSION[$name];

		}
		//deletes a user session by unseting the session
		public static function delete($name){
			if(self::exists($name)){
					unset($_SESSION[$name]);

			}
		}
		//flash's a masseage as a user
		public static function flash($name, $string = ''){
			//if flash messege exists it will be displayed and than deleted so it does not display when the user refreshes
			if(self::exists($name)) {
				$session = self::get($name);
				self::delete($name);
				return $session;				
			}	else {
				//otherwise the data will be set into a session using the put function
				self::put($name, $string);
			}
	
		}

	}