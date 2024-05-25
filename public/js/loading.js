var modalElement = document.getElementById('loadingModal');
var modal = new bootstrap.Modal(modalElement, {
    keyboard: false
});

function showLoading() {
    $('.proses-berjalan').html('');
    modal.show();
}

function hideLoading() {
    modalElement.addEventListener('shown.bs.modal', function () {
        modal.hide();
    });    
    $('.proses-berjalan').html('');
}

$(document).ajaxStart(function() {
    //showLoading();
});

$(document).ajaxStop(function() {
    //hideLoading();
});

$(document).ajaxError(function() {
    //hideLoading();
});