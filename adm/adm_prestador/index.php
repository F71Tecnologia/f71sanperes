<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../classes/global.php');
include("../../wfunction.php");
include('../../classes/PrestadorServicoClass.php');
include('../../classes_permissoes/acoes.class.php');
$usuario = carregaUsuario();
//$Master = $usuario['id_master'];

$filtro = false;
$objAcoes = new Acoes();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "2", "area" => "Administrativo", "id_form" => "form1", "ativo" => "Prestador de Serviço");
$breadcrumb_pages = array("Principal" => "../../admin/index.php");

if ((isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) || (isset($_SESSION['voltarPrestador']))) {
    $filtro = true;
    if (isset($_SESSION['voltarPrestador'])) {
        $_REQUEST['regiao'] = $_SESSION['voltarPrestador']['id_regiao'];
        $_REQUEST['projeto'] = $_SESSION['voltarPrestador']['id_projeto'];
        unset($_SESSION['voltarPrestador']);
    }

    $rs = montaQuery("prestadorservico", "*,DATE_FORMAT(contratado_em, '%d/%m/%Y') AS contratado_embr, DATE_FORMAT(encerrado_em, '%d/%m/%Y') AS encerrado_embr ", "id_regiao = {$_REQUEST['regiao']} AND status = 1 AND id_projeto = {$_REQUEST['projeto']} AND (encerrado_em >= CURRENT_DATE() OR encerrado_em IS NULL)", "prestador_tipo,c_razao", null, null, false);
    $num_rows = mysql_num_rows($rs);

    //enc = contratos encerrados
    $rs_enc = montaQuery("prestadorservico", "*,DATE_FORMAT(contratado_em, '%d/%m/%Y') AS contratado_embr, DATE_FORMAT(encerrado_em, '%d/%m/%Y') AS encerrado_embr ", "id_regiao = {$_REQUEST['regiao']} AND status = 1 AND id_projeto = {$_REQUEST['projeto']} AND encerrado_em < CURRENT_DATE()", "c_razao", null, null, false);
    $num_rows_enc = mysql_num_rows($rs_enc);

    //cria matriz dividida por prestador_tipo
    while ($row1 = mysql_fetch_assoc($rs)) {
        $row_prestador[$row1['prestador_tipo']][$row1['id_prestador']] = $row1;
    }

    //Array com os tipos de contrato
    $arrTipos = array(
        "1" => "Pessoa Jurídica",
        "2" => "Pessoa Jurídica - Cooperativa",
        "3" => "Pessoa Física",
        "4" => "Pessoa Jurídica - Prestador de Serviço",
        "5" => "Pessoa Jurídica - Administradora",
        "6" => "Pessoa Jurídica - Publicidade",
        "7" => "Pessoa Jurídica Sem Retenção",
        "9" => "Pessoa Jurídica - Médico");


    $query = "SELECT C.id_prestador, COUNT(B.prestador_documento_id) AS cnt
        FROM prestador_tipo_doc AS A
        LEFT JOIN prestador_documentos AS B ON (A.prestador_tipo_doc_id = B.prestador_tipo_doc_id)
        LEFT JOIN prestadorservico AS C ON (B.id_prestador = C.id_prestador)
        WHERE B.data_vencimento < CURDATE()
        AND id_regiao = {$_REQUEST['regiao']}
        AND id_projeto = {$_REQUEST['projeto']}
        GROUP BY C.id_prestador";
    $result = mysql_query($query);
    $i = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $id_doc_vencido[$i] = $row['id_prestador'];
        $qtd_doc_vencido[$row['id_prestador']] = $row['cnt'];
        $i++;
    }

    $query_enc = "SELECT C.id_prestador, COUNT(B.prestador_documento_id) AS cnt
        FROM prestador_tipo_doc AS A
        LEFT JOIN prestador_documentos AS B ON (A.prestador_tipo_doc_id = B.prestador_tipo_doc_id)
        LEFT JOIN prestadorservico AS C ON (B.id_prestador = C.id_prestador)
        WHERE B.data_vencimento < CURDATE()
        AND id_regiao = {$_REQUEST['regiao']}
        AND id_projeto = {$_REQUEST['projeto']}
        GROUP BY C.id_prestador";

    $result_enc = mysql_query($query_enc);
    $i_enc = 0;
    while ($row_enc = mysql_fetch_assoc($result_enc)) {
        $id_doc_vencido_enc[$i_enc] = $row_enc['id_prestador'];
        $qtd_doc_vencido_enc[$row_enc['id_prestador']] = $row_enc['cnt'];
        $i_enc++;
    }
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$regiaoR = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : null;
?>

