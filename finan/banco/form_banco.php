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
$prestaContas = array("0"=>"Não", "1"=>"Sim");

if(($_REQUEST['banco'] != '') && empty($_REQUEST['cadastrar'])){
    $banco_fin = $_REQUEST['banco'];
}elseif($_SESSION['unidade'] != ''){
    $banco_fin = $_SESSION['banco'];
}

$regiao_selecionada = $_REQUEST['hide_regiao'];

//tabela bancos
$row = getBancoID($banco_fin);
$regiao_bd = $row['id_regiao'];
$projeto_bd = $row['id_projeto'];
$idnacional_bd = $row['id_nacional'];
$interno = $row['interno'];
$presta_conta = $row['presta_conta'];

//tabela  listabancos
$lista_banco = getBancoBDIDNacional($idnacional_bd);
$id_lista = $lista_banco['id_lista'];

//insert
if(isset($_REQUEST['cadastrar']) && $_REQUEST['cadastrar'] == "Cadastrar"){
    cadBanco($regiao_selecionada, $usuario);
    $regiao_selecionada = $_SESSION['regiao'];    
}

//update
if(isset($_REQUEST['atualizar']) && $_REQUEST['atualizar'] == "Atualizar"){
    alteraBanco($usuario);
}

//trata insert/update
if($banco_fin == ''){
    $acao = 'Cadastro';
    $botao = 'Cadastrar';
    $projeto = montaSelect(getProjetos($regiao_selecionada),null, "id='projeto' name='projeto' class='form-control required[custom[select]]'");
    $banco_sel = montaSelect(getBancoBD(),null, "id='banco' name='banco' class='form-control'");
    $presta_conta = montaSelect($prestaContas,$presta_conta, "id='presta_conta' name='presta_conta' class='form-control'");
}else{
    $acao = 'Edição';
    $botao = 'Atualizar';
    $projeto = montaSelect(getProjetos($regiao_bd),$projeto_bd, "id='projeto' name='projeto' class='form-control'");
    $banco_sel = montaSelect(getBancoBD(),$id_lista, "id='banco' name='banco' class='form-control'");
    $presta_conta = montaSelect($prestaContas,$presta_conta, "id='presta_conta' name='presta_conta' class='form-control'");
}

//dados para voltar no index com select preenchido
if($regiao_selecionada == ''){
    $_SESSION['regiao_select'];    
//    session_write_close();
}else{
    $_SESSION['regiao_select'] = $regiao_selecionada;    
//    session_write_close();
}

$caminho = (!empty($_REQUEST['caminho']))?$_REQUEST['caminho']:0;

$breadcrumb_pages_array[0] = array("Gestão de Bancos" => "index.php");
$breadcrumb_pages_array[1] = array("Gestão de Bancos" => "index.php", "Detalhe do Banco" => "javascript:;");

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"$botao de Banco");
$breadcrumb_pages = $breadcrumb_pages_array[$caminho];

$breadcrumb_attr = array("Detalhe do Banco" => "class='link-sem-get' data-banco='$banco_fin' data-form='form1' data-url='detalhes_banco.php'");
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: <?=$botao?> de Banco</title>
        
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
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro</h2></div>                                                                                      
            <form action="" method="post" id="form1" class="form-horizontal top-margin1" enctype="multipart/form-data">
                
                <!--resposta de algum metodo realizado-->
                <div class="alert alert-dismissable alert-<?php echo $_SESSION['MESSAGE_TYPE']; ?> msg_cadsuporte">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <p><?php echo $_SESSION['MESSAGE'];
                    session_destroy(); ?></p>
                </div>
                <input type="hidden" id="msg" name="msg" value="<?php echo $_SESSION['MESSAGE']; ?>" />
                <input type="hidden" id="regiao_selecionada" name="regiao_selecionada" value="<?php echo $regiao_selecionada; ?>" />
                <input type="hidden" id="id_banco" name="id_banco" value="<?php echo $row['id_banco']; ?>" />
                
                <fieldset>
                    <legend><?php echo $acao; ?> de Banco</legend>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <div class="form-group">
                                    <label for="assunto" class="col-lg-2 control-label">Local</label>
                                    <div class="col-lg-9 text-left">
                                        <select name='interno' id='select' class="form-control">
                                            <option value='1' <?php echo selected('1', $interno); ?>>INTERNO</option>
                                            <option value='2' <?php echo selected('2', $interno); ?>>EXTERNO</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
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
                                    <label for="mensagem" class="col-lg-2 control-label">Banco</label>
                                    <div class="col-lg-9">
                                        <?php echo $banco_sel; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-lg-2 control-label">Nome para exibição</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control validate[required]" id="nome" name="nome" placeholder="Ex: Real - Educação" value="<?php echo $row['nome']; ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-lg-2 control-label">Localidade</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" id="localidade" name="localidade" placeholder="Ex: Mauá, Itaboraí" value="<?php echo $row['localidade']; ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-lg-2 control-label">Endereço</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" id="endereco" name="endereco" value="<?php echo $row['endereco']; ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-lg-2 control-label">Conta Corrente</label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control validate[required,custom[onlyNumberSp]]" id="conta" name="conta" placeholder="Somente números" value="<?php echo $row['conta']; ?>" />
                                    </div>
                                    <label for="mensagem" class="col-lg-1 control-label">Gerente</label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" id="gerente" name="gerente" value="<?php echo $row['gerente']; ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-lg-2 control-label">Agência</label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control validate[required,custom[onlyNumberSp]]" id="agencia" name="agencia" placeholder="Somente números" value="<?php echo $row['agencia']; ?>" />
                                    </div>
                                    <label for="mensagem" class="col-lg-1 control-label">Telefone</label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" id="tel" name="tel" value="<?php echo $row['tel']; ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-lg-2 control-label">Prestar Contas</label>
                                    <div class="col-lg-9">
                                        <?php echo $presta_conta ?>
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