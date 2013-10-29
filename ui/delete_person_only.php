<?php

require_once("../classes/Person.class.php");

	if (isset($_POST["person_id"])) {
		$person_id = ($_POST["person_id"]);
	}
	
	if (isset($_GET["person_id"])) {
		$person_id = ($_GET["person_id"]);
	}
	
	try {
		error_log("TRYING TO DELETE PERSON " . $person_id);
		$return = Person::deletePerson($person_id);
		error_log("SOMETHING WENT WRONG DELETING THE PERSON!!" . $return);
		echo $return;
	} catch(Exception $e) {
		error_log("SOMETHING WENT WRONG and here is the excepton!!!" . $e);
		parent::disconnect($conn);
		die("Query failed on delete of person: " . $e->getMessage());
	}

?>