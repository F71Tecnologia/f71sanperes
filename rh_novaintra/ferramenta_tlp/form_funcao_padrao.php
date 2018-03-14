<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include('../../classes/global.php');

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)


if (isset($_REQUEST['id'])) {
    $query = "SELECT * FROM cursos_padroes WHERE id_curso_padrao = {$_REQUEST['id']}";
    $result = mysql_query($query);
    $funcoes_padroes = mysql_fetch_assoc($result);
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'salvar') {
    $nome = trim(addslashes($_REQUEST['nome']));
    if (isset($_REQUEST['id_curso_padrao']) && !empty($_REQUEST['id_curso_padrao'])) {
        $query = "UPDATE cursos_padroes SET nome = '$nome' WHERE id_curso_padrao = '{$_REQUEST['id_curso_padrao']}'";
    } else {
        $query = "INSERT INTO cursos_padroes (nome, data_cad) VALUES ('$nome',NOW())";
    }
    $array = (mysql_query($query)) ? array('status' => TRUE, 'msg' => utf8_encode('Função Padrão salva com sucesso.')) : array('status' => FALSE, 'msg' => 'Erro ao salvar.');
    echo json_encode($array);
    exit();
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
                    <div class="page-header box-th-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>- Ferramenta TPL</small></h2></div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <form action="#" method="post" class="form-horizontal" id="form_funcao">
                        <div class="panel panel-default">
                            <div class="panel-heading">Formulário de Função Padrão</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">Nome</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="nome" id="nome" placeholder="nome" value="<?= $funcoes_padroes['nome'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <?php if ($funcoes_padroes['id_curso_padrao']) { ?>
                                    <input type="hidden" name="id_curso_padrao" value="<?= $funcoes_padroes['id_curso_padrao'] ?>">
                                <?php } ?>
                                <a class="btn btn-default" href="funcoes_padroes.php"><i class="fa fa-reply"></i> Voltar</a>
                                <button class="btn btn-primary" type="submit" name="method" value="salvar"><i class="fa fa-floppy-o"></i> Salvar</button>
                            </div>
                        </div>
                    </form>
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
        <script src="../../js/jquery.form.js"></script>
        <script>

            $(document).ready(function () {
                var options = {success: showResponse, dataType: 'json', clearForm: true};
                $('#form_funcao').ajaxForm(options);
            });

            function showResponse(response, statusText, xhr, $form) {
                var class_status = response.status ? 'success' : 'danger';
                bootAlert(response.msg, 'Salvando...', function () {
                    if (class_status) {
                        window.location.href = 'funcoes_padroes.php';
                    }
                }, class_status);
            }

        </script>
    </body>
</html>
