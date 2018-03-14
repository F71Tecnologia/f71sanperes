<?php
header("Location: principalrh.php");exit;
if (empty($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
}

include("../conn.php");
include("../wfunction.php");
include("../classes/FolhaClass.php");
include("../classes/BotoesClass.php");
include("../classes/EventoClass.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
//CARREGANDO MENU DE ACORDO COM AS PERMISSOES DA PESSOA
$botoes = new BotoesClass($dadosHeader['defaultPath']);
$botoesMenu = $botoes->getBotoesMenuModulo(2);

//LISTA DE EVENTOS
$objEvento = new Eventos();
$listaEventos = $objEvento->listaEventos();
$dadosEventos = $objEvento->getTerminandoEventos(date("Y-m-d"), $usuario['id_regiao'], null, null, 10);
$dadosArrayEventos = $objEvento->array_dados;
$status = $objEvento->getStatus();

echo "<!--";
print_r($dadosArrayEventos);
echo "-->";

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Gestão de RH");
//$breadcrumb_pages = array("Gestão de RH"=>"../", "Seguro Desemprego"=>"index2.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Gestão de RH</title>

        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <!--<link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">-->
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" type="text/css">
        <!--link href="../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css"-->

    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>

        <div class="container">

            <div class="row">

                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS</h2></div>
                    <!--ul class="breadcrumb">
                        <li><a href="../">Home</a></li>
                        <li><a href="javascript:;" data-key="3" class="return_principal">Recursos Humanos</a></li>
                        <li class="active">Gestão de RH</li>
                    </ul-->
                    <div class="bs-component">
                        <ul class="nav nav-tabs" style="margin-bottom: 15px;">
                            <li class="active"><a href="#avisos" data-toggle="tab">Avisos</a></li>
                            <?php foreach ($botoesMenu as $k => $btMenu) { ?>
                                <li><a href="#<?php echo $k ?>" data-toggle="tab"><?php echo $btMenu ?></a></li>
                            <?php } ?>
                        </ul>
                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane fade active in" id="avisos">
                                <div class="col-lg-8">
                                    <?php
                                    foreach ($dadosArrayEventos as $key => $dados) {
                                        if ($key == 1) {
                                            ?>
                                            <div class="panel panel-warning">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title">Funcionários com Eventos expirados Hoje</h3>
                                                </div>
                                                <div class="panel-body overflow" style="max-height: 450px;">

                                                    <table class='table table-striped table-hover'>
                                                        <thead>
                                                        <th>Data Retorno</th>
                                                        <th>Tipo de Evento</th>
                                                        <th>Projeto</th>
                                                        <th>Nome</th>
                                                        <th style="width:85px;">&emsp;</th>
                                                        <!--<th>Dias Vencidos</th>-->
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            foreach ($dados as $key1 => $valores) {
                                                                for ($i = 0; $i < count($valores); $i++) {
                                                                    ?>
                                                                    <tr>
                                                                        <td><?php echo ($valores[$i]['pericia'] == 1) ? $valores[$i]['data_retorno'] : $valores[$i]['data_final'] ?></td>
                                                                        <td><?php echo $status[$key1] ?></td>
                                                                        <td><?php echo $valores[$i]['projeto'] ?></td>
                                                                        <td><?php echo $valores[$i]['nome_clt'] ?></td>
                                                                        <td>
                                                                            <span class="btn-group">
                                                                                <a href='javascript:;' class='btn btn-sm btn-default voltar' data-key="<?php echo $valores[$i]["id_evento"]; ?>" data-retorno="<?php echo $valores[$i]["data_retorno"]; ?>"
                                                                                   data-toggle="tooltip" data-placement="top" title="Voltar p/ Atv. Normal">
                                                                                    <i class="fa fa-undo"></i>
                                                                                </a>
                                                                                <?php if ($valores[$i]["prorrogavel"] == 1) { ?>
                                                                                    <a href='javascript:;' class='btn btn-sm btn-default prorrogar' data-id="<?php echo $valores[$i]["id_evento"]; ?>" data-retorno="<?php echo $valores[$i]["data_retorno"]; ?>"
                                                                                       data-toggle="tooltip" data-placement="top" title="Prorrogar Evento">
                                                                                        <i class="fa fa-calendar"></i>
                                                                                    </a>    
                                                                                <?php } ?>
                                                                            </span>
                                                                        </td>
                                                                    <!--<td><?php echo abs($valores[$i]['dias_restantes']) ?></td>-->
                                                                    </tr>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>

                                                </div>
                                            </div>
                                        <?php } else if ($key == 0) { ?>
                                            <div class="panel panel-danger">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title">Funcionários com Eventos expirados</h3>
                                                </div>
                                                <div class="panel-body overflow" style="max-height: 450px;">

                                                    <table class='table table-striped table-hover'>
                                                        <thead>
                                                        <th>Data Retorno</th>
                                                        <th>Tipo de Evento</th>
                                                        <th>Projeto</th>
                                                        <th>Nome</th>
                                                        <th>Dias Vencidos</th>
                                                        <th style="width:85px;">&emsp;</th>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            foreach ($dados as $key2 => $valores) {
                                                                for ($i = 0; $i < count($valores); $i++) {
                                                                    ?>
                                                                    <tr>
                                                                        <td><?php echo ($valores[$i]['pericia'] == 1) ? $valores[$i]['data_retorno'] : $valores[$i]['data_final'] ?></td>
                                                                        <td><?php echo $status[$key2] ?></td>
                                                                        <td><?php echo $valores[$i]['projeto'] ?></td>
                                                                        <td><?php echo $valores[$i]['nome_clt'] ?></td>
                                                                        <td><?php echo abs($valores[$i]['dias_restantes']) ?></td>
                                                                        <td>
                                                                            <span class="btn-group">
                                                                                <a href='javascript:;' class='btn btn-sm btn-default voltar' data-key="<?php echo $valores[$i]["id_evento"]; ?>" data-retorno="<?php echo $valores[$i]["data_retorno"]; ?>" 
                                                                                   data-toggle="tooltip" data-placement="top" title="Voltar p/ Atv. Normal">
                                                                                    <i class="fa fa-undo"></i>
                                                                                </a>
                                                                                <?php if ($valores[$i]["prorrogavel"] == 1) { ?>
                                                                                    <a href='javascript:;' class='btn btn-sm btn-default prorrogar' data-id="<?php echo $valores[$i]["id_evento"]; ?>" data-retorno="<?php echo $valores[$i]["data_retorno"]; ?>" 
                                                                                       data-toggle="tooltip" data-placement="top" title="Prorrogar Evento">
                                                                                        <i class="fa fa-calendar"></i>
                                                                                    </a>    
                                                                                <?php } ?>
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>

                                                </div>
                                            </div>
                                        <?php } else if ($key == 2) { ?>
                                            <div class="panel panel-primary">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title">Funcionários para retorno de Eventos</h3>
                                                </div>
                                                <div class="panel-body overflow" style="max-height: 450px;">

                                                    <table class='table table-striped table-hover'>
                                                        <thead>
                                                        <th>Data Retorno</th>
                                                        <th>Tipo de Evento</th>
                                                        <th>Projeto</th>
                                                        <th>Nome</th>
                                                        <th>Dias Restantes</th>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            foreach ($dados as $key3 => $valores) {
                                                                for ($i = 0; $i < count($valores); $i++) {
                                                                    ?>
                                                                    <tr>
                                                                        <td><?php echo ($valores[$i]['pericia'] == 1) ? $valores[$i]['data_retorno'] : $valores[$i]['data_final'] ?></td>
                                                                        <td><?php echo $status[$key3] ?></td>
                                                                        <td><?php echo $valores[$i]['projeto'] ?></td>
                                                                        <td><?php echo $valores[$i]['nome_clt'] ?></td>
                                                                        <td><?php echo $valores[$i]['dias_restantes'] ?></td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        <?php } 
                                    }
                                    if (empty($dadosArrayEventos)) {
                                            ?>
                                            <div class="bs-callout bs-callout-info">
                                                <h4 class="text-info"> <i class="fa fa-info-circle"></i> Não há Eventos terminando em breve.</h4>
                                            </div>
                                            <?php
                                        }
                                    ?>


                                </div>
                                
                                <div class="col-lg-4">
                                    <div class="panel panel-success">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Resumo</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="bs-component">
                                                <ul class="list-group">
                                                    <?php foreach ($listaEventos as $val) { ?>
                                                        <li class="list-group-item">
                                                            <span class="badge"><?php echo $objEvento->contaCltPorEvento($val['codigo'], $usuario['id_regiao']); ?></span>
                                                            <?php echo $val['codigo'] . " - " . $val['especifica']; ?>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>

                            <?php foreach ($botoesMenu as $k => $btMenu) { ?>
                                <div class="tab-pane fade" id="<?php echo $k ?>">
                                    <div class="detalhes-modulo">
                                        <?php echo $botoes->getHtmlBotoesModulo($k, 3) ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

            </div>

            <?php include_once '../template/footer.php'; ?>

        </div><!-- /.container -->

        <!-- MODAL DE PRORROGACAO DE EVENTOS -->
        <form name="eventos" id="form1" action="">
            <input type="hidden" name="home" id="home" value="" />
            <input type="hidden" name="id_evento" id="id_evento" value="" />
            <input type="hidden" name="id_user" id="id_user" value="<?php echo $_COOKIE['logado'] ?>" />
            <input type="hidden" name="data_retorno" id="data_retorno" value="" />
            <div id="modal_motivo" class="minus">
                <p style="text-align: left;">
                    <label for="dias" style=" font-weight: bold;" >Quantidade de dias (a partir da data atual de retorno):</label><br />
                    <input type="number" name="dias"  min="0"  id="dias" style="width: 4em; height: 30px; margin: 3px 0px;" > <button type="button" id="calc-data">Calcular Prorrogação</button>
                </p>

                <p>
                    <label for="data_prorrogada" style="float: left; font-weight: bold;" >Data de Prorrogação:</label><br />
                    <input style="width: 425px; height: 30px; margin: 3px 0px;" type="text" name="data_prorrogada" id="data_prorrogada" value="" />
                </p>

                <p>
                    <label for="motivo" style="float: left; font-weight: bold;">Digite o motivo:</label><br />
                    <textarea name="motivo" style="width: 425px; height: 80px; margin: 3px 0px;"></textarea>
                </p>
                <input type="submit" name="finalizar" id="finalizar" style="float: right" value="Finalizar" />
                <div id="message_erro"></div>
            </div>
        </form>
        <!-- FIM DO MODAL DE PRORROGACAO DE EVENTOS -->

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../resources/js/rh/eventos/prorrogar_evento.js"></script>
        <script src="../js/jquery.maskedinput.min.js" type="text/javascript"></script>

        <script src="../resources/js/rh/index.js" type="text/javascript"></script>
    </body>
</html>