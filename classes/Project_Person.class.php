<?php


//connectivity
require_once("DataObject.class.php");

class Project_Person extends DataObject {
	protected $data = array(
		//these fields are in the project_person table.
		"project_id"=>"",
		"person_id"=>"",
		"total_budget_hours"=>"",
		"project_assigned_by"=>
	);
	
	//function returns all of the people associated with a given project (archived and not).
	public static function getPeopleForProject($project_id) {
		$conn=parent::connect();
		$sql = "SELECT * FROM " . TBL_PERSON . " WHERE person_id IN (SELECT person_id FROM " . TBL_PROJECT_PERSON . " WHERE project_id = :project_id)";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":project_id", $project_id, PDO::PARAM_INT);
			$st->execute();
			$people=array();
			foreach ($st->fetchAll() as $row) {
				$people[] = new Person($row);
			}
			$row=$st->fetch();
			parent::disconnect($conn);
			return array($people);
		}catch(PDOException $e) {
			parent::disconnect($conn);
			die("query failed returning the people for the project: " . $e->getMessage() . "query is " . $sql);
		}
	}
	
	//function returns all of the projects associated with a given person (archived and not)
	public static function getProjectsForPerson($person_id) {
		$conn=parent::connect();
		$sql = "SELECT * FROM " . TBL_PROJECT . " WHERE project_id IN (SELECT project_id FROM " . TBL_PROJECT_PERSON . " WHERE person_id = :person_id)";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":person_id", $person_id, PDO::PARAM_INT);
			$st->execute();
			//initialize this here, person may have NO projects.
			$project=array();
			foreach ($st->fetchAll() as $row) {
				$project[] = new Project($row);
			}
			$row=$st->fetch();
			parent::disconnect($conn);
			return array($project);
		}catch(PDOException $e) {
			parent::disconnect($conn);
			die("query failed returning the project for the person: " . $e->getMessage() . "query is " . $sql);
		}
	}
	
	//delete the rows in the table for a person with a given project.
	public function deleteProjectPerson($project_id) {
		$conn=parent::connect();
		$sql = "DELETE FROM " . TBL_PROJECT_PERSON . " WHERE project_id = :project_id";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":project_id", $project_id, PDO::PARAM_INT);
			$st->execute();
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on delete of project_person, sql is $sql " . $e->getMessage());
		}	
	}
	
	public function deletePersonProject($person_id) {
		$conn=parent::connect();
		$sql = "DELETE FROM " . TBL_PROJECT_PERSON . " WHERE person_id = :person_id";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":person_id", $person_id, PDO::PARAM_INT);
			$st->execute();
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on delete of project_person, sql is $sql " . $e->getMessage());
		}	
	}

	
	
	public function insertProjectPerson($person_id, $project_id, $project_assigned_by) {
		$conn=parent::connect();
		$sql = "INSERT INTO " . TBL_PROJECT_PERSON . " (
			project_id,
			person_id,
			total_budget_hours,
			project_assigned_by
			) VALUES (
			:project_id,
			:person_id,
			:total_budget_hours,
			:project_assigned_by
			)";
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":project_id", $project_id, PDO::PARAM_INT);
			$st->bindValue(":person_id", $person_id, PDO::PARAM_INT);
			$st->bindValue(":total_budget_hours", $this->data["total_budget_hours"], PDO::PARAM_INT);
			$st->bindValue(":project_assigned_by", $project_assigned_by, PDO::PARAM_STR);
			$st->execute();
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on insert of project_person, sql is $sql " . $e->getMessage());
		}	
	}
	
	public function updateProjectPerson($person_id, $project_id) {
		$conn=parent::connect();
		$sql = "UPDATE " . TBL_PROJECT_PERSON . " SET
				project_id = :project_id,
				person_id = :person_id,
				total_budget_hours = :total_budget_hours
				WHERE project_id = :project_id";
			try {
				$st = $conn->prepare($sql);
				$st->bindValue(":project_id", $this->data["project_id"], PDO::PARAM_STR);
				$st->bindValue(":person_id", $this->data["person_id"], PDO::PARAM_STR);
				$st->bindValue(":total_budget_hours", $this->data["total_budget_hours"], PDO::PARAM_INT);
				$st->execute();	
				parent::disconnect($conn);
			} catch (PDOException $e) {
				parent::disconnect($conn);
				die("Query failed on project update: " . $e->getMessage() . " sql is " . $sql);
			}
	}	
}
	
?>