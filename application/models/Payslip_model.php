<?php

class Payslip_model extends CI_Model
{
	public function calculate($employee_id, $from, $to, $bypass_check = FALSE)
	{
		if((($id = $this->check($employee_id, $from, $to)) !== TRUE) && !$bypass_check){
			return $id;
		}

		$this->load->model(['Employee_model' => 'employee', 'Position_model' => 'position', 'Loan_model' => 'loan']);
		$employee_data = $this->employee->get($employee_id);
		$position = $this->position->get($employee_data['position_id']);

		$attendance = $this->employee->get_attendance($employee_id, $from, $to);
		if(!$attendance){
			return [];
		}

		$total_regular_hrs = 0;
		$total_overtime_hrs = 0;
		$total_late_minutes = 0;

		// real actual hrs rendered
		$actual_hrs_rendered = 0;

		$pos_workday = json_decode($position['workday'], true);
		$workdays = [];
		foreach ($pos_workday as $key=>$value) {
			$workhours = 0;
			$first_workhours = 0;
			$second_workhours = 0;
			$time_count = 0;
			foreach ($value['time_breakdown'] as $key1 => $value1) {
				foreach ($value1 as $key2 => $value2) {
					$from_time = $value2[0];
					if($from_time=='12:00 AM')
						$from_time = '00:00:00';

					$to_time = $value2[1];

					$hour = $to_time - $from_time;
					$minute = date_format(date_create($to_time), 'i') - date_format(date_create($from_time), 'i');

					$total = abs($hour + ($minute/60));
					$workhours += $total;
					if($time_count<2)
						$first_workhours += $total;
					else
						$second_workhours += $total;
				}
			}

			array_push($workdays, [
				'from_day' => $value['from_day'],
				'to_day' => $value['to_day'],
				'time_breakdown' => $value['time_breakdown'],
				'total_workhours' => $workhours
			]);
		}

		/*echo "<pre>";
		print_r($workdays);
		echo "</pre>";*/

		$emp_att = [];
		if(!empty($pos_workday)){
			foreach($attendance AS $row){
				$datetime_in = date_create($row['datetime_in']); //datetime in
				$datetime_out = date_create($row['datetime_out']); //datetime out

				//extract time from datetime in and out
				$time_in = date_format($datetime_in, 'h:i A');
				$time_out = date_format($datetime_out, 'h:i A');			

				$day_in = date_format($datetime_in, 'N'); //get day in numeric form
				$day_out = date_format($datetime_out, 'N');

				$date_in = date_format($datetime_in, 'Y-m-d');				
				$date_out = date_format($datetime_out, 'Y-m-d');

				if(!$row['datetime_in'] || !$row['datetime_out']){
					continue;
				}

				$workday_index = $this->search_for_day_in_workday($pos_workday, $day_in);
				if(!empty($workday_index)){
					$att_flag = 0;
					foreach ($workday_index as $key => $value) {
						foreach ($workdays[$value]['time_breakdown'][$day_in] as $key1 => $value1) {
							$late = 0;
							$workhours = 0;
							if($value1[0]<=$time_in && $value1[1]>=$time_in){
								$time_diff = date_diff(date_create($time_in), date_create($value1[0]));
								$late = ($time_diff->h * 60) + $time_diff->i + ($time_diff->s / 60);

								$workhours = 0;
								if(($time_out - $time_in)<0){
									$hour = '12:00 AM' - $time_in;
									$minute = date_format(date_create('00:00:00'), 'i') - date_format(date_create($time_in), 'i');
									$workhours += abs($hour + ($minute/60));

									$hour = $time_out - '00:00:00';
									$minute = date_format(date_create($time_out), 'i') - date_format(date_create('00:00:00'), 'i');
									$workhours += abs($hour + ($minute/60));
								}
								else{
									$hour = $time_out - $time_in;
									$minute = date_format(date_create($time_out), 'i') - date_format(date_create($time_in), 'i');
									$workhours = abs($hour + ($minute/60));
								}

								$start_date = $date_in;
								$end_date = $date_out;
								if($workdays[$value]['from_day']!=$workdays[$value]['to_day'])
									$end_date = date_format(date_modify(date_create($start_date), "+1 day"), 'Y-m-d');								

								$att_flag = 1;
							}
							//else if($value1[0]>=$time_in && ($value1[1]<=$time_out))

							if($att_flag){
								$emp_att_flag = 1;
								if(!empty($emp_att)){
									foreach($emp_att as $emp_att_index=>$emp_att_value){
										if($emp_att_value['start_date']==$start_date && $emp_att_value['end_date']==$end_date && $emp_att_value['workday_index']==$value){
											$emp_att[$emp_att_index]['total_late'] += $late;
											$emp_att[$emp_att_index]['total_working_hours'] += $workhours;

											$emp_att_flag = 0;
											break;
										}
									}
								}

								if($emp_att_flag){
									array_push($emp_att, [
										'start_date' => $start_date,
										'end_date' => $end_date,
										'workday_index' => $value,
										'total_late' => $late,
										'total_working_hours' => $workhours
									]);
								}

								break;
							}
						}

						if($att_flag)
							break;
					}
				}
			}
		}

		/*echo "<pre>";
		print_r($emp_att);
		print_r($employee_data);
		echo "</pre>";*/

		// $data['salary_template'] = $salary;
		$data['employee_profile'] = $employee_data;
		$data['attendance'] = $attendance;
		$data['additionals'] = [];
		$data['deductions'] = [];
		$data['total_daily_deductions'] = 0;
		$data['total_monthly_deductions'] = 0;
		$data['total_deductions'] = 0;
		$data['total_daily_additionals'] = 0;
		$data['total_monthly_additionals'] = 0;
		$data['total_additionals'] = 0;

		$emp_loan = $this->loan->all($employee_data['id'], NULL, NULL, NULL, $from, $to);
		if($emp_loan){
			foreach ($emp_loan as $key => $loan) {
				foreach ($loan['payment_terms'] as $payment_key => $payment_value) {
					$data['deductions']['loan'][] = $payment_value;
					$data['total_deductions'] += $payment_value['payment_amount'];
				}
			}
		}

		array_map(function($var) USE(&$data){
			if($var['type'] === 'a'){
				$data['additionals'][] = $var;
				if($var['particular_type'] === 'd')
					$data['total_daily_additionals']  += $var['amount'];
				else if($var['particular_type'] === 'm')
					$data['total_monthly_additionals']  += $var['amount'];
				return;
			}
			$data['deductions'][] = $var;
			if($var['particular_type'] === 'd')
				$data['total_daily_deductions']  += $var['amount'];
			else if($var['particular_type'] === 'm')
				$data['total_monthly_deductions']  += $var['amount'];
		}, $employee_data['particulars']);

		$total_overtime_hrs = 0;
		$total_regular_hrs = 0;
		$total_late_minutes = 0;
		$total_regular_days = 0;
		$data['regular_overtime_pay'] = 0;
		if(!empty($emp_att)){
			foreach ($emp_att as $key => $value) {
				if($value['total_late']<=$employee_data['allowed_late_period'] && $value['total_working_hours']>=$workdays[$value['workday_index']]['total_workhours']){
					$total_late_minutes += $value['total_late'];
					$total_regular_hrs += $workdays[$value['workday_index']]['total_workhours'];
					$overtime_hours = $value['total_working_hours'] - $workdays[$value['workday_index']]['total_workhours'];
					$total_overtime_hrs += $overtime_hours;

					$overtime_hrly = ($employee_data['daily_rate'] * $employee_data['overtime_rate']) / 100;
					$data['regular_overtime_pay'] += round($overtime_hrly * $overtime_hours);

					$total_regular_days++;
				}
			}
		}

		$data['daily_wage'] = $employee_data['daily_rate'];
		$data['late_penalty'] = $employee_data['late_penalty'];

		$data['total_regular_days'] = $total_regular_days;
		$data['total_overtime_hrs'] = round($total_overtime_hrs, 2);
		$data['total_late_minutes'] = round($total_late_minutes, 2);
		$data['total_late_deduction'] = $data['total_late_minutes'] * $employee_data['late_penalty'];

		$data['regular_pay'] = round($data['total_regular_days'] * $employee_data['daily_rate'], 2);

		$data['total_earnings'] = $data['regular_pay'] + $data['regular_overtime_pay'];

		$data['total_additionals'] += ($data['total_regular_days'] * $data['total_daily_additionals']);
		$data['total_deductions'] += ($data['total_regular_days'] * $data['total_daily_deductions']);

		$data['net_pay'] = $data['total_earnings'] + $data['total_additionals'] - $data['total_deductions'] - $data['total_late_deduction'];
		
		/*echo "<pre>";
		print_r($data);
		echo "</pre>";*/
		return $data;
	}

