<?php

session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=false';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
//include("../classes/NFeClass.php");
//include("../classes/BancoClass.php");
include ("AssocContas.class.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

//PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);

//// seta um valor para variável aba. usado para definir a aba aberta.
//$aba = (isset($_REQUEST['aba'])) ? $_REQUEST['aba'] : 'cadastrar';

$prosoft_projeto1 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='prosoft_projeto1' name='prosoft_projeto1' class='form-control validate[required,custom[select]]'");
$projeto2 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto2' name='projeto' class='form-control validate[required,custom[select]]' data-for='prestador2'");
$projeto3 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto3' name='projeto' class='form-control validate[required,custom[select]]' data-for='prestador3'");

$global = new GlobalClass();

//function checkSel($Selecao1, $Selecao2) {
//    return ($Selecao1 == $Selecao2) ? 'active' : '';
//}
//function checkAba($aba1, $aba2) {
//    return ($aba1 == $aba2) ? 'active' : '';
//}

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "36", "area" => "Gestão Prestação de Contas", "ativo" => "Plano de Contas", "id_form" => "form1");

$qry = mysql_query("SELECT * FROM entradaesaida WHERE tipo = 1 ORDER BY cod");
$dados = mysql_fetch_array($qry);
$OpcaoCta[(" ")] = " ";
while ($dados = mysql_fetch_array($qry)) {
$OpcaoCta[$dados['id_entradasaida']] = $dados['cod']." - ".$dados['nome'];
}

$qry1 = mysql_query("SELECT * FROM plano_de_contas ORDER BY acesso" );
$dados1 = mysql_fetch_array($qry1);
$OpcaoCtas[(" ")] = " ";
while ($dados1 = mysql_fetch_array($qry1)) {
    $OpcaoCtas[$dados1['id_plano_contas']] = $dados1['acesso']." - ".$dados1['nome'];
}

$qryTerc = mysql_query("SELECT * FROM prestador_prosoft ORDER BY id_prestador_prosoft");
$terceiros = mysql_fetch_array($qryTerc);
$OpcaoTerceiros[(" ")] = " ";
while ($terceiros = mysql_fetch_array($qryTerc)) {
$OpcaoTerceiros[$terceiros['id_prestador_prosoft']] = $terceiros['nome'];
}

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão NFe</title>
        <link rel="shortcut icon" href="../favicon.png">
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
                <div class="col-lg-12">
                    <div class="page-header box-contas-header">
                        <h2><span class="glyphicon glyphicon-usd"></span> - Gestão Prestação de Contas<small> - Associação Plano de Contas (Prosoft)</small></h2>
                    </div>
                    <?php echo $global->getResposta($_SESSION['MESSAGE_TYPE'], $_SESSION['MESSAGE']); ?>
                    <div class="form-group">
                        <form action="associacaocontas.php" method="post" name="form1" id ="form1" class="form-horizontal">
                            <div class="form-group">
                                <label for="prosoft_regiao1" class="col-lg-2 control-label">Setor</label>
                                <div class="col-lg-6">
                                    <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoR, "id='prosoft_regiao1' name='prosoft_regiao1' class='validate[required,custom[select]] form-control' data-for='prosoft_projeto1'"); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="prosoft_projeto1" class="col-lg-2 control-label">Empresa</label>
                                <div class="col-lg-6">
                                    <?php echo $prosoft_projeto1; ?>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label for="" class="col-lg-2 control-label">Conta</label>
                                <div class="col-lg-6">
                                    <select class="form-control" name="prosoft_contaL" id="prosoft_contaL">
                                        <?php foreach ($OpcaoCta as $key => $value) { ?>
                                            <option value="<?php echo $key ;?>" class="text-sm text-center">
                                                <?php echo $value ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group esq">
                                <label for="" class="col-lg-2 control-label">Prosoft</label>
                                <div class="col-lg-6">
                                    <select class="form-control" name="prosoft_contaP" id="prosoft_contaP">
                                        <?php foreach ($OpcaoCtas as $key => $value) { ?>
                                            <option value="<?php echo $key ;?>" class="text-sm text-center">
                                                <?php echo $value ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group esq">
                                <label for="" class="col-lg-2 control-label">Terceiro</label>
                                <div class="col-lg-6">
                                    <select class="form-control" name="prosoft_terceiro" id="prosoft_terceiro">
                                        <?php foreach ($OpcaoTerceiros as $key => $value) { ?>
                                        <option value="<?php echo $key ;?>" class="text-sm text-center">
                                                <?php echo $key." ".$value ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="text-right panel-footer">                                     
                                <input class="btn btn-info" type="submit"  name="associar_contas" id="associar_contas" value="Associar"/>                                 
                                    <input type="button" class="btn btn-default" value="Ver Relarório" name="relatorio" id="relatorio" />

                            </div>
                        </form>  
                    </div>
                    <div id="resposta"></div>
                </div>
            </div>
            <?php include_once '../../template/footer.php'; ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../resources/js/financeiro/saida.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../js/jquery.form.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="../js/prestacaocontas.js" type="text/javascript"></script>
        <style>
            .legends{
                font-size: 0.9em;
                color: silver;
            }
        </style>
    </body>
</html>
    