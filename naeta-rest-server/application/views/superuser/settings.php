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
                    <div class="col-lg-5">
                        <h4>Account Registration</h4>
                        <p>Pilih role default saat pengguna mendaftar akun melalui API.</p>
                        <select style="width: 180px;" data-setting="default_role" name="default_role" id="default_role"></select>

                        <h4 style="margin-top: 3rem;">Order Settings (REST Controller)</h4>
                        <div class="form-group">
                            <p>Tentukan jumlah maksimum jam diperbolehkan menambah item pesanan setelah <strong>consumer</strong> membuat pesanan.</p>
                            <input style="width: 50px;" data-setting="order_maxhour" type="number" name="order_maxhour" id="order_maxhour">
                        </div>
                        <div class="form-group">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Status</th>

                                        <!-- ngRepeat: product actions -->
                                        <?php foreach($product_action as $pa):?>
                                        <th scope="col"><?= $pa['action']; ?></th>
                                        <?php endforeach;?>
                                        <!-- End ngRepeat: product actions -->
                                            
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    <!-- ngRepeat: product status -->
                                    <?php $i=1; foreach($product_status as $ps):?>
                                    <tr class="js-statusid-<?= $ps['id']; ?>">
                                        <th class="js-i" scope="row"><?= $i; ?></th>
                                        <th><?= $ps['status']; ?></th>

                                        <!-- ngRepeat: product actions -->
                                        <?php foreach($product_action as $pa):?>
                                        <th>
                                            <div class="form-check">
                                                <input type="checkbox" class="js-action-allowed form-check-input"
                                                    <?= check_product_action_allowed($ps['id'], $pa['id']); ?>
                                                    data-status="<?= $ps['id']; ?>"
                                                    data-action="<?= $pa['id']; ?>"
                                                >
                                            </div>
                                        </th>
                                        <?php endforeach;?>
                                        <!-- End ngRepeat: product actions -->
                                        
                                    </tr>
                                    <?php $i++; endforeach;?>
                                    <!-- End ngRepeat: product actions -->
                                    
                                </tbody>
                            </table>
                        </div>

                        <h4 style="margin-top: 3rem;">Notification</h4>
                        <div class="form-group">
                            <p>Pilih siapa yang harus mendapatkan notifikasi saat consumer membuat, membatalkan dan menambahkan item pesanan.</p>
                            <select data-setting="admin_notification" style="width: 180px;" name="admin_notification" id="admin_notification"></select>
                        </div>
                        <div class="form-group">
                            <p>Pilih siapa yang harus mendapatkan notifikasi saat stock mulai habis.</p>
                            <select data-setting="stock_notification" style="width: 180px;" name="stock_notification" id="stock_notification"></select>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php $this->load->view('templates/footer'); ?>

    </div>
    
    <?php $this->load->view('templates/starter/initbody'); ?>
    <script src="<?= base_url('src/js/apisettings.js'); ?>"></script>
    
</body>

</html>