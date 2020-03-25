<div class="container">

	<!-- Outer Row -->
	<div class="row justify-content-center">

	  <div class="col-xl-5 col-lg-7 col-md-4">

	    <div class="card o-hidden border-0 shadow-lg my-5">
	      <div class="card-body p-0">
	        <!-- Nested Row within Card Body -->
	        <div class="row">
	          <div class="col-lg">
	            <div class="p-5">
	              <div class="text-center">
	                <h1 class="h4 text-gray-900 mb-4">Rest Auth</h1>
	              </div>
	              <?= $this->session->flashdata('message'); ?>
	              <form class="user" action="<?= base_url('auth'); ?>" method="post">
	                <div class="form-group">
	                  <input autocomplete="off" type="text" class="form-control form-control-user" id="username" name="username" placeholder="Masukan nama pengguna..">
	                </div>
	                <div class="form-group">
	                  <input type="password" class="form-control form-control-user" id="password" name="password" placeholder="Password">
	                </div>
	                <button type="submit" class="btn btn-primary btn-user btn-block">
	                  Masuk
	                </button>
	              <div class="text-center mt-2">
	                <a class="small" href="#!">Buat Akun Baru!</a>
	              </div>
	            </div>
	          </div>
	        </div>
	      </div>
	    </div>

	  </div>

	</div>

</div>