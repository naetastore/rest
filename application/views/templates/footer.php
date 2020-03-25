			</div>
	        <!-- /.container-fluid -->

	    </div>
	    <!-- End of Main Content -->

    <footer class="sticky-footer bg-white">
      <div class="container my-auto">
        <div class="copyright text-center my-auto">
          <span>Copyright &copy; All right reserved <?= date('Y', time()); ?></span>
        </div>
      </div>
    </footer>

	</div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Bootstrap core JavaScript-->
  <script src="<?= base_url('assets/vendor/jquery/jquery.min.js'); ?>"></script>
  <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>

  <!-- Core plugin JavaScript-->
  <script src="<?= base_url('assets/vendor/jquery-easing/jquery.easing.min.js'); ?>"></script>

  <!-- Custom scripts for all pages-->
  <script src="<?= base_url('assets/js/sb-admin-2.min.js'); ?>"></script>

  <!-- Page level plugins -->
  <script src="<?= base_url('assets/vendor/datatables/jquery.dataTables.min.js'); ?>"></script>
  <script src="<?= base_url('assets/vendor/datatables/dataTables.bootstrap4.min.js'); ?>"></script>

  <!-- Page level custom scripts -->
  <script src="<?= base_url('assets/js/demo/datatables-demo.js'); ?>"></script>

  <script type="text/javascript">
    $(function(){

      $('.js-new').on('click', function() {
        console.log($('form').serialize());
      });

      $('.js-input-file').on('change', function(e) {
        const reader = new FileReader();
        const image = e.target.files[0];
        reader.onloadend = function() {
          $('.js-img-preview').attr('src', reader.result);
        }
        reader.readAsDataURL(image);
      });


      $('.js-remove-globalcategory').on('click', function(e){
        e.preventDefault();
        const id = $(this).data('id');
        if (window.confirm('Are you sure want ro remove?')){
          $.ajax({
            url: `<?= base_url('admin/globalcategory/remove'); ?>/${id}`,
            method: 'post',
            success: () => window.location.href = "<?= base_url('admin/globalcategory'); ?>"
          }); 
        }
      });


      $('.js-remove-category').on('click', function(e){
        e.preventDefault();
        const id = $(this).data('id');
        if (window.confirm('Are you sure want ro remove?')){
          $.ajax({
            url: `<?= base_url('admin/subcategory/remove'); ?>/${id}`,
            method: 'post',
            success: () => window.location.href = "<?= base_url('admin/subcategory'); ?>"
          }); 
        }
      });

      $('.js-remove-product').on('click', function(e){
        e.preventDefault();
        const id = $(this).data('id');
        if (window.confirm('Are you sure want ro remove?')){
          $.ajax({
            url: `<?= base_url('admin/product/remove'); ?>/${id}`,
            method: 'post',
            success: () => window.location.href = "<?= base_url('admin/product'); ?>"
          }); 
        }
      });

      $('.js-update-globalcategory').on('click', function(e){
        e.preventDefault();
        const id = $(e.target).data('id');
        const action = `<?= base_url('admin/globalcategory/update/'); ?>/${id}`

        $.ajax({
          url: `<?= base_url('admin/globalcategory/show'); ?>/${id}`,
          type: 'post',
          dataType: 'json',
          success: res => {
            $('#name').val(res.name);
            $('#description').val(res.description);
            $('.js-img-preview').attr('src', `<?= base_url('assets/img/global/'); ?>` + res.image);
            $('.js-input-file').removeAttr('required');
            $('.js-form-globals').attr('action', action);
          }
        });
      });

      $('.js-update-category').on('click', function(e) {
        e.preventDefault();
        const id = $(e.target).data('id');
        const action = `<?= base_url('admin/subcategory/update/'); ?>/${id}`;

        $.ajax({
          url: `<?= base_url('admin/subcategory/show/'); ?>${id}`,
          type: 'post',
          dataType: 'json',
          success: res => {
            $('#name').val(res.name);
            $('#description').val(res.description);
            $('#global_id').val(res.global_id);
            $('.js-form-subcategory').attr('action', action);
          }
        });

      });

      $('.js-update-product').on('click', function(e) {
        e.preventDefault();
        const id = $(e.target).data('id');
        const action = `<?= base_url('admin/product/update/'); ?>/${id}`

        $.ajax({
          url: `<?= base_url('admin/product/show/'); ?>${id}`,
          type: 'post',
          dataType: 'json',
          success: res => {
            $('.js-form-product').attr('action', action);
            $('#name').val(res.name);
            $('#price').val(res.price);
            $('#qty').val(res.qty);
            $('#category_id').val(res.category_id);
            $('#description').val(res.description);
            $('#seo_keyword').val(res.seo_keyword);
            $('.js-img-preview').attr('src', `<?= base_url('assets/img/product/'); ?>` + res.image);
            $('.js-input-file').removeAttr('required');

            let yes = `<option class="form-control" value="1">True</option>`;
            let no = `<option class="form-control" value="0">False</option>`;
            if (Number(res.suggested) === 0) {
              [yes, no] = [no, yes];
            }
            $('#suggested').html(yes + no);


            let ya = `<option class="form-control" value="1">True</option>`;
            let enggak = `<option class="form-control" value="0">False</option>`;
            if (Number(res.is_ready) === 0) {
              [ya, enggak] = [enggak, ya];
            }
            $('#is_ready').html(ya + enggak);
          }
        });
      });

    });
  </script>

</body>

</html>