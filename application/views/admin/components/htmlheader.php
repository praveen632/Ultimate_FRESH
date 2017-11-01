<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="description"
          content="attendance, client management, finance, freelance, freelancer, goal tracking, Income Managment, lead management, payroll, project management, project manager, support ticket, task management, timecard">
    <meta name="keywords"
          content="	attendance, client management, finance, freelance, freelancer, goal tracking, Income Managment, lead management, payroll, project management, project manager, support ticket, task management, timecard">
    <title><?php echo $title; ?></title>
    <!-- =============== VENDOR STYLES ===============-->
    <!-- FONT AWESOME-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/fontawesome/css/font-awesome.min.css">
    <!-- SIMPLE LINE ICONS-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/simple-line-icons/css/simple-line-icons.css">
    <!-- ANIMATE.CSS-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/animate.css/animate.min.css">
    <!-- WHIRL (spinners)-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/whirl/dist/whirl.css">
    <!-- =============== PAGE VENDOR STYLES ===============-->

    <!-- =============== APP STYLES ===============-->
    <?php $direction = $this->session->userdata('direction');
    if (!empty($direction) && $direction == 'rtl') {
        $RTL = 'on';
    } else {
        $RTL = config_item('RTL');
    }

    ?>
    <?php
    if (!empty($RTL)) {
        ?>
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-rtl.css" id="bscss">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/app-rtl.css" id="maincss">
    <?php } else {
        ?>
        <!-- =============== BOOTSTRAP STYLES ===============-->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.css" id="bscss">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/app.css" id="maincss">
    <?php }
    $custom_color = config_item('active_custom_color');
    if (!empty($custom_color) && $custom_color == 1) {
        include_once 'assets/css/bg-custom.php';
    } else {
        ?>
        <link id="autoloaded-stylesheet" rel="stylesheet"
              href="<?php echo base_url(); ?>assets/css/<?= config_item('sidebar_theme') ?>.css">
    <?php }
    ?>


    <!-- SELECT2-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/select2/dist/css/select2.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/select2/dist/css/select2-bootstrap.css">
    <!-- Datepicker-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/datepicker.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/timepicker.css">
    <!-- Toastr-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/toastr.min.css">
    <!-- Data Table  CSS -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/dataTables/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/dataTables/css/dataTables.colVis.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/dataTables/css/dataTables.bootstrap.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/dataTables/css/responsive.dataTables.min.css">
    <!-- summernote Editor -->
    <link href="<?php echo base_url(); ?>assets/plugins/summernote/summernote.min.css" rel="stylesheet" type="text/css">
    <!-- bootstrap-slider -->
    <link href="<?php echo base_url() ?>assets/plugins/bootstrap-slider/bootstrap-slider.min.css" rel="stylesheet">
    <!-- chartist -->
    <link href="<?php echo base_url() ?>assets/plugins/morris/morris.css" rel="stylesheet">
    <!----- bootstrap-select ---->
    <link href="<?php echo base_url() ?>assets/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet">
    <!-- bootstrap-editable -->
    <!--    <link href="-->
    <?php //echo base_url() ?><!--assets/plugins/bootstrap-editable/bootstrap-editable.css" rel="stylesheet"-->
    <!--          type="text/css">-->
    <!-- JQUERY-->
    <script src="<?= base_url() ?>assets/js/jquery.min.js"></script>
    <link href="<?php echo base_url() ?>asset/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="<?php echo base_url() ?>asset/js/bootstrap-toggle.min.js"></script>
    <script>
        var total_unread_notifications = <?php echo $unread_notifications; ?>,
            autocheck_notifications_timer_id = 0,
            base_url = "<?php echo base_url(); ?>",
            new_notification = "<?php lang('new_notification'); ?>",
            auto_check_for_new_notifications = <?php echo config_item('auto_check_for_new_notifications'); ?>,
            desktop_notifications = <?php echo config_item('desktop_notifications'); ?>;
    </script>

</head>
