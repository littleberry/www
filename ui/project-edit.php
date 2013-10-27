<?php
	//this shouldn't be necessary. headers are NOT sent yet if this is coded correctly.
	//function displayProjectPage() {
	//	//this probably isn't right, but I'll use it for now. won't work if JavaScript is off.
	//	printf("<script>location.href='projects.php'</script>");
	//}
	
	require_once("../common/common.inc.php");
	require_once("../classes/Project.class.php");
	require_once("../classes/Client.class.php");


	include('header.php'); //add header.php to page
?>
<?php	/*OVERALL CONTROL
		1. first time user comes in, call the displayClientAndContactsEditForm function.
		2. Set the client and contact objects to the value pulled from the database.
		3. User clicks on a button to submit the form, call the editClientAndContacts function.
		4. If required fields are missing in the form, re-display the form with error messages.
		5. If there are no missing required fields, call Project::updateProject*/	
 			
				if (isset($_POST["action"]) and $_POST["action"] == "edit_project") {
					editProject();
				} else {
					displayProjectEditForm(array(), array(), new Project(array()));
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
	if (isset($_GET["project_id"])) {
		$project=Project::getProjectByProjectId($_GET["project_id"]);
	}
	
?>
<div id="page-content" class="page-content">
	<header class="page-header">
		<h1 class="page-title">Edit Project: Project Name<?php //echo $project_details->getValue("project_name")?></h1>
		<h2 class="page-sub-title"><a href="#" class="" title="View client's details">Client</a></h2>
		<nav class="page-controls-nav">
			<ul class="page-controls-list project">
				<li class="page-controls-item link-btn">
				<a class="view-all-link" href="#">Save Project</a></li>
				<!-- <a class="view-all-link" href="project-edit.php?project_id=<?php //echo $project_id?>">Save Project</a> --></li>
				<li class="page-controls-item"><a class="view-archive-link" href="project-archives.php">View Archives</a></li>
				<li class="page-controls-item"><a class="view-all-link" href="projects.php">View All</a></li>
			</ul>
		</nav>
	</header>


	<div class="content">
		<!--BEGIN FORM-->
		<form action="project-edit.php" method="post" style="margin-bottom:50px;" enctype="multipart/form-data">
			<input type="hidden" name="action" value="edit_project">
			<input type="hidden" name="project_id" value="<?php echo $_GET["project_id"]?>">
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
								<label for="project-name" <?php validateField("project_name", $missingFields)?> class="entity-details-label">Project Name:</label>
								<input id="project-name" name="project_name" class="project-name-input" type="text" tabindex="1" value="<?php echo $project->getValueEncoded("project_name")?>" />
							</li>
							<li class="entity-details-item project-code project">
								<label for="project-code" <?php validateField("project_code", $missingFields)?> class="entity-details-label">Project Code</label>
								<input id="project-code" name="project_code" class="project-code-input" type="text" tabindex="2" value="<?php echo $project->getValueEncoded("project_code")?>" />
							</li>
							<li class="entity-details-item project-client project">
								<label for="client-id" class="entity-details-label">Select the client:</label>
		                  <select name="client-id" id="project-client-select" size="1">    
									<?php 
										//get the clients out to populate the drop down.
										list($clients) = Client::getClients();
										foreach ($clients as $client) { ?>
										<option value="<?php echo $client->getValue("client_id") ?>"><?php echo $client->getValue("client_name")?></option>
									<?php } ?>
								</select>
							</li>
							<li class="entity-details-item project-archive project">
								<label for="project-archived" <?php validateField("project_archived", $missingFields)?> class="entity-details-label">Archived project?</label>
								<input id="project-archived" name="project-archived" class="project-archive-input" type="text" tabindex="3" value="<?php echo $project->getValueEncoded("project_archived")?>" />
							</li>
						</ul>
					</section>
					<section id="project-info" class="entity-detail">
						<h2 class="entity-sub-title">Project Notes</h2>
						<ul class="details-list entity-details-list project">
							<li class="entity-details-item project-notes project"><label for="project-notes" <?php validateField("project_notes", $missingFields)?> class="entity-details-label">Project Notes:</label>
							<textarea id="project-notes" name="project-notes" class="entity-details-block" tabindex="4"><?php echo $project->getValueEncoded("project_notes")?></textarea></li>
						</ul>
					</section>
					<section id="project-info" class="entity-detail">
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
							<li class="entity-details-item">
								<label for="project-budget-email" class="entity-details-label">Send email?</label>
							</li>
						</ul>
					</section>
					<ul class="page-controls-list team">
						<li class="entity-details-item submit-btn client">
							<label for="project-save-btn" class="entity-details-label project">All done?</label>
							<input id="project-save-btn" name="project-save-btn" class="save-btn" type="submit" value="+ Save Changes" tabindex="12" /> or <a class="" href="#" tabindex="13"><a href="projects.php">Cancel</a>
					</li>
					</ul>
				</fieldset>
			</article>
		</form><?php } ?><!--END FORM-->
	</div>
	
<footer id="site-footer" class="site-footer">

</footer>
<script src="client-controls.js" type="text/javascript"></script>
</body>
</html>

<!--PROJECT PROCESSING FUNCTIONS (editProjects();)
	1. Set up the required fields.
	2. Create the object based on the values that were submitted the last time the user submitted the form.
	3. Set up the required fields in the $requiredFields array.
	4. Compare the existence of the fields in the objects (based on the $_POST values) with the fields in the $requiredFields array. If
	any are missing, put the fields into the $missingFields[] array.
	5. If the $missingFields array exists, loop through them and call the error message. If there are NO missing fields, still call the error message for the NON missing field errors (email, phone, etc).
	6. If there are error messages, call displayProjectEditForm with the error messages, the missing fields, and all the data for the object and the whole thing starts over again.
	7. If there are no errors, update the database with the new project information.
	8. If all went well, display the project details page.
	-->
<?php function editProject() {
	$requiredFields = array("project_name");
	$missingFields = array();
	$errorMessages = array();
		
	//CREATE THE PROJECT OBJECT ($PROJECT)
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
		//error_log($contact[0]);
		//error_log(gettype($contact));
		displayClientAndContactsEditForm($errorMessages, $missingFields, $project);
	} else {
		error_log("All of the required fields are there...Updating database...");
		$project_id=$project->getValue("project_id");
		$project->updateProject($project_id);		
		displayProjectPage();	
	}
}

?>
