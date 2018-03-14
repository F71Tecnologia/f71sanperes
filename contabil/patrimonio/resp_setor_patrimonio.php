<?php 
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = 'login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/global.php");
 
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$id = (isset($_REQUEST['id'])) ? $_REQUEST['id'] : 1;
$id_user = $_COOKIE['logado'];
$regiao = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$id_projeto = $_REQUEST['projeto'];
$data = date('d/m/Y');



if ($id == 2){

    $id_regiao = $_REQUEST['regiao'];
    $id_clt = $_REQUEST['id_clt'];
    $nome = $_REQUEST['nome'];
    $data = date('Y-m-d');
    $id_projeto = $_REQUEST['projeto'];
    
    mysql_query("INSERT INTO responsavel_setor(id_regiao,id_projeto,id_clt,data) values 
    ('$regiao','$id_projeto','$id_clt','$data')") or die 
    ("Erro <br><br>".mysql_error());

    print "
    <script>
    alert (\"Funcionário cadastrado!\"); 
    location.href=\"resp_setor_patrimonio.php?id=1\"
    </script>";exit;
    
} 
//$resultado_nome = mysql_query("SELECT id_clt,nome FROM rh_clt where id_regiao = '$regiao' ORDER BY nome");   
$result_local = mysql_query("SELECT * FROM regioes 
                            WHERE id_regiao = '$regiao'");

$row_local = mysql_fetch_array($result_local);

$nome_pagina = 'Cadastrar Responsável pelo Setor';
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"38", "area"=>"Contabilidade", "id_form"=>"form1", "ativo"=>$nome_pagina);
$breadcrumb_pages = array("Controle de Patrimônio" => "index.php");

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
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="page-header box-contabil-header"><h2><span class="fa fa-bar-chart"></span> - Contabilidade<small> - <?= $nome_pagina ?></small></h2></div>
            
            <form action="" method="post" class="form-horizontal" enctype="multipart/form-data" name="form1" onsubmit="return validaForm()" id="form1">
                <div class="panel panel-default">
                    <div class="panel-heading">DADOS DO PATRIMÔNIO LOCAL</div>
                    <div class="panel-body">
                        <legend><?=$row_local['regiao']." ".$data?></legend>
                       
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Projeto:</label>
                            <div class="col-xs-4">
                                <?php echo montaSelect(GlobalClass::carregaProjetosByRegiao($usuario['id_regiao']), null, "id='projeto' name='projeto' class='validate[custom[select]] form-control'") ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Nome:</label>
                            <div class="col-xs-9"><input type="text" class="form-control validate[required]" name="nome" id="buscanome"></div>
                            <input type="hidden" id="regiao" name="regiao" value="<?=$row_local['id_regiao']?>">
<!--                            <input type="hidden" id="id_projeto" name="projeto" value="<?=$row_local['id_projeto']?>">-->
                        </div>
                        <hr>
                        <div id="conteudo">
                            <!-- Aqui vem a consulta dos nomes -->
                        </div>
                       
                    <div class="panel-footer text-center">
                        <input type="hidden" value="2" name="id">
                        <input type="hidden" value="<?=$regiao?>" name="regiao">
                        <button type="submit" class="btn btn-primary" name="gravar" id="gravar" value="GRAVAR"><i class="fa fa-save"></i> GRAVAR</button>
                    </div>
                </div>
            </form>
            <?php include('../../template/footer.php'); ?>
        </div>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript"></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(document).ready(function(){
                
                
    
    
 

                $("#buscanome").keyup(function(){
                    var vnome = $("#buscanome").val();
                    var regiao = $("#regiao").val();
                    var id_projeto = $("#projeto").val();
                    console.log(regiao);
                //alert(vnome);
                    $.post("consulta.php",
                           {nome:vnome, regiao:regiao, projeto:id_projeto},
                           function(resposta){ 
                        $("#conteudo").html(resposta);
                    });
                });
            });

            $(function(){
                $("#form1").validationEngine({promptPosition : "topRight"});
            });
        </script>
    </body>
</html>