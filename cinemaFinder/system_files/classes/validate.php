<?php
class Validate {
	private $_passed = false,
			$errors = array(),
			$db=null;
			//gets the current instance of the database
			public function __construct() {
				$this->_db = DB::getInstance();
			}
			//checking items for validation
			public function check ($source, $items = array()) {
				//setting the array of items as single items rules
                
				foreach($items as $item => $rules){
					//setting rules as rule value
					foreach($rules as $rule => $rule_value){
						//value is the item being tested
						//$value = trim($source[$item]);
                        $value = $source[$item];
						//item is the item being tested against
						$item = escape($item);

						//if the rule has been set as required it will check for an empty value
						if($rule === 'required' && empty($value) && $rule_value) {
							// if the value is empty it will echo out the value is required
							$this->addError("{$item} is required");
							//if the value is not empty is will begin the switch statement for other rules
						} else if(!empty($value)) {
							switch($rule) {
								//if min and/or max is the rule set for this item it will check the value against the rule value eg 'name' => array( 'min' => 2  'max' => 50
								case 'selection': 
								if(strlen($value) == "0") {
										$this->addError("A {$item} must be selected.");
										}
								break;
								case 'min':
									if(strlen($value) < $rule_value) {
										$this->addError("{$item} must be a minimum of {$rule_value} characters.");
									}

								break;
								case 'max':
								if(strlen($value) > $rule_value) {
										$this->addError("{$item} must be a maximum of {$rule_value} characters.");
									}
								break;
                                                                case 'pwStrength':
									if(!$this->valid_password($value)) {
										$this->addError("{$item} two weak");
									}								
								break;


								//check if two values match the value given and the rule value
								case 'matches':
									if($value != $source[$rule_value]) {
										$this->addError("{$rule_value} must match {$item}");
									}								
								break;
								//check if a value given is unique within the database if it must be
								case 'unique':
									$check = $this->_db->get($rule_value, array($item, '=', $value));
									if($check->count()) {
										$this->addError("{$item} already exists.");
									}
                                break;
                                case 'fileError':
                                    if($source[$item]['error'] !== UPLOAD_ERR_OK) {
                                        $this->addError("{$item} upload failed " . $source[$item]['error']);
                                    }
                                break;
                                case 'fileType':
                                    $allowedMimeTypes = array( 
                                        'application/msword',
                                        'application/pdf'
                                    );
                                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                                    $mime = finfo_file($finfo, $source[$item]['tmp_name']);
                                    
                                    if ( !in_array( $mime, $allowedMimeTypes ) ) 
                                         $this->addError("{$item} upload failed - Unknown/not permitted file type. (MS Word or PDF only)");
                                break;
                                case 'date':
                                
                                if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$value)) {
                                 $this->addError("{$item} must be of format YYYY-MM-DD.");   
                                }
    
    
								break;
							}

						}


						}
					}
					//if there have been no errors then it will pass
					if(empty($this->_errors)) {
						$this->_passed =true;
					}
					return $this;
				}


    
    
			//adding errors to the errors array
			public function addError($error) {
				$this->_errors[] = $error;
			}
			// displaying errors
			public function errors() {
				return $this->_errors;
			}
			//setting passed
			public function passed(){
				return $this->_passed;
			}
private function valid_password($candidate) {
   $r1='/[A-Z]/';  //Uppercase
   $r2='/[a-z]/';  //lowercase
   $r3='/[!@&pound;#$%^&*()\-_=+{};:,<.>]/';  // whatever you mean by 'special char'
   $r4='/[0-9]/';  //numbers

   if(preg_match_all($r1,$candidate, $o)<2) return FALSE;

   if(preg_match_all($r2,$candidate, $o)<2) return FALSE;

   if(preg_match_all($r3,$candidate, $o)<2) return FALSE;

   if(preg_match_all($r4,$candidate, $o)<2) return FALSE;

   if(strlen($candidate)<8) return FALSE;

   return TRUE;
}


}