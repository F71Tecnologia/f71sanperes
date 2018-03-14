<?php
if (empty($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
}

include("../../../conn.php");
include("../../../wfunction.php");
include("../../../classes/ValeTransporteClass.php");
include("../../../classes_permissoes/acoes.class.php");

include "../../../classes/LogClass.php";
$log = new Log();

$objAcoes = new Acoes();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$objTransporte = new ValeTransporteClass();

if(isset($_REQUEST['excluir'])){
    $return = $objTransporte->excluir($_REQUEST['id']);
    $log->gravaLog('Benefícios - Vale Transporte', "Pedido Excluido: ID{$_REQUEST['id']}");
    echo json_encode(array('status'=>$return,'msg'=>'Excluido com sucesso.'));
    exit();
}

$lista = $objTransporte->listar();

$breadcrumb_config = array("nivel" => "../../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Vale Transporte");
$breadcrumb_pages = array("Gestão de RH" => "../../../rh/principalrh.php", "Benefícios" => "../");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão de RH</title>
        <link href="../../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../../resources/css/ui-datepicker-theme.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>- Vale Transporte</small></h2></div>
                    
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <strong>Pedidos Realizados</strong>
                            <span class="pull-right">
                                <?php if($_COOKIE['logado'] != 395){ ?>
                                <a class="btn btn-success btn-xs" href="form_pedido.php"><i class="fa fa-plus"></i> Novo Pedido</a>
                                <?php } ?>
                            </span>
                            
                        </div>
                        <?php if($lista){ ?>
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Cod</th>
                                    <th>Região</th>
                                    <th>Competência</th>
                                    <th>Processado Por</th>
                                    <th>Processado Em</th>
                                    <th>&emsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lista as $key => $value) { ?>
                                    <tr>
                                        <td><?= $value['id_vt_pedido'] ?></td>
                                        <td><?= $value['nome_regiao'] ?></td>
                                        <td><?= $value['mes'] ?>/<?= $value['ano'] ?></td>
                                        <td><?= $value['nome_func'] ?></td>
                                        <td><?= $value['data_proc'] ?></td>
                                        <td class="text-right">
                                            <a href="../listar_pedido_vt.php?id=<?= $value['id_vt_pedido'] ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="Listar Pedido"><i class="fa fa-list"></i></a>
                                            <a href="../controle_vt.php?id=<?= $value['id_vt_pedido'] ?>" class="btn btn-info btn-xs" href="#" data-toggle="tooltip" data-placement="top" title="Download"><i class="fa fa-download"></i></a>                                            
                                            <!--<button type="button" class="btn btn-danger btn-xs excluir" data-id="<?= $value['id_vt_pedido'] ?>" data-url="index.php?excluir=true&id=" data-toggle="tooltip" data-placement="top" title="Excluir"><i class="fa fa-trash-o"></i></button>-->
                                        </td>
                                    </tr>
                                <?php } ?>                                    
                            </tbody>
                        </table>
                        <?php } ?>
                    </div>
                    
                    <?php if(!$lista){ ?>
                    <br />
                    <div class="alert alert-warning">
                        Nenhum pedido cadastrado
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php include_once '../../../template/footer.php'; ?>
        </div><!-- /.container -->
        <script src="../../../js/jquery-1.10.2.min.js"></script>
        <script src="../../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../../resources/js/bootstrap.min.js"></script>
        <script src="../../../resources/js/tooltip.js"></script>
        <script src="../../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../../resources/js/main.js"></script>
        <script src="../../../js/global.js"></script>
        <script src="../../../resources/js/rh/eventos/prorrogar_evento.js"></script>
        <script src="../../../js/jquery.maskedinput.min.js" type="text/javascript"></script>
        <script src="../../../resources/js/rh/index.js" type="text/javascript"></script>
        <script>
            $(document).ready(function () {
                /*
                 * botão generico para excluir linha em tabela
                 * <button type="button" class="excluir" data-id="id a do item a ser excluido" data-url="url do script que fara a exclusao"></button>
                 */
                $('.excluir').click(function () {
                    var $this = $(this);
                    var id = $this.data('id');
                    var url = $this.data('url');
                    bootConfirm('Deseja mesmo Excluir este item?', 'Exclusão', function (confirm) {
                        if (confirm) {
                            $.post(url + id, null, function (data) {
                                if (data.status) {
                                    $this.closest('tr').remove();
                                }
                                var statusType = (data.status) ? 'success' : 'danger';
                                bootAlert(data.msg, 'Exclusão', null, statusType);
                            }, 'json');
                        }
                    }, 'danger');
                });
            });
        </script>
    </body>
</html>