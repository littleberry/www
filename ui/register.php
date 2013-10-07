<?php
	require_once("../common/common.inc.php");
	require_once("../classes/Person.class.php");


if (isset($_POST["action"]) and $_POST["action"] == "register") {
	processForm();
} else {
	displayForm(array(), array(), new Person(array()));
}

function displayForm($errorMessages, $missingFields, $person) {
	//displayPageHeader("Please create a user name and password.");
	if ($errorMessages) {
		foreach($errorMessages as $errorMessage) {
			echo $errorMessage;
		}
	} else {
		?>
		<!DOCTYPE html>

<html lang="en">
<head>
	<title>Manage</title>
	<meta charset="utf-8" />
	<link href='http://fonts.googleapis.com/css?family=Merriweather+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css' />
	<link href="styles.css" rel="stylesheet" type="text/css" />
	<script src="libraries/jquery-1.10.2.min.js" type="text/javascript"></script>
</head>

<body>
<header id="site-header" class="site-header">
	<h1 class="site-title">Time Tracker</h1>
	<nav id="site-nav" class="site-nav">
		<ul id="site-menu" class="site-menu">
			<li class="site-menu-item"><a class="site-menu-link" href="#">Timesheets</a></li>
			<li class="site-menu-item"><a class="site-menu-link" href="#">Reports</a></li>
			<li class="site-menu-item"><a class="site-menu-link" href="#">Invoices</a></li>
			<li class="site-menu-item"><a class="site-menu-link" href="manage.html">Manage</a></li>
		</ul>
	</nav>
	<nav id="section-nav" class="section-nav manage">
		<h1 class="section-nav-title">Manage: </h1>
		<ul class="section-menu">
			<li class="section-menu-item"><a class="section-menu-link" href="projects.php">Projects</a></li>
			<li class="section-menu-item"><a class="section-menu-link" href="clients.php">Clients</a></li>
			<li class="section-menu-item"><a class="section-menu-link" href="#">Team</a></li>
		</ul>
	</nav>
</header>
<section id="page-content" class="page-content">

        <p>Thanks for joining us at Time Tracker!</p>
        <p>To register, please fill in your details below and click Send Details.</p>
        <p>Fields marked with an asterisk (*) are required.</p>
 	<?php
	}
	?>
    
    <form action = "register.php" method="post" style="margin-bottom:50px;">
    <div style="width:30em;">
    <input type="hidden" name="action" value="register"/>
    <label for="username"<?php validateField("person_username", $missingFields)?>>Choose a username *</label>
    <input type="text" name="username" id="username" value="<?php echo $person->getValueEncoded("person_username")?>" /><br/>
    <label for="password1"<?php if($missingFields) echo ' class="error"'?>>Choose a password * </label>
    <input type="password" name="password1" id="password1" value="" /><br/>
    <label for="password2"<?php if($missingFields) echo ' class="error"'?>>Retype password * </label>
    <input type="password" name="password2" id="password2" value="" /><br/>
    <label for="emailAddress"<?php validateField("person_email", $missingFields)?>>Email Address *</label>
    <input type="text" name="emailAddress" id="emailAddress" value="<?php echo $person->getValueEncoded("person_email")?>" /><br/>
 	<label for="firstName"<?php validateField("person_first_name", $missingFields)?>>First name *</label>
    <input type="text" name="firstName" id="firstName" value="<?php echo $person->getValueEncoded("person_first_name")?>" /><br/>
    <label for="lastName"<?php validateField("person_last_name", $missingFields)?>>Last name *</label>
    <input type="text" name="lastName" id="lastName" value="<?php echo $person->getValueEncoded("person_last_name")?>" /><br/>
    <div style="clear:both">
    <input type="submit" name="submitButton" id="submitButton" value="Send Details" />
    <input type="reset" name="resetButton" id="resetButton" value="Reset Form" style="margin-right:20px;"/>
    </div>
    </div>
    </form>
    <?php
	//displayPageFooter();
}

function processForm() {
	$requiredFields = array("person_username", "person_password", "person_email", "person_first_name", "person_last_name");
	$missingFields = array();
	$errorMessages = array();
	
	$person = new Person( array(
		"person_username" => isset($_POST["username"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["username"]) : "",
		"person_password" => (isset($_POST["password1"]) and isset($_POST["password2"]) and $_POST["password1"] == $_POST["password2"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["password1"]) : "",
		"person_first_name" => isset($_POST["firstName"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["firstName"]) : "",
		"person_last_name" => isset($_POST["lastName"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["lastName"]) : "",
		"person_email" => isset($_POST["emailAddress"])? preg_replace("/[^ \@\. \-\_a-zA-Z0-9]/", "", $_POST["emailAddress"]) : "",
		"person_department" => isset($_POST["person_department"])? preg_replace("/[^ \'\,\. \-a-zA-Z0-9]/", "", $_POST["person_department"]) : "",
		"person_hourly_rate" => isset($_POST["person_hourly_rate"])? preg_replace("/[^ \'\,\. \-a-zA-Z0-9]/", "", $_POST["person_hourly_rate"]) : "",
		"person_perm_id" => isset($_POST["person_perm_id"])? preg_replace("/[^ \'\,\. \-a-zA-Z0-9]/", "", $_POST["person_perm_id"]) : "",
		"person_type" => isset($_POST["person_type"])? preg_replace("/[^ \'\,\. \-a-zA-Z0-9]/", "", $_POST["person_type"]) : "",
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
		
	if (Person::getByUsername($person->getValue("person_username"))) {
		$errorMessages[] = '<p class="error">A member with that username already exists in the database. Please choose another username.</p>';
	}
		
	if (Person::getByEmailAddress($person->getValue("person_email"))) {
		$errorMessages[] = '<p class="error">A member with that email address already exists in the database. Please choose another email address, or contact the webmaster to retrieve your password.</p>';
	}
		
	if ($errorMessages) {
		displayForm($errorMessages, $missingFields, $person);
	} else {
		$person->insertPerson();
		displayThanks();
	}
}
 
function displayThanks() {
	echo "Thank you for registering! You are now granted access to the <a href=manage.php>Time Tracker</a>";
}
?>		 
</section>
<footer id="site-footer" class="site-footer">
</footer>
</body>
</html>
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 