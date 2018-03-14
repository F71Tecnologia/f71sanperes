<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/ProjetoClass.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$id_projeto = validatePost('id_projeto');

if ($id_projeto === NULL) {
    header('Location: ' . _URL . 'rh/projeto/');
}

$objProj = new ProjetoClass();
$projeto = $objProj->getProjeto($id_projeto);
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Projeto</title>

        <link rel="shortcut icon" href="favicon.ico" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">

        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../js/global.js"></script>

        <script src="../../js/chartjs/knockout-2.2.1.js"></script>
        <script src="../../js/chartjs/globalize.js"></script>
        <script src="../../js/chartjs/dx.chartjs.js"></script>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">
            <div class="page-header">
                <h2>Projeto <?php echo $projeto['nome'] ?></h2>
                <div class="bs-callout bs-callout-info">
                    <p><?php echo $projeto['descricao'] ?></p>
                </div>
            </div>

            <div class="col-lg-12">

                <div class="bs-component">
                    <ul class="nav nav-tabs" style="margin-bottom: 15px;">
                        <li class="active"><a href="#acoes" data-toggle="tab">Ações</a></li>
                        <li><a href="#partici" data-toggle="tab">Participantes</a></li>
                        <li><a href="#relatorios" data-toggle="tab">Relatórios</a></li>
                        <li><a href="#estatisticas" data-toggle="tab">Estatísticas</a></li>
                    </ul>
                    <div id="myTabContent" class="tab-content">
                        <div class="tab-pane fade active in" id="acoes">
                            Botões
                        </div>
                        <div class="tab-pane fade active in" id="partici">
                            A
                        </div>
                        <div class="tab-pane fade" id="relatorios">

                            <div class="alert alert-dismissable alert-warning">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <h4>Atenção!</h4>
                                <p>Nessa versão, os relatórios estarão no banco de dados, e havará permissão específica para cada relatório/funcionário.</p>
                            </div>

                            <div class="bs-component">
                                <div class="list-group">
                                    <a href="#" class="list-group-item">
                                        <h4 class="list-group-item-heading">List group item heading</h4>
                                    </a>
                                    <a href="#" class="list-group-item">
                                        <h4 class="list-group-item-heading">List group item heading</h4>
                                        <p class="list-group-item-text">Donec id elit non mi porta gravida at eget metus. Maecenas sed diam eget risus varius blandit.</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="estatisticas">

                        </div>
                    </div>
                </div>
            </div>
            
            <div id="chartContainer" class="containers hidden" style="height: 300px; width: 250px;"></div>

            <footer>
                <div class="row">
                    <div class="page-header"></div>
                    <div class="pull-right"><a href="#top">Voltar ao topo</a></div>
                    <div class="col-lg-12">
                        <p>Pay All Fast 3.0</p>
                        <p>Todos os direitos reservados <a href="http://f71.com.br" rel="nofollow" target="_blank">F71 Sistemas</a>.</p>
                    </div>
                </div>
            </footer>
        </div>


        <script>
            $(function() {
                $(".pointer").click(function() {
                    var id = $(this).data('key');
                    $("#id_projeto").val(id);
                    $("#form1").submit();
                });
                
                $(".nav-tabs a[href='#estatisticas']").click(function(){
                    montaGrafico();
                    $("#chartContainer").removeClass("hidden");
                });
            });
        </script>

        <script>
            var montaGrafico = function() {
                var dataSource = [
                    {language: "Masculino", percent: 60.0},
                    {language: "Feminino", percent: 40.0}
                ];

                $("#chartContainer").dxPieChart({
                    dataSource: dataSource,
                    title: "Top internet languages",
                    legend: {
                        horizontalAlignment: "center",
                        verticalAlignment: "bottom"
                    },
                    series: [{
                            argumentField: "language",
                            valueField: "percent",
                            label: {
                                visible: true,
                                connector: {
                                    visible: true,
                                    width: 0.5
                                },
                                format: "fixedPoint",
                                customizeText: function(point) {
                                    return point.argumentText + ": " + point.valueText + "%";
                                }
                            },
                            smallValuesGrouping: {
                                mode: "smallValueThreshold",
                                threshold: 4.5
                            },
                        }]
                });
            };
        </script>
    </body>
</html>