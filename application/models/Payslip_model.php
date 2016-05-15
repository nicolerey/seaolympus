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

		$upload_batch_id = $this->employee->get_batch_id();
		$attendance = $this->employee->get_attendance($employee_id, $from, $to, $upload_batch_id-1);
		if(!$attendance){
			return [];
		}

		$total_regular_hrs = 0;
		$total_overtime_hrs = 0;
		$total_late_minutes = 0;

		// real actual hrs rendered
		$actual_hrs_rendered = 0;

		$pos_workday = json_decode($position['workday'], true);

		/*echo "<pre>";
		print_r($pos_workday);
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
				/*echo "<pre>";
				print_r($workday_index);
				echo "</pre><br><br>";*/
				if(!empty($workday_index)){
					foreach ($workday_index as $key => $value) {
						$late = 0;
						$workhours = 0;
						$am_workhours = 0;
						$pm_workhours = 0;

						$att_flag = 0;
						$am_flag = 1;
						$first_workday_date1 = date_create($date_in." ".$pos_workday[$value]['time']['from_time_1']);
						$first_workday_date2 = date_create($date_in." ".$pos_workday[$value]['time']['to_time_1']);
						$second_workday_date1 = date_create($date_in." ".$pos_workday[$value]['time']['from_time_2']);
						$second_workday_date2 = date_create($date_in." ".$pos_workday[$value]['time']['to_time_2']);

						if(($pos_workday[$value]['time']['to_time_1'] - $pos_workday[$value]['time']['from_time_1'])<0){
							$first_workday_date2 = date_modify($first_workday_date2, "+1 day");
							$second_workday_date1 = date_modify($second_workday_date1, "+1 day");
							$second_workday_date2 = date_modify($second_workday_date2, "+1 day");
						}
						else if(($pos_workday[$value]['time']['to_time_2'] - $pos_workday[$value]['time']['from_time_2'])<0){
							$second_workday_date1 = date_modify($second_workday_date1, "+1 day");
							$second_workday_date2 = date_modify($second_workday_date2, "+1 day");
						}

						$broken_time = 0;
						if(($first_workday_date2>$datetime_in && $first_workday_date2<$datetime_out) && ($second_workday_date1>$datetime_in && $second_workday_date1<$datetime_out)){
							$time_diff = date_diff($first_workday_date2, $datetime_in);
							if($first_workday_date1<$datetime_in){
								$late_diff = date_diff($datetime_in, $first_workday_date1);
								$late = ($late_diff->h * 60) + $late_diff->i + ($late_diff->s / 60);
							}
							else if($first_workday_date1>$datetime_in)
								$time_diff = date_diff($first_workday_date2, $first_workday_date1);

							$workhours += $time_diff->h + ($time_diff->i / 60) + ($time_diff->i / 60 / 60);
							$am_workhours += $workhours;

							$time_diff = date_diff($datetime_out, $second_workday_date1);
							$hours = $time_diff->h + ($time_diff->i / 60) + ($time_diff->i / 60 / 60);
							$workhours += $hours;
							$pm_workhours += $hours;

							$att_flag = 1;
							$broken_time = 1;
						}
						else{
							$second_workday_date1 = date_modify($second_workday_date1, "-1 day");
							$second_workday_date2 = date_modify($second_workday_date2, "-1 day");

							if($first_workday_date1<=$datetime_in && $first_workday_date2>=$datetime_in){
								$late_diff = date_diff($datetime_in, $first_workday_date1);
								$late = ($late_diff->h * 60) + $late_diff->i + ($late_diff->s / 60);

								$time_diff = date_diff($datetime_out, $datetime_in);
								$workhours += $time_diff->h + ($time_diff->i / 60) + ($time_diff->i / 60 / 60);
								$am_workhours += $workhours;

								$am_flag = 0;
								$att_flag = 1;
							}
							else if($first_workday_date1>$datetime_in && $first_workday_date1<$datetime_out){
								$time_diff = date_diff($datetime_out, $first_workday_date1);
								$workhours += $time_diff->h + ($time_diff->i / 60) + ($time_diff->i / 60 / 60);
								$am_workhours += $workhours;

								$am_flag = 0;
								$att_flag = 1;
							}

							if($am_flag){
								if($second_workday_date1<=$datetime_in && $second_workday_date2>=$datetime_in){
									$late_diff = date_diff($datetime_in, $second_workday_date1);
									$late = ($late_diff->h * 60) + $late_diff->i + ($late_diff->s / 60);

									$time_diff = date_diff($datetime_out, $datetime_in);
									$workhours += $time_diff->h + ($time_diff->i / 60) + ($time_diff->i / 60 / 60);
									$pm_workhours += $workhours;

									$att_flag = 1;
								}
								else if($second_workday_date1>$datetime_in && $second_workday_date1<$datetime_out){
									$time_diff = date_diff($datetime_out, $datetime_in);
									$workhours += $time_diff->h + ($time_diff->i / 60) + ($time_diff->i / 60 / 60);
									$pm_workhours += $workhours;

									$att_flag = 1;
								}
							}
						}

						if($broken_time){
							$start_date = date_format($first_workday_date1, 'Y-m-d');
							$end_date = date_format($second_workday_date2, 'Y-m-d');						
						}
						else{
							if(!$am_flag){
								$start_date = date_format($first_workday_date1, 'Y-m-d');
								$end_date = date_format($first_workday_date2, 'Y-m-d');
							}
							else{
								$start_date = date_format(date_modify($second_workday_date1, '-1 day'), 'Y-m-d');
								$end_date = date_format($second_workday_date2, 'Y-m-d');
							}
						}

						if($att_flag){
							$emp_att_flag = 1;
							if(!empty($emp_att)){
								foreach($emp_att as $emp_att_index=>$emp_att_value){
									if($emp_att_value['start_date']==$start_date && $emp_att_value['end_date']==$end_date && $emp_att_value['workday_index']==$value){
										$emp_att[$emp_att_index]['total_late'] += $late;
										$emp_att[$emp_att_index]['total_first_hours'] += $am_workhours;
										$emp_att[$emp_att_index]['total_second_hours'] += $pm_workhours;
										$emp_att[$emp_att_index]['total_working_hours'] += $workhours;

										$emp_att_flag = 0;
										break;
									}
								}
							}

							$emp_att_workday_start = date_format(date_create($start_date), 'N');
							$emp_att_workday_end = date_format(date_create($end_date), 'N');
							if($pos_workday[$value]['from_day']!=$emp_att_workday_start || $pos_workday[$value]['to_day']!=$emp_att_workday_end)
								continue;

							if($emp_att_flag){
								array_push($emp_att, [
									'start_date' => $start_date,
									'end_date' => $end_date,
									'workday_index' => $value,
									'total_late' => $late,
									'total_first_hours' => $am_workhours,
									'total_second_hours' => $pm_workhours,
									'total_working_hours' => $workhours
								]);
							}

							/*echo "<pre>";
							//print_r($datetime_in);
							//print_r($datetime_out);
							print_r($emp_att);
							echo "</pre><br><br>";*/

							break;
						}
					}
				}
			}
		}

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
				if($value['total_late']<=$employee_data['allowed_late_period']){
					$total_late_minutes += $value['total_late'];
					$total_regular_hrs += $pos_workday[$value['workday_index']]['total_working_hours'];
					$overtime_hours = 0;
					if($value['total_working_hours']>$pos_workday[$value['workday_index']]['total_working_hours']){
						$overtime_hours = $value['total_working_hours'] - $pos_workday[$value['workday_index']]['total_working_hours'];
						$total_overtime_hrs += floor($overtime_hours);
					}

					$overtime_hrly = $employee_data['daily_rate'] * ($employee_data['overtime_rate'] / 100);
					$data['regular_overtime_pay'] += round($overtime_hrly * floor($overtime_hours));

					if($value['total_working_hours']>=$pos_workday[$value['workday_index']]['total_working_hours'])
						$total_regular_days += 1;
					else
						$total_regular_days += $value['total_working_hours'] / $pos_workday[$value['workday_index']]['total_working_hours'];
				}
			}
		}

		$data['daily_wage'] = $employee_data['daily_rate'];
		$data['late_penalty'] = $employee_data['late_penalty'];

		$data['total_regular_days'] = round($total_regular_days, 2);
		$data['total_overtime_hrs'] = round($total_overtime_hrs, 2);
		$data['total_late_minutes'] = round($total_late_minutes, 2);
		$data['total_late_deduction'] = $data['total_late_minutes'] * $employee_data['late_penalty'];

		$data['regular_pay'] = round($data['total_regular_days'] * $employee_data['daily_rate'], 2);

		$data['total_earnings'] = $data['regular_pay'] + $data['regular_overtime_pay'];

		$data['total_additionals'] += ($data['total_regular_days'] * $data['total_daily_additionals']);
		$data['total_deductions'] += ($data['total_regular_days'] * $data['total_daily_deductions']);

		$data['net_pay'] = $data['total_earnings'] + $data['total_additionals'] - $data['total_deductions'] - $data['total_late_deduction'];
		
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
			$this->db->select('p.id, p.name, p.type, pp.amount, pp.units');
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

	public function insert_salary_particular($salary_particular, $payroll_particular)
	{
		$salary_flag = 0;
		if($this->db->insert_batch('salary_particulars', $salary_particular))
			$salary_flag = 1;

		$pp_flag = 0;
		if($this->db->insert_batch('payroll_particulars', $payroll_particular))
			$pp_flag = 1;

		return ($salary_flag && $pp_flag)?TRUE:FALSE;
	}

	public function update_payroll($payroll_id, $payroll_update, $payroll_particulars_update)
	{
		$payrol_flag = 0;
		$this->db->where('id', $payroll_id);
		if($this->db->update('payroll', $payroll_update))
			$payrol_flag = 1;

		$this->db->where('payroll_id', $payroll_id);
		$this->db->update_batch('payroll_particulars', $payroll_particulars_update, 'particulars_id');

		return ($payrol_flag)?TRUE:FALSE;
	}
}