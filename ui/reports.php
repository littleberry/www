<?php
	require_once("../common/common.inc.php");
	require_once("../functions/Report.php");
	
	//protect this page
	checklogin();
	
	//get the value off the url for timesheet_id
	//$processType = "A";
	//if (isset($_GET["timesheet_id"]) && $_GET["timesheet_id"] == "") {
	//	$processType = "A";
	//} elseif (isset($_GET["timesheet_id"])) {
	//	$processType = "E";
	//}
	
	//if (isset($_POST["func"])) {
	//	if ($_POST["func"] == "saveTimesheet") {
	//		//error_log(">>>>>>  save timesheet");
	//		if (isset($_POST["proc_type"])) {
	//			$processType = $_POST["proc_type"];
	//			echo saveTimesheet($processType);
	//		}
	//	}
	//} else {
		//if (isset($_POST["save_timesheet_button"]) and $_POST["save_timesheet_button"] == "Save Timesheet") {
		//	saveTimesheet($processType);
		//} else {
			include('header.php'); //add header.php to page moved to only be called when page is rendered so it's not sent back when page saved via JS/Ajax
			displayReport();
	//	}
	//}
	
	
	
function displayReport() {
	//error_log("HERE IS THE POST!!!!!!!!!!!!!!!!!!");
	//error_log(print_r($_POST, true));
	//first, get the date off of the URL. This is not set up yet, but will be required to get
	//the correct timesheet out of the database. Right now it just returns the current date.
	//THIS NEEDS SOME WORK!
	if (isset($_GET["timesheet_start_date"])) {
		$timesheet_start_date = $_GET["timesheet_start_date"];
	} else {
		$timesheet_start_date = '2013/11/25';
	}
	if (isset($_GET["timesheet_end_date"])) {
		$timesheet_end_date = $_GET["timesheet_end_date"];
	} else {
		$timesheet_end_date = '2013/12/1';
	}
	
	
	//echo $timesheet_start_date;
	//echo $timesheet_end_date;
	
	//list($timesheet_information) = Report::reportTimesheetsInfo($timesheet_start_date, $timesheet_end_date);
	
	
	//echo "here is timesheet aggregate when you first come in.";
	//print_r($timesheet_aggregate);
	//exit;
	if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] != "") {
		$person = Person::getByEmailAddress($_SESSION["logged_in"]);
	} else {
		error_log("Something is wrong here...this person is not logged in and you shouldn't be seeing this, timesheet.php.");
		exit();
	}
	
	
	/*	Timesheet::deleteTimesheet(356);
	Timesheet::deleteTimesheet(384);
	Timesheet::deleteTimesheet(385);
	Timesheet::deleteTimesheet(386);
	Timesheet::deleteTimesheet(390);
*/
	?>
	<div id="page-content" class="page-content">
		<header class="page-header">
			<h1>This week:</h1>
			<h1 class="page-title"><?php echo date("F j, Y");?></h1>
		</header>
	<table width="100%">
	<tr><td>
	<b>Hours Tracked</b><br>
	<?
	$array = Report::reportHourSum($timesheet_start_date, $timesheet_end_date);
	echo (!$array[0] ? 0 : $array[0]);
	?></td><td>Billable Hours<br>
	<?
	$array = Report::reportBillableHourSum($timesheet_start_date, $timesheet_end_date);
	echo (!$array[0] ? 0 : $array[0]);
	?>
	</td><td>Billable Amount</td><td>Uninvoiced Amount</td></tr>
	<tr><td>Clients</td><td>Projects</td><td>Tasks</td><td>Staff</td></tr>
	<tr><td>Name</td><td>Hours</td><td>Billable Hours</td><td>Billable Amount</td></tr>
	</table>
	</div>
	<footer id="site-footer" class="site-footer">
	
	</footer>
	
</body>
</html>		

<?php
}

?>
