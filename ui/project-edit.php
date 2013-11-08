<?php
	//this shouldn't be necessary. headers are NOT sent yet if this is coded correctly.
	//function displayProjectPage() {
	//	//this probably isn't right, but I'll use it for now. won't work if JavaScript is off.
	//	printf("<script>location.href='projects.php'</script>");
	//}
	
	require_once("../common/common.inc.php");
	require_once("../common/errorMessages.php");
	require_once("../classes/Client.class.php");
	require_once("../classes/Project_Person.class.php");
	require_once("../classes/Project_Task.class.php");
	require_once("../classes/Project.class.php");
	require_once("../classes/Task.class.php");
	require_once("../classes/Person.class.php");

	
?>
<?php	/*OVERALL CONTROL
		1. first time user comes in, call the displayClientAndContactsEditForm function.
		2. Set the client and contact objects to the value pulled from the database.
		3. User clicks on a button to submit the form, call the editClientAndContacts function.
		4. If required fields are missing in the form, re-display the form with error messages.
		5. If there are no missing required fields, call Project::updateProject*/	
 			if (isset($_GET["func"])) {
				if ($_GET["func"] == "returnClientMenu") {
					echo returnClientMenu();
				}
			} else if (isset($_POST["func"])) {
				if ($_POST["func"] == "editProject") {
					editProject();
				}
			} else {
				if (isset($_POST["action"]) and $_POST["action"] == "edit_project") {
					editProject();
				} else {
					displayProjectEditForm(array(), array(), new Project(array()), new Project_Person(array()), new Project_Task(array()));
				}
			}
	
	/*DISPLAY PROJECT EDIT WEB FORM (displayProjectEditForm)
	note...I think we can remove the PHP validation to update the style in validateField
	1. This is the form displayed to the user, the first time the user comes in it gets the client_id out of the $_GET variable (please encode!!)
	2. If first time, pull the project object from the database.
	3. on reocurring pulls, error messages may or may not be there, based on the user's input, object details will come from the $_POST variable.*/
