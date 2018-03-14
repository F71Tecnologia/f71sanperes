<?php
//session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../../login.php?entre=false';</script>";
}

include("../../../conn.php");
include("../../../wfunction.php");
include("../../../classes_permissoes/acoes.class.php");
include("../../../classes/BotoesClass.php");
include("../../../classes/NFSeClass.php");
include("../../../classes/BancoClass.php");
include("../../../classes/ContabilLancamentoClass.php");
include("../../../classes/global.php");

$usuario = carregaUsuario();
$id_regiao = (isset($_REQUEST['id_regiao'])) ? $_REQUEST['id_regiao'] : $usuario['id_regiao'];
$acoes = new Acoes();

$global = new GlobalClass();
$nfse = new NFSe();

$method = isset($_REQUEST['method']) ? $_REQUEST['method'] : NULL;

switch ($method) {
    case "excluir":
    echo ($nfse->cancelar_NFSe($_REQUEST['id'])) ? json_encode(array('status' => 'success', 'msg' => 'NFSe excluida com sucesso!')) : json_encode(array('status' => 'danger', 'msg' => 'Erro ao excluir NFSe!'));
    exit();
        break;
    case 'verifica_saidas':
        $id_nfse = addslashes($_REQUEST['id']);
        $saidas = $nfse->getIdSaidasByIdNfse($id_nfse);
        foreach ($saidas as $key => $lines) {
            foreach ($lines as $col => $row) {
                $saidas[$key][$col] = utf8_encode($row);
}
        }
        echo json_encode($saidas);
        exit();
        break;
    case 'alter_status':
        $id_nfse = $_REQUEST['id'];
        $status = $_REQUEST['status'];
        if (isset($_REQUEST['remove_saidas']) && $_REQUEST['remove_saidas'] == 1) {
            $saidas = $nfse->getIdSaidasByIdNfse($id_nfse);
            $ids = [];
            foreach ($saidas as $lines) {
                $ids[] = $lines['id_saida'];
            }
            $q = "UPDATE saida SET status = 0, id_deletado = {$usuario['id_funcionario']}, data_deletado = NOW() WHERE id_saida  IN(" . implode(',', $ids) . ")";
            mysql_query($q) or die('N�o foi poss�vel exclui as sa�das erro: ' . $q . ' - ' . mysql_error());

            $lan = new ContabilLancamentoClass();
            $lan->excluir_lancamentos_assoc_nfse($id_nfse);

        }

        $r = $nfse->alterStatus($id_nfse, $status);

        if ($r) {
            echo json_encode(['msg' => 'Status alterado com sucesso!', 'status' => 'success']);
        } else {
            echo json_encode(['msg' => 'Erro ao alterar status!', 'status' => 'danger']);
        }
        exit();
        break;

    case 'consultar':
        $data_ini = ConverteData($_REQUEST['data_ini']);
        $data_fim = ConverteData($_REQUEST['data_fim']);

        $cond[] = (!empty($id_regiao))?"a.id_regiao = $id_regiao":'';
        $cond[] = (!empty($_REQUEST['data_ini']) && !empty($_REQUEST['data_fim'])) ? "a.DataEmissao BETWEEN '{$data_ini}' AND '{$data_fim}'" : "";
        $cond[] = (!empty($_REQUEST['projeto']) && $_REQUEST['projeto'] != '-1') ? "a.id_projeto = {$_REQUEST['projeto']}" : "";
        $cond[] = ($_REQUEST['status'] >= 0 ) ? "a.status = {$_REQUEST['status']}" : "";
        $cond[] = ($_REQUEST['prestador'] >= 0 ) ? "REPLACE(REPLACE(REPLACE(c_cnpj, '-', ''), '/', ''), '.', '') = '{$_REQUEST['prestador']}'" : "";
        $cond = array_filter($cond);
        $where = (count($cond) > 0) ? "WHERE " . implode(' AND ', $cond) : '';

        $query = "SELECT a.*, b.nome AS nome_projeto, c.*,a.status AS nfse_status, b.id_projeto AS ExibiidProjeto, k.id AS idArquivo, k.arquivo_pdf AS arquivo_pdf, l.arquivo_pdf AS arquivo_xml, l.id_projeto AS projeto_xml, k.id_projeto AS projeto_pdf,a.status 
                FROM nfse AS a 
                LEFT JOIN 
                        (SELECT *,MAX(id) FROM nfse_anexos 
                                WHERE arquivo_pdf LIKE '%pdf' 
                                GROUP BY id_nfse
                                ORDER BY id ASC) AS k ON k.id_nfse = a.id_nfse 
                LEFT JOIN 
                        (SELECT *,MAX(id) 
                                FROM nfse_anexos 
                                WHERE arquivo_pdf LIKE '%xml' 
                                GROUP BY id_nfse
                                ORDER BY id ASC) AS l ON l.id_nfse = a.id_nfse 
                INNER JOIN projeto AS b ON a.id_projeto = b.id_projeto 
                INNER JOIN prestadorservico AS c ON a.PrestadorServico = c.id_prestador 
                $where";

        $result = mysql_query($query) or die($query . ' - ' . mysql_error());
        $arr_nfse = array();
        while ($row = mysql_fetch_assoc($result)) {
            $arr_nfse[] = $row;
        }
        break;
    default:
        break;
}

//PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE�ALHO (TROCA DE MASTER E DE REGI�ES)
switch ($_REQUEST['mod']) {
    case 'finan':
        $dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
        $breadcrumb_config = array("nivel" => "../../../", "key_btn" => "4", "area" => "Financeiro", "ativo" => "CONSULTAR NOTAS FISCAIS DE SERVI�O", "id_form" => "form1");
        $breadcrumb_pages = array("Principal" => "../../../finan/index.php");
        break;
    case 'contabil':
        $dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
        $breadcrumb_config = array("nivel" => "../../../", "key_btn" => "38", "area" => "Gest�o Cont�bil", "ativo" => "CONSULTAR NOTAS FISCAIS DE SERVI�O", "id_form" => "form1");
        break;
    case 'contratos':
    default:
        $dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
        $breadcrumb_config = array("nivel" => "../../../", "key_btn" => "35", "area" => "Gest�o de Contratos", "ativo" => "CONSULTAR NOTAS FISCAIS DE SERVI�O", "id_form" => "form1");
        break;
}


$query = "SELECT id_projeto,nome FROM projeto WHERE id_regiao = $id_regiao";
$xx = mysql_query($query);
$proj[-1] = "� TODOS �";
while ($row = mysql_fetch_assoc($xx)) {
    $proj[$row['id_projeto']] = $row['id_projeto'] . ' - ' . $row['nome'];
}

$id_projeto = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : '';
$id_prestador = (isset($_REQUEST['prestador'])) ? $_REQUEST['prestador'] : '';
$id_regiao = (isset($_REQUEST['id_regiao'])) ? $_REQUEST['id_regiao'] : $id_regiao;
$data_ini = (isset($_REQUEST['data_ini'])) ? $_REQUEST['data_ini'] : '';
$data_fim = (isset($_REQUEST['data_fim'])) ? $_REQUEST['data_fim'] : '';
$status_nfs = ($_REQUEST['status'] >= 0) ? $_REQUEST['status'] : '';
$projeto = montaSelect($proj, $id_projeto, "id='projeto' name='projeto' class='form-control validate[required,custom[select]]'");

// preencher select dos prestadores subi
$query = "SELECT GROUP_CONCAT(id_projeto) AS lista FROM projeto WHERE id_master = {$usuario['id_master']}";
$lista_projs = mysql_fetch_assoc(mysql_query($query));
$query = "SELECT c_razao AS razao, REPLACE(REPLACE(REPLACE(c_cnpj, '-', ''), '/', ''), '.', '') AS cnpj,IF(encerrado_em > CURDATE(),'Ativo','Inativo') AS vencido #,encerrado_em
            FROM prestadorservico 
            WHERE status = 1 AND id_projeto IN({$lista_projs['lista']})
            GROUP BY REPLACE(REPLACE(REPLACE(c_cnpj, '-', ''), '/', ''), '.', '')
            ORDER BY c_razao";
