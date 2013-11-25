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
	
if (isset($_POST["action"]) and $_POST["action"] == "Send Reminders") {
	sendReminders();
} else {
	include('header.php'); //add header.php to page moved to only be called when page is rendered so it's not sent back when page saved via JS/Ajax
	$msg = "";
	displayUnsubmittedTimesheets($msg);
}
	
function displayUnsubmittedTimesheets($msg) {
	list($timesheets) = Timesheet_Item::getSubmittedTimesheetsByManager($_SESSION["logged_in"], 0, 0);
	?>
	<form method="post" action="timesheet_unsubmitted.php">
	<h4><?php if ($msg) {
	include('header.php');
	foreach ($msg as $key=>$value) {
		echo $value;
		echo "<br>";
	}
	}?></h4>
	<h1>Unsubmitted Timesheets</h1>
	<table border="0px solid">			
	<input type="hidden" name="action" value="Send Reminders">
	<?php
	foreach($timesheets as $timesheet) {
		//print_r($timesheet);
		$person = Person::getPersonById($timesheet->getValue("person_id"));
		?> <tr><td><input type="checkbox" name="person_id[<?php echo $person->getValue("person_id");?>]"><?php echo $person->getValue("person_first_name") . " " . $person->getValue("person_last_name");?></td></tr><br><?php
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

//1. Set this up so the names use checkboxes to determine who gets a reminder.
//2. Send a reminder based on the email.
//3. Show timesheet if the user selects their name.
?>