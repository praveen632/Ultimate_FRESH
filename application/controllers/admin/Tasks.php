<?php

/**
 * Description of Tasks
 *
 * @author Nayeem
 */
class Tasks extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('tasks_model');
    }

    public function all_task($id = NULL, $opt_id = NULL)
    {
        $data['title'] = lang('all_task');
        $data['assign_user'] = $this->tasks_model->allowad_user('54');

        if ($id) { // retrive data from db by id
            if ($id == 'kanban') {
                $data['active'] = 1;
                $k_session['task_kanban'] = $id;
                $this->session->set_userdata($k_session);
            } elseif ($id == 'list') {
                $data['active'] = 1;
                $this->session->unset_userdata('task_kanban');
            } else {
                if ($id == 'not_started' || $id == 'in_progress' || $id == 'completed' || $id == 'deferred' || $id == 'waiting_for_someone') {
                    $data['active'] = 1;
                    $by_status = $this->db->where('task_status', $id)->get('tbl_task')->result();
                    $search = true;
                    $data['completed'] = true;
                } else {
                    $data['active'] = 2;
                    $can_edit = $this->tasks_model->can_action('tbl_task', 'edit', array('task_id' => $id));
                    $edited = can_action('54', 'edited');
                    if ($id == 'project') {
                        $data['project_id'] = $opt_id;
                    } elseif ($id == 'opportunities') {
                        $data['opportunities_id'] = $opt_id;
                    } elseif ($id == 'leads') {
                        $data['leads_id'] = $opt_id;
                    } elseif ($id == 'bugs') {
                        $data['bug_id'] = $opt_id;
                    } elseif ($id == 'goal') {
                        $data['goal_tracking_id'] = $opt_id;
                    } else {
                        if (!empty($can_edit) && !empty($edited)) {
                            //get all task information
                            $data['task_info'] = $this->db->where('task_id', $id)->get('tbl_task')->row();
                        }
                    }

                }
                $this->session->unset_userdata('task_kanban');
            }

        } else {
            $data['active'] = 1;
        }
        if (!empty($search)) {
            $data['all_task_info'] = $by_status;
        } else {
            $data['all_task_info'] = $this->tasks_model->get_permission('tbl_task');
        }
        $data['subview'] = $this->load->view('admin/tasks/tasks', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function import()
    {
        $data['title'] = lang('import') . ' ' . lang('tasks');
        $data['assign_user'] = $this->tasks_model->allowad_user('54');
        $data['subview'] = $this->load->view('admin/tasks/import_tasks', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function save_imported()
    {
        //load the excel library
        $this->load->library('excel');
        ob_start();
        $file = $_FILES["upload_file"]["tmp_name"];
        if (!empty($file)) {
            $valid = false;
            $types = array('Excel2007', 'Excel5');
            foreach ($types as $type) {
                $reader = PHPExcel_IOFactory::createReader($type);
                if ($reader->canRead($file)) {
                    $valid = true;
                }
            }
            if (!empty($valid)) {
                try {
                    $objPHPExcel = PHPExcel_IOFactory::load($file);
                } catch (Exception $e) {
                    die("Error loading file :" . $e->getMessage());
                }
                //All data from excel
                $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

                for ($x = 2; $x <= count($sheetData); $x++) {
                    // **********************
                    // Save Into tasks table
                    // **********************

                    $data['task_name'] = trim($sheetData[$x]["A"]);
                    $data['task_start_date'] = date('Y-m-d', strtotime($sheetData[$x]["B"]));
                    $data['due_date'] = date('Y-m-d', strtotime($sheetData[$x]["C"]));
                    $data['task_hour'] = trim($sheetData[$x]["D"]);
                    $data['task_progress'] = trim($sheetData[$x]["E"]);
                    $data['task_description'] = trim($sheetData[$x]["F"]);
                    $data['created_by'] = $this->session->userdata('user_id');

                    $permission = $this->input->post('permission', true);
                    if (!empty($permission)) {
                        if ($permission == 'everyone') {
                            $assigned = 'all';
                        } else {
                            $assigned_to = $this->tasks_model->array_from_post(array('assigned_to'));
                            if (!empty($assigned_to['assigned_to'])) {
                                foreach ($assigned_to['assigned_to'] as $assign_user) {
                                    $assigned[$assign_user] = $this->input->post('action_' . $assign_user, true);
                                }
                            }
                        }
                        if ($assigned != 'all') {
                            $assigned = json_encode($assigned);
                        }
                        $data['permission'] = $assigned;
                    }
                    $this->tasks_model->_table_name = "tbl_task"; // table name
                    $this->tasks_model->_primary_key = "task_id"; // $id
                    $id = $this->tasks_model->save($data);
                }

                //save data into table.

                $msg = lang('save_task');
                $activity = 'activity_new_task';

                // save into activities
                $activities = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'tasks',
                    'module_field_id' => $id,
                    'activity' => $activity,
                    'icon' => 'fa-tasks',
                    'link' => 'admin/tasks/view_task_details/' . $id,
                    'value1' => lang('import') . ' ' . lang('tasks'),
                );
                // Update into tbl_project
                $this->tasks_model->_table_name = "tbl_activities"; //table name
                $this->tasks_model->_primary_key = "activities_id";
                $this->tasks_model->save($activities);

                $type = "success";
                $message = $msg;

            } else {
                $type = 'error';
                $message = "Sorry your uploaded file type not allowed ! please upload XLS/CSV File ";
            }
        } else {
            $type = 'error';
            $message = "You did not Select File! please upload XLS/CSV File ";
        }
        set_message($type, $message);
        redirect($_SERVER['HTTP_REFERER']);

    }

    public function save_task($id = NULL)
    {
        $created = can_action('54', 'created');
        $edited = can_action('54', 'edited');
        if (!empty($created) || !empty($edited)) {
            $data = $this->tasks_model->array_from_post(array(
                'task_name',
                'task_description',
                'task_start_date',
                'due_date',
                'task_progress',
                'client_visible',
                'task_status',
                'hourly_rate',
                'billable'));
            
            $estimate_hours = $this->input->post('task_hour', true);
            $check_flot = explode('.', $estimate_hours);
            if (!empty($check_flot[0])) {
                if (!empty($check_flot[1])) {
                    $data['task_hour'] = $check_flot[0] . ':' . $check_flot[1];
                } else {
                    $data['task_hour'] = $check_flot[0] . ':00';
                }
            } else {
                $data['task_hour'] = '0:00';
            }


            if ($data['task_status'] == 'completed') {
                $data['task_progress'] = 100;
            }
            if ($data['task_progress'] == 100) {
                $data['task_status'] = 'completed';
            }
            if (empty($id)) {
                $data['created_by'] = $this->session->userdata('user_id');
            }
            if (empty($data['billable'])) {
                $data['billable'] = 'No';
            }
            if (empty($data['hourly_rate'])) {
                $data['hourly_rate'] = '0';
            }
            $result = 0;
            $related_to = $this->input->post('related_to', true);

            if ($related_to != '0') {
                $project_id = $this->input->post('project_id', TRUE);
                if (!empty($project_id)) {
                    $data['project_id'] = $project_id;
                    $data['milestones_id'] = $this->input->post('milestones_id', TRUE);
                } else {
                    $data['project_id'] = NULL;
                    $data['milestones_id'] = NULL;
                    $result += count(1);
                }
                $opportunities_id = $this->input->post('opportunities_id', TRUE);
                if (!empty($opportunities_id)) {
                    $data['opportunities_id'] = $opportunities_id;
                } else {
                    $data['opportunities_id'] = NULL;
                    $result += count(1);
                }
                $leads_id = $this->input->post('leads_id', TRUE);
                if (!empty($leads_id)) {
                    $data['leads_id'] = $leads_id;
                } else {
                    $data['leads_id'] = NULL;
                    $result += count(1);
                }
                $bug_id = $this->input->post('bug_id', TRUE);
                if (!empty($bug_id)) {
                    $data['bug_id'] = $bug_id;
                } else {
                    $data['bug_id'] = NULL;
                    $result += count(1);
                }
                $goal_tracking_id = $this->input->post('goal_tracking_id', TRUE);
                if (!empty($goal_tracking_id)) {
                    $data['goal_tracking_id'] = $goal_tracking_id;
                } else {
                    $data['goal_tracking_id'] = NULL;
                    $result += count(1);
                }

                if ($result == 5) {
                    if (!empty($id)) {
                        $task_info = $this->db->where('task_id', $id)->get('tbl_task')->row();
                        $data['project_id'] = $task_info->project_id;
                        $data['milestones_id'] = $task_info->milestones_id;
                        $data['opportunities_id'] = $task_info->opportunities_id;
                        $data['leads_id'] = $task_info->leads_id;
                        $data['bug_id'] = $task_info->bug_id;
                        $data['goal_tracking_id'] = $task_info->goal_tracking_id;
                    } else {
                        $data['project_id'] = $this->input->post('un_project_id', TRUE);
                        $data['milestones_id'] = $this->input->post('un_milestones_id', TRUE);;
                        $data['opportunities_id'] = $this->input->post('un_opportunities_id', TRUE);
                        $data['leads_id'] = $this->input->post('un_leads_id', TRUE);
                        $data['bug_id'] = $this->input->post('un_bug_id', TRUE);
                        $data['goal_tracking_id'] = $this->input->post('un_goal_tracking_id', TRUE);
                    }

                }
            } else {
                $data['project_id'] = NULL;
                $data['milestones_id'] = NULL;
                $data['goal_tracking_id'] = NULL;
                $data['bug_id'] = NULL;
                $data['leads_id'] = NULL;
                $data['opportunities_id'] = NULL;
            }
            $permission = $this->input->post('permission', true);
            if (!empty($permission)) {

                if ($permission == 'everyone') {
                    $assigned = 'all';
                    $assigned_to['assigned_to'] = $this->tasks_model->allowad_user_id('54');
                } else {
                    $assigned_to = $this->tasks_model->array_from_post(array('assigned_to'));
                    if (!empty($assigned_to['assigned_to'])) {
                        foreach ($assigned_to['assigned_to'] as $assign_user) {
                            $assigned[$assign_user] = $this->input->post('action_' . $assign_user, true);
                        }
                    }
                }
                if ($assigned != 'all') {
                    $assigned = json_encode($assigned);
                }
                $data['permission'] = $assigned;
            } else {
                set_message('error', lang('assigned_to') . ' Field is required');
                redirect($_SERVER['HTTP_REFERER']);
            }

            //save data into table.
            $this->tasks_model->_table_name = "tbl_task"; // table name
            $this->tasks_model->_primary_key = "task_id"; // $id
            $id = $this->tasks_model->save($data, $id);

            $u_data['index_no'] = $id;
            $id = $this->tasks_model->save($u_data, $id);
            $u_data['index_no'] = $id;
            $id = $this->tasks_model->save($u_data, $id);

            save_custom_field(3, $id);

            if ($assigned == 'all') {
                $assigned_to['assigned_to'] = $this->tasks_model->allowad_user_id('54');
            }

            if (!empty($id)) {

                $msg = lang('update_task');
                $activity = 'activity_update_task';
                $id = $id;
                if (!empty($assigned_to['assigned_to'])) {
                    // send update
                    $this->notify_assigned_tasks($assigned_to['assigned_to'], $id, TRUE);
                }
            } else {
                $msg = lang('save_task');
                $activity = 'activity_new_task';
                if (!empty($assigned_to['assigned_to'])) {
                    $this->notify_assigned_tasks($assigned_to['assigned_to'], $id);
                }
            }

            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'tasks',
                'module_field_id' => $id,
                'activity' => $activity,
                'icon' => 'fa-tasks',
                'link' => 'admin/tasks/view_task_details/' . $id,
                'value1' => $data['task_name'],
            );
            // Update into tbl_project
            $this->tasks_model->_table_name = "tbl_activities"; //table name
            $this->tasks_model->_primary_key = "activities_id";
            $this->tasks_model->save($activities);

            if (!empty($data['project_id'])) {
                $this->tasks_model->set_progress($data['project_id']);
            }

            $type = "success";
            $message = $msg;
            set_message($type, $message);
            if (!empty($data['project_id']) && is_numeric($data['project_id'])) {
                redirect('admin/projects/project_details/' . $data['project_id'] . '/' . '6');
            } else {
                redirect('admin/tasks/view_task_details/' . $id);
            }
        } else {
            redirect('admin/tasks/all_task');
        }


    }

    function notify_assigned_tasks($users, $task_id, $update = NULL)
    {
        if (!empty($update)) {
            $email_template = $this->tasks_model->check_by(array('email_group' => 'tasks_updated'), 'tbl_email_templates');
            $description = 'not_task_update';
        } else {
            $email_template = $this->tasks_model->check_by(array('email_group' => 'task_assigned'), 'tbl_email_templates');
            $description = 'assign_to_you_the_tasks';;
        }
        $tasks_info = $this->tasks_model->check_by(array('task_id' => $task_id), 'tbl_task');
        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $task_name = str_replace("{TASK_NAME}", $tasks_info->task_name, $message);

        $assigned_by = str_replace("{ASSIGNED_BY}", ucfirst($this->session->userdata('name')), $task_name);
        $Link = str_replace("{TASK_URL}", base_url() . 'admin/tasks/view_task_details/' . $tasks_info->task_id, $assigned_by);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';

        foreach ($users as $v_user) {
            $login_info = $this->tasks_model->check_by(array('user_id' => $v_user), 'tbl_users');
            $params['recipient'] = $login_info->email;
            $this->tasks_model->send_email($params);
            if ($v_user != $this->session->userdata('user_id')) {
                add_notification(array(
                    'to_user_id' => $v_user,
                    'from_user_id' => true,
                    'description' => $description,
                    'link' => 'admin/tasks/view_task_details/' . $task_id,
                    'value' => lang('task') . ' ' . $tasks_info->task_name,
                ));
            }
        }
        show_notification($users);
    }

    public function update_users($id)
    {
        $can_edit = $this->tasks_model->can_action('tbl_task', 'edit', array('task_id' => $id));
        if (!empty($can_edit)) {

            $data['assign_user'] = $this->tasks_model->allowad_user('54');

            $data['task_info'] = $this->tasks_model->check_by(array('task_id' => $id), 'tbl_task');
            $data['modal_subview'] = $this->load->view('admin/tasks/_modal_users', $data, FALSE);
            $this->load->view('admin/_layout_modal', $data);
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function update_member($id)
    {
        $can_edit = $this->tasks_model->can_action('tbl_task', 'edit', array('task_id' => $id));
        if (!empty($can_edit)) {
            $tasks_info = $this->tasks_model->check_by(array('task_id' => $id), 'tbl_task');

            $permission = $this->input->post('permission', true);
            if (!empty($permission)) {

                if ($permission == 'everyone') {
                    $assigned = 'all';
                    $assigned_to['assigned_to'] = $this->tasks_model->allowad_user_id('54');
                } else {
                    $assigned_to = $this->tasks_model->array_from_post(array('assigned_to'));
                    if (!empty($assigned_to['assigned_to'])) {
                        foreach ($assigned_to['assigned_to'] as $assign_user) {
                            $assigned[$assign_user] = $this->input->post('action_' . $assign_user, true);
                        }
                    }
                }
                if ($assigned != 'all') {
                    $assigned = json_encode($assigned);
                }
                $data['permission'] = $assigned;
            } else {
                set_message('error', lang('assigned_to') . ' Field is required');
                redirect($_SERVER['HTTP_REFERER']);
            }

            //save data into table.
            $this->tasks_model->_table_name = "tbl_task"; // table name
            $this->tasks_model->_primary_key = "task_id"; // $id
            $this->tasks_model->save($data, $id);

            $msg = lang('update_task');
            $activity = 'activity_update_task';
            if (!empty($assigned_to['assigned_to'])) {
                $this->notify_assigned_tasks($assigned_to['assigned_to'], $id);
            }

            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'tasks',
                'module_field_id' => $id,
                'activity' => $activity,
                'icon' => 'fa-tasks',
                'link' => 'admin/tasks/view_task_details/' . $id,
                'value1' => $tasks_info->task_name,
            );
// Update into tbl_project
            $this->tasks_model->_table_name = "tbl_activities"; //table name
            $this->tasks_model->_primary_key = "activities_id";
            $this->tasks_model->save($activities);

            $type = "success";
            $message = $msg;
            set_message($type, $message);
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect($_SERVER['HTTP_REFERER']);
        }

    }

    public function change_status($tasks_id, $status)
    {
        $can_edit = $this->tasks_model->can_action('tbl_task', 'edit', array('task_id' => $tasks_id));
        if (!empty($can_edit)) {
            $tasks_info = $this->tasks_model->check_by(array('task_id' => $tasks_id), 'tbl_task');

            $notifiedUsers = array();
            if (!empty($tasks_info->permission) && $tasks_info->permission != 'all') {
                $permissionUsers = json_decode($tasks_info->permission);
                foreach ($permissionUsers as $user => $v_permission) {
                    array_push($notifiedUsers, $user);
                }
            } else {
                $notifiedUsers = $this->tasks_model->allowad_user_id('54');
            }
            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'not_changed_status',
                            'link' => 'admin/tasks/view_task_details/' . $tasks_id,
                            'value' => lang('status') . ' : ' . $tasks_info->task_status . ' to ' . $status,
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }

            if (!empty($tasks_info->project_id)) {
                $this->tasks_model->set_progress($tasks_info->project_id);
            }

            if ($status == 'not_started') {
                $date = new DateTime("now");
                $curr_date = $date->format('Y-m-d - H:m a');
                $data['task_progress'] = 0;
                $data['task_update_time'] = $curr_date;
            }
            if ($status == 'completed') {
                $date = new DateTime("now");
                $curr_date = $date->format('Y-m-d - H:m a');
                $data['task_progress'] = 100;
                $data['task_status'] = $status;
                $data['task_update_time'] = $curr_date;
            } else {
                $date = new DateTime("now");
                $curr_date = $date->format('Y-m-d - H:m a');
                $data['task_status'] = $status;
                $data['task_update_time'] = $curr_date;
            }
            $this->tasks_model->_table_name = "tbl_task"; // table name
            $this->tasks_model->_primary_key = "task_id"; // $id
            $id = $this->tasks_model->save($data, $tasks_id);
            $activity = 'activity_update_task';
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'tasks',
                'module_field_id' => $id,
                'activity' => $activity,
                'icon' => 'fa-tasks',
                'link' => 'admin/tasks/view_task_details/' . $id,
                'value1' => $tasks_info->task_name,
            );
            // Update into tbl_project
            $this->tasks_model->_table_name = "tbl_activities"; //table name
            $this->tasks_model->_primary_key = "activities_id";
            $this->tasks_model->save($activities);
            // messages for user
            $type = "success";
            $message = lang('change_status');
            set_message($type, $message);
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect($_SERVER['HTTP_REFERER']);
        }

    }

    public function completed_tasks($id = NULL)
    {
        $can_edit = $this->tasks_model->can_action('tbl_task', 'edit', array('task_id' => $id));
        if (!empty($can_edit)) {
            $tasks_info = $this->tasks_model->check_by(array('task_id' => $id), 'tbl_task');
            if ($tasks_info->task_progress == 100) {
                $data['task_progress'] = 0;
                $data['task_status'] = 'not_started';
            } else {
                $data['task_progress'] = $this->input->post('task_progress');
                $data['task_status'] = $this->input->post('task_status');
            }
            //save data into table.
            $this->tasks_model->_table_name = "tbl_task"; // table name
            $this->tasks_model->_primary_key = "task_id"; // $id
            $id = $this->tasks_model->save($data, $id);

            $tasks_info = $this->tasks_model->check_by(array('task_id' => $id), 'tbl_task');
            if (!empty($tasks_info->project_id)) {
                $this->tasks_model->set_progress($tasks_info->project_id);
            }
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'tasks',
                'module_field_id' => $id,
                'activity' => 'activity_update_task',
                'icon' => 'fa-tasks',
                'link' => 'admin/tasks/view_task_details/' . $id,
                'value1' => $data['task_progress'],
            );
            // Update into tbl_project
            $this->tasks_model->_table_name = "tbl_activities"; //table name
            $this->tasks_model->_primary_key = "activities_id";
            $this->tasks_model->save($activities);
            $type = "success";
            $message = lang('update_task');
            echo json_encode(array("status" => $type, "message" => $message));
        } else {
            $type = "error";
            $message = lang('there_in_no_value');
            echo json_encode(array("status" => $type, "message" => $message));
        }
    }

    public function save_tasks_notes($id)
    {

        $data = $this->tasks_model->array_from_post(array('tasks_notes'));

//save data into table.
        $this->tasks_model->_table_name = "tbl_task"; // table name
        $this->tasks_model->_primary_key = "task_id"; // $id
        $id = $this->tasks_model->save($data, $id);
// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'tasks',
            'module_field_id' => $id,
            'activity' => 'activity_update_task',
            'icon' => 'fa-tasks',
            'link' => 'admin/tasks/view_task_details/' . $id . '/4',
            'value1' => $data['tasks_notes'],
        );
