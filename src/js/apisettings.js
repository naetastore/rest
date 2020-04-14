$(function () {

    // const inputs = $('input');
    // for (let i = 0; i < inputs.length; i++) {
    //     const setting = inputs[i].dataset.setting;
    //     getCurrentSetting(setting, df => updateUI(df, `#${setting}`));
    // }

    $('.js-action-allowed').on('click', function () {
        const status_id = $(this).data('status');
        const action_id = $(this).data('action');

        $.ajax({
            url: `${baseurl}superuser/change_product_action_allowed?${requiredparams}`,
            method: 'post',
            data: { status_id, action_id }
        });
    });

    getCurrentSetting('order_maxhour', df => updateUI(df, '#order_maxhour'));

    const selects = $('select');
    for (let i = 0; i < selects.length; i++) {
        const setting = selects[i].dataset.setting;
        getCurrentSetting(setting, df => updateUI(df, `#${setting}`));
    }

    function getCurrentSetting(setting, callback) {
        $.ajax({
            url: `${baseurl}superuser/get_current_setting?setting=${setting}&${requiredparams}`,
            method: 'get',
            dataType: 'json',
            success: res => callback(res)
        });
    }

    function updateUI(d, selector) {
        const element = $(selector);
        if (element[0] && element[0].tagName == 'INPUT') {
            if (d.current) {
                element[0].value = d.current.value;
                return;
            }
            element[0].placeholder = 'Input here..';
        } else {
            if (d.current) {
                const current = list(d.current);
                $(selector).html(current);
                let options = '';
                d.role.forEach(r => {
                    if (r.id !== d.current.value) {
                        options += list(r);
                    }
                });
                $(selector).append(options);
                return;
            }

            $(selector).html(`<option value="">Select options</option>`);
            let options = '';
            d.role.forEach(r => options += list(r));
            $(selector).append(options);
        }

    }

    function list(r) {
        return `<option value="${r.id}">${r.role}</option>`;
    }

    $('select').on('change', function () {
        const setting = $(this).data('setting');
        const value = $(this).val();

        saveChange({ value, setting }, function (res) {
            if (res.status == true) {
                $(this).prepend(`<div class="text-success">${res.message}</div>`);
            } else {
                $(this).prepend(`<div class="text-danger">${res.message}</div>`);
            }
        });
    });

    $('input#order_maxhour').on('change', function () {
        const setting = $(this).data('setting');
        const value = $(this).val();

        saveChange({ value, setting }, function (res) {
            if (res.status == true) {
                $(this).prepend(`<div class="text-success">${res.message}</div>`);
            } else {
                $(this).prepend(`<div class="text-danger">${res.message}</div>`);
            }
        });
    });

    function saveChange(data, callback) {
        $.ajax({
            url: `${baseurl}superuser/change_setting?${requiredparams}`,
            data,
            method: 'post',
            dataType: 'json',
            success: res => callback(res)
        });
    }



});