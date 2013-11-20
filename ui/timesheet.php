<?php
	require_once("../common/common.inc.php");
	require_once("../classes/Client.class.php");
	require_once("../classes/Project.class.php");
	require_once("../classes/Project_Person.class.php");
	require_once("../classes/Project_Task.class.php");
	require_once("../classes/Task.class.php");
	require_once("../classes/Task.class.php");
	require_once("../classes/Timesheet.class.php");
	require_once("../classes/Timesheet_Item.class.php");
	
	//protect this page
	checklogin();
	
	include('header.php'); //add header.php to page
	if (isset($_POST["save_timesheet_button"]) and $_POST["save_timesheet_button"] == "Save Timesheet") {
		saveTimesheet();
	} else {
		displayTimesheet(new Timesheet(array()), new Timesheet_Item(array()));
	}
	
function displayTimesheet($timesheet_aggregate) {
	error_log("HERE IS THE POST!!!!!!!!!!!!!!!!!!");
	//error_log(print_r($_POST, true));
	//first, get the date off of the URL. This is not set up yet, but will be required to get
	//the correct timesheet out of the database. Right now it just returns the current date.
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
					<!-- <li class="page-controls-item link-btn"><a class="view-all-link" href="project-add.php">+ Add Timesheet</a></li> --> <!-- We probably don't need this -->
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
							<li class="entity-details-item name project">
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
										//list($tasksForProject) = Project_Task::getTasksForProject(1);
										//foreach ($tasksForProject as $projectTask) { ?>
											<!-- <option value="<?php //echo $projectTask->getValue("task_id"); ?>"><?php //echo $projectTask->getValue("task_name"); ?></option> -->
									<?php //}	?> 
								</select>
							</li>
						</ul>
					</section>
				</fieldset>
			</form>
		</div>
		<div id="timesheet" class="entity-detail">
			<nav class="timesheet-controls">
				<span id="time-period">
					<a href="#" class="ui-button previous-date">Previous week</a>
					<a href="#" class="ui-button current-date">This week</a>
					<a href="#" class="ui-button next-date">Next week</a>
				</span>
				<!-- <input id="date-picker" type="hidden" title="Select date" value="" /> -->
				
				<!-- <span id="date-picker"></span> -->
				<span id="time-display">
					<input id="day-view" type="radio" name="time-view" class="ui-button time-view-button" title="Day" value="Day" /><label for="day-view">Day</label>
					<input id="week-view" type="radio" name="time-view" class="ui-button time-view-button" title="Week" value="Week" checked="checked" /><label for="week-view">Week</label>
				</span>
				
			</nav>
			<table id="timesheet-tasks-list" class="entity-table timesheet tablesorter" data-person_id="<?php echo $person->getValue('person_id'); ?>">
				<thead>
					<tr>
						<th class="task-name"></th>
						<th data-sorter="false" class="day">M</th>
						<th data-sorter="false" class="day">T</th>
						<th data-sorter="false" class="day">W</th>
						<th data-sorter="false" class="day">Th</th>
						<th data-sorter="false" class="day">F</th>
						<th data-sorter="false" class="day">Sa</th>
						<th data-sorter="false" class="day">Su</th>
						<th data-sorter="false" class="total day"></th>
						<th data-sorter="false" class="delete"></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td><a href="#" class="ui-button new-time-entry add-row ">+ Add Row</a> <a href="#" class="ui-button save">Save</a> <span class="last-saved"></span></td>
						<td class="day total"></td>
						<td class="day total"></td>
						<td class="day total"></td>
						<td class="day total"></td>
						<td class="day total"></td>
						<td class="day total"></td>
						<td class="day total"></td>
						<td class="week-total"></td>
						<td></td>
					</tr>
				</tfoot>
				<tbody>
				</tbody>
			</table>
		</div>
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
	//get out each timesheet for this person put each timesheet and its data into an array labeled with the timesheet ID.
	//this code only deals with timesheets the user has stored.
	//BUG FIX 3: we already have this, don't call the session again.
	$person=Person::getByEmailAddress($_SESSION["logged_in"]);
	//list($timesheet_ids) = Timesheet::getTimesheetIds($person->getValueEncoded("person_id"));
	list($timesheet_ids) = Timesheet::getTimesheetByPersonForDate($person->getValueEncoded("person_id"), $timesheet_date);
			
	//We're getting the values from the database, so task_id, project_id and person_id follow the field_name_$tid_$i style here.
	
	foreach ($timesheet_ids as $timesheet_id) {
		$tid = $timesheet_id->getValueEncoded("timesheet_id");
		$timesheet[$tid] = new Timesheet( array(
		//CHECK REG SUBS!!
		"timesheet_id" => isset($_POST["timesheet_id_$tid"]) ? preg_replace("/[^ 0-9]/", "", $_POST["timesheet_id_$tid"]) : "",
		"timesheet_approved" => isset($_POST["timesheet_approved_$tid"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_approved_$tid"]) : "",
		"timesheet_submitted" => isset($_POST["timesheet_submitted_$tid"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_submitted_$tid"]) : "",
		"timesheet_start_date" => isset($_POST["timesheet_start_date_$tid"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_start_date_$tid"]) : "",
		"timesheet_end_date" => isset($_POST["timesheet_end_date_$tid"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_end_date_$tid"]) : "",
		"person_id" => isset($_POST["person_id_$tid"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["person_id_$tid"]) : ""
		));
			
		//store the timesheet fields in an array
		$timesheet_aggregate[] = $timesheet[$tid];


		//create the timesheet item objects ($timesheet_items)
		for ($i=0; $i<7; $i++) {
			$timesheet_item[$tid][$i] = new Timesheet_Item( array(
				//CHECK REG SUBS!!
				"timesheet_item_id" => isset($_POST["timesheet_item_id_$tid"."_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_item_id_$tid"."_$i"]) : "",
				"person_id" => isset($_POST["person_id_$tid"."_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["person_id_$tid"."_$i"]) : "",
				"timesheet_date" => isset($_POST["timesheet_date_$tid"."_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_date_$tid"."_$i"]) : "",
				"task_id" => isset($_POST["task_id_$tid"."_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["task_id_$tid"."_$i"]) : "",
				"project_id" => isset($_POST["project_id_$tid"."_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["project_id_$tid"."_$i"]) : "",
				"timesheet_hours" => isset($_POST["timesheet_hours_$tid"."_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_hours_$tid"."_$i"]) : "",
				"timesheet_notes" => isset($_POST["timesheet_notes_$tid"."_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_notes_$tid"."_$i"]) : ""
			));
			//store the timesheet items in an array.
			$timesheet_item_aggregate[] = $timesheet_item[$tid][$i];
		}
	} 
		//debug code to check arrays after the values have been pulled
		//error_log("timesheet aggregate");
		//error_log(print_r($timesheet_aggregate,true));
		//error_log("timesheet item aggregate");
		//error_log(print_r($timesheet_item_aggregate, true));

	
	//USER IS ADDING A TIMESHEET FOR THE FIRST TIME, or they have no timesheets at all, so we don't have a timesheet id to put in the field
	//in the UI. Instead, use "#".
	//BUG FIX 4 instructions:
	//I am not going to fight the UI anymore. It is inconsistent, but the task_id, the project_id and the person_id all are based on the field_name_$tid style
	//and not field_name_$tid_$i style. If we keep this UI in any way, I will fight it later. 11/16
	
	
	if	(isset($_POST["person_id_#"])) {
		$tid = "#";
		$timesheet[$tid] = new Timesheet( array(
		//CHECK REG SUBS!!
		"timesheet_id" => isset($_POST["timesheet_id_$tid"]) ? preg_replace("/[^ 0-9]/", "", $_POST["timesheet_id_$tid"]) : "",
		"timesheet_approved" => isset($_POST["timesheet_approved_$tid"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_approved_$tid"]) : "",
		"timesheet_submitted" => isset($_POST["timesheet_submitted_$tid"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_submitted_$tid"]) : "",
		"timesheet_start_date" => isset($_POST["timesheet_start_date_$tid"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_start_date_$tid"]) : "",
		"timesheet_end_date" => isset($_POST["timesheet_end_date_$tid"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_end_date_$tid"]) : "",
		"person_id" => isset($_POST["person_id_$tid"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["person_id_$tid"]) : ""
		));
		
		$timesheet_aggregate[] = $timesheet[$tid];
		
		//create the timesheet detail object ($timesheet_item), all of the values are blank, since this timesheet is not in the db yet.
	
		for ($i=0; $i<7; $i++) {
			$timesheet_item[$tid][$i] = new Timesheet_Item( array(
				//CHECK REG SUBS!!
				"timesheet_item_id" => isset($_POST["timesheet_item_id_$tid"."_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_item_id_$tid"."_$i"]) : "",
				"timesheet_date" => isset($_POST["timesheet_date_$tid"."_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_date_$tid"."_$i"]) : "",
				"task_id" => isset($_POST["task_id_$tid"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["task_id_$tid"]) : "",
				"project_id" => isset($_POST["project_id_$tid"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["project_id_$tid"]) : "",
				"person_id" => isset($_POST["person_id_$tid"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["person_id_$tid"]) : "",
				"timesheet_hours" => isset($_POST["timesheet_hours_$tid"."_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_hours_$tid"."_$i"]) : "",
				"timesheet_notes" => isset($_POST["timesheet_notes_$tid"."_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_notes_$tid"."_$i"]) : ""
			));
			$timesheet_item_aggregate[] = $timesheet_item[$tid][$i];
		} 
	}
	
	error_log("here is the post");
	error_log(print_r($_POST,true));
	error_log("here are the timesheet aggregate");
	error_log(print_r($timesheet_aggregate,true));
	error_log(print_r($timesheet_item_aggregate, true));
	

		//timesheet aggregate has all the timesheets
		//loop through the timesheets in the aggregate and update or insert each based on the class.
		foreach ($timesheet_aggregate as $timesheet_object) {
			//insert or update the timesheet(s).
			if ((get_class($timesheet_object)) == "Timesheet") {
				$timesheet_exists = $timesheet_object->getTimesheetById($timesheet_object->getValueEncoded("person_id"), $timesheet_object->getValueEncoded("timesheet_start_date"),$timesheet_object->getValueEncoded("timesheet_end_date"));
				//this will work temporarily until we decide what to do with the start and end date.
				//this won't work!!!
				//$timesheet_item_exists = Timesheet_Item::getTimesheetItem($timesheet_object->getValueEncoded("timesheet_date"), $timesheet_object->getValueEncoded("person_id"), $timesheet_object->getValueEncoded("project_id"), $timesheet_object->getValueEncoded("task_id"));	
				
				if ($timesheet_exists == 0 && $timesheet_item_exists == 0) {
					error_log("<br><br>timesheet doesn't exist.");
					$timesheet_object->getValueEncoded("person_id");
					//error_log $timesheet_object->getValueEncoded("person_id");
					$lastInsertId = $timesheet_object->insertTimesheet($timesheet_object->getValueEncoded("person_id"));
					
					//put the timesheet id into the object, so we'll have it later.
					$timesheet_object->setValue("timesheet_id", $lastInsertId[0]);
					//error_log("<br><br>LAST INSERT ID IS ");
					//error_log(print_r($lastInsertId[0]));
					//error_log("<br><br><br>");
				} else {
					error_log("<br><br>timesheet exists.<br><br>");
					if (!$lastInsertId) {
						$lastInsertId = $timesheet_object->getLastInsert();
					}
					//error_log(print_r($timesheet_object),true);
					//timesheet exists, update it.
					$timesheet_object->updateTimesheet($timesheet_object->getValueEncoded("timesheet_start_date"),$timesheet_object->getValueEncoded("timesheet_end_date"));	
				}
			} 
		}
		
		
		//BUG FIX 5..these loops don't need to be separated this way.
		//timesheet item aggregate has all the timesheet items.
		//loop through the timesheet items in the aggregate and update or insert each based on the class.
		foreach ($timesheet_item_aggregate as $timesheet_object) {
			if ((get_class($timesheet_object)) == "Timesheet_Item") {
				$timesheet_object->setValue("timesheet_item_id", $lastInsertId[0]);
				$timesheet_item_exists = $timesheet_object->getTimesheetItem($timesheet_object->getValueEncoded("timesheet_date"), $timesheet_object->getValueEncoded("person_id"), $timesheet_object->getValueEncoded("project_id"), $timesheet_object->getValueEncoded("task_id"));
				//error_log(print_r($timesheet_item_exists),true);
				//no timesheet item, insert it.
				if (!$timesheet_item_exists) {
					error_log("LINE 341: this timesheet item doesn't exist.");
					error_log(print_r($timesheet_object,true));
					//error_log("<br><br>LAST sINSERT ID IS ");
					//error_log(print_r($lastInsertId[0]), true);
					//error_log("<br><br><br>here is what we're trying to insert: ");
					error_log(print_r($timesheet_object), true);
					
					error_log("checking value of lastInsertId:" . $lastInsertId[0]);
					$timesheet_object->insertTimesheetItem($timesheet_object->getValueEncoded("person_id"), $lastInsertId[0]);
				} else {
					//timesheet item exists, update it.
					$timesheet_object->updateTimesheetItem($timesheet_object->getValueEncoded("timesheet_item_id"));	
					error_log("that timesheet info does exist.");
				}
			}
				
		}	
	displayTimesheet($timesheet_aggregate, $timesheet_item_aggregate);
}

?>
