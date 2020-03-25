<!-- Page Heading -->
<h1 class="h3 my-4 text-gray-800"><?= $title ?></h1>

<p class="mb-4">Data ini nantinya akan menjadi kategori umum dari sebuah Aplikasi.</p>

<div class="card shadow mb-4">
<div class="card-header py-3">
	<h6 class="m-0 font-weight-bold text-primary">DataTables Example</h6>
</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>Nama</th>
						<th>Deskripsi</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
					<th>Nama</th>
					<th>Deskripsi</th>
					</tr>
				</tfoot>
				<tbody>
					<?php foreach($globalcategory as $global):?>
					<tr>
						<td><?= $global['name']; ?></td>
						<td><?= $global['description']; ?></td>
					</tr>
					<?php endforeach;?>
				</tbody>
			</table>
		</div>
	</div>
</div>