<?php

//class for hashing variables
class Hash{
	//Creates a hash with string and a salt or an empty string if a salt is not supplied
	public static function make ($string, $salt = ''){
		//returns hash of password + salt
		return hash('sha256', $string . $salt);
	}
	//creates a salt with a specific length
	public static function salt($length){
		//returns random string of specific length
		return mcrypt_create_iv($length);
	}
	//creates a unique hash
	public static function unique(){
		//returns a unique id
		return self::make(uniqid());
	}
}