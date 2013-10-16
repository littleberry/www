<?php
	//put this in a general place, htdocs.
	//change this if it is on muppetlabs or on localhost...
	require_once($_SERVER["DOCUMENT_ROOT"] . "/common/common.inc.php");
	require_once($_SERVER["DOCUMENT_ROOT"] . "/usercake/models/config.php");
	require_once($_SERVER["DOCUMENT_ROOT"] . "/common/common.inc.php");
	require_once($_SERVER["DOCUMENT_ROOT"] . "/classes/Person.class.php");

if(!isUserLoggedIn()){
	$_SESSION["redirect"] = $_SERVER["PHP_SELF"];
	header( 'Location: usercake/login.php' ) ;

}	


//if(isset($_POST["action"]) and $_POST["action"] == "login") {
//	processForm();
//}else{
//	displayForm(array(), array(), new Person(array()));
//}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Manage</title>
	<meta charset="utf-8" />
	<link href='http://fonts.googleapis.com/css?family=Merriweather+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css' />
	<link href="ui/styles.css" rel="stylesheet" type="text/css" />
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
			<li class="section-menu-item"><a class="section-menu-link" href="ui/projects.php">Projects</a></li>
			<li class="section-menu-item"><a class="section-menu-link" href="ui/clients.php">Clients</a></li>
			<li class="section-menu-item"><a class="section-menu-link" href="#">Team</a></li>
		</ul>
	</nav>
</header>
<section id="page-content" class="page-content">
HELLO, I AM THE INDEX.PHP PAGE.
THIS PAGE WILL EVENTUALLY BE THE DASHBOARD.

</section>
<footer id="site-footer" class="site-footer">

</footer>
</body>
</html>
