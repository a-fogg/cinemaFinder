<?php
//class for inputs
class input {
	//function to check if post or get exists it is set to post as default
	public static function exists($type = 'post'){
		//php switch on type variable
		switch($type) {
			//check if post or get is empty
			case'post';
				return (!empty($_POST)) ? true : false;
			break;

			case'get';

				return (!empty($_GET)) ? true : false;

			break;

				return false;
			break;
		}
	}
    
    
	//function to define which item to get
	public static function get($item) {
		if(isset($_POST[$item])) { 
			return $_POST[$item];

		} else if(isset($_GET[$item])) {
			return $_GET[$item];

		} 
		return '';
	}
}