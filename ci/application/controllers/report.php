<?php

class Report extends CI_Controller {
	var $base;
	var $css;
	var $to;
	var $from;
	var $client_id = "";
	
	function __construct() {
		//this is common code to all of these objects.
		parent::__construct();
		$this->fromdate = $this->input->get('fromdate');
		$this->todate = $this->input->get('todate');
		$client_id = $this->input->get('client_id');
		//date picker code
		$this->load->library('DatePicker');   
		$mypicker = $this->datepicker->show_picker();
	    $this->data['picker'] = $mypicker;
	    //time tracker url code
	    $this->load->library('TimeTrackerUrls');   
	    
		
		$this->base=$this->config->item('base_url');
		$this->css=$this->config->item('css');
		//check this out or delete this if it's not working for us.
		$this->load->library('menu');
		$this->menu_pages = array(
                    "report?fromdate=$this->fromdate&todate=$this->todate#clients" => "Clients",
                    "report?fromdate=$this->fromdate&todate=$this->todate#projects" => "Projects",
                    "report?fromdate=$this->fromdate&todate=$this->todate#tasks" => "Tasks",
                    "report?fromdate=$this->fromdate&todate=$this->todate#staff" => "Staff"
                );
 
				//get the name of the active page
				//$this->CI->load->library('uri');
				$this->active = $this->uri->segment(1);
 
				//setup the menu and load it ready for display in the view
				$this->data['menu'] = $this->menu->render($this->menu_pages, $this->active);
				$this->data['css'] = $this->css;
				$this->data['base'] = $this->base;
		}
	
	//this is the main index function
	function index() {
	    //these are all the queries
	    $this->load->model('Report_model', '', TRUE);
		//all hours
		$this->data['sumquery'] = $this->Report_model->sumHours($this->todate, $this->fromdate);
		//billable hours
		$this->data['billablequery'] = $this->Report_model->billableHours($this->todate, $this->fromdate);
		//clients returned to page as URLs
		//we'll try doing this here instead of in the view (which is probably right!!)
		//build the anchors dynamically to return to the view.
		$clientquery = $this->Report_model->getClients($this->todate, $this->fromdate);
		$client_url = array();
		foreach ($clientquery as $clients) {
			$myurl = $this->timetrackerurls->generate_url($clients->client_id, $clients->client_name);
			$client_url[] = $myurl;
		}
		$this->data['client_url'] = $client_url;
		
		$this->data['clientquery'] = $this->Report_model->getClients($this->todate, $this->fromdate);
		$this->data['clienthoursquery'] = $this->Report_model->getClientHours($this->todate, $this->fromdate);
		$this->data['sumquery'] = $this->Report_model->sumHours($this->todate, $this->fromdate);
		//billable hours
		$this->data['billablequery'] = $this->Report_model->billableHours($this->todate, $this->fromdate);
		//projects
		$this->data['projectquery'] = $this->Report_model->getProjects($this->todate, $this->fromdate);
		$this->data['projecthoursquery'] = $this->Report_model->getProjectHours($this->todate, $this->fromdate);
		
		$data = $this->data;
		$this->load->view('anchors', $data);
	}
	
}
