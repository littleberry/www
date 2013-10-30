<?php


require_once("DataObject.class.php");

class Person_Permissions extends DataObject {
	protected $data = array(
		"person_perm_id"=>"",
		"create_projects"=>"",
		"view_rates"=>"",
		"create_invoices"=>"",
		"person_id"=>""
	);
	
	//display all contact data for the client
	public static function getPermissions($person_id) {
		$conn=parent::connect();
		$sql="SELECT * FROM " . TBL_PERSON_PERMISSIONS . " where person_id = :person_id";
		
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":person_id", $person_id, PDO::PARAM_INT);
			$st->execute();
			$person_perms=array();
			foreach ($st->fetchAll() as $row) {
				$person_perms[] = new Person_Permissions($row);
			}
			$row=$st->fetch();
			parent::disconnect($conn);
			return $person_perms;
		}catch(PDOException $e) {
			parent::disconnect($conn);
			die("query failed here: " . $e->getMessage() . "query is " . $sql);
		}
	}
	
	//just trying to get the person permissions as an object, not as an array.
	public static function getPermissionsAsObject($person_id) {
		$conn=parent::connect();
		$sql="SELECT * FROM " . TBL_PERSON_PERMISSIONS . " where person_id = :person_id";
		
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":person_id", $person_id, PDO::PARAM_INT);
			$st->execute();
			$row=$st->fetch();
			parent::disconnect($conn);
			if ($row) return new Person_Permissions($row);
		}catch(PDOException $e) {
			parent::disconnect($conn);
			die("query failed getting the person permissions object: " . $e->getMessage() . "query is " . $sql);
		}
	}
	
	
	public function insertPermissions() {
		$conn=parent::connect();
		$sql = "INSERT INTO " . TBL_PERSON_PERMISSIONS . " (
			person_perm_id,
			create_projects,
			view_rates,
			create_invoices,
			person_id
			) VALUES (
			:person_perm_id,
			:create_projects,
			:view_rates,
			:create_invoices,
			:person_id
			)";
		
		try {
			$st = $conn->prepare($sql);
			$st->bindValue(":person_perm_id", $this->data["person_perm_id"], PDO::PARAM_INT);
			$st->bindValue(":create_projects", $this->data["create_projects"], PDO::PARAM_INT);
			$st->bindValue(":view_rates", $this->data["view_rates"], PDO::PARAM_INT);
			$st->bindValue(":create_invoices", $this->data["create_invoices"], PDO::PARAM_INT);
			$st->bindValue(":person_id", $this->data["person_id"], PDO::PARAM_INT);
			$st->execute();
			parent::disconnect($conn);
		} catch (PDOException $e) {
			parent::disconnect($conn);
			die("Query failed on insert of person permissions, sql is $sql " . $e->getMessage());
		}		
	}
	
	public function updatePermissions() {
		$conn=parent::connect();
		$sql = "UPDATE " . TBL_PERSON_PERMISSIONS . " SET
				person_perm_id = :person_perm_id,
				create_projects = :create_projects,
				view_rates = :view_rates,
				create_invoices = :create_invoices
				WHERE person_id = :person_id";
			try {
				$st = $conn->prepare($sql);
				$st->bindValue(":person_perm_id", $this->data["person_perm_id"], PDO::PARAM_INT);
				$st->bindValue(":create_projects", $this->data["create_projects"], PDO::PARAM_INT);
				$st->bindValue(":view_rates", $this->data["view_rates"], PDO::PARAM_INT);
				$st->bindValue(":create_invoices", $this->data["create_invoices"], PDO::PARAM_INT);
				$st->bindValue(":person_id", $this->data["person_id"], PDO::PARAM_INT);
				$st->execute();	
				parent::disconnect($conn);
			} catch (PDOException $e) {
				parent::disconnect($conn);
				die("Query failed on update: " . $e->getMessage() . " sql is " . $sql);
			}
	}

}
?>