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
		$testing = $this->Report_model->getAllHours($this->todate, $this->fromdate);
		error_log(print_r($testing,true));
		
		//hours tracked
		$this->data['total_hours'] = $this->Report_model->sumHours($this->todate, $this->fromdate);
		
		//billable hours
		//$this->data['billable_hours'] = $this->Report_model->billableHours($this->todate, $this->fromdate);
		
		//billable types
		$billable_type = $this->Report_model->billableType($this->todate, $this->fromdate);
		//project_hourly_rate
		$total_rate = array();
		$total_hours = array();
		//this should be in an outside function or called from a library.
		foreach ($billable_type as $project_type) {
			if ($project_type['project_invoice_by'] == "Project hourly rate") {
				$hourly_rate = $this->Report_model->getProjectHourlyRate($project_type['project_id']);
				$total_hours[] = $project_type['timesheet_hours'];
				$total_rate[] = money_format('%i', $hourly_rate[0]->project_hourly_rate * $project_type['timesheet_hours']);
			}
			if ($project_type['project_invoice_by'] == "Person hourly rate") {
				$hourly_rate = $this->Report_model->getPersonHourlyRate($project_type['person_id']);
				$total_hours[] = $project_type['timesheet_hours'];
				$total_rate[] = money_format('%i', $hourly_rate[0]->person_hourly_rate * $project_type['timesheet_hours']);
			}
			if ($project_type['project_invoice_by'] == "Task hourly rate") {
				$hourly_rate = $this->Report_model->getTaskHourlyRate($project_type['task_id']);
				$total_hours[] = $project_type['timesheet_hours'];
				$total_rate[] = money_format('%i', $hourly_rate[0]->task_hourly_rate * $project_type['timesheet_hours']);
			}
			if (($project_type['project_invoice_by'] == "Do not apply hourly rate")) {
				$total_rate[] = 0;
				$toal_hours[] = 0;
			}
		}
		
		$project_total_rate = 0;
		foreach ($total_rate as $rate) {
			//print_r($total_rate);
			$project_total_rate = $project_total_rate + $rate;
		}
		$project_total_hours = 0;
		foreach ($total_hours as $hours) {
			//print_r($total_rate);
			$project_total_hours = $project_total_hours + $hours;
		}
		
		$this->data['billable_hours'] = $project_total_hours;
		$this->data['billable_amount'] = $project_total_rate;
		//uninvoiced amount
		//wait to implement until invoicing is complete
		
		//top view common code
		$data = $this->data;
		$this->load->view('header_view');
		$this->load->view('top_view', $data);
		
		
		//we could do this by showing different views, although it would be nice if we could just highlight a different part of the same view, but maybe that is why index is this way...
		$this->input->get('page');
		if ($this->input->get('page') == "clients") {
			//****CLIENT DATA*****//
			$client_url = array();
			$client_rate = "";
			$this->data['controller'] = "report_controller";
			$this->data['view'] = "client_report";
			$clientquery = $this->Report_model->getClientHours($this->todate, $this->fromdate);
			foreach ($clientquery as $clients) {
				$client_hours_type = $this->Report_model->getHoursByClientType($this->todate, $this->fromdate, $clients['client_id']);
				error_log(print_r($client_hours_type,true));
				$i = 0;
				$client_rate = "";
				$client_billable_time = "";
				foreach ($client_hours_type as $hours) {
					if ($hours['project_billable'] == 1) {
						if ($hours['project_invoice_by'] == "Project hourly rate") {
							$hourly_rate = $this->Report_model->getProjectHourlyRate($hours['project_id']);
							$client_total_time = $hours['timesheet_hours'];
							$client_billable_time = $hours['timesheet_hours'];
							$client_rate = money_format('%i', $hourly_rate[0]->project_hourly_rate * $hours['timesheet_hours']);
						} elseif ($hours['project_invoice_by'] == "Person hourly rate") {
							$hourly_rate = $this->Report_model->getPersonHourlyRate($hours['person_id']);
							$client_total_time = $hours['timesheet_hours'];
							$client_billable_time = $hours['timesheet_hours'];
							$client_rate = money_format('%i', $hourly_rate[0]->person_hourly_rate * $hours['timesheet_hours']);
						} else if ($hours['project_invoice_by'] == "Task hourly rate") {
							$hourly_rate = $this->Report_model->getTaskHourlyRate($hours['task_id']);
							$client_total_time = $hours['timesheet_hours'];
							$client_billable_time = $hours['timesheet_hours'];
							$client_rate = money_format('%i', $hourly_rate[0]->task_hourly_rate * $hours['timesheet_hours']);
						} elseif ($hours['project_invoice_by'] == "Do not apply hourly rate") {
							$client_total_time = $hours['timesheet_hours'];
							$client_billable_time = "0.00";
							$client_rate = "0.00";
						}
					} else {
						if ($i == 0) {
							$client_total_time = $hours['timesheet_hours'];
							$client_billable_time = "0.00";
							$client_rate = "0.00";
						}
					}
				$client_rate_temp[] = array();
				$client_rate_temp['client_rate'][] = $client_rate;
				$client_rate_temp['client_id'][] = $hours['client_id'];
				$client_rate_temp['client_billable_time'][] = $client_billable_time;
				//print_r($client_billable_time);
				$client_rate_temp['client_total_time'][] = $client_total_time;
				$i++;
				}
			}

		$client_url[]['client_total_rate'] = "";
		$client_url[]['client_billable_hours'] = "";
		$client_url[]['client_total_hours'] = "";
		
		foreach ($clientquery as $clients) {			
			$running_total_rate = 0;
			$running_billable_time = 0;
			$running_total_time = 0;
			foreach($client_rate_temp['client_id'] as $key=>$val) {
				if ($clients['client_id'] == $client_rate_temp['client_id'][$key]) {		
					
					$anchored_client_url = $this->timetrackerurls->generate_client_url($clients['client_id'], $clients['client_name'], $this->data['controller'], $this->data['view']);
					$running_total_time = $client_rate_temp['client_total_time'][$key] + $running_total_time;
					$running_billable_time = $client_rate_temp['client_billable_time'][$key] + $running_billable_time;
					$running_total_rate = money_format('%i', $client_rate_temp['client_rate'][$key] + $running_total_rate);
				}
			}
			
			$client_url[]['client_url'] = $anchored_client_url;
			$client_url[]['client_total_hours'] = $running_total_time;
			$client_url[]['client_billable_hours'] = $running_billable_time;
			$client_url[]['client_total_rate'] = $running_total_rate;
		}
		

			$this->data['client_url'] = $client_url;
			$data = $this->data;
			$this->load->view('client_view', $data);
		} elseif ($this->input->get('page') == "projects") {
			//****PROJECT DATA******/
			$project_url = array();
			$projectquery = $this->Report_model->getProjects($this->todate, $this->fromdate);
			foreach ($projectquery as $project) {
				$project_url[]['project_name'] = $project['project_name'];
				$project_url[]['client_name'] = $project['client_name'];
				$project_url[]['project_time'] = $project['timesheet_hours'];
				$project_hours_type = $this->Report_model->getHoursByProjectType($this->todate, $this->fromdate, $project['project_id']);
				foreach ($project_hours_type as $hours) {
					if ($hours['project_billable'] == 1) {
						$project_url[]['project_billable_hours'] = $hours['timesheet_hours'];
					} elseif ($hours['project_billable'] == 0) {
						$project_url[]['project_billable_hours'] = $hours['timesheet_hours'] - $project['timesheet_hours'];
					}		
				}
				
			}
			$this->data['project_url'] = $project_url;
			$data = $this->data;
			$this->load->view('project_view', $data);		
		} elseif ($this->input->get('page') == "tasks") {
			//****TASK DATA******/
			$task_url = array();
			$taskquery = $this->Report_model->getTasks($this->todate, $this->fromdate);
			//error_log(print_r($taskquery,true));
			foreach ($taskquery as $task) {
				$this->data["tasks"][] = $task->task_name;
				$this->data["tasks"][] = $task->timesheet_hours;
			}
			//error_log(print_r($task_url, true));
			$this->data['task_url'] = $task_url;
			$data = $this->data;
			$this->load->view('task_view', $data);		
		} elseif ($this->input->get('page') == "staff") {
			$this->load->view('staff_view', $data);		
		}
	}	
}
