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
                    <button class="js-new btn btn-primary btn-lg" data-toggle="modal"  data-target="#menuform">Add New Sub Menu</button>
                </h3>
                <div class="row">
                <div class="col-lg-12">
                        

                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Title</th>
                                <th scope="col">Menu</th>
                                <th scope="col">Url</th>
                                <th scope="col">Icon</th>
                                <th scope="col">Active</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php $i=1; foreach($subMenu as $sm):?>
                            <tr class="js-menuid-<?= $sm['id']; ?>">
                                <th class="js-i" scope="row"><?= $i; ?></th>
                                <th><?= $sm['name']; ?></th>
                                <th><?= $sm['menu']; ?></th>
                                <th><?= $sm['url']; ?></th>
                                <th><?= $sm['icon']; ?></th>
                                <th><?= $sm['is_active']; ?></th>
                                <th>
                                    <a href="#" data-toggle="modal" data-target="#menuform" data-id="<?= $sm['id']; ?>" class="js-update badge bg-success">edit</a>
                                    <a href="#" data-id="<?= $sm['id']; ?>" class="js-remove badge bg-danger">delete</a>
                                </th>
                            </tr>
                            <?php $i++; endforeach;?>
                        </tbody>
                    </table>


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
                        <label class="form-label" for="menu_id">Menu: </label>
                        <select required id="menu_id" name="menu_id" class="form-control">

                            <!-- ngRepeat: menu -->
                            <?php foreach($menu as $m): ?>
                            <option value="<?= $m['id']; ?>"><?= $m['menu']; ?></option>
                            <?php endforeach;?>
                            <!-- End ngRepeat: menu -->

                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="name">Sub Menu: </label>
                                <input placeholder="Enter your new sub menu..." autocomplete="off" class="form-control" id="name" name="name"></input>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="url">URL: </label>
                                <input placeholder="URL here..." autocomplete="off" class="form-control" id="url" name="url"></input>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="icon">Icon: </label>
                                <input value="icon-doc" autocomplete="off" class="form-control" id="icon" name="icon"></input>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="is_active">Active? </label>
                                <select required class="form-control" id="is_active" name="is_active">
                                    <option class="form-control" value="1">True</option>
                                    <option class="form-control" value="0">False</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="js-cancel btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button style="display: none" class="js-remove btn btn-danger" type="button">Delete</button>
                    <button class="js-submit btn btn-primary" type="submit">Save Changes</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php $this->load->view('templates/starter/initbody'); ?>
    <script src="<?= base_url('src/js/submenu.js'); ?>"></script>
    
</body>

</html>