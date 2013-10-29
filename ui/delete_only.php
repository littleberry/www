<?php

require_once("../classes/Client.class.php");
require_once("../classes/Contact.class.php");

	if (isset($_POST["client_id"])) {
		$client_id = ($_POST["client_id"]);
	}
	
	if (isset($_GET["client_id"])) {
		$client_id = ($_GET["client_id"]);
	}
	
	try {
		error_log("TRYING TO DELETE CLIENT " . $client_id);
		$return = Client::deleteClient($client_id);
		error_log("SOMETHING WENT WRONG!!!." . $return);
		echo $return;
	} catch(Exception $e) {
		error_log("SOMETHING WENT WRONG!!!." . $e);
		parent::disconnect($conn);
		die("Query failed on delete of client: " . $e->getMessage());
	}

?>