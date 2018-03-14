<?php


$programadores = array(179,158,260);

include('../../conn.php');
if($_REQUEST['recisao_coletiva'] != 1){
    if (empty($_COOKIE['logado'])) {
        print "<script>location.href = '../login.php?entre=true';</script>";
        exit;
    }
}

include('../../classes/funcionario.php');
include('../../classes/curso.php');
include('../../classes/clt.php');
include('../../classes/projeto.php');
include('../../classes/calculos.php');
include('../../classes/global.php');

include('../../classes/abreviacao.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../classes/valor_proporcional.php');

include('../../classes_permissoes/acoes.class.php');
include('../../funcoes.php');
include('../../wfunction.php');
include('../../classes/RescisaoClass.php');
include('../../classes/CalculoFolhaClass.php');
include('../../classes/CalculoRescisaoClass.php');
include('../../classes/MovimentoClass.php');
include('../../classes/CltClass.php');

include('../../classes/EventoClass.php');


$usuario = carregaUsuario();
$rescisao = new Rescisao();
$objCalcFolha = new Calculo_Folha();
$objCalcRescisao= new Calculo_Rescisao();
$dadosClt = new CltClass();

function verificaRecisao($id_clt) {
    /*
     * Verifica se já foi realizada rescisão para o funcionário
     */
    $retorno = montaQuery('rh_recisao', 'id_clt,nome', "id_clt = '{$id_clt}' AND status = 1");
    $clt_status = montaQuery('rh_clt', 'status', "id_clt='{$id_clt}'");
    $clt_status = $clt_status[1]['status'];
    if (isset($retorno[1]['id_clt']) && !empty($retorno[1]['id_clt']) && isset($clt_status) && !empty($clt_status)) {
        ?>
        <script type="text/javascript">
            alert('A rescisão deste funcionário já foi realizada.\nNome: ' + '<?php echo $retorno[1]['nome'] ?>');
            window.history.back();
        </script>
        <?php
        exit();
    }
}


$Fun = new funcionario();
$Fun->MostraUser(0);
$user = $Fun->id_funcionario;
$ACOES = new Acoes();
$regiao = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$Calc = new calculos();
$Curso = new tabcurso();
$Clt = new clt();
$ClasPro = new projeto();
$obj_recisao = new Rescisao();

$eventos = new Eventos();

$optTiposDispensa = $rescisao->listTiposRescisao("array");


if (empty($_REQUEST['tela'])) {
    $tela = 1;
} else {
    $tela = $_REQUEST['tela'];
}

if(isset($_POST['desprocFerias'])){
    $id_ferias = $_POST['id'];
    
    $sql = "UPDATE rh_ferias SET status = 0, desprocessado_recisao = 1, dt_desproc_rescisao = NOW(), id_funcionario_desproc_rescisao = '{$_COOKIE['logado']}' WHERE id_ferias = $id_ferias LIMIT 1;";
    if(mysql_query($sql)){
        echo true;
    }
    exit;
}


if (isset($_REQUEST['method']) && !empty($_REQUEST['method'])) {
   
    if ($_REQUEST['method'] == "desprocessar_recisao") {
        $retorno = array("status" => false);
        $dados = $obj_recisao->verificaSaidaPagaDeRecisao($_REQUEST['id_rescisao'], $_REQUEST['id_regiao'], $_REQUEST['id_clt'], $_REQUEST['tpCanAvisoPr'], $_REQUEST['obs']);
        return $dados;
     }
}

if($_GET['voltar_aguardando'] == true){
    
    $rsclt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '".$_GET['id_clt']."'");
    $rowClt = mysql_fetch_assoc($rsclt);
    
    //dados para gravar log
    $local = "Desprocessar Aguardando Demissão";
    $ip = $_SERVER['REMOTE_ADDR'];
    $acao = "{$usuario['nome']} desprocessou o clt {$_GET['id_clt']}";
    $id_usuario = $usuario['id_funcionario'];
    $tipo_usuario = $usuario['tipo_usuario'];
    $grupo_usuario = $usuario['grupo_usuario'];
    $regiao_usuario = $usuario['id_regiao'];
    
    $rsEvent = mysql_query("SELECT id_evento FROM rh_eventos WHERE id_clt = '".$_GET['id_clt']."' AND cod_status = '991' AND status = 1"); //SELECIONANDO O EVENTO DE AGUARDANDO DEMISSÃO
    $arrEventos = array();
    while($row = mysql_fetch_assoc($rsEvent)){
        $arrEventos[] = $row['id_evento'];
    }
    
    $sql1 = "UPDATE rh_clt SET status = '10', data_saida = '', data_aviso = '', data_demi = '', status_demi = '' WHERE id_clt = '" . $_GET['id_clt'] . "' LIMIT 1";
    $sql2 = "UPDATE rh_eventos SET status = '0' WHERE id_evento IN (".implode(",",$arrEventos).")";            
    $sql3 = "INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) VALUES ('{$id_usuario}', '{$regiao_usuario}', '{$tipo_usuario}', '{$grupo_usuario}', '{$local}', NOW(), '{$ip}', '{$acao}')";
    
    mysql_query($sql1);
    mysql_query($sql2);
    mysql_query($sql3);
    
    header("Location: recisao2.php?regiao={$_GET['regiao']}");
}

$sqlBanco = mysql_query("SELECT * FROM bancos WHERE id_regiao = {$_GET['regiao']} ORDER BY id_banco");
while($rowBanco = mysql_fetch_array($sqlBanco)){
    $optionBanco .= "<option value='{$rowBanco['id_banco']}'>{$rowBanco['razao']}({$rowBanco['nome']})</option>";
}

require_once('../../classes/ArquivoTxtBancoClass.php');
$ArquivoTxtBancoClass = new ArquivoTxtBancoClass();
$arrayArquivos = $ArquivoTxtBancoClass->getRegistros('r');
if(isset($_REQUEST['arqRescisao']) AND !empty($_REQUEST['arqRescisao'])){
    $ArquivoTxtBancoClass->gerarTxtBanco('RESCISAO',$_REQUEST['banco'], $_REQUEST['data'], $_REQUEST['arqRescisao']);
    header("Location: arquivo_banco_rescisao.php");
}

