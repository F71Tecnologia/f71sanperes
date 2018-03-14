<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://f71lagos.com/intranet/login.php");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../classes/MovimentoClass.php');
include('../../classes/CltClass.php');
include('../../classes/ProjetoClass.php');
include('../../wfunction.php');

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$objProjeto = new ProjetoClass();
$objClt = new Movimentos();
$listaClts = $objClt->getListaClts($usuario[id_master], $usuario[id_regiao]);

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Movimentos");
$breadcrumb_pages = array("Gestão de RH"=>"../index.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Movimentos</title>
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
        <!--link href="../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css"-->
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Movimentos</small></h2></div>
                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->
            <?php foreach ($listaClts as $projeto => $clts) { ?>
                <h4>
                    <i class="fa fa-chevron-right"></i> <?=$objProjeto->getNome($projeto)?> <span class="pull-right"><a class="btn btn-success" href="javascript:;" onclick="tableToExcel('tbRelatorio<?=$projeto?>', 'Relatório')"><i class="fa fa-file-excel-o"></i> Exportar para Excel</a></span>
                </h4>
                <table class="table table-striped table-hover table-condensed table-bordered" id="tbRelatorio<?=$projeto?>">
                    <thead>
                        <tr class="bg-primary valign-middle">
                            <th style="width:5%;">COD</th>
                            <th style="width:45%;">NOME</th>
                            <th style="width:25%;">CARGO</th>
                            <th style="width:20%;">UNIDADE</th>
                            <th style="width:5%;"></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($clts as $clt) { $status='';?>
                        <?php if($clt['status'] == '40') { 
                            $status = '<span class="text-info text-sm">(Em Férias)</span>';
                       } elseif($clt['status'] == '200') {
                            $status = '<span class="text-danger text-sm">(Aguardando Demissão)</span>';
                        } ?>
                        <tr class="valign-middle">
                            <td><?=$clt['id_clt']?></td>
                            <td><?=$clt['nome'].$status?></td>
                            <td><?=$clt['curso_nome']?></td>
                            <td><?=$clt['unidade_nome']?></td>
                            <td class="text-center">
                                <a href="javascript:;" data-id-clt="<?=$clt['id_clt']?>" class="btn btn-xs btn-info lancar-movimentos">
                                    <i data-type="visualizar" class="tooo fa fa-external-link" data-toggle="tooltip" data-placement="top" title="Lançar Movimentos" data-original-title="Lançar Movimentos"></i>
                                    <!--img src="../imagens/icones/icon-edit.gif" data-type="visualizar" class="tooo" data-toggle="tooltip" data-placement="top" title="" data-original-title="Lançar Movimentos"-->
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
            <form class="form-horizontal" role="form" id="form1" method="post">
                <!--input type="hidden" name="regiao" id="regiao" value="36"-->
                <input type="hidden" name="home" id="home" value="" />
                <input type="hidden" name="clt" id="id_clt" value="">
            </form>
            <?php include_once ('../../template/footer.php'); ?>    
        </div><!-- /.container -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../resources/js/rh/rh_movimentos.js"></script>
        <script src="../../js/global.js"></script>
        
        <script src="../../jquery/priceFormat.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js" type="text/javascript"></script>
    </body>
</html>