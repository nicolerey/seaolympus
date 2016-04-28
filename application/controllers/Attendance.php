<?php

class Attendance extends HR_Controller
{

	protected $logged_employee = NULL;

	public function __construct()
	{
		parent::__construct();
		$this->load->model(['Employee_model' => 'employee', 'Payslip_model' => 'payslip']);
		$this->active_nav = '';
	}

	public function index()
	{
		$this->import_plugin_script(['moment.js']);
		$this->import_page_script('attendance.js');
		$this->generate_page('attendance');
	}

	public function log()
	{
		$this->form_validation->set_rules('uid', 'employee', 'required|callback__validate_uid');
		$this->form_validation->set_rules('timestamp', 'time', 'required');
		if($this->form_validation->run()){
			$this->logged_employee['log_time'] = $this->input->post('timestamp');
			if($result = $this->employee->set_attendance($this->logged_employee['id'], $this->logged_employee['log_time'])){
				$result['datetime_in'] = date_create($result['datetime_in'])->format('d-M-Y h:i A');
				if($result['datetime_out']){
					$result['datetime_out'] = date_create($result['datetime_out'])->format('d-M-Y h:i A');
				}
				$this->logged_employee += elements(['datetime_in', 'datetime_out'], $result);
				$this->json_response(['result' => TRUE, 'data' => $this->logged_employee]);
				return;
			}
			$this->json_response(['result' => FALSE]);
		}else{
			$this->json_response(['result' => FALSE, 'messages' => array_values($this->form_validation->error_array())]);
		}
	}

	public function _validate_uid($uid)
	{
		$this->form_validation->set_message('_validate_uid', "UID: {$uid} does not exist!");
		$employee = $this->employee->get_by_uid($uid);
		if($employee){
			$this->logged_employee = elements([
				'firstname', 'middlename', 'lastname', 'department', 'id_number', 'position', 'id'
			], $employee);
		}
		return $this->logged_employee !== NULL;
	}

	public function view()
	{
		$this->active_nav = NAV_VIEW_ATTENDANCE;
		$data = [];
		$test=  [];
		$range = elements(['start_date', 'end_date', 'employee_number'], $this->input->get(), NULL);
		$start_date = is_valid_date($range['start_date'], 'm/d/Y') ? date_create($range['start_date'])->format('Y-m-d') : date('Y-m-d');
		$end_date = is_valid_date($range['end_date'], 'm/d/Y') ? date_create($range['end_date'])->format('Y-m-d') : date('Y-m-d');
		$search_employee = TRUE;

		if($this->employee->exists($range['employee_number'])){
			$data = $this->employee->attendance($range['employee_number'], $start_date, $end_date);
			$test = $this->payslip->calculate($range['employee_number'], $start_date, $end_date, TRUE);
		}

		$this->import_plugin_script(['bootstrap-datepicker/js/bootstrap-datepicker.min.js']);
		$this->import_page_script(['view-attendance.js']);
		$this->generate_page('attendance/view', compact(['data', 'search_employee', 'test']));
	}



}