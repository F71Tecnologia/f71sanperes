<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br /><a href="login.php">Logar</a>';
    exit;
}

include('../../conn.php');
include('../../funcoes.php');
include('../../classes/clt.php');
include('../../classes/calculos.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes_permissoes/acoes.class.php');

$ACOES = new Acoes();

$usuario = carregaUsuario();
$regiao = $usuario['id_regiao'];

$sqlBanco = mysql_query("SELECT * FROM bancos WHERE id_regiao = {$regiao} ORDER BY id_banco");
while($rowBanco = mysql_fetch_array($sqlBanco)){
    $optionBanco .= "<option value='{$rowBanco['id_banco']}'>{$rowBanco['razao']}({$rowBanco['nome']})</option>";
}

require_once('../../classes/ArquivoTxtBancoClass.php');
$ArquivoTxtBancoClass = new ArquivoTxtBancoClass();
$arrayArquivos = $ArquivoTxtBancoClass->getRegistros('f'); //echo "<pre>";print_r($arrayArquivos);echo "</pre>";
if(isset($_REQUEST['arqFerias'])){
    //require_once('../../classes/ArquivoTxtBancoClass.php');
    $ArquivoTxtBancoClass->gerarTxtBanco('FERIAS',$_REQUEST['banco'], $_REQUEST['data'], $_REQUEST['arqFerias']);
    //$ArquivoTxtBancoClass->gerarTxtBanco(113, $_REQUEST['data'], $_REQUEST['arqFerias']);
    header("Location: arquivo_banco_ferias.php");
}

if (empty($_REQUEST['enc'])) {
    $tela = (isset($_REQUEST['tela'])) ? $_REQUEST['tela'] : 1;
} else {
    $enc = str_replace('--', '+', $_REQUEST['enc']);
    $link = decrypt($enc);
    list($regiao, $tela, $clt, $id_ferias) = explode('&', $link);
}

if ($_GET['deletar'] == true) {
//    PARA DESPORCESSAR AS FÉRIAS AGORA VAI PRA OUTRA TELA, COM MAIS VALIDAÇÕES     
//    $movimentos = mysql_result(mysql_query("SELECT movimentos FROM rh_ferias WHERE id_ferias = '" . $_GET['id'] . "' LIMIT 1"), 0);
//    $total_movimentos = (int) count(explode(',', $movimentos));
//    mysql_query("UPDATE rh_ferias SET status = '0' WHERE id_ferias = '" . $_GET['id'] . "' LIMIT 1");
//    mysql_query("UPDATE rh_movimentos_clt SET status_ferias = '1' WHERE id_movimento IN('" . $movimentos . "') LIMIT " . $total_movimentos . "");
//    mysql_query("UPDATE rh_clt SET status = 10  WHERE id_clt = '$_GET[id_clt]' LIMIT 1");
    
}

