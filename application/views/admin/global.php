<!-- Page Heading -->
<h1 class="h3 my-4 text-gray-800"><?= $title ?></h1>

<p class="mb-4">Data ini nantinya akan menjadi kategori umum dari sebuah Aplikasi.</p>

<button class="js-new btn btn-primary mb-2" href="#" data-toggle="modal" data-target="#globalcategoryform">Add New</button>
<?= $this->session->flashdata('message'); ?>

<div class="card shadow mb-4">
	<div class="card-header py-3">
		<h6 class="m-0 font-weight-bold text-primary"><?= $title ?></h6>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Name</th>
						<th>Description</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($globalcategory as $global):?>
					<tr>
						<td><?= $global['name']; ?></td>
						<td><?= $global['description']; ?></td>
						<td>
							<a href="" class="js-update-globalcategory badge badge-success" data-id="<?= $global['id']; ?>" data-toggle="modal" data-target="#globalcategoryform">Update</a>
							<a href="" class="js-remove-globalcategory badge badge-danger" data-id="<?= $global['id']; ?>">Remove</a>
						</td>
					</tr>
					<?php endforeach;?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="modal fade" id="globalcategoryform" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
      	<form method="post" class="js-form-globals" action="<?= base_url('admin/globalcategory/insert'); ?>" enctype="multipart/form-data" />
	        <div class="modal-header">
	          <h5 class="modal-title" id="exampleModalLabel"></h5>
	          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
	            <span aria-hidden="true">×</span>
	          </button>
	        </div>
	        <div class="modal-body">
	        	<div class="row">
                    <div class="col-md-4">
                    	<div class="form-group">
                            <label class="form-label" for="image">Image: </label>
                            <img src="<?= base_url('assets/img/product/ex_product.jpg'); ?>" alt="preview" class="js-img-preview img-thumbnail" required>
                            <input required class="js-input-file form-control" type="file" name="image" id="image">
                        </div>
                        <div class="form-group">
			    			<label class="form-label" for="description">Short Description: </label>
			    			<textarea class="form-control" id="description" name="description" rows="2" cols="4" placeholder="Maksimum 228 karakter.."></textarea>
			    		</div>
			    	</div>
			    	<div class="col-md-8">
		        		<div class="form-group">
		        			<label class="form-label" for="name">Category Name: </label>
		        			<input autocomplete="off" class="form-control" id="name" name="name" placeholder="Kategori umum.."></input>
		        		</div>
		        	</div>
		        </div>
	    	</div>
	        <div class="modal-footer">
	          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
	          <button class="btn btn-primary" type="submit">Save Changes</button>
	        </div>
		</form>
      </div>
    </div>
</div>