$(document).ready(function () {

    var url = window.location.href;
    $("#form1").validationEngine(); // add validation engine
    $("#form1").ajaxForm({
        resetForm: true,
        dataType: 'json',
        beforeSubmit: function () {
            $("#resp-autalizar").html("<br><p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
        },
        success: function (data) {
            if (data.status) {
                bootAlert(data.msg, 'Salvando...', null, 'success');
            } else {
                bootAlert(data.msg, 'Salvando...', null, 'danger');
            }
            $.post(url, {method: 'refresh_table', id_clt: $("#id_clt").val()}, function (data) {
                $("#resp-autalizar").html(data);
            });
        }
    });


    $("#data_ini").datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        changeYear: true,
        onClose: function (selectedDate) {
            $("#data_fim").datepicker("option", "minDate", selectedDate);
        }
    });
    $("#data_fim").datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        changeYear: true,
        onClose: function (selectedDate) {
            $("#data_ini").datepicker("option", "maxDate", selectedDate);
        }
    });

    $("#data_ini,#tipo").change(function () {
        var data_ini = $('#data_ini').val();
        var tipo = $('#tipo').val();
        if (data_ini != '' && tipo != '-1') {
            $.post('index.php', {method: 'calcData', data_ini: data_ini, tipo: tipo}, function (data) {
                if (data.data_fim != data_ini) {
                $("#data_fim").val(data.data_fim);
                } else {
                    $("#data_fim").val('');
                }
            }, 'json');
        } else {
            $("#data_fim").val('');
        }
    });

    $("#btn-informacao").click(function () {
        var informacao = $("#informacao").html();
        bootAlert(informacao, 'Informação', null, 'info');
});

});

function editar($this) {
    var id = $($this).data('id');
    $("#id_estabilidade").val(id);
    $("#form_editar").submit();
}
function excluir($this) {
    var id = $($this).data('id');
    bootConfirm('Tem certez que deseja excluir?', 'Excluindo', function (status) {
        if (status) {
            $.post(window.location.href, {method: 'excluir', id: id}, function (data) {
                if (data.status) {
                    console.log('aloha');
                    bootAlert(data.msg, 'Excluindo...', null, 'success');
                    $('#tr-' + id).remove();
                } else {
                    bootAlert('Erro ao excluir.', 'Excluindo...', null, 'danger');
                }
            }, 'json');
        }
    }, 'danger');
}
