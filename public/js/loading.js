var myModalWeb = new bootstrap.Modal(document.getElementById('loadingModal'), {
    keyboard: false
});       

function showLoading() {
    $('.proses-berjalan').html('');
    myModalWeb.show();
}

function hideLoading() {
    $(document).on('shown.bs.modal', '#loadingModal', function () {
        var modalElement = document.getElementById('loadingModal');
        var mymodal = bootstrap.Modal.getInstance(modalElement);
        mymodal.hide();
    });
    $('.proses-berjalan').html('');
}

$(document).ajaxStart(function() {
    showLoading();
});

$(document).ajaxStop(function() {
    hideLoading();
});

$(document).ajaxError(function() {
    hideLoading();
});