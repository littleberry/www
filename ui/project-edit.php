<?php
	//this shouldn't be necessary. headers are NOT sent yet if this is coded correctly.
	//function displayProjectPage() {
	//	//this probably isn't right, but I'll use it for now. won't work if JavaScript is off.
	//	printf("<script>location.href='projects.php'</script>");
	//}
	
	require_once("../common/common.inc.php");
	require_once("../classes/Project.class.php");
	require_once("../classes/Client.class.php");
	require_once("../classes/Project_Person.class.php");
	require_once("../classes/Project_Task.class.php");
	require_once("../classes/Project.class.php");
	require_once("../classes/Task.class.php");

	

	//OVERALL CONTROL
	//	1. first time user comes in, call the displayClientAndContactsEditForm function.
	//	2. Set the client and contact objects to the value pulled from the database.
	//	3. User clicks on a button to submit the form, call the editClientAndContacts function.
	//	4. If required fields are missing in the form, re-display the form with error messages.
	//	5. If there are no missing required fields, call Project::updateProject-->	
 			
				if (isset($_POST["action"]) and $_POST["action"] == "edit_project") {
					editProject();
				} else {
					displayProjectEditForm(array(), array(), new Project(array()), new Project_Person(array()), new Project_Task(array()));
				}
	
	/*DISPLAY PROJECT EDIT WEB FORM (displayProjectEditForm)
	note...I think we can remove the PHP validation to update the style in validateField
	1. This is the form displayed to the user, the first time the user comes in it gets the client_id out of the $_GET variable (please encode!!)
	2. If first time, pull the project object from the database.
	3. on reocurring pulls, error messages may or may not be there, based on the user's input, object details will come from the $_POST variable.*/
