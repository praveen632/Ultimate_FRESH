<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= lang('estimate') ?></title>
    <style type="text/css">
        @font-face {
            font-family: "Source Sans Pro", sans-serif;
        }

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {
            color: #0087C3;
            text-decoration: none;
        }

        body {
            color: #555555;
            background: #FFFFFF;
            font-size: 14px;
            font-family: "Source Sans Pro", sans-serif;
        }

        header {

            padding: 10px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #AAAAAA;
        }

        #logo {
            float: left;
        }

        #company {
            float: right;
            text-align: right;
        }

        #details {
            margin-bottom: 50px;
        }

        #client {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
            float: left;
        }

        #client .to {
            color: #777777;
        }

        h2.name {
            font-size: 1em;
            font-weight: normal;
            margin: 0;
        }

        #invoice {
            float: right;
            text-align: right;
        }

        #invoice h1 {
            color: #0087C3;
            font-size: 1.5em;
            line-height: 1em;
            font-weight: normal;
        }

        #invoice .date {
            font-size: 1.1em;
            color: #777777;
        }

        table {
            width: 100%;
            border-spacing: 0;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 10px;
        }

        table.items th,
        table.items td {
            padding: 8px;
            background: #EEEEEE;
            border-bottom: 1px solid #FFFFFF;
            text-align: left;
        }

        table.items th {
            white-space: nowrap;
            font-weight: normal;
        }

        table.items td {
            text-align: left;
        }

        table.items td h3 {
            color: #57B223;
            font-size: 1em;
            font-weight: normal;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        table.items .no {
            background: #DDDDDD;
        }

        table.items .desc {
            text-align: left;
        }

        table.items .unit {
            background: #DDDDDD;
        }

        table.items .qty {
        }

        table.items td.unit,
        table.items td.qty,
        table.items td.total {
            font-size: 1em;
        }

        table.items tbody tr:last-child td {
            border: none;

        }

        table.items tfoot td {
            padding: 10px 20px;
            background: #FFFFFF;
            border-bottom: none;
            font-size: 1.2em;
            white-space: nowrap;
            border-top: 1px solid #AAAAAA;
        }

        table.items tfoot tr:first-child td {
            border-top: none;
        }

        table.items tfoot tr:last-child td {
            color: #57B223;
            font-size: 1.4em;
            border-top: 1px solid #57B223;

        }

        table.items tfoot tr td:first-child {
            border: none;
            text-align: right;
        }

        #thanks {
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        #notices {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
        }

        #notices .notice {
            font-size: 1em;
            color: #777;
        }

        footer {
            color: #777777;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #AAAAAA;
            padding: 8px 0;
            text-align: center;
        }

        tr.total td, tr th.total, tr td.total {
            text-align: right;
        }

    </style>
</head>
<body>

<?php
$client_info = $this->estimates_model->check_by(array('client_id' => $estimates_info->client_id), 'tbl_client');

$client_lang = $client_info->language;
unset($this->lang->is_loaded[5]);
$language_info = $this->lang->load('sales_lang', $client_lang, TRUE, FALSE, '', TRUE);
$currency = $this->estimates_model->client_currency_sambol($estimates_info->client_id);
?>
<table class="clearfix">
    <tr>
        <td><
            <div id="logo" style="margin-top: 8px;">
                <img style=" width: 70px;" src="<?= base_url() . config_item('invoice_logo') ?>">
            </div>
        </td>
        <td>
            <div id="company">
                <h2 class="name"><?= (config_item('company_legal_name_' . $client_lang) ? config_item('company_legal_name_' . $client_lang) : config_item('company_legal_name')) ?></h2>
                <div><?= (config_item('company_address_' . $client_lang) ? config_item('company_address_' . $client_lang) : config_item('company_address')) ?></div>
                <div><?= (config_item('company_city_' . $client_lang) ? config_item('company_city_' . $client_lang) : config_item('company_city')) ?>
                    , <?= config_item('company_zip_code') ?></div>
                <div><?= (config_item('company_country_' . $client_lang) ? config_item('company_country_' . $client_lang) : config_item('company_country')) ?></div>
                <div> <?= config_item('company_phone') ?></div>
                <div><a href="mailto:<?= config_item('company_email') ?>"><?= config_item('company_email') ?></a>
                </div>
                <div><?= config_item('company_vat') ?></div>
            </div>
        </td>
    </tr>
</table>


