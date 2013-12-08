	<tr><td colspan=4>
	<div id="menucss"><?php echo $menu ?></div>
	</td></tr>
	<tr><td><h5>Name</h5></td><td><h5>Hours</h5></td><td><h5>Billable Hours</h5></td><td><h5>Billable Amount</h5></td></tr>
	<?php 
	print_r($client_url);
	//var_dump($client_url);
	foreach ($client_url as $key=>$value) {
		if(isset($value['client_total_rate'])) {
			$client_url[$key] = array_merge($client_url[$key], (array) $value['client_total_rate']);
			unset($client_url[$key]['client_total_rate']);
		}
	}
	echo "<BR><BR>";
	print_r($client_url);
	
	/*foreach($a as $key => $values){
  if(isset($values['fields']))
    {
       $a[$key] = array_merge($a[$key], (array) $values['fields']);
       unset($a[$key]['fields']);
    }
}
	/*foreach ($client_url as $key=>$value) {	
		foreach ($client_url[$key] as $clientkey=>$clientvalue) {
			if(is_int($clientkey)) {
				$arr['@children'] = call_user_func_array('array_merge', $arr['@children']);
		}
	}
	*/
	?>
	</table>
	</div>
	<footer id="site-footer" class="site-footer">
	
	</footer>

</body>
</html>