<?php 
if (!empty($_REQUEST['data_xls'])) {
    $dados = utf8_encode($_REQUEST['data_xls']);

    ob_end_clean();
    header("Content-Encoding: iso-8859-1");
    header("Pragma: private");
    header("Cache-control: private, must-revalidate");
    header("Expires: 0");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=folha.xls");

    echo "\xEF\xBB\xBF";
    echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel/' xmlns='http://www.w3.org/TR/REC-html40'>";
    echo "  <head>";
    echo "  <title>PARTICIPANTES DE VA</title>";
    echo "      <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->";
    echo "  </head>";
    echo "  <body>";
    echo "      $dados";
    echo "  </body>";
    echo "</html>";
    exit;
}
/*
 * ver_folha.php
 * 
 * 00-00-0000
 * 
 * Rotina para processamento de folha de pagamento
 * 
 * Versão: 1.1.0000 - 05/01/2016 - Jacques - Ativação do relatório relatorio_rescisao_2.php no lugar do relatorio_rescisao.php
 * 
 * @author Não definido
 * 
 * 
 */

// Verificando se o usuário está logado
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../../../login.php">Logar</a>';
    exit;
}

$arrayDeveloper = array(179, 158, 87);

// Incluindo Arquivos
require('../../conn.php');
require_once '../../classes/LogClass.php';
include('../../funcoes.php');
include('../../classes_permissoes/acoes.class.php');
include('../../classes/FolhaClass.php');
include('../../wfunction.php');
//require_once ('../../../../vendor/autoload.php');
//require_once ('../../../../framework/app/controller/helpers/encryptClass.php');
//error_reporting(0);
$ACOES = new Acoes();
$objFolha = new Folha();
$usuarioLogado = carregaUsuario();
// Buscando a Folha
if (isset($_REQUEST['enc'])) {
    list($regiao, $folha) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
} else {
    $regiao = $_REQUEST['id_regiao'];
    $folha = $_REQUEST['id_folha'];
}
//print_r($folha);

// Consulta da Data
$data = mysql_result(mysql_query("SELECT data_proc FROM rh_folha WHERE id_folha = '$folha'"), 0);


