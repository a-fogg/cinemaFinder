<?php 
//class to handle cookies for remember me function
class cookie{
	//check if the cookie exists for a particular user.
	public static function exists($name) {
		return(isset($_COOKIE[$name])) ? true : false;
	}
	//gets the cookie for the user specified
	public static function get($name) {
		return $_COOKIE[$name];
	}

	//sets a cookie for the user while setting a time and a name for the cookie
	public static function put($name, $value, $expiry){
		//if setcookie with the name value and expiry specific works return true
		if(setcookie($name, $value, time() + $expiry, '/')) {
			return true;
		}
		return false;
	}
	//deleting a users cookie
	public static function delete($name){
		//the cookie is removed by forcing it to expire
		self::put($name, '', time() - 1);
	}
}