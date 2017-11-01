<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Leads extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('items_model');

        $this->load->helper('ckeditor');
        $this->data['ckeditor'] = array(
            'id' => 'ck_editor',
            'path' => 'asset/js/ckeditor',
            'config' => array(
                'toolbar' => "Full",
                'width' => "99.8%",
                'height' => "400px"
            )
        );
    }

    public function index($id = NULL)
    {
        $data['title'] = lang('all_leads');
        if (!empty($id)) {
            if ($id == 'kanban') {
                $data['active'] = 1;
                $k_session['leads_kanban'] = $id;
                $this->session->set_userdata($k_session);
            } elseif ($id == 'list') {
                $data['active'] = 1;
                $this->session->unset_userdata('leads_kanban');
            } elseif ($id == 'by_status') {
                $data['active'] = 1;
                $lead_status_id = $this->uri->segment(5);
                if ($lead_status_id == 'all') {
                    redirect('admin/leads');
                } else {
                    $search = true;
                    $by_status = $this->items_model->get_leads('by_status', $lead_status_id);
                }
                $this->session->unset_userdata('leads_kanban');
            } elseif ($id == 'by_source') {
                $data['active'] = 1;
                $lead_source_id = $this->uri->segment(5);
                if ($lead_source_id == 'all') {
                    redirect('admin/leads');
                } else {
                    $search = true;
                    $by_status = $this->items_model->get_leads('by_source', $lead_source_id);
                }
                $this->session->unset_userdata('leads_kanban');
            } else {
                $data['active'] = 2;
                $can_edit = $this->items_model->can_action('tbl_leads', 'edit', array('leads_id' => $id));
                if (!empty($can_edit)) {
                    $data['leads_info'] = $this->items_model->check_by(array('leads_id' => $id), 'tbl_leads');
                }
                $this->session->unset_userdata('leads_kanban');
            }

        } else {
            $data['active'] = 1;
        }
        // get all leads status
        $status_info = $this->db->order_by('order_no', 'ASC')->get('tbl_lead_status')->result();
        if (!empty($status_info)) {
            foreach ($status_info as $v_status) {
                $data['status_info'][$v_status->lead_type][] = $v_status;
            }
        }
        $data['assign_user'] = $this->items_model->allowad_user('55');
        if (!empty($search)) {
            $data['all_leads'] = $by_status;
        } else {
            $data['all_leads'] = $this->items_model->get_permission('tbl_leads');
        }


        $data['subview'] = $this->load->view('admin/leads/all_leads', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function import_leads()
    {
        $data['title'] = lang('import_leads');
        $data['assign_user'] = $this->items_model->allowad_user('55');
        // get all leads status
        $status_info = $this->db->get('tbl_lead_status')->result();
        if (!empty($status_info)) {
            foreach ($status_info as $v_status) {
                $data['status_info'][$v_status->lead_type][] = $v_status;
            }
        }

        $data['subview'] = $this->load->view('admin/leads/import_leads', $data, TRUE);
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
                    // Save Into leads table
                    // **********************
                    $data = $this->items_model->array_from_post(array('client_id', 'lead_status_id', 'lead_source_id'));
                    $data['lead_name'] = trim($sheetData[$x]["A"]);
                    $data['organization'] = trim($sheetData[$x]["B"]);
                    $data['contact_name'] = trim($sheetData[$x]["C"]);
                    $data['email'] = trim($sheetData[$x]["D"]);
                    $data['phone'] = trim($sheetData[$x]["E"]);
                    $data['mobile'] = trim($sheetData[$x]["F"]);
                    $data['address'] = trim($sheetData[$x]["G"]);
                    $data['city'] = trim($sheetData[$x]["H"]);
                    $data['country'] = trim($sheetData[$x]["I"]);
                    $data['facebook'] = trim($sheetData[$x]["J"]);
                    $data['skype'] = trim($sheetData[$x]["K"]);
                    $data['twitter'] = trim($sheetData[$x]["L"]);
                    $data['notes'] = trim($sheetData[$x]["M"]);

                    $data['permission'] = 'all';
                    // save to tbl_leads
                    $this->items_model->_table_name = 'tbl_leads';
                    $this->items_model->_primary_key = 'leads_id';
                    $this->items_model->save($data);
                }
                $type = 'success';
                $message = lang('save_leads');
            } else {
                $type = 'error';
                $message = "Sorry your uploaded file type not allowed ! please upload XLS/CSV File ";
            }
        } else {
            $type = 'error';
            $message = "You did not Select File! please upload XLS/CSV File ";
        }
        set_message($type, $message);
        redirect('admin/leads');
    }

    public
    function saved_leads($id = NULL)
    {
        $this->items_model->_table_name = 'tbl_leads';
        $this->items_model->_primary_key = 'leads_id';

        $data = $this->items_model->array_from_post(array('client_id', 'lead_name', 'organization', 'lead_status_id', 'lead_source_id', 'contact_name', 'email', 'phone', 'mobile', 'address', 'city', 'state', 'country', 'facebook', 'skype', 'twitter', 'notes'));
        // update root category
        $where = array('client_id' => $data['client_id'], 'lead_name' => $data['lead_name']);
        // duplicate value check in DB
        if (!empty($id)) { // if id exist in db update data
            $leads_id = array('leads_id !=' => $id);
        } else { // if id is not exist then set id as null
            $leads_id = null;
        }

        // check whether this input data already exist or not
        $check_leads = $this->items_model->check_update('tbl_leads', $where, $leads_id);
        if (!empty($check_leads)) { // if input data already exist show error alert
            // massage for user
            $type = 'error';
            $msg = "<strong style='color:#000'>" . $data['lead_name'] . '</strong>  ' . lang('already_exist');
        } else { // save and update query
            $permission = $this->input->post('permission', true);
            if (!empty($permission)) {
                if ($permission == 'everyone') {
                    $assigned = 'all';
                } else {
                    $assigned_to = $this->items_model->array_from_post(array('assigned_to'));
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
            $return_id = $this->items_model->save($data, $id);

            if (!empty($id)) {
                $id = $id;
                $action = 'activity_update_leads';
                $description = 'not_update_leads';
                $msg = lang('update_leads');
            } else {
                $id = $return_id;
                $action = 'activity_save_leads';
                $description = 'not_save_leads';
                $msg = lang('save_leads');
            }
            $u_data['index_no'] = $id;
            $id = $this->items_model->save($u_data, $id);

            save_custom_field(5, $id);
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'leads',
                'module_field_id' => $id,
                'activity' => $action,
                'icon' => 'fa-rocket',
                'link' => 'admin/leads/leads_details/' . $id,
                'value1' => $data['lead_name']
            );
            $this->items_model->_table_name = 'tbl_activities';
            $this->items_model->_primary_key = 'activities_id';
            $this->items_model->save($activity);
            // messages for user
            $type = "success";

            $leads_info = $this->items_model->check_by(array('leads_id' => $id), 'tbl_leads');
            $notifiedUsers = array();
            if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
                $permissionUsers = json_decode($leads_info->permission);
                foreach ($permissionUsers as $user => $v_permission) {
                    array_push($notifiedUsers, $user);
                }
            } else {
                $notifiedUsers = $this->items_model->allowad_user_id('55');
            }
            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => $description,
                            'link' => 'admin/leads/leads_details/' . $leads_info->leads_id,
                            'value' => lang('lead') . ' ' . $leads_info->lead_name,
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }
        }
        $message = $msg;
        set_message($type, $message);
        redirect('admin/leads');
    }

    public
    function leads_details($id, $active = NULL, $op_id = NULL)
    {
        $data['title'] = lang('leads_details');
        //get all task information
        $data['leads_details'] = $this->items_model->check_by(array('leads_id' => $id), 'tbl_leads');

        $this->items_model->_table_name = "tbl_task_attachment"; //table name
        $this->items_model->_order_by = "leads_id";
        $data['files_info'] = $this->items_model->get_by(array('leads_id' => $id), FALSE);

        foreach ($data['files_info'] as $key => $v_files) {
            $this->items_model->_table_name = "tbl_task_uploaded_files"; //table name
            $this->items_model->_order_by = "task_attachment_id";
            $data['project_files_info'][$key] = $this->items_model->get_by(array('task_attachment_id' => $v_files->task_attachment_id), FALSE);
        }

        if ($active == 2) {
            $data['active'] = 2;
            $data['sub_active'] = 1;
            $data['sub_metting'] = 1;
        } elseif ($active == 3) {
            $data['active'] = 3;
            $data['sub_active'] = 1;
            $data['sub_metting'] = 1;
        } elseif ($active == 4) {
            $data['active'] = 4;
            $data['sub_active'] = 1;
            $data['sub_metting'] = 1;
        } elseif ($active == 5) {
            $data['active'] = 5;
            $data['sub_active'] = 1;
            $data['sub_metting'] = 1;
        } elseif ($active == 'metting') {
            $data['active'] = 3;
            $data['sub_active'] = 1;
            $data['sub_metting'] = 2;
            $data['mettings_info'] = $this->items_model->check_by(array('mettings_id' => $op_id), 'tbl_mettings');
        } elseif ($active == 'call') {
            $data['active'] = 2;
            $data['sub_active'] = 2;
            $data['call_info'] = $this->items_model->check_by(array('calls_id' => $op_id), 'tbl_calls');
            $data['sub_metting'] = 1;
        } else {
            $data['active'] = 1;
            $data['sub_active'] = 1;
            $data['sub_metting'] = 1;
        }

        $data['subview'] = $this->load->view('admin/leads/leads_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public
    function convert($id)
    {
        $data['title'] = lang('convert_to_client'); //Page title
        $data['person'] = 1;
        // get all country
        $this->items_model->_table_name = "tbl_countries"; //table name
        $this->items_model->_order_by = "id";
        $data['countries'] = $this->items_model->get();

        // get all currencies
        $this->items_model->_table_name = 'tbl_currencies';
        $this->items_model->_order_by = 'name';
        $data['currencies'] = $this->items_model->get();
        // get all language
        $this->items_model->_table_name = 'tbl_languages';
        $this->items_model->_order_by = 'name';
        $data['languages'] = $this->items_model->get();

        $data['leads_info'] = $this->items_model->check_by(array('leads_id' => $id), 'tbl_leads');
        $data['modal_subview'] = $this->load->view('admin/leads/_modal_convert', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public
    function converted($leads_id)
    {
        $data = $this->items_model->array_from_post(array('name', 'email', 'short_note', 'website', 'phone', 'mobile', 'fax', 'address', 'city', 'zipcode', 'currency',
            'skype_id', 'linkedin', 'facebook', 'twitter', 'language', 'country', 'vat', 'hosting_company', 'hostname', 'port', 'password', 'username', 'client_status', 'latitude', 'longitude', 'customer_group_id'));
        if (!empty($_FILES['profile_photo']['name'])) {
            $val = $this->items_model->uploadImage('profile_photo');
            $val == TRUE || redirect('admin/client/manage_client');
            $data['profile_photo'] = $val['path'];
        }
        $data['leads_id'] = $leads_id;
        $data['client_status'] = '0';

        $this->items_model->_table_name = 'tbl_client';
        $this->items_model->_primary_key = "client_id";
        $return_id = $this->items_model->save($data);
        // update to tbl_leads
        $u_data['converted_client_id'] = $return_id;
        $this->items_model->_table_name = 'tbl_leads';
        $this->items_model->_primary_key = "leads_id";
        $this->items_model->save($u_data, $leads_id);
        $action = ('activity_convert_to_client');
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'leads',
            'module_field_id' => $return_id,
            'activity' => $action,
            'icon' => 'fa-rocket',
            'link' => 'admin/leads/leads_details/' . $leads_id,
            'value1' => $data['name']
        );
        $this->items_model->_table_name = 'tbl_activities';
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);
        // messages for user
        $type = "success";
        $message = lang('convert_to_client_suucess');
        set_message($type, $message);

        $leads_info = $this->items_model->check_by(array('leads_id' => $leads_id), 'tbl_leads');
        $client_info = $this->items_model->check_by(array('client_id' => $return_id), 'tbl_client');
        $notifiedUsers = array();
        if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
            $permissionUsers = json_decode($leads_info->permission);
            foreach ($permissionUsers as $user => $v_permission) {
                array_push($notifiedUsers, $user);
            }
        } else {
            $notifiedUsers = $this->items_model->allowad_user_id('55');
        }
        if (!empty($notifiedUsers)) {
            foreach ($notifiedUsers as $users) {
                if ($users != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $users,
                        'from_user_id' => true,
                        'description' => 'not_lead_converted_to_client',
                        'link' => 'admin/leads/leads_details/' . $leads_info->leads_id,
                        'value' => lang('lead') . ' ' . $leads_info->lead_name . ' ' . lang('client') . ' ' . $client_info->name,
                    ));
                }
            }
            show_notification($notifiedUsers);
        }

        redirect('admin/client/client_details/' . $return_id);
    }

    public
    function update_users($id)
    {
        // get all assign_user
        $can_edit = $this->items_model->can_action('tbl_leads', 'edit', array('leads_id' => $id));
        if (!empty($can_edit)) {
            // get permission user by menu id
            $data['assign_user'] = $this->items_model->allowad_user('55');

            $data['leads_info'] = $this->items_model->check_by(array('leads_id' => $id), 'tbl_leads');
            $data['modal_subview'] = $this->load->view('admin/leads/_modal_users', $data, FALSE);
            $this->load->view('admin/_layout_modal', $data);
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public
    function update_member($id)
    {
        $can_edit = $this->items_model->can_action('tbl_leads', 'edit', array('leads_id' => $id));
        if (!empty($can_edit)) {
            $leads_info = $this->items_model->check_by(array('leads_id' => $id), 'tbl_leads');

            $permission = $this->input->post('permission', true);
            if (!empty($permission)) {

                if ($permission == 'everyone') {
                    $assigned = 'all';
                } else {
                    $assigned_to = $this->items_model->array_from_post(array('assigned_to'));
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
            $this->items_model->_table_name = "tbl_leads"; // table name
            $this->items_model->_primary_key = "leads_id"; // $id
            $this->items_model->save($data, $id);

            $msg = lang('update_leads');
            $activity = 'activity_update_leads';

// save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'leads',
                'module_field_id' => $id,
                'activity' => $activity,
                'icon' => 'fa-rocket',
                'link' => 'admin/leads/leads_details/' . $id,
                'value1' => $leads_info->lead_name,
            );
// Update into tbl_project
            $this->items_model->_table_name = "tbl_activities"; //table name
            $this->items_model->_primary_key = "activities_id";
            $this->items_model->save($activities);

            $notifiedUsers = array();
            if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
                $permissionUsers = json_decode($leads_info->permission);
                foreach ($permissionUsers as $user => $v_permission) {
                    array_push($notifiedUsers, $user);
                }
            } else {
                $notifiedUsers = $this->items_model->allowad_user_id('55');
            }
            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'assign_to_you_the_lead',
                            'link' => 'admin/leads/leads_details/' . $leads_info->leads_id,
                            'value' => lang('lead') . ' ' . $leads_info->lead_name,
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }

            $type = "success";
            $message = $msg;
            set_message($type, $message);
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public
    function change_status($leads_id, $lead_status_id)
    {
        $can_edit = $this->items_model->can_action('tbl_leads', 'edit', array('leads_id' => $leads_id));
        if (!empty($can_edit)) {
            $data['lead_status_id'] = $lead_status_id;
            $this->items_model->_table_name = 'tbl_leads';
            $this->items_model->_primary_key = 'leads_id';
            $this->items_model->save($data, $leads_id);

            $leads_info = $this->items_model->check_by(array('leads_id' => $leads_id), 'tbl_leads');
            $notifiedUsers = array();
            if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
                $permissionUsers = json_decode($leads_info->permission);
                foreach ($permissionUsers as $user => $v_permission) {
                    array_push($notifiedUsers, $user);
                }
            } else {
                $notifiedUsers = $this->items_model->allowad_user_id('55');
            }
            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'not_changed_status',
                            'link' => 'admin/leads/leads_details/' . $leads_info->leads_id,
                            'value' => lang('lead') . ' ' . $leads_info->lead_name,
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }
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

    public
    function saved_call($leads_id, $id = NULL)
    {
        $data = $this->items_model->array_from_post(array('date', 'call_summary', 'client_id', 'user_id'));
        $data['leads_id'] = $leads_id;
        $this->items_model->_table_name = 'tbl_calls';
        $this->items_model->_primary_key = 'calls_id';
        $return_id = $this->items_model->save($data, $id);
        if (!empty($id)) {
            $id = $id;
            $action = 'activity_update_leads_call';
            $msg = lang('update_leads_call');
        } else {
            $id = $return_id;
            $action = 'activity_save_leads_call';
            $msg = lang('save_leads_call');
        }
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'leads',
            'module_field_id' => $leads_id,
            'activity' => $action,
            'icon' => 'fa-rocket',
            'link' => 'admin/leads/leads_details/' . $leads_id . '/2',
            'value1' => $data['call_summary']
        );
        $this->items_model->_table_name = 'tbl_activities';
        $this->items_model->_primary_key = 'activities_id';
        $this->items_model->save($activity);

        $leads_info = $this->items_model->check_by(array('leads_id' => $leads_id), 'tbl_leads');
        $notifiedUsers = array();
        if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
            $permissionUsers = json_decode($leads_info->permission);
            foreach ($permissionUsers as $user => $v_permission) {
                array_push($notifiedUsers, $user);
            }
        } else {
            $notifiedUsers = $this->items_model->allowad_user_id('55');
        }
        if (!empty($notifiedUsers)) {
            foreach ($notifiedUsers as $users) {
                if ($users != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $users,
                        'from_user_id' => true,
                        'description' => 'not_add_call',
                        'link' => 'admin/leads/leads_details/' . $leads_info->leads_id . '/2',
                        'value' => lang('lead') . ' ' . $leads_info->lead_name,
                    ));
                }
            }
            show_notification($notifiedUsers);
        }
        // messages for user
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/leads/leads_details/' . $leads_id . '/' . '2');
    }

    public
    function delete_leads_call($leads_id, $id)
    {
        $calls_info = $this->items_model->check_by(array('calls_id' => $id), 'tbl_calls');
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'leads',
            'module_field_id' => $leads_id,
            'activity' => 'activity_leads_call_deleted',
            'icon' => 'fa-rocket',
            'link' => 'admin/leads/leads_details/' . $leads_id . '/2',
            'value1' => $calls_info->call_summary
        );
        $this->items_model->_table_name = 'tbl_activities';
        $this->items_model->_primary_key = 'activities_id';
        $this->items_model->save($activity);
        $this->items_model->_table_name = 'tbl_calls';
        $this->items_model->_primary_key = 'calls_id';
        $this->items_model->delete($id);
        $type = 'success';
        $message = lang('leads_call_deleted');
        set_message($type, $message);
        redirect('admin/leads/leads_details/' . $leads_id . '/' . '2');
    }

    public
    function delete_leads_mettings($leads_id, $id)
    {
        $mettings_info = $this->items_model->check_by(array('mettings_id' => $id), 'tbl_mettings');

        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'leads',
            'module_field_id' => $leads_id,
            'activity' => 'activity_leads_call_deleted',
            'icon' => 'fa-rocket',
            'link' => 'admin/leads/leads_details/' . $leads_id . '/3',
            'value1' => $mettings_info->meeting_subject
        );
        $this->items_model->_table_name = 'tbl_activities';
        $this->items_model->_primary_key = 'activities_id';
        $this->items_model->save($activity);
        $this->items_model->_table_name = 'tbl_mettings';
        $this->items_model->_primary_key = 'mettings_id';
        $this->items_model->delete($id);
        $type = 'success';
        $message = lang('leads_mettings_deleted');
        set_message($type, $message);
        redirect('admin/leads/leads_details/' . $leads_id . '/' . '3');
    }

    public
    function saved_metting($leads_id, $id = NULL)
    {
        $this->items_model->_table_name = 'tbl_mettings';
        $this->items_model->_primary_key = 'mettings_id';

        $data = $this->items_model->array_from_post(array('meeting_subject', 'user_id', 'location', 'description'));
        $data['start_date'] = strtotime($this->input->post('start_date') . ' ' . date('H:i', strtotime($this->input->post('start_time'))));
        $data['end_date'] = strtotime($this->input->post('end_date') . ' ' . date('H:i', strtotime($this->input->post('end_time'))));
        $data['leads_id'] = $leads_id;
        $user_id = serialize($this->items_model->array_from_post(array('attendees')));
        if (!empty($user_id)) {
            $data['attendees'] = $user_id;
        } else {
            $data['attendees'] = '-';
        }
        $return_id = $this->items_model->save($data, $id);

        if (!empty($id)) {
            $id = $id;
            $action = 'activity_update_leads_metting';
            $msg = lang('update_leads_metting');
        } else {
            $id = $return_id;
            $action = 'activity_save_leads_metting';
            $msg = lang('save_leads_metting');
        }
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'leads',
            'module_field_id' => $leads_id,
            'activity' => $action,
            'icon' => 'fa-rocket',
            'link' => 'admin/leads/leads_details/' . $leads_id . '/3',
            'value1' => $data['meeting_subject']
        );

        $this->items_model->_table_name = 'tbl_activities';
        $this->items_model->_primary_key = 'activities_id';
        $this->items_model->save($activity);

        $leads_info = $this->items_model->check_by(array('leads_id' => $leads_id), 'tbl_leads');
        $notifiedUsers = array();
        if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
            $permissionUsers = json_decode($leads_info->permission);
            foreach ($permissionUsers as $user => $v_permission) {
                array_push($notifiedUsers, $user);
            }
        } else {
            $notifiedUsers = $this->items_model->allowad_user_id('55');
        }
        if (!empty($notifiedUsers)) {
            foreach ($notifiedUsers as $users) {
                if ($users != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $users,
                        'from_user_id' => true,
                        'description' => 'not_add_meetings',
                        'link' => 'admin/leads/leads_details/' . $leads_info->leads_id . '/3',
                        'value' => lang('lead') . ' ' . $leads_info->lead_name,
                    ));
                }
            }
            show_notification($notifiedUsers);
        }
        // messages for user
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/leads/leads_details/' . $leads_id . '/' . '3');
    }

    public
    function save_comments()
    {

        $data['leads_id'] = $this->input->post('leads_id', TRUE);
        $data['comment'] = $this->input->post('comment', TRUE);
        if (!empty($_FILES['comments_attachment']['name']['0'])) {
            $old_path_info = $this->input->post('upload_path');
            if (!empty($old_path_info)) {
                foreach ($old_path_info as $old_path) {
                    unlink($old_path);
                }
            }
            $mul_val = $this->items_model->multi_uploadAllType('comments_attachment');
            $data['comments_attachment'] = json_encode($mul_val);
        }
        $data['user_id'] = $this->session->userdata('user_id');

        //save data into table.
        $this->items_model->_table_name = "tbl_task_comment"; // table name
        $this->items_model->_primary_key = "task_comment_id"; // $id
        $this->items_model->save($data);

        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'leads',
            'module_field_id' => $data['leads_id'],
            'activity' => 'activity_new_leads_comment',
            'icon' => 'fa-rocket',
            'link' => 'admin/leads/leads_details/' . $data['leads_id'] . '/4',
            'value1' => $data['comment'],
        );
        // Update into tbl_project
        $this->items_model->_table_name = "tbl_activities"; //table name
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);

        $leads_info = $this->items_model->check_by(array('leads_id' => $data['leads_id']), 'tbl_leads');
        $notifiedUsers = array();
        if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
            $permissionUsers = json_decode($leads_info->permission);
            foreach ($permissionUsers as $user => $v_permission) {
                array_push($notifiedUsers, $user);
            }
        } else {
            $notifiedUsers = $this->items_model->allowad_user_id('55');
        }
        if (!empty($notifiedUsers)) {
            foreach ($notifiedUsers as $users) {
                if ($users != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $users,
                        'from_user_id' => true,
                        'description' => 'not_new_comment',
                        'link' => 'admin/leads/leads_details/' . $leads_info->leads_id . '/4',
                        'value' => lang('lead') . ' ' . $leads_info->lead_name,
                    ));
                }
            }
            show_notification($notifiedUsers);
        }

        $type = "success";
        $message = lang('leads_comment_save');
        set_message($type, $message);
        redirect('admin/leads/leads_details/' . $data['leads_id'] . '/' . '4');
    }

    public function save_comments_reply($task_comment_id)
    {
        $data['leads_id'] = $this->input->post('leads_id', TRUE);
        $data['comment'] = $this->input->post('reply_comments', TRUE);
        $data['user_id'] = $this->session->userdata('user_id');
        $data['comments_reply_id'] = $task_comment_id;
        //save data into table.
        $this->items_model->_table_name = "tbl_task_comment"; // table name
        $this->items_model->_primary_key = "task_comment_id"; // $id
        $comment_id = $this->items_model->save($data);
        $comments_info = $this->items_model->check_by(array('task_comment_id' => $task_comment_id), 'tbl_task_comment');
        $user = $this->items_model->check_by(array('user_id' => $comments_info->user_id), 'tbl_users');
        if ($user->role_id == 2) {
            $url = 'client/';
        } else {
            $url = 'admin/';
        }
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'leads',
            'module_field_id' => $data['leads_id'],
            'activity' => 'activity_new_comment_reply',
            'icon' => 'fa-rocket',
            'link' => $url . 'leads/leads_details/' . $data['leads_id'] . '/4',
            'value1' => $this->db->where('task_comment_id', $task_comment_id)->get('tbl_task_comment')->row()->comment,
            'value2' => $data['comment'],
        );
        // Update into tbl_project
        $this->items_model->_table_name = "tbl_activities"; //table name
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);

        $leads_info = $this->items_model->check_by(array('leads_id' => $data['leads_id']), 'tbl_leads');

        $notifiedUsers = array($comments_info->user_id);
        if (!empty($notifiedUsers)) {
            foreach ($notifiedUsers as $users) {
                if ($users != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $users,
                        'from_user_id' => true,
                        'description' => 'not_new_comment',
                        'link' => $url . 'leads/leads_details/' . $leads_info->leads_id . '/4',
                        'value' => lang('lead') . ' ' . $leads_info->lead_name,
                    ));
                }
            }
            show_notification($notifiedUsers);
        }

        $type = "success";
        $message = lang('leads_comment_save');
        set_message($type, $message);
        redirect('admin/leads/leads_details/' . $data['leads_id'] . '/' . '4');
    }

    public
    function delete_comments($leads_id = null, $task_comment_id = null)
    {
        //save data into table.
        $this->items_model->_table_name = "tbl_task_comment"; // table name
        $this->items_model->_primary_key = "task_comment_id"; // $id
        $this->items_model->delete($task_comment_id);
        $type = "success";
        $message = lang('task_comment_deleted');
        set_message($type, $message);
        redirect('admin/leads/leads_details/' . $leads_id . '/' . '4');
    }

    public
    function save_attachment($task_attachment_id = NULL)
    {
        $data = $this->items_model->array_from_post(array('title', 'description', 'leads_id'));
        $data['user_id'] = $this->session->userdata('user_id');

        // save and update into tbl_files
        $this->items_model->_table_name = "tbl_task_attachment"; //table name
        $this->items_model->_primary_key = "task_attachment_id";
        if (!empty($task_attachment_id)) {
            $id = $task_attachment_id;
            $this->items_model->save($data, $id);
            $msg = lang('leads_file_updated');
        } else {
            $id = $this->items_model->save($data);
            $msg = lang('leads_file_added');
        }

        if (!empty($_FILES['task_files']['name']['0'])) {
            $old_path_info = $this->input->post('uploaded_path');
            if (!empty($old_path_info)) {
                foreach ($old_path_info as $old_path) {
                    unlink($old_path);
                }
            }
            $mul_val = $this->items_model->multi_uploadAllType('task_files');

            foreach ($mul_val as $val) {
                $val == TRUE || redirect('admin/leads/leads_details/' . $data['leads_id'] . '/' . '5');
                $fdata['files'] = $val['path'];
                $fdata['file_name'] = $val['fileName'];
                $fdata['uploaded_path'] = $val['fullPath'];
                $fdata['size'] = $val['size'];
                $fdata['ext'] = $val['ext'];
                $fdata['is_image'] = $val['is_image'];
                $fdata['image_width'] = $val['image_width'];
                $fdata['image_height'] = $val['image_height'];
                $fdata['task_attachment_id'] = $id;
                $this->items_model->_table_name = "tbl_task_uploaded_files"; // table name
                $this->items_model->_primary_key = "uploaded_files_id"; // $id
                $this->items_model->save($fdata);
            }
        }
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'leads',
            'module_field_id' => $data['leads_id'],
            'activity' => 'activity_new_leads_attachment',
            'icon' => 'fa-rocket',
            'link' => 'admin/leads/leads_details/' . $data['leads_id'] . '/5',
            'value1' => $data['title'],
        );
        // Update into tbl_project
        $this->items_model->_table_name = "tbl_activities"; //table name
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);

        $leads_info = $this->items_model->check_by(array('leads_id' => $data['leads_id']), 'tbl_leads');
        $notifiedUsers = array();
        if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
            $permissionUsers = json_decode($leads_info->permission);
            foreach ($permissionUsers as $user => $v_permission) {
                array_push($notifiedUsers, $user);
            }
        } else {
            $notifiedUsers = $this->items_model->allowad_user_id('55');
        }
        if (!empty($notifiedUsers)) {
            foreach ($notifiedUsers as $users) {
                if ($users != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $users,
                        'from_user_id' => true,
                        'description' => 'not_uploaded_attachment',
                        'link' => 'admin/leads/leads_details/' . $leads_info->leads_id . '/5',
                        'value' => lang('lead') . ' ' . $leads_info->lead_name,
                    ));
                }
            }
            show_notification($notifiedUsers);
        }
        // messages for user
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/leads/leads_details/' . $data['leads_id'] . '/' . '5');
    }

    public
    function delete_files($leads_id, $task_attachment_id)
    {
        $file_info = $this->items_model->check_by(array('task_attachment_id' => $task_attachment_id), 'tbl_task_attachment');
        // save into activities
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'leads',
            'module_field_id' => $leads_id,
            'activity' => 'activity_leads_attachfile_deleted',
            'icon' => 'fa-rocket',
            'link' => 'admin/leads/leads_details/' . $leads_id . '/4',
            'value1' => $file_info->title,
        );
        // Update into tbl_project
        $this->items_model->_table_name = "tbl_activities"; //table name
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);

        //save data into table.
        $this->items_model->_table_name = "tbl_task_attachment"; // table name
        $this->items_model->delete_multiple(array('task_attachment_id' => $task_attachment_id));

        $type = "success";
        $message = lang('leads_attachfile_deleted');
        set_message($type, $message);
        redirect('admin/leads/leads_details/' . $leads_id . '/' . '5');
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
            $uploaded_files_info = $this->items_model->check_by(array('uploaded_files_id' => $uploaded_files_id), 'tbl_task_uploaded_files');
            if ($uploaded_files_info->uploaded_path) {
                $data = file_get_contents($uploaded_files_info->uploaded_path); // Read the file's contents
                force_download($uploaded_files_info->file_name, $data);
            } else {
                $type = "error";
                $message = lang('operation_failed');
                set_message($type, $message);
                redirect('admin/leads/leads_details/' . $task_id . '/' . '5');
            }
        }
    }

    public
    function delete_leads($id)
    {
        $can_delete = $this->items_model->can_action('tbl_leads', 'delete', array('leads_id' => $id));
        if (!empty($can_delete)) {
            $leads_info = $this->items_model->check_by(array('leads_id' => $id), 'tbl_leads');
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'leads',
                'module_field_id' => $id,
                'activity' => 'activity_leads_deleted',
                'icon' => 'fa-rocket',
                'value1' => $leads_info->lead_name
            );
            $this->items_model->_table_name = 'tbl_activities';
            $this->items_model->_primary_key = 'activities_id';
            $this->items_model->save($activity);

            //delete data into table.
            $this->items_model->_table_name = "tbl_calls"; // table name
            $this->items_model->delete_multiple(array('leads_id' => $id));

            //delete data into table.
            $this->items_model->_table_name = "tbl_mettings"; // table name
            $this->items_model->delete_multiple(array('leads_id' => $id));

            //delete data into table.
            $this->items_model->_table_name = "tbl_task_comment"; // table name
            $this->items_model->delete_multiple(array('leads_id' => $id));

            $this->items_model->_table_name = "tbl_task_attachment"; //table name
            $this->items_model->_order_by = "leads_id";
            $files_info = $this->items_model->get_by(array('leads_id' => $id), FALSE);

            foreach ($files_info as $v_files) {
                //save data into table.
                $this->items_model->_table_name = "tbl_task_uploaded_files"; // table name
                $this->items_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
            }
            //save data into table.
            $this->items_model->_table_name = "tbl_task_attachment"; // table name
            $this->items_model->delete_multiple(array('leads_id' => $id));

            $this->items_model->_table_name = 'tbl_pinaction';
            $this->items_model->delete_multiple(array('module_name' => 'leads', 'module_id' => $id));

            $this->items_model->_table_name = 'tbl_leads';
            $this->items_model->_primary_key = 'leads_id';
            $this->items_model->delete($id);

            $type = 'success';
            $message = lang('leads_deleted');
            set_message($type, $message);
        } else {
            set_message('error', lang('there_in_no_value'));
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function change_leads_status($lead_status_id)
    {
        $leads_id = $this->input->post('leads_id', true);
        foreach ($leads_id as $key => $id) {
            $data['index_no'] = $key + 1;
            //save data into table.
            $data['lead_status_id'] = $lead_status_id;
            $this->items_model->_table_name = 'tbl_leads';
            $this->items_model->_primary_key = 'leads_id';
            $this->items_model->save($data, $id);
        }
        $type = "success";
        $message = lang('update_leads');
        echo json_encode(array("status" => $type, "message" => $message));

    }

}