// Update into tbl_project
        $this->tasks_model->_table_name = "tbl_activities"; //table name
        $this->tasks_model->_primary_key = "activities_id";
        $this->tasks_model->save($activities);

        $type = "success";
        $message = lang('update_task');
        set_message($type, $message);
        redirect('admin/tasks/view_task_details/' . $id . '/4');
    }

    public function save_comments()
    {

        $data['task_id'] = $this->input->post('task_id', TRUE);
        $data['comment'] = $this->input->post('comment', TRUE);
        if (!empty($_FILES['comments_attachment']['name']['0'])) {
            $old_path_info = $this->input->post('upload_path');
            if (!empty($old_path_info)) {
                foreach ($old_path_info as $old_path) {
                    unlink($old_path);
                }
            }
            $mul_val = $this->tasks_model->multi_uploadAllType('comments_attachment');
            $data['comments_attachment'] = json_encode($mul_val);
        }
        $data['user_id'] = $this->session->userdata('user_id');

//save data into table.
        $this->tasks_model->_table_name = "tbl_task_comment"; // table name
        $this->tasks_model->_primary_key = "task_comment_id"; // $id
        $comment_id = $this->tasks_model->save($data);
// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'tasks',
            'module_field_id' => $data['task_id'],
            'activity' => 'activity_new_task_comment',
            'icon' => 'fa-tasks',
            'link' => 'admin/tasks/view_task_details/' . $data['task_id'] . '/2',
            'value1' => $data['comment'],
        );
