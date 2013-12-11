<?php

class DatePicker{
	function show_picker() {
		$obj =& get_instance();
		$base = $obj->config->item('base_url');
		$obj->load->helper('url');
		$obj->load->library('session');
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = "";
		}
		$fromdate = $_GET['fromdate'];
		$todate = $_GET['todate'];
		if (isset($_GET['client_id'])) {
			$client_id = $_GET['client_id'];
		} else {
			$client_id = "";
		}
		//there are slicker ways to do this, but let's try this for now, i got one foot in the old and one in the new. ;)
		$date = new DateTime($todate);
		$date->modify('-1 week');
		$date = $date->format('Y-m-d');
		$todate = $date;
		$date = new DateTime($fromdate);
		$date->modify('-1 week');
		$date = $date->format('Y-m-d');
		$fromdate = $date;
		$kind = 'week';
		$controller = $obj->uri->segment(1); 
		$view = $obj->uri->segment(2);
	    $picker = anchor("$base/index.php/$controller/$view?fromdate=$fromdate&todate=$todate&page=$page&client_id=$client_id", "<<<<< Previous ||");
		$date = new DateTime($todate);
		$date->modify('+2 week');
		$date = $date->format('Y-m-d');
		$todate = $date;
		$date = new DateTime($fromdate);
		$date->modify('+2 week');
		$date = $date->format('Y-m-d');
		$fromdate = $date;
		$picker .= anchor("$base/index.php/$controller/$view?fromdate=$fromdate&todate=$todate&page=$page&client_id=$client_id", " Next >>>>>>");
		//$picker = anchor("start/hello/fred", "Say hello to Fred |");
		return $picker;
	}
}
?>