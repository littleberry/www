<?php


require_once("DataObject.class.php");

class Timesheet extends DataObject {
	protected $data = array(
		//these fields are in the timesheet table.
		"timesheet_id"=>"",
		"timesheet_notes"=>"",
		"task_id"=>"",
		"project_id"=>"",
		"person_id"=>""
	);
	
	
	//display all information about a timesheet for a person, including the details, returned as an array of timesheet objects.
	//leave this here, but pretty sure this isn't going to be how we use this.
	public function getTimesheetDetail($person_id) {
		$conn=parent::connect();
		//$sql="SELECT * FROM " . TBL_TIMESHEET . " WHERE timesheet_id = :timesheet_id";
		//We're going to need the aggregate at some point so I'll stick it here.
		//SELECT timesheet.*, sum(timesheet_detail.timesheet_number_of_hours)  as totalhours FROM timesheet, timesheet_detail where timesheet_detail.timesheet_id = timesheet.timesheet_id group by timesheet.timesheet_id;
		$sql = "SELECT ts.*, td.* FROM " . TBL_TIMESHEET . " as ts, " . TBL_TIMESHEET_DETAIL . " as td WHERE ts.timesheet_id = td.timesheet_id AND ts.person_id = :person_id";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":person_id", $person_id, PDO::PARAM_INT);
			$st->execute();
			$timesheet_details=array();
			foreach ($st->fetchAll() as $row) {
				$timesheet_details[] = $row;
			}
			parent::disconnect($conn);
			return array($timesheet_details);
		}catch(PDOException $e) {
			parent::disconnect($conn);
			die("query failed getting the timesheets for this person: " . $e->getMessage() . "query is " . $sql);
		}
	}
	
	//get all of the timesheets for a specific person
	public function getTimesheetByPerson($person_id) {
		$conn=parent::connect();
		$sql="SELECT distinct(timesheet_id), project_id, task_id FROM " . TBL_TIMESHEET . " WHERE person_id = :person_id";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":person_id", $person_id, PDO::PARAM_INT);
			$st->execute();
			$timesheet=array();
			foreach ($st->fetchAll() as $row) {
				$timesheet[] = new Timesheet($row);
			}
			parent::disconnect($conn);
			return array($timesheet);
		}catch(PDOException $e) {
			parent::disconnect($conn);
			die("query failed here: " . $e->getMessage() . "query is " . $sql);
		}
	}
	
	//get all of the timesheet info for a specific timesheet.
	public function getTimesheetById($person_id, $task_id, $project_id) {
		$conn=parent::connect();
		$sql="SELECT * FROM " . TBL_TIMESHEET . " WHERE project_id = :project_id and person_id = :person_id and task_id = :task_id";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":project_id", $project_id, PDO::PARAM_INT);
			$st->bindValue(":person_id", $person_id, PDO::PARAM_INT);
			$st->bindValue(":task_id", $task_id, PDO::PARAM_INT);
			$st->execute();
			$timesheet=array();
			foreach ($st->fetchAll() as $row) {
				$timesheet[] = new Timesheet($row);
			}
			parent::disconnect($conn);
			return $timesheet;
		}catch(PDOException $e) {
			parent::disconnect($conn);
			die("query failed here: " . $e->getMessage() . "query is " . $sql);
		}
	}
			
	//function inserts new timesheet into db and returns the autoincrement field so we can update the timesheet_detail table. 	
	public function insertTimesheet($person_id, $task_id, $project_id) {
		$conn=parent::connect();
		$sql = "INSERT INTO " . TBL_TIMESHEET . " (
			timesheet_id,
			timesheet_notes,
			task_id,
			project_id,
			person_id
			) VALUES (
			:timesheet_id,
			:timesheet_notes,
			:task_id,
			:project_id,
			:person_id
			)";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":timesheet_id", 'auto', PDO::PARAM_STR);
			$st->bindValue(":timesheet_notes", $this->data["timesheet_notes"], PDO::PARAM_INT);
			$st->bindValue(":task_id", $task_id, PDO::PARAM_INT);
			$st->bindValue(":project_id", $project_id, PDO::PARAM_INT);
			$st->bindValue(":person_id", $person_id, PDO::PARAM_STR);
			$st->execute();
			$sql = "SELECT LAST_INSERT_ID() FROM " . TBL_TIMESHEET;
			$st = $conn->prepare($sql);
			$st->execute();
			$row=$st->fetch();
			parent::disconnect($conn);
			if ($row) return $row;
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on insert of timesheet, sql is $sql " . $e->getMessage());
		}	
	}
	
	//this is old, the last insert ID is retrieved using MySQL now.
	public function getLastInsert() {
		$conn=parent::connect();
		$sql = "SELECT LAST_INSERT_ID() FROM " . TBL_TIMESHEET;
		try {
			$st = $conn->prepare($sql);
			$st->execute();
			$row=$st->fetch();
			parent::disconnect($conn);
			if ($row) return $row;
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed getting the increment value: " . $e->getMessage() . " sql is " . $sql);
		}
	}
	
	//update the timesheet data.
	public function updateTimesheet($timesheet_id) {
		$conn=parent::connect();
		$sql = "UPDATE " . TBL_TIMESHEET . " SET
			timesheet_notes = :timesheet_notes,
			task_id = :task_id,
			project_id = :project_id,
			person_id = :person_id
			WHERE timesheet_id = :timesheet_id";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":timesheet_notes", $this->data["timesheet_notes"], PDO::PARAM_STR);
			$st->bindValue(":task_id", $this->data["task_id"], PDO::PARAM_INT);
			$st->bindValue(":project_id", $this->data["project_id"], PDO::PARAM_STR);
			$st->bindValue(":person_id", $this->data["person_id"], PDO::PARAM_INT);
			$st->bindValue(":timesheet_id", $timesheet_id, PDO::PARAM_INT);
			$st->execute();
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on update of timesheet, sql is $sql " . $e->getMessage());
		}

	}
}


/*
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
			$st->bindValue(":client_logo_link", basename($this->data["client_logo_link"]), PDO::PARAM_STR);
			$st->bindValue(":client_id", $client_id, PDO::PARAM_INT);
			$st->execute();	
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on update: " . $e->getMessage());
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
		$conn=parent::connect();
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
		$conn=parent::connect();
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
	}*/
?>