<!DOCTYPE html>
<html>
    <head>
        <title>:: Intranet :: FORNECEDOR DE SERVIÇOS E PRODUTOS</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link rel="shortcut icon" href="../favicon.ico">

        <!--Custom CSS-->
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="prestador.css" rel="stylesheet" type="text/css" />

        <!--Jquery-->
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap.js" ></script>
        <script src="../../resources/js/bootstrap-dialog.min.js" ></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <!--<script src="../../resources/js/bootstrap-dialog.min.js" ></script>-->
        <script src="../../js/global.js" type="text/javascript"></script>
        <script src="../../resources/js/main.js" ></script>
        <script type="text/javascript" src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>


        <!-- Bootstrap -->
        <!--<link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">-->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet" media="screen">
        <!--<link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">-->
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <!--<link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">-->
        <!--<link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">-->

        <script>
            $(function () {
                $("#regiao").ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, null, "projeto");

                $(".bt-image").on("click", function () {
                    var action = $(this).data("type");
                    var key = $(this).data("key");
                    var emp = $(this).parents("tr").find("td:first").next().html();

                    //THICKBOX VISUALIZA DOCUMENTOS
                    if (action === "docs") {
                        thickBoxIframe(emp, "actions.php", {prestador: key, method: "getDocs"}, "625-not", "500");
                    } else if (action === "duplicar") {
                        $("#prestador").val(key);
                        $("#form1").attr('action', 'duplicar_prestador.php');
                        $("#form1").submit();
                    } else if (action === "prestador") {
                        $("#prestador").val(key);
                        $("#form1").attr('action', 'ver_prestador.php');
                        $("#form1").submit();
                    } else if (action === "editar") {
                        $("#prestador").val(key);
                        $("#form1").attr('action', 'form_prestador.php');
                        $("#form1").submit();
                    }else if (action == "remover"){
                        bootConfirm('Tem certeza que deseja excluir?', 'Exclusão', function(res){
                            if(res){
                                $("#prestador").val(key);
                                $("#form1").attr('action', 'remover_prestador.php');
                                $("#form1").submit();
                            }
                        }, 'warning')
                    }
                });

                $("#novoPrest").click(function () {
                    $("#form1").attr('action', 'form_prestador.php');
                    $("#form1").submit();
                });
            });
            function abre_processo(id) {
                $.post(window.location, {id: id, acao: 'abre_processo'}, function (data) {
                    console.log(data);
                }, 'json');
            }
        </script>

    </head>
    <body>

        <?php include("../../template/navbar_default.php"); ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-admin-header"><h2><span class="glyphicon glyphicon-cog"></span> - ADMINISTRATIVO<small> - FORNECEDOR DE SERVIÇOS E PRODUTOS</small></h2></div>
                </div>
            </div>
            <div id="alert" style="background-color:#F30;color:#FFF;font-weight:bold; padding-left:3px;"></div>
            <form class="form-horizontal" action="" method="post" name="form1" id="form1" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class="panel-heading">Filtro</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoR ?>" />
                            <input type="hidden" name="prestador" id="prestador" value="" />
                            <label class="control-label col-sm-2 first">Região</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect(GlobalClass::carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' class='form-control required[custom[select]]'") ?>
                            </div>
                            <label class="control-label col-sm-1 first">Projeto </label>
                            <div class="col-sm-4" >
                                <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto' name='projeto' class='form-control required[custom[select]]'") ?>
                            </div>
                        </div>
                    </div>

                    <div class="panel-footer hidden-print text-right controls">
                        <a href="relatorio_prestador.php" class="button btn btn-info" ><span class="fa fa-file"></span> Relatorio Prestador</a>
                        <button type="submit" class="button btn btn-primary" value="Filtrar" name="filtrar"><span class="fa fa-filter"></span> Filtrar</button>
                        <?php if ($filtro) { ?>
                            <button type="submit" class="button btn btn-success" value="Novo Prestador" name="novo" id="novoPrest"><span class="fa fa-user-plus"></span> Novo Prestador</button>
                        <?php } ?>
                    </div>

                </div>


                <?php
                if ($filtro) {
                    if ($num_rows > 0) {
                        $count = 0;
                        foreach ($row_prestador as $key => $value) {
                            ?>
                            <br/>
                            <p style="text-align: right; margin-top: 20px"><button type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar Para Excel</button></p>
                            <table id="tbRelatorio" class="table table-striped table-hover text-sm valign-middle table-bordered" >
                                <thead>
                                    <tr>
                                        <th style="text-align:center; font-size: 2em;" colspan="14"><?= $key . " - " . $arrTipos[$key] ?></th>
                                    </tr>
                                    <tr>
                                        <th>#</th>
                                        <th>Razão Social</th>
                                        <th>CNPJ</th>
                                        <th>Início</th>
                                        <th>Término</th>
                                        <th>Valor</th>
                                        <th>Quantidade Docs</th>
                                        <th>Qtd Docs Vencidos</th>
                                        <th>Contrato</th>
                                        <th>Docs</th>
                                        <th colspan="4">Açoes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($value as $row) { ?>
                                        <tr class="<?php echo ($count++ % 2 == 0) ? "odd" : "even" ?>">
                                            <td><?php echo $row['id_prestador']; ?></td>
                                            <td><?php echo $row['c_razao']; ?></td>
                                            <td><?php echo $row['c_cnpj']; ?></td>
                                            <td><?php echo $row['contratado_embr']; ?></td>
                                            <td><?php echo $row['encerrado_embr']; ?></td>
                                            <td>
                                                <?php
                                                if ($row['valor'] > 0) {
                                                    $convert_valor = str_replace(",", ".", $row['valor']);
                                                    echo formataMoeda($convert_valor);
                                                } else {
                                                    echo "";
                                                }
                                                ?>
                                            </td>
                                            <td class="center">
                                                <?php
                                                $doc_tot = PrestadorServico::getStatusList($row['id_prestador']);
                                                echo $doc_tot;
                                                ?>
                                            </td>

                                            <?php
                                            $qtd_vencidos = PrestadorServico::getDocsVencidos($row['id_prestador']);
                                            if($qtd_vencidos > 0){
                                                    $back_vermelho = "back-red";
                                                }else{
                                                    $back_vermelho = "";
                                            } ?>

                                            <td class="center <?= $back_vermelho ?>">
                                                <?php
                                                if ($doc_tot == '0') {
                                                    echo '0';
                                                } else {
                                                    echo $qtd_vencidos;
                                                }
                                                ?>
                                            </td>

                                            <td class="center"><a href="gerenciar/?id=<?php echo $row['id_prestador']; ?>" target="_blank"><span class="fa fa-file-text-o fa-lg"></span></a></td>
                                            <td class="center"><span class="fa fa-search fa-lg bt-image pointer" data-type="docs" data-key="<?php echo $row['id_prestador']; ?>" /></td>
                                            <td class="center"><span class="fa fa-file-text-o fa-lg bt-image pointer" data-type="prestador" data-key="<?php echo $row['id_prestador']; ?>" /></td>
                                            <td class="center"><span class="fa fa-edit fa-lg bt-image pointer" data-type="editar" data-key="<?php echo $row['id_prestador']; ?>" ></span></td>
                                            <td class="center"><span class="fa fa-copy fa-lg bt-image pointer" data-type="duplicar" data-key="<?php echo $row['id_prestador']; ?>" /></td>
                                            <?php if($objAcoes->verifica_permissoes(131)) { ?>
                                                <td class="center"><span class="fa fa-remove fa-lg pointer bt-image" data-type="remover" data-key="<?php echo $row['id_prestador']; ?>" /></td>
                                            <?php }?>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <?php
                        }
                    }
                    ?>

                    <?php if ($num_rows_enc > 0) { ?>
                        <br/>
                        <p style="text-align: right; margin-top: 20px"><button type="button" onclick="tableToExcel('tbRelatorio_enc', 'Relatório')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar Para Excel</button></p>
                        <table id="tbRelatorio_enc" class="table table-striped table-hover text-sm valign-middle table-bordered">
                            <thead>
                                <tr>
                                    <th style="text-align:center; font-size: 2em;" colspan="14">Contratos encerrados</th>
                                </tr>
                                <tr>
                                    <th>#</th>
                                    <th>Razão Social</th>
                                    <th>CNPJ</th>
                                    <th>Início</th>
                                    <th>Término</th>
                                    <th>Valor</th>
                                    <th>Quantidade Docs</th>
                                    <th>Qtd Docs Vencidos</th>
                                    <th>Contrato</th>
                                    <th>Docs</th>
                                    <th colspan="4">Açoes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row_enc = mysql_fetch_array($rs_enc)) { ?>
                                    <tr class="<?php echo ($count++ % 2 == 0) ? "odd" : "even" ?>">
                                        <td><?php echo $row_enc['id_prestador']; ?></td>
                                        <td><?php echo $row_enc['c_razao']; ?></td>
                                        <td><?php echo $row_enc['c_cnpj']; ?></td>
                                        <td><?php echo $row_enc['contratado_embr']; ?></td>
                                        <td><?php echo $row_enc['encerrado_embr']; ?></td>
                                        <td>
                                            <?php
                                            if ($row_enc['valor'] > 0) {
                                                $convert_valor_enc = str_replace(",", ".", $row_enc['valor']);
                                                echo formataMoeda($convert_valor_enc);
                                            } else {
                                                echo "";
                                            }
                                            ?>
                                        </td>
                                        <td class="center">
                                            <?php
                                            $doc_tot_enc = PrestadorServico::getStatusList($row_enc['id_prestador']);
                                            echo $doc_tot_enc;
                                            ?>
                                        </td>
                                        <td class="center">
                                            <?php
                                            if ($doc_tot_enc == '0') {
                                                echo '0';
                                            } else {
                                                echo PrestadorServico::getDocsVencidos($row_enc['id_prestador']);
                                            }
                                            ?>
                                        </td>
<!--                                        <td class="center"><a href="gerenciar/?id=<?php echo $row_enc['id_prestador']; ?>"><img src="../../imagens/icones/icon-doc.gif" title="Gerenciar" class="bt-image" /></a></td>
                                        <td class="center"><img src="../../imagens/icones/icon-docview.gif" title="Documentos" class="bt-image" data-type="docs" data-key="<?php echo $row_enc['id_prestador']; ?>" /></td>
                                        <td class="center"><img src="../../imagens/icones/icon-doc.gif" title="Ver Prestador" class="bt-image" data-type="prestador" data-key="<?php echo $row_enc['id_prestador']; ?>" /></td>
                                        <td class="center"><img src="../../imagens/icones/icon-edit.gif" title="Editar" class="bt-image" data-type="editar" data-key="<?php echo $row_enc['id_prestador']; ?>" /></td>
                                        <td class="center"><img src="../../imagens/icones/icon-copy.png" title="Duplicar" class="bt-image" data-type="duplicar" data-key="<?php echo $row_enc['id_prestador']; ?>" /></td>-->

                                        <td class="center"><a href="gerenciar/?id=<?php echo $row_enc['id_prestador']; ?>"><span class="fa fa-file-text-o fa-lg"></span></a></td>
                                        <td class="center"><span class="fa fa-search fa-lg bt-image pointer" data-type="docs" data-key="<?php echo $row_enc['id_prestador']; ?>" /></td>
                                        <td class="center"><span class="fa fa-file-text-o fa-lg bt-image pointer" data-type="prestador" data-key="<?php echo $row_enc['id_prestador']; ?>" /></td>
                                        <td class="center"><span class="fa fa-edit fa-lg bt-image pointer" data-type="editar" data-key="<?php echo $row_enc['id_prestador']; ?>" ></span></td>
                                        <td class="center"><span class="fa fa-copy fa-lg bt-image pointer" data-type="duplicar" data-key="<?php echo $row_enc['id_prestador']; ?>" /></td>
                                        <?php if($objAcoes->verifica_permissoes(131)) { ?>
                                            <td class="center"><span class="fa fa-remove fa-lg pointer bt-image" data-type="remover" data-key="<?php echo $row['id_prestador']; ?>" /></td>
                                        <?php }?>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } ?>

                    <?php if ($num_rows == 0 && $num_rows_enc == 0) { ?>
                        <br/>
                        <div id='message-box' class='message-yellow'>
                            <p>Nenhum registro encontrado</p>
                        </div>
                        <?php
                    }
                }
                ?>
            </form>

            <?php include('../../template/footer.php'); ?>

        </div>
    </body>
</html>
