$(function () {

    let initialaction = `${baseurl}menu/addmenu?${requiredparams}`;
    let currentaction = '';
    let i = $('.js-i').length;

    $('.js-new').on('click', function () {
        initform();
    });

    function initform() {
        currentaction = initialaction;
        $('#menu').val('');
    }

    let data = {
        "id": "",
        "menu": "",
        "session_id": session,
        username
    }

    $('.js-submit').on('click', function (e) {
        e.preventDefault();

        data.menu = $('#menu').val();
        $.ajax({
            url: currentaction,
            method: 'post',
            data,
            dataType: 'json',
            success: res => {
                $('.js-cancel').click();
                i = $('.js-i').length;
                updateUI(res);
            }
        });
    });

    $(document).on('click', function (e) {

        if (e.target.classList.contains('js-update')) {
            currentaction = `${baseurl}menu/updatemenu?${requiredparams}`;
            data.id = e.target.dataset.id;

            $.ajax({
                url: `${baseurl}menu/showmenu?id=${data.id}&${requiredparams}`,
                method: 'get',
                dataType: 'json',
                success: res => {
                    $('#menu').val(res.menu);
                }
            });
        }

        if (e.target.classList.contains('js-remove')) {
            if (!window.confirm('Are you sure want to remove?')) return;
            const id = e.target.dataset.id;

            $.ajax({
                url: `${baseurl}menu/removemenu?id=${id}&${requiredparams}`,
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
    function updateUI(m) {
        if (currentaction !== initialaction) {
            id = data.id;
            i = $(`tr.js-menuid-${id} th`).html();
            $(`tr.js-menuid-${id}`).replaceWith(list(m));
            console.log(id);
        } else {
            i += 1;
            id = m.id;
            $('tbody').append(list(m));
        }
    }

    function list(m) {
        return `
        <tr class="js-menuid-${id}">
            <th scope="row" class="js-i">${i}</th>
            <th>${m.menu}</th>
            <th>
                <a href="#" data-toggle="modal" data-target="#menuform" data-id="${id}" class="js-update badge bg-success">edit</a>
                <a href="#" data-id="${id}" class="js-remove badge bg-danger">delete</a>
            </th>
        </tr>`;
    }

});