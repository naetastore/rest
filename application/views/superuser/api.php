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
    <!-- CHOSEN-->
    <link rel="stylesheet" href="<?= base_url('src/'); ?>vendor/chosen_v1.2.0/chosen.min.css">
    <!-- XEDITABLE-->
    <link rel="stylesheet" href="<?= base_url('src/'); ?>vendor/x-editable/dist/bootstrap3-editable/css/bootstrap-editable.css">
    
</head>

<body>
    <div class="wrapper">

        <?php $this->load->view('templates/header'); ?>
        <?php $this->load->view('templates/sidebar'); ?>
        <?php $this->load->view('templates/offsidebar'); ?>
    
        <section>
            <div class="content-wrapper">
                <h3 style="padding: 13.5px 20px;">
                    <button class="js-new btn btn-primary btn-lg" data-toggle="modal" data-target="#apiform">
                        Register
                    </button>
                </h3>
                <div class="row">
                <div class="col-lg-10">
                    <div class="panel panel-default">
                        <div class="panel-heading">REST API Keys</div>
                        <div class="panel-body">
                            <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>User ID</th>
                                        <th>User Name</th>
                                        <th>API Key</th>
                                        <th>Web Application</th>
                                        <th>Date Created</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <!-- ngRepeat: API Keys -->
                                    <?php foreach($keys as $k):?>
                                    <tr class="js-keyid-<?= $k['id']; ?>">
                                        <td><?= $k['user_id']; ?></td>
                                        <td>
                                            <a title="Go to profile <?= $k['user']['username']; ?>" class="js-navlink" href="<?= $k['user']['url']; ?>"><?= $k['user']['username']; ?></a>
                                        </td>
                                        <td>
                                            <a data-inputid="key" 
                                                class="js-update js-apikey" data-id="<?= $k['id']; ?>" href="#"><?= $k['key']; ?></a>
                                        </td>
                                        <td>
                                            <a data-inputid="web_app" 
                                                class="js-update js-webapp" data-id="<?= $k['id']; ?>" href="#"><?= $k['web_app']; ?></a>
                                        </td>
                                        <td><?= $k['date_created']; ?></td>
                                        <td>
                                            <a href="#" data-id="<?= $k['id']; ?>" class="js-remove btn btn-danger btn-sm">Delete</a>
                                        </td>
                                    </tr>
                                    <?php endforeach;?>
                                    <!-- End ngRepeat: API Keys -->

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php $this->load->view('templates/footer'); ?>

    </div>

    <div class="modal fade" id="apiform" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong id="name"></strong>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="username">User Name: </label>
                                    <select required id="user_id" name="user_id" class="chosen-select form-control">

                                        <!-- ngRepeat: user -->
                                        <?php foreach($users as $s): ?>
                                        <option value="<?= $s['id']; ?>"><?= $s['username']; ?></option>
                                        <?php endforeach;?>
                                        <!-- End ngRepeat: user -->

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="web_app">Web App: </label>
                                    <input required autocomplete="off" type="text" id="web_app" name="web_app" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <button class="js-cancel btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="js-remove btn btn-danger" style="display: none" type="button">Delete</button>
                    <button class="js-submit btn btn-primary" type="button">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
    
    <?php $this->load->view('templates/starter/initbody'); ?>
    <!-- CHOSEN-->
    <script src="<?= base_url('src/'); ?>vendor/chosen_v1.2.0/chosen.jquery.min.js"></script>
    <!-- SLIDER CTRL-->
    <script src="<?= base_url('src/'); ?>vendor/seiyria-bootstrap-slider/dist/bootstrap-slider.min.js"></script>
    <!-- INPUT MASK-->
    <script src="<?= base_url('src/'); ?>vendor/jquery.inputmask/dist/jquery.inputmask.bundle.js"></script>
    <!-- SELECT2-->
    <script src="<?= base_url('src/'); ?>vendor/select2/dist/js/select2.js"></script>
    <!-- XEDITABLE-->
    <script src="<?= base_url('src/'); ?>vendor/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
    <script src="<?= base_url('src/js/api.js'); ?>"></script>
    
</body>

</html>