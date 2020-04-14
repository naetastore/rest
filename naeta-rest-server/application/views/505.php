<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
   <meta name="description" content="">
   <meta name="keywords" content="">
   <link rel="shortcut icon" href="<?= base_url('src/img/naetastore.svg'); ?>">
   <title></title>

   <?php $this->load->view('templates/starter/inithead'); ?>

</head>

<body>
   <div class="wrapper">

      <div class="abs-center wd-xl">
         <div class="text-center mb-xl">
            <div class="mb-lg">
               <em class="fa fa-wrench fa-5x text-muted"></em>
            </div>
            <div class="text-lg mb-lg">500</div>
            <p class="lead m0">Oh! Something went wrong :(</p>
            <p>Don't worry, we're now checking this.</p>
            <p>In the meantime, please try one of those links below or come back in a moment</p>
         </div>
         <ul class="list-inline text-center text-sm mb-xl">
            <li><a href="<?= base_url('admin/base?'); ?>" class="js-navlink text-muted">Go to App</a>
            </li>
            <li class="text-muted">|</li>
            <li><a href="<?= base_url('admin/base?'); ?>" class="js-navlink text-muted">Login</a>
            </li>
         </ul>
         <div class="p-lg text-center">
            <?php $this->load->view('templates/footer'); ?>
         </div>
      </div>

   </div>

   <?php $this->load->view('templates/starter/initbody'); ?>

</body>

</html>