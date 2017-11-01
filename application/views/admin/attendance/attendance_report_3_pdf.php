<!DOCTYPE html>
<html>
<head>
    <title><?php
        if (!empty($title)) {
            echo $title;
        } else {
            config_item('company_name');
        }
        ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style type="text/css">
        .table_tr1 {
            width: 100%;
        }

        .table_tr1 .th {
            border-bottom: 1px solid #aaaaaa;
            background-color: #dddddd;
            font-size: 12px;
        }

        .table_tr2 th, .table_tr3 th, .table_tr1 .th, .table_tr3 td {
            padding: 3px 0px 3px 5px;

        }
        .table_tr3 th {
            border-bottom: 1px solid #aaaaaa;

        }

        .table_tr3 td {
            border-bottom: 1px solid #dad3d3;
            font-size: 12px;
        }

        .table_tr3 .td {
            font-size: 13px;
            background: #dee0e4;
        }
        .th3{
            font-size: 13px;
            text-align: left;
        }
    </style>
</head>
<body style="min-width: 100%; min-height: 100%; ; alignment-adjust: central;">
<br/>
<div style="width: 100%; border-bottom: 2px solid black;">
    <table style="width: 100%; vertical-align: middle;">
        <tr>
            <td style="width: 50px; border: 0px;">
                <img style="width: 50px;height: 50px;margin-bottom: 5px;"
                     src="<?= base_url() . config_item('company_logo') ?>" alt="" class="img-circle"/>
            </td>

            <td style="border: 0px;">
                <p style="margin-left: 10px; font: 14px lighter;"><?= config_item('company_name') ?></p>
            </td>
        </tr>
    </table>
</div>
<br/>
<div style="width: 100%;">
    <div style="width: 100%; background-color: rgb(224, 224, 224); padding: 1px 0px 5px 15px;">
        <table style="width: 100%;">
            <tr style="font-size: 20px;  text-align: center">
                <td style="padding: 10px;">
                    <strong><?= lang('works_hours_deatils') . ' ' ?><?php echo $month; ?></strong>
                    <p><strong><?= lang('department') . ' : ' . $dept_name->deptname ?></strong></p>
                </td>
            </tr>
        </table>
    </div>
    <br/>
    <?php if (!empty($attendace_info)) { ?>
        <table class="table_tr1">
            <?php foreach ($attendace_info as $name => $v_attendace_info) { ?>
                <tr>
                <th class="th"><?php echo $employee[$name]->fullname; ?></th>
                <?php if (!empty($v_attendace_info)) {
                    foreach ($v_attendace_info as $week => $v_attendace) {
                        $total_hour = 0;
                        $total_minutes = 0;
                        ?>
                        <tr>
                            <th style="width: 100%;padding: 10px;"><?= lang('week') ?>
                                : <?php echo $week; ?>
                                <table class="table_tr3" style="width: 100%;">
                                    <tr >
                                        <th class="th3"><?= lang('clock_in_time') ?></th>
                                        <th class="th3"><?= lang('clock_out_time') ?></th>
                                        <th class="th3"><?= lang('ip_address') ?></th>
                                        <th class="th3"><?= lang('hours') ?></th>
                                    </tr>
                                    <?php
                                    $total_hh = 0;
                                    $total_mm = 0;
                                    if (!empty($v_attendace)) {
                                        foreach ($v_attendace as $key => $v_mytime) { ?>
                                            <tr>
                                                <td class="td" colspan="3"
                                                    style="font-weight: bold"><?php echo $key; ?></td>
                                            </tr>
                                            <?php
                                            foreach ($v_mytime as $mytime) {
                                                if ($mytime->attendance_status == 1) {
                                                    ?>
                                                    <tr>
                                                    <td><?php echo date('h:i A', strtotime($mytime->clockin_time)); ?></td>
                                                    <td><?php
                                                        if (empty($mytime->clockout_time)) {
                                                            echo '<span class="text-danger">' . lang('currently_clock_in') . '<span>';
                                                        } else {
                                                            if (!empty($mytime->comments)) {
                                                                $comments = ' <small> (' . $mytime->comments . ')</small>';
                                                            } else {
                                                                $comments = '';
                                                            }
                                                            echo date('h:i A', strtotime($mytime->clockout_time)) . $comments;
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?= $mytime->ip_address?></td>
                                                    <td><?php
                                                        if (!empty($mytime->clockout_time)) {
                                                            // calculate the start timestamp
                                                            $startdatetime = strtotime($mytime->date_in . " " . $mytime->clockin_time);
                                                            // calculate the end timestamp
                                                            $enddatetime = strtotime($mytime->date_out . " " . $mytime->clockout_time);
                                                            // calulate the difference in seconds
                                                            $difference = $enddatetime - $startdatetime;

                                                            $years = abs(floor($difference / 31536000));
                                                            $days = abs(floor(($difference - ($years * 31536000)) / 86400));
                                                            $hours = abs(floor(($difference - ($years * 31536000) - ($days * 86400)) / 3600));
                                                            $mins = abs(floor(($difference - ($years * 31536000) - ($days * 86400) - ($hours * 3600)) / 60));#floor($difference / 60);
                                                            $total_mm += $mins;
                                                            $total_hh += $hours;
                                                            echo $hours . " : " . $mins . " m";
                                                            // output the result
                                                        }
                                                        ?></td>
                                                <?php } elseif ($mytime->attendance_status == 'H') { ?>
                                                    <tr>
                                                        <td colspan="4" style="text-align: center">
                                                                            <span
                                                                                style="padding:5px 109px; font-size: 12px;"
                                                                                class="label label-info std_p"><?= lang('holiday') ?></span>
                                                        </td>
                                                    </tr>
                                                <?php } elseif ($mytime->attendance_status == '3') { ?>
                                                    <tr>
                                                        <td colspan="4" style="text-align: center">
                                                                            <span
                                                                                style="padding:5px 109px; font-size: 12px;"
                                                                                class="label label-warning std_p"><?= lang('on_leave') ?></span>
                                                        </td>
                                                    </tr>
                                                <?php } elseif ($mytime->attendance_status == '0') { ?>
                                                    <tr style="">
                                                        <td colspan="4" style="text-align: center">
                                                                            <span
                                                                                style="padding:5px 109px; font-size: 12px;"
                                                                                class="label label-danger std_p"><?= lang('absent') ?></span>
                                                        </td>
                                                    </tr>
                                                <?php } else { ?>
                                                    <tr>
                                                        <td colspan="4" style="text-align: center">
                                                                            <span style=" font-size: 12px;"
                                                                                  class=" std_p"><?= lang('no_data_available') ?> </span>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php }; ?>

                                        <?php } ?>
                                        <tr style="background: #cbd1dc">
                                            <td style="text-align: right">
                                                <strong
                                                    style="margin-right: 10px; "><?= lang('total_working_hour') ?>
                                                    : </strong>
                                            </td>
                                            <td colspan="2">
                                                <?php
                                                if ($total_mm > 60) {
                                                    $total_hh += intval($total_mm / 60);
                                                    $total_mm = intval($total_mm % 60);
                                                }
                                                echo $total_hh . " : " . $total_mm . " m";
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                            </th>

                        </tr>

                    <?php }
                }
                ?>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>
</div>
</body>
</html>