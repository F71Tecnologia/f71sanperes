$(document).keypress(function(e) {
    if(e.which == 13) {
        return false;
    }
});

$(document).ready(function () {
    
    $('#data_ini').datepicker({
        today: "Today",
        clear: "Clear",
        titleFormat: "MM yyyy", 
        language: "pt-BR",
        format: "dd/mm/yyyy",
        todayBtn: "linked",
        todayHighlight: true,
        calendarWeeks: false,
        weekStart: 0,
        autoclose: true,
        yearRange: '2005:c+1',
        startDate: "",
        endDate: "",                            
        changeMonth: true,
        changeYear: true                            
    });

    $("#data_ini").mouseover(function() {

        $("#data_ini").css("cursor","hand");

    });    


    // carrega projetos nos inputs de projeto (#projeto_lista, #projeto_rel)
    var regiao = $("#regiao").val();
    $.post('../../methods.php', {method: 'carregaProjetos', regiao: regiao}, function (data) {
        $(".projeto").html(data);
    });

// -----------------------------------------------------------------------------
// lista de clts

    $("body").on('click', ".gerar-ferias-lote", function () {

        var id_ferias = new Array();

        $('.gerar-ferias-lote').each(function () {

            if($(this).data("last-id-ferias")) id_ferias.push($(this).data("last-id-ferias"));            

        });
        
        for (i=0; i<id_ferias.length; i++) {

            console.log(id_ferias[i]);

        }
        
//        id_ferias.shift();
        
        console.log(id_ferias.toString());
        
        bootConfirm(
            "<strong>Confirma a Geração de Férias em Lote para o grupo listado?</strong>",
            'Necessário a confirmação', 
            function(dialog){
                
                if(dialog == true){

                    $.ajax({
                        url: "ferias_processar.php",
                        type: "POST",
                        dataType: "json",
                        data: {
                            method: "gerarPdf",
                            id_ferias: id_ferias.toString()
                        },
                        success: function (data) {

                            console.log(data);

                            if (data.status == 1) {

                                window.open(data.url);

                                event.preventDefault();         


                            }

                        },
                        error: function(data){

                            console.log(data.responseText);

                        }
                        
                    });
                    
                }
            },
            'warning'
        );        

        
    });


    // submit para trazer a lista de CLTs 
    $("#submit-lista").click(function () {
        var projeto = $("#projeto_lista").val();
        var regiao = $("#regiao").val();
        var pesquisa = $("#pesquisa").val();
        var data_ini_fmt = $("#data_ini_fmt").val();
        $("#retorno-lista").html("<p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
        $.post("ferias_lista-funcionarios.php", {regiao: regiao, projeto: projeto, pesquisa: pesquisa,data_ini_fmt: data_ini_fmt}, function (data) {
            $("#retorno-lista").html(data);
            $(".tooo").tooltip();
        }, 'html');
    });

    $("body").on('click', ".lancar-ferias", function () {
        console.log('tá funcionando');
        
        var id_clt = $(this).data('id-clt');
        $("#id_clt").val(id_clt);
        
        var url = '/intranet/?class=ferias/processar&id_clt='+id_clt;
        
        window.open(url);
        
//        $("#form-lista").attr("action", "ferias_processar.php");
//        $("#form-lista").submit();
    });
    
    

$('#chk_definir_dias_ferias').click(function(){
    if($(this).is(":checked")){
//        $('#dias_ferias').removeAttr('readonly','readonly');
        $('.dias_ferias_div').removeClass('hidden');
        
    }else{
//        $('#dias_ferias').attr('readonly','readonly');
        $('.dias_ferias_div').addClass('hidden');
    }
});

// -----------------------------------------------------------------------------
// relatorio de férias

    // trás os cargos para determinado projeto
    $('#projeto_rel').change(function () {
        $.post("/intranet/methods.php", {proj: [$("#projeto_rel").val()], tipo: 2, method: 'carregaCargos'}, function (resultado) {
            //console.log($("#regiao").val());
            $("#funcao").html(resultado);
        });
    });

    // submit para trazer o relatorio de CLTs
    $("#submit-relatorio").click(function () {
        var projeto = $("#projeto_rel").val();
        var regiao = $("#regiao").val();
        var mesIni = $("#mes_ini").val();
        var mesFim = $("#mes_fim").val();
        var anoIni = $("#ano_ini").val();
        var anoFim = $("#ano_fim").val();
        var funcao = $("#funcao").val();
        $("#retorno-relatorio").html("<p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
        $.post("ferias_relatorio-ferias.php", {regiao: regiao, projeto: projeto, mesIni: mesIni, mesFim: mesFim, anoIni: anoIni, anoFim: anoFim, funcao: funcao}, function (data) {
            $("#retorno-relatorio").html(data);
        }, 'html');
    });

    $("#mes_ini,#ano_ini,#mes_fim,#ano_fim").change(function () {
        var mes_ini = $("#mes_ini").val();
        var mes_fim = $("#mes_fim").val();
        var ano_ini = $("#ano_ini").val();
        var ano_fim = $("#ano_fim").val();
        var data_ini = ((mes_ini.length < 2) ? ano_ini + 0 + mes_ini : ano_ini + mes_ini);
        var data_fim = ((mes_fim.length < 2) ? ano_fim + 0 + mes_fim : ano_fim + mes_fim);
        if (mes_ini != '-1' && mes_fim != '-1' && ano_ini != '-1' && ano_fim != '-1') {
            if (parseInt(data_ini) > parseInt(data_fim)) {
                bootAlert("Data de início menor que data de saída.", "Erro nas datas!", null, "warning");
            }
        }
//        alert(data_ini + " " + data_fim);
    });

// -----------------------------------------------------------------------------


    // exibe histórico de férias do clt
    $('body').on('click',".historico-ferias",function () {
        var url = 'ferias_historico.php';
        var id_clt = $(this).data('id-clt');
        $.post(url,{id_clt:id_clt},function(data){
            bootDialog(data,'Histórico de Férias');
            $("[data-toggle='tooltip']").tooltip(); 
        });
    });
    // exibe Férias Agendadas do clt
    $('body').on('click',"#detalhamento",function () {
        var url = 'ferias_historico.php';
        var id_clt = $(this).data('id-clt');
        $.post(url,{id_clt:id_clt},function(data){
            bootDialog(data,'Histórico de Férias');
            $("[data-toggle='tooltip']").tooltip(); 
        });
    });

    // desprocessar ferias
//    $('body').on('click',".desprocessar_ferias",function () {
//        var id_clt = $(this).data('clt');
//        var id_ferias = $(this).data('ferias');
//        bootConfirm(
//            'Deseja Desprocessar esta Férias?', 
//            "<strong>Desprocessar Férias</strong>",
//            function(dialog){
//                if(dialog == true){
//                    cria_carregando_modal();
//                    $.post("desprocessar_ferias.php", {clt:id_clt, ferias:id_ferias}, function(resposta){
//                        console.log(resposta);
//                        bootAlert(resposta, 'Desprocessar Férias!', function(){$('.modal').modal('hide');}, 'success');
//                        remove_carregando_modal();
//                    });
//                }
//            },
//            'warning'
//        );
//    });


// -----------------------------------------------------------------------------
// Ferias Programadas
    $('body').on('click', ".ver_agendados", function () {
        var data = $(this).data('data');
        var qtd = $(this).data('qtd');
        var projeto = $('#projeto').val();
        //console.log(projeto);
        if(qtd > 0) {
            //cria_carregando_modal();
            $.post("action/action_ferias_programadas.php", { action:'ver_agendados', data:data, projeto:projeto }, function(resposta){
                
                bootDialog(
                    resposta, 
                    data.split("-").reverse().join('/'), 
                    [{
                        label: 'Fechar',
                        cssClass: 'hide',
                        action: function (dialog) {
                            dialog.close();
                        }
                    }],
                    'info'
                );
                //remove_carregando_modal();
            });
        }
    });

    $('body').on('click', ".ProximoAnterior", function () {
        var mes = $(this).data('mes');
        var ano = $(this).data('ano');
        var projeto = $('#projeto').val();
        cria_carregando_modal();
        $.post("ferias_calendario.php", {bugger:Math.random(), mes:mes, ano:ano, projeto:projeto}, function(resposta){
            //console.log(resposta);
            $("#calendario-container").html(resposta);
            remove_carregando_modal();
        });
    });

    $('body').on('change', ".trocaData", function () {
        var mes = $('#mes').val();
        var ano = $('#ano').val();
        var projeto = $('#projeto').val();
        cria_carregando_modal();
        $.post("ferias_calendario.php", {bugger:Math.random(), mes:mes, ano:ano, projeto:projeto}, function(resposta){
            //console.log(resposta);
            $("#calendario-container").html(resposta);
            remove_carregando_modal();
        });
    });
    
    $('#form').validationEngine();
    
    $('body').on('change', '#projeto, #curso', function(){
        cria_carregando_modal();
        $.post("action/action_ferias_programadas.php", { bugger:Math.random(), action:'get_clt', projeto:$('#projeto').val(), unidade:$('#curso').val()}, function(r1){
            $("#clts").html(r1);
            remove_carregando_modal();
        });
        $.post("action/action_ferias_programadas.php", { bugger:Math.random(), action:'get_curso', projeto:$('#projeto').val(), unidade:$('#curso').val()}, function(r2){
            $("#curso").html(r2);
        });
    });
    
    $('body').on('click', '#salvar', function(){
        cria_carregando_modal();
    });
    
    $('body').on('click', '.deletarAgendamento', function(){
        var id = $(this).data('key');
        bootConfirm(
            '<strong>Deletar Agendamento?</strong>',
            '<strong>Deletar Agendamento?</strong>', 
            function(dialog){
                if(dialog == true){
                    $.post("action/action_ferias_programadas.php", { action:'deletar', id:id}, function(resposta){
                        $('#projeto').trigger('change');
                        bootAlert(
                            'Agendamento Deletado',
                            'Agendamento Deletado',
                            function(){
                                $('.modal').modal('hide');
                            },
                            'success'
                        );
                    });
                }
            },
            'warning'
        );
        
    });
    
    $("#calendario-container").load("ferias_calendario.php");
    
    // -----------------------------------------------------------------------------
    // ABA SOLICITAÇÕES
    
    $('#retorno-solicitacoes').load('ferias_lista-solicitacoes.php');
    
//    $("#busca-solicitacoes").click(function () {
//        
////        var mes = $("#mes_ini_solicita").val();
////        var ano = $("#ano_ini_solicita").val();
////        var projeto = $("#projeto_solicita").val();
////        var status = $("#status").val();
//        
//        $("#retorno-solicitacoes").html("<p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
//        $.post("ferias_lista-solicitacoes.php", {mes: mes, projeto: projeto, ano: ano, status:status}, function (data) {
//            $("#retorno-solicitacoes").html(data);
//            $(".tooo").tooltip();
//        }, 'html');
//    });
    
    /**
     * VER DETALHES
     */
    $("body").on("click",".ver_detalhes",function(){
        var url = 'ferias_acoes_solicitacoes.php';
        var id_clt = $(this).data('id-clt');
        var projeto = $(this).data('projeto');
        var mes = $(this).data('mes');
        var ano = $(this).data('ano');
                
        $.post(url,{id_clt:id_clt, projeto:projeto, mes:mes, ano:ano},function(data){
            bootDialog(data,'Ver Detalhes');
            $("[data-toggle='tooltip']").tooltip(); 
        });
    });
    
    /**
     * CONCEDER PEDIDO DE FERIAS 
     */
    $("body").on("click",".agendarFerias",function(){
         var id_clt = $("#clt").val();
         var inicio = $("#inicio").val();
         var fim    = $("#fim").val();
         var solicitacao    = $("#id_solicitacao").val();
         var adiantamento_13 = $("#decimo_13").val();
         var id_ferias_programada = $("#id_ferias_programada").val();
         
         $.ajax({
            url:"ferias_acoes_solicitacoes.php",
            type:"post",
            dataType:"json",
            data:{
               adiantamento_13:adiantamento_13,
               id_solicitacao:solicitacao, 
               id_clt: id_clt,
               inicio: inicio,
               fim: fim,
               method: "agendarFerias",
               id_ferias_programada:id_ferias_programada
            },
            success: function(data){
                if(data.status){
                    $(".close").trigger("click");
                    //$("tr[data-key=" +solicitacao+ "]").hide();
                    console.log("Agendamento realizado com sucesso");
                }
            }
         });
         
    });

});
