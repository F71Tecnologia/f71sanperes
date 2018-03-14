$(document).ready(function () {
    var data_ini_antiga = $("#data_ini").val()
    var min_data = converteData(data_ini_antiga, '/'); // data será usada em validações
    
    var url = window.location.href; // url sera usada em ajaxs
    
    $("#form1").validationEngine(); // add validation engine
    $("#form1").ajaxForm({
        resetForm: true,
        dataType: 'json',
        beforeSubmit: function () {
            // verifica se a data não é menor que a data já salva no sistema
            if (converteData($("#data_ini").val(), '/') < min_data || converteData($("#data_fim").val(), '/') < min_data) {
                bootAlert("Selecione uma data que seja igual ou maior que "+data_ini_antiga+".", "Data Inválida", null, 'warning');
                return false;
            }
            $("#resp-autalizar").html("<br><p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
        },
        success: function (data) {
            if (data.status) {
                bootAlert(data.msg, 'Salvando...', function(){
                    window.history.back();
                }, 'success');
            } else {
                bootAlert(data.msg, 'Salvando...', null, 'danger');
            }
            $("#resp-autalizar").html('');
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

});
