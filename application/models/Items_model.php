<?php

/**
 * Description of Project_Model
 *
 * @author NaYeM
 */
class Items_Model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function estimated_details($id){

        
         date_default_timezone_set('Asia/Kolkata');
       $this->items_model->_table_name = "tbl_estimates"; //table name
        $this->items_model->_order_by = "project_id";
        $ess_data = $this->items_model->get_by(array('project_id' => $id), FALSE);

        $res = 0;
        if (empty($ess_data)) {
           echo '';
        }else{
            foreach ($ess_data as $row) {
            
            $this->items_model->_table_name = "tbl_estimate_items"; //table name
            $this->items_model->_order_by = "estimates_id";
            $ess = $this->items_model->get_by(array('estimates_id' => $row->estimates_id), FALSE);
            foreach ($ess as $rows) {
                $res = $rows->total_cost+$res;
            }
        }
        $data['total_estimates_cost'] = $res;

        $this->items_model->_table_name = "tbl_tasks_timer"; //table name
        $this->items_model->_order_by = "project_id";
        $tot_tim = $this->items_model->get_by(array('project_id' => $id), FALSE);

        $this->items_model->_table_name = "tbl_account_details"; //table name
        $this->items_model->_order_by = "user_id";
        $user_det = $this->items_model->get_by(array('user_id' => $tot_tim[0]->user_id), FALSE);
        $per_hour_rate = $user_det[0]->per_hour_rate;

        $res = 0;
        foreach ($tot_tim as $row) {

            $time = $row->start_time;
            $dt = new DateTime("@$time");  // convert UNIX timestamp to PHP DateTime
            $nstartdate = $dt->format('Y-m-d H:i:s'); // output = 2017-01-01 00:00:00
            $time1 = $row->end_time;
            $dt1 = new DateTime("@$time1");  // convert UNIX timestamp to PHP DateTime
            $nstartdate1 = $dt1->format('Y-m-d H:i:s'); // output = 2017-01-01 00:00:00
     
             $date11 = new DateTime($nstartdate);
             $date12 = new DateTime($nstartdate1);
          
             $dteDiff = date_diff($date11,$date12);
             $abc = array('years' => $dteDiff->y, 'months' => $dteDiff->m, 'days' => $dteDiff->d, 'hours' => $dteDiff->h, 'minutes' => $dteDiff->i, 'seconds' => $dteDiff->s);
             
            $hr_y = 0;
            $hr_m = 0;
            $hr_d = 0;
            $hr_h = 0;
            $hr_mi = 0;
            $hr_s = 0;
            $total_hr = 0;                       
            foreach ($abc as $k => $v) {
                //echo $k;
                if($k == 'years'){
                    $hr_y =  $v*8760;                    
                }
                if($k == 'months'){
                    $hr_m = $v*730.001;                    
                }
                if($k == 'days'){
                    $hr_d = $v*24;                                        
                }
                if($k == 'hours'){
                    $hr_h = $v;                                                            
                }
                if($k == 'minutes'){
                    $hr_mi = $v;                                                            
                }
                if($k == 'seconds'){
                    $hr_s = $v*0.000277778;
                }
              $total_hr = $hr_y + $hr_m + $hr_d + $hr_h + $hr_mi + $hr_s;
              
            }
            $res = $total_hr + $res;           
        }
        $total_hours =  floor($res);
        $data['total_resource_cost'] = $total_hours * $per_hour_rate;
   //print_r($data);die;
        return $data;
        }
        

    }

    public function calculate_progress_by_tasks($id)
    {
        $project = $this->get($id);
        $total_project_tasks = total_rows('tbl_task', array(
            'project_id' => $id
        ));
        $total_finished_tasks = total_rows('tbl_task', array(
            'project_id' => $id,
            'task_status' => 'completed'
        ));
        $percent = 0;
        if ($total_finished_tasks >= floatval($total_project_tasks)) {
            $percent = 100;
        } else {
            if ($total_project_tasks !== 0) {
                $percent = number_format(($total_finished_tasks * 100) / $total_project_tasks, 2);
            }
        }
        return $percent;
    }

    function calculate_milestone_progress($milestones_id)
    {
        $all_milestone_tasks = $this->db->where('milestones_id', $milestones_id)->get('tbl_task')->num_rows();
        $complete_milestone_tasks = $this->db->where(
            array('task_progress' => '100',
                'milestones_id' => $milestones_id
            ))->get('tbl_task')->num_rows();
        if ($all_milestone_tasks > 0) {
            return round(($complete_milestone_tasks / $all_milestone_tasks) * 100);
        } else {
            return 0;
        }
    }

    function calculate_project($project_value, $project_id)
    {
        switch ($project_value) {
            case 'project_cost':
                return $this->total_project_cost($project_id);
                break;
            case 'project_hours':
                return $this->total_project_hours($project_id, true);
                break;
        }
    }

    function total_project_cost($project_id)
    {
        $project_info = $this->db->where('project_id', $project_id)->get('tbl_project')->row();
        $tasks_cost = $this->calculate_all_tasks_cost($project_id);
        $project_time = $this->calculate_total_task_time($project_id);
        $project_hours = $project_time / 3600;
		if(empty($project_info->hourly_rate)){
			$project_info->hourly_rate=0;
		}
        $project_cost = $project_hours * $project_info->hourly_rate;

        if ($project_info->billing_type == 'tasks_hours') {
            return $tasks_cost;
        }
        if ($project_info->billing_type == 'tasks_and_project_hours') {
            return $project_cost + $tasks_cost;
        }
        if ($project_info->billing_type == 'project_hours') {
            return $project_cost;
        } else {
            return $this->get_any_field('tbl_project', array('project_id' => $project_id), 'project_cost');
        }
    }

    public function calculate_all_tasks_cost($project_id)
    {
        $all_tasks = $this->db->where('project_id', $project_id)->get('tbl_task')->result();
        $total_cost = 0;
        if (!empty($all_tasks)) {
            foreach ($all_tasks as $v_tasks) {
                if (!empty($v_tasks->billable) && $v_tasks->billable == 'Yes') {
                    $task_time = $this->task_spent_time_by_id($v_tasks->task_id);
                    $total_time = $task_time / 3600;
                    $total_cost += $total_time * $v_tasks->hourly_rate;
                }
            }
        }
        return $total_cost;
    }

    function total_project_hours($project_id, $second = null, $task = null)
    {
        $project_info = $this->db->where('project_id', $project_id)->get('tbl_project')->row();
        $project_time = $this->calculate_total_task_time($project_id);
        $all_tasks = $this->db->where('project_id', $project_id)->get('tbl_task')->result();

        $task_time = 0;
        if (!empty($all_tasks)) {
            foreach ($all_tasks as $v_tasks) {
                if (!empty($v_tasks->billable) && $v_tasks->billable == 'Yes') {
                    $task_time += $this->task_spent_time_by_id($v_tasks->task_id);
                }
            }
        }
        $c_logged_time = 0;
        if ($project_info->billing_type == 'project_hours') {
            $c_logged_time = $project_time / 3600;
        }
        if ($project_info->billing_type == 'tasks_hours') {
            $c_logged_time = $task_time / 3600;
        }
        if ($project_info->billing_type == 'tasks_and_project_hours') {
            $c_logged_time = ($task_time + $project_time) / 3600;
        }
        if (!empty($task)) {
            return $logged_time = $task_time;
        }
        if (!empty($second)) {
            $logged_time = $project_time;
        } else {
            $logged_time = $c_logged_time;
        }

        return $logged_time;

    }

    function calculate_total_task_time($project_id)
    {
        $total_time = "SELECT start_time,end_time,project_id,
		end_time - start_time time_spent FROM tbl_tasks_timer WHERE project_id = '$project_id'";
        $result = $this->db->query($total_time)->result();

        $time_spent = array();
        foreach ($result as $time) {
            if ($time->start_time != 0 && $time->end_time != 0) {
                $time_spent[] = $time->time_spent;
            }
        }
        if (is_array($time_spent)) {
            return array_sum($time_spent);
        } else {
            return 0;
        }
    }

    function task_spent_time_by_staff($task_id, $user_id)
    {
        $where = 'task_id = ' . $task_id AND 'user_id =' . $user_id;
        $total_time = "SELECT start_time,end_time,project_id,
		end_time - start_time time_spent FROM tbl_tasks_timer WHERE $where";
        $result = $this->db->query($total_time)->result();
        $time_spent = array();
        foreach ($result as $time) {
            if ($time->start_time != 0 && $time->end_time != 0) {
                $time_spent[] = $time->time_spent;
            }
        }
        if (is_array($time_spent)) {
            return array_sum($time_spent);
        } else {
            return 0;
        }
    }

    function project_hours($project_id)
    {
        $task_time = $this->get_sum('tbl_tasks', 'logged_time', array('project' => $project_id));
        $project_time = $this->get_sum('tbl_project', 'time_logged', array('project_id' => $project_id));
        $logged_time = ($task_time + $project_time) / 3600;
        return $logged_time;
    }


    function get_project_progress($id)
    {
        $project_info = $this->check_by(array('project_id' => $id), 'tbl_project');
        if ($project_info->project_status == 'completed') {
            $progress = 100;
        } else {
            if ($project_info->calculate_progress != '0') {
                if ($project_info->calculate_progress == 'through_project_hours') {
                    $estimate_hours = $project_info->estimate_hours;
                    $percentage = $this->get_estime_time($estimate_hours);
                    if ($percentage != 0) {
                        $logged_hour = $this->calculate_project('project_hours', $project_info->project_id);
                        if ($percentage != 0) {
                            $progress = round(($logged_hour / $percentage) * 100);
                        }
                    }
                } else {
                    $done_task = count($this->db->where(array('project_id' => $id, 'task_status' => 'completed'))->get('tbl_task')->result());
                    $total_tasks = count($this->db->where(array('project_id' => $id))->get('tbl_task')->result());
                    if ($total_tasks != 0) {
                        $progress = round(($done_task / $total_tasks) * 100);
                    }
                }
            } else {
                $progress = $project_info->progress;
            }
            if (empty($progress)) {
                $progress = 0;
            } else {
                if ($progress > 100) {
                    $progress = 100;
                }
            }
        }

        return $progress;
    }

    function set_progress($id)
    {
        $project_info = $this->check_by(array('project_id' => $id), 'tbl_project');

        if ($project_info->calculate_progress != '0') {
            if ($project_info->calculate_progress == 'through_project_hours') {
                $estimate_hours = $project_info->estimate_hours;
                $percentage = $this->get_estime_time($estimate_hours);
                $logged_hour = $this->calculate_project('project_hours', $project_info->project_id);
                if ($percentage != 0) {
                    $progress = round(($logged_hour / $percentage) * 100);
                }
            } else {
                $done_task = count($this->db->where(array('project_id' => $id, 'task_status' => 'completed'))->get('tbl_task')->result());
                $total_tasks = count($this->db->where(array('project_id' => $id))->get('tbl_task')->result());
                if (empty($total_tasks) || empty($done_task)) {
                    $progress = 0;
                } else {
                    $progress = round(($done_task / $total_tasks) * 100);
                }

                if ($progress > 100) {
                    $progress = 100;
                }
            }
        } else {
            $progress = $project_info->progress;
        }
        if(empty($progress)){
            $progress = 0;        
        }
        $p_data = array(
            'progress' => $progress,
        );
        $this->_table_name = "tbl_project"; //table name
        $this->_primary_key = "project_id";
        $this->save($p_data, $id);
    }

    public function get_leads($search_by, $id)
    {
        $all_leads_info = $this->get_permission('tbl_leads');
        if (!empty($all_leads_info)) {
            foreach ($all_leads_info as $v_leads) {
                if ($search_by == 'by_status') {
                    if ($v_leads->lead_source_id == $id) {
                        $leads[] = $v_leads;
                    }
                } else if ($search_by == 'by_source') {
                    if ($v_leads->lead_source_id == $id) {
                        $leads[] = $v_leads;
                    }
                }
            }
        }
        if (!empty($leads)) {
            return $leads;
        } else {
            return array();
        }
    }


}
