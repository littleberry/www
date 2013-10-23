<?php

//this is breaking, probably has something to do with the host name. Don't bother with it, hard code.
//$message = "Hello, " . $_POST["person-first-name"] . "! Welcome to Time Tracker! To set up your login, please click this link: <a href=\"" . $_SERVER['HTTP_HOST'] . "/time_tracker/ui/login.php\">Time Tracker</a>";
$message_part1 = "Hello, " . $_POST["person-first-name"] . "! Welcome to Time Tracker! To set up your login, please go to this link in your browser:";
$message_part2 = " localhost:8888/time_tracker/ui/change_password.php?emailAddress=" . $_POST["person-email"];
$message = $message_part1 . $message_part2;

error_log($message);

require_once("../classes/class.phpmailer.php");

error_log(print_r($_POST,true));

$mail = new PHPMailer();
$mail->IsSMTP();
//$mail->Host = $emailHostname;
$mail->SMTPAuth = true;
$mail->SMTPSecure = "tls";                 // sets the prefix to the server
$mail->Host = "smtp.gmail.com";      // sets GMAIL as the SMTP server
$mail->Port = 587;      
$mail->Username = "catsbap";
$mail->Password = "bapot3844";
$mail->From = "admin@timetracker.com";
$mail->FromName = "admin@timetracker.com";
$mail->AddAddress($_POST["person-email"]); 
$mail->IsHTML(true);
$mail->Subject = "You have been added to the Time Tracker System!";
$mail->Body = $message;
//$mail->SMTPDebug = 2;
	
$mail->Send();

?>