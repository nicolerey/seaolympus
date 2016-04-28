<?php

class Payslip extends HR_Controller
{
	protected $active_nav = NAV_PAYSLIP;
	protected $tab_title = 'Payslip';

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Payslip_model', 'payslip');
		$this->load->model('Employee_model', 'employee');
	}

	public function index()
	{
		$employees = $this->employee->all();
		foreach($employees AS &$emp){
			$emp['fullname'] = "{$emp['lastname']}, {$emp['firstname']} {$emp['middlename']} [{$emp['id']}]";
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
		$input = elements(['id', 'amount'], $this->input->post());
		if($this->payslip->adjust($input['id'], $input['amount'])){
			$this->json_response(['result' => TRUE]);
			return;
		}
		$this->json_response(['result' => FALSE]);
	}

	public function store()
	{
		$input = elements(['month', 'employee_number' ,'adjustment'], $this->input->post());
		if($this->payslip->create($input['employee_number'], $input['month'], $input['adjustment'])){
			redirect('payslip');
		}
	}

}