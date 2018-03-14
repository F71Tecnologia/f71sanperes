<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include('../../classes/global.php');


if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'excluir') {
    $id = $_REQUEST['id'];
    $query = "UPDATE cursos_padroes SET status = 0 WHERE id_curso_padrao = $id";
    $array = (mysql_query($query)) ? array('status' => TRUE, 'msg' => utf8_encode('Função Padrão exluida com sucesso.')) : array('status' => FALSE, 'msg' => 'Erro ao excluir.');
    echo json_encode($array);
    exit();
}


$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)


$query = "SELECT * FROM cursos_padroes WHERE status = 1 ORDER BY nome";
$result = mysql_query($query);
$funcoes_padroes = array();
while ($row = mysql_fetch_assoc($result)) {
    $funcoes_padroes[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão de RH</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" type="text/css">
        <style>
            .table > thead > tr > th,
            .table > tbody > tr > td {
                vertical-align: middle;
            }
            .text-captalize{
                text-transform: capitalize;
            }
        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>- Ferramenta TPL</small></h2></div>
                </div>
            </div>
            <div class="row margin_b20">
                <div class="col-sm-12">
                    <ul class="nav nav-tabs">
                        <li role="presentation"><a href="/intranet/relatorios/relatorio_consolidado_funcao.php">Ferramenta TLP</a></li>
                        <li role="presentation" class="active"><a href="/intranet/rh_novaintra/ferramenta_tlp/funcoes_padroes.php"> Gestão de Funções Padrões</a></li>
                        <li role="presentation"><a href="/intranet/rh_novaintra/ferramenta_tlp/associacao_funcao_unidade.php">Associação de Funções a Unidades</a></li>
                    </ul>
                </div>
            </div>
            <div class="row margin_b20">
                <div class="col-sm-12 text-right">
                    <a href="form_funcao_padrao.php" class="btn btn-success"><i class="fa fa-plus"></i> Nova Função Padrão</a>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Nome</th>
                                    <th>&emsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($funcoes_padroes as $value) { ?>
                                    <tr>
                                        <td class="text-center"><?= $value['id_curso_padrao'] ?></td>
                                        <td><?= $value['nome'] ?></td>
                                        <td class="text-right">
                                            <a class="btn btn-success btn-xs" href="form_funcao_padrao.php?id=<?= $value['id_curso_padrao'] ?>"><i class="fa fa-pencil"></i></a>
                                            <button class="btn btn-danger btn-xs excluir" data-id="<?= $value['id_curso_padrao'] ?>"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.container -->

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.maskedinput.min.js" type="text/javascript"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(document).ready(function () {
                $('.excluir').click(function () {
                    var $this = $(this);
                    var id = $this.data('id');

                    bootConfirm('Tem certeza que deseja excluir?', 'Excluindo...', function (result) {
                        if (result) {
                            $.post('funcoes_padroes.php', {method: 'excluir', id: id}, function (data) {
                                var class_status = data.status ? 'success' : 'danger';
                                bootAlert(data.msg, 'Salvando...', null, class_status);
                                if (data.status) {
                                    $this.closest('tr').remove();
                                }
                            }, 'json');
                        }
                    }, 'danger');
                });
            });

        </script>
    </body>
</html>
