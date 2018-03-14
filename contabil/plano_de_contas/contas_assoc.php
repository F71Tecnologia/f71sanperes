<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../funcoes.php");
include("../../wfunction.php");
include("../../classes_permissoes/acoes.class.php");

$usuario = carregaUsuario();
$objAcao = new Acoes();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$sqlProjetosRegiao = "SELECT id_projeto FROM projeto WHERE id_regiao = {$usuario['id_regiao']}";
$qryProjetosRegiao = mysql_query($sqlProjetosRegiao);
$inProjetos[] = 0;
while ($rowProjetosRegiao = mysql_fetch_assoc($qryProjetosRegiao)) {
    $inProjetos[] = $rowProjetosRegiao['id_projeto'];
}

if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'associarBanco' && $_REQUEST['tipoBco'] > 0) {
    foreach ($_REQUEST['classificacao'] as $key => $value) {
        $insert[] = "('', {$value}, {$_REQUEST['tipoBco']}, NOW(), {$usuario['id_funcionario']}, 1)";
    }
    mysql_query("INSERT INTO contabil_contas_assoc_banco VALUES " . implode(', ', $insert));
}

//if( !empty($_REQUEST['action']) && $_REQUEST['action'] == 'associarFolha' && $_REQUEST['tipoFolha'] > 0 ) {
//    foreach ($_REQUEST['classificacao'] as $key => $value) {
//        $insert[] = "('', {$value}, {$_REQUEST['tipoFolha']}, NOW(), {$usuario['id_funcionario']}, 1)";
//    } 
//    mysql_query("INSERT INTO contabil_contas_assoc_folha VALUES ". implode(', ', $insert));
//}

if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'associar' && $_REQUEST['tipo'] > 0) {
    foreach ($_REQUEST['classificacao'] as $key => $value) {
        $verifica = mysql_num_rows(mysql_query("SELECT id_assoc FROM contabil_contas_assoc WHERE id_conta = $value AND id_entradasaida = {$_REQUEST['tipo']} AND status = 1;"));
        if (!$verifica) {
            $insert[] = "('', {$value}, {$_REQUEST['tipo']}, NOW(), {$usuario['id_funcionario']}, 1)";
        }
    }
    mysql_query("INSERT INTO contabil_contas_assoc VALUES " . implode(', ', $insert));
}

if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'deletar') {
    if ($_REQUEST['tipo'] == 'banco') {
        $auxTabela = "_banco";
    } else if ($_REQUEST['tipo'] == 'folha') {
        $auxTabela = "_folha";
    }
    $sql = "UPDATE contabil_contas_assoc$auxTabela SET status = 0, data = NOW(), id_funcionario = {$usuario['id_funcionario']} WHERE id_assoc = {$_REQUEST['id_assoc']} LIMIT 1";
    mysql_query($sql)or die(mysql_error());
    exit;
}

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Contabilidade", "id_form" => "form1", "ativo" => "Associação de Contas");
$breadcrumb_pages = array("Plano de Contas" => "index.php");

$sqlTipos = "SELECT id_entradasaida, nome, grupo FROM entradaesaida WHERE grupo >= 5 ORDER BY grupo, nome";
$qryTipos = mysql_query($sqlTipos)or die(mysql_error());
$optTipos[0] = "SELECIONE O TIPO";
while ($rowTipos = mysql_fetch_assoc($qryTipos)) {
    $optTipos[$rowTipos['id_entradasaida']] = $rowTipos['nome'] . ' ( ' . $rowTipos['id_entradasaida'] . ' )';
}

$sqlBanco = "SELECT * FROM bancos WHERE id_regiao = {$usuario['id_regiao']} AND status_reg = 1 AND id_projeto IN(" . implode(', ', $inProjetos) . ")";
$qryBanco = mysql_query($sqlBanco)or die(mysql_error());
$optBancos[0] = "SELECIONE O BANCO";
while ($rowBancos = mysql_fetch_assoc($qryBanco)) {
    $optBancos[$rowBancos['id_banco']] = $rowBancos['nome'] . ' ( ' . $rowBancos['conta'] . ' ) - ' . $rowBancos['razao'];
}

echo $sqlPlanoContas = "SELECT A.id_conta, A.tipo, A.classificador, A.descricao, A.sped, B.nome 
                    FROM contabil_planodecontas A
                    LEFT JOIN projeto B ON(B.id_projeto = A.id_projeto)
                    WHERE A.id_projeto IN(" . implode(', ', $inProjetos) . ") AND A.status = 1 ORDER BY A.classificador";
