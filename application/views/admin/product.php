<!-- Page Heading -->
<h1 class="h3 my-4 text-gray-800"><?= $title ?></h1>

<button class="js-new btn btn-primary mb-2" href="#" data-toggle="modal" data-target="#productform">Add New</button>
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
						<th>Price</th>
						<th>Quantity</th>
						<th>Sub Category</th>
						<th>Description</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($products as $product):?>
					<tr>
						<td><?= $product['name']; ?></td>
						<td><?= $product['price']; ?></td>
						<td><?= $product['qty']; ?></td>
						<td><?= get_categories_where_product_id($product['category_id'])['name']; ?></td>
						<td><?= $product['description']; ?></td>
						<td>
							<a href="" class="js-update-product badge badge-success" data-toggle="modal" data-target="#productform" data-id="<?= $product['id']; ?>">Update</a>
							<a href="" class="js-remove-product badge badge-danger" data-id="<?= $product['id']; ?>">Remove</a>
						</td>
					</tr>
					<?php endforeach;?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="modal fade" id="productform" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
      	<form method="post" class="js-form-product" action="<?= base_url('admin/product/insert'); ?>" enctype="multipart/form-data" />
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
                            <img src="<?= base_url('assets/img/product/ex_product.jpg'); ?>" alt="preview" class="js-img-preview img-thumbnail">
                            <input required class="js-input-file form-control" type="file" name="image" id="image">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="description">Short Description: </label>
                            <textarea required class="form-control" id="description" name="description" rows="2" cols="4"></textarea>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="form-label" for="name">Name: </label>
                            <input required type="text" autocomplete="off" class="form-control" id="name" name="name"></input>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="price">Price: </label>
                                    <input required type="number" autocomplete="off" class="form-control" id="price" name="price"></input>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="qty">Quantity: </label>
                                    <input required type="number" autocomplete="off" class="form-control" id="qty" name="qty"></input>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="category_id">Category Name: </label>
                            <select required class="form-control" id="category_id" name="category_id">
                                <?php
                                    foreach($categories as $category):
                                ?>
                                <option class="form-control" value="<?= $category['id']; ?>"><?= $category['name']; ?></option>
                                <?php
                                    endforeach;
                                ?>
                            </select>
                        </div>
                        <div class="row">
                             <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="suggested">Is Suggested: </label>
                                    <select required class="form-control" id="suggested" name="suggested">
                                        <option class="form-control" value="1">True</option>
                                        <option class="form-control" value="0">False</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- tambahi is ready -->
                                <div class="form-group">
                                    <label class="form-label" for="is_ready">Is Ready: </label>
                                    <select required class="form-control" id="is_ready" name="is_ready">
                                        <option class="form-control" value="1">True</option>
                                        <option class="form-control" value="0">False</option>
                                    </select>
                                </div>
                                <!-- end of tambahi is ready -->
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="seo_keyword">SEO Keyword: </label>
                            <textarea required class="form-control" id="seo_keyword" name="seo_keyword" rows="2" cols="4"></textarea>
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