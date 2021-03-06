<?php

class Estimates_Model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    function estimate_calculation($estimate_value, $estimates_id)
    {
        switch ($estimate_value) {
            case 'estimate_cost':
                return $this->get_estimate_cost($estimates_id);
                break;
            case 'tax':
                return $this->get_estimate_tax_amount($estimates_id);
                break;
            case 'discount':
                return $this->get_estimate_discount($estimates_id);
                break;
            case 'estimate_amount':
                return $this->get_estimate_amount($estimates_id);
                break;
            case 'total':
                return $this->get_total_estimate_amount($estimates_id);
                break;
        }
    }

    function get_estimate_cost($estimates_id)
    {
        $this->db->select_sum('total_cost');
        $this->db->where('estimates_id', $estimates_id);
        $this->db->from('tbl_estimate_items');
        $query_result = $this->db->get();
        $cost = $query_result->row();
        if (!empty($cost->total_cost)) {
            $result = $cost->total_cost;
        } else {
            $result = '0';
        }
        return $result;
    }

    function get_estimate_tax_amount($estimates_id)
    {

        $invoice_info = $this->check_by(array('estimates_id' => $estimates_id), 'tbl_estimates');
        $tax_info = json_decode($invoice_info->total_tax);
        $tax = 0;
        if (!empty($tax_info)) {
            $total_tax = $tax_info->total_tax;
            if (!empty($total_tax)) {
                foreach ($total_tax as $t_key => $v_tax_info) {
                    $tax += $v_tax_info;
                }
            }
        }
        return $tax;
    }

    function get_estimate_discount($estimates_id)
    {
        $invoice_info = $this->check_by(array('estimates_id' => $estimates_id), 'tbl_estimates');
        return $invoice_info->discount_total;
    }

    function get_estimate_amount($estimates_id)
    {

        $tax = $this->get_estimate_tax_amount($estimates_id);
        $discount = $this->get_estimate_discount($estimates_id);
        $estimate_cost = $this->get_estimate_cost($estimates_id);
        return (($estimate_cost - $discount) + $tax);
    }

    function get_total_estimate_amount($estimates_id)
    {
        $invoice_info = $this->check_by(array('estimates_id' => $estimates_id), 'tbl_estimates');
        $tax = $this->get_estimate_tax_amount($estimates_id);
        $discount = $this->get_estimate_discount($estimates_id);
        $estimate_cost = $this->get_estimate_cost($estimates_id);
        return (($estimate_cost - $discount) + $tax + $invoice_info->adjustment);
    }

    function ordered_items_by_id($id)
    {
        $result = $this->db->where('estimates_id', $id)->order_by('order', 'asc')->get('tbl_estimate_items')->result();
        return $result;
    }

    public function generate_estimate_number()
    {
        $query = $this->db->select_max('estimates_id')->get('tbl_estimates');
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $next_number = ++$row->estimates_id;
            $next_number = $this->estimate_reference_no_exists($next_number);
            $next_number = sprintf('%04d', $next_number);
            return $next_number;
        } else {
            return sprintf('%04d', config_item('estimate_start_no'));
        }
    }

    public function estimate_reference_no_exists($next_number)
    {
        $next_number = sprintf('%04d', $next_number);

        $records = $this->db->where('reference_no', config_item('estimate_prefix') . $next_number)->get('tbl_estimates')->num_rows();
        if ($records > 0) {
            return $this->estimate_reference_no_exists($next_number + 1);
        } else {
            return $next_number;
        }
    }

    public function check_for_merge_invoice($client_id, $current_estimate)
    {

        $estimate_info = $this->db->where('client_id', $client_id)->get('tbl_estimates')->result();

        foreach ($estimate_info as $v_estimate) {
            if ($v_estimate->estimates_id != $current_estimate) {
                if (strtolower($v_estimate->status) == 'pending' || $v_estimate->status == 'draft') {
                    $estimate[] = $v_estimate;
                }
            }
        }
        if (!empty($estimate)) {
            return $estimate;
        } else {
            return array();
        }
    }

    public function get_invoice_filter()
    {
        $all_invoice = $this->get_permission('tbl_estimates');
        if (!empty($all_invoice)) {
            $all_invoice = array_reverse($all_invoice);
            foreach ($all_invoice as $v_invoices) {
                $year[] = date('Y', strtotime($v_invoices->estimate_date));
            }
        }
        if (!empty($year)) {
            $result = array_unique($year);
        }

        $statuses = array(
            array(
                'id' => 1,
                'value' => 'draft',
                'name' => lang('draft'),
                'order' => 1,
            ), array(
                'id' => 1,
                'value' => 'cancelled',
                'name' => lang('cancelled'),
                'order' => 1,
            ), array(
                'id' => 1,
                'value' => 'expired',
                'name' => lang('expired'),
                'order' => 1,
            ),
            array(
                'id' => 4,
                'value' => 'declined',
                'name' => lang('declined'),
                'order' => 4,
            ),
            array(
                'id' => 4,
                'value' => 'accepted',
                'name' => lang('accepted'),
                'order' => 4,
            ),
            array(
                'id' => 4,
                'value' => 'last_month',
                'name' => lang('last_month'),
                'order' => 4,
            ),
            array(
                'id' => 4,
                'value' => 'this_months',
                'name' => lang('this_months'),
                'order' => 4,
            )
        );
        if (!empty($result)) {
            foreach ($result as $v_year) {
                $test = array(
                    'id' => 1,
                    'value' => '_' . $v_year,
                    'name' => $v_year,
                    'order' => 1);
                if (!empty($test)) {
                    array_push($statuses, $test);
                }
            }
        }
        return $statuses;
    }
    public function get_estimate_filter()
    {
        $all_invoice = $this->get_permission('tbl_estimates');
        if (!empty($all_invoice)) {
            $all_invoice = array_reverse($all_invoice);
            foreach ($all_invoice as $v_invoices) {
                $year[] = date('Y', strtotime($v_invoices->estimate_date));
            }
        }
        if (!empty($year)) {
            $result = array_unique($year);
        }

        $statuses = array(
            array(
                'id' => 1,
                'value' => 'draft',
                'name' => lang('draft'),
                'order' => 1,
            ), array(
                'id' => 1,
                'value' => 'cancelled',
                'name' => lang('cancelled'),
                'order' => 1,
            ), array(
                'id' => 1,
                'value' => 'expired',
                'name' => lang('expired'),
                'order' => 1,
            ),
            array(
                'id' => 4,
                'value' => 'declined',
                'name' => lang('declined'),
                'order' => 4,
            ),
            array(
                'id' => 4,
                'value' => 'accepted',
                'name' => lang('accepted'),
                'order' => 4,
            ),
            array(
                'id' => 4,
                'value' => 'last_month',
                'name' => lang('last_month'),
                'order' => 4,
            ),
            array(
                'id' => 4,
                'value' => 'this_months',
                'name' => lang('this_months'),
                'order' => 4,
            )
        );
        if (!empty($result)) {
            foreach ($result as $v_year) {
                $test = array(
                    'id' => 1,
                    'value' => '_' . $v_year,
                    'name' => $v_year,
                    'order' => 1);
                if (!empty($test)) {
                    array_push($statuses, $test);
                }
            }
        }
        return $statuses;
    }

    public function get_estimates($filterBy = null, $client_id = null)
    {
        if (!empty($client_id)) {
            $all_invoice = $this->db->where('client_id', $client_id)->get('tbl_estimates')->result();
        } else {
            $all_invoice = $this->get_permission('tbl_estimates');
        }
        if (empty($filterBy) || !empty($filterBy) && $filterBy == 'all') {
            return $all_invoice;
        } else {
            if (!empty($all_invoice)) {
                $all_invoice = array_reverse($all_invoice);
                foreach ($all_invoice as $v_invoices) {

                    if ($filterBy == 'last_month' || $filterBy == 'this_months') {
                        if ($filterBy == 'last_month') {
                            $month = date('Y-m', strtotime('-1 months'));
                        } else {
                            $month = date('Y-m');
                        }
                        if (strtotime($v_invoices->estimate_month) == strtotime($month)) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($filterBy == 'expired') {
                        if (strtotime($v_invoices->due_date) < time() AND $v_invoices->status == ('pending') || strtotime($v_invoices->due_date) < time() AND $v_invoices->status == ('draft')) {
                            $invoice[] = $v_invoices;
                        }

                    } else if ($filterBy == $v_invoices->status) {
                        $invoice[] = $v_invoices;
                    } else if (strstr($filterBy, '_')) {
                        $year = str_replace('_', '', $filterBy);
                        if (strtotime($v_invoices->estimate_year) == strtotime($year)) {
                            $invoice[] = $v_invoices;
                        }
                    }

                }
            }
        }
        if (!empty($invoice)) {
            return $invoice;
        } else {
            return array();
        }

    }

}
