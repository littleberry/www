<?php 
require_once("../common/common.inc.php");
require_once("../classes/Person.class.php");
require_once("../classes/Project_Person.class.php");
require_once("../classes/Timesheet.class.php");
require_once("../classes/Timesheet_Item.class.php");

checkLogin();

if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] != "") {
		$person = Person::getByEmailAddress($_SESSION["logged_in"]);
	} else {
		error_log("Something is wrong here...this person is not logged in and you shouldn't be seeing this, timesheet.php.");
		exit();
	}
	
	
	//we will have all of this already in timsheets, but we need it for this test.
	
	
	list($timesheets) = Timesheet::getSubmittedTimesheetsByManager($_SESSION["logged_in"]);
	foreach($timesheets as $timesheet) {
		echo "You need to approve timesheet " . $timesheet->getValueEncoded("timesheet_id");
		list($timesheet_and_items)=$timesheet->getSubmittedTimesheetDetail($timesheet->getValueEncoded("timesheet_id"), 1);
	}
?>
<html>
<table>
<tr><td><?php echo $timesheet->getValueEncoded("timesheet_id"); ?></td></tr>
<?php foreach ($timesheet_and_items as $timesheet_items) {
	?><tr><td><?php print_r($timesheet_items) ?></td></tr>
<?php } ?>
</table>
</html>