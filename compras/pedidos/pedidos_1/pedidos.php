<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}
include("../../../conn.php");
include("../../../classes/global.php");
include("../../../classes/pedidosClass.php");
include("../../../wfunction.php");
include("../../../classes/BotoesClass.php");
include("../../../classes/BancoClass.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
// seta um valor para variável aba. usado para definir a aba aberta.
$aba = (isset($_REQUEST['aba'])) ? $_REQUEST['aba'] : 'cadastrar';

$projeto1 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto1' name='projeto1' class='form-control validate[required,custom[select]]' data-for='prestador1'");
$projeto2 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto2' name='projeto2' class='form-control validate[required,custom[select]]' data-for='prestador2'");
$projeto3 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto3' name='projeto3' class='form-control validate[required,custom[select]]' data-for='prestador3'");
$projeto4 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto4' name='projeto4' class='form-control validate[required,custom[select]]' data-for='prestador4'");
$global = new GlobalClass();

function checkAba($aba1, $aba2) {
    return ($aba1 == $aba2) ? 'active' : '';
}

$breadcrumb_config = array("nivel" => "../../../", "key_btn" => "35", "area" => "Gestão de Compras e Contratos", "ativo" => "Gestão de Pedidos", "id_form" => "form1");

//if (isset($_REQUEST['buscarprodutoS']) && $_REQUEST['buscarprodutoS'] == 'Buscar') {
//
//        $id_projeto1 = $_REQUEST['projeto1'];
//        $id_regiao1 = $_REQUEST['regiao1'];
//        $id_prestador1 = $_REQUEST['prestador1'];
//
//        $qry = mysql_query("SELECT A.*, B.* FROM prestadorservico AS A
//            LEFT JOIN nfe_produtos AS B ON (B.id_prestador = A.id_prestador)
//            WHERE B.status = '1' AND A.id_regiao = '$id_regiao1' AND A.id_projeto = '$id_projeto1' AND A.id_prestador = '$id_prestador1' ORDER BY B.cProd ASC");
//    
//        $total = mysql_num_rows($qry);
//    
//        while ($row_produtos = mysql_fetch_assoc($qry)) {
//            $retorno_produtos[] = array(
//                'id_prod' => $row_produtos['id_prod'],
//                'cProd' => $row_produtos['cProd'],
//                'xProd' => utf8_encode($row_produtos['xProd']),
//                'uCom' => $row_produtos['uCom'],
//                'vUnCom' => $row_produtos['vUnCom'],
//            );
//        }
//        echo (json_encode( $retorno_produtos));
//        exit();
//}
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet ::</title>

        <link rel="shortcut icon" href="../../../favicon.png">

        <!-- Bootstrap -->
        <link href="../../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../../resources/css/bootstrap-compras.css" rel="stylesheet" type="text/css">

    </head>
    <body>
        <?php include("../../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-compras-header">
                        <h2><span class="glyphicon glyphicon-shopping-cart"></span> - Gestão de Compras e Contratos <small>- Pedidos</small></h2>
                    </div>
                    <div role="tabpanel">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist" style="margin-bottom:30px;">
                            <li role="presentation" class="<?= checkAba('cadastrar', $aba) ?>"><a href="#cadastrar" aria-controls="cadastrar" role="tab" data-toggle="tab">Cadastro</a></li>
                            <li role="presentation" class="<?= checkAba('impressao', $aba) ?>"><a href="#impressao" aria-controls="impressao" role="tab" data-toggle="tab">Visualizar</a></li>
                            <li role="presentation" class="<?= checkAba('cadastrar1', $aba) ?>"><a href="#cadastrar1" aria-controls="cadastrar1" role="tab" data-toggle="tab">Preços</a></li>
                            <li role="presentation" class="<?= checkAba('arquivos', $aba) ?>"><a href="#arquivos" aria-controls="arquivos" role="tab" data-toggle="tab">Arquivo</a></li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane <?= checkAba('cadastrar', $aba) ?>" id="cadastrar">
                                <form action="pedidos_methods.php" method="post" class="form-horizontal" id="form-pedido" enctype="multipart/form-data">
                                    <input type="hidden" name="home" id="home" value="">
                                    <fieldset>
                                        <legend>Cadastro de Pedido</legend>
                                        <div class="panel panel-default">
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <label for="regiao" class="col-lg-2 control-label">Região</label>
                                                    <div class="col-lg-4">
                                                        <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao1' name='regiao1' class='validate[required,custom[select]] form-control' data-for='projeto1'"); ?>
                                                    </div>
                                                    <label for="projeto1" class="col-lg-1 control-label">Projeto</label>
                                                    <div class="col-lg-4">
                                                        <?php echo $projeto1; ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="" class="col-lg-2 control-label" onblur="">Prestador</label>
                                                    <div class="col-lg-9">
                                                        <?= montaSelect(array('-1' => '« Selecione o Prestador »'), NULL, 'class="col-lg-2 form-control validate[required,custom[select]]" name="prestador1" id="prestador1"'); ?>
                                                    </div>
                                                </div>
                                            </div><!-- /.panel-body -->
                                            <div class="panel-footer text-right">
                                                <input type="submit" id="buscarprodutoS" name="buscarprodutoS" value="Visualizar Produtos" class="btn btn-primary">
                                            </div>
                                            <table class="bg-stable table table-condensed table-striped hide text text-sm" id="tab-produtos">
                                                <thead>
                                                    <tr>
                                                        <th>Descrição</th>
                                                        <th>Und</th>
                                                        <th class="text text-sm" style="font-size:0.8em">Vlr Acordado</th>
                                                        <th>Quantidade</th>
                                                        <th>R$ Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                                <tfoot class="text text-right">
                                                    <tr>
                                                        <td></td>
                                                        <td colspan="5"><input type="submit" onclick="" id="gerarpedido" name="gerarpedido" value="Gerar Pedido" class="btn btn-success"></td>
                                                    </tr>
                                                </tfoot>                                                    
                                            </table>
                                        </div><!-- /.panel-default -->
                                    </fieldset>
                                    <div>
                                    </div>
                                </form>
                            </div>  <!-- /.#cadastro -->

                            <div role="tabpanel" class="tab-pane <?= checkAba('impressao', $aba) ?>" id="impressao">
                                <form action="methods.php" method="post" class="form-horizontal" id="form-xml" enctype="multipart/form-data">
                                    <fieldset>
                                        <legend>Imprimir Pedidos</legend> 
                                        <div class="panel panel-default">
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <label for="regiao1" class="col-lg-2 control-label">Região</label>
                                                    <div class="col-lg-4">
                                                        <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao2' name='regiao2' class='validate[required,custom[select]] form-control' data-for='projeto2'"); ?>
                                                    </div>
                                                    <label for="projeto2" class="col-lg-1 control-label">Projeto</label>
                                                    <div class="col-lg-4">
                                                        <?php echo $projeto2; ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="prestador2" class="col-lg-2 control-label" onblur="">Prestador</label>
                                                    <div class="col-lg-9">
                                                        <?= montaSelect(array('-1' => '« Selecione o Prestador »'), NULL, 'class="col-lg-2 form-control validate[required,custom[select]]" name="prestador2" id="prestador2"'); ?>
                                                    </div>
                                                </div>
                                                <!--                                                <div class="col-lg-3">
                                                        <input type="submit" value="Imprimir" name="imprimir" id="imprimir" data-status="false" class="btn btn-primary">
                                                    </div>
                                                </div>-->
                                            </div>
                                        </div>

                                        <div class="panel panel-default hidden" id="selectr-regiao-projeto">
                                            <div class="panel-body">
                                                <div class="form-group ">
                                                    <label for="regiao1" class="col-lg-2 control-label">Região</label>
                                                    <div class="col-lg-4">
                                                        <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao2' name='regiao' class='validate[required,custom[select]] form-control' data-for='projeto2'"); ?>
                                                    </div>
                                                    <label for="projeto" class="col-lg-1 control-label">Projeto</label>
                                                    <div class="col-lg-4">
                                                        <?php echo $projeto2; ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="" class="col-lg-2 control-label" onblur="">Prestador</label>
                                                    <div class="col-lg-9">
                                                        <select class="col-lg-4 form-control" name="prestador" id="prestador2">
                                                            <option>Selecione Pestador</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div><!-- /.panel-body -->
                                            <div class="panel-footer text-right">
                                                <input type="hidden" name="aba" id="aba" value="importar">
                                                <input type="hidden" name="acao" id="acao" value="upload">
                                                <input type="reset" value="Cancelar" id="xml-cancelar" class="btn btn-warning">
                                                <input type="submit" value="Salvar" name="salvar-xml" id="xml-salvar" class="btn btn-success" data-status="false" disabled>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <div id="list-prod-import"> </div>
                                </form>
                            </div>  <!-- /#importar -->

                            <div role="tabpanel" class="tab-pane <?= checkAba('cadastrar1', $aba) ?>" id="cadastrar1"> <!-- CADASTRO DE SERVIÇO -->
                                <form action="methods.php" method="post" class="form-horizontal" id="form-cadastro1" enctype="multipart/form-data">
                                    <input type="hidden" name="home" id="home" value="">
                                    <fieldset>
                                        <legend>Cadastro de Serviço</legend>
                                        <div class="panel panel-default">
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <label for="regiao" class="col-lg-2 control-label">Região</label>
                                                    <div class="col-lg-4">
                                                        <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao3' name='regiao' class='validate[required,custom[select]] form-control' data-for='projeto3'"); ?>
                                                    </div>
                                                    <label for="projeto" class="col-lg-1 control-label">Projeto</label>
                                                    <div class="col-lg-4">
                                                        <?php echo $projeto3; ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="" class="col-lg-2 control-label" onblur="">Prestador</label>
                                                    <div class="col-lg-9">
                                                        <?= montaSelect(array('-1' => '« Selecione o Projeto »'), NULL, 'class="col-lg-2 form-control validate[required,custom[select]]" name="prestador" id="prestador3"'); ?>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="form-group">
                                                    <label for="inputEmail3" class="col-lg-2 control-label">Código do Serviço</label>
                                                    <div class="col-lg-4">
                                                        <input type="text" class="form-control validate[required]" id="cProd" name="cProd" placeholder="">
                                                        <p class="help-block">Código informado pelo Prestador</p>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="inputEmail3" class="col-lg-2 control-label">Descrição do Serviço</label>
                                                    <div class="col-lg-9">
                                                        <input type="text" class="form-control validate[required]" id="xProd" name="xProd" placeholder="">
                                                        <!--<p class="help-block">Código informado pelo fornecedor.</p>-->
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="inputEmail3" class="col-lg-2 control-label">Valor do Serviço</label>
                                                    <div class="col-lg-4">
                                                        <div class="input-group">
                                                            <span class="input-group-addon">R$</span>
                                                            <input type="text" class="form-control validate[required]" id="vUnCom" name="vUnCom" placeholder="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                            </div><!-- /.panel-body -->
                                            <div class="panel-footer text-right">
                                                <input type="reset" value="Cancelar" class="btn btn-default">
                                                <input type="submit" name="cadastro1-salvar" value="Cadastrar" class="btn btn-primary">
                                            </div>
                                        </div><!-- /.panel-default -->
                                    </fieldset>
                                    <div id="resp-cadastro1"></div>
                                </form>
                            </div><!-- /.#cadastro -->
                            <div role="tabpanel" class="tab-pane <?= checkAba('arquivos', $aba) ?>" id="arquivos"> <!-- IMPORTAÇÃO DO ARQUIVO XML "CADASTRO DE SERVIÇO" -->
                                <form action="methods.php" method="post" class="form-horizontal" id="form1-xml" enctype="multipart/form-data">
                                    <fieldset>
                                        <legend>Importação Serviço da NFSe</legend>
                                        <div class="panel panel-default">
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <label for="nfse" class="col-lg-2 control-label text-right"> Arquivo XML</label>
                                                    <div class="col-lg-6">                             
                                                        <input type="file" name="nfse" id="nfse" class="form-control filestyle" data-buttonText=" Selecione Arquivo">
                                                    </div>
                                                    <div class="col-lg-3">                          
                                                        <input type="submit" value="Importar" name="importar1" id="xml-importar1" data-status="false" class="btn btn-primary">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel panel-default hidden" id="select-regiao-projeto1">
                                            <div class="panel-body">
                                                <div class="form-group ">
                                                    <label for="regiao4" class="col-lg-2 control-label">Região</label>
                                                    <div class="col-lg-4">
                                                        <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao4' name='regiao' class='validate[required,custom[select]] form-control' data-for='projeto4'"); ?>
                                                    </div>
                                                    <label for="projeto" class="col-lg-1 control-label">Projeto</label>
                                                    <div class="col-lg-4">
                                                        <?php echo $projeto4; ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="" class="col-lg-2 control-label" onblur="">Prestador</label>
                                                    <div class="col-lg-9">
                                                        <select class="col-lg-4 form-control" name="prestador" id="prestador4">
                                                            <option>Selecione Pestador</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div><!-- /.panel-body -->
                                            <div class="panel-footer text-right">
                                                <input type="hidden" name="aba" id="aba" value="importar1">
                                                <input type="hidden" name="acao" id="acao" value="upload">
                                                <input type="reset" value="Cancelar" id="xml-cancelar" class="btn btn-warning">
                                                <input type="submit" value="Visualizar" name="visualizaprodutos" id="visualizaprodutos" class="btn btn-success" data-status="false" disabled>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <div id="list-serv-import"> </div>
                                </form>
                            </div><!-- /#importar -->
                        </div>

                    </div>

                </div><!-- col-lg-12 -->

            </div><!-- row -->

            <?php include_once '../../../template/footer.php'; ?>

        </div><!-- container -->
        <script src="../../../js/jquery-1.10.2.min.js"></script>
        <script src="../../../js/jquery.form.js"></script>
        <script src="../../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../../js/global.js"></script>
        <script src="../../../resources/js/bootstrap.min.js"></script>
        <script src="../../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../../resources/js/main.js"></script>
        <script src="../../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script>
//            function showRequest(formData, jqForm, options) {
//                var BoolImportar = Boolean($("#importar").data("status"));
//                if (!BoolImportar) {
//                    var valid = $("#form-xml").validationEngine('validate');
//                    if (valid == true) {
//                        $("#list-prod-import").html("<br><p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
//                        return true;
//                    } else {
//                        return false;
//                    }
//                }
//            }
//
//            function showRequest1(formData, jqForm, options) {
//                var BoolImportar = Boolean($("#importar1").data("status"));
//                if (!BoolImportar) {
//                    var valid = $("#form1-xml").validationEngine('validate');
//                    if (valid == true) {
//                        $("#list-serv-import").html("<br><p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
//                        return true;
//                    } else {
//                        return false;
//                    }
//                }
//            }
//
            function showSuccessPedido(data) {
                console.log(data.itens);
                if (typeof data.itens != 'undefined') {
                    $("#tab-produtos").removeClass("hide");
                    var html = "";
                    $.each(data.itens, function (i, item) {
                        html += "<tr id=\"tr-item-" + data.itens[i].id_prod + "\">";
//                        html += "<td class=\"text text-center\">" + data.itens[i].cProd + "<input type=\"hidden\" name=\"idProd[]\" value=\""+data.itens[i].id_prod+ "\"  </td>";
                        html += "<td>" + data.itens[i].xProd + "</td>";
                        html += "<td class=\"text text-center\">" + data.itens[i].uCom + "</td>";
                        html += "<td class=\"text text-right\">" + number_format(data.itens[i].vUnCom, 2, ',', '.') + "<input type=\"hidden\" name=\"vUnCom[]\" id=\"vUnCom-" + data.itens[i].id_prod + "\" class=\"form-control money\" value=" + number_format(data.itens[i].vUnCom, 2, ',', '.') + " ></td>";
                        html += "<td><input type=\"text\" class=\"form-control text text-center item_qtde\" name=\"qtde[]\"  data-id=" + data.itens[i].id_prod + " size=\"12\" maxlength=\"12\"></td>";
                        html += "<td><input type=\"text\" name=\"vProd[]\" id=\"vProd-" + data.itens[i].id_prod + "\" size=\"12\" class=\"text form-control text-right\" readonly> </td>";
                        html += "</tr>";
                    });
                    $("#tab-produtos tbody").html(html);
                } else if (data.msg) {
                    bootAlert('Sem Dados!!!');
                }
            }

//            function showSuccessCadastro1(data) {
//                $("#resp-cadastro1").html(data);
//            }
//
//            function showSuccess(data, statusText, xhr, $form) {
//                var BoolImportar = Boolean($("#xml-importar").data('status'));
//                var BoolSalvar = Boolean($("#xml-salvar").data('status'));
//                if (BoolImportar) {
//                    $("#select-regiao-projeto").removeClass('hidden');
//                    $("#xml-salvar").prop('disabled', false);
//                    $("#importar").data('status', false);
//                }
//                if (BoolSalvar) {
//                    $("#select-regiao-projeto").addClass('hidden');
//                    $("#xml-salvar").prop('disabled', true);
//                    $("#form-xml").each(function () {
//                        this.reset();
//                    });
//                    $("#xml-salvar").data('status', false);
//                }
//                $("#list-prod-import").html(data); // exibir resultados
//            }
//
//            function showSuccess1(data, statusText, xhr, $form) {
//                var BoolImportar = Boolean($("#xml-importar1").data('status'));
//                var BoolSalvar = Boolean($("#xml-serv-salvar").data('status'));
//                if (BoolImportar) {
//                    $("#select-regiao-projeto1").removeClass('hidden');
//                    $("#xml-serv-salvar").prop('disabled', false);
//                    $("#importar-serv").data('status', false);
//                }
//
//                if (BoolSalvar) {
//                    $("#select-regiao-projeto1").addClass('hidden');
//                    $("#xml-serv-salvar").prop('disabled', true);
//                    $("#form1-xml").each(function () {
//                        this.reset();
//                    });
//                    $("#xml-serv-salvar").data('status', false);
//                }
//
//                $("#list-serv-import").html(data); // exibir resultados
//            }
//
            $(document).ready(function () {

                // options do ajaxForm -----------------------------------------
                var optionsCadastroPed = {
//                    beforeSubmit: showRequest,
                    success: showSuccessPedido,
//                    resetForm: true,
                    dataType: 'json'
                };
//                var optionsCadastro1 = {
//                    beforeSubmit: showRequest1,
//                    success: showSuccessCadastro1,
//                    resetForm: true
//                };
//                var optionsXML = {
//                    beforeSubmit: showRequest,
//                    success: showSuccess
//                };
//                var optionsXML1 = {
//                    beforeSubmit: showRequest1,
//                    success: showSuccess1
//                };

                // form-cadastro-pedido ---------------------------------------
                $("#vUnCom").maskMoney({thousands: '.', decimal: ','});
                $("#form-pedido").ajaxForm(optionsCadastroPed);// add javaxForm
                $("#form-pedido").validationEngine();// add validation engine
                // fim do form-cadastro ----------------------------------------

                // form-xml ----------------------------------------------------
                
            $('#regiao1,#regiao2,#regiao3,#regiao4').change(function () {
                var destino = $(this).data('for');
                $.post("http://www.f71lagos.com/intranet/methods.php", {method: "carregaProjetos", regiao: $(this).val()}, function (data) {
                    $("#" + destino).html(data);
                });
            });

            $('#projeto1,#projeto2,#projeto3,#projeto4').change(function () {
                var destino = $(this).data('for');
                $.post("http://www.f71lagos.com/intranet/methods.php", {method: "carregaPrestadores", projeto: $(this).val()}, function (data) {
                    $("#" + destino).html(data);
                });
            });
            
            $("#tab-produtos").on('blur', ".item_qtde", function () {
                var id_prod = $(this).data('id');
                var qtde = parseFloat($(this).val().replace(',', '.'));
                var vUni = parseFloat($("#vUnCom-" + id_prod).maskMoney('unmasked')[0]);
                var valor = (qtde * vUni).toFixed(2);
                valor = number_format(valor, 2, ',', '.');
                $("#vProd-" + id_prod).val(valor);
            });
                
                $("#gerarpedido").click(function(){
                    $('#form-pedido').ajaxFormUnbind();
                    $('#form-pedido').attr('action','gerarpedido.php');
                    $('#form-pedido').submit();
                });
                // acao nos cones de marcar V ou X -----------------------------
//                $("#list-prod-import").on('click', '.y', function () {
//                    var id = $(this).data('id');
//                    $(this).slideUp();
//                    $('.n[data-id=' + id + ']').slideDown();
//                    $(this).closest('tr').removeClass().addClass('success');
//                    $("#ok-" + id).val(1);
////                    console.log('y ' + $("#ok-" + id).val());
//                });
//                $("#list-prod-import").on('click', '.n', function () {
//                    var id = $(this).data('id');
//                    $(this).slideUp();
//                    $('.y[data-id=' + id + ']').slideDown();
//                    $(this).closest('tr').removeClass().addClass('danger');
//                    $("#ok-" + id).val(0);
////                    console.log('n ' + $("#ok-" + id).val());
//                });
                // -------------------------------------------------------------                

//                // acoes nos botoes --------------------------------------------
//                $("#xml-cancelar").click(function () {
//                    $("#list-prod-import").html('');
//                });
//                // usado no success do submit ----------------------------------
//                $("#xml-importar").click(function () {
//                    $("#xml-importar").data('status', true);
//                });
//                $("#xml-importar1").click(function () {
//                    $("#xml-importar1").data('status', true);
//                });
//                $("#xml-salvar").click(function () {
//                    $("#xml-salvar").data('status', true);
//                });
//                $("#xml-serv-salvar").click(function () {
//                    $("#xml-serv-salvar").data('status', true);
//                });
//                // fim usado no success do submit ------------------------------
//                // fim acao nos botoes -----------------------------------------
//
//                $("#form-xml").ajaxForm(optionsXML); // add javaxForm
//                $("#form-xml").validationEngine(); // add validation engine
//
//                $("#form1-xml").ajaxForm(optionsXML1); // add javaxForm
//                $("#form1-xml").validationEngine(); // add validation engine
//
//                // fim do form-xml ---------------------------------------------
            });
        </script>
    </body>
</html>