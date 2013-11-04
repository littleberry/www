<?php


require_once("DataObject.class.php");

class Project extends DataObject {
	protected $data = array(
		//these fields are in the project table.
		"project_id"=>"",
		"project_code"=>"",
		"project_name"=>"",
		"client_id" =>"",
		"project_billable"=>"",
		"project_invoice_by"=>"",
		"project_hourly_rate"=>"",
		"project_budget_by"=>"",
		"project_budget_total_fees"=>"",
		"project_budget_total_hours"=>"",
		"project_send_email_percentage"=>"",
		"project_show_budget"=>"",
		"project_budget_includes_expenses"=>"",
		"project_notes"=>"",
		"project_archived"=>""
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

	//return 1 if client has active projects.
	public static function hasActiveProjects($client_id) {
		$conn=parent::connect();
		$sql="SELECT COUNT(*) FROM " . TBL_PROJECT . " WHERE client_id = :client_id";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":client_id", $client_id, PDO::PARAM_INT);
			$st->execute();
			$row=$st->fetch();
			parent::disconnect($conn);
			return $row;
		}catch(PDOException $e) {
			parent::disconnect($conn);
			die("query failed returning the projects: " . $e->getMessage() . "query is " . $sql);
		}
	}
	
	//return 1 if person has projects
	//do we want to make these active projects only?
	public static function personHasActiveProjects($person_id) {
		$conn=parent::connect();
		$sql="SELECT COUNT(*) FROM " . TBL_PROJECT_PERSON . " WHERE person_id = :person_id";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":person_id", $person_id, PDO::PARAM_INT);
			$st->execute();
			$row=$st->fetch();
			parent::disconnect($conn);
			return $row;
		}catch(PDOException $e) {
			parent::disconnect($conn);
			die("query failed returning the projects for the person: " . $e->getMessage() . "query is " . $sql);
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
			project_id,
			project_code,
			project_name,
			client_id,
			project_billable,
			project_invoice_by,
			project_hourly_rate,
			project_budget_by,
			project_budget_total_fees,
			project_budget_total_hours,
			project_send_email_percentage,
			project_show_budget,
			project_budget_includes_expenses,
			project_notes,
			project_archived
			) VALUES (
			:project_id,
			:project_code,
			:project_name,
			:client_id,
			:project_billable,
			:project_invoice_by,
			:project_hourly_rate,
			:project_budget_by,
			:project_budget_total_fees,
			:project_budget_total_hours,
			:project_send_email_percentage,
			:project_show_budget,
			:project_budget_includes_expenses,
			:project_notes,
			:project_archived
			)";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":project_id", $this->data["project_id"], PDO::PARAM_INT);
			$st->bindValue(":project_code", $this->data["project_code"], PDO::PARAM_INT);
			$st->bindValue(":project_name", $this->data["project_name"], PDO::PARAM_STR);
			$st->bindValue(":client_id", $this->data["client_id"], PDO::PARAM_INT);
			$st->bindValue(":project_billable", $this->data["project_billable"], PDO::PARAM_STR);
			$st->bindValue(":project_invoice_by", $this->data["project_invoice_by"], PDO::PARAM_STR);
			$st->bindValue(":project_hourly_rate", $this->data["project_hourly_rate"], PDO::PARAM_INT);
			$st->bindValue(":project_budget_by", $this->data["project_budget_by"], PDO::PARAM_STR);
			$st->bindValue(":project_budget_total_fees", $this->data["project_budget_total_fees"], PDO::PARAM_INT);
			$st->bindValue(":project_budget_total_hours", $this->data["project_budget_total_hours"], PDO::PARAM_INT);
			$st->bindValue(":project_send_email_percentage", $this->data["project_send_email_percentage"], PDO::PARAM_STR);
			$st->bindValue(":project_show_budget", $this->data["project_show_budget"], PDO::PARAM_STR);
			$st->bindValue(":project_budget_includes_expenses", $this->data["project_budget_includes_expenses"], PDO::PARAM_STR);
			$st->bindValue(":project_notes", $this->data["project_notes"], PDO::PARAM_STR);
			$st->bindValue(":project_archived", $this->data["project_archived"], PDO::PARAM_STR);
			$st->execute();
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on insert of project, sql is $sql " . $e->getMessage());
		}	
	}
	
	//return the clients with active projects. We're only returning all this stuff because we may need it later for debug.
	function getClientsProjectsByStatus($ArchiveStatus) {
		$conn=parent::connect();
		$sql = "SELECT distinct(client.client_id), table_project.project_archived, client.client_name, table_project.project_name, table_project.project_id FROM " . TBL_CLIENT . " as client JOIN " . TBL_PROJECT . " as table_project on client.client_id = table_project.client_id WHERE table_project.project_archived = :ArchiveStatus";
		
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":ArchiveStatus", $ArchiveStatus, PDO::PARAM_STR);
			$st->execute();
			parent::disconnect($conn);
			$projects = array();
			foreach ($st->fetchAll() as $row) {
				//return $row;
				$projects[] = new Project($row);
			}
			return $projects;
		} catch(PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on you: " . $e->getMessage());
		}
	}

