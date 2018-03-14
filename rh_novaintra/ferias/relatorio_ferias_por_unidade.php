<?php
/**
 * Arquivo de relatório de férias por unidade 
 *
 * 
 * @file      relatorio_ferias_por_unidade.php
 * @license   Copyright - F71
 * @link      http://www.f71lagos.com/intranet/rh_novaintra/ferias/relatorio_ferias_por_unidade.php
 * @copyright 2016 F71
 * @author    Não definido
 * @package   
 * @access    public    
 * 
 * @version: 3.0.0000I - 13/09/2016 - Jacques - Adicionado a discriminação do valor dos proventos de férias
 * @version: 3.0.8191I - 15/09/2016 - Jacques - Adicionado processo de exportação via post
 * @version: 3.0.9445I - 20/10/2016 - Jacques - Incluído o uso do framework para inclusão de itens de férias e discriminação no relatório
 * @version: 3.0.9835I - 26/10/2016 - Jacques - Adição do campo de CPF, separação do campo id_clt do nome e uso dos valores de abono de rh_ferias ao invês de rh_ferias_itens
 * @version: 3.0.0129I - 28/11/2016 - Jacques - Existe um erro lógico na condição de busca por data feita. Primeiro porque a busca deve ser por competência, e segundo
 *                                              porque porque ao se fazer comparação com mes e ano de forma isolada de duas duas data_ini e data_fim gera inconsistência 
 * @version: 3.0.0227I - 03/12/2016 - Jacques - Adicionado filtro para listar apenas clt com adiantamento de décimo terceiro
 * @version: 3.0.0228I - 03/12/2016 - Jacques - Adicionado restrição de período por data_movimento com diferença negativa e positiva de um mes no periodo
 * @version: 3.0.0228I - 04/01/2017 - Jacques - Adicionado campo de valor da insalubridade para o relatório
 * 
 * @todo 
 * Existe um erro lógico na condição abaixo. Não sei quem as criou, mas encontrei dois problemas nessa condição
 * WHERE (MONTH(A.data_ini) = 12 OR MONTH(A.data_fim) = 12) AND (YEAR(A.data_ini) = 2016 OR YEAR(A.data_fim) = 2016) AND A.status = 1 AND A.id_clt != 0
 * 
 *     id_ferias    data_ini     data_fim
 * Ex:      4927  2015-12-14   2016-01-13
 * 
 */

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../funcoes.php");
include("../../wfunction.php");
include("../../classes_permissoes/acoes.class.php");
include("../../classes/OrcamentoClass.php");
//if(!include_once(ROOT_CLASS.'RhClass.php')) die ('Não foi possível incluir '.ROOT_CLASS.'RhClass.php a partir de '.__FILE__); 

//if($_COOKIE['debug']) { print_array($_REQUEST); exit; }
//
//$rh = new RhClass();

        
$acoes = new Acoes();
$usuario = carregaUsuario();
$objOrcamento = new OrcamentoClass();
$container_full = true;
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

/**
 * RECUPERAÇÃO DOS VALORES SELECIONADOS
 */
$id_projeto = ($_REQUEST['id_projeto']) ? $_REQUEST['id_projeto'] : null;

$ini = explode('/',$_REQUEST['data_ini']);
$fim = explode('/',$_REQUEST['data_fim']);

$data_ini = "{$ini[2]}-{$ini[1]}-{$ini[0]}";
$data_fim = "{$fim[2]}-{$fim[1]}-{$fim[0]}";
        

$chk_apenas_com_13 = $_REQUEST['chk_apenas_com_13'];


/**
 * Exportação para o Excel via post
 */
if (!empty($_REQUEST['export_data'])) {
//    header("Content-type: text/html; charset=utf-8");
    $dados = $_REQUEST['export_data'];
//    unset($_REQUEST['export_data']);
//    print_array($_REQUEST);
    
    ob_end_clean();
    header("Content-Encoding: UTF-8");
    header("Pragma: private");
    header("Cache-control: private, must-revalidate");
    header("Expires: 0");    
//    header("Content-type: application/vnd.ms-excel");
    header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename={$_REQUEST['export_file_name']}");
    
 
//    echo "\xEF\xBB\xBF";    
//    echo "<html>";
//    echo "  <head>";
//    echo "      <title>RELATÓRIO DE FÉRIAS POR UNIDADE</title>";
//    echo "  </head>";
//    echo "  <body>";
    echo $dados;
//    echo "  </body>";
//    echo "</html>";
    exit;
    
}

