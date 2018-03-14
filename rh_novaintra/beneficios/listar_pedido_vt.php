<?php
if (empty($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
}

error_reporting(E_ALL);

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/ValeTransporteClass.php");
include("../../classes_permissoes/acoes.class.php");
include "../../classes/LogClass.php";
include('../../classes/valor_proporcional.php');

$log = new Log();
$Trab = new proporcional();

if (!empty($_REQUEST['data_xls'])) {
    $dados = strip_tags(utf8_encode($_REQUEST['data_xls']), '<div>, <table>, <thead>, <tr>, <th>, <td>, <tbody>');

    ob_end_clean();
    header("Content-Encoding: iso-8859-1");
    header("Pragma: private");
    header("Cache-control: private, must-revalidate");
    header("Expires: 0");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=relatorio-de-pedido-de-vale-transporte.xls");

    echo "\xEF\xBB\xBF";
    echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel/' xmlns='http://www.w3.org/TR/REC-html40'>";
    echo "  <head>";
    echo "  <title>PARTICIPANTES DE VA</title>";
    echo "      <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->";
    echo "  </head>";
    echo "  <body>";
    echo "      $dados";
    echo "  </body>";
    echo "</html>";
    exit;
}

$objAcoes = new Acoes();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$objTransporte = new ValeTransporteClass();

$x = $objTransporte->consultar(array('id_vt_pedido' => $_REQUEST['id']));

$y = $objTransporte->gerForRelatorio($_REQUEST['id']);

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'editar_valor_pedido') {
    //$ret = $objTransporte->salvar(array('id_vt_relatorio' => $_REQUEST['id_vt_relatorio'], 'vt_valor_diario' => $_REQUEST['vt_valor_diario']));
    $update = mysql_query("UPDATE rh_vt_relatorio SET vt_valor_diario = '{$_REQUEST['vt_valor_diario']}' WHERE id_vt_relatorio = {$_REQUEST['id_vt_relatorio']}") or die(mysql_error());
    
    echo json_encode(array('status' => $update));
}

$mov_a_pagar = $objTransporte->getMovimentosVtEmAberto($_REQUEST['id']);
$total_mov_a_pagar = $mov_a_pagar[1]["tot_mov"];

$mov_pagos = $objTransporte->getMovimentosVtPagos($_REQUEST['id']);
$total_mov_pagos = $mov_pagos[1]["tot_mov"];

$disabled_criar = '';
$disabled_excluir = 'disabled';

if($total_mov_a_pagar > 0){
    $disabled_criar = 'disabled';
    $disabled_excluir = '';
}

if($total_mov_pagos > 0){
    $disabled_criar = 'disabled';
    $disabled_excluir = 'disabled';
}

