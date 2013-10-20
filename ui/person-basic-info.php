<?php
	//require_once($_SERVER["DOCUMENT_ROOT"] . "/usercake/models/config.php");
	require_once("../common/common.inc.php");
	require_once("../common/errorMessages.php");
	require_once("../classes/Person.class.php");
		
		session_start();
		
	//get the person off the URL if they came in from EDIT and $_GET
	//if they came in from add, use the session variable, since they come in from $_POST.
	if (isset($_GET['person'])) {
		$person = unserialize(urldecode($_GET['person']));
	} elseif (isset($_SESSION['person'])) {
		$person = unserialize($_SESSION['person']);
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
				processPerson();
		} else {
				displayPersonInsertForm(array(), array(), $person);
		} 
?>


<!--DISPLAY PERSON INSERT WEB FORM--->
<?php function displayPersonInsertForm($errorMessages, $missingFields, $person) { 

	
	//if there are errors in the form display the message
	if ($errorMessages) {
		foreach($errorMessages as $errorMessage) {
			echo $errorMessage;
		}
	}
	
include('header.php'); //add header.php to page
?>
<section id="page-content" class="page-content">
	<header class="page-header">
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
		<figure class="person-logo l-col-20">
		<?php
		//get the image out of the db. it is the default if the user hasn't udpated it yet, this would be the case if
		//they came from the add screen.
		//$image = Person::getImage($person->getValue("person_email"));
		//echo "wklhj";
		//echo ($person->getValue("person_email"));
		//print_r($image);
		?>
			<img class="person-logo-img small" src="<?php echo "images/" . basename($person->getValue("person_logo_link"))?>" style="height:100px; width:100px;" title="Person Image" alt="Person image" />
			
		</figure>

		<section class="client-detail l-col-80">
        	<fieldset class="client-details-entry">
				<ul class="details-list client-details-list">
		   			<li class="client-details-item name">
		   			This person is a: 
		   				<?php 
		   				$row = Person::getEnumValues("person_type");																					  						$enumList = explode(",", str_replace("'", "", substr($row['COLUMN_TYPE'], 5, (strlen($row['COLUMN_TYPE'])-6))));
			   			foreach ($enumList as $type) {	?>
			 				<input type="radio" name="person-type" value="<?php echo $type?>">   <?php echo $type ?>
		   				<?php }	?><br/>
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
						<select name="person-perm-id">
						<?php
						foreach($enumList as $value) { ?>
							<option name="person-perm-id" value="<?php echo $value?>"><?php echo $value ?></option>
						<?php } ?>
						</select>
						<p>This person can track time and expenses.</p>
					</li>
					<fieldset class="person-logo-upload">
				<!--legend class="person-logo-title">Upload Person Image</legend-->
				<header class="person-logo-header">
					<h1 class="person-logo-title">Upload Person Image</h1>
				</header>
				<?//hack the image by using a hidden field.?>
				<input type="hidden" name="person-logo-file" value="<?php echo $person->getValue("person_logo_link")?>">
				<input id="person-logo-file" name="person-logo-file" class="person-logo-file" type="file" value="browse" tabindex="21" />
			</fieldset>
				</ul>
				<fieldset class="client-details-entry">
				<ul class="details-list client-details-submit">
					<li class="client-details-item submit-client">
						<label for="client-add-btn" class="client-details-label">All done?</label>
						<!--modified field to be of type submit instead of button-->
                        <input id="client-add-btn" name="person-add-btn" class="client-add-btn" type="submit" value="Save" tabindex="11"/> 
						 or <a class="" href="#" tabindex="11">Cancel</a>
					</li>
				</ul>
			</fieldset>
</section>
</form>
<?php } ?>

<!--PROCESS THE CLIENT & THE CONTACT THAT WERE SUBMITTED--->
<?php function processPerson() {
 	//these are the required project fields in this form
	$requiredFields = array("person_first_name","person_last_name","person_email");
	$missingFields = array();
	$errorMessages = array();
	
	//this is for the photo upload, and it is in the wrong place.
		//this is also really hacky. We use a hidden field to get the value back into the post variable
		//and then spit it back into the database. EW!!
	if (isset($_FILES["person-logo-file"]) and $_FILES["person-logo-file"]["error"] == UPLOAD_ERR_OK) {
		if ( $_FILES["person-logo-file"]["type"] != "image/jpeg") {
			
			//I'm hardcoding the client_currency_index, because it's in the wrong place. This should be with the rest of the validation.
			$errorMessages[] = "<li>" . getErrorMessage("1","person_logo_link", "invalid_file") . "</li>";
		} elseif ( !move_uploaded_file($_FILES["person-logo-file"]["tmp_name"], "images/" . basename($_FILES["person-logo-file"]["name"]))) {
			$errorMessages[] = "<li>" . getErrorMessage("1","person_logo_link", "upload_problem") . "</li>";
		} else {
			//if the user is posting back, add the directory to the post array.
			$_POST["person_logo_link"] = "images/" . $_FILES["person-logo-file"]["name"];
		}
	} else {
		$_POST["person_logo_link"] = "images/" . (basename($_POST["person-logo-file"]));
	}

	
	//create the project object ($project)
	$person = new Person( array(
		//CHECK REG SUBS!!
		"person_first_name" => isset($_POST["person-first-name"]) ? preg_replace("/[^ \-\_a-zA-Z0-9^.]/", "", $_POST["person-first-name"]) : "",
		"person_last_name" => isset($_POST["person-last-name"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["person-last-name"]) : "",
		"person_email" => isset($_POST["person-email"]) ? preg_replace("/[^ \-\_a-zA-Z^0-9^@^.]/", "", $_POST["person-email"]) : "",
		"person_department" => isset($_POST["person-department"]) ? preg_replace("/[^ \-\_a-zA-Z0-9^@^.]/", "", $_POST["person-department"]) : "",
		"person_hourly_rate" => isset($_POST["person-hourly-rate"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["person-hourly-rate"]) : "",
		"person_perm_id" => isset($_POST["person-perm-id"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["person-perm-id"]) : "",
		"person_type" => isset($_POST["person-type"])? preg_replace("/[^ \-\_a-zA-Z^0-9]/", "", $_POST["person-type"]) : "",
		"person_logo_link" => isset($_POST["person_logo_link"]) ? preg_replace("/[^ \/\\-\_a-zA-Z0-9^.]/", "", $_POST["person_logo_link"]) :$_FILES["person_logo_link"],
		"project_notes" => isset($_POST["project-notes"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["project-notes"]) : "",
//		"client_currency_index" => isset($_POST["client_currency_index"])? preg_replace("/[^0-9]/", "", $_POST["client_currency_index"]) : "",
//		"client_fax" => isset($_POST["client-fax"]) ? preg_replace("/[^ \-\_a-zA-Z^0-9]/", "", $_POST["client-fax"]) : "",
	));
	error_log("here is the post<br>");
	error_log(print_r($_POST, true));
	error_log("here is the project array.<br>");
	error_log(print_r($person,true));
	
	
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
		displayPersonInsertForm($errorMessages, $missingFields, $person);
	} else {
		//lets put this into a try/catch for added security.
		try {
			//this is really an edit
			//session_start();
			//$_SESSION['person'] = serialize($person);
			$person->updatePerson($person->getValueEncoded('person_email'));
			displayPersonInsertForm($errorMessages, $missingFields, $person);
		} catch (Exception $e) {
			echo "something went wrong inserting this person into our database.";
			error_log($e);
			return;
		}
	}
} 

?>
<footer id="site-footer" class="site-footer">

</footer>
<script src="client-controls.js" type="text/javascript"></script>
</body>
</html>