	public function get_date_difference($datetime_1, $datetime_2)
	{
		$date_difference = date_diff($datetime_2, $datetime_1);
		if($date_difference->invert==1){
			$hour = $date_difference->h + ($date_difference->d * 24);
			$minutes = $date_difference->i / 60;

			return $hour+$minutes;
		}

		return NULL;
	}

	public function search_for_day_in_workday($data, $value_to_search)
	{
		$value_index = [];
		foreach ($data as $key => $value) {
			if($value['from_day']==$value_to_search || $value['to_day']==$value_to_search)
				array_push($value_index, $key);
		}

		return $value_index;
	}

	public function create($employee_number, $month, $adjustment = 0)
	{
		$range = phase($month);
		$payslip = $this->calculate($employee_number, $range[0], $range[1]);
		if(is_numeric($payslip) || empty($payslip)){
			return;
		}
		$data = [
			'employee_id' => $employee_number,
			'start_date' => $range[0],
			'end_date' => $range[1],
			'days_rendered' => $payslip['total_regular_days'],
			'overtime_hours_rendered' => $payslip['total_overtime_hrs'],
			'late_minutes' => $payslip['total_late_minutes'],
			'wage_adjustment' => $adjustment,
			'current_daily_wage' => $payslip['daily_wage'],
			'current_late_penalty' => $payslip['late_penalty'],
			'overtime_pay' => $payslip['regular_overtime_pay'],
			'created_by' => user_id(),
		];
		$this->db->trans_start();

		$this->db->insert('payroll', $data);
		$id = $this->db->insert_id();

		$particulars = [];
		foreach($payslip['employee_profile']['particulars'] AS $p){
			$particulars[] = [
				'payroll_id' => $id,
				'particulars_id' => $p['particulars_id'],
				'amount' => $p['amount']
			];
		}

		if(!empty($particulars)){
			$this->db->insert_batch('payroll_particulars', $particulars);
		}

		$this->db->trans_complete();

		return $this->db->trans_status();
	}

