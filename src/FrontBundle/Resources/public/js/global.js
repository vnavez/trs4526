$(document).ready(function () {
    socket_co();
});

function socket_co() {
    var webSocket = WS.connect("wss://download.navez.fr:80");
    var sessiong = '';

    webSocket.on("socket/connect", function (session) {
        sessiong = session;
        session.subscribe("torrent/update", function (uri, payload) {
            fireLine(payload);
        });
        session.subscribe("torrent/update/" + id_client, function (uri, payload) {
            fireLine(payload);
        })
    });
}

function fireLine(payload) {
    try {
        var response = JSON.parse(payload.msg);
        var id = response.id;
        if (response.action === "delete") {
            deleteLine(id);
        }
        else if ($('#torrent-' + id).length) {
            refreshLine(id, response.html);
        } else {
            addLine(id, response.html);
        }

    } catch (e) {
        console.log(payload);
    }
}

function deleteLine(i) {
    $('#torrent-' + i).animate({opacity: 0}, 500, function () {
        $(this).remove();
    });
}

function refreshLine(i, data) {
    $('#torrent-' + i).animate({opacity: 0.2}, 500, function () {
        $x = $(data);
        $x.css('opacity', 0.5);
        $(this).replaceWith($x);
        $x.animate({opacity: 1}, 500);
    });
}

function addLine(i, data) {
    $x = $(data);
    $x.css('opacity', 0.5);
    $('.table-responsive tbody').append($x);
    $x.animate({opacity: 1}, 500);
}

$(document).on('click', '#upload-torrent', function (e) {
    $.ajax({
        url: $(this).attr('href'),
        dataType: 'json',
        type: 'GET',
        success: function (response) {
            $('#upload-modal .modal-body').html(response.data);
        }
    });
    $(document).off('click', '.submit-upload-torrent');
    $(document).on('click', '.submit-upload-torrent', function () {
        $('#upload-modal .modal-body form').submit();
    });
    $('#upload-modal').modal();
    return false;
});

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
            error: function (response) {
            }
        })
    });
    $('#delete-modal').modal();
});

$(document).on('click', '.enable-transfert', function (e) {
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
        error: function (response) {
        }
    });
    return false;
});

$(document).on('click', '.compress-files', function (e) {
    if ($(this).attr('href')) {
        $.ajax({
            url: $(this).attr('href')
        });
    }
    return false;
});


$(document).on('click', '#test-connection', function (e) {
    $('#msg-danger,#msg-success').fadeOut(200);
    $.ajax({
        url: $(this).attr('href'),
        success: function (response) {
            if (response.error)
                $('#msg-danger').html('<strong>Erreur!</strong> ' + response.error).fadeIn(200);
            else
                $('#msg-success').html('<strong>Félicitations!</strong> Votre serveur est correctement configuré').fadeIn(200);
            $('html, body').animate({
                scrollTop: $('#test-connection').offset().top
            }, 200);
        }
    });
    return false;
});