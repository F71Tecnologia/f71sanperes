<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}
session_start();
/* include('sintetica/cabecalho_folha.php'); */
include_once("../../conn.php");
include_once("../../funcoes.php");
include_once("../../wfunction.php");
require_once("../../classes/LogClass.php");
include_once('../../classes/MovimentoClass.php');

$usuario = carregaUsuario();
$objMovimento = new Movimentos();

// Id da Folha
$enc = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
$folha = $enc[1];

$qry_projeto = mysql_query("SELECT id_projeto, nome FROM projeto WHERE id_regiao='{$usuario[id_regiao]}' ORDER BY nome");
$arrayProjetos = array('-1' => '-- Selecione --');
while ($dados_projeto = mysql_fetch_assoc($qry_projeto)) {
    $arrayProjetos[$dados_projeto[id_projeto]] = $dados_projeto[nome];
}

/**
 * MOVIMENTOS
 */
$qry_movimentos = mysql_query("SELECT id_mov,cod,descicao,categoria FROM rh_movimentos WHERE mov_lancavel = '1' ORDER BY categoria,descicao;") or die("Erro ao selecionar movimentos: " . mysql_error());
$arrayMovimentos = array((-1) => '-- TODOS OS MOVIMENTOS --');
while ($dados_mov = mysql_fetch_assoc($qry_movimentos)) {
    $cat = (!empty($dados_mov['categoria'])) ? " - " . $dados_mov['categoria'] . " - " : ' - ';
    $arrayMovimentos[$dados_mov['cod']] = $dados_mov['cod'] . $cat . $dados_mov['descicao'];
}

/**
 * FUNÇÕES
 */
$qry_funcao = mysql_query("SELECT * FROM curso AS A WHERE A.id_regiao = '{$usuario[id_regiao]}' AND A.`status` = 1");
$arrayFuncao = array("-1" => '-- TODAS AS FUNÇÕES --');
while ($dados_funcao = mysql_fetch_assoc($qry_funcao)) {
    $arrayFuncao[$dados_funcao['id_curso']] = "{$dados_funcao['nome']} {$dados_funcao['letra']}{$dados_funcao['numero']}";
}

