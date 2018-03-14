<?php
/**
 * index.php
 * 
 * 00-00-0000
 * 
 * Rotina para processamento das férias
 * 
 * Versï¿½o: 3.0.0001 - 02/09/2015 - Jacques - Implementada a carga de algumas variï¿½veis com tipo pre-definido e flag para processamento de fï¿½rias em lote
 *                                             Alterado todo $_POST para $_REQUEST a fim de receber variï¿½veis tambï¿½m do ferias_em_lote_real.php
 * Versï¿½o: 3.0.0002 - 07/12/2015 - Jacques - Adicionado a possibilidade de ignorar as fï¿½rias dobradas atravï¿½z de um checkbox
 * Versï¿½o: 3.0.0003 - 14/12/2015 - Jacques - Acerto na label 'Considerar Perï¿½odo de Abono:' que nï¿½o estava aparecendo
 * 
 * @author Nï¿½o definido 
 * 
 */

/*
 * @jacques
 * Inicializaï¿½ï¿½o de todas variï¿½veis que sï¿½o utilizadas no INSERT do processamento de fï¿½rias em lote
 */
$clt = 0;
$nome = '';
$regiao = 0;
$projeto = 0;
$mesE = 0;
$anoE = 0;
$aquisito_ini = '';
$aquisito_end = '';
$data_inicio = '';
$data_fim = '';
$data_retorno = '';
$salario = 0;
$salario_variavel = 0;
$remuneracao_base = 0;
$dias_ferias = 0;
$valor_dia = 0;
$valor_total = 0;
$um_terco = 0;
$total_remuneracoes = 0;
$pensao_alimenticia = 0;
$inss = 0;
$porcentagem_inss = 0;
$ir = 0;
$fgts = 0;
$total_descontos = 0;
$total_liquido = 0;
$abono_pecuniario = 0;
$umterco_abono_pecuniario = 0;
$dias_abono_pecuniario = 0;
$faltas = 0;
$dias_mes = 0;
$ferias_dobradas = '';
$valor_total1 = 0;
$valor_total2 = 0;
$acrescimo_constitucional1 = 0;
$acrescimo_constitucional2 = 0;
$total_remuneracoes1 = 0;
$total_remuneracoes2 = 0;
$update_movimentos_clt = '0';
$logado = 0;
$base_inss = 0;
$base_irrf = 0;
$percentual_irrf = 0;
$valor_ddir = 0;
$qnt_dependentes_irrf = 0;
$parcela_deducao_irrf = 0;
$total_movimentos = 0;


/*
 * Carga de variï¿½veis para mï¿½todo POST e GET
 */
$regiao = (int) $_REQUEST['regiao'];
$projeto = (int) $_REQUEST['projeto'];
$ferias_lote = (int) $_REQUEST['ferias_lote'];
$clt = (int) $_REQUEST['clt'];
$data_inicio = (string) $REQUEST['data_inicio'];
$dias_ferias = (int) $_REQUEST['quantidade_dias'];
$nome = (string) $_REQUEST['nome'];
$id = (int) $_REQUEST['id'];
$logado = (int) $_COOKIE['logado'];



if (empty($logado) && !$ferias_lote) {
    print 'Efetue o Login<br/><a href="../login.php">Logar</a>';
    exit;
}

include('../../conn.php');
include('../../funcoes.php');
include('../../classes/clt.php');
include('../../classes/calculos.php');
include('../../classes/global.php');
include('../../classes/CalculoFeriasClass.php');
include('../../wfunction.php');
include('../../classes_permissoes/acoes.class.php');

$ACOES = new Acoes();
$objCalcFerias = new Calculo_Ferias();
$usuario = carregaUsuario();


$tetoInss = 513.01;