// Update into tbl_project
        $this->tasks_model->_table_name = "tbl_activities"; //table name
        $this->tasks_model->_primary_key = "activities_id";
        $this->tasks_model->save($activities);

        $tasks_info = $this->tasks_model->check_by(array('task_id' => $data['task_id']), 'tbl_task');

        $notifiedUsers = array();
        if (!empty($tasks_info->permission) && $tasks_info->permission != 'all') {
            $permissionUsers = json_decode($tasks_info->permission);
            foreach ($permissionUsers as $user => $v_permission) {
                array_push($notifiedUsers, $user);
            }
        } else {
            $notifiedUsers = $this->tasks_model->allowad_user_id('54');
        }
        if (!empty($notifiedUsers)) {
            foreach ($notifiedUsers as $users) {
                if ($users != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $users,
                        'from_user_id' => true,
                        'description' => 'not_new_comment',
                        'link' => 'admin/tasks/view_task_details/' . $data['task_id'] . '/2',
                        'value' => lang('task') . ' ' . $tasks_info->task_name,
                    ));
                }
            }
            show_notification($notifiedUsers);
        }

// send notification
        $this->notify_comments_tasks($comment_id);

        $type = "success";
        $message = lang('task_comment_save');
        set_message($type, $message);
        redirect('admin/tasks/view_task_details/' . $data['task_id'] . '/' . 2);
    }

    public function save_comments_reply($task_comment_id)
    {
        $data['task_id'] = $this->input->post('task_id', TRUE);
        $data['comment'] = $this->input->post('reply_comments', TRUE);
        $data['user_id'] = $this->session->userdata('user_id');
        $data['comments_reply_id'] = $task_comment_id;
        //save data into table.
        $this->tasks_model->_table_name = "tbl_task_comment"; // table name
        $this->tasks_model->_primary_key = "task_comment_id"; // $id
        $comment_id = $this->tasks_model->save($data);
        $comments_info = $this->tasks_model->check_by(array('task_comment_id' => $task_comment_id), 'tbl_task_comment');
        $user = $this->tasks_model->check_by(array('user_id' => $comments_info->user_id), 'tbl_users');
        if ($user->role_id == 2) {
            $url = 'client/';
        } else {
            $url = 'admin/';
        }
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'tasks',
            'module_field_id' => $data['task_id'],
            'activity' => 'activity_new_comment_reply',
            'icon' => 'fa-tasks',
            'link' => $url . 'tasks/view_task_details/' . $data['task_id'] . '/2',
            'value1' => $this->db->where('task_comment_id', $task_comment_id)->get('tbl_task_comment')->row()->comment,
            'value2' => $data['comment'],
        );
        // Update into tbl_project
        $this->tasks_model->_table_name = "tbl_activities"; //table name
        $this->tasks_model->_primary_key = "activities_id";
        $this->tasks_model->save($activities);

        $tasks_info = $this->tasks_model->check_by(array('task_id' => $data['task_id']), 'tbl_task');
        $notifiedUsers = array($comments_info->user_id);
        if (!empty($notifiedUsers)) {
            foreach ($notifiedUsers as $users) {
                if ($users != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $users,
                        'from_user_id' => true,
                        'description' => 'not_comment_reply',
                        'link' => $url . 'tasks/view_task_details/' . $data['task_id'] . '/2',
                        'value' => lang('task') . ' ' . $tasks_info->task_name,
                    ));
                }
            }
            show_notification($notifiedUsers);
        }

        // send notification
        $this->notify_comments_tasks($comment_id);

        $type = "success";
        $message = lang('task_comment_save');
        set_message($type, $message);
        redirect('admin/tasks/view_task_details/' . $data['task_id'] . '/' . '2');
    }

    function notify_comments_tasks($comment_id)
    {
        $email_template = $this->tasks_model->check_by(array('email_group' => 'tasks_comments'), 'tbl_email_templates');
        $tasks_comment_info = $this->tasks_model->check_by(array('task_comment_id' => $comment_id), 'tbl_task_comment');
        $user = $this->tasks_model->check_by(array('user_id' => $tasks_comment_info->user_id), 'tbl_users');
        if ($user->role_id == 2) {
            $url = 'client/';
        } else {
            $url = 'admin/';
        }
        $tasks_info = $this->tasks_model->check_by(array('task_id' => $tasks_comment_info->task_id), 'tbl_task');
        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $task_name = str_replace("{TASK_NAME}", $tasks_info->task_name, $message);
        $assigned_by = str_replace("{POSTED_BY}", ucfirst($this->session->userdata('name')), $task_name);
        $Link = str_replace("{COMMENT_URL}", base_url() . $url . 'tasks/view_task_details/' . $tasks_info->task_id . '/' . $data['active'] = 2, $assigned_by);
        $comments = str_replace("{COMMENT_MESSAGE}", $tasks_comment_info->comment, $Link);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $comments);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';

        if (!empty($tasks_info->permission) && $tasks_info->permission != 'all') {
            $user = json_decode($tasks_info->permission);
            foreach ($user as $key => $v_user) {
                $allowad_user[] = $key;
            }
        } else {
            $allowad_user = $this->tasks_model->allowad_user_id('54');
        }
        if (!empty($allowad_user)) {
            foreach ($allowad_user as $v_user) {
                $login_info = $this->tasks_model->check_by(array('user_id' => $v_user), 'tbl_users');
                $params['recipient'] = $login_info->email;
                $this->tasks_model->send_email($params);
            }
        }
    }

    public function delete_task_comments($task_id, $task_comment_id)
    {
//save data into table.
        $this->tasks_model->_table_name = "tbl_task_comment"; // table name
        $this->tasks_model->_primary_key = "task_comment_id"; // $id
        $this->tasks_model->delete($task_comment_id);

        $type = "success";
        $message = lang('task_comment_deleted');
        set_message($type, $message);
        redirect('admin/tasks/view_task_details/' . $task_id . '/' . $data['active'] = 2);
    }

    public function delete_task_files($task_id, $task_attachment_id)
    {
        $file_info = $this->tasks_model->check_by(array('task_attachment_id' => $task_attachment_id), 'tbl_task_attachment');
// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'tasks',
            'module_field_id' => $task_id,
            'activity' => 'activity_task_attachfile_deleted',
            'icon' => 'fa-tasks',
            'link' => 'admin/tasks/view_task_details/' . $task_id . '/3',
            'value1' => $file_info->title,
        );
