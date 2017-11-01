<!DOCTYPE html>
<html>
<head>
    <title><?= lang('assign_stock') . ' ' . lang('report') ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        th {
            padding: 10px 0px 5px 5px;
            text-align: left;
            font-size: 13px;
            border: 1px solid black;
        }

        td {
            padding: 5px 0px 0px 5px;
            text-align: left;
            border: 1px solid black;
            font-size: 13px;
        }

        .col-md-6 {
            width: 100%;
        }

        .col-md-12 {
            width: 100%;
        }

        .custom-bg {
            background: #f0f0f0;
        }

        .p {
            padding: 10px !important;
        }

        .text-center {
            text-align: center;
        }
    </style>

</head>
<body style="min-width: 98%; min-height: 100%; overflow: hidden; alignment-adjust: central;">
<br/>
<div style="width: 100%; border-bottom: 2px solid black;">
    <table style="width: 100%; vertical-align: middle;">
        <tr>
            <td style="width: 35px; border: 0px;padding-bottom: 10px;">
                <img style="width: 60px;width: 60px;margin-top: -10px;margin-right: 10px;"
                     src="<?= base_url() . config_item('company_logo') ?>">
            </td>
            <td style="border: 0px;">
                <p style="margin-left: 10px; font: 22px lighter;"><?= config_item('company_name') ?></p>
            </td>
        </tr>
    </table>
</div>
<br/>
<div style="width: 100%;">
    <div style="background: #E0E5E8;padding: 5px;">
        <!-- Default panel contents -->
        <div
            style="font-size: 15px;padding: 5px 0px 0px 0px"><?= lang('stock') . ' ' . lang('report_from') ?>
            :<strong>
            <?= strftime(config_item('date_format'), strtotime($start_date)); ?></div>
        <div
            style="font-size: 15px;padding: 0px 0px 0px 0px"></strong><?= lang('stock') . ' ' . lang('report_to') ?>
            : <strong><?= strftime(config_item('date_format'), strtotime($end_date)); ?></strong></div>
    </div>
    <?php
    if (!empty($assign_stock) && !empty($purchase_stock)) {
        $col = 'col-md-6';
    } else {
        $col = 'col-md-12';
    }
    if (!empty($assign_stock) || !empty($purchase_stock)) { ?>
        <?php if (!empty($assign_stock)) { ?>

            <div class="<?= $col ?>">
            <div class="custom-bg p text-center"><strong><?= lang('assign_stock_list') ?></strong></div>
            <table style="width: 100%; font-family: Arial, Helvetica, sans-serif; border-collapse: collapse;" page-break-inside: auto;>
            <?php foreach ($assign_stock as $item_name => $v_assign_report) { ?>
                <tr style="width: 100%; background-color: rgb(224, 224, 224);margin-top: 15px;padding-bottom: 5px">
                    <td colspan="3"><strong><?php echo $item_name; ?></strong></td>
                </tr>
                <tr>
                    <th><?= lang('assigned_user') ?></th>
                    <th><?= lang('assign_date') ?></th>
                    <th><?= lang('assign_quantity') ?></th>
                </tr>
                <?php
                $total_assign_inventory = 0;
                if (!empty($v_assign_report)) {
                    foreach ($v_assign_report as $v_report) {
                        ?>

                        <tr style="width: 100%;">
                            <td><?php echo $v_report->fullname ?></td>
                            <td><?= strftime(config_item('date_format'), strtotime($v_report->assign_date)); ?></td>
                            <td><?php echo $v_report->assign_inventory; ?> </td>
                            <?php
                            $total_assign_inventory += $v_report->assign_inventory;
                            ?>
                        </tr>
                    <?php } ?>
                    <tr>
                        <th style="text-align: right;" colspan="2"><strong
                                style="margin-right: 5px"> <?= lang('total') ?> <?php echo $v_report->item_name ?>
                                : </strong></th>
                        <td><span><?php
                                echo $total_assign_inventory;
                                ?></span>
                        <span
                            style="padding-right: 20px;text-align: right;display: inline-block;overflow: hidden; "> <?= lang('available_stock') ?>
                            : <strong> <?php echo $v_report->total_stock; ?> </strong></span>
                    </td>
                </tr>
            <?php }; ?>
        <?php }; ?>
    <?php } else { ?>
        <tr>
            <td colspan="3">
                <strong><?= lang('nothing_to_display') ?></strong>
            </td>
        </tr>
        </table>

    <?php } ?>
</div>
<?php if (!empty($purchase_stock)) { ?>
<div class="<?= $col ?>">
    <div class="custom-bg p text-center"><strong><?= lang('stock_list') ?></strong></div>
    <table style="width: 100%; font-family: Arial, Helvetica, sans-serif; border-collapse: collapse;" page-break-inside:
           auto;>
        <?php foreach ($purchase_stock as $item_name => $v_purchase_stoc) { ?>

            <tr style="width: 100%; background-color: rgb(224, 224, 224);margin-top: 15px;padding-bottom: 5px">
                <td colspan="3"><strong><?php echo $item_name; ?></strong></td>
            </tr>
            <tr>
                <th><?= lang('item_name') ?></th>
                <th><?= lang('inventory') ?></th>
                <th><?= lang('buying_date') ?></th>
            </tr>
            <?php
            $total_inventory = 0;
            if (!empty($v_purchase_stoc)) {
                foreach ($v_purchase_stoc as $v_stock) {
                    ?>
                    <tr style="width: 100%;">
                        <td><?php echo $v_stock->item_name ?></td>
                        <td><?php echo $v_stock->inventory; ?> </td>
                        <td><?= strftime(config_item('date_format'), strtotime($v_stock->purchase_date)); ?></td>
                        <?php
                        $total_inventory += $v_stock->inventory;
                        ?>
                    </tr>
                <?php }
            ; ?>
                <tr>
                    <th style="text-align: right;"><strong
                            style="margin-right: 5px"> <?= lang('total') . ' ' . lang('assigned') . ': ' ?> </strong>
                    </th>
                    <td><?= $total_inventory - $v_stock->total_stock ?></td>
                    <td>
                        <span
                            style="padding-right: 20px;text-align: right;display: inline-block;overflow: hidden; "> <?= lang('available_stock') ?>
                            : <strong> <?php echo $v_report->total_stock; ?> </strong></span>
                    </td>
                </tr>
            <?php }; ?>
        <?php }; ?>
        <?php } else { ?>
        <tr>
            <td colspan="3">
                <strong><?= lang('nothing_to_display') ?></strong>
            </td>
        </tr>
    </table>
    <?php } ?>
</div>
<?php }; ?>
</body>
</html>
