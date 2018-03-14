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

if(isset($_REQUEST['enviar'])) { 
    
    $marca = $_REQUEST['marca'];
    $modelo = $_REQUEST['modelo'];
    $ano = $_REQUEST['ano'];
    $fab = $_REQUEST['fab'];
    $placa = $_REQUEST['placa'];
    $apolice = $_REQUEST['apolice'];
    $telefone = $_REQUEST['telefone'];
    //$regiao = $_REQUEST['regiao'];
    $regiao = $usuario['id_regiao'];

    $arquivo = isset($_FILES['foto']) ? $_FILES['foto'] : FALSE;

    //AQUI TEM FOTO
    if($arquivo['error'] == 0){
        //aki a imagem nao corresponde com as extenções especificadas
        if($arquivo[type] != "image/png" && $arquivo[type] != "image/x-png" && $arquivo[type] != "image/pjpeg" && $arquivo[type] != "image/gif" && $arquivo[type] != "image/jpe") {     
            $_SESSION['error']['tipo'] = "danger";
            $_SESSION['error']['msg'] = "Tipo de arquivo não permitido, os únicos padrões permitidos são .gif, .jpg , .jpeg ou .png";

        //aqui o arquivo é realente de imagem e vai ser carregado para o servidor
        } else {  

            $arr_basename = explode(".",$arquivo['name']); 
            $file_type = $arr_basename[1]; 

            if($file_type == "gif"){
                $tipo_name =".gif"; 
            }elseif($file_type == "jpg" or $arquivo[type] == "jpeg"){
                $tipo_name =".jpg"; 
            }elseif($file_type == "png") { 
                $tipo_name =".png"; 
            } 

            $foto = $tipo_name;

            mysql_query("INSERT INTO fr_carro (id_regiao,marca,modelo,ano,fab,placa,apolice,telefone,foto,user,data_cad)
            VALUES ('$regiao', '$marca', '$modelo', '$ano', '$fab', '$placa', '$apolice', '$telefone', '$foto', '$user', '$data_cad')");
            if(mysql_error()){
                $_SESSION['error']['tipo'] = "danger";
                $_SESSION['error']['msg'] = "Erro: " . mysql_errno();
            }

            $id_insert = mysql_insert_id();

            // Resolvendo o nome e para onde o arquivo será movido
            $diretorio = "fotos/";

            $nome_tmp = "carro".$id_insert.$tipo_name;
            $nome_arquivo = "$diretorio$nome_tmp" ;

            move_uploaded_file($arquivo['tmp_name'], $nome_arquivo ) or die ("Erro ao enviar o Arquivo: $nome_arquivo");
            //aqui fecha o IF que verificar se o arquivo tem a extenção especificada

        } 

    }else{
        mysql_query("INSERT INTO fr_carro (id_regiao,marca,modelo,ano,fab,placa,apolice,telefone,user,data_cad)
        VALUES ('$regiao', '$marca', '$modelo', '$ano', '$fab', '$placa', '$apolice', '$telefone', '$user', '$data_cad')");
        if(mysql_error()){
            $_SESSION['error']['tipo'] = "danger";
            $_SESSION['error']['msg'] = "Erro: " . mysql_errno();
        }
    }
    
    if(empty($_SESSION['error'])){
        $_SESSION['error']['tipo'] = "success";
        $_SESSION['error']['msg'] = "Veiculo cadastrado com sucesso!";
    }
    
    header("Location: index.php");
    exit;
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$nome_pagina = "Cadastro de Veiculos";
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
                        <div class="panel panel-info">
                            <div class="panel-heading text-center text-uppercase"><?= $nome_pagina ?></div>
                            <div class="panel-body">
                                <div class="col-md-offset-1 col-md-10">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <label class="control-label">Marca</label>
                                            <input type="text" name="marca" id="marca" class="form-control validate[required]">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label">Modelo</label>
                                            <input type="text" name="modelo" id="modelo" class="form-control validate[required]">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-4">
                                            <label class="control-label">Ano</label>
                                            <?= montaSelect(anosArray(2007), null, 'name="ano" id="ano" class="form-control validate[required]"') ?>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="control-label">Fabricação</label>
                                            <?= montaSelect(anosArray(2007), null, 'name="fab" id="fab" class="form-control validate[required]"') ?>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="control-label">Placa</label>
                                            <input type="text" name="placa" id="placa" class="form-control validate[required]">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <label class="control-label">Apólice de Seguro</label>
                                            <input type="text" name="apolice" id="apolice" class="form-control validate[required]">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label">Telefone Seguro</label>
                                            <input type="text" name="telefone" id="telefone" class="form-control validate[required]">
                                        </div>
                                    </div>
                                    <div class="form-group">
<!--                                        <div class="col-md-6">
                                            <label class="control-label">Local de Origem</label>
                                            <?= montaSelect(getRegioes(), null, 'name="regiao" id="regiao" class="form-control validate[required]"') ?>
                                        </div>-->
                                        <div class="col-md-6">
                                            <label class="control-label">Foto</label>
                                            <input type="file" name="foto" id="foto" class="form-control validate[required]">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <button class="btn btn-primary" type="submit" name="enviar" id="enviar" value="Gravar"><i class="fa fa-save"></i> Salvar</button>
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
                $('#placa').mask('aaa-9999');
                $('#telefone').mask('(99) 9999-9999?9');
            });
        </script>
        
    </body>
</html>