$meses = array('', '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Mar&ccedil;o', '04' => 'Abril', '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>:: Intranet :: F&eacute;rias</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
            <link href="../../net1.css" rel="stylesheet" type="text/css" />
            <link href="../../favicon.ico" rel="shortcut icon" />
            <link href="../../js/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
            <link href="../../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
            <script type="text/javascript" src="../../js/jquery-1.3.2.js"></script>
            <script type="text/javascript" src="../../js/jquery.ui.core.js"></script>
            <script type="text/javascript" src="../../js/jquery.ui.widget.js"></script>
            <script type="text/javascript" src="../../js/jquery.ui.datepicker.js"></script>
            <script type="text/javascript" src="../../js/jquery.ui.datepicker-pt-BR.js"></script>
            <script type="text/javascript" src="../../js/global.js"></script>
            <link href="../../js/highslide.css" rel="stylesheet" type="text/css" />
            <script src="../../js/highslide-with-html.js" type="text/javascript"></script>
            <script type="text/javascript">

                hs.graphicsDir = '../../images-box/graphics/';
                hs.outlineType = 'rounded-white';

                $(function() {
                    // Calendário
                    $('#data_inicio').datepicker({
                        changeMonth: true,
                        changeYear: true
                    });
                    // Exibe e Oculta a Div de Histórico
                    $("#ver_historico").click(function() {
                        $('#historico').toggle('fast');
                    });







                    $('select[name*=quantidade_dias]').change(function() {
                        var classe = $(this).find('option[selected]').attr('class');

                        if (classe == 'oculta') {
                            $('#periodo_abono').fadeOut();
                        } else if (classe == 'exibe') {

                            $('#periodo_abono').fadeIn();
                        }
                    });




                    $('a.regiao').click(function() {


                        var id_link = $(this).attr('href');

                        if ($('#' + id_link).css('display') == 'none') {


                            $('#' + id_link).show();
                            $('#' + id_link).css('width', '100%');


                            $('#verifica').val(id_link);

                        } else {

                            $('#' + id_link).hide();

                        }


                    });

                });
                /* Exibe e Oculta a Div de Abono
                 $("#quantidade_dias").change(function() {
                 $(this).find('.oculta').hide();
                 
                 $("#periodo_abono").css('display','block');
                 });*/

                // Quando não seleciona um período aquisitivo
                function verifica_nulo() {

                    var d = document.formp;
                    var contaForm = d.elements.length - 3;
                    var Yescheck = 0;
                    var Nocheck = 0;

                    for (i = 0; i <= contaForm; i++) {
                        if (d.elements[i].id == "periodo_aquisitivo") {
                            if (!d.elements[i].checked) {
                                Yescheck++;
                            } else {
                                Nocheck++;
                            }
                        }
                    }

                    if (Nocheck == 0) {
                        alert("Selecione um Período Aquisitivo");
                        return false;
                    }

                    return true;
                }



                // Verifica se a data é válida
                function mascara_data(d) {
                    var mydata = '';
                    data = d.value;
                    mydata = mydata + data;
                    if (mydata.length == 2) {
                        mydata = mydata + '/';
                        d.value = mydata;
                    }
                    if (mydata.length == 5) {
                        mydata = mydata + '/';
                        d.value = mydata;
                    }
                    if (mydata.length == 10) {

                        verifica_data(d);
                    }
                }

                function verifica_data(d) {

                    dia = (d.value.substring(0, 2));
                    mes = (d.value.substring(3, 5));
                    ano = (d.value.substring(6, 10));

                    situacao = "";
                    // verifica o dia valido para cada mes  
                    if ((dia < 01) || (dia < 01 || dia > 30) && (mes == 04 || mes == 06 || mes == 09 || mes == 11) || dia > 31) {
                        situacao = "falsa";
                    }

                    // verifica se o mes e valido  
                    if (mes < 01 || mes > 12) {
                        situacao = "falsa";
                    }

                    // verifica se e ano bissexto  
                    if (mes == 2 && (dia < 01 || dia > 29 || (dia > 28 && (parseInt(ano / 4) != ano / 4)))) {
                        situacao = "falsa";
                    }

                    if (d.value == "") {
                        situacao = "falsa";
                    }

                    if (situacao == "falsa") {
                        alert("Data digitada não valida, digite novamente!");
                        d.value = "";
                        d.focus();
                    }
                }






            </script>
            <style>
                body {
                    background-color:#FAFAFA;
                    text-align:center;
                    margin:0px;
                }

                #corpo {
                    width:90%;
                    background-color:#FFF;
                    border-color:#09F;
                    margin:0px auto;
                    text-align:left;
                    padding-top:20px;
                    padding-bottom:10px;
                }

                .aviso {
                    width:45%;
                    height:auto;
                    margin-left:40px;
                    margin-top:30px;
                    text-align: center;
                    float:left;
                }

                .regiao{
                    background-color:   #F2F9FF;
                    color:#000;
                    text-decoration:none;
                    padding-left:5px;
                    border: 1px solid  #E1E1E1;
                    font-size:18px;

                    font-family:Arial, Helvetica, sans-serif;
                    display:block;
                    cursor:pointer;

                }

                .regiao:hover{
                    background-color:   #E4E4E4;
                }

                .aberto {
                    background-color: #DEF;
                    color:#000;
                    text-decoration:none;
                    padding-left:5px;
                    border: 1px solid  #E1E1E1;
                    font-size:18px;

                    font-family:Arial, Helvetica, sans-serif;
                    display:block;
                    cursor:pointer;
                }


                .titulo{
                    background-color:#C99;
                    font-size:16px;
                    color:#FFF;
                    padding-left: 10px;
                    width:180px;
                    margin:20px auto;
                }



                .aviso fieldset legend{
                    font-size:14px;
                    font-weight:bold;
                }


                .novaintra .aviso fieldset{
                    padding: 10px;
                }

                /*form com filtro da consulta*/
                form.filtro{
                    margin: 10px auto;
                    width: 95%;
                }
            </style>
    </head>
    <body class="novaintra">
        <div id="corpo">
            <?php
