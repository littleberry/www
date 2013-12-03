<?php

class Report extends CI_Controller {
	var $base;
	var $css;	
	
	function __construct() {
		parent::__construct();
		$this->base=$this->config->item('base_url');
		$this->css=$this->config->item('css');
	}
	
	
	function hourSum() {
		//this is one of many ways probably to get the data
	    $from = $this->uri->segment(4);
		$to = $this->uri->segment(3);
		
		$query = $this->load->database();
		$query = $this->db->select('timesheet_hours');
		$query = $this->db->from('timesheet_item');
		$query = $this->db->get();
		$data=array();
		foreach ($query->result() as $row) {
			//make an array of values
			$holder_data[] = $row->timesheet_hours;
		}
		$data['timesheet_hours'] = $holder_data;
		//print_r($data);
		$data['css'] = $this->css;
		$data['base'] = $this->base;
		$data['mytitle'] = "Welcome to this site, $from";
		$data['myText'] = "Hello, $to, now we're getting dynamic!";
		$this->load->view('reportview', $data);
		//print_r($this);
	}
}
