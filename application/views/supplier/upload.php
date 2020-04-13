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

    <!-- =============== PAGE VENDOR STYLES ===============-->
    <!-- DATATABLES-->
    <link rel="stylesheet" href="<?= base_url('src/') ?>vendor/datatables-colvis/css/dataTables.colVis.css">
    <link rel="stylesheet" href="<?= base_url('src/') ?>vendor/datatables/media/css/dataTables.bootstrap.css">
    <link rel="stylesheet" href="<?= base_url('src/') ?>vendor/dataTables.fontAwesome/index.css">

</head>

<body>
    <div class="wrapper">

        <?php $this->load->view('templates/header'); ?>
        <?php $this->load->view('templates/sidebar'); ?>
        <?php $this->load->view('templates/offsidebar'); ?>

        <section>
            <div class="content-wrapper">
                <h3 style="padding: 13.5px 20px;">
                    <button class="js-new btn btn-primary btn-lg" data-toggle="modal" data-target="#productform">
                        Upload Product
                    </button>
                </h3>
                <div class="row">

                    <!-- ngRepeat: widget statistics -->
                    <?php foreach($statistic as $s):?>
                    <div class="col-lg-3 col-sm-6">
                        <div class="panel widget <?= $s['bgcolor']; ?>">
                            <div class="row row-table">
                                <div class="col-xs-4 text-center <?= $s['bgcolor']; ?>-dark pv-lg">
                                    <em class="icon-cloud-upload fa-3x"></em>
                                </div>
                                <div class="col-xs-8 pv-lg">
                                    <div class="h2 mt0" id="counter<?= $s['name']; ?>"><?= $s['count'] ?></div>
                                    <div class="text-uppercase"><?= $s['name'] ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach;?>
                    <!-- End ngRepeat: widget statistics -->

                </div>
            </div>
            <div class="content-wrapper">
                <div class="table-responsive b0">
                    <table id="datatable2" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width:30px"><strong>#</strong></th>
                                <th><strong>NAME</strong></th>
                                <th><strong>PRICE</strong></th>
                                <th style="width:80px"><strong>QUANTITY</strong></th>
                                <th class="text-center"><strong>STATUS</strong></th>
                                <th><strong>ADDED</strong></th>
                                <th class="text-center"><strong>VIEW</strong></th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- ngRepeat: product -->
                            <?php foreach($product as $p):?>
                            <tr class="js-productid-<?= $p['id']; ?>">
                                <td><?= $p['id']; ?></td>
                                <td><?= $p['name']; ?></td>
                                <td><?= $p['price']; ?></td>
                                <td><?= $p['qty']; ?></td>
                                <td class="text-center">
                                    <span class="label <?= $p['status_classname']; ?>"><?= $p['status_label']; ?></span>
                                </td>
                                <td><?= $p['created']; ?></td>
                                <td class="text-center">
                                    <button type="button" class="js-view btn btn-sm btn-default" data-toggle="modal" data-target="#productform" data-id="<?= $p['id']; ?>">
                                        <em class="js-view fa fa-search" data-id="<?= $p['id']; ?>"></em>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach;?>
                            <!-- End ngRepeat: product -->

                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <?php $this->load->view('templates/footer'); ?>

    </div>

    <div class="modal fade" id="productform" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" class="js-form-product"
                    action=""
                    enctype="multipart/form-data" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group text-center">
                                <img src="" alt="preview"
                                    class="js-img-preview img-thumbnail">
                                    <span style="width: 100%" class="js-file-input-button btn btn-success"></span>
                                <input class="js-file-input" style="display: none" required type="file" name="image" id="image">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="description">Short Description: <span
                                        id="charleft"></span> </label>
                                <textarea required class="form-control" id="description" name="description" rows="3"
                                    cols="4" maxLength="228" data-max="228"></textarea>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="form-label" for="name">Name: </label>
                                <input required type="text" class="form-control" id="name"
                                    name="name"></input>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="price">Price: </label>
                                        <input required type="number" class="form-control" id="price"
                                            name="price"></input>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="qty">Quantity: </label>
                                        <input required type="number" class="form-control" id="qty"
                                            name="qty"></input>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="category">Category Name: </label>
                                <select required class="form-control" id="category" name="category">

                                    <!-- ngRepeat: categories -->
                                    <?php foreach($categories as $category):?>
                                    <option class="form-control" value="<?= $category['id'] . ',' . $category['global_id']; ?>">
                                        <?= $category['name']; ?>
                                    </option>
                                    <?php endforeach;?>
                                    <!-- End ngRepeat: categories -->

                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="suggested">Is Suggested: </label>
                                        <select required class="form-control" id="suggested" name="suggested">
                                            <option class="form-control" value="1">True</option>
                                            <option class="form-control" value="0">False</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="is_ready">Is Ready: </label>
                                        <select required class="form-control" id="is_ready" name="is_ready">
                                            <option class="form-control" value="1">True</option>
                                            <option class="form-control" value="0">False</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="seo_keyword">SEO Keyword: </label>
                                <textarea required class="form-control" id="seo_keyword" name="seo_keyword" rows="2"
                                    cols="4"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="js-cancel btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="js-remove btn btn-danger" style="display: none" type="button">Delete</button>
                    <button class="js-submit btn btn-primary" type="submit">Save Changes</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <?php $this->load->view('templates/starter/initbody'); ?>

    <!-- =============== PAGE VENDOR SCRIPTS ===============-->
    <!-- DATATABLES-->
    <script src="<?= base_url('src/') ?>vendor/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="<?= base_url('src/') ?>vendor/datatables-colvis/js/dataTables.colVis.js"></script>
    <script src="<?= base_url('src/') ?>vendor/datatables/media/js/dataTables.bootstrap.js"></script>
    <script src="<?= base_url('src/') ?>js/upload.js"></script>

</body>

</html>