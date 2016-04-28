<?php

class Payslip_model extends CI_Model
{
	public function calculate($employee_id, $from, $to, $bypass_check = FALSE)
	{

		if((($id = $this->check($employee_id, $from, $to)) !== TRUE) && !$bypass_check){
			return $id;
		}

		$this->load->model(['Employee_model' => 'employee', 'Position_model' => 'position']);
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

		$o_time_in_am = strtotime($position['hour_of_day_start_am']); //required work time in (am)
		$o_time_out_am = strtotime($position['hour_of_day_end_am']); //required work time out (am)

		$o_time_in_pm = strtotime($position['hour_of_day_start_pm']); //required work time in (pm)
		$o_time_out_pm = strtotime($position['hour_of_day_end_pm']); //required work time out (pm)

		$required_hrs_am = ($o_time_out_am - $o_time_in_am) / 60 / 60;
		$required_hrs_pm = ($o_time_out_pm - $o_time_in_pm) / 60 / 60;

		$required_hrs = $required_hrs_am + $required_hrs_pm;

		$dates = [];
		
		foreach($attendance AS &$row){

			

			$datetime_in = strtotime($row['datetime_in']); //datetime in
			$datetime_out = strtotime($row['datetime_out']); //datetime out

			//extract time from datetime in and out
			$time_in = strtotime(date('H:i', $datetime_in));
			$time_out = strtotime(date('H:i', $datetime_out));			

			$day = date('N', $datetime_in); //get day in numeric form

			$date = date('Y-m-d', $datetime_in);

			if(!$row['datetime_in'] || !$row['datetime_out']){
				continue;
			}

			if(!isset($dates[$date])){
				$dates[$date] = [];
			}elseif(isset($dates[$date]) && isset($dates[$date]['am']) && isset($dates[$date]['pm'])){
				continue;
			}

			//if day is in between work day
			if(($day >= $position['day_of_week_start'] && $day <= $position['day_of_week_end'])){

				unset($done_serving_pm_time);
			
				$row['am_hrs'] = 0;
				$row['pm_hrs'] = 0;

				// if positive = late
				$in_AM = ($time_in - $o_time_in_am) / 60;

				$acceptable_late = $in_AM <= $employee_data['allowed_late_period'];

				// determine if late (AM) and is still in allowed late threshold
				if($acceptable_late && !isset($dates[$date]['am'])){

					// late still in threshold, accumulate late
					$total_late_minutes += ($in_AM > 0) ? $in_AM : 0;

					//get actual
					$actual_am_hrs = ($o_time_out_am - $o_time_in_am) / 60 / 60;
					
					$actual_hrs_rendered += $actual_am_hrs;

					$am_payable = $actual_hrs_rendered > $required_hrs_am ? $required_hrs_am : $actual_hrs_rendered;

					$total_regular_hrs += $am_payable;

					$row['am_hrs'] = $am_payable;

					$dates[$date]['am'] = $row['am_hrs'] ;
					
				}else if(!$acceptable_late && !isset($dates[$date]['pm'])){ //lapas late
					
					// determine if late (PM) and is still in allowed late threshold
					$temp_in_PM = ($time_in - $o_time_in_pm) / 60;
					if($temp_in_PM <= $employee_data['allowed_late_period']){

						// late still in threshold, accumulate late
						$total_late_minutes += ($temp_in_PM > 0) ? $temp_in_PM : 0;

						$actual_pm_hrs = ($time_out - $o_time_in_pm) / 60 / 60;

						if($actual_pm_hrs > 0){

							$actual_hrs_rendered += $actual_pm_hrs;

							$total_regular_hrs += $actual_pm_hrs;
							$row['pm_hrs'] = $actual_pm_hrs;

							$dates[$date]['pm'] = $row['pm_hrs'];
						}

					}
					$done_serving_pm_time = TRUE;
				}

				// determine if served PM time
				if(!isset($done_serving_pm_time) && $time_out > $o_time_in_pm && !isset($dates[$date]['pm'])){

					$actual_pm_hrs = ($time_out - $o_time_in_pm) / 60 / 60;

					$total_regular_hrs += $actual_pm_hrs;

					$actual_hrs_rendered += $actual_pm_hrs;
					
					$row['pm_hrs'] = $actual_pm_hrs;

					$dates[$date]['pm'] = $row['pm_hrs'];
				}

				// add up am and pm served hrs
				if(isset($dates[$date]['am']) && isset($dates[$date]['pm'])){
					$hrs_rendered =  $dates[$date]['am'] + $dates[$date]['pm'];
					// determine if has overtime
					// note: overtime starts if served 1hour after time out
					$overtime = $hrs_rendered - $required_hrs;
					if($overtime >= 1){
						$total_overtime_hrs += $overtime;
					}

					if($overtime > 0){
						$total_regular_hrs -= $overtime;
					}
				}

			}
		}

		// $data['salary_template'] = $salary;
		$data['employee_profile'] = $employee_data;
		$data['attendance'] = $attendance;
		$data['additionals'] = [];
		$data['deductions'] = [];
		$data['total_deductions'] = 0;
		$data['total_additionals'] = 0;

		array_map(function($var) USE(&$data){
			if($var['type'] === 'a'){
				$data['additionals'][] = $var;
				$data['total_additionals']  += $var['amount'];
				return;
			}
			$data['deductions'][] = $var;
			$data['total_deductions'] += $var['amount'];
		}, $employee_data['particulars']);

		$data['daily_wage'] = $employee_data['daily_rate'];
		$data['overtime_hrly'] = round(($employee_data['daily_rate'] * ($employee_data['overtime_rate']) / 100) /  $required_hrs, 2);
		$data['late_penalty'] = $employee_data['late_penalty'];

		$data['total_regular_days'] = round($total_regular_hrs/$required_hrs, 2);
		$data['total_overtime_hrs'] = round($total_overtime_hrs, 2);
		$data['total_late_minutes'] = round($total_late_minutes, 2);
		$data['total_late_deduction'] = $data['total_late_minutes'] * $employee_data['late_penalty'];

		$data['regular_pay'] = round($data['total_regular_days'] * $employee_data['daily_rate'], 2);
		$data['regular_overtime_pay'] = round($data['overtime_hrly'] * $data['total_overtime_hrs'], 2);

		$data['total_earnings'] = $data['regular_pay'] + $data['regular_overtime_pay'];

		$data['net_pay'] = $data['total_earnings'] + $data['total_additionals'] - $data['total_deductions'] - $data['total_late_deduction'];
		
		return $data;
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
			'current_overtime_rate' => $payslip['overtime_hrly'],
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
		$this->db->select('start_date, end_date, id')->from('payroll');
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
		$data = $this->db->get_where('payroll', ['id' => $id, 'employee_id' => $employee_id])->row_array();
		if($data){
			$data['particulars'] = ['deductions' => [], 'additionals' => []];
			$this->db->select('p.name, p.type, pp.amount');
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
		}
		return $data;
		
	}
}