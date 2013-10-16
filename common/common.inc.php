<?php

//function displayPageHeader($pageTitle, $membersArea = false) {
//}

ini_set ('display_errors', 0);
//server configuration variables for each server.
$location_id = gethostname();
//echo($location_id);

$catPattern = 'cathlenes-MacBook-Pro.local';
$muppetPattern = 'FORA';
switch ($location_id) { 
        case $catPattern : 
            $_SERVER["SITE_BASE"] = "/Applications/MAMP/htdocs/";
            $_SERVER["DOCUMENT"] = "time_tracker/"; 
            break; 
        case $muppetPattern:
        	$_SERVER["SITE_BASE"] = "C:\\WAMP\\WWW\\";
            $_SERVER["DOCUMENT"] = "";
            break; 
    } 


//THIS FILE IS ALWAYS INCLUDED, SO GET THE CONFIG FOR THE AUTH.
require_once($_SERVER["DOCUMENT_ROOT"] . "/time_tracker/usercake/models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

require_once($_SERVER["DOCUMENT_ROOT"] . "/time_tracker/classes/Person.class.php");


//is the value in the missing field array? If so, highlight the field using the "error" style..
function validateField($fieldName, $missingFields) {
	if (in_array($fieldName, $missingFields)) {
		echo ' class="client-details-label required"';
	}
}

//these functions pre-select checkboxes and menus on the page.
function setChecked(DataObject $obj, $fieldName, $fieldValue) {
	if ($obj->getValue($fieldName) == $fieldValue) {
		echo ' checked=checked';
	}
}

function setSelected(DataObject $obj, $fieldName, $fieldValue) {
	if ($obj->getValue($fieldName) == $fieldValue) {
		echo ' selected="selected"';
	}
}


//these functions are for the login part of the application. This function is called at the start of every page. (maybe it should go right here?)
function checkLogin($page) {
//if the session variable doesn't exist show the log in page.
	session_start();
	if (!$_SESSION["person"] or !$_SESSION["person"] = Person::getPerson($_SESSION["person"]->getValue( "person_username" ))) {
		$_SESSION["person"] = "";
		$_SESSION["callLoginFromPage"] = $page;
		//error_log("no session!");
		//error_log(print_r($_SESSION["person"],true));
		header("Location: login.php");
	}//else{
		//not logging right now.
	//	echo "blerg";
		//$logEntry = new LogEntry(array (
		//"memberId" => $_SESSION["member"]->getValue("id"),
		//"pageUrl" => basename($_SERVER["PHP_SELF"])
		//));
		//$logEntry->record();
	//}
}

?>