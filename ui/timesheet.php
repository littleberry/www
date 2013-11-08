<?php
	require_once("../common/common.inc.php");
	require_once("../classes/Project.class.php");
	require_once("../classes/Project_Person.class.php");
	require_once("../classes/Project_Task.class.php");
	require_once("../classes/Task.class.php");
	require_once("../classes/Task.class.php");
	require_once("../classes/Timesheet.class.php");
	require_once("../classes/Timesheet_Detail.class.php");
	require_once 'Calendar/Month/Weekdays.php';	
	require_once 'Calendar/Week.php';
	require_once 'Calendar/Year.php';
	require_once 'Calendar/Minute.php';
	
	//protect this page
	checklogin();
	
	include('header.php'); //add header.php to page
	
	if (isset($_POST["action"]) and $_POST["action"] == "save_timesheet") {
		saveTimesheet();
	} else {
		displayTimesheet(new Timesheet(array()), new Timesheet_Detail(array()));
	}
	


function displayTimesheet($timesheet, $timesheet_detail) {
for ($i=0; $i<7; $i++) {
	echo date('Y-m-d', mktime(1, 0, 0, date('m'), date('d')+$i-date('w'), date('Y'))) . ' 00:00:00';
	echo "<br>";
}
?>
<script type="text/javascript">
function FillTasks(f) {
    //alert(f.task_ids.value);
    f.task_id.value = f.task_ids.value + "," + f.task_id.value;    
}
</script>

<div id="page-content" class="page-content">
	<header class="page-header">
		<h1 class="page-title"><?php echo date("M d Y");?></h1>
		<nav class="page-controls-nav">
			<ul class="page-controls-list project">
				<li class="page-controls-item link-btn">
				<a class="view-all-link" href="project-add.php">+ Add Timesheet</a></li>
				<li class="page-controls-item"><a class="view-archive-link" href="projects.php?archives=1">View Archives</a></li>
				<li class="page-controls-item"><a class="view-all-link" href="projects.php">View All</a></li>
			</ul>
		</nav>
	</header>
	<div class="content">
		<!--BEGIN FORM-->
		<form action="timesheet.php" method="post">
			<table id="project-list" class="entity-table projects tablesorter">
				<thead>
					<tr>
						<?php
						if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] != "") {
							$person=Person::getByEmailAddress($_SESSION["logged_in"]);
						} else {
							echo "Something is wrong here...this person is not logged in and you shouldn't be seeing this, timesheet.php.";
							exit();
						}
						?>
						<input type="hidden" name="person_id" value="<?php echo $person->getValueEncoded("person_id");?>">
						<input type="hidden" name="action" value="save_timesheet">
						<img class="client-logo-img small" style="height:50px; width:50px;" src="<?php echo "images/" . $person->getValue("person_logo_link")?>" title="Person Logo" alt="Person logo" />
					</tr>
				</thead>
				<tbody>
					<tr>
				<?php	$month = new Calendar_Month_Weekdays(date('Y'), date('m'), date('d'));
						$week = new Calendar_Week(date('Y'), date('m'), date('d'));
						$year = new Calendar_Year(date('Y'), date('m'), date('d'));

$week->build();

//get the projects currently assigned to this user.
list($projects) = Project_Person::getProjectsForPerson($person->getValueEncoded("person_id"));
if (count($projects) == 0) echo $person->getValueEncoded("person_first_name") . " has no projects assigned. Assign some projects <a href='projects.php'>here.</a>";
?>
<table style="border:1px solid;">
<tr>
<td>
Choose Project/Task
</td>
</tr>
<tr>
<td>
<select name="project_id">
<?php foreach ($projects as $project) {?>
	<option name="project" value="<?php echo $project->getValueEncoded("project_id")?>"><?php echo $project->getValueEncoded("project_name")?></option> 
<?php } 
?>
</select>
<?php foreach ($projects as $project) {
	list($tasks) = Project_Task::getTasksForProject($project->getValueEncoded("project_id"));
	if (count($tasks) == 0) {
		echo $project->getValueEncoded("project_name") . " has no tasks assigned. Assign some tasks <a href='projects.php'>here.</a>";
	} else {?><select name="task_id"><?php
		foreach ($tasks as $task) {
			?>
			<option name="task" value="<?php echo $task->getValueEncoded("task_id")?>"><?php echo $task->getValueEncoded("task_name")?></option>
			<?php
		}
	}
	?></select><?php
}
?>
</td>
</tr>
</table>

