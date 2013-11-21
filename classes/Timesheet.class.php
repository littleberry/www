<?php


require_once("DataObject.class.php");

class Timesheet extends DataObject {
	protected $data = array(
		//these fields are in the timesheet table.
		"timesheet_id"=>"",
		"timesheet_approved"=>"",
		"timesheet_submitted"=>"",
		"timesheet_start_date"=>"",
		"timesheet_end_date"=>"",
		"person_id"=>""
	);
	
	
	//display all information about a timesheet for a person, including the details and whether it is submitted, returned as an array of timesheet_item objects. 
	public function getSubmittedTimesheetDetail($timesheet_id, $is_submitted) {
		$conn=parent::connect();
		//$sql="SELECT * FROM " . TBL_TIMESHEET . " WHERE timesheet_id = :timesheet_id";
		//We're going to need the aggregate at some point so I'll stick it here.
		$sql = "SELECT ts.*, td.* FROM " . TBL_TIMESHEET . " as ts, " . TBL_TIMESHEET_ITEM . " as td WHERE ts.timesheet_id = td.timesheet_item_id";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":timesheet_id", $timesheet_id, PDO::PARAM_INT);
			$st->execute();
			$timesheet_details=array();
			foreach ($st->fetchAll() as $row) {
				$timesheet_item[] = $row;
				error_log(print_r($row,true));
			}
			parent::disconnect($conn);
			return array($timesheet_item);
		}catch(PDOException $e) {
			parent::disconnect($conn);
			die("query failed getting the timesheets for this person: " . $e->getMessage() . "query is " . $sql);
		}
	}
	
	
	//get all of the submitted timesheets for a specific person (this just does the assignee right now, but it should include all people that are administrators as well).
	//maybe re-write this as a join. This is going to be slow and is confusing.
	public function getSubmittedTimesheetsByManager($manager_email) {
		$conn=parent::connect();
		error_log($manager_email);
		$sql="SELECT timesheet_id FROM " . TBL_TIMESHEET . " WHERE timesheet_id in (select timesheet_item_id from " . TBL_TIMESHEET_ITEM . " WHERE project_id in (select project_id from " . TBL_PROJECT_PERSON . " WHERE project_assigned_by = :manager_email)) and timesheet_submitted = 1";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":manager_email", $manager_email, PDO::PARAM_STR);
			//$st->bindValue(":timesheet_date", date('y-m-d', strtotime($timesheet_date)), PDO::PARAM_STR);
			$st->execute();
			$timesheet=array();
			foreach ($st->fetchAll() as $row) {
				error_log(print_r($row,true));
				$timesheet[] = new Timesheet($row);
			}
			parent::disconnect($conn);
			return array($timesheet);
		}catch(PDOException $e) {
			parent::disconnect($conn);
			die("query failed here: " . $e->getMessage() . "query is " . $sql);
		}
	}
	
	//get all of the timesheet ids for a specific person
	public function getTimesheetIds($person_id) {
		$conn=parent::connect();
		$sql="SELECT distinct(timesheet_id) FROM " . TBL_TIMESHEET . " WHERE person_id = :person_id";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":person_id", $person_id, PDO::PARAM_INT);
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
	
	//get all of the timesheet info for a specific timesheet.
	public function getTimesheetById($person_id, $timesheet_start_date, $timesheet_end_date) {
		$conn=parent::connect();
		$sql="SELECT * FROM " . TBL_TIMESHEET . " WHERE person_id = :person_id and timesheet_start_date = :timesheet_start_date and timesheet_end_date = :timesheet_end_date";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":timesheet_start_date", date('y-m-d', strtotime($timesheet_start_date)), PDO::PARAM_STR);
			$st->bindValue(":timesheet_end_date", date('y-m-d', strtotime($timesheet_end_date)), PDO::PARAM_STR);
			$st->bindValue(":person_id", $person_id, PDO::PARAM_INT);
			$st->execute();
			$timesheet=array();
			foreach ($st->fetchAll() as $row) {
				$timesheet[] = new Timesheet($row);
			}
			parent::disconnect($conn);
			if (count($timesheet) > 0) {
				return $timesheet;
			} else {
				return 0;
			}
		}catch(PDOException $e) {
			parent::disconnect($conn);
			die("query failed here: " . $e->getMessage() . "query is " . $sql);
		}
	}
	

			
	//function inserts new timesheet into db and returns the autoincrement field so we can update the timesheet_item table with the key 	
	public function insertTimesheet($person_id, $timesheet_start_date, $timesheet_end_date) {
		$conn=parent::connect();
		$sql = "INSERT INTO " . TBL_TIMESHEET . " (
			timesheet_id,
			timesheet_approved,
			timesheet_submitted,
			timesheet_start_date,
			timesheet_end_date,
			person_id
			) VALUES (
			:timesheet_id,
			:timesheet_approved,
			:timesheet_submitted,
			:timesheet_start_date,
			:timesheet_end_date,
			:person_id
			)";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":timesheet_id", 'auto', PDO::PARAM_INT);
			$st->bindValue(":timesheet_approved", $this->data["timesheet_approved"], PDO::PARAM_INT);
			$st->bindValue(":timesheet_submitted", $this->data["timesheet_submitted"], PDO::PARAM_INT);
			$st->bindValue(":timesheet_start_date", date('y-m-d', strtotime($timesheet_start_date)), PDO::PARAM_STR);
			$st->bindValue(":timesheet_end_date", date('y-m-d', strtotime($timesheet_end_date)), PDO::PARAM_STR);
			//error_log("here is the END DATE: ") . date('y-m-d', strtotime($this->data["timesheet_end_date"]));
			//error_log("here is the START DATE: ") . date('y-m-d', strtotime($this->data["timesheet_start_date"]));
			$st->bindValue(":person_id", $person_id, PDO::PARAM_INT);
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
		$sql = "SELECT timesheet_id FROM " . TBL_TIMESHEET . " WHERE timesheet_start_date = :timesheet_start_date and timesheet_end_date = :timesheet_end_date and person_id = :person_id";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":timesheet_start_date", date('y-m-d', strtotime($this->data["timesheet_start_date"])), PDO::PARAM_STR);
			$st->bindValue(":timesheet_end_date", date('y-m-d', strtotime($this->data["timesheet_end_date"])), PDO::PARAM_STR);
			$st->bindValue(":person_id", $this->data["person_id"], PDO::PARAM_INT);
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
	public function updateTimesheet($timesheet_start_date, $timesheet_end_date) {
		$conn=parent::connect();
		$sql = "UPDATE " . TBL_TIMESHEET . " SET
			person_id = :person_id,
			timesheet_approved = :timesheet_approved,
			timesheet_submitted = :timesheet_submitted,
			timesheet_start_date = :timesheet_start_date,
			timesheet_end_date = :timesheet_end_date
			WHERE timesheet_start_date = :timesheet_start_date and timesheet_end_date = :timesheet_end_date";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":person_id", $this->data["person_id"], PDO::PARAM_STR);
			$st->bindValue(":timesheet_approved", $this->data["timesheet_approved"], PDO::PARAM_STR);
			$st->bindValue(":timesheet_submitted", $this->data["timesheet_submitted"], PDO::PARAM_INT);
			$st->bindValue(":timesheet_start_date", date('y-m-d', strtotime($this->data["timesheet_start_date"])), PDO::PARAM_STR);
			$st->bindValue(":timesheet_end_date", date('y-m-d', strtotime($this->data["timesheet_end_date"])), PDO::PARAM_STR);
			$st->execute();
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on update of timesheet, sql is $sql " . $e->getMessage());
		}

	}
	
	//submit the timesheet.
	public function submitTimesheet($timesheet_id) {
		$conn=parent::connect();
		$sql = "UPDATE " . TBL_TIMESHEET . " SET
			timesheet_submitted = 1
			WHERE timesheet_id = :timesheet_id";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":timesheet_id", $timesheet_id, PDO::PARAM_STR);
			$st->execute();
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on update of timesheet, sql is $sql " . $e->getMessage());
		}

	}

}

?>