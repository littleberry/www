<?php


//include the DataObject code with the class to get the connectivity
require_once("DataObject.class.php");

class Client extends DataObject {
	//set up the array keys and initilize each key. Enables the class constructor to validate fields when a Member object is created.
	protected $data = array(
		"client_id"=>"",
		"client_first_name"=>"",
		"client_last_name"=>"",
		"clent_address_index"=>"",
		"client_currency_index"=>""
	);
	
	
	public static function getClient($clientId) {
		//connect to the database with the parent class connect() function.
		$conn=parent::connect();
		$sql="SELECT * FROM " . TBL_CLIENT . " WHERE client_id = " . $clientId;
		
		try {
			//call the PDO object's prepare statement and return the PDOStatement object
			$st = $conn->prepare($sql);
			//initialize the values to the placeholders. This ensures the right datatypes are bound (int in this case)
			//$st->bindValue(":startRow", $startRow, PDO::PARAM_INT);
			//$st->bindValue(":numRows", $numRows, PDO::PARAM_INT);
			$st->execute();
			//load the result set into an array and pass to the constructor ($data)
			$client=array();
			//get the rows from the sql query and store them as member objects in an associative array.
			foreach ($st->fetchAll() as $row) {
				$client[] = new Client($row);
			}
			//get the row count using the SQL found_rows function.
			//$st=$conn->query("SELECT found_rows() AS totalRows");
			$row=$st->fetch();
			//close db
			parent::disconnect($conn);
			//return the array
			return array($client);
		}catch(PDOException $e) {
			parent::disconnect($conn);
			die("query failed here: " . $e->getMessage() . "query is " . $sql);
		}
	}

	
	//
	/*public static function getMember($id) {
		$conn=parent::connect();
		//use a placeholder for the ID
		$sql = "SELECT * FROM " . TBL_MEMBERS . " WHERE id = :id";
		
		try {
			//get the PDO object
			$st = $conn->prepare($sql);
			//bind the value and set its datatype
			$st->bindValue(":id", $id, PDO::PARAM_INT);
			$st->execute();
			//this is a small return, so fetch() works fine.
			$row=$st->fetch();
			parent::disconnect($conn);
			if ($row) return new Member($row);
		} catch(PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on you: " . $e->getMessage());
		}
	}
	
	//function added as part of the member registration application
	public static function getByUserName($userName) {
		$conn=parent::connect();
		$sql = "SELECT * FROM " . TBL_MEMBERS . " WHERE username = :username";
	
	
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":username", $username, PDO::PARAM_STR);
			$st->execute();
			$row=$st->fetch();
			parent::disconnect($conn);
			if ($row) return new Member($row);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed: " . $e->getMessage());
		}
	}

	//function added as part of the member registration application
	public static function getByEmailAddress($emailAddress) {
		$conn=parent::connect();
		$sql = "SELECT * FROM " . TBL_MEMBERS . " WHERE emailAddress = :emailAddress";
	
	
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":emailAddress", $emailAddress, PDO::PARAM_STR);
			$st->execute();
			$row=$st->fetch();
			parent::disconnect($conn);
			if ($row) return new Member($row);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed: " . $e->getMessage());
		}
	}		
	
	//function added as part of the member registration application
	public function getGenres() {
		return $this->_genres;
	}
		
	//function added as part of the member registration application
	public function getGenderString() {
		return ($this->data["gender"] == "f") ? "Female" : "Male";
	}
	
	//function added as part of the member registration application
	public function getFavoriteGenreString () {
		return ($this->_genres[$this->data["favoriteGenre"]]);
	}
	
	//function added as part of the member registration application
	public function insert() {
		$conn=parent::connect();
		$sql = "INSERT INTO " . TBL_MEMBERS . " (
			username,
			password,
			firstName,
			lastName,
			joinDate,
			gender,
			favoriteGenre,
			emailAddress,
			otherInterests,
			admin
			) VALUES (
			:username,
			password(:password),
			:firstName,
			:lastName,
			:joinDate,
			:gender,
			:favoriteGenre,
			:emailAddress,
			:otherInterests,
			:admin
			)";
			
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":username", $this->data["username"], PDO::PARAM_STR);
			$st->bindValue(":password", $this->data["password"], PDO::PARAM_STR);
			$st->bindValue(":firstName", $this->data["firstName"], PDO::PARAM_STR);
			$st->bindValue(":lastName", $this->data["lastName"], PDO::PARAM_STR);
			$st->bindValue(":joinDate", $this->data["joinDate"], PDO::PARAM_STR);
			$st->bindValue(":gender", $this->data["gender"], PDO::PARAM_STR);
			$st->bindValue(":favoriteGenre", $this->data["favoriteGenre"], PDO::PARAM_STR);
			$st->bindValue(":emailAddress", $this->data["emailAddress"], PDO::PARAM_STR);
			$st->bindValue(":otherInterests", $this->data["otherInterests"], PDO::PARAM_STR);
			$st->bindValue(":admin", 0, PDO::PARAM_INT);
			$st->execute();
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on insert, sql is $sql " . $e->getMessage());
		}
	}
	
	//function is part of the member manager application
	public function update() {
		$conn=parent::connect();
		//set up the encrypted password string here or set it to blank(this didn't work as a variable!)
		$passwordSql = $this->data["password"] ? "password = password(:password)," : "";
		$sql = "UPDATE " . TBL_MEMBERS . " SET
			username = :username,
			password = password(:password),
			firstName = :firstName,
			lastName = :lastName,
			joinDate = :joinDate,
			gender = :gender,
			favoriteGenre = :favoriteGenre,
			emailAddress = :emailAddress,
			otherInterests = :otherInterests,
			admin = :admin 
			WHERE id = :id";
	try {
			$st = $conn->prepare($sql);
			$st->bindValue(":id", $this->data["id"], PDO::PARAM_INT);
			$st->bindValue(":username", $this->data["username"], PDO::PARAM_STR);
			$st->bindValue(":password", $this->data["password"], PDO::PARAM_STR);
			$st->bindValue(":firstName", $this->data["firstName"], PDO::PARAM_STR);
			$st->bindValue(":lastName", $this->data["lastName"], PDO::PARAM_STR);
			$st->bindValue(":joinDate", $this->data["joinDate"], PDO::PARAM_STR);
			$st->bindValue(":gender", $this->data["gender"], PDO::PARAM_STR);
			$st->bindValue(":favoriteGenre", $this->data["favoriteGenre"], PDO::PARAM_STR);
			$st->bindValue(":emailAddress", $this->data["emailAddress"], PDO::PARAM_STR);
			$st->bindValue(":otherInterests", $this->data["otherInterests"], PDO::PARAM_STR);
			$st->bindValue(":admin", $this->data["admin"], PDO::PARAM_INT);
			$st->execute();	
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on update: " . $e->getMessage());
		}
	}
	
	//function is part of the member manager application
	public function delete() {
		$conn = parent::connect();
		$sql = "DELETE FROM " . TBL_MEMBERS . " WHERE id = :id";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":id", $this->data["id"], PDO::PARAM_INT);
			$st->execute();
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed: " . $e->getMessage());
		}
	}
		 
	//function added as part of the members area
	public function authenticate() {
		$conn=parent::connect();
		$sql = "SELECT * FROM " . TBL_MEMBERS . " WHERE username = :username AND password = password(:password)";
		
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":username", $this->data["username"], PDO::PARAM_STR);
			$st->bindValue(":password", $this->data["password"], PDO::PARAM_STR);
			$st->execute();
			$row=$st->fetch();
			parent::disconnect( $conn );
			if ($row) return new Member($row);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("query failed: " . $e->getMessage() );
		}
	}*/
}
?>