$regiao = (!empty($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];

if (empty($_REQUEST['enc'])) {
    $tela = (!empty($_REQUEST['tela'])) ? $_REQUEST['tela'] : 1;
} else {
    $enc = str_replace('--', '+', $_REQUEST['enc']);
    $link = decrypt($enc);
    list($regiao, $tela, $clt, $id_ferias) = explode('&', $link);
}

function getDiasLicenca($dataInicio, $dataFinal, $id_clt = null) {

    $dataIni = new DateTime($dataInicio);
    $dataFim = new DateTime($dataFinal);
    $auxClt = (!empty($id_clt)) ? " AND id_clt = '$id_clt' " : $id_clt;
    $sql = "SELECT data, data_retorno, dias
    FROM rh_eventos
    WHERE status = 1 AND cod_status IN (20,70) AND (data >= '$dataInicio' AND data <= '$dataFinal' OR data_retorno <= '$dataInicio' AND data_retorno >= '$dataFinal') $auxClt";
//    if($_COOKIE['logado'] == 35) {echo "<br/><br/>".$sql."<br/><br/>";}
    $sql = mysql_query($sql) or die(mysql_error());
    while ($row = mysql_fetch_assoc($sql)) {

        $dataIniEvento = new DateTime($row['data']);
        $dataFimEvento = new DateTime($row['data_retorno']);

        $diasTemp = $row['dias'];

        if ($dataIniEvento < $dataIni) {
            $diff = $dataIniEvento->diff($dataIni);
            $diasTemp -= $diff->days;
        }
        if ($dataFimEvento > $dataFim) {
            $diff = $dataFimEvento->diff($dataFim);
            $diasTemp -= $diff->days;
        }

        $dias += $diasTemp;
    }
//    if($_COOKIE['logado'] == 35) {echo "<br/><br/>".$dias."<br/><br/>";}
    return $dias;
}

function verificaValorNegativo($valor) {
    if ($valor < 0) {
        return 0;
    } else {
        return $valor;
    }
}

if ($_GET['deletar'] == true) {
    $movimentos = mysql_result(mysql_query("SELECT movimentos FROM rh_ferias WHERE id_ferias = '" . $_GET['id'] . "' LIMIT 1"), 0);
    $total_movimentos = (int) count(explode(',', $movimentos));
    mysql_query("UPDATE rh_ferias SET status = '0' WHERE id_ferias = '" . $_GET['id'] . "' LIMIT 1");
    mysql_query("UPDATE rh_movimentos_clt SET status_ferias = '1' WHERE id_movimento IN('" . $movimentos . "') LIMIT " . $total_movimentos . "");
    mysql_query("UPDATE rh_clt SET status = 10  WHERE id_clt = '$_GET[id_clt]' LIMIT 1");
}

$meses = array('', '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Mar&ccedil;o', '04' => 'Abril', '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro');
if (!$ferias_lote) {
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
                <script type="text/javascript" src="../../js/jquery-1.8.3.min.js"></script>
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

                    $(function () {
                        // Calendï¿½rio
                        $('#data_inicio').datepicker({
                            changeMonth: true,
                            changeYear: true,
                            nextText: '>',
                            prevText: '<'
                        });
                        // Exibe e Oculta a Div de Histï¿½rico
                        $("#ver_historico").click(function () {
                            $('#historico').toggle('fast');
                        });


                        $('select[name*=quantidade_dias]').change(function () {
                            var classe = $(this).find('option[selected]').attr('class');

                            if (classe == 'oculta') {
                                $('#periodo_abono').fadeOut();
                            } else if (classe == 'exibe') {

                                $('#periodo_abono').fadeIn();
                            }
                        });


                        $('a.regiao').click(function () {


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

                    // Quando nï¿½o seleciona um perï¿½odo aquisitivo
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
                            alert("Selecione um Perï¿½odo Aquisitivo");
                            return false;
                        }

                        return true;
                    }



                    // Verifica se a data ï¿½ vï¿½lida
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
                            alert("Data digitada nï¿½o valida, digite novamente!");
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

                    .ferias_col{
                        float: right;
                        background: #CCC;
                        padding: 10px;
                        margin: 21px 0 0 0;
                        text-decoration: none;
                        color: #333;
                    }

                    .ferias_col:hover{
                        background: #eee;
                    }
                </style>
        </head>
        <body class="novaintra">
            <div id="corpo">
                <?php
            }
// Tela 1 (Seleï¿½ï¿½o de participante)
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
                            <br/><b>Data:</b> <?= date('d/m/Y') ?>&nbsp;
                        </div>
                        <div style="clear:both;"></div>
                        <a href="ferias_em_lote.php" target="_blank" class="ferias_col">Fï¿½rias Coletivas</a>
                        <a href="ferias_em_lote_real.php" target="_blank" class="ferias_col" style="margin-right:10px;">Fï¿½rias Em Lote</a>
                    </div>



                    <!--Fï¿½RIAS A VENCER------->
                    <div class="aviso" style="  background-color:#C4E1FF;">
                        <fieldset style="background-color:#C4E1FF;">
                            <legend>F&Eacute;RIAS A VENCER NOS PRï¿½XIMOS 90 DIAS</legend>

                            <?php include("ferias_vencer.php"); ?>


                        </fieldset>
                    </div>



                    <!--Fï¿½RIAS VENCIDAS----------->
                    <div  class="aviso" style=" background-color:#FF7575;">
                        <fieldset style=" background-color:#FF7575;">
                            <legend><span style="color:#FFF1EA;text-weight:bold;">Fï¿½RIAS  VENCIDAS</span> </legend>

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

                        if (!empty($_REQUEST['pesquisa'])) {
                            $valorPesquisa = explode(' ', $_REQUEST['pesquisa']);
                            foreach ($valorPesquisa as $valuePesquisa) {
                                $pesquisa[] .= "A.nome LIKE '%" . $valuePesquisa . "%'";
                            }
                            $pesquisa = implode(' AND ', $pesquisa);
                            $auxPesquisa = " AND (($pesquisa) OR (CAST(matricula AS CHAR) = '{$_REQUEST['pesquisa']}') OR (REPLACE(REPLACE(cpf, '.', ''), '-', '') = '{$_REQUEST['pesquisa']}' OR cpf = '{$_REQUEST['pesquisa']}'))";
                        }

                        $total_clt = NULL;
                        $qr_projetos = mysql_query("SELECT A.*, B.cnpj FROM projeto as A
            INNER JOIN rhempresa as B 
            ON (A.id_regiao = B.id_regiao AND B.id_projeto = A.id_projeto)
            WHERE A.id_regiao = '$regiao' $filtroProjeto AND A.status_reg = '1' OR A.status_reg = '0' ORDER BY A.nome ASC");
                        while ($projetos = mysql_fetch_assoc($qr_projetos)) {

//                            if($_COOKIE['logado'] == 40){
//                                echo "SELECT A.*, date_format(A.data_entrada, '%d/%m/%Y') AS data_entrada2,
//                                                date_format(A.data_saida, '%d/%m/%Y') AS data_saida2 FROM rh_clt AS A
//                                                LEFT JOIN (SELECT * FROM rh_recisao WHERE status = 1) AS B ON(A.id_clt = B.id_clt)
//                                                WHERE A.id_projeto = '$projetos[id_projeto]' AND A.id_regiao = '$regiao' 
//                                                AND (A.status < '60' OR A.status = '200') AND B.id_recisao IS NULL $auxPesquisa ORDER BY A.nome ASC";
//                            }

                            $REClts = mysql_query("SELECT A.*, date_format(A.data_entrada, '%d/%m/%Y') AS data_entrada2,
                                                date_format(A.data_saida, '%d/%m/%Y') AS data_saida2 FROM rh_clt AS A
                                                LEFT JOIN (SELECT * FROM rh_recisao WHERE status = 1) AS B ON(A.id_clt = B.id_clt)
                                                WHERE A.id_projeto = '$projetos[id_projeto]' AND A.id_regiao = '$regiao' 
                                                AND (A.status < '60' OR A.status = '200') AND B.id_recisao IS NULL $auxPesquisa ORDER BY A.nome ASC");
                            $numero_clts = mysql_num_rows($REClts);
                            if (!empty($numero_clts)) {
                                $total_clt++;
                                ?>




                                <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Relatï¿½rio')" value="Exportar para Excel" class="exportarExcel"></p>
                                <table id="tbRelatorio" width="95%" border='0' cellpadding='8' cellspacing='0' bgcolor='#f5f5f5' align='center' style="margin-top:20px;">
                                    <tr>
                                        <td colspan="7" class="show">
                                            &nbsp;<span class="seta">&#8250;</span> <?php echo $projetos['nome']; ?> / CNPJ: <?php echo $projetos['cnpj']; ?>
                                        </td>
                                    </tr>
                                    <tr class="novo_tr">
                                        <td width="5%">COD</td>
                                        <td width="35%">NOME</td>
                                        <td width="25%">FUNï¿½ï¿½O</td>
                                        <td width="15%">SALï¿½RIO BASE</td>
                                        <td>VALOR</td>
                                        <td width="20%" align="center">DATA DE ENTRADA</td>
                                        <td width="20%" align="center">AQUISI&Ccedil;&Atilde;O DE F&Eacute;RIAS</td>
                                        <td width="20%" align="center">VENC. DE F&Eacute;RIAS</td>

                                    </tr>
                                    <?php
                                }


                                while ($row_clt10 = mysql_fetch_array($REClts)) {

                                    $qr_ferias = mysql_query("SELECT A.*, C.nome as nome_funcao
                                                    FROM rh_ferias AS A
                                                    LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
                                                    LEFT JOIN curso AS C ON(B.id_curso = C.id_curso)
                                                    WHERE A.id_clt = '$row_clt10[id_clt]' AND A.status = '1' ORDER BY data_fim DESC");
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

                                    // Encriptografando a Variï¿½vel
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
                                        <td><?= $row_clt10[0] ?></td>
                                        <?php
                                        //08/09/2015
                                        $link_nome = "";
                                        if ($row_clt10['status'] < 60 && $row_clt10['status'] != 10 && $row_clt10['status'] != 40) {
                                            $link_nome = $row_clt10['nome'] . " (Em Evento) ";
                                        } else {
                                            $link_nome = "<a href='index.php?enc={$link2}'>" . $row_clt10['nome'] . "</a>";
                                        }
                                        ?>
                                        <td>
                                            <?php echo $link_nome; ?>
                                            <?php
                                            if ($row_clt10['status'] == '40') {
                                                echo '<span style="color:#069; font-weight:bold;">(Em Fï¿½rias)</span>';
                                            } elseif ($row_clt10['status'] == '200') {
                                                echo '<span style="color:red; font-weight:bold;">(Aguardando Demissï¿½o)</span>';
                                            }
                                            ?></td>
                                        <td><?php echo $ferias['nome_funcao']; ?></td>
                                        <td><?php echo number_format($ferias['salario'], "2", ',', "."); ?></td>
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

                            </table>
                            <?php
                        }
                    }

                    // Se nï¿½o tem nenhum CLT na regiï¿½o
                    if (empty($total_clt)) {
                        ?>

                                                <!--<META HTTP-EQUIV=Refresh CONTENT="2; URL=/intranet/principalrh.php?regiao=<?= $regiao ?>&id=1"/>-->
                        <p style="color:#C30; font-size:12px; font-weight:bold; margin:30px auto; width:50%; text-align:center;">
                            Obs: A regiï¿½o nï¿½o possui participantes CLTs.
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
                            <a href="#corpo" title="Subir navegaï¿½ï¿½o">Subir ao topo</a>
                        </div>

                    <?php } ?>


                    <!----------------------------------REGIï¿½O 15 --------------------------------------------------------------->
                    <?php
                    if ($regiao == '15') :

                        $status_reg = array(1 => 'Ativas', 2 => 'Inativas');

                        foreach ($status_reg as $chave => $valor) {


                            if ($chave == '1') {
                                $qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = 1 AND status_reg = 1 ORDER BY regiao");
                                ?>
                                <table width="95%" align='center' style="margin-top:5px;">
                                    <tr class="titulo">
                                        <td><strong> Regiï¿½es Ativas</strong></td>
                                    </tr>
                                </table>			
                                <?php
                            } else {

                                $qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = 0 OR status_reg = 0 ORDER BY regiao");
                                ?>
                                <table width="95%" align='center' style="margin-top:5px;">
                                    <tr class="titulo">
                                        <td><strong>Regiï¿½es Inativas</strong></td>
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

                                                        // Encriptografando a Variï¿½vel
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
                                                                    echo '<span style="color:#069; font-weight:bold;">(Em Fï¿½rias)</span>';
                                                                } elseif ($row_clt10['status'] == '200') {
                                                                    echo '<span style="color:red; font-weight:bold;">(Aguardando Demissï¿½o)</span>';
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
                    <!----------------------------------FIM  REGIï¿½O 15 --------------------------------------------------------------->




                    <?php
// Tela 2 (Movimentos e Histï¿½rico de Fï¿½rias)
                    break;
                case 2:

                    $result_clt = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM rh_clt WHERE id_clt = '$clt'");
                    $row_clt = mysql_fetch_array($result_clt);

// Encriptografando a Variï¿½vel
                    $tela = 3;
                    $link = encrypt("$regiao&$tela&$clt");
                    $link = str_replace("+", "--", $link);
                    $link2 = encrypt("$regiao&$clt");
                    $link2 = str_replace("+", "--", $link2);
                    $tela3 = 1;
                    $link3 = encrypt("$regiao&$tela3&$clt");
                    $link3 = str_replace("+", "--", $link3);

// Informaï¿½ï¿½es do CLT
                    $Clt = new clt();
                    $Clt->MostraClt($clt);
                    $data_entrada = $Clt->data_entrada;
                    $id_clt = $Clt->id_clt;

                    $objCalcFerias->setIdClt($id_clt);


                    if ($id_clt == 53530) {
                        $adiantaPeriodo = 2;
                    } else {
                        $adiantaPeriodo = 1;
                    }
                    
                    if ($id_clt == 55011) {
                        $adiantaPeriodo = 2;
                    } else {
                        $adiantaPeriodo = 1;
                    }
                    


//                        $periodos_gozados = $objCalcFerias->getPeriodosGozados();
//                        $periodos_disponiveis = $objCalcFerias->getPeriodoAquisitivo($data_entrada, $periodos_gozados, $adiantaPeriodo);
//                        $qrFerias = $objCalcFerias->getFeriasPorClt();
                    $periodos_gozados = $objCalcFerias->getPeriodosGozados();
                    $periodos_gozados2 = $objCalcFerias->getPeriodosGozados2();
                    $periodos_disponiveis = $objCalcFerias->getPeriodoAquisitivo($data_entrada, $periodos_gozados, $adiantaPeriodo, $periodos_gozados2);
                    
                    $qrFerias = $objCalcFerias->getFeriasPorClt();
                    ?>


                    <table cellpadding='8' cellspacing='8' align='center' style="border:1px solid #ddd; border-radius:10px; -moz-border-radius:10px; background-color:#f5f5f5;">
                        <tr>
                            <td align="right"><?php include('../../reportar_erro.php'); ?></td>
                        </tr>
                        <tr>
                            <td>
                                <div id="tela2">

                                    <span style="font-size:10px;">
                                        <?= $clt . ' - ' . $row_clt['nome'] ?>
                                    </span>


                                    <?php
                                    // Se nï¿½o tem perï¿½odos disponiveis e nï¿½o tem histï¿½rico de fï¿½rias
                                    if (count($periodos_disponiveis) == 0 and count($periodos_gozados) == 0) {
                                        ?>
                        <!--  <META HTTP-EQUIV=Refresh CONTENT="1; URL=/intranet/rh/ferias/index.php?enc=<?= $link3 ?>">-->
                                        <p style="color:#C30; font-size:12px; font-weight:bold; margin-top:10px;">
                                            Obs: candidato(a) n&atilde;o possui per&iacute;odo aquisitivo a fï¿½rias
                                        </p>

                                        <?php
                                        // Se nï¿½o tem perï¿½odos disponiveis mas tem histï¿½rico de fï¿½rias
                                    } elseif (count($periodos_disponiveis) == 0 and count($periodos_gozados) > 0) {
                                        ?>
                                        <p style="color:#C30; font-size:12px; font-weight:bold; margin-top:10px; margin-bottom:10px;">
                                            Obs: candidato(a) n&atilde;o possui per&iacute;odo aquisitivo a fï¿½rias
                                        </p>
                                        <a class="botao" style="margin:10px auto;" href="index.php?enc=<?= $link3 ?>">Voltar</a>
                                        <a id="ver_historico" class="botao" style="margin:10px auto;" href="#">Ver hist&oacute;rico de f&eacute;rias</a>

                                        <?php
                                        // Se tem periodos disponiveis
                                    } else {
                                        ?> 

                                        <p>&nbsp;</p>
                                        Jï¿½ lan&ccedil;ou os movimentos do candidato neste mï¿½s?
                                        <p>&nbsp;</p>

                                        <a class="botao" style="margin:10px auto;" href="index.php?enc=<?= $link ?>">Sim, prosseguir</a>
                                        <a class="botao" style="margin:10px auto;" href="../rh_movimentos.php?tela=2&ferias=true&enc=<?= $link2 ?>">Nï¿½o, inserir movimentos</a>
                                        <a class="botao" style="margin:10px auto;" href="../../rh_novaintra/ferias/">Cancelar</a>

                                        <?php if (count($periodos_gozados) > 0) { ?>
                                            <a id="ver_historico" class="botao" style="margin:10px auto;" href="#">Ver hist&oacute;rico de f&eacute;rias</a>
                                        <?php } ?>

                                        <?php
                                    } // Fim das condiï¿½ï¿½es de subtelas  


                                    if ($qrFerias['total_registro'] != 0) {
                                        ?>
                                        <div id="historico" style="border:1px solid #ddd; border-radius:10px; -moz-border-radius:10px; background-color:#eee; display:none; padding:10px;">
                                            <?php
                                            //  while ($historico = mysql_fetch_assoc($qr_historico)) {

                                            foreach ($qrFerias['registros'] as $historico) {
                                                $margem++;
                                                $id_ferias = $historico['id_ferias'];
                                                $mes = $historico['mes'];
                                                $ano = $historico['ano'];
                                                $data_aquisitivo_inicio = $historico['data_aquisitivo_iniBR'];
                                                $data_aquisitivo_fim = $historico['data_aquisitivo_fimBR'];
                                                $data_ferias_inicio = $historico['data_iniBR'];
                                                $data_ferias_fim = $historico['data_fimBR'];
                                                $data_publicacao = $historico['data_procBR'];
                                                $autor = $historico['nome_usuario'];
                                                // Encriptografando a Variï¿½vel
                                                $link_relatorio = encrypt("$regiao&$id_clt&$id_ferias");
                                                $link_relatorio = str_replace('+', '--', $link_relatorio);
                                                // --------------------------- 
                                                ?>
                                                <table cellspacing="0" cellpadding="2" align="center" style="font-size:12px; width:70%; <?php
                                                if ($margem != $qrFerias['total_registro']) {
                                                    echo 'margin-bottom:20px;';
                                                }
                                                ?>">
                                                    <tr>
                                                        <td rowspan="3">
                                                            <a href="ferias.php?enc=<?= $link_relatorio ?>" title="Gerar Relatï¿½rios">
                                                                <img src="../../imagens/pdf.gif" alt="Gerar Relatï¿½rios">
                                                            </a>
                                                        </td>
                                                        <td colspan="2"><?php echo '(' . $id_ferias . ') ' . $meses[$mes] . ' / ' . $ano; ?></td>
                                                        <td rowspan="3">
                                                            <?php if ($ACOES->verifica_permissoes(86)) { ?>
                                                                <a href="index.php?deletar=true&id=<?php echo $id_ferias; ?>&enc=<?php echo $_GET['enc']; ?>&id_clt=<?php echo $id_clt; ?>" title="Desprocessar Fï¿½rias" onclick="return window.confirm('Vocï¿½ tem certeza que quer desprocessar esta fï¿½rias?');"><img src="../imagensrh/deletar.gif" /></a>
                <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="110"><b>Perï¿½odo Aquisitivo:</b></td>
                                                        <td><?= $data_aquisitivo_inicio ?> <i>ï¿½</i> <?= $data_aquisitivo_fim ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="110"><b>Perï¿½odo de Fï¿½rias:</b></td>
                                                        <td><?= $data_ferias_inicio ?> <i>ï¿½</i> <?= $data_ferias_fim ?></td>
                                                    </tr>
                                                </table>
                                        <?php } ?>
                                        </div>
        <?php } ?>
                                </div>
                            </td>
                        </tr>
                    </table>


                    <?php
// Tela 3 (Perï¿½odos Aquisitivos)
                    break;
                case 3:

                    $result_clt = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM rh_clt WHERE id_clt = '$clt'");
                    $row_clt = mysql_fetch_array($result_clt);

                    // Encriptografando a Variï¿½vel
                    $tela = 4;
                    $link = encrypt("$regiao&$tela&$clt");
                    $link = str_replace("+", "--", $link);
                    $tela2 = 2;
                    $link2 = encrypt("$regiao&$tela2&$clt");
                    $link2 = str_replace("+", "--", $link2);

                    // Informaï¿½ï¿½es do CLT
                    $Clt = new clt();
                    $Clt->MostraClt($clt);
                    $data_entrada = $Clt->data_entrada;
                    $id_clt = $Clt->id_clt;

                    if ($id_clt == 53530) {
                        $adiantaPeriodo = 2;
                    } else {
                        $adiantaPeriodo = 1;
                    }
                    
                    if ($id_clt == 55011) {
                        $adiantaPeriodo = 2;
                    } else {
                        $adiantaPeriodo = 1;
                    }




                    $objCalcFerias->setIdClt($id_clt);

                    $periodos_gozados = $objCalcFerias->getPeriodosGozados();
                    $periodos_gozados2 = $objCalcFerias->getPeriodosGozados2();
                    $periodos_disponiveis = $objCalcFerias->getPeriodoAquisitivo($data_entrada, $periodos_gozados, $adiantaPeriodo, $periodos_gozados2);
                    $qrFerias = $objCalcFerias->getFeriasPorClt();

                    if ($_COOKIE['logado'] == 40) {
                        echo "<pre>";
                        print_r($periodos_disponiveis);
                        echo "</pre>";
                    }
                    ?>

                    <table border='0' cellpadding='8' cellspacing='8' bgcolor='#f5f5f5' align='center' style="border:1px solid #ddd; border-radius:10px; -moz-border-radius:10px;">
                        <tr>
                            <td align="right"><?php include('../../reportar_erro.php'); ?></td>
                        </tr>
                        <tr>
                            <td>
                                <div id="tela2">

                                    <span style="font-size:10px;">  <?= $clt . ' - ' . $row_clt['nome'] ?></span>
                                    <p>&nbsp;</p>
                                    Selecione um Perï¿½odo Aquisitivo:
                                    <p>&nbsp;</p>
                                    <form action="index.php" method="get" onSubmit="return verifica_nulo()" name="formp" id="formp">
                                        <?php
                                        foreach ($periodos_disponiveis as $periodo) {

                                            $data_explode = explode("-", $periodo);
                                            $data_inicio = explode("/", $data_explode[0]);
                                            $d_inicio = $data_inicio[2] . "-" . $data_inicio[1] . "-" . $data_inicio[0];

                                            $data_fim = explode("/", $data_explode[1]);
                                            $d_fim = $data_fim[2] . "-" . $data_fim[1] . "-" . $data_fim[0];

                                            $periodoAquisitivo = $d_inicio . '/' . $d_fim;
                                            $checked = (($_GET['periodo_aquisitivo'] == $periodoAquisitivo)) ? 'checked' : '';
                                            ?>  
                                            <label style="font-weight:normal; margin-bottom:5px;">
                                                <input type="radio" name="periodo_aquisitivo" id="periodo_aquisitivo" <?= (getDiasLicenca(str_replace(' ', '', $d_inicio), str_replace(' ', '', $d_fim), $row_clt['id_clt']) >= 180) ? 'disabled' : null ?> value="<?php echo $periodoAquisitivo ?>" <?php echo $checked ?>>
                                            <?php echo $periodo; //echo converteData($periodo['inicio'],'d/m/Y').' - '.converteData($periodo['fim'],'d/m/Y')  ?>
                                            </label>
                                            <br/>
        <?php } ?>

                                        <p>&nbsp;</p>
                                        <input type="submit" value="Prosseguir" class="botao" style="margin:10px auto;">
                                            <input type="button" value="Voltar" onClick="javascript:location.href = 'index.php?enc=<?= $link2 ?>'" class="botao" style="margin:10px auto;">
                                                <input type="hidden" name="enc" value="<?= $link ?>">
                                                    <input type="hidden" name="projeto" value="<?= $row_clt['id_projeto'] ?>" />
                                                    </form>
                                                    </div>
                                                    </td>
                                                    </tr>
                                                    </table>

                                                    <?php
// Tela 4 (Data da entrada de fï¿½rias e Quantidade de dias das fï¿½rias)
                                                    break;
                                                case 4:

                                                    $result_clt = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y')as data_entrada2, date_format(data_saida, '%d/%m/%Y')as data_saida2 FROM rh_clt WHERE id_clt = '$clt'");
                                                    $row_clt = mysql_fetch_array($result_clt);

// Encriptografando a Variï¿½vel
                                                    $tela = 5;
                                                    $link = encrypt("$regiao&$tela&$clt");
                                                    $link = str_replace("+", "--", $link);
                                                    $tela2 = 3;
                                                    $link2 = encrypt("$regiao&$tela2&$clt");
                                                    $link2 = str_replace("+", "--", $link2);

// Informaï¿½ï¿½es do CLT
                                                    $Clt = new clt();
                                                    $Clt->MostraClt($clt);
                                                    $data_entrada = $Clt->data_entrada;
                                                    $id_clt = $Clt->id_clt;

// Verificando o Perï¿½odo Aquisitivo
                                                    $periodo_aquisitivo = explode('/', $_REQUEST['periodo_aquisitivo']);
                                                    $aquisitivo_ini = $periodo_aquisitivo[0];
                                                    $aquisitivo_end = $periodo_aquisitivo[1];
                                                    $preview_ini = explode('-', $aquisitivo_ini);
                                                    $preview_fim = explode('-', $aquisitivo_end);
                                                    $dia_ini = $preview_ini[2];
                                                    $mes_ini = $preview_ini[1];
                                                    $ano_ini = $preview_ini[0];
                                                    $dia_fim = $preview_fim[2];
                                                    $mes_fim = $preview_fim[1];
                                                    $ano_fim = $preview_fim[0];

                                                    if ($id_clt == 53161 || $id_clt == 53876 || $id_clt == 53206 || $id_clt == 53996) {
                                                        $data_limite = date('d/m/Y', mktime('0', '0', '0', $mes_fim, $dia_fim - 1, $ano_fim + 2));
                                                        $data_dobrada = date('d/m/Y', mktime('0', '0', '0', $mes_fim, $dia_fim, $ano_fim + 2));
                                                    } else {
                                                        $data_limite = date('d/m/Y', mktime('0', '0', '0', $mes_fim, $dia_fim - 1, $ano_fim + 1));
                                                        $data_dobrada = date('d/m/Y', mktime('0', '0', '0', $mes_fim, $dia_fim, $ano_fim + 1));
                                                    }

                                                    $data_corrente_real = implode('/', array_reverse(explode('-', $aquisitivo_end)));

                                                    if (!empty($_GET['data_inicio'])) {
                                                        $data_corrente = $_GET['data_inicio'];
                                                    } else {
                                                        $data_corrente = implode('/', array_reverse(explode('-', $aquisitivo_end)));
                                                    }

                                                    // Buscando Faltas
                                                    $falta_aquisitivo_ini = explode('-', $aquisitivo_ini);
                                                    $falta_aquisitivo_end = explode('-', $aquisitivo_end);

                                                    if ($falta_aquisitivo_ini[1] == 12) {
                                                        $limite_falta1 = "mes_mov = '$falta_aquisitivo_ini[1]'";
                                                    } else {
                                                        $limite_falta1 = "mes_mov >= '$falta_aquisitivo_ini[1]'";
                                                    }

                                                    if ($falta_aquisitivo_end[1] == 1) {
                                                        $limite_falta2 = "mes_mov = '$falta_aquisitivo_ini[1]'";
                                                    } else {
                                                        $limite_falta2 = "mes_mov <= '$falta_aquisitivo_ini[1]'";
                                                    }

                                                    $data_ini_aquisitivo = str_replace(" ", "", $falta_aquisitivo_ini[0]) . '-' . str_replace(" ", "", $falta_aquisitivo_ini[1]);
                                                    $data_fin_aquisitivo = str_replace(" ", "", $falta_aquisitivo_end[0]) . '-' . str_replace(" ", "", $falta_aquisitivo_end[1]);

                                                    $qr_faltas1 = mysql_query("SELECT SUM(qnt) AS faltas 
                                                                               FROM rh_movimentos_clt
                                                                               WHERE id_clt = '$clt' AND id_mov IN (62,293) AND status=5 AND CONCAT(ano_mov,'-',lpad(mes_mov, 2, '0')) BETWEEN '{$data_ini_aquisitivo}' AND '{$data_fin_aquisitivo}'");
                                                                               

                                                    $row_faltas1 = mysql_fetch_array($qr_faltas1);

                                                    if ($_COOKIE['logado'] == 40) {
//        echo "SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt
//            WHERE id_clt = '$clt' AND id_mov = '62' AND status IN(1,5) 
//            AND CONCAT(ano_mov,'-',lpad(mes_mov, 2, '0')) BETWEEN '{$data_ini_aquisitivo}' AND '{$data_fin_aquisitivo}'";
                                                        echo "<pre>";
                                                        print_r($aquisitivo_end);
                                                        echo "</pre>";
                                                    }


//    $qr_faltas2 = mysql_query("SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$clt' AND id_mov = '62' AND status IN(1,5) AND $limite_falta2 AND ano_mov = '$falta_aquisitivo_end[0]'");
//    $row_faltas2 = mysql_fetch_array($qr_faltas2);


                                                    $despresa_faltas = $_REQUEST['despreza_faltas'];


                                                    if (!isset($despresa_faltas)) {

                                                        $faltas = $row_faltas1['faltas']; //+ $row_faltas2['faltas'];
                                                        $faltas_real = $row_faltas1['faltas'];

                                                        /**
                                                         * FEITO POR: SINï¿½SIO LUIZ  
                                                         * 18/06/2015
                                                         * VERIFICANDO REEMBOLSO DE FALTAS
                                                         */
                                                        $a = str_replace(" ", "", $aquisitivo_ini);
                                                        $b = str_replace(" ", "", $aquisitivo_end);
                                                        $competenciaIni = date("Y-m", strtotime($a));
                                                        $competenciaFim = date("Y-m", strtotime($b));

                                                        $qryVerifReembolso = "SELECT SUM(qnt) AS faltas
                                                            FROM rh_movimentos_clt
                                                            WHERE id_clt = '{$clt}' AND id_mov = '229' 
                                                            AND status IN(1,5) AND CONCAT(ano_mov,'-',lpad(mes_mov, 2, '0')) BETWEEN '{$competenciaIni}' AND '{$competenciaFim}'";


                                                        $sqlVerifReembolso = mysql_query($qryVerifReembolso) or die("Errp ap selecionar reembolso de faltas");
                                                        $total_reembolso = mysql_fetch_assoc($sqlVerifReembolso);


                                                        if ($_COOKIE['logado'] == 40 || $_COOKIE['logado'] == 41) {
                                                            echo "Faltas: " . $faltas . "<br/>";
                                                            echo "REEMBOLSO: " . $total_reembolso['faltas'] . "<br/>";
                                                            echo "Data Inicio: " . $competenciaIni . "<br/>";
                                                            echo "Data Fim: " . $competenciaFim . "<br/>";
                                                        }

                                                        //REDEFININDO VARIAVEL FALTAS, POR CONTA DE ALGUM REEMBOLSO
                                                        $faltas = $faltas - $total_reembolso['faltas'];

                                                        if (isset($_REQUEST['seta_qnt_faltas']) && $_REQUEST['seta_qnt_faltas'] > 0) {
                                                            $faltas = $_REQUEST['seta_qnt_faltas'];
                                                        }
                                                        
                                                        if($id_clt == 55986){ // ADELAINE LIMA DUARTE
                                                            $faltas = 6;
                                                        }
                                                        
                                                        if($id_clt == 54706){ // SARAH REGINA DE PAIVA SILVA PIRES
                                                            $faltas = 6;
                                                        }
                                                        
                                                        if($id_clt == 53876){ // ARMANDA HENRIQUE PIRES DE ALMEIDA
                                                            $faltas = 0;
                                                        }
                                                        if($id_clt == 53878){ // FLAVIA JUREMA FARIA CONSTANTINO
                                                            $faltas = 6;
                                                        }
                                                        if($id_clt == 55791){ // RENATA SILVA TORRES DOS SANTOS
                                                            $faltas = 12;
                                                        }
                                                        if($id_clt == 55380){ // TUANE MACHADO RODRIGUES
                                                            $faltas = 24;
                                                        }
                                                        if($id_clt == 54784){ // RENATA MIRIELE DE SOUZA SILVA
                                                            $faltas = 18;
                                                        }
                                                        if($id_clt == 55155){ // PATRÍCIA DA COSTA
                                                            $faltas = 0;
                                                        }
                                                        if($id_clt == 55377){ // PRISCILA DO NASCIMENTO MACHADO DA SILVA
                                                            $faltas = 6;
                                                        }
                                                        if($id_clt == 55156){ // QUEILA PINHEIRO CABRAL
                                                            $faltas = 6;
                                                        }
                                                        if($id_clt == 55294){ // ANA LUCIA DA SILVA RODRIGUES
                                                            $faltas = 12;
                                                        }
                                                        if($id_clt == 56142){ // MARLUCI CONCEIÇÃO DOS SANTOS ALMEIDA
                                                            $faltas = 12;
                                                        }
                                                        if($id_clt == 55237){ // MAXIMILIANO FARIA DE ALBUQUERQUE
                                                            $faltas = 18;
                                                        }
                                                        if($id_clt == 55226){ // ANTONIA ARAUJO DE FARIAS
                                                            $faltas = 42;
                                                        }
                                                        if($id_clt == 55290){ // ELAINE GUIMARÃES DA COSTA
                                                            $faltas = 12;
                                                        }
                                                        
                                                        if($id_clt == 55290){ // ELAINE GUIMARÃES DA COSTA
                                                            $faltas = 12;
                                                        }
                                                        
                                                        
                                                        if($id_clt == 53917){ // FABIO SOARES DE CARVALHO
                                                                $faltas = 0;
                                                        }
                                                        if($id_clt == 55783){ // INES DE FRANÇA 
                                                                $faltas = 18;
                                                        }
                                                        if($id_clt == 54882){ // CARLOS EDUARDO
                                                                $faltas = 18;
                                                        }
                                                        if($id_clt == 54681){ // MARLENE CARNEIRO
                                                                $faltas = 0;
                                                        }
                                                        if($id_clt == 55419){ // MARISA DE PAIVA
                                                                $faltas = 54;
                                                        }
                                                        if($id_clt == 55347){ // CARINE TEIXEIRA
                                                                $faltas = 0;
                                                        }
                                                        if($id_clt == 55250){ // CRISTIANE DO NASCIMENTO
                                                                $faltas = 36;
                                                        }
                                                        if($id_clt == 55346){ // MAYARA ALEMONGE
                                                                $faltas = 18;
                                                        }
                                                        if($id_clt == 54053){ // GISELE DA SILVA
                                                                $faltas = 48;
                                                        }
                                                        if($id_clt == 55340){ // LISA DE SOUSA
                                                                $faltas = 24;
                                                        }
                                                        if($id_clt == 55289){ // THAMIRYS FERNANDA
                                                                $faltas = 30;
                                                        }
                                                        if($id_clt == 53123){ // VANESSA VALERIO
                                                                $faltas = 0;
                                                        }

                                                        if($id_clt == 56387){ // OSWALDO LOPES
                                                                $faltas = 25;
                                                        }
                                                        if($id_clt == 56375){ // Lena Alves
                                                                $faltas = 11;
                                                        }
                                                        if($id_clt == 56418){ // Lena Alves
                                                                $faltas = 28;
                                                        }
                                                        if($id_clt == 55608){ // Camila Goulart de Abreu 
                                                                $faltas = 0;
                                                        }
                                                        if($id_clt == 55645){ //Daniele Mouta de Oliveira
                                                                $faltas = 18;
                                                        }
                                                        if($id_clt == 55754){ // Viviane Gomes Porto da Costa
                                                                $faltas = 0;
                                                        }
                                                        if($id_clt == 55609){ // Ana Paula Paulino Rodrigues
                                                                $faltas = 0;
                                                        }
                                                        if($id_clt == 56322){ $faltas = 12; } //Thayllane de Oliveira Abrantes
                                                        if($id_clt == 54056){ $faltas = 0; }  //Roberta da Silva Ribeiro Rocha
                                                        if($id_clt == 55986){ $faltas = 6; }  //Adelaine Lima Duarte
                                                        if($id_clt == 55226){ $faltas = 42; } //Antonia Araujo Farias
                                                        if($id_clt == 53878){ $faltas = 6; }  // Flavia Jurema Faria Constantino
                                                        if($id_clt == 55791){ $faltas = 12; } //Renata Silva Torres dos Santos
                                                        if($id_clt == 54784){ $faltas = 18; } //Renata Miriele de Souza Silva
                                                        if($id_clt == 55155){ $faltas = 0; }  //Patricia da Costa
                                                        if($id_clt == 55377){ $faltas = 6; }  //Priscila do Nascimento Machado Silva
                                                        if($id_clt == 55156){ $faltas = 6; }  //Queila Pinheiro Cabral
                                                        if($id_clt == 55294){ $faltas = 12; } //Ana Lucia da Silva Rodrigues
                                                        if($id_clt == 56142){ $faltas = 12; } //Marluci Conceição dos Santos Almeida
                                                        if($id_clt == 55237){ $faltas = 18; } //Maximiliano Faria de Albuquerque
                                                        if($id_clt == 54762){ $faltas = 24; } //Soraia Honorio de Azevedo 
                                                        if($id_clt == 56267){ $faltas = 6; }  //Michelle Alves Cabra
                                                        
                                                         if($id_clt == 55609){ $faltas = 0; } //ANA PAULA PAULINO RODRIGUES
                                                         if($id_clt == 55608){ $faltas = 0; } //CAMILA GOULART DE ABREU
                                                         if($id_clt == 55645){ $faltas = 18; } //DANIELE MOURA DE OLIVEIRA
                                                         
                                                         
                                                         if($id_clt == 55609){ $faltas = 0; }  // ANA PAULA PAULINO RODRIGUES
                                                         if($id_clt == 55608){ $faltas = 0; }  // CAMILA GOULART DE ABREU
                                                         if($id_clt == 55645){ $faltas = 18; } // DANIELE MOURA DE OLIVEIRA
                                                         if($id_clt == 56387){ $faltas = 18; } // OSWALDO LOPES JUNIOR

                                                        if($id_clt == 55497){ $faltas = 18;} // Yury Muniz Sobral
                                                        
 
                                                       if($id_clt == 55689){ $faltas = 0;}  // VALÉRIA CRISTINA DOS S S FREITE
                                                       if($id_clt == 55861){ $faltas = 38;}  // PAULO ROBERTO DIAS DE SOUZA
                                                       if($id_clt == 53404) {$faltas = 10;  }// SIMONE SILVA MOREIRA
                                                       
                                                       if($id_clt == 55815){ $faltas = 6;  }// QUEZIA FONSECA G PAVANI
                                                       
                                                       if($id_clt == 55740){ $faltas = 12; } // FATIMA MARIA INACIO
                                                       if($id_clt == 55859){ $faltas = 18; } // SANDRA FIGUEIREDO ABREU
                                                       if($id_clt == 55391) {$faltas = 7;  }// ALEXSANDRA FORDIANI
                                                       if($id_clt == 55776){ $faltas = 42; } // ROSANGELA VITOR A DE MEDEIROS

                                                       if($id_clt ==54677){$faltas = 24;}  //ARIANE RODRIGUES DA SILVA

                                                        if($id_clt ==55619){$faltas = 12;}//CLIMARA DE SOUZA A B. CARNEIRO

                                                        if($id_clt ==55435){$faltas = 12;}//SILAINE SALAZAR COELHO

                                                        if($id_clt ==54984){$faltas = 18;}//CHRISTIANE PIMENTEL ESTEVES
                                                        if($id_clt ==54330){$faltas = 30;}//DAYANE DE BRITO MOREIRA
                                                        if($id_clt ==54558){$faltas =6;}//JOSIANE APARECIDA DA SILVA
                                                        if($id_clt ==54322){$faltas = 6;}//KELLY CHRISTINE DOLAVALE CORREA
                                                        if($id_clt ==54641){$faltas = 36;}//THAIANA DE SOUZA SANTOS

                                                        if($id_clt ==55243){$faltas = 0;}//EMANUELLE DUARTE O. DE ALMEIDA
                                                        
                                                        if($id_clt ==55746){$faltas = 0;}//DENILZA LEMOS COSTA
                                                        if($id_clt ==56512){$faltas = 15;}//KAREN ROXANA MONTANO ESCALERA
                                                        if($id_clt ==55248){$faltas = 11;}//LUIZ FERNANDO DE MELO

                                                        if(in_array($id_clt, array(54580,54922,53459))) $faltas = 0;
                                                        
                                                        if($id_clt ==55631){$faltas =6;}//Daniela Ribeiro da Silva
                                                        if($id_clt ==53196){$faltas =0;}//Jocelly dos Santos Oliviera
                                                        if($id_clt ==54786){$faltas =12;}//Libia Dantas Besllusci
                                                        if($id_clt ==55684){$faltas =12;}//Elaine Gonçalves de Souza
                                                        if($id_clt ==55920){$faltas =0;}//Sueli Alves da Silva
                                                        if($id_clt ==55926){$faltas =6;}//Andrea Telles de Oliveira
                                                        if($id_clt ==55574){$faltas =12;}//Oseias Botelho
                                                        if($id_clt ==55573){$faltas =6;}//Maria Aparecida Ferreira
                                                        if($id_clt ==54679){$faltas =0;}//Nilza Léa
                                                        if($id_clt ==54675){$faltas =6;}//Tatiana Costa Nunes
                                                        if($id_clt ==54996){$faltas =0;}//Fatima Regina da Silva Alves
                                                        if($id_clt ==54041){$faltas =0;}//Viviane Delfino Pereira da Silva
                                                        if($id_clt ==55140){$faltas =36;}//Andre Luiz Pereira Menezes
                                                        if($id_clt ==54291){$faltas =0;}//Cintia Ramos Higino
                                                        if($id_clt ==56034){$faltas =18;}//Jorgiana Loureiro Mirnada Amorim 
                                                        if($id_clt ==53131){$faltas =18;}//Adriana Chaves do Rosario
                                                        if($id_clt ==56755){$faltas =6;}//Vanessa Rocha Brasil
                                                        if($id_clt ==55827){$faltas =6;}//Monique Scatinga Ferreira
                                                        
                                                        if($id_clt ==56146){$faltas =24;}//Andresa de Almeida Ferreira (Mesquita)

                                                        if($id_clt ==56146){$faltas = 24;}
                                                        if($id_clt ==55648){$faltas = 6;}
                                                        if($id_clt ==55438){$faltas = 0;}
                                                        if($id_clt ==55987){$faltas = 74;}

                                                        if($id_clt ==55788){$faltas = 36;}
                                                        if($id_clt ==53950){$faltas = 114;}
                                                     

                                                        if($id_clt ==55684){$faltas = 6;}
                                                        if($id_clt ==55488){$faltas = 43;}
                                                        if($id_clt ==55997){$faltas = 15;}
                                                        if($id_clt ==55557){$faltas = 12;}
                                                        if($id_clt ==55558){$faltas = 0;}
                                                        if($id_clt ==55839){$faltas = 18;}
                                                        if($id_clt ==55945){$faltas = 16;}

                                                        if($id_clt ==55407){$faltas = 58;}
                                                        if($id_clt ==55939){$faltas = 36;}

                                                        if($id_clt ==55819){$faltas = 22;}

                                                        if($id_clt ==55452){$faltas = 10;}

                                                        if($id_clt ==55374){$faltas = 24;}

                                                        if($id_clt ==56507){$faltas = 42;}

                                                        if($id_clt ==56163){$faltas = 18;}
                                                        if($id_clt ==54975){$faltas = 0;}
                                                        if($id_clt ==54332){$faltas = 18;}
                                                       
                                                        
                                                        
                                                         if($id_clt ==55805){$faltas = 20;}
                                                         if($id_clt ==55607){$faltas = 0;}
                                                         if($id_clt ==55614){$faltas = 6;}
                                                         if($id_clt ==55597){$faltas = 24;}
                                                         if($id_clt ==56645){$faltas = 12;}
                                                         if($id_clt ==53883){$faltas = 20;}
                                                     if($id_clt ==53951){$faltas = 0;}

                                                        
                                                        if ($faltas <= 5) {
                                                            $qnt_dias = 30;
                                                        } elseif ($faltas >= 6 and $faltas <= 14) {
                                                            $qnt_dias = 24;
                                                        } elseif ($faltas >= 15 and $faltas <= 23) {
                                                            $qnt_dias = 18;
                                                        } elseif ($faltas >= 24 and $faltas <= 32) {
                                                            $qnt_dias = 12;
                                                        } elseif ($faltas > 32) {
                                                            $qnt_dias = 0;
                                                        }
                                                    } else {

                                                        $faltas = 0;
                                                        $faltas_real = $row_faltas1['faltas'] + $row_faltas2['faltas'];
                                                        $qnt_dias = 30;
                                                    }                                                                                                        

                                                    $update_movimentos_clt = '0';

                                                    $qr_novo_faltas1 = mysql_query("SELECT id_movimento FROM rh_movimentos_clt WHERE id_clt = '$clt' AND id_mov = '62' AND status = '1'  AND $limite_falta1 AND ano_mov = '$falta_aquisitivo_ini[0]'");
                                                    while ($row_novo_faltas1 = mysql_fetch_assoc($qr_novo_faltas1)) {
                                                        $update_movimentos_clt .= ',' . $row_novo_faltas1['id_movimento'];
                                                    }

                                                    $qr_novo_faltas2 = mysql_query("SELECT id_movimento FROM rh_movimentos_clt WHERE id_clt = '$clt' AND id_mov = '62' AND status = '1'  AND $limite_falta2 AND ano_mov = '$falta_aquisitivo_end[0]'");
                                                    while ($row_novo_faltas2 = mysql_fetch_assoc($qr_novo_faltas2)) {
                                                        $update_movimentos_clt .= ',' . $row_novo_faltas2['id_movimento'];
                                                    }
//-----------------------------------------------------
                                                    ?>
                                                    <script language="javascript">
                                                        function verifica_tudo() {

                                                            var d = document.getElementById('data_inicio');

                                                            if (d.value == "") {
                                                                alert("Data Inï¿½cio nï¿½o pode estar vazia");
                                                                d.value = "<?= $data_corrente ?>";
                                                                d.focus();
                                                                return false;
                                                            }

                                                            var d = document.getElementById('data_inicio');
                                                            var datacorrente = "<?= $data_corrente_real ?>";
                                                            var data1 = datacorrente;
                                                            var data2 = d.value;

                                                            /*if ( parseInt( data2.split( "/" )[2].toString() + data2.split( "/" )[1].toString() + data2.split( "/" )[0].toString() ) < parseInt( data1.split( "/" )[2].toString() + data1.split( "/" )[1].toString() + data1.split( "/" )[0].toString() ) ){
                                                             alert("Fï¿½rias sï¿½ pode ter inï¿½cio a partir de <?= $data_corrente_real ?>");
                                                             d.value = "<?= $data_corrente_real ?>";
                                                             d.focus();
                                                             return false;
                                                             }
                                                             */
                                                            return true;

                                                        }

                                                        function verifica_dobradas() {

                                                            var c = document.getElementById('chk_desconsiderar_dobradas');
                                                            var d = document.getElementById('data_inicio');
                                                            var r = document.getElementById('ferias_dobradas');
                                                            var datacorrente = "<?= $data_limite ?>";
                                                            var data1 = datacorrente;
                                                            var data2 = d.value;

                                                            if ((parseInt(data2.split("/")[2].toString() + data2.split("/")[1].toString() + data2.split("/")[0].toString()) > parseInt(data1.split("/")[2].toString() + data1.split("/")[1].toString() + data1.split("/")[0].toString())) && !c.checked) {
                                                                r.style.display = ''
                                                            } else {
                                                                r.style.display = 'none'
                                                            }
                                                        }
                                                    </script>
                                                    <table border='0' cellpadding='8' cellspacing='8' bgcolor='#f5f5f5' align='center' style="border:1px solid #ddd; border-radius:10px; -moz-border-radius:10px;">
                                                        <tr>
                                                            <td align="right"><?php include('../../reportar_erro.php'); ?></td>
                                                        </tr>

                                                        <tr>
                                                            <td>
                                                                <div id="tela2">

                                                                    <span style="font-size:10px;">
        <?= $clt . " - " . $row_clt['nome'] ?>
                                                                    </span>

                                                                    <p>&nbsp;</p>
                                                                    Perï¿½odo Aquisitivo:
                                                                    <br/>

                                                                    <span style="font-weight:normal;">
        <?php echo implode('/', array_reverse(explode('-', $aquisitivo_ini))) . ' - ' . implode('/', array_reverse(explode('-', $aquisitivo_end))); ?>
                                                                    </span>

                                                                    <p>&nbsp;</p>

                                                                    <form action="index.php?enc=<?= $link ?>" method="post" onSubmit="return verifica_tudo()">
                                                                        Data de In&iacute;cio das F&eacute;rias:

                                                                        <input name="data_inicio" type="text" size="8" value="" maxlength="10" style="font-weight:normal;" 
                                                                               onKeyUp="mascara_data(this)" id="data_inicio" onChange="verifica_dobradas()">

                                                                            <span style="font-weight:normal; font-style:italic; color:#C30;
                                                                            <?php
                                                                            if (!isset($_GET['dobradas'])) {
                                                                                echo 'display:none;';
                                                                            }
                                                                            ?>
                                                                                  " id="ferias_dobradas">
                                                                                <br/>
                                                                                <input type="checkbox" id="chk_desconsiderar_dobradas" name="chk_desconsiderar_dobradas" onclick="verifica_dobradas()">Desconsiderar as fï¿½rias dobradas</a>
                                                                                    </label>
                                                                                    <br/>
                                                                                    <br/>
                                                                                    (<strong>f&eacute;rias dobradas</strong> a partir de <b><?= $data_dobrada ?></b>)
                                                                            </span>

                                                                            <?php if (!empty($faltas) or ( isset($_GET['faltas']) and ! empty($_GET['faltas']))) { ?>
                                                                                <br/>
                                                                                <br/>
                                                                            <?php if (!isset($_GET['despreza_faltas'])) { ?>
                                                                                    <span style="font-weight:normal; font-style:italic; color:#C30;">(<strong><?= $faltas ?> faltas</strong> no perï¿½odo)</span>
                                                                                    <br/>
                                                                                    <br/>
                                                                                    <?php } ?>
                                                                                <label id="periodo_faltas">   
                                                                                    <?php
                                                                                    $url_atual = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER ['REQUEST_URI'];
                                                                                    $url_nova = str_replace('&despreza_faltas=true', '', $url_atual);

                                                                                    if (!isset($_GET['despreza_faltas'])) {
                                                                                        ?>
                                                                                        <a onClick="javascript:window.location = '<?= $url_atual ?>&despreza_faltas=true'" href="#">Clique aqui para desconsiderar as faltas no perï¿½odo</a>
                                                                                    <?php } else { ?>
                                                                                        <a onClick="javascript:window.location = '<?= $url_nova ?>'" href="#">Clique aqui para reconsiderar as faltas no perï¿½odo</a>
                                                                                    <?php } ?>
                                                                                        
                                                                                    
                                                                                </label>
                                                                            <?php } ?>

                                                                            <br/>
                                                                            <br/>Quantidade de Dias:

                                                                            <?php
                                                                            // Considerarando Perï¿½odo de Faltas (Trabalhando Normalmente)
                                                                            if (!isset($_GET['despreza_faltas'])) {
                                                                                ?>


                                                                                <select name="quantidade_dias" >
                                                                                    <?php
                                                                                    // Prï¿½-selecionado quando volta da tela 5
                                                                                    if (isset($_GET['quantidade_dias'])) {
                                                                                        $pre_selected = $_GET['quantidade_dias'];

                                                                                        // Senï¿½o, seleciona pela Quantidade de Dias
                                                                                    } else {
                                                                                        $pre_selected = $qnt_dias;
                                                                                    }

                                                                                    // Inï¿½cio do Loop (de 1 Dia a Quantidade de Dias)
                                                                                    for ($a = 1; $a <= $qnt_dias; $a++) {

                                                                                        // Executa a Seleï¿½ï¿½o
                                                                                        if ($a == $pre_selected) {
                                                                                            $selected = " selected";
                                                                                        }

                                                                                        // Oculta a div de abono quando ï¿½ o mï¿½ximo de dias
                                                                                        if ($a == $qnt_dias) {
                                                                                            $script = " class='oculta'";
                                                                                        }

                                                                                        // Exibe a div de abono quando nï¿½o ï¿½ o mï¿½ximo de dias
                                                                                        if ($a != $qnt_dias) {
                                                                                            $script = " class='exibe'";
                                                                                        }

                                                                                        // Exibindo os Options
                                                                                        echo "<option value='" . sprintf('%02d', $a) . "'$selected$script>$a</option>";

                                                                                        // Resetando a variï¿½vel $selected para o prï¿½ximo loop
                                                                                        unset($selected);
                                                                                    }
                                                                                    ?>
                                                                                </select>


                                                                                <?php
                                                                                // Se Desconsiderar Perï¿½odo de Faltas
                                                                            } else {
                                                                                ?>


                                                                                <select name="quantidade_dias" >
                                                                                    <?php
                                                                                    // Prï¿½-selecionado quando volta da tela 5
                                                                                    if (isset($_GET['quantidade_dias'])) {
                                                                                        $pre_selected = $_GET['quantidade_dias'];

                                                                                        // Senï¿½o, seleciona pela Quantidade de Dias
                                                                                    } else {
                                                                                        $pre_selected = $qnt_dias;
                                                                                    }

                                                                                    // Inï¿½cio do Loop (de 1 Dia a 30 Dias)
                                                                                    for ($a = 1; $a <= $qnt_dias; $a++) {

                                                                                        // Executa a Seleï¿½ï¿½o
                                                                                        if ($a == $pre_selected) {
                                                                                            $selected = " selected";
                                                                                        }

                                                                                        // Oculta a div de abono quando ï¿½ o mï¿½ximo de dias
                                                                                        if ($a == $qnt_dias) {
                                                                                            $script = " class='oculta'";
                                                                                        }

                                                                                        // Exibe a div de abono quando nï¿½o ï¿½ o mï¿½ximo de dias
                                                                                        if ($a != $qnt_dias) {
                                                                                            $script = " class='exibe'";
                                                                                        }

                                                                                        // Exibindo os Options
                                                                                        echo "<option value='" . sprintf('%02d', $a) . "'$selected$script>$a</option>";

                                                                                        // Resetando a variï¿½vel $selected para o prï¿½ximo loop
                                                                                        unset($selected);
                                                                                    }
                                                                                    ?>
                                                                                </select>


                                                                                <?php
                                                                                // Terminando Seleï¿½ï¿½o de Quantidade de Dias
                                                                            }
                                                                            ?>


                                                                            <label id="periodo_abono" style="
                                                                            <?php
                                                                            echo 'display:block;';
//        if (isset($_GET['quantidade_dias']) and $_GET['quantidade_dias'] != $qnt_dias) {
//            echo 'display:block;';
//        } else {
//            echo 'display:none';
//        }
                                                                            ?>
                                                                                   ">

                                                                                <br/>Considerar Per&iacute;odo de Abono:

                                                                                <input type="checkbox" name="periodo_abono" value="1" 
                                                                                <?php
//        if (!isset($_GET['periodo_abono']) or ( isset($_GET['periodo_abono']) and ! empty($_GET['periodo_abono']))) {
//            echo 'checked';
//        }
                                                                                ?> 
                                                                                       >
                                                                            </label>
                                                                            <br/><br/>
                                                                            <input type="submit" value="Prosseguir" class="botao" style="margin:10px auto;">
                                                                                <input type="button" value="Voltar" onClick="javascript:location.href = 'index.php?enc=<?= $link2 ?>&periodo_aquisitivo=<?= $_REQUEST['periodo_aquisitivo'] ?>'" class="botao" style="margin:10px auto;">
                                                                                    <input type="hidden" name="direito_dias" value="<?= $qnt_dias ?>" />
                                                                                    <?php if (isset($_GET['despreza_faltas'])) { ?>
                                                                                        <input type="hidden" name="despreza_faltas" value="1" />
        <?php } ?>
                                                                                    <input type="hidden" name="periodo_aquisitivo" value="<?= $_REQUEST['periodo_aquisitivo'] ?>" />
                                                                                    <input type="hidden" name="faltas" value="<?= $faltas ?>" />
                                                                                    <input type="hidden" name="faltas_real" value="<?= $faltas_real ?>" />
                                                                                    <input type="hidden" name="projeto" value="<?= $row_clt['id_projeto'] ?>" />
                                                                                    <input type="hidden" name="update_movimentos_clt" value="<?= $update_movimentos_clt ?>" />
                                                                                    </form>

                                                                                    </div>
                                                                                    </td>
                                                                                    </tr>
                                                                                    </table>

                                                                                    <?php
                                                                                    // Tela 5 (Cï¿½lculo das Fï¿½rias e Resumo do Pagamento)
                                                                                    break;
                                                                                case 5:

//Parï¿½metro para processamento individual de fï¿½rias        
//location.href='index.php?enc=$link&periodo_aquisitivo=$_REQUEST[periodo_aquisitivo]&data_inicio=$_REQUEST[data_inicio]&quantidade_dias=$_REQUEST[quantidade_dias]$link_abono$link_dobradas&data=nulo        
//    if($_COOKIE['logado'] == 49){
//        echo "entrou no case 5\n";
//    }
//    
                                                                                    // Chamando a Classe Cï¿½lculos
                                                                                    $Calc = new calculos();
                                                                                    //--------------------------------
                                                                                    // Encriptografando o Botï¿½o Voltar
                                                                                    $tela = 4;
                                                                                    $link = encrypt("$regiao&$tela&$clt");
                                                                                    $link = str_replace("+", "--", $link);
                                                                                    //--------------------------------------
                                                                                    // Verificando se Desprezou Faltas
                                                                                    // $despreza_faltas = "&despreza_faltas=true";  
                                                                                    if (!empty($_REQUEST['despreza_faltas'])) {
                                                                                        $despreza_faltas = "&despreza_faltas=true";
                                                                                    } else {
                                                                                        $despreza_faltas = NULL;
                                                                                    }
                                                                                    //---------------------------------------------
                                                                                    // Chamando a Variï¿½vel de Updates de Movimentos
                                                                                    $update_movimentos_clt = $_REQUEST['update_movimentos_clt'];
                                                                                    //--------------------------------------
                                                                                    // Formatando a Data de Inï¿½cio de Fï¿½rias

                                                                                    $data_inicio = implode('-', array_reverse(explode('/', $_REQUEST['data_inicio'])));

                                                                                    //-------------------------------------
                                                                                    //-------------------------------------
                                                                                    // Feito para ferias em lote, mais agora vamos aproveitar para qualquer calculo de fï¿½rias,
                                                                                    // Pegando a quantidade de faltas para calcular os dias que a possoa poderï¿½ gozar de ferias.
                                                                                    // Segundo meu histï¿½rico do NetBeans alguï¿½m comentou esse cï¿½digo abaixo entre o dia 07 e 14/12 
                                                                                    // pois no primeiro dia de referï¿½ncia o cï¿½digo nï¿½o estï¿½ comentado e no segundo estï¿½.
                                                                                    // Consultando o Sinï¿½sio, ele me informou que comentou esse cï¿½digo porque ele estava dando erro
                                                                                    // em outra operaï¿½ï¿½o de processamento de fï¿½rias individual, mas nï¿½o conseguiu se lembrar qual era
                                                                                    // ficamos de tentar encontra o erro gerado por essa atribuiï¿½ï¿½o para nï¿½o retornar o erro gerado tambï¿½m
                                                                                    // por ela.

                                                                                    if ($_COOKIE['logado'] == 41) {
                                                                                        echo "Faltas: {$_REQUEST['faltas']}<br/>";
                                                                                    }

                                                                                    $_REQUEST['quantidade_dias'] = $objCalcFerias->getDiaProporcionalFaltas($_REQUEST['faltas']);

                                                                                    // Nao deveria ser assim, mas 2 variaveis com a mesma informaï¿½ï¿½o.
                                                                                    $dias_ferias = $_REQUEST['quantidade_dias'];

                                                                                    if (isset($_REQUEST['periodo_abono']) && $_REQUEST['periodo_abono'] != 0) {

                                                                                        $dias_abono_pecuniario = ceil($dias_ferias / 3);

                                                                                        $dias_ferias = $dias_ferias - $dias_abono_pecuniario;
                                                                                    }

                                                                                    $quantidade_dias = $dias_ferias;

                                                                                    // Calculando o Fim e Retorno de Fï¿½rias
                                                                                    $dataE = explode('-', $data_inicio);
                                                                                    $anoE = $dataE[0];
                                                                                    $mesE = $dataE[1];
                                                                                    $diaE = $dataE[2];

                                                                                    //--------------------------------------
                                                                                    // Em conversa com a JOSIE, ela me explicou que se zerar a qnt de dias de direito de gozo
                                                                                    // mostra a data de fim com 30 dias, pois era para a pessoa gozar 30, mais como teve muita falta
                                                                                    // ela perdeu o direito... sï¿½ a data de retorno vai ser igual a data de inicio, pois ela nem saiu de ferias
                                                                                    if (isset($quantidade_dias) && $quantidade_dias > 0 && $quantidade_dias <= 30) {
                                                                                        $vasco = $quantidade_dias;
                                                                                    } else {
                                                                                        $vasco = 30;
                                                                                    }

                                                                                    $data_fim = date("Y-m-d", mktime(0, 0, 0, $mesE, $diaE + $vasco - 1, $anoE));

                                                                                    //SE A PESSOA Nï¿½O TEM DIREITO A GOZO, O RETORNO DEVE SER MESMO DIA DO INICIO
                                                                                    if ($quantidade_dias == 0) {
                                                                                        $data_retorno = $data_inicio;
                                                                                    } else {
                                                                                        $data_retorno = date("Y-m-d", mktime(0, 0, 0, $mesE, $diaE + $quantidade_dias, $anoE));
                                                                                    }

                                                                                    //-----------------------------
                                                                                    // Selecionando os Dados do CLT
                                                                                    $qr_clt = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y')as data_entrada2, date_format(data_saida, '%d/%m/%Y')as data_saida2 FROM rh_clt WHERE id_clt = '$clt'");
                                                                                    $row_clt = mysql_fetch_array($qr_clt);



                                                                                    //---------------------
                                                                                    // Selecionando o Curso
                                                                                    $qr_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_clt[id_curso]'");
                                                                                    $row_curso = mysql_fetch_array($qr_curso);
                                                                                    //-------------------------------
                                                                                    // Definindo o Perï¿½odo Aquisitivo
                                                                                    if ($ferias_lote) {

                                                                                        $periodo_aquisitivo = explode('-', trim(str_replace(' ', '', $_REQUEST['periodo_aquisitivo'])));

                                                                                        $data = array('aquisitivo_ini' => array(), 'aquisitivo_end' => array());

//            print_array($periodo_aquisitivo);

                                                                                        $data['aquisitivo_ini'] = explode('/', $periodo_aquisitivo[0]);
                                                                                        $data['aquisitivo_end'] = explode('/', $periodo_aquisitivo[1]);

//            print_array($data);

                                                                                        $aquisitivo_ini = $data['aquisitivo_ini'][2] . '-' . $data['aquisitivo_ini'][1] . '-' . $data['aquisitivo_ini'][0];
                                                                                        $aquisitivo_end = $data['aquisitivo_end'][2] . '-' . $data['aquisitivo_end'][1] . '-' . $data['aquisitivo_end'][0];

//            print_array($aquisitivo_ini);
                                                                                    } else {

                                                                                        $periodo_aquisitivo = explode('/', trim(str_replace(' ', '', $_REQUEST['periodo_aquisitivo'])));

                                                                                        $aquisitivo_ini = $periodo_aquisitivo[0];
                                                                                        $aquisitivo_end = $periodo_aquisitivo[1];
                                                                                    }

                                                                                    //----------------
                                                                                    // Verificando Fï¿½rias Dobradas e Definindo Salï¿½rio Base
                                                                                    $preview = explode('-', $aquisitivo_end);


                                                                                    if ($clt == 53161 || $clt == 53876 || $clt == 53206 || $clt == 53296 || $clt == 53996 || $clt == 53376 || $clt == 54059 || $_REQUEST['chk_desconsiderar_dobradas']) {
                                                                                        $verifica_dobrado = date('Y-m-d', mktime(0, 0, 0, $preview[1], $preview[2], $preview[0] + 2));
                                                                                    } else {
                                                                                        $verifica_dobrado = date('Y-m-d', mktime(0, 0, 0, $preview[1], $preview[2], $preview[0] + 1));
                                                                                    }

                                                                                    if ($verifica_dobrado <= $data_inicio) {
                                                                                        $salario_base = $row_curso['salario'] * 2;
                                                                                        $ferias_dobradas = "sim";
                                                                                        $link_dobradas = "&dobradas=true";
                                                                                    } else {
                                                                                        $salario_base = $row_curso['salario'];
                                                                                        $ferias_dobradas = "nao";
                                                                                        $link_dobradas = NULL;
                                                                                    }

                                                                                    if($clt == 53241){
                                                                                        
                                                                                        $salario_base = $row_curso['salario'];
                                                                                        $ferias_dobradas = "nao";
                                                                                        $link_dobradas = NULL;
                                                                                    }
                                                                                    
                                                                                    if($clt==53062) $salario_base = 8000;
                                                                                    //if($clt ==56387) $salario_base = $salario_base * 2;
                                                                                    //---------------------------
                                                                                    // Definindo Salï¿½rio Variï¿½vel
                                                                                    $variavel_aquisitivo_ini = explode('-', $aquisitivo_ini);
                                                                                    $variavel_aquisitivo_end = explode('-', $aquisitivo_end);

                                                                                    if ($variavel_aquisitivo_ini[1] == 12) {
                                                                                        $limite_variavel1 = "mes_mov = '$variavel_aquisitivo_ini[1]'";
                                                                                    } else {
                                                                                        $limite_variavel1 = "mes_mov >= '$variavel_aquisitivo_ini[1]'";
                                                                                    }

                                                                                    if ($variavel_aquisitivo_end[1] == 1) {
                                                                                        $limite_variavel2 = "mes_mov = '$variavel_aquisitivo_ini[1]'";
                                                                                    } else {
                                                                                        $limite_variavel2 = "mes_mov <= '$variavel_aquisitivo_ini[1]'";
                                                                                    }

                                                                                    // Lanï¿½amentos
                                                                                    $qr_variavel1 = mysql_query("SELECT SUM(valor_movimento) AS credito FROM rh_movimentos_clt WHERE id_clt = '$clt' AND tipo_movimento = 'CREDITO' AND id_mov != '151' AND id_mov != '14' AND id_mov != '94' AND status = '1' AND status_ferias = '1' AND $limite_variavel1 AND ano_mov = '$variavel_aquisitivo_ini[0]' AND lancamento != 2");
                                                                                    $row_variavel1 = mysql_fetch_array($qr_variavel1);

                                                                                    $qr_variavel2 = mysql_query("SELECT SUM(valor_movimento) AS credito FROM rh_movimentos_clt WHERE id_clt = '$clt' AND tipo_movimento = 'CREDITO' AND id_mov != '151' AND id_mov != '14' AND id_mov != '94' AND status = '1' AND status_ferias = '1' AND $limite_variavel2 AND ano_mov = '$variavel_aquisitivo_end[0]' AND lancamento != 2");
                                                                                    $row_variavel2 = mysql_fetch_array($qr_variavel2);

                                                                                    $variavel = $row_variavel1['credito'] + $row_variavel2['credito'];
                                                                                    //
                                                                                    /*
                                                                                      // Lanï¿½amentos SEMPRE
                                                                                      $qr_variavel_sempre1 = mysql_query("SELECT valor_movimento AS credito FROM rh_movimentos_clt WHERE id_clt = '$clt' AND tipo_movimento = 'CREDITO' AND id_mov != '151' AND id_mov != '14' AND id_mov != '94' AND status = '1' AND status_ferias = '1' AND $limite_variavel1 AND ano_mov = '$variavel_aquisitivo_ini[0]' AND lancamento = '2'");
                                                                                      while($row_variavel_sempre1 = mysql_fetch_array($qr_variavel_sempre1)) {
                                                                                      $variavel_sempre += $row_variavel_sempre1['credito'];
                                                                                      }

                                                                                      $qr_variavel_sempre2 = mysql_query("SELECT valor_movimento AS credito FROM rh_movimentos_clt WHERE id_clt = '$clt' AND tipo_movimento = 'CREDITO' AND id_mov != '151' AND id_mov != '14' AND id_mov != '94' AND status = '1' AND status_ferias = '1' AND $limite_variavel2 AND ano_mov = '$variavel_aquisitivo_end[0]' AND lancamento = '2'");
                                                                                      while($row_variavel_sempre2 = mysql_fetch_array($qr_variavel_sempre2)) {
                                                                                      $variavel_sempre += $row_variavel_sempre2['credito'];
                                                                                      }

                                                                                      $variavel_sempre *= 12;
                                                                                      //

                                                                                      $variavel += $variavel_sempre;

                                                                                      if(!empty($variavel)) {
                                                                                      $salario_variavel = $variavel / 12;
                                                                                      }



                                                                                      //ALTERADO
                                                                                      if( in_array($regiao,array('28','32')) and  ($preview[0] == '2010' or $preview[0] == '2011')  and empty($salario_variavel)) {
                                                                                      $salario_variavel = 109;

                                                                                      }
                                                                                      ////////

                                                                                     */


                                                                                    //////////////////////////////////////////////////////////////
/////CALCULANDO A Mï¿½DIAS DE RENDIMENTOS DOS ï¿½LTIMOS 6 MESES
///////////////////////////////////////////////////////////////
//        if($_COOKIE['logado'] == 40) {
//            echo $aquisitivo_ini;
//            echo $aquisitivo_end;
//        }
//        $qr_folha = mysql_query("select A.* FROM rh_folha as A
//                                INNER JOIN rh_folha_proc as B
//                                ON A.id_folha = B.id_folha
//                                WHERE A.regiao = '$regiao' AND A.status=3 
//                                AND B.status = 3 AND A.terceiro != 1
//                                 AND A.data_inicio BETWEEN DATE_SUB('$data_inicio', INTERVAL 14 MONTH) AND '$data_inicio'
//                                AND B.id_clt = '$clt';") or die(mysql_error());
//        while ($row_folha = mysql_fetch_assoc($qr_folha)) {
//
//            $ids_mov = $row_folha['ids_movimentos_estatisticas'];
//
//            $qr_movimento = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '$clt' AND id_movimento IN($ids_mov) AND tipo_movimento = 'CREDITO'");
//            while ($row_mov = mysql_fetch_assoc($qr_movimento)) {
//
//                //POG para acertar a insalubridade do tipo sempre
//                if ($row_mov['id_mov'] == 56 AND $row_folha['ano'] == 2012 AND $row_mov['valor_movimento'] == '135.60') {
//
//                    $movimentos_confere[$row_folha['mes']][$row_mov['nome_movimento']] += 124.40;
//                    $movimentos[$row_mov['nome_movimento']] += 124.40;
//                } else {
//                    $movimentos_confere[$row_folha['mes']][$row_mov['nome_movimento']] += $row_mov['valor_movimento'];
//                    $movimentos[$row_mov['nome_movimento']] += $row_mov['valor_movimento'];
//                }
//            }
//        }

                                                                                    $qr_clt = mysql_query("SELECT A.*, YEAR(data_demi) as ano_demissao, B.salario, B.tipo_insalubridade,B.qnt_salminimo_insalu
                        FROM rh_clt as A
                       INNER JOIN curso as B
                       ON A.id_curso = B.id_curso
                       WHERE A.id_clt = $clt");
                                                                                    $row_clt = mysql_fetch_assoc($qr_clt);

                                                                                    $qr_folha = mysql_query("select  A.ids_movimentos_estatisticas, B.id_clt,A.mes
                                FROM rh_folha as A
                                INNER JOIN rh_folha_proc as B
                                ON A. id_folha = B.id_folha
                                WHERE B.id_clt   = '$clt'  AND B.status = 3 AND A.terceiro = 2 
                                AND A.data_inicio BETWEEN '{$aquisitivo_ini}' AND '{$aquisitivo_end}'                                
                                ORDER BY A.ano,A.mes");

                                                                                    while ($row_folha = mysql_fetch_assoc($qr_folha)) {

                                                                                        if (!empty($row_folha[ids_movimentos_estatisticas])) {

                                                                                            $qr_movimentos = mysql_query("SELECT *
                                                FROM rh_movimentos_clt
                                                WHERE id_movimento IN($row_folha[ids_movimentos_estatisticas]) AND incidencia = '5020,5021,5023' AND id_mov NOT IN(200,14,193,56) AND tipo_movimento = 'CREDITO' AND id_clt = $clt AND status = 5 ");
                                                                                            while ($row_mov = mysql_fetch_assoc($qr_movimentos)) {

                                                                                                $verifica_mov_fixo = mysql_query("SELECT * FROM rh_movimentos_clt   WHERE  id_mov = '$row_mov[id_mov]' AND   id_clt = '$id_clt' AND lancamento = 2 AND status = 1");
                                                                                                if (mysql_num_rows($verifica_mov_fixo) == 0) {
                                                                                                    $movimentos[$row_mov['nome_movimento']] +=$row_mov['valor_movimento'];
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                    if($_SESSION['logado']==55){
                                                                                        print_array($row_folha);
                                                                                    }

                                                                                    $qr_mov_fixo = mysql_query("SELECT * FROM rh_movimentos_clt
                                            WHERE  incidencia = '5020,5021,5023' AND id_mov NOT IN(200,14,193,56) AND tipo_movimento = 'CREDITO' AND id_clt = $clt AND (lancamento = 2 AND status = 1)
                                            ");
                                                                                    while ($row_mov = mysql_fetch_assoc($qr_mov_fixo)) {
                                                                                        $movimentos_fixos[$row_mov['nome_movimento']] = $row_mov['valor_movimento'];
                                                                                    }
                                                                                    ////////////////////////////////////////
                                                                                    //CONDIï¿½ï¿½O PARA A INSALUBRIDADEUBRIDADE ///////
                                                                                    ///////////////////////////////////////
                                                                                    if ($row_clt['insalubridade'] == 1) {

                                                                                        $qr_mov = mysql_query("SELECT fixo FROM rh_movimentos WHERE cod = '0001' AND anobase = '" . date('Y') . "'") or die(mysql_error());
                                                                                        $row_mov = mysql_fetch_assoc($qr_mov);


                                                                                        $percentInsalu = 0.20;
                                                                                        if ($row_clt['tipo_insalubridade'] == 2) {
                                                                                            $percentInsalu = 0.40;
                                                                                        }
                                                                                        $valorSalMinimoInsalubridade = $row_mov['fixo'] * $row_clt['qnt_salminimo_insalu'];

                                                                                        $valor_insalubridade_integral = ($valorSalMinimoInsalubridade * $percentInsalu);
                                                                                        $media_por_mov['INSALUBRIDADE 20%'] = $valor_insalubridade_integral;
                                                                                        $total_media += $media_por_mov['INSALUBRIDADE 20%'];
                                                                                    }

                                                                                    if (sizeof($movimentos) > 0) {
                                                                                        foreach ($movimentos as $nome_mov => $valor) {
                                                                                            $media_por_mov[$nome_mov] = $valor / 12;
                                                                                        }
                                                                                    }
                                                                                    if (sizeof($movimentos_fixos) > 0) {
                                                                                        foreach ($movimentos_fixos as $nome_mov => $valor) {
                                                                                            $media_por_mov[$nome_mov] = $valor;
                                                                                        }
                                                                                    }
                                                                                    $total_media = 0;
                                                                                    foreach ($media_por_mov as $key => $values) {
                                                                                        $total_media += $values;
                                                                                    }



                                                                                    if ($_COOKIE['logado'] == 40) {
                                                                                        echo $total_media;
                                                                                        echo "<pre>";
                                                                                        print_r($media_por_mov);
                                                                                        echo "</pre>";
                                                                                    }

                                                                                    /*                                                                                     * ***************************************************************
                                                                                     * ****************************************************************
                                                                                     * *************************************************************** */

//        foreach ($movimentos as $nome_mov => $valor) {
//            $salario_variavel += ($valor / 12);
//        }



                                                                                    if ($clt == 54037) {
                                                                                        $salario_variavel = 487.93;
                                                                                    } elseif($clt == 55411) {
                                                                                    	$salario_variavel = 396.36;
                                                                                    } elseif($clt == 54970) {
                                                                                    	$salario_variavel = $total_media - 11.4; // Diferença solicitada pelo Eduardo 03/01/2017
                                                                                    } else {
                                                                                        $salario_variavel = $total_media;
                                                                                    }
//////FIM CALCULO DA Mï¿½DIA


                                                                                    $i = 0;
                                                                                    $qr_novo_variavel1 = mysql_query("SELECT id_movimento FROM rh_movimentos_clt WHERE id_clt = '$clt' AND tipo_movimento = 'CREDITO' AND status = '1' AND status_ferias = '1' AND $limite_variavel1 AND ano_mov = '$variavel_aquisitivo_ini[0]'");
                                                                                    while ($row_novo_variavel1 = mysql_fetch_assoc($qr_novo_variavel1)) {
                                                                                        $update_movimentos_clt .= ( ++$i == 1 ? '' : ',') . $row_novo_variavel1['id_movimento'];
                                                                                    }

                                                                                    $i = 0;
                                                                                    $qr_novo_variavel2 = mysql_query("SELECT id_movimento FROM rh_movimentos_clt WHERE id_clt = '$clt' AND tipo_movimento = 'CREDITO' AND status = '1' AND status_ferias = '1' AND $limite_variavel2 AND ano_mov = '$variavel_aquisitivo_end[0]'");
                                                                                    while ($row_novo_variavel2 = mysql_fetch_assoc($qr_novo_variavel2)) {
                                                                                        $update_movimentos_clt .= ( ++$i == 1 ? '' : ',') . $row_novo_variavel2['id_movimento'];
                                                                                    }



//--------------------
// Definindo Variï¿½veis
                                                                                    $salario_contratual = number_format($row_curso['salario'], 2, ".", "");
                                                                                    $quantidade_dias_calc = 30;
//        $quantidade_dias_calc = cal_days_in_month(CAL_GREGORIAN, $mesE, $anoE);
//$salario    = ($salario_base / $quantidade_dias_calc) * $quantidade_dias;
                                                                                    $salario = $salario_base;
                                                                                    $valor_dia = ($salario_base + $salario_variavel) / $quantidade_dias_calc;

                                                                                    /**
                                                                                     * FEITO POR SINESIO LUIZ, SE TIVER ERRADO Nï¿½O ï¿½ PRA DESCONTAR EM... Nï¿½O SOU ANALISTA DE RH
                                                                                     */
                                                                                    //print_r($_REQUEST['periodo_abono']);

                                                                                    if (isset($_REQUEST['periodo_abono']) && $_REQUEST['periodo_abono'] != 0) {
                                                                                        //$dias_abono_pecuniario = $dias_ferias/3;
                                                                                        $link_abono = "&periodo_abono=$_REQUEST[periodo_abono]";
                                                                                        $abono_pecuniario = $valor_dia * $dias_abono_pecuniario;
                                                                                        $umterco_abono_pecuniario = $abono_pecuniario / 3;
                                                                                        $periodo_abono_ini = date('d/m/Y', strtotime($data_retorno));
                                                                                        $periodo_abono_fim = date('d/m/Y', strtotime('+9 days', strtotime($data_retorno)));
                                                                                        $periodo_abono_iniF = date('Y-m-d', strtotime($data_retorno));
                                                                                        $periodo_abono_fimF = date('Y-m-d', strtotime('+9 days', strtotime($data_retorno)));

                                                                                        //$quantidade_dias = $dias_ferias - $dias_abono_pecuniario;
                                                                                        // Verificando Abono Pecuniï¿½rio (Venda de Dias)
                                                                                        //        if (isset($_REQUEST['periodo_abono'])) {
                                                                                        //            $dias_abono_pecuniario = $_REQUEST['direito_dias'] - $_REQUEST['quantidade_dias'];
                                                                                        //            $link_abono = "&periodo_abono=$_REQUEST[periodo_abono]";
                                                                                        //        } else {
                                                                                        //            $dias_abono_pecuniario = 0;
                                                                                        //            $link_abono = "&periodo_abono=0";
                                                                                        //        }
                                                                                        //
            //        if (isset($_REQUEST['periodo_abono']) and ! empty($dias_abono_pecuniario)) {
                                                                                        //            $abono_pecuniario = $valor_dia * $dias_abono_pecuniario;
                                                                                        //            $umterco_abono_pecuniario = $abono_pecuniario / 3;
                                                                                        //        }
                                                                                    }

//        echo $periodo_abono_iniF . "<br/>";
//        echo $periodo_abono_fimF . "<br/>";

                                                                                    $valor_total = $valor_dia * $quantidade_dias;
// $um_terco = ((($salario_base + $salario_variavel) / 30) * $quantidade_dias) / 3;
// $um_terco = $valor_total / 3;
                                                                                    $um_terco = $valor_total / 3; //($salario + $salario_variavel) / 3;
                                                                                    $remuneracao_calc = $valor_total + $um_terco;


//-------------------
// Base para INSS / IRRF / FGTS
                                                                                    $calc_inss_irrf_fgts = $valor_total + $um_terco;

// Verificando Faltas
                                                                                    if (!empty($_REQUEST['faltas'])) {
                                                                                        $faltas = $_REQUEST['faltas'];
                                                                                        $link_faltas = "&faltas=$_REQUEST[faltas]";
                                                                                    } else {
                                                                                        $faltas = 0;
                                                                                        $link_faltas = "&faltas=$_REQUEST[faltas_real]";
                                                                                    }
//---------------------------------------------
//---------------------------------------
// Verificando a Data de Inï¿½cio de Fï¿½rias
                                                                                    if (empty($_REQUEST['data_inicio']) && !$ferias_lote) {
                                                                                        echo "<script language='JavaScript'>location.href='index.php?enc=$link&periodo_aquisitivo=$_REQUEST[periodo_aquisitivo]&data_inicio=$_REQUEST[data_inicio]&quantidade_dias=$_REQUEST[quantidade_dias]$link_abono$link_dobradas&data=nulo';
</script>";
                                                                                        exit;
                                                                                    }
//----------------
                                                                                    //USADA PARA Cï¿½LCULO DE INSS E IRRF DE ACORDO COM O ANO DE PORCESSAMENTO DAS Fï¿½RIAS
                                                                                    //ESSA CONDIï¿½ï¿½O PARA QUANDO O PERï¿½ODO DE Fï¿½RIAS FOR NO ANO SEGUINTE, CALCULAR COM A TABELA DO ANO VIGENTE
                                                                                    $data_calc = date('Y-m-d');
                                                                                    $BASE_INSS = $calc_inss_irrf_fgts;

                                                                                    //TA UM JOGO DE EMPURRA EMPURRA, VOU COMENTAR O Q A JOSIE PEDIU, E FAZER O Q O SABINO MANDOU
                                                                                    //TROUXE PARA CIMA A BASE DE IR, POIS DEPENDENDO DO TIPO DE DESCONTO DE INSS, VAI ALTERAR A BASE DO IR
                                                                                    //$BASE_IRRF = 0;
                                                                                    //CASO Nï¿½O TENHA DESCONTO INSS EM OUTRA EMPRESA, OU SE O DESCONTO DA OUTRA EMPRESA FOR PARCIAL CALCULA INSS
                                                                                    if (($row_clt['desconto_inss'] != 1) OR ( $row_clt['desconto_inss'] == 1 && $row_clt['tipo_desconto_inss'] == "parcial")) {
                                                                                        // Calculando INSS            
                                                                                        $Calc->MostraINSS($BASE_INSS, $data_calc);
                                                                                        $inss = $Calc->valor;
                                                                                        $porcentagem_inss = $Calc->percentual;
                                                                                        //$BASE_IRRF = $BASE_INSS - $inss;        //BASE IR ï¿½ A MESMA BASE DE INSS MENOS O VALOR DESCONTADO
                                                                                    }

                                                                                    /**
                                                                                     * aqui $row_clt['desconto_inss'], $row_clt['tipo_desconto_inss'],$row_clt['desconto_outra_empresa'],
                                                                                     */
                                                                                    if ($row_clt['desconto_inss'] == 1 && $row_clt['tipo_desconto_inss'] == "parcial") {
                                                                                        if (($row_clt['desconto_outra_empresa']) < $tetoInss) {
                                                                                            $inssResiduo = $tetoInss - $row_clt['desconto_outra_empresa'];
                                                                                        } else {
                                                                                            //$BASE_IRRF = $BASE_INSS - $tetoInss;        //SE DESCONTA O TETO EM OUTRA EMPRESA, DEFINO BASE IR AQUI QUE ï¿½ BASE INSS - TETO
                                                                                            $inssResiduo = 0;
                                                                                        }

                                                                                        if ($inss < $inssResiduo) {
                                                                                            $inss = $inss;
                                                                                            //$BASE_IRRF = $BASE_INSS - ($inss + $row_clt['desconto_outra_empresa']);     //SE O DESCONTO AQUI, FOR MENOR QUE O RESIDUO(MAXIMO QUE POSSO DESCONTAR AQUI), SOMO OS 2 PARA DESCONTAR O TOTAL DE INSS QUE O CARA RECOLHEU
                                                                                        } else {
                                                                                            $inss = $inssResiduo;
                                                                                            //$BASE_IRRF = $BASE_INSS - $tetoInss;        //SE O DESCONTO AQUI, MAIOR OU IGUAL AO MAXIMO QUE POSSO DESCONTAR, DESCONTO ESSE MAXIMO ENTï¿½O O CARA RECOLHEU O TETO ESSE MES
                                                                                        }
                                                                                    } else if ($row_clt['desconto_inss'] == 1 && $row_clt['tipo_desconto_inss'] == "isento") {
                                                                                        $inss = 0;
                                                                                        //$BASE_IRRF = $BASE_INSS - $tetoInss;            //SE DESCONTA O TETO EM OUTRA EMPRESA, DEFINO BASE IR AQUI QUE ï¿½ BASE INSS - TETO
                                                                                    }

                                                                                    if ($_COOKIE['logado'] == 41) {
                                                                                        echo $BASE_IRRF;
                                                                                    }

                                                                                    //--------------
                                                                                    // Calculando IR
                                                                                    $BASE_IRRF = $BASE_INSS - $inss;
                                                                                    $Calc->MostraIRRF($BASE_IRRF, $clt, $regiao, $data_calc);
                                                                                    $ir = $Calc->valor;

//        if($_COOKIE['logado'] == 40){
//            echo "<pre>";
//                print_r($BASE_IRRF);
//            echo "<pre>";
//        }

                                                                                    if ($ir != 0) {
                                                                                        $PERCENTUAL_IRRF = $Calc->percentual;
                                                                                        $VALOR_DDIR = $Calc->valor_deducao_ir_total;
                                                                                        $QNT_DEPENDENTES_IRRF = $Calc->total_filhos_menor_21;
                                                                                        $PARCELA_DEDUCAO_IRRF = $Calc->valor_fixo_ir;
                                                                                    } else {
                                                                                        $BASE_IRRF = 0;
                                                                                    }


//----------------
// Calculando FGTS
                                                                                    $fgts = $calc_inss_irrf_fgts * 0.08;
//----------------------------
// Buscando Pensï¿½o Alimenticia
                                                                                    $pensao_aquisitivo_ini = explode('-', $aquisitivo_ini);
                                                                                    $pensao_aquisitivo_end = explode('-', $aquisitivo_end);

                                                                                    if ($pensao_aquisitivo_ini[1] == 12) {
                                                                                        $limite_pensao1 = "mes_mov = '$pensao_aquisitivo_ini[1]'";
                                                                                    } else {
                                                                                        $limite_pensao1 = "mes_mov >= '$pensao_aquisitivo_ini[1]'";
                                                                                    }

                                                                                    if ($pensao_aquisitivo_end[1] == 1) {
                                                                                        $limite_pensao2 = "mes_mov = '$pensao_aquisitivo_ini[1]'";
                                                                                    } else {
                                                                                        $limite_pensao2 = "mes_mov <= '$pensao_aquisitivo_ini[1]'";
                                                                                    }

                                                                                    if ($_COOKIE['logado'] == 38) {
                                                                                        echo "SELECT * FROM rh_movimentos_clt WHERE id_clt = '$clt' AND status = '1' AND status_ferias = '1' AND $limite_pensao1 AND ano_mov = '$pensao_aquisitivo_ini[0]' AND id_mov IN('54','63','250','255','256','834') ORDER BY id_movimento DESC";
                                                                                        echo "<br/><br/>";
                                                                                        echo "SELECT * FROM rh_movimentos_clt WHERE id_clt = '$clt' AND status = '1' AND status_ferias = '1' AND $limite_pensao2 AND ano_mov = '$pensao_aquisitivo_end[0]' AND id_mov IN('54','63','250','255','256','834') ORDER BY id_movimento DESC";
                                                                                    }

                                                                                    $qr_pensao1 = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '$clt' AND status = '1' AND status_ferias = '1' AND $limite_pensao1 AND ano_mov = '$pensao_aquisitivo_ini[0]' AND id_mov IN('54','63','250','255','256','834') ORDER BY id_movimento DESC");
                                                                                    $row_pensao1 = mysql_fetch_array($qr_pensao1);
                                                                                    $numero_pensao1 = mysql_num_rows($qr_pensao1);

                                                                                    $qr_pensao2 = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '$clt' AND status = '1' AND status_ferias = '1' AND $limite_pensao2 AND ano_mov = '$pensao_aquisitivo_end[0]' AND id_mov IN('54','63','250','255','256','834') ORDER BY id_movimento DESC");
                                                                                    $row_pensao2 = mysql_fetch_array($qr_pensao2);
                                                                                    $numero_pensao2 = mysql_num_rows($qr_pensao2);


                                                                                    $numero_pensao = $numero_pensao1 + $numero_pensao2;

                                                                                    if (!empty($numero_pensao)) {

                                                                                        if (!empty($numero_pensao2)) {
                                                                                            $tipo_pensao = $row_pensao2['id_mov'];
                                                                                        } else {
                                                                                            $tipo_pensao = $row_pensao1['id_mov'];
                                                                                        }

                                                                                        if ($tipo_pensao == "54") {
                                                                                            $ps = 0.15;
                                                                                        } elseif ($tipo_pensao == "63") {
                                                                                            $ps = 0.30;
                                                                                        } elseif ($tipo_pensao == "250") {
                                                                                            $ps = 0.25;
                                                                                        } elseif ($tipo_pensao == "255") {
                                                                                            $ps = 0.20;
                                                                                        } elseif ($tipo_pensao == "256") {
                                                                                            $ps = 0.05;
                                                                                        } elseif ($tipo_pensao == "834") {
                                                                                            $ps = 0.39;
                                                                                        }

                                                                                        $pensao_alimenticia = number_format($remuneracao_calc * $ps, 2, ".", "");
                                                                                    }

                                                                                    /* CASO DE PENSï¿½O ALIMENTICIA AUTOMATICO E DIVIDO POR DEPENDENTE
                                                                                     * FOI FEITO ESSA GAMBI, PQ COMO SEMPRE IDR PRECISA PRA ONTEM..
                                                                                     */
                                                                                    $valor_pensao_fixa = 0;
                                                                                    $qry_verifica_pensao = "SELECT * FROM favorecido_pensao_assoc AS A WHERE A.id_clt = '{$clt}'";
                                                                                    $sql_verifica_pensao = mysql_query($qry_verifica_pensao) or die('Erro ao selecionar Pensï¿½o');
                                                                                    while ($rows_pensao = mysql_fetch_assoc($sql_verifica_pensao)) {
                                                                                        $valor_pensao_fixa += $remuneracao_calc * $rows_pensao['aliquota'];
                                                                                    }
                                                                                    $pensao_alimenticia += $valor_pensao_fixa;


                                                                                    $tot_remuneracoes = $valor_total + $um_terco + $abono_pecuniario + $umterco_abono_pecuniario;
                                                                                    $tot_descontos = $pensao_alimenticia + $inss + $ir;
                                                                                    $tot_liquido = $tot_remuneracoes - $tot_descontos;
                                                                                    
                                                                                    if ($clt == 53278) $pensao_alimenticia = '827.22';

                                                                                    if ($clt == 55706) $pensao_alimenticia = 437.64;
                                                                                    
                                                                                    if ($clt == 53234) $pensao_alimenticia = ($tot_liquido * 0.16);
                                                                                    
                                                                                    if ($clt == 54249) $pensao_alimenticia = ($tot_liquido * 0.25);
                                                                                    
                                                                                    if ($clt == 56203) $pensao_alimenticia = ($tot_liquido * 0.20);
                                                                                    
                                                                                    if ($clt == 56326 ) $pensao_alimenticia = ($tot_liquido * 0.20);
                                                                                    
                                                                                    if ($clt == 53566 ) $pensao_alimenticia = ($tot_liquido * 0.20);
                                                                                    
                                                                                    if ($clt == 56216 ) $pensao_alimenticia = ($tot_liquido * 0.39);
                                                                                    
                                                                                    if ($clt == 55442) $pensao_alimenticia = ($remuneracao_calc * 0.20); ;
                                                                                    
                                                                                    if ($clt == 54774) $pensao_alimenticia = ($remuneracao_calc * 0.30);
                                                                                    
                                                                                    
                                                                                    
                                                                                    // verifica se tirou fï¿½rias coletivas
                                                                                    $sql_feriasCol = mysql_query("SELECT *
                    FROM rh_ferias_coletiva
                    WHERE id_clt = {$clt} AND status = 1") or die(mysql_error());
                                                                                    $tot_feriasCol = mysql_num_rows($sql_feriasCol);
                                                                                    $res_feriasCol = mysql_fetch_assoc($sql_feriasCol);


                                                                                    $dataIni_fc = converteData($res_feriasCol['data_inicio'], "d/m/Y");
                                                                                    $dataFim_fc = converteData($res_feriasCol['data_fim'], "d/m/Y");

                                                                                    if ($tot_feriasCol == 1) {
                                                                                        $qtdDias_fc = diferencaDias($dataIni_fc, $dataFim_fc);
                                                                                    }

                                                                                    $i = 0;
                                                                                    $qr_novo_pensao1 = mysql_query("SELECT id_movimento FROM rh_movimentos_clt WHERE id_clt = '$clt' AND status = '1' AND status_ferias = '1' AND $limite_pensao1 AND ano_mov = '$pensao_aquisitivo_ini[0]' AND id_mov IN('54','63')");
                                                                                    while ($row_novo_pensao1 = mysql_fetch_assoc($qr_novo_pensao1)) {
                                                                                        $update_movimentos_clt .= ( ++$i == 1 ? '' : ',') . $row_novo_pensao1['id_movimento'];
                                                                                    }

                                                                                    $i = 0;
                                                                                    $qr_novo_pensao2 = mysql_query("SELECT id_movimento FROM rh_movimentos_clt WHERE id_clt = '$clt' AND status = '1' AND status_ferias = '1' AND $limite_pensao2 AND ano_mov = '$pensao_aquisitivo_end[0]' AND id_mov IN('54','63')");
                                                                                    while ($row_novo_pensao2 = mysql_fetch_assoc($qr_novo_pensao2)) {
                                                                                        $update_movimentos_clt .= ( ++$i == 1 ? '' : ',') . $row_novo_pensao2['id_movimento'];
                                                                                    }
//---------------------                                                                                                                                     
//
//                                                                                                                                                      )
// Calculando Variï¿½veis             
                                                                                    //$remuneracao_base = number_format($salario + $salario_variavel + $abono_pecuniario, 2, ".", "");
                                                                                    $remuneracao_base = number_format($salario_base + $salario_variavel, 2, ".", "");
                                                                                    $total_remuneracoes = number_format($valor_total + $um_terco + $abono_pecuniario + $umterco_abono_pecuniario, 2, ".", "");
                                                                                    $total_descontos = number_format($pensao_alimenticia + $inss + $ir, 2, ".", "");
                                                                                    $total_liquido = number_format($total_remuneracoes - $total_descontos, 2, ".", "");
                                                                                    //----------------------------
                                                                                    // Calculando Meses Diferentes
                                                                                    // POREM SE A PESSOA Nï¿½O TEM DIREITO, ZERO OS DIAS DOS 2 MESES
                                                                                    if ($quantidade_dias == 0) {
                                                                                        $dias_mes = 0;
                                                                                        $dias_ferias1 = 0;
                                                                                        $dias_ferias2 = 0;
                                                                                    } else {

                                                                                        $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mesE, $anoE);
                                                                                        $dias_ferias1 = $dias_mes - $diaE + 1;
                                                                                        $dias_ferias2 = $quantidade_dias - $dias_ferias1;
                                                                                    }

                                                                                    $valor_total1 = $dias_ferias1 * $valor_dia;
                                                                                    $acrescimo_constitucional1 = $valor_total1 / 3;
                                                                                    $total_remuneracoes1 = $valor_total1 + $acrescimo_constitucional1 + $abono_pecuniario + $umterco_abono_pecuniario;

                                                                                    $valor_total2 = $dias_ferias2 * $valor_dia;
                                                                                    $acrescimo_constitucional2 = $valor_total2 / 3;
                                                                                    $total_remuneracoes2 = $valor_total2 + $acrescimo_constitucional2;
                                                                                    //-------------------------
                                                                                    // Formataï¿½ï¿½o para exibiï¿½ï¿½o
                                                                                    $aqui_ini = $aquisitivo_ini;
                                                                                    $aqui_fim = $aquisitivo_end;
                                                                                    $aquisitivo_iniT = implode('/', array_reverse(explode('-', $aquisitivo_ini)));
                                                                                    $aquisitivo_endT = implode('/', array_reverse(explode('-', $aquisitivo_end)));
                                                                                    $data_inicioT = implode('/', array_reverse(explode('-', $data_inicio)));
                                                                                    $data_fimT = implode('/', array_reverse(explode('-', $data_fim)));
                                                                                    $data_retornoT = implode('/', array_reverse(explode('-', $data_retorno)));
                                                                                    $salario_contratualT = number_format($salario_contratual, 2, ",", "");
                                                                                    $salarioT = number_format($salario, 2, ",", "");
                                                                                    $salario_variavelT = number_format($salario_variavel, 2, ",", "");
                                                                                    $remuneracao_baseT = number_format($remuneracao_base, 2, ",", "");
                                                                                    $um_tercoT = number_format($um_terco, 2, ",", "");
                                                                                    $valor_diaT = number_format($valor_dia, 2, ",", "");
                                                                                    $valor_totalT = number_format($valor_total, 2, ",", "");
                                                                                    $inssT = number_format($inss, 2, ",", "");
                                                                                    $irT = number_format($ir, 2, ",", "");
                                                                                    $fgtsT = number_format($fgts, 2, ",", "");
                                                                                    $pensao_alimenticiaT = number_format($pensao_alimenticia, 2, ",", "");
                                                                                    $total_remuneracoesT = number_format($total_remuneracoes, 2, ",", "");
                                                                                    $total_descontosT = number_format($total_descontos, 2, ",", "");
                                                                                    $total_liquidoT = number_format($total_liquido, 2, ",", "");
                                                                                    $abono_pecuniarioT = number_format($abono_pecuniario, 2, ",", "");
                                                                                    $umterco_abono_pecuniarioT = number_format($umterco_abono_pecuniario, 2, ",", "");
                                                                                    //-----------------------------------------

                                                                                    if ($tot_feriasCol == 1) {
                                                                                        $quantidade_dias = $quantidade_dias - $qtdDias_fc;
                                                                                        $periodo_fc = "({$qtdDias_fc} dias em fï¿½rias coletivas)";
                                                                                        $data_fimT = date('d/m/Y', strtotime("-5 days", strtotime($data_fim)));
                                                                                    }

                                                                                    if ($ferias_lote) {

                                                                                        if ($quantidade_dias > 30) {

                                                                                            exit(json_encode(array("status" => 0, "especifica" => utf8_encode("Nao ï¿½ possï¿½vel conceder mais de 30 dias de fï¿½rias"))));
                                                                                        }

                                                                                        $sQuery = "
             SELECT id_clt
             FROM rh_ferias
             WHERE id_clt = {$clt} 
                 AND DATE_FORMAT(data_aquisitivo_ini,'%Y-%m-%d') = '$aquisitivo_ini'
                 AND DATE_FORMAT(data_aquisitivo_fim,'%Y-%m-%d') = '$aquisitivo_end'
                 AND status
             ";

                                                                                        $rs = mysql_query($sQuery);

                                                                                        $row = mysql_fetch_array($rs);

                                                                                        if (mysql_num_rows($rs) > 0) {

                                                                                            exit(json_encode(array("status" => 0, "especifica" => utf8_encode("Jï¿½ existe fï¿½rias processadas para o perï¿½odo aquisito."))));
                                                                                        }

                                                                                        $sQuery = "
             SELECT s.codigo, s.especifica 
             FROM rh_clt c INNER JOIN rhstatus s ON c.status = s.codigo
             WHERE c.id_clt = {$clt}
             ";

                                                                                        $rs = mysql_query($sQuery);

                                                                                        $row = mysql_fetch_array($rs);

                                                                                        if (mysql_num_rows($rs) > 0) {

                                                                                            if ($row['codigo'] == 10) {

                                                                                                if (empty($porcentagem_inss)) {
                                                                                                    $porcentagem_inss = 0;
                                                                                                }

                                                                                                /**
                                                                                                 * Nï¿½O ï¿½ A MELHOR SOLUï¿½ï¿½O, MAS PELO MENOS VAI DIMINUIR OS PROBLEMAS 
                                                                                                 * SOLUï¿½ï¿½O DESESï¿½RADORA AFIM DE ACABAR COM VALORES NEGATIVOS SENDO GRAVADOS NA TABELA
                                                                                                 */
                                                                                                $salario = verificaValorNegativo($salario);
                                                                                                $valor_dia = verificaValorNegativo($valor_dia);
                                                                                                $valor_total = verificaValorNegativo($valor_total);
                                                                                                $um_terco = verificaValorNegativo($um_terco);
                                                                                                $salario_variavel = verificaValorNegativo($salario_variavel);
                                                                                                $remuneracao_base = verificaValorNegativo($remuneracao_base);
                                                                                                $dias_ferias = verificaValorNegativo($dias_ferias);
                                                                                                $total_remuneracoes = verificaValorNegativo($total_remuneracoes);
                                                                                                $pensao_alimenticia = verificaValorNegativo($pensao_alimenticia);
                                                                                                $inss = verificaValorNegativo($inss);
                                                                                                $ir = verificaValorNegativo($ir);
                                                                                                $fgts = verificaValorNegativo($fgts);
                                                                                                $total_descontos = verificaValorNegativo($total_descontos);
                                                                                                $total_liquido = verificaValorNegativo($total_liquido);
                                                                                                $abono_pecuniario = verificaValorNegativo($abono_pecuniario);
                                                                                                $umterco_abono_pecuniario = verificaValorNegativo($umterco_abono_pecuniario);
                                                                                                $dias_abono_pecuniario = verificaValorNegativo($dias_abono_pecuniario);

                                                                                                $ferias_dobradas = verificaValorNegativo($ferias_dobradas);
                                                                                                $valor_total1 = verificaValorNegativo($valor_total1);
                                                                                                $valor_total2 = verificaValorNegativo($valor_total2);
                                                                                                $acrescimo_constitucional1 = verificaValorNegativo($acrescimo_constitucional1);
                                                                                                $acrescimo_constitucional2 = verificaValorNegativo($acrescimo_constitucional2);
                                                                                                $total_remuneracoes1 = verificaValorNegativo($total_remuneracoes1);
                                                                                                $total_remuneracoes2 = verificaValorNegativo($total_remuneracoes2);
                                                                                                $periodo_abono_iniF = date('Y-m-d', strtotime($data_retorno));
                                                                                                $periodo_abono_fimF = date('Y-m-d', strtotime('+' . $dias_abono_pecuniario . ' days', strtotime($data_retorno)));
                                                                                
                                                                                                $sQuery = "
                    INSERT INTO rh_ferias
                        (id_clt,
                        nome,
                        regiao,
                        projeto,
                        mes,
                        ano,
                        data_aquisitivo_ini,
                        data_aquisitivo_fim,
                        data_ini,
                        data_fim,
                        data_retorno,
                        salario,
                        salario_variavel,
                        remuneracao_base,
                        dias_ferias,
                        valor_dias_ferias,
                        valor_total_ferias,
                        umterco,
                        total_remuneracoes,
                        pensao_alimenticia,
                        inss,
                        inss_porcentagem,
                        ir,
                        fgts,
                        total_descontos,
                        total_liquido,
                        abono_pecuniario,
                        umterco_abono_pecuniario,
                        dias_abono_pecuniario,
                        faltas,
                        faltasano,
                        diasmes,
                        ferias_dobradas,
                        valor_total_ferias1,
                        valor_total_ferias2,
                        acrescimo_constitucional1,
                        acrescimo_constitucional2,
                        total_remuneracoes1,
                        total_remuneracoes2,
                        movimentos,
                        user,
                        data_proc,
                        status, 
                        base_inss,
                        base_irrf, 
                        percentual_irrf,
                        valor_ddir, 
                        qnt_dependente_irrf, 
                        parcela_deducao_irrf,
                        periodo_abono_ini,
                        periodo_abono_fim) 
                    VALUES 
                        ({$clt},
                        '{$nome}',
                        {$regiao},
                        {$projeto},
                        {$mesE},
                        {$anoE},
                        '{$aquisitivo_ini}',
                        '{$aquisitivo_end}',
                        '{$data_inicio}',
                        '{$data_fim}',
                        '{$data_retorno}',
                        {$salario},
                        {$salario_variavel},
                        {$remuneracao_base},
                        {$dias_ferias},
                        {$valor_dia},
                        {$valor_total},
                        {$um_terco}, 
                        {$total_remuneracoes},
                        {$pensao_alimenticia},
                        {$inss},
                        {$porcentagem_inss}*100,
                        {$ir},
                        {$fgts},
                        {$total_descontos},
                        {$total_liquido},
                        {$abono_pecuniario},
                        {$umterco_abono_pecuniario},
                        {$dias_abono_pecuniario},
                        {$faltas},
                        0,
                        {$dias_mes},
                        '{$ferias_dobradas}',
                        {$valor_total1},
                        {$valor_total2},
                        {$acrescimo_constitucional1},
                        {$acrescimo_constitucional2},
                        {$total_remuneracoes1},
                        {$total_remuneracoes2},
                        '{$update_movimentos_clt}',
                        {$logado},
                        NOW(),
                        1,
                        {$BASE_INSS},
                        {$BASE_IRRF}, 
                        {$percentual_irrf},
                        {$valor_ddir},
                        {$qnt_dependentes_irrf},
                        {$parcela_deducao_irrf},
                        '{$periodo_abono_iniF}',
                        '{$periodo_abono_fimF}')";

                                                                                                //exit($sQuery);

                                                                                                if (mysql_query($sQuery)) {

                                                                                                    $sQuery = "
                         UPDATE rh_clt
                         SET status=40
                         WHERE id_clt = $clt AND status=10
                         ";

                                                                                                    if (mysql_query($sQuery)) {

                                                                                                        $return = array("status" => 1, "especifica" => utf8_encode("Fï¿½rias Efetivada"));
                                                                                                    } else {

                                                                                                        $return = array("status" => 0, "especifica" => utf8_encode("Clt com evento para esse tipo de operaï¿½ï¿½o"));
                                                                                                    }
                                                                                                } else {

                                                                                                    $return = array("status" => 0, "especifica" => utf8_encode("Erro ao inserir registro"));
                                                                                                }
                                                                                            } else {

                                                                                                $return = array("status" => 0, "especifica" => utf8_encode($row['especifica']));
                                                                                            }
                                                                                        } else {

                                                                                            $return = array("status" => 0, "especifica" => $sQuery);
                                                                                        }

//        echo '<pre>';
//        echo $sQuery;
//        echo '</pre>';

                                                                                        echo json_encode($return);

                                                                                        exit();
                                                                                    }
                                                                                    ?>

                                                                                    <table width="95%" bgcolor="#ffffff" align="center" cellspacing="0">
                                                                                        <tr>
                                                                                            <td align="right"><?php include('../../reportar_erro.php'); ?></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="center" style="font-family:Arial; font-size:18px; color:#FFF; background:#036">
                                                                                    <?php echo $clt . " - " . $row_clt['nome']; ?>
                                                                                                </div>
                                                                                                <div align="center" style="font-family:Arial; font-size:13px; background:#efefef; padding:4px;">
                                                                                    <?php echo "<b>Unidade:</b> " . $row_clt['locacao'] . "<br/><b>Atividade:</b> " . $row_curso['nome'] . "<br/><b>Salï¿½rio Contratual:</b> R$ " . $salario_contratualT; ?>
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr bgcolor="#cccccc" class="linha">
                                                                                            <td height="112" align="center" valign="middle" bgcolor="#F7F7F7">
                                                                                                <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" name="form1" id="form1">
                                                                                                    <br/>
                                                                                                    <table width="60%" cellspacing="0" cellpadding="2" style="border:solid 1px #ccc; line-height:24px;">
                                                                                                        <tr>
                                                                                                            <td height="40" colspan="4" bgcolor="#CCCCCC">
                                                                                                                <div align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:13px; font-weight:bold;">Resumo do Per&iacute;odo de F&eacute;rias</div>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    <?php if ($verifica_dobrado <= $data_inicio) { ?>
                                                                                                            <tr>
                                                                                                                <td colspan="4" align="center">
                                                                                                                    <span class="linha" style="color:#C30;">(Fï¿½rias Dobradas)</span></td>
                                                                                                            </tr>
        <?php } ?>
                                                                                                        <tr>
                                                                                                            <td width="20%" colspan="2">
                                                                                                                <div align="right" class="linha">Perï¿½odo Aquisitivo:</div>
                                                                                                            </td>
                                                                                                            <td width="80%" colspan="2">
                                                                                                                &nbsp;<?php echo $aquisitivo_iniT . ' ï¿½ ' . $aquisitivo_endT; ?>
                                                                                                            </td>
                                                                                                        </tr>
        <?php if (!empty($faltas)) { ?>
                                                                                                            <tr>
                                                                                                                <td colspan="2">
                                                                                                                    <div align="right" class="linha">Faltas no Per&iacute;odo:</div>
                                                                                                                </td>
                                                                                                                <td colspan="2">
                                                                                                                    &nbsp;<?= $faltas ?> dias
                                                                                                                </td>
                                                                                                            </tr>
        <?php } ?>
                                                                                                        <tr>
                                                                                                            <td colspan="2">
                                                                                                                <div align="right" class="linha">Per&iacute;odo de F&eacute;rias:</div>
                                                                                                            </td>
                                                                                                            <td colspan="2">
                                                                                                                &nbsp;<?php  $data_fim_verificada = ($dias_ferias == 0) ? $data_inicioT : $data_fimT;
                                                                                                                echo $data_inicioT . ' ï¿½ ' . $data_fim_verificada; ?>
                                                                                                                
                                                                                                                &nbsp;<?php echo $data_inicioT . ' ï¿½ ' . $data_fimT; ?>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                        <tr>
                                                                                                            <td colspan="2">
                                                                                                                <div align="right" class="linha">Quantidade de Dias:</div>
                                                                                                            </td>
                                                                                                            <td colspan="2">
                                                                                                                &nbsp;<?= $quantidade_dias ?> dias <?php echo $periodo_fc; ?></td>
                                                                                                        </tr>
                                                                                                        <tr>
                                                                                                            <td colspan="2">
                                                                                                                <div align="right" class="linha">Data de Retorno:</div>
                                                                                                            </td>
                                                                                                            <td colspan="2">
                                                                                                                &nbsp;<?php echo $data_retornoT; ?>
                                                                                                            </td>
                                                                                                        </tr>
        <?php if (!empty($dias_abono_pecuniario)) { ?>
                                                                                                            <tr>
                                                                                                                <td colspan="2">
                                                                                                                    <div align="right" class="linha">Periodo de Abono Pecuni&aacute;rio:</div>
                                                                                                                </td>
                                                                                                                <td colspan="2">
            <?php if ($dias_abono_pecuniario > 0) {
                echo $periodo_abono_ini . " ï¿½ " . $periodo_abono_fim;
            } ?>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                            <tr>
                                                                                                                <td colspan="2">
                                                                                                                    <div align="right" class="linha">Dias de Abono Pecuni&aacute;rio:</div>
                                                                                                                </td>
                                                                                                                <td colspan="2">
                                                                                                                    &nbsp;<?= $dias_abono_pecuniario ?> dias
                                                                                                                </td>
                                                                                                            </tr>
        <?php } ?>
                                                                                                        <tr>
                                                                                                            <td height="40" colspan="4" bgcolor="#CCCCCC">
                                                                                                                <div align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:13px; font-weight:bold;">Resumo do Pagamento de F&eacute;rias</div>
                                                                                                            </td>
                                                                                                        </tr>    
                                                                                                        <tr>
                                                                                                            <td width="30%">
                                                                                                                <div align="right" class="linha">Sal&aacute;rio  Proporcional:</div>
                                                                                                            </td>
                                                                                                            <td colspan="3" width="17%">
        <?php $salProp = (($salarioT + $salario_variavelT) / 30) * $quantidade_dias; ?>
                                                                                                                R$ <?php echo number_format($salProp, 2, ',', '.'); ?>
                                                                                                                <span style="font-size: 9px; font-style: italic; color: #C30;"><?php echo "(" . $salarioT . " + " . $salario_variavelT . " /30) * " . $quantidade_dias . " Dias"; ?></span>
                                                                                                            </td>

                                                                                                        </tr>
                                                                                                        <tr>

                                                                                                            <td width="30%">
                                                                                                                <div align="right" class="linha">Sal&aacute;rio:</div>
                                                                                                            </td>
                                                                                                            <td width="17%">
                                                                                                                R$ <?= $salarioT ?>
                                                                                                            </td>
                                                                                                            <td width="30%">
                                                                                                                <div align="right" class="linha">Sal&aacute;rio Vari&aacute;vel:</div>

                                                                                                            </td>
                                                                                                            <td width="23%">
                                                                                                                R$ <?= $salario_variavelT ?>  
                                                                                                                <a href="action.confere_movimentos.php?id_clt=<?php echo $clt; ?>&regiao=<?php echo $regiao; ?>&data_inicio=<?php echo $data_inicio; ?>&aqui_ini=<?php echo $aqui_ini; ?>&aqui_fim=<?php echo $aqui_fim; ?>" onClick="return hs.htmlExpand(this, {objectType: 'iframe'})">ver</a>            

                                                                                                            </td>
                                                                                                        </tr>
                                                                                                        <tr>
                                                                                                            <td>
                                                                                                                <div align="right" class="linha">1/3 do Sal&aacute;rio: </div>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                R$ <?= $um_tercoT ?>
                                                                                                            </td>
        <?php if (!empty($dias_abono_pecuniario)) { ?>
                                                                                                                <td>
                                                                                                                    <div align="right" class="linha">Abono Pecuni&aacute;rio:</div>
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    R$ <?= $abono_pecuniarioT ?>
                                                                                                                </td>
                                                                                                            </tr>   
                                                                                                            <tr>    
                                                                                                                <td>
                                                                                                                    <div align="right" class="linha">1/3 Abono Pecuni&aacute;rio:</div>
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    R$ <?= $umterco_abono_pecuniarioT ?>
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <div align="right" class="linha">Remunera&ccedil;&otilde;es:</div>
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    R$ <?= $total_remuneracoesT ?>
                                                                                                                </td>
                                                                                                            </tr>
        <?php } else { ?> 
                                                                                                            <td>
                                                                                                                <div align="right" class="linha">Remunera&ccedil;&otilde;es:</div>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                R$ <?= $total_remuneracoesT ?>
                                                                                                            </td>
                                                                                                            </tr>
        <?php } ?>
                                                                                                        <tr>
                                                                                                            <td>
                                                                                                                <div align="right" class="linha"><span style="font-size: 9px; font-style: italic; color: #C30;">BASE INSS: </span></div>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                <span style="font-size: 9px; font-style: italic; color: #C30;">R$ <?php echo number_format($BASE_INSS, 2, ",", "."); ?></span>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                <div align="right" class="linha"><span style="font-size: 9px; font-style: italic; color: #C30;">BASE IRRF:</span></div>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                <span style="font-size: 9px; font-style: italic; color: #C30;">R$ <?php echo number_format($BASE_IRRF, 2, ",", "."); ?></span>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                        <tr>
                                                                                                            <td>
                                                                                                                <div align="right" class="linha">INSS: </div>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                R$ <?= $inssT ?>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                <div align="right" class="linha">IRRF:</div>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                R$ <?= $irT ?>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                        <tr>
                                                                                                            <td>
                                                                                                                <div align="right" class="linha">Pens&atilde;o Aliment&iacute;cia:</div>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                R$ <?= $pensao_alimenticiaT ?>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                <div align="right" class="linha">Descontos:</div>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                R$ <?= $total_descontosT ?>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                        <tr>
                                                                                                            <td height="40" colspan="4" align="center" bgcolor="#CCCCCC">
                                                                                                                <span class="linha">L&Iacute;QUIDO A RECEBER:&nbsp;</span> R$ <?= $total_liquidoT ?>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    </table>

                                                                                                    <br/>
                                                                                                    <br/>

                                                                                                    <input type="hidden" name="tot_coletiva" value="<?php echo $tot_feriasCol; ?>" />
                                                                                                    <input type="hidden" name="tela" value="6" />
                                                                                                    <input type="hidden" name="id_clt" value="<?= $clt ?>" />
                                                                                                    <input type="hidden" name="nome" value="<?= $row_clt['nome'] ?>" />
                                                                                                    <input type="hidden" name="regiao" value="<?= $regiao ?>" />
                                                                                                    <input type="hidden" name="projeto" value="<?= $_REQUEST['projeto'] ?>" />
                                                                                                    <input type="hidden" name="mes" value="<?php echo substr($_REQUEST['data_inicio'], 3, 2); ?>" />
                                                                                                    <input type="hidden" name="ano" value="<?php echo substr($_REQUEST['data_inicio'], 6, 4); ?>" />
                                                                                                    <input type="hidden" name="data_aquisitivo_ini" value="<?= $aquisitivo_ini ?>" />
                                                                                                    <input type="hidden" name="data_aquisitivo_fim" value="<?= $aquisitivo_end ?>" />
                                                                                                    <input type="hidden" name="data_inicio" value="<?= $data_inicio ?>">
                                                                                                        <input type="hidden" name="data_fim" value="<?= ConverteData($data_fimT) ?>">
                                                                                                            <input type="hidden" name="data_retorno" value="<?= $data_retorno ?>">
                                                                                                                <input type="hidden" name="salario" value="<?= $row_curso['salario'] ?>" />
                                                                                                                <input type="hidden" name="salario_variavel" value="<?= $salario_variavel ?>" />
                                                                                                                <input type="hidden" name="remuneracao_base" value="<?= $remuneracao_base ?>" />
                                                                                                                <input type="hidden" name="dias_ferias" value="<?= $quantidade_dias ?>" />
                                                                                                                <input type="hidden" name="valor_dias_ferias" value="<?= $valor_dia ?>" />
                                                                                                                <input type="hidden" name="valor_total_ferias" value="<?= $valor_total ?>" />
                                                                                                                <input type="hidden" name="umterco" value="<?= $um_terco ?>" />
                                                                                                                <input type="hidden" name="total_remuneracoes" value="<?= $total_remuneracoes ?>" />
                                                                                                                <input type="hidden" name="pensao_alimenticia" value="<?= $pensao_alimenticia ?>" />
                                                                                                                <input type="hidden" name="inss" value="<?= $inss ?>" />
                                                                                                                <input type="hidden" name="inss_porcentagem" value="<?= $porcentagem_inss ?>" />
                                                                                                                <input type="hidden" name="ir" value="<?= $ir ?>" />
                                                                                                                <input type="hidden" name="fgts" value="<?= $fgts ?>" />
                                                                                                                <input type="hidden" name="total_descontos" value="<?= $total_descontos ?>" />
                                                                                                                <input type="hidden" name="total_liquido" value="<?= $total_liquido ?>" />
                                                                                                                <input type="hidden" name="abono_pecuniario" value="<?= $abono_pecuniario ?>" />
                                                                                                                <input type="hidden" name="umterco_abono_pecuniario" value="<?= $umterco_abono_pecuniario ?>" />
                                                                                                                <input type="hidden" name="dias_abono_pecuniario" value="<?= $dias_abono_pecuniario ?>" />
                                                                                                                <input type="hidden" name="faltas" value="<?= $faltas ?>">
                                                                                                                    <input type="hidden" name="faltasano" value="<?= $faltasano ?>" />
                                                                                                                    <input type="hidden" name="dias_mes" value="<?= $dias_mes ?>" />
                                                                                                                    <input type="hidden" name="dias_ferias1" value="<?= $dias_ferias1 ?>" />
                                                                                                                    <input type="hidden" name="dias_ferias2" value="<?= $dias_ferias2 ?>" />
                                                                                                                    <input type="hidden" name="valor_total_ferias1" value="<?= $valor_total1 ?>" />
                                                                                                                    <input type="hidden" name="acrescimo_constitucional1" value="<?= $acrescimo_constitucional1 ?>" />
                                                                                                                    <input type="hidden" name="total_remuneracoes1" value="<?= $total_remuneracoes1 ?>" />
                                                                                                                    <input type="hidden" name="valor_total_ferias2" value="<?= $valor_total2 ?>" />
                                                                                                                    <input type="hidden" name="acrescimo_constitucional2" value="<?= $acrescimo_constitucional2 ?>" />
                                                                                                                    <input type="hidden" name="total_remuneracoes2" value="<?= $total_remuneracoes2 ?>" />
                                                                                                                    <input type="hidden" name="ferias_dobradas" value="<?= $ferias_dobradas ?>" />
                                                                                                                    <input type="hidden" name="user" value="<?= $_COOKIE['logado'] ?>" />
                                                                                                                    <input type="hidden" name="base_inss" value="<?= $BASE_INSS ?>" />
                                                                                                                    <input type="hidden" name="base_irrf" value="<?= $BASE_IRRF ?>" />
                                                                                                                    <input type="hidden" name="percentual_irrf" value="<?= $PERCENTUAL_IRRF ?>" />
                                                                                                                    <input type="hidden" name="valor_ddir" value="<?= $VALOR_DDIR ?>" />
                                                                                                                    <input type="hidden" name="qnt_dependente_irrf" value="<?= $QNT_DEPENDENTES_IRRF ?>" />
                                                                                                                    <input type="hidden" name="parcela_deducao_irrf" value="<?= $PARCELA_DEDUCAO_IRRF ?>" />
                                                                                                                    <input type="hidden" name="status" value="1" />
                                                                                                                    <input type="hidden" name="update_movimentos_clt" value="<?= $update_movimentos_clt ?>" />
                                                                                                                    <div style="width:220px; margin:0px auto;">
                                                                                                                        <input type="submit" value="Concluir" class="botao" style="width:100px; float:left;">
                                                                                                                            <input type="button" value="Voltar" class="botao" style="width:100px; float:left;" onClick="javascript:location.href = 'index.php?enc=<?= $link ?>&periodo_aquisitivo=<?= $_REQUEST['periodo_aquisitivo'] ?>&data_inicio=<?= $_REQUEST['data_inicio'] ?>&quantidade_dias=<?= $_REQUEST['quantidade_dias'] ?><?= $link_abono ?><?= $link_faltas ?><?= $link_dobradas ?><?= $despreza_faltas ?>'">
                                                                                                                                </div>
                                                                                                                                </form>
                                                                                                                                </td>
                                                                                                                                </tr>
                                                                                                                                <tr>
                                                                                                                                    <td colspan="4" bgcolor="#F7F7F7">&nbsp;</td>
                                                                                                                                </tr>
                                                                                                                                </table>
        <?php
        // Tela 6 (Lanï¿½ando no Banco de Dados e Redirecionando para a pï¿½gina PDF)
        break;
    case 6:
        //echo "aqui";        
        $id_clt = $_REQUEST['id_clt'];
        $nome = $_REQUEST['nome'];
        $regiao = $_REQUEST['regiao'];
        $projeto = $_REQUEST['projeto'];
        $mes = $_REQUEST['mes'];
        $ano = $_REQUEST['ano'];
        $data_aquisitivo_ini = str_replace(" ", "", $_REQUEST['data_aquisitivo_ini']);
        $data_aquisitivo_fim = str_replace(" ", "", $_REQUEST['data_aquisitivo_fim']);
        $data_inicio = $_REQUEST['data_inicio'];
        $data_fim = $_REQUEST['data_fim'];
        $data_retorno = $_REQUEST['data_retorno'];
        $salario = $_REQUEST['salario'];
        //$salario_variavel = $_REQUEST['salario_variavel'];
        if ($id_clt == 54037) {
            $salario_variavel = "487.93";
        } else {
            $salario_variavel = $_REQUEST['salario_variavel'];
        }

        $remuneracao_base = $_REQUEST['remuneracao_base'];
        $dias_ferias = $_REQUEST['dias_ferias'];
        $valor_dias_ferias = $_REQUEST['valor_dias_ferias'];
        $valor_total_ferias = $_REQUEST['valor_total_ferias'];
        $umterco = $_REQUEST['umterco'];
        $total_remuneracoes = $_REQUEST['total_remuneracoes'];
        $pensao_alimenticia = $_REQUEST['pensao_alimenticia'];
        $inss = $_REQUEST['inss'];
        $inss_porcentagem = substr($_REQUEST['inss_porcentagem'], 2, 2);
        $ir = $_REQUEST['ir'];
        $fgts = $_REQUEST['fgts'];
        $total_descontos = $_REQUEST['total_descontos'];
        $total_liquido = $_REQUEST['total_liquido'];
        $abono_pecuniario = $_REQUEST['abono_pecuniario'];
        $umterco_abono_pecuniario = $_REQUEST['umterco_abono_pecuniario'];
        $dias_abono_pecuniario = $_REQUEST['dias_abono_pecuniario'];
        $faltas = $_REQUEST['faltas'];
        $faltasano = $_REQUEST['faltasano'];
        $dias_mes = $_REQUEST['dias_mes'];
        $dias_ferias1 = $_REQUEST['dias_ferias1'];
        $dias_ferias2 = $_REQUEST['dias_ferias2'];
        $valor_total_ferias1 = $_REQUEST['valor_total_ferias1'];
        $acrescimo_constitucional1 = $_REQUEST['acrescimo_constitucional1'];
        $total_remuneracoes1 = $_REQUEST['total_remuneracoes1'];
        $valor_total_ferias2 = $_REQUEST['valor_total_ferias2'];
        $acrescimo_constitucional2 = $_REQUEST['acrescimo_constitucional2'];
        $total_remuneracoes2 = $_REQUEST['total_remuneracoes2'];
        $diasmes = $_REQUEST['diasmes'];
        $ferias_dobradas = $_REQUEST['ferias_dobradas'];
        $user = $_REQUEST['user'];
        $status = $_REQUEST['status'];
        $update_movimentos_clt = $_REQUEST['update_movimentos_clt'];


        $BASE_INSS = $_REQUEST['base_inss'];
        $BASE_IRRF = $_REQUEST['base_irrf'];
        $PERCENTUAL_IRRF = $_REQUEST['percentual_irrf'];
        $VALOR_DDIR = $_REQUEST['valor_ddir'];
        $QNT_DEPENDENTES_IRRF = $_REQUEST['qnt_dependete_irrf'];
        $PARCELA_DEDUCAO_IRRF = $_REQUEST['parcela_deducao_irrf'];
        $periodo_abono_iniF = date('Y-m-d', strtotime($data_retorno));
        $periodo_abono_fimF = date('Y-m-d', strtotime('+' . $dias_abono_pecuniario . ' days', strtotime($data_retorno)));
// Update no Movimentos CLT
//                                                                                                                                                                                                                                                                  

        if ($_REQUEST['tot_coletiva'] == 1) {
            //FERIAS COLETIVAS
            mysql_query("UPDATE rh_ferias_coletiva SET status = '2' WHERE id_clt = {$id_clt}");
        }
        mysql_query("UPDATE rh_movimentos_clt SET status_ferias = '0' WHERE id_movimento IN($update_movimentos_clt)");

        /**
         * Nï¿½O ï¿½ A MELHOR SOLUï¿½ï¿½O, MAS PELO MENOS VAI DIMINUIR OS PROBLEMAS 
         * SOLUï¿½ï¿½O DESESï¿½RADORA AFIM DE ACABAR COM VALORES NEGATIVOS SENDO GRAVADOS NA TABELA
         */
        if ($salario < 0) {
            $salario = 0;
        }
        if ($salario_variavel < 0) {
            $salario_variavel = 0;
        }
        if ($remuneracao_base < 0) {
            $remuneracao_base = 0;
        }
        if ($dias_ferias < 0) {
            $dias_ferias = 0;
        }
        if ($valor_dias_ferias < 0) {
            $valor_dias_ferias = 0;
        }
        if ($valor_total_ferias < 0) {
            $valor_total_ferias = 0;
        }
        if ($umterco < 0) {
            $umterco = 0;
        }
        if ($total_remuneracoes < 0) {
            $total_remuneracoes = 0;
        }
        if ($pensao_alimenticia < 0) {
            $pensao_alimenticia = 0;
        }
        if ($inss < 0) {
            $inss = 0;
        }
        if ($ir < 0) {
            $ir = 0;
        }
        if ($fgts < 0) {
            $fgts = 0;
        }
        if ($total_descontos < 0) {
            $total_descontos = 0;
        }
        if ($total_liquido < 0) {
            $total_liquido = 0;
        }
        if ($abono_pecuniario < 0) {
            $abono_pecuniario = 0;
        }
        if ($umterco_abono_pecuniario < 0) {
            $umterco_abono_pecuniario = 0;
        }
        if ($dias_abono_pecuniario < 0) {
            $dias_abono_pecuniario = 0;
        }
        if ($ferias_dobradas < 0) {
            $ferias_dobradas = 0;
        }
        if ($valor_total_ferias1 < 0) {
            $valor_total_ferias1 = 0;
        }
        if ($valor_total_ferias2 < 0) {
            $valor_total_ferias2 = 0;
        }
        if ($acrescimo_constitucional1 < 0) {
            $acrescimo_constitucional1 = 0;
        }
        if ($acrescimo_constitucional2 < 0) {
            $acrescimo_constitucional2 = 0;
        }
        if ($total_remuneracoes1 < 0) {
            $total_remuneracoes1 = 0;
        }
        if ($total_remuneracoes2 < 0) {
            $total_remuneracoes2 = 0;
        }
        if ($BASE_INSS < 0) {
            $BASE_INSS = 0;
        }
        if ($BASE_IRRF < 0) {
            $BASE_IRRF = 0;
        }
        if ($PERCENTUAL_IRRF < 0) {
            $PERCENTUAL_IRRF = 0;
        }
        if ($VALOR_DDIR < 0) {
            $VALOR_DDIR = 0;
        }
        if ($QNT_DEPENDENTES_IRRF < 0) {
            $QNT_DEPENDENTES_IRRF = 0;
        }
        if ($PARCELA_DEDUCAO_IRRF < 0) {
            $PARCELA_DEDUCAO_IRRF = 0;
        }

        if ($_COOKIE['logado'] == 40) {
//            echo "INSERT INTO rh_ferias 
//                (id_clt,nome,regiao,projeto,mes,ano,data_aquisitivo_ini,
//                data_aquisitivo_fim,data_ini,data_fim,data_retorno,salario,
//                salario_variavel,remuneracao_base,dias_ferias,valor_dias_ferias,
//                valor_total_ferias,umterco,total_remuneracoes,pensao_alimenticia,
//                inss,inss_porcentagem,ir,fgts,total_descontos,total_liquido,
//                abono_pecuniario,umterco_abono_pecuniario,dias_abono_pecuniario,
//                faltas,faltasano,diasmes,ferias_dobradas,valor_total_ferias1,
//                valor_total_ferias2,acrescimo_constitucional1,
//                acrescimo_constitucional2,total_remuneracoes1,
//                total_remuneracoes2,movimentos,user,data_proc,status, 
//                base_inss,base_irrf, percentual_irrf,valor_ddir, 
//                qnt_dependente_irrf, parcela_deducao_irrf,periodo_abono_ini,periodo_abono_fim ) 
//            VALUES 
//                ('$id_clt','$nome','$regiao','$projeto','$mes','$ano','$data_aquisitivo_ini','$data_aquisitivo_fim','$data_inicio','$data_fim','$data_retorno','$salario','$salario_variavel','$remuneracao_base','$dias_ferias','$valor_dias_ferias','$valor_total_ferias','$umterco','$total_remuneracoes','$pensao_alimenticia','$inss','$inss_porcentagem','$ir','$fgts','$total_descontos','$total_liquido','$abono_pecuniario','$umterco_abono_pecuniario','$dias_abono_pecuniario','$faltas','$faltasano','$diasmes','$ferias_dobradas','$valor_total_ferias1','$valor_total_ferias2','$acrescimo_constitucional1','$acrescimo_constitucional2','$total_remuneracoes1','$total_remuneracoes2','$update_movimentos_clt','$user',NOW(),'$status', '$BASE_INSS','$BASE_IRRF', '$PERCENTUAL_IRRF', '$VALOR_DDIR', '$QNT_DEPENDENTES_IRRF', '$PARCELA_DEDUCAO_IRRF','$periodo_abono_iniF','$periodo_abono_fimF')";
//        
//            exit();
        }

        echo $data_fim_verificada = ($dias_ferias == 0) ? $data_inicio : $data_fim;
        
        mysql_query("INSERT INTO rh_ferias 
                (id_clt,nome,regiao,projeto,mes,ano,data_aquisitivo_ini,
                data_aquisitivo_fim,data_ini,data_fim,data_retorno,salario,
                salario_variavel,remuneracao_base,dias_ferias,valor_dias_ferias,
                valor_total_ferias,umterco,total_remuneracoes,pensao_alimenticia,
                inss,inss_porcentagem,ir,fgts,total_descontos,total_liquido,
                abono_pecuniario,umterco_abono_pecuniario,dias_abono_pecuniario,
                faltas,faltasano,diasmes,ferias_dobradas,valor_total_ferias1,
                valor_total_ferias2,acrescimo_constitucional1,
                acrescimo_constitucional2,total_remuneracoes1,
                total_remuneracoes2,movimentos,user,data_proc,status, 
                base_inss,base_irrf, percentual_irrf,valor_ddir, 
                qnt_dependente_irrf, parcela_deducao_irrf,periodo_abono_ini,periodo_abono_fim ) 
            VALUES 
                ('$id_clt','$nome','$regiao','$projeto','$mes','$ano','$data_aquisitivo_ini','$data_aquisitivo_fim','$data_inicio','$data_fim_verificada','$data_retorno','$salario','$salario_variavel','$remuneracao_base','$dias_ferias','$valor_dias_ferias','$valor_total_ferias','$umterco','$total_remuneracoes','$pensao_alimenticia','$inss','$inss_porcentagem','$ir','$fgts','$total_descontos','$total_liquido','$abono_pecuniario','$umterco_abono_pecuniario','$dias_abono_pecuniario','$faltas','$faltasano','$diasmes','$ferias_dobradas','$valor_total_ferias1','$valor_total_ferias2','$acrescimo_constitucional1','$acrescimo_constitucional2','$total_remuneracoes1','$total_remuneracoes2','$update_movimentos_clt','$user',NOW(),'$status', '$BASE_INSS','$BASE_IRRF', '$PERCENTUAL_IRRF', '$VALOR_DDIR', '$QNT_DEPENDENTES_IRRF', '$PARCELA_DEDUCAO_IRRF','$periodo_abono_iniF','$periodo_abono_fimF')") or die(mysql_error());

        $id_ferias = mysql_insert_id();
        // foi solicitado retirar, pois existe o caso de programar fï¿½rias
        //mysql_query("UPDATE rh_clt SET status='40' WHERE id_clt = '$id_clt'");
        // comentado pq ferias nï¿½o insere mais evento
        // Encriptografando a Variï¿½vel
        $link = encrypt("$regiao&$id_clt&$id_ferias&0");
        $link = str_replace("+", "--", $link);

        print "<script>location.href = 'ferias.php?enc=$link';</script>";
        break;
}


?>
                                                                                                                        </div>
                                                                                                                        </body>
                                                                                                                        </html>
