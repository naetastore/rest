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
                    <button class="js-new btn btn-primary btn-lg" data-toggle="modal"  data-target="#roleform">Add New Role</button>
                </h3>
                <div class="row">
                    <div class="col-lg-6">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Role</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php $i=1; foreach($role as $r):?>
                                    <tr class="js-roleid-<?= $r['id']; ?>">
                                        <th class="js-i" scope="row"><?= $i; ?></th>
                                        <th><?= $r['role']; ?></th>
                                        <th>
                                            <div class="btn-group">
                                                <button type="button" data-toggle="dropdown" class="btn btn-sm dropdown-toggle btn-inverse">Privacy
                                                    <span class="caret"></span>
                                                </button>
                                                <ul role="menu" class="dropdown-menu">
                                                    <li><a href="<?= base_url('superuser/orderui?role_id=' . $r['id']) . '&'; ?>" class="js-navlink js-access">Order UI config</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="btn-group">
                                                <button type="button" data-toggle="dropdown" class="btn btn-sm dropdown-toggle btn-warning">Access
                                                    <span class="caret"></span>
                                                </button>
                                                <ul role="menu" class="dropdown-menu">
                                                    <li><a href="<?= base_url('superuser/roleaccess?role_id=' . $r['id']) . '&'; ?>" class="js-navlink js-access">Menu access</a>
                                                    </li>
                                                    <li class="divider"></li>
                                                    <li><a href="<?= base_url('superuser/orderaccess?role_id=' . $r['id']) . '&'; ?>" class="js-navlink js-access">Order API control</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <button data-id="<?= $r['id']; ?>" class="js-remove btn btn-danger btn-sm">Remove</button>
                                            <button data-id="<?= $r['id']; ?>" class="js-update btn btn-success btn-sm" data-toggle="modal"  data-target="#roleform">Update</button>
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

    <div class="modal fade" id="roleform" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
      aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="menu">Role: </label>
                        <input placeholder="Enter your new role here..." autocomplete="off" class="form-control" id="role" name="role"></input>
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
    <script src="<?= base_url('src/js/role.js'); ?>"></script>
    
</body>

</html>