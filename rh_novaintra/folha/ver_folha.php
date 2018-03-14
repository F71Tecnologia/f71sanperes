<?php
// Verificando se o usuário está logado
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../../../login.php">Logar</a>';
    exit;
}

$arrayDeveloper = array(179,158,87);

// Incluindo Arquivos
require('../../conn.php');
include('../../funcoes.php');
include('../../classes_permissoes/acoes.class.php');
include('../../classes/FolhaClass.php');

error_reporting(0);
$ACOES = new Acoes();
$objFolha = new Folha();
// Buscando a Folha
list($regiao, $folha) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
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
    include('../../wfunction.php');

    $Regi = new regiao();
    $Trab = new proporcional();
    $objMovimento = new Movimentos();

    // Consulta da Folha
    $qr_folha = mysql_query("SELECT *, date_format(data_inicio, '%d/%m/%Y') AS data_inicio_br,
                            date_format(data_fim, '%d/%m/%Y') AS data_fim_br,
                            date_format(data_proc, '%d/%m/%Y') AS data_proc_br,
                            (SELECT COUNT(id_clt) FROM rh_folha_proc WHERE id_folha = rh_folha.id_folha AND status_clt NOT IN(10,200)) as total_rescindidos
                            FROM rh_folha WHERE id_folha = '$folha' AND status = '3'");
    
    if($_COOKIE['logado'] == 179){
        echo "SELECT *, date_format(data_inicio, '%d/%m/%Y') AS data_inicio_br,
                            date_format(data_fim, '%d/%m/%Y') AS data_fim_br,
                            date_format(data_proc, '%d/%m/%Y') AS data_proc_br,
                            (SELECT COUNT(id_clt) FROM rh_folha_proc WHERE id_folha = rh_folha.id_folha AND status_clt NOT IN(10,200)) as total_rescindidos
                            FROM rh_folha WHERE id_folha = '$folha' AND status = '3'";
    }

    $row_folha = mysql_fetch_array($qr_folha);
    $data_inicio = $row_folha['data_inicio'];
    $data_fim = $row_folha['data_fim'];
    $ano = $row_folha['ano'];
    $mes = $row_folha['mes'];
    $mes_int = (int) $mes;
    
   

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
    if ($ano >= 2011) {
        $percentual_rat = '0.01';
    } else {
        $percentual_rat = '0.03';
    }

    // Encriptografando Links
    $link_voltar = 'folha.php?enc=' . str_replace('+', '--', encrypt("$regiao&1")) . '&tela=1';
    $link_lista_banco = 'ver_lista_banco.php?enc=' . str_replace('+', '--', $_REQUEST['enc']) . '&tela=1';
    $link_pagamento_lote = 'pg_lote.php?enc=' . str_replace('+', '--', $_REQUEST['enc']) . '&tela=1';
    $link_dados_bancarios = 'confere_banco.php?enc=' . str_replace('+', '--', $_REQUEST['enc']) . '&tela=1';
    $link_relatorio = 'relatorio_movimentos.php?enc=' . str_replace('+', '--', $_REQUEST['enc']);
    $link_rescisao = 'relatorio_rescisao.php?enc=' . str_replace('+', '--', $_REQUEST['enc']);
    $link_totalizadorObj = '../folha_oo/totalizador.php?enc=' . str_replace('+', '--', $_REQUEST['enc']);
    $link_rel_sindical = 'relatorio_sindical.php?enc=' . str_replace('+', '--', $_REQUEST['enc']);
    
    $usuario = carregaUsuario();
    
    $dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
    $breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Visualizar Folha de Pagamento");
    $breadcrumb_pages = array("Gestão de RH" => "../index.php", "Folha de Pagamento" => "folha.php");
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="iso-8859-1">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">

            <title>:: Intranet :: Visualizar Folha de Pagamento</title>
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
            <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
            <link href="../../resources/css/add-ons.min.css" rel="stylesheet">
            <link rel="stylesheet" href="../../jquery/thickbox/thickbox.css" type="text/css" media="screen" />
        </head>
        <body>
            <?php include("../../template/navbar_default.php"); ?>
            <div class="container">
                <form name="form" action="" method="post" id="form1" class="form-horizontal">
                <div class="row">
                    <div class="col-md-12">
                        <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Visualizar Folha de Pagamento (<?= $folha ?>)</small></h2></div>
                    </div>
                </div>
                <!--
                <table cellspacing="4" cellpadding="0" id="topo">
                    <tr height="30">
                        <td  style="font-size:12px;">
                                           
                        </td>
                        <td colspan="2">  <b>CNPJ: </b><?php echo $row_empresa['cnpj']; ?></td>
                    </tr>

                    <tr>
                        <td width="35%"><b>Data da Folha:</b> <?= $row_folha['data_inicio_br'] . ' &agrave; ' . $row_folha['data_fim_br'] ?></td>
                        <td width="30%"><b>Região:</b> <?= $regiao . ' - ' . mysql_result($qr_regiao, 0, 1) ?></td>
                        <td width="20%"><b>Participantes:</b> <?= $total_participantes ?></td>
                    </tr>
                    <tr>
                        <td><b>Data de Processamento:</b> <?= $row_folha['data_proc_br'] ?></td>
                        <td><b>Gerado por:</b> <?= @abreviacao(mysql_result($qr_usuario, 0), 2) ?></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td><b>Total de rescindidos:</b> <?= $row_folha['total_rescindidos'] ?></td>
                    </tr>
                </table>
                -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row padding-xs-vr">
                            <div class="col-xs-8 text-left">
                                <strong><?= mysql_result($qr_projeto, 0, 1) . ' (' . $mes_folha . ')' ?></strong>
                            </div>
                            <div class="col-xs-4 text-right">
                                <strong>CNPJ: </strong><?= $row_empresa['cnpj']; ?>
                            </div>
                        </div>
                        <div class="row padding-xs-vr">
                            <div class="col-xs-4 text-left">
                                <strong>Região: </strong><?= $regiao . ' - ' . mysql_result($qr_regiao, 0, 1) ?>
                            </div>
                            <div class="col-xs-4 text-center">
                                <strong>Data da Folha: </strong><?= $row_folha['data_inicio_br'] . ' &agrave; ' . $row_folha['data_fim_br'] ?>
                            </div>
                            <div class="col-xs-4 text-right">
                                <strong>Participantes: </strong><?= $total_participantes ?>
                            </div>
                        </div>
                        <div class="row padding-xs-vr">
                            <div class="col-xs-4 text-left">
                                <strong>Data de Processamento: </strong><?= $row_folha['data_proc_br'] ?>
                            </div>
                            <div class="col-xs-4 text-center">
                                <strong>Gerado por: </strong><?= @abreviacao(mysql_result($qr_usuario, 0), 2) ?>
                            </div>
                            <div class="col-xs-4 text-right">
                                <strong>Total de rescindidos: </strong><?= $row_folha['total_rescindidos'] ?>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-2 legenda" data-key="mostrar_todos">
                            <div class="tr-bg-active pointer"><span class="btn-label bg-dark-gray fa fa-file bordered"></span>Todos</div>
                        </div>
                        <div class="col-md-2 legenda" data-key="entrada">
                            <div class="tr-bg-active pointer "><span class="btn-label bg-warning fa fa-file bordered"></span>Admissão</div>
                        </div>
                        <div class="col-md-2 legenda" data-key="evento">
                            <div class="tr-bg-active pointer"><span class="btn-label bg-success fa fa-file bordered"></span>Licen&ccedil;a</div>
                        </div>
                        <div class="col-md-2 legenda" data-key="faltas">
                            <div class="tr-bg-active pointer"><span class="btn-label bg-danger fa fa-file bordered"></span>Faltas</div>
                        </div>
                        <div class="col-md-2 legenda" data-key="ferias">
                            <div class="tr-bg-active pointer"><span class="btn-label bg-info fa fa-file bordered"></span>F&eacute;rias</div>
                        </div>
                        <div class="col-md-2 legenda" data-key="rescisao">
                            <div class="tr-bg-active pointer"><span class="btn-label bg-pa-purple fa fa-file bordered"></span>Rescis&atilde;o</div>
                        </div>
                        <hr>
                        <table id="tabela" class="table table-bordered table-condensed table-hover text-sm essatb">
                            <thead>
                                <tr class="valign-middle bg-primary">
                                    <th class="text-center">COD</th>
                                    <th>NOME</th>
                                    <th class="text-center"><?= (isset($decimo_terceiro)) ? 'MESES' : 'DIAS'; ?></th>
                                    <th class="text-center">BASE</th>
                                    <th class="text-center">RENDIMENTOS</th>
                                    <th class="text-center">DESCONTOS</th>
                                    <th class="text-center">INSS</th>
                                    <th class="text-center">IRRF</th>
                                    <th class="text-center">FAM&Iacute;LIA</th>
                                    <th class="text-center">L&Iacute;QUIDO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row_participante = mysql_fetch_array($qr_participantes)) {
                                    //////***************FÓRMULA TOP SECRET PARA OCULTAR PARTICIPANTES************************///// 
                                    if ($row_participante['id_clt'] == '4213' or $row_participante['id_clt'] == '4425')
                                        continue;

                                    //totalizador da BASE INSS 13 de rescisao  
                                    $total_base_13_rescisao += $row_participante['base_inss_13_rescisao']; ?>

                                    <tr class="destaque valign-middle">
                                        <td class="text-center" width="70"><?= $row_participante['id_clt'] ?></td>
                                        <td width="350">

                                            <?php
                                            $contracheque = str_replace('+', '--', encrypt("$regiao&$row_participante[id_clt]&$folha"));
                                            $data_entrada = @mysql_result(mysql_query("SELECT data_entrada FROM rh_clt WHERE id_clt = '" . $row_participante['id_clt'] . "'"), 0);
                                            $licensas = array('20', '30', '50', '51', '52', '80', '90', '100', '110');
                                            $ferias = array('40');
                                            $rescisao = array('60', '61', '62', '63', '64', '65', '81', '101');
                                            $faltas = mysql_num_rows(mysql_query("
                                                      SELECT * FROM rh_movimentos_clt WHERE id_mov IN(62,232)  AND id_clt = $row_participante[id_clt]
                                                      AND id_movimento IN(" . $row_folha[ids_movimentos_estatisticas] . ") 
                                                      "));
                                            if(in_array($row_participante['status_clt'],$rescisao)){
                                                $qr_rescisao = mysql_query("SELECT * FROM rh_recisao WHERE id_clt = '$row_participante[id_clt]' AND status = 1");
                                                $row_resc = mysql_fetch_assoc($qr_rescisao);

                                                $pagina = (substr($row_resc['data_proc'],0,10) >= '2013-04-04')? 'nova_rescisao_2.php':'nova_rescisao.php';
                                                $link_folha = '../recisao/'.$pagina.'?enc='.str_replace('+', '--', encrypt("$regiao&$row_participante[id_clt]&$row_resc[id_recisao]"));                       
                                            } else {                    
                                                $link_folha='../contracheque/geracontra_4.php?enc='.$contracheque;
                                            } ?>

                                            <a  href="<?php echo $link_folha;?>"  target="_blank" class="participante" title="Gerar contracheque de <?= $row_participante['nome'] ?>">
                                                <span class="
                                                <?php
                                                if ($data_entrada > $data_inicio) {
                                                    echo 'entrada text-warning';
                                                    //CALCULA TOTAL PARA ADMISSÃO
                                                    $array_totais["entrada"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                                                    $array_totais["entrada"]["rendimento"] += $row_participante['rend'];
                                                    $array_totais["entrada"]["desconto"] += $row_participante['desco'];
                                                    $array_totais["entrada"]["inss"] += $row_participante['a5020'] + $row_participante['a5035'] + $row_participante['inss_dt'] + $row_participante['inss_rescisao'];
                                                    $array_totais["entrada"]["irrf"] += $row_participante['a5021'] + $row_participante['a5036'] + $row_participante['ir_dt'] + $row_participante['ir_rescisao'];
                                                    $array_totais["entrada"]["familia"] += $row_participante['a5022'];
                                                    $array_totais["entrada"]["liquido"] += $row_participante['salliquido'];

                                                } elseif (in_array($row_participante['status_clt'], $licensas)) {
                                                    echo 'evento text-success';

                                                    //CALCULA O TOTAL LICENÇA 
                                                    $array_totais["evento"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                                                    $array_totais["evento"]["rendimento"] += $row_participante['rend'];
                                                    $array_totais["evento"]["desconto"] += $row_participante['desco'];
                                                    $array_totais["evento"]["inss"] += $row_participante['a5020'] + $row_participante['a5035'] + $row_participante['inss_dt'] + $row_participante['inss_rescisao'];
                                                    $array_totais["evento"]["irrf"] += $row_participante['a5021'] + $row_participante['a5036'] + $row_participante['ir_dt'] + $row_participante['ir_rescisao'];
                                                    $array_totais["evento"]["familia"] += $row_participante['a5022'];
                                                    $array_totais["evento"]["liquido"] += $row_participante['salliquido'];

                                                } elseif (in_array($row_participante['status_clt'], $ferias) and $row_participante['dias_trab'] != 30) {
                                                    echo 'ferias text-info';

                                                    //CALCULA O TOTAL FÉRIASa
                                                    $array_totais["ferias"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                                                    $array_totais["ferias"]["rendimento"] += $row_participante['rend'];
                                                    $array_totais["ferias"]["desconto"] += $row_participante['desco'];
                                                    $array_totais["ferias"]["inss"] += $row_participante['a5020'] + $row_participante['a5035'] + $row_participante['inss_dt'] + $row_participante['inss_rescisao'];
                                                    $array_totais["ferias"]["irrf"] += $row_participante['a5021'] + $row_participante['a5036'] + $row_participante['ir_dt'] + $row_participante['ir_rescisao'];
                                                    $array_totais["ferias"]["familia"] += $row_participante['a5022'];
                                                    $array_totais["ferias"]["liquido"] += $row_participante['salliquido'];

                                                } elseif (in_array($row_participante['status_clt'], $rescisao)) {
                                                    echo 'rescisao text-pa-purple darker';

                                                    //CALCULA O TOTAL RECISAO
                                                    $array_totais["rescisao"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                                                    $array_totais["rescisao"]["rendimento"] += $row_participante['rend'];
                                                    $array_totais["rescisao"]["desconto"] += $row_participante['desco'];
                                                    $array_totais["rescisao"]["inss"] += $row_participante['a5020'] + $row_participante['a5035'] + $row_participante['inss_dt'] + $row_participante['inss_rescisao'];
                                                    $array_totais["rescisao"]["irrf"] += $row_participante['a5021'] + $row_participante['a5036'] + $row_participante['ir_dt'] + $row_participante['ir_rescisao'];
                                                    $array_totais["rescisao"]["familia"] += $row_participante['a5022'];
                                                    $array_totais["rescisao"]["liquido"] += $row_participante['salliquido'];

                                                } elseif (!empty($faltas)) {
                                                    echo 'faltas text-danger';

                                                    //CALCULA O TOTAL FALTAS
                                                    $array_totais["faltas"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                                                    $array_totais["faltas"]["rendimento"] += $row_participante['rend'];
                                                    $array_totais["faltas"]["desconto"] += $row_participante['desco'];
                                                    $array_totais["faltas"]["inss"] += $row_participante['a5020'] + $row_participante['a5035'] + $row_participante['inss_dt'] + $row_participante['inss_rescisao'];
                                                    $array_totais["faltas"]["irrf"] += $row_participante['a5021'] + $row_participante['a5036'] + $row_participante['ir_dt'] + $row_participante['ir_rescisao'];
                                                    $array_totais["faltas"]["familia"] += $row_participante['a5022'];
                                                    $array_totais["faltas"]["liquido"] += $row_participante['salliquido'];

                                                } else {
                                                    echo 'normal text-dark-gray';
                                                }
                                                ?>
                                                "><?= abreviacao($row_participante['nome'], 4, 1); ?></span>
                                            </a>

                                            <?php if($_COOKIE['logado'] == 87 || $_COOKIE['logado'] == 255){
                                                echo 'BASE INSS: '.$row_participante['base_inss'].'<br>';
                                                echo 'BASE 13: '.$row_participante['base_inss_13_rescisao'].'<br>';
                                            } ?>
                                        </td>
                                        <td class="text-center" width="20"><?= ($row_participante['valor_dt'] != '0.00') ? $row_participante['meses'] : $row_participante['dias_trab']; ?></td>
                                        <td class="text-right"><?= formato_real($row_participante['sallimpo_real'] + $row_participante['valor_dt']) ?></td>
                                        <td class="text-right"><?= formato_real($row_participante['rend']) ?></td>
                                        <td class="text-right"><?= formato_real($row_participante['desco']) ?></td>
                                        <td class="text-right"><?= formato_real($row_participante['a5020'] + $row_participante['a5035'] + $row_participante['inss_dt'] + $row_participante['inss_rescisao']) ?></td>
                                        <td class="text-right"><?= formato_real($row_participante['a5021'] + $row_participante['a5036'] + $row_participante['ir_dt'] + $row_participante['ir_rescisao']) ?></td>
                                        <td class="text-right"><?= formato_real($row_participante['a5022']) ?></td>
                                        <td class="text-right"><?= formato_real($row_participante['salliquido']) ?></td>
                                    </tr>
                                    <?php
                                    $totalizador_salario_maternidade += $row_participante['a6005'];
                                    $ddir += $row_participante['a5049'];
                                } // Fim do Loop de Participantes 
                                ?>
                                <tr class="totais">
                                    <td colspan="2"></td>
                                    <td class="text-bold">TOTAIS:</td>
                                    <td class="text-right text-bold"><?= formato_real($row_folha['total_limpo'] + $row_folha['valor_dt']) ?></td>
                                    <td class="text-right text-bold"><?= formato_real($row_folha['rendi_indivi']) ?></td>
                                    <td class="text-right text-bold"><?= formato_real($row_folha['descon_indivi']) ?></td>
                                    <td class="text-right text-bold"><?= formato_real($row_folha['total_inss'] + $row_folha['inss_dt'] + $row_folha['inss_rescisao'] + $row_folha['inss_ferias']) ?></td>
                                    <td class="text-right text-bold"><?= formato_real($row_folha['total_irrf'] + $row_folha['ir_dt'] + $row_folha['ir_rescisao'] + $row_folha['ir_ferias']) ?></td>
                                    <td class="text-right text-bold"><?= formato_real($row_folha['total_familia']) ?></td>
                                    <td class="text-right text-bold"><?= formato_real($row_folha['total_liqui']) ?></td>
                                </tr>
                                <?php foreach ($array_totais as $key => $total){ ?>
                                    <tr class="totais_<?php echo $key; ?> esconde_geral" style="display: none;">
                                        <td colspan="2"></td>
                                        <td class="text-bold">TOTAIS:</td>
                                        <!-- ********************** TOTAIS DE ENTRADAS ***************************** -->
                                        <td class="text-right text-bold"><?= formato_real($total["base"]) ?></td>
                                        <td class="text-right text-bold"><?= formato_real($total["rendimento"]) ?></td>
                                        <td class="text-right text-bold"><?= formato_real($total["desconto"]) ?></td>
                                        <td class="text-right text-bold"><?= formato_real($total["inss"]) ?></td>
                                        <td class="text-right text-bold"><?= formato_real($total["irrf"]) ?></td>
                                        <td class="text-right text-bold"><?= formato_real($total["familia"]) ?></td>
                                        <td class="text-right text-bold"><?= formato_real($total["liquido"]) ?></td>
                                    </tr>
                                <?php  }  ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer">
                        <div class="col-xs-6 text-left no-padding-l">
                            <a href="../../rh/folha/ver_folha_analitica.php?enc=<?= $_REQUEST['enc'] ?>" class="btn btn-info">Folha Analitica</a>
                        </div>
                        <div class="col-xs-6 text-right no-padding-r">
                            <a href="../../rh/folha/ver_folha_analitica_1.php?enc=<?= $_REQUEST['enc'] ?>" class="btn btn-warning">Folha Analitica Detalhada</a>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="panel-footer resumo" id="conteudo_para_esconder">
                        <div class="col-xs-6 no-padding-l">
                            <?php
                            // Resumo por Movimento
                            $movimentos_codigo = array('0001',
                                '5029',
                                '5037', '5037',
                                '4007', '4007',
                                '5020', '5031', '5035', '4007',
                                '5021', '5030', '5036', '4007',
                                '5022', '5019',
                                '7001', '8003',
                                '6005');

                            $movimentos_nome = array('SAL&Aacute;RIO',
                                'D&Eacute;CIMO TERCEIRO SAL&Aacute;RIO',
                                'F&Eacute;RIAS', 'VALOR PAGO NAS F&Eacute;RIAS',
                                'RESCIS&Atilde;O', 'VALOR PAGO NA RESCIS&Atilde;O',
                                'INSS', 'INSS SOBRE D&Eacute;CIMO TERCEIRO', 'INSS SOBRE F&Eacute;RIAS', 'INSS SOBRE RESCIS&Atilde;O',
                                'IRRF', 'IRRF SOBRE D&Eacute;CIMO TERCEIRO', 'IRRF SOBRE F&Eacute;RIAS', 'IRRF SOBRE RESCIS&Atilde;O',
                                'SAL&Aacute;RIO FAMILIA', 'CONTRIBUI&Ccedil;&Atilde;O SINDICAL',
                                'DESCONTO VALE TRANSPORTE', 'DESCONTO VALE REFEI&Ccedil;&Atilde;O',
                                'SAL&Aacute;RIO MATERNIDADE');

                            $movimentos_tipo = array('CREDITO',
                                'CREDITO',
                                'CREDITO', 'DEBITO',
                                'CREDITO', 'DEBITO',
                                'DEBITO', 'DEBITO',  'DEBITO', 'DEBITO',
                                'DEBITO', 'DEBITO', 'DEBITO', 'DEBITO',
                                'CREDITO', 'DEBITO',
                                'DEBITO', 'DEBITO',
                                'CREDITO');

                            $movimentos_valor = array($row_folha['total_limpo'],
                                $row_folha['valor_dt'],
                                $row_folha['valor_ferias'], $row_folha['valor_pago_ferias'],
                                $row_folha['valor_rescisao'], $row_folha['valor_pago_rescisao'],
                                $row_folha['total_inss'], $row_folha['inss_dt'], $row_folha['inss_ferias'], $row_folha['inss_rescisao'],
                                $row_folha['total_irrf'], $row_folha['ir_dt'], $row_folha['ir_ferias'], $row_folha['ir_rescisao'],
                                $row_folha['total_familia'], $row_folha['total_sindical'],
                                $row_folha['total_vt'], $row_folha['total_vr'],
                                $totalizador_salario_maternidade);

                            // Adicionando Mais Movimentos
                            if (!empty($row_folha['ids_movimentos_estatisticas'])) {

                                $chave = '18';

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
                            } ?>
                            <div class="panel panel-default">
                                <div class="panel-heading">Resumo por Movimento</div>
                                <div class="panel-body">
                                    <table class="table table-bordered table-condensed text-sm">
                                        <thead>
                                            <tr class="valign-middle bg-primary">
                                                <th>COD</th>
                                                <th>MOVIMENTO</th>
                                                <th></th>
                                                <th>RENDIMENTO</th>
                                                <th>DESCONTO</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $movimentos =  $objFolha->getResumoPorMovimento($folha);
                                            foreach($movimentos as $cod =>$valor){
                                                if($valor['tipo'] == 'CREDITO'){
                                                    $rendimento = $valor['valor'];
                                                    $desconto ='';
                                                    $total_credito += $valor['valor'];
                                                } else {
                                                    $rendimento ='';
                                                    $desconto = $valor['valor'];
                                                    $total_debito += $valor['valor'];
                                                }
                                                if($valor['qnt'] != 0){
                                                    $frequencia = $valor['qnt'];
                                                } elseif(!empty($valor['qnt_horas']) and $valor['qnt_horas'] != '00:00:00'){
                                                    $frequencia = $valor['qnt_horas'];
                                                } else {
                                                    $frequencia = $valor['percentual'];
                                                }
                                                $class =  ($linha++ % 2 == 0)? 'linha_um': 'linha_dois';

                                                echo '<tr class="'.$class.' valign-middle">';
                                                echo '<td class="text-center">'.$cod.'</td>';
                                                echo '<td class="text-left">'.$valor['nome'].'</td>';
                                                echo '<td class="text-center">'.$frequencia.'</td>';
                                                echo '<td class="text-right">'.formato_real($rendimento).'</td>';
                                                echo '<td class="text-right">'.formato_real($desconto).'</td>';
                                                echo '</tr>';
                                           } ?>
                                            <tr class="totais valign-middle">
                                                <td colspan="3" class="text-right text-bold">TOTAIS:</td>
                                                <td class="text-right text-bold"><?= formato_real($total_credito) ?></td>
                                                <td class="text-right text-bold"><?= formato_real($total_debito) ?></td>
                                            </tr>
                                            <tr class="totais valign-middle">
                                                <td colspan="3" class="text-right text-bold">L&Iacute;QUIDO:</td>
                                                <td class="text-right text-bold"><?= formato_real($total_credito - $total_debito) ?></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php
                            if (!empty($ids_movimentos_estatisticas)) {
                                $qr_movimentos_faltas = mysql_query("SELECT cod_movimento, nome_movimento, SUM(valor_movimento) as total ,tipo_movimento, id_mov 
                                                                    FROM `rh_movimentos_clt`
                                                                    WHERE id_movimento IN($ids_movimentos_estatisticas) 
                                                                     AND id_mov  = 62
                                                                    GROUP BY id_mov") or die(mysql_error()); ?>
                            <?php } ?>
                            <div class="panel panel-default">
                                <div class="panel-heading">Total de faltas</div>
                                <div class="panel-body">
                                    <table class="table table-bordered table-condensed text-sm">
                                        <thead>
                                            <tr class="valign-middle bg-primary">
                                                <th>COD</th>
                                                <th>TOTAL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($row_mov2 = mysql_fetch_assoc($qr_movimentos_faltas)){ ?>  
                                                <tr class="valign-middle">
                                                    <td class="text-right"><?=$row_mov2['cod_movimento']; ?></td>
                                                    <td class="text-right"><?=number_format($row_mov2['total'], 2, ',', '.'); ?></td>                
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <p class="text-danger text-sm">*As faltas são abatidas no salário base.</p>
                                </div>
                            </div>
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
                        $row_sem_pis  = mysql_fetch_assoc($qr_sem_pis);

                        $totalizadores_nome = array('L&Iacute;QUIDO', 'SEM DESCONTO DE INSS', 'BASE DE INSS', 'INSS', 'INSS (EMPRESA)', 'INSS (RAT)', 'INSS (TERCEIROS)'/* , 'INSS (RECOLHER)' */, 'BASE DE IRRF', 'IRRF', 'DDIR', 'BASE DE FGTS', 'BASE DE FGTS DE F&Eacute;RIAS',
                            'BASE DE FGTS TOTAL',  'FGTS', 'BASE INSS 13º <br>CALC. RESCISÃO',/* , 'FGTS' */
                            );
                        $totalizadores_valor = array(
                            $row_folha['total_liqui'],
                            $row_folha['total_sem_desconto_inss'],
                            $row_folha['base_inss'],
                            $row_folha['total_inss'] + $row_folha['inss_dt'] + $row_folha['inss_rescisao'] + $row_folha['inss_ferias'],
                            ($row_folha['base_inss'] * 0.2),
                            ($row_folha['base_inss'] * $percentual_rat),
                            ($row_folha['base_inss'] * 0.058),
                            //	 ((($row_folha['base_inss'] * 0.2) +
                            // ($row_folha['base_inss'] * $percentual_rat) +
                            //($row_folha['base_inss'] * 0.058) +
                            //($row_folha['total_inss'] + $row_folha['inss_dt'] + $row_folha['inss_rescisao'] + $row_folha['inss_ferias'])) - 
                            //$row_folha['total_familia']),
                            $row_folha['base_irrf'],
                            $row_folha['total_irrf'] + $row_folha['ir_dt'] + $row_folha['ir_rescisao'] + $row_folha['ir_ferias'],
                            $ddir,
                            $row_folha['base_fgts'],
                            $row_folha['base_fgts_ferias'],
                            $row_folha['base_fgts'] + $row_folha['base_fgts_ferias'],         
                           ($row_folha['base_fgts'] + $row_folha['base_fgts_ferias'])*0.08,
                            $total_base_13_rescisao,
                                /* $row_folha['total_fgts'] + $row_folha['fgts_dt'] + $row_folha['fgts_rescisao'] + $row_folha['fgts_ferias'] */ 
                        );
                        ?>
                        <div class="col-xs-6 no-padding-r" id="totalizadores">
                            <div class="panel panel-default">
                                <div class="panel-heading">Totalizadores</div>
                                <div class="panel-body">
                                    <table class="table table-bordered table-condensed table-hover text-sm">
                                        <tbody>
                                            <tr class="valign-middle">
                                                <td class="text-left">PARTICIPANTES:</td>
                                                <td class="text-right"><?= $total_participantes ?></td>
                                            </tr>
                                            <?php foreach ($totalizadores_valor as $chave => $valor) { ?>
                                                <tr class="valign-middle">
                                                    <td class="text-left"><?= $totalizadores_nome[$chave] ?>:</td>
                                                    <td class="text-right"><?= formato_real($valor) ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="panel-heading border-t text-bold resumo">Lista de Bancos</div>
                    <div class="panel-body resumo">
                        <table class="table table-bordered table-condensed table-hover table-striped text-sm">
                            <?php
                            // Verificando os bancos envolvidos na folha de pagamento
                            $qr_bancos = mysql_query("SELECT DISTINCT(id_banco) FROM rh_folha_proc WHERE id_banco != '9999' AND id_banco != '0' AND id_folha = '$folha' AND status IN(3,4)");
                            while ($row_bancos = mysql_fetch_array($qr_bancos)) {
                                $numero_banco++;
                                $qr_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row_bancos[id_banco]'");
                                $row_banco = mysql_fetch_array($qr_banco); ?>

                                <tr class="valign-middle">
                                    <td class="text-center" style="width:7%;"><img src="../../imagens/bancos/<?= $row_banco['id_nacional'] ?>.jpg" width="25" height="25"></td>
                                    <td style="width:35%; text-align:left; padding-left:5px;"><?= $row_banco['nome'] ?></td>		  
                                    <?php $total_finalizados = mysql_num_rows(mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha' AND status = '4' AND id_banco = '$row_banco[id_banco]'"));

                                    if (!empty($total_finalizados)) { ?>
                                        <td>&nbsp;</td>
                                        <td><a href="finalizados.php?regiao=<?= $regiao ?>&folha=<?= $folha ?>&projeto=<?= $projeto ?>&banco=<?= $row_banco['id_banco'] ?>">FINALIZADO</a></td>
                                        <td align="center"><?= $total_finalizados ?> Participantes</td>
                                    <?php } else {

                                        $qr_banco = mysql_query("SELECT * FROM rh_folha_proc folha INNER JOIN tipopg tipo ON folha.tipo_pg = tipo.id_tipopg WHERE folha.id_banco = '$row_bancos[0]' AND folha.id_folha = '$folha' AND folha.status = '3' AND tipo.tipopg = 'Depósito em Conta Corrente' AND tipo.id_regiao = '$regiao' AND tipo.id_projeto = '$projeto'");
                                        $total_banco = mysql_num_rows($qr_banco); ?>
                                        <td style="width:30%; text-align:center;">
                                            <form id="form1" name="form1" method="post" action="folha_banco.php?enc=<?= str_replace('+', '--', encrypt("$regiao&$folha")) ?>">
                                                <select name="banco" class="form-control input-sm">
                                                    <?php $qr_bancos_associados = mysql_query("SELECT * FROM bancos WHERE id_nacional = '$row_banco[id_nacional]' AND status_reg = '1' AND id_regiao != ''");
                                                    while ($row_banco_associado = mysql_fetch_assoc($qr_bancos_associados)) { ?>
                                                        <option value="<?= $row_banco_associado['id_banco'] ?>" <?= ($row_banco_associado['id_banco'] == $row_banco['id_banco']) ? 'selected' : ''; ?>>
                                                            <?=$row_banco_associado['id_banco'] . ' - ' . $row_banco_associado['nome'] . ' (' . @mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_banco_associado[id_regiao]'"), 0) . ')'; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                                <label id="data_pagamento<?= $numero_banco ?>" style="display:none;"> 
                                                    <input name="data" id="data[]" type="text" size="10" onKeyUp="mascara_data(this)" maxlength="10">
                                                    <input name="enviar" id="enviar[]" type="submit" value="Gerar">
                                                </label>
                                                <input type="hidden" name="banco_participante" value="<?= $row_banco['id_banco'] ?>">
                                            </form>
                                        </td>
                                        <td class="text-center" style="width:8%;"><a style="cursor:pointer;"><img src="imagens/ver_banc.png" border="0" alt="Visualizar Funcionários por Banco" onClick="document.all.data_pagamento<?= $numero_banco ?>.style.display = (document.all.data_pagamento<?= $numero_banco ?>.style.display == 'none') ? '' : 'none' ;"></a></td>
                                        <td style="width:20%; text-align:center; padding-right:5px;"><?= $total_banco ?> Participantes</td>
                                    <?php } ?>
                                </tr>
                            <?php } 
                            $qr_cheque = mysql_query("SELECT * FROM rh_folha_proc folha INNER JOIN tipopg tipo ON folha.tipo_pg = tipo.id_tipopg WHERE folha.id_folha = '$folha' AND folha.status = '3' AND tipo.tipopg = 'Cheque' AND tipo.id_regiao = '$regiao' AND tipo.id_projeto = '$projeto' AND tipo.campo1 = '2'");
                            $total_cheque = mysql_num_rows($qr_cheque);
                            $linkcheque = str_replace('+', '--', encrypt("$regiao&$folha&$row_TIpoCheq[0]&$row_TipoDepo[0]")); ?>
                            <tr class="valign-middle">
                                <td class="text-center" style="width:7%;"><img src="../../imagens/bancos/cheque.jpg" width="25" height="25" border="0"></td>
                                <td style="width:35%; text-align:left; padding-left:5px;">Cheque</td>
                                <td style="width:30%;">&nbsp;</td>
                                <td class="text-center" style="width:8%;"><a href="ver_cheque.php?enc=<?= $linkcheque ?>"><img src="imagens/ver_banc.png" border="0" alt="Visualizar Funcionários por Cheque"></a></td>
                                <td style="width:20%; text-align:center; padding-right:5px;"><?= $total_cheque ?> Participantes</td>
                            </tr>
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
                                    <a href="<?= $link_rescisao ?>" style="font-weight:bold; padding-left:5px;">Ver Relatório De Rescisão</a> |
                                    <?php if(in_array($_COOKIE['logado'], $arrayDeveloper)) { ?>
                                        <a href="<?= $link_totalizadorObj ?>" style="font-weight:bold; padding-left:5px;">Novo Totalizador</a> |
                                    <?php } ?>
                                    <a href="<?= $link_rel_sindical ?>" style="font-weight:bold; padding-left:5px;">Relatório Sindical</a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <p style="text-align: right;"><input type="button" onclick="tableToExcel('tabela', 'Folha Analitica')" value="Exportar para Excel" class="exportarExcel"></p>
                <?php include_once '../../template/footer.php'; ?>
            </div>
            <script src="../../js/jquery-1.10.2.min.js"></script>
            <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
            <script src="../../resources/js/bootstrap.min.js"></script>
            <script src="../../resources/js/bootstrap-dialog.min.js"></script>
            <script src="../../js/jquery.validationEngine-2.6.js"></script>
            <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
            <script src="../../js/jquery.mask.min.js" type="text/javascript"></script>
            <script src="../../resources/js/main.js"></script>
            <script src="../../js/global.js"></script>
            <script src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.js" type="text/javascript"></script>
            <script src="../../uploadfy/scripts/swfobject.js" type="text/javascript"></script>
            <script src="../../jquery/priceFormat.js" type="text/javascript"></script>
            <script language='javascript'>
                function mascara_data(d){  
                    var mydata = '';  
                    data = d.value;  
                    mydata = mydata + data;  
                    if (mydata.length == 2){  
                        mydata = mydata + '/';  
                        d.value = mydata;  
                    }  
                    if (mydata.length == 5){  
                        mydata = mydata + '/';  
                        d.value = mydata;  
                    }  
                    if (mydata.length == 10){  
                        verifica_data(d);  
                    }  
                } 

                function verifica_data (d) {  

                    dia = (d.value.substring(0,2));  
                    mes = (d.value.substring(3,5));  
                    ano = (d.value.substring(6,10));  


                    situacao = "";  
                    // verifica o dia valido para cada mes  
                    if ((dia < 01)||(dia < 01 || dia > 30) && (  mes == 04 || mes == 06 || mes == 09 || mes == 11 ) || dia > 31) {  
                        situacao = "falsa";  
                    }  

                    // verifica se o mes e valido  
                    if (mes < 01 || mes > 12 ) {  
                        situacao = "falsa";  
                    }  

                    // verifica se e ano bissexto  
                    if (mes == 2 && ( dia < 01 || dia > 29 || ( dia > 28 && (parseInt(ano / 4) != ano / 4)))) {  
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

                $(function(){

                    
                    $(".legenda").click(function(){
                        $('.destaque, .totais, .resumo').show();
                        $('.esconde_geral').hide();
                        
                        var classe = $(this).data('key');
                        if(classe == 'mostrar_todos'){ return false; }
                        $('.resumo').hide();
                        $('.totais').hide();
                        $(".destaque").each(function(){
                            $(this).find("span").not("."+classe).parents("tr").hide();
                        });
                        $('.totais_'+classe).show();
                    });
                });
            </script>
        </body>
    </html>
    <?php // se é folha antiga...
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
    $link = decrypt($enc);

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
                                if ($row_clt['id_clt'] == '4213' or $row_clt['id_clt'] == '4425')
                                    continue;



                                //DEFINIE QUE O FUNCIONÁRIO IRÁ RECEBER EM CHEQUE CASO ELE NÃO TENHA UM NUMERO DE CONTA, AGÊNCIA OU TIPO DE CONTA DEFINIDO.
                                $resultTipoConta = mysql_query("SELECT tipo_conta FROM rh_clt WHERE id_clt = $row_clt[id_clt]");
                                $rowP = mysql_fetch_array($resultTipoConta);

                                $tiposDePagamentos = mysql_query("SELECT * FROM tipopg WHERE id_regiao = '$regiao' and campo1 = '2' and id_projeto = '$row_projeto[0]'");
                                $rowTipoPg = mysql_fetch_array($tiposDePagamentos);
                                $pgEmCheque = $rowTipoPg[0];

                                if (($row_clt['conta'] == '') or ($row_clt['conta'] == '0')) {
                                    mysql_query("UPDATE rh_folha_proc SET tipo_pg = '$pgEmCheque' WHERE id_folha = '$folha' and id_clt = $row_clt[id_clt]");
                                }
                                if (($row_clt['agencia'] == '') or ($row_clt['agencia'] == '0')) {
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
                                            <td align="right">Base de INSS:</td>
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

                        $RE_Bancs = mysql_query("SELECT id_banco FROM rh_folha_proc WHERE id_banco != '9999' AND id_folha = '$folha' and id_banco != '0' and 
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

                    </td>
                </tr>
            </table>
            <p>&nbsp;</p>
        </body>
    </html>

<?php } ?>
            