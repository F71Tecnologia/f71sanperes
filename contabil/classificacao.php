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
include_once("../classes/ContabilLoteClass.php");
include_once("../classes_permissoes/acoes.class.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$mes = (!empty($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
$ano = (!empty($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');

$botoes = new BotoesClass("../img_menu_principal/");
$icon = $botoes->iconsModulos;

$classificacao = new c_classificacaoClass();

$ACOES = new Acoes();

//PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE�ALHO (TROCA DE MASTER E DE REGI�ES)
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);

// seta um valor para vari�vel aba. usado para definir a aba aberta.
$aba = (isset($_REQUEST['aba'])) ? $_REQUEST['aba'] : 'cadastrar';

$projeto1 = montaSelect(array("-1" => "� Selecione a Regi�o �"), $projetoR, "id='projeto1' name='projeto' class='form-control validate[required,custom[select]]'");
$projeto2 = montaSelect(array("-1" => "� Selecione a Regi�o �"), $projetoR, "id='projeto2' name='projeto' class='form-control validate[required,custom[select]]'");
$projeto3 = montaSelect(array("-1" => "� Selecione a Regi�o �"), $projetoR, "id='projeto3' name='projeto' class='form-control'");
$projeto4 = montaSelect(array("-1" => "� Selecione a Regi�o �"), $projetoR, "id='projeto4' name='projeto' class='form-control'");

$optExercicio = array();
for ($i = date('Y') - 5; $i <= date('Y') + 1; $i++) {
    $optExercicio[$i] = $i;
}

$global = new GlobalClass();

function checkSel($Selecao1, $Selecao2) {
    return ($Selecao1 == $Selecao2) ? 'active' : '';
}

function checkAba($aba1, $aba2) {
    return ($aba1 == $aba2) ? 'active' : '';
}

$breadcrumb_config = array("nivel" => "../", "key_btn" => "38", "area" => "Gest�o Contabil", "ativo" => "Classifica��o Cont�bil", "id_form" => "frmplanodeconta");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Classifica��o Cont�bil</title>
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
                        <h2><?php echo $icon['38'] ?> - Contabilidade <small>- An�lise Cont�bil</small></h2>
                    </div>
                    <?php echo $global->getResposta($_SESSION['MESSAGE_TYPE'], $_SESSION['MESSAGE']); ?>                                
                    <div class="bs-component" role="tablist">
                        <ul class="nav nav-tabs nav-justified" style="margin-bottom: 20px;">
                            <li class="active"><a class="contabil" href="#conciliacao" data-toggle="tab">Lan�amentos</a></li>
                            <li><a class="contabil" href="#relatorio" data-toggle="tab">Finalizados</a></li>
                        </ul>
                    </div>                    
                    <div class="tab-content">                        
                        <div class="tab-pane active" id="conciliacao">                            
                            <form action="classificacao_controle.php" method="post" name="form_consulta_conciliacao" id="form_consulta_conciliacao" class="form-horizontal top-margin">
                                <!--<input type="hidden" name="conciliacao" value="Filtra">-->
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label for="projeto3" class="col-lg-2 control-label">Projeto</label>
                                            <div class="col-lg-4">
                                                <?= montaSelect(getProjetos($usuario['id_regiao']), $value, 'name="projeto" id="projeto" class="form-control validate[required]"')?>
                                            </div>
                                            <label class="col-lg-1 control-label">Data</label>
                                            <div class="col-lg-4">
                                                <div class="input-group" id="div_competencia">
                                                    <?= montaSelect(mesesArray(), $mes, 'name="mes" id="mes" class="form-control validate[required]"') ?>
                                                    <div class="input-group-addon">/</div>
                                                    <?= montaSelect(anosArray(), $ano, 'name="ano" id="ano" class="form-control validate[required]"') ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-footer text-right">
                                        <!--<button type="button" class="btn btn-info btn-sm disabled" id="btn_editar"><i class="fa fa-pencil"></i> Alterar Lan�amentos</button>-->
                                        <button type="button" class="btn btn-success btn-sm add_lancamento"><i class="fa fa-plus"></i> Lan�amento</button>
                                        <button type="submit" id="consultar_conciliacao" name="method" value="consultar_conciliacao" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filtrar</button>
                                    </div>
                                </div>
                            </form>

                            <div id="tabela_conciliacao"></div>

                        </div>
                        
                        <div class="tab-pane" id="relatorio">
                            <form action="classificacao_controle.php" method="post" name="form_consulta_finalizados" id="form_consulta_finalizados" class="form-horizontal top-margin">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label for="projeto3" class="col-lg-2 control-label">Projeto</label>
                                            <div class="col-lg-6">
                                                <?= montaSelect(getProjetos($usuario['id_regiao']), $value, 'name="projeto" id="projeto" class="form-control validate[required]"')?>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="panel-footer text-right">
                                        <button type="submit" id="consultar_finalizados" name="method" value="consultar_finalizados" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filtrar</button>
                                    </div>
                                </div>
                            </form>
                            
                            <div class="panel" id="tabela_finalizados"></div>
                        </div>
                    </div>
                </div>
            </div> 
            <form method="post" id="form_post"></form>
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
        <script src="js/classificacao.js" type="text/javascript"></script>
    </body>
</html>