$projeto = (!empty($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$mes = (!empty($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
$ano = (!empty($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$mov = (!empty($_REQUEST['movimento'])) ? $_REQUEST['movimento'] : null;
$funcao = (!empty($_REQUEST['funcao'])) ? $_REQUEST['funcao'] : null;

if (isset($_REQUEST['projeto']) AND ! empty($_REQUEST['projeto'])) {


    // Consulta da Folha
    $qr_folha = "
    SELECT A.*, date_format(A.data_inicio, '%d/%m/%Y') AS data_inicio_br, 
    date_format(A.data_fim, '%d/%m/%Y') AS data_fim_br,
    date_format(A.data_proc, '%d/%m/%Y') AS data_proc_br ,
    B.id_projeto, B.nome nomeProjeto
    FROM rh_folha A LEFT JOIN projeto B ON B.id_projeto = A.projeto
    WHERE A.projeto = {$projeto} AND A.status IN (2,3) AND A.mes = $mes AND A.ano = $ano";
    $qr_folha = mysql_query($qr_folha) or die(mysql_error());
    $num = mysql_num_rows($qr_folha);
    $row_folha = mysql_fetch_assoc($qr_folha);
    // Definindo MÃªs da Folha
    if (!empty($decimo_terceiro)) {
        switch ($tipo_terceiro) {
            case 1: $mes_folha = '13&ordm; Primeira parcela';
                break;
            case 2: $mes_folha = '13&ordm; Segunda parcela';
                break;
            case 3: $mes_folha = '13&ordm; Integral';
                break;
        }
    } else {
        $mes_folha = mesesArray($row_folha[mes]) . " / {$row_folha[ano]}";
    }

    $criterios = "";
    if (isset($_REQUEST['funcao']) && !empty($_REQUEST['funcao']) && $_REQUEST['funcao'] != '-1') {
        $criterios = " AND C.id_curso = '{$_REQUEST['funcao']}' ";
    }

    // select novo para pegar só os movimentos
    $cond_mov = (isset($_REQUEST['movimento']) && $_REQUEST['movimento'] != (-1)) ? " AND cod_movimento = {$_REQUEST['movimento']}" : "";
//    $query_mov_clt = "SELECT B.id_clt, B.nome, A.cod_movimento, A.nome_movimento, A.valor_movimento, A.tipo_movimento, C.nome funcao, A.obs 
//                        FROM rh_movimentos_clt AS A
//                        LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
//                        LEFT JOIN curso AS C ON(C.id_curso = B.id_curso)
//                        WHERE A.id_projeto = '{$projeto}' AND ((A.mes_mov = '{$row_folha['mes']}' AND A.ano_mov = '{$row_folha['ano']}')/* OR A.lancamento IN(1,2)*/) $criterios AND A.status IN (5) AND (B.`status` < 60 OR B.`status` = 200) $cond_mov
//                        ORDER BY B.nome";
    
    
    /*
     * 07/04/2017
     * Solicitante: Dailton
     * By: Max
     * PARA OS CASOS DE CONTRIBUIÇÃO SINDICAL
     * TRAZER MOVIMENTOS DA RESCISÂO TAMBEM
     */
    
    if($_REQUEST['movimento'] == 5019){
        $cond_idmov = "((A.mes_mov = 16 AND A.ano_mov = {$ano} AND MONTH(A.data_movimento) = {$mes} AND A.status > 0 AND A.id_projeto = {$projeto} AND A.cod_movimento = 5019) OR (A.id_movimento IN ({$row_folha['ids_movimentos_estatisticas']}) $cond_mov))";
    }else{
        $cond_idmov = "A.id_movimento IN ({$row_folha['ids_movimentos_estatisticas']}) $cond_mov";
    }
    
    $query_mov_clt = "SELECT B.id_clt, B.cpf, B.nome, A.cod_movimento, A.nome_movimento, A.valor_movimento, A.tipo_movimento, C.nome funcao, A.obs, D.unidade
                        FROM rh_movimentos_clt AS A
                        LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
                        LEFT JOIN curso AS C ON(C.id_curso = B.id_curso)
                        LEFT JOIN unidade AS D ON(B.id_unidade = D.id_unidade)
                        WHERE {$cond_idmov}
                        ORDER BY TRIM(D.unidade), B.nome";


    if ($_COOKIE['logado'] == 179) {
        echo $query_mov_clt;
    }


    $result_mov_clt = mysql_query($query_mov_clt);
    $total_participantes = mysql_num_rows($result_mov_clt);
    while ($row = mysql_fetch_array($result_mov_clt)) {
        $clts[$row['id_clt']]['id_clt'] = $row['id_clt'];
        $clts[$row['id_clt']]['cpf'] = $row['cpf'];
        $clts[$row['id_clt']]['nome'] = $row['nome'];
        $clts[$row['id_clt']]['funcao'] = $row['funcao'];
        $clts[$row['id_clt']]['unidade'] = $row['unidade'];
        $clts[$row['id_clt']][] = $row;
    }
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Folha Sint&eacute;tica de CLT");
$breadcrumb_pages = array("Gestão de RH" => "../principalrh.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Folha Sint&eacute;tica de CLT</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <style>

        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row hidden-print">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Folha Sint&eacute;tica de CLT</small></h2></div>
                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->
            <form action="" id="form1" name="form1" method="post" class="form-horizontal">
                <div class="panel panel-default hidden-print">
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-xs-1 col-xs-offset-1 control-label">Projeto:</label>
                            <div class="col-xs-9">
                                <?= montaSelect($arrayProjetos, $projeto, 'class="form-control" name="projeto"'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Competência:</label>
                            <div class="col-xs-4">
                                <div class="input-group">
                                    <?= montaSelect(mesesArray(), $mes, 'class="form-control" name="mes"'); ?>
                                    <div class="input-group-addon"></div>
                                    <?= montaSelect(anosArray(), $ano, 'class="form-control" name="ano"'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-1 col-xs-offset-1 control-label">Função:</label>
                            <div class="col-xs-9">
                                <?= montaSelect($arrayFuncao, $funcao, 'class="form-control" name="funcao"'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-1 col-xs-offset-1 control-label">Movimento:</label>
                            <div class="col-xs-9">
                                <?= montaSelect($arrayMovimentos, $mov, 'class="form-control" name="movimento"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                    </div>
                </div>
            </form>
            <?php if (isset($_REQUEST[projeto]) AND ! empty($_REQUEST[projeto])) { ?>
                <?php if ($num > 0) { ?>
                    <p class="hidden-print"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Movimentos Folha')" value="Exportar para Excel" class="btn btn-success"></p>    
                    <table class="table table-condensed table-hover table-striped table-bordered" id="tbRelatorio">
                        <td colspan="8" class="no-padding">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-8 text-bold">
                                            <?= "$row_folha[nomeProjeto] ($mes_folha)" ?>
                                        </div>
                                        <div class="col-xs-4">
                                            <b>Data da Folha:</b> <?= $row_folha['data_inicio_br'] . ' &agrave; ' . $row_folha['data_fim_br'] ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-4">
                                            <b>Data de Processamento:</b> <?= $row_folha['data_proc_br'] ?>
                                        </div>
                                        <div class="col-xs-4">
                                            <b>Participantes com Movimento:</b> <?= $total_participantes ?>
                                        </div>
                                        <div class="col-xs-4">
                                            <b>Folha:</b> <?= $row_folha[id_folha] ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>        



                        <tbody>
                            <tr class="">
                                <th class="valign-middle text-center">UNIDADE</th>
                                <th class="valign-middle text-center">CLT</th>
                                <th class="valign-middle text-center">CPF</th>
                                <th class="valign-middle text-center">FUNÇÃO</th>
                                <th class="valign-middle text-center">MOVIMENTO</th>
                                <th class="valign-middle text-center">CATEGORIA</th>
                                <th class="valign-middle text-center">OBS</th>
                                <th class="valign-middle text-center">VALOR</th>
                            </tr>
                            <?php
                            //print_array($clts);
                            foreach ($clts as $linha_clt) {

                                $cpf = $linha_clt['cpf'];
                                $id = $linha_clt['id_clt'];
                                $nome = $linha_clt['nome'];
                                $funcao = $linha_clt['funcao'];
                                $unidade = $linha_clt['unidade'];
                                unset($linha_clt['cpf'], $linha_clt['id_clt'], $linha_clt['nome'], $linha_clt['funcao'], $linha_clt['unidade']);
//                                if ($auxUni != $unidade) {
                                    ?>
<!--                                    <tr class="info">
                                        <td colspan="6" class="valign-middle"><?= $unidade ?></td>
                                    </tr>   -->

                                    <?php $auxUni = $unidade;
//                                } ?>
            <?php if (count($linha_clt) == 1) { ?>
                                    <tr>
                                        <td class="valign-middle"><?= $unidade; ?></td>
                                        <td class="valign-middle"><?= $nome; ?></td>
                                        <td class="valign-middle"><?= $cpf; ?></td>
                                        <td class="valign-middle"><?= $funcao; ?></td>

                                        <td class="valign-middle"><?= $linha_clt[0]['cod_movimento'] . " - " . $linha_clt[0]['nome_movimento']; ?></td>
                                        <td class="valign-middle"><?= $linha_clt[0]['tipo_movimento']; ?></td>
                                        <td class="valign-middle"><?= (!empty($linha_clt[0]['obs'])) ? $linha_clt[0]['obs'] : '-'; ?></td>
                                        <td class="valign-middle">R$ <?= number_format($linha_clt[0]['valor_movimento'], 2, ',', '.'); ?></td>
                                    </tr>
            <?php } else { ?>
                                    <tr>
                                        <td rowspan="<?= count($linha_clt) + 1 ?>" class="valign-middle"><?= $unidade; ?></td>
                                        <td rowspan="<?= count($linha_clt) + 1 ?>" class="valign-middle"><?= $nome; ?></td>
                                        <td rowspan="<?= count($linha_clt) + 1 ?>" class="valign-middle"><?= $cpf; ?></td>
                                        <td rowspan="<?= count($linha_clt) + 1 ?>" class="valign-middle"><?= $funcao; ?></td>
                                    </tr>    
                <?php foreach ($linha_clt as $c) { ?>
                                        <tr>
                                            <td class="valign-middle"><?= $c['cod_movimento'] . " - " . $c['nome_movimento']; ?></td>
                                            <td class="valign-middle"><?= $c['tipo_movimento']; ?></td>
                                            <td class="valign-middle"><?= (!empty($c['obs'])) ? $c['obs'] : '-'; ?></td>
                                            <td class="valign-middle">R$ <?= number_format($c['valor_movimento'], 2, ',', '.'); ?></td>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
        <?php } ?>
                        </tbody>

                        <!--div class="panel-footer"></div-->

                    </table>
                <?php } else { ?>
                    <div class="alert alert-info text-bold">Não Existe nehuma folha para estes filtros!</div>
                <?php } ?>
            <?php } ?>
            <?php //include('sintetica/estatisticas_folha.php');  ?>
<?php include_once '../../template/footer.php'; ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.mask.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../jquery/priceFormat.js" type="text/javascript"></script>
    </body>
</html>