<table style="width:100%; border:1px solid;">
<tr>
<?php
$i=1;
while ($day = $week->fetch()) {
    if ($day->isFirst()) {
        ?><tr style="border:1px solid;"><?php
    }

    if ($day->isEmpty()) {
        ?><td style="border:1px solid;">&nbsp;</td><?php
    } else {
        ?>
        
        <td style="border:1px solid;">
        <?php //echo date("D", mktime(0,0,0,1,$i+2,2000));?>
        <input readonly name="timesheet_date_<?php echo $i ?>" value="<?php echo($year->thisYear() . "-" . $month->thisMonth() . "-" . $day->thisDay())?>">
        <input name="timesheet_total_time_<?php echo $i ?>"><?php //echo $timesheet_total_time_[$i]->getValue("timesheet_total_time")?></td>
        <?php 
    }

    if ($day->isLast()) {
        ?></tr><?php
    }
    $i++;
}
?>
</tr>
</table>
					</tr>
				</tbody>
			</table>
			<input type="submit">
		</form>

	</div>
</div>
<?php
}

function saveTimesheet() {
//CREATE THE TIMESHEET OBJECT ($timesheet)
	$timesheet = new Timesheet( array(
	//CHECK REG SUBS!!
	"timesheet_id" => isset($_POST["timesheet_id"]) ? preg_replace("/[^ 0-9]/", "", $_POST["timesheet_id"]) : "",
	"timesheet_notes" => isset($_POST["client-phone"]) ? preg_replace("/[^ \-\_a-zA-Z^0-9]/", "", $_POST["client-phone"]) : "",
	"task_id" => isset($_POST["task_id"]) ? preg_replace("/[^ \-\_a-zA-Z0-9^@^.]/", "", $_POST["task_id"]) : "",
	"project_id" => isset($_POST["project_id"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["project_id"]) : "",
	"person_id" => isset($_POST["person_id"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["person_id"]) : ""
	));

	//create the timesheet detail object ($timesheet_detail)	
	//this is a multiple field array, since each timesheet has seven possible entries.
	//we're gong to start the loop at 1, since there is no "0" day.
	for ($i=1; $i<=7; $i++) {
		$timesheet_detail[$i] = new Timesheet_Detail( array(
		//CHECK REG SUBS!!
		"timesheet_id" => isset($_POST["timesheet_id"]) ? preg_replace("/[^ 0-9]/", "", $_POST["timesheet_id"]) : "",
		"timesheet_date" => isset($_POST["timesheet_date_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_date_$i"]) : "",
		"timesheet_start_time" => isset($_POST["timesheet_start_time_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_start_time_$i"]) : "",
		"timesheet_end_time" => isset($_POST["timesheet_end_time_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_end_time_$i"]) : "",
		"timesheet_number_of_hours" => isset($_POST["timesheet_number_of_hours_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_number_of_hours_$i"]) : "",
		"timesheet_approved" => isset($_POST["timesheet_approved_$i"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["timesheet_approved_$i"]) : ""
		));
	}
	
	//print_r($timesheet);
	$lastInsertId = $timesheet->insertTimesheet($timesheet->getValueEncoded("person_id"), $timesheet->getValueEncoded("task_id"), $timesheet->getValueEncoded("project_id"));
	//print_r($lastInsertId);
		
	for ($i=1; $i<=7; $i++) {
		error_log("Here is the OBJECT");
		error_log(print_r($timesheet_detail[$i], true));
		//$timesheet_detail[$i]->insertTimesheetDetail($lastInsertId[0]);
		//print_r($timesheet_detail[$i]);
		//LAST_INSERT_ID()...use this function to insert the children rows.
		//echo "Here is the POST";
		//print_r($_POST);
	}
	displayTimesheet($timesheet, $timesheet_detail);
} 

//echo "<Br>";
//$mydate = "11-25";
//echo date('Y-m-d', strtotime($mydate));
?>
<footer id="site-footer" class="site-footer">

</footer>
<script src="project-controls.js" type="text/javascript"></script>
</body>
</html>