<?php
	require_once("../common/common.inc.php");
	require_once("../classes/Person.class.php");
		if(!isUserLoggedIn()){
		//redirect if user is not logged in.
		$_SESSION["redirect"] = $_SERVER["PHP_SELF"];
		header( 'Location: http://localhost:8888/time_tracker/usercake/login.php' ) ;
	}
	
	
	?>
	<!DOCTYPE html>
<html lang="en">
<head>
	<title>People</title>
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
			<li class="section-menu-item"><a class="section-menu-link" href="people.php">Team</a></li>
		</ul>
	</nav>
</header>
<section id="page-content" class="page-content">
	<header class="page-header">
		<h1 class="page-title">People</h1>
		<nav class="page-controls-nav">
			<ul class="client-page-controls">
				<li class="page-controls-item add-client-button"><a class="add-client-link" href="person-add.php">+ Add Person</a></li>
				<!--<li class="page-controls-item add-client-button"><a class="add-client-link" href="person-add.html">+ Add Client</a></li>-->
				<li class="page-controls-item"><a class="view-client-archive-link" href="project_archives.php">View Project Archives</a></li>
			</ul>
		</nav>
	</header>
		<?php 
			//personList is an array of objects.
			//1. Get out the employee types, display the folks by their jobs.
			list($personTypes) = Person::getPersonTypes();
			list($people) = Person::getPeople();
			foreach($personTypes as $personType) {
				echo $personType->getValue("person_type") . "s";
					foreach($people as $person) {
						if ($personType->getValue("person_type") == $person->getValue("person_type")) {?>
						<section class="content">
							<ul id="client-list" class="client-list">
								<li class="client-list-item l-col-33">
									<ul class="client-info-list">
										<li style="background-color:lightgray;" class="client-info-contact"><a class="client-info-contact-link" href="<?php echo "person-detail.php?person_id=" . $person->getValue("person_id")?>" title="View contact details">Edit</a>  <?php echo ($person->getValue("person_first_name") . " " . $person->getValue("person_last_name")); ?></li>
									</ul>		
								</li>
							</ul>
						</section>
					<?php }} ?>	
			 <?php }?>
</section>
<footer id="site-footer" class="site-footer">

</footer>

</body>
</html>