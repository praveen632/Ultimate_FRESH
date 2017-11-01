<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Proposals extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('proposal_model');

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

    public function index($action = NULL, $id = NULL, $item_id = NULL)
    {

        $data['page'] = lang('sales');
        $data['sub_active'] = lang('proposals');
        if (!empty($item_id)) {
            $can_edit = $this->proposal_model->can_action('tbl_proposals', 'edit', array('proposals_id' => $id));
            if (!empty($can_edit)) {
                $data['item_info'] = $this->proposal_model->check_by(array('proposals_items_id' => $item_id), 'tbl_proposals_items');
            }
        }
        if ($action == 'edit_proposals') {
            $data['active'] = 2;
            $can_edit = $this->proposal_model->can_action('tbl_proposals', 'edit', array('proposals_id' => $id));
            if (!empty($can_edit)) {
                $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
            }
        } else if ($action == 'client' || $action == 'leads') {
            $data['module_id'] = $id;
            $data['module'] = $action;
            $data['active'] = 2;
        } else {
            $data['active'] = 1;
        }
        // get all client
        $this->proposal_model->_table_name = 'tbl_client';
        $this->proposal_model->_order_by = 'client_id';
        $data['all_client'] = $this->proposal_model->get();
        // get permission user
        $data['permission_user'] = $this->proposal_model->all_permission_user('140');
        $type = $this->uri->segment(5);
        if (empty($type)) {
            $type = '_' . date('Y');
        }

        if (!empty($type) && !is_numeric($type)) {
            $filterBy = $type;
        } else {
            $filterBy = null;
        }
        // get all proposals
        $data['all_proposals_info'] = $this->proposal_model->get_proposals($filterBy);

        if ($action == 'proposals_details') {
            $data['title'] = lang('proposals') . ' ' . lang('details'); //Page title
            $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
            $subview = 'proposals_details';
        } elseif ($action == 'proposals_history') {
            $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
            $data['title'] = "proposals History"; //Page title
            $subview = 'proposals_history';
        } elseif ($action == 'email_proposals') {
            $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
            $data['title'] = "Email proposals"; //Page title
            $subview = 'email_proposals';
            $data['editor'] = $this->data;
        } elseif ($action == 'pdf_proposals') {
            $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
            $data['title'] = "proposals PDF"; //Page title
            $this->load->helper('dompdf');
            $viewfile = $this->load->view('admin/proposals/proposals_pdf', $data, TRUE);
            pdf_create($viewfile, 'proposals  # ' . $data['proposals_info']->reference_no);
        } else {
            $data['title'] = lang('proposals'); //Page title
            $subview = 'proposals';
        }
        $data['subview'] = $this->load->view('admin/proposals/' . $subview, $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function pdf_proposals($id)
    {
        $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
        $data['title'] = "proposals PDF"; //Page title
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/proposals/proposals_pdf', $data, TRUE);
        pdf_create($viewfile, 'proposals  # ' . $data['proposals_info']->reference_no);
    }

    public function save_proposals($id = NULL)
    {
        $created = can_action('140', 'created');
        $edited = can_action('140', 'edited');
        if (!empty($created) || !empty($edited)) {
            $data = $this->proposal_model->array_from_post(array('reference_no', 'discount_type', 'discount_percent', 'user_id', 'adjustment', 'discount_total', 'show_quantity_as'));
            $data['proposal_date'] = date('Y-m-d', strtotime($this->input->post('proposal_date', TRUE)));
            if (empty($data['proposal_date'])) {
                $data['proposal_date'] = date('Y-m-d');
            }
            $data['proposal_year'] = date('Y', strtotime($this->input->post('proposal_date', TRUE)));
            $data['proposal_month'] = date('Y-m', strtotime($this->input->post('proposal_date', TRUE)));
            $data['due_date'] = date('Y-m-d', strtotime($this->input->post('due_date', TRUE)));
            $data['notes'] = $this->input->post('notes', TRUE);
            $tax['tax_name'] = $this->input->post('total_tax_name', TRUE);
            $tax['total_tax'] = $this->input->post('total_tax', TRUE);
            $data['total_tax'] = json_encode($tax);
            $i_tax = 0;
            if (!empty($tax['total_tax'])) {
                foreach ($tax['total_tax'] as $v_tax) {
                    $i_tax += $v_tax;
                }
            }
            $data['tax'] = $i_tax;
            $save_as_draft = $this->input->post('status', TRUE);
            if (!empty($save_as_draft)) {
                $data['status'] = $save_as_draft;
            } else {
                $data['status'] = 'draft';
            }
            $data['module'] = $this->input->post('module', true);
            if (!empty($data['module']) && $data['module'] == 'leads') {
                $data['module_id'] = $this->input->post('leads_id', true);
                $curren = $this->input->post('currency', true);
            } else {
                $data['module_id'] = $this->input->post('client_id', true);
                $currency = $this->proposal_model->client_currency_sambol($data['module_id']);
                if (!empty($currency->code)) {
                    $curren = $currency->code;
                } else {
                    $curren = config_item('default_currency');
                }

            }
            $data['currency'] = $curren;


            $permission = $this->input->post('permission', true);
            if (!empty($permission)) {
                if ($permission == 'everyone') {
                    $assigned = 'all';
                } else {
                    $assigned_to = $this->proposal_model->array_from_post(array('assigned_to'));
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

            // get all client
            $this->proposal_model->_table_name = 'tbl_proposals';
            $this->proposal_model->_primary_key = 'proposals_id';
            if (!empty($id)) {
                $proposals_id = $id;
                $this->proposal_model->save($data, $id);
                $action = ('activity_proposals_updated');
                $msg = lang('proposals_updated');
                $description = 'not_proposal_updated';
            } else {
                $proposals_id = $this->proposal_model->save($data);
                $action = ('activity_proposals_created');
                $msg = lang('proposals_created');
                $description = 'not_proposal_created';
            }
            save_custom_field(11, $proposals_id);


            $removed_items = $this->input->post('removed_items', TRUE);
            if (!empty($removed_items)) {
                foreach ($removed_items as $r_id) {
                    if ($r_id != 'undefined') {
                        $this->db->where('proposals_items_id', $r_id);
                        $this->db->delete('tbl_proposals_items');
                    }
                }
            }

            $itemsid = $this->input->post('proposals_items_id', TRUE);
            $items_data = $this->input->post('items', true);

            if (!empty($items_data)) {
                $index = 0;
                foreach ($items_data as $items) {
                    $items['proposals_id'] = $proposals_id;
                    $tax = 0;
                    if (!empty($items['taxname'])) {
                        foreach ($items['taxname'] as $tax_name) {
                            $tax_rate = explode("|", $tax_name);
                            $tax += $tax_rate[1];

                        }
                        $items['item_tax_name'] = $items['taxname'];
                        unset($items['taxname']);
                        $items['item_tax_name'] = json_encode($items['item_tax_name']);
                    }
                    $price = $items['quantity'] * $items['unit_cost'];
                    $items['item_tax_total'] = ($price / 100 * $tax);
                    $items['total_cost'] = $price;
                    // get all client
                    $this->proposal_model->_table_name = 'tbl_proposals_items';
                    $this->proposal_model->_primary_key = 'proposals_items_id';
                    if (!empty($itemsid[$index])) {
                        $items_id = $itemsid[$index];
                        $this->proposal_model->save($items, $items_id);
                    } else {
                        $items_id = $this->proposal_model->save($items);
                    }
                    $index++;
                }
            }
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'proposals',
                'module_field_id' => $proposals_id,
                'activity' => $action,
                'icon' => 'fa-shopping-cart',
                'link' => 'admin/proposals/index/proposals_details/' . $proposals_id,
                'value1' => $data['reference_no']
            );
            $this->proposal_model->_table_name = 'tbl_activities';
            $this->proposal_model->_primary_key = 'activities_id';
            $this->proposal_model->save($activity);

            // send notification to client
            if (!empty($data['client_id'])) {
                $client_info = $this->proposal_model->check_by(array('client_id' => $data['client_id']), 'tbl_client');
                if (!empty($client_info->primary_contact)) {
                    $notifyUser = array($client_info->primary_contact);
                } else {
                    $user_info = $this->proposal_model->check_by(array('company' => $data['client_id']), 'tbl_account_details');
                    if (!empty($user_info)) {
                        $notifyUser = array($user_info->user_id);
                    }
                }
            }
            if (!empty($notifyUser)) {
                foreach ($notifyUser as $v_user) {
                    if ($v_user != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $v_user,
                            'icon' => 'shopping-cart',
                            'description' => $description,
                            'link' => 'client/proposals/index/proposals_details/' . $proposals_id,
                            'value' => $data['reference_no'],
                        ));
                    }
                }
                show_notification($notifyUser);
            }

            // messages for user
            $type = "success";
            $message = $msg;
            set_message($type, $message);
        }
        redirect('admin/proposals/index/proposals_details/' . $proposals_id);

    }

    public function insert_items($proposals_id)
    {
        $edited = can_action('140', 'edited');
        $can_edit = $this->proposal_model->can_action('tbl_proposals', 'edit', array('proposals_id' => $proposals_id));
        if (!empty($can_edit) && !empty($edited)) {
            $data['proposals_id'] = $proposals_id;
            $data['modal_subview'] = $this->load->view('admin/proposals/_modal_insert_items', $data, FALSE);
            $this->load->view('admin/_layout_modal', $data);
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function add_insert_items($proposals_id)
    {
        $can_edit = $this->proposal_model->can_action('tbl_proposals', 'edit', array('proposals_id' => $proposals_id));
        $edited = can_action('140', 'edited');
        if (!empty($can_edit) && !empty($edited)) {
            $saved_items_id = $this->input->post('saved_items_id', TRUE);
            if (!empty($saved_items_id)) {
                foreach ($saved_items_id as $v_items_id) {
                    $items_info = $this->proposal_model->check_by(array('saved_items_id' => $v_items_id), 'tbl_saved_items');
                    $tax_info = json_decode($items_info->tax_rates_id);
                    $tax_name = array();
                    if (!empty($tax_info)) {
                        foreach ($tax_info as $v_tax) {
                            $all_tax = $this->db->where('tax_rates_id', $v_tax)->get('tbl_tax_rates')->row();
                            $tax_name[] = $all_tax->tax_rate_name . '|' . $all_tax->tax_rate_percent;

                        }
                    }
                    if (!empty($tax_name)) {
                        $tax_name = $tax_name;
                    } else {
                        $tax_name = array();
                    }

                    $data['quantity'] = $items_info->quantity;
                    $data['proposals_id'] = $proposals_id;
                    $data['item_name'] = $items_info->item_name;
                    $data['item_desc'] = $items_info->item_desc;
                    $data['unit_cost'] = $items_info->unit_cost;
                    $data['item_tax_rate'] = '0.00';
                    $data['item_tax_name'] = json_encode($tax_name);
                    $data['item_tax_total'] = $items_info->item_tax_total;
                    $data['total_cost'] = $items_info->total_cost;

                    $this->proposal_model->_table_name = 'tbl_proposals_items';
                    $this->proposal_model->_primary_key = 'proposals_items_id';
                    $items_id = $this->proposal_model->save($data);
                    $action = 'activity_proposal_items_added';
                    $msg = lang('proposals_item_save');
                    $activity = array(
                        'user' => $this->session->userdata('user_id'),
                        'module' => 'proposals',
                        'module_field_id' => $items_id,
                        'activity' => $action,
                        'icon' => 'fa-shopping-cart',
                        'link' => 'admin/proposals/index/proposals_details/' . $proposals_id,
                        'value1' => $items_info->item_name
                    );
                    $this->proposal_model->_table_name = 'tbl_activities';
                    $this->proposal_model->_primary_key = 'activities_id';
                    $this->proposal_model->save($activity);
                }
                $type = "success";
            } else {
                $type = "error";
                $msg = 'Please Select a items';
            }
            $message = $msg;
            set_message($type, $message);
            redirect('admin/proposals/index/proposals_details/' . $proposals_id);
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function add_item($id = NULL)
    {
        $data = $this->proposal_model->array_from_post(array('proposals_id', 'item_order'));
        $can_edit = $this->proposal_model->can_action('tbl_proposals', 'edit', array('proposals_id' => $data['proposals_id']));
        $edited = can_action('140', 'edited');
        if (!empty($can_edit) && !empty($edited)) {
            $quantity = $this->input->post('quantity', TRUE);
            $array_data = $this->proposal_model->array_from_post(array('item_name', 'item_desc', 'item_tax_rate', 'unit_cost'));
            if (!empty($quantity)) {
                foreach ($quantity as $key => $value) {
                    $data['quantity'] = $value;
                    $data['item_name'] = $array_data['item_name'][$key];
                    $data['item_desc'] = $array_data['item_desc'][$key];
                    $data['unit_cost'] = $array_data['unit_cost'][$key];
                    $data['item_tax_rate'] = $array_data['item_tax_rate'][$key];
                    $sub_total = $data['unit_cost'] * $data['quantity'];

                    $data['item_tax_total'] = ($data['item_tax_rate'] / 100) * $sub_total;
                    $data['total_cost'] = $sub_total + $data['item_tax_total'];

                    // get all client
                    $this->proposal_model->_table_name = 'tbl_proposals_items';
                    $this->proposal_model->_primary_key = 'proposals_items_id';
                    if (!empty($id)) {
                        $proposals_items_id = $id;
                        $this->proposal_model->save($data, $id);
                        $action = ('activity_proposals_items_updated');
                    } else {
                        $proposals_items_id = $this->proposal_model->save($data);
                        $action = 'activity_proposals_items_added';
                    }
                    $activity = array(
                        'user' => $this->session->userdata('user_id'),
                        'module' => 'proposals',
                        'module_field_id' => $proposals_items_id,
                        'activity' => $action,
                        'icon' => 'fa-shopping-cart',
                        'value1' => $data['item_name']
                    );
                    $this->proposal_model->_table_name = 'tbl_activities';
                    $this->proposal_model->_primary_key = 'activities_id';
                    $this->proposal_model->save($activity);
                }
            }
            // messages for user
            $type = "success";
            $message = lang('proposals_item_save');
            set_message($type, $message);
            redirect('admin/proposals/index/proposals_details/' . $data['proposals_id']);
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public
    function clone_proposal($proposals_id)
    {
        $edited = can_action('140', 'edited');
        $can_edit = $this->proposal_model->can_action('tbl_proposals', 'edit', array('proposals_id' => $proposals_id));
        if (!empty($can_edit) && !empty($edited)) {
            $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $proposals_id), 'tbl_proposals');
            // get all client
            $this->proposal_model->_table_name = 'tbl_client';
            $this->proposal_model->_order_by = 'client_id';
            $data['all_client'] = $this->proposal_model->get();

            $data['modal_subview'] = $this->load->view('admin/proposals/_modal_clone_proposals', $data, FALSE);
            $this->load->view('admin/_layout_modal', $data);
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public
    function cloned_proposals($id)
    {
        $edited = can_action('140', 'edited');
        $can_edit = $this->proposal_model->can_action('tbl_proposals', 'edit', array('proposals_id' => $id));
        if (!empty($can_edit) && !empty($edited)) {
            if (config_item('increment_proposals_number') == 'FALSE') {
                $this->load->helper('string');
                $reference_no = config_item('proposal_prefix') . ' ' . random_string('nozero', 6);
            } else {
                $reference_no = config_item('proposal_prefix') . ' ' . $this->proposal_model->generate_proposal_number();
            }

            $invoice_info = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
            $module = $this->input->post('module', true);
            if (empty($module)) {
                $module = $invoice_info->module;
                $module_id = $invoice_info->module_id;
                $currency = $invoice_info->currency;
            } else {
                if ($module == 'leads') {
                    $module_id = $this->input->post('leads_id', true);
                    $currency = $this->input->post('currency', true);
                } else {
                    $module_id = $this->input->post('client_id', true);
                    $currenc = $this->proposal_model->client_currency_sambol($module_id);
                    $currency = $currenc->code;
                }
            }
            // save into invoice table
            $new_invoice = array(
                'reference_no' => $reference_no,
                'subject' => $invoice_info->subject,
                'module' => $module,
                'module_id' => $module_id,
                'proposal_date' => $invoice_info->proposal_date,
                'proposal_month' => $invoice_info->proposal_month,
                'proposal_year' => $invoice_info->proposal_year,
                'due_date' => $invoice_info->due_date,
                'notes' => $invoice_info->notes,
                'total_tax' => $invoice_info->total_tax,
                'tax' => $invoice_info->tax,
                'discount_type' => $invoice_info->discount_type,
                'discount_percent' => $invoice_info->discount_percent,
                'user_id' => $invoice_info->user_id,
                'adjustment' => $invoice_info->adjustment,
                'discount_total' => $invoice_info->discount_total,
                'show_quantity_as' => $invoice_info->show_quantity_as,
                'currency' => $currency,
                'status' => $invoice_info->status,
                'date_sent' => $invoice_info->date_sent,
                'emailed' => $invoice_info->emailed,
                'show_client' => $invoice_info->show_client,
                'convert' => $invoice_info->convert,
                'convert_module' => $invoice_info->convert_module,
                'convert_module_id' => $invoice_info->convert_module_id,
                'converted_date' => $invoice_info->converted_date,
                'permission' => $invoice_info->permission,
            );
            $this->proposal_model->_table_name = "tbl_proposals"; //table name
            $this->proposal_model->_primary_key = "proposals_id";
            $new_invoice_id = $this->proposal_model->save($new_invoice);

            $invoice_items = $this->db->where('proposals_id', $id)->get('tbl_proposals_items')->result();

            if (!empty($invoice_items)) {
                foreach ($invoice_items as $new_item) {
                    $items = array(
                        'proposals_id' => $new_invoice_id,
                        'item_name' => $new_item->item_name,
                        'item_desc' => $new_item->item_desc,
                        'unit_cost' => $new_item->unit_cost,
                        'quantity' => $new_item->quantity,
                        'item_tax_rate' => $new_item->item_tax_rate,
                        'item_tax_name' => $new_item->item_tax_name,
                        'item_tax_total' => $new_item->item_tax_total,
                        'total_cost' => $new_item->total_cost,
                        'unit' => $new_item->unit,
                        'order' => $new_item->order,
                    );
                    $this->proposal_model->_table_name = "tbl_proposals_items"; //table name
                    $this->proposal_model->_primary_key = "proposals_items_id";
                    $this->proposal_model->save($items);
                }
            }
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'proposals',
                'module_field_id' => $new_invoice_id,
                'activity' => ('activity_clone_proposal'),
                'icon' => 'fa-shopping-cart',
                'link' => 'admin/proposals/index/proposals_details/' . $new_invoice_id,
                'value1' => ' from ' . $invoice_info->reference_no . ' to ' . $reference_no,
            );
            // Update into tbl_project
            $this->proposal_model->_table_name = "tbl_activities"; //table name
            $this->proposal_model->_primary_key = "activities_id";
            $this->proposal_model->save($activities);

            // messages for user
            $type = "success";
            $message = lang('proposals_created');
            set_message($type, $message);
            redirect('admin/proposals/index/proposals_details/' . $new_invoice_id);
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function change_status($action, $id)
    {
        $can_edit = $this->proposal_model->can_action('tbl_proposals', 'edit', array('proposals_id' => $id));
        $edited = can_action('140', 'edited');
        if (!empty($can_edit) && !empty($edited)) {
            $where = array('proposals_id' => $id);
            if ($action == 'hide') {
                $data = array('show_client' => 'No');
            } elseif ($action == 'show') {
                $data = array('show_client' => 'Yes');
            } elseif ($action == 'sent') {
                $data = array('emailed' => 'Yes', 'date_sent' => date("Y-m-d H:i:s", time()), 'status' => 'sent');
            } elseif (!empty($action)) {
                $data = array('status' => $action);
            } else {
                $data = array('show_client' => 'Yes');
            }
            $this->proposal_model->set_action($where, $data, 'tbl_proposals');
            // messages for user
            $type = "success";
            $message = lang('proposals_status', $action);
            set_message($type, $message);
            redirect('admin/proposals/index/proposals_details/' . $id);
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public
    function delete($action, $proposals_id, $item_id = NULL)
    {
        $can_delete = $this->proposal_model->can_action('tbl_proposals', 'delete', array('proposals_id' => $proposals_id));
        $deleted = can_action('140', 'deleted');
        if (!empty($can_delete) && !empty($deleted)) {
            if ($action == 'delete_item') {
                $this->proposal_model->_table_name = 'tbl_proposals_items';
                $this->proposal_model->_primary_key = 'proposals_items_id';
                $this->proposal_model->delete($item_id);
            } elseif ($action == 'delete_proposals') {

                $this->proposal_model->_table_name = 'tbl_proposals_items';
                $this->proposal_model->delete_multiple(array('proposals_id' => $proposals_id));

                $this->proposal_model->_table_name = 'tbl_proposals';
                $this->proposal_model->_primary_key = 'proposals_id';
                $this->proposal_model->delete($proposals_id);
            }
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'proposals',
                'module_field_id' => $proposals_id,
                'activity' => ('activity_' . $action),
                'icon' => 'fa-shopping-cart',
                'link' => 'admin/proposals/index/proposals_details/' . $proposals_id,
                'value1' => $action
            );

            $this->proposal_model->_table_name = 'tbl_activities';
            $this->proposal_model->_primary_key = 'activities_id';
            $this->proposal_model->save($activity);
            $type = 'success';

            if ($action == 'delete_item') {
                $text = lang('proposals_item_deleted');
                set_message($type, $text);
                redirect('admin/proposals/index/proposals_details/' . $proposals_id);
            } else {
                $text = lang('proposals_deleted');
                set_message($type, $text);
                redirect('admin/proposals');
            }
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function send_proposals_email($proposals_id, $row = null)
    {
        if (!empty($row)) {
            $proposals_info = $this->proposal_model->check_by(array('proposals_id' => $proposals_id), 'tbl_proposals');
            if ($proposals_info->module == 'client') {
                $client_info = $this->proposal_model->check_by(array('client_id' => $proposals_info->module_id), 'tbl_client');
                $client = $client_info->name;
                $currency = $this->proposal_model->client_currency_sambol($proposals_info->module_id);
            } else if ($proposals_info->module == 'leads') {
                $client_info = $this->proposal_model->check_by(array('leads_id' => $proposals_info->module_id), 'tbl_leads');
                $client = $client_info->lead_name;
                $currency = $this->proposal_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
            } else {
                $client = '-';
                $currency = $this->proposal_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
            }

            $amount = $this->proposal_model->proposal_calculation('total', $proposals_info->proposals_id);
            $currency = $currency->code;
            $email_template = $this->proposal_model->check_by(array('email_group' => 'proposal_email'), 'tbl_email_templates');
            $message = $email_template->template_body;
            $ref = $proposals_info->reference_no;
            $subject = $email_template->subject;
        } else {
            $message = $this->input->post('message', TRUE);
            $ref = $this->input->post('ref', TRUE);
            $subject = $this->input->post('subject', TRUE);
            $client = $this->input->post('client_name', TRUE);
            $amount = $this->input->post('amount', true);
            $currency = $this->input->post('currency', TRUE);
        }

        $client_name = str_replace("{CLIENT}", $client, $message);
        $Ref = str_replace("{PROPOSAL_REF}", $ref, $client_name);
        $Amount = str_replace("{AMOUNT}", $amount, $Ref);
        $Currency = str_replace("{CURRENCY}", $currency, $Amount);
        $link = str_replace("{PROPOSAL_LINK}", base_url() . 'client/proposals/index/proposals_details/' . $proposals_id, $Currency);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $link);

        $this->send_email_proposals($proposals_id, $message, $subject); // Email proposals

        $data = array('status' => 'sent', 'emailed' => 'Yes', 'date_sent' => date("Y-m-d H:i:s", time()));

        $this->proposal_model->_table_name = 'tbl_proposals';
        $this->proposal_model->_primary_key = 'proposals_id';
        $this->proposal_model->save($data, $proposals_id);

        // Log Activity
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'proposals',
            'module_field_id' => $proposals_id,
            'activity' => 'activity_proposals_sent',
            'icon' => 'fa-shopping-cart',
            'link' => 'admin/proposals/index/proposals_details/' . $proposals_id,
            'value1' => $ref
        );
        $this->proposal_model->_table_name = 'tbl_activities';
        $this->proposal_model->_primary_key = 'activities_id';
        $this->proposal_model->save($activity);

        $type = 'success';
        $text = lang('proposals_email_sent');
        set_message($type, $text);
        redirect('admin/proposals/index/proposals_details/' . $proposals_id);
    }

    function send_email_proposals($proposals_id, $message, $subject)
    {
        $proposals_info = $this->proposal_model->check_by(array('proposals_id' => $proposals_id), 'tbl_proposals');
        if ($proposals_info->module == 'client') {
            $client_info = $this->proposal_model->check_by(array('client_id' => $proposals_info->module_id), 'tbl_client');
            $email = $client_info->email;
        } else if ($proposals_info->module == 'leads') {
            $client_info = $this->proposal_model->check_by(array('leads_id' => $proposals_info->module_id), 'tbl_leads');
            $email = $client_info->email;
        } else {
            $email = '-';
        }
        $recipient = $email;

        $data['message'] = $message;

        $message = $this->load->view('email_template', $data, TRUE);
        $params = array(
            'recipient' => $recipient,
            'subject' => $subject,
            'message' => $message
        );
        $params['resourceed_file'] = 'uploads/' . lang('proposal') . '_' . $proposals_info->reference_no . '.pdf';
        $params['resourcement_url'] = base_url() . 'uploads/' . lang('proposal') . '_' . $proposals_info->reference_no . '.pdf';

        $this->attach_pdf($proposals_id);

        $this->proposal_model->send_email($params);
        //Delete estimate in tmp folder
        if (is_file('uploads/' . lang('proposal') . '_' . $proposals_info->reference_no . '.pdf')) {
            unlink('uploads/' . lang('proposal') . '_' . $proposals_info->reference_no . '.pdf');
        }
        // send notification to client
        if (!empty($client_info->primary_contact)) {
            $notifyUser = array($client_info->primary_contact);
        } else {
            $user_info = $this->proposal_model->check_by(array('company' => $proposals_info->client_id), 'tbl_account_details');
            if (!empty($user_info)) {
                $notifyUser = array($user_info->user_id);
            }
        }
        if (!empty($notifyUser)) {
            foreach ($notifyUser as $v_user) {
                if ($v_user != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $v_user,
                        'icon' => 'shopping-cart',
                        'description' => 'not_email_send_alert',
                        'link' => 'client/proposals/index/proposals_details/' . $proposals_id,
                        'value' => lang('estimate') . ' ' . $proposals_info->reference_no,
                    ));
                }
            }
            show_notification($notifyUser);
        }
    }

    public function attach_pdf($id)
    {
        $data['page'] = lang('proposals');
        $data['sortable'] = true;
        $data['typeahead'] = true;
        $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
        $data['title'] = lang('proposals'); //Page title
        $this->load->helper('dompdf');
        $html = $this->load->view('admin/proposals/proposals_pdf', $data, TRUE);
        $result = pdf_create($html, lang('proposal') . '_' . $data['proposals_info']->reference_no, 1, null, true);
        return $result;
    }

    function proposals_email($proposals_id)
    {
        $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $proposals_id), 'tbl_proposals');
        $proposals_info = $data['proposals_info'];
        $client_info = $this->proposal_model->check_by(array('client_id' => $data['proposals_info']->client_id), 'tbl_client');

        $recipient = $client_info->email;

        $message = $this->load->view('admin/proposals/proposals_pdf', $data, TRUE);

        $data['message'] = $message;

        $message = $this->load->view('email_template', $data, TRUE);
        $params = array(
            'recipient' => $recipient,
            'subject' => '[ ' . config_item('company_name') . ' ]' . ' New proposals' . ' ' . $data['proposals_info']->reference_no,
            'message' => $message
        );
        $params['resourceed_file'] = '';

        $this->proposal_model->send_email($params);

        $data = array('status' => 'sent', 'emailed' => 'Yes', 'date_sent' => date("Y-m-d H:i:s", time()));

        $this->proposal_model->_table_name = 'tbl_proposals';
        $this->proposal_model->_primary_key = 'proposals_id';
        $this->proposal_model->save($data, $proposals_id);

        // Log Activity
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'proposals',
            'module_field_id' => $proposals_id,
            'activity' => 'activity_proposals_sent',
            'icon' => 'fa-shopping-cart',
            'link' => 'admin/proposals/index/proposals_details/' . $proposals_id,
            'value1' => $proposals_info->reference_no
        );
        $this->proposal_model->_table_name = 'tbl_activities';
        $this->proposal_model->_primary_key = 'activities_id';
        $this->proposal_model->save($activity);

        // send notification to client
        if (!empty($client_info->primary_contact)) {
            $notifyUser = array($client_info->primary_contact);
        } else {
            $user_info = $this->proposal_model->check_by(array('company' => $proposals_info->client_id), 'tbl_account_details');
            if (!empty($user_info)) {
                $notifyUser = array($user_info->user_id);
            }
        }
        if (!empty($notifyUser)) {
            foreach ($notifyUser as $v_user) {
                if ($v_user != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $v_user,
                        'icon' => 'shopping-cart',
                        'description' => 'not_email_send_alert',
                        'link' => 'client/proposals/index/proposals_details/' . $proposals_id,
                        'value' => lang('estimate') . ' ' . $proposals_info->reference_no,
                    ));
                }
            }
            show_notification($notifyUser);
        }

        $type = 'success';
        $text = lang('proposals_email_sent');
        set_message($type, $text);
        redirect('admin/proposals/index/proposals_details/' . $proposals_id);
    }

    public
    function convert_to($type, $id)
    {

        $data['title'] = lang('convert') . ' ' . lang($type);
        $edited = can_action('140', 'edited');
        $can_edit = $this->proposal_model->can_action('tbl_proposals', 'edit', array('proposals_id' => $id));
        if (!empty($can_edit) && !empty($edited)) {
            // get all client
            $this->proposal_model->_table_name = 'tbl_client';
            $this->proposal_model->_order_by = 'client_id';
            $data['all_client'] = $this->proposal_model->get();
            // get permission user
            $data['permission_user'] = $this->proposal_model->all_permission_user('140');

            $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');

            $data['modal_subview'] = $this->load->view('admin/proposals/convert_to_' . $type, $data, FALSE);
            $this->load->view('admin/_layout_modal_large', $data);
        } else {
            set_message('error', lang('there_in_no_value'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function converted_to_invoice($proposal_id)
    {
        $data = $this->proposal_model->array_from_post(array('reference_no', 'client_id', 'project_id', 'discount_type', 'discount_percent', 'user_id', 'adjustment', 'discount_total', 'show_quantity_as'));

        $data['allow_paypal'] = ($this->input->post('allow_paypal') == 'Yes') ? 'Yes' : 'No';
        $data['allow_stripe'] = ($this->input->post('allow_stripe') == 'Yes') ? 'Yes' : 'No';
        $data['allow_2checkout'] = ($this->input->post('allow_2checkout') == 'Yes') ? 'Yes' : 'No';
        $data['allow_authorize'] = ($this->input->post('allow_authorize') == 'Yes') ? 'Yes' : 'No';
        $data['allow_ccavenue'] = ($this->input->post('allow_ccavenue') == 'Yes') ? 'Yes' : 'No';
        $data['allow_braintree'] = ($this->input->post('allow_braintree') == 'Yes') ? 'Yes' : 'No';
        $data['client_visible'] = ($this->input->post('client_visible') == 'Yes') ? 'Yes' : 'No';
        $data['invoice_date'] = date('Y-m-d', strtotime($this->input->post('invoice_date', TRUE)));
        if (empty($data['invoice_date'])) {
            $data['invoice_date'] = date('Y-m-d');
        }
        $data['invoice_year'] = date('Y', strtotime($this->input->post('invoice_date', TRUE)));
        $data['invoice_month'] = date('Y-m', strtotime($this->input->post('invoice_date', TRUE)));
        $data['due_date'] = date('Y-m-d', strtotime($this->input->post('due_date', TRUE)));
        $data['notes'] = $this->input->post('notes', TRUE);
        $tax['tax_name'] = $this->input->post('total_tax_name', TRUE);
        $tax['total_tax'] = $this->input->post('total_tax', TRUE);
        $data['total_tax'] = json_encode($tax);
        $i_tax = 0;
        if (!empty($tax['total_tax'])) {
            foreach ($tax['total_tax'] as $v_tax) {
                $i_tax += $v_tax;
            }
        }
        $data['tax'] = $i_tax;
        $save_as_draft = $this->input->post('save_as_draft', TRUE);
        if (!empty($save_as_draft)) {
            $data['status'] = 'draft';
        }
        $currency = $this->proposal_model->client_currency_sambol($data['client_id']);
        if (!empty($currency->code)) {
            $curren = $currency->code;
        } else {
            $curren = config_item('default_currency');
        }
        $data['currency'] = $curren;

        $permission = $this->input->post('permission', true);
        if (!empty($permission)) {
            if ($permission == 'everyone') {
                $assigned = 'all';
            } else {
                $assigned_to = $this->proposal_model->array_from_post(array('assigned_to'));
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

        // get all client
        $this->proposal_model->_table_name = 'tbl_invoices';
        $this->proposal_model->_primary_key = 'invoices_id';

        $invoice_id = $this->proposal_model->save($data);
        $recuring_frequency = $this->input->post('recuring_frequency', TRUE);

        if (!empty($recuring_frequency) && $recuring_frequency != 'none') {
            $recur_data = $this->proposal_model->array_from_post(array('recur_start_date', 'recur_end_date'));
            $recur_data['recuring_frequency'] = $recuring_frequency;
            $this->get_recuring_frequency($invoice_id, $recur_data); // set recurring
        }
        // save items
        $invoices_to_merge = $this->input->post('invoices_to_merge', TRUE);
        $cancel_merged_invoices = $this->input->post('cancel_merged_invoices', TRUE);
        if (!empty($invoices_to_merge)) {
            foreach ($invoices_to_merge as $inv_id) {
                if (empty($cancel_merged_invoices)) {
                    $this->db->where('invoices_id', $inv_id);
                    $this->db->delete('tbl_invoices');

                    $this->db->where('items_id', $inv_id);
                    $this->db->delete('tbl_items');

                } else {
                    $mdata = array('status' => 'cancelled');
                    $this->proposal_model->_table_name = 'tbl_invoices';
                    $this->proposal_model->_primary_key = 'invoices_id';
                    $this->proposal_model->save($mdata, $inv_id);
                }
            }
        }

        $removed_items = $this->input->post('removed_items', TRUE);
        if (!empty($removed_items)) {
            foreach ($removed_items as $r_id) {
                if ($r_id != 'undefined') {
                    $this->db->where('items_id', $r_id);
                    $this->db->delete('tbl_items');
                }
            }
        }

        $itemsid = $this->input->post('items_id', TRUE);
        $items_data = $this->input->post('items', true);

        if (!empty($items_data)) {
            $index = 0;
            foreach ($items_data as $items) {
                $items['invoices_id'] = $invoice_id;
                if (!empty($items['taxname'])) {
                    $tax = 0;
                    foreach ($items['taxname'] as $tax_name) {
                        $tax_rate = explode("|", $tax_name);
                        $tax += $tax_rate[1];

                    }
                    $price = $items['quantity'] * $items['unit_cost'];
                    $items['item_tax_total'] = ($price / 100 * $tax);
                    $items['total_cost'] = $price;

                    $items['item_tax_name'] = $items['taxname'];
                    unset($items['taxname']);
                    $items['item_tax_name'] = json_encode($items['item_tax_name']);
                }
                // get all client
                $this->proposal_model->_table_name = 'tbl_items';
                $this->proposal_model->_primary_key = 'items_id';
                if (!empty($itemsid[$index])) {
                    $items_id = $itemsid[$index];
                    $this->proposal_model->save($items, $items_id);
                } else {
                    $items_id = $this->proposal_model->save($items);
                }
                $index++;
            }
        }

        $p_data = array('status' => 'accepted', 'convert' => 'Yes', 'convert_module' => 'invoice', 'convert_module_id' => $invoice_id);

        $this->proposal_model->_table_name = 'tbl_proposals';
        $this->proposal_model->_primary_key = 'proposals_id';
        $this->proposal_model->save($p_data, $proposal_id);

        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'proposals',
            'module_field_id' => $invoice_id,
            'activity' => 'convert_to_invoice_from_proposal',
            'icon' => 'fa-shopping-cart',
            'link' => 'admin/proposals/index/proposals_details/' . $proposal_id,
            'value1' => $data['reference_no']
        );
        $this->proposal_model->_table_name = 'tbl_activities';
        $this->proposal_model->_primary_key = 'activities_id';
        $this->proposal_model->save($activity);

        // send notification to client
        if (!empty($data['client_id'])) {
            $client_info = $this->invoice_model->check_by(array('client_id' => $data['client_id']), 'tbl_client');
            if (!empty($client_info->primary_contact)) {
                $notifyUser = array($client_info->primary_contact);
            } else {
                $user_info = $this->invoice_model->check_by(array('company' => $data['client_id']), 'tbl_account_details');
                if (!empty($user_info)) {
                    $notifyUser = array($user_info->user_id);
                }
            }
        }
        if (!empty($notifyUser)) {
            foreach ($notifyUser as $v_user) {
                if ($v_user != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $v_user,
                        'icon' => 'shopping-cart',
                        'description' => 'proposal_convert_to_invoice',
                        'link' => 'client/invoice/manage_invoice/invoice_details/' . $invoice_id,
                        'value' => $data['reference_no'],
                    ));
                }
            }
            show_notification($notifyUser);
        }
        // messages for user
        $type = "success";
        $message = lang('convert_to_invoice') . ' ' . lang('successfully');
        set_message($type, $message);
        redirect('admin/proposals/index/proposals_details/' . $proposal_id);
    }

    public function converted_to_estimate($proposal_id)
    {
        $data = $this->proposal_model->array_from_post(array('reference_no', 'client_id', 'project_id', 'discount_type', 'discount_percent', 'user_id', 'adjustment', 'discount_total', 'show_quantity_as'));

        $data['client_visible'] = ($this->input->post('client_visible') == 'Yes') ? 'Yes' : 'No';
        $data['estimate_date'] = date('Y-m-d', strtotime($this->input->post('estimate_date', TRUE)));
        if (empty($data['estimate_date'])) {
            $data['estimate_date'] = date('Y-m-d');
        }
        $data['estimate_year'] = date('Y', strtotime($this->input->post('estimate_date', TRUE)));
        $data['estimate_month'] = date('Y-m', strtotime($this->input->post('estimate_date', TRUE)));
        $data['due_date'] = date('Y-m-d', strtotime($this->input->post('due_date', TRUE)));
        $data['notes'] = $this->input->post('notes', TRUE);
        $tax['tax_name'] = $this->input->post('total_tax_name', TRUE);
        $tax['total_tax'] = $this->input->post('total_tax', TRUE);
        $data['total_tax'] = json_encode($tax);
        $i_tax = 0;
        if (!empty($tax['total_tax'])) {
            foreach ($tax['total_tax'] as $v_tax) {
                $i_tax += $v_tax;
            }
        }
        $data['tax'] = $i_tax;
        $save_as_draft = $this->input->post('status', TRUE);
        if (!empty($save_as_draft)) {
            $data['status'] = $save_as_draft;
        } else {
            $data['status'] = 'pending';
        }
        $currency = $this->proposal_model->client_currency_sambol($data['client_id']);
        if (!empty($currency->code)) {
            $curren = $currency->code;
        } else {
            $curren = config_item('default_currency');
        }
        $data['currency'] = $curren;

        $permission = $this->input->post('permission', true);
        if (!empty($permission)) {
            if ($permission == 'everyone') {
                $assigned = 'all';
            } else {
                $assigned_to = $this->proposal_model->array_from_post(array('assigned_to'));
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
        // get all client
        $this->proposal_model->_table_name = 'tbl_estimates';
        $this->proposal_model->_primary_key = 'estimates_id';
        if (!empty($id)) {
            $estimates_id = $id;
            $this->proposal_model->save($data, $id);
        } else {
            $estimates_id = $this->proposal_model->save($data);
        }
        // save items
        $invoices_to_merge = $this->input->post('invoices_to_merge', TRUE);
        $cancel_merged_invoices = $this->input->post('cancel_merged_estimate', TRUE);
        if (!empty($invoices_to_merge)) {
            foreach ($invoices_to_merge as $inv_id) {
                if (empty($cancel_merged_invoices)) {
                    $this->db->where('estimates_id', $inv_id);
                    $this->db->delete('tbl_estimates');

                    $this->db->where('estimate_items_id', $inv_id);
                    $this->db->delete('tbl_estimate_items');

                } else {
                    $mdata = array('status' => 'cancelled');
                    $this->proposal_model->_table_name = 'tbl_estimates';
                    $this->proposal_model->_primary_key = 'estimates_id';
                    $this->proposal_model->save($mdata, $inv_id);
                }
            }
        }

        $removed_items = $this->input->post('removed_items', TRUE);
        if (!empty($removed_items)) {
            foreach ($removed_items as $r_id) {
                if ($r_id != 'undefined') {
                    $this->db->where('estimate_items_id', $r_id);
                    $this->db->delete('tbl_estimate_items');
                }
            }
        }

        $itemsid = $this->input->post('estimate_items_id', TRUE);
        $items_data = $this->input->post('items', true);

        if (!empty($items_data)) {
            $index = 0;
            foreach ($items_data as $items) {
                $items['estimates_id'] = $estimates_id;
                if (!empty($items['taxname'])) {
                    $tax = 0;
                    foreach ($items['taxname'] as $tax_name) {
                        $tax_rate = explode("|", $tax_name);
                        $tax += $tax_rate[1];

                    }
                    $price = $items['quantity'] * $items['unit_cost'];
                    $items['item_tax_total'] = ($price / 100 * $tax);
                    $items['total_cost'] = $price;

                    $items['item_tax_name'] = $items['taxname'];
                    unset($items['taxname']);
                    $items['item_tax_name'] = json_encode($items['item_tax_name']);
                }
                // get all client
                $this->proposal_model->_table_name = 'tbl_estimate_items';
                $this->proposal_model->_primary_key = 'estimate_items_id';
                if (!empty($itemsid[$index])) {
                    $items_id = $itemsid[$index];
                    $this->proposal_model->save($items, $items_id);
                } else {
                    $items_id = $this->proposal_model->save($items);
                }
                $index++;
            }
        }
        $p_data = array('status' => 'accepted', 'convert' => 'Yes', 'convert_module' => 'estimate', 'convert_module_id' => $estimates_id);
        $this->proposal_model->_table_name = 'tbl_proposals';
        $this->proposal_model->_primary_key = 'proposals_id';
        $this->proposal_model->save($p_data, $proposal_id);

        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'proposals',
            'module_field_id' => $estimates_id,
            'activity' => 'convert_to_estimate_from_proposal',
            'icon' => 'fa-shopping-cart',
            'link' => 'admin/proposals/index/proposals_details/' . $proposal_id,
            'value1' => $data['reference_no']
        );
        $this->proposal_model->_table_name = 'tbl_activities';
        $this->proposal_model->_primary_key = 'activities_id';
        $this->proposal_model->save($activity);

        // send notification to client
        if (!empty($data['client_id'])) {
            $client_info = $this->proposal_model->check_by(array('client_id' => $data['client_id']), 'tbl_client');
            if (!empty($client_info->primary_contact)) {
                $notifyUser = array($client_info->primary_contact);
            } else {
                $user_info = $this->proposal_model->check_by(array('company' => $data['client_id']), 'tbl_account_details');
                if (!empty($user_info)) {
                    $notifyUser = array($user_info->user_id);
                }
            }
        }
        if (!empty($notifyUser)) {
            foreach ($notifyUser as $v_user) {
                if ($v_user != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $v_user,
                        'icon' => 'shopping-cart',
                        'description' => 'proposal_convert_to_estimate',
                        'link' => 'client/estimates/index/estimates_details/' . $estimates_id,
                        'value' => $data['reference_no'],
                    ));
                }
            }
            show_notification($notifyUser);
        }
        // messages for user
        $type = "success";
        $message = lang('convert_to_estimate') . ' ' . lang('successfully');
        set_message($type, $message);
        redirect('admin/proposals/index/proposals_details/' . $proposal_id);
    }

}
