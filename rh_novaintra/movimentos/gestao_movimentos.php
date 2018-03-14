<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../classes/MovimentoClass.php');
include('../../funcoes.php');
include('../../wfunction.php');
include('../../classes_permissoes/acoes.class.php');

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$movimento = new Movimentos();
$global = new GlobalClass();

$objAcoes = new Acoes();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>"Gestão de Movimentos");
$breadcrumb_pages = array("Gestão de RH" => "../../rh");
$filtro = false;

$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');

if ((isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar']))){
    $filtro = true;
    $categoria = $_REQUEST['categoria_mov'];
    $pesquisa = $_REQUEST['pesquisa'];
    $anoR = $_REQUEST['ano'];
    
    $qry_mov = $movimento->getMovimentos($categoria, $pesquisa,$anoR);
    $tot_mov = mysql_num_rows($qry_mov);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Gestão de Movimentos</title>
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
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Movimentos</small></h2></div>

<!--                     Nav tabs 
                    <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
                        <li role="presentation" class="active"><a href="#avisos" role="tab" data-toggle="tab">Avisos</a></li>
                        <li role="presentation"><a href="#lista" role="tab" data-toggle="tab">Lista de Funcionários</a></li>
                        <li role="presentation"><a href="#relatorio" role="tab" data-toggle="tab">Relatório de Férias</a></li>
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
                                            <label for="categoria_lista" class="col-lg-2 control-label">Categoria:</label>
                                            <div class="col-lg-9">                                                
                                                <?php echo montaSelect($movimento->selectCategoria(),$categoria, "id='categoria_mov' name='categoria_mov' class='form-control'"); ?>                                                
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="categoria_lista" class="col-lg-2 control-label">Ano:</label>
                                            <div class="col-lg-9">                                                
                                                <?php echo montaSelect(AnosArray(null, date('Y')), $anoR, "id='ano' name='ano' class='required[custom[select]] form-control'"); ?> 
                                            </div>
                                        </div>                                                                                
                                        <div class="form-group">
                                            <label for="nome_movimento" class="col-lg-2 control-label">Filtro:</label>
                                            <div class="col-lg-9"><input type="text" name="pesquisa" id="pesquisa" class="form-control" placeholder="Nome do Movimento" value="<?php echo $pesquisa; ?>"></div>
                                        </div>

                                    </div><!-- /.panel-body -->

                                    <div class="panel-footer text-right">
                                        <!--MARISA-->
                                        <?php if($objAcoes->verifica_permissoes(132)){ ?>                                       
                                        <a href="form_movimento.php" class="btn btn-success">Cadastrar</a>
                                        <?php } ?>
                                        <input type="submit" value="Consultar" id="submit-lista" name="filtrar" class="btn btn-primary">
                                    </div>

                                </div><!-- /.panel -->
                                
                                <?php 
                                if($filtro){
                                    if($tot_mov > 0){
                                ?>
                                <table class="table table-striped table-hover table-condensed table-bordered">
                                    <thead>
                                        <tr class="bg-primary valign-middle">
                                            <th class="text-center" style="width:5%;">COD</th>
                                            <th style="width:40%;">NOME</th>                                            
                                            <th class="text-center" style="width:10%;">CATEGORIA</th>
                                            <th class="text-center" style="width:25%;">INCIDÊNCIA</th>                                            
                                            <th style="width:10%;">&emsp;</th>
                                        </tr> 
                                    </thead>
                                    <tbody>
                                        <?php
                                        while($res = mysql_fetch_assoc($qry_mov)){
                                            $incidencia_inss = ($res['incidencia_inss'] == 1) ? "<span class='label label-success'>INSS</span>" : '';
                                            $incidencia_fgts = ($res['incidencia_fgts'] == 1) ? '<span class="label label-warning">FGTS</span>' : '';
                                            $incidencia_ir = ($res['incidencia_irrf'] == 1) ? '<span class="label label-info">IRRF</span>' : '';
                                            $incidencia_dsr = ($res['incide_dsr'] == 1) ? '<span class="label label-default">DSR</span>' : '';
                                        ?>
                                        <tr class="valign-middle">
                                            <td><?php echo $res['cod']; ?></td>
                                            <td><?php echo $res['descicao']; ?></td>
                                            <td class="text-center"><?php echo $res['categoria']; ?></td>
                                            <td class="text-center"><?php echo "{$incidencia_inss} {$incidencia_fgts} {$incidencia_ir} {$incidencia_dsr}"; ?></td>
                                            <td class="text-center">
                                                <a href="javascript:;" class="bt-image" data-type="visualizar" data-key="<?php echo $res['id_mov']; ?>" data-ano="<?php echo $anoR; ?>">
                                                    <i class="tooo btn btn-xs btn-primary fa fa-search" data-toggle="tooltip" data-placement="top" title="Visualizar"></i>
                                                </a>
                                                <?php if($objAcoes->verifica_permissoes(133)){ ?>
                                                <a href="form_movimento.php?id=<?php echo $res['id_mov']; ?>" data-key="<?php echo $res['id_mov']; ?>" data-type="editar" class="bt-image">
                                                    <i data-type="visualizar" class="tooo btn btn-xs btn-warning fa fa-edit" data-toggle="tooltip" data-placement="top" title="Editar"></i>
                                                </a>
                                                <?php } ?>
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

        <!--<script src="../../resources/js/rh/rh_movimentos.js"></script>-->
        <script>
            $(".bt-image").on("click", function() {
                var action = $(this).data("type");
                var id_mov = $(this).data("key");
                var ano = $(this).data("ano");
                var url = 'detalhe_movimento.php';                
                
                if(action === "visualizar") {
                    $.post(url,{id_mov:id_mov, ano:ano},function(data){
                        bootDialog(data,'Detalhes do movimento');
                    });
                }
            });
        </script>
    </body>
</html>