<?php
/***this is the main controller for the repots section.*/


class Report extends CI_Controller {
	var $base;
	var $css;
	var $to;
	var $from;
	var $client_id = "";
	
	function __construct() {
		//this is common code to all of these objects.
		parent::__construct();
		//load the header file
		//$this->load->helper('header_helper');
		$this->fromdate = $this->input->get('fromdate');
		$this->todate = $this->input->get('todate');
		$client_id = $this->input->get('client_id');
		//date picker code
		$this->load->library('DatePicker');   
		//now we have the task object
		$this->load->library('Task');
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
	    //this wass just a test to see how to load data into objects.
	    //$task = $this->Report_model->getTaskObject();
	    
	    //we'll try doing this here instead of in the view (which is probably right!!)
		//build the anchors dynamically to return to the view.
		//testing
		
		//hours tracked
		$all_data = $this->Report_model->getAllHours($this->todate, $this->fromdate);
			$this->data['controller'] = "report_controller";
			foreach ($all_data as $data) {
				$billable_time = "";
				$total_time = "";
				if ($data['project_billable'] == 1) {
					if ($data['project_invoice_by'] == "Project hourly rate") {
						$total_time = $data['timesheet_hours'];
						if ($data['project_hourly_rate'] <= 0) {
							$billable_time = 0.00;
						} else {
							$billable_time = $data['timesheet_hours'];
						}
						$billable_rate = money_format('%i', $data['project_hourly_rate'] * $billable_time);	
					} elseif ($data['project_invoice_by'] == "Person hourly rate") {
						$total_time = $data['timesheet_hours'];
						if ($data['person_hourly_rate'] <= 0) {
							$billable_time = 0.00;
						} else {
							$billable_time = $data['timesheet_hours'];
						}
						$billable_rate = money_format('%i', $data['person_hourly_rate'] * $billable_time);
					} else if ($data['project_invoice_by'] == "Task hourly rate") {
						$total_time = $data['timesheet_hours'];
						if ($data['task_hourly_rate'] <= 0) {
							$billable_time = 0.00;
						} else {
							$billable_time = $data['timesheet_hours'];
						}
						$billable_rate = money_format('%i', $data['task_hourly_rate'] * $billable_time);
					} elseif ($hours['project_invoice_by'] == "Do not apply hourly rate") {
						$total_time = $data['timesheet_hours'];
						$billable_time = "0.00";
						$billable_rate = "0.00";
					}
				} else {
					$total_time = $data['timesheet_hours'];
					$billable_time = "0.00";
					$billable_rate = "0.00";
				}
				$rate_temp[] = array();
				$rate_temp['total_time'][] = $total_time;
				$rate_temp['billable_rate'][] = $billable_rate;
				$rate_temp['client_id'][] = $data['client_id'];
				$rate_temp['project_id'][] = $data['project_id'];
				$rate_temp['task_id'][] = $data['task_id'];
				$rate_temp['person_id'][] = $data['person_id'];
				$rate_temp['billable_time'][] = $billable_time;
			}
		
		//uninvoiced amount
		//wait to implement until invoicing is complete
		
		//top view common code
		//this is aggregated at the very top level, so
		//roll everything up.
		$rate = "";
		foreach ($rate_temp['billable_time'] as $ratetemp) {
			$rate = $rate + $ratetemp;
		}
		$this->data['billable_time'] = $rate;
		
		$rate = "";
		foreach ($rate_temp['total_time'] as $ratetemp) {
			$rate = $rate + $ratetemp;
		}
		$this->data['total_time'] = $rate;
		
		$rate = "";
		foreach ($rate_temp['billable_rate'] as $ratetemp) {
			$rate = $rate + $ratetemp;
		}
		$this->data['billable_rate'] = $rate;
		
		$data = $this->data;
		$this->load->view('header_view');
		$this->load->view('top_view', $data);
		
		
		
		$this->input->get('page');
		if ($this->input->get('page') == "clients") {
		
		//****CLIENT DATA*****//
		$this->data['view'] = "client_report";
		$client_url = array();
		$client_url[]['client_billable_rate'] = array();
		$client_url[]['client_billable_time'] = array();
		$client_url[]['client_total_time'] = array();
		$clientquery = $this->Report_model->getClientHours($this->todate, $this->fromdate);
		foreach ($clientquery as $clients) {			
			$running_total_time = '';
			$running_billable_time = '';
			$running_billable_rate = '';
			foreach($rate_temp['client_id'] as $key=>$val) {
				if ($clients['client_id'] == $rate_temp['client_id'][$key]) {		
					
					$anchored_client_url = $this->timetrackerurls->generate_client_url($clients['client_id'], $clients['client_name'], $this->data['controller'], $this->data['view']);
					$running_total_time = $rate_temp['total_time'][$key] + $running_total_time;
					$running_billable_time = $rate_temp['billable_time'][$key] + $running_billable_time;
					$running_billable_rate = money_format('%i', $rate_temp['billable_rate'][$key] + $running_billable_rate);
				}
			}
			
			$client_url[]['client_url'] = $anchored_client_url;
			$client_url[]['client_total_hours'] = $running_total_time;
			$client_url[]['client_billable_hours'] = $running_billable_time;
			$client_url[]['client_total_rate'] = $running_billable_rate;
		}
		
			$this->data['client_url'] = $client_url;
			$data = $this->data;
			$this->load->view('client_view', $data);
		} elseif ($this->input->get('page') == "projects") {
			//****PROJECT DATA******/
		$this->data['view'] = "project_report";
		$project_url = array();
		$project_url[]['project_billable_rate'] = array();
		$project_url[]['project_billable_time'] = array();
		$project_url[]['project_total_time'] = array();
		$projectquery = $this->Report_model->getProjectHours($this->todate, $this->fromdate);
		foreach ($projectquery as $projects) {			
			//THE ARRAY HAS BLANKS BECAUSE THESE VALUES ARE BEING INITIALIZED HERE.
			$running_total_time = '';
			$running_billable_time = '';
			$running_billable_rate = '';
			foreach($rate_temp['project_id'] as $key=>$val) {
				if ($projects['project_id'] == $rate_temp['project_id'][$key]) {		
					
					$anchored_project_url = $this->timetrackerurls->generate_project_url($projects['project_id'], $projects['project_name'], $this->data['controller'], $this->data['view']);
					$running_total_time = $rate_temp['total_time'][$key] + $running_total_time;
					$running_billable_time = $rate_temp['billable_time'][$key] + $running_billable_time;
					$running_billable_rate = money_format('%i', $rate_temp['billable_rate'][$key] + $running_billable_rate);
				}
			}
			
			$project_url[]['project_url'] = $anchored_project_url;
			$project_url[]['project_total_hours'] = $running_total_time;
			$project_url[]['project_billable_hours'] = $running_billable_time;
			$project_url[]['project_total_rate'] = $running_billable_rate;
		}
		
			$this->data['project_url'] = $project_url;
			$data = $this->data;
			$this->load->view('project_view', $data);		
		} elseif ($this->input->get('page') == "tasks") {
			//****TASK DATA******/
		$this->data['view'] = "task_report";
		$task_url = array();
		$task_url[]['task_billable_rate'] = array();
		$task_url[]['task_billable_time'] = array();
		$task_url[]['task_total_time'] = array();
		$taskquery = $this->Report_model->getTaskHours($this->todate, $this->fromdate);
		foreach ($taskquery as $tasks) {			
			$running_total_time = '';
			$running_billable_time = '';
			$running_billable_rate = '';
			foreach($rate_temp['task_id'] as $key=>$val) {
				if ($tasks['task_id'] == $rate_temp['task_id'][$key]) {		
					
					$anchored_task_url = $this->timetrackerurls->generate_task_url($tasks['task_id'], $tasks['task_name'], $this->data['controller'], $this->data['view']);
					$running_total_time = $rate_temp['total_time'][$key] + $running_total_time;
					$running_billable_time = $rate_temp['billable_time'][$key] + $running_billable_time;
					$running_billable_rate = money_format('%i', $rate_temp['billable_rate'][$key] + $running_billable_rate);
				}
			}
			
			$task_url[]['task_url'] = $anchored_task_url;
			$task_url[]['task_total_hours'] = $running_total_time;
			$task_url[]['task_billable_hours'] = $running_billable_time;
			$task_url[]['task_total_rate'] = $running_billable_rate;
		}
		
			$this->data['task_url'] = $task_url;
			$data = $this->data;
			$this->load->view('task_view', $data);				
		} elseif ($this->input->get('page') == "staff") {
			$this->data['view'] = "person_report";
			$person_url = array();
		$person_url[]['person_billable_rate'] = array();
		$person_url[]['person_billable_time'] = array();
		$person_url[]['person_total_time'] = array();
		$personquery = $this->Report_model->getPersonHours($this->todate, $this->fromdate);
		foreach ($personquery as $persons) {			
			$running_total_time = '';
			$running_billable_time = '';
			$running_billable_rate = '';
			foreach($rate_temp['person_id'] as $key=>$val) {
				if ($persons['person_id'] == $rate_temp['person_id'][$key]) {		
					
					$anchored_person_url = $this->timetrackerurls->generate_person_url($persons['person_id'], $persons['person_first_name'], $this->data['controller'], $this->data['view']);
					$running_total_time = $rate_temp['total_time'][$key] + $running_total_time;
					$running_billable_time = $rate_temp['billable_time'][$key] + $running_billable_time;
					$running_billable_rate = money_format('%i', $rate_temp['billable_rate'][$key] + $running_billable_rate);
				}
			}
			$person_url[]['person_url'] = $anchored_person_url;
			$person_url[]['person_total_hours'] = $running_total_time;
			$person_url[]['person_billable_hours'] = $running_billable_time;
			$person_url[]['person_total_rate'] = $running_billable_rate;
		}
		
			$this->data['person_url'] = $person_url;
			$data = $this->data;
			$this->load->view('person_view', $data);		
		}
	}	
}
