<?php
session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/PagamentoClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$pagamento = new Pagamento();

$pagamento_fin = $_REQUEST['pagamento'];

$regiao_selecionada = $usuario['id_regiao'];

//tabela bancos
$row = $pagamento->getPagamentoID($pagamento_fin);
$regiao_bd = $row['id_regiao'];
$projeto_bd = $row['id_projeto'];

//insert
if(isset($_REQUEST['cadastrar']) && $_REQUEST['cadastrar'] == "Cadastrar"){
    $pagamento->cadPagamento();
}

//update
if(isset($_REQUEST['atualizar']) && $_REQUEST['atualizar'] == "Atualizar"){
    $pagamento->alteraPagamento();
}

//trata insert/update
if($pagamento_fin == ''){
    $acao = 'Cadastro';
    $botao = 'Cadastrar';
    $projeto = montaSelect(getProjetos($regiao_selecionada),null, "id='projeto' name='projeto' class='form-control validate[required,custom[select]]'");    
}else{
    $acao = 'Edição';
    $botao = 'Atualizar';
    $projeto = montaSelect(getProjetos($regiao_bd),$projeto_bd, "id='projeto' name='projeto' class='form-control validate[required,custom[select]]'");    
}

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"$acao de tipo de Pagamento");
$breadcrumb_pages = array("Gestão de Tipos de Pagamentos" => "index.php");;
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: <?=$acao?> de tipo de Pagamento</title>
        
        <link href="../../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - <?=$acao?> de tipo de Pagamento</small></h2></div>
            <form action="" method="post" id="form1" class="form-horizontal top-margin1" enctype="multipart/form-data">
                
                <!--resposta de algum metodo realizado-->
                <div class="alert alert-dismissable alert-<?php echo $_SESSION['MESSAGE_TYPE']; ?> msg_cadsuporte">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <p><?php echo $_SESSION['MESSAGE'];
                    session_destroy(); ?></p>
                </div>
                <input type="hidden" id="msg" name="msg" value="<?php echo $_SESSION['MESSAGE']; ?>" />
                <input type="hidden" id="regiao_selecionada" name="regiao_selecionada" value="<?php echo $regiao_selecionada; ?>" />
                <input type="hidden" id="id_pagamento" name="id_pagamento" value="<?php echo $row['id_tipopg']; ?>" />
                
                <fieldset>
                    <div class="panel panel-default">
                        <div class="panel-body">                            
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-lg-2 control-label">Projeto</label>
                                    <div class="col-lg-9">
                                        <?php echo $projeto; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="assunto" class="col-lg-2 control-label">Tipo de Pagamento</label>
                                    <div class="col-lg-9 text-left">
                                        <select id='tipopg' name='tipopg' class='form-control'>
                                            <option value="Depósito em Conta Corrente" <?php echo selected('Depósito em Conta Corrente', $row['tipopg']); ?>>Depósito em Conta Corrente</option>
                                            <option value="Cheque" <?php echo selected('Cheque', $row['tipopg']); ?>>Cheque</option>
                                            <option value="Dinheiro" <?php echo selected('Dinheiro', $row['tipopg']); ?>>Dinheiro</option>                                            
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <div class="col-sm-6 text-left">
                                <button type="button" class="btn btn-default" id="volta_index" name="voltar"><span class="fa fa-reply"></span>&nbsp;&nbsp;Voltar</button>
                            </div>
                            <div class="col-sm-6 text-right">
                                <input type="submit" class="btn btn-primary" name="<?php echo strtolower($botao); ?>" id="<?php echo strtolower($botao); ?>" value="<?php echo $botao; ?>" />
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </fieldset>
            </form>
            
            <?php include("../../template/footer.php"); ?>
        </div>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js" type="text/javascript" ></script>
        <script src="../../resources/js/financeiro/banco.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script>
            $(function() {
                $("#form1").validationEngine({promptPosition : "topRight"});                
            });
        </script>
    </body>
</html>