/**
 * MONTA SELECT DOS PROJETOS
 */
$sqlProjetos = mysql_query("SELECT id_projeto, nome FROM projeto WHERE id_master = {$usuario['id_master']} ORDER BY nome") or die("ERRO AO SELECIONAR O PROJETO:" . mysql_error());
$arrayProjetos = array("" => "-- TODOS --");
while ($rowProjetos = mysql_fetch_assoc($sqlProjetos)) {
    $arrayProjetos[$rowProjetos['id_projeto']] = $rowProjetos['id_projeto'] . " - " . $rowProjetos['nome'];
}

/**
 * MONTA SELECT DAS UNIDADES
 */

$auxProjeto = ($_REQUEST['id_projeto']) ? "WHERE campo1 = {$_REQUEST['id_projeto']}" : null;
$sqlUnidades = mysql_query("SELECT id_unidade, campo1, unidade FROM unidade $auxProjeto ORDER BY unidade");
$arrayUnidades = array("" => "-- TODAS --");
while ($rowUnidades = mysql_fetch_assoc($sqlUnidades)) {
    $arrayUnidadesProjeto[$rowUnidades['campo1']][] = $rowUnidades['id_unidade'];
    if($_REQUEST['method'] == 'unidades'){
        $arrayUnidades[$rowUnidades['id_unidade']] = $rowUnidades['id_unidade'] . " - " . utf8_encode($rowUnidades['unidade']);
    } else {
        $arrayUnidades[$rowUnidades['id_unidade']] = $rowUnidades['id_unidade'] . " - " . $rowUnidades['unidade'];
    }
}
if($_REQUEST['method'] == 'unidades'){
    echo montaSelect($arrayUnidades, $_REQUEST['id_unidade'], 'class="form-control" id="id_unidade" name="id_unidade"');
    exit;
}

if(isset($_REQUEST['filtrar'])){
    if($_REQUEST['id_projeto']) { $auxWHERE[] = "A.projeto = {$_REQUEST['id_projeto']}"; }
    if($_REQUEST['id_unidade']) { $auxWHERE[] = "B.id_unidade = {$_REQUEST['id_unidade']}"; }
    
    //if($_REQUEST['mes']) { $auxWHERE[] = "(MONTH(A.data_ini) = {$_REQUEST['mes']} OR MONTH(A.data_fim) = {$_REQUEST['mes']})"; }
    //if($_REQUEST['ano']) { $auxWHERE[] = "(YEAR(A.data_ini) = {$_REQUEST['ano']} OR YEAR(A.data_fim) = {$_REQUEST['ano']})"; }

    if(isset($data_ini) && isset($data_fim)) $auxWHERE[] = " A.data_ini BETWEEN '{$data_ini}' AND '{$data_fim}'"; 
    
    
    $auxWHERE[] = "A.status = 1";
    $auxWHERE[] = "A.id_clt != 0";
    $sql = "
    SELECT 
        D.id_unidade, D.unidade, 
        B.id_clt, 
        B.nome nomeClt, 
        B.cpf,
        C.id_curso, 
        C.nome nomeCurso, 
        C.valor,
        A.id_ferias, 
        A.data_aquisitivo_ini, 
        A.data_aquisitivo_fim, 
        A.data_ini, 
        A.data_fim, 
        A.dias_ferias,
        A.salario,
        A.salario_variavel,
        A.abono_pecuniario,
        A.umterco_abono_pecuniario,
        A.umterco,
        A.total_remuneracoes, 
        A.ir,
        A.inss,
        A.total_descontos,
        A.total_liquido,
        A.pensao_alimenticia,
        IF(
            A.insalubridade_periculosidade > 0 ,
            A.insalubridade_periculosidade,
            (
                SELECT m.valor_movimento
                FROM rh_movimentos_clt m
                WHERE 
                    m.status 
                    AND m.cod_movimento IN ('6006', '6007', '50251', '90080') 
                    AND (
                        m.id_ferias=A.id_ferias
                        OR (
                            m.mes_mov=17 
                            AND m.id_clt=A.id_clt 
                            AND DATE_FORMAT(A.data_ini,'%Y%m') BETWEEN DATE_FORMAT(DATE_SUB(m.data_movimento, INTERVAL 1 MONTH),'%Y%m') AND DATE_FORMAT(DATE_ADD(m.data_movimento, INTERVAL 1 MONTH),'%Y%m')
                            )
                        )
                LIMIT 1
            )
        ) insalubridade, 
        IF(
            A.adiantamento13 > 0, 
            A.adiantamento13,
	    (
                SELECT m.valor_movimento
                FROM rh_movimentos_clt m
                WHERE 
                    m.status 
                    AND m.cod_movimento='80030' 
                    AND (
                        m.id_ferias=A.id_ferias
                        OR (
                            m.mes_mov=17 
                            AND m.id_clt=A.id_clt 
                            AND DATE_FORMAT(A.data_ini,'%Y%m') BETWEEN DATE_FORMAT(DATE_SUB(m.data_movimento, INTERVAL 1 MONTH),'%Y%m') AND DATE_FORMAT(DATE_ADD(m.data_movimento, INTERVAL 1 MONTH),'%Y%m')
                            )
                        )
                LIMIT 1
	    )
	) adiantamento13
    FROM rh_ferias A 
    LEFT JOIN rh_clt B ON (A.id_clt = B.id_clt)
    LEFT JOIN curso C ON (B.id_curso = C.id_curso)
    LEFT JOIN unidade D ON (B.id_unidade = D.id_unidade)
    WHERE " . implode(' AND ', $auxWHERE) . "
    ORDER BY D.unidade, B.nome, A.ano, A.mes";
    
    if($_REQUEST['chk_apenas_com_13']) $sql = "SELECT * FROM ($sql) f WHERE adiantamento13 > 0";
    
    //echo "<pre>{$sql}</pre>";
    
    $qry = mysql_query($sql) or die(mysql_error());
    
    while($row = mysql_fetch_assoc($qry)){
        
        $arrayFerias[$row['unidade']][$row['id_clt']]['nome'] = $row['nomeClt'];
        $arrayFerias[$row['unidade']][$row['id_clt']]['cpf'] = $row['cpf'];
        $arrayFerias[$row['unidade']][$row['id_clt']]['funcao'] = $row['nomeCurso'];
        $arrayFerias[$row['unidade']][$row['id_clt']]['valor'] = $row['valor'];
        
        $arrayFerias[$row['unidade']][$row['id_clt']]['periodos'][$row['id_ferias']] = $row;
        
        $id_ferias[] = $row['id_ferias'];

    }
    
    //print_array($arr_ferias_itens);exit;
    
    
}

