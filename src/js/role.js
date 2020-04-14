$(function () {

    let initialaction = `${baseurl}superuser/addrole?${requiredparams}`;
    let currentaction = '';
    let i = $('.js-i').length;

    $('.js-new').on('click', function () {
        currentaction = initialaction;
        initform();
    });

    function initform() {
        currentaction = initialaction;
        $('#role').val('');
    }

    let data = {
        id: '',
        role: ''
    }

    $('.js-submit').on('click', function (e) {
        e.preventDefault();
        data.role = $('#role').val();

        $.ajax({
            url: currentaction,
            method: 'post',
            data: data,
            dataType: 'json',
            success: res => {
                $('.js-cancel').click();
                i = $('.js-i').length;
                updateUI(res);
            },
            error: err => console.log(err)
        })
    });

    $(document).on('click', function (e) {
        if (e.target.classList.contains('js-update')) {
            currentaction = `${baseurl}superuser/updaterole?${requiredparams}`;
            data.id = e.target.dataset.id;

            $.ajax({
                url: `${baseurl}superuser/showrole?id=${data.id}&${requiredparams}`,
                method: 'post',
                dataType: 'json',
                success: res => {
                    $('#role').val(res.role);
                }
            });
        }

        if (e.target.classList.contains('js-remove')) {
            data.id = e.target.dataset.id;
            if (!window.confirm('Are you sure want to remove?')) return;

            $.ajax({
                url: `${baseurl}superuser/removerole?id=${data.id}&${requiredparams}`,
                method: 'post',
                dataType: 'json',
                success: res => {
                    if (res.status === true) {
                        $(`tr.js-roleid-${res.id}`).remove();
                        i = $('.js-i').length;
                    } else {
                        console.error(res.message);
                    }
                }
            });
        }
    });

    let id = 0;
    function updateUI(r) {
        if (currentaction !== initialaction) {
            id = data.id;
            i = $(`tr.js-roleid-${id} th`).html();
            $(`tr.js-roleid-${id}`).replaceWith(list(r));
        } else {
            i += 1;
            id = r.id;
            $('tbody').append(list(r));
        }
    }

    function list(r) {
        return `
            <tr class="js-roleid-${r.id}">
                <th class="js-i" scope="row">${i}</th>
                <th>${r.role}</th>
                <th>
                    <div class="btn-group">
                        <button type="button" data-toggle="dropdown" class="btn btn-sm dropdown-toggle btn-inverse">Privacy
                            <span class="caret"></span>
                        </button>
                        <ul role="menu" class="dropdown-menu">
                            <li><a href="${baseurl}superuser/orderui?role_id=${r.id}&${requiredparams}" class="js-navlink js-access">Order UI config</a>
                            </li>
                        </ul>
                    </div>
                    <div class="btn-group">
                        <button type="button" data-toggle="dropdown" class="btn btn-sm dropdown-toggle btn-warning">Access
                            <span class="caret"></span>
                        </button>
                        <ul role="menu" class="dropdown-menu">
                            <li><a href="${baseurl}superuser/roleaccess?role_id=${r.id}&${requiredparams}" class="js-navlink js-access">Menu access</a>
                            </li>
                            <li class="divider"></li>
                            <li><a href="${baseurl}superuser/orderaccess?role_id=${r.id}&${requiredparams}" class="js-navlink js-access">Order API control</a>
                            </li>
                        </ul>
                    </div>
                    <button data-id="${r.id}" class="js-remove btn btn-danger btn-sm">Remove</button>
                    <button data-id="${r.id}" class="js-update btn btn-success btn-sm" data-toggle="modal"  data-target="#roleform">Update</button>
                </th>
            </tr>
        `;
    }

});