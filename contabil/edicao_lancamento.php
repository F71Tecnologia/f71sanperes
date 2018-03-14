<?php
error_reporting(E_ALL);

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=false';</script>";
}

include_once("../conn.php");
include_once("../wfunction.php");
include_once("../classes/BotoesClass.php");
include_once("../classes/BancoClass.php");
include_once("../classes/global.php");
include_once("../classes/c_classificacaoClass.php");
include_once("../classes/ContabilLancamentoClass.php");
include_once("../classes/ContabilLancamentoItemClass.php");
include_once("../classes/ContabilLoteClass.php");
include_once("../classes_permissoes/acoes.class.php");

$usuario = carregaUsuario();
$global = new GlobalClass();
$objLancamento = new ContabilLancamentoClass();
$objLancamentoItens = new ContabilLancamentoItemClass();

$projetos = getProjetos($usuario['id_regiao']);

//PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$botoes = new BotoesClass("../img_menu_principal/");
$icon = $botoes->iconsModulos;
$breadcrumb_config = array("nivel" => "../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => "Classificação Contábil", "id_form" => "frmplanodeconta");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Classificação Contábil</title>
        <link rel="shortcut icon" href="../favicon.png">
        <!-- Bootstrap -->        
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-compras.css" rel="stylesheet" media="screen">
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include_once("../template/navbar_default.php"); ?> 
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-contabil-header">
                        <h2><?php echo $icon['38'] ?> - Contabilidade <small>- Análise Contábil</small></h2>
                    </div>
                    <?php echo $global->getResposta($_SESSION['MESSAGE_TYPE'], $_SESSION['MESSAGE']); ?>                                

                    <div class="tab-content">                        
                        <div class="tab-pane active" id="conciliacao">
                            <?php
                            $dados = array(
                                'id_projeto' => (!empty($_REQUEST['projeto']) && $_REQUEST['projeto'] != '-1') ? $_REQUEST['projeto'] : '',
                                'mes' => (!empty($_REQUEST['mes'])) ? $_REQUEST['mes'] : '',
                                'ano' => (!empty($_REQUEST['ano'])) ? $_REQUEST['ano'] : '',
                                'status' => '1'
                            );

                            //$lancamento = $objLote->listaLotes($dados, $projetosRegiao);
                            $lancamento = $objLancamento->listaLancamentos($dados);
                            if (count($lancamento) > 0) {
                                ?>
                                <div class="row">
                                    <div class="col-xs-4"><h4><?= $projetos[$_REQUEST['projeto']] ?></h4></div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 text-right">
                                        <button type="button" class="btn btn-success btn-sm add_lancamento"><i class="fa fa-plus"></i> Novo Lançamento</button>
                                        <button type="button" class="btn btn-danger btn-sm add_lancamento"><i class="fa fa-trash"></i> Excluir Lançamentos</button>
                                    </div>
                                </div>
                                <hr>
                                <div class="container-fluid">

                                    <?php foreach ($lancamento as $k_data => $lan) { ?>
                                        <div class="row">
                                            <div class="col-sm-12 col-xs well well-sm">
                                                <h4><a href="#<?= $k_data ?>" class="inf"><?= ConverteData($k_data, 'd/m/Y') ?></a></h4>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div id="<?= $k_data ?>" class="col-xs-12 oculto well">
                                                <?php foreach ($lan as $k_lan => $tipo) { ?>
                                                    <table class="table table-condensed valign-middle text text-sm">
                                                        <tbody>
                                                            <tr class="text text-bold">
                                                                <td class="col-md-2">Lançamento</td>
                                                                <td colspan="5">Histórico</td>
                                                            </tr>
                                                            <tr id="tr<?= $k_lan ?>" class="active text text-default">
                                                                <td><?= $k_lan ?></td>
                                                                <td colspan="2"><?= $tipo['hist_lancamento'] ?></td>
                                                                <td colspan="3" class="lancamento text-right">
                                                                    <button type="button"  class="btn btn-xs btn-info editar_lancamento_item" data-id="<?= $t ?>" title="Editar"><i class="fa fa-pencil"></i></button>
                                                                    <button type="button"  class="btn btn-xs btn-danger exclui_lancamento" data-id="<?= $t ?>" title="Excluir"><i class="fa fa-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                            <tr class="text text-bold">
                                                                <td class="col-md-2">Conta</td>
                                                                <td>Acesso</td>
                                                                <td>Descrição</td>
                                                                <td>Classificação</td>
                                                                <td class="text text-right">Valor R$</td>
                                                                <td class="text text-right">&emsp;</td>
                                                            </tr>
                                                            <?php foreach ($tipo['lan'] as $t => $row) { ?>
                                                                <?php $class = ($row['tipo'] == 1) ? 'text-danger' : 'text-success' ?>
                                                                <tr id="tr<?= $row['id_lancamento'] ?>" class="<?= $class ?>">
                                                                    <td class="text text-left col-md-2"><?= $row['classificador'] ?>&emsp;</td>
                                                                    <td class="lancamento"><?= $row['acesso'] ?></td>
                                                                    <td class="lancamento"><?= $row['descricao'] ?></td>
                                                                    <td class="lancamento"><?= $row['tipo'] == 2 ? 'Devedora' : 'Credora' ?></td>
                                                                    <td class="lancamento text-right">
                                                                        <?= number_format($row['valor'], 2, ',', '.') ?>
                                                                    </td>
                                                                    <td class="text text-right">

                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>

                                </div>
                            <?php } else { ?>
                                <div class="alert alert-warning">NÃO HÁ LANCAMENTOS PARA ESTE FILTRO!</div>
                            <?php } ?>
                        </div>        
                    </div>
                </div>
            </div>
            <?php include_once '../template/footer.php'; ?>
        </div>
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../js/jquery.form.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../resources/js/financeiro/saida.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.form.js" type="text/javascript"></script>
        <script src="../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="js/editar_lancamento.js" type="text/javascript"></script>
    </body>
</html>
