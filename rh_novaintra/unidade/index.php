<?php
session_start();

if (!isset($_COOKIE['logado']))
{
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/UnidadeClass.php');

$usuario = carregaUsuario();

$id_regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];

$result = getUnidade($id_regiao, $id_projeto);
$total_unidade = mysql_num_rows($result);

$filtro = false;

if ((isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) || (isset($_SESSION['voltarCurso'])))
{
    $filtro = true;
    if (isset($_SESSION['voltarCurso']))
    {
        $_REQUEST['regiao'] = $_SESSION['voltarCurso']['id_regiao'];
        $_REQUEST['projeto'] = $_SESSION['voltarCurso']['id_projeto'];
        unset($_SESSION['voltarCurso']);
    }
    $result = getUnidade($_REQUEST['regiao'], $_REQUEST['projeto']);
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
if (isset($_REQUEST['projeto']) && isset($_REQUEST['regiao']))
{
    $projetoR = $_REQUEST['projeto'];
    $regiaoR = $_REQUEST['regiao'];
} elseif (isset($_SESSION['projeto']) && isset($_SESSION['regiao']))
{
    $projetoR = $_SESSION['projeto'];
    $regiaoR = $_SESSION['regiao'];
} elseif (isset($_SESSION['projeto_select']) && isset($_SESSION['regiao_select']))
{
    $projetoR = $_SESSION['projeto_select'];
    $regiaoR = $_SESSION['regiao_select'];
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Gestão de Unidades");
//$breadcrumb_pages = array("Gestão de RH"=>"../../");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Gestão de Unidades</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <style>
            body {
                background: #72cffa;
            }

            /* .modal-transparent */

            .modal-transparent {
                background: transparent;
            }
            .modal-transparent .modal-content {
                background: transparent;
            }
            .modal-backdrop.modal-backdrop-transparent {
                background: #ffffff;
            }
            .modal-backdrop.modal-backdrop-transparent.in {
                opacity: .9;
                filter: alpha(opacity=90);
            }

            /* .modal-fullscreen */

            .modal-fullscreen {
                background: transparent;
            }
            .modal-fullscreen .modal-content {
                background: transparent;
                border: 0;
                -webkit-box-shadow: none;
                box-shadow: none;
            }
            .modal-backdrop.modal-backdrop-fullscreen {
                background: #ffffff;
            }
            .modal-backdrop.modal-backdrop-fullscreen.in {
                opacity: .97;
                filter: alpha(opacity=97);
            }

            /* .modal-fullscreen size: we use Bootstrap media query breakpoints */

            .modal-fullscreen .modal-dialog {
                margin: 0;
                margin-right: auto;
                margin-left: auto;
                width: 100%;
            }
            @media (min-width: 768px) {
                .modal-fullscreen .modal-dialog {
                    width: 750px;
                }
            }
            @media (min-width: 992px) {
                .modal-fullscreen .modal-dialog {
                    width: 970px;
                }
            }
            @media (min-width: 1200px) {
                .modal-fullscreen .modal-dialog {
                    width: 1170px;
                }
            }

            /* centering styles for jsbin */
            html,
            body {
                width:100%;
                height:100%;
            }
            html {
                display:table;
            }
            body {
                display:table-cell;
                vertical-align:middle;
            }
            body > .btn {
                display: block;
                margin: 0 auto;
            }
        </style>

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-note.css.css" rel="stylesheet" type="text/css">

        <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Gestão de Unidades</small></h2></div>
                </div>
            </div>
            <!--resposta de algum metodo realizado-->
            <?php
            if (!empty($_SESSION['MESSAGE']))
            {
                ?>
                <div id="message-box" class="alert alert-dismissable alert-warning <?= $_SESSION['MESSAGE_COLOR']; ?> alinha2">
                    <?=
                    $_SESSION['MESSAGE'];
                    session_destroy();
                    ?>
                </div>
            <?php } ?>
            <form id="form1" class="form-horizontal" method="post">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="control-label col-xs-1">Região:</label> 
                            <div class="col-xs-5"><?= montaSelect(GlobalClass::carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' class='form-control required[custom[select]]'") ?></div>
                            <label class="control-label col-xs-1">Projeto:</label> 
                            <div class="col-xs-5"><?= montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto' name='projeto' class='form-control required[custom[select]]'") ?></div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?= $projetoR ?>" />
                        <input type="hidden" name="hide_regiao" id="hide_regiao" value="<?= $regiaoR ?>" />
                        <input type="hidden" name="unidade" id="unidade" value="" />
                        <input type="hidden" name="home" id="home" value="" />
                        <?php
                        if ($filtro)
                        {
                            ?>
                        <?php if ($_COOKIE['logado'] != 395) { ?>
                            <button type="submit" class="btn btn-success" value="Nova Unidade" name="novo" id="novaUnidade" /><span class="fa fa-plus"></span> Nova Unidade</button>
                        <?php } ?>
                        <?php } ?>
                        <button type="submit" class="btn btn-primary" value="Filtrar" name="filtrar" onclick="" />Filtrar</button>
                    </div>
                </div>
            </form>
            <?php
            if ($filtro)
            {
                if ($total_unidade > 0)
                {
                    ?>
                    <button type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Exportar para Excel</button>
                    <a data-toggle="modal" data-target="#detailModal" style="cursor: pointer"><button type="button" onclick="carregar_mapa();" value="Mapa" class="btn btn-success" data-target="#detailModal"><i class="fa fa-map-marker"></i> Mapa</button></a>
                    <table id="tbRelatorio" class="table table-hover table-condensed table-bordered">
                        <thead>
                            <tr class="bg-primary valign-middle">
                                <th>Cód.</th>
                                <th>Qtd. de Vínculos</th>
                                <th>Unidade</th>
                                <th>Cód. WEBSAASS</th>
                                <th>Cód. Serviço 1</th>
                                <th>Cód. Serviço 2</th>
                                <th>Telefone</th>
                                <th>Endereço</th>
                                <th>Responsável</th>
                                <th colspan="3" class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $contratacao = "";
                            $i = 0;
                            while ($row = mysql_fetch_assoc($result))
                            {
                                $clt = getRhClt($row['id_unidade']);

                                if (!is_null($row['latitude']))
                                {
                                    $lista[$i] = $row;
                                    $i++;
                                }

                                if ($contratacao != $row['tipo_contratacao_nome'])
                                {
                                    $contratacao = $row['tipo_contratacao_nome'];
                                    echo "<tr class='tr_contratacao'><td colspan='9'>" . ucwords($row['tipo_contratacao_nome']) . "</td><tr />";
                                }
                                ?>
                                <tr class="valign-middle" id="<?= $row['id_unidade'] ?>">
                                    <td><?= $row['id_unidade'] ?></td>
                                    <td><?= $clt ?></td>
                                    <td><?= strtoupper($row['unidade']) ?></td>
                                    <td><?= $row['cod_websaass'] ?></td>
                                    <td><?= $row['cod_servico1'] ?></td>
                                    <td><?= $row['cod_servico2'] ?></td>
                                    <td><?= $row['tel'] ?></td>
                                    <td><?= strtoupper($row['endereco']) ?></td>
                                    <td><?= strtoupper($row['responsavel']) ?></td>
                                    <td class="text-center"><a class="btn btn-xs btn-primary"><i title="Visualizar" class="bt-image fa fa-search" data-type="visualizar" data-key="<?= $row['id_unidade'] ?>"></i></a></td>
                                    <?php if ($_COOKIE['logado'] != 395) { ?>
                                    <td class="text-center"><a class="btn btn-xs btn-warning"><i title="Editar" class="bt-image fa fa-pencil" data-type="editar" data-key="<?= $row['id_unidade'] ?>"></i></a></td>
                                    <td class="text-center"><a class="btn btn-xs btn-danger"><i title="Excluir" class="bt-image fa fa-trash-o" data-clt="<?= $clt ?>" data-type="excluir" data-key="<?= $row['id_unidade'] ?>"></i></a></td>
                                    <?php } ?>
                                    <!--<td class="text-center"><img src="../../imagens/icones/icon-copy.png" title="Duplicar" class="bt-image" data-type="duplicar" data-key="<?php //echo $row['id_prestador'];            ?>" /></td>-->
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <?php
                } else
                {
                    ?>
                    <div class="col-xs-12">
                        <div class="alert alert-dismissable alert-warning">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>Nenhum registro encontrado!</strong>
                        </div>
                    </div>

                    <?php
                }
            }
            ?>
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
                $(function () {
                    $("#regiao").ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, null, "projeto");

                    $(".bt-image").on("click", function () {
                        var action = $(this).data("type");
                        var key = $(this).data("key");
                        var emp = $(this).parents("tr").find("td:first").next().html();
                        var clt = $(this).data("clt");

                        if (action === "visualizar") {
                            $("#unidade").val(key);
                            $("#form1").attr('action', 'detalhes_unidade.php');
                            $("#form1").submit();
                        } else if (action === "editar") {
                            $("#unidade").val(key);
                            $("#form1").attr('action', 'form_unidade.php');
                            $("#form1").submit();
                        } else if (action === "excluir") {

                            if (clt != 0) {
                                bootAlert("Unidade não pode ser excluida, pois existe CLT vinculada a mesma", "Exclusão de Unidade", null, 'danger');
                            } else {
                                bootConfirm("Você deseja realmente excluir esta unidade?", "Exclusão de Unidade", function (data) {
                                    if (data) {
                                        if (data == true) {
                                            $("#" + key).remove();
                                            $.ajax({
                                                url: "del_unidade.php?id=" + key
                                            });
                                        }
                                    }
                                }, 'warning');
                            }
                        }
                    });

                    $("#novaUnidade").click(function () {
                        $("#form1").attr('action', 'form_unidade.php');
                        $("#form1").submit();
                    });
                });
        </script>

        <!-- Modal Mapa -->

        <div class="modal modal-fullscreen fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="background-color: white">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="padding: 5px; border: none">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title" id="myModalLabel"></h4>
                    </div>
                </div>
            </div>
            <div id="mapa_local" style="width: 100%; height: 100%;"></div>
        </div>
        <!-- Fim Modal Mapa -->

        <script type="text/javascript" >
            var map = null;
            function carregar_mapa() {

                var latlng = new google.maps.LatLng(-23.550991, -46.628445);

                var myOptions = {
                    zoom: 12,
                    center: latlng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };

                //criando o mapa
                map = new google.maps.Map(document.getElementById("mapa_local"), myOptions);
<?php
foreach ($lista as $lin):
    if ($lin['latitude'] != $pas_lat):
        ?>
                        var a<?= $lin['id_unidade'] ?> = new google.maps.LatLng(<?= $lin['latitude'] ?>, <?= $lin['longitude'] ?>);
                        b<?= $lin['id_unidade'] ?> = new google.maps.Marker({
                            position: a<?= $lin['id_unidade'] ?>,
                            map: map,
                            title: "<?= $lin['unidade'] ?> (<?= $lin['endereco'] ?>)"
                        });
        <?php
    endif;
    $pas_lat = $lin['latitude'];
endforeach;
?>

                tfocus(map);

            }


            var tfocus = function (map) {
                setTimeout(function () {
                    google.maps.event.trigger(map, 'resize');
                    var center = new google.maps.LatLng(-23.550991, -46.628445);
                    map.panTo(center);
                }, 1000);
            };
        </script>        

    </body>
</html>