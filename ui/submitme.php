<?php 
require_once("../common/common.inc.php");
require_once("../classes/Person.class.php");
require_once("../classes/Project_Person.class.php");
require_once("../classes/Timesheet.class.php");
require_once("../classes/Timesheet_Item.class.php");

echo "HERE IS SOME CODE IN A TABLE.";

if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] != "") {
		$person = Person::getByEmailAddress($_SESSION["logged_in"]);
	} else {
		error_log("Something is wrong here...this person is not logged in and you shouldn't be seeing this, timesheet.php.");
		exit();
	}
	
	
	//we will have all of this already in timsheets, but we need it for this test.
	
	$person->getPersonId($person->getValueEncoded("person_email"));
	//print_r($person->getValueEncoded("person_id"));
	
	//just running this on one timesheet, since this is all I have right now.
	list($timesheets) = Timesheet::getTimesheetIds($person->getValueEncoded("person_id"));
	
	if (isset($_GET["action"])  && $_GET["action"] == "submit_timesheet") {
		$timesheet = $timesheets->submitTimeSheet($_GET["timesheet_id"]);
	} 
	
?>
<form method="get" action="submitme.php">
 <input type="hidden" name="action" value="submit_timesheet"/>
<TABLE border=1px solid;>
<field type="hidden" value="<?php echo $_GET["timesheet_id"]?>">
<TR><TD>January 10</TD></TR>
<TR><TD><input type="text" name="timesheet_id" value="<?php echo $timesheets->getValue("timesheet_id");?>"></TD></TD></TR>
</TABLE>
<button type="submit">Click to Submit the Timesheet.</button>

2. When the user submits a timesheet, get out the variable for who assigned it.
3. Update the timesheet "timesheet_submitted" variable to 1.
4. When the pm that assgned it looks in their page they should see it. Likewise, people should only see unsubmitted timesheets.

</form>
</body>
</html>