// Update into tbl_project
        $this->tasks_model->_table_name = "tbl_activities"; //table name
        $this->tasks_model->_primary_key = "activities_id";
        $this->tasks_model->save($activities);

//save data into table.
        $this->tasks_model->_table_name = "tbl_task_attachment"; // table name
        $this->tasks_model->delete_multiple(array('task_attachment_id' => $task_attachment_id));

        $type = "success";
        $message = lang('task_attachfile_deleted');
        set_message($type, $message);
        redirect('admin/tasks/view_task_details/' . $task_id . '/3');
    }

    public function save_task_attachment($task_attachment_id = NULL)
    {
        $data = $this->tasks_model->array_from_post(array('title', 'description', 'task_id'));
        $data['user_id'] = $this->session->userdata('user_id');

// save and update into tbl_files
        $this->tasks_model->_table_name = "tbl_task_attachment"; //table name
        $this->tasks_model->_primary_key = "task_attachment_id";
        if (!empty($task_attachment_id)) {
            $id = $task_attachment_id;
            $this->tasks_model->save($data, $id);
            $msg = lang('task_file_updated');
        } else {
            $id = $this->tasks_model->save($data);
            $msg = lang('task_file_added');
        }

        if (!empty($_FILES['task_files']['name']['0'])) {
            $old_path_info = $this->input->post('uploaded_path');
            if (!empty($old_path_info)) {
                foreach ($old_path_info as $old_path) {
                    unlink($old_path);
                }
            }
            $mul_val = $this->tasks_model->multi_uploadAllType('task_files');

            foreach ($mul_val as $val) {
                $val == TRUE || redirect('admin/tasks/view_task_details/3/' . $data['task_id']);
                $fdata['files'] = $val['path'];
                $fdata['file_name'] = $val['fileName'];
                $fdata['uploaded_path'] = $val['fullPath'];
                $fdata['size'] = $val['size'];
                $fdata['ext'] = $val['ext'];
                $fdata['is_image'] = $val['is_image'];
                $fdata['image_width'] = $val['image_width'];
                $fdata['image_height'] = $val['image_height'];
                $fdata['task_attachment_id'] = $id;
                $this->tasks_model->_table_name = "tbl_task_uploaded_files"; // table name
                $this->tasks_model->_primary_key = "uploaded_files_id"; // $id
                $this->tasks_model->save($fdata);
            }
        }
// save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'tasks',
            'module_field_id' => $data['task_id'],
            'activity' => 'activity_new_task_attachment',
            'icon' => 'fa-tasks',
            'link' => 'admin/tasks/view_task_details/' . $data['task_id'] . '/3',
            'value1' => $data['title'],
        );
