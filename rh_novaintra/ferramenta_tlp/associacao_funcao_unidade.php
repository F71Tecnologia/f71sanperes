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

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'getUnidade') {
    $query = "SELECT * FROM unidade WHERE campo1 = {$_REQUEST['id']} ORDER BY unidade";
    $result = mysql_query($query);
    $x = "<option value=\"-1\">« Selecione »</option>";
    while ($row = mysql_fetch_assoc($result)) {
        $x .= "<option value=\"{$row['id_unidade']}\">{$row['id_unidade']} - {$row['unidade']}</option>";
    }
    exit(utf8_encode($x));
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'excluir') {
    $query = "UPDATE cursos_unidades_assoc SET status = 0 WHERE id_assoc = {$_REQUEST['id']}";
    $status = mysql_query($query) or die(mysql_error());
    exit(json_encode(array('status'=>$status)));
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'add_assoc') {

    $q2 = "SELECT * FROM cursos_padroes WHERE status = 1";
    $r2 = mysql_query($q2);
    while ($rrr = mysql_fetch_assoc($r2)) {
        $cur[$rrr['id_curso_padrao']] = utf8_encode($rrr['nome']);
    }
    end($_REQUEST['row']);
    $key = key($_REQUEST['row']);

    $i = $_REQUEST['row'][$key]['value'] + 1;
    ?>
    <tr>
        <td>
            <?= montaSelect($cur, null, 'name="id_curso_padrao[' . $i . ']" class="form-control"') ?>
            <input type = "hidden" name = "id_assoc[<?= $i ?>]" class = "form-control" value = "">    
            <input type = "hidden" name = "id_unidade_x[<?= $i ?>]" class = "form-control" value = "<?= $_REQUEST['id_unidade'] ?>">    
            <input type = "hidden" name = "row[<?= $i ?>]" class = "form-control line_rows" value = "<?= $i ?>">
        </td>
        <td><input type = "text" name = "qtd_maxima[<?= $i ?>]" class = "form-control" value = ""></td>
        <td><input type = "text" name = "qtd_minima[<?= $i ?>]" class = "form-control" value = ""></td>
        <td><input type = "text" name = "qtd_sms[<?= $i ?>]" class = "form-control" value = ""></td>
        <td><input type = "text" name = "peso_funcao[<?= $i ?>]" class = "form-control" value = ""></td>
        <td>
            <button type="button" class="remove btn btn-danger"><i class="fa fa-trash-o"></i></button>
        </td>
    </tr>
    <?php
    exit();
}

if (isset($_REQUEST['id'])) {
    $query = "SELECT * FROM cursos_padroes WHERE id_curso_padrao = {$_REQUEST['id']}";
    $result = mysql_query($query);
    $funcoes_padroes = mysql_fetch_assoc($result);
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'consultar') {
    $q2 = "SELECT * FROM cursos_padroes WHERE status = 1";
    $r2 = mysql_query($q2);
    while ($rrr = mysql_fetch_assoc($r2)) {
        $cur[$rrr['id_curso_padrao']] = $rrr['nome'];
    }


    $query = "SELECT a.*,b.nome AS nome_curso
            FROM cursos_unidades_assoc AS a
            INNER JOIN cursos_padroes AS b ON a.id_curso_padrao = b.id_curso_padrao
            WHERE a.id_unidade = '{$_REQUEST['id_unidade']}' AND a.status = 1
            ORDER BY b.nome;";
    $result = mysql_query($query);
    $assoc = array();
    while ($row = mysql_fetch_assoc($result)) {
        $assoc[] = $row;
    }
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'salvar') {
    foreach ($_REQUEST['row'] AS $key => $value) {
        $qtd_maxima = $_REQUEST['qtd_maxima'][$key];
        $qtd_minima = $_REQUEST['qtd_minima'][$key];
        $qtd_sms = $_REQUEST['qtd_sms'][$key];
        $peso_funcao = $_REQUEST['peso_funcao'][$key];
        $id_unidade = $_REQUEST['id_unidade_x'][$key];
        $id_curso_padrao = $_REQUEST['id_curso_padrao'][$key];

        if (empty($_REQUEST['id_assoc'][$key])) {
            echo $query = "INSERT INTO cursos_unidades_assoc (id_curso_padrao,id_unidade,qtd_maxima,qtd_minima,qtd_sms,peso_funcao) VALUES ($id_curso_padrao,$id_unidade,$qtd_maxima,$qtd_minima,$qtd_sms,$peso_funcao);";
        } else {
            echo $query = "UPDATE cursos_unidades_assoc SET id_curso_padrao = '$id_curso_padrao', qtd_maxima = '$qtd_maxima', qtd_minima = '$qtd_minima', qtd_sms = '$qtd_sms', peso_funcao = '$peso_funcao' WHERE id_assoc = {$_REQUEST['id_assoc'][$key]};";
        }
        $r[] = mysql_query($query);
    }
    $resp = (!in_array(FALSE, $r)) ? array('status' => true, 'msg' => 'Salvo com sucesso.', 'r' => $r) : array('status' => FALSE, 'msg' => 'Erro ao salvar.', 'r' => $r);
}

