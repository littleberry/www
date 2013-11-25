<?php 
require_once("../common/common.inc.php");
require_once("../classes/Person.class.php");
require_once("../classes/Project_Person.class.php");
require_once("../classes/Timesheet.class.php");
require_once("../classes/Timesheet_Item.class.php");
require_once("../classes/Project.class.php");
require_once("../classes/Client.class.php");
require_once("../classes/Task.class.php");




checkLogin();



if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] != "") {
	$person = Person::getByEmailAddress($_SESSION["logged_in"]);
} else {
	error_log("Something is wrong here...this person is not logged in and you shouldn't be seeing this, timesheet.php.");
	exit();
}
	
if (isset($_POST["action"]) and $_POST["action"] == "Send Reminders") {
	sendReminders();
} else {
	include('header.php'); //add header.php to page moved to only be called when page is rendered so it's not sent back when page saved via JS/Ajax
	displayUnsubmittedTimesheets(new Timesheet(array()), new Timesheet_Item(array()));
}
	
function displayUnsubmittedTimesheets($timesheet, $timesheet_item) {
	list($timesheets) = $timesheet_item->getSubmittedTimesheetsByManager($_SESSION["logged_in"], 1, 1);
	?>
	<form method="post" action="timesheet_unsubmitted.php">
	<h1>Archived Timesheets</h1>
	<table border="0px solid">			
	<input type="hidden" name="action" value="Send Reminders">
	<?php
	foreach($timesheets as $timesheet) {
		//print_r($timesheet);
		$person = Person::getPersonById($timesheet->getValue("person_id"));
		echo "<tr><td>" . $person->getValue("person_first_name") . " " . $person->getValue("person_last_name") . "</td></tr><br>";
	}
	?>		
	<tr><td><input type="submit" name="send_reminder" value="Send Reminders"></td></tr>
	</table>
	</form>
	</html>
<?php 
}


function sendReminders () {
	echo "send a reminder.";	
}
?>
COMING SOON
