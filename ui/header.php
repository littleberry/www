<?php
	require_once("../classes/Person.class.php");
	require_once("../classes/Person_Permissions.class.php");

?>

<!DOCTYPE html>
<?php
/*
<!--
Header file for all Manage sections. Needs to eventually be able to tell which page is displaying and edit the <title> element and whatever else accordingly. Eventually it should also be able to adjust displayed menu for other sections and states (i.e. logged in or not, user permissions, etc.)

Edit this file for updating links to pages/screens


-->
*/
?>
<html lang="en">
<head>
	<title>Manage</title>
	<meta charset="utf-8" />
	<link href='http://fonts.googleapis.com/css?family=Merriweather+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css' />
	<link href="libraries/theme.blueprint.css" rel="stylesheet" type="text/css" /> <!--This should only be loaded for projects.php -->
	<link href="libraries/custom-theme/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" type="text/css" /> <!--This should only be loaded for projects.php -->
	<link href="styles.css" rel="stylesheet" type="text/css" />
	<script src="libraries/jquery-1.10.2.min.js" type="text/javascript"></script>
	<script src="libraries/jquery.tablesorter.min.js" type="text/javascript"></script> <!--This should only be loaded for projects.php -->
	<script src="libraries/jquery.tablesorter.widgets.min.js" type="text/javascript"></script> <!--This should only be loaded for projects.php -->
	<script src="libraries/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script> <!--This should only be loaded for projects.php -->
	<script src="libraries/purl.js" type="text/javascript"></script> <!--This should only be loaded for projects.php -->
</head>

<body>
<?php $person = Person::getByEmailAddress($_SESSION["person"]);
$person_perms = Person_Permissions::getPermissionsAsObject($person->getValueEncoded("person_id"));
?> 
<header id="site-header" class="site-header">
	<h1 class="site-title">Time Tracker</h1>
	<nav id="site-nav" class="site-nav">
		<ul id="site-menu" class="site-menu">
			<li class="site-menu-item"><a class="site-menu-link" href="#">Timesheets</a></li>
			<?php if ($person_perms->getValueEncoded("person_perm_id") != "Regular User") {?>
			<li class="site-menu-item"><a class="site-menu-link" href="#">Reports</a></li>
			<li class="site-menu-item"><a class="site-menu-link" href="#">Invoices</a></li>
			<li class="site-menu-item"><a class="site-menu-link" href="manage.php">Manage</a></li>
		<?php } ?>	
		</ul>
	</nav>
	<nav id="util-nav" class="util-nav">
		<ul id="util-menu" class="util-menu">
			<li class="section-menu-item"><a class="section-menu-link" href="logout.php">Log Out <?php echo $_SESSION["person"];?></a></li>
		</ul>
	</nav>
	<nav id="section-nav" class="section-nav manage">
		<h1 class="section-nav-title">Timesheets: </h1>
		<ul class="section-menu">
			<li class="section-menu-item"><a class="section-menu-link" href="timesheet.php">Time</a></li>
			<li class="section-menu-item"><a class="section-menu-link" href="#">Expenses</a></li>
			<li class="section-menu-item"><a class="section-menu-link" href="#">Pending Approval</a></li>
			<li class="section-menu-item"><a class="section-menu-link" href="#">Unsubmitted</a></li>
			<li class="section-menu-item"><a class="section-menu-link" href="#">Archive</a></li>

		</ul>
	</nav>
	<?php if ($person_perms->getValueEncoded("person_perm_id") != "Regular User") {?>
	<nav id="section-nav" class="section-nav manage">
		<h1 class="section-nav-title">Manage: </h1>
		<ul class="section-menu">
			<li class="section-menu-item"><a class="section-menu-link" href="projects.php">Projects</a></li>
			<li class="section-menu-item"><a class="section-menu-link" href="clients.php">Clients</a></li>
			<li class="section-menu-item"><a class="section-menu-link" href="people.php">People</a></li>
			<li class="section-menu-item"><a class="section-menu-link" href="tasks.php">Tasks</a></li>
		</ul>
	</nav>
	<?php } ?>
</header>