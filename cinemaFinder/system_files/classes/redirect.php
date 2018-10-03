<?php
//class for redirects
class redirect {
	//function to redirect
	public static function to($location = null) {
		//if the location is set
		if($location) {
			//if the location is numeric (for an error such as 404) switch to the relevent case
			if(is_numeric($location)) {
				switch($location) {
					case 404:
						header('HTTTP/1.0 404 Not Found');
						include 'includes/errors/404.php';			
						exit();
					break;
				}
			}
			//redirects have been rewritten to be Redirect::to(location)
			header('location:' . $location);
			exit();
		}
	}
}