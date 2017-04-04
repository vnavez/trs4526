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
                    el.remove();
                }
            },
            error: function(response) {
            }
        })
    });
    $('#delete-modal').modal();
});

$(document).on('click', '.enable-transfert', function(e) {
    var el = $(this).parent().parent();
    $.ajax({
        url: $(this).attr('href'),
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
                el.replaceWith(response.data);
            }
        },
        error: function(response) {
        }
    });
    return false;
});