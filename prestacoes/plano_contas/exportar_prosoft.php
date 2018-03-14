<?php

session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=false';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("AssocContas.class.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

////PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);

$prosoft_projeto = montaSelect(array("-1" => "« Selecione o Projeto »"), $projetoR, "id='prosoft_projeto' name='prosoft_projeto' class='form-control validate[required,custom[select]] data-for='empresa'");
$prosoft_projet1 = montaSelect(array("-1" => "« Selecione o Projeto »"), $projetoR, "id='prosoft_projet1' name='prosoft_projet1' class='form-control validate[required,custom[select]] data-for='empres1'");
$global = new GlobalClass();

$aba = (isset($_REQUEST['aba'])) ? $_REQUEST['aba'] : 'despesareceita';

$nome_pagina = "Exportação Classificação Contas";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => $nome_pagina, "id_form" => "form_exportar");
$breadcrumb_pages = array("Integração Sistema Prosoft"=>"index.php");



function checkAba($aba1, $aba2) {
    return ($aba1 == $aba2) ? 'active' : '';
}


?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão Contabil</title>
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
<!--        <script type="text/javascript" src="../../resources/js///jquery/jquery-1.4.2.min.js"></script>-->
        <style media="print" >
            .tabela_ramon{
                visibility: visible;
                margin-top:-350px;
            }
            #head, fieldset{
                visibility:  hidden;
            }
        </style>
        <style>
            .baixar{
                text-align: center;
                text-decoration: none;
                width: 60px;
                height: 35px;
                display: block;
                border: 1px solid #FFF;
            }
            .baixar:hover{
                text-decoration: underline;
                border: 1px solid #000;
            }
            .excluir{

            }
        </style>
    </head>

    <body>
        <?php include("../../template/navbar_default.php"); ?> 
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-contabil-header">
                        <h2><span class="glyphicon glyphicon-usd"></span> - Gestão Contabil <small> - Exportação dos Lançamentos para o Sistema Prosoft</small></h2>
                    </div>
                    <div role="tabpanel">
                        <ul class="nav nav-tabs nav-justified" role="tablist" style="margin-bottom:30px;">
                            <li role="presentation" class="<?= checkAba('despesareceita', $aba) ?>"><a href="#despesareceita" aria-controls="despesareceita" role="tab" data-toggle="tab">Despesa / Receita</a></li>
                            <li role="presentation" class="<?= checkAba('folhapagamento', $aba) ?>"><a href="#folhapagamento" aria-controls="folhapagamento" role="tab" data-toggle="tab">Folha de Pagamento</a></li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane <?= checkAba('despesareceita', $aba) ?>" id="despesareceita">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <form action="controle.php" method="post" name="form_exportar" id="form_exportar" class="form-horizontal">
                                            <div class="form-group">
                                                <label for="prosoft_regiao" class="col-md-2 control-label">Região</label>
                                                <div class="col-md-5">
                                                    <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoR, "id='prosoft_regiao' name='prosoft_regiao' class='validate[required,custom[select]] form-control' data-for='prosoft_projeto'"); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="prosoft_projeto" class="col-md-2 control-label">Projeto</label>
                                                <div class="col-md-5">
                                                    <?php echo $prosoft_projeto ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-2 control-label">Data Inicio</label>
                                                <div class="col-md-2">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><label class="glyphicon glyphicon-calendar"></label></span>
                                                        <input type="text" class="form-control text-center hasdatepicker" id="datainicio" name="datainicio" value="<?= $datainicio ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-2 control-label">Data Fim</label>
                                                <div class="col-md-2">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><label class="glyphicon glyphicon-calendar"></label></span>
                                                        <input type="text" class="form-control text-center hasdatepicker" id="datafim" name="datafim" value="<?= $datafim ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-2 control-label">Lote</label>
                                                <div class="col-md-3">
                                                    <input type="text" id="lote" name="lote" maxlength="5" class="form-control text-center">
                                                </div>
                                            </div>
                                            <div class="text-right panel-footer">
                                                <div class="col-md-12 left">
                                                    <input class="btn btn-default" type="submit"  name="arquivo_gerar" id="arquivo_gerar" value="Gerar Arquivo"/>                                 
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane <?= checkAba('folhapagamento', $aba) ?>" id="folhapagamento">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <form action="controle.php" method="post" name="" id=""  class="form-horizontal">
                                            <div class="form-group">
                                                <label for="prosoft_regia1" class="col-md-2 control-label">Região</label>
                                                <div class="col-md-5">
                                                    <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoR, "id='prosoft_regia1' name='prosoft_regia1' class='validate[required,custom[select]] form-control' data-for='prosoft_projet1'"); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="prosoft_projet1" class="col-md-2 control-label">Projeto</label>
                                                <div class="col-md-5">
                                                    <?php echo $prosoft_projet1 ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-2 control-label">Data Inicio</label>
                                                <div class="col-md-2">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><label class="glyphicon glyphicon-calendar"></label></span>
                                                        <input type="text" class="form-control text-center hasdatepicker" id="dtainicio" name="dtainicio" value="<?= $datainicio ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-2 control-label">Data Fim</label>
                                                <div class="col-md-2">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><label class="glyphicon glyphicon-calendar"></label></span>
                                                        <input type="text" class="form-control text-center hasdatepicker" id="dtafim" name="dtafim" value="<?= $datafim ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-2 control-label">Lote</label>
                                                <div class="col-md-3">
                                                    <input type="text" id="lote" name="lote" maxlength="5" class="form-control text-center">
                                                </div>
                                            </div>
                                            <div class="text-right panel-footer">
                                                <div class="col-md-12 left">
                                                    <input class="btn btn-default" type="submit" name="provisao_folha" id="provisao_folha" value="Folha Pagamento">   
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
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
        <script src="../js/prestacaocontas.js" type="text/javascript"></script><!-- depois apontar para aresourse -->
        <style media="print" >
            .tabela_ramon{
                visibility: visible;
                margin-top:-350px;
            }

            #head, fieldset{
                visibility:  hidden;
            }

            .baixar{
                text-align: center;
                text-decoration: none;
                width: 60px;
                height: 35px;
                display: block;
                border: 1px solid #FFF;
            }
            .baixar:hover{
                text-decoration: underline;
                border: 1px solid #000;
            }
            .excluir{

            }
            .legends{
                font-size: 0.9em;
                color: silver;
            }
        </style>
              
    </body>
</html>
