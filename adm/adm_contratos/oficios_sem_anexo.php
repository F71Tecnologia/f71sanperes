<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/regiao.php");
include("../../classes/ObrigacoesClass.php");

$usuario = carregaUsuario();

$objObrigacoes = new ObrigacoesClass();
$tipoOscip = (!empty($_REQUEST['tipo_oscip'])) ? "tipo_oscip = '{$_REQUEST['tipo_oscip']}'" : null;
$dadosOficios = $objObrigacoes->getOficiosSemAnexo($tipoOscip, 'tipo_oscip, data_publicacao');

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE�ALHO (TROCA DE MASTER E DE REGI�ES)

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"2", "area"=>"Administrativo", "id_form"=>"form1", "ativo"=>"Of�cios sem Anexo");
$breadcrumb_pages = array("Principal" => "../../admin/index.php?regiao=");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Of�cios sem Anexo</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-admin-header"><h2><span class="glyphicon glyphicon-cog"></span> - ADMINISTRATIVO<small> - Of�cios sem Anexo</small></h2></div>
                </div>
            </div>
            
            <form action="" method="post" id="form1" class="form-horizontal top-margin1" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="control-label col-sm-2">Tipo Of�cio</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="tipo_oscip">
                                    <option value="">Todos</option>
                                    <option value="Of�cios Enviados" <?=($_REQUEST['tipo_oscip'] == 'Of�cios Enviados')?'selected':''?>>Of�cios Enviados</option>
                                    <option value="Of�cios Recebidos" <?=($_REQUEST['tipo_oscip'] == 'Of�cios Recebidos')?'selected':''?>>Of�cios Recebidos</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
                    </div>
                </div>
            </form>
            <?php if(count($dadosOficios) > 0) { ?>
            <table class="table table-bordered table-condensed table-hover text-sm valign-middle">
                <thead>
                    <tr class="bg-primary">
                        <th class="text-center">COD</th>
                        <th class="">N� OF�CIO</th>
                        <th class="text-center">DATA PUBLICA��O</th>
                        <th class="">DESCRI��O</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dadosOficios as $oficios) { 
                    if(empty($_REQUEST['tipo_oscip']) && $auxTipo != $oficios['tipo_oscip']) { 
                        $auxTipo = $oficios['tipo_oscip']; 
                        echo '<tr><td colspan="4" class="text-center active">'.$auxTipo.'</td></tr>';
                    } ?>
                    <tr>
                        <td class="text-center"><?=$oficios['id_oscip']?></td>
                        <td class=""><?=$oficios['numero_oscip']?></td>
                        <td class="text-center"><?=implode('/', array_reverse(explode('-', $oficios['data_publicacao'])))?></td>
                        <td class=""><?=$oficios['descricao']?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php } else { ?>
                <div class="alert alert-warning">Nenhum Of�cio Encontrado!</div>
            <?php }
            include("../../template/footer.php"); ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../resources/js/administrativo/obrigacoes.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
    </body>
</html>