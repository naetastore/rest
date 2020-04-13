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
            <h3>
               <?= $title; ?>
            </h3>

            <!-- ngRepeat: General Category -->
            <?php foreach($general as $g):?>
            <div class="panel panel-default">
               <table class="table table-striped">
                  <thead>
                     <tr>
                        <th style="width: 50%" class="h4">
                           <a href="#" style="text-decoration: none" class="js-new" data-globalid="<?= $g['id']; ?>"
                              data-toggle="modal" data-target="#subcategoryform">
                              <em class="icon-plus text-muted" data-globalid="<?= $g['id']; ?>"></em>
                           </a>
                           <?= $g['name']; ?>
                        </th>
                        <th class="h4 text-center">Posts</th>
                        <th class="h4 text-center">Selled</th>
                        <th class="h4 text-center">Ratio</th>
                        <th class="h4 hidden-xs hidden-sm">Updated</th>
                        <th class="h4 text-center">View</th>
                     </tr>
                  </thead>
                  <tbody id="<?= $g['id']; ?>">

                     <!-- ngRepeat: Category -->
                     <?php foreach($g['category'] as $c):?>
                     <tr class="js-categoryid-<?= $c['id']; ?>">
                        <td>
                           <h4>
                              <a href="<?= $c['url']; ?>" class="js-navlink">
                                 <strong><?= $c['name']; ?></strong>
                              </a>
                           </h4>
                           <div class="text-muted"><?= $c['description']; ?></div>
                        </td>
                        <td class="text-muted text-center">
                           <strong><?= $c['product']; ?></strong>
                        </td>
                        <td class="text-muted text-center">
                           <strong><?= $c['selled']; ?></strong>
                        </td>
                        <td class="text-muted text-center">
                           <strong><?= $c['ratio']; ?></strong>
                        </td>
                        <td class="hidden-xs hidden-sm">
                           <a href="<?= $c['updated']['user']['url']; ?>"
                              class="js-navlink"><?= $c['updated']['user']['username']; ?></a>
                           <br>
                           <small><?= $c['updated']['date']; ?></small>
                        </td>
                        <td class="text-center">
                           <button type="button" class="js-view btn btn-sm btn-default" data-toggle="modal"
                              data-target="#subcategoryform" data-globalid="<?= $g['id']; ?>"
                              data-id="<?= $c['id']; ?>">
                              <em class="js-view fa fa-search" data-globalid="<?= $g['id']; ?>"
                                 data-id="<?= $c['id']; ?>"></em>
                           </button>
                        </td>
                     </tr>
                     <?php endforeach;?>
                     <!-- End ngRepeat: Category -->

                  </tbody>
               </table>
            </div>
            <?php endforeach;?>
            <!-- End ngRepeat: General Category -->

         </div>
      </section>

      <?php $this->load->view('templates/footer'); ?>

   </div>

   <div class="modal fade" id="subcategoryform" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
         <div class="modal-content">
            <form method="post" class="js-form-subcategory" action="">
               <div class="modal-body">
                  <div class="form-group">
                     <label class="form-label" for="name">Category Name: </label>
                     <input class="form-control" id="name" name="name"></input>
                  </div>
                  <div class="form-group">
                     <label class="form-label" for="description">Short Description: <span id="charleft"></span> </label>
                     <textarea class="form-control" id="description" name="description" rows="3" cols="4"
                        maxLength="228" data-max="228"></textarea>
                  </div>
                  <input type="hidden" name="global_id" id="global_id">
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
   <script src="<?= base_url('src/js/category.js'); ?>"></script>

</body>

</html>