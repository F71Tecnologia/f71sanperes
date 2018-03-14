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

if(isset($_REQUEST['enviar4'])) { 
    
    $veiculo2 = $_REQUEST['veiculo2'];
    $rota = $_REQUEST['rota'];
    $tipo = $_REQUEST['tipo'];
    $local = $_REQUEST['local'];
    $infrator = $_REQUEST['infrator'];
    $data4 = $_REQUEST['data4'];
    $cnh = $_REQUEST['cnh'];
    $regiao = $usuario['id_regiao'];
    $valor = str_replace(',','.',str_replace('.','',$_REQUEST['valor']));

    $data = ConverteData($data4);
    
    mysql_query("INSERT INTO fr_multa (id_carro,id_rota,id_user,tipo,local,data,cnh,user_cad,data_cad,status_reg,id_regiao,valor)
    VALUES ('$veiculo2', '$rota', '$infrator', '$tipo', '$local', '$data', '$cnh', '$user', '$data_cad', '1','$regiao','$valor')");
    if(mysql_error()){
        $_SESSION['error']['tipo'] = "danger";
        $_SESSION['error']['msg'] = "Erro: " . mysql_errno();
    }
    
    if(empty($_SESSION['error'])){
        $_SESSION['error']['tipo'] = "success";
        $_SESSION['error']['msg'] = "Multa cadastrada com sucesso!";
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

$RE_rota = mysql_query("SELECT A.*, B.regiao FROM fr_rota A LEFT JOIN regioes B ON (A.id_regiao = B.id_regiao) WHERE A.id_regiao = '{$usuario['id_regiao']}'");
$arrayRota[] = 'SELECIONE';
while($row = mysql_fetch_assoc($RE_rota)){
    $arrayRota[$row['id_rota']] = "{$row['id_rota']} - {$row['regiao']} - {$row['destino']}";
}



$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$nome_pagina = "Controle de Multas";
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
                        <div class="panel panel-danger">
                            <div class="panel-heading text-center text-uppercase"><?= $nome_pagina ?></div>
                            <div class="panel-body">
                                <div class="col-md-offset-1 col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <label class="control-label">Veículo</label>
                                            <?= montaSelect($arrayCarros, null, 'name="veiculo2" class="form-control" id="veiculo2"') ?>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label">Rota</label>
                                            <?= montaSelect($arrayRota, null, 'name="rota" class="form-control" id="rota"') ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <label class="control-label">Tipo de Multa</label>
                                            <input type="text" name="tipo" id="tipo" class="form-control validate[required]">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label">Local da Multa</label>
                                            <input type="text" name="local" id="local" class="form-control validate[required]">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <label class="control-label">Real Infrator</label>
                                            <?= montaSelect($arrayFunc, null, 'name="infrator" class="form-control" id="infrator"') ?>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label">Data</label>
                                            <input type="text" name="data4" id="data4" class="data form-control validate[required]">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <label class="control-label">CNH</label>
                                            <input type="text" name="cnh" id="cnh" class="form-control validate[required]">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label">Valor</label>
                                            <input type="text" name="valor" id="valor" class="valor form-control validate[required]">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <button class="btn btn-primary" type="submit" name="enviar4" id="enviar4" value="Gravar"><i class="fa fa-save"></i> Salvar</button>
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
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>s
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(function() {
                
                $('#placa').mask('aaa-9999');
                $(".valor").maskMoney({allowNegative: true, thousands:'.', decimal:','});
            });
        </script>
    </body>
</html>