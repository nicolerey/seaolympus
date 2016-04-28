<?php

class Employees extends HR_Controller
{
	protected $active_nav = NAV_DATA_ENTRY;
	protected $active_subnav = SUBNAV_EMPLOYEES;

	protected $id;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Employee_model', 'employee');
	}

	public function index()
	{
		$this->import_page_script('employee-list.js');
		$this->generate_page('employees/listing', [
			'items' => $this->employee->all()
		]);
	}

	public function create()
	{
		$this->load->model(['Department_model' => 'department', 'Position_model' => 'position', 'Pay_modifier_model' => 'particulars']);
		$this->import_page_script('manage-employees.js');
		$this->import_plugin_script(['bootstrap-datepicker/js/bootstrap-datepicker.min.js', 'price-format.js']);
		$this->generate_page('employees/manage', [
			'title' => 'Create new employee',
			'mode' => MODE_CREATE, 
			'data' => [],
			'departments' => array_column($this->department->all(), 'name', 'id'),
			'particulars' => array_column($this->particulars->all(), 'name', 'id'),
			'positions' => array_column($this->position->all(), 'name', 'id'),
		]);
	}

	public function edit($id = FALSE)
	{
		if(!$id || !$employee = $this->employee->get($id)){
			show_404();
		}
		$this->load->model(['Department_model' => 'department', 'Position_model' => 'position', 'Pay_modifier_model' => 'particulars']);
		$this->import_plugin_script(['bootstrap-datepicker/js/bootstrap-datepicker.min.js', 'price-format.js']);
		$this->import_page_script('manage-employees.js');
		$this->generate_page('employees/manage', [
			'title' => 'Update existing employee',
			'mode' => MODE_EDIT, 
			'data' => $employee,
			'departments' => array_column($this->department->all(), 'name', 'id'),
			'particulars' => array_column($this->particulars->all(), 'name', 'id'),
			'positions' => array_column($this->position->all(), 'name', 'id'),
		]);
	}

	public function store()
	{
		$this->output->set_content_type('json');
		$this->_perform_validation(MODE_CREATE);
		if(!$this->form_validation->run()){
			$this->output->set_output(json_encode([
				'result' => FALSE,
				'messages' => array_values($this->form_validation->error_array())
			]));
			return;
		}
		$input = $this->_format_data(MODE_CREATE);
		if($this->employee->create($input)){
			$this->output->set_output(json_encode(['result' => TRUE]));
			return;
		}
		$this->output->set_output(json_encode([
			'result' => FALSE,
			'messages' => ['Unable to create new employee. Please try again later.']
		]));
		return;
	}

	public function update($id = FALSE)
	{
		$this->output->set_content_type('json');
		if(!$id || !$this->employee->exists($id)){
			$this->output->set_output(json_encode([
				'result' => FALSE,
				'messages' =>['Please provide a valid employee id to update.']
			]));
			return;
		}
		$this->id = $id;
		$this->_perform_validation(MODE_EDIT);
		if(!$this->form_validation->run()){
			$this->output->set_output(json_encode([
				'result' => FALSE,
				'messages' => array_values($this->form_validation->error_array())
			]));
			return;
		}
		$input = $this->_format_data(MODE_EDIT);
		if($this->employee->update($id, $input)){
			$this->output->set_output(json_encode(['result' => TRUE]));
			return;
		}
		$this->output->set_output(json_encode([
			'result' => FALSE,
			'messages' => ['Unable to update employee. Please try again later.']
		]));
		return;
	}

	public function toggle_lock()
	{
		if(!$this->employee->exists($this->input->post('id'))){
			$this->json_response(['result' => FALSE, 'messages' => ['Employee doesn\'t exit']]);
			return;
		}
		$this->json_response(['result' => (bool)$this->employee->toggle_lock($this->input->post('id'))]);
	}

	public function _perform_validation($mode)
	{
		if($mode === MODE_CREATE){
			$this->form_validation->set_rules('rfid_uid', 'RFID UID', 'trim|is_unique[employees.rfid_uid]');
			$this->form_validation->set_rules('sss_number', 'SSS #', 'required|trim|is_unique[employees.sss_number]');
			$this->form_validation->set_rules('pagibig_number', 'PAG-IBIG #', 'required|trim|is_unique[employees.pagibig_number]');
			$this->form_validation->set_rules('tin_number', 'TIN #', 'required|trim|is_unique[employees.tin_number]|');
			$this->form_validation->set_rules('email_address', 'email address', 'required|valid_email|is_unique[employees.email_address]');
		}else{
			$this->form_validation->set_rules('rfid_uid', 'RFID UID', 'callback__validate_rfid_uid');
			$this->form_validation->set_rules('email_address', 'email address', 'required|valid_email|callback__validate_email');
			$this->form_validation->set_rules('sss_number', 'SSS #', 'required|trim|callback__validate_uniqueness[sss_number]');
			$this->form_validation->set_rules('pagibig_number', 'PAG-IBIG #', 'required|trim|callback__validate_uniqueness[pagibig_number]');
			$this->form_validation->set_rules('tin_number', 'TIN #', 'required|trim|callback__validate_uniqueness[tin_number]');
		}
		$this->form_validation->set_rules('firstname', 'first name', 'required');
		$this->form_validation->set_rules('lastname', 'last name', 'required');
		$this->form_validation->set_rules('middlename', 'middle name', 'required');
		$this->form_validation->set_rules('birthdate', 'date of birth', 'required|callback__validate_date');
		$this->form_validation->set_rules('gender', 'gender', 'required|in_list[M,F]', ['in_list' => 'The %s can only be male or female.']);
		$this->form_validation->set_rules('civil_status', 'civil status', 'required|in_list[sg,m,sp,d,w]', ['in_list' => 'Please provide a valid']);
		
		$this->form_validation->set_rules('mobile_number', 'mobile number', 'required');
		$this->form_validation->set_rules('date_hired', 'date hired', 'required|callback__validate_date');
		$this->form_validation->set_rules('department_id', 'department', 'required|callback__validate_department');
		$this->form_validation->set_rules('position_id', 'position', 'required|callback__validate_position');
		$this->form_validation->set_rules('daily_rate', 'daily wage', 'required|callback__validate_numeric');
		$this->form_validation->set_rules('overtime_rate', 'overtime rate', 'required|callback__validate_numeric');
		$this->form_validation->set_rules('allowed_late_period', 'allowed late  period', 'required|callback__validate_numeric');
		$this->form_validation->set_rules('late_penalty', 'late penalty', 'required|callback__validate_numeric');

	}

	public function _format_data($mode)
	{
		$basic_info = [];
		$basic_info += elements([
			'firstname', 
			'middlename', 
			'lastname', 
			'birthplace', 
			'gender', 
			'civil_status',
			'nationality',
			'religion',
			'full_address',
			'email_address',
			'mobile_number',
			'sss_number',
			'pagibig_number',
			'tin_number',
		], $this->input->post(), NULL);
		$basic_info += [
			'birthdate' => date('Y-m-d', strtotime($this->input->post('birthdate'))),
			'date_hired' => date('Y-m-d', strtotime($this->input->post('date_hired'))),
			'daily_rate' => str_replace(',', '', $this->input->post('daily_rate')),
			'overtime_rate' => str_replace(',', '', $this->input->post('overtime_rate')),
			'allowed_late_period' => str_replace(',', '', $this->input->post('allowed_late_period')),
			'late_penalty' => str_replace(',', '', $this->input->post('late_penalty'))
		];
		if($uid = trim($this->input->post('rfid_uid'))){
			$basic_info['rfid_uid'] = $uid;
		}
		$particulars = [];
		if(is_array($temp = $this->input->post('particulars'))){
			foreach($temp AS $row){
				if(isset($row['particulars_id']) && $row['particulars_id']){
					$particulars[] = [
						'particulars_id' => $row['particulars_id'],
						'amount' => str_replace(',', '', $row['amount'])
					];
				}
			}
		}
		return [
			'employee' => $basic_info,
			'department_id' => $this->input->post('department_id'),
			'position_id' => $this->input->post('position_id'),
			'particulars' => $particulars
		];
	}

	public function _validate_rfid_uid($uid)
	{
		if(!trim($uid)){
			return TRUE;
		}
		$this->form_validation->set_message('_validate_rfid_uid', 'The %s is already taken by another employee.');
		return $this->employee->has_unique_rfid_uid($uid, $this->id);
	}

	public function _validate_email($email)
	{
		$this->form_validation->set_message('_validate_email', 'The %s is already taken by another employee.');
		return $this->employee->has_unique_email($email, $this->id);
	}

	public function _validate_uniqueness($val, $column)
	{
		$this->form_validation->set_message('_validate_uniqueness', 'The %s is already taken by another employee.');
		return $this->employee->has_unique($column, $val, $this->id);
	}

	public function _validate_date($val)
	{
		$this->form_validation->set_message('_validate_date', 'Please provide a valid %s.');
		return is_valid_date($val, 'm/d/Y');
	}

	public function _validate_department($val)
	{
		$this->load->model('Department_model', 'department');
		$this->form_validation->set_message('_validate_department', 'Please select a valid %s.');
		return $this->department->exists($val);
	}

	public function _validate_position($val)
	{
		$this->load->model('Position_model', 'position');
		$this->form_validation->set_message('_validate_position', 'Please select a valid %s.');
		return $this->position->exists($val);
	}

	public function _validate_numeric($val)
	{
		$this->form_validation->set_message('_validate_numeric', 'The %s is invalid.');
		return is_numeric(str_replace(',', '', $val));
	}
}