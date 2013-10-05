<?php


//connectivity
require_once("DataObject.class.php");

class Project extends DataObject {
	protected $data = array(
		//these fields are in the project table.
		"project_id"=>"",
		"project_code"=>"",
		"project_name"=>"",
		"client_id" =>"",
		"project_invoice_method"=>"",
		"project_invoice_rate"=>"",
		"project_budget_type"=>"",
		"project_budget_hours"=>"",
		"project_show_budget"=>"",
		"project_send_email"=>"",
		"project_notes"=>"",
	);
	
	//display all information about a project. 
	//Returned value is an array of project objects. This function returns all projects, archived and not.
	public static function getProjects() {
		$conn=parent::connect();
		$sql="SELECT * FROM " . TBL_PROJECT;
		try {
			$st = $conn->prepare($sql);
			$st->execute();
			$project=array();
			foreach ($st->fetchAll() as $row) {
				$projects[] = new Project($row);
			}
			$row=$st->fetch();
			parent::disconnect($conn);
			return array($projects);
		}catch(PDOException $e) {
			parent::disconnect($conn);
			die("query failed returning the projects: " . $e->getMessage() . "query is " . $sql);
		}
	}
		
	//return all data for a specific client based on the client_id.
	//note that in the UI, this will have to be based on the person, not on the client.
	//returned value is an array of project objects.
	public static function getProjectByClientId($client_id) {
		$conn=parent::connect();
		$sql = "SELECT * FROM " . TBL_PROJECT . " WHERE client_id = '" . $client_id . "'";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":client_id", $client_id, PDO::PARAM_INT);
			$st->execute();
			$projects=array();
			foreach ($st->fetchAll() as $row) {
				$projects[] = new Project($row);
			}
			$row=$st->fetch();
			parent::disconnect($conn);
			return $projects;
		}catch(PDOException $e) {
			parent::disconnect($conn);
			die("query failed returning the projects: " . $e->getMessage() . "query is " . $sql);
		}
	}
	//function inserts new client into db. If ADDRESS_CONFIG value is 0, insert the address as a large varchar field, not as individual fields in the database.
	//it also gets the key for the record being inserted and inserts
	//a row in the address table if the information was sent.
	
	public function insertProject($client_id) {
		//insert the project into the project table. 	
		$conn=parent::connect();
		$sql = "INSERT INTO " . TBL_PROJECT . " (
			project_code,
			project_name,
			client_id,
			project_invoice_method,
			project_invoice_rate,
			project_budget_type,
			project_budget_hours,
			project_notes
			) VALUES (
			:project_code,
			:project_name,
			:client_id,
			:project_invoice_method,
			:project_invoice_rate,
			:project_budget_type,
			:project_budget_hours,
			:project_notes
			)";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":project_code", $this->data["project_code"], PDO::PARAM_INT);
			$st->bindValue(":project_name", $this->data["project_name"], PDO::PARAM_STR);
			$st->bindValue(":client_id", $this->data["client_id"], PDO::PARAM_INT);
			$st->bindValue(":project_invoice_method", $this->data["project_invoice_method"], PDO::PARAM_STR);
			$st->bindValue(":project_invoice_rate", $this->data["project_invoice_rate"], PDO::PARAM_STR);
			$st->bindValue(":project_budget_type", $this->data["project_budget_type"], PDO::PARAM_STR);
			$st->bindValue(":project_budget_hours", $this->data["project_budget_hours"], PDO::PARAM_STR);
			$st->bindValue(":project_notes", $this->data["project_notes"], PDO::PARAM_STR);
			$st->execute();
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on insert of project, sql is $sql " . $e->getMessage());
		}	
	}
	
	function getClientsProjectsByStatus($ArchiveStatus) {
		$conn=parent::connect();
		//$sql = "SELECT distinct(client_id) FROM " . TBL_PROJECT . " WHERE project_archived = '" . $ArchiveStatus . "'";
		$sql = "SELECT distinct(client.client_name), table_project.client_id FROM " . TBL_CLIENT . " as client JOIN " . TBL_PROJECT . " as table_project on client.client_id = table_project.client_id";
		
		try {
			$st = $conn->prepare($sql);
			$st->execute();
			parent::disconnect($conn);
			$_clients = array();
			foreach ($st->fetchAll() as $row) {
				//return $row;
				$clients[] = $row;
			}
			return $clients;
		} catch(PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on you: " . $e->getMessage());
		}
	}

