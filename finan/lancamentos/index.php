<?php
//session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=false';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes_permissoes/acoes.class.php");
include("../../classes/BotoesClass.php");
include("../../classes/FinanceiroFechamentoClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];
$acoes = new Acoes();
$global = new GlobalClass();

$objFechamento = new FinanceiroFechamentoClass();

$array_periodos = $objFechamento->ver_periodos($id_regiao);
$titulo = "Finalizar lançamentos por períodos";

$botoes = new BotoesClass("../../img_menu_principal/");
$icon = $botoes->iconsModulos;

//PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
//$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Gestão Financeiro", "ativo" => "Gestão do Financeiro finalizar períodos", "id_form" => "frmplanodeconta");
//$breadcrumb_pages = array("Plano de Contas" => "../index.php");
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Lançamento Financeiro");
$breadcrumb_pages = array("Principal" => "../index.php");


?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $titulo ?></title>
        <link rel="shortcut icon" href="../../favicon.png">
        <!-- Bootstrap -->        
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-compras.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?> 
        <div class="container"> 

            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-contabil-header hidden-print">
                        <h2><?php echo $icon['38'] ?> - Financeiro <small>- <?= $titulo ?></small></h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <!--resposta de algum metodo realizado-->
                    <?php echo $global->getResposta($_SESSION['MESSAGE_TYPE'], $_SESSION['MESSAGE']); ?>                                
                    <div class="panel panel-default">
                        <table class="table table-striped table-hover valign-middle text-sm" id="tb_periodo">
                            <thead>
                                <tr>
                                    <th colspan="2">Projeto</th>
                                    <th class="text text-right">Período</th>
                                    <th class="text text text-right">Lançamento</th>
                                    <th colspan="2">&emsp;</th>
                                </tr>
                            </thead>
                            <tbody> 
                                <?php foreach ($array_periodos as $periodos) { 
                                    $periodo = $periodos['mes_lanc'].'/'.$periodos['ano_lanc'];
                                    if($periodos['lancamento'] == "despesa") {
                                        $lancamento = 1; 
                                    } else {
                                        $lancamento = 2; 
                                    } ?>
                                    <tr tr-<?= $periodos['id'].'-'.$lancamento ?> >
                                        <td><?= $periodos['projeto_id']?></td>
                                        <td><?= $periodos['nome'] ?></td>
                                        <td class="text text-right"><?= $periodo ?></td>
                                        <td class="text text-uppercase text text-right"><?= $periodos['lancamento'] ?></td>
                                        <td><?= ' ( '.$periodos['qtde'].' )' ?></td>
                                        <td class="text-right">
                                            <button class="btn btn-xs btn-info btn_travar" data-lancamento="<?= $lancamento ?>" data-mes="<?= $periodos['mes_lanc'] ?>" data-ano="<?= $periodos['ano_lanc'] ?>" data-lancamento="<?= $lancamento ?>" data-projeto="<?= $periodos['projeto_id'] ?>" data-periodo="<?= $periodo ?>" data-id="<?= $periodos['id'] ?>"><i class="fa fa-lock"></i> Fechar</button>
                                        </td>
                                    </tr>
                                <?php } ?>
                           </tbody>
                        </table>
                    </div>
                    <?php include_once '../../template/footer.php'; ?>
                </div>
            </div>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../resources/js/financeiro/saida.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.form.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="js/lancamentos.js" type="text/javascript"></script><!-- depois apontar para aresourse -->
        <style>
            .legends{
                font-size: 0.9em;
                color: silver;
            }
        </style>
    </body>
</html>