<?php


// If it's going to need the database, then it's 
// probably smart to require it before we start.

require_once('database.php');
require_once('functions.php');



class User {

	protected static $table_name='users';
	protected static $db_fields = array();
	public  function __construct() {

		// Get columns from table
	global $database;
	$result_set = $database->query("SELECT * FROM ".self::$table_name." LIMIT 1");
	$num_fields = mysql_num_fields($result_set);
	for($i=0; $i<$num_fields; $i++) {
	$column_name = mysql_field_name($result_set, $i);
	
	 // Set column names as variables
	self::$db_fields[] = $column_name; // THIS WORKS
	$name =  $this->{$column_name} ; // THIS IS PROBLEMATIC!
		}
}


	public function __get($name) {

	echo "this object <b>$name</b> does not exist From User Class";
	
	}

	public $id;
	public $username;
	public $email;
	public $pwd;
	public $grupe;
	

	
	
	
	
	public static function create_user($username,$email,$pwd){
		
		$username = strip_tags($username);	
		
		$username = preg_replace("/[^a-zA-Z0-9s|]/", "", $username);
		
		$user = new User();
		$user->username = $username;
		$user->email    = $email;
		$user->pwd		= sha1($pwd);
		$user->grupe	= 0;
		$user->create();
		
		return 0;
		
	}
	

	public static function check_if_user_existes($loking_for, $data){
		global $database;
		$data = $database->escape_value($data);
	    $sql  = "SELECT * FROM users ";
		$sql .= "WHERE {$loking_for} = '{$data}' ";
		$result_array = self::find_by_sql($sql);
		return !empty($result_array) ? array_shift($result_array) : false;
	}

	

	


	public static function authenticate($email="", $pass="") {

		global $database;
		$email = $database->escape_value($email);
		$pass = $database->escape_value($pass);
		$pass = (sha1($pass));     
		$sql  = "SELECT * FROM users ";
		$sql .= "WHERE email = '{$email}' ";
		$sql .= "AND pwd = '{$pass}' ";
		$sql .= "LIMIT 1";
		$result_array = self::find_by_sql($sql);
	
	return !empty($result_array) ? array_shift($result_array) : false;
	}

	
	// Common Database Methods
	public static function find_all() {
		return self::find_by_sql("SELECT * FROM ".self::$table_name);
	}
	
	public static function find_by_id($id=0) {
		$result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE id={$id} LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	public static function find_by_sql($sql="") {
			
	global $database;
	
			$result_set = $database->query($sql);
			$object_array = array();
			while ($row = $database->fetch_array($result_set)) {
			$object_array[] = self::instantiate($row);
			}
		return $object_array;
	}

	public static function count_all() {
		
		global $database;
		
		$sql = "SELECT COUNT(*) FROM ".self::$table_name;
		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
		return array_shift($row);
	}







	private static function instantiate($record) {

	// Could check that $record exists and is an array
	$object = new self;
	// Simple, long-form approach:
	// $object->id 				= $record['id'];
	// $object->username 	= $record['username'];
	// $object->password 	= $record['password'];
	// $object->first_name = $record['first_name'];
	// $object->last_name 	= $record['last_name'];
	// More dynamic, short-form approach:
		foreach($record as $attribute=>$value){
			if($object->has_attribute($attribute)) {
			$object->$attribute = $value;
			}
		}
	return $object;
	}
	
	private function has_attribute($attribute) {
	// We don't care about the value, we just want to know if the key exists
	// Will return true or false
	return array_key_exists($attribute, $this->attributes());
	}
	
	protected function attributes() {
		 
		// return an array of attribute names and their values
		$attributes = array();
		foreach(self::$db_fields as $field) {
		if(property_exists($this, $field)) {
			
		$attributes[$field] = $this->$field;
		}
		}
	return $attributes;
	}

	
	protected function sanitized_attributes() {

	global $database;

	$clean_attributes = array();
	// sanitize the values before submitting
	// Note: does not alter the actual value of each attribute
		foreach($this->attributes() as $key => $value){
		$clean_attributes[$key] = $database->escape_value($value);
		}
	return $clean_attributes;
	}

	public function save() {
	// A new record won't have an id yet.
	return isset($this->id) ? $this->update() : $this->create();
	}
	
	public function create() {
		global $database;
		
		// Don't forget your SQL syntax and good habits:
		// - INSERT INTO table (key, key) VALUES ('value', 'value')
		// - single-quotes around all values
		// - escape all values to prevent SQL injection
		$attributes = $this->sanitized_attributes();
		$sql = "INSERT INTO ".self::$table_name." (";
		$sql .= join(", ", array_keys($attributes));
		$sql .= ") VALUES ('";
		$sql .= join("', '", array_values($attributes));
		$sql .= "')";
		if($database->query($sql)) {
		$this->id = $database->insert_id();
		return true;
		} else {
		return false;
		}
	}

	public function update() {
		
	global $database;
	
	// Don't forget your SQL syntax and good habits:
	// - UPDATE table SET key='value', key='value' WHERE condition
	// - single-quotes around all values
	// - escape all values to prevent SQL injection
	$attributes = $this->sanitized_attributes();
	$attribute_pairs = array();
	foreach($attributes as $key => $value) {
	$attribute_pairs[] = "{$key}='{$value}'";
	}
	
	$sql = "UPDATE ".self::$table_name." SET ";
	$sql .= join(", ", $attribute_pairs);
	$sql .= " WHERE id=". $database->escape_value($this->id);
	$database->query($sql);
	return ($database->affected_rows() == 1) ? true : false;
	}

	public function delete() {
		
	global $database;

	// Don't forget your SQL syntax and good habits:
	// - DELETE FROM table WHERE condition LIMIT 1
	// - escape all values to prevent SQL injection
	// - use LIMIT 1
	$sql = "DELETE FROM ".self::$table_name;
	$sql .= " WHERE id=". $database->escape_value($this->id);
	$sql .= " LIMIT 1";
	$database->query($sql);
	return ($database->affected_rows() == 1) ? true : false;
	// NB: After deleting, the instance of User still 
	// exists, even though the database entry does not.
	// This can be useful, as in:
	//   echo $user->first_name . " was deleted";
	// but, for example, we can't call $user->update() 
	// after calling $user->delete().

	}
}
?>