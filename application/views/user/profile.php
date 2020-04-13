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
                <div class="unwrap">
                    <div style="background-image: url(<?= base_url('src/img/bg/profile-bg.jpg'); ?>)" class="bg-cover">
                        <!-- user bgphoto -->
                        <div class="p-xl text-center text-white">
                            <img src="<?= $user['avatar']; ?>" alt="Image" class="img-thumbnail img-circle thumb128">
                            <!-- user photo -->
                            <h3 class="mt3"><?= $user['name']; ?></h3> <!-- user name -->
                        </div>
                    </div>

                    <div class="p-lg">
                        <div class="row">
                            <div class="col-lg-9">

                                <!-- Timeline -->
                                <ul class="timeline">
                                    <li data-datetime="Today" class="timeline-separator"></li>
                                    <li>
                                        <!-- default (no inverted) -->
                                        <div class="timeline-badge primary">
                                            <!-- replace: bgicon -->
                                            <em class="fa fa-comment"></em> <!-- replace: icon -->
                                        </div>
                                        <div class="timeline-panel">
                                            <div class="popover left">
                                                <div class="arrow"></div>
                                                <div class="popover-content">
                                                    <div class="table-grid table-grid-align-middle mb">
                                                        <div class="col col-xs">
                                                            <img src="<?= $user['avatar']; ?>" alt="Image"
                                                                class="media-object img-circle thumb48">
                                                        </div>
                                                        <div class="col">
                                                            <p class="m0">
                                                                <a href="#" class="text-muted">
                                                                    <strong><?= $user['name']; ?></strong>
                                                                </a>posted a comment</p> <!-- replace: description -->
                                                        </div>
                                                    </div>
                                                    <p>
                                                        <!-- replace: details -->
                                                        <em>"Fusce pellentesque congue justo in rutrum. Praesent non
                                                            nulla et ligula luctus mattis eget at lacus."</em>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </li>

                                    <li class="timeline-inverted">
                                        <!-- class: inverted -->
                                        <div class="timeline-badge info">
                                            <!-- replace: bgicon -->
                                            <em class="fa fa-file-o"></em> <!-- replace: icon -->
                                        </div>
                                        <div class="timeline-panel">
                                            <div class="popover right">
                                                <div class="arrow"></div>
                                                <div class="popover-content">
                                                    <div class="table-grid table-grid-align-middle mb">
                                                        <div class="col col-xs">
                                                            <img src="<?= $user['avatar']; ?>" alt="Image"
                                                                class="media-object img-circle thumb48">
                                                        </div>
                                                        <div class="col">
                                                            <p class="m0">
                                                                <a href="#" class="text-muted">
                                                                    <strong><?= $user['name']; ?></strong>
                                                                </a>shared new files</p> <!-- replace: description -->
                                                        </div>
                                                    </div>
                                                    <ul class="list-unstyled">
                                                        <!-- replace: detail -->
                                                        <li class="pb">
                                                            <em class="fa fa-file-o fa-fw mr"></em><a href="#"
                                                                class="text-info">framework-docs-part1.pdf<em
                                                                    class="pull-right fa fa-download fa-fw"></em></a>
                                                        </li>
                                                        <li class="pb">
                                                            <em class="fa fa-file-o fa-fw mr"></em><a href="#"
                                                                class="text-info">framework-docs-part2.pdf<em
                                                                    class="pull-right fa fa-download fa-fw"></em></a>
                                                        </li>
                                                        <li class="pb">
                                                            <em class="fa fa-file-o fa-fw mr"></em><a href="#"
                                                                class="text-info">framework-docs-part3.pdf<em
                                                                    class="pull-right fa fa-download fa-fw"></em></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <!-- End Timeline -->

                            </div>


                            <div class="col-lg-3">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div class="text-center">
                                            <h3 class="mt0"><?= $user['name']; ?></h3>
                                            <p>Role here...</p>
                                        </div>
                                        <hr>
                                        <ul class="list-unstyled ph-xl">
                                            <li>
                                                <em class="fa fa-home fa-fw mr-lg"></em><?= $user['address']; ?></li>
                                            <li>
                                                <em class="fa fa-phone fa-fw mr-lg"></em><a
                                                    href="#"><?= $user['phone']; ?></a>
                                            </li>
                                            <li>
                                                <em class="fa fa-briefcase fa-fw mr-lg"></em>Job here...</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="panel panel-default">
                                    <div class="panel-heading">Contacts</div>
                                    <div class="list-group">
                                        <a href="#" class="media p mt0 list-group-item">
                                            <span class="media-body">
                                                <span class="media-heading">
                                                    <strong><?= $user['phone']; ?></strong>
                                                    <br>
                                                    <small class="text-muted">Initial</small>
                                                </span>
                                            </span>
                                        </a>
                                    </div>

                                    <!-- ngRepeat: contacts -->
                                    <?php foreach($contacts as $c):?>
                                    <div class="list-group">
                                        <a href="#" class="media p mt0 list-group-item">
                                            <span class="media-body">
                                                <span class="media-heading">
                                                    <strong><?= $c['phonenumber']; ?></strong>
                                                    <br>
                                                    <small class="text-muted"># <?= $c['id']; ?></small>
                                                </span>
                                            </span>
                                        </a>
                                    </div>
                                    <?php endforeach;?>
                                    <!-- End ngRepeat: contacts -->

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php $this->load->view('templates/footer'); ?>

    </div>

    <?php $this->load->view('templates/starter/initbody'); ?>

</body>

</html>