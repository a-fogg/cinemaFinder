<?php
//class which handles all user functions
            class User {
                    private $_db,
                                    $_sessionName = null,
                                    $_cookieName = null,
                                    $_data = array(),
                                    $_isLoggedIn = false;
                                   
                    //function construct which gets the db instance session name and the cookie name
                    public  function __construct($user = null) {
                            $this->_db = DB::getInstance();
                            $this->_sessionName = Config::get('session/session_name');
                            $this->_cookieName = Config::get('remember/cookie_name');
                            // Check if a session exists and set user if so.
                           
                            if(Session::exists($this->_sessionName) && !$user) {
                                    $user = Session::get($this->_sessionName);
                                   
                                    if($this->find($user)) {
                                            $this->_isLoggedIn = true;
                                    } else {
                                            $this->logout();
                                    }
     
                            } else {
                                    $this->find($user);
                            }
     
                    }
                    //a function to check if the data exists
                    public function exists() {
                            return (!empty($this->_data)) ? true :
                            false;
                    }
                    //function to find user
                    public function find($user = null) {
                            // Check if user_id specified and grab details
                           
                            if($user) {
                                //if the user is being searched through id the field will be id otherwise it will be username
                                    $field = (is_numeric($user)) ? 'id' : 'username';
                                    //find the user from the database
                                    $data = $this->_db->get('users', array($field, '=', $user));
                                   //if data is found then add the first item found to data
                                    if($data->count()) {
                                            $this->_data = $data->first();
                                            return true;
                                    }
     
                            }
     
                            return false;
                    }
               
                    //function for creating user
                    public function create($fields = array()) {
                           //if the user can not be added to the database throw a error
                            //inserting all of the correct fields into users
                            if(!$this->_db->insert('users', $fields)) {
                                    throw new Exception('There was a problem creating an account.');
                            }
     
                    }
                    //updating user information
                    public function update($fields = array(), $id = null) {
                           //if it is not the id of the user currently logged in, get the correct id
                            if(!$id && $this->isLoggedIn()) {
                                    $id = $this->data()->id;
                            }
     
                           //if the user can not be updated throw an error
                            if(!$this->_db->update('users', $id, $fields)) {
                                    throw new Exception('There was a problem updating.');
                            }
     
                    }
                    //function to login a user
                    public function login($username = null, $password = null, $remember = false) {
                           //if the username and password has not been entered the user will be logged in using the id found in the current session
                            if(!$username && !$password && $this->exists()) {
                                    Session::put($this->_sessionName, $this->data()->id);
                            } else {
                                //find the user from the database
                                    $user = $this->find($username);
                                   
                                    if($user) {
                                           //if the password is = equal to itself + the salt hashed when compared to the hashed password stored then add user id to session
                                            if($this->data()->password === Hash::make($password, $this->data()->salt)) {
                                                    Session::put($this->_sessionName, $this->data()->id);
                                                   //if remember box is ticked create a unique hash
                                                    if($remember) {
                                                            $hash = Hash::unique();
                                                            //checking if a hash is already stored in t he database
                                                            $hashCheck = $this->_db->get('users_session', array('user_id', '=', $this->data()->id));
                                                           
                                                           //if there is no hash currently stored in the database
                                                            if(!$hashCheck->count()) {
                                                                //insert data into the database
                                                                    $this->_db->insert('users_session', array('user_id' => $this->data()->id,'hash' => $hash));
                                                            } else {
                                                                //otherwise we set the hash to the hash which is already stored in the database
                                                                    $hash = $hashCheck->first()->hash;
                                                            }
                                                            //store a cookie with the cookie name and the hash and we are setting how long until it expires
                                                            Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
                                                    }
     
                                                    return true;
                                            }
     
                                    }
     
                            }
     
                            return false;
                    }
                    //checks user permissions
                    public function hasPermission($key) {
                        //runs a query on the database to find the user groups
                            $group = $this->_db->get('groups', array('id', '=', $this->data()->group));
                           //count the results
                           if($group->count()) {
                            //decoding a json string from the database
                           $permissions = json_decode($group->first()->permissions, true);
                           	//if the required permission is true for the user then return true
                           	if($permissions[$key] ==true){
                           		return true;
                           	}
                           }
     				return false;
                        
                    }
                    //check if the user is logged in
                    public function isLoggedIn() {
                            return $this->_isLoggedIn;
                    }
                    //gets data for the current logged in user
                    public function data() {
                            return $this->_data;
                    }
                    //function logs out the user by deleting the cookie and the session and deletes the user session from the database
                    public function logout() {
                            $this->_db->delete('users_session', array('user_id', '=', $this->data()->id));
                            Cookie::delete($this->_cookieName);
                            Session::delete($this->_sessionName);
                    }
     
            
}
