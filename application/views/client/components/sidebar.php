<aside class="aside">
    <!-- START Sidebar (left)-->
    <?php
    $user_id = $this->session->userdata('user_id');
    $profile_info = $this->db->where('user_id', $user_id)->get('tbl_account_details')->row();
    $user_info = $this->db->where('user_id', $user_id)->get('tbl_users')->row();
    ?>
    <div class="aside-inner">
        <nav data-sidebar-anyclick-close="" class="sidebar">
            <!-- START sidebar nav-->
            <ul class="nav">
                <!-- START user info-->
                <li class="has-user-block">
                    <div id="user-block" class="block">
                        <div class="item user-block">
                            <!-- User picture-->
                            <div class="user-block-picture">
                                <div class="user-block-status">
                                    <img src="<?= base_url() . $profile_info->avatar ?>" alt="Avatar" width="60"
                                         height="60"
                                         class="img-thumbnail img-circle">
                                    <div class="circle circle-success circle-lg"></div>
                                </div>
                            </div>
                            <!-- Name and Job-->
                            <div class="user-block-info">
                                <span class="user-block-name"><?= $profile_info->fullname ?></span>
                                <span class="user-block-role"></i> <?= lang('online') ?></span>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <?php
            echo $this->menu->clientMenu();
            ?>
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="nav">
                <?php
                $online_user = $this->db->where(array('online_status' => '1'))->get('tbl_users')->result();

                if (!empty($online_user)):
                    ?>
                    <li class="content-header"
                        style=";font-weight: bold;color: #fff;font-size: 14px;"><?= lang('online') ?></li>
                    <?php
                    foreach ($online_user as $v_online_user):
                        if ($v_online_user->user_id != $this->session->userdata('user_id')) {
                            if ($v_online_user->role_id == 1) {
                                $user = lang('admin');
                            } elseif ($v_online_user->role_id == 2) {
                                $user = lang('staff');
                            } else {
                                $user = lang('client');
                            }
                            ?>
                            <li class="">
                                <a title="<?php echo $user ?>" data-placement="top" data-toggle="tooltip" class="dker"
                                   href="<?php echo base_url(); ?>client/message/get_chat/<?php echo $v_online_user->user_id ?>">
                                    <?php echo $v_online_user->username ?>
                                    <b class="label label-success pull-right"> <em
                                            class="fa fa-dot-circle-o fa-spin"></em></b>
                                </a>
                            </li>
                            <?php
                        }
                    endforeach;
                    ?>
                <?php endif ?>
            </ul>
        </nav>
    </div>
    <!-- END Sidebar (left)-->
</aside>