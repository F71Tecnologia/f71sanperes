/*
 * AUTOR: LEONARDO
 * 
 * Páginas que usam esse código:
 * acao_evento.php
 * edit_evento.php
 */
$(document).ready(function() {
// changes que calculam datas e dias
    $("#data").change(function() {
        // calcula a nova data de retorno
        $.post("../../methods.php", {method: 'novo_retorno', data: $("#data").val(), dias: $("#dias").val()},
        function(data) {
            $("#data_retorno").val(data.data_retorno);
        }, 'json');
    });
    // calcula a qtd dias
    $("#data_retorno").change(function() {
        $.post("../../methods.php",
                {method: 'calcDias', data: $("#data").val(), data_retorno: $("#data_retorno").val()},
        function(data) {
            if(data.dias >=0){
            $("#dias").val(data.dias);
        }else{
            $("#dias").val(0);
        }
        }, 'json');
    });
    // calcula a data de retorno
    $("#dias").change(function() {
        $.post("../../methods.php",
                {method: 'novo_retorno', data: $("#data").val(), dias: $("#dias").val()},
        function(data) {
            $("#data_retorno").val(data.data_retorno);
        }, 'json');
    });

    // nao deixa a quantidade de dias ser menor que 0
    $(".dias").change(function() {
        if (parseInt($(this).val()) < 0) {
            $(this).val(0);
        }
    });
    
});



