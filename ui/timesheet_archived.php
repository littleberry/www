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
	
if (isset($_GET["timesheet_id"])) {
	displayTimesheet();
} else {
	//include('header.php'); //add header.php to page moved to only be called when page is rendered so it's not sent back when page saved via JS/Ajax
	$msg = "";
	displayArchivedTimesheets();
}
	
function displayArchivedTimesheets() {
	//print_r($_POST);
	//can we put this here? I need it to come up when the page is opened, and not always because the user submitted the form.
	include('header.php');

	//get all the submitted timesheets for this person
	//get all the timesheets for the logged in person OR if the person is an administrator, get everything.
	$person_type = Person::getByEmailAddress($_SESSION["logged_in"]);
	if ($person_type->getValueEncoded("person_perm_id") == 'Administrator') {
		list($timesheets) = Timesheet_Item::getSubmittedTimesheets(1, 1);
	} else {
		list($timesheets) = Timesheet_Item::getSubmittedTimesheetsByManager($_SESSION["logged_in"], 1, 1);
	}
	?>
	<form method="post" action="timesheet_archived.php">
	<h1>Archived Timesheets</h1>
	<?php list($people) = Project_Person::getAssignedProjects();?>
	<select name="people_timesheets" id="people_timesheets" size="1" onchange="this.form.submit()">
	<option value="">---choose a person---</option>
	<?php 
		foreach ($people as $persons_with_timesheets) {
			?><option value="<?php echo $persons_with_timesheets->getValueEncoded("person_id")?>"><?php echo $persons_with_timesheets->getValueEncoded("person_first_name") . " " . $persons_with_timesheets->getValueEncoded("person_last_name")?></option>
		<?php } ?>
	</select>
	<table border="0px solid" style="width:100%;">			
	<?php
	//display the timesheets for the user that this person selected
	if (isset($_POST["people_timesheets"])) {
		echo "<tr><td>Displaying archived timesheets for " . $person = Person::getPersonById($_POST["people_timesheets"])->getValue("person_first_name") . " " . $person = Person::getPersonById($_POST["people_timesheets"])->getValue("person_last_name") . "</td></tr>";
		?><tr><td style="background-color:gray;">Time Period</td></tr><?php
		foreach($timesheets as $timesheet) {
			//print_r($timesheet);
			//echo($timesheet->getValueEncoded("person_id"));
			//echo($_POST["people_timesheets"]);
			if ($timesheet->getValueEncoded("person_id") == $_POST["people_timesheets"]) {
				//print_r($timesheet);
				//echo "<br>";
				?><tr><td><a href="timesheet_archived.php?timesheet_id=<?php echo $timesheet->getValueEncoded("timesheet_id")?>&detail=yes"><?php echo $timesheet->getValueEncoded("timesheet_start_date") . "-" . $timesheet->getValueEncoded("timesheet_end_date")?></a></td></tr><?php
			}
		}
	}
	?>		
	</table>
	</form>
	</html>
<?php 
}



function displayTimesheet () {
		include('header.php');
		echo "<h1>Your Timesheet will show up right here:</h1>";
		?><img src="images/timesheet.jpg"><?php

}
//1. Set this up so the names use checkboxes to determine who gets a reminder.
//2. Send a reminder based on the email.
//3. Show timesheet if the user selects their name.
?>