$opt_projeto = isset($_REQUEST['id_projeto']) ? $_REQUEST['id_projeto'] : NULL;
$opt_unidade = isset($_REQUEST['id_unidade']) ? $_REQUEST['id_unidade'] : -1;
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
                        <li role="presentation"><a href="/intranet/rh_novaintra/ferramenta_tlp/funcoes_padroes.php"> Gestão de Cursos Padrões</a></li>
                        <li role="presentation" class="active"><a href="/intranet/rh_novaintra/ferramenta_tlp/associacao_funcao_unidade.php">Associação de Funções a Unidades</a></li>
                    </ul>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <form action="#" method="post" class="form-horizontal" id="form_funcao">
                        <div class="panel panel-default">
                            <div class="panel-heading">Associação de Funções às Unidades</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label for="id_projeto" class="col-sm-1 control-label">Projeto</label>
                                    <div class="col-sm-4">
                                        <?= montaSelect(GlobalClass::carregaProjetos($usuario['id_master']), $opt_projeto, 'name="id_projeto" class="form-control" id="id_projeto"') ?>
                                    </div>
                                    <label for="id_unidade" class="col-sm-2 control-label">Unidade</label>
                                    <div class="col-sm-4">
                                        <?= montaSelect(array(-1 => '« Selecione »'), NULL, 'name="id_unidade" class="form-control" id="id_unidade"') ?>
                                        <input type="hidden" id="id_uni_oculto" value="<?= $opt_unidade ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <button class="btn btn-info" type="submit" name="method" value="consultar"><i class="fa fa-search"></i> Consultar</button>
                            </div>
                        </div>

                        <?php if (isset($resp)) { ?>
                            <div class="alert alert-<?= $resp['status'] ? 'success' : 'danger' ?>"><?= $resp['msg'] ?></div>
                        <?php } ?>

                        <?php if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'consultar') { ?>
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <button type="button" id="add_assoc" class="btn btn-success btn-xs" data-id-unidade="<?= $_REQUEST['id_unidade'] ?>"><i class="fa fa-plus"></i> Nova Associação</button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped" id="tb_assoc">
                                        <thead>
                                            <tr>
                                                <th>Função</th>
                                                <th>Qtd. Máxima</th>
                                                <th>Qtd. Mínima</th>
                                                <th>Qtd. SMS</th>
                                                <th>Peso da Função</th>
                                                <th>&emsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($assoc)) { ?>
                                                <?php
                                                $i = 0;
                                                foreach ($assoc as $values) {
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?= montaSelect($cur, $values['id_curso_padrao'], 'name="id_curso_padrao[' . $i . ']" class="form-control"') ?>
                                                            <input type="hidden" name="id_assoc[<?= $i ?>]" class = "form-control" value = "<?= $values['id_assoc'] ?>">
                                                            <input type="hidden" name="id_unidade_x[<?= $i ?>]" class = "form-control" value = "<?= $values['id_unidade'] ?>">
                                                            <input type="hidden" name="row[<?= $i ?>]" class="form-control line_rows" value = "<?= $i ?>">
                                                        </td>
                                                        <td><input type="text" name="qtd_maxima[<?= $i ?>]" class="form-control" value="<?= $values['qtd_maxima'] ?>"></td>
                                                        <td><input type="text" name="qtd_minima[<?= $i ?>]" class="form-control" value="<?= $values['qtd_minima'] ?>"></td>
                                                        <td><input type="text" name="qtd_sms[<?= $i ?>]" class="form-control" value="<?= $values['qtd_sms'] ?>"></td>
                                                        <td><input type="text" name="peso_funcao[<?= $i ?>]" class="form-control" value="<?= $values['peso_funcao'] ?>"></td>
                                                        <td>
                                                            <button type="button" class="btn btn-danger excluir" data-id="<?= $values['id_assoc'] ?>"><i class="fa fa-trash-o"></i></button>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                    $i++;
                                                }
                                                ?>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="panel-footer text-right">
                                    <button type="submit" name="method" value="salvar" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Salvar</button>
                                </div>
                            </div>
                        <?php } ?>
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

                $('#id_projeto').change(function () {
                    var id = $(this).val();
                    $.post('#', {method: 'getUnidade', id: id}, function (data) {
                        $("#id_unidade").html(data);
                        $("#id_unidade").val($("#id_uni_oculto").val());
                    });
                });

                $("#add_assoc").click(function () {
                    var id_unidade = $("#id_uni_oculto").val();
                    var row = $('.line_rows').serializeArray();
                    console.log($('input[name=row]').val());
                    $.post('#', {method: 'add_assoc', row: row, id_unidade: id_unidade}, function (data) {
                        $('#tb_assoc tbody').append(data);
                    });
                });

                $("body").on('click', '.remove', function () {
                    $(this).closest('tr').remove();
                });
                $("body").on('click', '.excluir', function () {
                    var id = $(this).data('id');
                    var $this = $(this);
                    bootConfirm('Tem certeza que deseja ecluir este item?', 'Antenção', function (status) {
                        if (status) {
                            $.post('#', {method: 'excluir', id: id}, function (data) {
                                if (data.status) {
                                    bootAlert('Excluido com successo', 'Atenção', null, 'success');
                                    $this.closest('tr').remove();
                                } else {
                                    bootAlert('Erro ao exlcuir', 'Atenção', null, 'danger');
                                }
                            }, 'json');
                        }
                    }, 'danger');
                });

                $('#id_projeto').trigger('change');

            });

        </script>
    </body>
</html>
