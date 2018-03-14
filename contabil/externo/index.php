<?php
if (empty($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes_permissoes/acoes.class.php");

$objAcoes = new Acoes();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

//CARREGANDO MENU DE ACORDO COM AS PERMISSOES DA PESSOA
$botoes = new BotoesClass($dadosHeader['defaultPath'],$dadosHeader['fullRootPath']);
//print_array($botoesMenu);
$nome_pagina = "Relatórios Contábeis";
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => $nome_pagina, "id_form" => "form1"); 
//$breadcrumb_pages = array("Gestão de RH"=>"../../", "Seguro Desemprego"=>"index2.php");


?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: </title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-contabil-header"><h2><span class="fa fa-bar-chart"></span> - Contabilidade Externa</h2></div>
                    <div class="bs-component">
                        <div class="detalhes-modulo">
                            <?php echo $botoes->getHtmlBotoesModulo(50,38) ?> 
                        </div>
                    </div> 
                </div>
            </div>
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.container -->
        <!-- MODAL DE PRORROGACAO DE EVENTOS -->
        <form name="eventos" id="form1" action="">
            <input type="hidden" name="home" id="home" value="" />
            <input type="hidden" name="id_evento" id="id_evento" value="" />
            <input type="hidden" name="id_user" id="id_user" value="<?php echo $_COOKIE['logado'] ?>" />
            <input type="hidden" name="data_retorno" id="data_retorno" value="" />
            <div id="modal_motivo" class="minus">
                <p style="text-align: left;">
                    <label for="dias" style=" font-weight: bold;" >Quantidade de dias (a partir da data atual de retorno):</label><br />
                    <input type="number" name="dias"  min="0"  id="dias" style="width: 4em; height: 30px; margin: 3px 0px;" > <button type="button" id="calc-data">Calcular Prorrogação</button>
                </p>
                <p>
                    <label for="data_prorrogada" style="float: left; font-weight: bold;" >Data de Prorrogação:</label><br />
                    <input style="width: 425px; height: 30px; margin: 3px 0px;" type="text" name="data_prorrogada" id="data_prorrogada" value="" />
                </p>
                <p>
                    <label for="motivo" style="float: left; font-weight: bold;">Digite o motivo:</label><br />
                    <textarea name="motivo" style="width: 425px; height: 80px; margin: 3px 0px;"></textarea>
                </p>
                <input type="submit" name="finalizar" id="finalizar" style="float: right" value="Finalizar" />
                <div id="message_erro"></div>
            </div>
        </form>
        <!-- FIM DO MODAL DE PRORROGACAO DE EVENTOS -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.maskedinput.min.js" type="text/javascript"></script>
    </body>
</html>