// verifica se há session iniciada
//if(isset($_SESSION['projeto'])){
//    $projetoR = $_SESSION['projeto'];
//    session_destroy();
//}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Intranet :: Rescis&atilde;o</title>
        <link href="../../favicon.ico" rel="shortcut icon" />
        <link href="../../net1.css" rel="stylesheet" type="text/css">

        <link href="../../js/highslide.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
        <link href="../../js/highslide.css" rel="stylesheet" type="text/css" />
        <!--<link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css"/>-->
        <script type="text/javascript" src="../../js/jquery-1.8.3.min.js"></script>
        <script type="text/javascript" src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script type="text/javascript" src="../../js/ramon.js"></script>
        <script type="text/javascript" src="../../js/global.js"></script>
        <script type="text/javascript" src="../../js/jquery.ui.core.js"></script>
        <script type="text/javascript" src="../../js/jquery.ui.datepicker.js"></script>
        <script src="../../js/highslide-with-html.js" type="text/javascript"></script>
        <!--<script type="text/javascript" src="../../js/jquery.validationEngine-2.6.js"></script>-->
        <!--<script type="text/javascript" src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>-->
        <script type="text/javascript">
            hs.graphicsDir = '../../images-box/graphics/';
            hs.outlineType = 'rounded-white';

            $(function() {
//                $("#form1").validationEngine();
                    
                $('#dispensa').change(function() {

                    var dispensa = parseInt($(this).val());
//                    console.log(dispensa);
                    
                    switch (dispensa) {
                        case 64:
                        case 66:
                        case 61:
                            $('#fator').val('empregador').css('background-color', '#eeeeee');
                            break;

                        case 63:
                        case 65:
                            $('#fator').val('empregado').css('background-color', '#eeeeee');
                            break;

                        default:
                            $('#fator').val('empregador').css('background-color', '#eeeeee');

                    }

                    if (dispensa == 61 || dispensa == 62 || dispensa == 65) {

                        $('#aviso').val('indenizado').css('background-color', '#ffffff').attr('disabled', false);
                        $('#previo').css('background-color', '#ffffff').attr('disabled', false);
                        $('#data_aviso').css('background-color', '#ffffff').attr('disabled', false);

                    } else {

                        $('#aviso').val('').css('background-color', '#eeeeee').attr('disabled', true);
                        $('#previo').css('background-color', '#eeeeee').attr('disabled', true);
                        $('#data_aviso').css('background-color', '#eeeeee').attr('disabled', true);

                    }
                    
//                    if(dispensa !== 61){
//                        $('#tpAvisoPre').attr('disabled', true);
//                        $('#obs').attr('disabled', true);
//                        $('#tpAvisoPre').val('');
//                        $('#obs').val('');
//                    }

                });


                $('#dispensa').change();

                $('#data_aviso').datepicker({
                    changeMonth: true,
                    changeYear: true

                });
                
                
                $('#desprocessaFerias').click(function(){
                   var id_ferias = $(this).data('key');
                   
                   if(confirm('Tem certeza que quer desprocessar as férias?')){                       
                       $.post('recisao2.php',{tela: 2, desprocFerias:1, id:id_ferias},  function(data){
                           if(data == 1){
                                alert('As férias foi desprocessada.');
                                $('.linha_ferias').fadeOut('slow');
                            }
                           
                       },'html');
                       
                   }
                   
                    return false;
                })
                
                
                

                $('#gerar').click(function() {

                    var regiao = $('#regiao').val();
                    var data_escolhida = $('#data_aviso').val();

                    $.ajax({
                        url: 'action.verifica_folha.php?data=' + data_escolhida + '&regiao=' + regiao,
                        type: 'GET',
                        dataType: 'json',
                        success: function(resposta) {

                            if (parseInt(resposta.verifica) == 0) {

                                alert('A data escolhida ultrapassou o prazo de 30 dias após a última folha finalizada \n\n Data da última folha: ' + resposta.data_ult_folha + '.');
                                $('#data_aviso').val('');

                                return false;
                            } else {

                                $('.form').submit();
                            }
                        }
                    });

                });


                $(".remove_recisao").click(function() {
                    $("#CancelAviso").show();
                    thickBoxModal("Desprocessar Recisão", "#CancelAviso", 350, 400);
                    $("#idCanRescisao").val($(this).attr("data-recisao"));
                    $("#idCanRegiao").val($(this).attr("data-regiao"));
                    $("#idCanClt").val($(this).attr("data-clt"));
                    
                });
                
                $(".btn").click(function (){
                        if ($(this).val() == 'Sim') {
                            var id_rescisao = $("#idCanRescisao").val();
                            var id_regiao = $("#idCanRegiao").val();
                            var id_clt = $("#idCanClt").val();
                            var tpCanAvisoPr = $("#tpCancelAvisoPre").val();
                            var obs = $("#obsCancel").val();
                            $.ajax({
                                url: "recisao2.php",
                                type: "POST",
                                dataType: "json",
                                data: {
                                    tpCanAvisoPr: tpCanAvisoPr,
                                    obs: obs,
                                    id_rescisao: id_rescisao,
                                    id_regiao: id_regiao,
                                    id_clt: id_clt,
                                    method: "desprocessar_recisao"
                                },
                                success: function(data) {
                                    if(!data.status){
                                        $(data.dados).each(function(k, v) {
                                            $(".data_demissao").html(v.data_demissao);
                                            $(".data_pagamento").html(v.data_pg);
                                            $(".nome").html(v.nome_clt);
                                            $(".status").html(v.status_saida);
                                            $(".valor").html(v.valor);
                                        });
                                        $("#mensagens").show();
                                        thickBoxModal("Desprocessar Recisão", "#mensagens", "350", "450");
                                    }else{
                                        history.go(0);
                                    }
                                }
                            });
                        }
                        
                        //history.go(0);
                    });
                    
                    

                    // AMANDA

//                    $('#aviso').change(function() {
//                        var motivo = parseInt($('#dispensa').val());
//                        if ($(this).val() === 'trabalhado' && motivo === 61) {
//                            $('#tpAvisoPre').attr('disabled', false);
//                            $('#obs').attr('disabled', false);
//                        } else {
//                            $('#tpAvisoPre').val('');
//                            $('#obs').val('');
//                            $('#tpAvisoPre').attr('disabled', true);
//                            $('#obs').attr('disabled', true);
//                        }
//                    });
                        
                    });
                
        </script>
        <style>
            body {
                background-color:#FAFAFA; text-align:center; margin:0px;
            }
            /*                p {
                                margin:0px;
                            }*/
            #corpo {
                width:90%; background-color:#FFF; margin:0px auto; text-align:left; padding-top:20px; padding-bottom:10px;
            }

            .gerar_rel{
                background-color: #E8E8E8;
                display: block;
                margin: 0;
                text-decoration: none;
                font-size: 14px;
                font-weight: 200;
                text-align: center;
                color: #000;
                padding: 2px;
                border: 1px solid #E6E6E6;
                width: 48%;
                float: left;
            }

            .gerar_rel2{
                background-color: #E8E8E8;
                display: block;
                margin: 0;
                text-decoration: none;
                font-size: 14px;
                font-weight: 200;
                text-align: center;
                color: #000;
                padding: 2px;
                border: 1px solid #E6E6E6;
                width: 47%;
                float: right;
            }

            .gerar_rel:hover{
                background-color:   #999;
            }

            .gerar_rel2:hover{
                background-color:   #999;
            }

            #movimentos{
                border-collapse: collapse;
            }       

            #movimentos tr{ border: 1px  #E8E8E8 solid;}        
            #movimentos td{ border: 1px #E8E8E8  solid;}        
            #movimentos thead{ font-weight: bold; text-align: center;}     

            /*form com filtro da consulta*/
            form.filtro{
                margin: 10px auto;
                width: 95%;
            }
            #mensagens{
                display: none;
            }
            #mensagens h3{
                text-align: center;
                text-transform: uppercase;
                font-size: 12px;
                color: red;
                text-align: left;
            }
            #mensagens p{
                font-size: 12px;
                color: #333;
                margin: 0px;
                padding: 0px;
                text-align: left;
            }
            

        </style>
    </head>
    <body class='novaintra'>
        <div id="corpo">
            <div id="mensagens">
                <h3>Erro ao Desprocessar</h3>
                <p>Não é possível desprocessar essa rescisão, pois existe uma saida paga para a mesma.</p>
                <br />
                <p>Data demissão: <span class="data_demissao"></span></p>
                <p>Data pagamento: <span class="data_pagamento"></span></p>
                <p>Nome: <span class="nome"></span></p>
                <p>Status: <span class="status"></span></p>
                <p>Valor: <span class="valor"></span></p>
            </div>
            <div id="CancelAviso" style="display: none;">
                <p>
                    <input type="hidden" id="idCanRescisao"/>
                    <input type="hidden" id="idCanRegiao"/>
                    <input type="hidden" id="idCanClt"/>
                </p>
                <p>Motivo do Cancelamento do Aviso Previo:</p>
                <p><select id="tpCancelAvisoPre" name="tpCancelAvisoPre" class="validate[required]">
                        <option value="">Selecione...</option>
                    <?php
                         $qr_canAvisoPre = mysql_query("SELECT id_tipoCanAvisoPre, descricao FROM tipo_cancelamento_aviso_previo;");
                         while ($rowAvisoPre = mysql_fetch_assoc($qr_canAvisoPre)) {
                    ?>
                            <option value="<?= $rowAvisoPre['id_tipoCanAvisoPre'] ?>"><?= $rowAvisoPre['descricao'] ?></option>
                    <?php } ?>
                    </select>
                </p>
                <p>Observação:</p>
                <p><textarea id="obsCancel" name="obsCancel" cols="30" rows="5"></textarea></p>
                <p class="controls">
                    <input type="button"  class="btn" value="Sim"/>
                </p>
            </div>    
            
            <?php
            switch ($tela) {
                case 1:
                    // tela de pesquisa
                    // criar filtro para pesquisa
                    if (isset($_REQUEST['filtro']) && !empty($_REQUEST['filtro'])) {
                        if ($_REQUEST['projeto'] != '-1') {
                            $filtroProjeto = "AND id_projeto = {$_REQUEST['projeto']}";
                        }
                        $projetoR = $_REQUEST['projeto'];
                    } else {
                        $filtroProjeto = '';
                    }
                    ?>
                    <div style="float:right; margin-right:20px;">
                        <?php include('../../reportar_erro.php'); ?>      
                    </div>

                    <div style="clear:right;"></div>

                    <div id="topo" style="width:95%; margin:0px auto;">
                        <div style="float:left; width:25%;">
                            <a href="../../principalrh.php?regiao=<?= $regiao ?>">
                                <img src="../../imagens/voltar.gif">
                            </a>
                        </div>
                        <div style="float:left; width:50%; text-align:center; font-family:Arial; font-size:24px; font-weight:bold; color:#000;">
                            RESCIS&Atilde;O
                        </div>
                        <div style="float:right; width:25%; text-align:right; font-family:Arial; font-size:12px; color:#333;">
                            <br><b>Data:</b> <?= date('d/m/Y') ?>&nbsp;
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    <?php
// Encriptografando a variável
                    $link = str_replace('+', '--', encrypt("$regiao"));
                    ?>

                    <table width="95%" align="center" border="0" cellpadding="8" cellspacing="0" style="margin-top:20px;">
                        <tr bgcolor="#999999">
                            <td colspan="4" class="show">
                                <span style="color:#F90; font-size:32px;">&#8250;</span> Relatório das rescisões
                            </td>
                            <td class="show">
                                <a href="../../relatorios/provisao_de_gastos.php?regiao=<?php echo $regiao; ?>" class="gerar_rel">Relatório de Rescisão em Lote</a>
                                <a href="recisao_mes.php?regiao=<?php echo $regiao; ?>" class="gerar_rel2"> Relatório por Mês</a>
                            </td>
                        </tr>
                    </table>

                    <form action="" method="post" class="filtro">
                        <fieldset>
                            <legend>Filtro</legend>
                            <input type="hidden" name="filtro" value="1" />
                            <p><label class="first">Projeto:</label> <?php echo montaSelect(GlobalClass::carregaProjetosByRegiao($regiao, array("-1" => "Todos")), $projetoR, "id='projeto' name='projeto' class='required[custom[select]]'") ?></p>
                            <p><label class="first"></label><input type="text" name="pesquisa" placeholder="Nome, Matricula, CPF" value="<?php echo $_REQUEST['pesquisa']; ?>"></p>
                            <p class="controls"><input type="submit" value="Consultar" class="button" name="consultar" /></p>
                        </fieldset>
                    </form>



                    <?php
                    if (isset($_REQUEST['filtro']) && !empty($_REQUEST['filtro'])) {
                        
                        if(!empty($_REQUEST['pesquisa'])){
                            $valorPesquisa = explode(' ',$_REQUEST['pesquisa']);
                            foreach ($valorPesquisa as $valuePesquisa) {
                                $pesquisa[] .= "nome LIKE '%".$valuePesquisa."%'";
                            }
                            $pesquisa = implode(' AND ',$pesquisa);
                            $auxPesquisa = " AND (($pesquisa) OR (CAST(matricula AS CHAR) = '{$_REQUEST['pesquisa']}') OR (REPLACE(REPLACE(cpf, '.', ''), '-', '') = '{$_REQUEST['pesquisa']}' OR cpf = '{$_REQUEST['pesquisa']}'))";
                        }
                        
                        // Consulta de Clts Aguardando Demissão
                        $qr_aguardo = mysql_query("SELECT * FROM rh_clt WHERE status = '200' AND id_regiao = '$regiao' $filtroProjeto $auxPesquisa ORDER BY nome ASC");
                        $total_aguardo = mysql_num_rows($qr_aguardo);

                        if (!empty($total_aguardo)) {
                            ?>

                            <table width="95%" align="center" border="0" cellpadding="8" cellspacing="0" style="margin-top:20px;">
                                <tr bgcolor="#999999">
                                    <td colspan="6" class="show">
                                        <span style="color:#F90; font-size:32px;">&#8250;</span> Participantes aguardando a Rescis&atilde;o
                                    </td>
                                </tr>
                                <tr class="novo_tr">
                                    <td width="6%">COD</td>
                                    <td width="35%">NOME</td>
                                    <td width="20%">PROJETO</td>
                                    <td width="20%">UNIDADE</td>
                                    <td width="19%">CARGO</td>
				    <td>AÇÃO</td>	
                                </tr>

                                <?php
                                while ($row_aguardo = mysql_fetch_array($qr_aguardo)) {

                                    $Curso->MostraCurso($row_aguardo['id_curso']);
                                    $NomeCurso = $Curso->nome;

                                    $ClasPro->MostraProjeto($row_aguardo['id_projeto']);
                                    $NomeProjeto = $ClasPro->nome;

                                    // Encriptografando a variável
                                    $link = str_replace('+', '--', encrypt("$regiao&$row_aguardo[0]"));
                                    ?>

                                    <tr style="background-color:<?php
                                    if ($cor++ % 2 != 0) {
                                        echo '#F0F0F0';
                                    } else {
                                        echo '#FDFDFD';
                                    }
                                    ?>">
                                        <td><?= $row_aguardo['campo3'] ?></td>
                                        <td><a href="recisao2.php?tela=2&enc=<?= $link ?>"><?= $row_aguardo['nome'] ?></a></td>
                                        <td><?= $NomeProjeto ?></td>
                                        <td><?= $row_aguardo['locacao'] ?></td>
                                        <td><?= $NomeCurso ?></td>
                                        <td>
                                            <?php if ($ACOES->verifica_permissoes(82)) { ?>
                                                <a href="recisao2.php?voltar_aguardando=true&id=<?php echo $row_aguardo[0]; ?>&regiao=<?php echo $_GET['regiao']; ?>&id_clt=<?php echo $row_aguardo[0]; ?>" title="Desprocessar Aguardando Demissão" onclick="return window.confirm('Você tem certeza que quer desprocessar aguardando demissão?');"><img src="../imagensrh/deletar.gif" /></a>
                                            <?php } ?>
                                        </td>
                                    </tr>

                                <?php } ?>

                            </table>

                        <?php } ?>
                        <form action='' method="post">
                        <table width="95%" align="center" border="0" cellpadding="8" cellspacing="0" style="margin-top:20px;">
                            <tr bgcolor="#999999">
                                <td colspan="10" class="show">
                                    <span class="seta" style="color:#F90; font-size:32px;">&#8250;</span> Participantes Desativados
                                </td>
                            </tr>
                            <tr class="novo_tr">
                                <td></td>
                                <td width="6%">COD</td>
                                <td width="32%">NOME</td>
                                <td width="22%">PROJETO</td>
                                <td width="20%" align="center">DATA</td>
                                <td width="6%" align="center">RESCIS&Atilde;O</td>
                                <td width="7%" align="center">COMPLEMENTAR</td>
                                <td width="7%" align="center">ADD</td>
                                <td>VALOR</td>
                                <td>&nbsp;</td>
                            </tr>

                            <?php
                            // Consulta de Clts que foram demitidos
                            $sql_demissao = "SELECT *, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM rh_clt WHERE status IN ('60','61','62','63','64','65','66','80','81','101') AND id_regiao = '$regiao' $filtroProjeto $auxPesquisa ORDER BY nome ASC";
//                            echo $sql_demissao."<br>";
                            $qr_demissao = mysql_query($sql_demissao);

                            while ($row_demissao = mysql_fetch_array($qr_demissao)) {

                                $Curso->MostraCurso($row_demissao['id_curso']);
                                $NomeCurso = $Curso->nome;

                                $ClasPro->MostraProjeto($row_demissao['id_projeto']);
                                $NomeProjeto = $ClasPro->nome;

                                $qr_rescisao = mysql_query("SELECT *, date_format(data_demi, '%d/%m/%Y') AS data_demi2 FROM rh_recisao WHERE id_clt = '$row_demissao[0]' AND rescisao_complementar != 1   AND status = '1'");
                                $row_rescisao = mysql_fetch_array($qr_rescisao);
                                $total_rescisao = mysql_num_rows($qr_rescisao);

                                $sql_rescisao_complementar = "SELECT * FROM rh_recisao  WHERE vinculo_id_rescisao = '$row_rescisao[0]' AND rescisao_complementar = 1  AND status = 1";
//                                echo $sql_rescisao_complementar;
                                
                                
                                
                                $qr_rescisao_complementar = mysql_query($sql_rescisao_complementar);
                                $total_rescisao_complementar = mysql_num_rows($qr_rescisao_complementar);
                                $arr_complementar = array();
                                while($row_rescisao_complementar = mysql_fetch_array($qr_rescisao_complementar)){
                                    $arr_complementar[] = $row_rescisao_complementar;
                                }
                                
                              

                                $link = str_replace('+', '--', encrypt("$regiao&$row_demissao[0]&$row_rescisao[0]"));

                                if (substr($row_rescisao['data_proc'], 0, 10) >= '2013-04-04') {
                                    $link_nova_rescisao = "nova_rescisao_2.php?enc=$link";
                                } else {
                                    $link_nova_rescisao = "nova_rescisao.php?enc=$link";
                                }
                                ?>

                                <tr style="background-color:<?php
                                if ($cor++ % 2 != 0) {
                                    echo '#F0F0F0';
                                } else {
                                    echo '#FDFDFD';
                                }
                                ?>">
                                    <td>
                                    <?php if($row_demissao['conta'] == '' OR $row_demissao['conta'] == '000000' OR $row_demissao['tipo_conta'] == ''){ ?>
                                        SEM CONTA
                                    <?php }else if($row_rescisao['total_liquido'] == 0.00){ ?>
                                        VALOR ZERADO
                                    <?php }else if(!array_key_exists($row_rescisao['id_recisao'],$arrayArquivos)){ ?>
                                        <input type='checkbox' name="arqRescisao[]" checked value="<?php echo $row_rescisao['id_recisao']; ?>" />
                                    <?php } ?>
                                    </td>
                                    <td><?= $row_demissao['campo3'] ?></td>
                                    <td><?= $row_demissao['nome'] ?></td>
                                    <td><?= $NomeProjeto ?></td>
                                    <td align="center"><?= $row_rescisao['data_demi2'] ?></td>
                                    <td align="center">


                                        <?php 
                                        if (empty($total_rescisao)) { ?>
                                            <img src="../../imagens/pdf.gif" border="0" style="opacity:0.2; filter:alpha(opacity=20)">
                                            <?php } else { ?>
                                                <a href="<?= $link_nova_rescisao; ?>" class="link" target="_blank" title="Visualizar Rescisão"><img src="../../imagens/pdf.gif" border="0"></a>
                                            <?php } ?>
                                    </td>
                                   
                                    <td align="center">
                                     <?php if (empty($total_rescisao_complementar)) { ?>
                                            <img src="../../imagens/pdf.gif" border="0" style="opacity:0.2; filter:alpha(opacity=20)" />
                                            
                                            <?php } else {
                                            
                                                foreach($arr_complementar as $row_rescisao_complementar){
                                                    $link_2                 =  str_replace('+', '--', encrypt("$regiao&$row_demissao[0]&$row_rescisao_complementar[0]"));
                                                    $link_resc_complementar =  "nova_rescisao_2.php?enc=$link_2";
                                                ?>
                                            
                                                <a href="<?= $link_resc_complementar; ?>" class="link" target="_blank" title="Visualizar Rescisão Complementar"><img src="../../imagens/pdf.gif" border="0"></a>
                                            <?php } } ?>
                                    </td>
                                    <td align="center">
                                        <a href="form_rescisao_complementar.php?id_clt=<?= $row_demissao['id_clt']; ?>&id_rescisao=<?= $row_rescisao['id_recisao']; ?>" title="Adicionar Complementar"><img alt="Adionar Complementar" src="../../imagens/icones/icon-add.png" border="0" /></a>
                                    </td>
                                   
                                    <td>R$ <?php
                                        $total_recisao = $row_rescisao['total_liquido'];
                                        echo number_format($total_recisao, 2, ',', '.');
                                        $totalizador_recisao += $total_recisao;
                                        ?>
                                    </td>
                                    <td align="center">
                                        <?php if ($ACOES->verifica_permissoes(82)){ ?> 
                                            <!--<a href="recisao2.php?deletar=true&id=<?php echo $row_rescisao[0]; ?>&regiao=<?php echo $_GET['regiao']; ?>&id_clt=<?php echo $row_demissao[0]; ?>" title="Desprocessar Rescisão" onclick="return window.confirm('Você tem certeza que quer desprocessar esta rescisão?');"><img src="../imagensrh/deletar.gif" /></a>-->
                                            <a href="javascript:;" title="Desprocessar Rescisão" data-recisao="<?php echo $row_rescisao[0]; ?>" data-regiao="<?php echo $_GET['regiao']; ?>" data-clt="<?php echo $row_demissao[0]; ?>" class="remove_recisao"><img src="../imagensrh/deletar.gif" /></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td align="right">TOTAL : </td>
                                <td>R$<?php echo number_format($totalizador_recisao, 2, ',', '.'); ?></td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="10">
                                    Banco: <select name="banco"><?php echo $optionBanco; ?></select>
                                    Data: <input type="text" name="data" >

                                    <input type="submit" value="Gerar Arquivo de Banco">&nbsp;&nbsp;&nbsp;&nbsp;<a href="arquivo_banco_rescisao.php" target="_blank">Gerenciar Arquivos</a>
                                </td>
                            </tr>
                        </table>
                        </form>
                        <form name="acao_recisao" id="acao_recisao" action="recisao2.php">
                            <input type="hidden" name="id_recisao" id="id_recisao" value="" />     
                            <input type="hidden" name="id_regiao" id="id_regiao" value="" />     
                            <input type="hidden" name="id_clt" id="id_clt" value="" />  
                            
                        </form>

                        <?php
                    }
                    break;
                case 2:
                    
                    // tela de rescisao
                    list($regiao, $id_clt) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
                    
                    $dados_contratacao = $dadosClt->getDadosContratacao($id_clt);
                    verificaRecisao($id_clt);
                    
                    
                    $Clt->MostraClt($id_clt);
                    $nome = $Clt->nome;
                    $codigo = $Clt->campo3;
                    $data_demissao = $Clt->data_demi;
                    $contratacao = $Clt->tipo_contratacao;
                    $data_aviso_previo = $Clt->data_aviso;
                    $data_demissaoF = $Fun->ConverteData($data_demissao);
                    
                    // Faltas no Mês
                    list($ano_demissao, $mes_demissao, $dia_demissao) = explode('-', $data_demissao);

                    $qr_faltas = mysql_query("SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$id_clt' AND id_mov = '62' AND (status = '1' OR status = '5') AND mes_mov = '" . $mes_demissao . "' AND ano_mov = '" . $ano_demissao . "'");
                    $faltas = @mysql_result($qr_faltas, 0);


                    if ($dia_demissao > 30) {
                        $dias_trabalhados = 30;
                    } else {
                        $dias_trabalhados = $dia_demissao;
                    }

                     // verifica se há férias agendadas 
                        $query = mysql_query( "SELECT *, DATE_FORMAT(data_ini, '%d/%m/%Y') as data_iniBR, DATE_FORMAT(data_fim, '%d/%m/%Y') as data_fimBR
                                              FROM rh_ferias where data_ini > NOW() AND `status` = '1' AND id_clt = '{$id_clt}' ORDER BY id_ferias DESC");
                        $row_feriasAgendadas = mysql_fetch_assoc($query);
                        $numFeriasAgendadas = mysql_num_rows($query);  
                  
                    
// Calculando Saldo FGTS
                    $qr_liquido = mysql_query("SELECT SUM(salliquido) AS liquido FROM rh_folha_proc WHERE id_clt = '$id_clt' AND status = '3'");
                    $fgts = number_format(mysql_result($qr_liquido, 0) * 0.08, 2, ',', '.');
                    ?>

                    <form action="recisao2.php" name="form1" id="form1" method="post" onsubmit="return validaForm()">
                        <table cellpadding="4" cellspacing="0" style="width:80%; margin:0px auto; border:0; line-height:30px;">
                            <tr>
                                <td colspan="2" class="show" align="center"><?= $id_clt . ' - ' . $nome ?></td>
                            </tr>
                            <tr>
                                <td width="38%" class="secao">Tipo de Dispensa:</td>
                                <td width="62%">
                                    <?php 
                                        // a variável indica se o funcionário pode ou não ser rescindido, deacordo com a regra da licença maternidade
                                        $indResPosMaternidade = $eventos->rescisaoPosMaternidade($id_clt);                                        
                                        if($indResPosMaternidade['indicativo'] == 'N'){
                                            unset($optTiposDispensa);
                                            $opt = array();
                                            $opt['65'] = "65 - Pedido de Dispensa";
                                            $optTiposDispensa = $opt;
                                        }
                                        
                                            echo montaSelect($optTiposDispensa,null,"id='dispensa' name='dispensa'"); ?>
                                        
<!--                                    <select name="dispensa" id="dispensa">
                                        <option value="">Selecione...</option>
                                        <?php
                                        $qr_dispensa = mysql_query("SELECT * FROM rhstatus WHERE tipo = 'recisao' ORDER BY CAST(codigo AS SIGNED)");
                                        while ($row_dispensa = mysql_fetch_array($qr_dispensa)) {
                                            ?>
                                            <option value="<?= $row_dispensa['codigo'] ?>"> <?= $row_dispensa['codigo'] ?> - <?= $row_dispensa['especifica'] ?></option>
                                        <?php } ?>
                                    </select>-->
                                </td>
                            </tr>
                            <tr>
                                <td class="secao">Fator:</td>
                                <td>
                                    <select id="fator" name="fator">
                                        <option value="empregado">empregado</option>
                                        <option value="empregador">empregador</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="secao">Dias de Saldo do Sal&aacute;rio:</td>
                                <td><input name="diastrab" type="text" id="diastrab" value="<?= abs($dias_trabalhados) ?>" size="1" maxlength="2"> dias (data para demissão: <?= $data_demissaoF ?>)</td>
                            </tr>
                            <tr>
                                <td class="secao">Remunera&ccedil;&atilde;o para Fins Rescis&oacute;rios:</td>
                                <td><input name="valor" type="text" id="valor" onkeydown="FormataValor(this, event, 17, 2)" value="0,00" size="6"/></td>
                            </tr>
                            <tr>
                                <td class="secao">Quantidade de Faltas:</td>
                                <td><input name="faltas" type="text" id="faltas" value="<?= $faltas ?>" size="2"/></td>
                            </tr>
                            <tr>
                                <td class="secao" >Aviso pr&eacute;vio:</td>
                                <td><select id="aviso" name="aviso" disabled="disabled">
                                        <option value=""></option>
                                        <option value="indenizado">Indenizado</option>
                                        <option value="trabalhado">Trabalhado</option>
                                    </select>
                                    <input name="previo" type="text" id="previo" size="1" maxlength="2" disabled="disabled"/> 
                                    dias de indeniza&ccedil;&atilde;o ou dias de trabalho
                                </td>
                            </tr>
<!--                            <tr>
                                <td class="secao" >Tipo de aviso pr&eacute;vio:</td>
                                <td><select id="tpAvisoPre" name="tpAvisoPre" disabled="disabled" class="validate[required]" >
                                        <option value="">Selecione...</option>
                                        <?php
//                                        $qr_tpAvisoPre = mysql_query("SELECT id_avisoPre, descricaoAvisoPre FROM tipo_aviso_previo;");
//                                        while ($rowAvisoPre = mysql_fetch_assoc($qr_tpAvisoPre)) {
                                            ?>
                                            <option value="<?= $rowAvisoPre['id_avisoPre'] ?>"><?= $rowAvisoPre['descricaoAvisoPre'] ?></option>
                                        <?php // }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="secao">Observação:</td>
                                <td><textarea id="obs" name="obs" cols="60" rows="5" disabled="disabled"></textarea></td>
                            </tr>-->
                            <tr>
                                <td class="secao">Data do Aviso:</td>
                                <td><input type="text" id="data_aviso" name="data_aviso" size="8" value="<?= formato_brasileiro($data_aviso_previo); ?>"
                                           onkeyup="mascara_data(this);
                                                   pula(10, this.id, devolucao.id)" disabled="disabled"/></td>
                            </tr>
                            <tr>
                                <td class="secao">Devolu&ccedil;&atilde;o de Cr&eacute;dito Indevido:</td>
                                <td><input name="devolucao" id="devolucao" size="6" onkeydown="FormataValor(this, event, 17, 2)" /></td>
                            </tr>
                            <?php
                                if($_REQUEST['recisao_coletiva'] == 0 and $numFeriasAgendadas != 0){
                             ?> 
                            <tr height='90' valign="top" align='center' class='linha_ferias'>
                                 <td colspan="2" style='color: #ce1a1a; font-weight: bold;'>Foi identificado que existe férias agendadas para este CLT no período de gozo de <?php echo $row_feriasAgendadas['data_iniBR'];?>
                                     a <?php echo $row_feriasAgendadas['data_fimBR'];?>. Este pode ter influência no cálculo da rescisão.
                                     <br/> Deseja desprocessar essas férias?
                                     <br><input type='button' id='desprocessaFerias' value='Desprocessar' data-key='<?php echo $row_feriasAgendadas['id_ferias']?>'/>
                                 </td>
                             </tr> 
                            <?php
                               }
                            
                               if(!empty($dados_contratacao)){ ?>
                                <tr>
                                    <td></td>
                                    <td><h3 style="text-align: left">HISTÓRICO DO FUNCIONÁRIO</h3></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                    <?php if($dados_contratacao["contratacao"] != "TIPO NÃO CADASTRADO"){ ?>
                                        <div class="box-periodico">
                                            <p style="margin: 0px; padding: 0px;"><span style="font-weight: bold">Data Entrada:</span> <?php echo $dados_contratacao["data_entrada"];  ?></p>
                                            <p style="margin: 0px; padding: 0px;"><span style="font-weight: bold">Tipo de Contrato:</span> <?php echo $dados_contratacao["contratacao"];  ?></p>
                                            <p style="margin: 0px; padding: 0px;">O primeiro período de experiência termina em <span style="font-weight: bold"> <?php echo $dados_contratacao["data_primeiro"];  ?></span>, podendo se prorrogar até <span style="font-weight: bold"><?php echo $dados_contratacao["data_segundo"];  ?></span></p>
                                        </div>
                                    <?php } ?>
                                    </td>
                                </tr>
                            <?php }  
                            
                            
                            ?>                            
                            <tr>
                                <td colspan="2" align="center">
                                    <table width="50%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td><input type="submit" value="Avançar"  class="botao" /></td>
                                            <td><input type="button" value="Cancelar" class="botao" onclick="javascript:location.href = 'recisao.php?tela=1&regiao=<?= $regiao ?>'"/></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <input type="hidden" name="tela" id="tela" value="3" />
                        <input type="hidden" name="idclt" id="idclt" value="<?= $id_clt ?>" />
                        <input type="hidden" name="regiao" id="regiao" value="<?= $regiao ?>" />
                    </form>

                    <script language="javascript">
                        function validaForm() {
                            d = document.form1;
                            if (d.valor.value == "") {
                                alert("O campo Valor deve ser preenchido!");
                                d.valor.focus();
                                return false;
                            }
                            return true;
                        }
                    </script>

                    <?php
                    break;
                case 3:
                    // tela de contabilização da rescisão
                    
                    $id_clt = $_REQUEST['idclt'];
                    $regiao = $_REQUEST['regiao'];
                    $fator = $_REQUEST['fator'];
                    $dispensa = $_REQUEST['dispensa'];
                    $faltas = $_REQUEST['faltas'];
                    $dias_trabalhados = $_REQUEST['diastrab'];
                    $aviso = $_REQUEST['aviso'];
                    $previo = $_REQUEST['previo'];
                    $valor = $_REQUEST['valor'];
                    $data_aviso = implode('-', array_reverse(explode('/', $_REQUEST['data_aviso'])));
                    $devolucao = str_replace(',', '.', str_replace('.', '', $_REQUEST['devolucao']));
                    
                    if($_REQUEST['recisao_coletiva'] != 1){
                        verificaRecisao($id_clt);
                    }

                    //////DADOS DO CLT
                    $ano_atual = date('Y');
                    if($_REQUEST['recisao_coletiva'] == 1){
                        $data_demi = "'".$_REQUEST['data_demi']."'";
                    }else{
                        $data_demi = "data_demi";
                    }
                    
                    $qr_clt = mysql_query("SELECT A.nome, A.campo3, A.data_demi, A.data_entrada, A.id_projeto, A.id_curso, A.id_regiao, A.id_curso,
                        DATE_FORMAT(data_entrada, '%d/%m/%Y') as data_entradaF, 
                        DATE_FORMAT({$data_demi}, '%d/%m/%Y') as data_demiF, 
                        A.insalubridade, A.desconto_inss, A.tipo_desconto_inss, A.valor_desconto_inss, A.trabalha_outra_empresa, 
                        A.salario_outra_empresa, A.desconto_outra_empresa,                     
                        IF(DATEDIFF({$data_demi}, data_entrada) >= 365, 1, 0) as um_ano, 
                        B.salario, B.nome as nome_funcao,
                        /*Verifica se o clt recebeu DT*/
                        (SELECT Count(a.id_clt) FROM rh_folha_proc a INNER JOIN rh_folha b ON a.id_folha = b.id_folha WHERE a.id_clt = $id_clt AND a.ano = YEAR(A.data_demi) AND a.status = '3' AND b.terceiro = 1) as verifica_dt,
                        ROUND( DATEDIFF({$data_demi}, data_entrada) / 30) as meses_dt,
                        

                        /*CALCULO PARA O ART. 479 E ART. 480 */
                        IF( ({$data_demi} = DATE_ADD(A.data_entrada, INTERVAL + 44 DAY)) OR ({$data_demi} = DATE_ADD(A.data_entrada, INTERVAL + 89 DAY)),0,
                                IF({$data_demi} <= DATE_ADD(A.data_entrada, INTERVAL + 44 DAY),
                                 DATEDIFF (DATE_ADD(A.data_entrada, INTERVAL + 44 DAY),{$data_demi}),
                                 DATEDIFF (DATE_ADD(A.data_entrada, INTERVAL + 89 DAY),{$data_demi}))
                        ) 
                        AS dias_restantes,
                        
                  /*MESES TRABALHADOS*/
                       ( SELECT IF( PERIOD_DIFF(demissao, admissao) >= 12, 12, PERIOD_DIFF(demissao, admissao)) as meses
                              FROM 
                                (SELECT CONCAT(YEAR(data_entrada),SUBSTR(data_entrada,6,2)) as admissao,
                                CONCAT(YEAR({$data_demi}), SUBSTR({$data_demi},6,2) ) as demissao,
                                data_entrada, data_demi
                                FROM rh_clt WHERE id_clt = $id_clt) as folha
                        ) as meses_trabalhados,
                        B.periculosidade_30

                        FROM rh_clt as A 
                        INNER JOIN curso as B
                        ON B.id_curso = A.id_curso
                        WHERE id_clt = '$id_clt' ") or die(mysql_error());
                    
                    
                    if($_REQUEST['recisao_coletiva'] == 1){
//                        echo "SELECT A.nome, A.campo3, A.data_demi, A.data_entrada, A.id_projeto, A.id_curso, A.id_regiao, A.id_curso,
//                        DATE_FORMAT(data_entrada, '%d/%m/%Y') as data_entradaF, 
//                        DATE_FORMAT({$data_demi}, '%d/%m/%Y') as data_demiF, 
//                        A.insalubridade, A.desconto_inss, A.tipo_desconto_inss, A.valor_desconto_inss, A.trabalha_outra_empresa, 
//                        A.salario_outra_empresa, A.desconto_outra_empresa,                     
//                        IF(DATEDIFF({$data_demi}, data_entrada) >= 365, 1, 0) as um_ano, 
//                        B.salario, B.nome as nome_funcao,
//                        /*Verifica se o clt recebeu DT*/
//                        (SELECT Count(a.id_clt) FROM rh_folha_proc a INNER JOIN rh_folha b ON a.id_folha = b.id_folha WHERE a.id_clt = $id_clt AND a.ano = YEAR(A.data_demi) AND a.status = '3' AND b.terceiro = 1) as verifica_dt,
//                        ROUND( DATEDIFF({$data_demi}, data_entrada) / 30) as meses_dt,
//                        
//
//                        /*CALCULO PARA O ART. 479 E ART. 480 */
//                        IF( ({$data_demi} = DATE_ADD(A.data_entrada, INTERVAL + 44 DAY)) OR ({$data_demi} = DATE_ADD(A.data_entrada, INTERVAL + 89 DAY)),0,
//                                IF({$data_demi} <= DATE_ADD(A.data_entrada, INTERVAL + 44 DAY),
//                                 DATEDIFF (DATE_ADD(A.data_entrada, INTERVAL + 44 DAY),{$data_demi}),
//                                 DATEDIFF (DATE_ADD(A.data_entrada, INTERVAL + 89 DAY),{$data_demi}))
//                        ) 
//                        AS dias_restantes,
//                        
//                  /*MESES TRABALHADOS*/
//                       ( SELECT IF( PERIOD_DIFF(demissao, admissao) >= 12, 12, PERIOD_DIFF(demissao, admissao)) as meses
//                              FROM 
//                                (SELECT CONCAT(YEAR(data_entrada),SUBSTR(data_entrada,6,2)) as admissao,
//                                CONCAT(YEAR({$data_demi}), SUBSTR({$data_demi},6,2) ) as demissao,
//                                data_entrada, data_demi
//                                FROM rh_clt WHERE id_clt = $id_clt) as folha
//                        ) as meses_trabalhados,
//                        B.periculosidade_30
//
//                        FROM rh_clt as A 
//                        INNER JOIN curso as B
//                        ON B.id_curso = A.id_curso
//                        WHERE id_clt = '$id_clt' ";
                    }
                    
                    
                    $row_clt = mysql_fetch_assoc($qr_clt);
                    $Curso->MostraCurso($row_clt['id_curso']);
                    $nome = $row_clt['nome'];
                    $codigo = $row_clt['campo3'];
                    $data_demissao = ($_REQUEST['recisao_coletiva'] != 1) ? $row_clt['data_demi'] : $_REQUEST['data_demi'];
                    $data_entrada = $row_clt['data_entrada'];
                    $idprojeto = $row_clt['id_projeto'];
                    $idcurso = $row_clt['id_curso'];
                    $idregiao = $row_clt['id_regiao'];
                    $data_demissaoF = ($_REQUEST['recisao_coletiva'] == 1) ? date("d/m/Y",  str_replace("-","/", strtotime($_REQUEST['data_demi']))) :$row_clt['data_demiF'];
                    $data_entradaF = $row_clt['data_entradaF'];
                    $clt_insalubridade = $row_clt['insalubridade'];
                    $um_ano = ($dispensa == 63 or $dispensa == 64 or $dispensa == 66) ? 2 : $row_clt['um_ano'];
                    
                    $dias_restantes = $row_clt['dias_restantes']; //USADO NO CALCULO DO ART. 479 e 480
                    //
              
                
///////////////////////////////
////////CONFIG////////////////
//////////////////////////////
                    $restatus = mysql_query("SELECT A.especifica, A.codigo_saque, B.* FROM rhstatus as A
                            INNER JOIN rescisao_config as B 
                            ON A.codigo = B.tipo
                            WHERE A.codigo = '$dispensa' AND ano = '$um_ano'");
                    
                    $row_status = mysql_fetch_assoc($restatus);
                   
                    $t_ss = $row_status['saldodesalario']; // SALDO SALARIO
                    $t_ap = $row_status['avisoprevio']; // AVISO PREVIO
                    $t_fv = $row_status['feriasvencidas'];//NÃO TA ENTRANDO AQUI GORDO
                    
                    // FERIAS VENCIDAS
                    $t_fp = $row_status['feriasproporcionais']; // FERIAS PROPORCIONAIS
                    $t_fa = $row_status['adicionaldeferias']; // FERIAS 1/3 ADICIONAL
                    $t_13 = $row_status['13salario']; // DECIMO TERCEIRO
                    $t_familia = $row_status['salariofamilia'];
                    $t_f8 = 0; // FGTS 8
                    $t_f4 = 0; // FGTS MULTA 40
                    $t_479 = $row_status['indenizacao479']; //INDENIZACAO ART. 479
                    $t_480 = $row_status['indenizacao480']; //INDENIZACAO ART. 480

                    $cod_saque_fgts = $row_status['codigo_saque'];

                    switch ($dispensa) {
                        case 60: $cod_mov_fgts = 'H';
                            $cod_saque_fgts = '00';
                            break;

                        case 61: $cod_mov_fgts = '11';
                            $cod_saque_fgts = '01';
                            break;

                        case 62:
                        case 81: $cod_mov_fgts = '11';
                            break;

                        case 63: $cod_mov_fgts = '01';
                            break;

                        case 64:
                            $cod_mov_fgts = '01';
                            $cod_saque_fgts = '04';
                            break;

                        case 65:
                            $cod_mov_fgts = '01';
                            break;

                        case 66: $cod_mov_fgts = '01';
                            break;

                        case 101: $cod_mov_fgts = '01';
                            break;
                    }



// Trabalhando com as Datas
                    $qnt_dias_mes = 30;
                    $data_hoje = date('Y-m-d');
                    $data_exp = explode('-', $data_demissao);
                    $data_adm = explode('-', $data_entrada);

                    $dia_demissao = (int) $data_exp[2];
                    $mes_demissao = (int) $data_exp[1];
                    $ano_demissao = (int) $data_exp[0];

                    $dia_admissao = (int) $data_adm[2];
                    $mes_admissao = (int) $data_adm[1];
                    $ano_admissao = (int) $data_adm[0];

                    $data_demissao_seg = mktime(0, 0, 0, $mes_demissao, $dia_demissao, $ano_demissao);
                    $data_admissao_seg = mktime(0, 0, 0, $mes_admissao, $dia_admissao, $ano_admissao);

                    
                    ///////INSTANCIANDO O OBJETO  DE MOVIMENTOS
                    $objMovimento = new Movimentos();
                    $objMovimento->carregaMovimentos($ano_demissao);
                    
                    
                    
/////////////////////////////////
//// DIAS TRABALHADOS  ///////////
/////////////////////////////////
                    if ($mes_admissao == $mes_demissao and $ano_demissao == $ano_admissao) {

                        $dias_trabalhados = (int) (($data_demissao_seg - $data_admissao_seg) / 86400) + 1;
                        $dias_trabalhados = $dias_trabalhados - $faltas;
                    } else {
                        if ((int) $mes_demissao == 2 and $dia_demissao >= 28) {

                            $dias_trabalhados = 30;
                        } else {

                            $dias_trabalhados = ($dia_demissao == 31) ? 30 : $dia_demissao;
                            $dias_trabalhados = $dias_trabalhados - $faltas;
                        }
                    }



////////////////
/////////////////////
////SALARIO BASE  ///
/////////////////////
                    if ($valor == '0,00') {
                        $salario_base_limpo = $row_clt['salario'];
                    } else {
                        $valor = str_replace(',', '.', str_replace('.', '', $valor));
                        $salario_base_limpo = $valor;
                    }



                    $valor_faltas = ($salario_base_limpo / $qnt_dias_mes) * $faltas;

            //carrega os movimentos para serem usados na classe
            $objCalcFolha->CarregaTabelas($ano_demissao);
            
            
            ///////////////////////
            ///INSALUBRIDADE/////
            //////////////////////
          
           
            if ($clt_insalubridade == 1) {                
                 $insalubridade = $objCalcFolha->getInsalubridade($dias_trabalhados, $Curso->tipo_insalubridade, $Curso->qnt_salminimo_insalu, $ano_demissao);       
                 $valor_insalubridade_integral = $insalubridade['valor_integral'];
                 $valor_insalubridade          = $insalubridade['valor_proporcional'];  
          }

/////////////////////////
/// PERICULOSIDADE /////
////////////////////////
       
          if($row_clt['periculosidade_30'] == 1){  
        
              $calPericulosidade          = $objCalcFolha->getPericulosidade($salario_base_limpo, $dias_trabalhados);
              $periculosidade_30_integral = $calPericulosidade['valor_integral'];              
              $periculosidade_30          = $calPericulosidade['valor_proporcional'];           
         
               
                $objMovimento->setIdRegiao($regiao);
                $objMovimento->setIdProjeto($id_projeto);
                $objMovimento->setIdClt($id_clt);
                $objMovimento->setIdMov(57);
                $objMovimento->setCodMov(6007);
                $objMovimento->setMes(16);
                $objMovimento->setAno(2014);
                $valor_mov = $periculosidade_30;

                $verfica_movimento = $objMovimento->verificaMovimento();
                
                if(empty($verfica_movimento)){
                    $insere = $objMovimento->insereMovimento($valor_mov);   
                }
//                else {
//
//                    if($verfica_movimento['valor_movimento'] != number_format($valor_mov,2,'.','')){
//                        $objMovimento->updateValorPorId($verfica_movimento['id_movimento'], $valor_mov);
//                    }
//
//                }
          }
          
          
                  
                    
                    
                    
                    
                    
                    
                    
                    
/////////////////////
// MOVIMENTOS FIXOS //
///////////////////

                    $qr_folha = mysql_query("select  A.ids_movimentos_estatisticas, B.id_clt,A.mes
                        FROM rh_folha as A
                        INNER JOIN rh_folha_proc as B
                        ON A. id_folha = B.id_folha
                        WHERE B.id_clt   = '$id_clt'  AND B.status = 3 AND A.terceiro = 2 
                        AND A.data_inicio >= DATE_SUB(NOW(), INTERVAL 13 MONTH) ORDER BY A.ano,A.mes");
                    
                    
                    while ($row_folha = mysql_fetch_assoc($qr_folha)) {
                        if (!empty($row_folha[ids_movimentos_estatisticas])) {
                            $qr_movimentos = mysql_query("SELECT *
                                       FROM rh_movimentos_clt
                                       WHERE id_movimento IN($row_folha[ids_movimentos_estatisticas]) AND incidencia = '5020,5021,5023'  AND tipo_movimento = 'CREDITO' AND id_clt = $id_clt AND id_mov NOT IN(56,200,57) ");  ///A PEDIDO DA REJANE, COM BASE NO EMAIL ESTOU REMOVENDO O MOVIMENTO DE DIFERENÇA SALARIAL PARA O CALCULO DAS MÉDIAS #13/11/2014
                            
                            
                            while ($row_mov = mysql_fetch_assoc($qr_movimentos)) {
                                if($_COOKIE['logado'] == 179){
                                    echo "<pre>";
                                        print_r($row_mov);
                                    echo "</pre>";
                                }
                                $movimentos[$row_mov['nome_movimento']] +=$row_mov['valor_movimento'];
                            }
                        }
                    }
                    
                    
                    
                    if($_COOKIE['logado'] == 179){
                        echo "<pre>";
                            print_r("Meses para Médiass: " . $row_clt['meses_trabalhados'] . "<br>");
                        echo "</pre>";
                    }
                  
                    if (sizeof($movimentos) > 0) {
                        $total_rendi = (array_sum($movimentos) / $row_clt['meses_trabalhados']);
                        $total_rendi = number_format($total_rendi, 2,'.','');
                    } else {
                        $total_rendi = 0;
                    }

                 
/////////////////////
// FIM MOVIMENTOS FIXOS /////



                    if ($array_valores_rendimentos == '') {
                        $array_valores_rendimentos[] = '0';
                    }


//////////////////////////////
////SALÁRIO FAMÍLIA ///////
///////////////////////////
                    if ($t_familia == 1) {

                        $Calc->Salariofamilia($salario_base_limpo, $id_clt, $idprojeto, $data_demissao, '2');
                        $valor_sal_familia = (($Calc->valor) / $qnt_dias_mes) * $dias_trabalhados;
                        if ($valor_sal_familia > 0) {
                            $TOTAL_MENOR = $Calc->filhos_menores;
                        }
                    }


///ARTIGO 479 e 480 PARA RESCISÃO ANTECIPADA 
                    $valor_art_480_479 = (($salario_base_limpo) / 30) * ($dias_restantes / 2);

                    $total_dias = ($data_demissao_seg - $data_admissao_seg) / 86400;




                    if ($t_479 == 1) {


                        $art_479 = $valor_art_480_479;
                        $art_480 = NULL;
                        $to_rendimentos += $art_479;
                    } elseif ($t_480 == 1) {


                        $art_479 = NULL;
                        $art_480 = $valor_art_480_479;
                        $to_descontos += $art_480;
                    }
                    
                    if($art_479 < 0){
                        $art_479 = 0;
                    }

                    //////////////////////////////////////
                    //////////////SALDO DE SALÁRIO///////
                    /////////////////////////////////////
                    if ($t_ss == 1) {
                        $valor_salario_dia = $salario_base_limpo / $qnt_dias_mes;
                        $saldo_de_salario = $valor_salario_dia * $dias_trabalhados;
                    }



                    // Décimo Terceiro (DT)
                    ///Verifica se  a pesssoa recebeu décimo terceiro no ano
                    $qr_verifica_13_folha = mysql_query("SELECT a.id_clt,a.salliquido,b.data_fim,tipo_terceiro
                                                FROM rh_folha_proc AS a 
                                                LEFT JOIN rh_folha AS b ON(a.id_folha = b.id_folha) 
                                                WHERE a.id_clt = '{$id_clt}' AND a.ano = " . date('Y') . " AND a.status = '3' AND b.terceiro = 1
                                                AND a.id_clt IS NOT NULL") or die(mysql_error());
                        
                    
                    $verifica_13_folha = mysql_num_rows($qr_verifica_13_folha);
                    $array_parcelas_decimo = array();
                    $valor_decimo_folha = 0;
                    while($row_veri_decimo = mysql_fetch_assoc($qr_verifica_13_folha)){
                        $array_parcelas_decimo[] = $row_veri_decimo['tipo_terceiro'];
                        
                        if ($row_veri_decimo['tipo_terceiro'] == 1) {
                            $valor_decimo_folha = $row_veri_decimo['sal_liquido'];
                        }
                    }
                    
                    if(in_array($_COOKIE['logado'], $programadores)){
                         echo "====================================Participou do Decimo Terceiro======================================== <br>";
                         echo "1 => Primeira Parcela, 2 => Segunda Parcela, 3 => Integral <br>";
                         print_r($array_parcelas_decimo);
                         echo "<br>================================================================================================== <br>";
                    }
                    


                    ///Verifica se  a pesssoa recebeu décimo terceiro em novembro
                    if ((in_array(1, $array_parcelas_decimo) && in_array(2, $array_parcelas_decimo)) || in_array(3, $array_parcelas_decimo)) {
                       
                        $total_dt = 0;
                        $meses_ativo_dt = 0;
                        
                    } else {
                        $primeiro_dia_ano = date('Y'). "-01-01";
                        if($verifica_13_folha == 0 && $data_entrada < $primeiro_dia_ano){
                            $dt_entrada_calc = $primeiro_dia_ano;
                        }else{
                            $dt_entrada_calc = $data_entrada;
                        }
                        //Quantidade de mese
                        
                        $Calc->Calc_qnt_meses_13_ferias_rescisao($dt_entrada_calc, $data_demissao);
                        if($dispensa == 60){
                            $meses_ativo_dt = 0;
                        }else{
                            $meses_ativo_dt = $Calc->meses_ativos_dt;
                        }
                        
                        if($_REQUEST['recisao_coletiva'] == 1){
                            //echo "<br>vasco".$meses_ativo_dt. "<br>";
                        }
                        
                        if ($aviso == 'indenizado') {

                            // Décimo Terceiro Saldo de Salário (Indenizado)
                            $qnt_13_indenizado = 1;
                            $valor_13_indenizado = ($salario_base_limpo + $valor_insalubridade_integral + $total_rendi + $periculosidade_30_integral) / 12;

                            if ($dispensa == 65) {
                                $total_avos_13_indenizado = "0";
                                $total_valor_13_indenizado = NULL;
                                $valor_13_indenizado = 0;
                            } else {
                                $total_avos_13_indenizado = $qnt_13_indenizado;
                                $total_valor_13_indenizado = $valor_13_indenizado;
                            }
                        }
                        
                        
                        /*************************ISSO AQUI É O SEGUINTE : *************************************************************/
                        /********SE O CLT JA RECEBE A 1° PARCELA, 2° PARCELA OU ATE MESMO O 13º INTEGRAL *******************************/
                        /********NÃO PODE DESCONTAR NOVAMENTE NA RESCISÃO **************************************************************/
                        if(in_array($_COOKIE['logado'], $programadores)){
                            echo "====================================Composição da Variável VALOR_TD===================================== <br>";
                            echo "(+) Salario Base: " . formato_real($salario_base_limpo) . "<br>";
                            echo "(+) Insalubridade: " . formato_real($valor_insalubridade_integral) . "<br>";
                            echo "(+) Total de Rendimento: " . formato_real($total_rendi) . "<br>";
                            echo "(+) Perioculosidade: " . formato_real($periculosidade_30_integral) . "<br>";
                            echo "(-) Adiantamento de 13° Salário: " . formato_real($valor_decimo_folha) . "<br>";
                            echo "(=) Total: " . formato_real($valor_decimo_folha) . "<br>";
                            echo "================================================================================================== <br>";
                        }
                        
                        
                        ///ALTERAÇÃO FEITA PARA NÃO PAGAR INSS E IR SOBRE 13° QUANDO O MESMO JA FOI DESCONTADO.
//                                          
//                        $qr_verifica_13 = "SELECT * FROM rh_movimentos_clt WHERE (mes_mov = '16' AND STATUS = '1' AND id_clt = '{$id_clt}') AND id_mov IN(292) ORDER BY nome_movimento";
//                        $query_adiantamento_13 = mysql_query($qr_verifica_13) or die('Erro ao selecionar movimento de adiantamente de 13°');
//                        $movimento_adiantamento_13 = array();
//                        while($rows_mov_adiantamento_13 = mysql_fetch_assoc($query_adiantamento_13)){
//                            $movimento_adiantamento_13 = $rows_mov_adiantamento_13['valor_movimento'];
//                        }
                        //echo $valor_td - $valor_decimo_folha;
                        $dias_trabalhados_mes = date("d", strtotime(str_replace("/", "-", $data_demissao)));
                        $valor_13_se_mais_de_quinze_dias = 0;
                        if($dias_trabalhados_mes >= 15){
                            $valor_13_se_mais_de_quinze_dias = $valor_13_indenizado;
                        }else{
                            $valor_13_se_mais_de_quinze_dias = 0;
                        }
                        
                        $valor_td = (($salario_base_limpo + $valor_insalubridade_integral + $total_rendi + $periculosidade_30_integral) / 12) * $meses_ativo_dt;
                        $BASE_CALC_INSS_13 = $valor_td + $valor_13_se_mais_de_quinze_dias - $valor_decimo_folha;
                        if(in_array($_COOKIE['logado'], $programadores)){
                            echo "====================================Composição da Variável VALOR_TD===================================== <br>";
                            echo "(+) Valor Dt: " . formato_real($valor_td) . "<br>";
                            echo "(+) Valor Decimo Indenizado: " . formato_real($valor_13_indenizado) . "<br>";
                            echo "(-) Valor Decimo Na folha: " . formato_real($valor_decimo_folha) . "<br>";
                            echo "================================================================================================== <br>";
                        }
                        
                        //$BASE_CALC_INSS_13 = 0 ;
                         
                        // Calculando INSS sobre DT
                        $Calc->MostraINSS($BASE_CALC_INSS_13, $data_demissao);
                        $valor_td_inss = $Calc->valor;
                        $PERCENTUAL_INSS_13 = $Calc->percentual;
                        
//                        if($_COOKIE['logado'] == 179){
//                            echo "<pre>";
//                                print_r($Calc);
//                            echo "</pre>";
//                        }
                        
                        // Calculando IRRF sobre DT
                        $BASE_CALC_IRRF_13 = $BASE_CALC_INSS_13 - $valor_td_inss;
                        $Calc->MostraIRRF($BASE_CALC_IRRF_13, $id_clt, $idprojeto, $data_demissao);
                        
                        
                        
                        
                        
                        $valor_td_irrf = $Calc->valor;

                        if ($valor_td_irrf > 0) {
                            $PERCENTUAL_IRRF_13 = $Calc->percentual;
                            $QNT_DEPENDENTES_IRRF_13 = $Calc->total_filhos_menor_21;
                            $VALOR_DDIR_13 = $Calc->valor_deducao_ir_total;
                            $PARCELA_DEDUCAO_IR_13 = $Calc->valor_fixo_ir;
                        } else {
                            $BASE_CALC_IRRF_13 = 0;
                        }
                        
                        $valor_td = number_format($valor_td, 2,'.','');
                        $valor_td_inss = number_format($valor_td_inss, 2,'.','');
                        $valor_13_indenizado = number_format($valor_13_indenizado, 2,'.','');
                        $valor_td_irrf = number_format($valor_td_irrf, 2,'.','');
                            
                        // Valor do DT
                        $total_dt = $valor_td - $valor_td_inss - $valor_td_irrf;
                        $to_descontos = $to_descontos + $valor_td_inss + $valor_td_irrf;
                        $to_rendimentos = $to_rendimentos + $BASE_CALC_INSS_13;
                    }
                    
                        
                    
                    ///COISAS DA REJANE
                    if($movimento_adiantamento_13 == $valor_td){
                        $valor_td_inss = 0;
                        $valor_td_irrf = 0;
                    }
                    
                    
                    // Fim de Décimo Terceiro (DT)
                    // ferias
                    // verifica se há férias agendadas
                    $qr_verifica_ferias = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$id_clt' AND status = 1 ORDER BY id_ferias DESC");
                    $total_verifica_ferias = mysql_num_rows($qr_verifica_ferias);

                    if (empty($total_verifica_ferias)) {

                        $aquisitivo_ini = $data_entrada;
                        $aquisitivo_end = date('Y-m-d', strtotime("" . $data_entrada . " +1 year"));
                    } else {

                        $aquisitivo_ini = date('Y-m-d', strtotime("" . $data_entrada . " + " . $total_verifica_ferias . " year"));
                        $aquisitivo_end = date('Y-m-d', strtotime("" . $data_entrada . " + " . ($total_verifica_ferias + 1) . " year"));
                    }



                    // Verificando Períodos Gozados
                    while ($periodos = mysql_fetch_assoc($qr_verifica_ferias)) {


                        $periodos_gozados[] = "$periodos[data_aquisitivo_ini]/$periodos[data_aquisitivo_fim]";
                     
                    }


                    // Verificando Períodos Aquisitivos, Períodos Vencidos e Período Proporcional
                    $quantidade_anos = (date('Y') - $ano_admissao) + 1;
                   
                    //echo "Sinesio: " . $ano_admissao;
                    for ($a = 0; $a < $quantidade_anos; $a++) {
                        $aquisitivo_inicio_ferias = date('Y-m-d', strtotime("$data_entrada + $a year"));
                        $aquisitivo_final_ferias = date('Y-m-d', mktime('0', '0', '0', $mes_admissao, $dia_admissao - 1, $ano_admissao + $a + 1));
                        break;
                    }
                    
                    
                    for ($a = 0; $a < $quantidade_anos; $a++) {
                        
                        $aquisitivo_inicio = date('Y-m-d', strtotime("$data_entrada + $a year"));
                        $aquisitivo_final = date('Y-m-d', mktime('0', '0', '0', $mes_admissao, $dia_admissao - 1, $ano_admissao + $a + 1));
                        
                        
                        if($_REQUEST['recisao_coletiva'] == 1){
//                            echo "<br>";
//                            print_r($aquisitivo_final) . "<br>"; 
//                            print_r($data_demissao) . "<br>"; 
                        }
                        if ($aquisitivo_final > $data_demissao) {
                            $periodo_aquisitivo = $aquisitivo_inicio . '/' . $data_demissao;
                            $periodos_aquisitivos[] = $aquisitivo_inicio . '/' . $data_demissao;
                        } else {
                            $periodo_aquisitivo = $aquisitivo_inicio . '/' . $aquisitivo_final;
                            $periodos_aquisitivos[] = $aquisitivo_inicio . '/' . $aquisitivo_final;
                        }
                        if($_REQUEST['recisao_coletiva'] == 1){
//                            print_r($periodo_aquisitivo) . "<br>"; 
//                            print_r($periodos_aquisitivos) . "<br>"; 
                        }

                        if (@!in_array($periodo_aquisitivo, $periodos_gozados) and $aquisitivo_final < $data_demissao) {

                            $periodos_vencidos[] = $aquisitivo_inicio . '/' . $aquisitivo_final;
                        } elseif ($aquisitivo_final >= $data_demissao and $aquisitivo_inicio < $data_demissao) {
                            $periodo_proporcional[] = $aquisitivo_inicio . '/' . $data_demissao;
                        }
                        //break;
                    } 
                    
                    //print_r("Periodo Proporcional: " . $periodo_proporcional);
                    
                    if($_REQUEST['recisao_coletiva'] == 1){
//                        echo "<br> - vasco";
//                        print_r($periodos_vencidos); 
                    }
                    
// Buscando Faltas
                    include('faltas_rescisao.php');
// Fim da Verificação de Férias
// Férias Vencidas
                    
                    
                    if ($t_fv == 1) {
                        
                        //CRIADO POR SINESIO - PROBLEMA DE PERIODO EM FERIAS VENCIDAS.
                        $periodos_vencidos = end($periodos_vencidos);
                        $periodos_venc = explode("/", $periodos_vencidos);
                        $periodo_venc_inicio = $periodos_venc[0];
                        $periodo_venc_final = $periodos_venc[1];
                        if(in_array($_COOKIE['logado'], $programadores)){
                            echo "<pre>";
                                echo "=========================================PERIODOS VENCIDOS=============================<br><br>";
                                print_r($periodo_venc_inicio . " * " . $periodo_venc_final);
                            echo "</pre>";
                        }
                        
                        $total_periodos_vencidos = count($periodos_vencidos);
                        
                        if (empty($total_periodos_vencidos)) {
                            
                            $ferias_vencidas = 'não';
                            $fv_valor_base = 0;
                            $fv_um_terco = 0;
                        } elseif ($total_periodos_vencidos == 1) {
                            
//                            if($_COOKIE['logado'] == 179){
//                                echo "<pre>";
//                                    print_r("Periodo Aquisitivo: " . $aquisitivo_inicio . '/' . $aquisitivo_final . "<br />");
//                                    print_r("Salário Base: " . $salario_base_limpo . "<br />");
//                                    print_r("Valor Insalubridade: " . $valor_insalubridade_integral . "<br />");
//                                    print_r("Total Rendimentos: " . $total_rendi . "<br />");
//                                    print_r("Valor Periculosidade: " . $periculosidade_30_integral . "<br />");
//                                echo "</pre>";
//                            }
                            
                            $ferias_vencidas = 'sim';
                            $fv_valor_base = (($salario_base_limpo + $valor_insalubridade_integral + $total_rendi + $periculosidade_30_integral) / $qnt_dias_mes) * $qnt_dias_fv;
                           
                            $fv_um_terco = $fv_valor_base / 3;
                            
                            $fv_valor_base = number_format($fv_valor_base, 2,'.','');
                            $fv_um_terco = number_format($fv_um_terco, 2,'.','');
                            
                            $fv_total = $fv_valor_base + $fv_um_terco;
                        } elseif ($total_periodos_vencidos > 1) {
                            
                            $ferias_vencidas = 'sim';
                            $fv_valor_base = ((($salario_base_limpo - $valor_insalubridade_integral + $total_rendi + $periculosidade_30) / $qnt_dias_mes) * $qnt_dias_fv );
                            $fv_um_terco = $fv_valor_base / 3;
                            
                            $fv_um_terco_dobro = ($fv_valor_base / 3) * $total_periodos_vencidos;
                            $multa_fv = ((($salario_base_limpo + $valor_insalubridade + $periculosidade_30) / $qnt_dias_mes) * $qnt_dias_fv) * $total_periodos_vencidos;
                           
                            
                            $fv_valor_base = number_format($fv_valor_base, 2,'.','');
                            $fv_um_terco = number_format($fv_um_terco, 2,'.','');
                            $fv_um_terco_dobro = number_format($fv_um_terco_dobro, 2,'.','');
                            $multa_fv = number_format($multa_fv, 2,'.','');
                            
                            $fv_total = $fv_valor_base + $fv_um_terco + $fv_um_terco_dobro;
                        }
                        
                              
                        
                        
                    } else {

                        $fv_total = 0;
                        $fv_valor_base = 0;
                        $fv_um_terco = 0;
                    }
                    
                    
// Fim de Férias Vencidas
//////////////////////////////
//FÉRIAS PROPORCIONAIS /////
///////////////////////////////
                    if ($t_fp == 1) {
                       
                        list($periodo_proporcional_inicio, $periodo_proporcional_final) = explode('/', $periodo_proporcional[0]);
                        
                        $Calc->Calc_qnt_meses_13_ferias($periodo_proporcional_inicio, $periodo_proporcional_final, NULL);
                        $meses_ativo_fp = $Calc->meses_ativos;                                                    
                        //echo "Sinesio: ". $meses_ativo_fp;
                        /*
                          if($_COOKIE['logado'] == 87){

                          $qr_faltas_ferias = mysql_query("SELECT SUM(qnt) as faltas from rh_movimentos_clt as A
                          INNER JOIN rh_folha as B
                          ON A.id_folha = B.id_folha
                          WHERE A.id_clt = '4762' AND id_mov = 62
                          AND B.data_inicio > '$periodo_proporcional_inicio' AND B.data_fim <= '$periodo_proporcional_final'
                          UNION
                          SELECT SUM(qnt) as faltas FROM rh_movimentos_clt WHERE id_clt = '4762' AND id_mov = 62 AND mes_mov = 16");
                          while($row_faltas = mysql_fetch_assoc($qr_faltas_ferias)){
                          $faltas_ferias +=  $row_faltas['faltas'];
                          }

                          if($faltas_ferias > 0 and $faltas_ferias <=5){             $qnt_ferias = $meses_ativo_fp * 2.5;
                          }elseif($faltas_ferias >5 and $faltas_ferias <=14){        $qnt_ferias = $meses_ativo_fp * 2;
                          }elseif($faltas_ferias > 14 and $faltas_ferias <=23 ){     $qnt_ferias = $meses_ativo_fp * 1.5;
                          }elseif (($faltas_ferias >24 and $faltas_ferias <= 32)) {  $qnt_ferias = $meses_ativo_fp * 1;
                          }elseif($faltas_ferias >32){                               $qnt_ferias = 0;   }




                          if($qnt_ferias != 0){


                          $fp_mes          = ($salario_base_limpo + $valor_insalubridade_integral)/12 ;
                          $fp_valor_total  = ($fp_mes/30) * $qnt_ferias;
                          echo $salario_base_limpo + $valor_insalubridade_integral;


                          } elseif($qnt_ferias == 0)  { $meses_ativo_fp  = 0;    }
                          else {

                          $fp_valor_total = ($fp_valor_mes  / 12) * $meses_ativo_fp;
                          }

                          }
                         */

                        $fp_valor_mes = (($salario_base_limpo + $valor_insalubridade_integral + $total_rendi + $periculosidade_30_integral) / $qnt_dias_mes) * $qnt_dias_fp;
                        $fp_valor_total = ($fp_valor_mes / 12) * $meses_ativo_fp;
                       

                        ///Férias (aviso_indenizado)
                        if ($aviso == 'indenizado' and $total_meses != 12 and $dispensa != 65 and $dispensa != 63 and $dispensa != 64 and $dispensa != 66) {

                            $ferias_aviso_indenizado         = $fp_valor_mes / 12;
                            $umterco_ferias_aviso_indenizado = $ferias_aviso_indenizado /3;
                            
                           $ferias_aviso_indenizado         = number_format($ferias_aviso_indenizado, 2,'.','');
                           $umterco_ferias_aviso_indenizado = number_format($umterco_ferias_aviso_indenizado, 2,'.','');
                        }

                        
                        $umterco_ferias_aviso_indenizado = number_format($umterco_ferias_aviso_indenizado, 2,'.','');
                        $fp_valor_total = number_format($fp_valor_total, 2,'.','');
                        
                        if ($t_fa == 1) {
                            $fp_um_terco = $fp_valor_total / 3;
                             $fp_um_terco = number_format($fp_um_terco, 2,'.','');
                            
                            $fp_total = $fp_valor_total + $fp_um_terco;
                        } else {
                            $fp_total = $fp_valor_total;
                        }
                    } else {
                        $fp_total = 0;
                    }


// Cálculo de Férias
                    $ferias_total = $fp_total + $fv_total + $ferias_aviso_indenizado + $umterco_ferias_aviso_indenizado;
                    
//                    if($_COOKIE['logado'] == 179){
//                        echo $to_rendimentos . "<br>";
//                        echo "Valor Base: " . $fv_valor_base . "<br>";
//                        echo "Férias Proporcional: " . $fp_valor_total . "<br>";
//                        echo "Terço Férias Proporcional: " . $fp_um_terco . "<br>";
//                        echo "Férias Vencidas: " . $fv_um_terco . "<br>";
//                        echo "1/3 Férias Vencidas dobro: " . $fv_um_terco_dobro . "<br>";
//                        echo "Multa Ferias Vencida: " . $multa_fv . "<br>";
//                        echo "Ferias Aviso Indenizado: " . $ferias_aviso_indenizado . "<br>";
//                        echo "1/3 Ferias Aviso Indenizado: " . $umterco_ferias_aviso_indenizado . "<br>";
//                    }
                    
                    $to_rendimentos = $to_rendimentos + $fv_valor_base + $fp_valor_total + $fp_um_terco + $fv_um_terco + $fv_um_terco_dobro + $multa_fv + $ferias_aviso_indenizado + $umterco_ferias_aviso_indenizado;
                    $to_descontos = $to_descontos;
                    
                   
// Fim de Férias
// Fim de Férias Proporcionais
//////ACERTANDO A PARTIR DAQUI (AVISO PRÉVIO)
                    $valor_de_media_ap = 0;
                    if($fator == "empregador" && $aviso == "indenizado"){
                        $valor_de_media_ap = $total_rendi;
                    }
                     $baseCalcAviso =  $salario_base_limpo + $valor_insalubridade_integral +  $periculosidade_30_integral + $valor_de_media_ap; //+ $total_rendi
                    ///NOVA REGRA DO AVISO PRÉVIO 
                          $diferenca_anos = ($data_demissao_seg - $data_admissao_seg) / 31536000;
                          for ($d = 1; $d <= (int) $diferenca_anos; $d++) {
                              $valorLei += ($baseCalcAviso / $qnt_dias_mes) * 3;
                          }         
                    
                      $baseCalcAviso = number_format($baseCalcAviso, 2,'.','');       
                      $valorLei = number_format($valorLei, 2,'.','');       
                      
                    //echo "Base de calculo do Aviso: " . $baseCalcAviso. "<br>";
                      
                    /*************CONDIÇÃO PARA LEI 12.506***************/  
                    $array_despensa = array(61,64,66);
                    if($fator == "empregador" AND in_array($dispensa, $array_despensa)){
                        $lei_12_506 = $valorLei;   
                    }  
                      
                    if ($t_ap == 1 and $aviso == 'indenizado') {
                        
                        $valor_aviso_previo = $baseCalcAviso;
                        if ($dispensa == 65) {
                            $aviso = "PAGO pelo funcionário";
                            $valor_ap_pago_trab = $valor_aviso_previo ;
                        } else {
                            $valor_ap_recebido_trab = $valor_aviso_previo;
                            //$lei_12_506 = $valorLei;
                            
                        }
                          
                    } elseif ($aviso == 'trabalhado' and $t_ap == 1 and $fator != "empregado"){
                        //echo "";
                        //$lei_12_506 = $valorLei;
                        
                    }   elseif( $t_ap == 0) {
                    
                        $valor_aviso_previo = NULL;
                        $total_avos_13_indenizado = "0";
                        $total_valor_13_indenizado = NULL;
                        $valor_ap_recebido_trab = NULL;
                        $valor_ap_pago_trab = NULL;
                    }

                    //GAMBI
                    //$valor_aviso_previo = 0;


                    $to_descontos = $to_descontos + $valor_ap_pago_trab;
                    $to_rendimentos = $to_rendimentos + $valor_ap_recebido_trab + $total_valor_13_indenizado;
                    
                    if($_COOKIE['logado'] == 179){
//                        echo "Valor Recebido Trabalhado: " . $valor_ap_recebido_trab . "<br>";
//                        echo "Total 13 Indenizado: " . $total_valor_13_indenizado . "<br>";
                    }
                    
                    // Fim Aviso Prévio
                    // Atraso no Pagamento da Rescisão
                    $data_aviso_previo_1 = date('Y-m-d', strtotime("$data_demissao + 1 days"));
                    $data_aviso_previo_10 = date('Y-m-d', strtotime("$data_demissao + 10 days"));
                    
                    /* ANTES
                     *  ($data_hoje > $data_aviso_previo_1 and $dispensa == 66)  or
                      ($t_ap == 1 and $aviso == 'trabalhado') or
                      ($data_hoje > $data_aviso_previo_10 and $t_ap == 1 and $aviso == 'indenizado')
                     */
                    
                    if(in_array($_COOKIE['logado'], $programadores)){
                        echo "<br>===================================Multa 477=====================================================<br>";
                            echo "Data Hoje: " . $data_hoje . "<br>";
                            echo "Data Aviso 1: " . $data_aviso_previo_1 . "<br>";
                            echo "Data Aviso 10: " . $data_aviso_previo_10 . "<br>";
                            echo "Tipo Aviso Previo: " . $t_ap . "<br>";
                            echo "Aviso Previo: " . $aviso . "<br>";
                            echo "Dispensa : " . $dispensa . "<br>";
                        echo "=====================================================================================================<br>";
                    }
                    //($data_hoje > $data_aviso_previo_1 and $dispensa == 66) || ($data_hoje >= $data_aviso_previo_10 and $dispensa == 65 and $aviso == 'indenizado') ||
                    if (($data_hoje > $data_aviso_previo_1 and $dispensa == 66) || ($data_hoje >= $data_aviso_previo_10 and $dispensa == 65 and $aviso == 'indenizado') || ($data_hoje >= $data_aviso_previo_10 && $t_ap == 1) || ($aviso == 'trabalhado' && $fator == "empregador" && $data_hoje >= $data_aviso_previo_1) ) {
                        //echo "<br><br>Vasco<br><br>";
                        $valor_atraso = $salario_base_limpo;
                    }




///OUTROS EVENTOS
                        $result_total_evento = mysql_query("SELECT *,IF(lancamento = 1,'LANÇADO','') as tipo_lancamento                                     
                                    FROM rh_movimentos_clt
                                    WHERE (mes_mov = '16' AND status = '1' AND id_clt = '$id_clt') AND id_mov NOT IN (56) 
                                    ORDER BY nome_movimento;") or die(mysql_error()); ///O MOVIMENTO COM O CODIGO 292 É ADIANTAMENTO DE 13° NÃO PODENDO ENTRAR NOVAMENTES PARA DEDUÇÃO  AND id_mov NOT IN(292)
                        $total_result = mysql_num_rows($result_total_evento);
                        
                        if($_COOKIE['logado'] == 179){
//                            print_r("SELECT *,IF(lancamento = 1,'LANÇADO','') as tipo_lancamento                                     
//                                    FROM rh_movimentos_clt
//                                    WHERE (mes_mov = '16' AND status = '1' AND id_clt = '$id_clt') AND id_mov NOT IN (56) AND id_mov NOT IN(292)
//                                    ORDER BY nome_movimento <br>");
                        }
                        
                        while ($row_total_evento = mysql_fetch_array($result_total_evento)) {

                            $cor = ($i++ % 2 == 0) ? '#eeeeee' : '#f4f4f4';

                            $movimentos[] = $row_total_evento['id_movimento'];  //usado para gravar os movimentos da rescisão na tabela rh_movimentos_recisao

                            if ($row_total_evento['tipo_movimento'] == 'CREDITO') {

                                $mov_cod[] = $row_total_evento['cod_movimento'];
                                $mov_nome[] = $row_total_evento['nome_movimento'];
                                $mov_tipo[] = $row_total_evento['tipo_lancamento'];
                                $mov_incidencia[] = ($row_total_evento['incidencia'] != ',,') ? 'INSS, IRRF, FGTS' : '';
                                $mov_rend[] = $row_total_evento['valor_movimento'];
                                $mov_desc[] = NULL;
                                $to_mov_rendimentos += $row_total_evento['valor_movimento'];

                                if ($row_total_evento['incidencia'] != ',,') {
                                    $total_mov_lancado += $row_total_evento['valor_movimento'];
                                }
                                
                                
                            } elseif ($row_total_evento['tipo_movimento'] == 'DESCONTO' or $row_total_evento['tipo_movimento'] == 'DEBITO') {
                                $mov_cod[] = $row_total_evento['cod_movimento'];
                                $mov_incidencia[] = ($row_total_evento['incidencia'] != ',,') ? 'INSS, IRRF, FGTS' : '';
                                $mov_nome[] = $row_total_evento['nome_movimento'];
                                $mov_tipo[] = $row_total_evento['tipo_lancamento'];
                                $mov_desc[] = $row_total_evento['valor_movimento'];
                                $mov_rend[] = NULL;
                                $to_mov_descontos += $row_total_evento['valor_movimento'];

                                if ($row_total_evento['incidencia'] != ',,' && $row_total_evento['cod_movimento'] != 80030) {
                                    $total_mov_lancado -= $row_total_evento['valor_movimento'];
                                }
                            }
                        }
                    
                    ///////////////////////////////////////////////
                    ////////// CÁLCULO DE INSS E IRRF /////////////
                    ///////////////////////////////////////////////
                        
                    $total_mov_lancado = number_format($total_mov_lancado, 2,'.','');           
                    if($_COOKIE['logado'] == 179){
                       //echo "Saldo de Salário: " . $saldo_de_salario . "<br> Insalubridade: " . $valor_insalubridade . "<br> Movimentos lançados: " . $total_mov_lancado ."<br> Recebido Trabalhado: ". $valor_ap_recebido_trab ."<br> Lei: ". $lei_12_506;
                    }
                    $BASE_CALC_INSS_SALDO_SALARIO = $saldo_de_salario + $valor_insalubridade + $total_mov_lancado;// + $lei_12_506;
                    $BASE_CALC_IRRF_SS = $saldo_de_salario + $valor_insalubridade + $total_mov_lancado;
                    
                    
                    
                    
                    
                    if($BASE_CALC_INSS_SALDO_SALARIO > 0) { 
                          
                          
                            $Calc->MostraINSS($BASE_CALC_INSS_SALDO_SALARIO, implode('-', $data_exp));
                            
                            if(in_array($_COOKIE['logado'], $programadores)){
                                echo "<pre>";
                                    echo "Base: " . $BASE_CALC_INSS_SALDO_SALARIO . "<br>";
                                    print_r($Calc);
                                echo "</pre>";
                            }
                            
                            $inss_saldo_salario = $Calc->valor;
                            $PERCENTUAL_INSS_SS = $Calc->percentual;

                            if($row_clt['desconto_inss'] == 1){
                                if ($row_clt['desconto_outra_empresa'] + $inss_saldo_salario > $Calc->teto) {
                                    $inss_saldo_salario = ($Calc->teto - $row_clt['desconto_outra_empresa'] );
                                }
                            }
                      } else {
                          $BASE_CALC_INSS_SALDO_SALARIO = 0;
                      }

                   


                    $BASE_CALC_IRRF_SALDO_SALARIO = $BASE_CALC_IRRF_SS - $inss_saldo_salario; //+ $valor_ap_recebido_trab + $lei_12_506;
                    
                    if(in_array($_COOKIE['logado'], $programadores)){
                        //echo $BASE_CALC_IRRF_SS . " - " .  $inss_saldo_salario . "<br>";
                    }
                    
                    $Calc->MostraIRRF($BASE_CALC_IRRF_SALDO_SALARIO, $id_clt, $idprojeto, $data_demissao);
                    $irrf_saldo_salario = $Calc->valor;
                    
                    if(in_array($_COOKIE['logado'], $programadores)){
                        echo "<pre>";
                            echo "Base: " . $Calc->base_calculo_ir . "<br>";
                            //print_r($Calc);
                        echo "</pre>";
                    }
                    
                    if ($irrf_saldo_salario > 0) {
                        $PERCENTUAL_IRRF_SS = $Calc->percentual;
                        $QNT_DEPENDENTES_IRRF_SS = $Calc->total_filhos_menor_21;
                        $VALOR_DDIR_SS = $Calc->valor_deducao_ir_total;
                        $PARCELA_DEDUCAO_IR_SS = $Calc->valor_fixo_ir;
                    } else {
                        $BASE_CALC_IRRF_SALDO_SALARIO = 0;
                    }

                       $inss_saldo_salario = number_format($inss_saldo_salario, 2,'.','');    
                       $irrf_saldo_salario = number_format($irrf_saldo_salario, 2,'.','');    
//////////////
////TOTAIS ///
/////////////       
                       
                   
                            
                    $TOTAL_SALDO_SALARIO = $saldo_de_salario - $inss_saldo_salario - $irrf_saldo_salario;
                    $TOTAL_DECIMO_PROPORCIONAL = ($valor_td + $valor_13_indenizado) - $valor_td_inss - $valor_td_irrf - $valor_decimo_folha;
                    $TOTAL_FERIAS = $fv_valor_base + $fv_um_terco + $fp_valor_total + $fp_um_terco + $multa_fv + $fv_um_terco_dobro + $ferias_aviso_indenizado + $umterco_ferias_aviso_indenizado;
                    $TOTAL_OUTROS_VENCIMENTOS = $valor_sal_familia + $valor_atraso + $valor_outro + $valor_ap_recebido_trab + $valor_insalubridade + $lei_12_506;
                    $TOTAL_OUTROS_DESCONTOS = $total_outros_descontos;

                    //$TOTAL_OUTROS_VENCIMENTOS = 0;
                    $to_descontos = $valor_faltas + $inss_saldo_salario + $irrf_saldo_salario + $valor_td_inss + $valor_td_irrf + $total_outros_descontos + $art_480 + $valor_ap_pago_trab + $to_mov_descontos + $valor_decimo_folha;
                    $to_rendimentos = $saldo_de_salario + $valor_td + $valor_13_indenizado + $TOTAL_FERIAS + $TOTAL_OUTROS_VENCIMENTOS + $to_mov_rendimentos + $art_479;
                     
                        
                    
                    if(in_array($_COOKIE['logado'], $programadores)){
                        
                        echo "====================================Composição do DESCONTOS======================================== <br>";
                        echo "(+) Valor Faltas: " . formato_real($valor_faltas) . "<br>";
                        echo "(+) INSS sobre saldo de salario: " . formato_real($inss_saldo_salario) . "<br>";
                        echo "(+) IRRF sobre Saldo de Salário: " . formato_real($irrf_saldo_salario) . "<br>";
                        echo "(+) INSS sobre 13°: " . formato_real($valor_td_inss) . "<br>";
                        echo "(+) IRRF sobre 13°: " . formato_real($valor_td_irrf) . "<br>";
                        echo "(+) Tottal descontos: " . formato_real($total_outros_descontos) . "<br>";
                        echo "(+) Art 480: " . formato_real($art_480) . "<br>";
                        echo "(+) Aviso Previo trabalhado: " . formato_real($valor_ap_pago_trab) . "<br>";
                        echo "(+) Total Movimentos descontos: " . formato_real($to_mov_descontos) . "<br>";
                        echo "(+) Adiantamento de Decimo terceiro: " . formato_real($valor_decimo_folha) . "<br>";
                        echo "(=) Total: " . formato_real($to_descontos) . "<br>";
                        echo "================================================================================================== <br><br>";
                        
                        echo "====================================Composição do RENDIMENTOS====================================== <br>";
                        echo "(+) Saldo de Salário: " . formato_real($saldo_de_salario) . "<br>";
                        echo "(+) Valor td: " . formato_real($valor_td) . "<br>";
                        echo "(+) Aviso Prévio: " . formato_real($valor_ap_recebido_trab) . "<br>";
                        echo "(+) 13 indenizado: " . formato_real($valor_13_indenizado) . "<br>";
                        echo "(+) Total férias: " . formato_real($TOTAL_FERIAS) . "<br>";
                        echo "(+) Outros Vencimentos: " . formato_real($TOTAL_OUTROS_VENCIMENTOS) .  "<br>";
                        echo "(+) Total movimentos rendimentos:  " . formato_real($to_mov_rendimentos) . "<br>";
                        echo "(+) Art 479: " . formato_real($art_479) . "<br>";
                        echo "(+) Lei 12.506: " . formato_real($lei_12_506) . "<br>";
                        echo "(=) Total: " . formato_real($to_rendimentos) . "<br>";
                        echo "================================================================================================== <br><br>";
                                                
                        
                    }
                    
//////////////////////
///VALOR FINAL//////
/////////////////////
                    $valor_rescisao_final = $to_rendimentos - $to_descontos;

                    if ($valor_rescisao_final < 0) {

                        $arredondamento_positivo = abs($valor_rescisao_final);

                        if ($dispensa == 60) {
                            $valor_rescisao_final = $aviso_previo_valor_d;
                        } else {
                            $valor_rescisao_final = NULL;
                        }
                        
                        $to_rendimentos = $to_rendimentos + $arredondamento_positivo;
                        $valor_rescisao_final = NULL;
                    } else {
                        $arredondamento_positivo = NULL;
                        $valor_rescisao_final = $to_rendimentos - $to_descontos;
                    }
                    ?>

                    <form action="acao.php" method="post" name="Form" id="Form">
                        <input type="hidden" name="recisao_coletiva" id="recisao_coletiva" value="<?php echo $_REQUEST['recisao_coletiva']; ?>" />
                        <table cellpadding="0" cellspacing="0" style="background-color:#FFF; margin:0px auto; width:80%; border:0; line-height:24px;">
                            <tr>
                                <td colspan="4" class="show" align="center"><?= $id_clt . ' - ' . $nome ?></td>
                            </tr>
                            <tr>
                                <td width="25%" class="secao">Data de Admiss&atilde;o:</td>
                                <td width="25%"><?= $data_entradaF ?></td>
                                <td width="25%" class="secao">Data de Demiss&atilde;o:</td>
                                <td width="25%"><?=$data_demissaoF?></td>
                            </tr>
                            <tr>
                                <td class="secao">Motivo do Afastamento:</td>
                                <td><?= $row_status['especifica'] ?></td>
                                <td class="secao">Salario base de c&aacute;lculo:</td>
                                <td>R$ <?= number_format(($salario_base_limpo), 2, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td class="secao">Média dos movimentos fixos:</td>
                                <td>R$ <?= formato_real($total_rendi) ?> 
                                    <a href="action.ver_rendimentos_1.php?clt=<?php echo $id_clt; ?>&m_trab=<?php echo $row_clt['meses_trabalhados']; ?>" id="ver_rend" onClick="return hs.htmlExpand(this, {objectType: 'iframe', width: 400, height: 300})" title="Média dos movimentos">
                                        <img src="../../imagensmenu2/visualizar.gif" width="13" height="13"/>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="secao">Fator:</td>
                                <td><?= $fator ?></td>
                                <td class="secao">Aviso pr&eacute;vio:</td>
                                <td><?= $aviso; ?></td>
                            </tr>
                            <tr style="font-weight:bold;">
                                <td colspan="2" align="center">
                                    <p>RENDIMENTOS:<a href="javascript:;" class="detalhe_rendimentos link_pers"> R$ <?= formato_real($to_rendimentos); ?></a></p>
                                    <p>DESCONTOS: <a href="javascript:;" class="detalhe_descontos link_pers">R$ <?= formato_real($to_descontos) ?></a></p>
                                </td>
                                <td colspan="2" style="font-size:14px; text-align:center;">
                                    Total a ser pago: <?= formato_real($valor_rescisao_final) ?><br />
                                    <?php
                                    if (!empty($arredondamento_positivo)) {
                                        echo 'Arredondamento Positivo: ' . formato_real($arredondamento_positivo) . '';
                                    }
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="4" class="divisor">Sal&aacute;rios</td>
                            </tr>
                            <tr>
                                <td class="secao">
                                    Saldo de sal&aacute;rio (<?= $dias_trabalhados ?>/<?php echo $qnt_dias_mes; ?>):          

                                </td>
                                <td>
                                    <a href="javascript:;" class="detalhe_saldo_salario link_pers">
                                        R$ <?php echo formato_real($saldo_de_salario); ?> 
                                    </a>        
                                    <?php
                                        if (!empty($faltas)) {
                                            echo '<a href="javascript:;" class="detalhe_faltas link_pers">(' . $faltas . ' faltas)</a>';
                                        }
                                    ?>
                                </td>
                                <td class="secao">INSS:</td>
                                <td>
                                    <a href="javascript:;" class="detalhe_inss link_pers">
                                        R$ <?php echo formato_real($inss_saldo_salario); ?> 
                                    </a>
                                </td>
                            </tr>
                            <tr>    
                                <td colspan="2" align="center">  <?php
                                    if ($row_clt['desconto_inss'] == 1) {
                                        echo '<br><strong>**Possui desconto de INSS em outra empresa</strong>';
                                        echo '<br><strong>Salário na outra empresa: </strong> R$ ' . formato_real($row_clt['salario_outra_empresa']);
                                        echo '<br><strong>INSS na outra empresa: </strong> R$ ' . formato_real($row_clt['desconto_outra_empresa']);
                                    }
                                    ?> </td>

                                <td class="secao">IRRF:</td>
                                <td colspan="3">
                                    <a href="javascript:;" class="detalhe_inss link_pers">
                                        R$ <?php echo formato_real($irrf_saldo_salario); ?>
                                    </a>
                                </td>        
                            </tr>
                            <tr>
                                <td colspan="4" align="center">
                                    <span style="font-size:14px; font-weight:bold;">R$ <?= formato_real($TOTAL_SALDO_SALARIO) ?></span>
                                </td>
                            </tr>
                            <?php if ($total_result != 0) { ?>
                                <tr>
                                    <td colspan="4" class="divisor">Outros eventos</td>
                                </tr> 
                                <tr>
                                    <td colspan="4">


                                        <table width="100%" border="0" id="movimentos">
                                            <thead>
                                                <tr style="background-color:  #f2f1f1;">
                                                    <td>COD</td>
                                                    <td align="left">NOME</td>     
                                                    <td align="center">INCIDÊNCIA</td>
                                                    <td align="center"> TIPO </td>
                                                    <td align="right">RENDIMENTO</td>                     
                                                    <td align="right">DESCONTO</td>                     
                                                </tr>
                                            </thead>

                                            <?php foreach ($mov_cod as $chave => $cod) { ?>

                                                <tr style="background-color:<?php echo $cor; ?>">
                                                    <td align="center"><?php echo $cod; ?></td>
                                                    <td align="left"><?php echo $mov_nome[$chave]; ?></td>
                                                    <td align="center"><?php echo $mov_incidencia[$chave]; ?></td>
                                                    <td align="center"><?php echo $mov_tipo[$chave]; ?></td>
                                                    <td align="right"> <?php echo (!empty($mov_rend[$chave])) ? '' . formato_real($mov_rend[$chave]) : ''; ?></td>
                                                    <td align="right"> <?php echo (!empty($mov_desc[$chave])) ? '' . formato_real($mov_desc[$chave]) : ''; ?></td>
                                                </tr>                    
                                            <?php } ?>
                                            <tr style="font-weight:bold;">
                                                <td colspan="4" align="right">TOTAIS:</td>
                                                <td align="right"><?php echo formato_real($to_mov_rendimentos); ?></td>
                                                <td align="right"><?php echo formato_real($to_mov_descontos); ?></td>
                                            </tr> 
                                            <tr><td colspan="6">&nbsp;</td></tr>
                                        </table>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td colspan="4" class="divisor">Décimo terceiro</td>
                            </tr>
                            <tr>
                                <td class="secao">Décimo terceiro proporcional <?php if($meses_ativo_dt > 12){echo ""; }else{ echo "(" . $meses_ativo_dt . "/12)"; } ?>:</td>
                                <td>
                                    <a href="javascript:;" class="detalhe_valor_dt link_pers">
                                        R$ <?php echo formato_real($valor_td) ?>
                                    </a>
                                </td>
                                <td class="secao">13&ordm; Saldo Indenizado 
                                    <?php if($total_avos_13_indenizado > 12){echo ""; }else{ echo "(" . $total_avos_13_indenizado . "/12)"; } ?>:
                                </td>
                                <td>
                                    <a href="javascript:;" class="detalhe_valor_dt_indenizado link_pers">
                                        R$ <?= formato_real($total_valor_13_indenizado); ?>
                                    </a>
                                </td>       
                            </tr>
                            <tr>
                                <td class="secao">INSS:</td>
                                <td>
                                    <a href="javascript:;" class="detalhe_valor_inss_dt link_pers">
                                        R$ <?php echo formato_real($valor_td_inss); ?>
                                    </a>
                                </td>
                                <td class="secao">IRRF:</td>
                                <td colspan="3">
                                    <a href="javascript:;" class="detalhe_valor_irrf_dt link_pers">
                                        R$ <?php echo number_format($valor_td_irrf, 2, ',', '.') ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <?php if (!empty($valor_decimo_folha)) { ?>
                                    <td class="secao"></td>
                                    <td></td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td colspan="4" align="center">
                                    <span style="font-size:14px; font-weight:bold;"></span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" align="left"><div class="divisor">Férias</div></td>
                            </tr>
                            <tr <?= $style_fv ?>>
                                <td class="secao">Férias vencidas: <br />(<?php echo date("d/m/Y",strtotime(str_replace("/", "-", $periodo_venc_inicio))) . ' à ' . date("d/m/Y",strtotime(str_replace("/", "-", $periodo_venc_final))); ?>)</td>
                                <td>
                                    <a href="javascript:;" class="detalhe_ferias_vencidas link_pers">
                                        R$ 
                                        <?php 
                                            echo formato_real($fv_valor_base);
                                            
                                        ?>
                                    </a>
                                </td>
                                <td class="secao">1/3 sobre férias vencidas:</td>
                                <td>
                                    <a href="javascript:;" class="detalhe_um_terco_ferias_vencidas link_pers">
                                        R$ <?= formato_real($fv_um_terco) ?>
                                    </a>
                                </td>
                            </tr>
                            <tr <?= $style_fp ?>>
                                <td class="secao">Férias proporcionais (<?= $meses_ativo_fp ?>/12): </td>
                                <td>
                                    <a href="javascript:;" class="detalhe_ferias_propor link_pers">
                                        R$ <?= formato_real($fp_valor_total) ?>
                                    </a>
                                </td>
                                <td class="secao">1/3 sobre férias proporcionais:</td>
                                <td>
                                    <a href="javascript:;" class="detalhe_um_terco_ferias_propor link_pers">
                                        R$ <?= formato_real($fp_um_terco) ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="secao">F&eacute;rias em Dobro:</td>
                                <td>
                                    <a href="javascript:;" class="detalhe_ferias_dobro link_pers">
                                        R$ <?= formato_real($multa_fv) ?>
                                    </a>
                                </td>
                                <td class="secao"> 1/3 sobre f&eacute;rias em Dobro:</td>
                                <td>
                                    <a href="javascript:;" class="detalhe_um_terco_ferias_dobro link_pers">
                                        R$ <?= formato_real($fv_um_terco_dobro) ?>
                                    </a>
                                </td>       
                            </tr> 
                            <tr>
                                <tr>
                                    <td class="secao"> Férias Aviso Indenizado (1/12):</td>
                                    <td>
                                        <a href="javascript:;" class="detalhe_ferias_aviso_indenizado link_pers">
                                            R$ <?= formato_real($ferias_aviso_indenizado) ?>
                                        </a>
                                    </td>
                                    <td class="secao"> 1/3 sobre férias Aviso Indenizado:</td>
                                    <td>
                                        
                                        R$ <?= formato_real($umterco_ferias_aviso_indenizado); ?>
                                    
                                    </td>

                                </tr>

                            <!--<tr>
                                     <td class="secao">Faltas no per&iacute;odo de f&eacute;rias proporcionais:</td>
                                     <td><?= $faltas_ferias ?></td>
                                 <td class="secao">Dias de f&eacute;rias recebido:</td>
                                 <td><?= $qnt_ferias ?> dias</td>
                                 </tr>-->

                                <tr>
                                    <td colspan="4" align="center">
                                        <span style="font-size:14px; font-weight:bold;">R$ <?= formato_real($TOTAL_FERIAS) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="divisor">Outros vencimentos</td>
                                </tr>
                                <tr>
                                    <td class="secao">Sal&aacute;rio familia:</td>
                                    <td>R$ <?= formato_real($valor_sal_familia) ?></td> 
                                    <td class="secao">Insalubridade</td>
                                    <td>R$ <?= formato_real($valor_insalubridade) ?></td>
                                </tr>
                                <tr>
                                    <td class="secao">Aviso Prévio:</td>
                                    <td>R$ <?= formato_real($valor_ap_recebido_trab) ?></td>
                                    <td class="secao">Lei nº 12.506:</td>
                                    <td>R$ <?= formato_real($lei_12_506) ?></td>         
                                </tr>    

                                <tr>
                                    <td class="secao">Atraso de Rescis&atilde;o (477):</td>
                                    <td>R$ <?= formato_real($valor_atraso); ?></td>       
                                    <td align="right"><span class="secao">Indeniza&ccedil;&atilde;o Artigo 479:</span></td>
                                    <td align="left">R$ <?php echo formato_real($art_479, 2, ',', '.'); ?>							
                                    </td>
                                </tr>                                 

                                <tr>
                                    <td colspan="4" style="font-size:14px; text-align:center; font-weight:bold;">
                                        R$ <?= number_format($TOTAL_OUTROS_VENCIMENTOS, 2, ',', '.'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4"><div class="divisor">Outros descontos</div></td>
                                </tr>
                                <tr>
                                    <td class="secao">Aviso Prévio pago pelo Funcion&aacute;rio:</td>
                                    <td>R$ <?= formato_real($valor_ap_pago_trab) ?></td>
                                    <td class="secao">Devolu&ccedil;&atilde;o:</td>
                                    <td>R$ <?= formato_real($devolucao) ?></td>
                                </tr>
                                <tr>
                                    <td class="secao">Indeniza&ccedil;&atilde;o Artigo 480:</td>
                                    <td colspan="3">R$  <?php echo formato_real($art_480); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="font-size:14px; font-weight:bold; text-align:center;">
                                        R$ <?= formato_real($TOTAL_OUTROS_DESCONTOS) ?>
                                    </td>
                                </tr>    

                                <tr>
                                    <td colspan="4"><div class="divisor">FGTS</div></td>
                                </tr>
                                <tr style="display:none;">
                                    <td class="secao">FGTS 8%:</td>
                                    <td>R$ <?= $fgts8_totalF ?> (<?= $mensagem_fgts8 ?>)</td>
                                    <td class="secao">FGTS 40%:</td>
                                    <td>R$ <?= $fgts4_totalF ?></td>
                                </tr>
                                <tr>
                                    <td class="secao">Código de Saque:</td>
                                    <td><?= $cod_saque_fgts ?></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="4" align="center">
                                        <p>&nbsp;</p>

                                        <?php
                                        $CAMPOS_INSERT['id_clt'] = $id_clt;
//$campos_insert['ajuda_custo']       =  $ajuda_custo;
                                        $CAMPOS_INSERT['nome'] = $nome;
                                        $CAMPOS_INSERT['id_regiao'] = $idregiao;
                                        $CAMPOS_INSERT['id_projeto'] = $idprojeto;
                                        $CAMPOS_INSERT['id_curso'] = $idcurso;
                                        $CAMPOS_INSERT['data_adm'] = $data_entrada;
                                        $CAMPOS_INSERT['data_demi'] = $data_demissao;
                                        $CAMPOS_INSERT['data_proc'] = date('Y-m-d');
                                        $CAMPOS_INSERT['dias_saldo'] = $dias_trabalhados;
                                        $CAMPOS_INSERT['um_ano'] = $um_ano;
//$campos_insert['meses_ativo']       =  $meses_ativo;
                                        $CAMPOS_INSERT['motivo'] = $dispensa;
                                        $CAMPOS_INSERT['fator'] = $fator;
                                        $CAMPOS_INSERT['aviso'] = $aviso;
                                        $CAMPOS_INSERT['aviso_valor'] = $valor_aviso_previo;
                                        $CAMPOS_INSERT['dias_aviso'] = $previo;
                                        $CAMPOS_INSERT['data_fim_aviso'] = $data_fim_avprevio;
                                        $CAMPOS_INSERT['fgts8'] = $fgts8_totalT;
                                        $CAMPOS_INSERT['fgts40'] = $fgts4_totalT;
                                        $CAMPOS_INSERT['fgts_anterior'] = $anterior;
                                        $CAMPOS_INSERT['fgts_cod'] = $cod_mov_fgts;
                                        $CAMPOS_INSERT['fgts_saque'] = $cod_saque_fgts;
                                        $CAMPOS_INSERT['sal_base'] = $salario_base_limpo;
                                        $CAMPOS_INSERT['saldo_salario'] = $saldo_de_salario;
                                        $CAMPOS_INSERT['inss_ss'] = $inss_saldo_salario;
                                        $CAMPOS_INSERT['previdencia_ss'] = $inss_saldo_salario;
                                        $CAMPOS_INSERT['ir_ss'] = $irrf_saldo_salario;
                                        $CAMPOS_INSERT['terceiro_ss'] = $valor_13_indenizado;
                                        $CAMPOS_INSERT['dt_salario'] = $valor_td;
                                        $CAMPOS_INSERT['inss_dt'] = $valor_td_inss;
                                        $CAMPOS_INSERT['previdencia_dt'] = $valor_td_inss;
                                        $CAMPOS_INSERT['ir_dt'] = $valor_td_irrf;
                                        $CAMPOS_INSERT['ferias_vencidas'] = $fv_valor_base;
                                        $CAMPOS_INSERT['umterco_fv'] = $fv_um_terco;
                                        $CAMPOS_INSERT['ferias_pr'] = $fp_valor_total;
                                        $CAMPOS_INSERT['umterco_fp'] = $fp_um_terco;
                                        $CAMPOS_INSERT['sal_familia'] = $valor_sal_familia;
                                        $CAMPOS_INSERT['to_sal_fami'] = ($valor_sal_familia + $sal_familia_anterior);
//$campos_insert['ad_noturno']            =  $valor_adnoturnoT;
                                        $CAMPOS_INSERT['insalubridade'] = $valor_insalubridade;
//$campos_insert['vale_refeicao']         =  $vale_refeicaoT;
//$campos_insert['debito_vale_refeicao']  =  $debito_vale_refeicaoT;
                                        $CAMPOS_INSERT['a480'] = $art_480;
                                        $CAMPOS_INSERT['a479'] = $art_479;
                                        $CAMPOS_INSERT['a477'] = $valor_atraso;
                                        $CAMPOS_INSERT['lei_12_506'] = $lei_12_506;
//$campos_insert['comissao']              =  $valor_comissaoT;
//$campos_insert['gratificacao']          =  $valor_grativicacao;
//$campos_insert['extra']                 =  $hora_extra;
//$campos_insert['outros']                =  $valor_outroT;
//$campos_insert['movimentos']            =  $a_rendimentos;
//$campos_insert['valor_movimentos']      =  $a_rendimentos;
                                        $CAMPOS_INSERT['total_rendimento'] = $to_rendimentos;
                                        $CAMPOS_INSERT['total_deducao'] = $to_descontos;
                                        $CAMPOS_INSERT['total_liquido'] = $valor_rescisao_final;
                                        $CAMPOS_INSERT['arredondamento_positivo'] = $arredondamento_positivo;
                                        $CAMPOS_INSERT['avos_dt'] = $meses_ativo_dt;
                                        $CAMPOS_INSERT['avos_fp'] = $meses_ativo_fp;
                                        $CAMPOS_INSERT['data_aviso'] = $data_aviso;
                                        $CAMPOS_INSERT['devolucao'] = $devolucao;
                                        $CAMPOS_INSERT['faltas'] = $faltas;
                                        $CAMPOS_INSERT['valor_faltas'] = $valor_faltas;
                                        $CAMPOS_INSERT['user'] = $user;
                                        $CAMPOS_INSERT['ferias_aviso_indenizado'] = $ferias_aviso_indenizado;
                                        $CAMPOS_INSERT['umterco_ferias_aviso_indenizado'] = $umterco_ferias_aviso_indenizado;
                                        $CAMPOS_INSERT['adiantamento_13'] = $valor_decimo_folha;
//$campos_insert['folha']                     =  '0';
//$campos_insert['adicional_noturno']         =  $adicional_noturno;
//$campos_insert['dsr']                       =  $dsr;
//$campos_insert['desc_auxilio_distancia']    =  $desc_auxilio_distancia;
                                        $CAMPOS_INSERT['um_terco_ferias_dobro']     =  $fv_um_terco_dobro;
                                        $CAMPOS_INSERT['fv_dobro']                  =  $multa_fv;
                                        $CAMPOS_INSERT['fp_data_ini'] = $periodo_proporcional_inicio;
                                        $CAMPOS_INSERT['fp_data_fim'] = $periodo_proporcional_final;
                                        $CAMPOS_INSERT['fv_data_ini'] = $periodo_venc_inicio;
                                        $CAMPOS_INSERT['fv_data_fim'] = $periodo_venc_final;
                                        $CAMPOS_INSERT['qnt_dependente_salfamilia'] = $TOTAL_MENOR;
                                        $CAMPOS_INSERT['base_inss_ss'] = $BASE_CALC_INSS_SALDO_SALARIO;
                                        $CAMPOS_INSERT['percentual_inss_ss'] = $PERCENTUAL_INSS_SS;
                                        $CAMPOS_INSERT['base_irrf_ss'] = $BASE_CALC_IRRF_SALDO_SALARIO;
                                        $CAMPOS_INSERT['percentual_irrf_ss'] = $PERCENTUAL_IRRF_SS;
                                        $CAMPOS_INSERT['parcela_deducao_irrf_ss'] = $PARCELA_DEDUCAO_IR_SS;
                                        $CAMPOS_INSERT['qnt_dependente_irrf_ss'] = $QNT_DEPENDENTES_IRRF_SS;
                                        $CAMPOS_INSERT['valor_ddir_ss'] = $VALOR_DDIR_SS;
                                        $CAMPOS_INSERT['base_fgts_ss'] = $BASE_CALC_INSS_SALDO_SALARIO;
                                        $CAMPOS_INSERT['base_inss_13'] = $BASE_CALC_INSS_13;
                                        $CAMPOS_INSERT['percentual_inss_13'] = $PERCENTUAL_INSS_13;
                                        $CAMPOS_INSERT['base_irrf_13'] = $BASE_CALC_IRRF_13;
                                        $CAMPOS_INSERT['percentual_irrf_13'] = $PERCENTUAL_IRRF_13;
                                        $CAMPOS_INSERT['parcela_deducao_irrf_13'] = $PARCELA_DEDUCAO_IR_13;
                                        $CAMPOS_INSERT['base_fgts_13'] = $BASE_CALC_INSS_13;
                                        $CAMPOS_INSERT['qnt_dependente_irrf_13'] = $QNT_DEPENDENTES_IRRF_13;
                                        $CAMPOS_INSERT['valor_ddir_13'] = $VALOR_DDIR_13;
                                        $CAMPOS_INSERT['desconto_inss'] = $row_clt['desconto_inss'];
                                        $CAMPOS_INSERT['salario_outra_empresa'] = $row_clt['salario_outra_empresa'];
                                        $CAMPOS_INSERT['desconto_inss_outra_empresa'] = $row_clt['desconto_outra_empresa'];

                                        if($_REQUEST['recisao_coletiva'] == 1){
                                            $CAMPOS_INSERT['recisao_provisao_de_calculo'] = 1;
                                            $CAMPOS_INSERT['status'] = 0;
                                            $CAMPOS_INSERT['id_recisao_lote'] = $_REQUEST['id_header'];
                                            
                                        }

                                        if(in_array($_COOKIE['logado'], $programadores)){
                                            echo "<pre>";
                                                print_r($CAMPOS_INSERT);
                                            echo "</pre>";
                                        }
                                        
                                      
                                        foreach ($CAMPOS_INSERT as $campo => $valor) {
                                            $campos[] = $campo;
                                            $valores[] = "'$valor'";
                                        }
                                        $campos = implode(',', $campos);
                                        $valores = implode(',', $valores);

                                        
                                        // Arquivo TXT
                                        $conteudo = "INSERT INTO rh_recisao($campos ) VALUES ( $valores);\r\n";
                                        /*************GAMBI FILHO DA PUTA PRA FUNCIONAR ESSA PORRA************/
                                        if($_REQUEST['recisao_coletiva'] == 1){
                                            mysql_query($conteudo) or die("erro ao criar recisao em lote");   
                                            $ultimo_rescisao_lote = mysql_insert_id();
//                                            if (sizeof($movimentos) > 0) {
//                                                $ids_movimentos = implode(',', $movimentos);
//
//                                                $qr_movimentos = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_movimento IN($ids_movimentos)");
//                                                while ($row_mov = mysql_fetch_assoc($qr_movimentos)) {
//                                                    $query_movimento = "INSERT INTO rh_movimentos_rescisao (id_rescisao, id_mov, id_clt, nome_movimento, valor, incidencia ) VALUES ('{$ultimo_rescisao_lote}','{$row_mov[id_mov]}', '{$row_mov[id_clt]}', '{$row_mov[nome_movimento]}', '{$row_mov[valor_movimento]}','{$row_mov[incidencia]}' )";
//                                                    mysql_query($query_movimento) or die("Erro ao selecionar movimentos de rescisão");
//                                                }
//                                            }
                                        }
                                        // Relaciona a rescisão ao tipo de aviso prévio -- AMANDA
//                                        if ($aviso == 'trabalhado' && $dispensa == 61) {
//                                            $id_tpAvisoPre = $_REQUEST['tpAvisoPre'];
//                                            $obs = $_REQUEST['obs'];
//                                            $conteudo .= "INSERT INTO rescisao_avisoPrevio_assoc (id_tpAvisoPre,obs,status,id_rescisao) VALUES ($id_tpAvisoPre,'$obs',1, ultimo_id_rescisao);\r\n";
//                                        }
                                        
                                        $conteudo .= "UPDATE rh_clt SET status = '$dispensa', data_saida = '$data_demissao', status_demi = '1' WHERE id_clt = '$id_clt' LIMIT 1;\r\n";
                                        // FIM 
                                        // AKI O PROBLEMA
                                        //$conteudo .= "INSERT INTO rh_eventos(id_clt, id_regiao, id_projeto, nome_status, cod_status, id_status, data, status) VALUES ('$id_clt', '$idregiao', '$idprojeto', '$row_evento[especifica]', '$dispensa', '$row_evento[0]', '$data_demissao', '1');\r\n";

                                        $nome_arquivo = 'recisaoteste_' . $id_clt . '_' . date('dmY') . '.txt';
                                        $arquivo = '../arquivos/' . $nome_arquivo;
                                        

                                        if (sizeof($movimentos) > 0) {
                                            $ids_movimentos = implode(',', $movimentos);

                                            $qr_movimentos = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_movimento IN($ids_movimentos)");
                                            while ($row_mov = mysql_fetch_assoc($qr_movimentos)) {
                                                $conteudo .= "INSERT INTO rh_movimentos_rescisao (id_rescisao, id_mov, id_clt, nome_movimento, valor, incidencia, tipo_qnt, qnt ) VALUES (ultimo_id_rescisao,'$row_mov[id_mov]', '$row_mov[id_clt]', '$row_mov[nome_movimento]', '$row_mov[valor_movimento]',  '$row_mov[incidencia]', '$row_mov[tipo_qnt]]', '$row_mov[qnt]]' ); \r\n";
                                            }
                                        }


// Tenta abrir o arquivo TXT            
                                        if (!$abrir = fopen($arquivo, "wa+")) {
                                            echo "Erro abrindo arquivo ($arquivo)";
                                            exit;
                                        }

// Escreve no arquivo TXT
                                        if (!fwrite($abrir, $conteudo)) {
                                            print "Erro escrevendo no arquivo ($arquivo)";
                                            exit;
                                        }
                                        
                                        
// Fecha o arquivo
                                        fclose($abrir);

// Encriptografando a variável
                                        $linkvolt = str_replace('+', '--', encrypt("$regiao&$id_clt"));
                                        $linkir = str_replace('+', '--', encrypt("$regiao&$id_clt&$nome_arquivo"));
                                        ?>
                                        <table width="50%" border="0" cellspacing="0" cellpadding="0">                                      
                                            
                                            <tr>
                                                <td><a href="recisao2.php?tela=4&enc=<?= $linkir ?>" class="botao recisao_lote">Processar Rescis&atilde;o</a></td>
                                                <td><a href="recisao2.php?tela=2&enc=<?= $linkvolt ?>" class="botao">Voltar</a></td>
                                            </tr>
                                        </table>
                                        <p>&nbsp;</p>
                                    </td>
                                </tr>
                        </table>
                    </form>
                    <?php
                    break;
                case 4:
                    // executando a rescisão
                    // Recebendo a variável criptografada
                    list($regiao, $id_clt, $arquivo) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
                    $file = '../arquivos/' . $arquivo;
                    $fp = file($file);
                    $i = '0';

                    verificaRecisao($id_clt);

                    foreach ($fp as $linha) {
                        $linha = str_replace('ultimo_id_rescisao', $idi[0], $linha);
                        mysql_query($linha) or die(mysql_error());
                        $i++;
                        $idi[] = mysql_insert_id();
                    }
                    
                    //CRIANDO LOG DE RESCISÃO
                    $query = "SELECT * FROM rh_clt WHERE id_clt = '{$id_clt}'";
                    $sql = mysql_query($query) or die("Erro ao selecionar clt");
                    $dados_clt = mysql_fetch_assoc($sql);
                    $data_cad = date("Y-m-d H:i:s");
                    $rescisao->criaLog($id_clt, $dados_clt['id_regiao'], $dados_clt['id_projeto'], $_COOKIE['logado'], $data_cad, 1, 1);
                                        
                    // Encriptografando a variável
                    $link = str_replace('+', '--', encrypt("$regiao&$id_clt&$idi[0]"));
                    echo '<script>location.href="nova_rescisao_2.php?enc=' . $link . '"</script>';
                    exit();

                    break;
            }
            ?>
        </div>
    </body>
</html>