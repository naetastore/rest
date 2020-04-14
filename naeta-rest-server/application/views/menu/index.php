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
                <h3 style="padding: 13.5px 20px;">
                    <button class="js-new btn btn-primary btn-lg" data-toggle="modal"  data-target="#menuform">Add New Menu</button>
                </h3>
                <div class="row">
                    <div class="col-lg-6">
                        

                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Menu</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php $i=1; foreach($menu as $m):?>
                                <tr class="js-menuid-<?= $m['id']; ?>">
                                    <th class="js-i" scope="row"><?= $i; ?></th>
                                    <th><?= $m['menu']; ?></th>
                                    <th>
                                        <a href="#" data-toggle="modal" data-target="#menuform" data-id="<?= $m['id']; ?>" class="js-update badge bg-success">edit</a>
                                        <a href="#" data-id="<?= $m['id']; ?>" class="js-remove badge bg-danger">delete</a>
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

    <div class="modal fade" id="menuform" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
      aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" class="js-form-menu" action="">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="menu">Menu: </label>
                        <input placeholder="Enter your new menu here..." autocomplete="off" class="form-control" id="menu" name="menu"></input>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="js-cancel btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="js-submit btn btn-primary" type="submit">Save Changes</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php $this->load->view('templates/starter/initbody'); ?>
    <script src="<?= base_url('src/js/menu.js'); ?>"></script>
    
</body>

</html>