//$rh->Legendas->db->setCodePage('latin1');  

//$arr_legendas = $rh->Legendas->setDefault()->select()->db->getCollection('id_legenda');
//
//$arr_ferias_itens = $rh->FeriasItens->setDefault()->setStatus(1)->setIdLegenda('14,15')->setIdFerias(implode($id_ferias,','))->select()->db->getCollection('id_ferias_itens');
//
//$array_label = array();
//
//$array_itens = array();
//
///**
// * Loop para agrupamento de itens de féiras por clt 
// */
//
//foreach ($arr_ferias_itens['dados'] as $id_ferias_itens => $itens_ferias) {
//    
//    if($itens_ferias['valor'] > 0) {
//
//        $array_itens[$itens_ferias['id_clt']][$arr_legendas['dados'][$itens_ferias['id_legenda']]['categoria']][$arr_legendas['dados'][$itens_ferias['id_legenda']]['descricao']] += $itens_ferias['valor'];
//        $array_label[$arr_legendas['dados'][$itens_ferias['id_legenda']]['categoria']][$arr_legendas['dados'][$itens_ferias['id_legenda']]['descricao']] = $arr_legendas['dados'][$itens_ferias['id_legenda']]['descricao'];       
//
//    }
//    
//}
//
///**
// * Loop para contagem do número de colunas de crédito e débito
// */
//
$cols = array('C' => 0, 'D' => 0);
//
////print_array($array_label);
//
//foreach ($array_label as $categoria => $array_labels) {
//    
//    foreach ($array_labels as $label => $value) {
//        
//        $cols[$categoria]++;
//        
//    }
//   
//}

//print_array($cols);

/**
 * PARAMETROS DE CONFIG DA PAGINA
 */
$nome_pagina = "RELATÓRIO DE FÉRIAS POR UNIDADE";
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>$nome_pagina);
$breadcrumb_pages = array("Gestão de RH"=>"/intranet/rh/principalrh.php", "Férias"=>"/intranet/rh_novaintra/ferias");