//return the details for the project as an object.
public static function getProjectByProjectId($project_id) {
		$conn=parent::connect();
		$sql = "SELECT * FROM " . TBL_PROJECT . " WHERE project_id = :project_id";
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


	//update the client record based on the client_id
	//if we want to break out the address, write the config to do the update later
	//so that we can update those fields as well.
	//9/4/13
	public function updateProject($project_id) {
		$conn=parent::connect();
		$sql = "UPDATE " . TBL_PROJECT . " SET
				project_name = :project_name,
				project_code = :project_code,
				client_id = :client_id,
				project_notes = :project_notes,
				project_archived = :project_archived,
				project_billable = :project_billable,
				project_invoice_by = :project_invoice_by,
				project_hourly_rate = :project_hourly_rate,
				project_budget_by = :project_budget_by,
				project_budget_total_fees = :project_budget_total_fees,
				project_budget_total_hours = :project_budget_total_hours,
				project_send_email_percentage = :project_send_email_percentage,
				project_show_budget = :project_show_budget,
				project_budget_includes_expenses = :project_budget_includes_expenses
				WHERE project_id = :project_id";
			try {
				$st = $conn->prepare($sql);
				$st->bindValue(":project_name", $this->data["project_name"], PDO::PARAM_STR);
				$st->bindValue(":project_code", $this->data["project_code"], PDO::PARAM_STR);
				$st->bindValue(":client_id", $this->data["client_id"], PDO::PARAM_INT);
				$st->bindValue(":project_notes", $this->data["project_notes"], PDO::PARAM_INT);
				$st->bindValue(":project_archived", $this->data["project_archived"], PDO::PARAM_INT);
				$st->bindValue(":project_billable", $this->data["project_billable"], PDO::PARAM_STR);
				$st->bindValue(":project_invoice_by", $this->data["project_invoice_by"], PDO::PARAM_STR);
				$st->bindValue(":project_hourly_rate", $this->data["project_hourly_rate"], PDO::PARAM_INT);
				$st->bindValue(":project_budget_by", $this->data["project_budget_by"], PDO::PARAM_STR);
				$st->bindValue(":project_budget_total_fees", $this->data["project_budget_total_fees"], PDO::PARAM_INT);
				$st->bindValue(":project_budget_total_hours", $this->data["project_budget_total_hours"], PDO::PARAM_INT);
				$st->bindValue(":project_send_email_percentage", $this->data["project_send_email_percentage"], PDO::PARAM_STR);
				$st->bindValue(":project_show_budget", $this->data["project_show_budget"], PDO::PARAM_STR);
				$st->bindValue(":project_budget_includes_expenses", $this->data["project_budget_includes_expenses"], PDO::PARAM_STR);
				$st->bindValue(":project_id", $this->data["project_id"], PDO::PARAM_INT);
				$st->execute();	
				parent::disconnect($conn);
			} catch (PDOException $e) {
				parent::disconnect($conn);
				die("Query failed on project update: " . $e->getMessage() . " sql is " . $sql);
			}
	}	
	
	public static function getEnumValues($colName) {
		$conn=parent::connect();
		$sql = "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '" . TBL_PROJECT . "' AND COLUMN_NAME = :colName";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":colName", $colName, PDO::PARAM_STR);
			$st->execute();
			$row=$st->fetch();
			parent::disconnect($conn);
			if ($row) return $row;
		} catch(PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on you: " . $e->getMessage() . " sql is " . $sql);
		}
	}

	
	
	//function returns project ID because it is an auto-increment. We need this value
	//to update the join tables project_person and project_task.
	public function getProjectId($project_name) {
		$conn=parent::connect();
		$sql = "SELECT project_id FROM " . TBL_PROJECT . " WHERE project_name = :project_name";			
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":project_name", $project_name, PDO::PARAM_STR);
			$st->execute();
			$row=$st->fetch();
			parent::disconnect($conn);
			if ($row) return $row;
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed getting the client id, sql is $sql " . $e->getMessage());
		}
	}
	
	//update the project archive. This must be called in project!
	public function setArchiveFlag($flag, $project_id) {
	error_log("SETTING FLAG TO " . $flag . " AND PROJECT ID TO " . $project_id );
		$conn=parent::connect();
		$sql = "UPDATE " . TBL_PROJECT . " SET
			project_archived = :project_archived
			WHERE project_id = :project_id";
	try {
			$st = $conn->prepare($sql);
			$st->bindValue(":project_id", $project_id, PDO::PARAM_INT);
			$st->bindValue(":project_archived", $flag, PDO::PARAM_INT);
			$st->execute();	
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on update: " . $e->getMessage());
		}

	}	
}

?>