<?php

class Report_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
	}
	
	
	function sumHours($to, $from) {
		$sumquery = $this->db->select_sum('timesheet_hours');
		$sumquery = $this->db->from('timesheet_item');
		$sumquery = $this->db->where('timesheet_date <', $to);
		$sumquery = $this->db->where('timesheet_date >', $from);
		$sumquery = $this->db->having('count(*) > 0');
		$sumquery = $this->db->get();
		return $sumquery->result();
	}
	
	function billableHours($to, $from) {
		$billablequery = $this->db->select_sum('timesheet_hours');
		$billablequery = $this->db->from('timesheet_item');
		$billablequery = $this->db->join('project', 'project.project_id = timesheet_item.project_id');
		$billablequery = $this->db->where('project_billable =', 1);
		$billablequery = $this->db->where('timesheet_date <', $to);
		$billablequery = $this->db->where('timesheet_date >', $from);
		$billablequery = $this->db->having('count(*) > 0');
		$billablequery = $this->db->get();	
		return $billablequery->result();
	}
	
	//get out the billable type.
	function billableType($to, $from) {
		$billabletypequery = $this->db->select('project_invoice_by');
		$billabletypequery = $this->db->from('project');
		$billabletypequery = $this->db->join('timesheet_item', 'timesheet_item.project_id = project.project_id');
		$billabletypequery = $this->db->where('timesheet_date <', $to);
		$billabletypequery = $this->db->where('timesheet_date >', $from);
		$billabletypequery = $this->db->having('count(*) > 0');
		$billabletypequery = $this->db->get();	
		return $billabletypequery->result();
	}
	
	//get out the project hours and rate.
	function getProjectHourlyRate($to, $from) {
		$hourlyratequery = $this->db->select('project_hourly_rate');
		$hourlyratequery = $this->db->from('project');
		$hourlyratequery = $this->db->select_sum('timesheet_hours');
		$hourlyratequery = $this->db->join('timesheet_item', 'timesheet_item.project_id = project.project_id');
		$hourlyratequery = $this->db->where('project_billable =', 1);
		$hourlyratequery = $this->db->where('timesheet_date <', $to);
		$hourlyratequery = $this->db->where('timesheet_date >', $from);
		$hourlyratequery = $this->db->having('count(*) > 0');
		$hourlyratequery = $this->db->get();	
		return $hourlyratequery->result();
	}
	
	//get out the person hours and rate.
	
	//get out the task hours and rate.
	
	//client queries
	function getClients($to, $from) {
		$clientquery = $this->db->distinct('client_name', 'client_id');
		$clientquery = $this->db->from('client');
		$clientquery = $this->db->join('project', 'project.client_id = client.client_id');
		$clientquery = $this->db->join('timesheet_item', 'project.project_id = timesheet_item.project_id');
		$clientquery = $this->db->where('timesheet_date <', $to);
		$clientquery = $this->db->where('timesheet_date >', $from);
		$clientquery = $this->db->group_by('client_name');
		$clientquery = $this->db->get();	
		return $clientquery->result();
	}
	
	function getClientHours($to, $from) {
		$clienthoursquery = $this->db->distinct('client_name');
		$clienthoursquery = $this->db->from('client');
		$clienthoursquery = $this->db->select_sum('timesheet_hours');
		$clienthoursquery = $this->db->join('project', 'project.client_id = client.client_id');
		$clienthoursquery = $this->db->join('timesheet_item', 'project.project_id = timesheet_item.project_id');
		$clienthoursquery = $this->db->where('timesheet_date <', $to);
		$clienthoursquery = $this->db->where('timesheet_date >', $from);
		$clienthoursquery = $this->db->group_by('client_name');
		$clienthoursquery = $this->db->get();	
		return $clienthoursquery->result();
	}
	
	//project queries
	function getProjectHours($to, $from) {
		$projecthoursquery = $this->db->select('project_name');
		$projecthoursquery = $this->db->from('project');
		$projecthoursquery = $this->db->select_sum('timesheet_hours');
		$projecthoursquery = $this->db->join('timesheet_item', 'project.project_id = timesheet_item.project_id');
		$projecthoursquery = $this->db->where('timesheet_date <', $to);
		$projecthoursquery = $this->db->where('timesheet_date >', $from);
		$projecthoursquery = $this->db->group_by('project_name');
		$projecthoursquery = $this->db->get();	
		return $projecthoursquery->result();
	}
	
	function getProjects($to, $from) {
		$projectquery = $this->db->distinct('project_name', 'client_name');
		$projectquery = $this->db->from('project');
		$projectquery = $this->db->join('client', 'client.client_id = project.client_id');
		$projectquery = $this->db->join('timesheet_item', 'project.project_id = timesheet_item.project_id');
		$projectquery = $this->db->where('timesheet_date <', $to);
		$projectquery = $this->db->where('timesheet_date >', $from);
		$projectquery = $this->db->group_by('project_name');
		$projectquery = $this->db->get();	
		return $projectquery->result();
	}

}
