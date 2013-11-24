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
	
	
if (isset($_POST["action"]) and $_POST["action"] == "Approve All Timesheets") {
	approveAllTimesheets();
} elseif (isset($_POST["action"]) and $_POST["action"] == "Approve Timesheet") {
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
		
					<input type="hidden" name="action" value="Approve Timesheet">
		<?php 
			$timesheets_for_approval = array();
			foreach($timesheets as $timesheet) {
			//print_r($timesheet);
				list($timesheet_dates) = $timesheet->getTimesheetDatesByTimesheetId($timesheet->getValueEncoded("timesheet_id"));
				?>
				<tr><td style="background-color:grey;"><a href="timesheet_submitted.php?timesheet_id=<?php echo $timesheet->getValueEncoded("timesheet_id");?>"><?php echo $timesheet_dates->getValueEncoded("timesheet_start_date"); ?> THROUGH <?php echo $timesheet_dates->getValueEncoded("timesheet_end_date"); ?></a></td></tr>
				<tr><td>
				<?php 
				$person = Person::getPersonById($timesheet_dates->getValueEncoded("person_id"));
				echo $person->getValueEncoded("person_first_name"); echo(" "); echo $person->getValueEncoded("person_last_name");?><?php echo $timesheet_item->sumTimesheetHours($timesheet->getValueEncoded("timesheet_id"))["sum(timesheet_hours)"]?></td></tr> 
				
		<?php 
				$timesheets_for_approval[] = $timesheet->getValueEncoded("timesheet_id");
				if (isset($_GET["timesheet_id"]) && $_GET["timesheet_id"] == $timesheet->getValueEncoded("timesheet_id")) {
					//echo "<tr><td>show timesheet item. for " . $timesheet->getValueEncoded("timesheet_id") . "</td></tr>";
					
					list($timesheet_items)=$timesheet_item->getSubmittedTimesheetDetail($timesheet->getValueEncoded("timesheet_id"));
					//all of this for the UI. >(
					$projects = Timesheet_Item::getDistinctValues($timesheet->getValueEncoded("timesheet_id"), "project_id");
					$tasks = Timesheet_Item::getDistinctValues($timesheet->getValueEncoded("timesheet_id"), "task_id");
					$people = Timesheet_Item::getDistinctValues($timesheet->getValueEncoded("timesheet_id"), "person_id");

						 //	$project = Project::getProjectByProjectId($timesheet_item->getValueEncoded("project_id"));
						//	$client_id = $project->getValueEncoded("client_id");
						//	$client = Client::getClient($client_id);
						//	echo $client->getValueEncoded("client_name"); 
						//	echo $project->getValueEncoded("project_name");
						//	$task = Task::getTaskName($timesheet_item->getValueEncoded("task_id"));
						///	echo $task["task_name"];
						//}
						//echo $timesheet_item->getValueEncoded("timesheet_hours");
						
					foreach($projects as $project) {
						foreach($tasks as $task) {
							foreach($people as $person) {
								$rows = Timesheet_Item::getTimesheetItemForPersonProjectTask($person,$project,$task);
								$i = 0;
								foreach ($rows as $row) {
									if ($i == 0) {
										echo "<tr><td>" . $row->getValue("person_id") . "</td>";
										echo "<td>" . $row->getValue("project_id") . "</td>";
										echo "<td>" . $row->getValue("task_id") . "</td>";
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
				}	
			}
			
		?>		
		<?php 
		if (isset($_GET["timesheet_id"]) && $_GET["timesheet_id"] == $timesheet->getValueEncoded("timesheet_id")) {
			$title = "Approve Timesheet";
		} else {
			$title = "Approve All Timesheets";
		}	
		?>
			<tr><td><input type="submit" value="<?php echo $title?>"></td></tr>
		</table>
		</form>
		</html>
<?php }

function approveAllTimesheets () {
	echo "approve all timesheets";	
}
function approveTimesheet () {
	echo "approve just this timesheet";	
}
?>
