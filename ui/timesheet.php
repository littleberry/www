<?php
	require_once("../common/common.inc.php");
	require_once("../classes/Project.class.php");
	require_once("../classes/Project_Person.class.php");
	require_once("../classes/Project_Task.class.php");
	require_once("../classes/Task.class.php");
	require_once("../classes/Task.class.php");
	require_once("../classes/Timesheet.class.php");
	require_once("../classes/Timesheet_Detail.class.php");
	
	//protect this page
	checklogin();
	
	include('header.php'); //add header.php to page
	if (isset($_POST["save_timesheet_button"]) and $_POST["save_timesheet_button"] == "Save Timesheet") {
		error_log("THE USER WANTS TO SAVE THE TIMESHEET");
		saveTimesheet();
	} else {
		displayTimesheet(new Timesheet(array()));
	}
	


function displayTimesheet($timesheet_aggregate) {
error_log("HERE IS THE POST!!!!!!!!!!!!!!!!!!");
error_log(print_r($_POST, true));
if (isset($_GET["timesheet_date"])) {
	$timesheet_date = $_GET["timesheet_date"];
} else {
	$timesheet_date = date('d-m-Y');
}
$d = strtotime($timesheet_date);
//echo "here is timesheet aggregate when you first come in.";
//print_r($timesheet_aggregate);
//exit;
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
						<input type="hidden" name="person_id_#" value="<?php echo $person->getValueEncoded("person_id");?>">
						<input type="hidden" name="project_id_#" value="<?php 
	if (isset($_GET["project_id"])) {
		echo $_GET["project_id"];
	} 
	?>">
	
	<img class="client-logo-img small" style="height:100px; width:100px;" src="<?php echo "images/" . $person->getValue("person_logo_link")?>" title="Person Logo" alt="Person logo" />
	<?php echo  "Adding Timesheet for Project " . Project::getProjectName($_GET["project_id"])[0] ?>	
	<input type="hidden" name="task_id_#" value="<?php 
	if (isset($_GET["task_id"]))	 {
		echo $_GET["task_id"];
	} 
	?>"><?php echo "<br>Adding Timesheet for Task " . Task::getTaskName($_GET["task_id"])[0]?>
	<input type="hidden" name="timesheet_notes_#" value="<?php 
	if (isset($_GET["timesheet_notes"]))	 {
		echo $_GET["timesheet_notes"];
	}
	?>">
						
<table style="width:100%; border:1px solid;">
<?php

list($timesheets) = Timesheet::getTimesheetByPerson($person->getValueEncoded("person_id"));

//get the general timesheet information
foreach ($timesheets as $timesheet) {
	//get out the details for a particular timesheet
	list($timesheet_details) = Timesheet_Detail::getTimesheetDetail($timesheet->getValueEncoded("timesheet_id"));
	
	?><tr>
	<td style="color:orange;border:1px solid;">Person ID:<?php echo $person->getValueEncoded("person_id");?>
	<input type="hidden" name="person_id_<?php echo $timesheet->getValueEncoded("timesheet_id");?>" value="<?php echo $person->getValueEncoded("person_id");?>"></td>
	<td style="color:blue;border:1px solid;">Timesheet ID:<?php echo $timesheet->getValueEncoded("timesheet_id");?>
	<input type="hidden" name="timesheet_id_<?php echo $timesheet->getValueEncoded("timesheet_id");?>" value="<?php echo $timesheet->getValueEncoded("timesheet_id");?>"></td>
	<td style="color:green;border:1px solid;">Task:<?php echo $timesheet->getValueEncoded("task_id");?>
	<input type="hidden" name="task_id_<?php echo $timesheet->getValueEncoded("timesheet_id");?>" value="<?php echo $timesheet->getValueEncoded("task_id");?>"></td>
	<td style="color:pink;border:1px solid;">Project:<?php echo $timesheet->getValueEncoded("project_id");?>
	<input type="hidden" name="project_id_<?php echo $timesheet->getValueEncoded("timesheet_id");?>" value="<?php echo $timesheet->getValueEncoded("project_id");?>">
	<input type="hidden" name="timesheet_notes_<?php echo $timesheet->getValueEncoded("timesheet_id");?>" value="<?php echo $timesheet->getValueEncoded("timesheet_notes");?>"></td></tr><tr>
	<?php 
	//yeah, not going to fight tables that will be gone eventually anyway. 
	$i = 0;
	foreach ($timesheet_details as $timesheet_detail) {
		?>
		<input type="hidden" name="timesheet_id_<?php echo $timesheet->getValueEncoded("timesheet_id");?>" value="<?php echo $timesheet->getValueEncoded("timesheet_id");?>">
		<td style="border:1px solid;"><input type="input" name="timesheet_date_<?php echo $timesheet->getValueEncoded("timesheet_id");?>_<?php echo $i?>" value="<?php echo $timesheet_detail->getValueEncoded("timesheet_date");?>"></td>
	<?php $i++; 
	} ?>
	</tr>
	<tr>
	<?php
	$i = 0;
	foreach ($timesheet_details as $timesheet_detail) {
		?><td style="border:1px solid;"><input name="timesheet_number_of_hours_<?php echo $timesheet->getValueEncoded("timesheet_id");?>_<?php echo $i ?>" value="<?php echo $timesheet_detail->getValueEncoded("timesheet_number_of_hours");?>"></td>
	<?php $i++;
	} ?>
	</tr>
<?php }



//add the timesheet to the UI if the person wanted to add a new timesheet.
//we don't have the timesheet ID yet.
//if (isset($_GET["project_id"])) {
?>
<tr>

	<input type="hidden" name="person_id_#" value="<?php echo $person->getValueEncoded("person_id");?>">
	<input type="hidden" name="project_id_#" value="<?php 
	if (isset($_GET["project_id"])) {
		echo $_GET["project_id"];
	} elseif (isset($timesheet)) {
		echo $timesheet->getValueEncoded("project_id");
	} else {
			echo "";
	}
	?>"><?php //echo  Project::getProjectName($_GET["project_id"])[0] ?>	
	<input type="hidden" name="task_id_#" value="<?php 
	if (isset($_GET["task_id"]))	 {
		echo $_GET["task_id"];
	} elseif (isset($timesheet)) {
		echo $timesheet->getValueEncoded("task_id");
	} else {
		echo "";
	}
	?>"><?php //echo Task::getTaskName($_GET["task_id"])[0]?>
	<input type="hidden" name="timesheet_notes_#" value="<?php 
	if (isset($_GET["timesheet_notes"]))	 {
		echo $_GET["timesheet_notes"];
	} elseif (isset($timesheet)) {
		echo $timesheet->getValueEncoded("timesheet_notes");
	} else {
		echo "";
	}
	?>">

<?if (isset($_GET["project_id"])) {
	for ($i=0; $i<7; $i++) {
		?><td><?php echo date("D M d", strtotime('sunday this week -1 week + ' . $i . ' days', $d))?>
		<input type="input" name="timesheet_date_#_<?php echo $i?>" value="<?php echo date("D M d", strtotime('sunday this week -1 week + ' . $i . ' days', $d))?>"></td>
		<?php } ?>
		</td></tr><tr>
		<?php 
			for ($i=0; $i<7; $i++) {
			?><td><input name="timesheet_number_of_hours_#_<?php echo $i ?>"></td>
	<?php } 
}
	?>	

</tr>
</table>
</tr>
</tbody>
<?php //} ?>
</table>
			<input type="button" name="add_row_button" value="Add Row" onclick="javascript:window.open('add_timesheet_row.php?person_id=<?php echo $person->getValueEncoded("person_id")?>','myWindow','width=300,height=200,left=250%,right=250%,scrollbars=no')">
			<input type="submit" name="save_timesheet_button" value="Save Timesheet">
		</form>

	</div>
</div>
<?php
}

 
function saveTimesheet() {
	//CREATE THE TIMESHEET OBJECTS ($timesheet).
	//put each timesheet and its data into an array labeled with the project ID. THIS IS GOING TO NEED THE DATE
	$person=Person::getByEmailAddress($_SESSION["logged_in"]);
	list($timesheet_ids) = Timesheet::getTimesheetIds($person->getValueEncoded("person_id"));
	
	//USER IS ADDING A TIMESHEET FOR THE FIRST TIME ADD THIS OBJECT TO THE UI.
	
	$tid = "#";
	if	(!$timesheet_ids || $tid == "#"){
	$timesheet[$tid] = new Timesheet( array(
	//CHECK REG SUBS!!
	"timesheet_id" => isset($_POST["timesheet_id_$tid"]) ? preg_replace("/[^ 0-9]/", "", $_POST["timesheet_id_$tid"]) : "",
	"timesheet_notes" => isset($_POST["timesheet_notes_$tid"]) ? preg_replace("/[^ \-\_a-zA-Z^0-9]/", "", $_POST["timesheet_notes_$tid"]) : "",
	"task_id" => isset($_POST["task_id_$tid"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["task_id_$tid"]) : "",
	"project_id" => isset($_POST["project_id_$tid"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["project_id_$tid"]) : "",
	"person_id" => isset($_POST["person_id_$tid"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["person_id_$tid"]) : ""
	));
		
	$timesheet_aggregate[] = $timesheet[$tid];
		
	//create the timesheet detail object ($timesheet_detail)
	
	for ($i=0; $i<7; $i++) {
		$timesheet_detail[$tid][$i] = new Timesheet_Detail( array(
		//CHECK REG SUBS!!
		"timesheet_detail_id" => isset($_POST["timesheet_detail_id_$tid"]) ? preg_replace("/[^ 0-9]/", "", $_POST["timesheet_detail_id_$tid"]) : "",
		"timesheet_id" => isset($_POST["timesheet_id_$tid"]) ? preg_replace("/[^ 0-9]/", "", $_POST["timesheet_id_$tid"]) : "",
		"timesheet_date" => isset($_POST["timesheet_date_$tid"."_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_date_$tid"."_$i"]) : "",
		"timesheet_start_time" => isset($_POST["timesheet_start_time_$tid"."_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_start_time_$tid"."_$i"]) : "",
		"timesheet_end_time" => isset($_POST["timesheet_end_time_$tid"."_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_end_time_$tid_"."$i"]) : "",
		"timesheet_number_of_hours" => isset($_POST["timesheet_number_of_hours_$tid"."_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_number_of_hours_$tid"."_$i"]) : "",
		"timesheet_approved" => isset($_POST["timesheet_approved_$tid"."_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_approved_$tid_"."$i"]) : ""
		));
		$timesheet_aggregate[] = $timesheet_detail[$tid][$i];
	} 
	}
	
	//echo("HERE IS THE TIMESHEET AGGREGATE AFTER WE GET TIMESHEETS for new timesheet");
	//print_r($timesheet_aggregate);
	//echo "<br><br>";
	
	//GET OUT THE STORED TIMESHEETS AND CREATE THE OBJECTS TO DISPLAY IN THE UI.	
	
	foreach ($timesheet_ids as $timesheet_id) {
		$tid = $timesheet_id->getValueEncoded("timesheet_id");
		//echo "HERE IS THE TID";
		//echo $tid;
		$timesheet[$tid] = new Timesheet( array(
		//CHECK REG SUBS!!
		"timesheet_id" => isset($_POST["timesheet_id_$tid"]) ? preg_replace("/[^ 0-9]/", "", $_POST["timesheet_id_$tid"]) : "",
		"timesheet_notes" => isset($_POST["timesheet_notes_$tid"]) ? preg_replace("/[^ \-\_a-zA-Z^0-9]/", "", $_POST["timesheet_notes_$tid"]) : "",
		"task_id" => isset($_POST["task_id_$tid"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["task_id_$tid"]) : "",
		"project_id" => isset($_POST["project_id_$tid"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["project_id_$tid"]) : "",
		"person_id" => isset($_POST["person_id_$tid"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["person_id_$tid"]) : ""
		));
			
		$timesheet_aggregate[] = $timesheet[$tid];

		//create the timesheet detail object ($timesheet_detail)
		for ($i=0; $i<7; $i++) {
			$timesheet_detail[$tid][$i] = new Timesheet_Detail( array(
			//CHECK REG SUBS!!
			"timesheet_detail_id" => isset($_POST["timesheet_detail_id_$tid"]) ? preg_replace("/[^ 0-9]/", "", $_POST["timesheet_detail_id_$tid"]) : "",
			"timesheet_id" => isset($_POST["timesheet_id_$tid"]) ? preg_replace("/[^ 0-9]/", "", $_POST["timesheet_id_$tid"]) : "",
			"timesheet_date" => isset($_POST["timesheet_date_$tid"."_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_date_$tid"."_$i"]) : "",
			"timesheet_start_time" => isset($_POST["timesheet_start_time_$tid"."_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_start_time_$tid"."_$i"]) : "",
			"timesheet_end_time" => isset($_POST["timesheet_end_time_$tid"."_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_end_time_$tid_"."$i"]) : "",
			"timesheet_number_of_hours" => isset($_POST["timesheet_number_of_hours_$tid"."_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_number_of_hours_$tid"."_$i"]) : "",
			"timesheet_approved" => isset($_POST["timesheet_approved_$tid"."_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_approved_$tid_"."$i"]) : ""
			));
			$timesheet_aggregate[] = $timesheet_detail[$tid][$i];
		}
	} 
	
	//echo("HERE IS THE TIMESHEET AGGREGATE AFTER WE GET EVERYTHING");
	//print_r($timesheet_aggregate);
			
	//get out each of the user's saved timesheets.
	//all if the timesheet information is in $timesheet_aggregate.
	//list($timesheet_ids) = Timesheet::getTimesheetIds($person->getValueEncoded("person_id"));
	//THE USER HAS NO TIMESHEETS AT ALL, so just insert this one.
	//if (!$timesheet_ids) {
		//echo "this person has no timesheets at all, insert this one:";
		//error_log(print_r($timesheet_aggregate, true));
		//foreach ($timesheet_aggregate as $timesheet_object) {
		//	if ((get_class($timesheet_object)) == "Timesheet") {
				//print_r($timesheet_object);
		//		$lastInsertId = $timesheet_object->insertTimesheet($timesheet_object->getValueEncoded("person_id"), $timesheet_object->getValueEncoded("task_id"), $timesheet_object->getValueEncoded("project_id"));
		//	} elseif ((get_class($timesheet_object)) == "Timesheet_Detail") {
		//		$timesheet_object->insertTimesheetDetail($lastInsertId[0]);
		//	}
	
		//}
	//} else {	
		foreach ($timesheet_aggregate as $timesheet_object) {
			//DEAL WITH THE TIMESHEET FIRST HERE.
			if ((get_class($timesheet_object)) == "Timesheet") {
				$timesheet_exists = $timesheet_object->getTimesheetById($timesheet_object->getValueEncoded("person_id"), $timesheet_object->getValueEncoded("task_id"), $timesheet_object->getValueEncoded("project_id"));	
				//timesheet is NOT THERE, insert it.
				if (!$timesheet_exists) {
					$lastInsertId = $timesheet_object->insertTimesheet($timesheet_object->getValueEncoded("person_id"), $timesheet_object->getValueEncoded("task_id"), $timesheet_object->getValueEncoded("project_id"));
				} else {
					//timesheet is THERE, update it.
					echo $timesheet_object->getValueEncoded("timesheet_id");
					//print_r($timesheet_object->getValue("timesheet_"));
					$timesheet_object->updateTimesheet($timesheet_object->getValueEncoded("timesheet_id"));	
				}
			} elseif ((get_class($timesheet_object)) == "Timesheet_Detail") {
				$timesheet_detail_exists = $timesheet_object->getTimesheetDetailByDate($timesheet_object->getValueEncoded("timesheet_id"), $timesheet_object->getValueEncoded("timesheet_date"));
				//timesheet detail is NOT THERE, insert it.
				if (!$timesheet_exists) {
					$timesheet_object->insertTimesheetDetail($lastInsertId[0]);
				} else {
					//timesheet detail is THERE, update it.
					$timesheet_object->updateTimesheetDetail($timesheet_object->getValueEncoded("timesheet_id"), $timesheet_object->getValueEncoded("timesheet_date"));	
				}
			}
				
		}	
	//} 
	displayTimesheet($timesheet_aggregate);
}

?>
<footer id="site-footer" class="site-footer">

</footer>
<script src="project-controls.js" type="text/javascript"></script>
</body>
</html>