//return the details for the project as an object.
public static function getProjectByProjectId($project_id) {
		$conn=parent::connect();
		$sql = "SELECT * FROM " . TBL_PROJECT . " WHERE project_id = :project_id'";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":project_id", $project_id, PDO::PARAM_INT);
			$st->execute();
			$row=$st->fetch();
			parent::disconnect($conn);
			if ($row) return new Project($row);
		} catch(PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on you: " . $e->getMessage());
		}
	}


	
	
	//return the clients name based on the client_id.
	//I'll keep this here as a utility function in case we need it.
	/*public function getClientNameById($client_id) {
		$conn=parent::connect();
		$sql = "SELECT client_name FROM " . TBL_CLIENT . " WHERE client_id = '" . $client_id . "'";			
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":client_id", $client_id, PDO::PARAM_INT);
			$st->execute();
			$row=$st->fetch();
			parent::disconnect($conn);
			if ($row) return $row;
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed getting the client name, sql is $sql " . $e->getMessage());
		}
	}
	
	public function getClientId($client_name) {
		$conn=parent::connect();
		$sql = "SELECT client_id FROM " . TBL_CLIENT . " WHERE client_name = '" . $client_name . "'";			
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":client_name", $client_name, PDO::PARAM_STR);
			$st->execute();
			$row=$st->fetch();
			parent::disconnect($conn);
			if ($row) return $row;
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed getting the client id, sql is $sql " . $e->getMessage());
		}
	}
	
	//get the available currencies out of the currency table
	//9/4: Client only needs US here, so this only returns USD at this point, but this is built to handle others.
	public function getCurrency() {
		$conn=parent::connect();
		$sql = "SELECT * FROM " . TBL_CURRENCY;
		
		try {
			$st = $conn->prepare($sql);
			$st->execute();
			parent::disconnect($conn);
			$_currency = array();
			foreach ($st->fetchAll() as $row) {
				$currency[] = $row;
			}
			return $currency;
		} catch(PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on you: " . $e->getMessage());
		}
	}
	
	//get the currency for a specific currency index
	public function getCurrencyByIndex($client_currency_index) {
		$conn=parent::connect($client_currency_index);
		$sql = "SELECT client_preferred_currency FROM " . TBL_CURRENCY . " WHERE client_currency_index = :client_currency_index";
		
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":client_currency_index", $client_currency_index, PDO::PARAM_INT);
			$st->execute();
			$row=$st->fetch();
			parent::disconnect($conn);
			//just return the value for the currency.
			if ($row) return $row[0];
		} catch(PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on you: " . $e->getMessage());
		}
	}
	
		
	//update the client record based on the client_id
	//if we want to break out the address, write the config to do the update later
	//so that we can update those fields as well.
	//9/4/13
	/*public function updateClient($client_id) {
		$conn=parent::connect();
		$sql = "UPDATE " . TBL_CLIENT . " SET
				client_name = :client_name,
				client_email = :client_email,
				client_phone = :client_phone,
				client_fax = :client_fax,
				client_currency_index = :client_currency_index,
				client_logo_link = :client_logo_link,
				client_archived = :client_archived
				WHERE client_id = :client_id";
			try {
				$st = $conn->prepare($sql);
				$st->bindValue(":client_name", $this->data["client_name"], PDO::PARAM_STR);
				$st->bindValue(":client_email", $this->data["client_email"], PDO::PARAM_STR);
				$st->bindValue(":client_phone", $this->data["client_phone"], PDO::PARAM_INT);
				$st->bindValue(":client_fax", $this->data["client_fax"], PDO::PARAM_INT);
				$st->bindValue(":client_archived", $this->data["client_archived"], PDO::PARAM_INT);
				$st->bindValue(":client_currency_index", $this->data["client_currency_index"], PDO::PARAM_INT);
				//NO NO NO THIS IS TOO HARDCODED!!
				if ($this->data["client_logo_link"]) {
					$st->bindValue(":client_logo_link", "images/" . $this->data["client_logo_link"], PDO::PARAM_STR);
				} else {
					$st->bindValue(":client_logo_link", "images/default.jpg", PDO::PARAM_STR);
				}				
				$st->bindValue(":client_id", $client_id, PDO::PARAM_INT);
				$st->execute();	
				parent::disconnect($conn);
			} catch (PDOException $e) {
				parent::disconnect($conn);
				die("Query failed on update: " . $e->getMessage());
			}
	}
	
	//update the archive flag in the client table.
	public function setArchiveFlag($flag, $client_id) {
		$conn=parent::connect();
		$sql = "UPDATE " . TBL_CLIENT . " SET
			client_archived = :client_archived
			WHERE client_id = :client_id";
	try {
			$st = $conn->prepare($sql);
			$st->bindValue(":client_id", $client_id, PDO::PARAM_INT);
			$st->bindValue(":client_archived", $flag, PDO::PARAM_INT);
			$st->execute();	
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on update: " . $e->getMessage());
		}

	}
	
	//get the archive flag out of the client table.
	public function getArchiveFlag($client_id) {
		$conn=parent::connect($client_id);
		$sql = "SELECT client_archived FROM " . TBL_CLIENT . " WHERE client_id = :client_id";
		
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":client_id", $client_id, PDO::PARAM_INT);
			$st->execute();
			$row=$st->fetch();
			parent::disconnect($conn);
			if ($row) return $row[0];
		} catch(PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on you: " . $e->getMessage());
		}
	}
	
	
	//get the archive flag out of the client table.
	public function deleteClient($client_id) {
		//first delete the contacts.
		$conn=parent::connect();
		$sql = "DELETE FROM " . TBL_CONTACT . " WHERE client_id = :client_id";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":client_id", $client_id, PDO::PARAM_INT);
			$st->execute();	
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on delete of contact rows.: " . $e->getMessage());
		}
		$conn=parent::connect($client_id);
		$sql = "DELETE FROM " . TBL_CLIENT . " WHERE client_id = :client_id";
		
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":client_id", $client_id, PDO::PARAM_INT);
			$st->execute();
			parent::disconnect($conn);
		} catch(PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on delete of client: " . $e->getMessage());
		}
	}
/*	// OLD function stubs below this line.
	//display all client names
	/*public static function getClientNameAndLogo($clientId) {
		$conn=parent::connect();
		$sql="SELECT client_name, client_logo_link FROM " . TBL_CLIENT . " WHERE client_id = :client_id";
		
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":client_id", $clientId, PDO::PARAM_INT);
			$st->execute();
			$row=$st->fetch();
			parent::disconnect($conn);
			if ($row) return new Client($row);
		}catch(PDOException $e) {
			parent::disconnect($conn);
			die("query failed here: " . $e->getMessage() . "query is " . $sql);
		}
	}
	
	

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
	
	public function getGenres() {
		return $this->_genres;
	}
		
	public function getGenderString() {
		return ($this->data["gender"] == "f") ? "Female" : "Male";
	}
	
	public function getFavoriteGenreString () {
		return ($this->_genres[$this->data["favoriteGenre"]]);
	}
	
	***OLD FUNCTION, CAN PROBABLY REMOVE***
	public function insertClientAddress($clientId) {
		$conn=parent::connect();
		$sql = "INSERT INTO " . TBL_CLIENT_ADDRESS . " (
			client_id,
			client_address_number,
			client_street_name,
			client_state,
			client_zip,
			client_apartment,
			client_city
			) VALUES (
			:client_id,
			:client_address_number,
			:client_street_name,
			:client_state,
			:client_zip,
			:client_apartment,
			:client_city
			)";
			
	try {
			$st = $conn->prepare($sql);
			$st->bindValue(":client_id", $this->data_address["client_id"], PDO::PARAM_STR);
			$st->bindValue(":client_address_number", $this->data_address["client_address_number"], PDO::PARAM_INT);
			$st->bindValue(":client_street_name", $this->data_address["client_street_name"], PDO::PARAM_STR);
			$st->bindValue(":client_state", $this->data_address["client_state"], PDO::PARAM_STR);
			$st->bindValue(":client_zip", $this->data_address["client_zip"], PDO::PARAM_INT);
			$st->bindValue(":client_apartment", $this->data_address["client_apartment"], PDO::PARAM_STR);
			$st->bindValue(":client_city", $this->data_address["client_city"], PDO::PARAM_STR);
			$st->execute();
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on insert, sql is $sql " . $e->getMessage());
		}
	}
	
	**OLD FUNCTION CAN PROBABLY REMOVE**
	public function getClientIdByName($client_name) {
		$conn=parent::connect();
		$sql = "SELECT client_id FROM " . TBL_CLIENT . " WHERE client_name = :client_name";			
		try {
			$st = $conn->prepare($sql);
			//$st->bindValue(":client_name", $this->data["client_name"], PDO::PARAM_STR);
			$st->bindValue(":client_name", $client_name, PDO::PARAM_STR);
			$st->execute();
			$row=$st->fetch();
			parent::disconnect($conn);
			if ($row) return new Client($row);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on insert, sql is $sql " . $e->getMessage());
		}
	}
	
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