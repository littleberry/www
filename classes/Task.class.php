<?php


//connectivity
require_once("DataObject.class.php");

class Task extends DataObject {
	protected $data = array(
		//these fields are in the tasks table.
		"task_id"=>"",
		"task_name"=>"",
		"task_hourly_rate"=>"",
		"task_bill_by_default"=>"",
		"task_common"=>"",
		"task_archived"=>""
	);
	
	//display all information about a task. returned as an array. This function returns all tasks, archived and not, based on value passed in.
	public static function getTasks($archive_flag) {
		$conn=parent::connect();
		$sql="SELECT * FROM " . TBL_TASK . " WHERE task_archived = :archive_flag";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":archive_flag", $archive_flag, PDO::PARAM_INT);
			$st->execute();
			foreach ($st->fetchAll() as $row) {
				$tasks[] = new Task($row);
			}
			parent::disconnect($conn);
			return array($tasks);
		}catch(PDOException $e) {
			parent::disconnect($conn);
			die("query failed here: " . $e->getMessage() . "query is " . $sql);
		}
	}
		
	//return all data for a task client based on the task_id.
	//*OK, I'll be honest here. This query makes no sense, but I'll leave it here, because clearly I am either
	//crazy or tired.
	public static function getTask($task_id) {
		$conn=parent::connect();
		$sql = "SELECT * FROM " . TBL_CLIENT . " WHERE task.task_id = :task_id";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":task_id", $task_id, PDO::PARAM_INT);
			$st->execute();
			$row=$st->fetch();
			parent::disconnect($conn);
			if ($row) return new Task($row);
		} catch(PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on you: " . $e->getMessage());
		}
	}
	
	//function returns the individual common tasks.
	public static function getCommonTasks() {
		$conn=parent::connect();
		$sql = "SELECT distinct(task_name), task_id FROM " . TBL_TASK . " WHERE task_common = 1";
	
		try {
			$st = $conn->prepare($sql);
			$st->execute();
			foreach ($st->fetchAll() as $row) {
				$task[] = new Task($row);
			}
			parent::disconnect($conn);
			return array($task);
		}catch(PDOException $e) {
			parent::disconnect($conn);
			die("query failed here: " . $e->getMessage() . "query is " . $sql);
		}	
	}
	
	//function inserts new task into db. 
	
	public function insertTask() {
		$conn=parent::connect();
		$sql = "INSERT INTO " . TBL_TASK . " (
			task_name,
			task_hourly_rate,
			task_bill_by_default,
			task_common
			) VALUES (
			:task_name,
			:task_hourly_rate,
			:task_bill_by_default,
			:task_common
			)";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":task_name", $this->data["task_name"], PDO::PARAM_STR);
			$st->bindValue(":task_hourly_rate", $this->data["task_hourly_rate"], PDO::PARAM_STR);
			$st->bindValue(":task_bill_by_default", $this->data["task_bill_by_default"], PDO::PARAM_INT);
			$st->bindValue(":task_common", $this->data["task_common"], PDO::PARAM_INT);
			$st->execute();
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on insert, sql is $sql " . $e->getMessage());
		}	
	}



	public function getTaskId($task_name) {
		$conn=parent::connect();
		$sql = "SELECT task_id FROM " . TBL_TASK . " WHERE task_name = :task_name";			
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":task_name", $task_name, PDO::PARAM_STR);
			$st->execute();
			$row=$st->fetch();
			parent::disconnect($conn);
			if ($row) return $row;
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed getting the task id, sql is $sql " . $e->getMessage());
		}
	}
	
	//return all data for a task based on the task_id.
	public static function getTaskById($task_id) {
		$conn=parent::connect();
		$sql = "SELECT * FROM " . TBL_TASK . " WHERE task_id = :task_id";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":task_id", $task_id, PDO::PARAM_INT);
			$st->execute();
			$row=$st->fetch();
			parent::disconnect($conn);
			if ($row) return new Task($row);
		} catch(PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on you: " . $e->getMessage());
		}
	}
	
	//update the task's information based on the task_id.	
	public function updateTask($task_id) {
		$conn=parent::connect();
		$sql = "UPDATE " . TBL_TASK . " SET
				task_name = :task_name,
				task_hourly_rate = :task_hourly_rate,
				task_bill_by_default = :task_bill_by_default,
				task_common = :task_common,
				task_archived = :task_archived
				WHERE task_id = :task_id";
			try {
				$st = $conn->prepare($sql);
				$st->bindValue(":task_name", $this->data["task_name"], PDO::PARAM_STR);
				$st->bindValue(":task_hourly_rate", $this->data["task_hourly_rate"], PDO::PARAM_STR);
				$st->bindValue(":task_bill_by_default", $this->data["task_bill_by_default"], PDO::PARAM_STR);
				$st->bindValue(":task_common", $this->data["task_common"], PDO::PARAM_STR);
				$st->bindValue(":task_archived", $this->data["task_archived"], PDO::PARAM_STR);
				$st->bindValue(":task_id", $task_id, PDO::PARAM_INT);
				$st->execute();	
				parent::disconnect($conn);
			} catch (PDOException $e) {
				parent::disconnect($conn);
				die("Query failed on update of task: " . $e->getMessage() . " sql is " . $sql);
			}
	}
	
	//archive this task, you also have to remove it from project_task.	
	public function archiveTask($archive_flag, $task_id) {
		$conn=parent::connect();
		$sql = "UPDATE " . TBL_TASK . " SET
				task_archived = :archive_flag WHERE task_id = :task_id";
			try {
				$st = $conn->prepare($sql);
				$st->bindValue(":archive_flag", $archive_flag, PDO::PARAM_INT);
				$st->bindValue(":task_id", $task_id, PDO::PARAM_INT);
				$st->execute();	
				parent::disconnect($conn);
			} catch (PDOException $e) {
				parent::disconnect($conn);
				die("Query failed on archive of task: " . $e->getMessage() . " sql is " . $sql);
			}
	}


}



	
/*OTHER FUNCTIONS BELOW THIS LINE
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
		/*} else {
			$conn=parent::connect();
			$sql = "INSERT INTO " . TBL_CLIENT . " (
				client_name,
				client_address,
				client_currency_index
				) VALUES (
				:client_name,
				:client_address,
				:client_currency_index
				)";
			try {
				$st = $conn->prepare($sql);
				$st->bindValue(":client_name", $this->data["client_name"], PDO::PARAM_STR);
				$st->bindValue(":client_address", $this->data["client_address"], PDO::PARAM_STR);
				$st->bindValue(":client_currency_index", $this->data["client_currency_index"], PDO::PARAM_INT);
				$st->execute();
				parent::disconnect($conn);
			} catch (PDOException $e) {
				parent::disconnect($conn);
				die("Query failed on insert of new client record, sql is $sql " . $e->getMessage());
			}	
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
?>