(function (window, document, $, undefined) {

    $(function () {

        $('.chosen-select').chosen();
        $('[data-masked]').inputmask();
        editable();

        let initialaction = `${baseurl}superuser/add_webclient?${requiredparams}`;
        let currentaction = '';

        $('.js-new').on('click', function () {
            initform();
        });

        function initform() {
            currentaction = initialaction;
            $('#username').val('');
            $('#apikey').val('');
        }

        $('.js-submit').on('click', function (e) {
            e.preventDefault();

            const data = {
                "user_id": $('#user_id').val(),
                "web_app": $('#web_app').val()
            }

            $.ajax({
                url: currentaction,
                method: 'post',
                data: data,
                dataType: 'json',
                success: res => {
                    $('.js-cancel').click();
                    updateUI(res);
                },
                error: err => console.log(err.responseText)
            })
        });

        let data = {
            id: '',
            inputId: '',
            inputValue: '',
        }

        $(document).on('click', function (e) {
            if (e.target.classList.contains('js-update')) {
                data.id = e.target.dataset.id;
                data.inputId = e.target.dataset.inputid;
            }

            if (e.target.classList.contains('editable-submit')) {
                data.inputValue = $('.editable-input input').val();
                data = {
                    id: data.id,
                    [data.inputId]: data.inputValue
                }
                $.ajax({
                    url: `${baseurl}superuser/update_webclient?${requiredparams}`,
                    method: 'post',
                    dataType: 'json',
                    data
                });
            }

            if (e.target.classList.contains('js-remove')) {
                if (!window.confirm('Are you sure want to remove?')) return;
                const id = $(e.target).data('id');
                $.ajax({
                    url: `${baseurl}superuser/remove_webclient?${requiredparams}`,
                    method: 'get',
                    dataType: 'json',
                    data: { id },
                    success: res => {
                        $(`.js-keyid-${res.id}`).remove();
                    }
                });
            }
        });

        function updateUI(k) {
            $('tbody').append(`
                <tr class="js-keyid-${k.id}">
                    <td>${k.user_id}</td>
                    <td>
                        <a title="Go to profile ${k.user.username}" class="js-navlink" href="${k.user.url}">${k.user.username}</a>
                    </td>
                    <td>
                        <a class="js-apikey" data-id="${k.id}" href="#" data-type="text" data-pk="1" data-title="API Key">${k.key}</a>
                    </td>
                    <td>
                        <a class="js-webapp" href="${k.web_app}">${k.web_app}</a>
                    </td>
                    <td>${k.date_created}</td>
                    <td>
                        <a href="#" data-id="${k.id}" class="js-remove btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
            `);
            editable();
        }

        $.fn.editableform.buttons =
            '<button type="submit" class="btn btn-primary btn-sm editable-submit">' +
            '<i class="js-submit fa fa-fw fa-check"></i>' +
            '</button>' +
            '<button type="button" class="btn btn-default btn-sm editable-cancel">' +
            '<i class="fa fa-fw fa-times"></i>' +
            '</button>';

        function editable() {
            $('.js-apikey').editable({
                type: 'text',
                pk: 1,
                name: 'key',
                title: 'Enter API Key'
            });
            $('.js-webapp').editable({
                type: 'text',
                pk: 1,
                name: 'web_app',
                title: 'Enter Web Application URL'
            });
        }
    });

})(window, document, window.jQuery);