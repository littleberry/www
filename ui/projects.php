<?php
	require_once("../common/common.inc.php");
	require_once("../classes/Project.class.php");
	
	
	?>
	<!DOCTYPE html>
<html lang="en">
<head>
	<title>Projects</title>
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
			<li class="section-menu-item"><a class="section-menu-link" href="#">Projects</a></li>
			<li class="section-menu-item"><a class="section-menu-link" href="clients.php">Clients</a></li>
			<li class="section-menu-item"><a class="section-menu-link" href="#">Team</a></li>
		</ul>
	</nav>
</header>
<section id="page-content" class="page-content">
	<header class="page-header">
		<h1 class="page-title">Projects</h1>
		<nav class="page-controls-nav">
			<ul class="client-page-controls">
				<li class="page-controls-item add-client-button"><a class="add-client-link" href="project-add.php">+ Create Project</a></li>
				<!--<li class="page-controls-item add-client-button"><a class="add-client-link" href="client-add.html">+ Add Client</a></li>-->
				<li class="page-controls-item"><a class="view-client-archive-link" href="project_archives.php">View Project Archives</a></li>
			</ul>
		</nav>
	</header>
		<?php $clientList = Project::getClientsProjectsByStatus(0);
			foreach($clientList as $clients) {
		?>
	<section class="content">
		<ul id="client-list" class="client-list">
			<li class="client-list-item l-col-33">
				<ul class="client-info-list">
					<li style="background-color:lightgray;" class="client-info-contact">Client: <a class="client-info-contact-link" href="<?php echo "client-detail.php?client_id=" . $clients["client_id"]?>" title="View contact details"><?php echo $clients["client_name"] ?></li>
					<?php 
					//get the array of project objects for this client.
					$projects = Project::getProjectByClientId($clients["client_id"]);
					foreach ($projects as $project) { 
					?>
						<li class="client-info-name"><a class="client-info-name-link" href="<?php echo "project-detail.php?project_id=" . $project->getValueEncoded("project_id")?>" title="View client details"><?php echo $project->getValueEncoded("project_name")?></a></li>
					<?php } ?>
				</ul>		
			</li>
		</ul>
	</section>
	<?php } ?>
</section>
<footer id="site-footer" class="site-footer">

</footer>

</body>
</html>