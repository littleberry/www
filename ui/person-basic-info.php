<?php
	//require_once($_SERVER["DOCUMENT_ROOT"] . "/usercake/models/config.php");
	require_once("../common/common.inc.php");
	require_once("../common/errorMessages.php");
	require_once("../classes/Person.class.php");
	require_once("../classes/Project.class.php");
	require_once("../classes/Project_Person.class.php");
	require_once("../classes/Person_Permissions.class.php");

				
	//checkLogin();

		
	//get the person off the URL if they came in from the add page.
	//if they came in from the login page, use the session variable since it has already been set.
	if (isset($_GET['person'])) {
		error_log("AUTH: User came in from GET and the EDIT SCREEN.");
		$person = unserialize(urldecode($_GET['person']));
		$person_perms = unserialize(urldecode($_GET['person_perms']));
		error_log("AUTH: PERSON VALUE IS " . print_r($person,true));
		//set project to null because it's blank.
		$project = "";
	} elseif (isset($_SESSION['person'])) {
		error_log("AUTH: User's session var is set because they logged in.");
		//we're not going to set this here.
		//$person = $_SESSION['person'];
		$project = "";
		//what if we get rid of the session right here?
	} else {
		echo "something is terribly wrong, we have no information about this user!!";
		exit;
	}	
		//print_r($person);
		//removed auth via userCake re:keith 10/17
		//if(!isUserLoggedIn()){
		//redirect if user is not logged in.
		//$_SESSION["redirect"] = $_SERVER["PHP_SELF"];
		//header( 'Location: ../usercake/login.php' ) ;
	//}
	
	//OVERALL CONTROL
	//I need this code to be first so I can redirect the page. We may need to do this for others			
		if (isset($_POST["action"]) and $_POST["action"] == "person-basic-info") {
				if (isset($_POST["person-add-btn"]) and $_POST["person-add-btn"] == "Save Person") {
					processPerson(0);
				} elseif (isset($_POST["person-add-btn"]) and $_POST["person-add-btn"] == "Save Projects") {
					processPerson(1);
				} elseif (isset($_POST["person-add-btn"]) and $_POST["person-add-btn"] == "Change Password") {
					processPerson(2);
				} elseif (isset($_POST["person-add-btn"]) and $_POST["person-add-btn"] == "Resend Invitation") {
					processPerson(3);
				}
		} else {
				displayPersonInsertForm(array(), array(), $person, $project, $person_perms);
		} 
?>


