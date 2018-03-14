<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}
if (empty($_REQUEST["projeto"])) {
    header("Location: ver.php");
}

include_once('../conn.php');
include_once('../funcoes.php');
include_once('../wfunction.php');
include_once("../classes/EventoClass.php");
include_once('../classes_permissoes/acoes.class.php');

$usuario = carregaUsuario();

$permissao = new Acoes();
//$permissao = $permissao->getAcoes($_COOKIE['logado'], $_REQUEST['regiao']);

$projeto = $_REQUEST['projeto'];
$regiao = $usuario['id_regiao'];

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'reset_pass') {
    $id_clt = $_REQUEST['id'];
    $query = "DELETE FROM portal_senha WHERE id_clt = {$id_clt} LIMIT 1";
    $arr = mysql_query($query) ? array('status' => true) : array('status' => false);
    echo json_encode($arr);
    exit();
}

// verifica se a regiao é a mesma do projeto, se não volta para o ver.php
$query = "SELECT id_regiao FROM projeto WHERE id_projeto = $projeto;";
$arr_proj = mysql_fetch_assoc(mysql_query($query));
if ($regiao != $arr_proj['id_regiao']) {
    header('location: /intranet/rh/ver.php');
}


//OBJ EVENTO
$data = date("Y-m-d");
$eventos = new Eventos();
$dadosEventos = $eventos->getTerminandoEventos($data, $regiao, $projeto);
$dadosArrayEventos = $eventos->array_dados;
$status = $eventos->getStatus();

// FUNÇÃO NOME
function abreviacao($nome) {
    list($primeiro_nome, $segundo_nome, $terceiro_nome, $quarto_nome, $quinto_nome) = explode(' ', $nome);
    if ($quarto_nome == "DAS" or $quarto_nome == "DA" or $quarto_nome == "DE" or $quarto_nome == "DOS" or $quarto_nome == "DO" or $quarto_nome == "E") {
        $nome_abreviado = "$primeiro_nome $segundo_nome $terceiro_nome $quarto_nome $quinto_nome";
    } else {
        $nome_abreviado = "$primeiro_nome $segundo_nome $terceiro_nome $quarto_nome";
    }
    return $nome_abreviado;
}

if (isset($_REQUEST['pesquisa']) AND ! empty($_REQUEST['pesquisa'])) {
    $valorPesquisa = explode(' ', $_REQUEST['pesquisa']);
    foreach ($valorPesquisa as $valuePesquisa) {
        $pesquisa[] .= "A.nome LIKE '%" . $valuePesquisa . "%'";
    }
    $pesquisa = implode(' AND ', $pesquisa);
    $auxPesquisa = " AND (($pesquisa) OR (CAST(A.matricula AS CHAR) = '{$_REQUEST['pesquisa']}') OR (REPLACE(REPLACE(A.cpf, '.', ''), '-', '') = '{$_REQUEST['pesquisa']}' OR A.cpf = '{$_REQUEST['pesquisa']}'))";

    $query = "SELECT id_unidade FROM rh_clt A WHERE id_projeto = '$projeto' AND (status < '60' OR status = '200') AND status != 0 $auxPesquisa";
    $queryAut = "SELECT id_unidade FROM autonomo A WHERE status = '1' AND id_projeto = '$projeto' $auxPesquisa";

    if ($_POST['classe'] == 'autonomo')
        $query = $queryAut;

    if ($_POST['classe'] == 'desativados')
        $query = "$query UNION $queryAut";

    $sqlPesquisaUnidade = mysql_query($query);
    while ($rowPesquisaUnidade = mysql_fetch_assoc($sqlPesquisaUnidade)) {
        if ($rowPesquisaUnidade['id_unidade'] == '') {
            $pesquisaUnidade[-1] = "''";
        } else {
            $pesquisaUnidade[$rowPesquisaUnidade['id_unidade']] = $rowPesquisaUnidade['id_unidade'];
        }
    }
    $auxPesquisaUnidade = " AND id_unidade IN(" . implode(',', $pesquisaUnidade) . ")";
}

/**
 * QUERY CLT
 */
