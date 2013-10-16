<?php	
	require_once("../common/common.inc.php");
	require_once("../classes/Client.class.php");
	require_once("../classes/Contact.class.php");
	require_once("../common/errorMessages.php");
	require_once("../classes/Project.class.php");

			$client_id = $_POST["client_id"];
			$activeProjects = Project::hasActiveProjects($client_id);
			error_log(print_r($activeProjects, true));
			if ($activeProjects[0] > 0) {
			echo 1;
			} else {
			Client::deleteClient($client_id);
			echo 0;
			}
			
			?>