//GERA MOVIMENTOS DE VT
if(isset($_REQUEST['method']) && $_REQUEST['method'] == "gera_movimentos_vt") {
    $id_pedido = $_REQUEST['id'];
    
    $pedido = $objTransporte->listar($id_pedido);    
    $mes = date('m', strtotime('-1 month', strtotime("{$pedido['ano']}-{$pedido['mes']}-01")));        
    
    if($pedido['mes'] == 1){
        $ano = $pedido['ano'] - 1;
    }else{
        $ano = $pedido['ano'];
    }
    
    $id_regiao = $pedido['id_regiao'];
    $id_projeto = $pedido['id_regiao'];
    
    $participantes_pedido = $objTransporte->gerForRelatorio($id_pedido, true);
    
    foreach ($participantes_pedido as $key => $value) {
        $id_clt = $value['id_clt'];
        $valor = $value['vt_valor_diario'];
        
        if($valor > 0){        
            //5080 ADIANTAMENTO DE VT
            $mov1 = "INSERT INTO 
                    rh_movimentos_clt (id_clt, id_regiao, id_projeto, mes_mov, ano_mov, id_mov, cod_movimento, tipo_movimento, nome_movimento, data_movimento, user_cad, valor_movimento, qnt, status, importacao, lancamento, id_pedido) 
                    VALUES ('{$id_clt}', '{$id_regiao}', '{$id_projeto}', '{$mes}', '{$ano}', 435, 5080, 'CREDITO', 'ADIANTAMENTO DE VT', NOW(), {$_COOKIE['logado']}, '{$valor}', 1, 1, 1, 1, {$id_pedido})";                

            //5081 DESC. ADIANTAMENTO VT
            $mov2 = "INSERT INTO 
                    rh_movimentos_clt (id_clt, id_regiao, id_projeto, mes_mov, ano_mov, id_mov, cod_movimento, tipo_movimento, nome_movimento, data_movimento, user_cad, valor_movimento, qnt, status, importacao, lancamento, id_pedido) 
                    VALUES ('{$id_clt}', '{$id_regiao}', '{$id_projeto}', '{$mes}', '{$ano}', 436, 5081, 'DEBITO', 'DESC. ADIANTAMENTO VT', NOW(), {$_COOKIE['logado']}, '{$valor}', 1, 1, 1, 1, {$id_pedido})";

            $qry_clt = "SELECT A.nome, B.salario, A.data_entrada
                FROM rh_clt AS A
                LEFT JOIN curso AS B ON(A.id_curso = B.id_curso)
                WHERE A.id_clt = {$id_clt}";
            $sql_clt = mysql_query($qry_clt) or die(mysql_error());
            $info_clt = mysql_fetch_assoc($sql_clt);

            $salario = $info_clt['salario'];
            $inicio = explode('-', $info_clt['data_entrada']);
            $dias_trabalhados = 31 - $inicio[2];
            
            $Trab->calculo_proporcional($salario, $dias_trabalhados);
            
            /*
             * CALCULO DO DESCONTO EM CIMA DO PROPORCIONAL
             * PARA CASOS DE ADMISSÃO
             */
            if(($inicio[1] == $mes) && ($inicio[0] == $ano)){
                $percentual_descVT = $Trab->valor_proporcional * 0.06;
            }else{
                $percentual_descVT = $salario * 0.06;
            }
            
            if($valor > $percentual_descVT){
                $valor_desc = $percentual_descVT;
            }else{
                $valor_desc = $valor;
            }
            
            //7001 DESCONTO VALE TRANSPORTE
            $mov3 = "INSERT INTO 
                    rh_movimentos_clt (id_clt, id_regiao, id_projeto, mes_mov, ano_mov, id_mov, cod_movimento, tipo_movimento, nome_movimento, data_movimento, user_cad, valor_movimento, qnt, status, importacao, lancamento, id_pedido) 
                    VALUES ('{$id_clt}', '{$id_regiao}', '{$id_projeto}', '{$mes}', '{$ano}', 203, 7001, 'DEBITO', 'DESCONTO VALE TRANSPORTE', NOW(), {$_COOKIE['logado']}, '{$valor_desc}', 1, 1, 1, 1, {$id_pedido})";
            
            $insere_mov1 = mysql_query($mov1) or die(mysql_error());
            $insere_mov2 = mysql_query($mov2) or die(mysql_error());
            $insere_mov3 = mysql_query($mov3) or die(mysql_error());
        }
        
        if($insere_mov1 && $insere_mov2 && $insere_mov3){
            $retorno = 1;
        }else{
            $retorno = 0;
        }
    }
    
    echo json_encode(array('status' => $retorno));
    exit();
}

