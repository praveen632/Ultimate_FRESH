<div class="pusher"></div>
<!-- ===============  SCRIPTS ===============-->

<!-- MODERNIZR-->

<script src="<?= base_url() ?>assets/plugins/modernizr/modernizr.custom.js"></script>
<!-- BOOTSTRAP-->
<script src="<?= base_url() ?>assets/plugins/bootstrap/dist/js/bootstrap.js"></script>
<!-- STORAGE API-->
<script src="<?= base_url() ?>assets/plugins/jQuery-Storage-API/jquery.storageapi.js"></script>
<!-- JQUERY EASING-->
<script src="<?= base_url() ?>assets/plugins/jquery.easing/js/jquery.easing.js"></script>
<!-- SCREENFULL-->
<script src="<?= base_url() ?>assets/plugins/screenfull/screenfull.js"></script>
<!-- ANIMO-->
<script src="<?= base_url() ?>assets/plugins/animo.js/animo.js"></script>
<!-- LOCALIZE-->
<script src="<?= base_url() ?>assets/plugins/jquery-localize-i18n/dist/jquery.localize.js"></script>
<!-- SELECT2-->
<script src="<?= base_url() ?>assets/plugins/select2/dist/js/select2.js"></script>
<!-- Data Table -->
<?php include_once 'assets/plugins/dataTables/js/jquery.dataTables.min.php'; ?>


<script src="<?php echo base_url('assets/plugins/tinymce/tinymce.min.js'); ?>"></script>

<script src="<?php echo base_url(); ?>assets/plugins/dataTables/js/dataTables.buttons.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/dataTables/js/buttons.print.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/dataTables/js/buttons.colVis.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/dataTables/js/jszip.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/dataTables/js/pdfmake.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/dataTables/js/vfs_fonts.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/dataTables/js/buttons.html5.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/dataTables/js/dataTables.select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/dataTables/js/dataTables.responsive.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/dataTables/js/dataTables.bootstrap.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/dataTables/js/dataTables.bootstrapPagination.js"></script>
<!-- summernote Editor -->
<script src="<?php echo base_url() ?>assets/plugins/summernote/summernote.min.js"></script>
<!-- =============== Date and time picker ===============-->

<?php include_once 'assets/js/bootstrap-datepicker.php'; ?>

<script src="<?= base_url() ?>assets/js/timepicker.js"></script>
<!-- bootstrap-slider -->
<script src="<?php echo base_url() ?>assets/plugins/bootstrap-slider/bootstrap-slider.min.js"></script>

<!-- bootstrap-editable -->
<script src="<?php echo base_url() ?>assets/plugins/bootstrap-editable/bootstrap-editable.min.js"></script>

<!-- jquery-classyloader -->
<script src="<?php echo base_url() ?>assets/plugins/jquery-classyloader/jquery.classyloader.min.js"></script>
<!-- =============== Toastr ===============-->
<script src="<?= base_url() ?>assets/js/toastr.min.js"></script>
<!-- =============== Toastr ===============-->
<script src="<?= base_url() ?>assets/js/jasny-bootstrap.min.js"></script>
<!-- EASY PIE CHART-->
<script src="<?php echo base_url() ?>assets/plugins/easy-pie-chart/jquery.easypiechart.js"></script>
<!-- morris CHART-->
<script src="<?php echo base_url() ?>assets/plugins/raphael/raphael.js"></script>
<script src="<?php echo base_url() ?>assets/plugins/morris/morris.js"></script>
<!-- sparkline CHART-->
<script src="<?php echo base_url() ?>assets/plugins/sparkline/index.js"></script>

<script src="<?php echo base_url() ?>assets/plugins/parsleyjs/parsley.min.js"></script>

<!----- bootstrap-select ---->
<link href="<?php echo base_url() ?>assets/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet">
<script src="<?php echo base_url() ?>assets/plugins/bootstrap-select/bootstrap-select.min.js"></script>

<script src="<?php echo base_url() ?>assets/plugins/push_notification/push_notification.js"></script>
<?php
$realtime_notification = config_item('realtime_notification');
if (!empty($realtime_notification)) { ?>
    <!--    <script src="--><?php //echo base_url() ?><!--assets/plugins/pusher/pusher.min.js"></script>-->
    <script src="https://js.pusher.com/4.1/pusher.min.js"></script>
    <script type="text/javascript">
        // Enable pusher logging - don't include this in production
        // Pusher.logToConsole = true;
        <?php $pusher_options = do_action('pusher_options', array());
        if (!isset($pusher_options['cluster']) && config_item('pusher_cluster') != '') {
            $pusher_options['cluster'] = config_item('pusher_cluster');
        } ?>
        var pusher_options = <?php echo json_encode($pusher_options); ?>;
        var pusher = new Pusher("<?php echo config_item('pusher_app_key'); ?>", pusher_options);
        var channel = pusher.subscribe('notifications-channel-<?php echo $this->session->userdata('user_id'); ?>');
        channel.bind('notification', function (data) {
            fetch_notifications();
        });
    </script>
<?php } ?>
<!-- =============== APP SCRIPTS ===============-->
<script src="<?= base_url() ?>assets/js/app.js"></script>
<?php include_once 'assets/plugins/dataTables/js/dataTables.php'; ?>


</body>

</html>
