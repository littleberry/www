<?php
	require_once("../common/common.inc.php");
	require_once("../classes/Project.class.php");
	require_once("../classes/Project_Person.class.php");
	require_once("../classes/Project_Task.class.php");
	require_once("../classes/Task.class.php");
	require_once("../classes/Task.class.php");
	require_once("../classes/Timesheet.class.php");
	require_once("../classes/Timesheet_Detail.class.php");
	//require_once 'Calendar/Month/Weekdays.php';	
	//require_once 'Calendar/Week.php';
	//require_once 'Calendar/Year.php';
	//require_once 'Calendar/Minute.php';
	
	//protect this page
	checklogin();
	
	include('header.php'); //add header.php to page
	if (isset($_POST["save_timesheet_button"]) and $_POST["save_timesheet_button"] == "Save Timesheet") {
		error_log("THE USER WANTS TO SAVE THE TIMESHEET");
		saveTimesheet();
	} else {
		displayTimesheet(new Timesheet(array()), new Timesheet_Detail(array()));
	}
	


function displayTimesheet($timesheet, $timesheet_detail) {
if (isset($_GET["timesheet_date"])) {
	$timesheet_date = $_GET["timesheet_date"];
} else {
	$timesheet_date = date('d-m-Y');
}
$d = strtotime($timesheet_date);
?>

<div id="page-content" class="page-content">
	<header class="page-header">
		<h1 class="page-title"><?php echo date("M d Y");?></h1>
		<nav class="page-controls-nav">
			<ul class="page-controls-list project">
				<li class="page-controls-item link-btn">
				<a class="view-all-link" href="project-add.php">+ Add Timesheet</a></li>
				<li class="page-controls-item"><a class="view-archive-link" href="projects.php?archives=1">View Archives</a></li>
				<li class="page-controls-item"><a class="view-all-link" href="projects.php">View All</a></li>
			</ul>
		</nav>
	</header>
	<div class="content">
		<!--BEGIN FORM-->
		<form action="timesheet.php" method="post">
						<?php
						if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] != "") {
							$person=Person::getByEmailAddress($_SESSION["logged_in"]);
						} else {
							echo "Something is wrong here...this person is not logged in and you shouldn't be seeing this, timesheet.php.";
							exit();
						}
						?>
						<input type="hidden" name="person_id" value="<?php echo $person->getValueEncoded("person_id");?>">
						<input type="hidden" name="project_id" value="<?php 
	if (isset($_GET["project_id"])) {
		echo $_GET["project_id"];
	} else {
		echo $timesheet->getValue("project_id");
	}
	?>"><?php echo  Project::getProjectName($_GET["project_id"])[0] ?>	
	<input type="hidden" name="task_id" value="<?php 
	if (isset($_GET["task_id"]))	 {
		echo $_GET["task_id"];
	} else {
		echo $timesheet->getValue("task_id");
	}
	?>"><?php echo Task::getTaskName($_GET["task_id"])[0]?>
	<input type="hidden" name="timesheet_notes" value="<?php 
	if (isset($_GET["timesheet_notes"]))	 {
		echo $_GET["timesheet_notes"];
	} else {
		echo $timesheet->getValue("timesheet_notes");
	}
	?>">
						<img class="client-logo-img small" style="height:50px; width:50px;" src="<?php echo "images/" . $person->getValue("person_logo_link")?>" title="Person Logo" alt="Person logo" />
<table style="width:100%; border:1px solid;">
<?php
//if ($timesheet_detail) echo "this person already has a timesheet.";
//print_r($timesheet_detail);

//list($timesheets) = $timesheet->getTimesheetDetail($person->getValueEncoded("person_id"));

list($timesheets) = $timesheet->getTimesheetByPerson($person->getValueEncoded("person_id"));