$borderMes = ' style="border: 2px solid #00F;" '; 
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="http://<?=$_SERVER['SERVER_NAME']?>/intranet/resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="http://<?=$_SERVER['SERVER_NAME']?>/intranet/resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="http://<?=$_SERVER['SERVER_NAME']?>/intranet/resources/css/main.css" rel="stylesheet" media="all">
        <link href="http://<?=$_SERVER['SERVER_NAME']?>/intranet/resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="http://<?=$_SERVER['SERVER_NAME']?>/intranet/resources/dropzone/dropzone.css" rel="stylesheet" media="all">
        <link href="http://<?=$_SERVER['SERVER_NAME']?>/intranet/resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css" media="all">
        
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="<?=($container_full) ? 'container-full' : 'container'?>">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro  - <small><?= $nome_pagina ?></small></h2></div>
                    <?php if($_COOKIE['debug'] == 666 && ((count($arrayGetOrcamento['arraySubGrupos']) + count($arrayGetOrcamento['arrayEntradas'])) > 0)){ ?>
                    <?= $objOrcamento->getAvisos($arrayUnidadesProjeto[$_REQUEST['id_projeto']], $_REQUEST['id_unidade']) ?>
                    <?= $objOrcamento->getAvisosSaldoAcumulado($arrayGetOrcamento['arrayTotalSubGrupos'], $arrayGetOrcamento['arraySaldoAcumuladoDespesaTotal']) ?>
                    <?php } ?>
                    <form action="" method="post" id="form1" class="form-horizontal top-margin1" enctype="multipart/form-data" autocomplete="off">
                        
                    <div class="panel panel-default hidden-print">
                        <div class="panel-body">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <div class="text-bold">Projeto:</div>
                                    <div class="" id="div_projeto">
                                        <?= montaSelect($arrayProjetos, $_REQUEST['id_projeto'], 'class="form-control" id="id_projeto" name="id_projeto"') ?>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="text-bold">Unidade:</div>
                                    <div class="" id="div_unidade">
                                        <?= montaSelect($arrayUnidades, $_REQUEST['id_unidade'], 'class="form-control" id="id_unidade" name="id_unidade"') ?>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    
                                    <div class="text-bold">Data Inicio:</div>
                                    <div id="data_ini" class='input-group date'>
                                        <input type='text' id="data_ini_fmt" name="data_ini" class="form-control span2" readonly="true" value=""/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar">
                                            </span>
                                        </span>
                                    </div>                                    
                                    <span class="add-on"><i class="icon-th"></i></span>
                                    
                                </div>
                                <div class="col-sm-2">
                                    
                                    <div class="text-bold">Data Fim:</div>
                                    <div id="data_fim" class='input-group date'>
                                        <input type='text' id="data_fim_fmt" name="data_fim" class="form-control span2" readonly="true" value=""/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar">
                                            </span>
                                        </span>
                                    </div>                                    
                                    <span class="add-on"><i class="icon-th"></i></span>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <label><input type='checkbox' value='1' id='chk_apenas_com_13' name='chk_apenas_com_13'>&nbsp;Listar Apenas quem recebeu adiantamento de Décimo Terceiro</label></br>
                            <button name="filtrar" class="btn btn-primary"><i class="fa fa-filter"></i> FILTRAR</button>
                        </div>
                    </div>
                    </form>
                    <hr>
                    <?php if(count($arrayFerias) > 0) { ?>
                    <button type="button" id="exportarExcel" class="btn btn-success margin_b10 margin_l10 pull-right hidden-print"><i class="fa fa-file-excel-o"></i> Exportar para Excel</button>&nbsp
                    <button type="button" form="formPdf" name="pdf" data-title="Relatório de Férias por Unidade" data-id="relatorio" id="pdf" value="Gerar PDF" class="btn btn-danger pull-right"><i class="fa fa-file-pdf-o"></i> Gerar PDF</button>
                    <div id="relatorio_div">
                    <table id="relatorio" class="table table-bordered table-hover table-condensed valign-middle text-sm">
                        <tr>
                            <th class="text-center info" rowspan="2">UNIDADE</th>
                            <th class="text-center info" rowspan="2">ID</th>
                            <th class="text-center info" rowspan="2">NOME</th>
                            <th class="text-center info" rowspan="2">CPF</th>
                            <th class="text-center info" rowspan="2">FUN&Ccedil;&Atilde;O</th>
                            <th class="text-center info" rowspan="2">SAL&Aacute;RIO</th>
                            <th class="text-center info" rowspan="2">AQUIS. INI.</th>
                            <th class="text-center info" rowspan="2">AQUIS. FIM.</th>
                            <th class="text-center info" rowspan="2">INICIO</th>
                            <th class="text-center info" rowspan="2">FIM</th>
                            <th class="text-center info" rowspan="2">DIAS</th>
                            <th class="text-center success" rowspan="1" colspan="<?=$cols['C']+8?>">CR&Eacute;DITOS</th>
                            <th class="text-center danger" rowspan="1" colspan="<?=$cols['D']+4?>">D&Eacute;BITOS</th>
                            <th class="text-center info" rowspan="2">L&Iacute;QUIDO</th>
                            <th class="text-center info" rowspan="2">&nbsp;</th>
                        </tr>
                        <tr> <!--Discriminação dos valores de crédito-->
                            <th class="text-center success">FIXO</th>
                            <th class="text-center success">VARI&Aacute;VEL</th>
                            <th class="text-center success">INSALUBRIDADE</th>
                            <th class="text-center success">UM TER&Ccedil;O</th>
                            <th class="text-center success">ABONO PECUNI&Aacute;RIO</th>
                            <th class="text-center success">1/3 ABONO PECUNI&Aacute;RIO</th>
                            <th class="text-center success">ADIANTAMENTO 13&ordm;</th>
                            <?php foreach ($array_label['C'] as $label => $value) { ?>
                            <th class="text-center success"><?=$label?></th>
                            <?php } ?>
                            <th class="text-center success">TOTAL</th>
                            
                            <th class="text-center danger">IR</th>
                            <th class="text-center danger">INSS</th>
                            <th class="text-center danger">PENSÃO ALIMENT&Iacute;CIA</th>
                            <?php foreach ($array_label['D'] as $label => $value) { ?>
                            <th class="text-center danger"><?=$label?></th>
                            <?php } ?>
                            <th class="text-center danger">TOTAL</th>
                        </tr>
                        <?php foreach ($arrayFerias as $unidade => $clts) { ?>
                            <!--tr><td colspan="13" class="active"><?= $unidade ?></td></tr-->
                            <?php foreach ($clts as $id_clt => $clt) { $c = 0; ?>
                                <tr>
                                    <td rowspan="<?= count($clt['periodos']) ?>"><?=$unidade?></td>
                                    <td rowspan="<?= count($clt['periodos']) ?>"><?=$id_clt?></td>
                                    <td rowspan="<?= count($clt['periodos']) ?>"><?=$clt['nome']?></td>
                                    <td rowspan="<?= count($clt['periodos']) ?>"><?=$clt['cpf']?></td>
                                    <td rowspan="<?= count($clt['periodos']) ?>"><?= $clt['funcao'] ?></td>
                                    <td class="text-right" rowspan="<?= count($clt['periodos']) ?>"><?= number_format($clt['valor'],2,',','.') ?></td>
                                    <?php foreach ($clt['periodos'] as $id_ferias => $ferias) { $c++; ?>
                                    <?php if($c > 1) { ?></tr><tr><?php } ?>
                                    <td class="text-center"><?= implode('/', array_reverse(explode('-',$ferias['data_aquisitivo_ini']))) ?></td>
                                    <td class="text-center"><?= implode('/', array_reverse(explode('-',$ferias['data_aquisitivo_fim']))) ?></td>
                                    <td class="text-center"><?= implode('/', array_reverse(explode('-',$ferias['data_ini']))) ?></td>
                                    <td class="text-center"><?= implode('/', array_reverse(explode('-',$ferias['data_fim']))) ?></td>
                                    <td class="text-center"><?= $ferias['dias_ferias'] ?></td>

                                    <!--Total de Créditos-->
                                    <td class="text-right success"><?= number_format(($ferias['salario']/30)*$ferias['dias_ferias'],2,',','.') ?></td>
                                    <td class="text-right success"><?= number_format($ferias['salario_variavel'],2,',','.') ?></td>
                                    <td class="text-right success"><?= number_format($ferias['insalubridade'],2,',','.') ?></td>
                                    <td class="text-right success"><?= number_format($ferias['umterco'],2,',','.') ?></td>
                                    <td class="text-right success"><?= number_format($ferias['abono_pecuniario'],2,',','.') ?></td>
                                    <td class="text-right success"><?= number_format($ferias['umterco_abono_pecuniario'],2,',','.') ?></td>
                                    <td class="text-right success"><?= number_format($ferias['adiantamento13'],2,',','.') ?></td>
                                    <?php foreach ($array_label['C'] as $label => $value) { ?>
                                    <th class="text-right success"><?=number_format($array_itens[$id_clt]['C'][$value],2,',','.')?></th>
                                    <?php } ?>
                                    <td class="text-right success"><?= number_format($ferias['total_remuneracoes'],2,',','.') ?></td>

                                    <!--Total de Débitos-->
                                    <td class="text-right danger"><?= number_format($ferias['ir'],2,',','.') ?></td>
                                    <td class="text-right danger"><?= number_format($ferias['inss'],2,',','.') ?></td>
                                    <td class="text-right danger"><?= number_format($ferias['pensao_alimenticia'],2,',','.') ?></td>
                                    <?php foreach ($array_label['D'] as $label => $value) { ?>
                                    <th class="text-right danger"><?=number_format($array_itens[$id_clt]['D'][$value],2,',','.')?></th>
                                    <?php } ?>
                                    <td class="text-right danger"><?= number_format($ferias['total_descontos'],2,',','.') ?></td>

                                    <td class="text-right"><?= number_format($ferias['total_liquido'],2,',','.') ?></td>
                                    <td class="text-center"><a class="btn btn-xs btn-danger" href="../../?class=ferias/processar&method=gerarPdf&id_ferias=<?= $id_ferias ?>&value=pdf" target="_blank"><i class="fa fa-file-pdf-o"></a></td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>
                            <!--tr><td colspan="13">&nbsp;</td></tr-->
                        <?php } ?>
                        
                    </table>
                    <?php } else { ?>
                        <div class="alert alert-info text-bold">Nenhuma informação encontrada!</div>
                    <?php } ?>
                    </div>    
                </div>
            </div>
            <?php include('../../template/footer.php'); ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script type="text/javascript" src="/intranet/js/bootstrap-datepicker-1.4.0-dist/js/bootstrap-datepicker.js?tag_rev"></script>
        <script type="text/javascript" src="/intranet/js/bootstrap-datepicker-1.4.0-dist/js/bootstrap-datepicker.min.js?tag_rev"></script>
        <script type="text/javascript" src="/intranet/js/bootstrap-datepicker-1.4.0-dist/locales/bootstrap-datepicker.pt-BR.min.js?tag_rev"></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
        $(function(){
            $('body').on('change', '#id_projeto', function(){
                $.post('', {method: 'unidades', id_projeto: $(this).val()}, function(result){
                    $('#div_unidade').html(result);
                });
            });
            
            $('#data_ini,  #data_fim').datepicker({
                startDate: "",
                endDate: "",                            
                today: "Today",
                clear: "Clear",
                titleFormat: "MM yyyy", 
                language: "pt-BR",
                format: "dd/mm/yyyy",
                todayBtn: "linked",
                todayHighlight: true,
                calendarWeeks: false,
                weekStart: 0,
                autoclose: true,
                yearRange: '2005:c+1',
                changeMonth: true,
                changeYear: true              

            });            
            
            $('body').on('click', '#exportarExcel', function(){
                
                var html = $("#relatorio_div").html();
                
                $('th, td, tr').each(function($i,$v){
                    $($v).css('background-color', $($v).css('background-color'));
                    $($v).css('color', $($v).css('color'));
                    $($v).css('font', $($v).css('font'));
                    $($v).css('text-align', $($v).css('text-align'));
                    $($v).css('vertical-align', $($v).css('vertical-align'));
                });
                
                $("#form_x").remove();
                
                $("body").append('<form action="" method="post" id="form_x" enctype="multipart/form-data"></form>');                
                
                $("#form_x").append('<input type="hidden" name="class" value="util/index"/>');                
                $("#form_x").append('<input type="hidden" name="method" value="export" />');                
                $("#form_x").append('<input type="hidden" name="export_content_type" value="application/vnd.ms-excel"/>');                
                $("#form_x").append('<input type="hidden" name="export_data" value="" id="export_data"/>');                
                $("#form_x").append('<input type="hidden" name="export_titulo" value="RELATÓRIO DE FÉRIAS POR UNIDADE" />');                
                $("#form_x").append('<input type="hidden" name="export_file_name" value="relatorio-de-ferias-por-unidade.xls" />');  
                
                $("#export_data").val(html); 
//                console.log($("#export_data").val());
                
                $("#form_x").submit();

            });            

        });
        </script>
    </body>
</html>