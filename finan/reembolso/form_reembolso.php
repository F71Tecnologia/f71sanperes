<?php
session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/ReembolsoClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$reembolso = new Reembolso();

//insert
if(isset($_REQUEST['cadastrar']) && $_REQUEST['cadastrar'] == "Cadastrar"){
    $reembolso->cadReembolso();    
}

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Solicitação de Reembolso");
$breadcrumb_pages = array("Reembolso" => "index.php");
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Solicitação de Reembolso</title>
        
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
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - Solicitação de Reembolso</small></h2></div>                                                                                      
            <form action="" method="post" id="form1" class="form-horizontal top-margin1" enctype="multipart/form-data">
                
                <?php if(isset($_SESSION['regiao'])){ ?>
                <!--resposta de algum metodo realizado-->
                <div class="alert alert-dismissable alert-<?php echo $_SESSION['MESSAGE_TYPE']; ?> msg_cadsuporte">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <p><?php echo $_SESSION['MESSAGE'];
                    session_destroy(); ?></p>
                </div>
                <?php } ?>                                
                
                <fieldset>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Funcionário</label>
                                    <div class="col-lg-9 text-left">
                                        <div class="radio radio-inline">
                                            <label>
                                                <input type="radio" id="fun1" name="funcionario" class="validate[required]" value="1" /> Sim
                                            </label>
                                        </div>
                                        <div class="radio radio-inline">
                                            <label>
                                                <input type="radio" id="fun2" name="funcionario" class="validate[required]" value="2" /> Não
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Nome / Razão</label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control validate[required]" id="nome_razao" name="nome_razao" style="display: none;" />
                                        <?php echo montaSelect($reembolso->getCltR(),null, "id='user' name='user' class='form-control validate[required,custom[select]]' style='display: none;'"); ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Valor</label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control validate[required]" id="valor" name="valor" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Descrição</label>
                                    <div class="col-lg-9">
                                        <textarea class="form-control validate[required]" rows="3" id="textArea" name="descricao"></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="panel-wide">
                            
                            <h6 class="text-light-gray text-bold text-sm form-group-margin" style="margin:20px 0 10px 0;">DADOS BANCÁRIOS PARA O DEPÓSITO</h6>
                            
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Banco</label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" id="banco" name="banco" />
                                    </div>
                                    <label class="col-lg-1 control-label">Nome</label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" id="nomefavo" placeholder="Favorecido" name="nomefavo" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Agência</label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" id="agencia" name="agencia" />
                                    </div>
                                    <label class="col-lg-1 control-label">CPF</label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" id="cpf" name="cpf" placeholder="CPF/CNPJ" />
                                    </div>
                                </div>
                            </div>
                             <div class="row">
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Conta</label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" id="conta" name="conta" />
                                    </div>                                    
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <div class="col-sm-6 text-left"><button type="button" class="btn btn-default" id="volta_index" name="voltar"><span class="fa fa-reply"></span>&nbsp;Voltar</button></div>
                            <div class="col-sm-6 text-right"><input type="submit" class="btn btn-primary" name="cadastrar" id="cadastrar" value="Cadastrar" /></div>
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
        <script src="../../resources/js/financeiro/reembolso.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script>
            $(function() {
                $("#valor").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});
                $("#form1").validationEngine({promptPosition : "topRight"});
            });
        </script>
    </body>
</html>