<!--DISPLAY PERSON INSERT WEB FORM--->
<?php function displayPersonInsertForm($errorMessages, $missingFields, $person, $project, $person_perms) { 
	//if there are errors in the form display the message
	if ($errorMessages) {
		foreach($errorMessages as $errorMessage) {
			echo $errorMessage;
		}
	}
	
include('header.php'); //add header.php to page
?>
<script type="text/javascript">
function FillProjects(f) {
    //window.alert(f);
    f.projectidselectname.value = f.projectidselectname.value + f.projectid[f.projectid.selectedIndex].text + ",";
    f.projectidselect.value = f.projectidselect.value + f.projectid.value + ",";
    //USE THIS V FUNCTION TO UPDATE A HIDDEN FIELD
    //f.projectidselect.value = f.projectidselect.value + f.projectid.value + ",";
    //f.shippingname.value;
    //f.billingcity.value = f.shippingcity.value;
    //return false;
    
}
function showP(elem){
   if(elem.value == "Regular User"){
      document.getElementById('perm_ru').style.display = "block";
      document.getElementById('perm_pm').style.display = "none";
      document.getElementById('perm_a').style.display = "none";
   } else if(elem.value == "Project Manager") {
      document.getElementById('perm_ru').style.display = "none";
      document.getElementById('perm_pm').style.display = "block";
      document.getElementById('perm_a').style.display = "none";
   } else if(elem.value == "Administrator") {  
   	 document.getElementById('perm_ru').style.display = "none";
     document.getElementById('perm_pm').style.display = "none";
     document.getElementById('perm_a').style.display = "block";
	}
}

</script>
<section id="page-content" class="page-content">
	<header class="page-header">
	<figure class="person-logo l-col-20">
		<?php
		//get the image out of the db. it is the default if the user hasn't udpated it yet, this would be the case if
		//they came from the add screen.
		?>
			<img class="person-logo-img small" src="<?php echo "images/" . basename($person->getValue("person_logo_link"))?>" style="height:100px; width:100px;" title="Person Image" alt="Person image" />
			
		</figure>
		<h1 class="page-title"><?php echo $person->getValueEncoded("person_first_name")?>'s Basic Info</h1>
		<nav class="page-controls-nav">
			<ul class="client-page-controls">
				<!--li class="page-controls-item add-client-button"><a class="add-client-link" href="client-add.php">+ Add Person</a></li-->
				<!--<li class="page-controls-item add-client-button"><a class="add-client-link" href="client-add.html">+ Add Client</a></li>-->
				<!--li class="page-controls-item"><a class="view-client-archive-link" href="client-archives.php">View Archives</a></li-->
			</ul>
		</nav>
	</header>
	<section class="content">
    <!--added because we need the information to be submitted in a form-->
    <?php
    /*OK, NOT SURE HOW THIS UI IS GOING TO END UP.*/
    /*SO, I'M GOING TO LEAVE THIS AS ONE FORM */
    /*AND PUT IT INTO TWO FORMS IF THE UI REQUIRES THAT.*/
    ?>
      <form action="person-basic-info.php" method="post" style="margin-bottom:50px;" enctype="multipart/form-data">
      <input type="hidden" name="action" value="person-basic-info"/>
      
    <!--end add-->
		<!--<figure class="client-logo l-col-20">
			<img class="client-logo-img small" src="images/default.jpg" title="Client/Company name logo" alt="Client/Company name logo" />
			<fieldset class="client-logo-upload">
				<legend class="client-logo-title">Upload Client Logo</legend>
				<header class="client-logo-header">
					<h1 class="client-logo-title">Upload Client Logo</h1>
				</header>
				<input id="client-logo-file" name="client-logo-file" class="client-logo-file" type="file" value="Browse" />
				<input id="client-logo-upload-btn" name="client-logo-upload-btn" class="client-logo-upload-btn" type="button" value="Upload" /> or <a class="" href="#">Cancel</a>
			</fieldset>
		</figure>-->

		<section class="client-detail l-col-80">
        	<fieldset class="client-details-entry">
				<ul class="details-list client-details-list">
		   			<li class="client-details-item name">
		   			This person is a: 
		   				<?php 
		   				$row = Person::getEnumValues("person_type");																					  						$enumList = explode(",", str_replace("'", "", substr($row['COLUMN_TYPE'], 5, (strlen($row['COLUMN_TYPE'])-6))));
			   			foreach ($enumList as $type) {	?>
			 				<input type="radio" name="person-type" value="<?php echo $type?>" <?php setChecked($person, "person_type", $type) ?>>   <?php echo $type ?>
		   				<?php }	
			   				//need to keep the person_id!
		   				?><br/>
		   				 <input type="hidden" name="person_id" value="<?php echo $person->getValueEncoded("person_id")?>"/>

						<label for="client-name" <?php validateField("person_first_name", $missingFields)?> class="client-details-label">First Name:</label>
						<input id="client-name" name="person-first-name" class="client-name-input" type="text" tabindex="1" value="<?php echo $person->getValueEncoded("person_first_name")?>" /><br />
					</li>
					<li class="client-details-item phoneNum">
						<label for="client-phone" <?php validateField("person_last_name", $missingFields)?> class="client-details-label">Last Name:</label>
						<input id="client-phone" name="person-last-name" class="client-phone-input" type="text" tabindex="2" value="<?php echo $person->getValueEncoded("person_last_name")?>" />
					</li>
					<li class="client-details-item email">
						<label for="client-email" <?php validateField("person_email", $missingFields)?> class="client-details-label">Email:</label>
						<input id="client-email" name="person-email" class="client-email-input" type="text" tabindex="3" value="<?php echo $person->getValueEncoded("person_email")?>" />
					</li>
					<li class="client-details-item email">
						<label for="client-city" <?php validateField("person_department", $missingFields)?> class="client-details-label">Department</label>
						<input id="client-city" name="person-department" class="client-city-input" type="text" tabindex="6" value="<?php echo $person->getValueEncoded("person_department")?>" /><br />
					</li>
					<li class="client-details-item email">
						<label for="client-zip" <?php validateField("person_hourly_rate", $missingFields)?> class="client-details-label">Hourly Rate:</label>
						$<input id="client-zip" name="person-hourly-rate" class="client-zip-input" type="text" tabindex="8" value="<?php echo $person->getValueEncoded("person_hourly_rate")?>" /><br />
					</li>
					<li class="client-details-item email">
						<label for="client-zip" <?php validateField("person_perm_id", $missingFields)?> class="client-details-label">Permissions:</label>
						<?php
						$row = Person::getEnumValues("person_perm_id");
						$enumList = explode(",", str_replace("'", "", substr($row['COLUMN_TYPE'], 5, (strlen($row['COLUMN_TYPE'])-6))));
						?>
						<?php //yeah, this is super lame. ?>
						<select id='person-perm-id' name="person-perm-id" onchange="showP(this)">
						<?php
						foreach($enumList as $value) { ?>
							<!--this needs to use person_perms!-->
							<option id='person-perm-id' name="person-perm-id" value="<?php echo $value?>" <?php setSelected($person, "person_perm_id", $value) ?>><?php echo $value ?></option>
						<?php } ?>
						</select>
						<p id="perm_ru" style="display: none;">This person can track time and expenses.</p>
						<div id="perm_pm" style="display: none;">
						<input type="checkbox" name="create_projects" id="create_projects" <?php setChecked($person_perms, "create_projects", 1) ?>>Create projects for all clients<br>
						<input type="checkbox" name="view_rates" id="view_notes" <?php setChecked($person_perms, "view_rates", 1) ?>>View rates<br>
						<input type="checkbox" name="create_invoices" id="create_invoices" <?php setChecked($person_perms, "create_invoices", 1) ?>>Create invoices for projects they manage<br>
						</div>
						<p id="perm_a" style="display: none;">This person can see all projects, invoices and reports in Time Tracker.</p>
					</li>
					<fieldset class="person-logo-upload">
				<!--legend class="person-logo-title">Upload Person Image</legend-->
				<header class="person-logo-header">
					<h1 class="person-logo-title">Upload Person Image</h1>
				</header>
				<?//hack the image by using a hidden field.?>
				<?//this is where we put the image into the post array!?>
				<input type="hidden" id="person-logo-file" name="person-logo-file" value="<?php echo $person->getValue("person_logo_link")?>">
				<input id="person-logo-file" name="person-logo-file" class="person-logo-file" type="file" value="browse" tabindex="21" />
			</fieldset>
			
				<fieldset class="client-details-entry">
				<ul class="details-list client-details-submit">
					<li class="client-details-item submit-client">
						<label for="client-add-btn" class="client-details-label">All done?</label>
						<!--modified field to be of type submit instead of button-->
                        <input id="client-add-btn" name="person-add-btn" class="client-add-btn" type="submit" value="Save Person" tabindex="11"/> 
						 or <a class="" href="people.php" tabindex="11">Cancel</a></li>
						 <li>
						 <?php $person_id=$person->getValue("person_id");?>
						 <input id="client-delete-btn" name="person-delete-btn" class="client-delete-btn" onclick="window.open('delete_person.php?person_id=<?php echo $person_id ?>','myWindow','width=200,height=200,left=250%,right=250%,scrollbars=no')" type="button" value="- Delete Person" tabindex="11" />	
					</li>
				</ul>
			</fieldset>
			
	<section class="content">
		<fieldset class="client-details-entry">
				<legend class="client-details-title"><?php echo $person->getValueEncoded("person_first_name")?>'s Projects</legend>
				<header class="client-details-header">
					<h1 class="client-details-title"><?php echo $person->getValueEncoded("person_first_name")?>'s Projects</h1>
				</header>
				
        	</fieldset>


		<form action="person-basic-info.php" method="post" style="margin-bottom:50px;" enctype="multipart/form-data">
		<label for="client-zip" class="client-details-label"></label>
		<input id="projectidselectname" name="projectidselectname" class="projectidselectname" type="text" tabindex="8" value="<?php
		//output all of this users current projects in name form.
		list($projectForPerson) = Project_Person::getProjectsForPerson($person->getValue("person_id"));
		foreach ($projectForPerson as $projectPerson) {
				 echo $projectPerson->getValue("project_name") . ",";
		} 
		//output all of this users current projects in id form, this is what the database needs. :) HIDE THIS!!
		?>">
		<input id="projectidselect" name="projectidselect" class="projectidselect" type="text" tabindex="8" value="<?php
		//output all of this users current projects.
		list($projectForPerson) = Project_Person::getProjectsForPerson($person->getValue("person_id"));
		
		foreach ($projectForPerson as $projectPerson) {
				 echo $projectPerson->getValue("project_id") . ",";
		} ?>">
		<button name="Assign New Projects" onclick="FillProjects(this.form); return false;" >Assign New Projects</button>
		<br />
		<?php		//this is the select box.
					list($projects) = Project::getProjects();?>
					<select name="projectid" id="projectid" size="1">    
						<?php foreach ($projects as $project) { ?>
   							<option value="<?php echo $project->getValue("project_id") ?>" text="<?php echo $project->getValue("project_name")?>"><?php echo $project->getValue("project_name")?></option>
    					<?php } ?>
    			 </select><br />

					<ul>
					<?php 
					//get out all of the people associated with this project.
					if ($projectForPerson) {
						echo("<br/>" . $person->getValue("person_first_name") . " has the following assigned projects:<br/>");
						foreach ($projectForPerson as $projectPerson) {?>
							<li class="client-details-item phoneNum"><?php echo $projectPerson->getValue("project_name")?></li>
						<?php } ?>
					<?php } else {
							echo("<br/>" . $person->getValue("person_first_name") . " doesn't have any projects assigned yet.");
						}
					?>
					</ul>
				<fieldset class="client-details-entry">
				<ul class="details-list client-details-submit">
					<li class="client-details-item submit-client">
						<label for="client-add-btn" class="client-details-label">All done?</label>
						<!--modified field to be of type submit instead of button-->
                        <input id="client-add-btn" name="person-add-btn" class="client-add-btn" type="submit" value="Save Projects" tabindex="11"/> 
						 or <a class="" href="people.php" tabindex="11">Cancel</a>
					</li>
				</ul>
			</fieldset>
</section>
<header class="page-header">
<?php
	//if the password is not set up, resend the invitation. Otherwise, change the password.
	//use the email here, since it is unique.
	$password_is_set = Person::isPasswordSet($person->getValue("person_email"));
	if (!$password_is_set) {
	?>
			<h1 class="page-title">Resend <?php echo $person->getValueEncoded("person_first_name")?>'s Invitation</h1>
			<ul class="details-list client-details-submit">
					<li class="client-details-item submit-client">
						<label for="client-add-btn" class="client-details-label"></label>
                        <input id="client-add-btn" name="person-add-btn" class="client-add-btn" type="submit" value="Resend Invitation" tabindex="11"/> 
						 or <a class="" href="people.php" tabindex="11">Cancel</a>
					</li>
				</ul>
	<?php } else {?>
			<h1 class="page-title">Change Your Password</h1>
	<input type="hidden" name="emailAddress" value="<?php echo $person->getValue("person_email")?>"/>
	<div style="width:30em;">
    <label for="password1" class="required">Choose a password</label>
    <input type="password" name="password1" id="password1" value="" /><br/>
    <label for="password2" class="required">Retype password</label>
    <input type="password" name="password2" id="password2" value="" /><br/>
    <div style="clear:both">
    </div>
    </div>
			<ul class="details-list client-details-submit">
					<li class="client-details-item submit-client">
						<label for="client-add-btn" class="client-details-label"></label>
                        <input id="client-add-btn" name="person-add-btn" class="client-add-btn" type="submit" value="Change Password" tabindex="11"/> 
						 or <a class="" href="people.php" tabindex="11">Cancel</a>
					</li>
				</ul>
	<?php } ?>	
	</header>
	<div id='cow'>

</form>
<?php } ?>

