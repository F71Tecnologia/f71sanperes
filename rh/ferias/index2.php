<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
//include("../../classes/CalculoFeriasClass.php");
include("../../classes/FeriasClass.php");
include('../../wfunction.php');


$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$feriasObj = new Ferias();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)


$ferias_vencidas = $feriasObj->listaFeriasVencidas($id_regiao);
$ferias_vencer = $feriasObj->listaFeriasVencer($id_regiao);

$count_vencidas = count($ferias_vencidas);
$count_vencer = count($ferias_vencer);

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>"Férias");
$breadcrumb_pages = array("Gestão de RH"=>"../");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Férias</title>
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
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS</h2></div>
                    <h3>Férias</h3>

                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
                        <li role="presentation" class="active"><a href="#avisos" role="tab" data-toggle="tab">Avisos</a></li>
                        <li role="presentation"><a href="#lista" role="tab" data-toggle="tab">Lista de Funcionários</a></li>
                        <li role="presentation"><a href="#relatorio" role="tab" data-toggle="tab">Relatório de Férias</a></li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="avisos">

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="panel panel-info">
                                        <div class="panel-heading"><span class="badge"><?= $count_vencer ?></span> Férias a Vencer</div>
                                        <div class="panel-body overflow" style="max-height: 250px;">
                                            <?php if ($count_vencer > 0) { ?>
                                                <table class="table table-hover table-striped text-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Nome</th>
                                                            <th>Projeto</th>
                                                            <th>Data de vencimento</th>
                                                            <th style="width: 125px;">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($ferias_vencer as $value) { ?>
                                                            <tr>
                                                                <td><?= $value['id_clt'] ?></td>
                                                                <td><?= $value['nome'] ?></td>
                                                                <td><?= $value['id_projeto'] . ' - ' . $value['projeto_nome'] ?></td>
                                                                <td><?= date('d/m/Y', $value['data_vencimento']) ?></td>
                                                                <td><span class="text-info">Vence em <?= (int) $value['vencendo'] ?> dias</span></td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            <?php }else {
                                                ?>
                                                <div class="bs-callout bs-callout-info">
                                                    <p class="text-info"><i class="fa fa-info-circle"></i> Não há CLT com férias a vencer.</p>
                                                </div>
                                                <?php } ?>
                                        </div><!-- /.panel-body -->
                                    </div><!-- /.panel-primary -->
                                </div><!-- /.col-lg-6 -->

                                <div class="col-lg-6">
                                    <div class="panel panel-danger">
                                        <div class="panel-heading"><span class="badge"><?= $count_vencidas ?></span> Férias Vencidas</div>
                                        <div class="panel-body overflow" style="max-height: 250px;">
                                            <?php if ($count_vencidas > 0) { ?>
                                                <table class="table table-hover table-striped text-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Nome</th>
                                                            <th>Projeto</th>
                                                            <th>Data de vencimento</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($ferias_vencidas as $value) { ?>
                                                            <tr>
                                                                <td><?= $value['id_clt'] ?></td>
                                                                <td><?= $value['nome'] ?></td>
                                                                <td><?= $value['id_projeto'] . ' - ' . $value['projeto_nome'] ?></td>
                                                                <td><?= date('d/m/Y', $value['data_vencimento']) ?></td>
                                                                <td><span class="text-danger">Vencida</span></td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            <?php } else {
                                                ?>
                                                <div class="bs-callout bs-callout-danger">
                                                    <p class="text-danger"><i class="fa fa-info-circle"></i> Não há CLT com férias vencidas.</p>
                                                </div>
                                                <?php }
                                            ?>
                                        </div><!-- /.panel-body -->
                                    </div><!-- /.panel-primary -->
                                </div><!-- /.col-lg-6 -->

                            </div><!-- /.row -->
                        </div><!-- /#avisos -->

                        <div role="tabpanel" class="tab-pane" id="lista">

                            <form class="form-horizontal" role="form" id="form-lista" method="post">
                                <input type="hidden" name="home" id="home" value="" />
                                <input type="hidden" name="regiao" id="regiao" value="<?= $id_regiao ?>">
                                <input type="hidden" name="id_clt" id="id_clt" value="">
                                <div class="panel panel-default">
                                    <div class="panel-body">

                                        <div class="form-group">
                                            <label for="projeto_lista" class="col-lg-2 control-label">Projeto:</label>
                                            <div class="col-lg-9">
                                                <select name="projeto" id="projeto_lista" class="form-control projeto">
                                                    <option>-- Todos os Projetos --</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="nome_clt" class="col-lg-2 control-label">Filtro:</label>
                                            <div class="col-lg-9"><input type="text" name="pesquisa" id="pesquisa" class="form-control" placeholder="Nome do CLT, CPF, Matrícula"></div>
                                        </div>

                                    </div><!-- /.panel-body -->

                                    <div class="panel-footer text-right">
                                        <input type="button" value="Consultar" id="submit-lista" class="btn btn-primary">
                                    </div>

                                </div><!-- /.panel -->
                                
                                <div id="retorno-lista"></div>
                                
                            </form>

                        </div><!-- /#lista -->

                        <div role="tabpanel" class="tab-pane" id="relatorio">
                            <form class="form-horizontal" role="form" id="form-rel" method="post">
                                <input type="hidden" name="regiao" id="regiao" value="<?= $id_regiao ?>">
                                <div class="panel panel-default">
                                    <div class="panel-body">

                                        <div class="form-group">
                                            <label for="projeto_rel" class="col-lg-2 control-label">Projeto:</label>
                                            <div class="col-lg-9">
                                                <select name="projeto" id="projeto_rel" class="projeto form-control">
                                                    <option>-- Todos os Projetos --</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="mes_ini" class="col-lg-2 control-label">Início:</label>
                                            <div class="col-lg-4"><?= montaSelect(mesesArray(), null, array('name' => 'mes_ini', 'id' => 'mes_ini', 'class' => 'form-control')) ?></div>
                                            <div class="col-lg-4"><?= montaSelect(anosArray(date('Y') - 5, date('Y') + 1, array('-1' => '« Selecione o ano »')), null, array('name' => 'ano_ini', 'id' => 'ano_ini', 'class' => 'form-control')) ?></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="mes_ini" class="col-lg-2 control-label">Fim:</label>
                                            <div class="col-lg-4">
                                                <?= montaSelect(mesesArray(), null, array('name' => 'mes_fim', 'id' => 'mes_fim', 'class' => 'form-control')) ?>
                                            </div>
                                            <div class="col-lg-4">
                                                <?= montaSelect(anosArray(date('Y') - 5, date('Y') + 1, array('-1' => '« Selecione o ano »')), null, array('name' => 'ano_fim', 'id' => 'ano_fim', 'class' => 'form-control')) ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="funcao" class="col-lg-2 control-label">Função:</label>
                                            <div class="col-lg-9"><select name="funcao" id="funcao" class="form-control"><option value="">« Selecione »</option></select></div>
                                        </div>
                                    </div><!-- /.panel-body -->

                                    <div class="panel-footer text-right">
                                        <input type="button" value="Consultar" id="submit-relatorio" class="btn btn-primary">
                                    </div>

                                </div><!-- /.panel -->
                                
                                <div id="retorno-relatorio"></div>
                            </form>
                        </div><!-- /#relatorio -->
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

        <script src="../../resources/js/rh/ferias/index2.js"></script>
    </body>
</html>
