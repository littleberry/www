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
	
	//this is the main index function
	function index() {
	    //load the model
	    $this->load->model('Report_model', '', TRUE);
		//we'll try doing this here instead of in the view (which is probably right!!)
		//build the anchors dynamically to return to the view.
		
		//hours tracked
		$this->data['total_hours'] = $this->Report_model->sumHours($this->todate, $this->fromdate);
		
		//billable hours
		$this->data['billable_hours'] = $this->Report_model->billableHours($this->todate, $this->fromdate);
		
		//unbillable hours
		//this is in the view
		
		//billable amount
		$this->data['billable_type'] = $this->Report_model->billableType($this->todate, $this->fromdate);
		//project_hourly_rate
		$this->data['project_hourly_rate'] = $this->Report_model->getProjectHourlyRate($this->todate, $this->fromdate);
		//uninvoiced amount
		
		
		//top view common code
		$data = $this->data;
		$this->load->view('top_view', $data);
		
		
		//show the different views here for clients, projects, and tasks.
		$this->input->get('page');
		//we could do this by showing different views, although it would be nice if we could just highlight a different part of the same view, but maybe that is why index is this way...
		//client view
		if ($this->input->get('page') == "clients") {
			$client_url = array();
			$this->data['controller'] = "report_controller";
			$this->data['view'] = "client_report";
			$clientquery = $this->Report_model->getClients($this->todate, $this->fromdate);
			foreach ($clientquery as $clients) {
				$myurl = $this->timetrackerurls->generate_client_url($clients->client_id, $clients->client_name, $this->data['controller'], $this->data['view']);
				$client_url[] = $myurl;
			}
			$this->data['client_url'] = $client_url;
		
			$data = $this->data;
			$this->load->view('client_view', $data);
		} else {
			$this->load->view('project_view', $data);		
		}
	}
	
}
