 <?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<style>
    .note-editor .note-editable {
        height: 150px;
    }
</style>


                <style>
                    .tooltip-inner {
                        white-space: pre-wrap;
                    }
                </style>
                <div class="nav-tabs-custom">
                    <!-- Tabs within a box -->
                    <ul class="nav nav-tabs">
                        <li class=""><a href="#general" data-toggle="tab" id="gen_fun"><?= lang('timesheet') ?></a>
                        </li>
                        <li class=""><a href="#contact" data-toggle="tab" id="con_fun"><?= lang('manual_entry') ?></a>
                        </li>
                    </ul>
                    <div class="tab-content bg-white">
                        <!-- ************** general *************-->
                        <div class="" id="general">
                            <div class="table-responsive">
                                <table id="table-tasks-timelog" class="table table-striped     DataTables">
                                    <thead>
                                    <tr>
                                        <th><?= lang('user') ?></th>
                                        <th><?= lang('start_time') ?></th>
                                        <th><?= lang('stop_time') ?></th>

                                        <th><?= lang('project_name') ?></th>
                                        <th class="col-time"><?= lang('time_spend') ?></th>
                                         <th><?= lang('action') ?></th>
                                       
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php

                                    if (!empty($total_timer)) {
 
                                        foreach ($total_timer as $v_timer) {
                                          //print_r($v_timer);die;
                                            $aproject_info = $this->db->where(array('project_id' => $v_timer->project_id))->get('tbl_project')->row();
                                            if (!empty($aproject_info)) {
                                                if ($v_timer->start_time != 0 && $v_timer->end_time != 0) {
                                                    ?>
                                                    <tr>
                                                        <td class="small">

                                                            <a class="pull-left recect_task  ">
                                                                <?php
                                                                $profile_info = $this->db->where(array('user_id' => $v_timer->user_id))->get('tbl_account_details')->row();
                                                                $user_info = $this->db->where(array('user_id' => $v_timer->user_id))->get('tbl_users')->row();
                                                                if (!empty($user_info)) {
                                                                    ?>
                                                                    <img style="width: 30px;margin-left: 18px;
                                                                             height: 29px;
                                                                             border: 1px solid #aaa;"
                                                                         src="<?= base_url() . $profile_info->avatar ?>"
                                                                         class="img-circle">
                                                                <?php } else {
                                                                    echo '-';
                                                                } ?>
                                                            </a>


                                                        </td>

                                                        <td><span
                                                                class="label label-success"><?= strftime(config_item('date_format') . ' %H:%M', $v_timer->start_time) ?></span>
                                                        </td>
                                                        <td><span
                                                                class="label label-danger"><?= strftime(config_item('date_format') . ' %H:%M', $v_timer->end_time) ?></span>
                                                        </td>

                                                        <td>
                                                            <?php 
                                                            $name = $this->db->where(array('project_id' => $v_timer->project_id))->get('tbl_project')->row();
                                                           
                                                            ?>

                                                            <a href="<?= base_url() ?>admin/projects/project_details/<?= $v_timer->project_id . "#timesheet" ?>"
                                                               class="text-info small"><?php echo $name->project_name ?>
                                                                <?php
                                                                if (!empty($v_timer->reason)) {
                                                                    $edit_user_info = $this->db->where(array('user_id' => $v_timer->edited_by))->get('tbl_users')->row();
                                                                    echo '<i class="text-danger" data-html="true" data-toggle="tooltip" data-placement="top" title="Reason : ' . $v_timer->reason . '<br>' . ' Edited By : ' . $edit_user_info->username . '">Edited</i>';
                                                                }
                                                                ?>
                                                            </a></td>
                                                        <td>
                                                            <small
                                                                class="small text-muted"><?= $this->items_model->get_time_spent_result($v_timer->end_time - $v_timer->start_time) ?></small>
                                                        </td>
                                                        <td>
                                                            <?= btn_edit('admin/projects/project_details/' . $v_timer->project_id . '/7/' . $v_timer->tasks_timer_id) ?>
                                                            <?= btn_delete('admin/projects/update_project_timer/' . $v_timer->tasks_timer_id . '/delete_task_timmer') ?>
                                                        </td>
                                                       

                                                    </tr>
                                                    <?php
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="" id="contact">
                            <form role="form" enctype="multipart/form-data" id="form"
                                  action="<?php echo base_url(); ?>admin/timesheets/update_time_timer/<?php
                                  if (!empty($project_timer_info)) {
                                      echo $project_timer_info->tasks_timer_id;
                                  }
                                  ?>" method="post" class="form-horizontal">
                                <?php
                                if (!empty($project_timer_info)) {
                                    $start_date = date('Y-m-d', $project_timer_info->start_time);
                                    $start_time = date('H:i', $project_timer_info->start_time);
                                    $end_date = date('Y-m-d', $project_timer_info->end_time);
                                    $end_time = date('H:i', $project_timer_info->end_time);
                                } else {
                                    $start_date = '';
                                    $start_time = '';
                                    $end_date = '';
                                    $end_time = '';
                                }
                                ?>
                                <?php if ($this->session->userdata('user_type') == '1' && empty($project_timer_info->tasks_timer_id)) { ?>
                                    <div class="form-group margin">
                                        <div class="col-sm-8 center-block">
                                            <label
                                                class="control-label"><?= lang('select') . ' ' . lang('project') ?>
                                                <span
                                                    class="required">*</span></label>
                                            <select class="form-control select_box" name="project_id"
                                                    required="" style="width: 100%">
                                                <?php
                                                $all_tasks_info = $this->db->get('tbl_project')->result();
                                                if (!empty($all_tasks_info)):foreach ($all_tasks_info as $v_task_info):
                                                    ?>
                                                    <option
                                                        value="<?= $v_task_info->project_id ?>" <?= $v_task_info->project_id == $v_timer->project_id ? 'selected' : null ?>><?= $v_task_info->project_name ?></option>
                                                <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <input type="hidden" name="project_id"
                                           value="<?= $project_details->project_id ?>">
                                <?php } ?>
                                <div class="form-group margin">
                                    <div class="col-sm-4">
                                        <label class="control-label"><?= lang('start_date') ?> </label>
                                        <div class="input-group">
                                            <input type="text" name="start_date"
                                                   class="form-control datepicker"
                                                   value="<?= $start_date ?>"
                                                   data-date-format="<?= config_item('date_picker_format'); ?>">
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-calendar"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="control-label"><?= lang('start_time') ?></label>
                                        <div class="input-group">
                                            <input type="text" name="start_time"
                                                   class="form-control timepicker2"
                                                   value="<?= $start_time ?>">
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-clock-o"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group margin">
                                    <div class="col-sm-4">
                                        <label class="control-label"><?= lang('end_date') ?></label>
                                        <div class="input-group">
                                            <input type="text" name="end_date"
                                                   class="form-control datepicker" value="<?= $end_date ?>"
                                                   data-date-format="<?= config_item('date_picker_format'); ?>">
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-calendar"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="control-label"><?= lang('end_time') ?></label>
                                        <div class="input-group">
                                            <input type="text" name="end_time"
                                                   class="form-control timepicker2"
                                                   value="<?= $end_time ?>">
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-clock-o"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group margin">
                                    <div class="col-sm-8 center-block">
                                        <label class="control-label"><?= lang('edit_reason') ?> <span
                                                class="required">*</span></label>
                                        <div>
                                                <textarea class="form-control" name="reason" required="" rows="6"><?php
                                                    if (!empty($project_timer_info)) {
                                                        echo $project_timer_info->reason;
                                                    }
                                                    ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" style="margin-top: 20px;">
                                    <div class="col-lg-6">
                                        <button type="submit"
                                                class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>


                    </div>
                </div>
            </div>
        <script type="text/javascript">
            $("#contact").hide();
            $("#con_fun").click(function(){
                   $("#general").hide();
                   $("#contact").show();
                });

            $("#gen_fun").click(function(){
                   $("#general").show();
                   $("#contact").hide();
                });
        </script>