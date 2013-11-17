<?php
	require_once("../common/common.inc.php");
	require_once("../classes/Client.class.php");
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
	//error_log(print_r($_POST, true));
	if (isset($_GET["timesheet_date"])) {
		$timesheet_date = $_GET["timesheet_date"];
	} else {
		$timesheet_date = date('d-m-Y');
	}
	$d = strtotime($timesheet_date);
	//echo "here is timesheet aggregate when you first come in.";
	//print_r($timesheet_aggregate);
	//exit;
	if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] != "") {
		$person = Person::getByEmailAddress($_SESSION["logged_in"]);
	} else {
		error_log("Something is wrong here...this person is not logged in and you shouldn't be seeing this, timesheet.php.");
		exit();
	}

	?>
	
	<div id="page-content" class="page-content">
		<header class="page-header">
			<h1 class="page-title"><?php echo date("F j, Y");?></h1>
			<nav class="page-controls-nav">
				<ul class="page-controls-list timesheet">
					<li class="page-controls-item link-btn"><a class="view-all-link" href="project-add.php">+ Add Timesheet</a></li>
					<li class="page-controls-item"><a class="view-archive-link" href="projects.php?archives=1">View Archives</a></li>
					<li class="page-controls-item"><a class="view-all-link" href="projects.php">View All</a></li>
				</ul>
			</nav>
		</header>
		<div id="add-ts-entry-modal" class="entity-detail" title="Add Time Entry">
			<form id="ts-entry-input-form" action="timesheets.php" method="post" enctype="multipart/form-data">
				<input id="proc-type" type="hidden" name="action" value="<?php echo $processType ?>"/>
				<fieldset class="entity-details-entry  modal">
					<header class="entity-details-header timesheet">
						<h1 class="entity-details-title">Enter time entry details:</h1>
						<h4 class="required">= Required</h4>
					</header>
					<section id="timesheet-info" class="entity-detail">
						<ul class="details-list entity-details-list timesheet">
							<li class="entity-details-item name task">
								<label for="project_name" <?php validateField("project_name", $missingFields)?> class="entity-details-label">Project:</label>
								<select id="project-name" name="project_name" class="project-name-select" tabindex="1">
									<?php //this may be moved to JS
										list($projectsForPerson) = Project_Person::getProjectsForPerson($person->getValue("person_id"));
										//$first = 31;
										foreach ($projectsForPerson as $projectPerson) {
											$client = Client::getClient( $projectPerson->getValue("client_id") ); ?>
											<option value="<?php echo $projectPerson->getValue("project_id"); ?>"><span><?php echo $projectPerson->getValue("project_name"); ?></span> (<?php echo $client->getValue("client_name"); ?>)</option>
									<?php }	?> 
								</select>
							</li>
							<li class="entity-details-item hourly-rate task">
								<label for="task_name" <?php validateField("task_name", $missingFields)?> class="entity-details-label">Task:</label>
								<select id="task-name" name="task_name" class="task-name-select" tabindex="1">
									<?php //this may be moved to JS
										list($tasksForProject) = Project_Task::getTasksForProject(1);
										foreach ($tasksForProject as $projectTask) { ?>
											<option value="<?php echo $projectTask->getValue("task_id"); ?>"><?php echo $projectTask->getValue("task_name"); ?></option>
									<?php }	?> 
								</select>
							</li>
						</ul>
					</section>
				</fieldset>
			</form>
		</div>
		<table id="timesheet-tasks-list" class="entity-table timesheet tablesorter">
			<thead>
				<tr>
					<th class="task-name"></th>
					<th data-sorter="false" class="day">M</th>
					<th data-sorter="false" class="day">T</th>
					<th data-sorter="false" class="day">W</th>
					<th data-sorter="false" class="day">Th</th>
					<th data-sorter="false" class="day">F<th>
					<th data-sorter="false" class="day">Sa<th>
					<th data-sorter="false" class="day">Su<th>
					<th data-sorter="false" class="total"></th>
					<th data-sorter="false" class="delete"></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>

	</div>
	<footer id="site-footer" class="site-footer">
	
	</footer>
<script src="timesheet-controls.js" type="text/javascript"></script>
</body>
</html>		

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
