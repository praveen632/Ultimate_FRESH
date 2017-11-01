<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title"
            id="myModalLabel"><?= lang('change_status') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <form id="form" role="form" enctype="multipart/form-data"
              action="<?php echo base_url() ?>admin/job_circular/change_application_status/<?php
              if (!empty($job_application_info->job_appliactions_id)) {
                  echo $job_application_info->job_appliactions_id;
              }
              ?>" method="post" class="form-horizontal form-groups-bordered">

            <div class="form-group" id="border-none">
                <label for="field-1" class="col-sm-3 control-label"><?= lang('status') ?><span
                        class="required"> *</span></label>
                <div class="col-sm-5">
                    <select class="form-control" name="status">
                        <option
                            value="0" <?= ($job_application_info->application_status == 0 ? 'selected' : '') ?> ><?= lang('unread') ?></option>
                        <option
                            value="2" <?= ($job_application_info->application_status == 2 ? 'selected' : '') ?>><?= lang('primary_selected') ?></option>
                        <option
                            value="3" <?= ($job_application_info->application_status == 3 ? 'selected' : '') ?>><?= lang('call_for_interview') ?></option>
                        <option
                            value="1" <?= ($job_application_info->application_status == 1 ? 'selected' : '') ?>><?= lang('approved') ?></option>
                        <option
                            value="4" <?= ($job_application_info->application_status == 4 ? 'selected' : '') ?>><?= lang('rejected') ?></option>
                    </select>
                </div>
            </div>
            <input type="hidden" name="flag" value="1"/>
            <div class="form-group">
                <div class="col-sm-3"></div>
                <div class="col-sm-2">
                    <button type="submit" class="btn btn-primary btn-block"><?= lang('update') ?></button>
                </div>
            </div>
        </form>
    </div>
</div>



