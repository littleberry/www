<?php
	require_once("../common/common.inc.php");
	require_once("../classes/Task.class.php");
	//if(!isUserLoggedIn()){
		//redirect if user is not logged in.
	//	$_SESSION["redirect"] = $_SERVER["PHP_SELF"];
	//	header( 'Location: http://localhost:8888/time_tracker/usercake/login.php' ) ;
	//}

	list($tasks) = Task::getTasks(1);
	
	include('header.php'); //add header.php to page
	?>

<section id="page-content" class="page-content">
	<header class="page-header">
		<h1 class="page-title">Archived Tasks</h1>
		<nav class="page-controls-nav">
			<ul class="client-page-controls">
				<!-- <li class="page-controls-item add-client-button"><a class="add-client-link" href="client-add.html">+ Add Client</a></li> -->
				<li class="page-controls-item"><a class="view-client-active-link" href="tasks.php">View Active Tasks</a></li>
			</ul>
		</nav>
	</header>
	<section class="content">
		<ul id="client-list" class="client-list">
        <?php foreach ($tasks as $task) { 
				if (isset($_POST["task-unarchive-btn"]) && $_POST["task-unarchive-btn"] == $task->getValue("task_id")) {
					Task::archiveTask(0, $task->getValue("task_id"));
				} ?>

					<li class="client-info-contact">Task: <a class="client-info-contact-link" href="tasks.php" title="View task details"><?php echo $task->getValue("task_name") ?></a></li>
					<?php 
				//just putting this here for now, remove it once the appropriate UI solution is in place.?>
				<!--so, you want I should archive your client?-->
				<form action="task_archives.php" method="post" style="margin-bottom:50px;">
				<button id="client-unarchive-btn" name="task-unarchive-btn" class="client-unarchive-btn" type="submit" value="<?php echo $task->getValue("task_id"); ?>" tabindex="11">Unarchive Task</button></form> 
				</ul>		
			</li>
          <?php } ?>

		</ul>
	</section>
</section>
<footer id="site-footer" class="site-footer">

</footer>

</body>
</html>
