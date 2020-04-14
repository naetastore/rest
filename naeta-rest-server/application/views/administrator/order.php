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
   <link rel="stylesheet" href="<?= base_url('src/'); ?>vendor/datatables-colvis/css/dataTables.colVis.css">
   <link rel="stylesheet" href="<?= base_url('src/'); ?>vendor/datatables/media/css/dataTables.bootstrap.css">
   <link rel="stylesheet" href="<?= base_url('src/'); ?>vendor/dataTables.fontAwesome/index.css">

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
            <div class="container-fluid">
               <div class="row">
                  <div class="col-lg-12">
                     <div class="panel panel-default">
                        <div class="panel-body">
                           <div class="table-responsive">
                              <table id="datatable1" class="table table-striped table-hover">
                                 <thead>
                                    <tr>
                                       <th style="width:80px"><strong>ORDER ID</strong></th>
                                       <th><strong>ORDERED ON</strong></th>
                                       <th><strong>CLIENT NAME</strong></th>
                                       <th><strong>AMOUNT</strong></th>
                                       <th><strong>QUANTITY</strong></th>
                                       <th class="text-center"><strong>STATUS</strong></th>
                                       <th class="text-center"><strong>VIEW</strong></th>
                                    </tr>
                                 </thead>
                                 <tbody>

                                    <!-- ngRepeat: order -->
                                    <?php foreach($order as $r):?>
                                    <tr>
                                       <td><?= $r['id']; ?></td>
                                       <td><?= $r['created']; ?></td>
                                       <td><a href="<?= $r['consumer']['url']; ?>" class="js-navlink"><?= $r['consumer']['name']; ?></a></td>
                                       <td><?= $r['currency'] . " " . $r['amount']; ?></td>
                                       <td><?= $r['qty']; ?></td>
                                       <td class="text-center">
                                          <span class="label <?= $r['status']['textcolor']; ?>"><?= $r['status']['name']; ?></span>
                                       </td>
                                       <td class="text-center">
                                          <a href="<?= base_url('administrator/showorder?id=' . $r['id'] . '&') ?>" class="js-navlink btn btn-sm btn-default">
                                             <em class="fa fa-search"></em>
                                          </a>
                                       </td>
                                    </tr>
                                    <?php endforeach;?>
                                    <!-- End ngRepeat: order -->

                                 </tbody>
                              </table>
                           </div>
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

   <!-- =============== PAGE VENDOR SCRIPTS ===============-->
   <!-- DATATABLES-->
   <script src="<?= base_url('src/'); ?>vendor/datatables/media/js/jquery.dataTables.min.js"></script>
   <script src="<?= base_url('src/'); ?>vendor/datatables-colvis/js/dataTables.colVis.js"></script>
   <script src="<?= base_url('src/'); ?>vendor/datatables/media/js/dataTables.bootstrap.js"></script>
   <script src="<?= base_url('src/'); ?>vendor/datatables-buttons/js/dataTables.buttons.js"></script>
   <script src="<?= base_url('src/'); ?>vendor/datatables-buttons/js/buttons.bootstrap.js"></script>
   <script src="<?= base_url('src/'); ?>vendor/datatables-buttons/js/buttons.colVis.js"></script>
   <script src="<?= base_url('src/'); ?>vendor/datatables-buttons/js/buttons.flash.js"></script>
   <script src="<?= base_url('src/'); ?>vendor/datatables-buttons/js/buttons.html5.js"></script>
   <script src="<?= base_url('src/'); ?>vendor/datatables-buttons/js/buttons.print.js"></script>
   <script src="<?= base_url('src/'); ?>vendor/datatables-responsive/js/dataTables.responsive.js"></script>
   <script src="<?= base_url('src/'); ?>vendor/datatables-responsive/js/responsive.bootstrap.js"></script>
   <script src="<?= base_url('src/'); ?>js/order.js"></script>

</body>

</html>