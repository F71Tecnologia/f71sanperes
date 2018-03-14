<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = 'login.php?entre=true';</script>";
}

include("conn.php");
include("wfunction.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE�ALHO (TROCA DE MASTER E DE REGI�ES)

$id = (isset($_REQUEST['id'])) ? $_REQUEST['id'] : 1;
$id_user = $_COOKIE['logado'];
$regiao = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];

$data = date('d/m/Y');

if ($id == 2){

    $id_regiao = $_REQUEST['regiao'];

    $numero = $_REQUEST['numero'];
    $numero = strtoupper($numero);

    $nome = $_REQUEST['nome'];
    $nome = strtoupper($nome);

    $marca = $_REQUEST['marca'];
    $marca = strtoupper($marca);

    $descricao = $_REQUEST['descricao'];
    $descricao = strtoupper($descricao);

    $local = $_REQUEST['local'];
    $local = strtoupper($local);

    $valor = $_REQUEST['valor'];
    $data_compra = $_REQUEST['data_compra'];

    $dataCompra = explode("/",$data_compra);
    $d = $dataCompra[0];
    $m = $dataCompra[1];
    $a = $dataCompra[2];

    $data_compra = $a.'-'.$m.'-'.$d;

    $nota = $_REQUEST['nota'];
    $nota = strtoupper($nota);

    $valor = str_replace(".","", $valor);

    $data = date('Y-m-d');

    $arquivo = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;

    if(empty($_REQUEST['foto'])){
        $foto_base = "0";

        mysql_query("INSERT INTO patrimonio(id_regiao,numero,nome,marca,descricao,local,valor,data,foto,data_compra, nota) values 
        ('$regiao','$numero','$nome','$marca','$descricao','$local','$valor','$data','$foto_base','$data_compra','$nota' )") or die 
        ("Erro <br><br>".mysql_error());

    } else {
        $foto_base = "1";

        if($arquivo[type] != "image/x-png" && $arquivo[type] != "image/pjpeg" && $arquivo[type] != "image/gif" && $arquivo[type] != "image/jpe") { 

            print "<center>
            <hr><font size=2><b>
            Tipo de arquivo n�o permitido, os �nicos padr�es permitidos s�o .gif, .jpg , .jpeg ou .png<br>
            $arquivo[type] <br><br>
            <a href='patrimonio.php?id=1&regiao=$regiao'>Voltar</a>
            </b></font>
            "; 

            exit; 

        }  else {

            $arr_basename = explode(".",$arquivo['name']); 
            $file_type = $arr_basename[1]; 

            if($file_type == "gif"){
                $tipo_name =".gif"; 
            }  if($file_type == "jpg" or $arquivo[type] == "jpeg"){
                $tipo_name =".jpg"; 
            }  if($file_type == "png") { 
                $tipo_name =".png"; 
            } 

            mysql_query("INSERT INTO patrimonio(id_regiao,numero,nome,marca,descricao,local,valor,data,foto,data_compra, nota) values 
            ('$regiao','$numero','$nome','$marca','$descricao','$local','$valor','$data','$tipo_name', '$data_compra','$nota')") or die 
            ("Erro <br><br>".mysql_error());

            $id_insert = mysql_insert_id();

            // Resolvendo o nome e para onde o arquivo ser� movido
            $diretorio = "patrimonio/";

            $nome_tmp = $regiao."patrimonio".$id_insert.$tipo_name;
            $nome_arquivo = "$diretorio$nome_tmp" ;

            move_uploaded_file($arquivo['tmp_name'], $nome_arquivo ) or die ("Erro ao enviar o Arquivo: $nome_arquivo");

        }
    }


    print "
    <script>
    alert (\"Patrimonio cadastrado!\"); 
    location.href=\"patrimonio.php?id=1&regiao=$regiao\"
    </script>";exit;
    
} 
    
$result_local = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao'");
$row_local = mysql_fetch_array($result_local);

$nome_pagina = 'CADASTRO DE PATRIM�NIO';
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"38", "area"=>"Contabilidade", "id_form"=>"form1", "ativo"=>$nome_pagina);
$breadcrumb_pages = array("CONTROLE DE PATRIM�NIO" => "contabil/patrimonio/");

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link href="favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="resources/css/main.css" rel="stylesheet" media="screen">
        <link href="resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("template/navbar_default.php"); ?>
        <div class="container">
            <div class="page-header box-contabil-header"><h2><span class="fa fa-bar-chart"></span> - Contabilidade<small> - <?= $nome_pagina ?></small></h2></div>
            
            <form action="" method="post" class="form-horizontal" enctype="multipart/form-data" name="form1" onsubmit="return validaForm()" id="form1">
                <div class="panel panel-default">
                    <div class="panel-heading">DADOS DO PATRIM�NIO LOCAL</div>
                    <div class="panel-body">
                        <legend><?=$row_local['regiao']." ".$data?></legend>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Numero:</label>
                            <div class="col-xs-4"><input type="text" class="form-control validate[required]" name="numero" id="numero"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Nome:</label>
                            <div class="col-xs-9"><input type="text" class="form-control validate[required]" name="nome" id="nome"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Marca / Modelo:</label>
                            <div class="col-xs-4"><input type="text" class="form-control" name="marca" id="marca"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Data da Compra:</label>
                            <div class="col-xs-4"><input type="text" class="form-control data" name="data_compra" id="data_compra"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Nota Fiscal:</label>
                            <div class="col-xs-4"><input type="text" class="form-control" name="nota" id="nota"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Localiza��o:</label>
                            <div class="col-xs-9"><input type="text" class="form-control" name="local" id="local"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Valor Estimado:</label>
                            <div class="col-xs-4"><input type="text" class="form-control valor validate[required]" name="valor" id="valor"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Observa��es:</label>
                            <div class="col-xs-9"><input type="text" class="form-control" name="descricao" id="descricao"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Foto:</label>
                            <div class="col-xs-4 text-left margin_t5"><input type="checkbox" class="" name="foto" id="foto" value="1" onclick="document.all.tablearquivo.style.display = (document.all.tablearquivo.style.display == 'none') ? '' : 'none' ;"></div>
                        </div>
                        <div class="form-group" id="tablearquivo" style="display:none">
                            <label class="col-xs-2 control-label">SELECIONE::</label>
                            <div class="col-xs-4"><input name="arquivo" class="form-control" type="file" id="arquivo" size="60"></div>
                        </div>
                    </div>
                    <div class="panel-footer text-center">
                        <input type="hidden" value="2" name="id">
                        <input type="hidden" value="<?=$regiao?>" name="regiao">
                        <button type="submit" class="btn btn-primary" name="gravar" id="gravar" value="GRAVAR"><i class="fa fa-save"></i> GRAVAR</button>
                    </div>
                </div>
            </form>
            <?php include('template/footer.php'); ?>
        </div>
        
        <script src="js/jquery-1.10.2.min.js"></script>
        <script src="js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="resources/js/bootstrap.min.js"></script>
        <script src="resources/js/bootstrap-dialog.min.js"></script>
        <script src="js/jquery.validationEngine-2.6.js"></script>
        <script src="js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="js/jquery.maskedinput-1.3.1.js"></script>
        <script src="js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="resources/dropzone/dropzone.js"></script>
        <script src="resources/js/main.js"></script>
        <script src="js/global.js"></script>
        <script src="js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(function() {                
                $("#form1").validationEngine({promptPosition : "topRight"});
            });
        </script>
    </body>
</html>