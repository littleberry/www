<?php
	require_once("../common/common.inc.php");
	require_once("../classes/Project.class.php");
	require_once("../classes/Project_Person.class.php");
	require_once("../classes/Project_Task.class.php");
	require_once("../classes/Task.class.php");
	require_once("../classes/Task.class.php");
	require_once("../classes/Timesheet.class.php");
	require_once("../classes/Timesheet_Detail.class.php");
	
	if (isset($_GET["person_id"])) {
		$person_id = $_GET["person_id"];
	} else {
		echo "Something is very wrong. We can't add a timesheet without a person id.";
	}
	//include('header.php'); //add header.php to page
?>
<script type="text/javascript">

function showTasks(elem) {
		//document.getElementById('project_id').value = "X";
		$totalOptions = (elem.length);
		for (var i=0; i<$totalOptions; i++) {
			if (elem.value == elem[i].value) { 
				document.getElementById(elem[i].value).style.display = "block";
			} else {
				document.getElementById(elem[i].value).style.display = "none";
			}
		}
}

function sendTask(elem) {
		document.getElementById('task_id').value = elem.value;
}

function sendProject(elem) {
		document.getElementById('project_id').value = elem.value;
}


function go_to_timesheet() {
	task_id = document.getElementById('task_id').value;
	project_id = document.getElementById('project_id').value; 
	timesheet_notes = document.getElementById('timesheet_notes').value;
	timesheet_date = document.getElementById('timesheet_date').value;
	//alert(task_id);
	//alert(project_id);
	window.open('timesheet.php?task_id=' + task_id + '&project_id=' + project_id + '&timesheet_notes=' + timesheet_notes +"action=add_row&timesheet_date=" + timesheet_date);
	window.close();
}
</script>


<div id="page-content" class="page-content">
	<header class="page-header">
		<h2 class="page-title">Choose Project/Task</h2>
		<nav class="page-controls-nav">
				<?php //get the projects currently assigned to this user.
list($projects) = Project_Person::getProjectsForPerson($person_id);
//if (count($projects) == 0) echo $person->getValueEncoded("person_first_name") . " has no projects assigned. Assign some projects <a href='projects.php'>here.</a>";
?>
<form action="timesheet.php" method="get">
Choose Project/Task
<select name="project_id" onchange="showTasks(this)" onclick="sendProject(this)">
<?php foreach ($projects as $project) {?>
	<option name="project" value="<?php echo $project->getValueEncoded("project_id")?>"><?php echo $project->getValueEncoded("project_name")?></option> 
<?php } 
?>
</select>
<?php foreach ($projects as $project) {
	list($tasks) = Project_Task::getTasksForProject($project->getValueEncoded("project_id"));
	if (count($tasks) != 0) {
		?><select id="<?php echo $project->getValueEncoded("project_id");?>" style="display:none;" onchange="sendTask(this)"><?php
		foreach ($tasks as $task) {
			?>
			<option value="<?php echo $task->getValueEncoded("task_id")?>"><?php echo $task->getValueEncoded("task_name")?></option>
			<?php
		}
		?>
		</select><?php
	} else {
		?><select id="<?php echo $project->getValueEncoded("project_id");?>" style="display:none;">
		<option value="#">No tasks have been assigned to <?php echo $project->getValueEncoded("project_name")?> yet.</option>
		</select>
		<?php
	}
}
?>
				<textarea id="timesheet_notes"></textarea>
				<button onclick="go_to_timesheet();">Add Row</button>
<input name="task_id" id="task_id">
<input name="project_id" id="project_id">
<input type="hidden" id="timesheet_date" value="<?php echo $_GET["timesheet_date"] ?>">
</form>

		</nav>
	</header>
	<div class="content">	<footer id="site-footer" class="site-footer">
	</footer>
	</div>
<script src="project-controls.js" type="text/javascript"></script>
</body>
</html>