?>	
<?php function displayProjectEditForm($errorMessages, $missingFields, $project) {
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
<!DOCTYPE html>
<section id="page-content" class="page-content">
	<header class="page-header">
		<h1 class="page-title">Edit Project Details</h1>
		<nav class="page-controls-nav">
			<ul class="client-page-controls">
				<li class="page-controls-item link-btn"><a class="add-client-link" href="project-add.php">+ Edit Project</a></li>
				<li class="page-controls-item"><a class="view-client-archive-link" href="projects.php?archives=1">View Archives</a></li>
				<li class="page-controls-item"><a class="view-all-link" href="projects.php">View All</a></li>
			</ul>
		</nav>
	</header>
	<section class="content">
	<!--BEGIN FORM-->
	<form action="project-edit.php" method="post" style="margin-bottom:50px;" enctype="multipart/form-data">
	<input type="hidden" name="action" value="edit_project">
	<input type="hidden" name="project_id" value="<?php echo $project_id ?>">
		<section class="client-detail l-col-80">
			<fieldset class="client-details-entry">
				<!-- <legend class="client-details-title">Edit client details:</legend> -->
				<header class="client-details-header">
					<h1 class="client-details-title">Edit project details:</h1>
					<h4 class="required">= Required</h4>
				</header>
				<ul class="details-list client-details-list">
				<?php 
						//get the clients out to populate the drop down in the edit UI.
						list($clients) = Client::getClients();
					?>
					<li class="client-details-item address">
						<label for="client-zip" <?php validateField("project_name", $missingFields)?> class="client-details-label">Project Name:</label>
						<input id="client-zip" name="project-name" class="client-zip-input" type="text" tabindex="8" value="<?php echo $project->getValueEncoded("project_name")?>" /><br />
						<label for="client-city" <?php validateField("project_code", $missingFields)?> class="client-details-label">Project Code</label>
						<input id="client-city" name="project_code" class="client-city-input" type="text" tabindex="6" value="<?php echo $project->getValueEncoded("project_code")?>" /><br />
						<li class="client-details-item currency">
						<label for="client-currency" class="client-details-label">Please select a client:</label>
                        <select name="client-id" id="client_currency_index" size="1">    
						<?php foreach ($clients as $client) { ?>
   							<option value="<?php echo $client->getValue("client_id") ?>"><?php echo $client->getValue("client_name")?></option>
    					<?php } ?>
    			 </select><br />
  				<?php //took these out for now. Do not expose them in the UI!?>
    			 <!--label for="client-currency" class="project-billable">Invoice Method:</label><br/>
    			 <input id="project-billable" name="project-billable" class="project-billable" value="N" type="radio" <?php //setChecked($contacts, "contact_primary", "1") ?> />This project is not billable<br/>
						<input id="project-billable" name="project-billable" class="project-billable" value="Y" type="radio" <?php //setChecked($contacts, "contact_primary", "1") ?> />This project is billable and we invoice by<br/-->
						<label for="client-streetAddress" <?php validateField("project_notes", $missingFields)?> class="client-details-label">Project Notes:</label>
						<textarea id="client-streetAddress" name="project-notes" class="client-streetAddress-input" tabindex="5"><?php echo $project->getValueEncoded("project_notes")?></textarea><br />
						

<br />
				</ul>
			</fieldset>
		</section>
<?php //BEGIN TASKS 
			//obviously this is just the beginning of how this should ultimately work.
		//retrieve the tasks for this project.
		
		?>
		<section class="client-detail l-col-80">
        	<fieldset class="client-details-entry">
				<legend class="client-details-title">Enter Task details:</legend>
				<header class="client-details-header">
					<h1 class="client-details-title">Enter Task details:</h1>
					<h4 class="required">= Required</h4>
				</header>
				<li class="client-details-item phoneNum">
					<label for="client-phone" <?php validateField("task_id", $missingFields)?> class="client-details-label">Tasks associated with this project:</label>
					<?php
					//get out all of the tasks associated with this project.
						list($tasksForProject) = Project_Task::getTasksForProject($project_id);
						$taskList = "";
						foreach ($tasksForProject as $projectTask) {
							$taskList = $taskList . $projectTask->getValue("task_id") . ", ";
						}
					?>
					<input id="client-phone" name="task_id" class="client-phone-input" type="text" tabindex="2" value="<?php echo $taskList ?>" />
				</li>
				<ul class="details-list client-details-list">
				<?php 
						//get the taskss out to populate the drop down.
						list($tasks) = Task::getTasks();
					?>
					<li class="client-details-item currency">
						<label for="client-currency" class="client-details-label">Please choose a task:</label>
                        <select name="task_ids" id="client_currency_index" size="1">    
						<?php foreach ($tasks as $task) { ?>
   							<option value="<?php echo $task->getValue("task_id") ?>"><?php echo $task->getValue("task_name")?></option>
    					<?php } ?>
    			 </select><br />
					</li>
				</ul>
        	</fieldset>
		</section>
		<?php //BEGIN PEOPLE
		//obviously this is just the beginning of how this should ultimately work.
		?>
		<section class="client-detail l-col-80">
        	<fieldset class="client-details-entry">
				<legend class="client-details-title">Enter Person details:</legend>
				<header class="client-details-header">
					<h1 class="client-details-title">Enter Person details:</h1>
					<h4 class="required">= Required</h4>
				</header>
				<li class="client-details-item phoneNum">
					<label for="client-phone" <?php validateField("person_id", $missingFields)?> class="client-details-label">People assigned to this project:</label>
					<?php
					//get out all of the people associated with this project.
						list($peopleForProject) = Project_Person::getPeopleForProject($project_id);
						$peopleList = "";
						foreach ($peopleForProject as $projectPerson) {
							$peopleList = $peopleList . $projectPerson->getValue("person_id") . ", ";
						}
					?>
					<input id="client-phone" name="person_id" class="client-phone-input" type="text" tabindex="2" value="<?php echo $peopleList ?>" />
				</li>
				<ul class="details-list client-details-list">
				<?php 
						//get the people out to populate the drop down.
						list($people) = Person::getPeople();
					?>
					<li class="client-details-item currency">
						<label for="client-currency" class="client-details-label">Please choose a person:</label>
                        <select name="person_ids" id="client_currency_index" size="1">    
						<?php foreach ($people as $person) { ?>
   							<option value="<?php echo $person->getValue("person_id") ?>"><?php echo $person->getValue("person_first_name");echo " " . $person->getValue("person_last_name")?></option>
    					<?php } ?>
    			 </select><br />
					</li>
				</ul>
        	</fieldset>
		</section>
				<fieldset class="client-details-entry">
				<ul class="details-list client-details-submit">
					<li class="client-details-item submit-client">
						<label for="client-add-btn" class="client-details-label">All done?</label>
                        <input id="client-add-btn" name="project-add-btn" class="client-add-btn" type="submit" value="+ Update Project" tabindex="11"/> 
						 or <a class="" href="#" tabindex="11">Cancel</a>
					</li>
				</ul>
			</fieldset>
			</section>

</form>
<footer id="site-footer" class="site-footer">

</footer>
<script src="client-controls.js" type="text/javascript"></script>
</body>
</html><?php } ?>
		

