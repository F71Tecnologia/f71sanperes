<?php 
/* 
 * CRIADO POR: RAMON LIMA 12/02/2016
 * 
 * 
 */

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include("../../../conn.php");
include("../../../wfunction.php");
include("../../../classes/global.php");
include("../../../classes/InformeRendimentoClass.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$global = new GlobalClass();
$dirf = new InformeRendimentoClass($usuario['id_master']);

$breadcrumb_config = array("nivel"=>"../../../", "key_btn"=>"4", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Verificação DIRF e Informe");
$breadcrumb_pages = array("Principal" => "../../principalrh.php");

$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y')-1;
$optAnos = $dirf->montaOptionsAnos();
$tpsContrato = array("1"=>"Autonomo","2"=>"CLT","3"=>"Cooperado");

if(isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])){
    $c=0;
    $dirf->setAnoBase($_REQUEST['ano']);
    $rsParticipantes = $dirf->getParticipantes($_REQUEST['projeto'], $usuario['id_regiao']);
    
}

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Verificação de DIRF e Informe de Rendimentos</title>
        
        <link href="../../../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/datepicker.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
        <link href="../../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <style>
            /*GABIARRA PARA AUMENTAR O TAMANHO DO MODAL*/
            @media (min-width: 992px){
                .modal-lg {
                    width: 70%!important;
                }
            }
            @media (min-width: 768px){
                .modal-lg {
                    width: 80%!important;
                }
            }
            
            @media (max-width: 767px){
                .modal-lg {
                    width: 90%!important;
                }
            }
        </style>
    </head>
    <body>
        <?php include("../../../template/navbar_default.php"); ?>

        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="glyphicon glyphicon-user"></span> - Recursos Humanos<small> - Validação DIRF e Informe</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
                
                <div class="panel panel-default">
                    <div class="panel-heading text-bold">Filtro</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-sm-1 control-label">Projeto</label>
                            <div class="col-sm-4">
                                <?=montaSelect($global->carregaProjetosByRegiao($usuario['id_regiao'], array("-1" => "« Selecione o Projeto »")), $projetoR, "id='projeto' name='projeto' class='validate[required,custom[select]] form-control'")?>
                            </div>
                            <label for="select" class="col-sm-3 control-label">Ano Base/Ano Competencia</label>
                            <div class="col-sm-4">
                                <?=montaSelect($optAnos,$anoR, "id='ano' name='ano' class='required[custom[select]] form-control'")?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <button type="submit" class="btn btn-success" name="filtrar" value="filtrar"><i class="fa fa-filter"></i> Filtrar</button>
                    </div>
                </div>
            </form>
            
            <?php if(mysql_num_rows($rsParticipantes) > 0){?>
            <table class='table table-hover table-striped table-bordered text-sm valign-middle'>
                <thead>
                    <tr>
                        <td>Nome</td>
                        <td>CPF</td>
                        <td>Vinculo</td>
                        <td>Qnt Vinc</td>
                        <td>Ren Trib</td>
                        <td>Ver</td>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        while($row = mysql_fetch_assoc($rsParticipantes)){ 
                            $dirf->setTipo($row['tipo_contratacao']);
                            $dirf->limpaVariaveis();
                            $multVinculos['total'] = null;
                            $idClt = null;
                            $campoID = null;
                            
                            $multVinculos = $dirf->verificaDuplicidade(str_replace(array(".","-"),"",$row['cpf']),$row['tipo_contratacao']);
                            
                            if($row['tipo_contratacao'] == 2){
                                $campoID = "id_clt";
                            }else{
                                $campoID = "id_autonomo";
                            }
                            
                            if($multVinculos['total'] > 1){
                                $ARidClt = null;
                                foreach($multVinculos['rs'] as $multClt){
                                    $ARidClt[] = $multClt[$campoID];
                                }
                                $idClt = implode(",",$ARidClt);
                            }else{
                                $idClt = $row["id_clt"];
                            }

                            $dirf->getDadosFolhas($idClt);
                            $dirf->getDadosRescisao2015($idClt);
                            $dirf->getDadosFerias($idClt);
                            
                    ?>
                    <tr>
                        <td><?php echo $row['nome']."<!--".$idClt."-->"?></td>
                        <td><?php echo $row['cpf']?></td>
                        <td><?php echo $tpsContrato[$row['tipo_contratacao']]?></td>
                        <td><?php echo $multVinculos['total']?></td>
                        <td><?php echo number_format($dirf->salario,2,",",".")?></td>
                        <td class="text-center"><a class="btn btn-xs btn-success btview" href="javascript:;" data-key="<?php echo $idClt ?>" data-ano="<?php echo $_REQUEST['ano']?>" data-tipo="<?php echo $row['tipo_contratacao']?>"><i class="fa fa-search"> </i></a></td>
                    </tr>
                    <?php 
                        $dirf->limpaVariaveis();
                        unset($row,$multVinculos);
                        /*if($c++ == 100){
                            break;
                        }*/
                    }
                    
                    
                    ?>
                </tbody>
            </table>
            <?php } ?>
            
            <?php include('../../../template/footer.php'); ?>
        </div>
        
        <script src="../../../js/jquery-1.10.2.min.js"></script>
        <script src="../../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../../resources/js/bootstrap.min.js"></script>
        <script src="../../../resources/js/bootstrap-datepicker.js"></script>
        <script src="../../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../../resources/js/tooltip.js"></script>
        <script src="../../../resources/js/main.js"></script>
        <script src="../../../js/global.js"></script>        
        <script src="../../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(function() {                
                $("#form1").validationEngine();
                $(".modal-lg").css('width','80%');
                
                //datepicker
                var options = new Array();
                options['language'] = 'pt-BR';
                $('.datepicker').datepicker(options);
                                
                $(".btview").on("click", function() {
                    var id = $(this).data("key");
                    var ano = $(this).data("ano");
                    var tipo = $(this).data("tipo");
                    var url = 'detalhe_dirf.php';
                    var texto = $(this).parent().prev().prev().prev().html();
                    
                    $.post(url,{id:id, ano:ano, tipo:tipo},function(data){
                        //bootDialog(data,'Detalhes do '+texto);
                        $(".modal-lg").css('width','80%');
                        
                        new BootstrapDialog({
                            nl2br: false,
                            size: BootstrapDialog.SIZE_WIDE,
                            title: 'Detalhes do '+texto,
                            message: data,
                            closable: false,
                            type: 'type-primary',
                            buttons: [
                                {
                                    label: 'Fechar',
                                    cssClass: 'btn-default',
                                    action: function (dialog) {
                                        dialog.close();
                                    }
                                }]
                        }).open();
                        
                        
                    });
                });
            });
        </script>
    </body>
</html>