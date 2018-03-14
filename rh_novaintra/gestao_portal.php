<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}
include "../classes/LogClass.php";
$log = new Log();

include("../conn.php");
include("../classes/funcionario.php");
include("../classes_permissoes/regioes.class.php");
include("../classes_permissoes/acoes.class.php");
include("../wfunction.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$optRegiao = getRegioes();
$ACOES = new Acoes();

$id_usuario = $_COOKIE['logado'];

if($_REQUEST['method'] == 'editar_mensagem'){
    $editar_titulo = utf8_decode($_REQUEST['editar_titulo']);
    $editar_mensagem = utf8_decode($_REQUEST['editar_mensagem']);
    $id_portal_aviso = $_REQUEST['id_portal_aviso'];
    $editar_exibicao_ini = implode('-', array_reverse(explode('/',$_REQUEST['editar_exibicao_ini']))) ;
    $editar_exibicao_fim = implode('-', array_reverse(explode('/',$_REQUEST['editar_exibicao_fim'])));
    
    $sql_gestaoPortal = 
    "UPDATE portal_avisos SET 
    id_usuario = '{$id_usuario}', titulo_mensagem = '{$editar_titulo}', texto_mensagem = '{$editar_mensagem}', exibicao_ini  = '{$editar_exibicao_ini}', exibicao_fim = '{$editar_exibicao_fim}'
    WHERE id_portal_avisos = {$id_portal_aviso}";
 
    mysql_query($sql_gestaoPortal);
    $log->gravaLog('Gestão do Portal', "Edição da Mensagem: ID{$id_portal_aviso}");
    
    exit();
}elseif($_REQUEST['method'] == 'excluir_mensagem'){
    $id_portal_aviso = $_REQUEST['id_portal_aviso'];
        
    $sql_gestaoPortal = 
    "UPDATE portal_avisos SET 
    status = 0
    WHERE id_portal_avisos = {$id_portal_aviso}";
 
    mysql_query($sql_gestaoPortal);
    $log->gravaLog('Gestão do Portal', "Exclusão da Mensagem: ID{$id_portal_aviso}");

}


$titulo_mensagem = $_REQUEST['titulo'];
$mensagem_mensagem = $_REQUEST['mensagem'];

$data_cad = date('Y-m-d H:i:s');
$status = $_REQUEST['status_aviso'];

$exibicao_ini = implode('-', array_reverse(explode('/',$_REQUEST['exibicao_ini']))) ;
$exibicao_fim = implode('-', array_reverse(explode('/',$_REQUEST['exibicao_fim'])));




if($_POST['titulo']){    
    $sql_gestaoPortal = "INSERT INTO portal_avisos
    (id_usuario, titulo_mensagem, texto_mensagem, data_cad, exibicao_ini , exibicao_fim) 
    VALUES
    ('{$id_usuario}', '{$titulo_mensagem}', '{$mensagem_mensagem}', '{$data_cad}', '{$exibicao_ini}','{$exibicao_fim}')";
    mysql_query($sql_gestaoPortal);
    
    $log->gravaLog('Gestão do Portal', "Nova Mensagem: ID".  mysql_insert_id());

    header("Location:gestao_portal.php");exit();
}
//if($_POST['editar_titulo']){    
//    $sql_gestaoPortal = "UPADTE portal_avisos SET 
//    (id_usuario, titulo_mensagem, texto_mensagem, exibicao_ini , exibicao_fim) 
//    VALUES
//    ('{$id_usuario}', '{$titulo_mensagem}', '{$mensagem_mensagem}', '{$exibicao_ini}','{$exibicao_fim}')";
//    mysql_query($sql_gestaoPortal);
//    header("Location:gestao_portal.php");exit();
//}


            
 

$sql_editarAviso = "SELECT A.*, B.nome AS nome_clt  FROM portal_avisos AS A
    LEFT JOIN funcionario as B ON (B.id_funcionario = A.id_usuario)
    WHERE A.status = 1 ORDER BY A.data_cad DESC LIMIT 5";
$query_aviso = mysql_query($sql_editarAviso);
$rows = mysql_num_rows($query_aviso);

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Relatório de Admissões");
$breadcrumb_pages = array("Visualizar Projeto" => "../rh/ver.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Mensagens da Extranet</title>
        
        <link href="../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">

    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS  <small> - Mensagens da Extranet</small></h2></div>
            
            <!--<div class="btn btn-warning" id="btn_msg">Nova Mensagem Geral</a></div>-->
<!--            <div style="text-align: right">
            <a href="gestao_portal_editar.php"><div class="btn btn-default" id="editar_aviso">Editar Mensagens <i class="fa fa-pencil-square"></i></div></a>
            </div>-->
            <br>
            
            
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">

                <div class="panel panel-default" id="mensagem_portal">
                    
                    <div class="panel-heading text-bold hidden-print">Nova Mensagem</div>
                    <div class="panel-body">
                        <!--<input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />-->
                        <div class="form-group">
                            <label for="select" class=" col-sm-1 control-label " >Titulo</label>
                            <div class="col-sm-4">
                                <input type="text" name="titulo" id="titulo" value="" class="form-control" required="required" placeholder="Titulo da Mensagem" />
                                <?php // echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?>
                            </div>
                            
                             <label for="select" class="col-sm-offset-1 col-sm-2 control-label ">Periodo de Exibição</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <input type="text" name="exibicao_ini" class="form-control data">
                                    <div class="input-group-addon">Até</div>
                                    <input type="text" name="exibicao_fim" class="form-control data">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="select" class=" col-sm-1 control-label ">Mensagem</label>
                            <div class="col-sm-4">
                                <textarea name="mensagem" id="mensagem" value="" class="form-control" required="required" placeholder="Escreva a mensagem global para o Portal onde todos poderão ver!"></textarea>
                            </div>
                        </div>

                    </div>
                    <div class="panel-footer text-right hidden-print">
                        <input type="submit" name="enviar_mensagem" id="enviar_mensagem" value="Enviar" class="btn btn-success" />
                    </div>
                </div>               
                
                <div class="panel panel-default">
                    <div class="panel-heading pointer" role="tab" id="headingOne" data-toggle="collapse" data-parent="#expirado" href="#expirado">
                        <span class="badge"><?php echo $rows;  ?></span> Últimas Mensagens
                    </div>
                    <div id="expirado" class="panel-collapse collapse" role="tabpanel">
                        <table class="table table-hover table-striped text-sm">
                            <thead>
                                <tr>
                                    <th class="text-center">Título</th>
                                    <th class="text-center">Mensagem</th>
                                    <!--<th class="text-center">Data Cadastrada</th>-->
                                    <th class="text-center">Data de Exibição</th>
                                    <th class="text-center">Cadastrado Por</th>
                                    <th class="text-center">Editar/Excluir</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($sql_aviso = mysql_fetch_assoc($query_aviso)){ ?>
                                    <tr>
                                        <td class="text-center"><?php echo $sql_aviso['titulo_mensagem']; ?></td>
                                        <td class="text-center"><?php echo  $sql_aviso['texto_mensagem']; ?></td>
                                        <!--<td class="text-center"><?php echo implode('/', array_reverse(explode('-',$sql_aviso['data_cad']))); ?></td>-->
                                        <td class="text-center"><?php echo implode('/', array_reverse(explode('-',$sql_aviso['exibicao_ini'])))." - ".implode('/', array_reverse(explode('-',$sql_aviso['exibicao_fim']))); ?></td>
                                        <td class="text-center"><?php echo $sql_aviso['nome_clt']; ?></td>
                                        <td class="text-center">
                                            <a href="javascript:;" class="btn btn-xs btn-success bt-edit editar_aviso"  data-key="<?php echo $sql_aviso['id_portal_avisos'] ?>" data-titulo="<?php echo $sql_aviso['titulo_mensagem'] ?>" data-mensagem="<?php echo $sql_aviso['texto_mensagem'] ?>" data-inicio="<?php echo implode('/', array_reverse(explode('-',$sql_aviso['exibicao_ini'])))?>" data-fim="<?php echo implode('/', array_reverse(explode('-',$sql_aviso['exibicao_fim'])))?>" >
                                                <i class="fa fa-pencil"></i>
                                            </a>   
                                            <a href="javascript:;" class="btn btn-xs btn-danger bt-exclui excluir_aviso" data-key="<?php echo $sql_aviso['id_portal_avisos'] ?>" >
                                                <i class="fa fa-trash"></i>
                                            </a>   
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </form> 

            
            
            
            
            <?php 
            
         
            ?>

            <?php include('../template/footer.php'); ?>
            <div class="clear"></div>
        </div>
        
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.ui.datepicker-pt-BR.js" type="text/javascript"></script>
        <script src="../js/jquery.form.js" type="text/javascript"></script>
        
        <script>
            $(function() {
                $('.data').datepicker({
                    dateFormat: 'dd-mm-yy',
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '2005:c+1'
                });
                
                $(".editar_aviso").click(function(){
//                    alert();
                var html = 
                    $('<form>', { method: "post", class: 'form-horizontal form_solicitacao' }).append(
                        $('<div>',{ class: "form-group"  }).append(
                            $('<label>',{class: "col-sm-4 control-label", html: "Editar Titulo"}),
                            $('<div>',{ class: "col-sm-6"  }).append(
                                    $('<input>',{type:"text", class:"form-control", name:"editar_titulo", id:"editar_titulo", value:$(this).data("titulo")}),
                                    $('<input>',{type:"hidden", class:"form-control", name:"id_portal_aviso", id:"id_portal_aviso", value:$(this).data("key")}),
                                    $('<input>',{type:"hidden", class:"form-control", name:"method", id:"method", value:"editar_mensagem"})
                            )
                        ),
                        $('<div>',{ class: "form-group"  }).append(
                            $('<label>',{class: "col-sm-4 control-label", html: "Editar Mensagem"}),
                                $('<div>',{ class: "col-sm-6"  }).append(
                                    $('<textarea>',{type:"text", class:"form-control", style:"height:100px", name:"editar_mensagem", id:"editar_mensagem", html:$(this).data("mensagem")})
                            )
                        ),
                        $('<div>',{ class: "form-group"  }).append(
                            $('<label>',{class: "col-sm-4 control-label", html: "Periodo de Exibição"}),
                            $('<div>',{ class: "col-sm-6"  }).append(
                                $('<div>',{class:"input-group"}).append(
                                    $('<input>',{type:"text", class:"form-control data",style:" position: relative; z-index: 100000;", name:"editar_exibicao_ini", id:"editar_exibicao_ini", value:$(this).data("inicio")}),
                                        $('<div>',{class:"input-group-addon", html:"Até"}),
                                            $('<input>',{type:"text", class:"form-control data", style:" position: relative; z-index: 100000;", name:"editar_exibicao_fim", id:"editar_exibicao_fim", value:$(this).data("fim")})
                                )
                            )
                    )
                    );
   
                    html.find(".data").datepicker({
                        dateFormat: 'dd/mm/yy',
                        changeMonth: true,
                        changeYear: true,
                        yearRange: '2005:c+1'
                    });

                    bootDialog(
                        html, 
                        'Editar Mensagem',
                        [{
                            label: 'Cancelar',
                            action: function (dialog) {
                                typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(false);
                                dialog.close();
                            }
                        }, {
                            label: 'Atualizar',
                            cssClass: 'btn-success' ,
                            action: function (dialog) {

                                $.post('', $('.form_solicitacao').serialize(), function(data){
                                   bootAlert("Atualizado com Sucesso", "Atualizado!", function(){location.reload();}, "success" );
                                });
                            }
                        }],
                        'warning'
                    );
                });
                
                $(".excluir_aviso").click(function(){
                    var html =  
                               
                                $('<form>', { method: "post", class: 'form-horizontal form_exclusao' }).append(
                                 $('<h5>', {html:"Deseja realmente excluir?"}),
                                    $('<input>',{type:"hidden", class:"form-control", name:"id_portal_aviso", id:"id_portal_aviso", value:$(this).data("key")}),
                                    $('<input>',{type:"hidden", class:"form-control", name:"method", id:"method", value:"excluir_mensagem"})
                                );
                    
                    bootDialog(
                        html, 
                        'Deseja Excluir?', 
                        [{
                            label: 'Cancelar',
                            action: function (dialog) {
                                typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(false);
                                dialog.close();
                            }
                        }, {
                            label: 'Excluir',
                            cssClass: 'btn-danger' ,
                            action: function (dialog) {

                                $.post('', $('.form_exclusao').serialize(), function(data){
                                   bootAlert("Excluido com Sucesso", "Atualizado!", function(){location.reload();}, "success" );
                                });
                            }
                        }],
                        'danger'
                    );      
                });
                
                
            });
        </script>
    
    </body>
</html>