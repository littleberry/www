<?php
	require_once("../common/common.inc.php");
	require_once("../classes/Task.class.php");
		//remove auth
		//if(!isUserLoggedIn()){
		//redirect if user is not logged in.
		//$_SESSION["redirect"] = $_SERVER["PHP_SELF"];
		//header( 'Location: http://localhost:8888/time_tracker/usercake/login.php' ) ;
	//}
	
	

include('header.php'); //add header.php to page
?>
<section id="page-content" class="page-content">
	<header class="page-header">
		<h1 class="page-title">Tasks</h1>
		<nav class="page-controls-nav">
			<ul class="client-page-controls">
				<li class="page-controls-item add-client-button"><a class="add-client-link" href="task-add.php">+ Add Task</a></li>
				<!--<li class="page-controls-item add-client-button"><a class="add-client-link" href="person-add.html">+ Add Client</a></li>-->
				<li class="page-controls-item"><a class="view-client-archive-link" href="task_archives.php">View Task Archives</a></li>
			</ul>
		</nav>
	</header>
		<?php 
		//this is the add task UI (IT IS NOT SEPARATE IN THIS MODULE!!!)
		
		
		
		
		//this is the display of all tasks.
			//1. Get out the task types, this is ugly but it works. Could have called a bunch of functions to get this right but NAAAAAH!!
			list($tasks) = Task::getTasks(); 
				//common tasks here 
				?>
				<li style="background-color:lightblue;" class="client-info-contact">Tasks common to all projects</li>
				<!--billable tasks-->
				<li style="background-color:lightgray;" class="client-info-contact">Task billable by default</li>
				<?php foreach($tasks as $task) {
					if ($task->getValue("task_common")) {
							if ($task->getValue("task_bill_by_default")) {?>
								<section class="content">
								<ul id="client-list" class="client-list">
								<li class="client-list-item l-col-33">
								<ul class="client-info-list">
								<li class="client-info-contact"><a class="client-info-contact-link" href="#" title="View contact details"><button>Edit</button></a>  <?php echo ($task->getValue("task_name")); ?></li>
								<br/><hr/>
								</ul>		
								</li>
								</ul>
								</section>
							<?php }
					}
				}?>
				<!--non billable tasks-->
				<li style="background-color:lightgray;" class="client-info-contact">Non-billable by default</li>
					<?php foreach($tasks as $task) {
					if ($task->getValue("task_common")) {
							if (!$task->getValue("task_bill_by_default")) {?>
								<section class="content">
								<ul id="client-list" class="client-list">
								<li class="client-list-item l-col-33">
								<ul class="client-info-list">
								<li class="client-info-contact"><a class="client-info-contact-link" href="#" title="View contact details"><button>Edit</button></a>  <?php echo ($task->getValue("task_name")); ?></li>
								<br/><hr/>
								</ul>		
								</li>
								</ul>
								</section>
							<?php }
					}
				}
				//other tasks here
				?>
				<li style="background-color:lightblue;" class="client-info-contact">Other tasks</li>
				<!--billable tasks-->
				<li style="background-color:lightgray;" class="client-info-contact">Task billable by default</li>
				<?php foreach($tasks as $task) {
					if (!$task->getValue("task_common")) {
							if ($task->getValue("task_bill_by_default")) {?>
								<section class="content">
								<ul id="client-list" class="client-list">
								<li class="client-list-item l-col-33">
								<ul class="client-info-list">
								<li class="client-info-contact"><a class="client-info-contact-link" href="#" title="View contact details"><button>Edit</button></a>  <?php echo ($task->getValue("task_name")); ?></li>
								<br/><hr/>
								</ul>		
								</li>
								</ul>
								</section>
							<?php }
					}
				}?>
				<!--non billable tasks-->
				<li style="background-color:lightgray;" class="client-info-contact">Non-billable by default</li>
					<?php foreach($tasks as $task) {
					if (!$task->getValue("task_common")) {
							if (!$task->getValue("task_bill_by_default")) {?>
								<section class="content">
								<ul id="client-list" class="client-list">
								<li class="client-list-item l-col-33">
								<ul class="client-info-list">
								<li class="client-info-contact"><a class="client-info-contact-link" href="#" title="View contact details"><button>Edit</button></a>  <?php echo ($task->getValue("task_name")); ?></li>
								<br/><hr/>
								</ul>		
								</li>
								</ul>
								</section>
							<?php }
					}
				}
?>
</section>
<footer id="site-footer" class="site-footer">

</footer>

</body>
</html>