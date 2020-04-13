$(function () {

    $('.js-update').on('click', function () {
        const roleId = $(this).data('role');
        const statusId = $(this).data('status');
        const actionId = $(this).data('action');

        $.ajax({
            url: `${baseurl}superuser/change_orderaccess?${requiredparams}`,
            method: 'post',
            data: {
                roleId, statusId, actionId
            },
            success: res => console.log(res),
            error: err => console.log(err)
        });
    });


});