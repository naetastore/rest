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
            <div class="text-lg mb-lg">404</div>
            <p class="lead m0">We couldn't find this page.</p>
            <p>The page you are looking for does not exists.</p>
         </div>
         <ul class="list-inline text-center text-sm mb-xl">
            <li><a href="<?= base_url('administrator?'); ?>" class="js-navlink text-muted">Go to App</a>
            </li>
            <li class="text-muted">|</li>
            <li><a href="<?= base_url('auth?'); ?>" class="js-navlink text-muted">Login</a>
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