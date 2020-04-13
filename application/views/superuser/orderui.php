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
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">UI</th>
                                    <th scope="col">Hidden</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php $i=1; foreach($ui as $u):?>
                                <tr class="js-uiid-<?= $u['id']; ?>">
                                    <th class="js-i" scope="row"><?= $i; ?></th>
                                    <th><?= $u['ui']; ?></th>
                                    <th>
                                        <div class="form-check">
                                            <input type="checkbox" class="js-update form-check-input"
                                                <?= check_ui_config($role['id'], $u['id']); ?>
                                                data-role="<?= $role['id']; ?>"
                                                data-ui="<?= $u['id']; ?>"
                                            >
                                        </div>
                                    </th>
                                </tr>
                                <?php $i++; endforeach;?>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <?php $this->load->view('templates/footer'); ?>

    </div>
    
    <?php $this->load->view('templates/starter/initbody'); ?>
    <script src="<?= base_url('src/js/orderui.js'); ?>"></script>
    
</body>

</html>