<?php


//connectivity
require_once("DataObject.class.php");

class Person extends DataObject {
	protected $data = array(
		//these fields are in the person table.
		"person_id"=>"",
		"person_username"=>"",
		"person_password"=>"",
		"person_name"=>"",
		"person_first_name"=>"",
		"person_last_name"=>"",
		"person_email"=>"",
		"person_department"=>"",
		"person_hourly_rate"=>"",
		"person_perm_id"=>"",
		"person_type"=>"",
	);
	
//just starting to work on authentication here... not sure this is where we want to ultimately put this, but for now I'm putting it in the person class.

	public function authenticate() {
		$conn=parent::connect();
		$sql = "SELECT * FROM " . TBL_PERSON . " WHERE person_username = :person_username AND person_password = password(:person_password)";
		
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":person_username", $this->data["person_username"], PDO::PARAM_STR);
			$st->bindValue(":person_password", $this->data["person_password"], PDO::PARAM_STR);
			$st->execute();
			$row=$st->fetch();
			parent::disconnect( $conn );
			if ($row) return new Person($row);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("query failed: " . $e->getMessage() );
		}
	}
	
	//gets people out of the client table. We'll return this as an array and put it in a list once the
	//data is out.
	public static function getPeople() {
		$conn=parent::connect();
		$sql = "SELECT * FROM " . TBL_PERSON ;
		try {
			$st = $conn->prepare($sql);
			$st->execute();
			$person=array();
			foreach ($st->fetchAll() as $row) {
				$person[] = new Person($row);
			}
			parent::disconnect($conn);
			return array($person);
		}catch(PDOException $e) {
			parent::disconnect($conn);
			die("query failed here: " . $e->getMessage() . "query is " . $sql);
		}
	}


//the email address is the user's login. The user is sent an email address and are asked to set up their password before they login. The password is not set here,
//it is set once the user gets the email.
	public function insertPerson() {
	//first insert the parent row
		//removed foreign key on person_permissions. Consider what will happen with permissions!
		$conn=parent::connect();
		$sql = "INSERT INTO " . TBL_PERSON . " (
			person_first_name,
			person_last_name,
			person_email,
			person_department,
			person_hourly_rate,
			person_perm_id
			) VALUES (
			:person_first_name,
			:person_last_name,
			:person_email,
			:person_department,
			:person_hourly_rate,
			:person_perm_id
			)";
			
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":person_first_name", $this->data["person_first_name"], PDO::PARAM_STR);
			$st->bindValue(":person_last_name", $this->data["person_last_name"], PDO::PARAM_STR);
			$st->bindValue(":person_email", $this->data["person_email"], PDO::PARAM_STR);
			$st->bindValue(":person_department", $this->data["person_department"], PDO::PARAM_STR);
			$st->bindValue(":person_hourly_rate", $this->data["person_hourly_rate"], PDO::PARAM_INT);
			$st->bindValue(":person_perm_id", $this->data["person_perm_id"], PDO::PARAM_STR);
			$st->execute();
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on insert of person, sql is $sql " . $e->getMessage());
		}
	}

//this seems like a duplicate of getByUserName. Can we delete this?
public static function getPerson($person_username) {
		$conn=parent::connect();
		$sql = "SELECT * FROM " . TBL_PERSON . " WHERE person_username = :person_username";
		
		try {
			//get the PDO object
			$st = $conn->prepare($sql);
			//bind the value and set its datatype
			$st->bindValue(":person_username", $person_username, PDO::PARAM_STR);
			$st->execute();
			//this is a small return, so fetch() works fine.
			$row=$st->fetch();
			parent::disconnect($conn);
			if ($row) return new Person($row);
		} catch(PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on you: " . $e->getMessage());
		}
	}
	
public static function getByUserName($person_username) {
		$conn=parent::connect();
		$sql = "SELECT * FROM " . TBL_PERSON . " WHERE person_username = :person_username";
	
	
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":person_username", $person_username, PDO::PARAM_STR);
			$st->execute();
			$row=$st->fetch();
			parent::disconnect($conn);
			if ($row) return new Person($row);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed: " . $e->getMessage());
		}
	}

	public static function getByEmailAddress($person_email) {
		$conn=parent::connect();
		$sql = "SELECT * FROM " . TBL_PERSON . " WHERE person_email = :person_email";
	
	
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":person_email", $person_email, PDO::PARAM_STR);
			$st->execute();
			$row=$st->fetch();
			parent::disconnect($conn);
			if ($row) return new Person($row);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed: " . $e->getMessage());
		}
	}
	//function returns the individual person types
	public static function getPersonTypes() {
		$conn=parent::connect();
		$sql = "SELECT distinct(person_type) FROM " . TBL_PERSON;
	
	
		try {
			$st = $conn->prepare($sql);
			$st->execute();
			$person=array();
			foreach ($st->fetchAll() as $row) {
				$person[] = new Person($row);
			}
			parent::disconnect($conn);
			return array($person);
		}catch(PDOException $e) {
			parent::disconnect($conn);
			die("query failed here: " . $e->getMessage() . "query is " . $sql);
		}	}
	
}

?>