<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Timesheets extends Admin_Controller
{
	public function index($active = NULL, $op_id = NULL){
		$data['title'] = lang('project_details');
        $data['page_header'] = lang('task_management');
        //get all task information
        //$data['project_details'] = $this->items_model->check_by(array('project_id' => $id), 'tbl_project');

        $this->items_model->_table_name = "tbl_tasks_timer"; //table name
        $this->items_model->_order_by = "project_id";
        $data['total_timer'] = $this->items_model->get();

        $data['subview'] = $this->load->view('admin/timesheets/timesheets_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
	}

	public function update_time_timer($id = NULL, $action = NULL)
    {

        if (!empty($action)) {
            $t_data['project_id'] = $this->db->where(array('tasks_timer_id' => $id))->get('tbl_tasks_timer')->row()->project_id;
            $activity = 'activity_delete_tasks_timesheet';
            $msg = lang('delete_timesheet');
        } else {
            $activity = ('activity_update_task_timesheet');
            $msg = lang('timer_update');
        }
        if ($action != 'delete_task_timmer') {
            $t_data = $this->items_model->array_from_post(array('project_id', 'start_date', 'start_time', 'end_date', 'end_time'));

            if (empty($t_data['start_date'])) {
                $t_data['start_date'] = date('Y-m-d');
            }
            if (empty($t_data['end_date'])) {
                $t_data['end_date'] = date('Y-m-d');
            }
            if (empty($t_data['start_time'])) {
                $t_data['start_time'] = date('H:i');
            }
            if (empty($t_data['end_time'])) {
                $t_data['end_time'] = date('H:i');
            }
            $data['start_time'] = strtotime($t_data['start_date'] . ' ' . $t_data['start_time']);
            $data['end_time'] = strtotime($t_data['end_date'] . ' ' . $t_data['end_time']);

            $data['reason'] = $this->input->post('reason', TRUE);
            $data['edited_by'] = $this->session->userdata('user_id');

            $data['project_id'] = $t_data['project_id'];
            $data['user_id'] = $this->session->userdata('user_id');

            $this->items_model->_table_name = "tbl_tasks_timer"; //table name
            $this->items_model->_primary_key = "tasks_timer_id";
            if (!empty($id)) {
                $id = $this->items_model->save($data, $id);
            } else {
                $id = $this->items_model->save($data);
            }
            //print_r($data);die;
            $task_start = $this->items_model->check_by(array('project_id' => $data['project_id']), 'tbl_project');

            $estimate_hours = $task_start->estimate_hours;

            $percentage = $this->items_model->get_estime_time($estimate_hours);
            $logged_hour = $this->items_model->calculate_project('project_hours', $task_start->project_id);
            if ($percentage != 0) {
                $progress = round(($logged_hour / $percentage) * 100);
                if ($progress > 100) {
                    $progress = 100;
                }
                $p_data = array(
                    'progress' => $progress,
                );
                $this->items_model->_table_name = "tbl_project"; //table name
                $this->items_model->_primary_key = "project_id";
                $this->items_model->save($p_data, $data['project_id']);
            }
        } else {
            $this->items_model->_table_name = "tbl_tasks_timer"; //table name
            $this->items_model->_primary_key = "tasks_timer_id";
            $this->items_model->delete($id);
        }
        $project_info = $this->items_model->check_by(array('project_id' => $t_data['project_id']), 'tbl_project');
        $notifiedUsers = array();
        if (!empty($project_info->permission) && $project_info->permission != 'all') {
            $permissionUsers = json_decode($project_info->permission);
            foreach ($permissionUsers as $user => $v_permission) {
                array_push($notifiedUsers, $user);
            }
        } else {
            $notifiedUsers = $this->items_model->allowad_user_id('57');
        }
        if (!empty($notifiedUsers)) {
            foreach ($notifiedUsers as $users) {
                if ($users != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $users,
                        'from_user_id' => true,
                        'description' => 'not_update_timer',
                        'link' => 'admin/projects/project_details/' . $project_info->project_id . '/7',
                        'value' => lang('project') . ' ' . $project_info->project_name,
                    ));
                }
            }
        }
        show_notification($notifiedUsers);
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'projects',
            'module_field_id' => $id,
            'activity' => $activity,
            'icon' => 'fa-folder-open-o',
            'link' => 'admin/projects/project_details/' . $id . '/7',
            'value1' => $project_info->project_name,
        );
        $this->items_model->_table_name = "tbl_activities"; //table name
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/timesheets');
    }
}
?>