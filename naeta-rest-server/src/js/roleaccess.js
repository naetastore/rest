$(function () {

    $('.js-update').on('click', function () {
        const menuId = $(this).data('menu');
        const roleId = $(this).data('role');

        $.ajax({
            url: `${baseurl}superuser/changeaccess?${requiredparams}`,
            method: 'post',
            data: {
                menuId, roleId
            },
            success: res => console.log(res),
            error: err => console.log(err)
        });
    });

});