$(function() {
    
    $(".bt-image").on("click", function() {
        var action = $(this).data("type");
        var key = $(this).data("key");
        var periodo = $(this).data("periodo");
        var emp = $(this).parents("tr").find("td:first").next().html();
        var clt = $(this).data("clt");
        var target = $(this).data("target");
        if(action === "visualizar") {
            $("#banco").val(key);
            $("#form1").attr('action','detalhes_banco.php');
            $("#form1").prop('target',target);
            $("#form1").submit();
        }else if(action === "editar"){
            $("#banco").val(key);
            $("#form1").attr('action','form_banco.php');
            $("#form1").prop('target',target);
            $("#form1").submit();
        }else if(action === "editar_saida"){
            $("#id_saida").val(key);
            $("#form1").attr('action','../form_saida.php');
            $("#form1").prop('target',target);
            $("#form1").submit();
        }else if(action === "editar_saida_rh"){
            $("#id_saida").val(key);
            $("#form1").attr('action','../form_saida_rh.php');
            $("#form1").prop('target',target);
            $("#form1").submit();
        }
        $("#form1").attr('action','');
        $("#form1").prop('target','');
    });
    
    $("#novoBanco").click(function(){
        $("#form1").attr('action','form_banco.php');
        $("#form1").submit();
    });
    
    $("#editarBanco").click(function(){
        var action = $(this).data("type");
        var key = $(this).data("key");
        
        if (action === "editar") {
            $("#banco").val(key);
            $("#form1").attr('action','form_banco.php');
            $("#form1").submit();
        }
    });
    
    /////////////////////
    //CADASTRO DE SAIDA//
    /////////////////////
    
    function limita_caractere(campo, limite, muda_campo) {
        var tamanho = campo.val().length;
        
        if (tamanho == limite) {                   
            muda_campo.focus();
            var valor = campo.val().substr(0, limite);
            campo.val(valor);
        }
    }
    
    $("#select_grupo").change(function(){
        var grupo = $(this).val();                
        var terceiro = $('#select_grupo option:selected').data('terceiro');
        $("#tipo").val('-1');
        var projeto = $("#projeto").val();
        
        //if(grupo != '-1'){ 
         if(grupo == 30){
              $("#regiao_prestador").change();
               $("#tipo").change();
             
             
             $("#intera_prestador").hide();
             $("#intera_nome").show();
         }
        
        
            if(grupo == 20 || grupo == 80){                
            if(terceiro) { 
                var regiao_prestador = $("#regiao_prestador").val();                                                
                
                if(regiao_prestador != '-1'){
                    $("#regiao_prestador").change();
//                    $("#projeto_prestador option[value='3232']").attr({selected:true});
//                    console.log("vasco");
                }
                $("#intera_prestador").show();
                $("#intera_nome").hide();
                $("#nome").removeClass('validate[required,custom[select]]');
                $("#nome").val('-1');
            }else{ 
                $("#intera_prestador").hide();
                $("#intera_nome").show();
                $("#nome").addClass('validate[required,custom[select]]');
                $("#regiao_prestador, #projeto_prestador, #prestador, #fornecedor").val('-1');
                $("#tipo_empresa1").prop('checked', false);
                $("#tipo_empresa2").prop('checked', false);
                $("#nome_prestador").hide();
                $("#nome_fornecedor").hide();
                $("#nome_outros").hide();
            }
        }
    });
    
    $("#tipo").change(function(){
        var faturamento = $('#tipo option:selected').data('faturamento');
        var terceiro = $('#select_grupo option:selected').data('terceiro');
        
        if(faturamento == 2){
            $("#intera_prestador").show();
            $("#intera_nome").hide();
            $("#nome").removeClass('validate[required,custom[select]]');
            $("#nome").val('-1');
        } else if(faturamento == 1){
            $("#intera_prestador").hide();
            $("#intera_nome").show();
            $("#nome").addClass('validate[required,custom[select]]');
            $("#regiao_prestador, #projeto_prestador, #prestador, #fornecedor").val('-1');
            $("#tipo_empresa1").prop('checked', false);
            $("#tipo_empresa2").prop('checked', false);
            $("#nome_prestador").hide();
            $("#nome_fornecedor").hide();
            $("#nome_outros").hide();
        } else if(terceiro) {
            //$("#intera_prestador").show();
           // $("#intera_nome").hide();
            $("#nome").removeClass('validate[required,custom[select]]');
            $("#nome").val('-1');
        }else{
            $("#intera_prestador").hide();
            $("#intera_nome").show();
            $("#nome").addClass('validate[required,custom[select]]');
            $("#regiao_prestador, #projeto_prestador, #prestador, #fornecedor").val('-1');
            $("#tipo_empresa1").prop('checked', false);
            $("#tipo_empresa2").prop('checked', false);
            $("#nome_prestador").hide();
            $("#nome_fornecedor").hide();
            $("#nome_outros").hide();
        }
    });
    
    $("#tipo_empresa1").click(function(){
        if($(this).is(":checked")){
            $("#nome_prestador").show();
            $("#nome_fornecedor").hide();
            $("#nome_outros").hide();
            $("#prestador_inativo").val('-1');
            $("#prestador_outros").val('-1');            
        }
    });
    
    $("#tipo_empresa2").click(function(){
        if($(this).is(":checked")){
            $("#nome_fornecedor").show();
            $("#nome_prestador").hide();
            $("#nome_outros").hide();
            $("#prestador").val('-1');
            $("#prestador_outros").val('-1');
        }
    });
    
    $("#tipo_empresa3").click(function(){
        if($(this).is(":checked")){
            $("#nome_outros").show();
            $("#nome_fornecedor").hide();
            $("#nome_prestador").hide();
            $("#prestador").val('-1');
            $("#prestador_inativo").val('-1');
        }
    });
    
    $("#add_nome").click(function(){
        var tipo = $("#tipo").val();        
        
        BootstrapDialog.show({
            title: 'Cadastro de Nome',
            message: $('<div></div>').load('form_nome.php?tipo='+tipo)
        });
    });
    
    $("#add_prestador").click(function(){
        
        BootstrapDialog.show({
            title: 'Cadastro de Prestador',
            message: $('<div></div>').load('form_prestador.php')
        });
    });
    
    $("#tipo").change(function(){
       var tipo = $(this).val();       
       
       if(tipo != '-1'){
           $("#add_nome").show();
       }else{
           $("#add_nome").hide();
       }
       
       if(tipo == 132){
           $("#intera_prestador").show();
           $("#add_nome").hide();
       }
    });
    
    $("#referencia").change(function(){
        var ref = $(this).val();
        
        if(ref == 2){
            $("#intera_bens").show();
        }else{
            $("#intera_bens").hide();
            $("#bens").val('-1');
        }
    });
    
    $("#tipo_pg").change(function(){
       var tipo_pg = $(this).val();
       
       if(tipo_pg == 1){
           $("#intera_boleto").show();
           $('.link_nfe').hide();
           $("#dados_nf").hide();
       }else if(tipo_pg == 3){
           $("#intera_boleto").hide();  
           $("#cod_barra_consumo").hide();
           $("#cod_barra_geral").hide();
           $('.link_nfe').show();
           $("#tipo_boleto").val('-1');
           $("#nosso_numero").val('');
           $("#intera_numero").hide();
           $("#dados_nf").show();
       }else if(tipo_pg == 8 || tipo_pg == 15){
           $("#intera_boleto").hide();  
           $("#cod_barra_consumo").hide();
           $("#cod_barra_geral").hide();
           $('.link_nfe').hide();
           $("#tipo_boleto").val('-1');
           $("#nosso_numero").val('');
           $("#intera_numero").hide();
           $("#dados_nf").hide();
           $("#dados_cheque").show();
       }else{
           $("#intera_boleto").hide();
           $("#cod_barra_consumo").hide();
           $("#cod_barra_geral").hide();
           $('.link_nfe').hide();
           $("#tipo_boleto").val('-1');
           $("#nosso_numero").val('');
           $("#intera_numero").hide();
           $("#dados_nf").hide();
       }
    });
    
    $("#tipo_boleto").change(function(){
        var tipo_bol = $(this).val();
        
        if(tipo_bol == 1){
            $("#intera_numero").hide();
            $("#cod_barra_consumo").show();
            $("#cod_barra_geral").hide();
            $("#nosso_numero").val('');
            $("#barra_geral01,#barra_geral02,#barra_geral03,#barra_geral04,#barra_geral05,#barra_geral06,#barra_geral07,#barra_geral08").val('');
        }else{
            $("#intera_numero").show();
            $("#cod_barra_consumo").hide();
            $("#cod_barra_geral").show();
            $("#barra_consumo01,#barra_consumo02,#barra_consumo03,#barra_consumo04,#barra_consumo05,#barra_consumo06,#barra_consumo07,#barra_consumo08").val('');
        }
        
        if(tipo_bol == '-1'){
            $("#intera_numero").hide();
            $("#cod_barra_consumo").hide();
            $("#cod_barra_geral").hide();
            $("#nosso_numero").val('');
        }
        
    });
    
    $('#barra_consumo01, #barra_consumo03, #barra_consumo05, #barra_consumo07').keyup(function() {
        limita_caractere($(this), 11, $(this).next().next());
    });
    
    $('#barra_consumo02, #barra_consumo04, #barra_consumo06, #barra_geral07').keyup(function() {
        limita_caractere($(this), 1, $(this).next().next());
    });
    
    $('#barra_geral01, #barra_geral02, #barra_geral03, #barra_geral05').keyup(function() {
        limita_caractere($(this), 5, $(this).next().next());
    });
    
    $('#barra_geral04, #barra_geral06').keyup(function() {
        limita_caractere($(this), 6, $(this).next().next());
    });
    
    $("#estorno").change(function(){
        var est = $(this).val();
        
        if(est == 1){
            $("#intera_valest").hide();
            $("#intera_descest").show();
            $("#valor_estorno_parcial").val('');
        }else if(est == 2){
            $("#intera_valest").show();
            $("#intera_descest").show();
        }else{
            $("#intera_valest").hide();
            $("#intera_descest").hide();
            $("#valor_estorno_parcial, #descricao_estorno").val('');
        }
    });
    
    $("#regiao").change(function(){
        var regiao = $(this).val();        
                
//        $("#banco").val('-1');
        $("#regiao_prestador").val(regiao);
    });
    
//    $("#projeto").change(function(){
//        var projeto = $(this).val();                
//                
//        $("#projeto_prestador").val(projeto);
//    });
    
    ///////////////////
    //EDIÇÃO DE SAIDA//
    ///////////////////
    
    //tipo de empresa
    if($("#tipo_empresa1").is(":checked")){
        $("#nome_prestador").show();
        $("#nome_fornecedor").hide();
//        $("#prestador_inativo").val('-1');
//        $("#prestador_outros").val('-1');
    }else if($("#tipo_empresa2").is(":checked")){
        $("#nome_fornecedor").show();
        $("#nome_prestador").hide();
//        $("#prestador").val('-1');
//        $("#prestador_outros").val('-1');
    }else if($("#tipo_empresa3").is(":checked")){
        $("#nome_outros").show();
        $("#nome_prestador").hide();
        $("#nome_fornecedor").hide();
//        $("#prestador_inativo").val('-1');
//        $("#prestador").val('-1');
    }
    
    if($("#referencia").val() == 2){
        $("#intera_bens").show();
    }else{
        $("#intera_bens").hide();
        $("#bens").val('-1');
    }
    
    //tipo de pgt
    var tipo_pg = $("#tipo_pg").val();
    if(tipo_pg == 1){
        $("#intera_boleto").show();
        $('.link_nfe').hide();
    }else if(tipo_pg == 3){
        $("#intera_boleto").hide();
        $("#cod_barra_consumo").hide();
        $("#cod_barra_geral").hide();
        $('.link_nfe').show();
        $("#tipo_boleto").val('-1');
        $("#nosso_numero").val('');
        $("#intera_numero").hide();
    }else{
        $("#intera_boleto").hide();
        $("#cod_barra_consumo").hide();
        $("#cod_barra_geral").hide();
        $('.link_nfe').hide();
        $("#tipo_boleto").val('-1');
        $("#nosso_numero").val('');
        $("#intera_numero").hide();
    }
    
    //tipo de boleto
    var tipo_bol = $("#tipo_boleto").val();
    
    if(tipo_bol == 1){
        $("#intera_numero").hide();
        $("#cod_barra_consumo").show();
        $("#cod_barra_geral").hide();
        $("#nosso_numero").val('');
        $("#barra_geral01,#barra_geral02,#barra_geral03,#barra_geral04,#barra_geral05,#barra_geral06,#barra_geral07,#barra_geral08").val('');
    }else{
        $("#intera_numero").show();
        $("#cod_barra_consumo").hide();
        $("#cod_barra_geral").show();
        $("#barra_consumo01,#barra_consumo02,#barra_consumo03,#barra_consumo04,#barra_consumo05,#barra_consumo06,#barra_consumo07,#barra_consumo08").val('');
    }
    
    if(tipo_bol == '-1'){
        $("#intera_numero").hide();
        $("#cod_barra_consumo").hide();
        $("#cod_barra_geral").hide();
        $("#nosso_numero").val('');
    }
    
    //estorno    
    if($("#estorno").val() == 1){
        $("#intera_valest").hide();
        $("#intera_descest").show();
        $("#valor_estorno_parcial").val('');
    }else if($("#estorno").val() == 2){
        $("#intera_valest").show();
        $("#intera_descest").show();
    }else{
        $("#intera_valest").hide();
        $("#intera_descest").hide();
        $("#valor_estorno_parcial, #descricao_estorno").val('');
    }
    
    if($("#prestador_bd").val() != ''){
        var status_prestador = $("#status_prestador").val();
        if(status_prestador == "ativo"){
            console.log('ativo');
            $("#tipo_empresa1").attr("checked",true);
            $("#nome_outros").hide();
            $("#nome_prestador").show();
            $("#nome_fornecedor").hide();
        }else if(status_prestador == 'inativo'){
            $("#tipo_empresa2").attr("checked",true);
            $("#nome_outros").hide();
            $("#nome_prestador").hide();
            $("#nome_fornecedor").show();
        }else{            
            $("#tipo_empresa3").attr("checked",true);
            $("#nome_outros").show();
            $("#nome_prestador").hide();
            $("#nome_fornecedor").hide();
        }
    }
    
});