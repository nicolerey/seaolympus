<?php

class Departments extends HR_Controller
{
	protected $active_nav = NAV_DATA_ENTRY;
	protected $active_subnav = SUBNAV_DEPARTMENTS;
	protected $id;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Department_model', 'department');
	}

	public function index()
	{
		$this->generate_page('departments/listing', [
			'items' => $this->department->all()
		]);
	}

	public function create()
	{
		$this->load->model('Division_model', 'division');
		$this->import_page_script('manage-departments.js');
		$this->generate_page('departments/manage', [
			'title' => 'Create new department',
			'mode' => MODE_CREATE, 
			'data' => [],
			'divisions_list' => array_column($this->division->all(), 'name', 'id')
		]);
	}

	public function edit($id = FALSE)
	{
		if(!$id || !$department = $this->department->get($id)){
			show_404();
		}
		$this->load->model('Division_model', 'division');
		$this->import_page_script('manage-departments.js');
		$this->generate_page('departments/manage', [
			'title' => 'Update existing department',
			'mode' => MODE_EDIT, 
			'data' => $department,
			'divisions_list' => array_column($this->division->all(), 'name', 'id'),
			'employees_list' => array_column($this->department->get_employees($id), 'fullname', 'id')
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
		if($this->department->create($input)){
			$this->output->set_output(json_encode(['result' => TRUE]));
			return;
		}
		$this->output->set_output(json_encode([
			'result' => FALSE,
			'messages' => ['Unable to create new department. Please try again later.']
		]));
		return;
	}

	public function update($id = FALSE)
	{
		$this->output->set_content_type('json');
		if(!$id || !$this->department->exists($id)){
			$this->output->set_output(json_encode([
				'result' => FALSE,
				'messages' =>['Please provide a valid department id to update.']
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
		if($this->department->update($id, $input)){
			$this->output->set_output(json_encode(['result' => TRUE]));
			return;
		}
		$this->output->set_output(json_encode([
			'result' => FALSE,
			'messages' => ['Unable to update department. Please try again later.']
		]));
		return;
	}

	public function _perform_validation($mode)
	{
		if($mode === MODE_CREATE){
			$this->form_validation->set_rules('id_number', 'department number', 'required|numeric|is_unique[departments.id_number]');
			$this->form_validation->set_rules('name', 'department name', 'required|is_unique[departments.name]');
		}else{
			$this->form_validation->set_rules('name', 'department name', 'required|callback__validate_department_name');
			$this->form_validation->set_rules('employee_id', 'department supervisor', 'required|callback__validate_employee');
		}
		$this->form_validation->set_rules('division_id', 'division', 'required|callback__validate_division');
	}

	public function _format_data($mode)
	{
		$data = [];
		if($mode === MODE_CREATE){
			$data += [ 'department' => elements(['id_number', 'name'], $this->input->post()) ];
		}else{
			$data += [ 'department' => ['name' => $this->input->post('name')], 'employee_id' => $this->input->post('employee_id') ];
		}
		$data += ['division_id' => $this->input->post('division_id')];
		return $data;
	}

	public function _validate_department_name($val)
	{
		$this->form_validation->set_message('_validate_department_name', 'The %s is already in use.');
		return $this->department->has_unique_name($val, $this->id);
	}

	public function _validate_division($val)
	{
		$this->load->model('Division_model', 'division');
		$this->form_validation->set_message('_validate_division', 'Please select a valid %s');
		return $this->division->exists($val);
	}

	public function _validate_employee($val)
	{
		$this->form_validation->set_message('_validate_employee', 'Please ensure that the %s under the selected department');
		return $this->department->has_employee($this->id, $val);
	}
}