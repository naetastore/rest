<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
   <meta name="description" content="<?= $metadescription ?>">
   <meta name="keywords" content="<?= $metakeyword ?>">
   <link rel="shortcut icon" href="<?= base_url('src/img/naetastore.svg'); ?>">
   <title><?= $title ?></title>

   <!-- Starter Template -->
   <?php $this->load->view('templates/starter/inithead'); ?>

</head>

<body>
   <div class="wrapper">

      <?php $this->load->view('templates/header'); ?>
      <?php $this->load->view('templates/sidebar'); ?>
      <?php $this->load->view('templates/offsidebar'); ?>

      <section>
         <div class="content-wrapper">
            <div class="content-heading">
               <div class="pull-right">
                  <div class="btn-group">
                     <button type="button" class="btn btn-primary">Generate Report</button>
                  </div>
               </div>
               <?= $title; ?>
            </div>
            <div class="row">

               <!-- ngRepeat: widget statistics -->
               <?php foreach($statistic as $s):?>
               <div class="col-lg-3 col-sm-6">
                  <div class="panel widget <?= $s['bgcolor']; ?>">
                     <div class="row row-table">
                        <div class="col-xs-4 text-center <?= $s['bgcolor']; ?>-dark pv-lg">
                           <em class="<?= $s['icon']; ?> fa-3x"></em>
                        </div>
                        <div class="col-xs-8 pv-lg">
                           <div class="h2 mt0"><?= $s['count']; ?></div>
                           <div class="text-uppercase"><?= $s['name']; ?></div>
                        </div>
                     </div>
                  </div>
               </div>
               <?php endforeach;?>
               <!-- End ngRepeat: widget statistics -->

               <div class="col-lg-3 col-md-6 col-sm-12">
                  <div class="panel widget">
                     <div class="row row-table">
                        <div class="col-xs-4 text-center bg-green pv-lg">
                           <!-- See formats: https://docs.angularjs.org/api/ng/filter/date-->
                           <div data-now="" data-format="MMMM" class="text-sm"></div>
                           <br>
                           <div data-now="" data-format="D" class="h2 mt0"></div>
                        </div>
                        <div class="col-xs-8 pv-lg">
                           <div data-now="" data-format="dddd" class="text-uppercase"></div>
                           <br>
                           <div data-now="" data-format="h:mm" class="h2 mt0"></div>
                           <div data-now="" data-format="a" class="text-muted text-sm"></div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>

            <div class="row">
               <!-- Dashboard main content-->
               <div class="col-lg-9">
                  <div class="row">
                     <div class="col-lg-12">
                        <div id="panelChart9" class="panel panel-default panel-demo">
                           <div class="panel-heading">
                              <a href="#" data-tool="panel-refresh"
                                 class="pull-right">
                                 <em class="fa fa-refresh"></em>
                              </a>
                              <a href="#" data-tool="panel-collapse"
                                 class="pull-right">
                                 <em class="fa fa-minus"></em>
                              </a>
                              <div class="panel-title">Inbound visitor statistics</div>
                           </div>
                           <div class="panel-body">
                              <div class="chart-spline flot-chart"></div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="row">
                     <div class="col-lg-4">
                        <div class="panel widget">
                           <div class="panel-body">
                              <div class="clearfix">
                                 <h3 class="pull-left text-muted mt0">300</h3>
                                 <em class="pull-right text-muted fa fa-coffee fa-2x"></em>
                              </div>
                              <div data-sparkline="" data-type="line" data-height="80" data-width="100%" data-line-width="2" data-line-color="#7266ba" data-spot-color="#888" data-min-spot-color="#7266ba" data-max-spot-color="#7266ba" data-fill-color=""
                              data-highlight-line-color="#fff" data-spot-radius="3" data-values="1,3,4,7,5,9,4,4,7,5,9,6,4" data-resize="true" class="pv-lg"></div>
                              <p>
                                 <small class="text-muted">Actual progress</small>
                              </p>
                              <div class="progress progress-xs">
                                 <div role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 80%" class="progress-bar progress-bar-info progress-bar-striped">
                                    <span class="sr-only">80% Complete</span>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-8">
                        <div class="panel panel-default">
                           <div class="panel-heading">
                              <div class="panel-title">Team messages</div>
                           </div>
                           <div data-height="180" data-scrollable="" class="list-group">
                              
                              <!-- ngRepeat: message -->
                              <a href="#" class="list-group-item">
                                 <div class="media-box">
                                    <div class="pull-left">
                                       <img src="<?= base_url('src/img/avatar/default.svg'); ?>" alt="Image" class="media-box-object img-circle thumb32">
                                    </div>
                                    <div class="media-box-body clearfix">
                                       <small class="pull-right">2h</small>
                                       <strong class="media-box-heading text-primary">
                                          Andi Naeta
                                       </strong>
                                       <p class="mb-sm">
                                          <small>Cras sit amet nibh libero, in gravida nulla. Nulla...</small>
                                       </p>
                                    </div>
                                 </div>
                              </a>
                              <!-- End ngRepeat: message -->

                           </div>
                           <div class="panel-footer clearfix">
                              <div class="input-group">
                                 <input type="text" placeholder="Search message .." class="form-control input-sm">
                                 <span class="input-group-btn">
                                    <button type="submit" class="btn btn-default btn-sm"><i class="fa fa-search"></i>
                                    </button>
                                 </span>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- Include new main content above -->
               </div>
               <!-- End Dashboard main content-->

               <!-- Dashboard side content-->
               <aside class="col-lg-3">
                  <div class="panel panel-default">
                     <div class="panel-body">
                        <a href="#" class="text-muted pull-right">
                           <em class="fa fa-arrow-right"></em>
                        </a>
                        <div class="text-info">Average Monthly Uploads</div>
                        <canvas data-classyloader="" data-speed="20" data-font-size="40px" data-line-color="#23b7e5" data-remaining-line-color="rgba(200,200,200,0.4)" data-line-width="10" data-rounded-line="true" class="center-block" data-diameter="70"
                           data-percentage="70"></canvas> <!-- Replace: data-percentage -->
                        <div data-sparkline="" data-bar-color="#23b7e5" data-height="30" data-bar-width="5" data-bar-spacing="2" class="text-center"
                           data-values="5,4,8,7,8,5,4,6,5,5,9,4,6,3,4,7,5,4,7"></div> <!-- Replace: data-values -->
                     </div>
                     <div class="panel-footer">
                        <p class="text-muted">
                           <em class="fa fa-upload fa-fw"></em>
                           <span>This Month</span>
                           <span class="text-dark">1000 Gb</span>
                        </p>
                     </div>
                  </div>

                  <div class="panel panel-default">
                     <div class="panel-heading">
                        <div class="panel-title">Latest activities</div>
                     </div>
                     <div class="list-group">

                        <!-- ngRepeat: activities -->
                        <?php foreach($activity as $a):?>
                        <div class="list-group-item">
                           <div class="media-box">
                              <div class="pull-left">
                                 <span class="fa-stack">
                                    <em class="fa fa-circle fa-stack-2x <?= $a['backcolor']; ?>"></em>
                                    <em class="fa <?= $a['icon']; ?> fa-stack-1x fa-inverse <?= $a['textcolor'] ?>"></em>
                                 </span>
                              </div>
                              <div class="media-box-body clearfix">
                                 <small class="text-muted pull-right ml"><?= $a['created']; ?></small>
                                 <div class="media-box-heading">
                                    <a href="<?= $a['url']; ?>" class="js-navlink <?= $a['backcolor']; ?> m0"><?= $a['description']; ?></a>
                                 </div>
                                 <p class="m0">
                                    <small>
                                       <a href="<?= $a['url']; ?>" class="js-navlink"><?= $a['name']; ?></a>
                                    </small>
                                 </p>
                              </div>
                           </div>
                        </div>
                        <?php endforeach;?>
                        <!-- End ngRepeat: activities -->

                     </div>
                     <div class="panel-footer clearfix">
                        <a href="#" class="pull-left">
                           <small>Load more</small>
                        </a>
                     </div>
                  </div>
                  <!-- Include new side content above -->
               </aside>
               <!-- End Dashboard side content-->
            </div>
         </div>
      </section>

      <?php $this->load->view('templates/footer'); ?>

   </div>

   <!-- Starter Template -->
   <?php $this->load->view('templates/starter/initbody'); ?>
   <!-- SPARKLINE-->
   <script src="<?= base_url('src/'); ?>vendor/sparkline/index.js"></script>
   <!-- FLOT CHART-->
   <script src="<?= base_url('src/'); ?>vendor/Flot/jquery.flot.js"></script>
   <script src="<?= base_url('src/'); ?>vendor/flot.tooltip/js/jquery.flot.tooltip.min.js"></script>
   <script src="<?= base_url('src/'); ?>vendor/Flot/jquery.flot.resize.js"></script>
   <script src="<?= base_url('src/'); ?>vendor/Flot/jquery.flot.pie.js"></script>
   <script src="<?= base_url('src/'); ?>vendor/Flot/jquery.flot.time.js"></script>
   <script src="<?= base_url('src/'); ?>vendor/Flot/jquery.flot.categories.js"></script>
   <script src="<?= base_url('src/'); ?>vendor/flot-spline/js/jquery.flot.spline.min.js"></script>
   <!-- CLASSY LOADER-->
   <script src="<?= base_url('src/'); ?>vendor/jquery-classyloader/js/jquery.classyloader.min.js"></script>
   <!-- MOMENT JS-->
   <script src="<?= base_url('src/'); ?>vendor/moment/min/moment-with-locales.min.js"></script>

   <script src="<?= base_url('src/'); ?>js/base.js"></script>

</body>

</html>