// Tela 1 (Seleção de participante)
            switch ($tela) {
                case 1:
                    ?>
                    <div style="float:right">
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
                            F&Eacute;RIAS</div>
                        <div style="float:right; width:25%; text-align:right; font-family:Arial; font-size:12px; color:#333;">
                            <br /><b>Data:</b> <?= date('d/m/Y') ?>&nbsp;
                        </div>
                        <div style="clear:both;"></div>
                    </div>



                    <!--FÉRIAS A VENCER------->
                    <div class="aviso" style="  background-color:#C4E1FF;">
                        <fieldset style="background-color:#C4E1FF;">
                            <legend>F&Eacute;RIAS A VENCER</legend>

                            <?php include("ferias_vencer.php"); ?>


                        </fieldset>
                    </div>



                    <!--FÉRIAS VENCIDAS----------->
                    <div  class="aviso" style=" background-color:#FF7575;">
                        <fieldset style=" background-color:#FF7575;">
                            <legend><span style="color:#FFF1EA;text-weight:bold;">FÉRIAS  VENCIDAS</span> </legend>

                            <?php include("ferias_vencida.php"); ?>
                        </fieldset>
                    </div>


                    <div style="clear:left;"></div>

                    <?php
                    // criar filtro para pesquisa
                    if (isset($_REQUEST['filtro']) && !empty($_REQUEST['filtro'])) {
                        if ($_REQUEST['projeto'] != '-1') {
                            $filtroProjeto = "AND A.id_projeto = {$_REQUEST['projeto']}";
                        }
                        $projetoR = $_REQUEST['projeto'];
                    } else {
                        $filtroProjeto = '';
                    }
                    ?>

                    <!-- form de filtro -->
                    <form action="" method="post" class="filtro">
                        <fieldset>
                            <legend>Filtro</legend>
                            <input type="hidden" name="filtro" value="1" />
                            <p><label class="first">Projeto:</label> <?php echo montaSelect(GlobalClass::carregaProjetosByRegiao($regiao, array("-1" => "Todos")), $projetoR, "id='projeto' name='projeto' class='required[custom[select]]'") ?></p>
                            <p><label class="first"></label><input type="text" name="pesquisa" placeholder="Nome, Matricula, CPF" value="<?php echo $_REQUEST['pesquisa']; ?>"></p>
                            <p class="controls"><input type="submit" value="Consultar" class="button" name="consultar" /><a href="relatorio_ferias.php" style="margin-left: 20px;"><input type="button" value="Relatorio" class="button" name="consultar" /></a></p>
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

                        $total_clt = NULL;
                        $qr_projetos = mysql_query("SELECT A.*, B.cnpj FROM projeto as A
            INNER JOIN rhempresa as B 
            ON (A.id_regiao = B.id_regiao AND B.id_projeto = A.id_projeto)
            WHERE A.id_regiao = '$regiao' $filtroProjeto AND A.status_reg = '1' OR A.status_reg = '0' ORDER BY A.nome ASC");
                        while ($projetos = mysql_fetch_assoc($qr_projetos)) {
                            $REClts = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM rh_clt WHERE id_projeto = '$projetos[id_projeto]' AND id_regiao = '$regiao' AND (status < '60' OR status = '200') $auxPesquisa ORDER BY nome ASC");
                            $numero_clts = mysql_num_rows($REClts);
                            if (!empty($numero_clts)) {
                                $total_clt++;
                                ?>


                                <form name="banco" action="" method="post">
                                <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="exportarExcel"></p>
                                <table id="tbRelatorio" width="95%" border='0' cellpadding='8' cellspacing='0' bgcolor='#f5f5f5' align='center' style="margin-top:20px;">
                                    <tr>
                                        <td colspan="7" class="show">
                                            &nbsp;<span class="seta">&#8250;</span> <?php echo $projetos['nome']; ?> / CNPJ: <?php echo $projetos['cnpj']; ?>
                                        </td>
                                    </tr>
                                    <tr class="novo_tr">
                                        <td></td>
                                        <td width="5%">COD</td>
                                        <td width="35%">NOME</td>
                                        <td>VALOR</td>
                                        <td width="20%" align="center">DATA DE ENTRADA</td>
                                        <td width="20%" align="center">AQUISI&Ccedil;&Atilde;O DE F&Eacute;RIAS</td>
                                        <td width="20%" align="center">VENC. DE F&Eacute;RIAS</td>

                                    </tr>
                                    <?php
                                }

                                while ($row_clt10 = mysql_fetch_array($REClts)) {

                                    $qr_ferias = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$row_clt10[id_clt]' AND status = '1' ORDER BY data_fim DESC");
                                    $ferias = mysql_fetch_assoc($qr_ferias);


                                    if (empty($ferias['data_ini'])) {
                                        $DataEntrada = $row_clt10['data_entrada2'];
                                    } else {
                                        $preview1 = explode('-', $ferias['data_fim']);
                                        $preview2 = $preview1[0];
                                        $preview3 = explode('/', $row_clt10['data_entrada2']);
                                        $DataEntrada = "$preview3[0]/$preview3[1]/$preview2";
                                    }

                                    $DataEntrada = explode('/', $DataEntrada);

                                    $F_ini = date('d/m/Y', mktime(0, 0, 0, $DataEntrada[1] + 12, $DataEntrada[0], $DataEntrada[2]));
                                    $F_ini_E = explode('/', $F_ini);

                                    $F_fim = date('d/m/Y', mktime(0, 0, 0, $F_ini_E[1], $F_ini_E[0] - 1, $F_ini_E[2] + 1));

                                    $result_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_clt10[id_projeto]' AND status_reg = '1' ");
                                    $row_pro = mysql_fetch_array($result_pro);

                                    // Encriptografando a Variável
                                    $link = encrypt("$regiao&2&$row_clt10[0]");
                                    $link2 = str_replace("+", "--", $link);
                                    // -----------------------------
                                    ?>
                                    <tr style="background-color:<?php
                                    if ($alternateColor++ % 2 != 0) {
                                        echo "#F0F0F0";
                                    } else {
                                        echo "#FDFDFD";
                                    }
                                    ?>">
                                        <td><?php
                                            if($row_clt10['conta'] == '' OR $row_clt10['conta'] == '000000' OR $row_clt10['tipo_conta'] == ''){
                                                echo 'SEM CONTA';
                                            }else if($ferias['total_liquido'] != 0.00){ 
                                                if(!array_key_exists($ferias[id_ferias],$arrayArquivos)){
                                                    echo '<input type="checkbox" name="arqFerias[]" checked value="'.$ferias[id_ferias].'">';
                                                }
                                            }
                                        ?></td>
                                        <td><?= $row_clt10[0] ?></td>
                                        <td><a href='index.php?enc=<?= $link2 ?>'><?= $row_clt10['nome'] ?></a>
                                            <?php
                                            if ($row_clt10['status'] == '40') {
                                                echo '<span style="color:#069; font-weight:bold;">(Em Férias)</span>';
                                            } elseif ($row_clt10['status'] == '200') {
                                                echo '<span style="color:red; font-weight:bold;">(Aguardando Demissão)</span>';
                                            }
                                            ?></td>
                                        <td>R$<?php
                                            $total_ferias = $ferias['total_liquido'];
                                            $totalizador_ferias += $total_ferias;
                                            echo number_format($total_ferias, 2, ',', '.');
                                            ?>
                                        </td>
                                        <td align="center" class="style3"><?= $row_clt10['data_entrada2'] ?></td>
                                        <td align="center" class="style3"><?= $F_ini ?></td>
                                        <td align="center" class="style3"><?= $F_fim ?></td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td colspan="9">
                                        Banco: <select name="banco"><?php echo $optionBanco; ?></select>
                                        Data: <input type="text" name="data" >
                                        <input type="submit" value="Gerar Arquivo de Banco">&nbsp;&nbsp;&nbsp;&nbsp;<a href="arquivo_banco_ferias.php">Gerenciar Arquivos</a>
                                    </td>
                                </tr>
                            </table>
                            </form>
                            <?php
                        }
                    }

                    // Se não tem nenhum CLT na região
                    if (empty($total_clt)) {
                        ?>

                                                                                                                        <!--<META HTTP-EQUIV=Refresh CONTENT="2; URL=/intranet/principalrh.php?regiao=<?= $regiao ?>&id=1"/>-->
                        <p style="color:#C30; font-size:12px; font-weight:bold; margin:30px auto; width:50%; text-align:center;">
                            Obs: A região não possui participantes CLTs.
                        </p>

                    <?php } else { ?>
                        <table width="95%" border='0' cellpadding='8' cellspacing='0' bgcolor='#f5f5f5' align='center'>
                            <tr>
                                <td width="5%">&nbsp;</td>
                                <td width="35%" align="right">TOTAL : </td>
                                <td>R$ <?php echo number_format($totalizador_ferias, 2, ',', '.'); ?></td>
                                <td width="20%">&nbsp;</td> 
                                <td width="20%">&nbsp;</td>
                                <td width="20%">&nbsp;</td>
                            </tr>
                        </table>
                        <div style="width:95%; margin:0px auto; font-size:13px; padding-bottom:4px; margin-top:15px; text-align:right;">
                            <a href="#corpo" title="Subir navegação">Subir ao topo</a>
                        </div>

                    <?php } ?>


                    <!----------------------------------REGIÃO 15 --------------------------------------------------------------->
                    <?php
                    if ($regiao == '15') :

                        $status_reg = array(1 => 'Ativas', 2 => 'Inativas');

                        foreach ($status_reg as $chave => $valor) {


                            if ($chave == '1') {
                                $qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = 1 AND status_reg = 1 ORDER BY regiao");
                                ?>
                                <table width="95%" align='center' style="margin-top:5px;">
                                    <tr class="titulo">
                                        <td><strong> Regiões Ativas</strong></td>
                                    </tr>
                                </table>			
                                <?php
                            } else {

                                $qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = 0 OR status_reg = 0 ORDER BY regiao");
                                ?>
                                <table width="95%" align='center' style="margin-top:5px;">
                                    <tr class="titulo">
                                        <td><strong>Regiões Inativas</strong></td>
                                    </tr>
                                </table>			
                                <?php
                            }

                            while ($row_regiao = mysql_fetch_assoc($qr_regioes)):

                                $status++;

                                $mostrar_regiao = 0;

                                $total_clt = NULL;
                                $qr_projetos = mysql_query("SELECT A.*, B.cnpj FROM projeto as A
                                                                    INNER JOIN rhempresa as B 
                                                                    ON (A.id_regiao = B.id_regiao AND B.id_projeto = A.id_projeto)
                                                                    WHERE A.id_regiao = '$regiao' AND A.status_reg = '1' OR A.status_reg = '0' ORDER BY A.nome ASC");
                                while ($projetos = mysql_fetch_assoc($qr_projetos)) {

                                    $REClts = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM rh_clt WHERE id_projeto = '$projetos[id_projeto]' AND id_regiao = '$row_regiao[id_regiao]' AND (status < '60' OR status = '200')  ORDER BY nome ASC");
                                    $numero_clts = mysql_num_rows($REClts);

                                    if (empty($numero_clts))
                                        continue;

                                    if ($mostrar_regiao == 0) {

                                        $mostrar_regiao = 1;
                                        ?>

                                        <table width="95%" align='center' style="margin-top:0px;">
                                            <tr>
                                                <td colspan="7">
                                                    <a href="<?php echo $row_regiao['id_regiao']; ?>" class="regiao" onclick="return false" > <?php echo $row_regiao['regiao']; ?></a>

                                                </td>
                                            </tr>

                                            <tr id="<?php echo $row_regiao['id_regiao']; ?>" style="display:none;" >
                                                <td>


                                                    <?php
                                                }
                                                if (!empty($numero_clts)) {
                                                    $total_clt++;
                                                    ?>

                                                    <table width="100%" border='0' cellpadding='8' cellspacing='0' bgcolor='#f5f5f5' align='center' style="margin-top:10px; ">
                                                        <tr>
                                                            <td colspan="7" class="show" >
                                                                &nbsp;<span class="seta">&#8250;</span> <?php echo $projetos['nome']; ?> / CNPJ:  <?php echo $projetos['cnpj']; ?> 
                                                            </td>
                                                        </tr>
                                                        <tr class="novo_tr">
                                                            <td width="5%">COD</td>
                                                            <td width="35%">NOME</td>
                                                            <td width="20%" align="center">DATA DE ENTRADA</td>
                                                            <td width="20%" align="center">AQUISI&Ccedil;&Atilde;O DE F&Eacute;RIAS</td>
                                                            <td width="20%" align="center">VENC. DE F&Eacute;RIAS</td>
                                                        </tr>
                                                        <?php
                                                    }

                                                    while ($row_clt10 = mysql_fetch_array($REClts)) {

                                                        $qr_ferias = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$row_clt10[id_clt]' AND status = '1'");
                                                        $ferias = mysql_fetch_assoc($qr_ferias);

                                                        if (empty($ferias['data_ini'])) {
                                                            $DataEntrada = $row_clt10['data_entrada2'];
                                                        } else {
                                                            $preview1 = explode('-', $ferias['data_fim']);
                                                            $preview2 = $preview1[0];
                                                            $preview3 = explode('/', $row_clt10['data_entrada2']);
                                                            $DataEntrada = "$preview3[0]/$preview3[1]/$preview2";
                                                        }

                                                        $DataEntrada = explode('/', $DataEntrada);

                                                        $F_ini = date('d/m/Y', mktime(0, 0, 0, $DataEntrada[1] + 12, $DataEntrada[0], $DataEntrada[2]));
                                                        $F_ini_E = explode('/', $F_ini);

                                                        $F_fim = date('d/m/Y', mktime(0, 0, 0, $F_ini_E[1], $F_ini_E[0] - 1, $F_ini_E[2] + 1));

                                                        $result_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_clt10[id_projeto]' AND status_reg = '1'");
                                                        $row_pro = mysql_fetch_array($result_pro);

                                                        // Encriptografando a Variável
                                                        $link = encrypt("$regiao&2&$row_clt10[0]");
                                                        $link2 = str_replace("+", "--", $link);
                                                        // -----------------------------
                                                        ?>

                                                        <tr style="background-color:<?php
                                                        if ($alternateColor++ % 2 != 0) {
                                                            echo "#F0F0F0";
                                                        } else {
                                                            echo "#FDFDFD";
                                                        }
                                                        ?>">
                                                            <td><?= $row_clt10[0] ?> </td>
                                                            <td><a href='index.php?enc=<?= $link2 ?>'><?= $row_clt10['nome'] ?></a>
                                                                <?php
                                                                if ($row_clt10['status'] == '40') {
                                                                    echo '<span style="color:#069; font-weight:bold;">(Em Férias)</span>';
                                                                } elseif ($row_clt10['status'] == '200') {
                                                                    echo '<span style="color:red; font-weight:bold;">(Aguardando Demissão)</span>';
                                                                }
                                                                ?></td>
                                                            <td align="center" class="style3"><?= $row_clt10['data_entrada2'] ?></td>
                                                            <td align="center" class="style3"><?= $F_ini ?></td>
                                                            <td align="center" class="style3"><?= $F_fim ?></td>
                                                        </tr>
                                                    <?php } //CLT    ?>

                                                    <tr style="backgorund-color:#FFF;">
                                                        <td colspan="7">&nbsp;</td>
                                                    </tr>        

                                                </table>
                                            <?php } //PROJETO
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                                <?php
                            endwhile;
                            echo ' <div style="width:auto; height:50px;"></div>';
                        }//FIM LOOP


                    endif; //FIM REGIAO 15
                    ?>
                    <!----------------------------------FIM  REGIÃO 15 --------------------------------------------------------------->




                    <?php
// Tela 2 (Movimentos e Histórico de Férias)
                    break;
            }
            ?>
        </div>
    </body>
</html>