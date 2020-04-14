<script src="<?= base_url('src/') ?>vendor/modernizr/modernizr.custom.js"></script>
<script src="<?= base_url('src/') ?>vendor/matchMedia/matchMedia.js"></script>
<script src="<?= base_url('src/') ?>vendor/jquery/dist/jquery.js"></script>
<script src="<?= base_url('src/') ?>vendor/bootstrap/dist/js/bootstrap.js"></script>
<script src="<?= base_url('src/') ?>vendor/jQuery-Storage-API/jquery.storageapi.js"></script>
<script src="<?= base_url('src/') ?>vendor/jquery.easing/js/jquery.easing.js"></script>
<script src="<?= base_url('src/') ?>vendor/animo.js/animo.js"></script>
<script src="<?= base_url('src/') ?>vendor/slimScroll/jquery.slimscroll.min.js"></script>
<script src="<?= base_url('src/') ?>vendor/screenfull/dist/screenfull.js"></script>
<script src="<?= base_url('src/') ?>vendor/jquery-localize-i18n/dist/jquery.localize.js"></script>
<script src="<?= base_url('src/') ?>js/demo/demo-rtl.js"></script>
<!-- =============== APP SCRIPTS ===============-->
<script src="<?= base_url('src/') ?>js/app.js"></script>
<script> window.sessionStorage.setItem('naetastore_base', '<?= base_url();?>'); </script>
<script>
    const baseurl = window.sessionStorage.getItem('naetastore_base');
    const session = window.localStorage.getItem('naetastore_sess');
    const username = window.localStorage.getItem('naetastore_name');
    const requiredparams = `session=${session}&username=${username}`;

    $(function() {

        const navlink = $('.js-navlink');
        for (let i = 0; i < navlink.length; i++) {
            const nav = navlink[i];
            if (nav.attributes.href.value !== "#") {
                nav.setAttribute('href', `${nav.attributes.href.value}${requiredparams}`);
            }
        }

        $(document).on('click', function () {
            const sessionid = $('#session_id');
            const name = $('#username');
            if (sessionid.length) {
                sessionid[0].setAttribute('value', session);
                name[0].setAttribute('value', username);
            }
        });

        $('.js-new').on('click', function () {
            const maxLength = $('#description').data('max');
            $('#description').on('keyup', function (e) {
                let charlefts = maxLength - e.target.value.length;
                $('#charleft').html(`Char Lefts (${charlefts})`);
            });
        });

        $('.js-file-input').on('change', function (e) {
            const reader = new FileReader();
            const image = e.target.files[0];
            reader.onloadend = function () {
                $('.js-img-preview').attr('src', reader.result);
            }
            reader.readAsDataURL(image);
            $('.js-file-input-button').html('Change photo...');
        });

        $('.js-logout').on('click', function () {
            if (!window.confirm('Are you sure want to logout?')) return;
            $.ajax({
                url: `${baseurl}auth/logout?${requiredparams}`,
                method: 'get',
                success: function() {
                    window.localStorage.removeItem('naetastore_sess');
                    window.localStorage.removeItem('naetastore_name');
                    window.location.href = `${baseurl}auth`;
                }
            });
        });

    });
</script>
