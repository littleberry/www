<?php


require_once("DataObject.class.php");

class Timesheet_Item extends DataObject {
	protected $data = array(
		//these fields are in the timesheet table.
		"timesheet_item_id" =>"",
		"person_id"=>"",
		"task_id"=>"",
		"project_id"=>"",
		"timesheet_date"=>"",
		"timesheet_hours"=>"",
		"timesheet_notes"=>""
	);
	
		//get all of the submitted timesheets for a specific person (this just does the assignee right now, but it should include all people that are administrators as well).
        //maybe re-write this as a join. This is going to be slow and is confusing.
        //this returns all submitted timesheets for this manager, which is what we want.
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
	
	//display all information about a timesheet returned as an array.
	public function getTimesheetItems($timesheet_item_id) {
		$conn=parent::connect();
		$sql="SELECT * FROM " . TBL_TIMESHEET_ITEM . " WHERE timesheet_item_id = :timesheet_item_id";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":timesheet_item_id", $timesheet_item_id, PDO::PARAM_INT);
			$st->execute();
			$timesheet_item=array();
			foreach ($st->fetchAll() as $row) {
				$timesheet_item[] = new Timesheet_Item($row);
			}
			parent::disconnect($conn);
			if (count($timesheet_item) > 0) {
				return array($timesheet_item);
			} else {
				return 0;
			}
		}catch(PDOException $e) {
			parent::disconnect($conn);
			die("query failed here getting the details for the timesheet: " . $e->getMessage() . "query is " . $sql);
		}
	}
		//display all information about a timesheet returned as an array.
		//let's remove this if we can, it is a confusing name.
	public function getTimesheetItemsForDate($timesheet_item_id, $timesheet_date) {
		$conn=parent::connect();
		$sql="SELECT * FROM " . TBL_TIMESHEET_ITEM . " WHERE timesheet_item_id = :timesheet_item_id and timesheet_date = :timesheet_date";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":timesheet_item_id", $timesheet_item_id, PDO::PARAM_INT);
			$st->bindValue(":timesheet_date", date('y-m-d', strtotime($timesheet_date)), PDO::PARAM_STR);
			$st->execute();
			$timesheet_item=array();
			foreach ($st->fetchAll() as $row) {
				$timesheet_item[] = new Timesheet_Item($row);
			}
			parent::disconnect($conn);
			if (count($timesheet_item) > 0) return array($timesheet_item);
		}catch(PDOException $e) {
			parent::disconnect($conn);
			die("query failed here getting the details for the timesheet: " . $e->getMessage() . "query is " . $sql);
		}
	}
	
	
	//display all information about a timesheet for a specific date returned as an array.
	public function getTimesheetItemForPersonProjectTask($timesheet_date, $person_id, $project_id, $task_id) {
		$conn=parent::connect();
		$sql="SELECT * FROM " . TBL_TIMESHEET_ITEM . " WHERE timesheet_date = :timesheet_date and person_id = :person_id and project_id = :project_id and task_id = :task_id";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":person_id", $person_id, PDO::PARAM_INT);
			$st->bindValue(":project_id", $project_id, PDO::PARAM_INT);
			$st->bindValue(":task_id", $task_id, PDO::PARAM_INT);
			$st->bindValue(":timesheet_date", date('y-m-d', strtotime($timesheet_date)), PDO::PARAM_STR);
			$st->execute();
			$timesheet_item=array();
			foreach ($st->fetchAll() as $row) {
				$timesheet_item[] = new Timesheet_Item($row);
			}
			parent::disconnect($conn);
			if (count($timesheet_item) > 0) {
				return $timesheet_item;
			} else {
				return 0;
			}
		}catch(PDOException $e) {
			parent::disconnect($conn);
			die("query failed here getting the details for the timesheet: " . $e->getMessage() . "query is " . $sql);
		}
	}
		
	//function inserts new timesheet item into db. 	
	public function insertTimesheetItem($person_id, $timesheet_item_id) {
		$conn=parent::connect();
		$sql = "INSERT INTO " . TBL_TIMESHEET_ITEM . " (
			timesheet_item_id,
			person_id,
			task_id,
			project_id,
			timesheet_date,
			timesheet_hours,
			timesheet_notes
			) VALUES (
			:timesheet_item_id,
			:person_id,
			:task_id,
			:project_id,
			:timesheet_date,
			:timesheet_hours,
			:timesheet_notes
			)";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":timesheet_item_id", $timesheet_item_id, PDO::PARAM_INT);
			$st->bindValue(":person_id", $person_id, PDO::PARAM_INT);
			$st->bindValue(":timesheet_date", date('y/m/d', strtotime($this->data["timesheet_date"])), PDO::PARAM_INT);
			$st->bindValue(":task_id", $this->data["task_id"], PDO::PARAM_INT);
			$st->bindValue(":project_id", $this->data["project_id"], PDO::PARAM_INT);
			$st->bindValue(":timesheet_hours", $this->data["timesheet_hours"], PDO::PARAM_INT);
			$st->bindValue(":timesheet_notes", $this->data["timesheet_notes"], PDO::PARAM_INT);
			$st->execute();
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on insert of timesheet item, sql is $sql " . $e->getMessage());
		}	
	}
	
	//this function should not need an argument and is probably throwing an error.
	public function updateTimesheetItem($timesheet_item_id) {
		$conn=parent::connect();
		$sql = "UPDATE " . TBL_TIMESHEET_ITEM . " SET
			task_id = :task_id,
			person_id = :person_id,
			timesheet_hours = :timesheet_hours,
			timesheet_date = :timesheet_date,
			project_id = :project_id
			WHERE project_id = :project_id and person_id = :person_id and task_id = :task_id and timesheet_date = :timesheet_date";
		try {
			$st = $conn->prepare($sql);
			//$st->bindValue(":timesheet_item_id", $timesheet_item_id, PDO::PARAM_INT);
			$st->bindValue(":timesheet_date", date('y/m/d', strtotime($this->data["timesheet_date"])), PDO::PARAM_INT);
			$st->bindValue(":task_id", $this->data["task_id"], PDO::PARAM_INT);
			$st->bindValue(":person_id", $this->data["person_id"], PDO::PARAM_INT);
			$st->bindValue(":timesheet_hours", $this->data["timesheet_hours"], PDO::PARAM_INT);
			$st->bindValue(":project_id", $this->data["project_id"], PDO::PARAM_INT);
			$st->execute();	
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on update of timesheet item: " . $e->getMessage());
		}
	}
	
	//display all information about a timesheet for a person, including the details and whether it is submitted, returned as an array of timesheet_item objects. 
	public function getSubmittedTimesheetDetail($timesheet_id) {
		$conn=parent::connect();
		//$sql="SELECT * FROM " . TBL_TIMESHEET . " WHERE timesheet_id = :timesheet_id";
		//We're going to need the aggregate at some point so I'll stick it here.
		$sql = "SELECT td.*, ts.* FROM " . TBL_TIMESHEET_ITEM . " as td JOIN " . TBL_TIMESHEET . " as ts ON ts.timesheet_id = td.timesheet_item_id WHERE ts.timesheet_id = :timesheet_id";
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
	
	public function deleteTimesheetItem($person_id, $project_id, $task_id, $timesheet_date) {
		$conn=parent::connect();
		$sql = "DELETE FROM " . TBL_TIMESHEET_ITEM . " WHERE project_id = :project_id and person_id = :person_id and task_id = :task_id and timesheet_date = :timesheet_date";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":project_id", $person_id, PDO::PARAM_INT);
			$st->bindValue(":timesheet_date", $project_id, PDO::PARAM_INT);
			$st->bindValue(":task_id", $task_id, PDO::PARAM_INT);
			$st->bindValue(":person_id", $timesheet_date, PDO::PARAM_INT);
			$st->execute();	
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on update of timesheet item: " . $e->getMessage());
		}
	}


}
?>