<?php
error_reporting(E_ALL);

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=false';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/BancoClass.php");
include("../../classes/global.php");
include("../../classes/c_classificacaoClass.php");
include("../../classes/ContabilLancamentoClass.php");
include("../../classes/ContabilLancamentoItemClass.php");
include("../../classes/ContabilLoteClass.php");
include("../../classes/c_planodecontasClass.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];
$id_master = $usuario['id_master'];

$query_master = "SELECT * FROM master WHERE id_master = $id_master";
$master = mysql_fetch_assoc(mysql_query($query_master));

$botoes = new BotoesClass("../../img_menu_principal/");
$icon = $botoes->iconsModulos;

$objClassificador = new c_classificacaoClass();
$objLoteEncerramento = new ContabilLoteClass();
$objLancamentoEncerramento = new ContabilLancamentoClass();
$objLancamentoItensEncerramento = new ContabilLancamentoItemClass();
$objPlanoConta = new c_planodecontasClass();

$projeto = ($_REQUEST['projeto'] > 0) ? $_REQUEST['projeto'] : null;
$ano = (!empty($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$inicio = (!empty($_REQUEST['inicio'])) ? $_REQUEST['inicio'] : "01/" . date('m/Y');
$final = (!empty($_REQUEST['final'])) ? $_REQUEST['final'] : date('t', date('m-Y') . "-01") . date('/m/Y');

if (isset($_REQUEST['filtrar']) || isset($_REQUEST['encerrar'])) {
    if ($_REQUEST['exercicio'] == 1) {
        $historico_do_exercicio = "ENCERRAMENTO DO EXERCÍCIO NO ANO DE " . $ano;
        $lote_numero = "ENCERRAMENTO DRE " . $ano;
        $array = $objClassificador->encerramento($projeto, $ano, 12, null, null, true);
    } elseif ($_REQUEST['exercicio'] == 2) {
        $historico_do_exercicio = "ENCERRAMENTO DO EXERCÍCIO NO PERÍODO DE " . ConverteData($inicio, 'd/m/Y') . " À " . ConverteData($final, 'd/m/Y');
        $lote_numero = "ENCERRAMENTO DRE " . $inicio . " - " . $final;
        $array = $objClassificador->encerramento($projeto, null, null, ConverteData($inicio, 'Y-m-d'), ConverteData($final, 'Y-m-d'), true);
    }
    
    $apuracao = $array['total'];
    
}
if (isset($_REQUEST['encerrar'])) {

    $projeto = ($_REQUEST['projeto'] > 0) ? $_REQUEST['projeto'] : null;

    $search = array('(', ')', '.');
    $conta_preju = str_replace(',', '.', str_replace($search, '', $_REQUEST['prejuizo']));
    $conta_lucro = str_replace(',', '.', str_replace($search, '', $_REQUEST['lucro']));

    if ($conta_preju != 0.00) {
        $conta = $_REQUEST['id_deficit'];
        $valor = $conta_preju;
        $tipo = 2;
    } else if ($conta_lucro != 0.00) {
        $conta = $_REQUEST['id_superavit'];
        $valor = $conta_lucro;
        $tipo = 1;
    } else {
        $conta = NULL;
        $valor = 0.00;
    }
    
    if ($_REQUEST['exercicio'] == 2) {
        $ano_encerramento = ConverteData($_REQUEST['final'], 'Y');
        $mes_encerramento = ConverteData($_REQUEST['final'], 'm');
        $data_lancamento = ConverteData($_REQUEST['final'], 'Y-m-d');
        $lote = ConverteData($_REQUEST['final'], 'dmY');
    } else if ($_REQUEST['exercicio'] == 1){
        $ano_encerramento = $_REQUEST['ano'];
        $mes_encerramento = 12;
        $data_lancamento = $_REQUEST['ano'].'-'.'12'.'-'.'31';
        $lote = '3112'.$_REQUEST['ano'];
    }
    
    $objLancamentoEncerramento->setIdLote($lote);
    $objLancamentoEncerramento->setIdProjeto($projeto);
    $objLancamentoEncerramento->setIdUsuario($usuario['id_funcionario']);
    $objLancamentoEncerramento->setIdSaida('0');
    $objLancamentoEncerramento->setIdEntrada('0');
    $objLancamentoEncerramento->setDataLancamento($data_lancamento);
    $objLancamentoEncerramento->setHistorico($historico_do_exercicio);
    $objLancamentoEncerramento->setContabil(1);
    $objLancamentoEncerramento->setStatus(3);
    
    $arrayLancamento = $objLancamentoEncerramento->insert();
    $id_lancamento = $objLancamentoEncerramento->getIdLancamento();
    
    $objLancamentoItensEncerramento->setIdLancamento($id_lancamento);
    $objLancamentoItensEncerramento->setIdConta($conta);
    $objLancamentoItensEncerramento->setValor($valor);
    $objLancamentoItensEncerramento->setTipo($tipo);
    $objLancamentoItensEncerramento->setHistorico($historico_do_exercicio);
    $objLancamentoItensEncerramento->setStatus(3);
    
    $arrayLancamentoItens = $objLancamentoItensEncerramento->insert();
}

$arrayConta = $objPlanoConta->retornaConta($projeto);

if ($arrayConta[0]['classificador'] == '8.01.01.01') {
    $superavit = '['.$arrayConta[0]['cod_reduzido'].'] '.$arrayConta[0]['classificador'].' - '.$arrayConta[0]['descricao'];
    $id_superavit = $arrayConta[0]['id_conta'];
}
if ($arrayConta[1]['classificador'] == '8.01.01.01') {
    $deficit = '['.$arrayConta[1]['cod_reduzido'].'] '.$arrayConta[1]['classificador'].' - '.$arrayConta[1]['descricao'];
    $id_deficit = $arrayConta[1]['id_conta'];
}


$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => "Encerramento", "id_form" => "frmencerramento");


if ($apuracao < 0) {
        $prejuizo = $apuracao;
    } else { 
        $prejuizo = 0.00;
    }
    if($apuracao >= 0) {
        $lucro = $apuracao;
    } else {
        $lucro = 0.00;
    }

?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Encerramento</title>
        <link rel="shortcut icon" href="../../favicon.png">
        <!-- Bootstrap -->        
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-compras.css" rel="stylesheet" media="all">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="all">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?> 
        <div class="container">
            <div class="col-sm-12">
                <div class="page-header box-contabil-header hidden-print">
                    <h2><?php echo $icon['38'] ?> - Contabilidade <small>- Encerramento</small></h2>
                </div>
                <form action="" method="post" name="form_encerramento" id="form_encerramento" class="form-horizontal top-margin hidden-print" enctype="multipart/form-data">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="" class="col-sm-2 text-sm control-label">Encerramento</label>
                                <div class="col-sm-2">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input type="radio" id="ano_exercicio" name="exercicio" <?= (isset($_REQUEST['exercicio']) && $_REQUEST['exercicio'] == 1) ? 'checked' : '' ?>  value="1"></div>
                                        <label class="form-control pointer input-sm" for="ano_exercicio">Anual</label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input type="radio" id="periodo_exercicio" name="exercicio" <?= (isset($_REQUEST['exercicio']) && $_REQUEST['exercicio'] == 2) ? 'checked' : '' ?>  value="2"></div>
                                        <label class="form-control pointer input-sm" for="periodo_exercicio">Trimestral</label>
                                    </div>
                                </div>
                                <div class="col-sm-4 text-sm text-right">
                                    <div id="exercicio_periodo" class="input-group text-sm" <?= ($_REQUEST['exercicio'] == 1 || !isset($_REQUEST['exercicio'])) ? 'style="display:none"' : '' ?>>
                                        <input type="text" id='inicio' name='inicio' class='text-sm text-center data validate[required,custom[select]] form-control' value="<?= $inicio ?>">
                                        <div class="input-group-addon text-sm">até</div>
                                        <input type="text" id='final' name='final' class='text-sm text-center data validate[required,custom[select]] form-control' value="<?= $final ?>">
                                    </div>
                                    <div id="exercicio_ano" class="input-group"  <?= ($_REQUEST['exercicio'] == 2 || !isset($_REQUEST['exercicio'])) ? 'style="display:none"' : '' ?>>
                                        <span class="input-group-addon text-sm"><label class="glyphicon  glyphicon-calendar"></label></span>
                                        <?php echo montaSelect(anosArray(), $ano, "id='ano' name='ano' class='input-sm validate[required,custom[select]] form-control'"); ?>
                                    </div>
                                </div>
                            </div>                        
                            <div class="form-group">                                    
                                <label for="projeto1" class="col-sm-2 text-sm control-label">Projeto</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <?= utf8_decode(montaSelect(getProjetos($usuario['id_regiao']), $projeto, "id='projeto' name='projeto' class='form-control input-sm validate[required,custom[select]]'")) ?>
                                    </div> 
                                </div>
                                <button type="submit" id="mensal" name="filtrar" value="mensal" class="btn btn-default btn-sm"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                    <?php if (isset($_REQUEST['filtrar']) || isset($_REQUEST['filtra'])) { ?>
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="form-group">
                                    <label for="" class="col-sm-5 text-danger text-sm control-label">Contas de Resultado</label>
                                </div>                                
                                <div class="form-group">
                                    <label for="" class="col-sm-2 text-sm control-label">Lucro</label>                                    
                                    <div class="col-sm-2">
                                        <input readonly class="text-right form-control" id="lucro" name="lucro" type="text" value="<?= number_format($lucro, 2, ',', '.') ?>">
                                    </div>
                                    <div class="col-sm-8">
                                        <input readonly class="text-left form-control" type="text" id="superavit" name="superavit" value="<?= utf8_decode($superavit) ?>">
                                        <input type="hidden" id="id_superavit" name="id_superavit" value="<?= $id_superavit ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 text-sm control-label">Prejuízo</label>                                    
                                    <div class="col-sm-2">
                                        <input readonly class="text-right form-control" id="prejuizo" name="prejuizo" type="text" value="<?= $prejuizo < 0 ? '(' . number_format($prejuizo * -1, 2, ',', '.') . ')' : number_format($prejuizo, 2, ',', '.') ?>">                                     
                                    </div>
                                    <div class="col-sm-8">
                                        <input readonly class="text-left form-control" type="text" id="deficit" name="deficit" value="<?= utf8_decode($deficit) ?>">
                                        <input type="hidden" id="id_deficit" name="id_deficit" value="<?= $id_deficit ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 text-sm control-label">Apuração</label>                                   
                                    <div class="col-sm-2">
                                        <input readonly class=" text-right form-control" type="text" value="<?= $saldo < 0 ? '(' . number_format($saldo * -1, 2, ',', '.') . ')' : number_format($saldo, 2, ',', '.') ?>">                                     
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 text-sm control-label">Histórico</label>
                                    <div class="col-sm-8">
                                        <input type="text" id='encerramento_historico' name='encerramento_historico' class='text-sm form-control' value="<?= $historico_do_exercicio ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-8">
                                        <div class="input-group">
                                            <div class="input-group-addon"><input type="checkbox" id="encerramento_empresa" name="encerramento_empresa" <?= (isset($_REQUEST['encerramento_empresa']) || (!isset($_REQUEST['encerramento_empresa']) && !isset($_REQUEST['encerramento_empresa']))) ? '' : null ?> class=''></div>
                                            <label class="form-control pointer input-sm" for="encerramento_empresa">Gravar a data do encerramento, como data do fechamento da Empresa!</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <button type="submit" id="encerrar" name="encerrar" value="" class="btn btn-info btn-sm"><i class="fa fa-check-square-o"></i> Encerrar</button>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-info">Nenhuma informação encontrada neste filtro!</div>
                    <?php } ?>
                </form>
                <?php include_once '../../template/footer.php'; ?>
            </div>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../js/jquery.form.js"></script>
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
        <script src="js/encerramento.js" type="text/javascript"></script>
    </body>
</html>