//get the general timesheet information
foreach ($timesheets as $timesheet) {
	//get out the details for a particular timesheet
	list($timesheet_details) = Timesheet_Detail::getTimesheetDetail($timesheet->getValueEncoded("timesheet_id"));
	?><tr>
	<td style="color:blue;border:1px solid;"><?php echo $timesheet->getValueEncoded("timesheet_id");?></td>
	<td style="color:green;border:1px solid;"><?php echo $timesheet->getValueEncoded("task_id");?></td>
	<td style="color:pink;border:1px solid;"><?php echo $timesheet->getValueEncoded("project_id");?></td></tr><tr>
	<?php 
	//yeah, not going to fight tables that will be gone eventually anyway. 
	$i = 0;
	foreach ($timesheet_details as $timesheet_detail) {
		?><td style="border:1px solid;"><input type="input" name="timesheet_date_<?php echo $i?>" value="<?php echo $timesheet_detail->getValueEncoded("timesheet_date");?>"></td>
	<?php $i++; 
	} ?>
	</tr>
	<tr>
	<?php
	$i = 0;
	foreach ($timesheet_details as $timesheet_detail) {
		?><td style="border:1px solid;"><input name="timesheet_number_of_hours_<?php echo $i ?>" value="<?php echo $timesheet_detail->getValueEncoded("timesheet_number_of_hours");?>"></td>
	<?php $i++;
	} ?>
	</tr>
<?php }



//add the timesheet to the UI if the person wanted to add a new timesheet.
if (isset($_GET["project_id"])) {
?>
<tr><td>
<?php for ($i=0; $i<7; $i++) {
	?><td><?php echo date("D M d", strtotime('sunday this week -1 week + ' . $i . ' days', $d))?>
	<input type="input" name="timesheet_date_<?php echo $i?>" value="<?php echo date("D M d", strtotime('sunday this week -1 week + ' . $i . ' days', $d))?>"></td>
<?php } ?>
</td></tr><tr><td>
<?php for ($i=0; $i<7; $i++) {
	?><td><input name="timesheet_number_of_hours_<?php echo $i ?>"><?php echo $timesheet_detail->getValue("timesheet_number_of_hours")?></td>
<?php } ?>

</td>
</tr>
</table>
</tr>
</tbody>
<?php } ?>
</table>
			<input type="button" name="add_row_button" value="Add Row" onclick="javascript:window.open('add_timesheet_row.php?person_id=<?php echo $person->getValueEncoded("person_id")?>','myWindow','width=300,height=200,left=250%,right=250%,scrollbars=no')">
			<input type="submit" name="save_timesheet_button" value="Save Timesheet">
		</form>

	</div>
