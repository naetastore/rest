<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="<?= $metadescription ?>">
	<meta name="author" content="<?= $metaauthor ?>">
	<link rel="stylesheet" href="<?= base_url('src/') ?>css/bootstrap.min.css">
	<link rel="stylesheet" href="<?= base_url('src/') ?>css/auth.css">
	<title></title>
</head>
<body>
	<div class="container">
		<div class="row login">
			<div class="col-xl-10 col-lg-12 col-md-9">
				<div class="card o-hidden border-0 shadow-lg my-5">
					<div class="card-body p-0">
						<div class="row">
							<div class="col-lg-6 d-none d-lg-block bg-login-image text-center p-4 text-white">
							</div>
			  				<div class="col-lg-6">
							  	<div class="p-5">
									<p class="text-center">Enter your login details to access your dashboard</p>
									<div class="text-danger" id="alert"></div>
									<div class="form-group">
										<input autocomplete="off" type="text" id="username" name="username" placeholder="Username">
									</div>
									<div class="form-group">
										<input type="password" id="password" name="password" placeholder="Your Password">
									</div>
									<div class="text-center">
										<button type="button" class="js-signin btn-signin">Continue</button>
									</div>
									<div class="copyright text-center mt-3">
										<span>Copyright &copy; <?= date('Y', time()); ?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="<?= base_url('src/vendor/jquery/dist/jquery.js'); ?>"></script>
	<script>window.sessionStorage.setItem('naetastore_base', '<?= base_url(); ?>')</script>
	<script src="<?= base_url('src/js/auth.js'); ?>"></script>
</body>
</html>