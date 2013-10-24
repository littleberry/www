<?php
	require_once("../common/common.inc.php");
	require_once("../classes/Project.class.php");
	
	//if(!isUserLoggedIn()){
	//	//redirect if user is not logged in.
	//	$_SESSION["redirect"] = $_SERVER["PHP_SELF"];
	//	header( 'Location: http://localhost:8888/time_tracker/usercake/login.php' ) ;
	//	//header( 'Location: http://strawberry.dev/MBTimeTtracker/usercake/login.php' ) ;
	//}
	//I am taking this out temporarily until we come back to it.
	//checkLogin();
	//$person = unserialize($_SESSION['person']);

	include('header.php'); //add header.php to page
?>
	

<section id="page-content" class="page-content">
	<section class="content">
		<table id="project-list" class="entity-table projects tablesorter">
			<thead>
				<tr>
					<!-- you can also add a placeholder using script; $('.tablesorter th:eq(0)').data('placeholder', 'hello') -->
					<th data-placeholder="Try B*{space} or alex|br*|c" class="filter-match">Project(<span></span> filter-match )</th>
					<th data-placeholder="Try <d">Client</th>
					<th data-placeholder="Try >=33">Hours/Budget</th><!-- add class="filter-false" to disable the filter in this column -->
				</tr>
				<!--
<tr>
					<td>Project</td>
					<td>Client</td>
					<td>Hours/Budget</td>
				</tr>
-->
			</thead>
			<tbody>
			<?php $clientList = Project::getClientsProjectsByStatus(0);
				foreach($clientList as $clients) {
					//get the array of project objects for this client.
					$projects = Project::getProjectByClientId($clients["client_id"]);
					foreach ($projects as $project) { 
					?>
						<tr>
							<!-- <td><input id="select-project" name="select-project" type="checkbox" value="all" title="Select project" /> -->
							<td><a class="project-info-name-link" href="<?php echo "project-detail.php?project_id=" . $project->getValueEncoded("project_id")?>" title="View project details"><?php echo $project->getValueEncoded("project_name")?></a></td>
							<td><a class="client-info-contact-link" href="<?php echo "client-detail.php?client_id=" . $clients["client_id"]?>" title="View client details"><?php echo $clients["client_name"] ?></a></td>
							<td>x Hours/y budget</td>
						</tr>
					<?php } ?>
				<?php } ?>
			</tbody>
		</table>
	</section>
		
</section>
<footer id="site-footer" class="site-footer">

</footer>
<script src="project-controls.js" type="text/javascript"></script>
</body>
</html>