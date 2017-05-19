var lastrefresh = new Date().getTime();

$(document).ready(function() {
    socket_co();
});

function socket_co() {
    var webSocket = WS.connect("ws://82.64.8.222:8080");
    var sessiong = '';

    webSocket.on("socket/connect", function(session){
        sessiong = session;
        //session is an Autobahn JS WAMP session.
        session.subscribe("torrent/update", function(uri, payload){
            try {
                var responses = JSON.parse(payload.msg);
                for (var i in responses) {
                    if ($('#torrent-'+i).length) {
                        refreshLine(i, responses[i]);
                    } else {
                        addLine(i, responses[i]);
                    }
                }

            } catch (e) {
                console.log(payload);
            }
        });
    });
}

function refreshLine(i, data) {
    $('#torrent-'+i).animate({opacity: 0.2}, 500, function() {
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