<!DOCTYPE html>
<html lang="en">
<?php
	require_once("/Applications/MAMP/htdocs/time_tracker/common/common.inc.php");
	//probably shouldn't be in the view, but we'll leave it here for now
	//take this out for now until I can figure out what's wrog
	//checklogin();
	include('header.php'); //add header.php to page moved to only be called when page is rendered so it's not sent back when page saved via JS/Ajax
?>
<style>
#menucss
ul{
list-style-type:none;
margin:0;
padding:0;
overflow:hidden;
}
#menucss
li
{
float:left;
}
#menucss
a:link
{
display:block;
width:120px;
font-weight:bold;
color:#FFFFFF;
background-color:#98bf21;
text-align:center;
padding:4px;
text-decoration:none;
text-transform:uppercase;
}
#menucss 
a:hover
{
background-color:#7A991A;
}
#menucss
a:active
{
background-color: aqua;
}

</style>

<body>
	
	<div id="page-content" class="page-content">
		<header class="page-header">
			<h1>This week:</h1>
			<h3 class="page-title"><?php echo date_format(new DateTime($_GET['fromdate']), "F j, Y");?> to <?php echo date_format(new DateTime($_GET['todate']), "F j, Y");?></h3>
		</header>
	<table width="100%" style="border:1px solid;">
	<tr><td><?php echo $picker ?>
	</td></tr>
	<tr><td><?php echo $client_name[0]->client_name;?>
	<tr><td>
	<b>Hours Tracked</b><br>
	<?php 
	print_r($sum_project_hours);
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
	<tr><td colspan="4">	<div id="menucss"><?php echo $menu ?></div>
</td></tr>
	<tr><td><b>Name</b><br>
				<?php
		foreach ($project_url as $projects) {
		print_r($projects);
		echo "<br>";
	}
	?>

	</td><td><b>Hours</b><br>
		<?php
		//foreach ($clienthoursquery as $clienthours) {
		//echo $clienthours->timesheet_hours;
		//echo "<br>";
	//}
	?>

	</td><td>Billable Hours</td><td>Billable Amount</td></tr>
	</table>
	</div>
	<footer id="site-footer" class="site-footer">
	
	</footer>

</body>
</html>