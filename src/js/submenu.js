$(function () {

    let initialaction = `${baseurl}menu/addsubmenu?${requiredparams}`;
    let currentaction = '';
    let menuSelectedName = '';
    let idSelected = null;
    let i = $('.js-i').length;

    $('.js-new').on('click', function () {
        initform();
    });

    function initform() {
        currentaction = initialaction;
        $('#name').val('');

        const options = $('#menu_id').html();
        $('#menu_id').html('<option value="">Select Menu</option>');
        $('#menu_id').append(options);

        $('#url').val('');
        $('#icon').val('icon-doc');
        $('#is_active').val();
    }

    $('.js-submit').on('click', function (e) {
        e.preventDefault();

        const selectedIndex = $('#menu_id')[0].options.selectedIndex;
        menuSelectedName = $('#menu_id option')[selectedIndex].innerHTML;

        let data = $('form').serialize();
        data += `&id=${idSelected}`;
        $.ajax({
            url: currentaction,
            method: 'post',
            data,
            dataType: 'json',
            success: res => {
                $('.js-cancel').click();
                i = $('.js-i').length;
                updateUI(res);
            },
            error: err => console.log(err)
        });

    });

    $(document).on('click', function (e) {
        if (e.target.classList.contains('js-update')) {
            currentaction = `${baseurl}menu/updatesubmenu?${requiredparams}`;
            const id = e.target.dataset.id;
            idSelected = id;

            $.ajax({
                url: `${baseurl}menu/showsubmenu?id=${id}&${requiredparams}`,
                method: 'get',
                dataType: 'json',
                success: res => {
                    $('#name').val(res.submenu.name);
                    $('#menu_id').html(`<option value="${res.submenu.menu_id}">${res.submenu.menu}</option>`);

                    const menu = res.menu;
                    let menuOptions = '';
                    for (let i = 0; i < menu.length; i++) {
                        if (menu[i].id !== res.submenu.menu_id) {
                            menuOptions += `<option value="${menu[i].id}">${menu[i].menu}</option>`;
                        }
                    }
                    $('#menu_id').append(menuOptions);

                    $('#url').val(res.submenu.url);
                    $('#icon').val(res.submenu.icon);

                    let yes = `<option class="form-control" value="1">True</option>`;
                    let no = `<option class="form-control" value="0">False</option>`;
                    if (Number(res.submenu.is_active) === 0) {
                        [yes, no] = [no, yes];
                    }
                    $('#is_active').html(yes + no);
                }
            });
        }

        if (e.target.classList.contains('js-remove')) {
            if (!window.confirm('Are you sure want to remove?')) return;
            const id = e.target.dataset.id;

            $.ajax({
                url: `${baseurl}menu/removesubmenu?id=${id}&${requiredparams}`,
                method: 'get',
                data: id,
                dataType: 'json',
                success: res => {
                    document.querySelector(`tr.js-menuid-${res.id}`).remove();
                    i = $('.js-i').length;
                }
            });
        }
    });

    let id = 0;
    function updateUI(sm) {
        if (currentaction !== initialaction) {
            id = idSelected;
            i = $(`tr.js-menuid-${idSelected} th`).html();
            $(`tr.js-menuid-${idSelected}`).replaceWith(list(sm));
        } else {
            i += 1;
            id = sm.id;
            $('tbody').append(list(sm));
        }
    }

    function list(sm) {
        return `
            <tr class="js-menuid-${id}">
                <th class="js-i" scope="row">${i}</th>
                <th>${sm.name}</th>
                <th>${menuSelectedName}</th>
                <th>${sm.url}</th>
                <th>${sm.icon}</th>
                <th>${sm.is_active}</th>
                <th>
                    <a href="#" data-toggle="modal" data-target="#menuform" data-id="${id}" class="js-update badge bg-success">edit</a>
                    <a href="#" data-id="${id}" class="js-remove badge bg-danger">delete</a>
                </th>
            </tr>
        `;
    }

});