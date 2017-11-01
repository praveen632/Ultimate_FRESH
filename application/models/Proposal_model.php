<?php

class Proposal_Model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    function proposal_calculation($proposal_value, $proposals_id)
    {
        switch ($proposal_value) {
            case 'proposal_cost':
                return $this->get_proposal_cost($proposals_id);
                break;
            case 'tax':
                return $this->get_proposal_tax_amount($proposals_id);
                break;
            case 'discount':
                return $this->get_proposal_discount($proposals_id);
                break;
            case 'proposal_amount':
                return $this->get_proposal_amount($proposals_id);
                break;
            case 'total':
                return $this->get_total_proposal_amount($proposals_id);
                break;
        }
    }

    function get_proposal_cost($proposals_id)
    {
        $this->db->select_sum('total_cost');
        $this->db->where('proposals_id', $proposals_id);
        $this->db->from('tbl_proposals_items');
        $query_result = $this->db->get();
        $cost = $query_result->row();
        if (!empty($cost->total_cost)) {
            $result = $cost->total_cost;
        } else {
            $result = '0';
        }
        return $result;
    }

    function get_proposal_tax_amount($proposals_id)
    {

        $invoice_info = $this->check_by(array('proposals_id' => $proposals_id), 'tbl_proposals');
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

    function get_proposal_discount($proposals_id)
    {
        $invoice_info = $this->check_by(array('proposals_id' => $proposals_id), 'tbl_proposals');
        return $invoice_info->discount_total;
    }

    function get_proposal_amount($proposals_id)
    {

        $tax = $this->get_proposal_tax_amount($proposals_id);
        $discount = $this->get_proposal_discount($proposals_id);
        $proposal_cost = $this->get_proposal_cost($proposals_id);
        return (($proposal_cost - $discount) + $tax);
    }

    function get_total_proposal_amount($proposals_id)
    {
        $invoice_info = $this->check_by(array('proposals_id' => $proposals_id), 'tbl_proposals');
        $tax = $this->get_proposal_tax_amount($proposals_id);
        $discount = $this->get_proposal_discount($proposals_id);
        $proposal_cost = $this->get_proposal_cost($proposals_id);
        return (($proposal_cost - $discount) + $tax + $invoice_info->adjustment);
    }

    function ordered_items_by_id($id)
    {
        $result = $this->db->where('proposals_id', $id)->order_by('order', 'asc')->get('tbl_proposals_items')->result();
        return $result;
    }

    public function generate_proposal_number()
    {
        $query = $this->db->select_max('proposals_id')->get('tbl_proposals');
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $next_number = ++$row->proposals_id;
            $next_number = $this->proposal_reference_no_exists($next_number);
            $next_number = sprintf('%04d', $next_number);
            return $next_number;
        } else {
            return sprintf('%04d', config_item('proposal_start_no'));
        }
    }

    public function proposal_reference_no_exists($next_number)
    {
        $next_number = sprintf('%04d', $next_number);

        $records = $this->db->where('reference_no', config_item('proposal_prefix') . $next_number)->get('tbl_proposals')->num_rows();
        if ($records > 0) {
            return $this->proposal_reference_no_exists($next_number + 1);
        } else {
            return $next_number;
        }
    }

    public function check_for_merge_invoice($client_id, $current_proposal)
    {

        $proposal_info = $this->db->where('client_id', $client_id)->get('tbl_proposals')->result();

        foreach ($proposal_info as $v_proposal) {
            if ($v_proposal->proposals_id != $current_proposal) {
                if ($v_proposal->status == 'Pending' || $v_proposal->status == 'draft') {
                    $proposal[] = $v_proposal;
                }
            }
        }
        if (!empty($proposal)) {
            return $proposal;
        } else {
            return array();
        }
    }

    public function get_invoice_filter()
    {
        $all_invoice = $this->get_permission('tbl_proposals');
        if (!empty($all_invoice)) {
            $all_invoice = array_reverse($all_invoice);
            foreach ($all_invoice as $v_invoices) {
                $year[] = date('Y', strtotime($v_invoices->proposal_date));
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

    public function get_proposals($filterBy = null)
    {
        $all_invoice = $this->get_permission('tbl_proposals');

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
                        if (strtotime($v_invoices->proposal_month) == strtotime($month)) {
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
                        if (strtotime($v_invoices->proposal_year) == strtotime($year)) {
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
