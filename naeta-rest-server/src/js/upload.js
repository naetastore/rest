(function (window, document, $, undefined) {

    $(function () {

        var dtInstance2 = $('#datatable2').dataTable({
            'paging': true,  // Table pagination
            'ordering': true,  // Column ordering
            'info': true,  // Bottom left status text
            'responsive': true, // https://datatables.net/extensions/responsive/examples/
            // Text translation options
            // Note the required keywords between underscores (e.g _MENU_)
            oLanguage: {
                sSearch: 'Search all columns:',
                sLengthMenu: '_MENU_ records per page',
                info: 'Showing page _PAGE_ of _PAGES_',
                zeroRecords: 'Nothing found - sorry',
                infoEmpty: 'No records available',
                infoFiltered: '(filtered from _MAX_ total records)'
            }
        });
        var inputSearchClass = 'datatable_input_col_search';
        var columnInputs = $('tfoot .' + inputSearchClass);

        // On input keyup trigger filtering
        columnInputs
            .keyup(function () {
                dtInstance2.fnFilter(this.value, columnInputs.index(this));
            });

        $('.js-file-input-button').on('click', function () {
            $('.js-file-input').click();
        });

        $('.js-new').on('click', function () {
            initform();
        });


        function initform() {
            const removeButon = $('.js-remove');
            removeButon[0].style.setProperty('display', 'none');

            $('.js-form-product').attr('action', `${baseurl}supplier/addproduct?${requiredparams}`);
            $('#name').val('');
            $('#price').val('');
            $('#qty').val('');
            $('#category').val('');
            $('#description').val('');
            $('#seo_keyword').val('');
            $('.js-img-preview').attr('src', `${baseurl}src/img/product/dummy_image.jpg`);
            $('.js-file-input-button').html('Add one photo...');
            $('.js-remove').attr('data-id', '');
            $('.js-remove').attr('data-globalid', '');
            $('.js-file-input')[0].setAttribute('required', '');

            let yes = `<option class="form-control" value="1">True</option>`;
            let no = `<option class="form-control" value="0">False</option>`;
            $('#suggested').html(yes + no);

            let ya = `<option class="form-control" value="1">True</option>`;
            let enggak = `<option class="form-control" value="0">False</option>`;
            $('#is_ready').html(ya + enggak);
        }

        $(document).on('click', function (e) {

            if (e.target.classList.contains('js-remove')) {
                const id = e.target.dataset.id;
                const global_id = e.target.dataset.globalid;
                const image = e.target.dataset.image;

                const qty = Number(e.target.dataset.qty);
                const counterproducts = Number($('#counteritems')[0].innerText);
                const counterstocks = Number($('#counterstocks')[0].innerText);

                if (!window.confirm('Are you sure want ro remove?')) return;
                $.ajax({
                    url: `${baseurl}supplier/removeproduct?id=${id}&global_id=${global_id}&image=${image}&${requiredparams}`,
                    method: 'get',
                    success: () => {
                        $('.js-cancel').click();
                        $('#counterproducts').html(counterproducts - 1);
                        $('#counterstocks').html(counterstocks - qty);
                        $(`.js-productid-${id}`).remove();
                    }
                });
            }

            if (e.target.classList.contains('js-view')) {
                const id = $(e.target).data('id');
                const action = `${baseurl}supplier/updateproduct?id=${id}&${requiredparams}`;
                const removeButon = $('.js-remove');
                removeButon[0].style.removeProperty('display');

                $.ajax({
                    url: `${baseurl}supplier/showproduct?id=${id}&${requiredparams}`,
                    type: 'get',
                    dataType: 'json',
                    success: res => {
                        $('.js-form-product').attr('action', `${action}&oldimage=${res.image}`);
                        $('#name').val(res.name);
                        $('#price').val(res.price);
                        $('#qty').val(res.qty);
                        $('#category').val(`${res.category_id},${res.global_id}`);
                        $('#description').val(res.description);
                        $('#seo_keyword').val(res.seo_keyword);

                        $('.js-img-preview').attr('src', `${baseurl}src/img/product/${res.image}`);
                        $('.js-file-input-button').html('Change photo...');
                        $('.js-file-input').removeAttr('required');

                        $('.js-remove').attr('data-id', res.id);
                        $('.js-remove').attr('data-globalid', res.global_id);
                        $('.js-remove').attr('data-image', res.image);
                        $('.js-remove').attr('data-qty', res.qty);

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
            }

        });

    });

})(window, document, window.jQuery);