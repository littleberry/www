<?php

class Report_controller extends CI_Controller {
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
		$this->client_id = $this->input->get('client_id');
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
                    "report?fromdate=$this->fromdate&todate=$this->todate&page=clients" => "Clients",
                    "report?fromdate=$this->fromdate&todate=$this->todate&page=projects" => "Projects",
                    "report?fromdate=$this->fromdate&todate=$this->todate&page=tasks" => "Tasks",
                    "report?fromdate=$this->fromdate&todate=$this->todate&page=staff" => "Staff"
                );
 
				//get the name of the active page
				//$this->CI->load->library('uri');
				$this->active = $this->uri->segment(1);
 
				//setup the menu and load it ready for display in the view
				$this->data['menu'] = $this->menu->render($this->menu_pages, $this->active);
				$this->data['css'] = $this->css;
				$this->data['base'] = $this->base;
		}
	
	
	function client_report() {
	    $this->load->model('Report_model', '', TRUE);
		//all hours
		//$this->data['sumquery'] = $this->Report_model->sumHours($this->todate, $this->fromdate);
		//billable hours
		$this->data['billablequery'] = $this->Report_model->billableHours($this->todate, $this->fromdate);
		//clients returned to page as URLs
		//we'll try doing this here instead of in the view (which is probably right!!)
		//build the anchors dynamically to return to the view.
		$this->data['controller'] = "report_controller";
		$this->data['view'] = "project_report";
		$projectquery = $this->Report_model->getProjectsByClient($this->todate, $this->fromdate, $this->client_id);
		$project_url = array();
		$this->data['sum_project_hours'] = "0";
		foreach ($projectquery as $projects) {
			//print_r($clients);
			$myurl = $this->timetrackerurls->generate_project_url($projects['project_id'], $projects['project_name'], $this->data['controller'], $this->data['view']);
			$project_url[] = $myurl;
			$this->data['sum_project_hours'] = $projects['timesheet_hours'];
		}
		$this->data['project_url'] = $project_url;
		$this->data['client_name'] = $this->Report_model->getClientName($_GET["client_id"]);
		//error_log($this->client_id);
		
		//$this->data['clientquery'] = $this->Report_model->getClients($this->todate, $this->fromdate);
		//$this->data['clienthoursquery'] = $this->Report_model->getClientHours($this->todate, $this->fromdate);
		
		$data = $this->data;
		$this->load->view('report_client_view', $data);
	}
	
	function project_report() {
		
		//these are all the queries
		$this->load->model('Report_model', '', TRUE);
		//all hours
		$this->data['sumquery'] = $this->Report_model->sumHours($this->todate, $this->fromdate);
		//billable hours
		$this->data['billablequery'] = $this->Report_model->billableHours($this->todate, $this->fromdate);
		//projects
		$this->data['projectquery'] = $this->Report_model->getProjects($this->todate, $this->fromdate);
		$this->data['projecthoursquery'] = $this->Report_model->getProjectHours($this->todate, $this->fromdate);
		$data = $this->data;
		$this->load->view('report_project_view', $data);
	}

	
}