<!--PROCESS THE CLIENT & THE CONTACT THAT WERE SUBMITTED--->
<?php function processPerson($var) {
 	//these are the required project fields in this form
	if ($var == 0) {
		$requiredFields = array("person_first_name","person_last_name","person_email");
		$missingFields = array();
		$errorMessages = array();
	} elseif ($var >= 1) {
		$requiredFields = array();
		$missingFields = array();
		$errorMessages = array();
	}
	
	//this is for the photo upload.
	if (isset($_FILES["person-logo-file"]) and $_FILES["person-logo-file"]["error"] == UPLOAD_ERR_OK) {
		if ( $_FILES["person-logo-file"]["type"] != "image/jpeg") {
			
			//I'm hardcoding the client_currency_index, because it's in the wrong place. This should be with the rest of the validation.
			$errorMessages[] = "<li>" . getErrorMessage("1","person_logo_link", "invalid_file") . "</li>";
		} elseif ( !move_uploaded_file($_FILES["person-logo-file"]["tmp_name"], "images/" . basename($_FILES["person-logo-file"]["name"]))) {
			$errorMessages[] = "<li>" . getErrorMessage("1","person_logo_link", "upload_problem") . "</li>";
		} else {
			//if the user is posting back, add the directory to the post array.
			//putting the directory here allows us to keep just the filename in the database.
			$_POST["person_logo_link"] = "images/" . $_FILES["person-logo-file"]["name"];
		}
	} else {
		$_POST["person_logo_link"] = "images/" . (basename($_POST["person-logo-file"]));
	}

	
/* KEEP THINGS IN ONE FORM FOR NOW*/
	//create the project object ($project)
	$person = new Person( array(
		//CHECK REG SUBS!!
		"person_id" => isset($_POST["person_id"]) ? preg_replace("/[^ \-\_a-zA-Z0-9^.]/", "", $_POST["person_id"]) : "",
		"person_first_name" => isset($_POST["person-first-name"]) ? preg_replace("/[^ \-\_a-zA-Z0-9^.]/", "", $_POST["person-first-name"]) : "",
		"person_last_name" => isset($_POST["person-last-name"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["person-last-name"]) : "",
		"person_email" => isset($_POST["person-email"]) ? preg_replace("/[^ \-\_a-zA-Z^0-9^@^.]/", "", $_POST["person-email"]) : "",
		"person_department" => isset($_POST["person-department"]) ? preg_replace("/[^ \-\_a-zA-Z0-9^@^.]/", "", $_POST["person-department"]) : "",
		"person_hourly_rate" => isset($_POST["person-hourly-rate"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["person-hourly-rate"]) : "",
		"person_perm_id" => isset($_POST["person-perm-id"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["person-perm-id"]) : "",
		"person_type" => isset($_POST["person-type"])? preg_replace("/[^ \.\-\_a-zA-Z^0-9]/", "", $_POST["person-type"]) : "",
		"person_logo_link" => isset($_POST["person_logo_link"]) ? preg_replace("/[^ \.\/\-\_a-zA-Z0-9]/", "", $_POST["person_logo_link"]) :$_FILES["person_logo_link"],
		"project_notes" => isset($_POST["project-notes"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["project-notes"]) : "",
	));
	
	//use this for the project section, since this is all one page and part of the page is displaying the person's project information.
	$project = new Project( array(
		//CHECK REG SUBS!!
		/*PLEASE NOTE THAT PROJECTIDSELECT VALUE HERE IS JUST TEMPORARY SO I CAN GET THINGS DONE WITH JAVASCRIPT TEMPORARY HACK***/
		"project_id" => isset($_POST["projectidselect"]) ? preg_replace("/[^ \,0-9]/", "", $_POST["projectidselect"]) : "",
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
	
	//edit the person_permissions object ($person_perms)
	$person_perms = new Person_Permissions( array(
		"person_perm_id" => isset($_POST["person-perm-id"]) ? preg_replace("/[^ \,\-\_a-zA-Z0-9]/", "", $_POST["person-perm-id"]) : "",
		"create_projects" => isset($_POST["create_projects"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["create_projects"]) : "",
		"view_rates" => isset($_POST["view_rates"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["view_rates"]) : "",
		"create_invoices" => isset($_POST["create_invoices"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["create_invoices"]) : "",
		"person_id" => isset($_POST["person_id"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["person_id"]) : "",
	));
	
	
	error_log(print_r($_POST, true));
	error_log("here is the person array.<br>");
	error_log(print_r($person,true));
	error_log("here is the project array.<br>");
	error_log(print_r($project,true));
	error_log("here is the person_perm array.<br>");
	error_log(print_r($person_perms,true));
	
	
//error messages and validation script
	foreach($requiredFields as $requiredField) {
			if ( !$person->getValue($requiredField)) {
				$missingFields[] = $requiredField;
			}	
	}
	
	
	if ($missingFields) {
		$i = 0;
		$errorType = "required";
		foreach ($missingFields as $missingField) {
			$errorMessages[] = "<li>" . getErrorMessage("1",$missingField, $errorType) . "</li>";
			$i++;
		}
	} //else {
	/*take these out for people until a little later
	//EMAIL ADDRESS MUST BE UNIQUE HERE!
		$email = $client->getValue("client_email");
		$phone = $client->getValue("client_phone");
		$zip = $client->getValue("client_zip");
		
		// validate the email address
		if(!preg_match("/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$/i", $email)) {
			$errorMessages[] = "<li>" . getErrorMessage($client->getValue("client_currency_index"),"client_email", "invalid_input") . "</li>";
		}
		
		// validate the phone number
		if(!preg_match("/^([1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$/i", $phone)) {
			$errorMessages[] = "<li>" . getErrorMessage($client->getValue("client_currency_index"),"client_phone", "invalid_input") . "</li>";
		}
		
		//validate the zip code
		if (!preg_match ("/^[0-9]{5}$/", $zip)) {
			$errorMessages[] = "<li>" . getErrorMessage($client->getValue("client_currency_index"),"client_zip", "invalid_input") . "</li>";
		}	
	}*/
		
	if ($errorMessages) {
		displayPersonInsertForm($errorMessages, $missingFields, $person, $project);
	} else {
		//lets put this into a try/catch for added security.
		if ($var == 0) {
			try {
				//set up the person id.
				$person_id = Person::getPersonId($person->getValue("person_email"));
				$person_perms->setValue("person_id", $person_id["person_id"]);
				$person->updatePerson($person->getValueEncoded('person_email'));
				//put in the code here to put the appropriate permissions in the person_perm id table.
				//we may need other permissions once things flesh out, these are the ones of which I am aware.
				if ($person->getValue("person_perm_id") == "Regular User") {
						$person_perms->setValue("create_projects", 0);
						$person_perms->setValue("view_rates", 0);
						$person_perms->setValue("create_invoices", 0);
				} elseif ($person->getValue("person_perm_id") == "Project Manager") {
					if (isset($person_perms) && $person_perms->getValue("create_projects") == "on") {
						$person_perms->setValue("create_projects", 1);					
					} else {
						$person_perms->setValue("create_projects",0);
					}
					if (isset($person_perms) && $person_perms->getValue("view_rates") == "on") {
						$person_perms->setValue("view_rates", 1);					
					} else {
						$person_perms->setValue("view_rates", 0);
					}
					if (isset($person_perms) && $person_perms->getValue("create_invoices") == "on") {
						$person_perms->setValue("create_invoices", 1);
					} else {
						$person_perms->setValue("create_invoices", 0);
						
					}
				} elseif ($person->getValue("person_perm_id") == "Administrator") {
						$person_perms->setValue("create_projects", 1);
						$person_perms->setValue("view_rates", 1);
						$person_perms->setValue("create_invoices", 1);
				}
				error_log("HERE IS PERSON PERMS");
				error_log(print_r($person_perms, true));
				//technically, this should always be an update, since permissions are set when the user is added.
				//BUT we'll put in a fail case here. If the user doesn't exist, insert. otherwise, update.
				$person_id = $person_perms->getValue("person_id");
				if (!$person_perms->getPermissions($person_id)) {
					$person_perms->insertPermissions();
				}else{
					$person_perms->updatePermissions();
				}
				displayPersonInsertForm($errorMessages, $missingFields, $person, $project, $person_perms);
			} catch (Exception $e) {
				echo "something went wrong updating this person into our database.";
				error_log($e);
				return;
			}
		} elseif ($var == 1) {
			try {
				//print_r($person);
				//print_r($project);
				$person_id = Person::getByEmailAddress($person->getValue("person_email"));
				$project_ids = explode(',', $project->getValue("project_id"));
				//echo $person->getValue("person_id");
				//update the person's projects here, inserting them into the project_person table.
				Project_Person::deletePersonProject($person_id->getValue("person_id"));
				foreach ($project_ids as $project_id) {	
					if (($project_id) && ($person_id)) {
						$project_person->insertProjectPerson($person_id->getValue("person_id"), $project_id);
					}
				}
			} catch (Exception $e) {
				echo "something went wrong with the project add.";
			}
			displayPersonInsertForm($errorMessages, $missingFields, $person, $project, $person_perms);
		} elseif ($var == 2) {
			//can we just put the password validation check here? HA HA HA HA HA that's funny!!
			try {
				if (!$_POST["password1"]) {
					echo "please enter a password in field 1.";
					exit;
				}
				if (!$_POST["password2"]) {
					echo "please enter a password in field 2.";
					exit;
				}
				//this is in the clear. BAD, but leave it here for now.
				$person_password = $_POST["password1"];
				$person_email = $person->getValue("person_email"); 
				//$password = Person::getPassword($person_email);
				//echo $password["person_password"];
				Person::setUserPassword($person_email, $person_password);
				//$password = Person::getPassword($person_email);
				//echo $password["person_password"];
			} catch (Exception $e) {
				echo "something went wrong updating the user's password.";
			}
			displayPersonInsertForm($errorMessages, $missingFields, $person, $project, $person_perms);
		} elseif ($var == 3) {
			try {
				include("newUserEmail.php");		
			} catch (Exception $e) {
				echo "could not send the email for some reason.";
			}
			displayPersonInsertForm($errorMessages, $missingFields, $person, $project, $person_perms);
		}
	}
} 
?>
<script type="text/javascript">
//yeah, OK, so my javascript is rusty.
showP(document.getElementById('person-perm-id'));
</script>
<footer id="site-footer" class="site-footer">

</footer>
<script src="client-controls.js" type="text/javascript"></script>
</body>
</html>