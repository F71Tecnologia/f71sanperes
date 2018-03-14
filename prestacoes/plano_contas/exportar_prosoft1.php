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
$global = new GlobalClass();

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "36", "area" => "Gestão Prestação de Contas", "ativo" => "Exportação de Arquivos", "id_form" => "form1");

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
                    <div class="page-header box-contas-header">
                        <h2><span class="glyphicon glyphicon-usd"></span> - Gestão Contabil <small> - Exportação dos Lançamentos para o Sistema Prosoft</small></h2>
                    </div>
                    <div class="row">
                        <form action="controle.php" method="post" name="form_exportar" id="form_exportar"  class="form-horizontal">
                            <div class="form-group">
                                <label for="prosoft_regiao" class="col-lg-2 control-label">Região</label>
                                <div class="col-lg-4">
                                    <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoR, "id='prosoft_regiao' name='prosoft_regiao' class='validate[required,custom[select]] form-control' data-for='prosoft_projeto'"); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="prosoft_projeto" class="col-lg-2 control-label">Projeto</label>
                                <div class="col-lg-4">
                                    <?php echo $prosoft_projeto; ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label">Data Inicio</label>
                                <div class="col-lg-2">
                                    <div class="input-group">
                                        <span class="input-group-addon"><label class="glyphicon glyphicon-calendar"></label></span>
                                        <input type="text" class="form-control text-center hasdatepicker" id="datainicio" name="datainicio" value="<?= $datainicio ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label">Data Fim</label>
                                <div class="col-lg-2">
                                    <div class="input-group">
                                        <span class="input-group-addon"><label class="glyphicon glyphicon-calendar"></label></span>
                                        <input type="text" class="form-control text-center hasdatepicker" id="datafim" name="datafim" value="<?= $datafim ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label">Lote</label>
                                <div class="col-lg-3">
                                    <input type="text" id="lote" name="lote" maxlength="5" class="form-control text-center">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label">Empresa</label>
                                <div class="col-lg-3">
                                    <input type="text" id="empresaP" name="empresaP" placeholder="Código no Prosoft" class="form-control text-center">
                                </div>
                            </div>
                            
                            <div class="text-right panel-footer">
                                <div class="col-lg-12 left">
                                    <input class="btn btn-default" type="submit"  name="despesa_arquivo" id="despesa_arquivo" value="Impostos Provisão"/>                                 
                                    <input class="btn btn-default" type="submit"  name="despesa_arquivo" id="despesa_arquivo" value="Folha Pagamento"/>                                 
                                    <input class="btn btn-default" type="submit"  name="receita_arquivo" id="receita_arquivo" value="Despesa X Receita"/>                                
                                </div>
                            </div>
                        </form>  
                    </div>
                </div>
            </div>
            <div id="resposta"></div>
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
