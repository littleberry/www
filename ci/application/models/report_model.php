<?php

class Report_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
	}
	
	
	function sumHours($to, $from) {
		$sumquery = $this->db->select_sum('timesheet_hours');
		$sumquery = $this->db->from('timesheet_item');
		$sumquery = $this->db->where('timesheet_date <=', $to);
		$sumquery = $this->db->where('timesheet_date >=', $from);
		$sumquery = $this->db->having('count(*) > 0');
		$sumquery = $this->db->get();
		return $sumquery->result();
	}
	
	function billableHours($to, $from) {
		$billablequery = $this->db->select_sum('timesheet_hours');
		$billablequery = $this->db->from('timesheet_item');
		$billablequery = $this->db->join('project', 'project.project_id = timesheet_item.project_id');
		$billablequery = $this->db->where('project_billable =', 1);
		$billablequery = $this->db->where('timesheet_date <=', $to);
		$billablequery = $this->db->where('timesheet_date >=', $from);
		$billablequery = $this->db->having('count(*) > 0');
		$billablequery = $this->db->get();	
		return $billablequery->result();
	}
	
	function projectBillableHours($to, $from, $project_id) {
		$billablequery = $this->db->select_sum('timesheet_item.timesheet_hours');
		$billablequery = $this->db->from('timesheet_item');
		$billablequery = $this->db->join('project', 'project.project_id = timesheet_item.project_id');
		$billablequery = $this->db->where('project.project_id =', $project_id);
		$billablequery = $this->db->where('project.project_billable =', 1);
		$billablequery = $this->db->where('timesheet_item.timesheet_date <=', $to);
		$billablequery = $this->db->where('timesheet_item.timesheet_date >=', $from);
		$billablequery = $this->db->having('count(*) > 0');
		$billablequery = $this->db->get();	
		return $billablequery->result();
	}
	
	//get out the billable type.
	function billableType($to, $from) {
		$rows = array(); //will hold all results
		$billabletypequery = $this->db->select('project.project_invoice_by');
		$billabletypequery = $this->db->select_sum('timesheet_item.timesheet_hours');
		$billabletypequery = $this->db->from('project');
		$billabletypequery = $this->db->select('timesheet_item.person_id');
		$billabletypequery = $this->db->select('timesheet_item.project_id');
		$billabletypequery = $this->db->select('timesheet_item.task_id');
		$billabletypequery = $this->db->join('timesheet_item', 'timesheet_item.project_id = project.project_id');
		$billabletypequery = $this->db->where('timesheet_item.timesheet_date <=', $to);
		$billabletypequery = $this->db->where('timesheet_item.timesheet_date >=', $from);
		$billabletypequery = $this->db->group_by('timesheet_item.person_id');
		$billabletypequery = $this->db->group_by('timesheet_item.project_id');
		$billabletypequery = $this->db->group_by('timesheet_item.task_id');
		$billabletypequery = $this->db->having('count(*) > 0');
		$billabletypequery = $this->db->get();	
		foreach($billabletypequery->result_array() as $row)
		{    
        $rows[] = $row; //add the fetched result to the result array;
		}
		return $rows;
	}
	
	//get out the billable type for the client
	function clientBillableType($to, $from, $client_id) {
		$rows = array(); //will hold all results
		$billabletypequery = $this->db->select('timesheet_item.timesheet_date');
		$billabletypequery = $this->db->select('project.project_invoice_by');
		$billabletypequery = $this->db->select_sum('timesheet_item.timesheet_hours');
		$billabletypequery = $this->db->from('project');
		$billabletypequery = $this->db->select('timesheet_item.person_id');
		$billabletypequery = $this->db->select('timesheet_item.project_id');
		$billabletypequery = $this->db->select('timesheet_item.task_id');
		$billabletypequery = $this->db->select('client.client_id');
		$billabletypequery = $this->db->join('client', 'client.client_id = project.client_id');
		$billabletypequery = $this->db->join('timesheet_item', 'timesheet_item.project_id = project.project_id');
		$billabletypequery = $this->db->where('timesheet_item.timesheet_date <=', $to);
		$billabletypequery = $this->db->where('timesheet_item.timesheet_date >=', $from);
		$billabletypequery = $this->db->group_by('client.client_id');
		$billabletypequery = $this->db->group_by('timesheet_item.person_id');
		$billabletypequery = $this->db->group_by('timesheet_item.project_id');
		$billabletypequery = $this->db->group_by('timesheet_item.task_id');
		$billabletypequery = $this->db->having('count(*) > 0');
		$billabletypequery = $this->db->get();	
		foreach($billabletypequery->result_array() as $row)
		{    
        $rows[] = $row; //add the fetched result to the result array;
		}
		return $rows;
	}

	
	//get out the project hours and rate.
	function getProjectHourlyRate($project_id) {
		$hourlyratequery = $this->db->select('project_hourly_rate');
		$hourlyratequery = $this->db->select('project_id');
		$hourlyratequery = $this->db->from('project');
		$hourlyratequery = $this->db->where('project_id =', $project_id);
		$hourlyratequery = $this->db->get();	
		return $hourlyratequery->result();
	}
	
	//get out the person hours and rate.
	function getPersonHourlyRate($person_id) {
		$hourlyratequery = $this->db->select('person_hourly_rate');
		$hourlyratequery = $this->db->select('person_id');
		$hourlyratequery = $this->db->from('person');
		$hourlyratequery = $this->db->where('person_id =', $person_id);
		$hourlyratequery = $this->db->get();	
		return $hourlyratequery->result();
	}
	
	//get out the task hours and rate.
	function getTaskHourlyRate($task_id) {
		$hourlyratequery = $this->db->select('task_hourly_rate');
		$hourlyratequery = $this->db->select('task_id');
		$hourlyratequery = $this->db->from('task');
		$hourlyratequery = $this->db->where('task_id =', $task_id);
		$hourlyratequery = $this->db->get();	
		return $hourlyratequery->result();
	}
	
	//client queries to be used in the client view, called from report controller
	function getClientName($client_id) {
		$query = $this->db->select('client.client_name');
		$query = $this->db->from('client');
		$query = $this->db->where('client.client_id =', $client_id);
		$query = $this->db->get();	
		return $query->result();
	}
	
	function getClients($to, $from) {
		$rows = array();
		$clientquery = $this->db->select('client.client_name');
		$clientquery = $this->db->select('client.client_id');
		$clientquery = $this->db->from('client');
		$clientquery = $this->db->join('project', 'project.client_id = client.client_id');
		$clientquery = $this->db->join('timesheet_item', 'project.project_id = timesheet_item.project_id');
		$clientquery = $this->db->where('timesheet_item.timesheet_date <=', $to);
		$clientquery = $this->db->where('timesheet_item.timesheet_date >=', $from);
		$clientquery = $this->db->group_by('client.client_name');
		$clientquery = $this->db->group_by('project.project_id');
		$clientquery = $this->db->get();	
		foreach($clientquery->result_array() as $row)
		{    
        $rows[] = $row; //add the fetched result to the result array;
		}
		return $rows;	}
	
	function getClientHours($to, $from) {
		$rows = array(); //will hold all results
		$clienthoursquery = $this->db->select('client.client_name');
		$clienthoursquery = $this->db->select('client.client_id');
		$clienthoursquery = $this->db->select_sum('timesheet_item.timesheet_hours');
		$clienthoursquery = $this->db->from('client');
		$clienthoursquery = $this->db->join('project', 'project.client_id = client.client_id');
		$clienthoursquery = $this->db->join('timesheet_item', 'project.project_id = timesheet_item.project_id');
		$clienthoursquery = $this->db->where('timesheet_item.timesheet_date <=', $to);
		$clienthoursquery = $this->db->where('timesheet_item.timesheet_date >=', $from);
		$clienthoursquery = $this->db->group_by('client.client_name');
		$clienthoursquery = $this->db->having('count(*) > 0');
		$clienthoursquery = $this->db->get();	
		foreach($clienthoursquery->result_array() as $row)
		{    
        $rows[] = $row; //add the fetched result to the result array;
		}
		return $rows;	
	}
	
	function getHoursByClientType($to, $from, $client_id) {
		$rows = array(); //will hold all results
		$clienthoursquery = $this->db->select('project.project_billable');
		$clienthoursquery = $this->db->select('project.project_invoice_by');
		$clienthoursquery = $this->db->select('client.client_id');
		$clienthoursquery = $this->db->select('project.project_id');
		$clienthoursquery = $this->db->select('timesheet_item.task_id');
		$clienthoursquery = $this->db->select('timesheet_item.person_id');
		$clienthoursquery = $this->db->select_sum('timesheet_item.timesheet_hours');
		$clienthoursquery = $this->db->from('client');
		$clienthoursquery = $this->db->join('project', 'project.client_id = client.client_id');
		$clienthoursquery = $this->db->join('timesheet_item', 'project.project_id = timesheet_item.project_id');
		$clienthoursquery = $this->db->where('client.client_id =', $client_id);
		$clienthoursquery = $this->db->where('timesheet_item.timesheet_date <=', $to);
		$clienthoursquery = $this->db->where('timesheet_item.timesheet_date >=', $from);
		$clienthoursquery = $this->db->group_by('client.client_id');
		$clienthoursquery = $this->db->group_by('project.project_billable');
		$clienthoursquery = $this->db->group_by('timesheet_item.person_id');
		$clienthoursquery = $this->db->group_by('timesheet_item.task_id');
		$clienthoursquery = $this->db->having('count(*) > 0');
		$clienthoursquery = $this->db->get();	
		foreach($clienthoursquery->result_array() as $row)
		{    
        $rows[] = $row; //add the fetched result to the result array;
		}
		return $rows;	
	}
	
	//project queries 
	function getHoursByProjectType($to, $from, $project_id) {
		$rows = array(); //will hold all results
		$projecthoursquery = $this->db->select('project.project_billable');
		$projecthoursquery = $this->db->select('project.project_invoice_by');
		$projecthoursquery = $this->db->select('project.project_id');
		$projecthoursquery = $this->db->select('timesheet_item.task_id');
		$projecthoursquery = $this->db->select('timesheet_item.person_id');
		$projecthoursquery = $this->db->select_sum('timesheet_item.timesheet_hours');
		$projecthoursquery = $this->db->from('project');
		$projecthoursquery = $this->db->join('timesheet_item', 'project.project_id = timesheet_item.project_id');
		$projecthoursquery = $this->db->where('project.project_id =', $project_id);
		$projecthoursquery = $this->db->where('timesheet_item.timesheet_date <=', $to);
		$projecthoursquery = $this->db->where('timesheet_item.timesheet_date >=', $from);
		$projecthoursquery = $this->db->group_by('project.project_billable');
		$projecthoursquery = $this->db->group_by('project.project_id');
		$projecthoursquery = $this->db->group_by('timesheet_item.person_id');
		$projecthoursquery = $this->db->group_by('timesheet_item.task_id');
		$projecthoursquery = $this->db->having('count(*) > 0');
		$projecthoursquery = $this->db->get();	
		foreach($projecthoursquery->result_array() as $row)
		{    
        $rows[] = $row; //add the fetched result to the result array;
		}
		return $rows;	
	}
	
	function getProjectsByClient($to, $from, $client_id) {
		$rows = array();
		$projectquery = $this->db->select('project.project_name');
		$projectquery = $this->db->select('project.project_id');
		//$projectquery = $this->db->select('client.client_name');
		//$projectquery = $this->db->select('client.client_id');
		$projectquery = $this->db->from('project');
		$projectquery = $this->db->select_sum('timesheet_item.timesheet_hours');
		$projectquery = $this->db->join('client', 'client.client_id = project.client_id');
		$projectquery = $this->db->join('timesheet_item', 'project.project_id = timesheet_item.project_id');
		$projectquery = $this->db->where('client.client_id =', $client_id);
		$projectquery = $this->db->where('timesheet_item.timesheet_date <=', $to);
		$projectquery = $this->db->where('timesheet_item.timesheet_date >=', $from);
		//$projectquery = $this->db->group_by('client.client_name');
		$projectquery = $this->db->group_by('project.project_name');
		$projectquery = $this->db->having('count(*) > 0');
		$projectquery = $this->db->get();	
		foreach($projectquery->result_array() as $row)
		{    
        $rows[] = $row; //add the fetched result to the result array;
		}
		return $rows;	
	}
	
	//NOT SURE WE'RE GOING TO USE THIS
	function getProjectHours($to, $from) {
		$projecthoursquery = $this->db->select('project_name');
		$projecthoursquery = $this->db->from('project');
		$projecthoursquery = $this->db->select_sum('timesheet_hours');
		$projecthoursquery = $this->db->join('timesheet_item', 'project.project_id = timesheet_item.project_id');
		$projecthoursquery = $this->db->where('timesheet_date <=', $to);
		$projecthoursquery = $this->db->where('timesheet_date >=', $from);
		$projecthoursquery = $this->db->group_by('project_name');
		$projecthoursquery = $this->db->having('count(*) > 0');
		$projecthoursquery = $this->db->get();	
		return $projecthoursquery->result();
	}
	
	function getProjects($to, $from) {
		$rows = array();
		$projectquery = $this->db->select('project.project_name');
		$projectquery = $this->db->select('project.project_id');
		$projectquery = $this->db->select('client.client_name');
		$projectquery = $this->db->select('client.client_id');
		$projectquery = $this->db->from('project');
		$projectquery = $this->db->select_sum('timesheet_item.timesheet_hours');
		$projectquery = $this->db->join('client', 'client.client_id = project.client_id');
		$projectquery = $this->db->join('timesheet_item', 'project.project_id = timesheet_item.project_id');
		$projectquery = $this->db->where('timesheet_item.timesheet_date <=', $to);
		$projectquery = $this->db->where('timesheet_item.timesheet_date >=', $from);
		$projectquery = $this->db->group_by('project.project_name');
		$projectquery = $this->db->group_by('client.client_name');
		$projectquery = $this->db->having('count(*) > 0');
		$projectquery = $this->db->get();	
		foreach($projectquery->result_array() as $row)
		{    
        $rows[] = $row; //add the fetched result to the result array;
		}
		return $rows;	
	}
	
	//tasks****

	function getTasks($to, $from) {
		$taskquery = $this->db->select('task.task_name');
		$taskquery = $this->db->from('task');
		$taskquery = $this->db->select_sum('timesheet_item.timesheet_hours');
		$taskquery = $this->db->join('timesheet_item', 'task.task_id = timesheet_item.task_id');
		$taskquery = $this->db->where('timesheet_item.timesheet_date <=', $to);
		$taskquery = $this->db->where('timesheet_item.timesheet_date >=', $from);
		$taskquery = $this->db->group_by('task.task_name');
		$taskquery = $this->db->having('count(*) > 0');
		$taskquery = $this->db->get();	
		foreach($taskquery->result() as $row)
		{    
        $rows[] = $row; //add the fetched result to the result array;
		}
		return $rows;	
	}
	
	//THIS IS THE THING TO WORK ON ON TUESDAY 12/10
	function getTasksByProject($to, $from, $client_id) {
		$rows = array();
		$projectquery = $this->db->select('task.task_name');
		$projectquery = $this->db->select('task.task_id');
		//$projectquery = $this->db->select('client.client_name');
		//$projectquery = $this->db->select('client.client_id');
		$projectquery = $this->db->from('task');
		$projectquery = $this->db->select_sum('timesheet_item.timesheet_hours');
		$projectquery = $this->db->join('project', 'project.project_id = project.client_id');
		$projectquery = $this->db->join('timesheet_item', 'project.project_id = timesheet_item.project_id');
		$projectquery = $this->db->where('client.client_id =', $client_id);
		$projectquery = $this->db->where('timesheet_item.timesheet_date <=', $to);
		$projectquery = $this->db->where('timesheet_item.timesheet_date >=', $from);
		//$projectquery = $this->db->group_by('client.client_name');
		$projectquery = $this->db->group_by('project.project_name');
		$projectquery = $this->db->having('count(*) > 0');
		$projectquery = $this->db->get();	
		foreach($projectquery->result_array() as $row)
		{    
        $rows[] = $row; //add the fetched result to the result array;
		}
		return $rows;	
	}
	
	//this is just a test to see how to load data into objects. 
	function getTaskObject() {
		$taskquery = $this->db->select('*');
		$taskquery = $this->db->from('task');
		$taskquery = $this->db->get();	
		foreach($taskquery->result('Task') as $row)
		{    
		$rows[] = $row; //add the fetched result to the result array;
		}
		//error_log(print_r($rows,true));
		return $rows;
	}
}
