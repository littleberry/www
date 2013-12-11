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
			<?php echo $picker?><?php echo date_format(new DateTime($_GET['fromdate']), "F j, Y");?> to <?php echo date_format(new DateTime($_GET['todate']), "F j, Y");?>
		</header>
	<table width="100%">
	<tr><td><h5>Hours Tracked</h5><h3><?php 
	foreach ($total_hours as $timesheet_hours) {
			echo $timesheet_hours->timesheet_hours;
	}
	if (!$total_hours) echo "0";
	?></h3>
	<tr><td>
	<div id="menucss"><?php echo $menu ?></div>
	</td></tr>
	</table>
	</div>
	<footer id="site-footer" class="site-footer">
	
	</footer>

</body>
</html>