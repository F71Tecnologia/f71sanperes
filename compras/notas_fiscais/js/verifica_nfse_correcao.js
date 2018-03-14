$(document).ready(function () {
    var now = new Date();

    var validade = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1);

    $.post('/intranet/compras/notas_fiscais/nfse_atualiza.php', {method: 'verifica_nfe_correcao'}, function (data) {
        if (data.status) {
            var table = $('<table>', {class: 'table'}).append(
                    $('<thead>').append(
                    $('<tr>').append(
                    $('<th>').append('Numero'),
                    $('<th>').append('Prestador'),
                    $('<th>').append('Competência'),
                    $('<th>').append('Motivo de Correção'),
                    $('<th>').append('&emsp;')
                    )
                    )
                    );

            var tbody = $('<tbody>');

            var last_id; // para guardar o último id

            $.each(data.lista, function (i, v) {
                tbody.append(
                        $('<tr>').append(
                        $('<td>', {style: 'vertical-align: middle'}).append(v.Numero),
                        $('<td>', {style: 'vertical-align: middle'}).append(v.nome_prestador),
                        $('<td>', {style: 'vertical-align: middle'}).append(v.Competencia),
                        $('<td>', {style: 'vertical-align: middle'}).append(v.motivo),
                        $('<td>', {style: 'vertical-align: middle'}).append($('<a>', {class: 'btn btn-success btn-xs', href: '/intranet/compras/notas_fiscais/form_nfse.php?id_edit=' + v.id_nfse, target: '_blank'}).append($('<i>', {class: 'fa fa-pencil'})))
                        )
                        );
                last_id = v.id_correcao;
            });

            table.append(tbody);

            if (getCookie('last_id') != last_id){
                bootAlert(table, 'Notas com Erro de Cadastro', null, 'warning');
                setCookie('last_id',last_id,validade,null,window.location.host);
            }
        }
    }, 'json');
});

function setCookie(name, value, expires, path, domain, secure) {
    var curCookie = name + "=" + escape(value) +
            ((expires) ? "; expires=" + expires.toGMTString() : "") +
            ((path) ? "; path=" + path : "") +
            ((domain) ? "; domain=" + domain : "") +
            ((secure) ? "; secure" : "");
    document.cookie = curCookie;
}

function getCookie(name) {
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1) {
        begin = dc.indexOf(prefix);
        if (begin != 0)
            return null;
    } else
        begin += 2;
    var end = document.cookie.indexOf(";", begin);
    if (end == -1)
        end = dc.length;
    return unescape(dc.substring(begin + prefix.length, end));
}

function deleteCookie(name, path, domain) {
    if (getCookie(name)) {
        document.cookie = name + "=" +
                ((path) ? "; path=" + path : "") +
                ((domain) ? "; domain=" + domain : "") +
                "; expires=Thu, 01-Jan-70 00:00:01 GMT";
        history.go(0);
    }
}