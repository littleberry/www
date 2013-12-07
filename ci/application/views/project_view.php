	<tr><td colspan=4>
	<div id="menucss"><?php echo $menu ?></div>
	</td></tr>
	<tr><td><h5>Name</h5></td><td><h5>Clients</h5></td><td><h5>Hours</h5></td><td><h5>Billable Hours</h5></td><td><h5>Billable Amount</h5></td></tr>
	<?php foreach ($project_url as $url) {
		
		if (isset($url['project_name'])) {
			echo "<tr><td>";
			print_r($url['project_name']);
			echo "</td>";
		}
		
		if (isset($url['client_name'])) {
			echo "<td>";
			print_r($url['client_name']);
			echo "</td>";
		}
		
		if (isset($url['project_time'])) {
			echo "<td>";
			print_r($url['project_time']);
			echo "</td>";
		}
		
		if (isset($url['project_billable_hours'])) {
			echo "</td><td>";
			print_r($url['project_billable_hours']);
			echo "</td>";
		}
		
		/*if (isset($url['client_unbillable_hours'])) {
			echo "<td>";
			print_r($url['client_unbillable_hours']);
			echo "</td></tr>";
		} */	
	}
	?>
	</td></tr>
	</table>
	</div>
	<footer id="site-footer" class="site-footer">
	
	</footer>

</body>
</html>