// Se a Folha é nova...
if ($data > date('2010-06-09')) {

    // Incluindo Arquivos
    include('../../classes/calculos.php');
    include('../../classes/abreviacao.php');
    include('../../classes/formato_valor.php');
    include('../../classes/formato_data.php');
    include('../../classes/valor_proporcional.php');
    include('../../classes/MovimentoClass.php');
    include('../../classes/regiao.php');

    $Regi = new regiao();
    $Trab = new proporcional();
    $objMovimento = new Movimentos();

    // Consulta da Folha
    $qr_folha = mysql_query("SELECT *, date_format(data_inicio, '%d/%m/%Y') AS data_inicio_br,
                            date_format(data_fim, '%d/%m/%Y') AS data_fim_br,
                            date_format(data_proc, '%d/%m/%Y') AS data_proc_br,
                            (SELECT COUNT(id_clt) FROM rh_folha_proc WHERE id_folha = rh_folha.id_folha AND status_clt NOT IN(10,200)) as total_rescindidos
                            FROM rh_folha WHERE id_folha = '$folha' AND status = '3'");

//    if($_COOKIE['logado'] == 179){
//        echo "SELECT *, date_format(data_inicio, '%d/%m/%Y') AS data_inicio_br,
//                            date_format(data_fim, '%d/%m/%Y') AS data_fim_br,
//                            date_format(data_proc, '%d/%m/%Y') AS data_proc_br,
//                            (SELECT COUNT(id_clt) FROM rh_folha_proc WHERE id_folha = rh_folha.id_folha AND status_clt NOT IN(10,200)) as total_rescindidos
//                            FROM rh_folha WHERE id_folha = '$folha' AND status = '3'";
//    }

    $row_folha = mysql_fetch_array($qr_folha);
    $data_inicio = $row_folha['data_inicio'];
    $data_fim = $row_folha['data_fim'];
    $ano = $row_folha['ano'];
    $mes = $row_folha['mes'];
    $mes_int = (int) $mes;    
    
    if($row_folha['id_folha'] == 113){
        $row_folha['total_inss'] -= 724.57;
    } else if($row_folha['id_folha'] == 114){
        $row_folha['total_inss'] -= 393.85;    
    } else if($row_folha['id_folha'] == 119){
        $row_folha['total_inss'] -= 558.41;
    } else if($row_folha['id_folha'] == 118){
        $row_folha['total_inss'] -= -1722.17;
    }

    // Consulta do Usuário que gerou a Folha
    $qr_usuario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$row_folha[user]'");

    // Redefinindo Variáveis de Décimo Terceiro
    if ($row_folha['terceiro'] != 1) {
        $decimo_terceiro = NULL;
    } else {
        $decimo_terceiro = 1;
        $tipo_terceiro = $row_folha['tipo_terceiro'];
    }

    $qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$row_folha[regiao]' AND id_projeto = '$row_folha[projeto]]'");
    $row_empresa = mysql_fetch_assoc($qr_empresa);

    // Consulta da Região
    $qr_regiao = mysql_query("SELECT id_regiao, regiao FROM regioes WHERE id_regiao = '$row_folha[regiao]'");
    $regiao = mysql_result($qr_regiao, 0, 0);

    // Consulta do Projeto
    $qr_projeto = mysql_query("SELECT id_projeto, nome, id_master FROM projeto WHERE id_projeto = '$row_folha[projeto]'");
    $projeto = mysql_result($qr_projeto, 0, 0);

    // Consulta dos Participantes da Folha
    if ($_COOKIE['logado'] == 87) {



        $qr_participantes = mysql_query("SELECT ad.*, C.nome as funcao FROM                                                                                                                                                                                                                       
                                         (SELECT A.*,B.id_curso as curso,                                                                                                                                                                                                             
                                         (SELECT id_curso_de FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '$row_folha[data_incio]' ORDER BY id_transferencia ASC LIMIT 1) AS de,              
                                         (SELECT id_curso_para FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '$row_folha[data_incio]' ORDER BY id_transferencia DESC LIMIT 1) AS para          
                                                                                                                                                                                                                                    
                                         FROM rh_folha_proc as A                                                                                                                                                                         
                                         LEFT JOIN rh_clt as B                                                                                                                                                                           
                                         ON B.id_clt = A.id_clt                                                                                                                                                                          
                                                                                                                                                                                                                                    
                                         WHERE A.id_folha = '$folha' AND A.status IN(3,4) ORDER BY A.nome ASC) as ad                                                                                                                       
                                         LEFT JOIN curso AS C ON (IF(ad.para IS NOT NULL,C.id_curso=ad.para,IF(ad.de IS NOT NULL,C.id_curso=ad.de,C.id_curso=curso)))") or die(mysql_error());
    } else {

        $qr_participantes = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha' AND status IN(3,4) ORDER BY nome ASC");
    }
    $total_participantes = mysql_num_rows($qr_participantes);

    // Definindo Mês da Folha
    $meses = array('Erro', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');

    if (!empty($decimo_terceiro)) {
        switch ($tipo_terceiro) {
            case 1:
                $mes_folha = '13º Primeira parcela';
                break;
            case 2:
                $mes_folha = '13º Segunda parcela';
                break;
            case 3:
                $mes_folha = '13º Integral';
                break;
        }
    } else {
        $mes_folha = "$meses[$mes_int] / $ano";
    }

    // Percentual RAT
    if ($ano >= 2011 AND $ano <= 2014) {
        $percentual_rat = '0.01';
    } elseif ($ano >= 2015) {
        $percentual_rat = '0.02';
    } else {
        $percentual_rat = '0.03';
    }

    if ($regiao == 47 && $folha == 2139) {
        $percentual_rat = '0.01';
    }

    list($regiao, $id_folha) = explode('&', decrypt(str_replace('--', '+', str_replace('+', '--', $_REQUEST['enc']))));

    // Encriptografando Links
    $link_voltar = 'folha.php?enc=' . str_replace('+', '--', encrypt("$regiao&1")) . '&tela=1';
    $link_lista_banco = 'ver_lista_banco.php?enc=' . str_replace('+', '--', $_REQUEST['enc']) . '&tela=1';
    $link_pagamento_lote = 'pg_lote.php?enc=' . str_replace('+', '--', $_REQUEST['enc']) . '&tela=1';
    $link_dados_bancarios = 'confere_banco.php?enc=' . str_replace('+', '--', $_REQUEST['enc']) . '&tela=1';
    $link_relatorio = 'relatorio_movimentos.php?enc=' . str_replace('+', '--', $_REQUEST['enc']);
    $link_rescisao = "relatorio_rescisao_2.php?regiao={$regiao}&id_folha={$id_folha}";
    $link_totalizadorObj = '../folha_oo/totalizador.php?enc=' . str_replace('+', '--', $_REQUEST['enc']);
    $link_rel_sindical = 'relatorio_sindical.php?enc=' . str_replace('+', '--', $_REQUEST['enc']);
    $link_rel_folha_simples = 'relatorio_folha_simples.php?enc=' . str_replace('+', '--', $_REQUEST['enc']);
    $link_rel_folha_pensao = 'relatorio_folha_pensoes.php?enc=' . str_replace('+', '--', $_REQUEST['enc']);
    $link_rel_folha_iabas = 'relatorio_folha_finalizada.php?enc=' . str_replace('+', '--', $_REQUEST['enc']);
    $link_rel_folha_pis = 'relatorio_folha_pis.php?enc=' . str_replace('+', '--', $_REQUEST['enc']);
    $linkGerarPdf = "gerarPdf.php?id_folha={$id_folha}";
    ?>
    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
            <title>:: Intranet :: Folha Finalizada de CLT (<?= $folha ?>)</title>
            <link href="sintetica/folha.css" rel="stylesheet" type="text/css">
            <link href="http://<?= $_SERVER['SERVER_NAME'] ?>/intranet/resources/css/font-awesome.css" rel="stylesheet" media="all">
            <link href="http://<?= $_SERVER['SERVER_NAME'] ?>/intranet/resources/css/main.css" rel="stylesheet" media="all">
            <link href="../../favicon.ico" rel="shortcut icon">
            <link href="../../js/highslide.css" rel="stylesheet" type="text/css" />
            <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
            <script src="../../js/highslide-with-html.js" type="text/javascript"></script>
            <script src="../../resources/js/tooltip.js"></script>
            <script src="../../resources/js/main.js" type="text/javascript"></script>
            <script src="../../js/global.js" type="text/javascript"></script>
            <script type="text/javascript">
                hs.graphicsDir = '../../images-box/graphics/';
                hs.outlineType = 'rounded-white';
                $(function () {

                    /****************MOSTRA TODOS *****************************/
                    $(".legenda .mostrar_todos").click(function () {
                        history.go(0);
                    });

                    /*****************ESCONDE OS TOTAIS************************/
                    $(".legenda").click(function () {
                        $(".totais").hide();
                    });

                    /****************MOSTRA TODOS QUE ENTRARAM NA FOLHA********/
                    $(".legenda .entrada").click(function () {
                        $(".destaque").each(function () {
                            $(this).find("span").parents("tr").show();
                            $(this).find("span").not(".entrada").parents("tr").hide();
                        });
                        $(".esconde_geral").hide();
                        $(".totais_entrada").show();
                        $("#conteudo_para_esconder").hide();
                    });

                    /****************MOSTRA TODOS COM EVENTOS LANÇADOS*********/
                    $(".legenda .evento").click(function () {
                        $(".destaque").each(function () {
                            $(this).find("span").parents("tr").show();
                            $(this).find("span").not(".evento").parents("tr").hide();
                        });
                        $(".esconde_geral").hide();
                        $(".totais_linceca").show();
                        $("#conteudo_para_esconder").hide();
                    });

                    /****************MOSTRA TODOS COM FALTAS*********/
                    $(".legenda .faltas").click(function () {
                        $(".destaque").each(function () {
                            $(this).find("span").parents("tr").show();
                            $(this).find("span").not(".faltas").parents("tr").hide();
                        });
                        $(".esconde_geral").hide();
                        $(".totais_faltas").show();
                        $("#conteudo_para_esconder").hide();
                    });

                    /****************MOSTRA TODOS COM FALTAS*********/
                    $(".legenda .ferias").click(function () {
                        $(".destaque").each(function () {
                            $(this).find("span").parents("tr").show();
                            $(this).find("span").not(".ferias").parents("tr").hide();
                        });
                        $(".esconde_geral").hide();
                        $(".totais_ferias").show();
                        $("#conteudo_para_esconder").hide();
                    });

                    /****************MOSTRA TODOS EM RESCISAO*********/
                    $(".legenda .rescisao").click(function () {
                        $(".destaque").each(function () {
                            $(this).find("span").parents("tr").show();
                            $(this).find("span").not(".rescisao").parents("tr").hide();
                        });
                        $(".esconde_geral").hide();
                        $(".totais_rescisao").show();
                        $("#conteudo_para_esconder").hide();
                    });
                });
            </script>
            <style type="text/css">
                .highslide-html-content { width:600px; padding:0px; }
                .essatb{
                    margin: 0px auto;
                    text-align: left;
                    width: 98%;
                    font-size: 11px;
                    line-height: 40px;
                }
                .mostrar_todos{
                    background: #000;
                }
                .nota{
                    cursor: pointer;
                }
                .totais_entrada, .totais_linceca, .totais_faltas, .totais_ferias, .totais_rescisao{
                    display: none;
                    font-weight: bold;
                    text-align: center;
                }
                .esconde { display: none; }

                @media print {
                    .printDoc {
                        display:none!important;
                    }           
                    .printDocBlock {
                        display:block;
                    }           
                }
            </style>
        </head>
        <body>
            <div id="corpo">

                <table cellspacing="4" cellpadding="0" id="topo">
                    <tr height="30">
                        <td width="15%" rowspan="3" valign="middle" align="center">
                            <img src="../../imagens/logomaster<?= mysql_result($qr_projeto, 0, 2) ?>.gif" width="110" height="79">
                        </td>
                        <td  style="font-size:12px;">
                            <b><?= mysql_result($qr_projeto, 0, 1) . ' (' . $mes_folha . ')' ?></b>               
                        </td>
                        <td colspan="2">  <b>CNPJ: </b><?php echo $row_empresa['cnpj']; ?></td>
                        <td width="20%"><b>Participantes:</b> <?= $total_participantes ?></td>
                    </tr>

                    <tr>
                        <td width="35%"><b>Data da Folha:</b> <?= $row_folha['data_inicio_br'] . ' &agrave; ' . $row_folha['data_fim_br'] ?></td>
                        <td width="30%"><b>Região:</b> <?= $regiao . ' - ' . mysql_result($qr_regiao, 0, 1) ?></td>
                        <td><b>Total de rescindidos:</b> <?= $row_folha['total_rescindidos'] ?></td>
                    </tr>
                    <tr>
                        <td><b>Data de Processamento:</b> <?= $row_folha['data_proc_br'] ?></td>
                        <td><b>Gerado por:</b> <?= @abreviacao(mysql_result($qr_usuario, 0), 2) ?> <span id="cookie" style="display:none"><?php echo $_COOKIE['logado'] ?></span></td>
                        <td><b>Folha:</b> <span id="id_folha"><?= $folha ?></span></td>
                    </tr>

                </table>
                <div style="margin-botton: 15px;margin-top: 10px"><p style="text-align: center" class="printDocBlock esconde"><?= 'Gerado em ' . date('d/m/Y') . ' às ' . date('h:i:s') . ' por ' . $usuarioLogado['nome'] . ' - F71 Sistemas Web' ?></p></div>

                <table class="printDoc" cellpadding="0" cellspacing="1" id="folha">
                    <tr>
                        <td colspan="2">
                            <a href="<?= $link_voltar ?>" class="voltar">Voltar</a>
                        </td>
                        <td colspan="8">
                            <div style="float:right;">
                                <div class="legenda"><div class="nota mostrar_todos"></div>Todos</div>
                                <div class="legenda"><div class="nota entrada"></div>Admissão</div>
                                <div class="legenda"><div class="nota evento"></div>Licen&ccedil;a</div>
                                <div class="legenda"><div class="nota faltas"></div>Faltas</div>
                                <div class="legenda"><div class="nota ferias"></div>F&eacute;rias</div>
                                <div class="legenda"><div class="nota rescisao"></div>Rescis&atilde;o</div>
                            </div>
                        </td>
                    </tr>
                </table>

                <p class="printDoc" style="text-align: right;"><button type="button" value="Exportar" class="btn btn-success" id="exportarExcel"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button> <button type="button" id="imprimirPdf" class="printDoc btn btn-danger" >Gerar PDF</button> </p>
                
                <form class="printDoc" style="display: none" action="../../relatorios/exportTablePdf.php" method="post" id="formPdf">
                    
                    <input type="text" name="titlePdf" id="titlePdf" value="Folha Finalizada - <?= mysql_result($qr_projeto, 0, 1) . ' (' . $mes_folha . ')' ?>"/>
                    <textarea name="tabelaPdf" id="tabelaPdf" value="" ></textarea>
                </form>
                <div id="relatorio_exp">
                    <div id="tabelaExpPDF">
                        <table cellpadding="0" cellspacing="1" id="tabela" class="essatb">
                            <tr class="secao">
                                <td >COD</td>
                                <td align="left" style="padding-left:5px;" >NOME</td>
                                <td ><?php
                                    if (isset($decimo_terceiro)) {
                                        echo 'MESES';
                                    } else {
                                        echo 'DIAS';
                                    }
                                    ?></td>
                                <td >BASE</td>
                                <td >RENDIMENTOS</td>
                                <td>DESCONTOS</td>
                                <td >INSS</td>
                                <td>IRRF</td>
                                <td >FAM&Iacute;LIA</td>
                                <td>L&Iacute;QUIDO</td>
                                <td class="printDoc">AÇÕES</td>
                            </tr>

                            <?php
                            while ($row_participante = mysql_fetch_array($qr_participantes)) {

                                //////***************FÓRMULA TOP SECRET PARA OCULTAR PARTICIPANTES************************///// 
    //                        if ($row_participante['id_clt'] == '4213' or $row_participante['id_clt'] == '4425')
    //                            continue;
                                //totalizador da BASE INSS 13 de rescisao  
                                $total_base_13_rescisao += $row_participante['base_inss_13_rescisao'];
                                
                                if(($row_participante['status_clt'] == 65) || ($row_participante['status_clt'] == 63)){
                                    $total_base_13_rescisao_fgts += $row_participante['base_inss_13_rescisao'];
                                }
                                
                                //Consulta Pensão Alimentícia
                                $sqlPensao = "SELECT SUM(valor_mov)
                                              FROM itens_pensao_para_contracheque AS A
                                              LEFT JOIN favorecido_pensao_assoc AS B ON (REPLACE(REPLACE(B.cpf,'.',''),'-','') = A.cpf_favorecido)
                                              WHERE A.id_folha = '$folha' AND status = 1 AND B.id_clt = '{$row_participante['id_clt']}'";
                                $queryPensao = mysql_query($sqlPensao);
                                $valorPensao = mysql_result($queryPensao,0);
                                ?>

                                <tr class="linha_<?php
                        if ($linha++ % 2 == 0) {
                            echo 'um';
                        } else {
                            echo 'dois';
                        }
                                ?> destaque">


                                    <td><?= $row_participante['id_clt'] ?></td>
                                    <td  align="left">

                                        <?php
                                        $contracheque = str_replace('+', '--', encrypt("$regiao&$row_participante[id_clt]&$folha"));
                                        $data_entrada = @mysql_result(mysql_query("SELECT data_entrada FROM rh_clt WHERE id_clt = '" . $row_participante['id_clt'] . "'"), 0);
                                        $licensas = array('20', '30', '50', '51', '52', '80', '90', '100', '110');
                                        $ferias = array('40');
                                        $rescisao = array('60', '61', '62', '63', '64', '65', '66', '81', '101');
                                        $faltas = mysql_num_rows(mysql_query("
                                              SELECT * FROM rh_movimentos_clt WHERE id_mov IN(62,232)  AND id_clt = $row_participante[id_clt]
                                              AND id_movimento IN(" . $row_folha[ids_movimentos_estatisticas] . ") 
                                              "));


                                        if (in_array($row_participante['status_clt'], $rescisao)) {
                                            $qr_rescisao = mysql_query("SELECT * FROM rh_recisao WHERE id_clt = '$row_participante[id_clt]' AND status = 1");
                                            $row_resc = mysql_fetch_assoc($qr_rescisao);

                                            $pagina = (substr($row_resc['data_proc'], 0, 10) >= '2013-04-04') ? 'nova_rescisao_2.php' : 'nova_rescisao.php';
                                            
                                            $link_folha = '../recisao/' . $pagina . '?enc=' . str_replace('+', '--', encrypt("$regiao&$row_participante[id_clt]&$row_resc[id_recisao]"));
                                            
                                            if($row_resc['rescisao_nova']) $pagina = "/intranet/?class=rescisao/processar&id_clt={$row_participante['id_clt']}";
                                            
                                        }
                                        /* elseif(in_array($row_participante['status_clt'], $ferias)){

                                          $qr_ferias = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$row_participante[id_clt]' AND status = 1");
                                          $row_ferias = mysql_fetch_assoc($qr_ferias);
                                          $link_folha = '../arquivos/ferias/ferias_'.$row_participante['id_clt'].'_'.$row_ferias['id_ferias'].'.pdf';


                                          } */ else {
                                            $link_folha = '../contracheque/geracontra_4.php?enc=' . $contracheque;
                                            $link_folha = '../contracheque/contra_cheque_oo.php?enc=' . $contracheque;
                                        }
                                        ?>

                                        <a style="text-align:center" href="<?php echo $link_folha; ?>"  target="_blank" class="printDoc participante" >
                                            <span title="Gerar contracheque de <?= $row_participante['nome'] ?>" class="
                                            <?php
                                            if ($data_entrada > $data_inicio) {
                                                echo 'entrada';
                                                //CALCULA TOTAL PARA ADMISSÃO
                                                $array_totais["entrada"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                                                $array_totais["entrada"]["rendimento"] += $row_participante['rend'];
                                                $array_totais["entrada"]["desconto"] += $row_participante['desco'];
                                                $array_totais["entrada"]["inss"] += $row_participante['a5020'] + $row_participante['a5035'] + $row_participante['inss_dt'] + $row_participante['inss_rescisao'];
                                                $array_totais["entrada"]["irrf"] += $row_participante['a5021'] + $row_participante['a5036'] + $row_participante['ir_dt'] + $row_participante['ir_rescisao'];
                                                $array_totais["entrada"]["familia"] += $row_participante['a5022'];
                                                $array_totais["entrada"]["liquido"] += $row_participante['salliquido'];
                                            } elseif (in_array($row_participante['status_clt'], $licensas)) {
                                                echo 'evento';

                                                //CALCULA O TOTAL LICENÇA 
                                                $array_totais["linceca"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                                                $array_totais["linceca"]["rendimento"] += $row_participante['rend'];
                                                $array_totais["linceca"]["desconto"] += $row_participante['desco'];
                                                $array_totais["linceca"]["inss"] += $row_participante['a5020'] + $row_participante['a5035'] + $row_participante['inss_dt'] + $row_participante['inss_rescisao'];
                                                $array_totais["linceca"]["irrf"] += $row_participante['a5021'] + $row_participante['a5036'] + $row_participante['ir_dt'] + $row_participante['ir_rescisao'];
                                                $array_totais["linceca"]["familia"] += $row_participante['a5022'];
                                                $array_totais["linceca"]["liquido"] += $row_participante['salliquido'];
                                            } elseif (in_array($row_participante['status_clt'], $ferias) and $row_participante['dias_trab'] != 30) {
                                                echo 'ferias';

                                                //CALCULA O TOTAL FÉRIASa
                                                $array_totais["ferias"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                                                $array_totais["ferias"]["rendimento"] += $row_participante['rend'];
                                                $array_totais["ferias"]["desconto"] += $row_participante['desco'];
                                                $array_totais["ferias"]["inss"] += $row_participante['a5020'] + $row_participante['a5035'] + $row_participante['inss_dt'] + $row_participante['inss_rescisao'];
                                                $array_totais["ferias"]["irrf"] += $row_participante['a5021'] + $row_participante['a5036'] + $row_participante['ir_dt'] + $row_participante['ir_rescisao'];
                                                $array_totais["ferias"]["familia"] += $row_participante['a5022'];
                                                $array_totais["ferias"]["liquido"] += $row_participante['salliquido'];
                                            } elseif (in_array($row_participante['status_clt'], $rescisao)) {
                                                echo 'rescisao';

                                                //CALCULA O TOTAL RECISAO
                                                $array_totais["rescisao"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                                                $array_totais["rescisao"]["rendimento"] += $row_participante['rend'];
                                                $array_totais["rescisao"]["desconto"] += $row_participante['desco'];
                                                $array_totais["rescisao"]["inss"] += $row_participante['a5020'] + $row_participante['a5035'] + $row_participante['inss_dt'] + $row_participante['inss_rescisao'];
                                                $array_totais["rescisao"]["irrf"] += $row_participante['a5021'] + $row_participante['a5036'] + $row_participante['ir_dt'] + $row_participante['ir_rescisao'];
                                                $array_totais["rescisao"]["familia"] += $row_participante['a5022'];
                                                $array_totais["rescisao"]["liquido"] += $row_participante['salliquido'];
                                            } elseif (!empty($faltas)) {
                                                echo 'faltas';

                                                //CALCULA O TOTAL FALTAS
                                                $array_totais["faltas"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                                                $array_totais["faltas"]["rendimento"] += $row_participante['rend'];
                                                $array_totais["faltas"]["desconto"] += $row_participante['desco'];
                                                $array_totais["faltas"]["inss"] += $row_participante['a5020'] + $row_participante['a5035'] + $row_participante['inss_dt'] + $row_participante['inss_rescisao'];
                                                $array_totais["faltas"]["irrf"] += $row_participante['a5021'] + $row_participante['a5036'] + $row_participante['ir_dt'] + $row_participante['ir_rescisao'];
                                                $array_totais["faltas"]["familia"] += $row_participante['a5022'];
                                                $array_totais["faltas"]["liquido"] += $row_participante['salliquido'];
                                            } else {
                                                echo 'normal';
                                            }
                                            ?>
                                                  "><?php echo abreviacao($row_participante['nome'], 4, 1); ?>

                                            </span>   
                                        </a>
                                        <span class="printDocBlock esconde <?php
                                    if ($data_entrada > $data_inicio) {
                                        echo 'entrada';
                                    } elseif (in_array($row_participante['status_clt'], $licensas)) {
                                        echo 'evento';
                                    } elseif (in_array($row_participante['status_clt'], $ferias) and $row_participante['dias_trab'] != 30) {
                                        echo 'ferias';
                                    } elseif (in_array($row_participante['status_clt'], $rescisao)) {
                                        echo 'rescisao';
                                    } elseif (!empty($faltas)) {
                                        echo 'faltas';
                                    } else {
                                        echo 'normal';
                                    }
                                            ?>
                                              "><?php echo abreviacao($row_participante['nome'], 4, 1); ?>

                                        </span>          

                                        <?php
                                        if ($_COOKIE[debug] == 666) {
                                            echo 'BASE INSS: ' . number_format($row_participante['base_inss'], 2, ',', '.') . '<br>';
                                            echo 'BASE FGTS: ' . number_format($row_participante['fgts'] / 0.08, 2, ',', '.') . '<br>';
                                            echo 'FGTS: ' . number_format($row_participante['fgts'], 2, ',', '.') . '<br>';
                                            echo 'BASE 13: ' . $row_participante['base_inss_13_rescisao'] . '<br>';
                                        }
                                        ?>
                                    </td>
                                    <td><?php
                                if ($row_participante['valor_dt'] != '0.00') {
                                    echo $row_participante['meses'];
                                } else {
                                    echo $row_participante['dias_trab'];
                                }
                                        ?>
                                    </td>
                                    <td><?= formato_real($row_participante['sallimpo_real'] + $row_participante['valor_dt']) ?></td>
                                    <td><?= formato_real($row_participante['rend']) ?></td>
                                    <!--<td><?= formato_real($row_participante['desco'] + $valorPensao - $row_participante['a5035'] - $row_participante['a5036']) ?></td>-->
                                    <td><?= formato_real($row_participante['desco'] - $row_participante['a5035'] - $row_participante['a5036']) ?></td>
                                    <td><?= formato_real($row_participante['a5020'] + $row_participante['a5035'] + $row_participante['inss_dt'] + $row_participante['inss_rescisao']) ?></td>
                                    <td><?= formato_real($row_participante['a5021'] + $row_participante['a5036'] + $row_participante['ir_dt'] + $row_participante['ir_rescisao']) ?></td>
                                    <td><?= formato_real($row_participante['a5022']) ?></td>
                                    <td><?= formato_real($row_participante['salliquido']) ?></td>
                                    <td class="printDoc">
                                        <a style="text-decoration: none" title="Atualizar Cadastro" href="../alter_clt.php?clt=<?= $row_participante['id_clt'] ?>&amp;pro=<?= $projeto ?>&amp;pagina=/intranet/rh/folha/ver_folha.php?enc=<?= $_REQUEST['enc'] ?>" onclick="return hs.htmlExpand(this, {objectType: 'iframe'})">
                                            <img src="../../imagens/icone_lapis.png" width="16" height="16" border="0">
                                        </a>
                                        <a style="text-decoration: none" title="Alterar Forma de Pagamento para Este Movimento" href="../view/alt_tipo_pag.php?regiao=<?= $regiao ?>&amp;projeto=<?= $projeto ?>&amp;folha=<?= $folha ?>&amp;id_clt=<?= $row_participante['id_clt'] ?>&amp;tipo_pg=<?= $row_participante['tipo_pg'] ?>" onclick="return hs.htmlExpand(this, {objectType: 'iframe'})" class=" ">
                                            <img src="../../imagens/btn-forma-de-pagamento.png" width="16" height="16" border="0">
                                        </a>
                                    </td>
                                </tr>

                                <?php
                                $totalizador_salario_maternidade += $row_participante['a6005'];


                                $ddir += $row_participante['a5049'];
                            } // Fim do Loop de Participantes 
                            ?>

                            <tr class="totais">
                                <td colspan="2">
                                    <?php // if ($total_participantes > 10) {  ?>
                                    <!--<a href="#corpo" class="ancora">Subir ao topo</a>-->
                                    <?php // }  ?></td>
                                <td>TOTAIS:</td>

                                <td><?= formato_real($row_folha['total_limpo'] + $row_folha['valor_dt']) ?></td>
                                <td><?= formato_real($row_folha['rendi_indivi']) ?></td>
                                <td><?= formato_real($row_folha['descon_indivi']) ?></td>
                                <td><?= formato_real($row_folha['total_inss'] + $row_folha['inss_dt'] + $row_folha['inss_rescisao'] + $row_folha['inss_ferias']) ?></td>
                                <td><?= formato_real($row_folha['total_irrf'] + $row_folha['ir_dt'] + $row_folha['ir_rescisao'] + $row_folha['ir_ferias']) ?></td>
                                <td><?= formato_real($row_folha['total_familia']) ?></td>
                                <td><?= formato_real($row_folha['total_liqui']) ?></td>
                            </tr>

                            <?php foreach ($array_totais as $key => $total) { ?>

            <!--                        <tr class="totais_<?php echo $key; ?> esconde_geral">
                                        <td colspan="2">
                                <?php // if ($total_participantes > 10) {  ?>
                                                <a href="#corpo" class="ancora">Subir ao topo</a>
                                <?php // }  ?>
                                        </td>-->
            <!--                            <td>TOTAIS:</td>
                                         ********************** TOTAIS DE ENTRADAS ***************************** 
                                        <td><?= formato_real($total["base"]) ?></td>
                                        <td><?= formato_real($total["rendimento"]) ?></td>
                                        <td><?= formato_real($total["desconto"]) ?></td>
                                        <td><?= formato_real($total["inss"]) ?></td>
                                        <td><?= formato_real($total["irrf"]) ?></td>
                                        <td><?= formato_real($total["familia"]) ?></td>
                                        <td><?= formato_real($total["liquido"]) ?></td>
                                    </tr>-->

                            <?php } ?>

                        </table>
                    </div>
                                <form id="form2" method="post">            
                                    <input type="hidden" id="data_xls" name="data_xls" value="">
                                </form>
                 </div>
                <div style="margin-botton: 15px;margin-top: 10px"><p style="text-align: center" class="printDocBlock esconde"><?= 'Gerado em ' . date('d/m/Y') . ' às ' . date('h:i:s') . ' por ' . $usuarioLogado['nome'] . ' - F71 Sistemas Web' ?></p></div>
                <div class="printDoc" id="estatisticas">



                    <?php
                    // Resumo por Movimento
                    // Resumo por Movimento
//                    $movimentos_codigo = array('0001','0002',
//                        '5029',
//                        '5037', '5037',
//                        '4007', '4007',
//                        '5020', '5031', '5035', '4007',
//                        '5021', '5030', '5036', '4007',
//                        '5022', '5019',
//                        '7001', '8003',
//                        '6005');
//
//                    $movimentos_nome = array('SAL&Aacute;RIO','SALDO DE SALARIO RESCISAO',
//                        'D&Eacute;CIMO TERCEIRO SAL&Aacute;RIO',
//                        'F&Eacute;RIAS', 'VALOR PAGO NAS F&Eacute;RIAS',
//                        'RESCIS&Atilde;O', 'VALOR PAGO NA RESCIS&Atilde;O',
//                        'INSS', 'INSS SOBRE D&Eacute;CIMO TERCEIRO', 'INSS SOBRE F&Eacute;RIAS', 'INSS SOBRE RESCIS&Atilde;O',
//                        'IRRF', 'IRRF SOBRE D&Eacute;CIMO TERCEIRO', 'IRRF SOBRE F&Eacute;RIAS', 'IRRF SOBRE RESCIS&Atilde;O',
//                        'SAL&Aacute;RIO FAMILIA', 'CONTRIBUI&Ccedil;&Atilde;O SINDICAL',
//                        'DESCONTO VALE TRANSPORTE', 'DESCONTO VALE REFEI&Ccedil;&Atilde;O',
//                        'SAL&Aacute;RIO MATERNIDADE');
//
//                    $movimentos_tipo = array('CREDITO','DEBITO',
//                        'CREDITO',
//                        'CREDITO', 'DEBITO',
//                        'CREDITO', 'DEBITO',
//                        'DEBITO', 'DEBITO', 'DEBITO', 'DEBITO',
//                        'DEBITO', 'DEBITO', 'DEBITO', 'DEBITO',
//                        'CREDITO', 'DEBITO',
//                        'DEBITO', 'DEBITO',
//                        'CREDITO');
//
//                    $movimentos_valor = array($row_folha['total_limpo'],$saldo_rescisao,
//                        $row_folha['valor_dt'],
//                        $row_folha['valor_ferias'], $row_folha['valor_pago_ferias'],
//                        $row_folha['valor_rescisao'], $row_folha['valor_pago_rescisao'],
//                        $row_folha['total_inss'], $row_folha['inss_dt'], $row_folha['inss_ferias'], $row_folha['inss_rescisao'],
//                        $row_folha['total_irrf'], $row_folha['ir_dt'], $row_folha['ir_ferias'], $row_folha['ir_rescisao'],
//                        $row_folha['total_familia'], $row_folha['total_sindical'],
//                        $row_folha['total_vt'], $row_folha['total_vr'],
//                        $totalizador_salario_maternidade);
//
                    // Adicionando Mais Movimentos
                    if (!empty($row_folha['ids_movimentos_estatisticas'])) {



                        $chave = count($movimentos_codigo);

                        $ids_movimentos_estatisticas = $row_folha['ids_movimentos_estatisticas'];
                        settype($movimentos_listados, 'array');


                        $qr_movimentos = mysql_query("SELECT cod_movimento, nome_movimento, SUM(valor_movimento) as total ,tipo_movimento, id_mov 
			   								 FROM `rh_movimentos_clt`                                                                                         
											 WHERE id_movimento IN($ids_movimentos_estatisticas) 
                                                                                             AND id_mov NOT IN(62)
											 GROUP BY id_mov") or die(mysql_error());
                        while ($movimento = mysql_fetch_array($qr_movimentos)) {

                            $chave++;
                            $movimentos_listados[] = $movimento['id_mov'];
                            $movimentos_codigo[] = $movimento['cod_movimento'];
                            $movimentos_nome[] = $movimento['nome_movimento'];
                            $movimentos_tipo[] = $movimento['tipo_movimento'];
                            $movimentos_valor[$chave] += $movimento['total'];
                        }

                        unset($chave);

                        // Organizado as Arrays pelo Código
                        array_multisort($movimentos_codigo, $movimentos_nome, $movimentos_tipo, $movimentos_valor);
                    }
                    ?>

                    <div class="printDoc" id="conteudo_para_esconder">
                        <div id="botoes" style="width: 300px; height: 100px;">            
                            <a href="ver_folha_analitica.php?enc=<?= $_REQUEST['enc'] ?>" class="" style="background: #eee; width: 150px; height: 30px; padding: 10px; border-radius: 4px; border: 1px solid #999;">Folha Analitica</a>
                            <br><br><br>
                            <a href="ver_folha_analitica_1.php?enc=<?= $_REQUEST['enc'] ?>" class="" style="background: #eee; width: 240px; height: 30px; padding: 10px; border-radius: 4px; border: 1px solid #999;">Folha Analitica Detalhada</a>
                            <br>
                        </div>

                        <div id="resumo" class="resumo">

                            <table cellspacing="1" widht="50%">
                                <tr>
                                    <td colspan="5" class="secao_pai">Resumo por Movimento* </td>
                                </tr>
                                <tr class="secao">
                                    <td>COD</td>
                                    <td  class="movimento">MOVIMENTO</td>
                                    <td  class="movimento"></td>
                                    <td >RENDIMENTO</td>
                                    <td >DESCONTO</td>
                                </tr>
    <?php
    $movimentos = $objFolha->getResumoPorMovimento($folha);
    if($_COOKIE['debug'] == 666){
        print_array('////////////$movimentos = $objFolha->getResumoPorMovimento($folha);//////////////////');
        print_array($movimentos);
    }
    foreach ($movimentos as $cod => $valor) {
        if ($valor['valor'] > 0) {
            // ADICIONANDO VALOR DE MATERNIDADE A TOTALIZADORES DA FOLHA 
            if ($valor['nome'] == 'SALARIO MATERNIDADE') {
                $valor_maternidadeM += $valor['valor'];
            }
            
            if ($valor['nome'] == 'SALARIO MATERNIDADE MÊS ANTERIOR') {
                $valor_maternidadeM += $valor['valor'];
            }

            if ($valor['tipo'] == 'CREDITO') {

                $rendimento = $valor['valor'];
                $desconto = '';
                $total_credito += $valor['valor'];
            } else {
                $rendimento = '';
                $desconto = $valor['valor'];
                $total_debito += $valor['valor'];
            }
            if ($valor['qnt'] != 0) {
                $frequencia = $valor['qnt'];
            } elseif (!empty($valor['qnt_horas']) and $valor['qnt_horas'] != '00:00:00') {
                $frequencia = $valor['qnt_horas'];
            } else {
                $frequencia = $valor['percentual'];
            }

            $class = ($linha++ % 2 == 0) ? 'linha_um' : 'linha_dois';

            echo '<tr class="' . $class . '">';
            echo '<td>' . $cod . '</td>';
            echo '<td align="left" >' . $valor['nome'] . '</td>';
            echo '<td>' . $frequencia . '</td>';
            echo '<td>' . formato_real($rendimento) . '</td>';
            echo '<td>' . formato_real($desconto) . '</td>';
            echo '</tr>';
        }
    }
    ?>

                                <?php
                                $querySaldoNaRescisao = "SELECT SUM(A.saldo_salario) as total
                                                FROM rh_recisao AS A
                                                WHERE MONTH(A.data_demi) = {$row_folha['mes']} AND YEAR(A.data_demi) = {$row_folha['ano']} 
                                                AND A.`status` = 1 AND A.id_projeto = {$row_folha['projeto']}";
                                                
                                if($_COOKIE['logado'] == 179){
                                    echo $querySaldoNaRescisao;
                                }                
                                                
                                $sqlSaldoNaRescisao = mysql_query($querySaldoNaRescisao);
                                while ($rowsSaldoNaRescisao = mysql_fetch_assoc($sqlSaldoNaRescisao)) {
                                    $saldo_rescisao = $rowsSaldoNaRescisao['total'];
                                }

                                $queryTotalPensao = "SELECT SUM(A.valor_mov) AS total
                                                        FROM itens_pensao_para_contracheque AS A
                                                        WHERE A.id_folha = '{$row_folha['id_folha']}' AND status = 1";
                                $sqlTotalPensao = mysql_query($queryTotalPensao);
                                while ($rowsTotalPensao = mysql_fetch_assoc($sqlTotalPensao)) {
                                    $valor_total_pensao = $rowsTotalPensao['total'];
                                }
                                if($_COOKIE['debug'] == 666){
                                    print_array('////////////$queryTotalPensao//////////////////');
                                    print_array($queryTotalPensao);
                                }
                                ?>
                                <!--- TOTALIZADOR DE SALDO DE SALARIO NA RESCISAO --->
                                <?php $totalSaldoRescisao = 0; if($row_folha['terceiro'] == 2){ ?>
                                <tr class="linha_dois">
                                    <td style="text-align: center">0002</td>
                                    <td align="left"  >SALDO DE SALARIO RESCISÃO</td>
                                    <td></td>
                                    <td></td>
                                    <td style="text-align: center"><?php echo number_format($saldo_rescisao, 2, ',', '.'); ?></td>
                                    <?php $totalSaldoRescisao = $saldo_rescisao; ?>
                                </tr>
                                <?php } ?>
                                
                                <!--- TOTALIZADOR DE SALDO DE SALARIO NA RESCISAO --->
                                <tr class="linha_um">
                                    <td style="text-align: center">0003</td>
                                    <td align="left" >PENSÃO</td>
                                    <td></td>
                                    <td></td>
                                    <td style="text-align: center"><?php echo number_format($valor_total_pensao, 2, ',', '.'); ?></td>
                                </tr>
                                <?php $total_debito = $total_debito + $valor_total_pensao + $totalSaldoRescisao; ?>
                                <tr class="totais">
                                    <td colspan="3" align="right">TOTAIS:</td>
                                    <td><?= formato_real($total_credito) ?></td>
                                    <td><?= formato_real($total_debito) ?></td>
                                </tr>
                                <tr class="totais">
                                    <td colspan="3" align="right">L&Iacute;QUIDO:</td>
                                    <td><?= formato_real($total_credito - $total_debito) ?></td>
                                    <td>&nbsp;</td>
                                </tr>
                            </table>

                        </div>


    <?php
    
    // Totalizadores
    //SEM PIS    
    $qr_sem_pis = mysql_query("SELECT A.pis,B.nome, CAST(B.pis as SIGNED),(id_folha_proc), (A.base_inss) as pis_zerado_bases, (A.a5020) as pis_zerado_inss
                                    FROM rh_folha_proc as A
                                    INNER JOIN  rh_clt as B
                                    ON A.id_clt = B.id_clt
                                    WHERE A.id_folha = $folha AND  (CAST(A.pis as SIGNED)=0 OR B.pis IS NULL)
                                    ORDER BY `A`.`id_folha_proc` ASC");
    $row_sem_pis = mysql_fetch_assoc($qr_sem_pis);



    $base_fgts_sem_rescisao = "SELECT SUM(A.base_inss) AS total_fgts
        FROM rh_folha_proc AS A
        WHERE A.id_folha = '{$folha}' AND A.status_clt NOT IN(61,63,64,66)
        GROUP BY A.id_folha";
    $sql_fgts_sem_rescisao = mysql_query($base_fgts_sem_rescisao);

    $valor_fgts_sem_rescisao = mysql_result($sql_fgts_sem_rescisao, 0);



    $base_fgts_rescisao = "SELECT SUM(A.base_inss) AS total_fgts
        FROM rh_folha_proc AS A
        WHERE A.id_folha = '{$folha}' AND A.status_clt IN(61,63,64,66)
        GROUP BY A.id_folha";
    $sql_fgts_rescisao = mysql_query($base_fgts_rescisao);

    $valor_fgts_rescisao = mysql_result($sql_fgts_rescisao, 0);

    $totalizadores_nome = array(
        'L&Iacute;QUIDO',
        'SEM DESCONTO DE INSS',
        'BASE DE INSS SEFIP',
        'BASE DE INSS',
        'INSS',
        'INSS (EMPRESA)',
        'INSS (EMPRESA) + AUTONOMO',
        'INSS (RAT)',
        'INSS (TERCEIROS)'/* , 'INSS (RECOLHER)' */,
        'BASE DE IRRF',
        'IRRF',
        'DDIR',
        //'BASE DE FGTS',
        'BASE DE PIS',
        'VALOR PIS',
        'BASE DE FGTS DE F&Eacute;RIAS',
        'BASE DE FGTS TOTAL',
        'FGTS',
        'SALARIO MATERNIDADE',
        'BASE INSS 13º <br>CALC. RESCISÃO',
        'BASE FGTS 13º <br>CALC. RESCISÃO',
        'FGTS 13º <br>CALC. RESCISÃO',
        'BASE INSS AUTONOMO',
        'DESC INSS AUTONOMO',
        'BASE IR AUTONOMO',
        'DESC IR AUTONOMO',
        'DESC ISS AUTONOMO'
    );
    
    if($row_folha['terceiro'] == 2){
        $qrsql_ferias_sefip = "SELECT SUM(base_inss) soma, SUM(inss) inss
                            FROM rh_ferias
                            WHERE projeto = '{$row_folha['projeto']}' AND '{$row_folha['ano']}-{$row_folha['mes']}' BETWEEN DATE_FORMAT(data_ini, '%Y-%m') AND DATE_FORMAT(data_fim, '%Y-%m') AND STATUS = 1 AND MONTH(data_ini) = '{$row_folha['mes']}' 
                            ORDER BY id_ferias DESC;";
        if ($_COOKIE[debug] == 666) {
            echo '<br>////////////////////////$qrsql_ferias_sefip////////////////////////<br>';
            echo $qrsql_ferias_sefip;
        }
        $sql_ferias_sefip = mysql_query($qrsql_ferias_sefip);
        $sql_ferias_sefip = mysql_fetch_assoc($sql_ferias_sefip);

        $slq333 = "SELECT SUM(base_inss) soma, SUM(base_irrf) base_irrf, SUM(imprenda) total_irrf, SUM(ir_dt) ir_dt, SUM(ir_rescisao) ir_rescisao, SUM(ir_ferias) ir_ferias, SUM(inss) inss, SUM(salliquido) salliquido FROM rh_folha_proc A WHERE id_folha = '{$row_folha['id_folha']}' AND status = 3;";
        $row333 = mysql_fetch_assoc(mysql_query($slq333));
        if ($_COOKIE[debug] == 666) {
            echo '<br>////////////////////////$slq333////////////////////////<br>';
            echo $slq333;
        }

        $sql_13 = "SELECT id_folha FROM rh_folha AS A WHERE A.ano = '{$row_folha['ano']}'  AND A.status = 3 AND A.terceiro = 1 AND tipo_terceiro > 1 AND projeto IN ({$row333['id_projeto']});";
        $qry_13 = mysql_query($sql_13);
        $row_13 = mysql_fetch_assoc($qry_13);
        if(!empty($row_13['id_folha'])){
//            $aux13_FROM = "LEFT JOIN (SELECT id_clt, base_inss FROM rh_folha_proc WHERE id_folha IN ({$row_13['id_folha']}) AND status = 3) AS DT ON (B.id_clt = DT.id_clt)";
//            $aux13_SELECT = " - IFNULL(DT.base_inss,0)";
        }
        
        $slq_base_13_sefip = "/*base_inss_13*/
            SELECT SUM(IF((A.base_inss_13 $aux13_SELECT) > 0,(IF(MONTH(A.data_demi) = 1 && DAY(A.data_demi) >= 15,A.base_inss_13, 0) $aux13_SELECT), 0.01)) soma, SUM(inss_dt) inss_dt, SUM(A.base_inss_ss) base_inss_ss, SUM(inss_ss) inss, SUM(IF(motivo = 60,base_inss_13,0)) rescisao_sem_direito
            FROM rh_recisao A 
            $aux13_FROM
            WHERE id_projeto = '{$row_folha['projeto']}' AND MONTH(data_demi) = '{$row_folha['mes']}' AND YEAR(data_demi) = '{$row_folha['ano']}' AND status = 1 AND rescisao_complementar = 0;";
        $row_base_13_sefip = mysql_fetch_assoc(mysql_query($slq_base_13_sefip));
        if ($_COOKIE[debug] == 666) {
            echo '<br>////////////////////////$slq_base_13_sefip////////////////////////<br>';
            echo $slq_base_13_sefip . $movimentos['90016']['valor'];
        }
//        if($row_folha['id_folha'] != 97){
//            $row_base_13_sefip['rescisao_sem_direito'] = 0;
//        }

        $slq_base_autonomo = "SELECT SUM(desc_iss) AS desc_iss, SUM(desc_ir) AS desc_ir, SUM(base_ir) AS base_ir, SUM(desc_inss) AS desc_inss, SUM(soma) AS soma FROM (SELECT SUM(G.valor_iss) AS desc_iss, SUM(G.valor_ir) AS desc_ir, SUM(G.base_ir) AS base_ir, IF(SUM(G.valor_inss) < CAST((SELECT teto FROM rh_movimentos WHERE cod IN(50241) AND anobase = 2017 AND IFNULL(G.valor,0) BETWEEN v_ini AND v_fim) AS DECIMAL(10,4)),SUM(G.valor_inss), CAST((SELECT teto FROM rh_movimentos WHERE cod IN(50241) AND anobase = 2017 AND IFNULL(G.valor,0) BETWEEN v_ini AND v_fim) AS DECIMAL(10,4)) ) AS desc_inss, SUM(G.valor) AS soma FROM rpa_autonomo AS G LEFT JOIN autonomo AS H ON(G.id_autonomo = H.id_autonomo) WHERE G.id_projeto_pag = {$row_folha['projeto']} AND H.status_reg = 1 AND G.mes_competencia = {$row_folha['mes']} AND G.ano_competencia = {$row_folha['ano']} AND REPLACE(REPLACE(H.pis,'.',''),'-','') NOT IN (SELECT REPLACE(REPLACE(pis,'.',''),'-','') FROM rh_folha_proc rfp LEFT JOIN rh_clt rc ON (rfp.id_clt = rc.id_clt) WHERE rfp.id_folha IN ({$row_folha['id_folha']}) AND rfp.status = 3) GROUP BY H.id_autonomo) AS aut;";
        $row_base_autonomo = mysql_fetch_assoc(mysql_query($slq_base_autonomo));
        if ($_COOKIE[debug] == 666) {
            echo '<br>////////////////////////$slq_base_autonomo////////////////////////<br>';
            echo $slq_base_autonomo;
            //echo ("{$row333['soma']}+{$sql_ferias_sefip['soma']}+{$row_base_13_sefip['soma']}+{$row_base_13_sefip['base_inss_ss']}");
        }

        $sql_base_nao_incide_fgts = "SELECT SUM(base_inss) soma
                            FROM rh_folha_proc A
                            LEFT JOIN rh_clt B ON (A.id_clt = B.id_clt)
                            LEFT JOIN rh_recisao C ON (A.id_clt = C.id_clt)
                            WHERE A.id_folha IN ({$row_folha['id_folha']}) AND MONTH(C.data_demi) = {$row_folha['mes']} AND YEAR(C.data_demi) = {$row_folha['ano']} AND rescisao_complementar = 0 AND C.status = 1 AND B.status IN (61,64,66);";
        $qry_base_nao_incide_fgts = mysql_fetch_assoc(mysql_query($sql_base_nao_incide_fgts));
        if ($_COOKIE[debug] == 666) {
            echo '<br>////////////////////////$sql_base_nao_incide_fgts////////////////////////<br>';
            echo $sql_base_nao_incide_fgts;
        }

        $slq_ac_trabalho_fgts = "SELECT SUM(base_inss) base_fgts, SUM(base_inss) base_inss, SUM(inss) inss FROM rh_folha_proc A WHERE id_folha = '{$row_folha['id_folha']}' AND status_clt IN (70) AND status = 3;";
        $row_ac_trabalho_fgts = mysql_fetch_assoc(mysql_query($slq_ac_trabalho_fgts));
        if ($_COOKIE[debug] == 666) {
            echo '<br>////////////////////////$slq_ac_trabalho_fgts////////////////////////<br>';
            echo $slq_ac_trabalho_fgts;
        }
        
        if ($_COOKIE['debug'] == 666) {
            echo '//////////////FGTS ACERTO/////////////////';
            echo '<pre>';
            print_r($base_fgts_acerto);
            echo '</pre>';
            
        }
        
        if ($row_folha['data_proc'] < '2016-06-30') {
            if ($row_folha['id_folha'] == 47) {
                $base_fgts_acerto = 1065.5 - $movimentos['90016']['valor'];
                $inss_acerto = 292.9;
            }
            $row_base_13_sefip['inss'] = $qry_base_nao_incide_fgts['soma'] = 0;
        } else {
            $sql_ferias_sefip['soma'] = 0;
        }
        
        $base_fgts_total = $row333['soma'] - $qry_base_nao_incide_fgts['soma'] + $sql_ferias_sefip['soma'] - $row_base_13_sefip['rescisao_sem_direito'];
        
        if($row_folha['id_folha'] == 97){
            //inss (RESCISÃO / 13º)
            $inss_acerto = 2584.48;
            
            $base_fgts_total -= 142.55;
        } else if($row_folha['id_folha'] == 96){
            //inss (RESCISÃO / 13º)
            $inss_acerto = 2770.90 - 72.85;
//            $inss_acerto = $inss_acerto + 570.88;
            
            $base_fgts_total += 910.73;
        } else if($row_folha['id_folha'] == 98){
            $inss_acerto = 658.38;
        } else if($row_folha['id_folha'] == 108){
            $inss_acerto = -570.88; //2343 - inss descontado nas ferias e na folha (HELOISA FIGUEIREDO CUNALI)
            $inss_acerto -= 223.88; //rescisao - inss 13 memos de 15 dias ((105.54 + 33.15 + 30.93 + 20.03 + 34.23))
            $inss_acerto += 83.43;
        } else if($row_folha['id_folha'] == 109){
            $inss_acerto = -608.44; //2603 - inss descontado nas ferias, pois carta de inss estava desatualizada (MAYARA CORRAL DA SILVA)
        } else if($row_folha['id_folha'] == 113){
            $inss_acerto = - 724.57;
        } else if($row_folha['id_folha'] == 114){
            $inss_acerto = - 393.85;
        } else if($row_folha['id_folha'] == 119){        
            $inss_acerto = - 558.41;
            $base_inss_acerto = 1814.44;        
        } else if($row_folha['id_folha'] == 118){        
            $inss_acerto = -1722.17;
        }
        
    } else {
        $slq333 = "
            SELECT SUM(base_inss) soma, SUM(IFNULL(B.valor_movimento,0) + salliquido) base_fgts, SUM(base_irrf) base_irrf, SUM(imprenda) total_irrf, SUM(ir_dt) ir_dt, SUM(ir_rescisao) ir_rescisao, SUM(ir_ferias) ir_ferias, SUM(inss_dt) inss, SUM(salliquido) salliquido, SUM(C.valor_movimento) AS adiantamento_indevido_base
            FROM rh_folha_proc A 
            LEFT JOIN (SELECT id_clt, valor_movimento FROM rh_movimentos_clt WHERE cod_movimento = '80030' AND mes_mov = '13' AND id_movimento IN ({$row_folha['ids_movimentos_estatisticas']})) AS B ON (B.id_clt = A.id_clt)
            LEFT JOIN (SELECT id_clt, valor_movimento FROM rh_movimentos_clt WHERE cod_movimento = '80031' AND mes_mov = '14' AND id_movimento IN ({$row_folha['ids_movimentos_estatisticas']})) AS C ON (C.id_clt = A.id_clt)
            WHERE id_folha = '{$row_folha['id_folha']}' AND A.status = 3";
            
            /**
             * By Ramon 20/12/2016
             * REMOVI A LINHA ABAIXO DA QUERY ACIMA POIS SE FINALIZOU A FOLHA COM OS RECINDIDOS, VAI TER Q TOTALIZAR ELES TBM...
             */
            //AND A.id_clt NOT IN (SELECT id_clt FROM rh_recisao WHERE MONTH(data_demi) = {$row_folha['mes']} AND YEAR(data_demi) = {$row_folha['ano']} AND rescisao_complementar = 0 AND status = 1 AND id_projeto = {$row_folha['projeto']} AND motivo IN (61,64,66))";
        $row333 = mysql_fetch_assoc(mysql_query($slq333));
        
        
        if ($_COOKIE['debug'] == 666) {
            echo '//////////////FGTS ACERTO/////////////////';
            echo '<pre>';
            print_r($base_fgts_acerto);
            echo '</pre>';
            
        }
        
        $sql_base_nao_incide_fgts = "SELECT A1.id_clt, SUM(A1.base_inss - IFNULL(C1.valor_movimento,0) - IFNULL(E.valor_movimento,0)) decimo_terceiro, A1.base_inss
                FROM rh_folha_proc A1
                LEFT JOIN rh_folha AS B1 ON (A1.id_folha = B1.id_folha)
                LEFT JOIN (SELECT id_clt, valor_movimento FROM rh_movimentos_clt WHERE cod_movimento = '80031' AND mes_mov = '14' AND id_movimento IN ({$row_folha['ids_movimentos_estatisticas']})) AS C1 ON (C1.id_clt = A1.id_clt)
                LEFT JOIN (SELECT id_clt, SUM(valor_movimento) valor_movimento FROM rh_movimentos_clt WHERE cod_movimento IN (5050,80031) AND mes_mov = '14' AND ano_mov = {$row_folha['ano']} AND id_movimento IN ({$row_folha['ids_movimentos_estatisticas']}) GROUP BY id_clt) AS E ON (E.id_clt = A1.id_clt)
                    LEFT JOIN rh_recisao R ON (A1.id_clt = R.id_clt)
                WHERE A1.status = 3 AND B1.status = 3 AND B1.terceiro = 1 AND B1.tipo_terceiro IN (2,3) AND B1.projeto = '{$row_folha['projeto']}'
                AND MONTH(R.data_demi) = {$row_folha['mes']} AND YEAR(R.data_demi) = {$row_folha['ano']} AND R.rescisao_complementar = 0 AND R.status = 1 AND R.motivo IN (61,64,66)";//"(61,64,66);";
        $qry_base_nao_incide_fgts = mysql_fetch_assoc(mysql_query($sql_base_nao_incide_fgts));
        if ($_COOKIE[debug] == 666) {
            echo '<br>////////////////////////$sql_base_nao_incide_fgts////////////////////////<br>';
            echo $sql_base_nao_incide_fgts;
        }
        
        //GAMBI INICIO
        if($row_folha['id_folha'] == 104){
//            $row333['soma'] = '4871660.93';
            $acerto_inss_13 = 363672.82;
            $acerto_inss_13 = $acerto_inss_13 + 1373.12;
            $row333['soma'] = $row333['soma'] - $row333['adiantamento_indevido_base'];
        } elseif($row_folha['id_folha'] == 105){
            $row333['soma'] = '4213104.19';
            $acerto_inss_13 = 333246.53;
        } elseif($row_folha['id_folha'] == 103){
            $row333['soma'] = '395515.02';
        }
        //GAMBI FINAL
        
        if ($_COOKIE[debug] == 666) {
            echo '<br>////////////////////////$slq333////////////////////////<br>';
            echo $slq333;
        }
        
        if($row_folha['tipo_terceiro'] > 1){
            $base_fgts_total = $total_credito - $movimentos[5050]['valor'] - $movimentos[80019]['valor'] - $movimentos[80031]['valor'] - $qry_base_nao_incide_fgts['decimo_terceiro'];
            
            $row333['soma'] -= $movimentos[80019]['valor'];
            
            if($_COOKIE['debug'] == 666){
                echo "<br><br><br>";
                echo "base_fgts_total = total_credito({$total_credito}) - movimentos[5050]['valor']({$movimentos[5050]['valor']}) - movimentos[80019]['valor']({$movimentos[80019]['valor']}) - movimentos[80031]['valor']({$movimentos[80031]['valor']}) - qry_base_nao_incide_fgts['decimo_terceiro']({$qry_base_nao_incide_fgts['decimo_terceiro']})";
                echo "<br><br><br>";
            }
            
            //GAMBI INICIO
            if($row_folha['id_folha'] == 104){
                $base_fgts_total += 609.25;
            } else if($row_folha['id_folha'] == 105){
                $base_fgts_total -= 3819.45;
            }
            //GAMBI FINAL
        } else {
            $base_fgts_total = $row333['base_fgts'];
        }
    }
    
    if ($_COOKIE[debug] == 666) {
        echo '<br>////////////////////////BASE FGTS////////////////////////<br>';
        echo "((row333:{$row333['soma']} - qry_base_nao_incide_fgts:{$qry_base_nao_incide_fgts['soma']} + sql_ferias_sefip:{$sql_ferias_sefip['soma']} - base_fgts_acerto:{$base_fgts_acerto} - row_base_13_sefip:{$row_base_13_sefip['rescisao_sem_direito']}))";
        
        echo '<br>////////////////////////BASE INSS////////////////////////<br>';
        echo "((row333:{$row333['soma']} + sql_ferias_sefip:{$sql_ferias_sefip['soma']} + row_base_13_sefip:{$row_base_13_sefip['soma']} - row_ac_trabalho_fgts:{$row_ac_trabalho_fgts['base_inss']} - row_base_13_sefip:{$row_base_13_sefip['rescisao_sem_direito']}))";

        echo '<br>////////////////////////INSS////////////////////////<br>';
        echo "(acerto_inss_13:{$acerto_inss_13} > 0) ? acerto_inss_13:{$acerto_inss_13} : row333['inss']:{$row333['inss']} + sql_ferias_sefip['inss']:{$sql_ferias_sefip['inss']} + row_base_13_sefip['inss_dt']:{$row_base_13_sefip['inss_dt']} + row_base_13_sefip['inss']:{$row_base_13_sefip['inss']} + inss_acerto:{$inss_acerto} - row_ac_trabalho_fgts['inss']:{$row_ac_trabalho_fgts['inss']}";       
        
        echo "<br>";
        echo "<br>";
        echo "<br>";
        echo "SOMA BASE_INSS*: {$row333['soma']} + {$sql_ferias_sefip['soma']} + {$row_base_13_sefip['soma']} - {$row_ac_trabalho_fgts['base_inss']} - {$row_base_13_sefip['rescisao_sem_direito']}: " . $row333['soma'] + $sql_ferias_sefip['soma'] + $row_base_13_sefip['soma'] - $row_ac_trabalho_fgts['base_inss'] - $row_base_13_sefip['rescisao_sem_direito'] . "<br>";
        echo "SOMA DE FERIAS: " . $sql_ferias_sefip['soma'] . "<br>";
        echo "SOMA DE 13 : " . $row_base_13_sefip['soma'] . "<br>";
        echo "****************************<br>";
        echo "BASE Autonomo: " . $row_base_autonomo['soma'] . "<br>";
        
        
        echo "INSS DESC: ({$acerto_inss_13} : {$row333['inss']} + {$sql_ferias_sefip['inss']} + {$row_base_13_sefip['inss_dt']} + {$row_base_13_sefip['inss']} + {$inss_acerto} - {$row_ac_trabalho_fgts['inss']},<br>";

        echo ((($row333['soma'] + $sql_ferias_sefip['soma'] + $row_base_13_sefip['soma'] - $row_ac_trabalho_fgts['base_inss'] - $row_base_13_sefip['rescisao_sem_direito']) * 0.2) + ($row_base_autonomo['soma'] * 0.2));
    }
    

    /* if($_COOKIE['logado'] == 158){
      $totalizador_fp = $objFolha->getTotalizadoresFP($row_folha['id_folha'], 3);

      echo "<pre>";
      print_r($totalizador_fp);
      echo "</pre>";
      } */

    $totalizadores_valor = array(
        $row333['salliquido'], /* $row_folha['total_liqui'] */
        $row_folha['total_sem_desconto_inss'],
        ($row333['soma'] + $sql_ferias_sefip['soma'] + $row_base_autonomo['soma']),
        $row333['soma'] + $sql_ferias_sefip['soma'] + $row_base_13_sefip['soma'] + $total_base_13_rescisao - $row_ac_trabalho_fgts['base_inss'] - $row_base_13_sefip['rescisao_sem_direito'] - $base_inss_acerto,
        /* $row_folha['total_inss'] + $row_folha['inss_dt'] + $row_folha['inss_rescisao'] + $row_folha['inss_ferias'], */
        ($acerto_inss_13 > 0) ? $acerto_inss_13 : $row333['inss'] + $sql_ferias_sefip['inss'] + $row_base_13_sefip['inss_dt'] + $row_base_13_sefip['inss'] + $inss_acerto - $row_ac_trabalho_fgts['inss'],
        (($row333['soma'] + $sql_ferias_sefip['soma'] + $row_base_13_sefip['soma'] + $total_base_13_rescisao - $row_ac_trabalho_fgts['base_inss'] - $row_base_13_sefip['rescisao_sem_direito'] - $base_inss_acerto) * 0.2),
        ((($row333['soma'] + $sql_ferias_sefip['soma'] + $row_base_13_sefip['soma'] + $total_base_13_rescisao - $row_ac_trabalho_fgts['base_inss'] - $row_base_13_sefip['rescisao_sem_direito'] - $base_inss_acerto) * 0.2) + ($row_base_autonomo['soma'] * 0.2)),
        (($row333['soma'] + $sql_ferias_sefip['soma'] + $row_base_13_sefip['soma'] + $total_base_13_rescisao - $row_ac_trabalho_fgts['base_inss'] - $row_base_13_sefip['rescisao_sem_direito'] - $base_inss_acerto) * $percentual_rat),
        (($row333['soma'] + $sql_ferias_sefip['soma'] + $row_base_13_sefip['soma'] + $total_base_13_rescisao - $row_ac_trabalho_fgts['base_inss'] - $row_base_13_sefip['rescisao_sem_direito'] - $base_inss_acerto) * 0.058),
        //	 ((($row_folha['base_inss'] * 0.2) +
        // ($row_folha['base_inss'] * $percentual_rat) +
        //($row_folha['base_inss'] * 0.058) +
        //($row_folha['total_inss'] + $row_folha['inss_dt'] + $row_folha['inss_rescisao'] + $row_folha['inss_ferias'])) - 
        //$row_folha['total_familia']),
        $row333['base_irrf'] - $ddir,
        $row333['total_irrf'] + $row333['ir_dt'] + $row333['ir_rescisao'] + $row333['ir_ferias'],
        $ddir,
        //$row_folha['base_fgts'],
        //($row333['soma'] - $qry_base_nao_incide_fgts['soma'] - $base_fgts_acerto),
        ($row333['soma'] - $qry_base_nao_incide_fgts['soma'] - $base_fgts_acerto),
        ($row333['soma'] - $qry_base_nao_incide_fgts['soma'] - $base_fgts_acerto) * 0.01,
        /* $row_folha['base_fgts_ferias'] */ $sql_ferias_sefip['soma'],
        (($base_fgts_total - $base_fgts_acerto)), /* $valor_fgts_sem_rescisao, // + $row_folha['base_fgts_ferias']   */
        (($base_fgts_total - $base_fgts_acerto)) * 0.08, //+ $row_folha['base_fgts_ferias']
        $valor_maternidadeM,
        //        $row_folha['base_fgts'] + $row_folha['base_fgts_ferias'],         
        //       ($row_folha['base_fgts'] + $row_folha['base_fgts_ferias'])*0.08,
        $total_base_13_rescisao,
        $total_base_13_rescisao_fgts,
        $total_base_13_rescisao_fgts * 0.08,
        $row_base_autonomo['soma'],
        $row_base_autonomo['desc_inss'],
        $row_base_autonomo['base_ir'],
        $row_base_autonomo['desc_ir'],
        $row_base_autonomo['desc_iss'],
            /* $row_folha['total_fgts'] + $row_folha['fgts_dt'] + $row_folha['fgts_rescisao'] + $row_folha['fgts_ferias'] */
    );
    //    if($_COOKIE['logado'] == 257){echo '<pre>';print_r($totalizadores_valor);exit;}

    $percente_43 = 0.43;
    $totalizadores_valor_43 = array(
        $row333['salliquido'] * $percente_43, /* $row_folha['total_liqui'] */
        $row_folha['total_sem_desconto_inss'] * $percente_43,
        ($row333['soma'] + $sql_ferias_sefip['soma'] + $row_base_autonomo['soma']) * $percente_43,
        ($row333['soma'] + $sql_ferias_sefip['soma'] + $row_base_13_sefip['soma']) * $percente_43,
        /* $row_folha['total_inss'] + $row_folha['inss_dt'] + $row_folha['inss_rescisao'] + $row_folha['inss_ferias'], */
        ($row333['inss'] + $sql_ferias_sefip['inss'] + $row_base_13_sefip['inss_dt'] + $row_base_13_sefip['inss'] + $inss_acerto) * $percente_43,
        (($row333['soma'] + $sql_ferias_sefip['soma'] + $row_base_13_sefip['soma']) * 0.2) * $percente_43,
        ((($row333['soma'] + $sql_ferias_sefip['soma'] + $row_base_13_sefip['soma']) * 0.2) + ($row_base_autonomo['soma'] * 0.2)) * $percente_43,
        (($row333['soma'] + $sql_ferias_sefip['soma'] + $row_base_13_sefip['soma']) * $percentual_rat) * $percente_43,
        (($row333['soma'] + $sql_ferias_sefip['soma'] + $row_base_13_sefip['soma']) * 0.058) * $percente_43,
        //	 ((($row_folha['base_inss'] * 0.2) +
        // ($row_folha['base_inss'] * $percentual_rat) +
        //($row_folha['base_inss'] * 0.058) +
        //($row_folha['total_inss'] + $row_folha['inss_dt'] + $row_folha['inss_rescisao'] + $row_folha['inss_ferias'])) - 
        //$row_folha['total_familia']),
        ($row333['base_irrf'] - $ddir) * $percente_43,
        ($row333['total_irrf'] + $row333['ir_dt'] + $row333['ir_rescisao'] + $row333['ir_ferias']) * $percente_43,
        $ddir * $percente_43,
        //$row_folha['base_fgts'],
        //($row333['soma'] - $qry_base_nao_incide_fgts['soma'] - $base_fgts_acerto) * $percente_43,
        ($row333['soma'] - $qry_base_nao_incide_fgts['soma'] - $base_fgts_acerto) * $percente_43,
        (($row333['soma'] - $qry_base_nao_incide_fgts['soma'] - $base_fgts_acerto) * 0.01) * $percente_43,
        /* $row_folha['base_fgts_ferias'] */ $sql_ferias_sefip['soma'] * $percente_43,
        (($base_fgts_total - $base_fgts_acerto)) * $percente_43, /* $valor_fgts_sem_rescisao, // + $row_folha['base_fgts_ferias']   */
        ((($base_fgts_total - $base_fgts_acerto)) * 0.08) * $percente_43, //+ $row_folha['base_fgts_ferias']
        $valor_maternidadeM * $percente_43,
        //        $row_folha['base_fgts'] + $row_folha['base_fgts_ferias'],         
        //       ($row_folha['base_fgts'] + $row_folha['base_fgts_ferias'])*0.08,
        $total_base_13_rescisao * $percente_43,
        $row_base_autonomo['soma'] * $percente_43,
        $row_base_autonomo['desc_inss'] * $percente_43,
        $row_base_autonomo['base_ir'] * $percente_43,
        $row_base_autonomo['desc_ir'] * $percente_43,
        $row_base_autonomo['desc_iss'] * $percente_43,
            /* $row_folha['total_fgts'] + $row_folha['fgts_dt'] + $row_folha['fgts_rescisao'] + $row_folha['fgts_ferias'] */
    );

    $percente_57 = 0.57;
    $totalizadores_valor_57 = array(
        $row333['salliquido'] * $percente_57, /* $row_folha['total_liqui'] */
        $row_folha['total_sem_desconto_inss'] * $percente_57,
        ($row333['soma'] + $sql_ferias_sefip['soma'] + $row_base_autonomo['soma']) * $percente_57,
        ($row333['soma'] + $sql_ferias_sefip['soma'] + $row_base_13_sefip['soma']) * $percente_57,
        /* $row_folha['total_inss'] + $row_folha['inss_dt'] + $row_folha['inss_rescisao'] + $row_folha['inss_ferias'], */
        ($row333['inss'] + $sql_ferias_sefip['inss'] + $row_base_13_sefip['inss_dt'] + $row_base_13_sefip['inss'] + $inss_acerto) * $percente_57,
        (($row333['soma'] + $sql_ferias_sefip['soma'] + $row_base_13_sefip['soma']) * 0.2) * $percente_57,
        ((($row333['soma'] + $sql_ferias_sefip['soma'] + $row_base_13_sefip['soma']) * 0.2) + ($row_base_autonomo['soma'] * 0.2)) * $percente_57,
        (($row333['soma'] + $sql_ferias_sefip['soma'] + $row_base_13_sefip['soma']) * $percentual_rat) * $percente_57,
        (($row333['soma'] + $sql_ferias_sefip['soma'] + $row_base_13_sefip['soma']) * 0.058) * $percente_57,
        //	 ((($row_folha['base_inss'] * 0.2) +
        // ($row_folha['base_inss'] * $percentual_rat) +
        //($row_folha['base_inss'] * 0.058) +
        //($row_folha['total_inss'] + $row_folha['inss_dt'] + $row_folha['inss_rescisao'] + $row_folha['inss_ferias'])) - 
        //$row_folha['total_familia']),
        ($row333['base_irrf'] - $ddir) * $percente_57,
        ($row333['total_irrf'] + $row333['ir_dt'] + $row333['ir_rescisao'] + $row333['ir_ferias']) * $percente_57,
        $ddir * $percente_57,
        //$row_folha['base_fgts'],
        //($row333['soma'] - $qry_base_nao_incide_fgts['soma'] - $base_fgts_acerto) * $percente_57,
        ($row333['soma'] - $qry_base_nao_incide_fgts['soma'] - $base_fgts_acerto) * $percente_57,
        (($row333['soma'] - $qry_base_nao_incide_fgts['soma'] - $base_fgts_acerto) * 0.01) * $percente_57,
        /* $row_folha['base_fgts_ferias'] */ $sql_ferias_sefip['soma'] * $percente_57,
        (($base_fgts_total - $base_fgts_acerto)) * $percente_57, /* $valor_fgts_sem_rescisao, // + $row_folha['base_fgts_ferias']   */
        ((($base_fgts_total - $base_fgts_acerto)) * 0.08) * $percente_57, //+ $row_folha['base_fgts_ferias']
        $valor_maternidadeM * $percente_57,
        //        $row_folha['base_fgts'] + $row_folha['base_fgts_ferias'],         
        //       ($row_folha['base_fgts'] + $row_folha['base_fgts_ferias'])*0.08,
        $total_base_13_rescisao * $percente_57,
        $row_base_autonomo['soma'] * $percente_57,
        $row_base_autonomo['desc_inss'] * $percente_57,
        $row_base_autonomo['base_ir'] * $percente_57,
        $row_base_autonomo['desc_ir'] * $percente_57,
        $row_base_autonomo['desc_iss'] * $percente_57,
            /* $row_folha['total_fgts'] + $row_folha['fgts_dt'] + $row_folha['fgts_rescisao'] + $row_folha['fgts_ferias'] */
    );
    ?>

                        <div id="totalizadores">
                            <table cellspacing="1">
                                <tr>
                                    <td class="secao_pai" colspan="2">Totalizadores </td>
                                </tr>
                                <tr class="linha_um">
                                    <td class="secao">PARTICIPANTES</td>
                                    <td class="valor"><?= $total_participantes ?></td>
                                </tr>
    <?php foreach ($totalizadores_valor as $chave => $valor) { ?>
                                    <tr class="linha_<?php
        if ($linha2++ % 2 == 0) {
            echo 'dois';
        } else {
            echo 'um';
        }
        ?>">
                                        <td class="secao"><?= $totalizadores_nome[$chave] ?>:</td>
                                        <td class="valor"><?= formato_real($valor) ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>

                            </table>
                        </div>
                        <br><br><br>
                        

                                <?php
                                    if (!empty($ids_movimentos_estatisticas)) {
                                        $qr_movimentos_faltas = mysql_query("SELECT cod_movimento, nome_movimento, SUM(valor_movimento) as total ,tipo_movimento, id_mov 
			   								 FROM `rh_movimentos_clt`                                                                                         
											 WHERE id_movimento IN($ids_movimentos_estatisticas) 
                                                                                          AND id_mov  = 62
											 GROUP BY id_mov") or die(mysql_error());
                                        ?>
                            <table cellspacing="1" width="300">
                                <tr>
                                    <td colspan="2" class="secao_pai">Total de faltas</td>
                                </tr>
                                <tr class="secao">
                                    <td width="9%">COD</td>
                                    <td width="9%">TOTAL</td>
                                </tr>

                            <?php
                            while ($row_mov2 = mysql_fetch_assoc($qr_movimentos_faltas)):
                                ?>  
                                    <tr class="linha_dois">
                                        <td  align="right"  style="font-size:12px;"><?php echo $row_mov2['cod_movimento']; ?></td>
                                        <td  align="right" style="font-size:12px;" > <?php echo number_format($row_mov2['total'], 2, ',', '.'); ?></td>                
                                    </tr>

            <?php
        endwhile;
        ?>
                            </table>
                            <p style="font-style:italic; text-align: left; font-size: 10px; color: #ff6666; margin-left: 70px;">*As faltas são abatidas no salário base.</p>
                            <?php } ?>




                        <div id="resumo" style="width:100%; clear:both; margin-top:20px;">
                            <table cellspacing="1">
                                <tr>
                                    <td class="secao_pai" colspan="5">Lista de Bancos</td>
                                </tr>

    <?php
    // Verificando os bancos envolvidos na folha de pagamento
    $qr_bancos = mysql_query("SELECT DISTINCT(id_banco) FROM rh_folha_proc WHERE id_banco != '9999' AND id_banco > '0' AND id_folha = '$folha' AND status IN(3,4)");
    while ($row_bancos = mysql_fetch_array($qr_bancos)) {

        $numero_banco++;
        $qr_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row_bancos[id_banco]'");
        $row_banco = mysql_fetch_array($qr_banco);
        ?>
<?php if ($_COOKIE['logado'] != 395) { ?>
                                    <tr class="linha_<?php
        if ($linha4++ % 2 == 0) {
            echo 'um';
        } else {
            echo 'dois';
        }
        ?>">
                                        <td style="width:7%;"><img src="../../imagens/bancos/<?= $row_banco['id_nacional'] ?>.jpg" width="25" height="25"></td>
                                        <td style="width:35%; text-align:left; padding-left:5px;"><?= $row_banco['nome'] ?></td>		  

                                    <?php
                                    $total_finalizados = mysql_num_rows(mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha' AND status = '4' AND id_banco = '$row_banco[id_banco]'"));

                                    if (!empty($total_finalizados)) {
                                        ?>

                                            <td>&nbsp;</td>
                                            <td><a href="finalizados.php?regiao=<?= $regiao ?>&folha=<?= $folha ?>&projeto=<?= $projeto ?>&banco=<?= $row_banco['id_banco'] ?>">FINALIZADO</a></td>
                                            <td align="center"><?= $total_finalizados ?> Participantes</td>

            <?php
        } else {

            $qr_banco = mysql_query("SELECT * FROM rh_folha_proc folha INNER JOIN tipopg tipo ON folha.tipo_pg = tipo.id_tipopg WHERE folha.id_banco = '$row_bancos[0]' AND folha.id_folha = '$folha' AND folha.status = '3' AND tipo.tipopg = 'Depósito em Conta Corrente' AND tipo.id_regiao = '$regiao' AND tipo.id_projeto = '$projeto'");
            $total_banco = mysql_num_rows($qr_banco);
            ?>

                                            <td style="width:30%; text-align:center;">
                                                <!--- 23/09/2016 --->
                                                <form id="form1" name="form1" method="post" action="folha_banco.php?enc=<?= str_replace('+', '--', encrypt("$regiao&$folha")) ?>">
                                                    <select name="banco">
            <?php
            $qr_bancos_associados = mysql_query("SELECT * FROM bancos WHERE id_nacional = '$row_banco[id_nacional]' AND status_reg = '1' AND id_regiao != ''");
            while ($row_banco_associado = mysql_fetch_assoc($qr_bancos_associados)) {
                ?>
                                                            <option value="<?= $row_banco_associado['id_banco'] ?>" <?php
                                                if ($row_banco_associado['id_banco'] == $row_banco['id_banco']) {
                                                    echo 'selected';
                                                }
                                                ?>>
                <?php echo $row_banco_associado['id_banco'] . ' - ' . $row_banco_associado['nome'] . ' (' . @mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_banco_associado[id_regiao]'"), 0) . ')'; ?>
                                                            </option>
            <?php } ?>
                                                    </select>
                                                    <label id="data_pagamento<?= $numero_banco ?>" style="display:none;"> 
                                                        <input type='hidden' name='test' value='' />  
                                                        <input name="data" id="data[]" type="text" size="10" onKeyUp="mascara_data(this)" maxlength="10">
                                                        <input name="enviar" id="enviar[]" type="submit" value="Gerar">
                                                    </label>
                                                    <input type="hidden" name="banco_participante" value="<?= $row_banco['id_banco'] ?>">
                                                </form>
                                            </td>
                                            <td style="width:8%;">
                                                <a class="testArquivo" style="cursor:pointer;"><img src="imagens/ver_banc.png" border="0" alt="Visualizar Funcionários por Banco" onClick="document.all.data_pagamento<?= $numero_banco ?>.style.display = (document.all.data_pagamento<?= $numero_banco ?>.style.display == 'none') ? '' : 'none';"></a>
                                                | <a style="cursor:pointer;"><img src="imagens/ver_banc.png" border="0" alt="Visualizar Funcionários por Banco" onClick="document.all.data_pagamento<?= $numero_banco ?>.style.display = (document.all.data_pagamento<?= $numero_banco ?>.style.display == 'none') ? '' : 'none';"></a>
                                            </td>
                                            <td style="width:20%; text-align:center; padding-right:5px;"></td>
                                        </tr>

            <?php
    } }
    }

    $qr_cheque = mysql_query("SELECT * FROM rh_folha_proc folha INNER JOIN tipopg tipo ON folha.tipo_pg = tipo.id_tipopg WHERE folha.id_folha = '$folha' AND folha.status = '3' AND tipo.tipopg = 'Cheque' AND tipo.id_regiao = '$regiao' AND tipo.id_projeto = '$projeto' AND tipo.campo1 = '2'");
    $total_cheque = mysql_num_rows($qr_cheque);
    $linkcheque = str_replace('+', '--', encrypt("$regiao&$folha&$row_TIpoCheq[0]&$row_TipoDepo[0]"));
    ?>

                               <?php if ($_COOKIE['logado'] != 395) { ?> <tr class="linha_<?php
    if ($linha4++ % 2 == 0) {
        echo 'um';
    } else {
        echo 'dois';
    }
    ?>">
                                    <td style="width:7%;"><img src="../../imagens/bancos/cheque.jpg" width="25" height="25" border="0"></td>
                                    <td style="width:35%; text-align:left; padding-left:5px;">Cheque</td>
                                    <td style="width:30%;">&nbsp;</td>
                                    <td style="width:8%;"><a href="ver_cheque.php?enc=<?= $linkcheque ?>"><img src="imagens/ver_banc.png" border="0" alt="Visualizar Funcionários por Cheque"></a></td>
                                    <td style="width:20%; text-align:center; padding-right:5px;"><?= $total_cheque ?> Participantes</td>
                               </tr><?php } ?>
                                <tr>
                                    <td colspan="5">
                                <?php if ($ACOES->verifica_permissoes(77)) { ?>
                                            <a href="<?= $link_pagamento_lote ?>" style="font-weight:bold; padding-left:5px;" >Pagamento em lote</a> |
                                <?php } ?> 
                                <?php if ($ACOES->verifica_permissoes(81)) { ?>
                                            <a href="<?= $link_dados_bancarios ?>" style="font-weight:bold; padding-left:5px;" >Verificar dados bancários</a> |
    <?php } ?> 
                                        <a href="<?= $link_lista_banco ?>" style="font-weight:bold; padding-left:5px;">Ver Lista por Banco</a> |
                                        <a href="<?= $link_relatorio ?>" style="font-weight:bold; padding-left:5px;">Ver Relatório por movimentos</a> |
                                        <!-- a href="<?= $link_rescisao ?>" style="font-weight:bold; padding-left:5px;">Ver Relatório De Rescisão</a> | -->
    <?php if (in_array($_COOKIE['logado'], $arrayDeveloper)) { ?>
                                            <a href="<?= $link_totalizadorObj ?>" style="font-weight:bold; padding-left:5px;">Novo Totalizador</a> |
    <?php } ?>
                                        <a href="<?= $link_rel_sindical ?>" style="font-weight:bold; padding-left:5px;">Relatório Sindical</a> |
                                        <!--a href="<?= $link_rel_folha_simples ?>" style="font-weight:bold; padding-left:5px;">Relatório Simplificada</a> | -->
                                        <a href="<?= $link_rel_folha_pensao ?>" style="font-weight:bold; padding-left:5px;">Relatório Pensão</a> |
                                        <a href="<?= $link_rel_folha_iabas ?>" style="font-weight:bold; padding-left:5px;">Relatório de Folha Finalizada</a> |
                                        <!--a href="<?= $link_rel_folha_pis ?>" style="font-weight:bold; padding-left:5px;">Relatório de PIS</a> | -->
                                        <a href="<?= $linkGerarPdf ?>" target="_blank" style="font-weight:bold; padding-left:5px;">Gerar PDF Resumo da Folha</a> |
                                        <a href="relatorio_rpas_pagos_na_folha.php" style="font-weight:bold; padding-left:5px;">Relatório RPAs Pagos na Folha</a>
                                    </td>
                                </tr>
                            </table>
                        </div>

                    </div>
                </div>

                <div class="printDoc clear"></div>
                <div class="printDoc" id="rodape_pdf" style="font-size: 11px;">
                    <p>Gerado em <?php echo date("d/m/Y H:i:s") ?> - por: <?php echo $usuarioLogado['nome1'] ?> - F71 Sistemas WEB</p>
                </div>
            </div>
        </body>
    </html>




    <?php
    // se é folha antiga...
} else {
    ?>




    <?php
    if (!empty($_REQUEST['agencia'])) {

        $ag = $_REQUEST['agencia'];
        $cc = $_REQUEST['conta'];

        $clt = $_REQUEST['clt'];
        $tipo_conta = $_REQUEST['radio_tipo_conta'];

        $RE_clt = mysql_query("SELECT * FROM rh_folha_proc where id_folha_proc = '$clt' and status = 3 and tipo_pg = '0'") or die(mysql_error());
        $RowCLT = mysql_fetch_array($RE_clt);

        mysql_query("UPDATE rh_clt SET agencia='$ag', conta='$cc', tipo_conta='$tipo_conta' WHERE id_clt = '$RowCLT[id_clt]'") or die(mysql_error());
        mysql_query("UPDATE rh_folha_proc SET agencia='$ag', conta='$cc' WHERE id_folha_proc = '$clt'") or die(mysql_error());
    }

    include "../../classes/regiao.php";

    $Regi = new regiao();

//RECEBENDO A VARIAVEL CRIPTOGRAFADA
    $enc = $_REQUEST['enc'];
    $enc = str_replace("--", "+", $enc);
//    $decrypt = new \Rh\encryptClass();
//    $link_novo = $decrypt->Decrypt($enc);
//    $enc = str_replace("--", "+", $enc);
    $link = decrypt($enc);
//    $link = $decrypt->getPlainTextDec();
//    echo '<pre>';
//    var_dump($enc);
//    var_dump($link_novo);
//    echo '</pre>';

    $decript = explode("&", $link);

    $regiao = $decript[0];
    $folha = $decript[1];

//RECEBENDO A VARIAVEL CRIPTOGRAFADA

    $id_user = $_COOKIE['logado'];


    $result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
    $row_user = mysql_fetch_array($result_user);

    $result_master = mysql_query("SELECT * FROM master where id_master = '$row_user[id_master]'");
    $row_master = mysql_fetch_array($result_master);

    $result_folha = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc2,date_format(data_inicio, '%d/%m/%Y')as data_inicio,date_format(data_fim, '%d/%m/%Y')as data_fim FROM rh_folha where id_folha = '$folha'");
    $row_folha = mysql_fetch_array($result_folha);

    $result_projeto = mysql_query("SELECT * FROM projeto where id_projeto = '$row_folha[projeto]'");
    $row_projeto = mysql_fetch_array($result_projeto);

    $meses = array('Erro', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
    $mesInt = (int) $row_folha['mes'];
    $mes_da_folha = $meses[$mesInt];

    $titulo = "Folha: Projeto $row_projeto[nome] mês de $mes_da_folha";

    $ano = date("Y");
    $mes = date("m");
    $dia = date("d");

    $data = date("d/m/Y");

    $data_menor14 = date("Y-m-d", mktime(0, 0, 0, $mes, $dia, $ano - 14));
    $data_menor21 = date("Y-m-d", mktime(0, 0, 0, $mes, $dia, $ano - 21));

    $result_codigos = mysql_query("SELECT distinct(cod) FROM rh_movimentos WHERE cod != '0001' ORDER BY cod");

    while ($row_codigos = mysql_fetch_array($result_codigos)) {
        $ar_codigos[] = $row_codigos['0'];
    }





    $RE_TipoDepo = mysql_query("SELECT id_tipopg,tipopg FROM tipopg WHERE id_projeto = '$row_folha[projeto]' and campo1 = '1'");
    $row_TipoDepo = mysql_fetch_array($RE_TipoDepo);

    $RE_TIpoCheq = mysql_query("SELECT id_tipopg,tipopg FROM tipopg WHERE id_projeto = '$row_folha[projeto]' and campo1 = '2'");
    $row_TIpoCheq = mysql_fetch_array($RE_TIpoCheq);
    ?>
    <html>
        <head>
            <script type="text/javascript" src="../../js/prototype.js"></script>
            <script type="text/javascript" src="../../js/scriptaculous.js?load=effects,builder"></script>
            <script type="text/javascript" src="../../js/lightbox.js"></script>
            <script type="text/javascript" src="../../js/highslide-with-html.js"></script>
            <link rel="stylesheet" href="../../js/lightbox.css" type="text/css" media="screen"/>
            <link rel="stylesheet" type="text/css" href="../../js/highslide.css" />

            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
            <title><?= $titulo ?></title>

            <script type="text/javascript">
                                            hs.graphicsDir = '../../images-box/graphics/';
                                            hs.outlineType = 'rounded-white';
            </script>
            <style type="text/css">
                a:visited {font-size: 10px; color: #F00; text-decoration: none; font-weight: bold; font-family: Verdana, Arial, Helvetica, sans-serif;}
                a:link{font-size: 10px; color:#F00; text-decoration: none; font-weight: bold; font-family: Verdana, Arial, Helvetica, sans-serif;}


            </style>
            <link href="../../net1.css" rel="stylesheet" type="text/css">
        </head>

        <body>

            <table width="95%" border="0" align="center">
                <tr>
                    <td align="center" valign="middle" bgcolor="#FFFFFF"><div style="font-size:9px; text-align:left; color:#E2E2E2;"><b>ID:
    <?php
    echo $folha . ", região: ";
    $Regi->MostraRegiao($row_folha['regiao']);
    echo $Regi->regiao;
    ?>
                            </b></div>
                        <table width="90%" border="0" align="center">
                            <tr>
                                <td width="100%" height="81" align="center" valign="middle" bgcolor="#003300" class="title"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td width="16%" align="center" valign="middle" bgcolor="#E2E2E2"><span class="style1"><img src="../../imagens/logomaster<?= $row_user['id_master'] ?>.gif" alt="" width="110" height="79" align="absmiddle" ></span></td>
                                            <td width="62%" bgcolor="#E2E2E2"><span class="Texto10">
                                <?= $row_master['razao'] ?>
                                                    <br>
                                                    CNPJ : <?= $row_master['cnpj'] ?>
                                                </span><span class="style1"><br>
                                                </span></td>
                                            <td width="22%" bgcolor="#E2E2E2">
                                                <span class="Texto10">
                                                    Processamento: 
    <?= $row_folha['data_proc2'] ?>
                                                    <br>
                                                    Inicio da folha: 
    <?= $row_folha['data_inicio'] ?>
                                                    <br />
                                                    Fim da folha: 
    <?= $row_folha['data_fim'] ?>
                                                </span></td>
                                        </tr>
                                    </table></td>
                            </tr>
                        </table>
                        <br />
                        <span class="titulo_opcoes">Folha de Pagamento - <?= $mes_da_folha ?> / <?= $row_folha['ano'] ?> </span><br />
                        <br />
                        <table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr style="font-weight:bold;">
                                <td width="7%" height="25" bgcolor="#CCCCCC">C&oacute;digo</td>
                                <td width="31%" bgcolor="#CCCCCC">Nomes </td>
                                <td width="8%" align="center" bgcolor="#CCCCCC">Sal&aacute;rio</td>
                                <td width="4%" align="center" bgcolor="#CCCCCC">Dias</td>
                                <td width="7%" align="center" bgcolor="#CCCCCC">Rendim.</td>
                                <td width="7%" align="center" bgcolor="#CCCCCC">Descontos</td>
                                <td width="8%" align="center" bgcolor="#CCCCCC">Sal. Base</td>
                                <td width="5%" align="center" bgcolor="#CCCCCC">INSS</td>
                                <td width="8%" align="center" bgcolor="#CCCCCC">IRRF</td>
                                <td width="6%" align="center" bgcolor="#CCCCCC">Sal. Fam. </td>
                                <td width="9%" align="center" bgcolor="#CCCCCC">Sal. L&iacute;q.</td>
                            </tr>

    <?php
    $cont = "0";

    $resultClt = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha' and 
		  ( status = '2' or status = '3' or status = '4')  ORDER BY nome");
    while ($row_clt = mysql_fetch_array($resultClt)) {

        //////***************FÓRMULA TOP SECRET PARA OCULTAR PARTICIPANTES************************///// 
//                                if ($row_clt['id_clt'] == '4213' or $row_clt['id_clt'] == '4425')
//                                    continue;
        //DEFINIE QUE O FUNCIONÁRIO IRÁ RECEBER EM CHEQUE CASO ELE NÃO TENHA UM NUMERO DE CONTA, AGÊNCIA OU TIPO DE CONTA DEFINIDO.
        $resultTipoConta = mysql_query("SELECT tipo_conta FROM rh_clt WHERE id_clt = $row_clt[id_clt]");
        $rowP = mysql_fetch_array($resultTipoConta);

        $tiposDePagamentos = mysql_query("SELECT * FROM tipopg WHERE id_regiao = '$regiao' and campo1 = '2' and id_projeto = '$row_projeto[0]'");
        $rowTipoPg = mysql_fetch_array($tiposDePagamentos);
        $pgEmCheque = $rowTipoPg[0];

        if (($row_clt['conta'] == '') or ( $row_clt['conta'] == '0')) {
            mysql_query("UPDATE rh_folha_proc SET tipo_pg = '$pgEmCheque' WHERE id_folha = '$folha' and id_clt = $row_clt[id_clt]");
        }
        if (($row_clt['agencia'] == '') or ( $row_clt['agencia'] == '0')) {
            mysql_query("UPDATE rh_folha_proc SET tipo_pg = '$pgEmCheque' WHERE id_folha = '$folha' and id_clt = $row_clt[id_clt]");
        }
        if ($rowP['tipo_conta'] == '') {
            mysql_query("UPDATE rh_folha_proc SET tipo_pg = '$pgEmCheque' WHERE id_folha = '$folha' and id_clt = $row_clt[id_clt]") or die(mysql_error());
        }


        //----FORMATANDO OS VALORES------------------------
        //$row_clt[cod]
        $salario_brutoF = number_format($row_clt['salbase'], 2, ",", ".");
        $total_rendiF = number_format($row_clt['rend'], 2, ",", ".");
        $total_debitoF = number_format($row_clt['desco'], 2, ",", ".");
        $valor_inssF = number_format($row_clt['a5020'], 2, ",", ".");
        //$valor_IRF = number_format($row_clt['imprenda'],2,",",".");
        $valor_IRF = number_format($row_clt['a5021'], 2, ",", ".");
        $valor_familiaF = number_format($row_clt['a5022'], 2, ",", ".");

        $valor_final_individualF = number_format($row_clt['salliquido'], 2, ",", ".");

        //$valor_desconto_sindicatoF = number_format($valor_desconto_sindicato,2,",",".");
        //$valor_deducao_irF = number_format($valor_deducao_ir,2,",",".");
        //-------------------
        //---- EMBELEZAMENTO DA PAGINA ----------------------------------
        if ($cont % 2) {
            $color = "corfundo_um";
        } else {
            $color = "corfundo_dois";
        }
        $nome = str_split($row_clt['nome'], 30);
        $nomeT = sprintf("% -30s", $nome[0]);
        if ($row_clt['status_clt'] == '50' or $row_clt['status_clt'] == '51') {
            $nomeT = "<span style='color:#693;'>$nomeT</span>";
        }
        $bord = "style='border-bottom:#000 solid 1px;'";
        //-----------------
        // colocando o valor livre de redimento (feito por jr 05-02-2010 as 14:49)	
        //$salario = number_format($row_clt['salbase'] - $row_clt['rend'],2,",","."); ALTERADO JR 27/04/2010
        $salario = number_format($row_clt['sallimpo'], 2, ",", ".");
        $salario_final += $row_clt['sallimpo'];

        echo "<tr class=\"novalinha $color\">";
        echo "<td align='left' valign='middle'>" . $row_clt['cod'] . " </td>";
        //echo "<td align='center' valign='middle' $bord>".$nomeT."</td>";
        echo "<td align='left' valign='middle'>$nomeT</a> $divTT</td>";
        echo "<td align='center' valign='middle'>" . $salario . "</td>";
        echo "<td align='center' valign='middle'>" . $row_clt['dias_trab'] . "</td>";
        echo "<td align='center' valign='middle'>" . $total_rendiF . "</td>";
        echo "<td align='center' valign='middle'>" . $total_debitoF . "</td>";
        echo "<td align='center' valign='middle'>" . $salario_brutoF . "</td>";
        echo "<td align='center' valign='middle'>" . $valor_inssF . "</td>";
        echo "<td align='center' valign='middle'>" . $valor_IRF . "</td>";
        echo "<td align='center' valign='middle'>" . $valor_familiaF . "</td>";
        echo "<td align='center' valign='middle'>" . $valor_final_individualF . "</td></tr>";


        // AQUI TERMINA O LAÇO ONDE MOSTRA E CALCULA OS VALORES REFERENTES A UM ÚNICO FUNCIONARIO
        // FORMATANDO OS DADOS FINAIS

        $cont++;
    }




    //---- FORMATANDO OS TOTAIS GERAIS DA FOLHA -----------
    $salario_base_finalF = number_format($row_folha['total_salarios'], 2, ",", ".");
    $rendi_indiviF = number_format($row_folha['rendi_indivi'], 2, ",", ".");
    $rendi_finalF = number_format($row_folha['rendi_final'], 2, ",", ".");
    $final_indiviF = number_format($row_folha['descon_indivi'], 2, ",", ".");
    $final_INSSF = number_format($row_folha['total_inss'], 2, ",", ".");
    $final_IRF = number_format($row_folha['total_irrf'], 2, ",", ".");
    $final_familiaF = number_format($row_folha['total_familia'], 2, ",", ".");
    ;
    $valor_finalF = number_format($row_folha['total_liqui'], 2, ",", ".");
    $totalDeFGTS = number_format($row_folha['total_fgts'], 2, ",", ".");

    $base_INSS_TO = number_format($row_folha['valor_dt'] + $row_folha['base_inss'], 2, ",", ".");
    $base_IRRFF = number_format($row_folha['base_irrf'], 2, ",", ".");
    //-----------------------
    //VERIFICANDO SE VAI MOSTRAR OU NÃO OS DESCONTOS FIXOS (EX VALE, INSS, IR, FAMILIA)------------
    $movimentos_fixos = array(0001, 7001, 5020, 5021, 5022, 5019, 5047);
    $valores_movimentos_fixos = array($salario_base_finalF, $vale_transporte_finalF, $final_INSSF, $final_IRF, $final_familiaF, $final_sindicatoF, $final_deducaoIRF);

    // colocando o valor livre de redimento (feito por jr 05-02-2010 as 14:49)
    // (feito por jr 06-05-2010 as 16:16) $salariototal = number_format($row_folha['total_salarios'] - $row_folha['rendi_indivi'],2,",",".");
    $salariototal = number_format($row_folha['total_salarios'], 2, ",", ".");
    $salario_finalF = number_format($salario_final, 2, ",", ".");
    ?>

                            <tr>
                                <td height="20" align="center" valign="middle" class="style23">&nbsp;</td>
                                <td height="20" align="right" valign="bottom" class="style23">TOTAIS:</td>
                                <td height="20" align="center" valign="bottom" class="style23"><?= $salario_finalF ?></td>
                                <td height="20" align="center" valign="bottom" class="style23">&nbsp;</td>
                                <td align="center" valign="bottom" class="style23"><?= $rendi_indiviF ?></td>
                                <td align="center" valign="bottom" class="style23"><?= $final_indiviF ?></td>
                                <td align="center" valign="bottom" class="style23"><?= $salario_base_finalF ?></td>
                                <td align="center" valign="bottom" class="style23"><?= number_format($row_folha['total_inss'] + $row_folha['inss_dt'], 2, ",", "."); ?></td>
                                <td align="center" valign="bottom" class="style23"><?= $final_IRF ?></td>
                                <td align="center" valign="bottom" class="style23"><?= $final_familiaF ?></td>
                                <td align="center" valign="bottom" class="style23"><?= $valor_finalF ?></td>
                            </tr>

                        </table>
                        <br />
                        <br>
                        <br>
                        <br>
                        <table width="97%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="39%" align="center" valign="top" bgcolor="#F8F8F8" style="border-right:solid 2px #FFF"><br>
                                    <table width="90%" align="center" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td height="24" colspan="2" align="center" valign="middle" class="show">TOTALIZADORES</td>
                                        </tr>
                                        <tr class="novalinha corfundo_um">
                                            <td width="53%" align="right">Sal&aacute;rios L&iacute;quidos:</td>
                                            <td width="47%" align="left"> &nbsp;&nbsp;<?= $valor_finalF ?></td>
                                        </tr>
                                        <tr class="novalinha corfundo_dois">
                                            <td align="right">Base de INSS*:</td>
                                            <td align="left"> &nbsp;&nbsp;<?= $base_INSS_TO ?></td>
                                        </tr>
                                        <tr class="novalinha corfundo_um">
                                            <td align="right">Base de IRRF:</td>
                                            <td align="left">&nbsp;&nbsp;<?= $base_IRRFF ?></td>
                                        </tr>
                                        <tr class="novalinha corfundo_dois">
                                            <td align="right">Base de FGTS:</td>
                                            <td align="left">&nbsp;&nbsp;<?= $base_INSS_TO ?></td>
                                        </tr>
                                        <tr class="novalinha corfundo_um">
                                            <td align="right">Total de FGTS:</td>
                                            <td align="left" valign="middle">&nbsp;&nbsp;<?= $totalDeFGTS ?></td>
                                        </tr>
                                        <tr class="novalinha corfundo_dois">
                                            <td align="right">Base de FGTS (Sefip):</td>
                                            <td align="left" valign="middle">&nbsp;&nbsp;<?= $base_INSS_TO ?></td>
                                        </tr>
                                        <tr class="novalinha corfundo_um">
                                            <td align="right">FGTS a Recolher (Sefip):</td>
                                            <td align="left" valign="middle">&nbsp;&nbsp;<?= $totalDeFGTS ?></td>
                                        </tr>
                                        <tr class="novalinha corfundo_dois">
                                            <td align="right">Multa do FGTS:</span></td>
                                            <td align="left">&nbsp;&nbsp; 0,00</td>
                                        </tr>
                                        <tr class="novalinha corfundo_um">
                                            <td align="right">Funcion&aacute;rios Listados:</td>
                                            <td align="left" valign="middle">&nbsp;&nbsp;<?= $row_folha['clts'] ?></td>
                                        </tr>
                                    </table></td>
                                <td width="61%" align="center" valign="top" bgcolor="#F8F8F8" style="border-left:solid 2px #FFF"><br>
                                    <table width="95%" align="center" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td height="30" colspan="4" align="center" valign="middle" class="show">Resumo por Evento (R$)</td>
                                        </tr>
                                        <tr class="novo_tr_dois">
                                            <td width="11%" align="center" valign="middle" >Evento</td>
                                            <td width="45%" align="left" valign="middle" >Descri&ccedil;&atilde;o </td>
                                            <td width="21%" height="20" align="right" valign="middle" >Rendimentos </td>
                                            <td width="23%" align="right" valign="middle"  style='margin-right:5;'>Descontos</td>
                                        </tr>
                                        <tr class="novalinha corfundo_um">
                                            <td align="center">0001</td>
                                            <td align="left" >SALARIO BASE</td>
                                            <td align="right" ><b><?= $salario_base_finalF ?></b></td>
                                            <td align="right" >&nbsp;</td>
                                        </tr>
    <?php
    $qntd = count($ar_codigos);
    for ($i = 0; $i < $qntd; $i++) {
        $result_codNomes = mysql_query("SELECT descicao FROM rh_movimentos WHERE cod='$ar_codigos[$i]'");
        $row_codNome = mysql_fetch_array($result_codNomes);
        $campo = "a" . $ar_codigos[$i];

        $reult_soma = mysql_query("SELECT SUM($campo) FROM rh_folha_proc WHERE id_folha = '$folha' and status = '3'");
        $row_soma = @mysql_fetch_array($reult_soma);

        $debitos_tab = array('5019', '5020', '5021', '6004', '7003', '8000', '7009', '5020', '5020', '5021', '5021', '5021', '5020', '9500', '7001');
        $rendimentos_tab = array('5011', '5022', '6006', '6007', '9000', '5022');

        if (in_array($ar_codigos[$i], $debitos_tab)) {
            if ($ar_codigos[$i] == "5020") {
                $debito = number_format($row_folha['total_inss'], 2, ",", ".");
            } else {
                $debito = number_format($row_soma['0'], 2, ",", ".");
            }
        } else {
            $rendimento = number_format($row_soma['0'], 2, ",", ".");
        }

        if ($rendimento == "0,00" or $debito == "0,00") {
            $disable = "style='display:none'";
        } else {
            $disable = "style='display:'";
        }

        if ($campo == "a5049") {     //DDIR
            $disable = "style='display:none'";
        }

        print "<tr class=\"novalinha corfundo_um\" $disable>
	          <td height='18' align='center' valign='middle'>$ar_codigos[$i]</td>
	          <td align='left' valign='middle'>$row_codNome[0]</td>
	          <td align='right' valign='middle'><span style='margin-right:1;'><b>" . $rendimento . "&nbsp;</b></span></td>
	          <td align='right' valign='middle' ><span style='margin-right:5;'><b>" . $debito . "&nbsp;</b></span></td></tr>";

        $debito = "";
        $rendimento = "";
    }
    ?>
                                        <?php if ($row_folha['terceiro'] == 1) { ?>
                                            <tr class="novalinha corfundo_um">
                                                <td height="18" align="center" valign="middle">5029</td>
                                                <td align="left" valign="middle">D&Eacute;CIMO TERCEIRO SAL&Aacute;RIO</td>
                                                <td align="right" valign="middle"><b>
                                            <?= number_format($row_folha['valor_dt'], 2, ",", ".") ?>
                                                    </b></td>
                                                <td align="right" valign="middle">&nbsp;</td>
                                            </tr>
                                            <tr class="novalinha corfundo_dois">
                                                <td height="18" align="center" valign="middle">5030</td>
                                                <td align="left" valign="middle">IRRF D&Eacute;CIMO TERCEIRO SAL&Aacute;RIO</td>
                                                <td align="right" valign="middle">&nbsp;</td>
                                                <td align="right" valign="middle"><span style="margin-right:5;"><b><?= number_format($row_folha['ir_dt'], 2, ",", ".") ?></b></span></td>
                                            </tr>
                                            <tr class="novalinha corfundo_um">
                                                <td height="18" align="center" valign="middle">5031</td>
                                                <td align="left" valign="middle">INSS TERCEIRO SAL&Aacute;RIO</td>
                                                <td align="right" valign="middle">&nbsp;</td>
                                                <td align="right" valign="middle"><span style="margin-right:5;"><b><?= number_format($row_folha['inss_dt'], 2, ",", ".") ?></b></span></td>
                                            </tr>
                                                        <?php
                                                    }


                                                    //FORMATANDO TOTAIS POR EVENTO
                                                    $re_tot_rendimentofimF = number_format($row_folha['rendi_final'], 2, ",", ".");
                                                    $re_tot_descontoF = number_format($row_folha['descon_final'], 2, ",", ".");
                                                    ?>
                                        <tr class="novo_tr_dois">
                                            <td colspan="3" align="center">TOTAIS</td>
                                            <td height="20" align="right" ><?= $re_tot_rendimentofimF ?></td>
                                            <td align="right" style="text-align:right"><span style="margin-right:5;"><?= $re_tot_descontoF ?></span></td>
                                        </tr>
                                    </table></td>
                            </tr>
                        </table>
                        <br>


                                        <?php
                                        //VERIFICANDO QUAIS BANCOS ESTÃO ENVOLVIDOS COM ESSA FOLHA DE PAGAMENTO

                                        $RE_Bancs = mysql_query("SELECT id_banco FROM rh_folha_proc WHERE id_banco != '9999' AND id_folha = '$folha' and id_banco > '0' and 
	  (status = '3' or status = '4') GROUP BY id_banco");
                                        $num_Bancs = mysql_num_rows($RE_Bancs);

                                        echo "<table border='0' width='50%' border='0' cellpadding='0' cellspacing='0'>";
                                        echo "<tr><td colspan=5 align='center' $bord><div style='font-size: 17px;'><b>Lista de Bancos</b></div></td></tr>";
                                        $contCol = 0;
                                        while ($row_Bancs = mysql_fetch_array($RE_Bancs)) {

                                            $RE_Bancos = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row_Bancs[0]'");
                                            $row_Bancos = mysql_fetch_array($RE_Bancos);
                                            //-- ENCRIPTOGRAFANDO A VARIAVEL
                                            $linkBanc = encrypt("$regiao&$row_Bancos[0]&$folha");
                                            $linkBanc = str_replace("+", "--", $linkBanc);
                                            // -----------------------------
                                            $linkBank = "folha_banco.php?enc=$linkBanc";
                                            $disable_form = "style='display:none'";
                                            echo "<tr>";
                                            echo "<td align='center' valign='middle' width='30' $bord><div style='font-size: 15px;'>";
                                            echo "<img src=../../imagens/bancos/$row_Bancos[id_nacional].jpg  width='25' height='25' 
		  align='absmiddle' border='0'></td>";
                                            echo "<td valign='middle' $bord>&nbsp;&nbsp;" . $row_Bancos['nome'] . "</div></a></td>";

                                            $resultBancosFinalizados = mysql_query("SELECT id_banco FROM rh_folha_proc WHERE id_folha = '$folha' and status='4' and id_banco = '$row_Bancs[0]' group by id_banco");
                                            $numBancosFinalizados = mysql_affected_rows();
                                            if ($numBancosFinalizados != 0) {
                                                $rowBancosFinalizados = mysql_fetch_array($resultBancosFinalizados);
                                                $resultPartFinalizados = mysql_query("SELECT id_clt FROM rh_folha_proc where id_folha = '$folha' and status = '4' and id_banco = '$rowBancosFinalizados[0]'");
                                                $numPartFinalizados = mysql_num_rows($resultPartFinalizados);
                                                print "<td $bord>&nbsp;</td>";
                                                print "<td  align='right' $bord>";
                                                print "&nbsp;&nbsp;<a href=finalizados.php?regiao=$regiao&folha=$folha&projeto=$row_projeto[0]&banco=$rowBancosFinalizados[0]>FINALIZADO</a>";
                                                print "</td>";

                                                echo "<td align='center' valign='middle' width='10%' $bord>$numPartFinalizados Participantes</td>";
                                            } else {
                                                $resultPorBanco = mysql_query("SELECT id_banco FROM rh_folha_proc WHERE id_folha = '$folha' and status = '3' and id_banco = '$row_Bancs[0]'");
                                                $quant_por_banco = mysql_affected_rows();

                                                if ($quant_por_banco != 0) {
                                                    echo "<td valign='center' $bord><form id='form1' name='form1' method='post' action='$linkBank'>&nbsp;
							  <label id='data_pag$contCol' $disable_form> 
                                                          <input type='hidden' name='test' value='' />     
							  <input name='data' type='text' id='data[]' size='10' class='campotexto'
							  onKeyUp='mascara_data(this)' maxlength='10' onFocus=\"this.style.background='#CCFFCC'\"
							  onBlur=\"this.style.background='#FFFFFF'\" style='background:#FFFFFF' >
							  <input name='enviar' id='enviar[]' type='submit' value='Gerar'/></label>
							  </td>";
                                                    echo "</form>";

                                                    echo "<td align='right' valign='middle' width='15%' $bord><a style='TEXT-DECORATION: none;'>
						  <img src='imagens/ver_banc.png' border='0' alt='Visualizar Funcionarios por Banco' onClick=\"document.all.data_pag$contCol.style.display = (document.all.data_pag$contCol.style.display == 'none') ? '' : 'none' ;\"></a></td>";
                                                    echo "<td align='center' valign='middle' width='15%' $bord>$quant_por_banco Participantes</td>";
                                                } else {
                                                    echo "<td $bord>&nbsp;</td>";
                                                    echo "<td $bord align='right'><span style='font-family:verdana, arial; font-size:9px; color:red'><strong>VERIFICAR</strong></span></td>";
                                                    echo "<td align='center' valign='middle' width='15%' $bord>$quant_por_banco Participantes</td>";
                                                }
                                            }
                                            $contCol++;
                                        }

                                        $RE_ToCheq = mysql_query("SELECT * FROM rh_folha_proc WHERE (id_folha = '$folha' and id_banco = '0' and status = '3') or (id_folha = '$folha' and agencia = '' and status = '3') or (id_folha = '$folha' and conta = '' and status = '3') or (id_folha = '$folha' and tipo_pg = '$rowTipoPg[0]' and status = '3')");
                                        /// $num_ToCheq = mysql_num_rows($RE_ToCheq);
                                        $num_ToCheq = mysql_affected_rows();

                                        //-- ENCRIPTOGRAFANDO A VARIAVEL
                                        $linkcheque = encrypt("$regiao&$folha&$row_TIpoCheq[0]&$row_TipoDepo[0]");
                                        $linkcheque = str_replace("+", "--", $linkcheque);
                                        // -----------------------------

                                        echo "<tr>";
                                        echo "<td align='center' valign='middle' width='30' $bord>";
                                        echo "<img src=../../imagens/bancos/cheque.jpg  width='25' height='25' align='absmiddle' border='0'></td>";
                                        echo "<td valign='middle' $bord><div style='font-size: 15px;'>&nbsp;&nbsp;Cheque</div></a></td>";
                                        echo "<td valign='center' $bord>&nbsp;</td>";
                                        echo "<td align='right' valign='middle' width='10%' $bord><a href='ver_cheque.php?enc=$linkcheque'>
		  <img src='imagens/ver_banc.png' border='0' alt='Visualizar Funcionarios por Cheque'></a></td>";

                                        echo "<td align='center' valign='middle' width='15%' $bord>$num_ToCheq Participantes</td>";
                                        echo "</tr></table>";
                                        ?>


                        <br>
                        <br>
                        <?php
//-- ENCRIPTOGRAFANDO A VARIAVEL
                        $linkvolt = encrypt("$regiao&1");
                        $linkvolt = str_replace("+", "--", $linkvolt);
// -----------------------------
                        $enc2 = str_replace("+", "--", $enc);
                        ?>
                        <br></td>
                </tr>
                <tr>
                    <td align="center" valign="middle" bgcolor="#CCCCCC">
                        <b><a href='folha.php?<?= "enc=" . $linkvolt . "&tela=1" ?>' class="botao">VOLTAR</a></b>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <b><a href='ver_lista_banco.php?<?= "enc=" . $enc2 ?>' class="botao">VER LISTA POR BANCO</a></b>
                        <b><a href='ver_lista_banco.php?<?= "enc=" . $enc2 ?>' class="botao">GERAR PDF</a></b>

                    </td>
                </tr>
            </table>
            <p>&nbsp;</p>
        </body>
    </html>

<?php } ?>


<?php
if ($_COOKIE['logado'] == 204) {
    //echo $total_entra_base;
}
?>
<footer>
    <div class="row">
        <a id="top" href="#top" class="hidden-print" style="margin: 15px; display: inline;">
            <span class="fa fa-arrow-circle-up"></span>
        </a>
    </div>
</footer>
<script language='javascript'>

    $(document).ready(function () {
        $('#imprimirPdf').click(function () {
            window.print();
            return false;
        });

        $("body").on("click", ".testArquivo", function () {
            $("input[name='test']").val(1);
        });

        /*aki*/
        $.post('write.php',
                {
                    dom:
                            $('<div></div>').
                            append('<link href="css/folha-pdf.css" rel="stylesheet" type="text/css">').
                            append('<div id="resumo" class="resumo">' + $('.resumo').html() + '</div>').
                            append('<div id="totalizadores" class="totalizadores">' + $('#totalizadores').html() + '</div>').
                            append('<div id="totalizadores" class="totalizadores">' + $('.totalizadores_57').html() + '</div>').
                            append('<div id="totalizadores" class="totalizadores">' + $('.totalizadores_43').html() + '</div>').
                            append('<div style="margin-top:15px;">' + $('#rodape_pdf').html() + '</div>').
                            html(),
                    id_folha:
                            $("#id_folha").html(),
                    cookie:
                            $("#cookie").html()
                });
        $('#titlePdf').on('click', function () {
            $('.participante').removeAttr('href');
            $('.participante').css('text-decoration', 'none');
            $('.participante').css('color', '#000');



        });
        var tabela = $('#tabelaExpPDF').html();
        $('#tabelaPdf').val(tabela);
        
        //exportar excel
//        $("#exportarExcel").click(function () {
//            $("#relatorio_exp img:last-child").remove(); 
//            $(".esconde").remove();             
//            //$(".subiraotopo").remove();
//            //$('#folha tr:nth-child(1)').appendTo('.temporaria');
//            //$('#folha tr:nth-child(1)').appendTo('.temporaria');            
//            var html = $("#relatorio_exp").html(); 
//            $("#data_xls").val(html);
//            $("#form2").submit();
//            //var htmltemp= $('.temporaria').html();
//            //$('#folha').prepend(htmltemp);                   
//        });
        //exportar excel 2
         $("#exportarExcel").click(function (){
            $.post( "../../funcoes/excelFolha.php", 
                { //particip: dados,
                  folha : <?php echo $folha ?>
                })
                .done(function( data ) {
                  //console.log( "Data Loaded: " + data ); // retorna 'undefined index'
                  window.location.href = "http://f71iabassp.com/intranet/funcoes/excelFolha.xlsx";
                });
        });
    });


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
            alert("Data digitada é inválida, digite novamente!");
            d.value = "";
            d.focus();
        }

    }
</script> 