// Update into tbl_project
        $this->tasks_model->_table_name = "tbl_activities"; //table name
        $this->tasks_model->_primary_key = "activities_id";
        $this->tasks_model->save($activities);
// send notification message
        $this->notify_attchemnt_tasks($id);
// messages for user
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/tasks/view_task_details/' . $data['task_id'] . '/3');
    }

    function notify_attchemnt_tasks($task_attachment_id)
    {
        $email_template = $this->tasks_model->check_by(array('email_group' => 'tasks_attachment'), 'tbl_email_templates');
        $tasks_comment_info = $this->tasks_model->check_by(array('task_attachment_id' => $task_attachment_id), 'tbl_task_attachment');

        $tasks_info = $this->tasks_model->check_by(array('task_id' => $tasks_comment_info->task_id), 'tbl_task');
        $message = $email_template->template_body;

        $subject = $email_template->subject;

        $task_name = str_replace("{TASK_NAME}", $tasks_info->task_name, $message);
        $assigned_by = str_replace("{UPLOADED_BY}", ucfirst($this->session->userdata('name')), $task_name);
        $Link = str_replace("{TASK_URL}", base_url() . 'admin/tasks/view_task_details/' . $tasks_info->task_id . '/' . $data['active'] = 3, $assigned_by);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);

        $params['subject'] = $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';
        if (!empty($tasks_info->permission) && $tasks_info->permission != 'all') {
            $user = json_decode($tasks_info->permission);
            foreach ($user as $key => $v_user) {
                $allowad_user[] = $key;
            }
        } else {
            $allowad_user = $this->tasks_model->allowad_user_id('54');
        }
        if (!empty($allowad_user)) {
            foreach ($allowad_user as $v_user) {
                $login_info = $this->tasks_model->check_by(array('user_id' => $v_user), 'tbl_users');
                $params['recipient'] = $login_info->email;
                $this->tasks_model->send_email($params);

                if ($v_user != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $v_user,
                        'from_user_id' => true,
                        'description' => 'not_uploaded_attachment',
                        'link' => 'admin/tasks/view_task_details/' . $tasks_info->task_id . '/3',
                        'value' => lang('task') . ' ' . $tasks_info->task_name,
                    ));
                }
            }
            show_notification($allowad_user);
        }
    }

    public function view_task_details($id, $active = NULL, $edit = NULL)
    {

        if (!empty($edit)) {
            $tasks_timer_id = $id;
            $id = $this->db->where(array('tasks_timer_id' => $id))->get('tbl_tasks_timer')->row()->task_id;
        } else {
            $id = $id;
        }
        $data['title'] = lang('task_details');


//get all task information
        $data['task_details'] = $this->tasks_model->check_by(array('task_id' => $id), 'tbl_task');

//        //get all comments info
//        $data['comment_details'] = $this->tasks_model->get_all_comment_info($id);
// get all assign_user
        $this->tasks_model->_table_name = 'tbl_users';
        $this->tasks_model->_order_by = 'user_id';
        $data['assign_user'] = $this->tasks_model->get_by(array('role_id !=' => '2'), FALSE);

        $this->tasks_model->_table_name = "tbl_task_attachment"; //table name
        $this->tasks_model->_order_by = "task_id";
        $data['files_info'] = $this->tasks_model->get_by(array('task_id' => $id), FALSE);

        foreach ($data['files_info'] as $key => $v_files) {
            $this->tasks_model->_table_name = "tbl_task_uploaded_files"; //table name
            $this->tasks_model->_order_by = "task_attachment_id";
            $data['project_files_info'][$key] = $this->tasks_model->get_by(array('task_attachment_id' => $v_files->task_attachment_id), FALSE);
        }

        if ($active == 2) {
            $data['active'] = 2;
            $data['time_active'] = 1;
            $data['bugs_active'] = 1;
        } elseif ($active == 3) {
            $data['active'] = 3;
            $data['time_active'] = 1;
            $data['bugs_active'] = 1;
        } elseif ($active == 4) {
            $data['active'] = 4;
            $data['time_active'] = 1;
            $data['bugs_active'] = 1;
        } elseif ($active == 9) {
            $data['active'] = 4;
            $data['time_active'] = 1;
            $data['bugs_active'] = 1;
        } elseif ($active == 5) {
            $data['active'] = 5;
            if (!empty($edit)) {
                $data['time_active'] = 2;
                $data['tasks_timer_info'] = $this->tasks_model->check_by(array('tasks_timer_id' => $tasks_timer_id), 'tbl_tasks_timer');
            } else {
                $data['time_active'] = 1;
                $data['bugs_active'] = 1;
            }
        } else {
            $data['active'] = 1;
            $data['time_active'] = 1;
            $data['bugs_active'] = 1;
        }
        //print_r($data);die;

        $data['subview'] = $this->load->view('admin/tasks/view_task', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function update_tasks_timer($id = NULL, $action = NULL)
    {
        if (!empty($action)) {
            $t_data['task_id'] = $this->db->where(array('tasks_timer_id' => $id))->get('tbl_tasks_timer')->row()->task_id;
            $activity = 'activity_delete_tasks_timesheet';
            $msg = lang('delete_timesheet');
        } else {
            $activity = ('activity_update_task_timesheet');
            $msg = lang('timer_update');
        }
        if ($action != 'delete_task_timmer') {
            $t_data = $this->tasks_model->array_from_post(array('task_id', 'start_date', 'start_time', 'end_date', 'end_time'));
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

            $data['task_id'] = $t_data['task_id'];
            $data['user_id'] = $this->session->userdata('user_id');

            $this->tasks_model->_table_name = "tbl_tasks_timer"; //table name
            $this->tasks_model->_primary_key = "tasks_timer_id";
            if (!empty($id)) {
                $id = $this->tasks_model->save($data, $id);
            } else {
                $id = $this->tasks_model->save($data);
            }
        } else {
            $this->tasks_model->_table_name = "tbl_tasks_timer"; //table name
            $this->tasks_model->_primary_key = "tasks_timer_id";
            $this->tasks_model->delete($id);
        }
        $task_info = $this->tasks_model->check_by(array('task_id' => $t_data['task_id']), 'tbl_task');
        $notifiedUsers = array();
        if (!empty($task_info->permission) && $task_info->permission != 'all') {
            $permissionUsers = json_decode($task_info->permission);
            foreach ($permissionUsers as $user => $v_permission) {
                array_push($notifiedUsers, $user);
            }
        } else {
            $notifiedUsers = $this->tasks_model->allowad_user_id('54');
        }
        if (!empty($notifiedUsers)) {
            foreach ($notifiedUsers as $users) {
                if ($users != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $users,
                        'from_user_id' => true,
                        'description' => 'not_update_timer',
                        'link' => 'admin/tasks/view_task_details/' . $task_info->task_id . '/5',
                        'value' => lang('task') . ' ' . $task_info->task_name,
                    ));
                }
            }
        }
        show_notification($notifiedUsers);
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'tasks',
            'module_field_id' => $id,
            'activity' => $activity,
            'icon' => 'fa-tasks',
            'link' => 'admin/tasks/view_task_details/' . $id . '/5',
            'value1' => $task_info->task_name,
        );
        $this->tasks_model->_table_name = "tbl_activities"; //table name
        $this->tasks_model->_primary_key = "activities_id";
        $this->tasks_model->save($activities);
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/tasks/view_task_details/' . $t_data['task_id'] . '/5');
    }

    public function download_files($task_id, $uploaded_files_id, $comments = null)
    {
        $this->load->helper('download');
        if (!empty($comments)) {
            if ($task_id) {
                $down_data = file_get_contents('uploads/' . $uploaded_files_id); // Read the file's contents
                force_download($uploaded_files_id, $down_data);
            } else {
                $type = "error";
                $message = 'Operation Fieled !';
                set_message($type, $message);
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $uploaded_files_info = $this->tasks_model->check_by(array('uploaded_files_id' => $uploaded_files_id), 'tbl_task_uploaded_files');
            if ($uploaded_files_info->uploaded_path) {
                $data = file_get_contents($uploaded_files_info->uploaded_path); // Read the file's contents
                force_download($uploaded_files_info->file_name, $data);
            } else {
                $type = "error";
                $message = lang('operation_failed');
                set_message($type, $message);
                redirect('admin/tasks/view_task_details/' . $task_id . '/3');
            }
        }
    }

    public function delete_task($id)
    {
        $can_delete = $this->tasks_model->can_action('tbl_task', 'delete', array('task_id' => $id));
        if (!empty($can_delete)) {
            $task_info = $this->tasks_model->check_by(array('task_id' => $id), 'tbl_task');

// save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'tasks',
                'module_field_id' => $task_info->task_id,
                'activity' => 'activity_task_deleted',
                'icon' => 'fa-tasks',
                'value1' => $task_info->task_name,
            );
// Update into tbl_project
            $this->tasks_model->_table_name = "tbl_activities"; //table name
            $this->tasks_model->_primary_key = "activities_id";
            $this->tasks_model->save($activities);

            $this->tasks_model->_table_name = "tbl_task_attachment"; //table name
            $this->tasks_model->_order_by = "task_id";
            $files_info = $this->tasks_model->get_by(array('task_id' => $id), FALSE);
            foreach ($files_info as $v_files) {
                $this->tasks_model->_table_name = "tbl_task_uploaded_files"; //table name
                $this->tasks_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
            }
//delete into table.
            $this->tasks_model->_table_name = "tbl_task_attachment"; // table name
            $this->tasks_model->delete_multiple(array('task_id' => $id));

//delete data into table.
            $this->tasks_model->_table_name = "tbl_task_comment"; // table name
            $this->tasks_model->delete_multiple(array('task_id' => $id));

            $pin_info = $this->tasks_model->check_by(array('module_name' => 'tasks', 'module_id' => $id), 'tbl_pinaction');
            if (!empty($pin_info)) {
                $this->tasks_model->_table_name = 'tbl_pinaction';
                $this->tasks_model->delete_multiple(array('module_name' => 'tasks', 'module_id' => $id));
            }

            $this->tasks_model->_table_name = "tbl_task"; // table name
            $this->tasks_model->_primary_key = "task_id"; // $id
            $this->tasks_model->delete($id);

            $type = "success";
            $message = lang('task_deleted');
            set_message($type, $message);
        } else {
            set_message('error', lang('there_in_no_value'));
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function tasks_timer($status, $task_id, $details = NULL)
    {
        $task_start = $this->tasks_model->check_by(array('task_id' => $task_id), 'tbl_task');
        $notifiedUsers = array();
        if (!empty($task_start->permission) && $task_start->permission != 'all') {
            $permissionUsers = json_decode($task_start->permission);
            foreach ($permissionUsers as $user => $v_permission) {
                array_push($notifiedUsers, $user);
            }
        } else {
            $notifiedUsers = $this->tasks_model->allowad_user_id('54');
        }

        if ($status == 'off') {
// check this user start time or this user is admin
// if true then off time
// else do not off time
            $check_user = $this->timer_started_by($task_id);
            if ($check_user == TRUE) {
                $task_logged_time = $this->tasks_model->task_spent_time_by_id($task_id);
                $time_logged = (time() - $task_start->start_time) + $task_logged_time; //time already logged
                $data = array(
                    'timer_status' => $status,
                    'logged_time' => $time_logged,
                    'start_time' => ''
                );
// Update into tbl_task
                $this->tasks_model->_table_name = "tbl_task"; //table name
                $this->tasks_model->_primary_key = "task_id";
                $this->tasks_model->save($data, $task_id);
// save into tbl_task_timer
                $t_data = array(
                    'task_id' => $task_id,
                    'user_id' => $this->session->userdata('user_id'),
                    'start_time' => $task_start->start_time,
                    'end_time' => time()
                );

// insert into tbl_task_timer
                $this->tasks_model->_table_name = "tbl_tasks_timer"; //table name
                $this->tasks_model->_primary_key = "tasks_timer_id";
                $this->tasks_model->save($t_data);

// save into activities
                $activities = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'tasks',
                    'module_field_id' => $task_id,
                    'activity' => ('activity_tasks_timer_off'),
                    'icon' => 'fa-tasks',
                    'link' => 'admin/tasks/view_task_details/' . $task_id . '/5',
                    'value1' => $task_start->task_name,
                );
// Update into tbl_project
                $this->tasks_model->_table_name = "tbl_activities"; //table name
                $this->tasks_model->_primary_key = "activities_id";
                $this->tasks_model->save($activities);
                if (!empty($notifiedUsers)) {
                    foreach ($notifiedUsers as $users) {
                        if ($users != $this->session->userdata('user_id')) {
                            add_notification(array(
                                'to_user_id' => $users,
                                'from_user_id' => true,
                                'description' => 'not_timer_stop',
                                'link' => 'admin/tasks/view_task_details/' . $task_start->task_id . '/5',
                                'value' => lang('task') . ' ' . $task_start->task_name,
                            ));
                        }
                    }
                    show_notification($notifiedUsers);
                }
            }
        } else {
            $data = array(
                'timer_status' => $status,
                'timer_started_by' => $this->session->userdata('user_id'),
                'start_time' => time()
            );

// save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'tasks',
                'module_field_id' => $task_id,
                'activity' => 'activity_tasks_timer_on',
                'icon' => 'fa-tasks',
                'link' => 'admin/tasks/view_task_details/' . $task_id . '/5',
                'value1' => $task_start->task_name,
            );
// Update into tbl_project
            $this->tasks_model->_table_name = "tbl_activities"; //table name
            $this->tasks_model->_primary_key = "activities_id";
            $this->tasks_model->save($activities);

// Update into tbl_task
            $this->tasks_model->_table_name = "tbl_task"; //table name
            $this->tasks_model->_primary_key = "task_id";
            $this->tasks_model->save($data, $task_id);

            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'not_timer_start',
                            'link' => 'admin/tasks/view_task_details/' . $task_start->task_id . '/5',
                            'value' => lang('task') . ' ' . $task_start->task_name,
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }
        }
