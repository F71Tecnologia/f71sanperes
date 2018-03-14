<?php
session_start();
include ("controle.php");

$usuario = carregaUsuario();
$regiao_usuario = $usuario['id_regiao'];
$id_user = $_COOKIE['logado'];
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}
$abashow = 1;
if(isset($_SESSION['msg'])){
echo $_SESSION['msg']; 

}
 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
        <title>Gest&atilde;o de RH</title> 
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt.js"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script>
            $(function() {
                var listaFuncionarios = [];
                
                $("#form").validationEngine();
                $("#regiao").ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, null, "projeto");

                $("#controle").css("display", "block");
                $("#opcoes").css("display", "block");
                $("#validade").css("display", "block");
                $("#filtro").css("display", "none");
               
                $(".bt-menu").click(function() {
                   var item = $(this).attr('data-item');
                   switch(item){
                       case '3':
                           $("#filtro").css("display", "block");
                           break;
                       case '4':
                            $("#apuracao").css("display", "block");
                            break;
                       default:
                            $("#filtro").css("display", "none");
                            $("#apuracao").css("display", "none");
                            break;
                   }
                });

                $("#admTrab").change(function() {
                    var checar = $(this).prop("checked");
                    if (checar) {
                        $.post('controle.php', {method: "admTrab", regiao: $("#regiao").val(), projeto: $("#projeto").val()}, function(data) {
                            if (data !== '') {
                                $("#aviso").css("display", "block");
                                $("#aviso").html(data);
                            }
                        });
                    } else {
                        $("#aviso").css("display", "none");
                        $("#aviso").val('');
                    }
                });
                
                $("#todosTrab").change(function() {
                    var checar = $(this).prop("checked");
                    if (checar) {
                        $("#filtro").css("display", "block");
                    } else {
                        $("#filtro").css("display", "none");
                    }
                });
                var evento; 
                $(".buscaTrab").change(function() {
                    var checar = $(this).prop("checked");
                    if (checar) {
                        $("#phidden").removeClass("hidden");
                        evento = $(this).val();
                    } else {
                        $('input[type="checkbox"][class="buscaTrab"]:checked').each(function(key, value) {
                            checar = true;
                        });
                        if (!checar) {
                            $("#nome").val("");
                            $("#phidden").addClass("hidden");
                        }
                    }
                });
                
                $("#nome").keyup(function() {
                    if ($(this).val().length >= 3) {
                        $.post('controle.php', {method :'buscaTodosTrab', regiao: $("#regiao").val(), projeto: $("#projeto").val(), evento: evento}, function(data) {
                            $('#nome').autocomplete({source: data.trab});
                        }, 'json');
                    }
                });
                
                $("#validarCad").click(function(){
                   var evento;
                   $("#aviso").html('');
                   $("input:checkbox").each(function() {
                       var items = [];
                       var checar = $(this).prop("checked");
                       if(checar){
                           $("#aviso").css("display","block");
                           evento = $(this).val();
                           $.ajax({
                            url: "controle.php",
                            type: "POST",
                            dataType: "json",
                            data:{
                                method: "validaCad", 
                                evento: evento, 
                                listaFunc:$("#listaFunc").val(), 
                                regiao:$("#regiao").val(), 
                                projeto:$("#projeto").val(), 
                                idMaster:$("#idMaster").val(), 
                                dtInicio:$("#iniValidade").val()},
                            success: function(data) {
                                if(data){
                                    $.each( data.erro, function( id, msg ) {
                                    items.push( "<li> ERRO:" + id + '-' + msg + "</li>" );
                                    });

                                    $("<ul/>",{
                                      "class": "message-red",
                                       html: items.join( "" )
                                    }).appendTo( "#aviso" );
                                }else{
                                    $("#aviso").html("<div id = 'message-box' class = 'message-green'> Nenhum erro detectado. </div>");
                                }
                            },
                            error: function(data) {
                                $("#aviso").html("<div id = 'message-box' class = 'message-red'> Nenhum registro encontrado para o filtro selecionado. </div>");
                            }
                        });
                       }
                       
                   });
                });
                               
                function montaTextArea (listaFuncionarios){
                    textAreaValor = [];
                    listaFuncionarios.map(function(funcionario){
                      textAreaValor.push(funcionario["id"]+" - "+funcionario["nome"]);
                    });
                    $( "#listaFunc" ).val(textAreaValor.join("\n"));
                }
                
                $("#add-func").click(function() {
                    funcionario = [];
                    funcionario["id"] = $("#nome").val().split("-")[0];
                    funcionario["nome"] = $( "#nome" ).val().split("-")[1];
                    listaFuncionarios.push(funcionario);
                    $( "#nome" ).val("");
                    montaTextArea(listaFuncionarios);
                });
                
                $("#remove-func").click(function() {
                    listaFuncionarios = $.grep(listaFuncionarios, function(lFunc) {
                      return lFunc["id"] === $("#id-func").val();
                    }, true);
                    $( "#id-func" ).val("");
                    montaTextArea(listaFuncionarios);
                });

                $(".tpevento").click(function() {
                    if ($(this).val() === 'alteracao') {
                        $("#alterar").css("display", "block");
                    } else {
                        $("#alterar").css("display", "none");
                    }
                });
                $(".validade").datepicker({
                    changeMonth: true,
                    changeYear: true,
                    yearRange: "2000:3000"
                });
                
            });
        </script>
    </head>
    <body class="novaintra" data-type="adm">
        <form method="post" name="form" id="form" action="controle.php">
            <input type="hidden" name="abashow" id="abashow" value="<?php echo $abashow; ?>" />
            <div id="content">
                <div id="geral">
                    <div id="topo" style="height: 117px;">
                        <div class="conteudoTopo txcenter">
                            <div class="imgTopo">
                                <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                            </div>
                            <div class="fright"><?php include('../../reportar_erro.php'); ?></div>
                        </div> 
                    </div>
                    <div id="conteudo">
                        <div class="colEsq">
                            <div class="titleEsq">Ações</div>
                            <ul>
                                <li>
                                    <a href="javascript:;" data-item="1" class="bt-menu aselected" >Eventos Iniciais</a>
                                </li>
                                <li>
                                    <a href="javascript:;" data-item="2" class="bt-menu">Eventos De Tabelas</a>
                                </li>
                                <li>
                                    <a href="javascript:;" data-item="3" class="bt-menu">Eventos Não Periódicos</a>
                                </li>
                                <li>
                                    <a href="javascript:;" data-item="4" class="bt-menu">Eventos Periódicos</a>
                                </li>
                                <li>
                                    <a href="javascript:;" data-item="5" class="bt-menu">Evento de Exclusão</a>
                                </li>
                            </ul>
                        </div>
                        <div class="colDir">                            
                            <div id="opcoes">
                                <p>
                                    <input type="radio" name="tpevento" value="inclusao" class="tpevento" checked="chacked">Inclusão</input>
                                    <input type="radio" name="tpevento" value="alteracao" class="tpevento">Alteração</input>
                                    <input type="radio" name="tpevento" value="exclusao" class="tpevento">Exclusão</input>
                                </p>
                            </div>
                            <div id="validade">
                                <p>
                                    <fieldset>
                                        <legend>Validade das Informações</legend>
                                        <div class="fleft">
                                            <p>
                                                <label class="first">Início: </label>
                                                <input type="text" name="iniValidade" id="iniValidade" class="validade validate[required[custon[date]]]"/>
                                                <label class="first">Término: </label>
                                                <input type="text" name="fimValidade" id="fimValidade" class="validade"/>
                                            </p>
                                        </div>
                                    </fieldset> 
                                </p>
                            </div>
                            <div id="alterar">
                                <p>
                                    <fieldset>
                                        <legend>Nova validade das Informações</legend>
                                        <div class="fleft">
                                            <p>
                                                <label class="first">Início: </label>
                                                <input type="text" name="iniValidadeN" id = "iniValidadeN" class="validade"/>
                                                <label class="first">Término: </label>
                                                <input type="text" name="fimValidadeN" id= "fimValidadeN" class="validade"/>
                                            </p>
                                            <p><label class="first">Númedo do Recibo:</label><input type="text" name="recibo" id="recibo" value=""/></p>
                                        </div>
                                    </fieldset>  
                                </p>
                            </div>
                            <div id="filtro">
                                <p><label class="first">Região:</label> <?php echo montaSelect(GlobalClass::carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' class='validate[required]'") ?></p>
                                <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto' name='projeto' class='validate[required]'") ?></p>
                                <div id="phidden" class="hidden">
                                    <p>
                                        <label>Busca funcionário (código ou nome):</label>
                                        <input type = 'text' id='nome' name = 'nome'/>
                                        <input type='button' id='add-func' name = 'add-func' value="»" title="Adicionar a lista de funcionários"/>
                                    </p>
                                    <p>
                                        <label>Remove funcioário (código):</label>
                                        <input name="id-func" id="id-func" type="text"/>
                                        <input  type='button' id="remove-func" value="«"/><br/>
                                    </p>
                                    <p>
                                        <label>Lista de Funcioários:</label><br/>
                                        <textarea cols="60" rows="7" id="listaFunc" name="listaFunc" readonly="readonly" class="validate[required]"></textarea>
                                    </p>
                                </div>
                            </div>
                            <div id="aviso"></div>
                            <div id="item1">
                                <p><input type="checkbox" name="evento[]" value="s1000">S1000 - Informações do Empregador/Contribuinte ok </input></p>
                                <p><input type="checkbox" name="evento[]" value="s1060">S1060 - Tabela de estabelecimentos ok </input></p>
                                <p><input type="checkbox" name="evento[]" value="s2100" id="todosTrab">S2100 - Evento Cadastramento Inicial do Vínculo ok</input></p>    
                            </div>
                            <div id="item2">
                                <p><input type="checkbox" name="evento[]" value="s1010">S1010 - Tabela de Rubricas ok</input></p>
                                <p><input type="checkbox" name="evento[]" value="s1020">S1020 - Tabela de Lotações ok</input></p>
                                <p><input type="checkbox" name="evento[]" value="s1030">S1030 - Tabela de Cargos ok</input></p>
                                <p><input type="checkbox" name="evento[]" value="s1050" class="validar">S1050 - Tabela de horários/Turnos de trabalho ok</input></p>
                                <p><input type="checkbox" name="evento[]" value="s1070">S1070 - Tabela de processos</input></p>
                            </div>
                            <div id="item3">
                                <p><input type="checkbox" name="evento[]" value="s2200" id="admTrab">S2200 - Admissao de Trabalhador ok</input></p>
                                <p><input type="checkbox" name="evento[]" value="s2220" class = "buscaTrab">S2220 - Alteração de Dados Cadastrais do Trabalhador ok</input></p>
                                <p><input type="checkbox" name="evento[]" value="s2240" class = "buscaTrab">S2240 - Alteração de Contrato de Trabalho ok</input></p>
                                <p><input type="checkbox" name="evento[]" value="s2260" class = "buscaTrab">S2260 - Cominicação de Acidente</input></p>
                                <p><input type="checkbox" name="evento[]" value="s2280" class = "buscaTrab">S2280 - Atestado de Saúde Ocupacional</input></p>
                                <p><input type="checkbox" name="evento[]" value="s2320" class = "buscaTrab">S2320 - Afastamento Temporário ok</input></p>
                                <p><input type="checkbox" name="evento[]" value="s2325" class = "buscaTrab">S2325 - Alteração de Motivo de Afastamento ok</input></p>
                                <p><input type="checkbox" name="evento[]" value="s2330" class = "buscaTrab">S2330 - Retorno de Afastamento Temporário ok</input></p>
                                <p><input type="checkbox" name="evento[]" value="s2340" class = "buscaTrab">S2340 - Estabilidade - Início MICHELE</input></p>
                                <p><input type="checkbox" name="evento[]" value="s2345" class = "buscaTrab">S2340 - Estabilidade - Término</input></p>
                                <p><input type="checkbox" name="evento[]" value="s2360" class = "buscaTrab">S2360 - Condição Diferenciada de Trabalho - Início ok </input></p>
                                <p><input type="checkbox" name="evento[]" value="s2365" class = "buscaTrab">S2365 - Condição Diferenciada de Trabalho - Término ok</input></p>
                                <p><input type="checkbox" name="evento[]" value="s2400" class = "buscaTrab avisoPrevio">S2400 - Aviso Prévio ok</input></p>
                                <p><input type="checkbox" name="evento[]" value="s2405" class = "buscaTrab avisoPrevio">S2405 - Cancelamento de Aviso Prévio ok</input></p>
                                <p><input type="checkbox" name="evento[]" value="s2600" class = "buscaTrab autonomo">S2600 - Trabalhado Sem Vínculo de Emprego - Início ok</input></p>
                                <p><input type="checkbox" name="evento[]" value="s2620" class = "buscaTrab autonomo">S2620 - Trabalhado Sem Vínculo de Emprego - Alt. Contratual ok</input></p>
                                <p><input type="checkbox" name="evento[]" value="s2680" class = "buscaTrab autonomo">S2680 - Trabalhado Sem Vínculo de Emprego - Término</input></p>
                                <p><input type="checkbox" name="evento[]" value="s2800" class = "buscaTrab">S2800 - Desligamento ok</input></p>
                                <p><input type="checkbox" name="evento[]" value="s2820" class = "buscaTrab">S2820 - Reintegração</input></p>
                            </div>
                            <div id="item4">
                                <p><input type="checkbox" name="evento[]" value="s1100">S1100 - Abertura ok</input></p>
                                <p><input type="checkbox" name="evento[]" value="s1200">S1200 - Remuneração do Trabalhador ok</input></p>
                                <p><input type="checkbox" name="evento[]" value="s1300">S1300 - Pagamentos Diversos</input></p>
                                <p><input type="checkbox" name="evento[]" value="s1330">S1330 - Serv. Tomados de Coop. de Trabalho</input></p>
                                <p><input type="checkbox" name="evento[]" value="s1340">S1340 - Serv. Prestados pela Coop. de Trabalho</input></p>
                                <p><input type="checkbox" name="evento[]" value="s1370">S1370 - Rec. Recebidos ou Repassados p/ Clube de Futebol</input></p>
                            </div>
                            <div id="item5">
                                <p>
                                    <input type="checkbox" name="evento[]" value="s2900">S2900 - Exclusão de Eventos</input>
                                    <input type="text" name="evt"/>
                                    <p><label class="first">Número do Recibo:</label><input type="text" name="nrRecibo" id="nrRecibo"/></p>
                                </p>
                            </div>    
                            <div id="controle">
                                <input type="hidden" name="idMaster" id="idMaster" value="<?= $usuario['id_master'] ?>" />
                                <input type="hidden" name="idUsuario" value="<?= $usuario['id_funcionario'] ?>" />
                                <p class="controls">
                                    <input type="button"  name="validarCad" id="validarCad" value="Validar Cadastro"/>
                                    <input type="submit"  name="gerar_arquivo" id="gerar_arquivo" value="Gerar arquivo"/>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </body>             
</html>