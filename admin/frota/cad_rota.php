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

if(isset($_REQUEST['enviar3'])) { 
    
    $veiculo = $_REQUEST['veiculo'];
    $destino = $_REQUEST['destino3'];
    //$origem = $_REQUEST['regiao3'];
    $origem = $usuario['id_regiao'];
    $responsavel = $_REQUEST['responsavel'];
    $data = $_REQUEST['data3'];
    $data_cad = date('Y-m-d');
    $user_cad = $usuario['id_funcionario'];
    $km = $_REQUEST['km'];

    $data = ConverteData($data);

    mysql_query("INSERT INTO fr_rota (id_carro,id_regiao,id_user,destino,data,kmini,user_cad,data_cad,status_reg) 
    VALUES ('$veiculo', '$origem', '$responsavel', '$destino', '$data', '$km', '$user_cad', '$data_cad', '1')");
    if(mysql_error()){
        $_SESSION['error']['tipo'] = "danger";
        $_SESSION['error']['msg'] = "Erro: " . mysql_errno();
    }
    
    if(empty($_SESSION['error'])){
        $_SESSION['error']['tipo'] = "success";
        $_SESSION['error']['msg'] = "Controle cadastrado com sucesso!";
    }
    
    header("Location: index.php");
    exit;
}

if(isset($_REQUEST['enviar4'])) { 
    
    $rota = $_REQUEST['rota'];
    $km = $_REQUEST['km'];
    $da_ent = date('Y-m-d H:i:s');

    mysql_query("UPDATE fr_rota SET kmfim = '$km', data_ent = '$da_ent', status_reg = '2' where id_rota = '$rota'");
    if(mysql_error()){
        $_SESSION['error']['tipo'] = "danger";
        $_SESSION['error']['msg'] = "Erro: " . mysql_errno();
    }
    
    if(empty($_SESSION['error'])){
        $_SESSION['error']['tipo'] = "success";
        $_SESSION['error']['msg'] = "Entrega realizada com sucesso!";
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
$nome_pagina = "Controle de Rotas";
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
                    <div class="page-header box-admin-header"><h2><?= $icon[2] ?> - ADMINISTRATIVO - <small><?= $nome_pagina ?></small></h2></div>
                    <form action="" method="post" name="form1" enctype='multipart/form-data' class="form-horizontal">
                        <div class="panel panel-warning">
                            <div class="panel-heading text-center text-uppercase"><?= $nome_pagina ?></div>
                            <div class="panel-body">
                                <div class="col-md-offset-1 col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <label class="control-label">Veículo</label>
                                            <?= montaSelect($arrayCarros, null, 'name="veiculo" class="form-control" id="veiculo"') ?>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label">Km Atual</label>
                                            <input type="text" name="km" id="km" class="form-control validate[required]">
                                        </div>
                                    </div>
                                    <div class="form-group">
<!--                                        <div class="col-md-6">
                                            <label class="control-label">Local de Origem</label>
                                            <?= montaSelect(getRegioes(), null, 'name="regiao3" id="regiao3" class="form-control validate[required]"') ?>
                                        </div>-->
                                        <div class="col-md-12">
                                            <label class="control-label">Destino</label>
                                            <input type="text" name="destino3" id="destino3" class="form-control validate[required]">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <label class="control-label">Responsável</label>
                                            <?= montaSelect($arrayFunc, null, 'name="responsavel" class="form-control" id="responsavel"') ?>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label">Data</label>
                                            <input type="text" name="data3" id="data3" class="data form-control validate[required]">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <button class="btn btn-primary" type="submit" name="enviar3" id="enviar3" value="Gravar"><i class="fa fa-save"></i> Salvar</button>
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
    </body>
</html>