$sqlAtivos = "
SELECT A.matricula, A.nome AS nomeParticipante, B.nome AS nomeFuncao, DATE_FORMAT(A.data_entrada, '%d/%m/%Y') AS data_entrada, A.cpf, A.id_clt, A.id_regiao, A.id_projeto, A.id_antigo, CONCAT(C.id_unidade, ' - ', C.unidade) unidade
FROM rh_clt A 
LEFT JOIN curso B ON (B.id_curso = A.id_curso)
LEFT JOIN unidade C ON (A.id_unidade = C.id_unidade)
WHERE A.id_projeto = '$projeto' AND (A.status < '60' OR A.status = '200') AND A.status != 0
$auxPesquisa
ORDER BY A.nome ASC";
$qryAtivos = mysql_query($sqlAtivos) or die("ERRO ao selecionar os Participantes: " . mysql_error());
while ($rowAtivos = mysql_fetch_assoc($qryAtivos)) {
    $arrayAtivos[$rowAtivos['unidade']][$rowAtivos['id_clt']] = $rowAtivos;
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel" => "../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Lista Participantes");
$breadcrumb_pages = array("Lista Projetos" => "../rh/ver.php", "Visualizar Projeto" => "../rh/ver.php?projeto=$projeto");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Lista Participantes</title>
        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
        <link href="../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
    </head>
    <body class="overflow-y-scroll">
        <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Lista Participantes</small></h2></div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-12 note">
                        <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 tr-bg-success">&nbsp;<span class="hidden-md hidden-lg">Regularizado com foto</span></div><div class="col-lg-3 col-md-3 hidden-sm hidden-xs">Regularizado com foto</div>
                        <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 tr-bg-warning">&nbsp;<span class="hidden-md hidden-lg">Regularizado</span></div><div class="col-lg-2 col-md-2 hidden-sm hidden-xs">Regularizado</div>
                        <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 tr-bg-danger">&nbsp;<span class="hidden-md hidden-lg">Com Observa&ccedil;&atilde;o / Sem C&oacute;digo / Sem Unidade</span></div><div class="col-lg-4 col-md-4 hidden-sm hidden-xs">Com Observa&ccedil;&atilde;o / Sem C&oacute;digo / Sem Unidade</div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
            <?php if ($_REQUEST['sucesso'] == "edicao") { ?>
                <div class="alert alert-dismissable alert-success">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    Participante atualizado com sucesso!
                </div>
            <?php } ?>
            <div class="row">
                <div class="col-lg-12">
                    <form name="formPesquisa" id="form1" method="post" class="form-horizontal">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <input type="hidden" name="regiao" value="<?= $_REQUEST['regiao'] ?>">
                                <input type="hidden" name="projeto" value="<?= $_REQUEST['projeto'] ?>">
                                <input type="hidden" name="home" id="home" value="">
                                <label class="col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label">Busca:</label>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <input type="text" name="pesquisa" id="pesquisa" class="form-control" placeholder="Nome, Matricula, CPF" value="<?= $_REQUEST['pesquisa'] ?>">
                                </div>
                                <button type="submit" class="col-lg-2 col-md-2 col-sm-2 col-xs-2 btn btn-primary"><i class="fa fa-search"></i> Pesquisar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <ul class="nav nav-tabs margin_b10">
                        <li class="tab active" data-tab="clt"><a href=".clt" data-toggle="tab">Clts</a></li>
                        <li class="tab" data-tab="autonomo"><a href=".autonomo" data-toggle="tab">Autônomos</a></li>
                        <li class="tab" data-tab="cooperado"><a href=".cooperado" data-toggle="tab">Cooperados</a></li>
                        <li class="tab" data-tab="estagiario"><a href=".estagiario" data-toggle="tab">Estagiários</a></li>
                        <li class="tab" data-tab="desativados"><a href=".desativados" data-toggle="tab">Desativados</a></li>
                    </ul>
                </div>
            </div>
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane clt active">
                    <div class="row">
                        <div class="col-lg-12">
                            <?php
                            if (count($arrayAtivos) > 0) {
                                foreach ($arrayAtivos AS $nomeUnidade => $arrayParticipantes) { ?>
                                    <button type="button" onclick="tableToExcel('<?= md5($nomeUnidade) ?>', 'Relatório')" class="btn btn-success pull-right margin_b10"><span class="fa fa-file-excel-o"></span>&nbsp;&nbsp;Exportar para Excel</button>
                                    <table id="<?= md5($nomeUnidade) ?>" class="table table-condensed table-bordered table-hover text-sm">
                                        <thead>
                                            <tr class="bg-primary valign-middle">
                                                <th colspan="8" class=""><i class="fa fa-home"></i>&nbsp; <?= $nomeUnidade ?></th>
                                            </tr>
                                            <tr class="info valign-middle">
                                                <th width="5%" align="center">COD</th>
                                                <th width="30%">NOME</th>
                                                <th width="23%">CARGO</th>
                                                <th width="10%"  align="center">CPF</th>
                                                <th width="7%" align="center">ENTRADA</th>
                                                <?php if ($permissao->verifica_permissoes(126)) { ?>
                                                    <th width="11%" align="center">Resetar Senha Extranet</th>
                                                <?php } ?>
                                            <!--<th width="5%" align="center">PONTO</th>-->
                                            <!--<th width="9%">CURRÍCULOS</th>-->
                                            </tr>
                                        </thead>
                                        <tbody id="cltTbody">
                                            <?php foreach ($arrayParticipantes AS $row_clt) { 
                                                // --------------- VERIFICANDO ASSINATURAS DE CLT ---------------------------------------------------------
                                                $color = "warning";
                                                $textcor = "ok";

                                                if ($row_clt['campo3'] == "INSERIR") {
                                                    $color = "danger";
                                                    $textcor = "!";
                                                }

                                                if ($row_clt['locacao'] == "1 - A CONFIRMAR") {
                                                    $color = "danger";
                                                    $textcor = "!";
                                                }

                                                if ($row_clt['foto'] == "1") {
                                                    $color = "success";
                                                    $textcor = "ok";
                                                }

                                                if (!empty($row_clt['observacao'])) {
                                                    $color = "danger";
                                                    $obs = "title=\"Observações: $row_clt[observacao]\"";
                                                    $textcor = "!";
                                                } ?>

                                                <tr class="<?= $color ?> valign-middle">
                                                    <td align="center"> <?= $row_clt['matricula'] ?></td>
                                                    <td><a class="pointer participante" target="_blank" href="../rh/ver_clt.php?reg=<?= $row_clt['id_regiao'] ?>&clt=<?= $row_clt['id_clt'] ?>&ant=<?= $row_clt['id_antigo'] ?>&pro=<?= $projeto ?>&pagina=bol" <?= $obs ?>> <?= abreviacao($row_clt['nomeParticipante']) ?> </a></td>
                                                    <td><?= str_replace('CAPACITANDO EM', '', $row_clt['nomeFuncao'] . " " . $row_clt['letraCurso'] . $row_clt['numeroCurso']) ?></td>
                                                    <td align="center"><?= $row_clt['cpf'] ?></td>
                                                    <td align="center"><?= $row_clt['data_entrada'] ?></td>
                                                    <?php if ($permissao->verifica_permissoes(126)) { ?>
                                                        <td align="center">
                                                            <button type="button" class="btn btn-danger btn-xs reset_pass" data-id="<?= $row_clt['id_clt'] ?>"><i class="fa fa-key"></i></button>
                                                        </td>
                                                    <?php } ?>
                                                <!--<td align='center'><a href="folha_ponto.php?id=2&unidade=<?= $row_unidades['0'] ?>&regiao=<?= $regiao ?>&pro=<?= $projeto ?>&id_bol=<?= $row_bolsistas['0'] ?>&tipo=clt&caminho=2" class="pointer">Gerar</a></td>-->
                                                <!--<td align="center" ><?= $CLTcurriculo ?></td>-->
                                                </tr>
                                                <?php unset($obs); ?>
                                                <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } ?>
                            <?php } else { ?>
                                <div class="alert alert-info">Nenhum Registro Encontrado!</div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php include_once '../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../resources/dropzone/dropzone.js"></script>
    </body>
</html>