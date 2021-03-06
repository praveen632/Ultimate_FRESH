<?php echo message_box('success') ?>
<div class="row">
    <!-- Start Form -->
    <div class="col-lg-12">

        <div class="panel panel-custom">
            <header class="panel-heading  "><?= lang('payment_settings') ?></header>
            <div class="panel-body">
                <input type="hidden" name="settings" value="<?= $load_setting ?>">
                <?php if (!empty($payment)) : ?>
                    <form role="form" id="form"
                          action="<?php echo base_url(); ?>admin/settings/save_payments/<?= $payment ?>" method="post"
                          class="form-horizontal  ">
                        <?php if ($payment == 'paypal'):
                            ?>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><?= lang('paypal_email') ?> <span
                                        class="text-danger">*</span></label>
                                <div class="col-lg-7">
                                    <input type="email" name="paypal_email" class="form-control"
                                           value="<?= $this->config->item('paypal_email') ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-4 control-label"><?= lang('paypal_ipn_url') ?> </label>
                                <div class="col-lg-7">
                                    <input type="text" class="form-control" data-toggle="tooltip" data-placement="top"
                                           data-original-title="<?= lang('change_if_necessary') ?>"
                                           value="<?= $this->config->item('paypal_ipn_url') ?>" name="paypal_ipn_url">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><?= lang('paypal_cancel_url') ?> <span
                                        class="text-danger">*</span></label>
                                <div class="col-lg-7">
                                    <input type="text" class="form-control" data-toggle="tooltip" data-placement="top"
                                           data-original-title="<?= lang('change_if_necessary') ?>"
                                           value="<?= $this->config->item('paypal_cancel_url') ?>"
                                           name="paypal_cancel_url">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><?= lang('paypal_success_url') ?></label>
                                <div class="col-lg-7">
                                    <input type="text" class="form-control" data-toggle="tooltip" data-placement="top"
                                           data-original-title="<?= lang('change_if_necessary') ?>"
                                           value="<?= $this->config->item('paypal_success_url') ?>"
                                           name="paypal_success_url">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><?= lang('paypal_live') ?></label>
                                <div class="col-lg-6">
                                    <label>
                                        <input type="hidden" value="off" name="paypal_live"/>
                                        <input type="checkbox" <?php
                                        if (config_item('paypal_live') == 'TRUE') {
                                            echo "checked=\"checked\"";
                                        }
                                        ?> name="paypal_live">
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><?= lang('status') ?></label>
                                <div class="col-lg-6">
                                    <select name="paypal_status" class="form-control">
                                        <option <?= (config_item('paypal_status') == 'active' ? 'selected' : '') ?>
                                            value="active"><?= lang('active') ?></option>
                                        <option <?= (config_item('paypal_status') == 'deactive' ? 'selected' : '') ?>
                                            value="deactive"><?= lang('deactive') ?></option>
                                    </select>
                                </div>
                            </div>
                        <?php elseif ($payment == '2checkout'):
                            ?>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">2checkout Publishable Key</label>
                                <div class="col-lg-7">
                                    <input type="text" class="form-control"
                                           value="<?= config_item('2checkout_publishable_key') ?>"
                                           name="2checkout_publishable_key">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">2checkout Private Key</label>
                                <div class="col-lg-7">
                                    <input type="text" class="form-control"
                                           value="<?= config_item('2checkout_private_key') ?>"
                                           name="2checkout_private_key">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">2checkout Seller ID</label>
                                <div class="col-lg-7">
                                    <input type="text" class="form-control"
                                           value="<?= config_item('2checkout_seller_id') ?>" name="2checkout_seller_id">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><?= lang('status') ?></label>
                                <div class="col-lg-6">
                                    <select name="2checkout_status" class="form-control">
                                        <option <?= (config_item('2checkout_status') == 'active' ? 'selected' : '') ?>
                                            value="active"><?= lang('active') ?></option>
                                        <option <?= (config_item('2checkout_status') == 'deactive' ? 'selected' : '') ?>
                                            value="deactive"><?= lang('deactive') ?></option>
                                    </select>
                                </div>
                            </div>

                        <?php elseif ($payment == 'Stripe'):
                            ?>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><?= lang('stripe_private_key') ?></label>
                                <div class="col-lg-7">
                                    <input type="text" class="form-control"
                                           value="<?= $this->config->item('stripe_private_key') ?>"
                                           name="stripe_private_key">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-4 control-label"><?= lang('stripe_public_key') ?></label>
                                <div class="col-lg-7">
                                    <input type="text" class="form-control"
                                           value="<?= $this->config->item('stripe_public_key') ?>"
                                           name="stripe_public_key">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><?= lang('status') ?></label>
                                <div class="col-lg-6">
                                    <select name="stripe_status" class="form-control">
                                        <option <?= (config_item('stripe_status') == 'active' ? 'selected' : '') ?>
                                            value="active"><?= lang('active') ?></option>
                                        <option <?= (config_item('stripe_status') == 'deactive' ? 'selected' : '') ?>
                                            value="deactive"><?= lang('deactive') ?></option>
                                    </select>
                                </div>
                            </div>
                        <?php elseif ($payment == 'Authorize.net'): ?>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><?= lang('authorize') ?></label>
                                <div class="col-lg-7">
                                    <input type="text" class="form-control"
                                           value="<?= $this->config->item('authorize') ?>"
                                           name="authorize">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><?= lang('authorize_transaction_key') ?></label>
                                <div class="col-lg-7">
                                    <input type="text" class="form-control"
                                           value="<?= $this->config->item('authorize_transaction_key') ?>"
                                           name="authorize_transaction_key">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><?= lang('status') ?></label>
                                <div class="col-lg-6">
                                    <select name="authorize_status" class="form-control">
                                        <option <?= (config_item('authorize_status') == 'active' ? 'selected' : '') ?>
                                            value="active"><?= lang('active') ?></option>
                                        <option <?= (config_item('authorize_status') == 'deactive' ? 'selected' : '') ?>
                                            value="deactive"><?= lang('deactive') ?></option>
                                    </select>
                                </div>
                            </div>


                        <?php elseif ($payment == 'CCAvenue'):
                            ?>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><?= lang('ccavenue_merchant_id') ?></label>
                                <div class="col-lg-7">
                                    <input type="text" class="form-control"
                                           value="<?= $this->config->item('ccavenue_merchant_id') ?>"
                                           name="ccavenue_merchant_id">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><?= lang('ccavenue_key') ?></label>
                                <div class="col-lg-7">
                                    <input type="text" class="form-control"
                                           value="<?= $this->config->item('ccavenue_key') ?>" name="ccavenue_key">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><?= lang('status') ?></label>
                                <div class="col-lg-6">
                                    <select name="ccavenue_status" class="form-control">
                                        <option <?= (config_item('ccavenue_status') == 'active' ? 'selected' : '') ?>
                                            value="active"><?= lang('active') ?></option>
                                        <option <?= (config_item('ccavenue_status') == 'deactive' ? 'selected' : '') ?>
                                            value="deactive"><?= lang('deactive') ?></option>
                                    </select>
                                </div>
                            </div>

                        <?php elseif ($payment == 'Braintree'): ?>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><?= lang('braintree_merchant_id') ?></label>
                                <div class="col-lg-7">
                                    <input type="text" class="form-control"
                                           value="<?= $this->config->item('braintree_merchant_id') ?>"
                                           name="braintree_merchant_id">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><?= lang('braintree_private_key') ?></label>
                                <div class="col-lg-7">
                                    <input type="text" class="form-control"
                                           value="<?= $this->config->item('braintree_private_key') ?>"
                                           name="braintree_private_key">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><?= lang('braintree_public_key') ?></label>
                                <div class="col-lg-7">
                                    <input type="text" class="form-control"
                                           value="<?= $this->config->item('braintree_public_key') ?>"
                                           name="braintree_public_key">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><?= lang('braintree_default_account') ?></label>
                                <div class="col-lg-7">
                                    <input type="text" class="form-control"
                                           value="<?= $this->config->item('braintree_default_account') ?>"
                                           name="braintree_default_account">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><?= lang('braintree_live_or_sandbox') ?></label>
                                <div class="col-lg-7">
                                    <input type="text" class="form-control"
                                           value="<?= $this->config->item('braintree_live_or_sandbox') ?>"
                                           name="braintree_live_or_sandbox">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><?= lang('status') ?></label>
                                <div class="col-lg-6">
                                    <select name="braintree_status" class="form-control">
                                        <option <?= (config_item('braintree_status') == 'active' ? 'selected' : '') ?>
                                            value="active"><?= lang('active') ?></option>
                                        <option <?= (config_item('braintree_status') == 'deactive' ? 'selected' : '') ?>
                                            value="deactive"><?= lang('deactive') ?></option>
                                    </select>
                                </div>
                            </div>
                        <?php elseif
                        ($payment == 'Mollie'
                        ): ?>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><?= lang('api_key') ?></label>
                                <div class="col-lg-7">
                                    <input type="text" class="form-control"
                                           value="<?= $this->config->item('mollie_api_key') ?>"
                                           name="mollie_api_key">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><?= lang('partner_id') ?></label>
                                <div class="col-lg-7">
                                    <input type="text" class="form-control"
                                           value="<?= $this->config->item('mollie_partner_id') ?>"
                                           name="mollie_partner_id">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"><?= lang('status') ?></label>
                                <div class="col-lg-6">
                                    <select name="mollie_status" class="form-control">
                                        <option <?= (config_item('mollie_status') == 'active' ? 'selected' : '') ?>
                                            value="active"><?= lang('active') ?></option>
                                        <option <?= (config_item('mollie_status') == 'deactive' ? 'selected' : '') ?>
                                            value="deactive"><?= lang('deactive') ?></option>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label class="col-lg-4 control-label"></label>
                            <div class="col-lg-1">
                                <button type="submit"
                                        class="btn btn-sm btn-primary"><?= lang('save_changes') ?></button>
                            </div>
                        </div>
                    </form>
                <?php else : ?>

                    <section class="panel panel-custom">
                        <div class="table-responsive">
                            <table class="table table-striped DataTables " id="Transation_DataTables">
                                <thead>
                                <tr>
                                    <th><?= lang('icon') ?></th>
                                    <th><?= lang('gateway_name') ?></th>
                                    <th><?= lang('status') ?></th>
                                    <th><?= lang('action') ?></th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                $payment_method = $this->db->get('tbl_online_payment')->result();
                                foreach ($payment_method as $v_payments) {
                                    ?>
                                    <tr>
                                        <td><img style="width: 80px;height: 50px"
                                                 src="<?= base_url() ?>asset/images/payment_logo/<?= $v_payments->icon; ?>">
                                        </td>
                                        <td><?= $v_payments->gateway_name; ?></td>
                                        <td><?php
                                            if ($v_payments->gateway_name == 'paypal') {
                                                $status = $this->config->item('paypal_status');
                                            } elseif ($v_payments->gateway_name == 'Stripe') {
                                                $status = $this->config->item('stripe_status');
                                            } elseif ($v_payments->gateway_name == 'bitcoin') {
                                                $status = $this->config->item('bitcoin_status');
                                            } elseif ($v_payments->gateway_name == '2checkout') {
                                                $status = $this->config->item('2checkout_status');
                                            } elseif ($v_payments->gateway_name == 'Authorize.net') {
                                                $status = $this->config->item('authorize_status');
                                            } elseif ($v_payments->gateway_name == 'CCAvenue') {
                                                $status = $this->config->item('ccavenue_status');
                                            } elseif ($v_payments->gateway_name == 'Mollie') {
                                                $status = $this->config->item('mollie_status');
                                            } else {
                                                $status = $this->config->item('braintree_status');
                                            }
                                            if ($status == 'active') {
                                                ?>
                                                <span class="label label-success"><?= lang($status) ?></span>
                                            <?php } else { ?>
                                                <span class="label label-danger"><?= lang($status) ?></span>
                                            <?php }
                                            ?></td>
                                        <td><a data-toggle="tooltip" title="<?= lang('edit') ?>"
                                               class="btn btn-xs btn-primary"
                                               href="<?= base_url() ?>admin/settings/payments/<?= $v_payments->gateway_name ?>"><i
                                                    class="fa fa-edit"></i></a></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- End details -->
                    </section>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- End Form -->