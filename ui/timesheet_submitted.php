<?php 
require_once("../common/common.inc.php");
require_once("../classes/Person.class.php");
require_once("../classes/Project_Person.class.php");
require_once("../classes/Timesheet.class.php");
require_once("../classes/Timesheet_Item.class.php");
require_once("../classes/Project.class.php");
require_once("../classes/Client.class.php");
require_once("../classes/Task.class.php");




checkLogin();



if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] != "") {
	$person = Person::getByEmailAddress($_SESSION["logged_in"]);
} else {
	error_log("Something is wrong here...this person is not logged in and you shouldn't be seeing this, timesheet.php.");
	exit();
}
	
if (isset($_POST["approve_timesheets"]) and $_POST["approve_timesheets"] == "Approve All Timesheets") {
	approveAllTimesheets();
} elseif (isset($_POST["approve_timesheets"]) and $_POST["approve_timesheets"] == "Approve Timesheet") {
	approveTimesheet();
} else {
	include('header.php'); //add header.php to page moved to only be called when page is rendered so it's not sent back when page saved via JS/Ajax
	displayTimesheetApprovalForm(new Timesheet(array()), new Timesheet_Item(array()));
}
	
function displayTimesheetApprovalForm($timesheet, $timesheet_item) {
	list($timesheets) = $timesheet_item->getSubmittedTimesheetsByManager($_SESSION["logged_in"]);
	?>
	<form method="post" action="timesheet_submitted.php">
	<h1>Pending Approval</h1>
		<table border="1px solid">
							
					<?php 
					if (isset($_GET["timesheet_id"]) && $_GET["timesheet_id"] == $timesheet->getValueEncoded("timesheet_id")) {
						?><input type="hidden" name="action" value="Approve Timesheet"><?php
					} else {
						?><input type="hidden" name="action" value="Approve All Timesheets"><?php
					}
		 
			$timesheets_for_approval = array();
			foreach($timesheets as $timesheet) {
			//print_r($timesheet);				
				list($timesheet_dates) = $timesheet->getTimesheetDatesByTimesheetId($timesheet->getValueEncoded("timesheet_id"));
				?>
				<tr><td style="background-color:grey;"><?php echo $timesheet_dates->getValueEncoded("timesheet_start_date"); ?> THROUGH <?php echo $timesheet_dates->getValueEncoded("timesheet_end_date"); ?></td></tr>
				<tr><td>
				
				<?php 
				$person = Person::getPersonById($timesheet_dates->getValueEncoded("person_id"));
				?><a href="timesheet_submitted.php?timesheet_id=<?php echo $timesheet->getValueEncoded("timesheet_id");?>"><?php echo $person->getValueEncoded("person_first_name"); echo(" "); echo $person->getValueEncoded("person_last_name");?></a><b><?php echo $timesheet_item->sumTimesheetHours($timesheet->getValueEncoded("timesheet_id"))["sum(timesheet_hours)"]?>Hours</b></td></tr> 
				
		<?php 
				$timesheets_for_approval[] = $timesheet->getValueEncoded("timesheet_id");
				if (isset($_GET["timesheet_id"]) && $_GET["timesheet_id"] == $timesheet->getValueEncoded("timesheet_id")) {
					//echo "<tr><td>show timesheet item. for " . $timesheet->getValueEncoded("timesheet_id") . "</td></tr>";
					
					list($timesheet_items)=$timesheet_item->getSubmittedTimesheetDetail($timesheet->getValueEncoded("timesheet_id"));
					//all of this for the UI. >(
					$projects = Timesheet_Item::getDistinctValues($timesheet->getValueEncoded("timesheet_id"), "project_id");
					$tasks = Timesheet_Item::getDistinctValues($timesheet->getValueEncoded("timesheet_id"), "task_id");
					$people = Timesheet_Item::getDistinctValues($timesheet->getValueEncoded("timesheet_id"), "person_id");


						
					foreach($projects as $project) {
						foreach($tasks as $task) {
							foreach($people as $person) {
								$rows = Timesheet_Item::getTimesheetItemForPersonProjectTask($person,$project,$task);
								$i = 0;
								foreach ($rows as $row) {
									if ($i == 0) {
										$projects = Project::getProjectByProjectId($row->getValue("project_id"));
										$client_id = $projects->getValueEncoded("client_id");
										$client_name = Client::getClientNameById($client_id);
										echo "<tr><td>" . $client_name["client_name"] . " > ";
										$task = Task::getTask($row->getValue("task_id"));
										echo  $task->getValueEncoded("task_name") . "</br>";
										$project = Project::getProjectName($row->getValue("project_id"));
										echo $project["project_name"] . "</td>";
										echo "<td>" . $row->getValue("timesheet_date") . "<br/>";
										echo $row->getValue("timesheet_hours") . "</td>";
									} else {
										echo "<td>" . $row->getValue("timesheet_date") . "<br/>";
										echo $row->getValue("timesheet_hours") . "</td>";
									}
									$i++;
								}
							}
						}		
					}
					//this is just showing the same stuff in a different way
					if (isset($_GET["detail"])) {
						list($timesheet_items)=$timesheet_item->getSubmittedTimesheetDetail($timesheet->getValueEncoded("timesheet_id"));
						//all of this for the UI. >(
						$projects = Timesheet_Item::getDistinctValues($timesheet->getValueEncoded("timesheet_id"), "project_id");
						$tasks = Timesheet_Item::getDistinctValues($timesheet->getValueEncoded("timesheet_id"), "task_id");
						$people = Timesheet_Item::getDistinctValues($timesheet->getValueEncoded("timesheet_id"), "person_id");
						foreach($projects as $project) {
							foreach($tasks as $task) {
								foreach($people as $person) {
								$rows = Timesheet_Item::getTimesheetItemForPersonProjectTask($person,$project,$task);
								$i = 0;
								foreach ($rows as $row) {
									echo "<tr><td>" . $row->getValue("timesheet_date") . " - " . date('D', strtotime($row->getValue("timesheet_date"))) . "</td></tr>";
									$projects = Project::getProjectByProjectId($row->getValue("project_id"));
									$client_id = $projects->getValueEncoded("client_id");
									$client_name = Client::getClientNameById($client_id);
									echo "<tr><td>" . $client_name["client_name"];
									$project = Project::getProjectName($row->getValue("project_id"));
									echo " - " . $project["project_name"] . "<br>";
									$task = Task::getTask($row->getValue("task_id"));
									echo  $task->getValueEncoded("task_name");
									echo "<b>" . $row->getValue("timesheet_hours") . "</b></td>";
"</td></tr>";
								}
							}
						}		
					}

						?><tr><td>
						<a href="timesheet_submitted.php?timesheet_id=<?php echo $timesheet->getValueEncoded("timesheet_id")?>">Hide Timesheet Details</a><br><?php
					} else {
						?><tr><td><a href="timesheet_submitted.php?timesheet_id=<?php echo $timesheet->getValueEncoded("timesheet_id")?>&detail=yes">Show Timesheet Details</a><br><?php
					
					}
						?>
						
					<input type="submit" name="approve_timesheets" value="Approve Timesheet"></td></tr><?php
				}	
			}
					
		//put the array into the post.
		if (isset($_GET["timesheet_id"])) {
					?><input type="hidden" name="timesheet_id" value="<?php echo $_GET["timesheet_id"]?>">		
		<?php }	
		?>	
		
		<input type="hidden" name="timesheets_for_approval" value="<?php echo base64_encode(serialize($timesheets_for_approval))?>">		
		<?php 
		if (!isset($_GET["timesheet_id"])) {
				?><tr><td><input type="submit" name="approve_timesheets" value="Approve All Timesheets"></td></tr>
		<?php } ?>
		</table>
		</form>
		</html>
<?php 
}


function approveTimesheet () {
	if (isset($_POST["timesheet_id"])) {
		$tid = $_POST["timesheet_id"];
		//echo $tid;
		Timesheet::approveTimesheet($tid);
		header("Location: timesheet_submitted.php");
	} else {
		echo "Wait, we don't have a timesheet id! We can't approve a timesheet we don't know about. :)";
	}
}

function approveAllTimesheets () {
		$this_array = $_POST["timesheets_for_approval"];
		$timesheets_for_approval = (unserialize(base64_decode($this_array)));
		foreach ($timesheets_for_approval as $tid) {
			Timesheet::approveTimesheet($tid);		
		}
		header("Location: timesheet_submitted.php");
}
?>
