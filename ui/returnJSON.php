<?php
require_once("../common/common.inc.php");
require_once("../classes/Project.class.php");
require_once("../classes/Client.class.php");
require_once("../classes/Contact.class.php");
require_once("../classes/Person.class.php");
require_once("../classes/Project_Person.class.php");
require_once("../classes/Project_Task.class.php");
require_once("../classes/Task.class.php");


if (isset($_GET["func"])) {
	if ($_GET["func"] == "returnProjectJSON") {
		if (isset($_GET["id"])) {
			$id = $_GET["id"];
		} else {
			$id = "";
		}
		if (isset($_GET["collection"])) {
			$collection = $_GET["collection"];
		} else {
			$collection = "";
		}
		echo returnProjectJSON($id , $collection);
		
	} else if ($_GET["func"] == "returnClientJSON") {
		if (isset($_GET["id"])) {
			$id = $_GET["id"];
		} else {
			$id = "";
		}
		echo returnClientJSON($id);
		
	} else if  ($_GET["func"] == "returnClientNameJSON") {
		if (isset($_GET["id"])) {
			$id = $_GET["id"];
		}
		echo returnClientNameJSON($id);
		
	} else if ($_GET["func"] == "returnContactsJSON") {
		if (isset($_GET["id"])) {
			$id = $_GET["id"];
		} else {
			$id = "";
		}
		echo returnContactsJSON($id);
		
	} else if ($_GET["func"] == "returnPeopleJSON") {
		if (isset($_GET["id"])) {
			$id = $_GET["id"];
		} else {
			$id = "";
		}
		if (isset($_GET["collection"])) {
			$collection = $_GET["collection"];
		} else {
			$collection = "";
		}
		echo returnPeopleJSON($id, $collection);
		
	} else if ($_GET["func"] == "returnTasksJSON") {
		if (isset($_GET["id"])) {
			$id = $_GET["id"];
		} else {
			$id = "";
		}
		if (isset($_GET["collection"])) {
			$collection = $_GET["collection"];
		} else {
			$collection = "";
		}
		echo returnTasksJSON($id, $collection);
		
	}

}

function returnProjectJSON($id, $collection) {
	//returns the Project object as JSON encoded data for jQuery to use
	if ($collection == "client") {
		if ($id != "") {
			$projects = Project::getProjectByClientId($id);
		} else {
			$projects = Project::getClientsProjectsByStatus(0);
		}
		//$projects = $projects[0];
	} else if ($collection == "person") {
		$projects = Project_Person::getProjectsForPerson($id);
		$projects = $projects[0];
	} else if ($collection == "project") {
		if ($id != "") {
			$projects = array();
			$projects[] = Project::getProjectByProjectId($id);
		} else {
			$projects = Project::getProjects();
			$projects = $projects[0];
		}
	}
	
	$projJSON = array();
	//print_r($projects);
	
	foreach ($projects as $project) {
		$projJSON[] = array(
			"project_id" => $project->getValue("project_id"),
			"project_name" => $project->getValue("project_name"),
			"project_code" => $project->getValue("project_code"),
			"client_id" => $project->getValue("client_id"),
			"project_billable" => $project->getValue("project_billable"),
			"project_invoice_by" => $project->getValue("project_invoice_by"),
			"project_hourly_rate" => $project->getValue("project_hourly_rate"),
			"project_budget_by" => $project->getValue("project_budget_by"),
			"project_budget_total_fees" => $project->getValue("project_budget_total_fees"),
			"project_budget_total_hours" => $project->getValue("project_budget_total_hours"),
			"project_send_email_percentage" => $project->getValue("project_send_email_percentage"),
			"project_show_budget" => $project->getValue("project_show_budget"),
			"project_budget_includes_expenses" => $project->getValue("project_budget_includes_expenses"),
			"project_notes" => $project->getValue("project_notes"),
			"project_archived" => $project->getValue("project_archived")
		);
	}
	
	return json_encode($projJSON);
}

