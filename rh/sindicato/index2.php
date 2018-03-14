<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/SindicatoClass.php');

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$result = getSindicato($id_regiao);
$total_sindicato = mysql_num_rows($result);

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMUL�RIO SELECIONADO */
if(isset($_REQUEST['regiao'])){
    $regiaoR = $_REQUEST['regiao'];
}elseif(isset($_SESSION['regiao'])){
    $regiaoR = $_SESSION['regiao'];
}elseif(isset($_SESSION['regiao_select'])) {
    $regiaoR = $_SESSION['regiao_select'];
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE�ALHO (TROCA DE MASTER E DE REGI�ES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Sindicatos");
$breadcrumb_pages = array("Gest�o de RH"=>"../../rh");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Sindicatos</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <!--link href="../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css"-->
    </head>
    <body>
    <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>Sindicatos</small></h2></div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                <!--resposta de algum metodo realizado-->
                    <div id="message-box" class="<?php echo $_SESSION['MESSAGE_COLOR']; ?> alinha2">
                        <?php echo $_SESSION['MESSAGE'];
                        session_destroy(); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <form class="form-horizontal" id="form1" method="post" enctype="multipart/form-data">
                        <input type="hidden" id="sindicato" name="sindicato" />
                        <input type="hidden" id="home" name="home" />
                        <input type="hidden" id="caminho" name="caminho" />
                
                        <div class="form-group">
                            <div class="col-lg-12">
                                <input type="submit" class="btn btn-success pull-right" value="Novo Sindicato" name="novo" id="novoSindicato" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-lg-12">
                    <?php if ($total_sindicato > 0) { ?>
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>C�d.</th>
                                    <th>Qtd. de V�nculos</th>
                                    <th>Nome</th>
                                    <th>M�s de desconto</th>
                                    <th>M�s de diss�dio</th>
                                    <th>Telefone</th>
                                    <th>Contato</th>
                                    <th colspan="3">A��es</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            while ($row = mysql_fetch_assoc($result)) {
                                $qtd_vinculos = getRhClt($row['id_sindicato']); ?>
                                <tr id="<?=$row['id_sindicato']?>">
                                    <td><?=$row['id_sindicato']?></td>
                                    <td><?=$qtd_vinculos?></td>
                                    <td><?=acentoMaiusculo($row['nome'])?></td>
                                    <td><?=mesesArray($row['mes_desconto'])?></td>
                                    <td><?=mesesArray($row['mes_dissidio'])?></td>
                                    <td><?=mascara_stringTel($row['tel'])?></td>
                                    <td><?=$row['contato']?></td>
                                    <td class="center"><img src="../../imagens/icones/icon-docview.gif" title="Visualizar" class="bt-image" data-type="visualizar" data-key="<?=$row['id_sindicato']?>" /></td>
                                    <td class="center"><img src="../../imagens/icones/icon-edit.gif" title="Editar" class="bt-image" data-type="editar" data-caminho="1" data-key="<?=$row['id_sindicato']?>" /></td>
                                    <td class="center"><img src="../../imagens/icones/icon-delete.gif" title="Excluir" class="bt-image" data-qtd="<?=$qtd_vinculos?>" data-type="excluir" data-key="<?=$row['id_sindicato']?>" /></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <?php } else { ?>
                        <div id='message-box' class='warning'>
                            <p>Nenhum registro encontrado</p>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        
        <script>
            $(function() {
                $(".bt-image").on("click", function() {
                    var action = $(this).data("type");
                    var key = $(this).data("key");                   
                    var qtd = $(this).data("qtd");
                    var caminho = $(this).data("caminho");
                    
                    
                    if(action === "visualizar") {
                        $("#sindicato").val(key);
                        $("#form1").attr('action','detalhes_sindicato2.php');
                        $("#form1").submit();
                        
                    }else if(action === "editar"){
                        $("#sindicato").val(key);
                        $("#caminho").val(caminho);
                        $("#form1").attr('action','form_sindicato2.php');
                        $("#form1").submit();
                        
                    }else if(action === "excluir"){
                        
                        if(qtd != 0){
                            thickBoxAlert("Exclus�o de Sindicato", "Sindicato n�o pode ser excluido, pois existe(m) v�nculo(s)", 300, 130, null);
                        }else{
                            thickBoxConfirm("Exclus�o de Fun��o", "Voc� deseja realmente excluir este sindicato?", 300, 200, function(data){                                
                                if(data == true){                                        
                                    $.ajax({
                                        url:"del_sindicato.php",
                                        type:"POST",
                                        dataType:"json",
                                        data:{
                                            id:key,
                                            method:"excluir_sindicato"
                                        },
                                        success:function(data){
                                            $("#"+key).remove();
                                        }
                                    });
                                }                                
                            });
                        }
                    }
                });
                
                $("#novoSindicato").click(function(){
                    $("#caminho").val('1');
                    $("#form1").attr('action','form_sindicato2.php');
                    $("#form1").submit();
                });
            });
        </script>
    </body>
</html>
