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
                    <div class="col-lg-12">
                        <p>A row with content</p>
                    </div>
                </div>
            </div>
        </section>

        <?php $this->load->view('templates/footer'); ?>

    </div>
    
    <?php $this->load->view('templates/starter/initbody'); ?>
    
</body>

</html>