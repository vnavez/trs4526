var lastrefresh = new Date().getTime();

$(document).ready(function() {
    //reloadTorrents();
});

function reloadTorrents() {
    $.ajax({
        url: $('#reload-url').attr('href')+"?time="+lastrefresh,
        dataType: 'json',
        type: 'GET',
        success: function() {
            lastrefresh = new Date().getTime();
        }
    });
    setTimeout(reloadTorrents, 30000);
}

$(document).on('click', '.delete-torrent', function (e) {
    var el = $(this).parent().parent();
    var url = $(this).attr('href');
    e.preventDefault();
    $(document).off('click', '.submit-delete-torrent');
    $(document).on('click', '.submit-delete-torrent', function () {
        $('#delete-modal').modal('hide');
        $.ajax({
            url: url,
            dataType: 'json',
            type: 'GET',
            beforeSend: function () {
                el.css('opacity', '0.5');
            },
            complete: function () {
                el.css('opacity', '1');
            },
            success: function (response) {
                if (response.success) {
                    showSuccess(response.success);
                    el.remove();
                }
                else
                    showError('Erreur lors de la suppression du torrent')
            },
            error: function(response) {
                showError('Erreur requête, statut '+response.status);
            }
        })
    });
    $('#delete-modal').modal();
});

function showError(response) {
    showAlert('Une erreur est survenue : ', response, 'alert-danger');
}

function showSuccess(response) {
    showAlert('Confirmation : ', response, 'alert-success');
}

function showAlert(header, content, type) {
    $('#heading-message').empty();
    $('<div/>', {
        id: 'heading-alert',
        class: 'alert '+type+' alert-dismissible fade in',
        role: 'alert',
        text: content
    }).appendTo('#heading-message');
    $('<strong/>', {
        text: header
    }).prependTo('#heading-alert');
}