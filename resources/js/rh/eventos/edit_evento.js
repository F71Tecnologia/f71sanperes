/*
 * AUTOR: LEONARDO
 * 
 * Páginas que usam esse código:
 * acao_evento.php
 * edit_evento.php
 */

// funcao que pega o valor da flag pericia
//function getPericia(evento) {
//    var retorno;
//    ajaxViaSync("../../methods.php", {method: 'getPericia', evento: evento},
//    function (data) {
//        retorno = data.pericia;
////            console.log(retorno);
//    });
//    return retorno;
//}

// ajax configurado para enviar com async false
function ajaxViaSync(url, data, sucess) {
    $.ajax({
        url: url,
        type: 'post',
        data: data,
        async: false,
        dataType: 'json',
        success: sucess
    });
}

$(document).ready(function () {

    // changes que calculam datas e dias
    $("#data").change(function () {
        var evento = $("#evento").val();
        if (evento != 10) {
            var data = $(this).val();
            var dias = (($("#dias").val() != '') ? $("#dias").val() : '0');
//            var pericia = getPericia(evento);
//            var tag_data = ((pericia == 1) ? "#data_retorno" : "#data_final");
            var tag_data = "#data_retorno";

            // calcula a nova data de retorno
            ajaxViaSync("../../methods.php", {method: 'novo_retorno', data: data, dias: dias},
            function (data) {
                if (dias != 0) {
                $(tag_data).val(data.data_retorno);
                }
            });
        }
    });

    // calcula a qtd dias
    $("#data_retorno,#data_final").change(function () {
        var data = $("#data").val();
        var data_this = $(this).val();

        ajaxViaSync("../../methods.php", {method: 'calcDias', data: data, data_retorno: data_this},
        function (data) {
            if (data.dias >= 0) {
                $("#dias").val(data.dias);
            } else {
                $("#dias").val(0);
            }
        });
    });

    // calcula a data de retorno
    $("#dias").blur(function () {
//        var evento = $("#evento").val();
        var data = $("#data").val();
        var dias = (($("#dias").val() != '') ? $("#dias").val() : '0');

//        var pericia = getPericia(evento);
//        var tag_data = ((pericia == 1) ? "#data_retorno" : "#data_final");
        var tag_data = "#data_retorno";
        console.log('dias2' + dias);
        ajaxViaSync("../../methods.php", {method: 'novo_retorno', data: data, dias: dias},
        function (dados) {
            if (dias != 0 && data != '') {
                $(tag_data).val(dados.data_retorno);
            }else{
                $(tag_data).val('');
            }
        });
    });

    // nao deixa a quantidade de dias ser menor que 0
    $(".dias").change(function () {
        if (parseInt($(this).val()) < 0) {
            $(this).val(0);
        }
    });

});



