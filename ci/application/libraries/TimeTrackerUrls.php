<?php

class TimeTrackerUrls{
	function generate_url($client_id = "", $client_name="") {
		$obj =& get_instance();
		$base = $obj->config->item('base_url');
		$obj->load->helper('url');
		$obj->load->library('session');
		$fromdate = $_GET['fromdate'];
		$todate = $_GET['todate'];
		$kind = 'week';
		$controller = $obj->uri->segment(1); 
		$view = $obj->uri->segment(2);
	    $anchor = anchor("$base/index.php/$controller/$view?fromdate=$fromdate&todate=$todate&client_id=$client_id", "$client_name");
		return $anchor;
	}
}
?>