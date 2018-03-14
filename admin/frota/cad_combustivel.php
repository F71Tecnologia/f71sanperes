<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/regiao.php");
include("../../classes/BotoesClass.php");

$usuario = carregaUsuario();
$botoes = new BotoesClass($dadosHeader['defaultPath'],$dadosHeader['fullRootPath']);
$icon = $botoes->iconsModulos;

if(isset($_REQUEST['enviar2'])) { 
    
    $interno = $_REQUEST['interno']; //SIM OU NÃO ( 1 OU 2 )
    $veiculo = $_REQUEST['veiculo'];
    $carro = $_REQUEST['veiculo2'];
    $placa = $_REQUEST['placa'];
    $funcionario = $_REQUEST['funcionario']; //INTERNO OU NÃO ( 1 OU 2 )
    $userw = $_REQUEST['user'];
    $nome = $_REQUEST['nome'];
    $rg = $_REQUEST['rg'];
    $destino = $_REQUEST['destino'];
    //$origem = $_REQUEST['regiao'];
    $origem = $usuario['id_regiao'];
    $kmatual = $_REQUEST['kmatual'];
//    $valor = str_replace(',','.',str_replace('.','',$_REQUEST['valor']));
    $dataT = $_REQUEST['dataT'];
    $dataT = ConverteData($dataT);

    mysql_query("INSERT INTO fr_combustivel(id_carro,id_user,id_regiao,funcionario,nome,rg,interno,carro,placa,data,destino,user_cad,data_cad,status_reg,kmatual) 
    VALUES                                 ('$veiculo', '$userw', '$origem', '$funcionario', '$nome', '$rg', '$interno', '$carro', '$placa', '$dataT', '$destino','$user', '$data_cad', '1','$kmatual')");
    if(mysql_error()){
        $_SESSION['error']['tipo'] = "danger";
        $_SESSION['error']['msg'] = "Erro: " . mysql_errno();
    }
	
    if(empty($_SESSION['error'])){
        $_SESSION['error']['tipo'] = "success";
        $_SESSION['error']['msg'] = "Combustível solicitado com sucesso!";
    }
    
    header("Location: index.php");
    exit;
}

$RE_carros = mysql_query("SELECT * FROM fr_carro WHERE id_regiao IN ({$usuario['id_regiao']})");
$arrayCarros[] = 'SELECIONE';
while($row = mysql_fetch_assoc($RE_carros)){
    $arrayCarros[$row['id_carro']] = $row['marca'] . " " . $row['modelo'] . " " . $row['placa'];
}

$RE_func = mysql_query("SELECT * FROM funcionario WHERE status_reg = 1 ORDER BY nome");
$arrayFunc[] = 'SELECIONE';
while($row = mysql_fetch_assoc($RE_func)){
    $arrayFunc[$row['id_funcionario']] = $row['nome'];
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$nome_pagina = "Solicitação de Combustível";
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"2", "area"=>"Administrativo", "id_form"=>"form1", "ativo"=>$nome_pagina);
$breadcrumb_pages = array("Gestão da Frota" => "index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">

            <div class="row">

                <div class="col-sm-12">
                    <div class="page-header box-admin-header"><h2><?=$icon[2]?> - ADMINISTRATIVO - <small><?= $nome_pagina ?></small></h2></div>
                    <form action="" method="post" name="form1" enctype='multipart/form-data' class="form-horizontal">
                        <div class="panel panel-success">
                            <div class="panel-heading text-center text-uppercase"><?= $nome_pagina ?></div>
                            <div class="panel-body">
                                <div class="col-md-offset-1 col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <label class="col-md-12 control-label text-left no-padding-hr">Interno</label>
                                            <div class="col-md-6 no-padding-hr">
                                                <div class="input-group">
                                                    <label class="input-group-addon pointer" for="interno1"><input type="radio" name="interno" id="interno1" class="interno" value="1"></label>
                                                    <label class="form-control pointer" for="interno1">Sim</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 no-padding-hr">
                                                <div class="input-group">
                                                    <label class="input-group-addon pointer" for="interno2"><input type="radio" name="interno" class="interno" id="interno2" value="2"></label>
                                                    <label class="form-control pointer" for="interno2">Não</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 veiculo" style="display: none;">
                                            <label class="control-label">Veículo</label>
                                            <?= montaSelect($arrayCarros, null, 'name="veiculo" class="form-control" id="veiculo"') ?>
                                        </div>
                                        <div class="col-md-3 spa1" style="display: none;">
                                            <label class="control-label">Veículo</label>
                                            <input name="veiculo2" type="text" class="form-control" id="veiculo2">
                                        </div>
                                        <div class="col-md-3 spa1" style="display: none;">
                                            <label class="control-label">Placa</label>
                                            <input name="placa" type="text" class="form-control" id="placa">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <label class="col-md-12 control-label text-left no-padding-hr">Funcionário</label>
                                            <div class="col-md-6 no-padding-hr">
                                                <div class="input-group">
                                                    <label class="input-group-addon pointer" for="interno1"><input type="radio" name="funcionario" id="funcionario1" class="funcionario" value="1"></label>
                                                    <label class="form-control pointer" for="funcionario1">Sim</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 no-padding-hr">
                                                <div class="input-group">
                                                    <label class="input-group-addon pointer" for="interno2"><input type="radio" name="funcionario" id="funcionario2" class="funcionario" value="2"></label>
                                                    <label class="form-control pointer" for="funcionario2">Não</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 user" style="display: none;">
                                            <label class="control-label">Nome</label>
                                            <?= montaSelect($arrayFunc, null, 'name="user" class="form-control" id="user"') ?>
                                        </div>
                                        <div class="col-md-3 spa2" style="display: none;">
                                            <label class="control-label">Nome</label>
                                            <input name="nome" type="text" class="form-control" id="nome">
                                        </div>
                                        <div class="col-md-3 spa2" style="display: none;">
                                            <label class="control-label">RG</label>
                                            <input name="rg" type="text" class="form-control" id="rg">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <label class="control-label">Km Atual</label>
                                            <input type="text" name="kmatual" id="kmatual" class="form-control validate[required]">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label">Destino</label>
                                            <input type="text" name="destino" id="destino" class="form-control validate[required]">
                                        </div>
                                    </div>
                                    <div class="form-group">
<!--                                        <div class="col-md-6">
                                            <label class="control-label">Local de Origem</label>
                                            <?= montaSelect(getRegioes(), null, 'name="regiao" id="regiao" class="form-control validate[required]"') ?>
                                        </div>-->
                                        <div class="col-md-6">
                                            <label class="control-label">Data</label>
                                            <input type="text" name="dataT" id="dataT" class="data form-control validate[required]">
                                        </div>
<!--                                        <div class="col-md-6">
                                            <label class="control-label">Valor</label>
                                            <input type="text" name="valor" id="valor" class="valor form-control validate[required]">
                                        </div>-->
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <button class="btn btn-primary" type="submit" name="enviar2" id="enviar2" value="Gravar"><i class="fa fa-save"></i> Salvar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php include("../../template/footer.php"); ?>
        </div>

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(function() {
                $('body').on('change', '.interno', function(){
                    if($(this).val() == 1){
                        $('.veiculo').show();
                        $('.spa1').hide();
                    } else if($(this).val() == 2){
                        $('.veiculo').hide();
                        $('.spa1').show();
                    }
                });
                $('body').on('change', '.funcionario', function(){
                    if($(this).val() == 1){
                        $('.user').show();
                        $('.spa2').hide();
                    } else if($(this).val() == 2){
                        $('.user').hide();
                        $('.spa2').show();
                    }
                });
                
                $('#placa').mask('aaa-9999');
                $(".valor").maskMoney({allowNegative: true, thousands:'.', decimal:','});
            });
        </script>
    </body>
</html>