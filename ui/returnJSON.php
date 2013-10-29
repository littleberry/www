<?php
require_once("../common/common.inc.php");
require_once("../classes/Project.class.php");
require_once("../classes/Client.class.php");
require_once("../classes/Project_Person.class.php");
require_once("../classes/Project_Task.class.php");
require_once("../classes/Project.class.php");
require_once("../classes/Task.class.php");


if (isset($_GET["func"])) {
	if ($_GET["func"] == "returnProjectJSON") {
		$projId = $_GET["projid"];
		echo returnProjectJSON($projId);
	}
}

function returnProjectJSON($use_id) {
	//returns the Project object as JSON encoded data for jQuery to use
	$project = Project::getProjectByProjectId($use_id);
	//print_r($project);
	
	$projJSON = array(
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
		"project_send_email" => $project->getValue("project_send_email"),
		"project_show_budget" => $project->getValue("project_show_budget"),
		"project_budget_includes_expenses" => $project->getValue("project_budget_includes_expenses"),
		"project_notes" => $project->getValue("project_notes"),
		"project_archived" => $project->getValue("project_archived")
	);
	
	return json_encode($projJSON);
}

function returnClientJSON($use_id) {
	//Returns the Client object as JSON encoded data for jQuery to use
	$client = Client::getClient($use_id);
	//print_r($project);
	
	$clientJSON = array(
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
	
	return json_encode($clientJSON);	
}

function returnClientNameJSON($use_id) {
	//Returns just the client's name as JSON encoded object for jQuery to use
	$clientName = Client::getClientNameById($use_id);
	$clientJSON = array(
		"client_name" => $client->getValue("client_name")
	);
	return json_encode($clientJSON);
}

function returnContactsJSON($use_id) {
	//Returns list of contacts for a given client as JSON encode object for jQuery to use
	$contacts = Contact::getContacts($use_id);
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

function returnPeopleJSON($use_id) {
	//Returns Person object as JSON encoded object for jQuery to use
	$people = Person::getPeople();
	
	$peopleJSON = array();
	foreach ($people as $person) {
		$peopleJSON[] = array(
			"person_id" => $person->getValue("person_id"),
			"person_username" => $person->getValue("person_username"),
			"person_password" => $person->getValue("person_password"),
			"person_name" => $person->getValue("person_name"),
			"person_firstname" => $person->getValue("person_firstname"),
			"person_lastname" => $person->getValue("person_lastname"),
			"person_email" => $person->getValue("person_email"),
			"person_department" => $person->getValue("person_department"),
			"person_hourly_rate" => $person->getValue("person_hourly_rate"),
			"person_perm_id" => $person->getValue("person_perm_id"),
			"person_type" => $person->getValue("person_type"),
			"person_logo_link" => $person->getValue("person_logo_link")
		)
	}
	return json_encode($peopleJSON);
}

function returnProjectPeopleJSON($use_id) {
	//Returns list of people associated with a project, based on project id, as JSON encoded object for jQuery to use

}
function returnPersonProjectsJSON($use_id){
	//Returns list of projects associated with a person, based on person id, as JSON encoded object for jQuery to use
}

function returnTasksJSON($use_id) {
	
}



?>