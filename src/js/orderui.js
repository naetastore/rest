$(function () {

    $('.js-update').on('click', function () {
        const roleId = $(this).data('role');
        const uiId = $(this).data('ui');

        $.ajax({
            url: `${baseurl}superuser/changeorderui?${requiredparams}`,
            method: 'post',
            data: {
                uiId, roleId
            },
            success: res => console.log(res),
            error: err => console.log(err)
        });
    });


});