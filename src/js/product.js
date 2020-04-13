$(function () {

    $('.js-file-input-button').on('click', function () {
        $('.js-file-input').click();
    });

    $('.js-new').on('click', function () {
        initform();
    });


    function initform() {
        const removeButon = $('.js-remove');
        removeButon[0].style.setProperty('display', 'none');
        let searchstory = window.location.search;
        searchstory = searchstory.replace('?', '&');
        const action = `${baseurl}supplier/addproduct?redirect=supplier/product&${requiredparams}${searchstory}`;

        $('.js-form-product').attr('action', action);
        $('#name').val('');
        $('#price').val('');
        $('#qty').val('');
        // $('#category').val('');
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

            if (!window.confirm('Are you sure want ro remove?')) return;
            $.ajax({
                url: `${baseurl}supplier/removeproduct?id=${id}&global_id=${global_id}&image=${image}&${requiredparams}`,
                method: 'get',
                success: () => {
                    $('.js-cancel').click();
                    $(`.js-productid-${id}`).remove();
                },
                error: err => console.log(err)
            });
        }

        if (e.target.classList.contains('js-view')) {
            let searchstory = window.location.search;
            searchstory = searchstory.replace('?', '&');

            const id = $(e.target).data('id');
            const action = `${baseurl}supplier/updateproduct?id=${id}&redirect=supplier/product&${requiredparams}${searchstory}`;
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