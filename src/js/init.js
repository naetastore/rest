$(function () {
    const baseurl = window.sessionStorage.getItem('naetastore_base');
    const session = window.localStorage.getItem('naetastore_sess');
    const username = window.localStorage.getItem('naetastore_name');
    const requiredparams = `session=${session}&username=${username}`;
});