$qryPlanoContas = mysql_query($sqlPlanoContas)or die(mysql_error());
//$optPlanoContas = "<option value=''>SELECIONE</option>";
while ($rowPlanoContas = mysql_fetch_assoc($qryPlanoContas)) {
    $c = explode('.', $rowPlanoContas['classificador']);
    
    $style = ($rowPlanoContas['sped'] == 1) ? ' disabled style="" ' : ' style="color: #00F;" ';
    //$optPlanoContas .= "<option value='{$rowPlanoContas['id_conta']}' $style >{$rowPlanoContas['classificador']} - {$rowPlanoContas['descricao']}</option>";
    if (strlen($c[0]) == 1 && strlen($c[1]) == 1) {
        echo 'aqui 1: ';
        echo "$c[0] - $c[1]<br>";
        $legenda[$c[0]] = "{$rowPlanoContas['classificador']} - {$rowPlanoContas['descricao']}";
       
    } else { 
        echo 'aqui 2:';
        echo "$c[0] - $c[1]<br>";
        $arrayPlanoContas[$c[0]] .= "<option value='{$rowPlanoContas['id_conta']}' $style >{$rowPlanoContas['classificador']} - {$rowPlanoContas['descricao']} {$rowPlanoContas['nome']} </option>";
    }
}

foreach ($arrayPlanoContas AS $key => $value) {
    $multiSelClassificadores .= '
    <div class="col-xs-6">
        <label class="control-label">' . $legenda[$key] . '</label>
        <div class=""><select name="classificacao[]" id="classificacao' . $key . '" class="h-300 form-control" multiple="">' . $value . '</select></div>
    </div>';
}

if ($_REQUEST['show'] == 'tipo') {

    $sqlContasAssociadas = "
        SELECT A.id_assoc, A.id_entradasaida, C.nome, B.classificador, B.descricao, D.nome AS projeto 
        FROM contabil_contas_assoc A
        INNER JOIN contabil_planodecontas B ON(A.id_conta = B.id_conta AND B.id_projeto IN(" . implode(', ', $inProjetos) . "))
        INNER JOIN entradaesaida C ON(A.id_entradasaida = C.id_entradasaida)
        LEFT JOIN projeto D ON(D.id_projeto = B.id_projeto)
        WHERE A.status = 1";
    $qryContasAssociadas = mysql_query($sqlContasAssociadas) or die(mysql_error());
    $numContasAssociadas = mysql_num_rows($qryContasAssociadas);
    while ($rowContasAssociadas = mysql_fetch_assoc($qryContasAssociadas)) {
        ?>
        <tr>
            <td><?= $rowContasAssociadas['id_entradasaida'] . ' - ' . utf8_encode($rowContasAssociadas['nome']) ?></td>
            <td><?= $rowContasAssociadas['classificador'] . ' - ' . utf8_encode($rowContasAssociadas['descricao']) . ' (' . utf8_encode($rowContasAssociadas['projeto']) . ')' ?></td>
        <?php if ($objAcao->verifica_permissoes(108)) { ?><td class="text-center" width="10px"><button type="button" class="btn btn-danger btn-sm deletar" data-tipo="tipo" data-assoc="<?= $rowContasAssociadas['id_assoc'] ?>"><i class="fa fa-trash-o"></i></button></td><?php } ?>
        </tr>
    <?php
    }
    exit;
}

if ($_REQUEST['show'] == 'banco') {

    $sqlBancosAssociados = "
        SELECT A.id_conta, A.id_banco, C.nome, C.razao, C.conta, B.classificador, B.descricao, D.nome AS projeto
        FROM contabil_contas_assoc_banco A
        INNER JOIN contabil_planodecontas B ON(A.id_conta = B.id_conta AND B.id_projeto IN(" . implode(', ', $inProjetos) . "))
        INNER JOIN bancos C ON(A.id_banco = C.id_banco)
        LEFT JOIN projeto D ON(D.id_projeto = B.id_projeto)
        WHERE A.status = 1";
    $qryBancosAssociados = mysql_query($sqlBancosAssociados) or die(mysql_error());
    $numBancosAssociados = mysql_num_rows($qryBancosAssociados);
    while ($rowBancosAssociados = mysql_fetch_assoc($qryBancosAssociados)) {
        ?>
        <tr>
            <td><?= $rowBancosAssociados['id_banco'] . ' - ' . utf8_encode($rowBancosAssociados['nome']) . ' - ' . $rowBancosAssociados['conta'] ?></td>
            <td><?= $rowBancosAssociados['classificador'] . ' - ' . utf8_encode($rowBancosAssociados['descricao']) . ' (' . utf8_encode($rowBancosAssociados['projeto']) . ')' ?></td>
        <?php if ($objAcao->verifica_permissoes(108)) { ?><td class="text-center" width="10px"><button type="button" class="btn btn-danger btn-sm deletar" data-tipo="banco" data-assoc="<?= $rowBancosAssociados['id_assoc'] ?>"><i class="fa fa-trash-o"></i></button></td><?php } ?>
        </tr>
    <?php
    }
    exit;
}

