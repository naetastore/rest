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
            <div class="panel panel-default">
               <div class="panel-heading">Order Information</div>
               <div class="panel-body">
                  <div class="row">

                     <div class="col-md-6">
                        <p class="lead bb">Details</p>
                        <form class="form-horizontal p-20">
                           <div class="form-group">
                              <div class="col-sm-4">Order ID:</div>
                              <div class="col-sm-8">
                                 <strong>#<?= $data['order']['id'] ?></strong>
                              </div>
                           </div>
                           <div class="form-group">
                              <div class="col-sm-4">Ordered On:</div>
                              <div class="col-sm-8">
                                 <strong><?= $data['order']['created']; ?></strong>
                              </div>
                           </div>
                           <div class="form-group">
                              <div class="col-sm-4">Client Name:</div>
                              <div class="col-sm-8">
                                 <strong>
                                    <a href="<?= $data['order']['consumer']['url']; ?>" class="js-navlink">
                                       <?= $data['order']['consumer']['name']; ?>
                                    </a>
                                 </strong>
                              </div>
                           </div>
                           <div class="form-group">
                              <div class="col-sm-4">Items:</div>
                              <div class="col-sm-8">
                                 <strong><?= $data['order']['qty']; ?></strong>
                              </div>
                           </div>
                           <div class="form-group">
                              <div class="col-sm-4">Amount:</div>
                              <div class="col-sm-8">
                                 <strong><?= $data['order']['currency'] . " " . $data['order']['amount']; ?></strong>
                              </div>
                           </div>
                           <div class="form-group">
                              <div class="col-sm-4">Status</div>
                              <div class="col-sm-8">
                                 <div class="label <?= $data['order']['status']['textcolor']; ?>">
                                    <?= $data['order']['status']['name']; ?></div>
                              </div>
                           </div>
                        </form>
                     </div>

                     <div class="col-md-6">
                        <p class="lead bb">Client</p>
                        <form class="form-horizontal p-20">
                           <div class="form-group">
                              <div class="col-sm-4">Client ID:</div>
                              <div class="col-sm-8">
                                 <strong>#<?= $data['order']['consumer']['id'] ?></strong>
                              </div>
                           </div>
                           <div class="form-group">
                              <div class="col-sm-4">Name:</div>
                              <div class="col-sm-8">
                                 <strong>
                                    <a href="<?= $data['order']['consumer']['url']; ?>" class="js-navlink">
                                       <?= $data['order']['consumer']['name']; ?>
                                    </a>
                                 </strong>
                              </div>
                           </div>
                           <div class="form-group">
                              <div class="col-sm-4">Phone:</div>
                              <div class="col-sm-8">
                                 <strong><?= $data['order']['consumer']['phone'] ?></strong>
                              </div>
                           </div>
                           <div class="form-group">
                              <div class="col-sm-4">Status</div>
                              <div class="col-sm-8">
                                 <div class="label label-success">Active</div>
                              </div>
                           </div>
                        </form>
                     </div>

                  </div>
                  <div class="row">

                     <!-- ngRepeat: Billing -->
                     <?php $d=[ 'Billing Address', 'Shipping Address' ]; for($i=0; $i<2; $i++):?>
                     <div class="col-md-6">
                        <p class="lead bb"><?= $d[$i]; ?></p>
                        <form class="form-horizontal p-20">
                           <div class="form-group">
                              <div class="col-sm-4">Name</div>
                              <div class="col-sm-8">
                                 <a href="<?= $data['order']['consumer']['url']; ?>" class="js-navlink">
                                    <?= $data['order']['consumer']['name']; ?>
                                 </a>
                              </div>
                           </div>
                           <div class="form-group">
                              <div class="col-sm-4">Address:</div>
                              <div class="col-sm-8">
                                 <strong><?= $data['order']['consumer']['address']; ?></strong>
                              </div>
                           </div>
                           <div class="form-group">
                              <div class="col-sm-4">Phone</div>
                              <div class="col-sm-8">
                                 <strong><?= $data['order']['consumer']['phone']; ?></strong>
                              </div>
                           </div>
                        </form>
                     </div>
                     <?php endfor;?>
                     <!-- End ngRepeat: Billing -->

                  </div>
                  <div class="alert alert-warning">
                     <em class="fa fa-exclamation-circle fa-lg fa-fw"></em>Shipping address is different than billing
                     address</div>
               </div>
            </div>

            <div class="panel">
               <div class="panel-heading">Products in order</div>
               <div class="table-responsive">
                  <table class="table table-hover table-bordered table-striped">
                     <thead>
                        <tr>
                           <th>Product ID</th>
                           <th>Price</th>
                           <th>Quantity</th>
                           <th class="text-center">Status</th>
                           <th>Total</th>
                        </tr>
                     </thead>
                     <tbody>

                        <!-- ngRepeat: products -->
                        <?php foreach($data['product'] as $p):?>
                        <tr>
                           <td><a href="#" class="js-productview" data-toggle="modal" data-target="#productform" data-id="<?= $p['product_id']; ?>">Product #<?= $p['product_id']; ?></a></td>
                           <td><?= $p['currency'] . " " . $p['price'] ?></td>
                           <td><?= $p['qty']; ?></td>
                           <td class="text-center">
                              <span class="label <?= $p['status']['textcolor']; ?>"><?= $p['status']['name']; ?></span>
                           </td>
                           <td><?= $p['currency'] . " " . $p['total']; ?></td>
                        </tr>
                        <?php endforeach;?>
                        <!-- End ngRepeat: products -->

                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </section>

      <?php $this->load->view('templates/footer'); ?>

   </div>

   <div class="modal fade" id="productform" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-4">
                     <div class="form-group text-center">
                        <img src="" alt="preview" class="js-img-preview img-thumbnail">
                     </div>
                     <div class="form-group">
                        <label class="form-label" for="description">Description: <span id="charleft"></span>
                        </label>
                        <p id="description"></p>
                     </div>
                  </div>
                  <div class="col-md-8">
                     <div class="form-group">
                        <strong id="name"></strong>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="form-label" for="price">Price: </label>
                              <p id="price"></p>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="form-label" for="qty">Quantity: </label>
                              <p id="qty"></p>
                           </div>
                        </div>
                     </div>
                     
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="form-label" for="suggested">Is Suggested: </label>
                              <p id="suggested"></p>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="form-label" for="is_ready">Is Ready: </label>
                              <p id="is_ready"></p>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button class="btn btn-inverse btn-lg" type="button" data-dismiss="modal">Close</button>
            </div>
         </div>
      </div>
   </div>

   <?php $this->load->view('templates/starter/initbody'); ?>

   <script src="<?= base_url('src/js/orderview.js'); ?>"></script>

</body>

</html>