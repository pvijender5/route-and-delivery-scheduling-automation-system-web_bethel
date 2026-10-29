$(document).on('click', '.activity-log-btn', function () {
    var shipmentId = $(this).data('shipment-id');
    openModalWithData('#activityLogModal', '/fetch-activity-log/' + shipmentId);
});

$(document).on('click', '.hide-activity-log .close', function () {
    closeModal('#activityLogModal');
});

$(document).on('click', '.close', function () {
    closeModal('#activityLogModal');
});

function openModalWithData(modalId, dataUrl) {
    $.ajax({
        url: dataUrl,
        method: 'GET',
        success: function (response) {
            $(modalId + ' .modal-body').html(response);
            $(modalId).modal('show');
        },
        error: function () {
            // Handle error
        }
    });
}

function closeModal(modalId) {
    $(modalId).modal('hide');
}