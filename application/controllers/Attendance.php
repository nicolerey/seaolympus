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
				'firstname', 'middleinitial', 'lastname', 'department', 'id_number', 'position', 'id'
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

		if(!empty($range['start_date']))
			$start_date = is_valid_date($range['start_date'], 'm/d/Y') ? date_create($range['start_date'])->format('Y-m-d') : date('Y-m-d');
		else
			$start_date = NULL;

		if(!empty($range['end_date']))
			$end_date = is_valid_date($range['end_date'], 'm/d/Y') ? date_create($range['end_date'])->format('Y-m-d') : date('Y-m-d');
		else
			$end_date = NULL;

		$search_employee = TRUE;

		if(!empty($range['employee_number'])){
			if($this->employee->exists($range['employee_number'])){
				$emp_result = $this->employee->attendance($range['employee_number'], $start_date, $end_date);
				//$test = $this->payslip->calculate($range['employee_number'], $start_date, $end_date, TRUE);
			}
		}
		else{
			$emp_result = $this->employee->attendance();
		}

		if($emp_result){
			$x = 0;
			foreach ($emp_result as $attendance) {
				$name = $this->employee->get_employee_name($attendance['employee_id']);
				$data[$x]['name'] = $name['firstname']." ".$name['middleinitial']." ".$name['lastname'];
				$data[$x]['date'] = date_format(date_create($attendance['datetime_in']), 'Y-m-d');
				$data[$x]['in'] = date_format(date_create($attendance['datetime_in']), 'h:i A');
				$data[$x]['out'] = date_format(date_create($attendance['datetime_out']), 'h:i A');
				$data[$x]['total_hours'] = number_format(((date_format(date_create($data[$x]['out']), 'i')/60) + (date_format(date_create($data[$x]['out']), 'H'))) - ((date_format(date_create($data[$x]['in']), 'i')/60) + (date_format(date_create($data[$x]['in']), 'H'))), 2);

				$x++;
			}
		}

		$this->import_plugin_script(['bootstrap-datepicker/js/bootstrap-datepicker.min.js']);
		$this->import_page_script(['view-attendance.js']);
		$this->generate_page('attendance/view', compact(['data', 'search_employee', 'test']));
	}

	public function upload_attendance(){
		$config['upload_path']   = './assets/uploads/'; 
		$config['allowed_types'] = 'txt';
		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('userfile')) {
			/*$error = array('error' => $this->upload->display_errors()); 
			$this->load->view('upload_form', $error);*/ 
			echo $this->upload->display_errors();
		}
		else { 
			/*$data = array('upload_data' => $this->upload->data()); 
			$this->load->view('upload_success', $data);*/

			$file_info = $this->upload->data();

			$this->load->helper('file');

			$string = file_get_contents('./assets/uploads/'.$file_info['file_name']);

			$row = explode("\n", $string);
			unset($row[0]);
			$data = [];
			foreach ($row as $key=>$value) {
				$col_val = explode("\t", $value);

				if(isset($col_val[2]) && isset($col_val[9])){
					$id = intval($col_val[2]);
					$attendance_result = $this->employee->check_empty_attendance($id);
					$bio_in = new DateTime(trim($col_val[9]));

					echo "<pre>";
					print_r($attendance_result);
					//print_r($bio_in);
					echo "</pre>";

					/*if($attendance_result){
						$att_in = new DateTime($attendance_result['datetime_in']);
						if($bio_in->format("Y-m-d")==$att_in->format("Y-m-d"))
							$this->employee->update_employee_attendance($att_in, $bio_in);
						else{

						}
					}
					else
						$this->employee->insert_employee_attendance(['employee_id'=>$id, 'datetime_in'=>$bio_in]);*/
				}
			}

			unlink('./assets/uploads/'.$file_info['file_name']);
		}
	}
}