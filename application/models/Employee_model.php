<?php

class Employee_model extends CI_Model
{

    const MAX_SICK_LEAVE_PER_MONTH = 1.25;
    const MAX_MENSTRUATION_LEAVE_PER_MONTH = 2;

    protected $table = 'employees';

	public function create($data)
    {
        $this->db->trans_start();

        // insert basic information
        $this->db->insert($this->table, $data['employee']);

        // get generated id
        $id = $this->db->insert_id();


        //update password
        $password = md5($id);
        $this->db->update($this->table, ['password' => $password], ['id' => $id]);

        // insert department record
        $department = ['employee_id' => $id, 'department_id' => $data['department_id'], 'from' => date('Y-m-d')];
        $this->db->insert('employee_departments', $department);

        // insert position yaz_record(id, pos, type)
        $position = ['employee_id' => $id, 'position_id' => $data['position_id'], 'from' => date('Y-m-d')];
        $this->db->insert('employee_positions', $position);

        if(!empty($data['particulars'])){
            foreach($data['particulars'] AS &$row){
                $row['employee_id'] = $id;
            }

            $this->db->insert_batch('salary_particulars', $data['particulars']);
        }
        

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function all()
    {
        $this->db->select('emp.*, dep.name AS department, pos.name AS position');
        $this->db->from($this->table.' AS emp');
        $this->db->join('employee_departments AS empdep', 'empdep.employee_id = emp.id AND empdep.to IS NULL', 'left', FALSE);
        $this->db->join('departments AS dep', 'dep.id = empdep.department_id', 'left');
        $this->db->join('employee_positions AS emppos', 'emppos.employee_id = emp.id AND emppos.to IS NULL', 'left', FALSE);
        $this->db->join('positions AS pos', 'pos.id = emppos.position_id', 'left');
        $this->db->order_by('emp.lastname');
        return $this->db->get()->result_array();
    }

    public function update($id, $data)
    {
    	$this->db->trans_start();

        // update basic information
        $this->db->update($this->table, $data['employee'], ['id' => $id]);
        

        $this->db->select('department_id')->where('employee_id', $id)->where('`to` IS NULL', FALSE, FALSE);
        $result = $this->db->get('employee_departments')->row_array();
        $current_department = $result['department_id'];

        if($current_department != $data['department_id']){
            $this->db->where('employee_id', $id)->where('`to` IS NULL');
            $this->db->update('employee_departments', ['to' => date('Y-m-d')]);

            $department = ['employee_id' => $id, 'department_id' => $data['department_id'], 'from' => date('Y-m-d')];
            $this->db->insert('employee_departments', $department);
        }

        $this->db->select('position_id')->where('employee_id', $id)->where('`to` IS NULL', FALSE, FALSE);
        $result = $this->db->get('employee_positions')->row_array();
        $current_position = $result['position_id'];

        if($current_position != $data['position_id']){
            $this->db->where('employee_id', $id)->where('`to` IS NULL');
            $this->db->update('employee_positions', ['to' => date('Y-m-d')]);

            $position = ['employee_id' => $id, 'position_id' => $data['position_id'], 'from' => date('Y-m-d')];
            $this->db->insert('employee_positions', $position);
        }

        $this->db->delete('salary_particulars', ['employee_id' => $id]);
        if(!empty($data['particulars'])){
            foreach($data['particulars'] AS &$row){
                $row['employee_id'] = $id;
            }
            $this->db->insert_batch('salary_particulars', $data['particulars']);
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function get($id = FALSE, $id_number = FALSE)
    {
        if(!$id && !$id_number){
            return NULL;
        }
        $this->db->select('emp.*, empdep.department_id, emppos.position_id, pos.name AS position, dep.name AS department');
        $this->db->from($this->table.' AS emp');
        $this->db->join('employee_departments AS empdep', 'empdep.employee_id = emp.id AND empdep.to IS NULL', 'left', FALSE);
        $this->db->join('employee_positions AS emppos', 'emppos.employee_id = emp.id AND emppos.to IS NULL', 'left', FALSE);
        $this->db->join('departments AS dep', 'dep.id = empdep.department_id', 'left');
        $this->db->join('positions AS pos', 'pos.id = emppos.position_id', 'left');
        if($id){
            $this->db->where('emp.id', $id);
        }else{
            $this->db->where('emp.id_number', $id_number);
        }
        
        $result = $this->db->get()->row_array();
        if($result){
            $result['birthdate'] = date('m/d/Y', strtotime($result['birthdate']));
            $result['date_hired'] = date('m/d/Y', strtotime($result['date_hired']));
        }


        $result['particulars'] = $this->db->select('sp.amount, pm.type, pm.name, sp.particulars_id')
            ->from('salary_particulars AS sp')
            ->join('pay_modifiers AS pm', 'pm.id = sp.particulars_id')
            ->where( ['employee_id' => $id])
            ->get()
            ->result_array();
        
        return $result;
    }

    public function delete($id)
    {
    	return $this->db->delete($this->table, ['id' => $id]);
    }

    public function exists($id)
    {
        return $this->db->select('id')->from($this->table)->where('id', $id)->get()->num_rows() === 1;
    }

    public function has_unique_id_number($id_number, $id = FALSE)
    {
        if($id){
            $this->db->where('id !=', $id);
        }
        return $this->db->select('id_number')->from($this->table)->where('id_number', $id_number)->get()->num_rows() === 0;
    }

    public function has_unique_rfid_uid($uid, $id = FALSE)
    {
        if($id){
            $this->db->where('id !=', $id);
        }
        return $this->db->select('rfid_uid')->from($this->table)->where('rfid_uid', $uid)->get()->num_rows() === 0;
    }

    public function has_unique($column, $value, $id = FALSE)
    {
        if($id){
            $this->db->where('id !=', $id);
        }
        return $this->db->select($column)->from($this->table)->where($column, $value)->get()->num_rows() === 0;
    }

    public function has_unique_email($email, $id = FALSE)
    {
        if($id){
            $this->db->where('id !=', $id);
        }
        return $this->db->select('email_address')->from($this->table)->where('email_address', $email)->get()->num_rows() === 0;
    }

    public function get_department($id)
    {
        $this->db->select('department_id')->from('employee_departments')->where('employee_id', $id)->where('`to` IS NULL');
        $result = $this->db->get()->row_array();
        return $result ? $result['department_id'] : NULL;
    }

    public function get_position($id)
    {
        $this->db->select('p.name, p.id')->from('employee_positions AS emppos, positions AS p');
        $this->db->where('p.id = emppos.position_id');
        $this->db->where('emppos.`to` IS NULL', FALSE, FALSE)->where('emppos.employee_id', $id);
        return $this->db->get()->row_array();
    }

    public function get_attendance($id, $from, $to)
    {
        $this->db->where("DATE(datetime_in) >= '{$from}' AND DATE(datetime_in) <= '{$to}'", FALSE, FALSE);
        $this->db->where('datetime_in IS NOT NULL AND datetime_out IS NOT NULL', FALSE, FALSE);
        return $this->db->get_where('employee_attendance', ['employee_id' => $id])->result_array();
    }

    public function get_filed_requests($id)
    {
        $this->db->select('empreq.*, CONCAT(emp.firstname, " ", emp.middlename, " ", emp.lastname) AS sender_fullname', FALSE);
        $this->db->join('employees AS emp', 'emp.id = empreq.sender_id');
        $this->db->order_by('empreq.id', 'DESC');
        return $this->db->get_where('employee_requests AS empreq', ['empreq.sender_id' => $id])->result_array();
    }

    public function get_remaining_sick_leaves($id)
    {
        $employee = $this->get($id);

        $current_date = date_create();
        $year_start = date_create($current_date->format('Y').'-01-01');

        $total_months = date_diff($year_start, $current_date)->format('%m')+1;

        $total_accumulated_sick_leaves = $total_months * self::MAX_SICK_LEAVE_PER_MONTH;

        //get count of used sick leaves
        $total_used_sick_leaves = 0;
        $result = $this->db->select('date_start, date_end, halfday', FALSE)
            ->from('employee_requests')
            ->where([
                'sender_id' => $id, 
                'type' => 'sl', 
                'status' => 'a'
            ])
            ->where('YEAR(datetime_filed) =  YEAR(CURDATE())')
            ->get()->result_array();

        if($result){
            foreach($result AS $row){
                $start = date_create($row['date_start']);
                $end = date_create($row['date_end']);
                $duration = $row['halfday'] ? 0.5 : date_diff($start, $end)->format('%a')+1;
                $total_used_sick_leaves += $duration ;
            }
        }

        return $total_accumulated_sick_leaves - $total_used_sick_leaves;

    }

    public function get_remaining_menstruation_leaves($id)
    {
        $employee = $this->get($id);

        if($employee['gender'] === 'M'){
            return 0;
        }

        //get count of mentstruation sick leaves
        $total_used_leaves = 0;
        $this->db->select('IFNULL(COUNT(id), 0) AS total', FALSE)->from('employee_requests');
        $this->db->where('MONTH(date_start) = MONTH(CURDATE())');
        $result = $this->db->where(['sender_id' => $id, 'type' => 'wml', 'status' => 'a'])->get()->row_array();
        if($result){
            $total_used_leaves = $result['total'];
        }
        $remaining = self::MAX_MENSTRUATION_LEAVE_PER_MONTH - $total_used_leaves;
        return $remaining < 0 ? 0 : $remaining ;

    }

    public function get_by_uid($uid)
    {
        $employee = $this->db->select('id')->get_where($this->table, ['rfid_uid' => $uid])->row_array();
        return $employee ? $this->get($employee['id']) : NULL;
    }

    public function set_attendance($id, $datetime)
    {
        //check if the employee has already timed in
        $this->db->where("datetime_out IS NULL AND datetime_in IS NOT NULL AND DAY(datetime_in) = DAY('{$datetime}')");
        $time_in = $this->db->get_where('employee_attendance', ['employee_id' => $id])->row_array();
        if($time_in){
            $this->db->update('employee_attendance', ['datetime_out' => $datetime], ['employee_id' => $id, 'datetime_in' => $time_in['datetime_in']]);
            return $this->db->get_where('employee_attendance', [
                'employee_id' => $id, 
                'datetime_in' => $time_in['datetime_in']
            ])->row_array();
        }
        $this->db->insert('employee_attendance', [
            'datetime_in' => $datetime,
            'datetime_out' => NULL,
            'employee_id' => $id
        ]);
        $id = $this->db->insert_id();
        return $this->db->get_where('employee_attendance', ['id' => $id])->row_array();
    }

    public function attendance($id, $start_date = FALSE, $end_date = FALSE)
    {

        $this->db->select('a.*, ar.type, ar.custom_type_name')
            ->from('employee_attendance AS a')
            ->join('employee_requests AS ar', 'ar.id = a.request_id', 'left')
            ->where('employee_id', $id)
            ->where('datetime_in IS NOT NULL AND datetime_out IS NOT NULL');

        if($start_date){
            $this->db->where('DATE(datetime_in) >=', $start_date);
        }

        if($end_date){
            $this->db->where('DATE(datetime_in) <=', $end_date);
        }

        return $this->db->get()->result_array();
    }

    public function toggle_lock($id)
    {   
        $this->db->set('is_locked', '!is_locked', FALSE);
        return $this->db->where('id', $id)->update($this->table);
    }


}