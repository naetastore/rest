$(function () {

    let initialaction = `${baseurl}supplier/addcategory?${requiredparams}`;
    let currentaction = '';

    $('.js-new').on('click', function (e) {
        initform(e);
    });

    function initform(e) {
        const removeButon = $('.js-remove');
        removeButon[0].style.setProperty('display', 'none');
        currentaction = initialaction;

        const gid = $(e.target).data('globalid');
        $('#name').val('');
        $('#description').val('');
        $('#global_id').val(gid);
        $('form').attr('action', initialaction);
    }

    $('.js-submit').on('click', function (e) {

        e.preventDefault();

        const form = $('form').serialize();
        $.ajax({
            url: currentaction,
            method: 'post',
            dataType: 'json',
            data: form,
            success: res => {
                $('.js-cancel').click();
                updateUI(res);
            },
            error: err => console.log(err.responseText)
        });

    });

    function updateUI(c) {
        if (currentaction !== initialaction) {
            $(`.js-categoryid-${c.id}`).html(`
                <td>
                    <h4><a href="${c.url + requiredparams}" class="js-navlink"><strong>${c.name}</strong></a></h4>
                    <div class="text-muted">${c.description}</div>
                </td>
                <td class="text-muted text-center hidden-xs hidden-sm">
                    <strong>${c.product}</strong>
                </td>
                <td class="text-muted text-center hidden-xs hidden-sm">
                    <strong>${c.selled}</strong>
                </td>
                <td class="text-muted text-center hidden-xs hidden-sm">
                    <strong>${c.ratio}</strong>
                </td>
                <td class="hidden-xs hidden-sm">
                    <a href="${c.updated.user.url}" class="js-navlink">${c.updated.user.username}</a>
                    <br>
                    <small>${c.updated.date}</small>
                </td>
                <td class="text-center">
                    <button type="button" class="js-view btn btn-sm btn-default" data-toggle="modal" data-target="#subcategoryform" data-globalid="${c.global_id}" data-id="${c.id}">
                        <em class="js-view fa fa-search" data-globalid="${c.global_id}" data-id="${c.id}"></em>
                    </button>
                </td>
            `);
            return;
        }

        $(`tbody#${c.global_id}`).append(`
            <tr class="js-categoryid-${c.id}">
                <td>
                    <h4><a href="${c.url + requiredparams}" class="js-navlink"><strong>${c.name}</strong></a></h4>
                    <div class="text-muted">${c.description}</div>
                </td>
                <td class="text-muted text-center hidden-xs hidden-sm">
                    <strong>0</strong>
                </td>
                <td class="text-muted text-center hidden-xs hidden-sm">
                    <strong>${c.selled}</strong>
                </td>
                <td class="text-muted text-center hidden-xs hidden-sm">
                    <strong>${c.ratio}</strong>
                </td>
                <td class="hidden-xs hidden-sm">
                    <a href="${c.updated.user.url}" class="js-navlink">${c.updated.user.username}</a>
                    <br>
                    <small>${c.updated.date}</small>
                </td>
                <td class="text-center">
                    <button type="button" class="js-view btn btn-sm btn-default" data-toggle="modal" data-target="#subcategoryform" data-globalid="${c.global_id}" data-id="${c.id}">
                        <em class="js-view fa fa-search" data-globalid="${c.global_id}" data-id="${c.id}"></em>
                    </button>
                </td>
            </tr>
        `);
    }

    $(document).on('click', function (e) {

        if (e.target.classList.contains('js-view')) {
            const id = e.target.dataset.id;
            const gid = e.target.dataset.globalid;
            const action = `${baseurl}supplier/updatecategory?id=${id}&${requiredparams}`;

            currentaction = action;

            const removeButon = $('.js-remove');
            removeButon[0].style.removeProperty('display');

            $.ajax({
                url: `${baseurl}supplier/showcategory?id=${id}&${requiredparams}`,
                method: 'get',
                dataType: 'json',
                success: res => {
                    $('#name').val(res.name);
                    $('#description').val(res.description);
                    $('#global_id').val(gid);
                    $('.js-remove').attr('data-id', res.id);
                    $('form').attr('action', action);
                }
            })
        }

        if (e.target.classList.contains('js-remove')) {
            const id = e.target.dataset.id;

            if (!window.confirm('Are you sure want to remove? removing the category will too remove all product.')) return;
            $.ajax({
                url: `${baseurl}supplier/removecategory?id=${id}&${requiredparams}`,
                method: 'get',
                success: () => {
                    $('.js-cancel').click();
                    $(`.js-categoryid-${id}`).remove();
                },
                error: err => console.log(err)
            });
        }

    });













});