//EXCLUI MOVIMENTOS DE VT
if(isset($_REQUEST['method']) && $_REQUEST['method'] == "exclui_movimentos_vt") {
    $id_pedido = $_REQUEST['id'];
    
    $campos = array(
      "status" => 0
    );
    
    $deleta_movimentosVT = $objTransporte->sqlUpdate("rh_movimentos_clt", $campos, "id_pedido = {$id_pedido}");
    
    if($deleta_movimentosVT){
        $retorno = 1;
    }else{
        $retorno = 0;
    }
    
    echo json_encode(array('status' => $retorno));
    exit();
}

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Participantes do Pedido");
$breadcrumb_pages = array("Gestão de RH" => "../../rh/principalrh.php", "Benefícios" => "../beneficios", "Vale Transporte" => "vale_transporte");
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
            .exportButtons {
                width: 282px;
                float: right;
            }
            
            .competencia {
                margin: 6px 0;
                float: left;
            }
        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <form method="post" id="form1" action="" name="form1">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>- Vale Transporte</small></h2></div>
                    <div class="alert alert-success">
                        <input type="hidden" id="data_xls" name="data_xls" value="">
                        <p class="competencia"><strong>Competência:</strong> <?= mesesArray($y[1]['mes']).' de '.$y[1]['ano']; ?></p>
                        <div class="exportButtons">
                            <button type="button" form="formPdf" name="pdf" data-title="Relatório de Pedido de Vale Transporte" data-id="tbRelatorio" id="pdf" value="Gerar PDF" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Gerar PDF</button>
                            <button type="button" value="Exportar" class="btn btn-success" id="exportarExcel"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <?php if (count($y) > 0) { ?>
                        <div class="panel panel-default">
                            <div id="relatorio_exp">
                                <table class="table table-striped table-hover text-sm tablesorter" id="tbRelatorio">
                                    <thead>
                                        <tr>
                                            <th>Matrícula</th>
                                            <th>Nome</th>
                                            <th class="sorter-shortDate dateFormat-ddmmyyyy">Entrada</th>
                                            <th>CPF</th>
                                            <th>Unidade</th>
                                            <th>Cargo</th>
                                            <?php if ($_COOKIE['logado'] == 353) { ?>
                                                <th>Sindicato</th>
                                            <?php } ?>                                            
                                            <th>Dias</th>                                            
                                            <th>Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $tot_participantes = 0;
                                        $tot_valor = 0;
                                        
                                        foreach ($y as $key => $value) { ?>
                                            <tr>
                                                <td><?= $value['matricula_sodexo'] ?></td>
                                                <td><?= $value['nome'] ?></td>
                                                <td><?= converteData($value['data_entrada'], 'd/m/Y') ?></td>
                                                <td><?= $value['cpf'] ?></td>
                                                <td><?= $value['nome_unidade'] ?></td>
                                                <td><?= $value['nome_curso'] ?></td>
                                                <?php if ($_COOKIE['logado'] == 353) { ?>
                                                    <td><?= $value['nome_sindicato'] ?></td>
                                                <?php } ?>
<!--                                                <td class="action_val text-right"  data-id="<?= $value['id_vt_relatorio'] ?>">
                                                    <a href="javascript:;" id="<?= $value['id_vt_relatorio'] ?>_span">
                                                        <?= number_format($value['vt_valor_diario'], 2, ',', '.') ?>
                                                    </a>
                                                    <input type="hidden" name="valor[]" class="input_valor_edit valor_msk" value="<?= $value['vt_valor_diario'] ?>" id="<?= $value['id_vt_relatorio'] ?>_valor" data-id="<?= $value['id_vt_relatorio'] ?>">
                                                </td>-->                                                
                                                <td><?= $value['dias_uteis'] ?></td>                                                
                                                <td class="action_val text-right"  data-id="<?= $value['id_vt_relatorio'] ?>">
                                                    <a href="javascript:;" id="<?= $value['id_vt_relatorio'] ?>_span"><?= number_format($value['vt_valor_diario'], 2, ',', '.') ?></a>
                                                    <?php if($_COOKIE['logado'] != 395){ ?>
                                                    <input type="hidden" name="valor[]" class="input_valor_edit valor_msk" value="<?= $value['vt_valor_diario'] ?>" id="<?= $value['id_vt_relatorio'] ?>_valor" data-id="<?= $value['id_vt_relatorio'] ?>">
                                                    <?php } ?>
                                                </td>                                          
                                            </tr>
                                        <?php 
                                        $tot_participantes++;
                                        $tot_valor += $value['vt_valor_diario'];
                                        } ?>
                                            <tr>
                                                <td colspan="1" class="text-right"></td>
                                                <td colspan="4" class="text-right">Participantes: <strong><?php echo $tot_participantes; ?></td>                                                
                                                <td colspan="4" class="text-right">Valor total: <strong><?php echo number_format($tot_valor, 2, ',', '.'); ?></td>
                                            </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="panel-footer">
                                <a href="controle_vt.php?id=<?= $x[1]['id_vt_pedido'] ?>" name="download" value="download" class="btn btn-info"><i class="fa fa-download"></i> Download</a>                                                                                               
                                <button type="button" id="cria_mov_vt" name="cria_mov_vt" data-key="<?= $x[1]['id_vt_pedido'] ?>" value="criar" class="btn btn-warning" data-placement="top" <?php echo $disabled_criar; ?>><i class="fa fa-play-circle-o"></i> Criar Movimentos</button>                                
                                <button type="button" id="del_mov_vt" name="del_mov_vt" data-key="<?= $x[1]['id_vt_pedido'] ?>" value="deletar" class="btn btn-danger" data-placement="top" <?php echo $disabled_excluir; ?>><i class="fa fa-trash"></i> Excluir Movimentos</button>                                                                
                            </div>
                        </div>


                    <?php } ?>
                </div>
            </div>
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.container -->
        </form>

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/rh/eventos/prorrogar_evento.js"></script>
        <script src="../../js/jquery.maskedinput.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../jquery/tablesorte/jquery.tablesorter.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <!--<script src="../../resources/js/rh/beneficios/vale_alimentacao.js" type="text/javascript"></script>-->

        <script>
            $(document).ready(function () {
                $("#regiao").ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, null, "projeto");
            });

            $(function () {
                $(".valor_msk").maskMoney({prefix: 'R$ ', allowNegative: true, thousands: '', decimal: '.'});

                $("table").tablesorter({
                    dateFormat: "mmddyyyy", // set the default date format

                    // or to change the format for specific columns, add the dateFormat to the headers option:
                    headers: {
                        0: {sorter: "shortDate"} //, dateFormat will parsed as the default above
                        // 1: { sorter: "shortDate", dateFormat: "ddmmyyyy" }, // set day first format; set using class names
                        // 2: { sorter: "shortDate", dateFormat: "yyyymmdd" }  // set year first format; set using data attributes (jQuery data)
                    }
                });

                $("#exportarExcel").click(function () {
                    $("#relatorio_exp img:last-child").remove();

                    var html = $("#relatorio_exp").html();

                    $("#data_xls").val(html);
                    $("#form1").submit();
                });
                
                $("#checkAll").click(function () {
                    if ($("#checkAll").prop("checked")) {
                        $(".chk").prop("checked", true);
                    } else {
                        $(".chk").prop("checked", false);
                    }
                });
                
                $(".action_val").click(function () {
                    var id = $(this).data("id");
                    var valor = $("#" + id + "_valor");
                    var valor_txt = $("#" + id + "_span");

                    $(valor).attr("type", "text");
                    $(valor_txt).attr("class", "hidden");
                });
                
                $('.input_valor_edit').blur(function () {
                    var id = $(this).data('id');
                    var valor_novo = $(this).val();
                    var valor = $("#" + id + "_valor");
                    var valor_txt = $("#" + id + "_span");
                    
                    $(valor).attr("type", "hidden");
                    $(valor_txt).removeClass("hidden");
                    $(valor_txt).text(valor_novo);
                    
                    $.post('#', {method: 'editar_valor_pedido', id_vt_relatorio: id, vt_valor_diario: valor_novo}, function (data) {
                        if (data.status) {
                            $(valor).attr("type", "hidden");
                            $(valor_txt).removeClass("hidden");
                            $(valor_txt).text(valor_novo);
                        }
                    }, 'json');
                });
                
                $("#exportarExcel").click(function () {
                    $("#relatorio_exp img:last-child").remove();

                    var html = $("#relatorio_exp").html();

                    $("#data_xls").val(html);
                    $("#form1").submit();
                });
                
                $('#cria_mov_vt').click(function () {
                    var key = $(this).data("key");
                    
                    BootstrapDialog.confirm('Deseja <b>GERAR</b> os movimentos de VT?<br><br><ul><li>Adiantamento de VT</li><li>Desc. Adiantamento VT</li><li>Desconto Vale Transporte</li></ul>', 'Confirmação', function(result) {
                        if (result) {
                            $.ajax({
                                type: "post",
                                url: "listar_pedido_vt.php",
                                dataType: "json",
                                data: {
                                    id: key,
                                    method: "gera_movimentos_vt"
                                },
                                success: function(data) {
                                    if(data.status == "1"){
                                        bootAlert('Movimentos Cadastrados com sucesso', 'Lançamentos', null, null);
                                        $("#cria_mov_vt").attr('disabled','disabled');
                                        $("#del_mov_vt").removeAttr('disabled');
                                    }else if(data.status == "0"){
                                        bootAlert('Algo deu errado, entre em contato com a F71', 'Lançamentos', null, 'danger');
                                    }
                                }
                            });
                        }
                    },
                    'danger');
                });
                
                $('#del_mov_vt').click(function () {
                    var key = $(this).data("key");
            
                    BootstrapDialog.confirm('Deseja <b>EXCLUIR</b> os movimentos de VT?<br><br><ul><li>Adiantamento de VT</li><li>Desc. Adiantamento VT</li><li>Desconto Vale Transporte</li></ul>', 'Confirmação de Exclusão', function(result) {
                        if (result) {
                            $.ajax({
                                type: "post",
                                url: "listar_pedido_vt.php",
                                dataType: "json",
                                data: {
                                    id: key,
                                    method: "exclui_movimentos_vt"
                                },
                                success: function(data) {
                                    if(data.status == "1"){
                                        bootAlert('Movimentos Excluidos com sucesso', 'Lançamentos', null, null);
                                        $("#del_mov_vt").attr('disabled','disabled');
                                        $("#cria_mov_vt").removeAttr('disabled');
                                    }else if(data.status == "0"){
                                        bootAlert('Algo deu errado, entre em contato com a F71', 'Lançamentos', null, 'danger');
                                    }
                                }
                            });
                        }
                    },
                    'danger');
                });
            });
        </script>
    </body>
</html>