$result = mysql_query($query);
$prestadores['-1'] = "� TODOS �";
while ($row = mysql_fetch_array($result)) {
    $prestadores[$row['cnpj']] = mascara_string('##.###.###/####-##', $row['cnpj']) . " - {$row['razao']}";
}

$pestador = montaSelect($prestadores, $id_prestador, "id='prestador' name='prestador' class='form-control validate[required,custom[select]]'");

$opcoes = array(
    -1 => 'Todos',
    1 => 'Com erro de Cadastro',
    2 => 'Liberado Para Cont�bil',
    3 => 'Liberado Para Financeiro',
    4 => 'LIberado Para Pagamento',
    0 => 'Nota Cancelada'
);
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: CONSULTAR NOTAS FISCAIS DE SERVI�O</title>
        <link rel="shortcut icon" href="../../../favicon.png">
        <!-- Bootstrap -->        
        <link href="../../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-compras.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <style>
            table.nfse{
                width: 100%;
                border-collapse: collapse;
                border: 1px solid #000;
            }
            table.nfse td, table.nfse th{
                border: 1px solid #000;
                padding: 3px;
            }

            tr.warning td{
                background-color: #ffff99;
            }
            tr.danger td{
                background-color: #ff9999;
            }

            tr.correcao td {
                background-color:  #ffe0b3;
            }

        </style>
    </head>
    <body>
        <?php include("../../../template/navbar_default.php"); ?> 
        <div class="container"> 
            <div class="row">
                <div class="col-lg-12">
                    <?php
                    switch ($_REQUEST['mod']) {
                        case 'finan':
                            ?>
                            <div class="page-header box-financeiro-header">
                                <h2><span class="glyphicon glyphicon-usd"></span> - Financeiro <small>- CONSULTAR NOTAS FISCAIS DE SERVI�O</small></h2>
                            </div>
                            <?php
                            break;
                        case 'contabil':
                            ?>
                            <div class="page-header box-contabil-header">
                                <h2><span class="fa fa-bar-chart"></span> - Contabilidade <small>- CONSULTAR NOTAS FISCAIS DE SERVI�O</small></h2>
                            </div>
                            <?php
                            break;
                        case 'contratos':
                        default:
                            ?>
                            <div class="page-header box-compras-header">
                                <h2><span class="glyphicon glyphicon-shopping-cart"></span> - Gest�o de Contratos <small>- CONSULTAR NOTAS FISCAIS DE SERVI�O</small></h2>
                            </div>
                            <?php
                            break;
                    }
                    ?>
                    <!--resposta de algum metodo realizado-->
                    <?php echo $global->getResposta($_SESSION['MESSAGE_TYPE'], $_SESSION['MESSAGE']); ?>                                
                    <!--<div role="tabpanel">-->
                    <form class="form-horizontal" method="post" name="Formulario" id="Formulario">
                        <div class="panel panel-default">
                            <div class="panel-heading">Consulta</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label for="regiao" class="col-lg-2 control-label">Regi�o</label>
                                    <div class="col-lg-6">
                                        <?php echo montaSelect($global->carregaRegioes($usuario['id_master'], ["" => "� TODOS �"]), $id_regiao, "id='id_regiao' name='id_regiao' class='validate[required,custom[select]] form-control' data-for='projeto1'"); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="projeto" class="col-lg-2 control-label">Projeto</label>
                                    <div class="col-lg-6">
                                        <?php echo $projeto; ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="prestador" class="col-lg-2 control-label">Prestador</label>
                                    <div class="col-lg-6">
                                        <?php echo $pestador; ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="data_ini" class="col-sm-2 control-label">Intervalo</label>
                                    <div class="col-sm-6">
                                        <div class="input-group">
                                            <div class="input-group-addon">De</div>
                                            <input type="text" class="form-control" id="data_ini" name="data_ini" placeholder="" value="<?= $data_ini ?>">
                                            <div class="input-group-addon">At�</div>
                                            <input type="text" class="form-control" id="data_fim" name="data_fim" placeholder="" value="<?= $data_fim ?>">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="data_ini" class="col-sm-2 control-label">Status da Nota</label>
                                    <div class="col-sm-6">
                                        <?= montaSelect($opcoes, $status_nfs, 'name="status" id="status" class="form-control"') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <button type="submit" class="btn btn-primary" name="method" value="consultar"><i class="fa fa-search"></i> Consultar</button>
                            </div>
                        </div>
                        <?php
                        if ($_REQUEST['method'] == 'consultar') {
                            if (count($arr_nfse)) {
                                ?>
                                <div class="row">
                                    <?php
                                    $cor = array(1 => ' #ffe0b3', 2 => "#d9edf7", 3 => "#fcf8e3", 4 => "#dff0d8", 0 => "#f2dede");
//                                    $cor = array(1 => '#fbe164', 2 => "#51c6ea", 3 => "#ffab5e", 4 => "#43d967", 0 => "#f47f7f");
                                    foreach ($opcoes as $key => $value) {
                                        if ($key >= 0) {
                                            ?>
                                            <div class="col-md-3"><span style="border:1px solid #ccc; background-color: <?= $cor[$key] ?>">&emsp;</span> <?= $value ?></div>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                                <br>
                                <div class="panel panel-default">
                                    <div class="table-responsive">
                                        <table class="table table-condensed text-sm">
                                            <thead>
                                                <tr>
                                                    <th>Numero</th>
                                                    <th>Cod. Verificador</th>
                                                    <th>Data</th>
                                                    <th>Projeto</th>
                                                    <th>Prestador</th>
                                                    <th style="width: 155px;">CNPJ</th>
                                                    <th colspan="5"><?php //var_dump($arr_nfse);                                 ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($arr_nfse as $values) { ?>
                                                    <?php $class = array(1 => 'correcao', 2 => "info", 3 => "warning", 4 => "success", 0 => "danger") ?>
                                                    <?php // $class = array(1 => 'bg-yellow-light', 2 => "bg-info-light", 3 => "bg-warning-light", 4 => "bg-success-light", 0 => "bg-danger-light") ?>
                                                    <tr class="<?= $class[$values['nfse_status']] ?>">
                                                        <td><?= $values['Numero'] ?></td>
                                                        <td><?= $values['CodigoVerificacao'] ?></td>
                                                        <td><?= ConverteData($values['DataEmissao'], 'd/m/Y') ?></td>
                                                        <td><?= $values['nome_projeto'] ?></td>
                                                        <td><?= $values['c_razao'] ?></td>
                                                        <td><?= $values['c_cnpj'] ?></td>
                                                        <td>
                                                            <?php if (!empty($values['arquivo_pdf'])) { ?>
                                                                <a href="../nfse_anexos/<?= $values['projeto_pdf'] ?>/<?= $values['arquivo_pdf'] ?>" target="_blank" class="btn btn-default btn-xs" id="ver<?= $values['idArquivo'] ?>"><i class="fa fa-file-pdf-o text-danger" ></i> PDF</a>
                                                            <?php } ?>
                                                        </td>
                                                        <!--INICIO EXCLUIR ARQUIVO merge-->
                                                        <td>

                                                            <?php if (!empty($values['arquivo_pdf'])) { ?>
                                                                <button type="button" target="_blank" class="btn btn-default btn-xs ExcluirArquivo" data-idarquivo="<?= $values['idArquivo'] ?>" data-idprojeto="<?= $values['projeto_pdf'] ?>" data-nomearquivo="<?= $values['arquivo_pdf'] ?>" id="exclui<?= $values['idArquivo'] ?>"> 
                                                                    <!--<span class="fa-stack fa-xs">-->
                                                                    <i class="fa fa-file-pdf-o text-danger"></i>
                                                                    Excluir PDF
                                                                </button>
                                                            <?php } ?>
                                                        </td>
                                                        <!--FIM EXCLUIR ARQUIVO-->

                                                        <td>
                                                            <?php if (!empty($values['arquivo_xml'])) { ?>
                                                                <a href="../nfse_anexos/<?= $values['projeto_xml'] ?>/<?= $values['arquivo_xml'] ?>" target="_blank" class="btn btn-default btn-xs"><i class="fa fa-file-code-o text-info"></i> XML</a>
                                                            <?php } ?>
                                                        </td>

                                                        <td>
                                                            <?php $dis144 = $acoes->verifica_permissoes(144) && $values['status'] != 0 ? '' : 'disabled'; ?>
                                                            <div class="btn-group">
                                                                <button type="button" <?= $dis144 ?> class="btn btn-xs btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Alterar Status">
                                                                    <i class="fa fa-refresh"></i> <span class="caret"></span>
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                            <?php
                                                                    $tags = [2 => 'info', 3 => 'warning', 4 => 'success'];
                                                                    foreach ($tags as $k => $t) {
                                                                        if ($k == $values['status']) {
                                                                            break;
                                                            }
                                                            ?>
                                                                        <li><a href="javascript:void(0)" class="alter_status" data-id="<?= $values['id_nfse'] ?>" data-status_new="<?= $k ?>" data-status_old="<?= $values['status'] ?>"><i class="fa fa-circle text-<?= $t ?>"></i> <?= $opcoes[$k] ?></a></li>
                                                                    <?php } ?>
                                                                </ul>
                                                            </div>
                                                        </td>

                                                        <td>
                                                            <?php $dis113 = (in_array($values['nfse_status'], array(1, 2)) && $acoes->verifica_permissoes(113)) ? '' : 'disabled'; ?>
                                                            <a class="btn btn-success btn-xs <?= $dis113 ?>" href="../form_nfse.php?id_edit=<?= $values['id_nfse'] ?>" target="_blanck" title="Editar"><i class="fa fa-pencil-square-o"></i> </a>
                                                        </td>

                                                        <td>
                                                            <?php $dis114 = ($acoes->verifica_permissoes(114)) ? '' : 'disabled'; ?>
                                                            <button type="button" class="btn btn-info btn-xs <?= $dis114 ?> btn_detalhes" data-id="<?= $values['id_nfse'] ?>" title="Detalhes"><i class="fa fa-search"></i></button>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            if ($values['nfse_status'] == 2 && $acoes->verifica_permissoes(115)) {
                                                                $dis115 = '';
                                                            } else {
                                                                $dis115 = 'disabled';
                                                            }
                                                            ?>
                                                            <button type="button" class="btn btn-danger btn-xs btn_excluir" data-id="<?= $values['id_nfse'] ?>" <?= $dis115 ?> title="Cancelar Nota"><i class="fa fa-ban"></i></button>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="alert alert-danger">
                                    <i class="fa fa-info-circle fa-lg"></i> N�o h� Notas fiscais com o para esta consulta.
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </form>
                </div>
            </div> 
            <?php include_once '../../../template/footer.php'; ?>
        </div>
        <script src="../../../js/jquery-1.10.2.min.js"></script>
        <script src="../../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../../resources/js/bootstrap.min.js"></script>
        <script src="../../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../../resources/js/main.js"></script>
        <script src="../../../js/global.js"></script>
        <script src="../../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../../resources/js/financeiro/saida.js"></script>
        <script src="../../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../../js/jquery.form.js" type="text/javascript"></script>
        <script src="../../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="../js/relatorio_nfse.js" type="text/javascript"></script>
        <script src="JqueryNfse.js" type="text/javascript"></script>

        <?php if ($acoes->verifica_permissoes(113)) { // apenas se houver permiss�o para editar que a mensagem de notas com erro de cadastro aparecer�o         ?>
            <script src="../js/verifica_nfse_correcao.js" type="text/javascript"></script>
        <?php } ?>
        <style>
            .legends{
                font-size: 0.9em;
                color: silver;
            }
        </style>
    </body>
</html>