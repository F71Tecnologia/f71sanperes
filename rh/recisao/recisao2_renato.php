<?php
include('../../conn.php');
if($_REQUEST['recisao_coletiva'] != 1){
    if (empty($_COOKIE['logado'])) {
        print "<script>location.href = '../login.php?entre=true';</script>";
        exit;
    }
}

//if($_COOKIE['logado'] == 204){
//    echo "<pre>";
//    print_r($_REQUEST);
//    echo "</pre>";exit;
//}

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
include('../../classes/MovimentoClass.php');
  
$usuario = carregaUsuario();
$rescisao = new Rescisao();
$objCalcFolha = new Calculo_Folha();


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

if (empty($_REQUEST['tela'])) {
    $tela = 1;
} else {
    $tela = $_REQUEST['tela'];
}

//if($_COOKIE['logado'] != 204){
//    if ($_GET['deletar'] == true) {
//        $id_rescisao = $_GET['id'];
//        //$movimentos = mysql_result(mysql_query("SELECT movimentos FROM rh_recisao WHERE id_recisao = '".$_GET['id']."' LIMIT 1"),0);
//        //$total_movimentos = (int)count(explode(',',$movimentos));
//        //mysql_query("UPDATE rh_movimentos_clt SET status_ferias = '1' WHERE id_movimento IN('".$movimentos."') LIMIT ".$total_movimentos."");
//
//        mysql_query("DELETE FROM rh_movimentos_rescisao WHERE id_clt = '" . $_GET['id_clt'] . "' ") or die(mysql_error());
//        mysql_query("UPDATE rh_recisao SET status = '0' WHERE id_recisao = '$id_rescisao' LIMIT 1");
//        mysql_query("UPDATE rh_clt SET status = '200', data_saida = '', status_demi = '' WHERE id_clt = '" . $_GET['id_clt'] . "' LIMIT 1");
//
//        $query = "SELECT * FROM rh_clt WHERE id_clt = '{$_GET['id_clt']}'";
//        $sql = mysql_query($query) or die("Erro ao selecionar clt");
//        $dados_clt = mysql_fetch_assoc($sql);
//        $data_cad = date("Y-m-d H:i:s");
//        $rescisao->criaLog($_GET['id_clt'], $dados_clt['id_regiao'], $dados_clt['id_projeto'], $_COOKIE['logado'], $data_cad, 2, 1);
//    }                   
//}

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

