<?php

class positions extends HR_Controller
{
	protected $active_nav = NAV_DATA_ENTRY;
	protected $active_subnav = SUBNAV_POSITIONS;
	protected $id;

	protected $days = [
		1 => 'Monday', 
		2 => 'Tuesday', 
		3 => 'Wednesday', 
		4 => 'Thursday', 
		5 => 'Friday', 
		6 => 'Saturday', 
		7 => 'Sunday'
	];

	protected $fields = [
		['name' => 'day_of_week_start', 	'label' => 'day of week start', 	'rules' => 'required|callback__validate_day_of_week'],
		['name' => 'day_of_week_end', 		'label' => 'day of week end', 		'rules' => 'required|callback__validate_day_of_week'],
		['name' => 'hour_of_day_start_am', 	'label' => 'time in (AM)', 			'rules' => 'required|callback__validate_am_time_in'],
		['name' => 'hour_of_day_start_pm', 	'label' => 'time in (PM)', 			'rules' => 'required|callback__validate_pm_time_in'],
		['name' => 'hour_of_day_end_am', 	'label' => 'time out (AM)', 		'rules' => 'required|callback__validate_am_time_out'],
		['name' => 'hour_of_day_end_pm', 	'label' => 'time out (PM)', 		'rules' => 'required|callback__validate_pm_time_out']
	];

	protected $validation_errors = [];

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Position_model', 'position');
	}

	public function index()
	{
		$this->generate_page('positions/listing', [
			'items' => $this->position->all()
		]);
	}

	public function create()
	{
		$this->import_plugin_script('bootstrap-timepicker/bootstrap-timepicker.min.js');
		$this->import_page_script('manage-positions.js');
		$this->generate_page('positions/manage', [
			'title' => 'Create new position',
			'mode' => MODE_CREATE, 
			'days' => $this->days,
			'data' => []
		]);
	}

	public function edit($id = FALSE)
	{
		if(!$id || !$position = $this->position->get($id)){
			show_404();
		}
		$this->import_plugin_script('bootstrap-timepicker/bootstrap-timepicker.min.js');
		$this->import_page_script('manage-positions.js');
		$this->generate_page('positions/manage', [
			'title' => 'Update existing position',
			'mode' => MODE_EDIT, 
			'days' => $this->days,
			'data' => $position
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
		if($this->position->create($input)){
			$this->output->set_output(json_encode(['result' => TRUE]));
			return;
		}
		$this->output->set_output(json_encode([
			'result' => FALSE,
			'messages' => ['Unable to create new position. Please try again later.']
		]));
		return;
	}

	public function update($id = FALSE)
	{
		$this->output->set_content_type('json');
		if(!$id || !$this->position->exists($id)){
			$this->output->set_output(json_encode([
				'result' => FALSE,
				'messages' =>['Please provide a valid position id to update.']
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
		if($this->position->update($id, $input)){
			$this->output->set_output(json_encode(['result' => TRUE]));
			return;
		}
		$this->output->set_output(json_encode([
			'result' => FALSE,
			'messages' => ['Unable to update position. Please try again later.']
		]));
		return;
	}

	public function _perform_validation($mode)
	{
		if($mode === MODE_CREATE){
			$this->form_validation->set_rules('name', 'position name', 'required|is_unique[positions.name]');
		}else{
			$this->form_validation->set_rules('name', 'position name', 'required|callback__validate_position_name');
		}
		$this->form_validation->set_rules('login_type', 'login account type', 'required|in_list[sv,re,po,hr]', [
			'in_list' => 'Please provide a valid %s.'
		]);
		foreach($this->fields AS $field){
			$this->form_validation->set_rules($field['name'], $field['label'], $field['rules']);
		}
	}

	public function _format_data($mode)
	{
		$input = $this->input->post();
		$data = elements(['name', 'login_type'], $this->input->post());
		$data += [
			'hour_of_day_start_am' => date_create($input['hour_of_day_start_am'])->format('H:i'),
			'hour_of_day_end_am' => date_create($input['hour_of_day_end_am'])->format('H:i'),
			'hour_of_day_start_pm' => date_create($input['hour_of_day_start_pm'])->format('H:i'),
			'hour_of_day_end_pm' => date_create($input['hour_of_day_end_pm'])->format('H:i'),
			'day_of_week_start' => $input['day_of_week_start'],
			'day_of_week_end' => $input['day_of_week_end']
		];
		return $data;
	}

	public function _validate_position_name($val)
	{
		$this->form_validation->set_message('_validate_position_name', 'The %s is already in use.');
		return $this->position->has_unique_name($val, $this->id);
	}

	public function _validate_day_of_week($val)
	{
		$this->form_validation->set_message('_validate_day_of_week', 'Please select %s.');
		return in_array($val, array_keys($this->days));
	} 

	public function _validate_am_time_in($val)
	{
		$this->form_validation->set_message('_validate_am_time_in', "Please enter a valid %s. ({$val})");
		if(!is_valid_date($val, 'g:i A')){
			return FALSE;
		}
		$from = date_create('00:00');
		$time = date_create($val);
		return ($time >= $from);
	}

	public function _validate_am_time_out($val)
	{
		$this->form_validation->set_message('_validate_am_time_out', "Please enter a valid %s. ({$val})");
		if(!is_valid_date($val, 'g:i A')){
			return FALSE;
		}
		$from = date_create($this->input->post('hour_of_day_start_am'));
		$time = date_create($val);
		return ($time >= $from);
	}

	public function _validate_pm_time_in($val)
	{
		$this->form_validation->set_message('_validate_pm_time_in', "Please enter a valid %s. ({$val})");
		if(!is_valid_date($val, 'g:i A')){
			return FALSE;
		}
		$from = date_create($this->input->post('hour_of_day_end_am'));
		$time = date_create($val);
		return ($time >= $from);
	}

	public function _validate_pm_time_out($val)
	{
		$this->form_validation->set_message('_validate_pm_time_out', "Please enter a valid %s. ({$val})");
		if(!is_valid_date($val, 'g:i A')){
			return FALSE;
		}
		$from = date_create($this->input->post('hour_of_day_start_pm'));
		$to = date_create('23:59');
		$time = date_create($val);
		return ($time >= $from) && ($time <= $to);
	}

	public function sample()
	{
		$a = date_create('8:00 AM');
		$b = date_create('00:00');
		$c = date_create('12:00');
		echo ($a >= $b) && ($a <= $c);
		echo "";
	}


}