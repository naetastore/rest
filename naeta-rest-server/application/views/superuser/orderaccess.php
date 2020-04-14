<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="description" content="<?= $metadescription ?>">
    <meta name="keywords" content="<?= $metakeyword ?>">
    <link rel="shortcut icon" href="<?= base_url('src/img/naetastore.svg'); ?>">
    <title><?= $title ?></title>

    <?php $this->load->view('templates/starter/inithead'); ?>

</head>

<body>
    <div class="wrapper">

        <?php $this->load->view('templates/header'); ?>
        <?php $this->load->view('templates/sidebar'); ?>
        <?php $this->load->view('templates/offsidebar'); ?>
    
        <section>
            <div class="content-wrapper">
                <h3><?= $title; ?>
                    <small><?= $subtitle; ?></small>
                </h3>
                <div class="row">
                    <div class="col-lg-6">
                        <div style="font-style: italic; color: #777;">
                            <p>
                                <strong>"</strong> Status <strong>Purchased & Deleted</strong> hanya berjalan ketika Order <strong>Soft Deleted</strong>.
                                <br>
                                status <strong>In Order</strong> hanya dapat menjalankan fitur <strong>Confirm</strong> dan <strong>Cancel</strong>.
                                <br>
                                <strong>Admin</strong> masih dapat melihat <strong>Soft Delete</strong>, kecuali <strong>Consumer</strong>. <strong>"</strong>
                            </p>
                        </div>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Status</th>

                                    <!-- ngRepeat: order actions -->
                                    <?php foreach($order_action as $oa):?>
                                    <th scope="col"><?= $oa['action']; ?></th>
                                    <?php endforeach;?>
                                    <!-- End ngRepeat: order actions -->
                                        
                                </tr>
                            </thead>
                            <tbody>
                                
                                <!-- ngRepeat: order status -->
                                <?php $i=1; foreach($order_status as $os):?>
                                <tr class="js-statusid-<?= $os['id']; ?>">
                                    <th class="js-i" scope="row"><?= $i; ?></th>
                                    <th><?= $os['status']; ?></th>

                                    <!-- ngRepeat: order actions -->
                                    <?php foreach($order_action as $oa):?>
                                    <th>
                                        <div class="form-check">
                                            <input type="checkbox" class="js-update form-check-input"
                                                <?= check_order_access($role['id'], $os['id'], $oa['id']); ?>
                                                data-role="<?= $role['id']; ?>"
                                                data-status="<?= $os['id']; ?>"
                                                data-action="<?= $oa['id']; ?>"
                                            >
                                        </div>
                                    </th>
                                    <?php endforeach;?>
                                    <!-- End ngRepeat: order actions -->
                                    
                                </tr>
                                <?php $i++; endforeach;?>
                                <!-- End ngRepeat: order actions -->
                                
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </section>

        <?php $this->load->view('templates/footer'); ?>

    </div>
    
    <?php $this->load->view('templates/starter/initbody'); ?>
    <script src="<?= base_url('src/js/orderaccess.js'); ?>"></script>
    
</body>

</html>