</div>
<?php
}

 
function saveTimesheet() {
	//CREATE THE TIMESHEET OBJECT ($timesheet)
	$timesheet = new Timesheet( array(
	//CHECK REG SUBS!!
	"timesheet_id" => isset($_POST["timesheet_id"]) ? preg_replace("/[^ 0-9]/", "", $_POST["timesheet_id"]) : "",
	"timesheet_notes" => isset($_POST["timesheet_notes"]) ? preg_replace("/[^ \-\_a-zA-Z^0-9]/", "", $_POST["timesheet_notes"]) : "",
	"task_id" => isset($_POST["task_id"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["task_id"]) : "",
	"project_id" => isset($_POST["project_id"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["project_id"]) : "",
	"person_id" => isset($_POST["person_id"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["person_id"]) : ""
	));

	//create the timesheet detail object ($timesheet_detail)
	for ($i=0; $i<7; $i++) {
		$timesheet_detail[$i] = new Timesheet_Detail( array(
		//CHECK REG SUBS!!
		"timesheet_detail_id" => isset($_POST["timesheet_detail_id"]) ? preg_replace("/[^ 0-9]/", "", $_POST["timesheet_detail_id"]) : "",
		"timesheet_id" => isset($_POST["timesheet_id"]) ? preg_replace("/[^ 0-9]/", "", $_POST["timesheet_id"]) : "",
		"timesheet_date" => isset($_POST["timesheet_date_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_date_$i"]) : "",
		"timesheet_start_time" => isset($_POST["timesheet_start_time_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_start_time_$i"]) : "",
		"timesheet_end_time" => isset($_POST["timesheet_end_time_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_end_time_$i"]) : "",
		"timesheet_number_of_hours" => isset($_POST["timesheet_number_of_hours_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_number_of_hours_$i"]) : "",
		"timesheet_approved" => isset($_POST["timesheet_approved_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_approved_$i"]) : ""
		));
	}
	
	//get out the timesheet so we know to either update or insert the timesheet.
	$timesheet_exists = $timesheet->getTimesheetById($timesheet->getValueEncoded("person_id"), $timesheet->getValueEncoded("task_id"), $timesheet->getValueEncoded("project_id"));
	error_log("THESE ARE THE VALUES WE HAVE.");
	error_log("HERE IS THE POST");
	error_log(print_r($_POST,true));
	error_log($timesheet->getValueEncoded("person_id"));
	error_log($timesheet->getValueEncoded("task_id"));
	error_log($timesheet->getValueEncoded("project_id"));
	error_log($timesheet->getValueEncoded("timesheet_notes"));
	error_log(print_r($timesheet_exists, true));
	foreach ($timesheet_exists as $exists) {
		error_log("UCKY");
		error_log(print_r($exists,true));
		error_log(print_r($timesheet_exists, true));
	}
	
	//timesheet is not there, so update it.
	if (!$timesheet_exists) {
		$lastInsertId = $timesheet->insertTimesheet($timesheet->getValueEncoded("person_id"), $timesheet->getValueEncoded("task_id"), $timesheet->getValueEncoded("project_id"));
		
		//the timesheet doesn't exist, so we know the timesheet details aren't there and need to be inserted.
		for ($i=0; $i<7; $i++) {
			$timesheet_detail[$i]->insertTimesheetDetail($lastInsertId[0]);
			error_log("Here is the OBJECT");
			error_log(print_r($timesheet_detail[$i], true));
		}
	} else {
		//the timesheet exists already, so update the timesheet with new data.
		//if this is a timesheet detail that has already been inserted update the rows. if they are there. Otherwise, insert the details.
		foreach ($timesheet_exists as $t_exists) {
			//echo "that timesheet already exists. Update it, please.";
			$t_exists->updateTimesheet($t_exists->getValueEncoded("timesheet_id"));	
			for ($y=0; $y<7; $y++) {
				//print_r($timesheet_detail[$i]->getValueEncoded("timesheet_date"));
				$timesheet_detail_exists = $timesheet_detail[$y]->getTimesheetDetailByDate($t_exists->getValueEncoded("timesheet_id"), $timesheet_detail[$y]->getValueEncoded("timesheet_date"));
				if ($timesheet_detail_exists) {
					$i = 0;
					foreach ($timesheet_detail_exists as $td_exists) {
						//echo "timesheet detail exists and so does the timesheet, update it.";
						//print_r($td_exists->getValueEncoded("timesheet_detail_id"));
						//echo "<br>";
						//echo $td_exists->getValueEncoded("timesheet_detail_id");
						$timesheet_detail[$i]->updateTimesheetDetail($td_exists->getValueEncoded("timesheet_detail_id"));
						$i++;
					}
				} else {
					//echo "The timesheet exists, but the timesheet detail doesn't. Insert the timesheet detail.";
					$timesheet_detail[$y]->insertTimesheetDetail($exists->getValue("timesheet_id"));
					error_log("Here is the OBJECT");
					error_log(print_r($timesheet_detail[$y], true));
				}
			}
		}
	}		
	displayTimesheet($timesheet, $timesheet_detail);
} 

?>
<footer id="site-footer" class="site-footer">

</footer>
<script src="project-controls.js" type="text/javascript"></script>
</body>
</html>