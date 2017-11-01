<?php

class Tasks_Model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    function set_progress($id)
    {
        $project_info = $this->check_by(array('project_id' => $id), 'tbl_project');
        if ($project_info->calculate_progress != '0') {
            if ($project_info->calculate_progress == 'through_tasks') {
                $done_task = count($this->db->where(array('project_id' => $id, 'task_status' => 'completed'))->get('tbl_task')->result());
                $total_tasks = count($this->db->where(array('project_id' => $id))->get('tbl_task')->result());
                $progress = round(($done_task / $total_tasks) * 100);
                if ($progress > 100) {
                    $progress = 100;
                }
            }
        } else {
            $progress = $project_info->progress;
        }
        if (empty($progress)) {
            $progress = 0;
        }
        $p_data = array(
            'progress' => $progress,
        );

        $this->_table_name = "tbl_project"; //table name
        $this->_primary_key = "project_id";
        $this->save($p_data, $id);
        return true;
    }

    public function get_statuses()
    {
        $statuses = array(
            array(
                'id' => 1,
                'value' => 'not_started',
                'name' => lang('not_started'),
                'order' => 1,
            ),
            array(
                'id' => 2,
                'value' => 'in_progress',
                'name' => lang('in_progress'),
                'order' => 2,
            ),
            array(
                'id' => 3,
                'value' => 'completed',
                'name' => lang('completed'),
                'order' => 3,
            ),
            array(
                'id' => 4,
                'value' => 'deferred',
                'name' => lang('deferred'),
                'order' => 4,
            ),
            array(
                'id' => 5,
                'value' => 'waiting_for_someone',
                'name' => lang('waiting_for_someone'),
                'order' => 5,
            )
        );
        return $statuses;
    }
}
