	<tr><td colspan=4>
	<div id="menucss"><?php echo $menu ?></div>
	</td></tr>
	<tr><td><h5>Name</h5></td><td><h5>Hours</h5></td><td><h5>Billable Hours</h5></td><td><h5>Billable Amount</h5></td></tr>
	<?php 
	foreach ($client_url as $url) {
			error_log(print_r($url,true));	
			foreach ($url as $myurl) {
				print_r($url);
				
				if (isset($url['client_url'])) {
					echo "<tr><td>$myurl</td>";	
				};
					
				if (isset($url['client_time'])) {
					echo "<td>$myurl</td>";
				};
					
				if (isset($url["client_billable_hours"])) {
					echo "<td>$myurl</td>";
				}
				
				if (isset($url["client_rate"])) {
					echo "<td>$myurl</td></tr><tr>";
				}
			}
		}
	?>
	</table>
	</div>
	<footer id="site-footer" class="site-footer">
	
	</footer>

</body>
</html>