<?php 
function editProject() {
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
		//CHECK REG SUBS!!
		"project_id" => isset($_POST["project_id"]) ? preg_replace("/[^ 0-9]/", "", $_POST["project_id"]) : "",
		//not available for edit.
		"project_code" => isset($_POST["project_code"]) ? preg_replace("/[^ 0-9]/", "", $_POST["project_code"]) : "",
		"project_name" => isset($_POST["project-name"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["project-name"]) : "",
		"client_id" => isset($_POST["client-id"]) ? preg_replace("/[^ \-\_a-zA-Z^0-9]/", "", $_POST["client-id"]) : "",
		"project_billable" => isset($_POST["project-billable"]) ? preg_replace("/[^ \-\_a-zA-Z0-9^@^.]/", "", $_POST["project-billable"]) : "",
		"project_invoice_by" => isset($_POST["project-invoice-by"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["project-invoice-by"]) : "",
		"project_hourly_rate" => isset($_POST["project-hourly_rate"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["project-hourly_rate"]) : "",
		"project_budget_by" => isset($_POST["project-budget-by"])? preg_replace("/[^ \-\_a-zA-Z^0-9]/", "", $_POST["project-budget-by"]) : "",
		"project_budget_total_fees" => isset($_POST["project-budget-total-fees"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["project-budget-total-fees"]) : "",
		"project_budget_total_hours" => isset($_POST["project-budget-total-hours"])? preg_replace("/[^0-9]/", "", $_POST["project-budget-total-hours"]) : "",
		"project_send_email" => isset($_POST["project-send-email"])? preg_replace("/[^0-9]/", "", $_POST["project-send-email"]) : "",
		"project_show_budget" => isset($_POST["project-show-budget"])? preg_replace("/[^0-9]/", "", $_POST["project-show-budget"]) : "",
		"project_budget_includes_expenses" => isset($_POST["project-budget-includes-expenses"])? preg_replace("/[^0-9]/", "", $_POST["project-budget-includes-expenses"]) : "",
		"project_notes" => isset($_POST["project-notes"]) ? preg_replace("/[^ \-\_a-zA-Z^0-9]/", "", $_POST["project-notes"]) : "",
		"project_archived" => isset($_POST["project-archived"]) ? preg_replace("/[^ \-\_a-zA-Z^0-9]/", "", $_POST["project-archived"]) : "",
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
		displayClientAndContactsEditForm($errorMessages, $missingFields, $project);
	} else {
		try {
			$client_id = $project->getValue("client_id");
			//insert the project into the project table.
			$project->updateProject($client_id);
			//insert the project and the associated people into the project_people table.
			$project_id = Project::getProjectId($project->getValue("project_name"));
			$person_ids = explode(',', $project_person->getValue("person_id"));
			//try deleting all of the rows in the table and then adding the new ones, since we don't know how many people will be deleted.
			Project_Person::deleteProjectPerson($project_id[0]);
			foreach ($person_ids as $person_id) {			
				$project_person->insertProjectPerson($person_id, $project_id[0]);
			}
			$task_ids = explode(',', $project_task->getValue("task_id"));
			//try deleting all of the rows in the table and then adding the new ones, since we don't know how many tasks will be deleted.
			Project_Task::deleteProjectTask($project_id[0]);
			foreach ($task_ids as $task_id) {				
				$project_task->insertProjectTask($task_id, $project_id[0]);
			}

			displayProjectEditForm(array(), array(), new Project(array()), new Project_Person(array()), new Project_Person(array()));
		} catch (Error $e) {
			die("could not insert a project. " . $e->getMessage());
			
		}
	}

}

?>


