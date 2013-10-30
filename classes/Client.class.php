<?php


require_once("DataObject.class.php");

class Client extends DataObject {
	protected $data = array(
		//these fields are in the client table.
		"client_id"=>"",
		"client_name"=>"",
		"client_currency_index"=>"",
		"client_logo_link"=>"",
		"client_email"=>"",
		"client_phone"=>"",
		"client_address"=>"",
		"client_archived"=>"",
		//these fields are in the client_address table.
		//address fields, need to use for detailed addy.
		//"client_address_number"=>"",
		//"client_street_name"=>"",
		//"client_address_number"=>"",
		//"client_street_name"=>"",
		"client_state"=>"",
		"client_zip"=>"",
		//"client_apartment"=>"",
		"client_fax"=>"",
		"client_city"=>""
	);
	
	//display all information about a client returned as an array. This function returns all clients, archived and not.
	public function getClients() {
		$conn=parent::connect();
		$sql="SELECT * FROM " . TBL_CLIENT;
		try {
			$st = $conn->prepare($sql);
			$st->execute();
			$client=array();
			foreach ($st->fetchAll() as $row) {
				$clients[] = new Client($row);
			}
			parent::disconnect($conn);
			return array($clients);
		}catch(PDOException $e) {
			parent::disconnect($conn);
			die("query failed here: " . $e->getMessage() . "query is " . $sql);
		}
	}
		
	//return all data for a specific client based on the client_id.
	public function getClient($clientId) {
		$conn=parent::connect();
		//OLD SQL, use a left join here, rather than just deliver an error for missing data.
		//$sql = "SELECT " . TBL_CLIENT . ".*," . TBL_CLIENT_ADDRESS . ".* FROM " . TBL_CLIENT . "," . TBL_CLIENT_ADDRESS . " WHERE " . TBL_CLIENT . ".client_id = :client_id and ". TBL_CLIENT . ".client_id=" . TBL_CLIENT_ADDRESS . ".client_id";
		$sql = "SELECT " . TBL_CLIENT . ".*," . TBL_CLIENT_ADDRESS . ".* FROM " . TBL_CLIENT . " as client LEFT JOIN " . TBL_CLIENT_ADDRESS . " as client_address on client.client_id = client_address.client_id WHERE client.client_id = :client_id";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":client_id", $clientId, PDO::PARAM_INT);
			$st->execute();
			$row=$st->fetch();
			parent::disconnect($conn);
			if ($row) return new Client($row);
		} catch(PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on you: " . $e->getMessage());
		}
	}
	
	
	//return the clients name based on the client_id.
	//I'll keep this here as a utility function in case we need it.
	public function getClientNameById($client_id) {
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
	
	//return the client_id for a specific name.
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
	
	//function inserts new client into db. 	
	public function insertClient($client_email) {
		$conn=parent::connect();
		$sql = "INSERT INTO " . TBL_CLIENT . " (
			client_name,
			client_email,
			client_phone,
			client_fax,
			client_currency_index,
			client_logo_link,
			client_archived
			) VALUES (
			:client_name,
			:client_email,
			:client_phone,
			:client_fax,
			:client_currency_index,
			:client_logo_link,
			:client_archived
			)";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":client_name", $this->data["client_name"], PDO::PARAM_STR);
			$st->bindValue(":client_email", $this->data["client_email"], PDO::PARAM_STR);
			$st->bindValue(":client_phone", $this->data["client_phone"], PDO::PARAM_INT);
			$st->bindValue(":client_fax", $this->data["client_fax"], PDO::PARAM_INT);
			$st->bindValue(":client_currency_index", $this->data["client_currency_index"], PDO::PARAM_INT);
			$st->bindValue(":client_archived", $this->data["client_archived"], PDO::PARAM_INT);
			$st->bindValue(":client_logo_link", $this->data["client_logo_link"], PDO::PARAM_STR);
			$st->execute();
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on insert, sql is $sql " . $e->getMessage());
		}	
			
		//get the client ID out of the client table based on the email address we just inserted. It must use the same key (auto increment) created when the record
		//was inserted into the client table.
		$conn=parent::connect();
		$sql = "SELECT client_id FROM " . TBL_CLIENT . " WHERE client_email = '" . $client_email . "'";			
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":client_email", $client_email, PDO::PARAM_STR);
			$st->execute();
			$client_id=$st->fetch();
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed getting the client id, sql is $sql " . $e->getMessage());
		}
		//insert the address data into the address table for the appropriate client_id.
		$conn=parent::connect();
		$sql = "INSERT INTO " . TBL_CLIENT_ADDRESS . " (
			client_id,
			client_address,
			client_state,
			client_zip,
			client_city
		) VALUES (
			:client_id,
			:client_address,
			:client_state,
			:client_zip,
			:client_city
		)";
			
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":client_id", $client_id["client_id"], PDO::PARAM_STR);
			$st->bindValue(":client_address", $this->data["client_address"], PDO::PARAM_INT);
			$st->bindValue(":client_state", $this->data["client_state"], PDO::PARAM_STR);
			$st->bindValue(":client_zip", $this->data["client_zip"], PDO::PARAM_INT);
			$st->bindValue(":client_city", $this->data["client_city"], PDO::PARAM_STR);
			$st->execute();
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on insert of client address, sql is $sql " . $e->getMessage());
		}
	}
	
	//update the client record based on the client_id
	//if we want to break out the address, write the config to do the update later
	//so that we can update those fields as well.
	//9/4/13
	public function updateClient($client_id) {
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
			$st->bindValue(":client_logo_link", $this->data["client_logo_link"], PDO::PARAM_STR);
			$st->bindValue(":client_id", $client_id, PDO::PARAM_INT);
			$st->execute();	
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on update: " . $e->getMessage());
		}
		
		
		//update the address data into the address table for the appropriate client_id.
		$conn=parent::connect();
		$sql = "UPDATE " . TBL_CLIENT_ADDRESS . " SET
			client_address = :client_address,
			client_state = :client_state,
			client_zip = :client_zip,
			client_city = :client_city
			WHERE client_id = :client_id";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":client_id", $client_id, PDO::PARAM_STR);
			$st->bindValue(":client_address", $this->data["client_address"], PDO::PARAM_INT);
			$st->bindValue(":client_state", $this->data["client_state"], PDO::PARAM_STR);
			$st->bindValue(":client_zip", $this->data["client_zip"], PDO::PARAM_INT);
			$st->bindValue(":client_city", $this->data["client_city"], PDO::PARAM_STR);
			$st->execute();
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on update of client address, sql is $sql " . $e->getMessage());
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
	
	
//if you delete a client you delete all of the contacts associated with them as well.
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
			return 1;
		} catch(PDOException $e) {
			parent::disconnect($conn);
			return 0;
			die("Query failed on delete of client: " . $e->getMessage());
		}
	}
}
?>