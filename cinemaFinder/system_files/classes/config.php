<?php
//config class is made to access the config data created in init.php, fully expansive and allowing for config to become as large as needed
class Config {
    // if the user types Config::get(then path to item in array e.g permissions/admin)
    public static function get($path = null) {
        //if path has been set
        if($path){
            //config variable is equal to the globals config variable
            $config = $GLOBALS['config'];
            //explodes path by the /
            $path = explode('/', $path);
                //each which has been exploded it put into a bit
                foreach($path as $bit) {
                    //config is set as the first bit
                    if(isset($config[$bit])){
                        //the config array is searched by the second bit
                    $config = $config[$bit];
                    
                } 
                    
            }
            
            return $config;
        }
    return false;
    }
    
}






?>