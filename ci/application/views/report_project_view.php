<!DOCTYPE html>
<html lang="en">
<?php
	//require_once("/Applications/MAMP/htdocs/time_tracker/common/common.inc.php");
	//probably shouldn't be in the view, but we'll leave it here for now
	//take this out for now until I can figure out what's wrog
	//checklogin();
	//include('header.php'); //add header.php to page moved to only be called when page is rendered so it's not sent back when page saved via JS/Ajax
?>

<body>
	<div id="page-content" class="page-content">
		<header class="page-header">
			<h1>This week:</h1>
			<h3 class="page-title"><?php echo date_format(new DateTime($_GET['fromdate']), "F j, Y");?> to <?php echo date_format(new DateTime($_GET['todate']), "F j, Y");?></h3>
		</header>
	<table width="100%">
	<tr><td><?php echo $project_url;?></td></tr>
	<tr><td><?php echo $picker ?>
	</td></tr>
	<tr><td>
	<b>Hours Tracked</b><br>
	<?php 
	foreach ($sumquery as $timesheet_hours) {
		echo $timesheet_hours->timesheet_hours;
	}
	if (empty($sumquery)) echo "0";
	?>
	</td><td><b>Billable Hours</b><br>
	<?
	foreach ($billablequery as $timesheet_hours) {
		if (!$timesheet_hours) { 
			echo "0";
		} else {
			echo $timesheet_hours->timesheet_hours;
		}
	}
	?>
	</td><td>Billable Amount</td><td>Uninvoiced Amount</td></tr>
	<tr><td><h3><a href="http://localhost:8888/time_tracker/ci/index.php/report_controller/client_report?fromdate=<?php echo $_GET['fromdate']?>&todate=<?php echo $_GET['todate']?>">Clients</a></h3></td>
	</td><td><h1>Projects</h1></td><td><h3>Tasks</h3></td><td><h3>Staff</h3></td></tr>
	<tr><td><hr></td></tr>
	<tr><td><b>Name</b><br>
				<?php
		foreach ($projectquery as $projects) {
		echo $projects->project_name;
		echo "<br>";
	}
	?>
	</td><td><b>Clients</b><br>
	<?php
		foreach ($projectquery as $clients) {
		echo $clients->client_name;
		echo "<br>";
	}
	?>
	
	
	</td><td><b>Hours</b><br>
		<?php
		foreach ($projecthoursquery as $projecthours) {
		echo $projecthours->timesheet_hours;
		echo "<br>";
	}
	?>

	</td><td>Billable Hours</td><td>Billable Amount</td></tr>
	</table>
	</div>
	<footer id="site-footer" class="site-footer">
	
	</footer>

</body>
</html>