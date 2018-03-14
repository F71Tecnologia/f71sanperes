<?php
session_start();

//Classe para cria��o de log.
include "../../classes/LogClass.php";
$log = new Log();

if(!isset($_COOKIE['logado'])) {
    header("Location: /intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include('../../classes/global.php');
include('../../classes/FuncoesClass.php');

$usuario = carregaUsuario();
$master = $usuario['id_master'];
$id_regiao = $usuario['id_regiao'];
$id_usuario = $_COOKIE['logado'];

$regiao_selecionada = $_REQUEST['hide_regiao'];
$projeto_selecionado = $_REQUEST['hide_projeto'];

$qr_horario = mysql_query("SELECT * FROM rh_horarios WHERE id_horario = '{$_REQUEST[id_horario]}'");
$row_horario = mysql_fetch_assoc($qr_horario);

if(isset($_REQUEST['atualizar'])) {
    
    $idHorario = $_REQUEST['id_horario'];

    $arrAnt = $log->getLinha('rh_horarios',$idHorario);
    
    FuncoesClass::alteraHorario($_REQUEST['id_horario'], $id_regiao);
    
    $arrNovo = $log->getLinha('rh_horarios',$idHorario);
    
    $local = 2;
    $acao = "Edi��o do Hor�rio ID: {$_REQUEST['id_horario']}";
    //Classe de grava��o de log.
    $l = $log->log($local, $acao, 'rh_horarios', $arrAnt, $arrNovo);
    
//    print_array($l);
    header('Location: index.php');
    exit;
}

$_SESSION['regiao_select'] = $regiao_selecionada;
$_SESSION['projeto_select'] = $projeto_selecionado;
session_write_close();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE�ALHO (TROCA DE MASTER E DE REGI�ES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Edi��o de Hor�rio");
$breadcrumb_pages = array("Gest�o de Hor�rios"=>"index.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Edi��o de Hor�rio</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <!--link href="../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css"-->
        
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="jquery.autocomplete.css" rel="stylesheet" type="text/css" />  
        <style>
            .cooperado, #p_valor_hora{
                display: none;
            }
            .some_insa, #hide_noturno, #del_hor {
                display: none;
            }
        </style>
    </head>
    <body>
    <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Edi��o de Hor�rio</small></h2></div>
                </div>
            </div>
            <div class="row">
                <form action="" class="form-horizontal" method="post" name="form1" id="form1" autocomplete="off">
                    <div class="col-xs-12 form_funcoes">
                            <div class="panel-body">
                                <fieldset class="horario" data-position="0">
                                    <legend>Dados do Hor�rio</legend>
                                    <div class="form-group">
                                        <div class="">
                                            <label class="col-xs-1 col-xs-offset-11 control-label pull-right pointer del_hor" id="del_hor"><img src="../../imagens/icones/icon-delete.gif" title="Deletar hor�rio" /></label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-xs-2 control-label">Nome do Hor�rio:</label>
                                        <div class="col-xs-10">
                                            <input type="text" name="nome_horario[]" id="nome_horario" value="<?php echo $row_horario['nome']; ?>" class="form-control limpa nome_horario" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-xs-2 control-label">Observa��es:</label>
                                        <div class="col-xs-10">
                                            <input type="text" name="obs[]" id="obs" value="<?php echo $row_horario['obs']; ?>" class="form-control limpa obs" />
                                        </div>
                                    </div>
                                    <div class="form-group remove">
                                        <label for="nome" class="col-xs-2 control-label">Preenchimento:</label>
                                        <div class="col-xs-2">
                                            Entrada <input type="text" name="entrada[]" id="entrada_0" value="<?php echo $row_horario['entrada_1']; ?>" class="form-control preenchimento  limpa entrada" data-ordem="0" />
                                        </div>
                                        <div class="col-xs-2">
                                            Sa�da Almo�o <input type="text" name="ida_almoco[]" id="ida_almoco_0" value="<?php echo $row_horario['saida_1']; ?>" class="form-control preenchimento  limpa ida_almoco" />
                                        </div>
                                        <div class="col-xs-2">
                                            Retorno Almo�o <input type="text" name="volta_almoco[]" id="volta_almoco_0" value="<?php echo $row_horario['entrada_2']; ?>" class="form-control preenchimento  limpa volta_almoco" />
                                        </div>
                                        <div class="col-xs-2">
                                            Sa�da <input type="text" name="saida[]" id="saida_0" value="<?php echo $row_horario['saida_2']; ?>" class="form-control preenchimento  limpa saida" />
                                        </div>
                                    </div>
                                    <div class="form-group esquerda" id="esquerda">
                                        <label for="nome" class="col-xs-2 control-label">Horas M�s:</label>
                                        <div class="col-xs-4">
                                            <input type="text" name="horas_mes[]" id="horas_mes" value="<?php echo $row_horario['horas_mes']; ?>" size="30" maxlength="4" class="form-control validate[custom[onlyNumberSp]] limpa horas_mes" />
                                        </div>
                                        <label for="nome" class="col-xs-2 control-label">Dias M�s:</label>
                                        <div class="col-xs-4">
                                            <input type="text" name="dias_mes[]" id="dias_mes" value="<?php echo $row_horario['dias_mes']; ?>" size="30" maxlength="4" class="form-control validate[custom[onlyNumberSp]] limpa dias_mes" />
                                        </div>
                                    </div>
                                    <div class="form-group direita" id="direita">
                                        <label for="nome" class="col-xs-2 control-label">Horas Semanais:</label>
                                        <div class="col-xs-4">
                                            <input type="text" name="horas_semana[]" id="horas_semana" value="<?php echo $row_horario['horas_semanais']; ?>" size="30" maxlength="4" class="form-control validate[custom[onlyNumberSp]] limpa horas_semana" />
                                        </div>
                                        <label for="nome" class="col-xs-2 control-label">Dias Semana:</label>
                                        <div class="col-xs-4">
                                            <input type="text" name="dias_semana[]" id="dias_semana" value="<?php echo $row_horario['dias_semana']; ?>" size="30" maxlength="4" class="form-control validate[custom[onlyNumberSp]] limpa dias_semana" />
                                        </div>
                                    </div>
                                    <div class="form-group direita" id="direita">
                                        <label for="nome" class="col-xs-2 control-label">Folgas:</label>
                                        <div class="col-xs-2">
                                            <div class="checkbox">
                                                <label><input id="sabado" class="check" type="checkbox" name="folga[][0]" value="1" <?php if($row_horario['folga'] == 1 || $row_horario['folga'] == 3) { echo 'checked'; } ?> /> S�bado</label>
                                            </div>
                                        </div>
                                        <div class="col-xs-2">
                                            <div class="checkbox">
                                                <label><input id="domingo" class="check" type="checkbox" name="folga[][1]" value="2" <?php if($row_horario['folga'] == 2 || $row_horario['folga'] == 3) { echo 'checked'; } ?> /> Domingo</label>
                                            </div>
                                        </div>
                                        <div class="col-xs-2">
                                            <div class="checkbox">
                                                <label><input id="plantonista" class="check" type="checkbox" name="folga[][2]" value="5" <?php if($row_horario['folga'] == 5) { echo 'checked'; } ?> /> Plantonista</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-xs-2 control-label">Adicional Noturno:</label>
                                        <div class="col-xs-2">
                                            <div class="radio">
                                                <label><input type="radio" name="noturno[]" value="1" id="n_sim" class="n_sim check_not" <?php if($row_horario['adicional_noturno']) { echo 'checked="checked"'; } ?> /> Sim</label>
                                            </div>
                                        </div>
                                        <div class="col-xs-2">
                                            <div class="radio">
                                                <label><input type="radio" name="noturno[]" value="0" id="n_nao" class="n_nao check_not" <?php if(!$row_horario['adicional_noturno']) { echo 'checked="checked"'; } ?> /> N�o</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" id="hide_noturno" <?php if($row_horario['adicional_noturno']) { echo 'style="display:block;"'; } ?>>
                                        <label for="nome" class="col-xs-2 control-label">Horas Noturno:</label>
                                        <div class="col-xs-6">
                                            <input type="text" name="horas_noturno[]" id="horas_noturno" value="<?php echo $row_horario['horas_noturnas']; ?>" maxlength="4" class="form-control horas_noturno" />
                                        </div>
                                        <label for="porcentagemAd" class="col-xs-2 control-label">Porcentagem:</label>
                                        <div class="col-xs-2">
                                            <?= montaSelect(['0.2'=>"20%",'0.3'=>"30%",'0.35'=>"35%",'0.4'=>"40%",'0.5'=>"50%"],$row_horario['porcentagem_adicional'],"id='porcentagemAd' class='form-control' name='porcentagemAd[]'"); ?>
                                        </div>
                                    </div>
                                </fieldset>
                                <input name="id_horario" value="<?php echo $_REQUEST['id_horario']; ?>" type="hidden">
                            </div>
                            <div class="panel-footer text-right">
                                <input type="submit" class="btn btn-primary" name="atualizar" id="atualizar" value="Atualizar" />
                                <!--input type="button" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'index.php';" /-->
                            </div>
                    </div>
                </form>
            </div>
        <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        
        <script src="../../js/jquery.price_format.2.0.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <!--script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script-->
        <script src="../../js/jquery.autocomplete.js" type="text/javascript"></script>
        <script>
            $(function(){
                //mascara
                $("#data_ini").mask("99/99/9999");
                $("#data_fim").mask("99/99/9999");
                $("#salario, #valor, #quota, #salario_novo").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});
                $(".entrada, .ida_almoco, .volta_almoco, .saida").mask("99:99:99");
                
                //autocomplete
                $("#cbo").autocomplete("lista_cbo.php", {
                    width: 600,
                    matchContains: false,      
                    minChars: 3,
                    selectFirst: false                    
                });
                
                //validation engine
                $("#form1").validationEngine({promptPosition : "topRight"});
                
                //oculta/exibe dados do CLT
                window.func2 = $("#func2").clone();
                $('#contratacao').change(function(){                    
                    if(($(this).val() == "1") || ($(this).val() == "3")){
                        $("#func2").remove();
                    }else if($(this).val() == "2"){
                        if (!$("div.form_funcoes fieldset#func2").length) {
                           var fieldset =  $(document.createElement('fieldset')).append(window.func2.html()).prop('id','func2');
                           $("#func1").after(fieldset);
                        }
                    }
                });
                
                //chickbox
                $(".bt-image").on("click", function() {
                    var action = $(this).data("type");
                    
                    var txtVal = $("#textVal").html();
                    $("#salario_antigo").html("#salario_new");
                    $(".valorForm").html(txtVal);
                    $("#salario_novo").val("");
                    $("#diferenca").html("");
                    
                    if (action === "salario") {
                        //thickBoxIframe("Altera��o Salarial", "altera_salario.php", {curso: key, method: "getDocs"}, "360-not", "240");
                        thickBoxModal("Altera��o Salarial", "#box_salario", "240", "360", null, null).css({display: "block"});
                    }
                });
                
                //calculo de diferen�a salarial
                $(".bt-image2").click(function() {
                    var antigo = $('#salario_antigo').val();
                    var novo = $('#salario_novo').val().replace('.', '');
                        novo = novo.replace(',', '.');
                    var total = (parseFloat(novo) - parseFloat(antigo)).toFixed(2);
                    
                    console.log(antigo);
        
                    $("#diferenca").html(total);
                    $("#difere").val(total);
                    $("#salario_new").val(novo);
                    $("#salario").val(novo);
                });
                
                $("#altera_salario").click(function() {                    
                    var novo = $('#salario_novo').val().replace('.', '');
                        novo = novo.replace(',', '.');
                    var data = $("#form2").serialize();
                    
                    if((novo === 0) || (novo === '')){
                        $("#erro2").html('<strong>Preencha o Sal�rio Novo</strong>').css({color: "#F00"});
                    }else if($("#difere").val() === ''){
                        $("#erro2").html('<strong>Calcule a diferen�a</strong>').css({color: "#F00"});
                    }else{
                        $.post('edit_curso.php?method=alteraSalario&' + data, null, function(data){
                            if(data.status == 1){
                                $('#textVal').html(data.valor);
                                //$(".ui-icon-closethick").trigger("click");
                                thickBoxClose();                                
                            }
                        },'json');
                    }
                });                               
                
                //clona o fieldset de horario
                $("#add_hor").click(function(){
                    var clone = $('.form_funcoes .horario:last').clone(false);
                    var next_position = parseFloat( clone.attr('data-position')) + 1;
                    clone.attr('data-position', next_position);                                       
                    clone.find("*[id]").andSelf().each(function() { 
                        $(this).attr("id", $(this).attr("id") + next_position); 
                    });
                    
                    clone.find(".check_not").each(function(){
                        $(this).attr({name:"noturno[" + next_position + "]"});
                    });
                    
                    $('.form_funcoes .horario:last').after(clone);
                    var p = $(this).prev().attr("data-position");
                    if(p == next_position){
                        $("fieldset[data-position = " + next_position + "] .check[value=1]").attr({name:"folga[" + next_position + "][0]"});
                        $("fieldset[data-position = " + next_position + "] .check[value=2]").attr({name:"folga[" + next_position + "][1]"});
                        $("fieldset[data-position = " + next_position + "] .check[value=5]").attr({name:"folga[" + next_position + "][2]"});
                    }
                                        
                    clone.find(".limpa").val("");
                    clone.find(".check").prop('checked', false);
                    //$("p").removeClass("preenchimento");
                    //$("#entrada, #ida_almoco, #volta_almoco, #saida").mask("99:99:99");
                    
                    $('.form_funcoes .horario:last').addClass("del");
                    $(".del .del_hor").css({display: 'block'});
                    
                    $(".entrada, .ida_almoco, .volta_almoco, .saida")
                        .unmask() //Desabilita a m�scara. Se n�o fizer isso d� problema
                        .mask("99:99:99"); //Habilita novamente, pegando todos os campos criados 
                
                    $(".check_not").on('click', function (){
                        var hide_noturno = $(this).parent().parent().parent().parent().next();
                        var noturno = $(this).val();

                        if(noturno == 1){
                            hide_noturno.show();
                        }else{
                            hide_noturno.children().val('');
                            hide_noturno.hide();
                        }
                    });
                    
                    $(".n_sim").each(function(){
                        if($(".n_sim").is(':checked')){
                            $(this).parent().parent().parent().parent().next();
                        }
                    });
                    
                    $(".del_hor").on('click', function (){
                        $(this).parents("fieldset").remove();
                    });
                });
                
                $(".del_hor").on('click', function (){
                    $(this).parents("fieldset").remove();
                });
                
                //trata insalubridade/periculosidade
                $("#insal").click(function(){
                    $(".some_insa").show();                    
                    $("#insalubridade").addClass("validate[custom[select]]");
                    $("#qtd_salarios").addClass("validate[required,custom[onlyNumberSp]]");
                });
                
                $("#peric").click(function(){
                    $(".some_insa").hide();
                    $("#insalubridade").removeClass("validate[custom[select]]");
                    $("#qtd_salarios").removeClass("validate[required,custom[onlyNumberSp]]");
                });
                
                if($("#insal").is(':checked')){
                    $(".some_insa").show();
                }
                
                $(".check_not").on('click', function (){
                    var hide_noturno = $(this).parent().parent().parent().parent().next();
                    var noturno = $(this).val();
                    
                    if(noturno == 1){
                        hide_noturno.show();
                    }else{
                        hide_noturno.children().val('');
                        hide_noturno.hide();
                    }
                });
                
                $(".n_sim").each(function(){
                    if($(".n_sim").is(':checked')){
                        $(this).parent().parent().parent().parent().next();
                    }
                });

                $("#plantonista").click(function(){
                    if($("#plantonista").is(':checked')){
                        $("#sabado, #domingo").removeAttr('checked');
                    }
                });

                $("#sabado, #domingo").click(function(){
                    if($("#sabado, #domingo").is(':checked')){
                        $("#plantonista").removeAttr('checked');
                    }
                });
            });
        </script>
    </body>
</html>
