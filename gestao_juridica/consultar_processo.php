<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include("../conn.php");
include("../classes/global.php");
include("../classes/ComprasChamados.php");
include("../wfunction.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$projeto1 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto1' name='projeto' class='form-control validate[required,custom[select]]'");
$global = new GlobalClass();

$breadcrumb_config = array("nivel" => "../", "key_btn" => "24", "area" => "Jurídico", "ativo" => "Consultar Processos", "id_form" => "consulta_processo");

if (isset($_REQUEST['consultar'])) {
    if (!empty($_REQUEST['n_processo']) || !empty($_REQUEST['nome'])) {
        $cond[] = (empty($_REQUEST['n_processo'])) ? '' : "b.n_processo_numero LIKE '%{$_REQUEST['n_processo']}%'";
        $cond[] = (empty($_REQUEST['nome'])) ? '' : "c.nome LIKE '%{$_REQUEST['nome']}%'";
        $cond = array_filter($cond);
        $query = "SELECT a.proc_id,d.proc_tipo_nome,b.n_processo_numero,d.proc_tipo_nome, c.nome,a.proc_tipo_id
            FROM processos_juridicos AS a
            INNER JOIN processo_tipo AS d ON a.proc_tipo_id = d.proc_tipo_id
            LEFT JOIN n_processos AS b ON a.proc_id = b.proc_id
            LEFT JOIN processos_juridicos_nomes AS c ON a.proc_id = c.proc_id
            WHERE (" . implode(' OR ', $cond) . ") AND a.status = 1
            GROUP BY a.proc_id
            ORDER BY nome";
        $result = mysql_query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $processos[$row['proc_id']] = $row;
            $query_nome = "SELECT nome FROM processos_juridicos_nomes WHERE proc_id = {$row['proc_id']} ORDER BY nome";
            $result_nome = mysql_query($query_nome);
            while ($row_nome = mysql_fetch_assoc($result_nome)) {
                $processos[$row['proc_id']]['nomes'][] = $row_nome['nome'];
            }
            $query_num = "SELECT n_processo_numero FROM n_processos WHERE proc_id = {$row['proc_id']} ORDER BY n_processo_numero";

            $result_num = mysql_query($query_num);
            while ($row_num = mysql_fetch_assoc($result_num)) {
                $processos[$row['proc_id']]['num'][] = $row_num['n_processo_numero'];
            }
            $processos[$row['proc_id']]['nomes'] = array_filter($processos[$row['proc_id']]['nomes']); // limpa linhas vazias
            $processos[$row['proc_id']]['num'] = array_filter($processos[$row['proc_id']]['num']); // limpa linhas vazias
        }
    } else {
        $processos = array();
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet ::</title>

        <link rel="shortcut icon" href="../favicon.png">

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-compras.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-juridico-header">
                        <h2><span class="glyphicon glyphicon-briefcase"></span> - JURÍDICO <small>- Consulta de Processos</small></h2>
                    </div>
                </div>
                <div class="col-lg-12">
                    <form id="consulta_processo" action="#" method="post" class="form-horizontal">
                        <input type="hidden" name="home" id="home" value="">
                        <div class="panel panel-default">
                            <div class="panel-heading">Filtro de Busca</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="n_processo">N&ordm; do Processo</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="n_processo" name="n_processo" placeholder="" value="<?= $_REQUEST['n_processo'] ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="nome">Nome</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="nome" name="nome" placeholder="" value="<?= $_REQUEST['nome'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <button type="submit" class="btn btn-primary" name="consultar" value="1"><i class="fa fa-search"></i> Consultar</button>
                            </div>
                        </div>
                    </form>
                    <?php if (isset($_REQUEST['consultar'])) { ?>
                        <?php if (!empty($processos)) { ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>N&ordm; do Processo</th>
                                        <th>Nome</th>
                                        <th>Processo</th>
                                        <th>&emsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($processos as $value) { ?>
                                        <tr>
                                            <td><?= implode(', ', $value['num']) ?></td>
                                            <td><?= implode(', ', $value['nomes']) ?></td>
                                            <td><?= $value['proc_tipo_nome'] ?></td>
                                            <td>
                                                <?php
                                                $link = ($value['proc_tipo_id'] == 1) ? 'processo_trabalhista/dados_trabalhador/ver_trabalhador.php' : 'outros_processos/dados_processo/ver_processo.php';
                                                ?>
                                                <a href="<?= $link ?>?id_processo=<?= $value['proc_id'] ?>" class="btn btn-info btn-xs"><i class="fa fa-search"></i></a>
                                            </td>
                                        </tr>

                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <div class="alert alert-info">
                                <h4>Atenção!</h4>
                                <p>Não há processos que atendam aos filtros da busca.</p>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <?php include_once '../template/footer.php'; ?>
        </div>
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery.form.js"></script>
        <script src="../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/global.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="../js/jquery.form.js" type="text/javascript"></script>
        <script>
            $(function () {

            });
        </script>

    </body>
</html>

