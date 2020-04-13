$(function () {

    $('.js-productview').on('click', function (e) {

        const id = $(e.target).data('id');

        $.ajax({
            url: `${baseurl}administrator/showproduct?id=${id}&${requiredparams}`,
            type: 'get',
            dataType: 'json',
            success: res => {
                $('#name').html(res.name);
                $('#price').html(res.price);
                $('#qty').html(res.qty);
                $('#description').html(res.description);

                $('.js-img-preview').attr('src', `${baseurl}src/img/product/${res.image}`);

                let suggested = 'False';
                if (Number(res.suggested) == 1) suggested = 'True';
                $('#suggested').html(suggested);

                let is_ready = 'False';
                if (Number(res.is_ready) == 1) is_ready = 'True';
                $('#is_ready').html(is_ready);
            }
        });

    });





});
