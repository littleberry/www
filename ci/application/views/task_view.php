	<tr><td colspan=4>
	<div id="menucss"><?php echo $menu ?></div>
	</td></tr>
	<tr><td><h5>Name</h5></td><td><h5>Hours</h5></td><td><h5>Billable Hours</h5></td><td><h5>Billable Amount</h5></td></tr>
	<table>
	<?php 
	//
	$i = 0;
	foreach ($tasks as $task) {
		echo "<td>$task</td>";
		if ($i%2 == 1) {
			echo "<tr>";
		}
	$i++;
	}
	
	?>
	</table>
	</div>
	<footer id="site-footer" class="site-footer">
	
	</footer>

</body>
</html>