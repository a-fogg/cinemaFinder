<?php
// a class to handle all database functions
    class DB {
          //variables which handle the current user instance, the current query, the errors to echo out, the results of the query and the amount of results.
        private static $_instance = null;
        private $_pdo,
                $_query, 
                $_error = false, 
                $_results, 
                $_lastId,
                $_count = 0 ;	

           //construct function uses the config class to get database information and try to connect to the database using PDO
        private function __construct(){
     			try{
     				$this->_pdo = new PDO('mysql:host=' . Config::get('mysql/host') . ';dbname=' . Config::get('mysql/db'), Config::get('mysql/username'), Config::get('mysql/password'));
     		//if it is unable to connect to the database it will print a error and kill the script
     			}catch(PDOException $e){
     				die($e->getMessage());
     			}


     		}
               //Check if object has been instantiated if has not it will be instantiated otherwise the instance will be returned
     		public static function getInstance(){
     			if(!isset(self::$_instance)) {
     				self::$_instance = new DB();
     			}
     			return self::$_instance;

     		}
               //constructs the query using the parameters written by the user, also allows the user to write the query in a high level coding way. 
     		public function query($sql, $params = array()){
     			//resets the error to false so errors can be run one by one without getting previous erros
                    $this->_error = false;
                    //Check if query has been prepared properly
     			if($this->_query = $this->_pdo->prepare($sql)) {
     				//variable x has been created to increment with each loop
                         $x = 1;
                         //counting the items inside params
     				if(count($params)) {
                              //for each params it has been put into param
     					foreach($params as $param){
                                   //binding values of param the position of x in the array
     						$this->_query->bindValue($x, $param);
     					$x++;
     					}

     				}
                         //if the query successfully executed
     				if($this->_query->execute()){
                              //store all the results into _results
     					$this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
     					//adds the count of results from the query to _count
                              $this->_count = $this->_query->rowCount();
                         $this->_lastId = $this->_pdo->lastInsertId();
     				}else{
                              //if query is unsuccessful set an error
     				$this->_error = true;	
     				}
     			}
                    //return the current object
     			return $this;

     		}
               //function which runs a query action such as select, delete or update
               public function action($action, $table, $where = array()){
                    //if the where query has 3 values within it
                    if(count($where) === 3) {
                         //operators for the query added to an array
                    $operators = array('=','>','<','>=','<=');

                    //first item in the where array will be the field from the database
                    $field    = $where[0];
                    //second item in where array will be the operator such as = or <
                    $operator = $where[1];
                    //third item in the where array will be the value such as 3 e.g size = 3
                    $value    = $where[2];
                    //check if operator is inside the operators array
                    if(in_array($operator, $operators)) {
                         //construct a query using the values entered for each variable
                         $sql = "{$action} FROM {$table} WHERE {$field} {$operator}  ?";
                         //runs query and binds ? to value entered if there is no error
                         if (!$this ->query($sql, array($value))->error()){
                              return $this;
                              }
                         }
                    }

                    return false;

               }
               //function for SELECT query takes values for the table and values for the where array
               public function get($table, $where){
                    return $this->action('SELECT *', $table, $where);

               }
                //function for DELETE query takes values for the table and values for the where array
               public function delete($table, $where = []){
                    return $this->action('DELETE', $table, $where);

               }
               //function to display the first result from the query
               public function first() {
                    return $this->results()[0]; 

               }
               //Constructs query for inserting into the database
               public function insert ($table, $fields = array()){
                         //keys are equal to the fields which will be updated
                         $keys = array_keys($fields);
                         //value is set to null
                         $values = null;
                         //value for incrimenting in loop
                         $x = 1;
                         //for each field as field (each as a single value)
                         foreach($fields as $field){
                              //adding question mark to each value
                              $values .= "?";
                              //count if x is less than the count of fields
                              //if we are not a , wiil be added after the field
                              if($x< count($fields)) {
                                   $values .=',';
                              }
                              $x++;
                         }

                         //constructing query for insert into
                         //Each key will be imploded to have a , between each and ` around each
                         //values will be the values which will be updated
                         $sql = "INSERT INTO {$table} (`". implode('`,`', $keys) ."`) VALUES ({$values})";
                         //if there are no errors the query will be run
                         if(!$this->query($sql, $fields)->error()) {
                              return $this->_count; //was true.
                         }      
                    return false;
               }
         
                        //Constructs query for inserting into the database
        public function insertNEW ($table, $fields = array(), $datatypes = array()){
            //keys are equal to the fields which will be updated
            $keys = array_keys($fields);
            $types = array_keys($datatypes);
              
            //value is set to null
            $values = null;
            //value for incrimenting in loop
            $x = 1;
            //for each field as field (each as a single value)
            foreach($fields as $field){
                //adding question mark to each value
                $values .= "?";
                //count if x is less than the count of fields
                //if we are not a , wiil be added after the field
                if($x< count($fields)) {
                    $values .=',';
                }
                $x++;
            }

            //constructing query for insert into
            //Each key will be imploded to have a , between each and ` around each
            //values will be the values which will be updated
            $sql = "INSERT INTO {$table} (`". implode('`,`', $keys) ."`) VALUES ({$values})";
            //if there are no errors the query will be run
            if(!$this->query($sql, $fields)->error()) {
                return true;
            }      
            return false;
        }
         
               //function to updata data in the database
               public function update($table, $id, $id_field, $fields) {
                    //set will hold the fields
                    $set = '';
                     //value for incrimenting in loop
                    $x = 1;

                    //
                    foreach($fields as $name => $value){
                         //adding the name variable and a equals question mark to each set (question mark will be the value being added to the database)
                         $set .="{$name} = ?";
                         //if x is less than or equal to the fields which have been entered then ad a ,
                         if($x < count($fields)){
                              $set .=', ';
                         }
                              $x++;

                    }
                    
                    //updates specified table with the sets which have been defined where the id is the id defined
                    $sql = "UPDATE {$table} SET {$set}  WHERE {$id_field} = {$id}";
                     //if there are no errors the query will be run
                    if(!$this->query($sql, $fields)->error()) {
                              return $this->_count; //was true.
                              }
                              return false;
               }
               //displays results of the query
               public function results() {
                    return $this->_results;

               }
               //displays any errors
     		public function error() {
     			return $this->_error;
     		}
               //displays the count of results from the query
        public function count(){
            return $this->_count;
        }
        
        public function lastId(){
            return $this->_lastId;
        }

       
     
     	public function query_single($sql, $params = array()) {
	    $this->_error = false;
	    if($this->_query = $this->_pdo->prepare($sql)) {
	        $x = 1;
	        if(count($params)) {
	            foreach ($params as $param) {
	                $this->_query->bindValue($x, $param);
	                $x++;
	            }
	        }

	        if ($this->_query->execute()) {
	            $this->_results = $this->_query->fetch(PDO::FETCH_ASSOC);
	            $this->_count = $this->_query->rowCount();
	        } else {
	            $this->_error = true;
	        }
	    }

	    return $this->_results;
	}
	
	
public function update_or_delete_query($sql, $params = array()) {
	    $this->_error = false;
	    if($this->_query = $this->_pdo->prepare($sql)) {
	        $x = 1;
	        if(count($params)) {
	            foreach ($params as $param) {
	                $this->_query->bindValue($x, $param);
	                $x++;
	            }
	        }
	        if ($this->_query->execute()) {
	            $this->_count = $this->_query->rowCount();
	        } else {
	            $this->_error = true;
	        }
	    }

	    return $this->_count;
	}
}
     ?>