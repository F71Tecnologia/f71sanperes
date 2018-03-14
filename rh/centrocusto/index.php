<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../classes/CentroCustoClass.php');
include('../../funcoes.php');
include('../../wfunction.php');

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$centrocusto = new CentroCusto();
$global = new GlobalClass();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE�ALHO (TROCA DE MASTER E DE REGI�ES)

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>"Gest�o de Centro de Custo");

$filtro = false;

if ((isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar']))){
    $filtro = true;
    $regiao = $_REQUEST['regiao'];
    $pesquisa = $_REQUEST['pesquisa'];
    
    $qry = $centrocusto->getCentroCusto($regiao, $pesquisa);
    $tot = mysql_num_rows($qry);
}

if(isset($_REQUEST['method']) && $_REQUEST['method'] != ""){
    $retorno = array("status" => 0);
    
    if($_REQUEST['method'] == 'exclusao'){
        $centrocusto->delCentroCusto();
        
        $retorno = array(
            "status" => 1
        );
    }
    
    echo json_encode($retorno);
    exit();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Gest�o de Centro de Custo</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <!--link href="../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css"-->
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        
        <div class="container">
            
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Centro de Custo</small></h2></div>

<!--                     Nav tabs 
                    <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
                        <li role="presentation" class="active"><a href="#avisos" role="tab" data-toggle="tab">Avisos</a></li>
                        <li role="presentation"><a href="#lista" role="tab" data-toggle="tab">Lista de Funcion�rios</a></li>
                        <li role="presentation"><a href="#relatorio" role="tab" data-toggle="tab">Relat�rio de F�rias</a></li>
                    </ul>-->

                    <!--resposta de algum metodo realizado-->
                    <?php echo $global->getResposta($_SESSION['MESSAGE_TYPE'], $_SESSION['MESSAGE']); ?>

                    <!-- Tab panes -->
                    <div class="tab-content">                        
                        <div role="tabpanel" class="tab-pane active" id="lista">

                            <form class="form-horizontal" role="form" id="form-lista" method="post" autocomplete="off">
                                <input type="hidden" name="home" id="home" value="" />
                                <input type="hidden" name="regiao" id="regiao" value="<?= $id_regiao ?>">
                                <input type="hidden" name="id_clt" id="id_clt" value="">
                                <div class="panel panel-default hidden-print">
                                    <div class="panel-body">
                                        
                                        <div class="form-group">
                                            <label for="categoria_lista" class="col-lg-2 control-label">Regi�o:</label>
                                            <div class="col-lg-9">                                                
                                                <?php echo montaSelect(getRegioes(),$regiao, "id='regiao' name='regiao' class='form-control'"); ?>                                                
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="nome_centrocusto" class="col-lg-2 control-label">Filtro:</label>
                                            <div class="col-lg-9"><input type="text" name="pesquisa" id="pesquisa" class="form-control" placeholder="Nome do Centro de Custo" value="<?php echo $pesquisa; ?>"></div>
                                        </div>
                                    
                                    </div><!-- /.panel-body -->
                                    
                                    <div class="panel-footer text-right">
                                        <?php if($filtro){ ?>
                                        <a href="form.php" class="btn btn-success">Cadastrar</a>
                                        <?php } ?>
                                        <input type="submit" value="Consultar" id="submit-lista" name="filtrar" class="btn btn-primary">
                                    </div>

                                </div><!-- /.panel -->
                                
                                <?php 
                                if($filtro){
                                    if($tot > 0){
                                ?>
                                <table class="table table-striped table-hover table-condensed table-bordered">
                                    <thead>
                                        <tr class="bg-primary valign-middle">
                                            <th class="text-center" style="width:5%;">COD</th>
                                            <th style="width:40%;">NOME</th>
                                            <th style="width:10%;">&emsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while($res = mysql_fetch_assoc($qry)){
                                        ?>
                                        <tr class="valign-middle centrocusto_<?php echo $res['id_centro_custo']; ?>">
                                            <td><?php echo $res['id_centro_custo']; ?></td>
                                            <td><?php echo $res['nome']; ?></td>
                                            <td class="text-center">
<!--                                                <a href="javascript:;" class="bt-image" data-type="visualizar" data-key="<?php echo $res['id_centro_custo']; ?>">
                                                    <i class="tooo btn btn-xs btn-primary fa fa-search" data-toggle="tooltip" data-placement="top" title="Visualizar"></i>
                                                </a>-->
                                                <a href="form.php?id=<?php echo $res['id_centro_custo']; ?>" data-key="<?php echo $res['id_centro_custo']; ?>" data-type="editar" class="bt-image">
                                                    <i data-type="visualizar" class="tooo btn btn-xs btn-warning fa fa-edit" data-toggle="tooltip" data-placement="top" title="Editar"></i>
                                                </a>
                                                <a href="javascript:;" class="bt-image" data-type="excluir" data-key="<?php echo $res['id_centro_custo']; ?>" data-nome="<?php echo $res['nome']; ?>">
                                                    <i class="tooo btn btn-xs btn-danger fa fa-trash-o" data-toggle="tooltip" data-placement="top" title="Excluir"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                        } ?>
                                    </tbody>
                                </table>
                                
                                <?php }else{ ?>
                                <div class="alert alert-danger" role="alert">
                                    Nenhum cadastrado encontrado
                                </div>
                                <?php }
                                } ?>
                                
                            </form>

                        </div><!-- /#lista -->                       
                    </div>

                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->
            
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.container -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
                
        <script>
            $(".bt-image").on("click", function() {
                var action = $(this).data("type");
                var id = $(this).data("key");
                var nome = $(this).data("nome");
                
                if(action === "excluir"){
                    bootConfirm(
                        "Deseja <strong>DELETAR</strong> o Centro de Custo "+nome+"?",
                        'Deletar Centro de Custo',
                        function(dialog){
                            if(dialog == true){                                
                                $.ajax({
                                    url: "index.php",
                                    type: "POST",
                                    dataType: "json",
                                    data: {
                                        method: "exclusao",
                                        id: id
                                    },
                                    success: function(data) {
                                        if(data.status == "1"){
                                            $('.centrocusto_'+id).remove();
                                        }
                                    }
                                });
                            }
                        },
                        'danger'
                    );
                }
            });
        </script>
    </body>
</html>