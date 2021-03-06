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
		//get the information for the page we're viewing.
		if (isset($_GET['client_id'])) {
			$this->client_id = $this->input->get('client_id');
		} elseif (isset($_GET['project_id'])) {
			$this->project_id = $this->input->get('project_id');
		} elseif (isset($_GET['task_id'])) {
			$this->task_id = $this->input->get('task_id');
		} elseif (isset($_GET['person_id'])) {
			$this->person_id = $this->input->get('person_id');
		}
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
		$this->data['billablequery'] = $this->Report_model->billableHours($this->todate, $this->fromdate);
		$this->data['controller'] = "report_controller";
		$this->data['view'] = "project_report";
		$projectquery = $this->Report_model->getProjectsByClient($this->todate, $this->fromdate, $this->client_id);
		$project_url = array();
		foreach ($projectquery as $projects) {
			$myurl = $this->timetrackerurls->generate_project_url($projects['project_id'], $projects['project_name'], $this->data['controller'], $this->data['view']);
			$billable_hours_by_project = $this->Report_model->getHoursByProjectType($this->todate, $this->fromdate, $projects['project_id']);
			$i = 0;
			$project_rate = "";
			$project_billable_time = "";
				foreach ($billable_hours_by_project as $hours) {
					if ($hours['project_billable'] == 1) {
						if ($hours['project_invoice_by'] == "Project hourly rate") {
							$hourly_rate = $this->Report_model->getProjectHourlyRate($hours['project_id']);
							$project_total_time = $hours['timesheet_hours'];
							$project_billable_time = $hours['timesheet_hours'];
							$project_rate = money_format('%i', $hourly_rate[0]->project_hourly_rate * $hours['timesheet_hours']);
						} elseif ($hours['project_invoice_by'] == "Person hourly rate") {
							$hourly_rate = $this->Report_model->getPersonHourlyRate($hours['person_id']);
							$project_total_time = $hours['timesheet_hours'];
							$project_billable_time = $hours['timesheet_hours'];
							$project_rate = money_format('%i', $hourly_rate[0]->person_hourly_rate * $hours['timesheet_hours']);
						} else if ($hours['project_invoice_by'] == "Task hourly rate") {
							$hourly_rate = $this->Report_model->getTaskHourlyRate($hours['task_id']);
							$project_total_time = $hours['timesheet_hours'];
							$project_billable_time = $hours['timesheet_hours'];
							$project_rate = money_format('%i', $hourly_rate[0]->task_hourly_rate * $hours['timesheet_hours']);
						} elseif ($hours['project_invoice_by'] == "Do not apply hourly rate") {
							$project_total_time = $hours['timesheet_hours'];
							$project_billable_time = "0.00";
							$project_rate = "0.00";
						}
					} else {
						if ($i == 0) {
							$project_total_time = $hours['timesheet_hours'];
							$project_billable_time = "0.00";
							$project_rate = "0.00";
						}
					}
				$project_rate_temp[] = array();
				$project_rate_temp['project_rate'][] = $project_rate;
				$project_rate_temp['project_id'][] = $hours['project_id'];
				$project_rate_temp['project_billable_time'][] = $project_billable_time;
				$project_rate_temp['project_total_time'][] = $project_total_time;
				$i++;
				}
			}

		$project_url[]['project_total_rate'] = "";
		$project_url[]['project_billable_hours'] = "";
		$project_url[]['project_total_hours'] = "";
		//this is to display all of the project hours by rate rolled up.
		$project_billable_sum = 0;
		//this is to display all of the billable project hours rolled up.
		$project_billable_hours_sum = 0;
		//this is to display all of the project hours rolled up.
		$project_hours_sum = 0;
		foreach ($projectquery as $projects) {			
			$running_total_rate = 0;
			$running_billable_time = 0;
			$running_total_time = 0;
			foreach($project_rate_temp['project_id'] as $key=>$val) {
				if ($projects['project_id'] == $project_rate_temp['project_id'][$key]) {		
					
					$anchored_project_url = $this->timetrackerurls->generate_project_url($projects['project_id'], $projects['project_name'], $this->data['controller'], $this->data['view']);
					$running_total_time = $project_rate_temp['project_total_time'][$key] + $running_total_time;
					$running_billable_time = $project_rate_temp['project_billable_time'][$key] + $running_billable_time;
					$running_total_rate = money_format('%i', $project_rate_temp['project_rate'][$key] + $running_total_rate);
				}
			}
			
			$project_url[]['project_url'] = $anchored_project_url;
			$project_url[]['project_total_hours'] = $running_total_time;
			$project_url[]['project_billable_hours'] = $running_billable_time;
			$project_url[]['project_total_rate'] = $running_total_rate;
			//client sum hours at the project level
			$project_hours_sum = $project_hours_sum + $running_total_time;
			//client sum billable hours at the project level
			$project_billable_hours_sum = $project_billable_hours_sum + $running_billable_time; 
			//client sum billable amount at the project level
			$project_billable_sum = $project_billable_sum + $running_total_rate;
		}

		$this->data['project_hours_sum'] = $project_hours_sum;
		$this->data['project_billable_hours_sum'] = $project_billable_hours_sum;
		$this->data['project_billable_sum'] = $project_billable_sum;
		$this->data['project_url'] = $project_url;
		$this->data['client_name'] = $this->Report_model->getClientName($_GET["client_id"]);
		
		
		$data = $this->data;
		$this->load->view('header_view');
		$this->load->view('report_client_view', $data);
	}
	
	function project_report() {
		
		//these are all the queries
		$this->load->model('Report_model', '', TRUE);
		//all hours for projects
		//NOT THIS THIS IS ALL HOURS, WE NEED TO AGGREGATE AT THE PROJECT LEVEL
		//$this->data['sumquery'] = $this->Report_model->sumHours($this->todate, $this->fromdate);
		//billable hours
		//NOT THIS THIS IS ALL BILLABLE HOURS WE NEED TO AGGREGATE AT THE PROJECT LEVEL
		//$this->data['billablequery'] = $this->Report_model->billableHours($this->todate, $this->fromdate);
		//projects
		error_log($this->project_id);
		$this->data['project_query'] = $this->Report_model->projectBillableHours($this->todate, $this->fromdate, $this->project_id);
		$this->data['project_hours_query'] = $this->Report_model->getHoursByProjectType($this->todate, $this->fromdate, $this->project_id);
		$data = $this->data;
		$this->load->view('header_view');
		$this->load->view('report_project_view', $data);
	}
	
	function task_report() {
		$data = array();
		$this->load->view('header_view');
		$this->load->view('report_task_view', $data);
	}

	
}
