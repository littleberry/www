<?php

	require_once("../common/common.inc.php");
	require_once("../classes/Person.class.php");

if (isset($_POST["action"]) and $_POST["action"] == "change_password") {
	processForm();
} else {
	displayForm(array(), array(), new Person(array()));
}

function displayForm($errorMessages, $missingFields, $person) {

	if ($errorMessages) {
		foreach($errorMessages as $errorMessage) {
			echo $errorMessage;
		}
	} else {
	
		
		if (isset($_GET["emailAddress"])) {
				$emailAddress = $_GET["emailAddress"];
		} elseif (isset($_POST["emailAddress"])) {
				$emailAddress = $_POST["emailAddress"];
		} else {
				$emailAddress = "";
		}
		
			include('header.php');

		?>
		<!DOCTYPE html>

	<section id="page-content" class="page-content">

        <p>Welcome to Time Tracker, now you can track your time and basically be an awesome person!</p>
        <p>Since this is your first visit, please fill in your password information below and click Change Password to log in.</p>
        <p class="required">= required.</p>
 	<?php
	}
	?>
    
    <form action = "change_password.php" method="post" style="margin-bottom:50px;">     
    <input type="hidden" name="action" value="change_password"/>
    <input type="hidden" name="emailAddress" value="<?php echo $emailAddress?>"/>
	<div style="width:30em;">
    <label for="password1"<?php if($missingFields) echo ' class="error"'?> class="required">Choose a password</label>
    <input type="password" name="password1" id="password1" value="" /><br/>
    <label for="password2"<?php if($missingFields) echo ' class="error"'?> class="required">Retype password</label>
    <input type="password" name="password2" id="password2" value="" /><br/>
    <div style="clear:both">
    <input type="submit" name="submitButton" id="submitButton" value="Change Password" />
    <input type="reset" name="resetButton" id="resetButton" value="Reset Form" style="margin-right:20px;"/>
    </div>
    </div>
    </form>
<?php }

function processForm() {
	$requiredFields = array("person_password");
	$missingFields = array();
	$errorMessages = array();
	
	$person = new Person( array(
		"person_password" => (isset($_POST["password1"]) and isset($_POST["password2"]) and $_POST["password1"] == $_POST["password2"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["password1"]) : "",
		"person_email" => isset($_POST["emailAddress"])? preg_replace("/[^ \@\. \-\_a-zA-Z0-9]/", "", $_POST["emailAddress"]) : "",
	));
	
	foreach($requiredFields as $requiredField) {
		if ( !$person->getValue($requiredField)) {
			$missingFields[] = $requiredField;
		}
	}
	
	if ($missingFields) {
		$errorMessages[] = '<p class="error">There were some missing fields in the form you submitted. Please complete the fields highlighted below and click Send Details to resend the form.</p>';
	}
		
	if (!isset($_POST["password1"]) or (!isset($_POST["password2"]) or !$_POST["password1"] or !$_POST["password2"])) {
		$errorMessages[] = '<p class="error">Please make sure you enter your password correctly in both password fields.</p>';
	}
		

		
	if ($errorMessages) {
		displayForm($errorMessages, $missingFields, $person);
	} else {
		$email = $person->getValue("person_email");
		$password = $person->getValue("person_password");
		error_log("here " . $email . " and " . $password);
		Person::setUserPassword($email, $password);
		header("Location: index.php");
	}
}
 
?>		 
</section>
<footer id="site-footer" class="site-footer">
</footer>
</body>
</html>