function returnClientJSON($id) {
	//Returns the Client object as JSON encoded data for jQuery to use
	if ($id != "") {
		$clients = array();
		$clients[] = Client::getClient($id);
	} else {
		$clients = Client::getClients();
		$clients = $clients[0];
	}
	//print_r($clients);
	
	$clientJSON = array();
	foreach ($clients as $client) {
		$clientJSON[] = array(
			"client_id" => $client->getValue("client_id"),
			"client_name" => $client->getValue("client_name"),
			"client_currency_index" => $client->getValue("client_currency_index"),
			"client_logo_link" => $client->getValue("client_logo_link"),
			"client_email" => $client->getValue("client_email"),
			"client_phone" => $client->getValue("client_phone"),
			"client_address" => $client->getValue("client_address"),
			"client_city" => $client->getValue("client_city"),
			"client_state" => $client->getValue("client_state"),
			"client_zip" => $client->getValue("client_zip"),
			"client_fax" => $client->getValue("client_fax"),
			"client_archived" => $client->getValue("client_archived")
		);
	}
	return json_encode($clientJSON);	
}

function returnClientNameJSON($id) {
	//Returns just the client's name as JSON encoded object for jQuery to use
	$clientName = Client::getClientNameById($id);
	$clientJSON = array(
		"client_name" => $client->getValue("client_name")
	);
	return json_encode($clientJSON);
}

function returnContactsJSON($id) {
	//Returns list of contacts for a given client as JSON encode object for jQuery to use
	$contacts = Contact::getContacts($id);
	//print_r($contacts);
	$contactJSON = array();
	foreach ($contacts as $contact) {
		$contactJSON[] = array(
			"contact_id" => $contact->getValue("contact_id"),
			"contact_name" => $contact->getValue("contact_name"),
			"contact_email" => $contact->getValue("contact_email"),
			"contact_office_number" => $contact->getValue("contact_office_number"),
			"contact_mobile_number" => $contact->getValue("contact_mobile_number"),
			"contact_fax_number" => $contact->getValue("contact_fax_number"),
			"client_id" => $contact->getValue("client_id"),
			"contact_primary" => $contact->getValue("contact_primary")
		);
	}
	return json_encode($contactJSON);
}

function returnPeopleJSON($id, $collection) {
	//Returns Person object as JSON encoded object for jQuery to use
	if ($collection == "project") {
		if ($id != "") {
			$people = Project_Person::getPeopleForProject($id);
		} else {
			$people = Person::getPeople();
		}
		$people = $people[0];
	} else if (($collection == "person") && ($id != "")) {
		$people = array();
		$people[] = Person::getPersonById($id);
	} else {
		$people = Person::getPeople();
	}
	
	//print_r($people);
	$peopleJSON = array();
	foreach ($people as $person) {
		$peopleJSON[] = array(
			"person_id" => $person->getValue("person_id"),
			"person_username" => $person->getValue("person_username"),
			"person_name" => $person->getValue("person_name"),
			"person_first_name" => $person->getValue("person_first_name"),
			"person_last_name" => $person->getValue("person_last_name"),
			"person_email" => $person->getValue("person_email"),
			"person_department" => $person->getValue("person_department"),
			"person_hourly_rate" => $person->getValue("person_hourly_rate"),
			"person_perm_id" => $person->getValue("person_perm_id"),
			"person_type" => $person->getValue("person_type"),
			"person_logo_link" => $person->getValue("person_logo_link")
		);
	}
	return json_encode($peopleJSON);
}

function returnTasksJSON($id, $collection) {
	//Returns Task object as JSON encoded object for jQuery to use
	if ($collection == "project") {
		if ($id != "") {
			$tasks = Project_Task::getTasksForProject($id);
		} else {
			$tasks = Task::getTasks(0);
		}
		$tasks = $tasks[0];

	} else if ($collection == "common") {
		$tasks = Task::getCommonTasks();
		$tasks = $tasks[0];
		
	} else if ($collection == "person") {
		if ($id != "") {
			//$tasks = Project_Person::getPeopleForProject($id);
		} else {
			//$tasks = Person::getPeople();
		}
	
	} else if (($collection == "task") && ($id != "")) {
		$tasks = array();
		$tasks[] = Task::getTask($id);
		//print_r(Task::getTask($id));
	} else {
		$tasks = Task::getTasks(0);
		$tasks = $tasks[0];
	}
	
	//print_r($tasks);
	
	$taskJSON = array();
	foreach ($tasks as $task) {
		$taskJSON[] = array(
			"task_id" => $task->getValue("task_id"),
			"task_name" => $task->getValue("task_name"),
			"task_hourly_rate" => $task->getValue("task_hourly_rate"),
			"task_bill_by_default" => $task->getValue("task_bill_by_default"),
			"task_common" => $task->getValue("task_common")
		);
	}
	return json_encode($taskJSON);
}



?>