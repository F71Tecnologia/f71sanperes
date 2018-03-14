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
$respo_setor = $_REQUEST['select_resp'];
if ($id == 2){

    $id_regiao = $_REQUEST['regiao'];
    
    $id_projeto = $_REQUEST['projeto'];

    $nome = $_REQUEST['nome'];

    $respo_setor = $_REQUEST['select_resp'];
    
    $descricao = $_REQUEST['descricao'];

    $local = $_REQUEST['local'];

    $data_criado = $_REQUEST['data_criado'];

    $dataCriado = explode("/",$data_criado);
    $d = $dataCriado[0];
    $m = $dataCriado[1];
    $a = $dataCriado[2];

    $data_Criado = $a.'-'.$m.'-'.$d;

    $data = date('Y-m-d');

    mysql_query("INSERT INTO setor_patrimonio(id_regiao,id_projeto,nome,id_respo_setor,descricao,local,data) values 
    ('$regiao','$id_projeto','$nome','$respo_setor','$descricao','$local','$data')") or die 
    ("Erro <br><br>".mysql_error());

    print "
    <script>
    alert (\"Setor cadastrado!\"); 
    location.href=\"setor_patrimonio.php?id=1\"
    </script>";exit;
    
} 

//$resultado_respo = mysql_query("SELECT B.nome, C.id_projeto, B.id_clt, A.id_responsavel_setor FROM responsavel_setor AS A
//                                LEFT JOIN rh_clt AS B ON B.id_clt = A.id_clt
//                                LEFT JOIN projeto AS C ON B.id_projeto = C.id_projeto
//                                WHERE B.id_projeto = '$id_projeto'");

$result_local = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao'");
$row_local = mysql_fetch_array($result_local);

$nome_pagina = 'Cadastro de Setor';
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
                    <div class="panel-heading">Cadastre o Setor</div>
                    <div class="panel-body">
                        <legend><?=$row_local['regiao']." ".$data?></legend>
                          <div class="form-group">
                            <label class="col-xs-2 control-label">Projeto:</label>
                            <div class="col-xs-4">
                                <?php echo montaSelect(GlobalClass::carregaProjetosByRegiao($usuario['id_regiao']), null, "id='id_projeto' name='id_projeto' class='validate[custom[select]] form-control'") ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Nome:</label>
                            <div class="col-xs-9"><input type="text" class="form-control validate[required]" name="nome" id="nome"></div>
                        </div>
<!--                        <div class="form-group">
                            <label class="col-xs-2 control-label">TESTE1:</label>
                            <div class="col-xs-4"><input type="text" class="form-control" name="teste1" id="marca"></div>
                        </div>-->
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Responsavel pelo Setor:</label>
                                <div class="col-xs-4"> 
                                    <select id="select_resp" name="select_resp" class="form-control validate[required]">
                                                                            
                                    </select>
                                </div>
                          </div>
<!--                        <div class="form-group">
                            <label class="col-xs-2 control-label">TESTE2:</label>
                            <div class="col-xs-4"><input type="text" class="form-control" name="teste2" id="nota"></div>
                        </div>-->
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Localização do Setor:</label>
                            <div class="col-xs-9"><input type="text" class="form-control" name="local" id="local"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Observações:</label>
                            <div class="col-xs-9"><input type="text" class="form-control" name="descricao" id="descricao"></div>
                        </div>
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
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(document).ready(function() {                
                $("#form1").validationEngine({promptPosition : "topRight"});
                
                
                $("#id_projeto").change(function(){
                var id_projeto = $("#id_projeto").val();
                
                $.post("consulta_funcionario.php", {id_projeto:id_projeto}, function(resposta){ 
                        $("#select_resp").html(resposta);
                    });
                });
            });
                
            
        </script>
    </body>
</html>