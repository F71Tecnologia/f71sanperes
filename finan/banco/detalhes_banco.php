<?php
session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/BancoClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$banco_fin = $_REQUEST['banco'];

$usuario = carregaUsuario();
$row = getBancoID($banco_fin);

//trata local
$local_banco = $row['interior'];
if($local_banco == 1){
    $loc = 'INTERNO';
}else{
    $loc = 'EXTERNO';
}

$id_regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];

$regiao_selecionada = $_REQUEST['hide_regiao'];
$projeto_selecionado = $_REQUEST['hide_projeto'];

$_SESSION['regiao_select'] = $regiao_selecionada;
$_SESSION['projeto_select'] = $projeto_selecionado;
session_write_close();

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Detalhe do Banco");
$breadcrumb_pages = array("Gestão de Bancos" => "index.php");
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Detalhe do Banco</title>
        
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
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - Detalhe do Banco</small></h2></div>
            <form action="" method="post" id="form1" class="form-horizontal top-margin1" enctype="multipart/form-data">
                
                <input type="hidden" id="banco" name="banco" value="<?php echo $row['id_banco']; ?>" />
                <input type="hidden" id="caminho" name="caminho" value="1" />
                
                <fieldset>
                    <legend>Banco <?php echo $row['nome']; ?></legend>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-lg-2 control-label">Região</label>
                                    <label class="col-lg-4 control-label text-left text-normal">
                                        <?php echo $row['id_regiao'] . " - " . $row['nome_regiao']; ?>
                                    </label>
                                    <label for="mensagem" class="col-lg-1 control-label">Projeto</label>
                                    <label class="col-lg-4 control-label text-left text-normal">
                                        <?php echo $row['id_projeto'] . " - " . $row['nome_projeto']; ?>
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-lg-2 control-label">Nome</label>
                                    <label class="col-lg-4 control-label text-left text-normal">
                                        <?php echo $row['nome']; ?>
                                    </label>
                                    <label for="mensagem" class="col-lg-1 control-label">Local</label>
                                    <label class="col-lg-4 control-label text-left text-normal">
                                        <?php echo $loc; ?>
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-lg-2 control-label">Banco</label>
                                    <label class="col-lg-4 control-label text-left text-normal">
                                        <?php echo $row['razao']; ?>
                                    </label>
                                    <label for="mensagem" class="col-lg-1 control-label">Localidade</label>
                                    <label class="col-lg-4 control-label text-left text-normal">
                                        <?php echo $row['localidade']; ?>
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-lg-2 control-label">Conta Corrente</label>
                                    <label class="col-lg-4 control-label text-left text-normal">
                                        <?php echo $row['conta']; ?>
                                    </label>
                                    <label for="mensagem" class="col-lg-1 control-label">Gerente</label>
                                    <label class="col-lg-4 control-label text-left text-normal">
                                        <?php echo $row['gerente']; ?>
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-lg-2 control-label">Agência</label>
                                    <label class="col-lg-4 control-label text-left text-normal">
                                        <?php echo $row['agencia'];; ?>
                                    </label>
                                    <label for="mensagem" class="col-lg-1 control-label">Telefone</label>
                                    <label class="col-lg-4 control-label text-left text-normal">
                                        <?php echo $row['tel']; ?>
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-lg-2 control-label">Endereço</label>
                                    <label class="col-lg-4 control-label text-left text-normal">
                                        <?php echo $row['endereco'];; ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <div class="col-sm-6 text-left">
                                <button type="button" class="btn btn-default" id="volta_index"><span class="fa fa-reply"></span> Voltar</button>
                            </div>
                            <div class="col-sm-6 text-right">
                                <input type="submit" class="btn btn-primary" value="Editar" name="editarBanco" id="editarBanco" data-type="editar" data-key="<?=$row['id_banco']?>" data-caminho="1" />
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
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(function() {
                $("#form1").validationEngine({promptPosition : "topRight"});
                $("#tel").mask("(99)9999-9999?9");
            });
        </script>
    </body>
</html>