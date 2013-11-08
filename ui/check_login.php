<?php

	require_once("../classes/Person.class.php");
	//session_start();
	//error_log(print_r($_POST,true));
	
	//we don't want to overload $person here because we need it to be 
	//separate from the rest of the application.
	$authenticate = new Person( array(
	"person_username" => isset($_POST["username"]) ? preg_replace("/[^\-\.\@\_a-zA-Z0-9]/", "", $_POST["username"]) : "",
	"person_password" => isset($_POST["password"]) ? preg_replace("/[^\-\_a-zA-Z0-9]/", "", $_POST["password"]) : "",
	));
		
	$authenticate->authenticate();
	if ($authenticate->authenticate()) {
		error_log("SETTING THE SESSION VARIABLE FOR THIS USER IN CHECK_LOGIN.PHP");
		session_start();
		$_SESSION['logged_in'] = $authenticate->getValue("person_username");
		echo 1;
	} else {
		echo 2;
	}

?>