if ($_REQUEST['show'] == 'folha') {

    $sqlFolhaAssociadas = "
        SELECT A.id_assoc, A.id_cod, B.classificador, B.descricao
        FROM contabil_contas_assoc_folha A
        INNER JOIN contabil_planodecontas B ON(A.id_conta = B.id_conta AND B.id_projeto IN(" . implode(', ', $inProjetos) . "))
        WHERE A.status = 1";
    $qryFolhaAssociadas = mysql_query($sqlFolhaAssociadas) or die(mysql_error());
    $numFolhaAssociadas = mysql_num_rows($qryFolhaAssociadas);
    while ($rowFolhaAssociadas = mysql_fetch_assoc($qryFolhaAssociadas)) {
        ?>
        <tr>
            <td>//<?= $rowFolhaAssociadas['id_cod'] . ' - ' . utf8_encode($optFolha[$rowFolhaAssociadas['id_cod']]) ?></td>
            <td>//<?= $rowFolhaAssociadas['classificador'] . ' - ' . utf8_encode($rowFolhaAssociadas['descricao']) ?></td>
        <?php if ($objAcao->verifica_permissoes(108)) { ?><td class="text-center" width="10px"><button type="button" class="btn btn-danger btn-sm deletar" data-tipo="folha" data-assoc="<?= $rowFolhaAssociadas['id_assoc'] ?>"><i class="fa fa-trash-o"></i></button></td><?php } ?>
        </tr>
    <?php
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Associação de Contas</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-contabil-header"><h2><span class="glyphicon glyphicon-usd"></span> - CONTABILIDADE<small> - Associação de Contas</small></h2></div>
                    <ul class="nav nav-tabs nav-justified" style="margin-bottom: 20px;">
                        <li class="active" ><a class="contabil" href="#despesa_receita" data-toggle="tab">Despesas / Receitas </a></li>
                        <!--<li><a class="contabil" href="#folha" data-toggle="tab">Folha de Pagamento</a></li>-->
                        <li><a class="contabil" href="#banco" data-toggle="tab">Bancos</a></li>
                    </ul>
                    <div class="tab-content">   

                        <div class="tab-pane active" id="despesa_receita">
                            <form action="" method="post" class="form-horizontal" id="form_despesareceita">
                                <div class="panel panel-default">
                                    <div class="panel-heading"></div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <div class="col-xs-7">
                                                <label class="control-label">Tipos </label>
                                                <div class="text-center"><?= montaSelect($optTipos, $value, 'name="tipo" id="tipo" class="form-control validate[required]"') ?></div>
                                            </div>
                                        </div>
                                        <label class="control-label">Classificadores </label>
                                        <hr>
                                        <div class="form-group">                                
<?= $multiSelClassificadores ?>
                                        </div>
                                    </div>
                                    <div class="panel-footer text-right">
                                        <input type="hidden" class="hid" name="action" value="associar" />
                                        <button class="btn btn-sm btn-primary"><i class="fa fa-chain"></i> Associar</button>
                                    </div>
                                    <div class="panel-heading border-t">
                                        Despesas e Receitas - Lista de Associações
                                    </div>
                                    <div class="panel-footer">
                                        <table class="table table-bordered table-condensed table-hover text-sm valign-middle">
                                            <thead>
                                                <tr>
                                                    <th>TIPO</th>
                                                    <th>CONTA</th>
<?php if ($objAcao->verifica_permissoes(108)) { ?><th class="text-center"><i class="fa fa-trash-o text-danger"></i></th><?php } ?>
                                                </tr>
                                            </thead>
                                            <tbody id="body_tipo"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!--                        <div class="tab-pane" id="folha">
                                                    <form action="" method="post" class="form-horizontal" id="form_folha">
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading"></div>
                                                            <div class="panel-body">
                                                                <div class="form-group">
                                                                    <div class="col-xs-6">
                                                                        <label class="control-label">Lista de Movimentos da Folha:</label>
                                                                        <div class="text-center"><?= montaSelect($optFolha, $value, 'name="tipoFolha" id="tipoFolha" class="form-control validate[required]"') ?></div>
                                                                    </div>
                                                                </div>
                                                                <label class="control-label">Classificadores</label>
                                                                <hr>
                                                                <div class="form-group">                                
<?= $multiSelClassificadores ?>
                                                                </div>
                                                            </div>
                                                            <div class="panel-footer text-right">
                                                                <input type="hidden" class="hid" name="action" value="associarFolha" />
                                                                <button class="btn btn-sm btn-primary"></i> Associar</button>
                                                            </div>
                                                            <div class="panel-heading border-t">
                                                                Provisão Folha - Lista de Associações
                                                            </div>
                                                            <div class="panel-footer">
                                                                <table class="table table-bordered table-condensed table-hover text-sm valign-middle">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>TIPO</th>
                                                                            <th>CONTA</th>
<?php if ($objAcao->verifica_permissoes(108)) { ?><th class="text-center"><i class="fa fa-trash-o text-danger"></i></th><?php } ?>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="body_folha"></tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>-->

                        <div class="tab-pane" id="banco">
                            <form action="" method="post" class="form-horizontal" id="form_banco">
                                <div class="panel panel-default">
                                    <div class="panel-heading"></div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <div class="col-xs-7">
                                                <label class="control-label">Bancos: </label>
                                                <div class="text-center"><?= montaSelect($optBancos, $value, 'name="tipoBco" id="tipoBco" class="form-control validate[required]"') ?></div>
                                            </div>
                                        </div>
                                        <label class="control-label">Classificadores </label>
                                        <hr>
                                        <div class="form-group">                                
<?= $multiSelClassificadores ?>
                                        </div>
                                    </div>
                                    <div class="panel-footer text-right">
                                        <input type="hidden" class="hid" name="action" value="associarBanco" />
                                        <button class="btn btn-sm btn-primary"><i class="fa fa-chain"></i> Associar</button>
                                    </div>
                                    <div class="panel-heading border-t">
                                        Bancos - Lista de Associações
                                    </div>
                                    <div class="panel-footer">
                                        <table class="table table-bordered table-condensed table-hover text-sm valign-middle">
                                            <thead>
                                                <tr>
                                                    <th>TIPO</th>
                                                    <th>CONTA</th>
<?php if ($objAcao->verifica_permissoes(108)) { ?><th class="text-center"><i class="fa fa-trash-o text-danger"></i></th><?php } ?>
                                                </tr>
                                            </thead>
                                            <tbody id="body_banco"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </form>
                        </div>

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
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.form.js" type="text/javascript"></script>
        <script>
            $(function () {
                $('#form_despesareceita, #form_banco, #form_folha').ajaxForm({
                    success:
                            function (tipo_assoc) {
                                if (tipo_assoc == 'banco') {
                                    recarrega_banco();
                                } else if (tipo_assoc == 'folha') {
                                    recarrega_folha();
                                } else if (tipo_assoc == 'tipo') {
                                    recarrega_tipo();
                                }
                                $(".form-control").val(0);
                            }
                });

                $('body').on('click', '.deletar', function () {
                    var tipo_assoc = $(this).data('tipo');
                    var id_assoc = $(this).data('assoc');
                    bootConfirm('Deseja excluir esta associação?', 'Confirmação de Exclusão',
                            function (data) {
                                if (data == true) {
                                    $.post("", {bugger: Math.random(), action: 'deletar', id_assoc: id_assoc, tipo: tipo_assoc}, function (resultado) {
                                        //console.log(resultado); return false;
                                        if (!resultado) {
                                            if (tipo_assoc == 'banco') {
                                                recarrega_banco();
                                            } else if (tipo_assoc == 'folha') {
                                                recarrega_folha();
                                            } else if (tipo_assoc == 'tipo') {
                                                recarrega_tipo();
                                            }
                                        } else {
                                            console.log(resultado);
                                        }
                                    });
                                }
                            },
                            'danger');
                });

                function recarrega_tipo() {
                    cria_carregando_modal();
                    $.post("", {bugger: Math.random(), show: 'tipo'}, function (resultado) {
                        $('#body_tipo').html(resultado);
                        remove_carregando_modal();
                    });
                }
                ;

                function recarrega_folha() {
                    cria_carregando_modal();
                    $.post("", {bugger: Math.random(), show: 'folha'}, function (resultado) {
                        $('#body_folha').html(resultado);
                        remove_carregando_modal();
                    });
                }
                ;

                function recarrega_banco() {
                    cria_carregando_modal();
                    $.post("", {bugger: Math.random(), show: 'banco'}, function (resultado) {
                        $('#body_banco').html(resultado);
                        remove_carregando_modal();
                    });
                }
                ;

                recarrega_tipo();
//            recarrega_folha();
                recarrega_banco();

            });
        </script>
    </body>
</html>