<table id="details" class="clearfix">
    <tr>
        <td><
            <div id="client">
                <h2 class="name"><?= $client_info->name ?></h2>
                <div class="address"><?= $client_info->address ?></div>
                <div class="address"><?= $client_info->city ?>, <?= $client_info->zipcode ?>
                    ,<?= $client_info->country ?></div>
                <div class="address"><?= $client_info->phone ?></div>
                <div class="email"><a href="mailto:<?= $client_info->email ?>"><?= $client_info->email ?></a></div>
                <?php if (!empty($client_info->vat)) { ?>
                    <div class="email"><?= lang('vat_number') ?>: <?= $client_info->vat ?></div>
                <?php } ?>
            </div>
        </td>
        <td>
            <div id="invoice">
                <h1><?= $estimates_info->reference_no ?></h1>
                <div class="date"><?= $language_info['estimate_date'] ?>
                    :<?= strftime(config_item('date_format'), strtotime($estimates_info->estimate_date)); ?></div>
                <div class="date"><?= $language_info['valid_until'] ?>
                    :<?= strftime(config_item('date_format'), strtotime($estimates_info->due_date)); ?></div>
                <div class="date"><?= $language_info['estimate_status'] ?>: <?= lang($estimates_info->status) ?></div>
                <?php if (!empty($estimates_info->user_id)) { ?>
                    <div class="date">
                        <?= lang('sales') . ' ' . lang('agent') ?><?php
                        $profile_info = $this->db->where('user_id', $estimates_info->user_id)->get('tbl_account_details')->row();
                        if (!empty($profile_info)) {
                            echo $profile_info->fullname;
                        }
                        ?>
                    </div>
                <?php } ?>
            </div>
        </td>
    </tr>
</table>

<table class="items" border="0" cellspacing="0" cellpadding="0" page-break-inside: auto;>
    <thead>
    <tr>
        <th class="desc"><?= $language_info['item'] ?></th>
        <th class="unit"><?= $language_info['qty'] ?></th>
        <th class="desc"><?= $language_info['price'] ?></th>
        <th class="unit"><?= $language_info['tax'] ?></th>
        <th class="total"><?= $language_info['total'] ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $invoice_items = $this->estimates_model->ordered_items_by_id($estimates_info->estimates_id);

    if (!empty($invoice_items)) :
        foreach ($invoice_items as $key => $v_item) :
            $item_name = $v_item->item_name ? $v_item->item_name : $v_item->item_desc;
            $item_tax_name = json_decode($v_item->item_tax_name);
            ?>
            <tr>
                <td class="desc"><h3><?= $item_name ?></h3><?= nl2br($v_item->item_desc) ?></td>
                <td class="unit"><?= $v_item->quantity . '   ' . $v_item->unit ?></td>
                <td class="desc"><?= display_money($v_item->unit_cost) ?></td>
                <td class="unit"><?php
                    if (!empty($item_tax_name)) {
                        foreach ($item_tax_name as $v_tax_name) {
                            $i_tax_name = explode('|', $v_tax_name);
                            echo $i_tax_name[0] . ' (' . $i_tax_name[1] . ' %) <br>';
                        }
                    }
                    ?></td>
                <td class="total"><?= display_money($v_item->total_cost) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif ?>

    </tbody>
    <tfoot>
    <tr class="total">
        <td colspan="3"></td>
        <td colspan="1"><?= $language_info['sub_total'] ?></td>
        <td><?= display_money($this->estimates_model->estimate_calculation('estimate_cost', $estimates_info->estimates_id)) ?></td>
    </tr>
    <?php if ($estimates_info->discount_total > 0): ?>
        <tr class="total">
            <td colspan="3"></td>
            <td colspan="1"><?= $language_info['discount'] ?>(<?php echo $estimates_info->discount_percent; ?>%)</td>
            <td> <?= display_money($this->estimates_model->estimate_calculation('discount', $estimates_info->estimates_id)) ?></td>
        </tr>
    <?php endif;
    $tax_info = json_decode($estimates_info->total_tax);
    $tax_total = 0;
    if (!empty($tax_info)) {
        $tax_name = $tax_info->tax_name;
        $total_tax = $tax_info->total_tax;
        if (!empty($tax_name)) {
            foreach ($tax_name as $t_key => $v_tax_info) {
                $tax = explode('|', $v_tax_info);
                $tax_total += $total_tax[$t_key];
                ?>
                <tr class="total">
                    <td colspan="3"></td>
                    <td colspan="1"><?= $tax[0] . ' (' . $tax[1] . ' %)' ?></td>
                    <td> <?= display_money($total_tax[$t_key]); ?></td>
                </tr>
            <?php }
        }
    } ?>
    <?php if ($tax_total > 0): ?>
        <tr class="total">
            <td colspan="3"></td>
            <td colspan="1"><?= $language_info['total'] . ' ' . $language_info['tax'] ?></td>
            <td><?= display_money($tax_total); ?></td>
        </tr>
    <?php endif;
    if ($estimates_info->adjustment > 0): ?>
        <tr class="total">
            <td colspan="3"></td>
            <td colspan="1"><?= $language_info['adjustment'] ?></td>
            <td><?= display_money($estimates_info->adjustment); ?></td>
        </tr>
    <?php endif ?>
    <tr class="total">
        <td colspan="3"></td>
        <td colspan="1"><?= $language_info['total'] ?></td>
        <td><?= display_money($this->estimates_model->estimate_calculation('total', $estimates_info->estimates_id), $currency->symbol); ?></td>
    </tr>
    </tfoot>
</table>
<div id="thanks"><?= lang('thanks') ?>!</div>
<div id="notices">
    <div class="notice"><?= $estimates_info->notes ?></div>
</div>
<footer>
    <?= config_item('estimate_footer') ?>
</footer>
</body>
</html>