// messages for user
        $type = "success";
        $message = lang('task_timer_' . $status);
        set_message($type, $message);
        redirect($_SERVER['HTTP_REFERER']);

    }

    public function timer_started_by($task_id)
    {
        $user_id = $this->session->userdata('user_id');
        $user_info = $this->tasks_model->check_by(array('user_id' => $user_id), 'tbl_users');
        $timer_started_info = $this->tasks_model->check_by(array('task_id' => $task_id), 'tbl_task');
        if ($timer_started_info->timer_started_by == $user_id || $user_info->role_id == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function claer_activities($module, $id)
    {
        //save data into table.
        $where = array('module' => $module, 'module_field_id' => $id);
        $this->tasks_model->_table_name = "tbl_activities"; // table name
        $this->tasks_model->delete_multiple($where);
        redirect($_SERVER['HTTP_REFERER']);

    }

    public function export_report($id)
    {
        $data['title'] = lang('export_report');
        $this->tasks_model->_table_name = "tbl_task_attachment"; //table name
        $this->tasks_model->_order_by = "task_id";
        $data['files_info'] = $this->tasks_model->get_by(array('task_id' => $id), FALSE);

        foreach ($data['files_info'] as $key => $v_files) {
            $this->tasks_model->_table_name = "tbl_task_uploaded_files"; //table name
            $this->tasks_model->_order_by = "task_attachment_id";
            $data['project_files_info'][$key] = $this->tasks_model->get_by(array('task_attachment_id' => $v_files->task_attachment_id), FALSE);
        }
        $data['task_details'] = $this->tasks_model->check_by(array('task_id' => $id), 'tbl_task');
        $viewfile = $this->load->view('admin/tasks/export_report', $data, TRUE);
        $this->load->helper('dompdf');
        pdf_create($viewfile, $data['task_details']->task_name . '-' . lang('details'), 1, 1);
//        $this->load->view('admin/_layout_main', $data);
    }

    public function change_tasks_status($task_status = NULL)
    {
        $task_id = $this->input->post('task_id', true);
        foreach ($task_id as $key => $id) {
            $data['task_status'] = $task_status;
            if ($data['task_status'] == 1) {
                $data['task_status'] = 'not_started';
            } elseif ($data['task_status'] == 2) {
                $data['task_status'] = 'in_progress';
            } elseif ($data['task_status'] == 3) {
                $data['task_status'] = 'completed';
            } elseif ($data['task_status'] == 4) {
                $data['task_status'] = 'deferred';
            } else {
                $data['task_status'] = 'waiting_for_someone';
            }
            $data['index_no'] = $key + 1;
            //save data into table.
            $this->tasks_model->_table_name = "tbl_task"; // table name
            $this->tasks_model->_primary_key = "task_id"; // $id
            $id = $this->tasks_model->save($data, $id);

        }
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'tasks',
            'module_field_id' => $id,
            'activity' => 'activity_update_task',
            'icon' => 'fa-tasks',
            'link' => 'admin/tasks/view_task_details/' . $id,
            'value1' => $data['task_status'],
        );
        // Update into tbl_project
        $this->tasks_model->_table_name = "tbl_activities"; //table name
        $this->tasks_model->_primary_key = "activities_id";
        $this->tasks_model->save($activities);

        $task_start = $this->tasks_model->check_by(array('task_id' => $task_id), 'tbl_task');
        $notifiedUsers = array();
        if (!empty($task_start->permission) && $task_start->permission != 'all') {
            $permissionUsers = json_decode($task_start->permission);
            foreach ($permissionUsers as $user => $v_permission) {
                array_push($notifiedUsers, $user);
            }
        } else {
            $notifiedUsers = $this->tasks_model->allowad_user_id('54');
        }
        if (!empty($notifiedUsers)) {
            foreach ($notifiedUsers as $users) {
                if ($users != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $users,
                        'from_user_id' => true,
                        'description' => 'not_changed_status',
                        'link' => 'admin/tasks/view_task_details/' . $task_start->task_id,
                        'value' => lang('task') . ' ' . $task_start->task_name,
                    ));
                }
            }
            show_notification($notifiedUsers);
        }

        $type = "success";
        $message = lang('update_task');
        echo json_encode(array("status" => $type, "message" => $message));
    }
}
