$(document).ready(function () {
// PRORROGAR EVENTO
    $('.prorrogar').click(function () {
        var id = $(this).data('id');
        BootstrapDialog.show({
            title: "Prorrogar Evento",
            message: $('<div></div>').load('/intranet/rh/eventos/index2.php?id=' + id + "&method=modal_prorrogar"),
            closable: false,
            buttons: [{
                    label: "Prorrogar",
                    cssClass: "btn-primary",
                    action: function (dialog) {
                        salvarProrrogacao();
                        dialog.close();
                    }
                }, {
                    label: "Cancelar",
                    action: function (dialog) {
                        dialog.close();
                    }
                }]
        });
    });

    $("body").on("focusout", "#dias2", function () {
        var id = $("#id_evento").val();
        var dias = $("#dias2").val();
        console.log("id: " + id + "\ndias: " + dias);
        $.post('/intranet/methods.php', {id: id, calcData: true, qtdDias: dias}, function (data) {
            if (data != 0) {
                $("#data_prorrogada").val(data.data);
            } else {
                alert('Falha ao carregar evento!');
                exit();
            }
        }, 'json');
    });
});

function salvarProrrogacao() {
    var id_evento = $("#id_evento").val();
    var id_user = $("#id_user").val();
    var data_retorno = $("#data_retorno2").val();
    var mensagem = $("#obs").val();
    var data_prorrogada = $("#data_prorrogada").val();

    console.log("id_evento: " + id_evento + "\nid_user: " + id_user + "\ndata_retorno: " + data_retorno + "\nmensagem: " + mensagem + "\ndata_prorrogada: " + data_prorrogada);

    $.ajax({
        url: "../../methods.php",
        type: "POST",
        dataType: "json",
        data: {
            id_evento: id_evento,
            id_user: id_user,
            data_retorno: data_retorno,
            mensagem: mensagem,
            data_prorrogada: data_prorrogada,
            method: "prorroga_evento"
        },
        success: function (data) {
            if (data.status) {
                var botao = [{
                    label: 'Ok',
                    action: function (dialog) {
                        window.location.reload();
                    }
                }];
                bootDialog('Prorrogação',"Prorrogação salva com sucesso!",botao);
//                BootstrapDialog.alert({title: 'Prorrogação', message: "Prorrogação salva com sucesso!", type: 'type-success'});
//                //history.go(0);
//                window.location.reload();
            } else {
                var html = "";
                $.each(data.erro, function (key, value) {
                    html += "<p>" + value + "</p><br />";
                });
                BootstrapDialog.alert({title: 'Prorrogação', message: html, type: 'type-warning'});
            }
        }
    });
}