<!-- Include AJAX Framework -->

<link rel='stylesheet' type='text/css' href='ajax-login.css' />
<script type='text/javascript' src='../Libraries/jquery-1.10.2.min.js'></script>
<script type='text/javascript' src='ajax-login.js'></script>
<script src="ajax/ajax_framework.js" language="javascript"></script>
<?php 	include('header.php'); //add header.php to page 
	//I'm just trying this out. User is redirected here if checklogin in common.inc.php fails.
	//this calls the javascript function check_login() which is in the ajax-login.js file in this directory. 
	
?>

<!-- Form: the action="javascript:login()"call the javascript function "login" into ajax_framework.js -->
<form action="javascript:check_login()" method="post">
<h2>Please log in to Time Tracker.</h2>
<label class='error' id='error' style='display: none; font-size: 12px;'></label>
<input name="username" type="text" id="username" value=""/><br/>
<input name="password" type="password" id="password" value=""/><br/>
<input type="submit" name="Submit" value="Login"/>
</form>