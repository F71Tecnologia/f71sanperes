<?php
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
include('../../classes/CalculoRescisaoClass.php');
include('../../classes/CalculoFeriasClass.php');
include('../../classes/CalculoFolhaClass.php');
include('../../classes/MovimentoClass.php');
include('../../classes/CltClass.php');

$usuario = carregaUsuario();
$objRescisao = new Rescisao();
$objCalcFolha = new Calculo_Folha();
$objCalcFerias = new Calculo_Ferias();
$objRescisao = new Rescisao();      
$objCalcRescisao = new Calculo_rescisao();
$objClt = new CltClass();


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

if (empty($_REQUEST['tela'])) {
    $tela = 1;
} else {
    $tela = $_REQUEST['tela'];
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
    
    header("Location: teste_objeto.php?regiao={$_GET['regiao']}");
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
                        url:"teste_objeto.php",
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
                                        <td><a href="teste_objeto.php?tela=2&enc=<?= $link ?>"><?= $row_aguardo['nome'] ?></a></td>
                                        <td><?= $NomeProjeto ?></td>
                                        <td><?= $row_aguardo['locacao'] ?></td>
                                        <td><?= $NomeCurso ?></td>
                                        <td>
                                            <?php if ($ACOES->verifica_permissoes(82)) { ?>
                                                <a href="teste_objeto.php?voltar_aguardando=true&id=<?php echo $row_aguardo[0]; ?>&regiao=<?php echo $_GET['regiao']; ?>&id_clt=<?php echo $row_aguardo[0]; ?>" title="Desprocessar Aguardando Demissão" onclick="return window.confirm('Você tem certeza que quer desprocessar aguardando demissão?');"><img src="../imagensrh/deletar.gif" /></a>
                                            <?php } ?>
                                        </td>
                                    </tr>

                                <?php } ?>

                            </table>

                        <?php } ?>

                        <table width="95%" align="center" border="0" cellpadding="8" cellspacing="0" style="margin-top:20px;">
                            <tr bgcolor="#999999">
                                <td colspan="8" class="show">
                                    <span class="seta" style="color:#F90; font-size:32px;">&#8250;</span> Participantes Desativados
                                </td>
                            </tr>
                            <tr class="novo_tr">
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
                                        <?php if ($ACOES->verifica_permissoes(82)) { ?>
                                            <!--<a href="teste_objeto.php?deletar=true&id=<?php echo $row_rescisao[0]; ?>&regiao=<?php echo $_GET['regiao']; ?>&id_clt=<?php echo $row_demissao[0]; ?>" title="Desprocessar Rescisão" onclick="return window.confirm('Você tem certeza que quer desprocessar esta rescisão?');"><img src="../imagensrh/deletar.gif" /></a>-->
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
                        </table>
                        <form name="acao_recisao" id="acao_recisao" action="teste_objeto.php">
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
                    
                    verificaRecisao($id_clt);
                    $dados_contratacao = $objClt->getDadosContratacao($id_clt);
                    
                    
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

// Calculando Saldo FGTS
                    $qr_liquido = mysql_query("SELECT SUM(salliquido) AS liquido FROM rh_folha_proc WHERE id_clt = '$id_clt' AND status = '3'");
                    $fgts = number_format(mysql_result($qr_liquido, 0) * 0.08, 2, ',', '.');
                    ?>

                    <form action="teste_objeto.php" name="form1" method="post" onsubmit="return validaForm()">
                        <table cellpadding="4" cellspacing="0" style="width:80%; margin:0px auto; border:0; line-height:30px;">
                            <tr>
                                <td colspan="2" class="show" align="center"><?= $id_clt . ' - ' . $nome ?></td>
                            </tr>
                            <tr>
                                <td width="38%" class="secao">Tipo de Dispensa:</td>
                                <td width="62%">
                                    <select name="dispensa" id="dispensa">
                                        <option value="">Selecione...</option>
                                        <?php
                                        $qr_dispensa = mysql_query("SELECT * FROM rhstatus WHERE tipo = 'recisao' ORDER BY codigo ASC");
                                        while ($row_dispensa = mysql_fetch_array($qr_dispensa)) {
                                            ?>
                                            <option value="<?= $row_dispensa['codigo'] ?>">	<?= $row_dispensa['codigo'] ?>-<?= $row_dispensa['especifica'] ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="secao">Fator:</td>
                                <td>
                                    <select id="fator" name="fator" id="fator">
                                        <option value="empregado">empregado</option>
                                        <option value="empregador">empregador</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="secao">Dias de Saldo do Sal&aacute;rio:</td>
                                <td><?php echo abs($dias_trabalhados) ?> dias (data para demissão: <?= $data_demissaoF ?>)</td>
                            </tr>
                            <tr>
                                <td class="secao">Remunera&ccedil;&atilde;o para Fins Rescis&oacute;rios:</td>
                                <td><input name="valor" type="text" id="valor" onkeydown="FormataValor(this, event, 17, 2)" value="0,00" size="6"/></td>
                            </tr>
                         <!--   <tr>
                                <td class="secao">Quantidade de Faltas:</td>
                                <td><input name="faltas" type="text" id="faltas" value="<?= $faltas ?>" size="2"/></td>
                            </tr> -->
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
                            <tr>
                                <td class="secao">Data do Aviso:</td>
                                <td><input type="text" id="data_aviso" name="data_aviso" size="8"
                                           onkeyup="mascara_data(this);
                                                   pula(10, this.id, devolucao.id)" disabled="disabled"/></td>
                            </tr>
                            <tr>
                                <td class="secao">Devolu&ccedil;&atilde;o de Cr&eacute;dito Indevido:</td>
                                <td><input name="devolucao" id="devolucao" size="6" onkeydown="FormataValor(this, event, 17, 2)" /></td>
                            </tr>
                            <?php if(!empty($dados_contratacao)){ ?>
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
                            <?php } ?>
                            
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
                    $tipo_dispensa = $_REQUEST['dispensa'];
                    $faltas = $_REQUEST['faltas'];
                    $dias_trabalhados = $_REQUEST['diastrab'];
                    $aviso  = $_REQUEST['aviso'];
                    $previo = $_REQUEST['previo'];
                    $valor  = $_REQUEST['valor'];
                    $data_aviso = implode('-', array_reverse(explode('/', $_REQUEST['data_aviso'])));
                    $devolucao = str_replace(',', '.', str_replace('.', '', $_REQUEST['devolucao']));
                    
                    if($_REQUEST['recisao_coletiva'] != 1){
                        verificaRecisao($id_clt);
                    }
                    
                    //////DADOS DO CLT  
                    $dadosClt           = $objClt->getDadosCltRescisao($id_clt, $tipo_dispensa);        
                    $salario_contratual = $dadosClt['salario'];                    
                    $data_demissao      = ($_REQUEST['recisao_coletiva'] != 1) ? $dadosClt['data_demi'] : $_REQUEST['data_demi'];
                    
                    $objCalcRescisao->setClt($id_clt);
                    $objCalcRescisao->setMotivoRescisao($tipo_dispensa);
                    $objCalcRescisao->setTipoAviso($aviso);
                    $objCalcRescisao->getRescisaoConfig($dadosClt['um_ano']);   
                    
                    $arrayDataDemissao  = $objCalcRescisao->getData($data_demissao, 2);
                    $periodoTrabalhado  = $objCalcRescisao->getPeriodoTrabalhado($dadosClt['data_entrada'], $data_demissao);
                    $diasTrab           = $periodoTrabalhado['dias_trabalhados'];
                    $mesesTrab          = $periodoTrabalhado['meses_trabalhados'];
                    
                    ///////INSTANCIANDO O OBJETO  DE MOVIMENTOS
                    $objMovimento = new Movimentos();
                    $objMovimento->carregaMovimentos($ano_demissao);                    
                    $insalubridade      = $objCalcFolha->getInsalubridade($diasTrab, $dadosClt['tipo_insalubridade'], $dadosClt['qnt_salminimo_insalu'], $arrayDataDemissao['ano']) ;
                    $objCalcRescisao->setValoresRescisao(2, array( 'id_mov' => $insalubridade['id_mov'],  'cod_mov' => $insalubridade['cod_mov'],     'valor'   => $insalubridade['valor_porporcional']));
                 
                    
                   ///////////////////////////////////////////////
                   //Carregando tabelas para calculo dos impostos
                    $objCalcFolha->CarregaTabelas($arrayDataDemissao['ano']);
                    
                    
                  
                    ////////////////////////////////////////////////////
                    ///////////   MÉDIA DOS MOVIMENTOS  RECEBIDOS  ////
                    //////////////////////////////////////////////////                  
                    //$media_movimentos = $objCalcFolha->getMediaMovimentos($id_clt, $arrayDataDemissao['mes'], $arrayDataDemissao['ano'], $mesesTrab,true); //Confirmar forma de calcular
                    $media_movimentos = $objCalcRescisao->getMediaMovimentos();
                    $total_rendi      = $media_movimentos['total_media'];
                    
                    //Periculosidade
                    if($dadosClt['periculosidade_30'] == 1){
                        $periculosidade  = $objCalcFolha->getPericulosidade($salario_base_limpo, $dias_trabalhados);
                        $objMovimento->setIdRegiao($regiao);
                        $objMovimento->setIdProjeto($id_projeto);
                        $objMovimento->setIdClt($id_clt);
                        $objMovimento->setIdMov(57);
                        $objMovimento->setCodMov(6007);
                        $objMovimento->setMes(16);
                        $objMovimento->setAno(2014);
                        $valor_mov = $periculosidade['valor_proporcional'];
                        $verfica_movimento = $objMovimento->verificaMovimento();

                        if(empty($verfica_movimento['num_rows'])){
                             $insere = $objMovimento->insereMovimento($valor_mov); 
                        }/* else {

                            if($verfica_movimento['valor_movimento'] != number_format($valor_mov,2,'.','')){
                                $objMovimento->updateValorPorId($verfica_movimento['id_movimento'], $valor_mov);
                            }

                        }*/
                        $objCalcRescisao->setValoresRescisao(2, array( 'id_mov' => $periculosidade['id_mov'],  'cod_mov' => $periculosidade['cod_mov'],     'valor'   => $periculosidade['valor_porporcional'])); 
                  }
                                    
                    
                    //////////////////////////////////////////////////////////
                    /// Base de cáclulo para 13º, Férias e  Aviso Prévio /////
                    /////////////////////////////////////////////////////////
                    $salarioBaseCalc = $salario_contratual + $total_rendi+ $insalubridade['valor_integral'] + $periculosidade['valor_integral'];
                    
                    
                    ////////////////////////////////////////////////////////////////////////////////
                    ////CALCULO DE INSS E IRRF SOBRE SALDO DE SALARIO E MOVIMENTOS  LANÇADOS  //////
                    //////////////////////////////////////////////////////////////////////////////                        
                    $saldo_salario      = $objCalcRescisao->getSaldoSalario($salario_contratual, $diasTrab);
                    $movimentosLancados = $objCalcRescisao->getMovimentosRescisaoLancados();

                    $baseCalcINSS = $saldo_salario + $movimentosLancados['base_inss'] + $insalubridade['valor_proporcional'];
                    $inss = $objCalcFolha->getCalcInss($baseCalcINSS,2, $dadosClt['desconto_inss'], $dadosClt['tipo_desconto_inss'], $dadosClt['salario_outra_empresa'], $dadosClt['desconto_outra_empresa']);

                    $baseCalcIrrf  = $saldo_salario + $movimentosLancados['base_irrf'] + $insalubridade['valor_proporcional'] - $inss['valor_inss'];
                    $irrf = $objCalcFolha->getCalcIrrf($baseCalcIrrf, $id_clt, 2);
                    
                     $objCalcRescisao->setValoresRescisao(1, array( 'id_mov' => 254,  'cod_mov' => 50226,     'valor'   => $saldo_salario,    'tipo_qnt' => 2,   'qnt' => $diasTrab));                      
                     $objCalcRescisao->setValoresRescisao(2, array('cod_mov' => 5020,'valor' => $inss['valor_inss'],'percentual' => $inss['percentual'] )); 
                     $objCalcRescisao->setValoresRescisao(2, array('cod_mov' => 5021,'valor' => $irrf['valor_irrf'],'percentual' => $irrf['percentual'], 'qnt_dependente' => $irrf['qnt_dependente_irrf'] )); 
                 
                
                    
                    ///////////////////////////////////////////////////
                    ////CALCULO DE INSS E IRRF SOBRE 13º SALARIO  ////
                    /////////////////////////////////////////////////
                    $decimoTerceiro = $objCalcRescisao->getDecimoTerceiroProporcional($salarioBaseCalc, $dadosClt['data_entrada'],$data_demissao);
                    $inss_13        = $objCalcFolha->getCalcInss($decimoTerceiro['base_inss'], 2);

                    $baseCalcIrrf_13 = $decimoTerceiro['base_inss'] - $inss_13['valor_inss'];
                    $irrf_13         = $objCalcFolha->getCalcIrrf($baseCalcIrrf_13, $id_clt, 2);
                    
                    $objCalcRescisao->setValoresRescisao(1, array( 'id_mov' => 47,  'cod_mov' => 5045,    
                                                                   'aquisitivo_ini' => $decimoTerceiro['periodo']['inicio'],
                                                                   'aquisitivo_fim' => $decimoTerceiro['periodo']['fim'],
                                                                   'tipo_qnt' => 3,   'qnt' => $decimoTerceiro['avos_13'],
                                                                    'valor'   => $decimoTerceiro['valor_13'] ));      

                    $objCalcRescisao->setValoresRescisao(1, array( 'id_mov' => 275,  'cod_mov' => 80015,    
                                                                   'aquisitivo_ini' => $decimoTerceiro['periodo']['inicio'],
                                                                   'aquisitivo_fim' => $decimoTerceiro['periodo']['fim'],
                                                                   'tipo_qnt' => 3,   'qnt' => 1,
                                                                    'valor'   => $decimoTerceiro['valor_13_indenizado'] ));      

                    $objCalcRescisao->setValoresRescisao(2, array( 'id_mov' => 2,  'cod_mov' => 4002,
                                                                   'valor'   => $inss_13['valor_inss'] ));   

                    $objCalcRescisao->setValoresRescisao(2, array( 'id_mov' => 4,  'cod_mov' => 4004,
                                                                   'percentual' => $irrf_13['percentual_irrf'],                    
                                                                   'qnt_dependente' => $irrf_13['qnt_dependente'],                    
                                                                   'valor'   => $irrf_13['valor_irrf'] ));  
                     
                  
                    //////////////////////////////////
                    /////////     Férias    /////////
                    ////////////////////////////////                    
                    $objCalcFerias->setIdClt($id_clt);
                    $ferias = $objCalcFerias->getPeriodoFeriasRescisao($id_clt, $dadosClt['data_entrada'], $data_demissao);

                            //Proporcionais
                            $meses_fp = $objCalcRescisao->getCalculoQntAvos($ferias['periodo_proporcional']['inicio'], $ferias['periodo_proporcional']['fim']);

                            //faltas no periodo
                           // $qntFaltasProp = $objCalcFerias->getFaltasNoPeriodo($ferias['periodo_proporcional']['inicio'], $ferias['periodo_proporcional']['fim']);
                           // $qntDiasFaltasProp = $objCalcRescisao->getDiaProporcionalFaltasRescisao($meses_fp, $qntFaltasProp['total_faltas']);

                             $feriasProporcionais = $objCalcRescisao->getCalculoFeriasProp($salarioBaseCalc, $meses_fp);

                             $objCalcRescisao->setValoresRescisao(1, array( 'id_mov' => 267,  'cod_mov' => 50141, 
                                                                         'aquisitivo_ini' => $ferias['periodo_proporcional']['inicio'],
                                                                         'aquisitivo_fim' => $ferias['periodo_proporcional']['fim'],
                                                                         'tipo_qnt' => 3, 'qnt' => $meses_fp,
                                                                         'valor'   => $feriasProporcionais['valor_ferias'])); 

                             $objCalcRescisao->setValoresRescisao(1, array( 'id_mov' => 272,  'cod_mov' => 80012, 
                                                                         'aquisitivo_ini' => $ferias['periodo_proporcional']['inicio'],
                                                                         'aquisitivo_fim' => $ferias['periodo_proporcional']['fim'],
                                                                         'valor'   => $feriasProporcionais['valor_um_terco_ferias'])); 

                             $objCalcRescisao->setValoresRescisao(1, array( 'id_mov' => 273,  'cod_mov' => 80013, 
                                                                         'valor'   => $feriasProporcionais['ferias_aviso_indenizado'])); 

                             $objCalcRescisao->setValoresRescisao(1, array( 'id_mov' => 274,  'cod_mov' => 80014, 
                                                                         'valor'   => $feriasProporcionais['um_terco_ferias_aviso_indenizado'])); 




                            //Vencidas
                            if(sizeof($ferias['periodos_vencido']) >0){
                                foreach($ferias['periodos_vencido'] as $periodo){

                                    $dadosFeriasVenc = $objCalcRescisao->getCalcFeriasVencidas($salarioBaseCalc, $periodo['inicio'], $periodo['fim']);
                                    $feriasVencidas[] = $dadosFeriasVenc;
                                    $TOTAL_FERIAS_VENCIDAS  += $dadosFeriasVenc['valor_ferias'] + $dadosFeriasVenc['valor_um_terco_ferias'];

                                    $objCalcRescisao->setValoresRescisao(1, array( 'id_mov' => 268,  'cod_mov' => 50142, 
                                                                         'aquisitivo_ini' => $periodo['inicio'],
                                                                         'aquisitivo_fim' => $periodo['fim'],
                                                                         'tipo_qnt' => 3,
                                                                         'qnt'      =>12,
                                                                         'valor'   => $dadosFeriasVenc['valor_ferias'])); 

                                    $objCalcRescisao->setValoresRescisao(1, array( 'id_mov' => 20,  'cod_mov' => 5018, 
                                                                         'aquisitivo_ini' => $periodo['inicio'],
                                                                         'aquisitivo_fim' => $periodo['fim'],
                                                                         'valor'   => $dadosFeriasVenc['valor_um_terco_ferias']));      
                                }
                            }

                           
                
                            
                      //Outros  
                    //  $salarioFamilia = $objCalcRescisao->getSalarioFamilia($salario_contratual, $diasTrab, $dados['id_projeto'], $dados['data_demi']); //Verificar na calculos.php                    
                    $art479         = $objCalcRescisao->getArt479($salario_contratual, $dadosClt['data_entrada'], $data_demissao);
                    $art480         = $objCalcRescisao->getArt480($salario_contratual, $dadosClt['data_entrada'], $data_demissao);
                    $art477         = $objCalcRescisao->getArt477($salario_contratual,  $data_demissao);
                    $avisoPrevio    = $objCalcRescisao->getAvisoPrevio($salarioBaseCalc,$periodoTrabalhado['anos_trabalhados']); 
                    
                    $objCalcRescisao->setValoresRescisao(1, array( 'id_mov' => 18,   'cod_mov' => 5016,     'valor'   => $art479['valor']));  
                    $objCalcRescisao->setValoresRescisao(2, array( 'id_mov' => 11,   'cod_mov' => 5004,     'valor'   => $art480['valor'])); 
                    $objCalcRescisao->setValoresRescisao(1, array( 'id_mov' => 19,   'cod_mov' => 5017,     'valor'   => $art477));
                    $objCalcRescisao->setValoresRescisao(1, array( 'id_mov' => 5,    'cod_mov' => 4005,     'valor'   => $avisoPrevio['aviso_credito']));
                    $objCalcRescisao->setValoresRescisao(1, array( 'id_mov' => 280,  'cod_mov' => 80018,'tipo_qnt' => 2, 'qnt' => $avisoPrevio['dias_lei_12506'],'valor' => $avisoPrevio['valor_lei_12506']));
                    $objCalcRescisao->setValoresRescisao(2, array( 'id_mov' => 278,  'cod_mov' => 80016,     'valor'   => $avisoPrevio['aviso_debito']));       
                            
                            echo '<pre>';
                              print_r($avisoPrevio);
                            echo '</pre>';

                            
                            
                      ////////////
                     ///TOTAIS //
                    ////////////
                    $TOTAL_SALDO_SALARIO = $saldo_salario + $movimentosLancados['total_rendimentos'] - ($movimentosLancados['total_desconto'] + $inss['valor_inss'] + $irrf['valor_irrf']);
                    $TOTAL_DECIMO_PROPORCIONAL = ($decimoTerceiro['valor_13'] + $decimoTerceiro['valor_13_indenizado']) - $inss_13['valor_inss'] - $irrf_13['valor_irrf'] - $decimoTerceiro['valor_13_folha'];
                    $TOTAL_FERIAS_PROPORCIONAIS = $feriasProporcionais['valor_ferias'] + $feriasProporcionais['valor_um_terco_ferias'] + $feriasProporcionais['ferias_aviso_indenizado'] + $feriasProporcionais['um_terco_ferias_aviso_indenizado'];
                    $TOTAL_OUTROS_VENCIMENTOS  = $valor_sal_familia + $art477 + $art479 + $avisoPrevio['aviso_credito'] + $insalubridade['valor_proporcional'] + $avisoPrevio['valor_lei_12506'];
                    $TOTAL_OUTROS_DESCONTOS    = $art480['valor'] + $avisoPrevio['aviso_debito'];


                
                    $to_rendimentos = $saldo_salario + $movimentosLancados['total_rendimentos'] + $decimoTerceiro['valor_13'] + $decimoTerceiro['valor_13_indenizado'] + $TOTAL_FERIAS_PROPORCIONAIS 
                                      + $TOTAL_FERIAS_VENCIDAS + $TOTAL_OUTROS_VENCIMENTOS;

                    $to_descontos   = $movimentosLancados['total_desconto'] + $inss['valor_inss'] + $irrf['valor_irrf'] + $inss_13['valor_inss'] + $irrf_13['valor_irrf'] + $decimoTerceiro['valor_13_folha']
                                      + $TOTAL_OUTROS_DESCONTOS;

                    
                    /////////////////////
                    ///VALOR FINAL//////
                    ////////////////////
                   $valoresFinais = $objCalcRescisao->getVerificaValorFinal($to_rendimentos, $to_descontos);
                                    
                   
                    ?>

                    <form action="acao.php" method="post" name="Form" id="Form">
                        <input type="hidden" name="recisao_coletiva" id="recisao_coletiva" value="<?php echo $_REQUEST['recisao_coletiva']; ?>" />
                        <table cellpadding="0" cellspacing="0" style="background-color:#FFF; margin:0px auto; width:80%; border:0; line-height:24px;">
                            <tr>
                                <td colspan="4" class="show" align="center"><?= $id_clt . ' - ' . $dadosClt['nome'] ?></td>
                            </tr>
                            <tr>
                                <td width="25%" class="secao">Data de Admiss&atilde;o:</td>
                                <td width="25%"><?= $dadosClt['data_entradaF'] ?></td>
                                <td width="25%" class="secao">Data de Demiss&atilde;o:</td>
                                <td width="25%"><?= $dadosClt['data_demiF']?></td>
                            </tr>
                            <tr>
                                <td width="25%" class="secao">Dependente IRRF:</td>
                                <td width="25%"><?= (!empty($irrf['qnt_dependente_irrf'])) ? $irrf['qnt_dependente_irrf']: 0; ?></td>
                            </tr>
                            <tr>
                                <td class="secao">Motivo do Afastamento:</td>
                                <td><?= $dadosClt['tipo_rescisao'] ?></td>
                                <td class="secao">Salario base de c&aacute;lculo:</td>
                                <td>R$ <?= number_format(($salario_contratual), 2, ',', '.') ?></td>
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
                                    RENDIMENTOS: R$ <?= formato_real($valoresFinais['total_rendimento']); ?><br />
                                    DESCONTOS: R$ <?= formato_real($valoresFinais['total_desconto']) ?></td>
                                <td colspan="2" style="font-size:14px; text-align:center;">
                                    Total a ser pago: <?= formato_real($valoresFinais['valor_final']) ?><br />
                                    <?php
                                    if (!empty($arredondamento_positivo)) {
                                        echo 'Arredondamento Positivo: ' . formato_real($valoresFinais['valor_ajuste']) . '';
                                    }
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="4" class="divisor">Sal&aacute;rios</td>
                            </tr>
                            <tr height="50">
                                <td class="secao">
                                    Saldo de sal&aacute;rio (<?= $diasTrab ?>/30):
                                </td>
                                <td colspan="3">
                                    R$ <?= formato_real($saldo_salario) ?> <?php
                                    if (!empty($faltas)) {
                                        echo '(' . $faltas . ' faltas)';
                                    }
                                    ?>
                                </td>                               
                            </tr>
                            <?php if (sizeof($movimentosLancados['movimentos']) > 0) {?>                               
                                <tr>
                                    <td colspan="4">
                                        <table width="100%" border="0" id="movimentos">
                                            <thead>
                                                <tr style="background-color:  #f2f1f1;">
                                                    <td align="center" colspan="6">Movimentos Lançados</td>
                                                </tr>
                                          
                                                <tr style="background-color:  #f2f1f1;">
                                                       <td>COD</td>
                                                       <td align="left">NOME</td>     
                                                       <td align="center">INCIDÊNCIA</td>
                                                       <td align="center"> TIPO </td>
                                                       <td align="right">RENDIMENTO</td>                     
                                                       <td align="right">DESCONTO</td>                     
                                                   </tr>
                                                  </thead>
                                            <?php foreach ($movimentosLancados['movimentos'] as $id_movimento => $valor) { ?>

                                                <tr style="background-color:<?php echo $cor; ?>">
                                                    <td align="center"><?php echo $valor['codigo']; ?></td>
                                                    <td align="left"><?php echo $valor['nome'];; ?></td>
                                                    <td align="center"><?php echo $valor['incidencia']; ?></td>
                                                    <td align="center"><?php echo $valor['categoria']; ?></td>
                                                    <td align="right"> <?php echo ($valor['categoria'] == 'CREDITO') ?  formato_real($valor['valor']) : '' ; ?></td>
                                                    <td align="right"> <?php echo ($valor['categoria'] == 'DEBITO') ?  formato_real($valor['valor']) : '' ; ?></td>
                                                </tr>                    
                                            <?php } ?>
                                            <tr style="font-weight:bold;">
                                                <td colspan="4" align="right">TOTAIS:</td>
                                                <td align="right"><?php echo formato_real($movimentosLancados['total_rendimento']); ?></td>
                                                <td align="right"><?php echo formato_real($movimentosLancados['total_desconto']); ?></td>
                                            </tr> 
                                            
                                        </table>
                                    </td>
                                </tr>
                            <?php } ?>                            
                            <tr>
                                 <td class="secao">INSS:</td>
                                <td>R$ <?= formato_real($inss['valor_inss']) ?> </td>
                                   <td colspan="2" align="center">  <?php
                                    if ($inss['desconto_inss'] == 1) {
                                        echo '<br><strong>**Possui desconto de INSS em outra empresa</strong>';
                                        echo '<br><strong>Salário na outra empresa: </strong> R$ ' . formato_real($inss['salario_outra_empresa']);
                                        echo '<br><strong>INSS na outra empresa: </strong> R$ ' . formato_real($inss['valor_desconto_outra_empresa']);
                                    }
                                    ?> </td>
                            </tr>
                             <td class="secao">IRRF:</td>
                                <td colspan="3">R$ <?= formato_real($irrf['valor_irrf']); ?></td>   
                            <tr>   
                            </tr>
                            <tr>
                                <td colspan="4" align="center">
                                    <span style="font-size:14px; font-weight:bold;">R$ <?= formato_real($TOTAL_SALDO_SALARIO) ?></span>
                                </td>
                            </tr>
                            
                            <tr>
                                <td colspan="4" class="divisor">Décimo terceiro</td>
                            </tr>
                            <tr>
                                <td class="secao">Décimo terceiro proporcional (<?= $decimoTerceiro['avos_13'] ?>/12):</td>
                                <td>R$ <?php echo formato_real($decimoTerceiro['valor_13']) ?></td>
                                <td class="secao">13&ordm; Saldo Indenizado (<?= $decimoTerceiro['avos_13_indenizado']; ?>/12):</span></td>
                                <td>R$ <?= formato_real($decimoTerceiro['valor_13_indenizado']); ?></td>       
                            </tr>
                            <tr>
                                <td class="secao">INSS:</td>
                                <td>R$ <?php echo formato_real($inss_13['valor_inss']); ?></td>
                                <td class="secao">IRRF:</td>
                                <td colspan="3">R$ <?php echo number_format($irrf['valor_irrf'], 2, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <?php if ($decimoTerceiro['valor_13_folha'] != '0.00' ) { ?>
                                    <td class="secao">ADIANTAMENTO DE 13º:</td>
                                    <td>R$ <?= formato_real($decimoTerceiro['valor_13_folha']) ?></td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td colspan="4" align="center">
                                    <span style="font-size:14px; font-weight:bold;">R$ <?= formato_real($TOTAL_DECIMO_PROPORCIONAL) ?></span>
                                </td>
                            </tr>
                               <tr>
                                <td colspan="4" align="left"><div class="divisor">Férias vencidas</div></td>
                            </tr>
                            <?php
                            foreach($feriasVencidas as $feriasvenc){                                
                                        $perIni = $feriasvenc['periodo']['inicio'];
                                        $perFim = $feriasvenc['periodo']['fim'];
                                    
                            ?>
                                <tr <?= $style_fv ?>>
                                    <td class="secao">Periodo Aquisitivo ( <?php echo formato_brasileiro($perIni).' a '.formato_brasileiro($perFim)?>):</td>
                                  <td>R$ <?= formato_real($feriasvenc['valor_ferias']) ?></td>
                                  <td class="secao">1/3 sobre férias vencidas:</td>
                                  <td>R$ <?= formato_real($feriasvenc['valor_um_terco_ferias']) ?></td>
                              </tr>

                            <?php
                            }
                            ?>
                            <tr>
                               <td colspan="4" align="center">
                                   <span style="font-size:14px; font-weight:bold;">R$ <?= formato_real($TOTAL_FERIAS_VENCIDAS) ?></span>
                               </td>
                           </tr>
                                
                            
                            <tr>
                                <td colspan="4" align="left"><div class="divisor">Férias Proporcionais</div></td>
                            </tr>
                            
                            <tr <?= $style_fp ?>>
                                <td class="secao">Férias proporcionais (<?= $meses_fp ?>/12)<br> Periodo <?php echo formato_brasileiro($ferias['periodo_proporcional']['inicio']).' a '.  formato_brasileiro($ferias['periodo_proporcional']['fim'])?>: </td>
                                <td>R$ <?= formato_real($feriasProporcionais['valor_ferias']) ?></td>
                                <td class="secao">1/3 sobre férias proporcionais:</td>
                                <td>R$ <?= formato_real($feriasProporcionais['valor_um_terco_ferias']) ?></td>
                            </tr>
                           <!-- <tr>
                                <td class="secao">F&eacute;rias em Dobro:</td>
                                <td>R$ <?= formato_real($multa_fv) ?></td>
                                <td class="secao"> 1/3 sobre f&eacute;rias em Dobro:</td>
                                <td>R$ <?= formato_real($fv_um_terco_dobro) ?></td>       
                            </tr> -->
                            <tr>
                                <tr>
                                    <td class="secao"> Férias Aviso Indenizado (1/12):</td>
                                    <td>R$ <?= formato_real($feriasProporcionais['ferias_aviso_indenizado']) ?></td>
                                    <td class="secao"> 1/3 sobre férias Aviso Indenizado:</td>
                                    <td>R$ <?= formato_real($feriasProporcionais['um_terco_ferias_aviso_indenizado']); ?></td>

                                </tr>

                            <!--<tr>
                                     <td class="secao">Faltas no per&iacute;odo de f&eacute;rias proporcionais:</td>
                                     <td><?= $faltas_ferias ?></td>
                                 <td class="secao">Dias de f&eacute;rias recebido:</td>
                                 <td><?= $qnt_ferias ?> dias</td>
                                 </tr>-->

                                <tr>
                                    <td colspan="4" align="center">
                                        <span style="font-size:14px; font-weight:bold;">R$ <?= formato_real($TOTAL_FERIAS_PROPORCIONAIS) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="divisor">Outros vencimentos</td>
                                </tr>
                                <tr>
                                    <td class="secao">Sal&aacute;rio familia:</td>
                                    <td>R$ <?= formato_real($salarioFamilia['valor']) ?></td> 
                                    <td class="secao">Insalubridade</td>
                                    <td>R$ <?= formato_real($insalubridade['valor_proporcional']) ?></td>
                                </tr>
                                <tr>
                                    <td class="secao">Aviso Prévio:</td>
                                    <td>R$ <?= formato_real($avisoPrevio['aviso_credito']) ?></td>
                                    <td class="secao">Lei nº 12.506 (<?php echo $avisoPrevio['dias_lei_12506']?> dias):</td>
                                    <td>R$ <?= formato_real($avisoPrevio['valor_lei_12506']) ?></td>         
                                </tr>    

                                <tr>
                                    <td class="secao">Atraso de Rescis&atilde;o (477):</td>
                                    <td>R$ <?= formato_real($art477); ?></td>       
                                    <td align="right"><span class="secao">Indeniza&ccedil;&atilde;o Artigo 479:</span></td>
                                    <td align="left">R$ <?php echo formato_real($art479, 2, ',', '.'); ?>							
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
                                    <td>R$ <?= formato_real($avisoPrevio['aviso_debito']) ?></td>
                                    <td class="secao">Devolu&ccedil;&atilde;o:</td>
                                    <td>R$ <?= formato_real($devolucao) ?></td>
                                </tr>
                                <tr>
                                    <td class="secao">Indeniza&ccedil;&atilde;o Artigo 480:</td>
                                    <td colspan="3">R$  <?php echo formato_real($art480); ?>
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
                                       
                                        
                                        <table>
                                        <?php
                                        $arrayMovimentos =  $objMovimento->getTodosMovimentos();
                                        
                                        foreach($objCalcRescisao->ValoresRescisao as $chave => $movimentos){
                                            
                                           echo '<tr><td>'.$chave.'</td></tr>';
                                           
                                            foreach($movimentos as $dadosMov){
                                                
                                                switch ($dadosMov['tipo_qnt']){                                                    
                                                    case 1: $quantidade = '('.$dadosMov['qnt'].' Horas)';
                                                        break;
                                                    case 2: $quantidade = '('.$dadosMov['qnt'].'/30)';
                                                        break;
                                                    case 3: $quantidade = '('.$dadosMov['qnt'].'/12)';
                                                        break;
                                                    default :$quantidade ='';
                                                }  
                                                
                                           $perAquisitivo = (!empty($dadosMov['aquisitivo_ini'])) ? 'Periodo: '.$dadosMov['aquisitivo_ini'].' a '.$dadosMov['aquisitivo_fim'] :'' ;
                                          
                                           ?>   
                                            <tr>
                                                <td><?php  echo $arrayMovimentos[$dadosMov['cod_mov']]['descicao'];?></td>
                                                <td><?php echo $quantidade; ?></td>
                                                <td><?php echo $perAquisitivo; ?></td>
                                                <td><?php echo $dadosMov['valor']?></td>
                                            </tr>
                                            
                                            <?php    
                                            }
                                        }
                                        echo '</table>';
                                        
                                        
                                        
                                        $CAMPOS_INSERT['id_clt'] = $id_clt;
//$campos_insert['ajuda_custo']       =  $ajuda_custo;
                                        $CAMPOS_INSERT['nome'] = $nome;
                                        $CAMPOS_INSERT['id_regiao']  = $dadosClt['id_regiao'];
                                        $CAMPOS_INSERT['id_projeto'] = $dadosClt['id_projeto'];
                                        $CAMPOS_INSERT['id_curso']   = $dadosClt['id_curso'];
                                        $CAMPOS_INSERT['data_adm']   = $dadosClt['data_entrada'];
                                        $CAMPOS_INSERT['data_demi']  = $data_demissao;
                                        $CAMPOS_INSERT['data_proc']  = date('Y-m-d');
                                        $CAMPOS_INSERT['dias_saldo'] = $diasTrab;
                                        $CAMPOS_INSERT['um_ano']     = $periodoTrabalhado['anos_trabalhados'];
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
                                        
                                        
                                        $conteudo .= "UPDATE rh_clt SET status = '$dispensa', data_saida = '$data_demissao', status_demi = '1' WHERE id_clt = '$id_clt' LIMIT 1;\r\n";
                                        

                                        // AKI O PROBLEMA
                                        //$conteudo .= "INSERT INTO rh_eventos(id_clt, id_regiao, id_projeto, nome_status, cod_status, id_status, data, status) VALUES ('$id_clt', '$idregiao', '$idprojeto', '$row_evento[especifica]', '$dispensa', '$row_evento[0]', '$data_demissao', '1');\r\n";

                                        $nome_arquivo = 'recisaoteste_' . $id_clt . '_' . date('dmY') . '.txt';
                                        $arquivo = '../arquivos/' . $nome_arquivo;
                                        

                                        if (sizeof($movimentos) > 0) {
                                            $ids_movimentos = implode(',', $movimentos);

                                            $qr_movimentos = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_movimento IN($ids_movimentos)");
                                            while ($row_mov = mysql_fetch_assoc($qr_movimentos)) {
                                                $conteudo .= "INSERT INTO rh_movimentos_rescisao (id_rescisao, id_mov, id_clt, nome_movimento, valor, incidencia ) VALUES (ultimo_id_rescisao,'$row_mov[id_mov]', '$row_mov[id_clt]', '$row_mov[nome_movimento]', '$row_mov[valor_movimento]',  '$row_mov[incidencia]' ); \r\n";
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
                                                <td><a href="recisao2_teste.php?tela=4&enc=<?= $linkir ?>" class="botao recisao_lote">Processar Rescis&atilde;o</a></td>
                                                <td><a href="recisao2_teste.php?tela=2&enc=<?= $linkvolt ?>" class="botao">Voltar</a></td>
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
                    $objRescisao->criaLog($id_clt, $dados_clt['id_regiao'], $dados_clt['id_projeto'], $_COOKIE['logado'], $data_cad, 1, 1);
                                        
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