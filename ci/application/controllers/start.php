<?php

class Start extends CI_Controller {
	var $base;
	var $css;	
	
	function __construct() {
		parent::__construct();
		$this->base=$this->config->item('base_url');
		$this->css=$this->config->item('css');
	}
	
	
	function hello($name) {
		$query = $this->load->database();
		$query = $this->db->select('name');
		$query = $this->db->from('people');
		$this->db->join('sites', 'sites.people_id = people.people_id');
		$query = $this->db->get();
		$data=array();
		foreach ($query->result() as $row) {
			print $row->name;
			$data['username'] = $row->name;
		}
		$data['css'] = $this->css;
		$data['base'] = $this->base;
		$data['mytitle'] = "Welcome to this site, $name";
		$data['myText'] = "Hello, $name, now we're getting dynamic!";
		$this->load->library('Menu');   
		$mymenu = $this->menu->show_menu();
		$data['menu'] = $mymenu;
		//testing a form
		$this->load->helper('form');
		//lets try putting this in an array
		$attributes = array('name'=>'url', 'id'=>'url', 'value'=>'www.aol.com', 'maxlength'=>'100', 'size'=>'50', 'style'=>'yellow',);
		//$variable = form_input($data);
		$data['stuff'] = form_input($attributes);
		$this->load->view('testview', $data);
		//print_r($this);
	}
}
