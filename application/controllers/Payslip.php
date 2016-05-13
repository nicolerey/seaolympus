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
			/*$this->session->set_flashdata('mass_payroll_status_complete', $created);
			redirect('payslip');*/
		}

		/*if(!$this->employee->exists($input['employee_number'])){
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
		]);*/
	}


	public function adjust()
	{
		$input = $this->input->post();
		if(isset($input['additional_name']) || isset($input['deduction_name'])){
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
			$payroll_id = $input['id'];
			$salary_particular = [];
			$payroll_particular = [];
			if(isset($input['additional_name'])){
				foreach ($input['additional_name'] as $key => $value) {
					$salary_particular[] = [
						'employee_id' => $employee_id,
						'particulars_id' => $input['additional_name'][$key],
						'amount' => $input['additional_particular_rate'][$key]
					];
					$payroll_particular[] = [
						'payroll_id' => $payroll_id,
						'particulars_id' => $input['additional_name'][$key],
						'units' => $input['particular_units'][$key],
						'amount' => $input['additional_particular_rate'][$key]
					];
				}
			}

			if(isset($input['deduction_name'])){
				foreach ($input['deduction_name'] as $key => $value) {
					$salary_particular[] = [
						'employee_id' => $employee_id,
						'particulars_id' => $input['deduction_name'][$key],
						'amount' => $input['deduction_particular_rate'][$key]
					];
					$payroll_particular[] = [
						'payroll_id' => $payroll_id,
						'particulars_id' => $input['deduction_name'][$key],
						'units' => 0,
						'amount' => $input['deduction_particular_rate'][$key]
					];
				}
			}

			$payroll_update = [
				'current_daily_wage' => $input['basic_rate'],
				'daily_wage_units' => $input['basic_rate_units'][0]
			];

			$payroll_particulars_update = [];
			foreach ($input['particular_id'] as $key => $value) {
				$unit = 0;
				if(isset($input['units'][$key]))
					$unit = $input['units'][$key];

				$payroll_particulars_update[] = [
					'particulars_id' => $value,
					'units' => $unit,
					'amount' => $input['particular_rate'][$key]
				];
			}

			$insert_flag = 0;
			if(!empty($salary_particular) && !empty($payroll_particular)){
				if($this->payslip->insert_salary_particular($salary_particular, $payroll_particular))
					$insert_flag = 1;
			}
			else
				$insert_flag = 1;

			$update_flag = 0;
			if($this->payslip->update_payroll($payroll_id, $payroll_update, $payroll_particulars_update))
				$update_flag = 1;

			if($insert_flag && $update_flag){
				$this->output->set_output(json_encode(['result' => TRUE]));
				return;
			}

			$this->output->set_output(json_encode([
				'result' => FALSE,
				'messages' => ['Unable to make save payslip. Please try again later.']
			]));
			return;
		}
	}

	public function _perform_validation()
	{
		$input = $this->input->post();
		if(isset($input['additional_name'])){
			$this->form_validation->set_rules('additional_name[]', 'particular name', 'required');
			$this->form_validation->set_rules('additional_particular_rate[]', 'particular rate', 'required');
		}

		if(isset($input['deduction_name'])){
			$this->form_validation->set_rules('deduction_name[]', 'particular name', 'required');
			$this->form_validation->set_rules('deduction_particular_rate[]', 'particular rate', 'required');
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