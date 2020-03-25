<!-- Page Heading -->
<h1 class="h3 my-4 text-gray-800"><?= $title ?></h1>

<button class="js-new btn btn-primary mb-2" href="#" data-toggle="modal" data-target="#subcategoryform">Add New</button>
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
					<?php foreach($categories as $category):?>
					<tr>
						<td><?= $category['name']; ?></td>
						<td><?= $category['description']; ?></td>
						<td>
							<a href="" data-toggle="modal" data-target="#subcategoryform" class="js-update-category badge badge-success" data-id="<?= $category['id']; ?>">Update</a>
							<a href="" class="js-remove-category badge badge-danger" data-id="<?= $category['id']; ?>">Remove</a>
						</td>
					</tr>
					<?php endforeach;?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="modal fade" id="subcategoryform" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
      	<form method="post" class="js-form-subcategory" action="<?= base_url('admin/subcategory/insert'); ?>">
	        <div class="modal-header">
	          <h5 class="modal-title" id="modalTitle"></h5>
	          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
	            <span aria-hidden="true">×</span>
	          </button>
	        </div>
	        <div class="modal-body">
        		<div class="form-group">
        			<label class="form-label" for="name">Category Name: </label>
        			<input autocomplete="off" class="form-control" id="name" name="name"></input>
        		</div>
        		<div class="form-group">
        			<label class="form-label" for="description">Short Description: </label>
        			<textarea class="form-control" id="description" name="description" rows="3" cols="4"></textarea>
        		</div>
        		<div class="form-group">
        			<label class="form-label" for="description">Global Category: </label>
        			<select class="form-control" id="global_id" name="global_id">

        				<?php foreach($globals as $global): ?>
        				<option class="form-control" value="<?= $global['id']; ?>"><?= $global['name']; ?></option>
        				<?php endforeach; ?>
        				
        			</select>
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