	public function check($employee_id, $from, $to)
	{
		$this->db->select('id')->from('payroll')->where([
			'employee_id' => $employee_id,
			'start_date' => $from,
			'end_date' => $to,
		]);
		$result = $this->db->get()->row_array();
		return $result ? $result['id'] : TRUE;
	}

	public function all($employee_id = FALSE)
	{
		$this->db->select('p.start_date, p.end_date, p.id, e.firstname, e.middleinitial, e.lastname')->from('payroll AS p')->join('employees AS e', 'p.employee_id = e.id');
		if($employee_id){
			$this->db->where('employee_id', $employee_id);
		}
		$this->db->order_by('end_date', 'DESC');
		return $this->db->get()->result_array();
	}

	public function adjust($id, $amount)
	{
		return $this->db->update('payroll', ['wage_adjustment' => $amount], ['id' => $id]);
	}

	public function get_by_employee($id, $employee_id)
	{
		$this->load->model(['Loan_model' => 'loan']);
		$data = $this->db->get_where('payroll', ['id' => $id, 'employee_id' => $employee_id])->row_array();
		if($data){
			$data['particulars'] = ['deductions' => [], 'additionals' => []];
			$this->db->select('p.id, p.name, p.type, pp.amount');
			$this->db->from('payroll_particulars AS pp');
			$this->db->join('pay_modifiers AS p', 'p.id = pp.particulars_id');
			$this->db->where('payroll_id', $data['id']);
			$particulars = $this->db->get()->result_array();
			foreach($particulars AS $p){
				if($p['type'] === 'a'){
					$data['particulars']['additionals'][] = $p;
				}else{
					$data['particulars']['deductions'][] = $p;
				}
			}
			$loan = $this->loan->all($employee_id, NULL, NULL, NULL, $data['start_date'], $data['end_date']);
			if($loan){
				foreach ($loan as $loan_key => $loan_value) {
					foreach ($loan_value['payment_terms'] as $payment_key => $payment_value) {
						$data['particulars']['deductions']['loan'][] = $payment_value;
					}
				}
			}
		}
		return $data;
		
	}

	public function insert_salary_particular($salary_particular)
	{
		return $this->db->insert_batch('salary_particulars', $salary_particular);
	}
}