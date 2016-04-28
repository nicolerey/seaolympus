<?php

class My_payslip extends HR_Controller
{
	protected $tab_title = 'View my payslip';
	protected $active_nav = NAV_MY_PAYSLIP;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Payslip_model', 'payslip');
	}

	public function index()
	{
		$this->generate_page('my-payslip/listing', [
			'items' => $this->payslip->all(user_id())
		]);
	}

	public function view($id)
	{
		$employee_id = $this->input->get('employee_number') ? $this->input->get('employee_number') : user_id();
		$this->import_page_script('adjust-payslip.js');
		$this->load->model('Employee_model', 'employee');
		$this->generate_page('my-payslip/view', [
			'payslip' => $this->payslip->get_by_employee($id, $employee_id),
			'employee_data' => $this->employee->get($employee_id)
		]);
	}


}