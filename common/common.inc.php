<?php

/*
function displayPageHeader($pageTitle) {
?>
<!doctype html public "-//w3c//dtd html 1.0 strict//en" "http://www.w3.org/TR/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php echo $pageTitle?></title>
<link rel="stylesheet" type="text/css" href="/common/common.css" />
<style type="text/css">
th {text-align:left; background-color:#bbb;}
th,td {padding:0.4em;}
tr.alt td {background: #ddd;}
.error {background:#d33; color:white; padding:0.2em;}
</style>
</head>
<body>
<h1><?php echo $pageTitle?></h1>
<?php
}


//display page footer information
function displayPageFooter() {
?>
</body>
</html>
<?php
}

*/

function displayPageHeader($pageTitle, $membersArea = false) {
}

ini_set ('display_errors', 0);
require_once("../classes/Person.class.php");


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
	//echo "CALLED ERT";
}

function setSelected(DataObject $obj, $fieldName, $fieldValue) {
	if ($obj->getValue($fieldName) == $fieldValue) {
		echo ' selected="selected"';
	}
}


//these functions are for the login part of the application. This function is called at the start of every page. (maybe it should go right here?)
function checkLogin() {
//if the session variable doesn't exist show the log in page.
	session_start();
	if (!$_SESSION["person"] or !$_SESSION["person"] = Person::getPerson($_SESSION["person"]->getValue( "person_username" ))) {
		$_SESSION["person"] = "";
		//error_log("no session!");
		//error_log(print_r($_SESSION["person"],true));
		header("Location: login.php");
		exit;
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