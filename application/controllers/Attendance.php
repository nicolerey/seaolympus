<?php

class Attendance extends HR_Controller
{

	protected $logged_employee = NULL;

	public function __construct()
	{
		parent::__construct();
		$this->load->model(['Employee_model' => 'employee', 'Payslip_model' => 'payslip', 'Position_model' => 'position']);
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

		$upload_batch_id = $this->employee->get_batch_id();

		$search_employee = TRUE;

		if(!empty($range['employee_number'])){
			if($this->employee->exists($range['employee_number'])){
				$emp_result = $this->employee->attendance($range['employee_number'], $start_date, $end_date, $upload_batch_id-1);
			}
		}
		else
			$emp_result = $this->employee->attendance(NULL, NULL, NULL, $upload_batch_id-1);

		if($emp_result){
			$x = 0;
			foreach ($emp_result as $attendance) {
				$name = $this->employee->get_employee_name($attendance['employee_id']);
				$data[$x]['emp_attendance_id'] = $attendance['id'];
				$data[$x]['name'] = $name['firstname']." ".$name['middleinitial']." ".$name['lastname'];
				$data[$x]['datetime_in'] = ($attendance['datetime_in']) ? date_format(date_create($attendance['datetime_in']), 'Y-m-d h:i A') : NULL;
				$data[$x]['datetime_out'] = ($attendance['datetime_out']) ? date_format(date_create($attendance['datetime_out']), 'Y-m-d h:i A') : NULL;
				$date_diff = date_diff(date_create($attendance['datetime_out']), date_create($attendance['datetime_in']));
				$data[$x]['total_hours'] = number_format(($date_diff->d * 24) + $date_diff->h + ($date_diff->i / 60) + ($date_diff->s / 60 / 60), 2);

				$x++;
			}
		}

		$this->import_plugin_script(['bootstrap-datepicker/js/bootstrap-datepicker.min.js', 'x_editable/bootstrap3-editable/js/bootstrap-editable.min.js', 'bootstrap-datetimepicker-smalot/js/bootstrap-datetimepicker.min.js', 'moment.js']);
		$this->import_page_script(['view-attendance.js']);
		$this->generate_page('attendance/view', array('data'=>$data, 'search_employee'=>$search_employee, 'test'=>$test));
	}

	public function upload_attendance(){
		$config['upload_path']   = './assets/uploads/'; 
		$config['allowed_types'] = 'txt';
		$this->load->library('upload', $config);
		$u_status = 0;

		if (!$this->upload->do_upload('userfile')){
			$this->session->set_flashdata('upload_status', 0);
			redirect('attendance/view');
		}
		else {
			$file_info = $this->upload->data();

			$this->load->helper('file');

			$string = file_get_contents('./assets/uploads/'.$file_info['file_name']);

			$row = explode("\n", $string);
			unset($row[0]);

			$upload_batch_id = $this->employee->get_batch_id();
			foreach ($row as $value) {
				$col_val = explode("\t", $value);
				$insert_flag = 1;

				if(isset($col_val[2]) && isset($col_val[9])){
					$bio_id = $col_val[2];
					$bio_datetime = new DateTime(trim($col_val[9]));

					$emp_id = $this->employee->get_by_uid($bio_id);
					if($emp_id){
						$attendance_result = $this->employee->check_empty_attendance($emp_id['id'], $bio_datetime->format('Y-m-d H:i:s'), $upload_batch_id);
						if($attendance_result){
							foreach($attendance_result as $att_result){
								$date_difference = date_diff(date_create(trim($col_val[9])), date_create($att_result['datetime_in']));
								if($date_difference->days<2 && $date_difference->invert==1){
									$this->employee->update_employee_attendance($att_result['id'], $bio_datetime->format('Y-m-d H:i:s'));
									$insert_flag = 0;
									break;
								}
							}
						}
						
						if($insert_flag){
							$this->employee->insert_employee_attendance(['employee_id'=>$emp_id['id'], 'datetime_in'=>$bio_datetime->format('Y-m-d H:i:s'), 'upload_batch'=>$upload_batch_id]);
						}
					}
				}
			}

			unlink('./assets/uploads/'.$file_info['file_name']);

			$this->session->set_flashdata('upload_status', 1);
			redirect('attendance/view');
		}
	}

	public function save_datetime()
	{
		$data = [];
		$input = $this->input->post();
		$data['id'] = $input['pk'];
		if($input['name']=='datetime_in'){
			// save datetime_out
			$data['datetime_in'] = date_format(date_create($input['value']), 'Y-m-d H:i:s');
		}
		else{
			// save datetime_in
			$data['datetime_out'] = date_format(date_create($input['value']), 'Y-m-d H:i:s');
		}

		if($this->employee->update_emp_att($data)){
			$this->output->set_output(json_encode([
				'status' => 'ok',
				'msg' => 'Value updated successfuly.'
			]));
		}
		else{
			$this->output->set_output(json_encode([
				'status' => 'error',
				'msg' => 'Value updated unsuccessfuly.'
			]));
		}

		return;
	}
}