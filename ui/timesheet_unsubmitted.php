<?php 
require_once("../common/common.inc.php");
require_once("../classes/Person.class.php");
require_once("../classes/Project_Person.class.php");
require_once("../classes/Timesheet.class.php");
require_once("../classes/Timesheet_Item.class.php");
require_once("../classes/Project.class.php");
require_once("../classes/Client.class.php");
require_once("../classes/Task.class.php");
require_once("../classes/class.phpmailer.php");



checkLogin();



if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] != "") {
	$person = Person::getByEmailAddress($_SESSION["logged_in"]);
} else {
	error_log("Something is wrong here...this person is not logged in and you shouldn't be seeing this, timesheet.php.");
	exit();
}
	
if (isset($_POST["send_reminder"]) and $_POST["send_reminder"] == "Send Reminders") {
	sendReminders();
} elseif (isset($_GET["person_id"])) {
	displayTimesheet();
} else {
	//include('header.php'); //add header.php to page moved to only be called when page is rendered so it's not sent back when page saved via JS/Ajax
	$msg = "";
	displayUnsubmittedTimesheets($msg);
}
	
function displayUnsubmittedTimesheets($msg) {
	//can we put this here? I need it to come up when the form is submitted, and not always because the user submitted the form.
	include('header.php');
	$person_type = Person::getByEmailAddress($_SESSION["logged_in"]);
	if ($person_type->getValueEncoded("person_perm_id") == 'Administrator') {
		list($timesheets) = Timesheet_Item::getSubmittedTimesheets(0, 0);
	} else {
		list($timesheets) = Timesheet_Item::getSubmittedTimesheetsByManager($_SESSION["logged_in"], 0, 0);
	}
	//print_r($_POST);
	?>
	<form method="post" action="timesheet_unsubmitted.php">
	<h4><?php if ($msg) {
	foreach ($msg as $key=>$value) {
		echo $value;
		echo "<br>";
	}
	}?></h4>
	<h1>Unsubmitted Timesheets</h1>
	<?php $timesheet_timeframes = Timesheet::getTimesheetDates();
	?><select name="timesheet_timeframe" id="timesheet_timeframe" size="1" if onchange="this.form.submit()">
		<option value="">---choose a timeframe---</option>
 <?php   
		foreach ($timesheet_timeframes as $timesheet_timeframe) {
		//select the one the user just put in
		$select_string = $timesheet_timeframe->getValueEncoded("timesheet_start_date") . ":" . $timesheet_timeframe->getValueEncoded("timesheet_end_date");
		$url_string = $_POST["timesheet_timeframe"];
		//echo "<<<<<<<<<<<<<<";
		//echo $select_string;
		//echo "<<<<<<<<<<<<<<<<<";
		//echo $url_string;
		if ($select_string = $url_string) {
			$selected = "selected";
		} else {
			$selected = "";
		}
			?><option value="<?php echo ($timesheet_timeframe->getValueEncoded("timesheet_start_date") . ":" . $timesheet_timeframe->getValueEncoded("timesheet_end_date"));?>"><?php echo ($timesheet_timeframe->getValueEncoded("timesheet_start_date") . " - " . $timesheet_timeframe->getValueEncoded("timesheet_end_date")); ?></option>
		<?php }
	?>
	</select>
	<table border="0px solid">			
	<input type="hidden" name="action" value="Send Reminders">
	<?php
	//only display those timesheets that are for the given timeframe, do this in code and not in the DB.
	if (isset($_POST["timesheet_timeframe"])) {
		$exploded_dates = explode(":", $_POST["timesheet_timeframe"]);
		$start_date = $exploded_dates[0];
		$end_date = $exploded_dates[1];
		echo "<br><br>Displaying timesheets for " . $start_date . " through " . $end_date . ".";
		echo "<br>"; 
		
		foreach($timesheets as $timesheet) {
			if ($timesheet->getValue("timesheet_start_date") == $start_date && $timesheet->getValue("timesheet_end_date") == $end_date) {
				$person = Person::getPersonById($timesheet->getValue("person_id"));
				?> <tr><td><input type="checkbox" name="person_id[<?php echo $person->getValue("person_id");?>]">   <a href="timesheet_unsubmitted.php?person_id=<?php 			echo $timesheet->getValue("person_id")?>""><?php echo $person->getValue("person_first_name") . " " . $person->getValue("person_last_name");?></a></td></tr><br><?php
			}
		}
	}
	
	?>		
	<tr><td><input type="submit" name="send_reminder" value="Send Reminders"></td></tr>
	</table>
	</form>
	</html>
<?php 
}


function sendReminders () {
	$people = $_POST["person_id"];
	foreach ($people as $key=>$value) {
		$person_email = Person::getPersonById($key);
		$person_email = $person_email->getValueEncoded("person_email");
		$msg[] = "Reminder Sent to " . $person_email; 
		//send email code
		$message = "You have unsubmitted timesheets.";
		$mail = new PHPMailer();
		$mail->IsSMTP();
		//$mail->Host = $emailHostname;
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = "tls";                 // sets the prefix to the server
		$mail->Host = "smtp.gmail.com";      // sets GMAIL as the SMTP server
		$mail->Port = 587;      
		$mail->Username = "catsbap";
		$mail->Password = "catot3844";
		$mail->From = "admin@timetracker.com";
		$mail->FromName = "admin@timetracker.com";
		$mail->AddAddress($person_email); 
		$mail->IsHTML(true);
		$mail->Subject = "Please remember to submit your time.";
		$mail->Body = $message;
		$mail->Send();

	}
	displayUnsubmittedTimesheets($msg);
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