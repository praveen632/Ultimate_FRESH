<form name="myform" role="form"
      enctype="multipart/form-data"
      id="form"
      action="<?php echo base_url(); ?>admin/proposals/save_proposals/<?php
      if (!empty($proposals_info)) {
          echo $proposals_info->proposals_id;
      }
      ?>" method="post" class="form-horizontal  ">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.16.0/jquery.validate.js"></script>
    <?php include_once 'assets/admin-ajax.php'; ?>
    <?php include_once 'assets/js/sales.php'; ?>
    <?= message_box('success'); ?>
    <?= message_box('error'); ?>

    <?php
    $curency = $this->proposal_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');

    if ($this->session->userdata('user_type') == 1) {
        $margin = 'margin-bottom:30px';
        $h_s = config_item('proposal_state');
        ?>
        <div id="state_report" style="display: <?= $h_s ?>">

            <?php
            $currency = $this->db->where(array('code' => config_item('default_currency')))->get('tbl_currencies')->row();
            ?>
            <div class="row">
                <!-- END widget-->
                <?php
                $expired = 0;
                $draft = 0;
                $total_draft = 0;
                $total_sent = 0;
                $total_declined = 0;
                $total_accepted = 0;
                $total_expired = 0;
                $sent = 0;
                $declined = 0;
                $accepted = 0;
                $pending = 0;
                $cancelled = 0;
                $all_proposals = $this->proposal_model->get_permission('tbl_proposals');
                if (!empty($all_proposals)) {
                    $all_proposals = array_reverse($all_proposals);
                    foreach ($all_proposals as $v_invoice) {
                        if (strtotime($v_invoice->due_date) < time() AND $v_invoice->status == ('pending') || strtotime($v_invoice->due_date) < time() AND $v_invoice->status == ('draft')) {
                            $total_expired += $this->proposal_model->proposal_calculation('total', $v_invoice->proposals_id);
                            $expired += count($v_invoice->proposals_id);;
                        }
                        if ($v_invoice->status == ('draft')) {
                            $draft += count($v_invoice->proposals_id);
                            $total_draft += $this->proposal_model->proposal_calculation('total', $v_invoice->proposals_id);
                        }
                        if ($v_invoice->status == ('sent')) {
                            $sent += count($v_invoice->proposals_id);
                            $total_sent += $this->proposal_model->proposal_calculation('total', $v_invoice->proposals_id);
                        }
                        if ($v_invoice->status == ('declined')) {
                            $declined += count($v_invoice->proposals_id);
                            $total_declined += $this->proposal_model->proposal_calculation('total', $v_invoice->proposals_id);
                        }
                        if ($v_invoice->status == ('accepted')) {
                            $accepted += count($v_invoice->proposals_id);
                            $total_accepted += $this->proposal_model->proposal_calculation('total', $v_invoice->proposals_id);
                        }
                        if ($v_invoice->status == ('pending')) {
                            $pending += count($v_invoice->proposals_id);
                        }
                        if ($v_invoice->status == ('cancelled')) {
                            $cancelled += count($v_invoice->proposals_id);
                        }
                    }
                }
                ?>

                <div class="col-lg-5ths">
                    <!-- START widget-->
                    <div class="panel widget">
                        <div class="panel-body pl-sm pr-sm pt-sm pb0 text-center">
                            <h3 class="mt0 mb0 "><?= display_money($total_draft, $currency->symbol) ?></h3>
                            <p class="m0"><?= lang('draft') ?></p>
                        </div>
                    </div>
                    <!-- END widget-->
                </div>
                <div class="col-lg-5ths">
                    <!-- START widget-->
                    <div class="panel widget">
                        <div class="panel-body pl-sm pr-sm pt-sm pb0 text-center">
                            <h3 class="mt0 mb0"><?= display_money($total_sent, $currency->symbol) ?></h3>
                            <p class="text-primary m0"><?= lang('sent') ?></p>
                        </div>
                    </div>
                    <!-- END widget-->
                </div>
                <div class="col-lg-5ths">
                    <!-- START widget-->
                    <div class="panel widget">
                        <div class="panel-body pl-sm pr-sm pt-sm pb0 text-center">
                            <h3 class="mt0 mb0 "><?= display_money($total_expired, $currency->symbol) ?></h3>
                            <p class="text-danger m0"><?= lang('expired') ?></p>
                        </div>
                    </div>
                    <!-- END widget-->
                </div>
                <div class="col-lg-5ths">
                    <!-- START widget-->
                    <div class="panel widget">
                        <div class="panel-body pl-sm pr-sm pt-sm pb0 text-center">
                            <h3 class="mt0 mb0 "><?= display_money($total_declined, $currency->symbol) ?></h3>
                            <p class="text-warning m0"><?= lang('declined') ?></p>
                        </div>
                    </div>
                    <!-- END widget-->
                </div>
                <div class="col-lg-5ths">
                    <!-- START widget-->
                    <div class="panel widget">
                        <div class="panel-body pl-sm pr-sm pt-sm pb0 text-center">
                            <h3 class="mt0 mb0 "><?= display_money($total_accepted, $currency->symbol) ?></h3>
                            <p class="text-success m0"><?= lang('accepted') ?></p>
                        </div>
                    </div>
                    <!-- END widget-->
                </div>
            </div>
            <?php if (!empty($all_proposals)) { ?>
                <div class="row">
                    <div class="col-lg-5ths">
                        <!-- START widget-->
                        <div class="panel widget">
                            <div class="pl-sm pr-sm pb-sm">
                                <strong><a class="text-purple" style="font-size: 15px"
                                           href="<?= base_url() ?>admin/proposals/index/filter_by/draft"><?= lang('draft') ?></a>
                                    <small class="pull-right " style="padding-top: 2px"> <?= $draft ?>
                                        / <?= count($all_proposals) ?></small>
                                </strong>
                                <div class="progress progress-striped progress-xs mb-sm">
                                    <div class="progress-bar progress-bar-primary " data-toggle="tooltip"
                                         data-original-title="<?= ($draft / count($all_proposals)) * 100 ?>%"
                                         style="width: <?= ($draft / count($all_proposals)) * 100 ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <!-- END widget-->
                    </div>
                    <div class="col-lg-5ths pr-lg">
                        <!-- START widget-->
                        <div class="panel widget">
                            <div class="pl-sm pr-sm pb-sm">
                                <strong><a class="text-primary" style="font-size: 15px"
                                           href="<?= base_url() ?>admin/proposals/index/filter_by/sent"><?= lang('sent') ?></a>
                                    <small class="pull-right " style="padding-top: 2px"> <?= $sent ?>
                                        / <?= count($all_proposals) ?></small>
                                </strong>
                                <div class="progress progress-striped progress-xs mb-sm">
                                    <div class="progress-bar progress-bar-aqua " data-toggle="tooltip"
                                         data-original-title="<?= ($sent / count($all_proposals)) * 100 ?>%"
                                         style="width: <?= ($sent / count($all_proposals)) * 100 ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <!-- END widget-->
                    </div>
                    <div class="col-lg-5ths">
                        <!-- START widget-->
                        <div class="panel widget">
                            <div class="pl-sm pr-sm pb-sm">
                                <strong><a class="text-danger" style="font-size: 15px"
                                           href="<?= base_url() ?>admin/proposals/index/filter_by/expired"><?= lang('expired') ?></a>
                                    <small class="pull-right " style="padding-top: 2px"> <?= $expired ?>
                                        / <?= count($all_proposals) ?></small>
                                </strong>
                                <div class="progress progress-striped progress-xs mb-sm">
                                    <div class="progress-bar progress-bar-primary " data-toggle="tooltip"
                                         data-original-title="<?= ($expired / count($all_proposals)) * 100 ?>%"
                                         style="width: <?= ($expired / count($all_proposals)) * 100 ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <!-- END widget-->
                    </div>
                    <div class="col-lg-5ths">
                        <!-- START widget-->
                        <div class="panel widget">
                            <div class="pl-sm pr-sm pb-sm">
                                <strong><a class="text-warning" style="font-size: 15px"
                                           href="<?= base_url() ?>admin/proposals/index/filter_by/declined"><?= lang('declined') ?></a>
                                    <small class="pull-right " style="padding-top: 2px"> <?= $declined ?>
                                        / <?= count($all_proposals) ?></small>
                                </strong>
                                <div class="progress progress-striped progress-xs mb-sm">
                                    <div class="progress-bar progress-bar-primary " data-toggle="tooltip"
                                         data-original-title="<?= ($declined / count($all_proposals)) * 100 ?>%"
                                         style="width: <?= ($declined / count($all_proposals)) * 100 ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <!-- END widget-->
                    </div>
                    <div class="col-lg-5ths">
                        <!-- START widget-->
                        <div class="panel widget">
                            <div class="pl-sm pr-sm pb-sm">
                                <strong><a class="text-success" style="font-size: 15px"
                                           href="<?= base_url() ?>admin/proposals/index/filter_by/accepted"><?= lang('accepted') ?></a>
                                    <small class="pull-right " style="padding-top: 2px"> <?= $accepted ?>
                                        / <?= count($all_proposals) ?></small>
                                </strong>
                                <div class="progress progress-striped progress-xs mb-sm">
                                    <div class="progress-bar progress-bar-warning " data-toggle="tooltip"
                                         data-original-title="<?= ($accepted / count($all_proposals)) * 100 ?>%"
                                         style="width: <?= ($accepted / count($all_proposals)) * 100 ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <!-- END widget-->
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php }
    $type = $this->uri->segment(5);

    if (empty($type)) {
        $type = '_' . date('Y');
    }
    ?>
    <div class="btn-group mb-lg pull-left mr">
        <button class=" btn btn-xs btn-white dropdown-toggle"
                data-toggle="dropdown">
            <i class="fa fa-search"></i>
            <?php
            echo lang('filter_by');
            if (!empty($type) && !is_numeric($type)) {
                $ex = explode('_', $type);
                if (!empty($ex)) {
                    if (!empty($ex[1]) && is_numeric($ex[1])) {
                        echo ' : ' . $ex[1];
                    } else {
                        echo ' : ' . lang($type);
                    }
                } else {
                    echo ' : ' . lang($type);
                }

            } ?>
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu animated zoomIn">
            <li><a href="<?= base_url() ?>admin/proposals/index/filter_by/all"><?= lang('all'); ?></a></li>
            <?php
            $invoiceFilter = $this->proposal_model->get_invoice_filter();
            if (!empty($invoiceFilter)) {
                foreach ($invoiceFilter as $v_Filter) {
                    ?>
                    <li <?php if ($v_Filter['value'] == $type) {
                        echo 'class="active"';
                    } ?> >
                        <a href="<?= base_url() ?>admin/proposals/index/filter_by/<?= $v_Filter['value'] ?>"><?= $v_Filter['name'] ?></a>
                    </li>
                    <?php
                }
            }
            ?>
        </ul>
    </div>
    <?php
    if ($this->session->userdata('user_type') == 1) {
        $type = 'proposal';
        if ($h_s == 'block') {
            $title = lang('hide_quick_state');
            $url = 'hide';
            $icon = 'fa fa-eye-slash';
        } else {
            $title = lang('view_quick_state');
            $url = 'show';
            $icon = 'fa fa-eye';
        }
        ?>
        <div onclick="slideToggle('#state_report')" id="quick_state" data-toggle="tooltip" data-placement="top"
             title="<?= $title ?>"
             class="btn-xs btn btn-purple pull-left">
            <i class="fa fa-bar-chart"></i>
        </div>
        <div class="btn-xs btn btn-white pull-left ml ">
            <a class="text-dark" id="change_report"
               href="<?= base_url() ?>admin/dashboard/change_report/<?= $url . '/' . $type ?>"><i
                    class="<?= $icon ?>"></i>
                <span><?= ' ' . lang('quick_state') . ' ' . lang($url) . ' ' . lang('always') ?></span></a>
        </div>
    <?php }
    $created = can_action('140', 'created');
    $edited = can_action('140', 'edited');
    $deleted = can_action('140', 'deleted');
    if (!empty($created) || !empty($edited)){
    ?>
    <a data-toggle="modal" data-target="#myModal"
       href="<?= base_url() ?>admin/invoice/zipped/proposal"
       class="btn btn-success btn-xs ml-lg"><?= lang('zip_proposal') ?></a>
    <div class="row">
        <div class="col-sm-12">
            <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs">
                    <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage"
                                                                        data-toggle="tab"><?= lang('all_proposals') ?></a>
                    </li>
                    <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#new"
                                                                        data-toggle="tab"><?= lang('create_proposal') ?></a>
                    </li>
                </ul>
                <div class="tab-content bg-white">
                    <!-- ************** general *************-->
                    <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
                        <?php } else { ?>
                        <div class="panel panel-custom">
                            <header class="panel-heading ">
                                <div class="panel-title"><strong><?= lang('all_proposals') ?></strong></div>
                            </header>
                            <?php } ?>
                            <div class="table-responsive">
                                <table class="table table-striped DataTables " id="DataTables" cellspacing="0"
                                       width="100%">
                                    <thead>
                                    <tr>
                                        <th><?= lang('proposal') ?> #</th>
                                        <th><?= lang('proposal_date') ?></th>
                                        <th><?= lang('expire_date') ?></th>
                                        <th><?= strtoupper(lang('TO')) ?></th>
                                        <th><?= lang('amount') ?></th>
                                        <th><?= lang('status') ?></th>
                                        <?php if (!empty($edited) || !empty($deleted)) { ?>
                                            <th class="hidden-print"><?= lang('action') ?></th>
                                        <?php } ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php

                                    if (!empty($all_proposals_info)) {
                                        foreach ($all_proposals_info as $v_proposals) {
                                            $can_edit = $this->proposal_model->can_action('tbl_proposals', 'edit', array('proposals_id' => $v_proposals->proposals_id));
                                            $can_delete = $this->proposal_model->can_action('tbl_proposals', 'delete', array('proposals_id' => $v_proposals->proposals_id));

                                            if ($v_proposals->status == 'pending') {
                                                $label = "info";
                                            } elseif ($v_proposals->status == 'accepted') {
                                                $label = "success";
                                            } else {
                                                $label = "danger";
                                            }
                                            ?>
                                            <tr>
                                                <td>
                                                    <a class="text-info"
                                                       href="<?= base_url() ?>admin/proposals/index/proposals_details/<?= $v_proposals->proposals_id ?>"><?= $v_proposals->reference_no ?></a>
                                                    <?php if ($v_proposals->convert == 'Yes') {
                                                        if ($v_proposals->convert_module == 'invoice') {
                                                            $c_url = base_url() . 'admin/invoice/manage_invoice/invoice_details/' . $v_proposals->convert_module_id;
                                                            $text = lang('invoiced');
                                                        } else {
                                                            $text = lang('estimated');
                                                            $c_url = base_url() . 'admin/estimates/index/estimates_details/' . $v_proposals->convert_module_id;
                                                        }
                                                        if (!empty($c_url)) { ?>
                                                            <p class="text-sm m0 p0">
                                                                <a class="text-success"
                                                                   href="<?= $c_url ?>">
                                                                    <?= $text ?>
                                                                </a>
                                                            </p>
                                                        <?php }
                                                    } ?>
                                                </td>
                                                <td><?= strftime(config_item('date_format'), strtotime($v_proposals->proposal_date)) ?></td>
                                                <td><?= strftime(config_item('date_format'), strtotime($v_proposals->due_date)) ?>
                                                    <?php
                                                    if (strtotime($v_proposals->due_date) < time() AND $v_proposals->status == 'pending' || strtotime($v_proposals->due_date) < time() AND $v_proposals->status == ('draft')) { ?>
                                                        <span class="label label-danger "><?= lang('expired') ?></span>
                                                    <?php }
                                                    ?>
                                                </td>
                                                <?php
                                                if ($v_proposals->module == 'client') {
                                                    $client_info = $this->proposal_model->check_by(array('client_id' => $v_proposals->module_id), 'tbl_client');
                                                    $client_name = $client_info->name;
                                                    $currency = $this->proposal_model->client_currency_sambol($v_proposals->module_id);
                                                } else if ($v_proposals->module == 'leads') {
                                                    $client_info = $this->proposal_model->check_by(array('leads_id' => $v_proposals->module_id), 'tbl_leads');
                                                    $client_name = $client_info->lead_name;
                                                    $currency = $this->proposal_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                                                } else {
                                                    $client_name = '-';
                                                    $currency = $this->proposal_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                                                }

                                                ?>
                                                <td><?= $client_name ?></td>
                                                <?php ?>
                                                <td>
                                                    <?= display_money($this->proposal_model->proposal_calculation('total', $v_proposals->proposals_id), $currency->symbol); ?>
                                                </td>
                                                <td><span
                                                        class="label label-<?= $label ?>"><?= lang($v_proposals->status) ?></span>
                                                </td>
                                                <?php if (!empty($edited) || !empty($deleted)) { ?>
                                                    <td>
                                                        <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                                            <?= btn_edit('admin/proposals/index/edit_proposals/' . $v_proposals->proposals_id) ?>
                                                        <?php }
                                                        if (!empty($can_delete) && !empty($deleted)) {
                                                            ?>
                                                            <?= btn_delete('admin/proposals/delete/delete_proposals/' . $v_proposals->proposals_id) ?>
                                                        <?php } ?>
                                                        <?php if (!empty($can_edit) && !empty($edited)) { ?>
                                                            <div class="btn-group">
                                                                <button class="btn btn-xs btn-default dropdown-toggle"
                                                                        data-toggle="dropdown">
                                                                    <?= lang('change_status') ?>
                                                                    <span class="caret"></span></button>
                                                                <ul class="dropdown-menu animated zoomIn">
                                                                    <li>
                                                                        <a href="<?= base_url() ?>admin/proposals/index/email_proposals/<?= $v_proposals->proposals_id ?>"><?= lang('send_email') ?></a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="<?= base_url() ?>admin/proposals/index/proposals_details/<?= $v_proposals->proposals_id ?>"><?= lang('view_details') ?></a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="<?= base_url() ?>admin/proposals/index/proposals_history/<?= $v_proposals->proposals_id ?>"><?= lang('history') ?></a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="<?= base_url() ?>admin/proposals/change_status/declined/<?= $v_proposals->proposals_id ?>"><?= lang('declined') ?></a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="<?= base_url() ?>admin/proposals/change_status/accepted/<?= $v_proposals->proposals_id ?>"><?= lang('accepted') ?></a>
                                                                    </li>

                                                                </ul>
                                                            </div>
                                                        <?php } ?>
                                                    </td>
                                                <?php } ?>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php if (!empty($created) || !empty($edited)) { ?>
                        <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="new">
                            <div class="row mb-lg invoice proposal-template">
                                <div class="col-xs-6 br pv">
                                    <div class="row">
                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('reference_no') ?> <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-lg-7">
                                                <?php $this->load->helper('string'); ?>
                                                <input type="text" class="form-control" value="<?php
                                                if (!empty($proposals_info)) {
                                                    echo $proposals_info->reference_no;
                                                } else {
                                                    echo config_item('proposal_prefix');
                                                    if (config_item('increment_proposal_number') == 'FALSE') {
                                                        $this->load->helper('string');
                                                        echo random_string('nozero', 6);
                                                    } else {
                                                        echo $this->proposal_model->generate_proposal_number();
                                                    }
                                                }
                                                ?>" name="reference_no">
                                            </div>

                                        </div>
                                        <?php if (!empty($proposals_info->module)) {
                                            if ($proposals_info->module == 'leads') {
                                                $leads_id = $proposals_info->module_id;
                                            }
                                            if ($proposals_info->module == 'client') {
                                                $client_id = $proposals_info->module_id;
                                            }

                                        } elseif (!empty($module)) {
                                            if ($module == 'leads') {
                                                $leads_id = $module_id;

                                            }
                                            if ($module == 'client') {
                                                $client_id = $module_id;
                                            }
                                            ?>
                                            <input type="hidden" name="un_module" required class="form-control"
                                                   value="<?php echo $module ?>"/>
                                            <input type="hidden" name="un_module_id" required class="form-control"
                                                   value="<?php echo $module_id ?>"/>
                                        <?php }
                                        ?>
                                        <div class="form-group" id="border-none">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('related_to') ?> </label>
                                            <div class="col-sm-7">
                                                <select name="module" class="form-control select_box"
                                                        id="check_related" required
                                                        onchange="get_related_moduleName(this.value,true)"
                                                        style="width: 100%">
                                                    <option> <?= lang('none') ?> </option>
                                                    <option
                                                        value="leads" <?= (!empty($leads_id) ? 'selected' : '') ?>> <?= lang('leads') ?> </option>
                                                    <option
                                                        value="client" <?= (!empty($client_id) ? 'selected' : '') ?>> <?= lang('client') ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group" id="related_to">

                                        </div>
                                        <?php if (!empty($leads_id)): ?>
                                            <div class="form-group <?= $leads_id ? 'leads_module' : 'company' ?>"
                                                 id="border-none">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('leads') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-7">
                                                    <select name="leads_id" style="width: 100%"
                                                            class="select_box <?= $leads_id ? 'leads_module' : 'company' ?>"
                                                            required="1">
                                                        <?php
                                                        $all_leads_info = $this->db->get('tbl_leads')->result();
                                                        if (!empty($all_leads_info)) {
                                                            foreach ($all_leads_info as $v_leads) {
                                                                ?>
                                                                <option value="<?= $v_leads->leads_id ?>" <?php
                                                                if (!empty($leads_id)) {
                                                                    echo $v_leads->leads_id == $leads_id ? 'selected' : '';
                                                                }
                                                                ?>><?= $v_leads->lead_name ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group <?= $leads_id ? 'leads_module' : 'company' ?>"
                                                 id="border-none">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('currency') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-7">
                                                    <select name="currency" style="width: 100%"
                                                            class="select_box <?= $leads_id ? 'leads_module' : 'company' ?>"
                                                            required="1">
                                                        <?php
                                                        $all_currency = $this->db->get('tbl_currencies')->result();
                                                        foreach ($all_currency as $v_currency) {
                                                            ?>
                                                            <option
                                                                value="<?= $v_currency->code ?>" <?= (config_item('default_currency') == $v_currency->code ? ' selected="selected"' : '') ?>><?= $v_currency->name ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <?php if (!empty($client_id)): ?>
                                            <div class="form-group <?= $client_id ? 'client_module' : 'company' ?>"
                                                 id="border-none">
                                                <label for="field-1"
                                                       class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('client') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-7">
                                                    <select name="client_id" style="width: 100%"
                                                            class="select_box <?= $client_id ? 'client_module' : 'company' ?>"
                                                            required="1">
                                                        <?php
                                                        if (!empty($all_client)) {
                                                            foreach ($all_client as $v_client) {
                                                                ?>
                                                                <option value="<?= $v_client->client_id ?>"
                                                                    <?php
                                                                    if (!empty($client_id)) {
                                                                        echo $client_id == $v_client->client_id ? 'selected' : '';
                                                                    }
                                                                    ?>
                                                                ><?= $v_client->name ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php endif ?>

                                        <div class="form-group">
                                            <label for="discount_type"
                                                   class="control-label col-sm-3"><?= lang('discount_type') ?></label>
                                            <div class="col-sm-7">
                                                <select name="discount_type" class="selectpicker" data-width="100%">
                                                    <option value=""
                                                            selected><?php echo lang('no') . ' ' . lang('discount'); ?></option>
                                                    <option value="before_tax" <?php
                                                    if (isset($proposals_info)) {
                                                        if ($proposals_info->discount_type == 'before_tax') {
                                                            echo 'selected';
                                                        }
                                                    } ?>><?php echo lang('before_tax'); ?></option>
                                                    <option value="after_tax" <?php if (isset($proposals_info)) {
                                                        if ($proposals_info->discount_type == 'after_tax') {
                                                            echo 'selected';
                                                        }
                                                    } ?>><?php echo lang('after_tax'); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('status') ?> </label>
                                            <div class="col-lg-7">
                                                <select name="status" class="selectpicker" data-width="100%">
                                                    <option
                                                        value="draft"><?= lang('draft') ?></option>
                                                    <option
                                                        value="sent"><?= lang('sent') ?></option>
                                                    <option
                                                        value="open"><?= lang('open') ?></option>
                                                    <option
                                                        value="revised"><?= lang('revised') ?></option>
                                                    <option
                                                        value="declined"><?= lang('declined') ?></option>
                                                    <option
                                                        value="accepted"><?= lang('accepted') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-6 br pv">
                                    <div class="row">
                                        <div class="form-group">
                                            <label for="field-1"
                                                   class="col-sm-3 control-label"><?= lang('assigned') . ' ' . lang('users') ?></label>
                                            <div class="col-sm-7">
                                                <select class="form-control select_box" required style="width: 100%"
                                                        name="user_id">
                                                    <option
                                                        value=""><?= lang('select') . ' ' . lang('assigned') . ' ' . lang('users') ?></option>
                                                    <?php
                                                    $all_user = $this->db->where('role_id != ', 2)->get('tbl_users')->result();
                                                    if (!empty($all_user)) {
                                                        foreach ($all_user as $v_user) {
                                                            $profile_info = $this->db->where('user_id', $v_user->user_id)->get('tbl_account_details')->row();
                                                            if (!empty($profile_info)) {
                                                                ?>
                                                                <option value="<?= $v_user->user_id ?>"
                                                                    <?php
                                                                    if (!empty($proposals_info->user_id)) {
                                                                        echo $proposals_info->user_id == $v_user->user_id ? 'selected' : null;
                                                                    } else {
                                                                        echo $this->session->userdata('user_id') == $v_user->user_id ? 'selected' : null;
                                                                    }
                                                                    ?>
                                                                ><?= $profile_info->fullname ?></option>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label
                                                class="col-lg-3 control-label"><?= lang('proposal_date') ?></label>
                                            <div class="col-lg-7">
                                                <div class="input-group">
                                                    <input type="text" name="proposal_date"
                                                           class="form-control datepicker"
                                                           value="<?php
                                                           if (!empty($proposals_info->proposal_date)) {
                                                               echo $proposals_info->proposal_date;
                                                           } else {
                                                               echo date('Y-m-d');
                                                           }
                                                           ?>"
                                                           data-date-format="<?= config_item('date_picker_format'); ?>">
                                                    <div class="input-group-addon">
                                                        <a href="#"><i class="fa fa-calendar"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('expire_date') ?></label>
                                            <div class="col-lg-7">
                                                <div class="input-group">
                                                    <input type="text" name="due_date"
                                                           class="form-control datepicker"
                                                           value="<?php
                                                           if (!empty($proposals_info->due_date)) {
                                                               echo $proposals_info->due_date;
                                                           } else {
                                                               echo date('Y-m-d');
                                                           }
                                                           ?>"
                                                           data-date-format="<?= config_item('date_picker_format'); ?>">
                                                    <div class="input-group-addon">
                                                        <a href="#"><i class="fa fa-calendar"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        if (!empty($proposals_info)) {
                                            $proposals_id = $proposals_info->proposals_id;
                                        } else {
                                            $proposals_id = null;
                                        }
                                        ?>
                                        <?= custom_form_Fields(11, $proposals_id); ?>

                                    </div>
                                    <div class="form-group" id="border-none">
                                        <label for="field-1"
                                               class="col-sm-3 control-label"><?= lang('permission') ?> <span
                                                class="required">*</span></label>
                                        <div class="col-sm-9">
                                            <div class="checkbox c-radio needsclick">
                                                <label class="needsclick">
                                                    <input id="" <?php
                                                    if (!empty($proposals_info->permission) && $proposals_info->permission == 'all') {
                                                        echo 'checked';
                                                    } elseif (empty($proposals_info)) {
                                                        echo 'checked';
                                                    }
                                                    ?> type="radio" name="permission" value="everyone">
                                                    <span class="fa fa-circle"></span><?= lang('everyone') ?>
                                                    <i title="<?= lang('permission_for_all') ?>"
                                                       class="fa fa-question-circle" data-toggle="tooltip"
                                                       data-placement="top"></i>
                                                </label>
                                            </div>
                                            <div class="checkbox c-radio needsclick">
                                                <label class="needsclick">
                                                    <input id="" <?php
                                                    if (!empty($proposals_info->permission) && $proposals_info->permission != 'all') {
                                                        echo 'checked';
                                                    }
                                                    ?> type="radio" name="permission" value="custom_permission"
                                                    >
                                                        <span
                                                            class="fa fa-circle"></span><?= lang('custom_permission') ?>
                                                    <i
                                                        title="<?= lang('permission_for_customization') ?>"
                                                        class="fa fa-question-circle" data-toggle="tooltip"
                                                        data-placement="top"></i>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group <?php
                                    if (!empty($proposals_info->permission) && $proposals_info->permission != 'all') {
                                        echo 'show';
                                    }
                                    ?>" id="permission_user_1">
                                        <label for="field-1"
                                               class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('users') ?>
                                            <span
                                                class="required">*</span></label>
                                        <div class="col-sm-9">
                                            <?php
                                            if (!empty($permission_user)) {
                                                foreach ($permission_user as $key => $v_user) {

                                                    if ($v_user->role_id == 1) {
                                                        $role = '<strong class="badge btn-danger">' . lang('admin') . '</strong>';
                                                    } else {
                                                        $role = '<strong class="badge btn-primary">' . lang('staff') . '</strong>';
                                                    }

                                                    ?>
                                                    <div class="checkbox c-checkbox needsclick">
                                                        <label class="needsclick">
                                                            <input type="checkbox"
                                                                <?php
                                                                if (!empty($proposals_info->permission) && $proposals_info->permission != 'all') {
                                                                    $get_permission = json_decode($proposals_info->permission);
                                                                    foreach ($get_permission as $user_id => $v_permission) {
                                                                        if ($user_id == $v_user->user_id) {
                                                                            echo 'checked';
                                                                        }
                                                                    }

                                                                }
                                                                ?>
                                                                   value="<?= $v_user->user_id ?>"
                                                                   name="assigned_to[]"
                                                                   class="needsclick">
                                                        <span
                                                            class="fa fa-check"></span><?= $v_user->username . ' ' . $role ?>
                                                        </label>

                                                    </div>
                                                    <div class="action_1 p
                                                <?php

                                                    if (!empty($proposals_info->permission) && $proposals_info->permission != 'all') {
                                                        $get_permission = json_decode($proposals_info->permission);

                                                        foreach ($get_permission as $user_id => $v_permission) {
                                                            if ($user_id == $v_user->user_id) {
                                                                echo 'show';
                                                            }
                                                        }

                                                    }
                                                    ?>
                                                " id="action_1<?= $v_user->user_id ?>">
                                                        <label class="checkbox-inline c-checkbox">
                                                            <input id="<?= $v_user->user_id ?>" checked
                                                                   type="checkbox"
                                                                   name="action_1<?= $v_user->user_id ?>[]"
                                                                   disabled
                                                                   value="view">
                                                        <span
                                                            class="fa fa-check"></span><?= lang('can') . ' ' . lang('view') ?>
                                                        </label>
                                                        <label class="checkbox-inline c-checkbox">
                                                            <input id="<?= $v_user->user_id ?>"
                                                                <?php

                                                                if (!empty($proposals_info->permission) && $proposals_info->permission != 'all') {
                                                                    $get_permission = json_decode($proposals_info->permission);

                                                                    foreach ($get_permission as $user_id => $v_permission) {
                                                                        if ($user_id == $v_user->user_id) {
                                                                            if (in_array('edit', $v_permission)) {
                                                                                echo 'checked';
                                                                            };

                                                                        }
                                                                    }

                                                                }
                                                                ?>
                                                                   type="checkbox"
                                                                   value="edit"
                                                                   name="action_<?= $v_user->user_id ?>[]">
                                                        <span
                                                            class="fa fa-check"></span><?= lang('can') . ' ' . lang('edit') ?>
                                                        </label>
                                                        <label class="checkbox-inline c-checkbox">
                                                            <input id="<?= $v_user->user_id ?>"
                                                                <?php

                                                                if (!empty($proposals_info->permission) && $proposals_info->permission != 'all') {
                                                                    $get_permission = json_decode($proposals_info->permission);
                                                                    foreach ($get_permission as $user_id => $v_permission) {
                                                                        if ($user_id == $v_user->user_id) {
                                                                            if (in_array('delete', $v_permission)) {
                                                                                echo 'checked';
                                                                            };
                                                                        }
                                                                    }

                                                                }
                                                                ?>
                                                                   name="action_<?= $v_user->user_id ?>[]"
                                                                   type="checkbox"
                                                                   value="delete">
                                                        <span
                                                            class="fa fa-check"></span><?= lang('can') . ' ' . lang('delete') ?>
                                                        </label>
                                                        <input id="<?= $v_user->user_id ?>" type="hidden"
                                                               name="action_<?= $v_user->user_id ?>[]"
                                                               value="view">

                                                    </div>


                                                    <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group terms">
                                <label class="col-lg-1 control-label"><?= lang('notes') ?> </label>
                                <div class="col-lg-11">
                        <textarea name="notes" class="form-control textarea_"><?php
                            if (!empty($proposals_info)) {
                                echo $proposals_info->notes;
                            } else {
                                echo $this->config->item('proposal_terms');
                            }
                            ?></textarea>
                                </div>
                            </div>
                            <?php
                            if (!empty($proposals_info)) {
                                if ($proposals_info->module == 'client') {
                                    $client_info = $this->proposal_model->check_by(array('client_id' => $proposals_info->module_id), 'tbl_client');
                                    $currency = $this->proposal_model->client_currency_sambol($proposals_info->module_id);
                                    $client_lang = $client_info->language;
                                } else {
                                    $client_info = $this->proposal_model->check_by(array('leads_id' => $proposals_info->module_id), 'tbl_leads');
                                    if (!empty($client_info)) {
                                        $client_info->name = $client_info->lead_name;
                                        $client_info->zipcode = null;
                                    }
                                    $client_lang = 'english';
                                    $currency = $this->proposal_model->check_by(array('code' => $proposals_info->currency), 'tbl_currencies');
                                }
                            } else {
                                $client_lang = 'english';
                                $currency = $this->proposal_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                            }
                            unset($this->lang->is_loaded[5]);
                            $language_info = $this->lang->load('sales_lang', $client_lang, TRUE, FALSE, '', TRUE);
                            ?>

                            <style type="text/css">
                                .dropdown-menu > li > a {
                                    white-space: normal;
                                }

                                .dragger {
                                    background: url(../assets/img/dragger.png) 10px 32px no-repeat;
                                    cursor: pointer;
                                }

                                <?php if (!empty($proposals_info)) { ?>
                                .dragger {
                                    background: url(../../../../assets/img/dragger.png) 10px 32px no-repeat;
                                    cursor: pointer;
                                }

                                <?php }?>
                                .input-transparent {
                                    box-shadow: none;
                                    outline: 0;
                                    border: 0 !important;
                                    background: 0 0;
                                    padding: 3px;
                                }

                            </style>
                            <?php
                            $saved_items = $this->proposal_model->get_all_items();
                            ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <select name="item_select" class="selectpicker m0" data-width="100%"
                                                id="item_select"
                                                data-none-selected-text="<?php echo lang('add_items'); ?>"
                                                data-live-search="true">
                                            <option value=""></option>
                                            <?php
                                            if (!empty($saved_items)) {
                                                $saved_items = array_reverse($saved_items, true);
                                                foreach ($saved_items as $group_id => $v_saved_items) {
                                                    if ($group_id != 0) {
                                                        $group = $this->db->where('customer_group_id', $group_id)->get('tbl_customer_group')->row()->customer_group;
                                                    } else {
                                                        $group = '';
                                                    }
                                                    ?>
                                                    <optgroup data-group-id="<?php echo $group_id; ?>"
                                                              label="<?php echo $group; ?>">
                                                        <?php
                                                        if (!empty($v_saved_items)) {
                                                            foreach ($v_saved_items as $v_item) { ?>
                                                                <option
                                                                    value="<?php echo $v_item->saved_items_id; ?>"
                                                                    data-subtext="<?php echo strip_tags(mb_substr($v_item->item_desc, 0, 200)) . '...'; ?>">
                                                                    (<?= display_money($v_item->unit_cost, $currency->symbol); ?>
                                                                    ) <?php echo $v_item->item_name; ?></option>
                                                            <?php }
                                                        }
                                                        ?>
                                                    </optgroup>

                                                <?php } ?>
                                                <?php
                                                $item_created = can_action('39', 'created');
                                                if (!empty($item_created)) { ?>
                                                    <option data-divider="true"></option>
                                                    <option value="newitem"
                                                            data-content="<span class='text-info'><?php echo lang('new_item'); ?></span>"></option>
                                                <?php } ?>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-5 pull-right">
                                    <div class="form-group">
                                        <label
                                            class="col-sm-4 control-label"><?php echo lang('show_quantity_as'); ?></label>
                                        <div class="col-sm-8">
                                            <label class="radio-inline c-radio">
                                                <input type="radio" value="qty" id="<?php echo lang('qty'); ?>"
                                                       name="show_quantity_as"
                                                    <?php if (isset($proposals_info) && $proposals_info->show_quantity_as == 'qty') {
                                                        echo 'checked';
                                                    } else if (!isset($hours_quantity) && !isset($qty_hrs_quantity)) {
                                                        echo 'checked';
                                                    } ?>>
                                                <span class="fa fa-circle"></span><?php echo lang('qty'); ?>
                                            </label>
                                            <label class="radio-inline c-radio">
                                                <input type="radio" value="hours" id="<?php echo lang('hours'); ?>"
                                                       name="show_quantity_as" <?php if (isset($proposals_info) && $proposals_info->show_quantity_as == 'hours' || isset($hours_quantity)) {
                                                    echo 'checked';
                                                } ?>>
                                                <span class="fa fa-circle"></span><?php echo lang('hours'); ?>
                                            </label>
                                            <label class="radio-inline c-radio">
                                                <input type="radio" value="qty_hours"
                                                       id="<?php echo lang('qty') . '/' . lang('hours'); ?>"
                                                       name="show_quantity_as"
                                                    <?php if (isset($proposals_info) && $proposals_info->show_quantity_as == 'qty_hours' || isset($qty_hrs_quantity)) {
                                                        echo 'checked';
                                                    } ?>>
                                                <span
                                                    class="fa fa-circle"></span><?php echo lang('qty') . '/' . lang('hours'); ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive s_table">
                                    <table class="table invoice-items-table items">
                                        <thead style="background: #e8e8e8">
                                        <tr>
                                            <th></th>
                                            <th><?= $language_info['item_name'] ?></th>
                                            <th><?= $language_info['description'] ?></th>
                                            <?php
                                            $qty_heading = $language_info['qty'];
                                            if (isset($proposals_info) && $proposals_info->show_quantity_as == 'hours' || isset($hours_quantity)) {
                                                $qty_heading = lang('hours');
                                            } else if (isset($proposals_info) && $proposals_info->show_quantity_as == 'qty_hours') {
                                                $qty_heading = lang('qty') . '/' . lang('hours');
                                            }
                                            ?>
                                            <th class="qty col-sm-1"><?php echo $qty_heading; ?></th>
                                            <th class="col-sm-2"><?= $language_info['price'] ?></th>
                                            <th class="col-sm-2"><?= $language_info['tax_rate'] ?> </th>
                                            <th class="col-sm-1"><?= $language_info['total'] ?></th>
                                            <th class="col-sm-1 hidden-print"><?= $language_info['action'] ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (isset($proposals_info)) {
                                            echo form_hidden('merge_current_invoice', $proposals_info->proposals_id);
                                            echo form_hidden('isedit', $proposals_info->proposals_id);
                                        }
                                        ?>
                                        <tr class="main">
                                            <td></td>
                                            <td>
                        <textarea name="item_name" class="form-control"
                                  placeholder="<?php echo lang('item_name'); ?>"></textarea>
                                            </td>
                                            <td>
                        <textarea name="item_desc" class="form-control"
                                  placeholder="<?php echo lang('description'); ?>"></textarea>
                                            </td>
                                            <td>
                                                <input type="number" name="quantity" step="0.01" min="0" value="1"
                                                       class="form-control"
                                                       placeholder="<?php echo lang('qty'); ?>">
                                                <input type="text"
                                                       placeholder="<?php echo lang('unit') . ' ' . lang('type'); ?>"
                                                       name="unit"
                                                       class="form-control input-transparent">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="unit_cost" class="form-control"
                                                       placeholder="<?php echo lang('price'); ?>">
                                            </td>
                                            <td>
                                                <?php
                                                $taxes = $this->db->order_by('tax_rate_percent', 'ASC')->get('tbl_tax_rates')->result();
                                                $default_tax = config_item('default_tax');
                                                if (!is_numeric($default_tax)) {
                                                    $default_tax = unserialize($default_tax);
                                                }
                                                $select = '<select class="selectpicker tax main-tax" data-width="100%" name="taxname" multiple data-none-selected-text="' . lang('no_tax') . '">';
                                                foreach ($taxes as $tax) {
                                                    $selected = '';
                                                    if (!empty($default_tax) && is_array($default_tax)) {
                                                        if (in_array($tax->tax_rates_id, $default_tax)) {
                                                            $selected = ' selected ';
                                                        }
                                                    }
                                                    $select .= '<option value="' . $tax->tax_rate_name . '|' . $tax->tax_rate_percent . '"' . $selected . 'data-taxrate="' . $tax->tax_rate_percent . '" data-taxname="' . $tax->tax_rate_name . '" data-subtext="' . $tax->tax_rate_name . '">' . $tax->tax_rate_percent . '%</option>';
                                                }
                                                $select .= '</select>';
                                                echo $select;
                                                ?>
                                            </td>
                                            <td></td>
                                            <td>
                                                <?php
                                                $new_item = 'undefined';
                                                if (isset($proposals_info)) {
                                                    $new_item = true;
                                                }
                                                ?>
                                                <button type="button"
                                                        onclick="add_item_to_table('undefined','undefined',<?php echo $new_item; ?>); return false;"
                                                        class="btn-xs btn btn-info"><i class="fa fa-check"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php if (isset($proposals_info) || isset($add_items)) {
                                            $i = 1;
                                            $items_indicator = 'items';
                                            if (isset($proposals_info)) {
                                                $add_items = $this->proposal_model->ordered_items_by_id($proposals_info->proposals_id);
                                                $items_indicator = 'items';
                                            }
                                            foreach ($add_items as $item) {
                                                $manual = false;
                                                $table_row = '<tr class="sortable item">';
                                                $table_row .= '<td class="dragger">';
                                                if (!is_numeric($item->quantity)) {
                                                    $item->quantity = 1;
                                                }
                                                $invoice_item_taxes = $this->proposal_model->get_invoice_item_taxes($item->proposals_items_id, 'proposal');

                                                // passed like string
                                                if ($item->proposals_items_id == 0) {
                                                    $invoice_item_taxes = $invoice_item_taxes[0];
                                                    $manual = true;
                                                }
                                                $table_row .= form_hidden('' . $items_indicator . '[' . $i . '][proposals_items_id]', $item->proposals_items_id);
                                                $amount = $item->unit_cost * $item->quantity;
                                                $amount = ($amount);
                                                // order input
                                                $table_row .= '<input type="hidden" class="order" name="' . $items_indicator . '[' . $i . '][order]"><input type="hidden" name="proposals_items_id[]" value="' . $item->proposals_items_id . '">';
                                                $table_row .= '</td>';
                                                $table_row .= '<td class="bold item_name"><textarea name="' . $items_indicator . '[' . $i . '][item_name]" class="form-control">' . $item->item_name . '</textarea></td>';
                                                $table_row .= '<td><textarea name="' . $items_indicator . '[' . $i . '][item_desc]" class="form-control" >' . $item->item_desc . '</textarea></td>';
                                                $table_row .= '<td><input type="number" step="0.01" min="0" onblur="calculate_total();" onchange="calculate_total();" data-quantity name="' . $items_indicator . '[' . $i . '][quantity]" value="' . $item->quantity . '" class="form-control">';
                                                $unit_placeholder = '';
                                                if (!$item->unit) {
                                                    $unit_placeholder = lang('unit');
                                                    $item->unit = '';
                                                }
                                                $table_row .= '<input type="text" placeholder="' . $unit_placeholder . '" name="' . $items_indicator . '[' . $i . '][unit]" class="form-control input-transparent text-right" value="' . $item->unit . '">';
                                                $table_row .= '</td>';
                                                $table_row .= '<td class="rate"><input type="text" data-toggle="tooltip"  onblur="calculate_total();" onchange="calculate_total();" name="' . $items_indicator . '[' . $i . '][unit_cost]" value="' . $item->unit_cost . '" class="form-control"></td>';
                                                $table_row .= '<td class="taxrate">' . $this->admin_model->get_taxes_dropdown('' . $items_indicator . '[' . $i . '][taxname][]', $invoice_item_taxes, 'invoice', $item->proposals_items_id, true, $manual) . '</td>';
                                                $table_row .= '<td class="amount">' . $amount . '</td>';
                                                $table_row .= '<td><a href="#" class="btn-xs btn btn-danger pull-left" onclick="delete_item(this,' . $item->proposals_items_id . '); return false;"><i class="fa fa-trash"></i></a></td>';
                                                $table_row .= '</tr>';
                                                echo $table_row;
                                                $i++;
                                            }
                                        }
                                        ?>

                                        </tbody>
                                    </table>
                                </div>

                                <div class="row">
                                    <div class="col-xs-8 pull-right">
                                        <table class="table text-right">
                                            <tbody>
                                            <tr id="subtotal">
                                                <td><span class="bold"><?php echo lang('sub_total'); ?> :</span>
                                                </td>
                                                <td class="subtotal">
                                                </td>
                                            </tr>
                                            <tr id="discount_percent">
                                                <td>
                                                    <div class="row">
                                                        <div class="col-md-7">
                                                            <span class="bold"><?php echo lang('discount'); ?>
                                                                (%)</span>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <?php
                                                            $discount_percent = 0;
                                                            if (isset($proposals_info)) {
                                                                if ($proposals_info->discount_percent != 0) {
                                                                    $discount_percent = $proposals_info->discount_percent;
                                                                }
                                                            }
                                                            ?>
                                                            <input type="number"
                                                                   value="<?php echo $discount_percent; ?>"
                                                                   class="form-control pull-left" min="0" max="100"
                                                                   name="discount_percent">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="discount_percent"></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="row">
                                                        <div class="col-md-7">
                                                                <span
                                                                    class="bold"><?php echo lang('adjustment'); ?></span>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="number"
                                                                   value="<?php if (isset($proposals_info)) {
                                                                       echo $proposals_info->adjustment;
                                                                   } else {
                                                                       echo 0;
                                                                   } ?>" class="form-control pull-left"
                                                                   name="adjustment">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="adjustment"></td>
                                            </tr>
                                            <tr>
                                                <td><span class="bold"><?php echo lang('total'); ?> :</span>
                                                </td>
                                                <td class="total">
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div id="removed-items"></div>
                                <div class="modal-footer">
                                    <div class="col-sm-5 pull-right row">
                                        <input type="submit" value="<?= lang('update') ?>" name="update"
                                               class="btn btn-primary btn-block">
                                    </div>
                                </div>
                            </div>

</form>
<?php } else { ?>
    </div>
<?php } ?>
</div>
<script type="text/javascript">
    function slideToggle($id) {
        $('#quick_state').attr('data-original-title', '<?= lang('view_quick_state') ?>');
        $($id).slideToggle("slow");
    }
    $(document).ready(function () {
        init_items_sortable();
    });
</script>
