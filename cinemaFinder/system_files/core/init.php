<?php
//starting the session as this will be in all pages
session_start();
error_reporting(-1);
//setting global config variable
$GLOBALS['config'] = array(
        'mysql'    => array(
        'host'     => 'localhost',
        'username' => 'root',
        'password' => '',
        'db'       => 'users_assessment2'
    ),
    'remember'     => array(
    'cookie_name'  => 'hash',
    'cookie_expiry'=> 604800
    ),
    'session'      => array(
    'session_name' => 'user',
    'token_name'   => 'token',
    'token_register'   => 'tokenregister',
    'token_login'   => 'tokenlogin',
    'token_loginhor'   => 'tokenloginhor'
    )
);

//autoload function for variables depending on the class name called
spl_autoload_register(function($class){
    require_once('system_files/classes/' .$class. '.php');    
});

require_once 'system_files/functions/sanitize.php';

// checking if the cookie exists and if the session does not exist if so it will start a session for that user.
if(Cookie::exists(Config::get('remember/cookie_name')) && !Session::exists(Config::get('session/session_name'))){
    $hash = Cookie::get(Config::get('remember/cookie_name'));
    $hashCheck = DB::getInstance()->get('users_session', array('hash', '=', $hash));

    if($hashCheck->count()) {
        $user = new User($hashCheck->first()->user_id);
        $user->login();
    }
}