if(isset($_REQUEST['method']) && !empty($_REQUEST['method'])){ 
     if($_REQUEST['method'] == "desprocessar_recisao"){
         $retorno = array("status" => false);
         $dados = $obj_recisao->verificaSaidaPagaDeRecisao($_REQUEST['id_recisao'], $_REQUEST['id_regiao'], $_REQUEST['id_clt']);
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

        <script type="text/javascript" src="../../js/jquery-1.8.3.min.js"></script>
        <script type="text/javascript" src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script type="text/javascript" src="../../js/ramon.js"></script>
        <script type="text/javascript" src="../../js/global.js"></script>
        <script type="text/javascript" src="../../js/jquery.ui.core.js"></script>
        <script type="text/javascript" src="../../js/jquery.ui.datepicker.js"></script>
        <script src="../../js/highslide-with-html.js" type="text/javascript"></script>
        <script type="text/javascript">
            hs.graphicsDir = '../../images-box/graphics/';
            hs.outlineType = 'rounded-white';

            $(function() {


                $('#dispensa').change(function() {

                    var dispensa = parseInt($(this).val());

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

                });


                $('#dispensa').change();

                $('#data_aviso').datepicker({
                    changeMonth: true,
                    changeYear: true

                });


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

                $(".remove_recisao").click(function(){
                    var id_recisao = $(this).attr("data-recisao");
                    var id_regiao  = $(this).attr("data-regiao");
                    var id_clt     = $(this).attr("data-clt");

                    $.ajax({
                        url:"recisao2.php",
                        type:"POST",
                        dataType:"json",
                        data:{
                            id_recisao:id_recisao,
                            id_regiao:id_regiao,
                            id_clt:id_clt,
                            method:"desprocessar_recisao"
                        },
                        success: function(data){
                            if(!data.status){
                                history.go(0);
                            }else{
                                $(data.dados).each(function(k, v){
                                    $(".data_demissao").html(v.data_demissao);
                                    $(".data_pagamento").html(v.data_pg);
                                    $(".nome").html(v.nome_clt);
                                    $(".status").html(v.status_saida);
                                    $(".valor").html(v.valor);
                                });
                                $("#mensagens").show();
                                thickBoxModal("Desprocessar Recisão", "#mensagens", "350", "450");
                            }
                        }
                    });
                });
                
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
                <p>Data demissão: <span class="data_demissao"></span><p>
                <p>Data pagamento: <span class="data_pagamento"></span><p>
                <p>Nome: <span class="nome"></span><p>
                <p>Status: <span class="status"></span><p>
                <p>Valor: <span class="valor"></span><p>
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
                                <td colspan="8" class="show">
                                    <span class="seta" style="color:#F90; font-size:32px;">&#8250;</span> Participantes Desativados
                                </td>
                            </tr>
                            <tr class="novo_tr">
                                <td width="6%"></td>
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
                            $sql_demissao = "SELECT *, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM rh_clt WHERE status IN ('60','61','62','63','64','65','66','80','101') AND id_regiao = '$regiao' $filtroProjeto $auxPesquisa ORDER BY nome ASC";
//                            echo $sql_demissao."<br>";
                            $qr_demissao = mysql_query($sql_demissao);

                            while ($row_demissao = mysql_fetch_array($qr_demissao)) {

                                $Curso->MostraCurso($row_demissao['id_curso']);
                                $NomeCurso = $Curso->nome;

                                $ClasPro->MostraProjeto($row_demissao['id_projeto']);
                                $NomeProjeto = $ClasPro->nome;

                                $qr_rescisao = mysql_query("SELECT *, date_format(data_demi, '%d/%m/%Y') AS data_demi2 FROM rh_recisao WHERE id_clt = '$row_demissao[0]' AND status = '1'");
                                $row_rescisao = mysql_fetch_array($qr_rescisao);
                                $total_rescisao = mysql_num_rows($qr_rescisao);

                                $sql_rescisao_complementar = "SELECT * FROM rh_recisao  WHERE vinculo_id_rescisao = '$row_rescisao[0]' AND rescisao_complementar = 1  AND status = 1";
//                                echo $sql_rescisao_complementar;
                                
                                
                                
                                $qr_rescisao_complementar = mysql_query($sql_rescisao_complementar);
                                
                                $arr_complementar = array();
                                while($row_rescisao_complementar = mysql_fetch_array($qr_rescisao_complementar)){
                                    $arr_complementar[] = $row_rescisao_complementar;
                                }
                                
                                $total_rescisao_complementar = mysql_num_rows($qr_rescisao_complementar);

                                $link = str_replace('+', '--', encrypt("$regiao&$row_demissao[0]&$row_rescisao[0]"));

                                if (substr($row_rescisao['data_proc'], 0, 10) >= '2013-04-04') {
                                    $link_nova_rescisao = "nova_rescisao_2.php?enc=$link";
                                } else {
                                    $link_nova_rescisao = "nova_rescisao.php?enc=$link";
                                }
                                ?>

                                <tr style="background-color:<?php if ($cor++ % 2 != 0) { echo '#F0F0F0'; } else { echo '#FDFDFD';} ?> ">
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


                                        <?php if (empty($total_rescisao)) { ?>
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
                                        <?php if ($ACOES->verifica_permissoes(82)) { ?>
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
                                <td>&nbsp;</td>
                                <td align="right">TOTAL : </td>
                                <td>R$<?php echo number_format($totalizador_recisao, 2, ',', '.'); ?></td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="9">
                                    Banco: <select name="banco"><?php echo $optionBanco; ?></select>
                                    Data: <input type="text" name="data" >

                                    <input type="submit" value="Gerar Arquivo de Banco">&nbsp;&nbsp;&nbsp;&nbsp;<a href="arquivo_banco_rescisao.php" target="_blank">Gerenciar Arquivos</a>
                                </td>
                            </tr>
                        </table>
                        </form>
                        <form name="acao_recisao" id="acao_recisao" action="recisao2_renato.php">
                            <input type="hidden" name="id_recisao" id="id_recisao" value="" />     
                            <input type="hidden" name="id_regiao" id="id_regiao" value="" />     
                            <input type="hidden" name="id_clt" id="id_clt" value="" />  
                            
                        </form>

                        <?php
                    }
                    break;
            }
            ?>
        </div>
    </body>
</html>