?>	
<?php
	//Returns a select menu of clients with ids. Meant to be called via ajax.
	function returnClientMenu() {
		//$select = "<strong>test</strong>";
		$select = "";
		//get the clients out to populate the drop down.
		list($clients) = Client::getClients();
		$select .= '<select name="client_id" id="project-client-select" size="1">';
		
		foreach ($clients as $client) {
			$select .= '<option value="' . $client->getValue("client_id") . '">' . $client->getValue("client_name") .'</option>';
		}
		$select .= '</select>';
		
		return $select;
	}
	
	function displayProjectEditForm($errorMessages, $missingFields, $project, $project_person, $project_task) {
		if ($errorMessages) {
			foreach($errorMessages as $errorMessage) {
				echo $errorMessage;
			}
		}
		
		//get  out the project ID, since the user came in from the edit project button.
		if (isset($_GET["project_id"])) {
			$project_id = $_GET["project_id"];
			$project=Project::getProjectByProjectId($project_id);
		} elseif (isset($_POST["project_id"])) {
			$project_id = $_POST["project_id"];
			$project=Project::getProjectByProjectId($project_id);
		} else {
			echo "You cannot edit a project unless you have provided a project ID.";
			exit();
		}
		

		
	include('header.php'); //add header.php to page
?>
<script type="text/javascript">
function FillTasks(f) {
    //alert(f.task_ids.value);
    f.task_id.value = f.task_ids.value + "," + f.task_id.value;    
}
function FillPeople(f) {
    //alert(f.person_ids.value);
    f.person_id.value = f.person_ids.value + "," + f.person_id.value;    
}

//THESE ARE ONLY HERE FOR THE DEMO
function showProjectHourlyRate(f) {
	if(f.value == "Project hourly rate"){
		document.getElementById('project_hourly_rate').style.display = "inline";
    } else {
	    document.getElementById('project_hourly_rate').style.display = "none";
    }
}

function showBudgetFields(f) {
	if (f.value == "Total project hours") {
    	document.getElementById('project_budget_total_hours').style.display = "inline";
        document.getElementById('project_budget_total_fees').style.display = "none";
        document.getElementById('project_budget_includes_expenses'). style.display = "none";
        document.getElementById('project_budget_includes_expenses_label'). style.display = "none";
	} else if (f.value == "Total project fees") {
		document.getElementById('project_budget_total_hours').style.display = "none";
        document.getElementById('project_budget_total_fees').style.display = "inline";
        document.getElementById('project_budget_includes_expenses'). style.display = "inline";
        document.getElementById('project_budget_includes_expenses_label'). style.display = "inline";
	} else {
		document.getElementById('project_budget_total_hours').style.display = "none";
        document.getElementById('project_budget_total_fees').style.display = "none";
        document.getElementById('project_budget_includes_expenses'). style.display = "none";
        document.getElementById('project_budget_includes_expenses_label'). style.display = "none";
	}
}


</script>
<div id="page-content" class="page-content">
	<header class="page-header">
		<h1 class="page-title">Edit Project: <?php echo $project->getValue("project_name")?></h1>
		<?php $client_name = Client::getClientNameById($project->getValueEncoded("client_id"));
		?>
		<h2 class="page-sub-title"><a href="client-detail.php?client_id=<?php echo $project->getValueEncoded("client_id")?>" class="" title="View client's details"><?php echo $client_name["client_name"]?></a></h2>
		<nav class="page-controls-nav">
			<ul class="page-controls-list project">
				<li class="page-controls-item link-btn">
				<a class="view-all-link" href="#">Save Project</a></li>
				<!-- <a class="view-all-link" href="project-edit.php?project_id=<?php //echo $project_id?>">Save Project</a> --></li>
				<li class="page-controls-item"><a class="view-archive-link" href="projects.php?archives=1">View Archives</a></li>
				<li class="page-controls-item"><a class="view-all-link" href="projects.php">View All</a></li>
			</ul>
		</nav>
	</header>


	<div class="content">
		<!--BEGIN FORM-->
		<form action="project-edit.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="action" value="edit_project">
			<input type="hidden" name="project_id" value="<?php echo $project_id?>">
			<article class="entity-detail">
				<fieldset class="entity-details-entry">
					<header class="entity-details-header project">
						<h1 class="entity-details-title">Project Settings:</h1>
						<h4 class="required">= Required</h4>
					</header>
					<section id="project-info" class="entity-detail">
						<h2 class="entity-sub-title">Project Info</h2>
						<ul class="details-list entity-details-list project">
							<li class="entity-details-item name project">
								<label for="project_name" <?php validateField("project_name", $missingFields)?> class="entity-details-label">Project Name:</label>
								<input id="project-name" name="project_name" class="project-name-input" type="text" tabindex="1" value="<?php echo $project->getValueEncoded("project_name")?>" />
							</li>
							<li class="entity-details-item project-code project">
								<label for="project_code" <?php validateField("project_code", $missingFields)?> class="entity-details-label">Project Code</label>
								<input id="project-code" name="project_code" class="project-code-input" type="text" tabindex="2" value="<?php echo $project->getValueEncoded("project_code")?>" />
							</li>
							<li class="entity-details-item project-client project">
								<label for="client_id" class="entity-details-label">Select the client:</label>
								<select name="client_id" id="project-client-select" size="1">    
									<?php 
										//get the clients out to populate the drop down.
										list($clients) = Client::getClients();
										foreach ($clients as $client) { ?>
										<option value="<?php echo $client->getValue("client_id") ?>" <?php setSelected($client, "client_id", $project->getValue("client_id")) ?>><?php echo $client->getValue("client_name")?></option>
									<?php } ?>
								</select>
							</li>
							<!--li class="entity-details-item project-archive project">
								<label for="project-archived" <?php validateField("project_archived", $missingFields)?> class="entity-details-label">Archived project?</label>
								<input id="project-archived" name="project-archived" class="project-archive-input" type="text" tabindex="3" value="<?php echo $project->getValueEncoded("project_archived")?>" /-->
							</li>
						</ul>
					</section>
					<!--DEMO TO SHOW THE FIELDS WORK-->
					<?php //took these out for now. Do not expose them in the UI!?>
					<h4>Invoice Methods:</h4>
					<li class="billable">
						<input type="radio" id="project_billable" name="project_billable" class="client-email-input" tabindex="3" value="0" <?php setChecked($project, "project_billable", 0) ?>/>
						<label for="project_billable" class="client-details-label">This project is not billable</label><br/>
						<input type="radio" id="project_billable" name="project_billable" class="client-email-input" tabindex="3" value="1" <?php setChecked($project, "project_billable", 1) ?>/>
						<label for="project_billable" class="client-details-label">This project is billable and we invoice by:</label>
					</li>
					<?php
						$row = Project::getEnumValues("project_invoice_by");
						$enumList = explode(",", str_replace("'", "", substr($row['COLUMN_TYPE'], 5, (strlen($row['COLUMN_TYPE'])-6))));
						?>
						<select name="project_invoice_by" onchange="showProjectHourlyRate(this)">
						<?php
						foreach($enumList as $value) { ?>
							<option name="project_invoice_by" value="<?php echo $value?>"><?php echo $value ?></option>
						<?php } ?>
						</select>    <input id="project_hourly_rate" name="project_hourly_rate" style="width:50px;display:none;" value="$"/>

						<h4>Budget</h4>
						<?php
						$row = Project::getEnumValues("project_budget_by");
						$enumList = explode(",", str_replace("'", "", substr($row['COLUMN_TYPE'], 5, (strlen($row['COLUMN_TYPE'])-6))));
						?>
						<select name="project_budget_by" onchange="showBudgetFields(this)">
						<?php
						foreach($enumList as $value) { ?>
							<option name="project_budget_by" value="<?php echo $value?>"><?php echo $value ?></option>
						<?php } ?>
						</select>     <input id="project_budget_total_hours" name="project_budget_total_hours" style="width:50px;display:none;" value="Hours"/>
						<input id="project_budget_total_fees" name="project_budget_total_fees" style="width:50px;display:none;" value="$"/><br/>
						<input type="checkbox" id="project_budget_includes_expenses" name="project_budget_includes_expenses" style="display:none;"tabindex="3" />
						<div style="display:none;" id="project_budget_includes_expenses_label" for="project_budget_includes_expenses_label" class="project_budget_includes_expenses_label" >Budget includes project expenses</div>						
						<li class="invoice_instructions">
						<input type="checkbox" id="project_show_budget" name="project_show_budget" class="project_show_budget" tabindex="3" />
						<label for="project_show_budget" class="project_show_budget">Show budget report to all employees and contractors on this project</label><br/>
						<input type="checkbox" id="project_send_email" name="project_send_email" tabindex="3" />
						<label for="project_email" class="client-details-label">Send email alerts if project reaches	<input id="project_send_email_percentage" name="project_send_email_percentage" style="width:50px;" value=""/> of budget</label>
						</li>
					<!--END DEMO-->
					
					<section id="project-info" class="entity-detail">
						<h2 class="entity-sub-title">Project Notes</h2>
						<ul class="details-list entity-details-list project">
							<li class="entity-details-item project-notes project"><label for="project_notes" <?php validateField("project_notes", $missingFields)?> class="entity-details-label">Project Notes:</label>
							<textarea id="project-notes" name="project_notes" class="entity-details-block" tabindex="4"><?php echo $project->getValueEncoded("project_notes")?></textarea></li>
						</ul>
					</section>
					<!--section id="project-info" class="entity-detail">
						<h2 class="entity-sub-title">Invoicing Method</h2>
						<ul class="details-list entity-details-list project">
							<li class="entity-details-item invoicing project">
								<label for="project-billable" class="entity-details-label">Is this project billable?</label>
								<input id="project-billable" name="project-billable" class="project-billable" type="radio" tabindex="5"/> Yes.
								<input id="project-billable" name="project-billable" class="project-billable" type="radio" checked="checked" tabindex="6" /> No.
							</li>
							<li class="entity-details-item invoicing project">
								<label for="invoice-method" class="entity-details-label" tabindex="7">Invoice project by:</label>
								<select id="invoice-method" name="invoice-method" class="">
									<option value="task-hourly">Task hourly rate</option>
									<option value="person-hourly">Person hourly rate</option>
									<option value="project-hourly">Project hourly rate</option>
									<option value="no-rate" selected="selected">No hourly rate applied</option>
								</select>
							</li>
							<li class="entity-details-item invoicing project">
								<label for="project-hourly-rate" class="entity-details-label">Project hourly rate is:</label>
								<input id="project-hourly-rate" name="project-hourly-rate" class="project-hourly-rate" type="text" tabindex="8"/>
							</li>
						</ul>
					</section>
					<section id="project-budget" class="entity-detail">
						<h2 class="entity-sub-title">Budget</h2>
						<ul class="entity-list entity-details-list">
							<li class="entity-details-item">
								<label for="project-budget" class="entity-details-label">Project budget uses:</label>
								<select id="budget-method" name="budget-method" class="" tabindex="9">
									<option value="total-hours">Total project hours</option>
									<option value="total-fees">Total project fees</option>
									<option value="task-hours">Hours per task</option>
									<option value="person-hours">Hours per person</option>
									<option value="no-budget" selected="selected">No budget</option>
								</select>
							</li>
							<li class="entity-details-item">
								<label for="project-budget-view-permissions" class="entity-details-label">Who can view project?</label>
								<input id="project-budget-view-permissions" name="project-budget-view-permissions" class="project-budget" type="radio" value="employees" tabindex="10"/> Employees
								<input id="project-budget-view-permissions" name="project-budget-view-permissions" class="project-budget" type="radio" value="contractors" tabindex="11" /> Contractors
								<input id="project-budget-view-permissions" name="project-budget-view-permissions" class="project-budget" type="radio" checked="checked" value="all" tabindex="11" /> Both
							</li>
							<
<li class="entity-details-item">
								<label for="project-budget-email" class="entity-details-label">Send email?</label>
							</li>
-->
						</ul>
					</section>
					<ul class="page-controls-list team">
						<li class="entity-details-item submit-btn client">
							<label for="project-save-btn" class="entity-details-label project">All done?</label>
							<input id="project-save-btn" name="project-save-btn" class="save-btn" type="submit" value="+ Save Changes" tabindex="12" /> or <a class="" href="projects.php" tabindex="13">Cancel</a>
						</li>
					</ul>
				</fieldset>
			</article>
			<article id="tasks" class="entity-detail tasks">
				<fieldset class="entity-details-entry">
					<header class="entity-details-header project">
						<h1 class="entity-details-title">Project Tasks:</h1>
						<h4 class="required">= Required</h4>
					</header>
					<?php //BEGIN TASKS 
							//obviously this is just the beginning of how this should ultimately work.
							//retrieve the tasks for this project.
					?>
					<ul class="entity-list entity-sub-details-list">
						<li class="entity-details-item">
							<label for="" <?php validateField("task_id", $missingFields)?> class="entity-details-label">Tasks currently assigned to project:</label>
							<table id="task-list" class="entity-table tasks tablesorter">
								<thead>
									<tr>
										<!-- you can also add a placeholder using script; $('.tablesorter th:eq(0)').data('placeholder', 'hello') -->
										<th data-placeholder="Try B*{space} or alex|br*|c" class="filter-match">Task</th>
										<th class="filter-false" data-placeholder="Try <d">Remove From Project</th>
									</tr>
								</thead>
								<tbody>
								<?php
									//get out all of the tasks associated with this project.
									list($tasksForProject) = Project_Task::getTasksForProject($project_id);
									//$taskList = "";
								
									foreach ($tasksForProject as $projectTask) { ?>
										<tr>
											<td><?php	echo $projectTask->getValue("task_name"); ?></td>
											<td><a href="#" class="remove-btn"></a></td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</li>

						<input id="client-phone" name="task_id" class="client-phone-input" type="text" tabindex="2" value="<?php //NOTE TO ALL THOSE THAT FOLLOW HERE:
											//this should NOT work this way, it is only here to show the demo to MB. This is not the way these fields should work at ALL.
											list($tasksForProject) = Project_Task::getTasksForProject($project_id);
											foreach ($tasksForProject as $projectTask) { 
												echo $projectTask->getValue("task_id") . ",";
											}
											?>" />
				</li>
						<li class="entity-details-item">
							<label for="task_ids" class="entity-details-label">Add additional tasks:</label>
							<?php 
								//get the tasks out to populate the drop down.
								list($tasks) = Task::getTasks("0");
							?>
							<select name="task_ids" id="task_ids" size="1" onchange="FillTasks(this.form); return false;">    
								<?php foreach ($tasks as $task) { ?>
		   						<option value="<?php echo $task->getValue("task_id") ?>"><?php echo $task->getValue("task_name")?></option>
		    					<?php } ?>
			 				</select>
						</li>
						<ul class="page-controls-list tasks">
							<li class="entity-details-item submit-btn tasks">
								<label for="project-save-btn" class="entity-details-label project">All done?</label>
								<input id="project-save-btn" name="project-save-btn" class="save-btn" type="submit" value="+ Save Changes" tabindex="12" /> or <a class="projects.php" href="projects.php" tabindex="13">Cancel</a>
							</li>
						</ul>
					</ul>
				</fieldset>
			</article>
			<article id="people" class="entity-detail tasks">
				<fieldset class="entity-details-entry">
					<header class="entity-details-header people">
						<h1 class="entity-details-title">Project Team:</h1>
						<h4 class="required">= Required</h4>
					</header>
					<ul class="entity-list entity-sub-details-list">
						<li class="entity-details-item">
							<label for="" <?php validateField("person_id", $missingFields)?> class="entity-details-label">People currently assigned to project:</label>
							<?php //BEGIN PEOPLE
								//obviously this is just the beginning of how this should ultimately work.
							?>
							<table id="people-list" class="entity-table people tablesorter">
								<thead>
									<tr>
										<!-- you can also add a placeholder using script; $('.tablesorter th:eq(0)').data('placeholder', 'hello') -->
										<th data-placeholder="Try B*{space} or alex|br*|c" class="filter-match">Team Member</th>
										<th class="filter-false" data-placeholder="Try <d">Remove From Project</th>
									</tr>
								</thead>
								<tbody>
									<?php
										//get out all of the people associated with this project.
										list($peopleForProject) = Project_Person::getPeopleForProject($project_id);
										//$peopleList = "";
										foreach ($peopleForProject as $projectPerson) { ?>
											<tr>
												<td><?php 
												$personName = Person::getPersonById($projectPerson->getValue("person_id"));
												echo $personName->getValue("person_first_name") . " ";
												echo $personName->getValue("person_last_name");
												?></td>
												<td><a href="#" class="remove-btn"></a></td>
											</tr>
										<?php } ?>
								</tbody>
							</table>
						</li>
											<input id="client-phone" name="person_id" class="client-phone-input" type="text" tabindex="2" value="<?php
											//NOTE TO ALL THOSE THAT FOLLOW HERE:
											//this should NOT work this way, it is only here to show the demo to MB. This is not the way these fields should work at ALL.
											list($peopleForProject) = Project_Person::getPeopleForProject($project_id);
											foreach ($peopleForProject as $projectPerson) { 
												$personName = Person::getPersonById($projectPerson->getValue("person_id"));
												echo $projectPerson->getValue("person_id") . ",";
											}
											?>
											" />

						<li class="entity-details-item">
							<label for="person_ids" class="entity-details-label">Add additional people:</label>
							<?php 
								//get the people out to populate the drop down.
								list($people) = Person::getPeople();
							?>
							<select name="person_ids" id="person_ids" size="1" onchange="FillPeople(this.form); return false;">    
								<?php foreach ($people as $person) { ?>
		   						<option value="<?php echo $person->getValue("person_id") ?>"><?php echo $person->getValue("person_first_name");echo " " . $person->getValue("person_last_name")?></option>
		    					<?php } ?>
			 				</select>
						</li>
					</ul>
					<ul class="page-controls-list team">
						<li class="entity-details-item submit-btn client">
							<label for="project-save-btn" class="entity-details-label project">All done?</label>
							<input id="project-save-btn" name="project-save-btn" class="save-btn" type="submit" value="+ Save Changes" tabindex="12" /> or <a class="projects.php" href="#" tabindex="13">Cancel</a>
						</li>
					</ul>
				</fieldset>
			</article>
		</form><!--END FORM-->
	</div>
</div>
<footer id="site-footer" class="site-footer">

</footer>
<script src="client-controls.js" type="text/javascript"></script>
</body>
</html><?php } ?>

<?php 
function editProject() {
	error_log(print_r($_POST,true));
	//PROJECT PROCESSING FUNCTIONS (editProjects();)
	//1. Set up the required fields.
	//2. Create the object based on the values that were submitted the last time the user submitted the form.
	//3. Set up the required fields in the $requiredFields array.
	//4. Compare the existence of the fields in the objects (based on the $_POST values) with the fields in the $requiredFields array. If
	//any are missing, put the fields into the $missingFields[] array.
	//5. If the $missingFields array exists, loop through them and call the error message. If there are NO missing fields, still call the error message for the NON missing field errors (email, phone, etc).
	//6. If there are error messages, call displayProjectEditForm with the error messages, the missing fields, and all the data for the object and the whole thing starts over again.
	//7. If there are no errors, update the database with the new project information.
	//8. If all went well, display the project details page.

	$requiredFields = array("project_name");
	$missingFields = array();
	$errorMessages = array();
		
	//EDIT THE PROJECT OBJECT ($PROJECT)
	$project = new Project( array(
		"project_code" => isset($_POST["project_code"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["project_code"]) : "",
		"project_id" => isset($_POST["project_id"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["project_id"]) : "",
		"project_name" => isset($_POST["project_name"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["project_name"]) : "",
		"client_id" => isset($_POST["client_id"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["client_id"]) : "",
		"project_billable" => isset($_POST["project_billable"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["project_billable"]) : "",
		"project_invoice_by" => isset($_POST["project_invoice_by"])? preg_replace("/[^ a-zA-Z]/", "", $_POST["project_invoice_by"]) : "",
		"project_hourly_rate" => isset($_POST["project_hourly_rate"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["project_hourly_rate"]) : "",
		"project_budget_by" => isset($_POST["project_budget_by"])? preg_replace("/[^ a-zA-Z]/", "", $_POST["project_budget_by"]) : "",
		"project_budget_total_fees" => isset($_POST["project_budget_total_fees"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["project_budget_total_fees"]) : "",
		"project_budget_total_hours" => isset($_POST["project_budget_total_hours"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["project_budget_total_hours"]) : "",
		"project_send_email_percentage" => isset($_POST["project_send_email_percentage"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["project_send_email_percentage"]) : "",
		"project_show_budget" => isset($_POST["project_show_budget"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["project_show_budget"]) : "",
		"project_budget_includes_expenses" => isset($_POST["project_budget_includes_expenses"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["project_budget_includes_expenses"]) : "",
		"project_notes" => isset($_POST["project-notes"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["project-notes"]) : "",
		"project_archived" => isset($_POST["project_archived"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["project_archived"]) : "",
	));

	//edit the project_person object ($project_person)
	$project_person = new Project_Person( array(
		"person_id" => isset($_POST["person_id"]) ? preg_replace("/[^ \,\-\_a-zA-Z0-9]/", "", $_POST["person_id"]) : "",
		"total_budget_hours" => isset($_POST["total_project_hours"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["total_project_hours"]) : "",
	));
	//edit the project_task object ($project_task)
	$project_task = new Project_Task( array(
		"task_id" => isset($_POST["task_id"]) ? preg_replace("/[^ \,\-\_a-zA-Z0-9]/", "", $_POST["task_id"]) : "",
		"total_budget_hours" => isset($_POST["total_project_hours"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["total_project_hours"]) : "",
	));

	
	error_log("here are the values in the POST array");
	error_log(print_r($_POST,true));
	error_log("Here are the values in the client array.");
	error_log(print_r($project,true));	
		
//error messages and validation script.
//these errors may happen in the client OR the contact object, so we have to
//call each separately.
	foreach($requiredFields as $requiredField) {
		if ( !$project->getValue($requiredField)) {
			$missingFields[] = $requiredField;
		}
					
	}
	
	
	if ($missingFields) {
		$i = 0;
		$errorType = "required";
		foreach ($missingFields as $missingField) {
			$errorMessages[] = "<li>" . getErrorMessage(1,$missingField, $errorType) . "</li>";
			$i++;
		}
	} else {
		//TAKE THIS OUT FOR NOW AND DEAL WITH IT LATER!!
		/*$clientEmail = $client->getValue("client_email");
		$clientPhone = $client->getValue("client_phone");
		$clientZip = $client->getValue("client_zip");
		$contactEmail = $contact->getValue("contact_email");
		$contactOfficePhone = $contact->getValue("contact_office_number");
		$contactMobilePhone = $contact->getValue("contact_mobile_number");
		$contactFax = $contact->getValue("contact_fax_number");
		
		
		// validate the email addresses
		if(!preg_match("/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$/i", $clientEmail)) {
			$errorMessages[] = "<li>" . getErrorMessage(1,"client_email", "invalid_input") . "</li>";
		}
		if(!preg_match("/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$/i", $contactEmail)) {
			$errorMessages[] = "<li>" . getErrorMessage(1,"contact_email", "invalid_input") . "</li>";
		}
		
		// validate the phone numbers
		if(!preg_match("/^([1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$/i", $clientPhone)) {
			$errorMessages[] = "<li>" . getErrorMessage(1,"client_phone", "invalid_input") . "</li>";
		}
		if(!preg_match("/^([1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$/i", $contactOfficePhone)) {
			$errorMessages[] = "<li>" . getErrorMessage(1,"contact_office_number", "invalid_input") . "</li>";
		}
		if(!preg_match("/^([1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$/i", $contactMobilePhone)) {
			$errorMessages[] = "<li>" . getErrorMessage(1,"contact_mobile_number", "invalid_input") . "</li>";
		}
		if(!preg_match("/^([1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$/i", $contactFax)) {
			$errorMessages[] = "<li>" . getErrorMessage(1,"contact_fax_number", "invalid_input") . "</li>";
		}
		
		//validate the zip code
		if (!preg_match ("/^[0-9]{5}$/", $clientZip)) {
			$errorMessages[] = "<li>" . getErrorMessage(1,"client_zip", "invalid_input") . "</li>";
		}	*/
	}
		
	if ($errorMessages) {
		error_log("There were errors in the input (errorMessages was not blank. Redisplaying the edit form with missing fields.");
		displayProjectEditForm($errorMessages, $missingFields, $project, $project_person, $project_task);
	} else {
		try {
			$client_id = $project->getValue("client_id");
			//update the project into the project table.
			$project->updateProject($client_id);
			$project_id = Project::getProjectId($project->getValue("project_name"));
			$person_ids = explode(',', $project_person->getValue("person_id"));
			//delete all of the rows in the table associated with this project.
			Project_Person::deleteProjectPerson($project_id["project_id"]);
			//add the new ones, since we don't know how many people will be deleted and how many will be added.
			foreach ($person_ids as $person_id) {
			if ($person_id) {
				$project_person->insertProjectPerson($person_id, $project_id["project_id"]);
			}
			}
			//do the same for tasks.
			$task_ids = explode(',', $project_task->getValue("task_id"));
			Project_Task::deleteProjectTask($project_id["project_id"]);
			foreach ($task_ids as $task_id) {
			if ($task_id) {	
				$project_task->insertProjectTask($task_id, $project_id["project_id"]);
			}
			}

			displayProjectEditForm(array(), array(), $project, $project_person, $project_task);
		} catch (Error $e) {
			die("could not insert a project. " . $e->getMessage());
			
		}
	}

}

?>
