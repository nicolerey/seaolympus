<?php

class Payslip extends HR_Controller
{
	protected $active_nav = NAV_MY_PAYSLIP;
	protected $tab_title = 'Payslip';

	public function __construct()
	{
		parent::__construct();
		$this->load->model(['Payslip_model' => 'payslip', 'Employee_model' => 'employee']);
	}

	public function index()
	{
		$employees = $this->employee->all();
		foreach($employees AS &$emp){
			$emp['fullname'] = "{$emp['lastname']}, {$emp['firstname']} {$emp['middleinitial']} [{$emp['id']}]";
		}
		$this->import_plugin_script(['bootstrap-datepicker/js/bootstrap-datepicker.min.js']);
		$this->import_page_script(['manage-payslip.js']);
		$this->generate_page('payslip/generate', [
			'title' => 'Generate payslip',
			'employees' => array_column($employees, 'fullname', 'id')
		]);
	}

	public function generate()
	{
		$input = elements(['month', 'employee_number'], $this->input->get());

		$date = [];
		$range = phase($input['month']);

		if($input['employee_number'] === 'all'){
			$all = array_column($this->employee->all(), 'id');
			$created = 0;
			foreach($all AS $row){
				$status = $this->payslip->create($row, $input['month'], 0);
				if($status === TRUE){
					$created++;
				}
			}
			$this->session->set_flashdata('mass_payroll_status_complete', $created);
			redirect('payslip');
		}

		if(!$this->employee->exists($input['employee_number'])){
			redirect('payslip');
		}
		
		$employee_data = $this->employee->get($input['employee_number']);
		$data = $this->payslip->calculate($input['employee_number'], $range[0], $range[1]);

		if(is_numeric($data)){
			redirect("my_payslip/view/{$data}?employee_number={$employee_data['id']}");
		}

		$this->generate_page('payslip/manage', [
			'title' => 'Manage payslip',
			'data' => $data,
			'employee_data' => $employee_data,
			'from' =>  $range[0],
			'to' =>  $range[1],
			'month' => $input['month']
		]);
	}


	public function adjust()
	{
		$input = $this->input->post();
		print_r($input);
		/*if(isset($input['additional_name']) || isset($input['deduction_name'])){
			$this->_perform_validation();
			if(!$this->form_validation->run()){
				$this->output->set_output(json_encode([
					'result' => FALSE,
					'messages' => array_values($this->form_validation->error_array())
				]));
				return;
			}
		}

		if($input){
			$employee_id = $input['employee_id'];
			$salary_particular = [];
			if(isset($input['additional_name'])){
				foreach ($input['additional_name'] as $key => $value) {
					$salary_particular[] = [
						'employee_id' => $employee_id,
						'particulars_id' => $input['additional_name'][$key],
						'amount' => $input['particular_rate'][$key]
					];
				}
			}

			if(isset($input['deduction_name'])){
				foreach ($input['deduction_name'] as $key => $value) {
					$salary_particular[] = [
						'employee_id' => $employee_id,
						'particulars_id' => $input['deduction_name'][$key],
						'amount' => $input['deduction_particular_amount'][$key]
					];
				}
			}

			if(!empty($salary_particular)){
				if($this->payslip->insert_salary_particular($salary_particular)){
					$this->output->set_output(json_encode(['result' => TRUE]));
						return;
				}
			}
			else{
				$this->output->set_output(json_encode(['result' => TRUE]));
				return;
			}

			$this->output->set_output(json_encode([
				'result' => FALSE,
				'messages' => ['Unable to make a loan. Please try again later.']
			]));
			return;
		}*/
	}

	public function _perform_validation()
	{
		$input = $this->input->post();
		if(isset($input['additional_name'])){
			echo "rey";
			$this->form_validation->set_rules('additional_name[]', 'particular name', 'required');
			$this->form_validation->set_rules('particular_rate[]', 'particular rate', 'required');
		}

		if(isset($input['deduction_name'])){
			echo "arriesga";
			$this->form_validation->set_rules('deduction_name[]', 'particular name', 'required');
			$this->form_validation->set_rules('deduction_particular_amount[]', 'particular rate', 'required');
		}
	}

	public function store()
	{
		$input = elements(['month', 'employee_number' ,'adjustment'], $this->input->post());
		if($this->payslip->create($input['employee_number'], $input['month'], $input['adjustment'])){
			redirect('payslip');
		}
	}

}