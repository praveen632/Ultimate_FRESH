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
            padding: 3px 0px 1px 3px;
        }

        .th3 {
            font-size: 13px;
            padding: 3px 0px 1px 9px;
            border-bottom: 1px solid #dad3d3;
            background: #dee0e4;
        }

        .td {
            border-bottom: 1px solid #dad3d3;
            padding: 3px 0px 1px 9px;
        }
        .n{
            font-size: 12px;
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
            <?php foreach ($attendace_info as $week => $v_attndc_info) { ?>
                <tr>
                    <th colspan="8" class="th"><?= lang('week') ?> : <?php echo $week; ?></th>
                </tr>
                <tr>
                    <th class="th3"><?= lang('name') ?></th>
                    <?php
                    if (!empty($v_attndc_info)) {
                        foreach ($v_attndc_info as $date => $attendace) {
                            $total_hour = 0;
                            $total_minutes = 0;
                            ?>
                            <th class="th3"><?= strftime(config_item('date_format'), strtotime($date)) ?></th>
                        <?php }
                    }; ?>
                </tr>
                <?php
                foreach ($employee_info as $v_employee) {
                    ?>
                    <tr>
                        <td class="td n"><?php echo $v_employee->fullname ?></td>
                        <?php
                        if (!empty($v_attndc_info)):foreach ($v_attndc_info as $date => $attendace):

                            $total_hh = 0;
                            $total_mm = 0;
                            foreach ($attendace as $key => $v_attendace) {
                                if ($key == $v_employee->user_id) {
                                    ?>
                                    <?php
                                    if (!empty($v_attendace)) {
                                        foreach ($v_attendace as $v_attandc) {
                                            if (!empty($v_attandc->clockout_time)) {

                                                // calculate the start timestamp
                                                $startdatetime = strtotime($v_attandc->date_in . " " . $v_attandc->clockin_time);
                                                // calculate the end timestamp
                                                $enddatetime = strtotime($v_attandc->date_out . " " . $v_attandc->clockout_time);
                                                // calulate the difference in seconds
                                                $difference = $enddatetime - $startdatetime;
                                                $years = abs(floor($difference / 31536000));
                                                $days = abs(floor(($difference - ($years * 31536000)) / 86400));
                                                $hours = abs(floor(($difference - ($years * 31536000) - ($days * 86400)) / 3600));
                                                $mins = abs(floor(($difference - ($years * 31536000) - ($days * 86400) - ($hours * 3600)) / 60));#floor($difference / 60);
                                                $total_mm += $mins;
                                                $total_hh += $hours;
                                                // output the result
                                                //echo round($hoursDiff) . " : " . round($minutesDiffRemainder) . " m";
                                            } elseif (!empty($v_attandc->date) && $v_attandc->date == $date && $v_attandc->attendance_status == 'H') {
                                                $holiday = 1;
                                            } elseif ($v_attandc->attendance_status == '3') {
                                                $leave = 1;
                                            } elseif ($v_attandc->attendance_status == '0') {
                                                $absent = 1;
                                            }
                                        }
                                    }
                                }
                            }
                            ?>
                            <td class="td">

                                <?php
                                if ($total_mm > 60) {
                                    $total_hh += intval($total_mm / 60);
                                    $total_mm = intval($total_mm % 60);
                                }
                                $total_hour += $total_hh;
                                $total_minutes += $total_mm;

                                if ($total_hh != 0 || $total_mm != 0) {
                                    echo $total_hh . " : " . $total_mm . " m";
                                } elseif (!empty($holiday)) {
                                    echo '<span style="font-size: 12px;" class="label label-info std_p">' . lang('holiday') . '</span>';
                                } elseif (!empty($leave)) {
                                    echo '<span style="font-size: 12px;" class="label label-warning std_p">' . lang('on_leave') . '</span>';
                                } elseif (!empty ($absent)) {
                                    echo '<span style="font-size: 12px;" class="label label-danger std_p">' . lang('absent') . '</span>';
                                } else {
                                    echo $total_hh . " : " . $total_mm . " m";
                                }
                                ?>
                            </td>
                            <?php
                            $holiday = NULL;
                            $leave = NULL;
                            $absent = NULL;
                        endforeach;
                        endif;
                        ?>
                    </tr>
                <?php }; ?>
            <?php } ?>
        </table>
    <?php } ?>
</div>
</body>
</html>