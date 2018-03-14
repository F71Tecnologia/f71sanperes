<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include_once "../../conn.php";
include_once "../../classes/GRRFClass.php";
include_once "../../classes/funcionario.php";
include_once '../../classes_permissoes/regioes.class.php';
include_once "../../wfunction.php";

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "GRRF");
$breadcrumb_pages = array("Gestão de RH" => "../../rh/principalrh.php");

$objGRRF = new GRRFClass();

if (isset($_REQUEST['gerar_grrf_lote'])) {
    include_once("monta_arquivo_grrf_lote.php");
    
    exit;
}

if (isset($_GET[download])) {
    include_once("monta_arquivo_grrf_lote.php");
}

$regiaoSel = (!empty($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario[id_regiao];
$projetoSel = (!empty($_REQUEST['projeto_gerado'])) ? $_REQUEST['projeto_gerado'] : $_REQUEST['projeto'];
$projetoSel = (!empty($projetoSel)) ? $projetoSel : -1;
$anoSel = (!empty($_REQUEST['ano'])) ? $_REQUEST['ano'] : date("Y");
$mesSel = (!empty($_REQUEST['mes'])) ? $_REQUEST['mes'] : date("m");

/*
 * LISTAGEM DE CLTS DISPONÍVEIS
 * PARA GERAR GRRF
 */
if (isset($_REQUEST['gerar'])) {
    $list = $objGRRF->listaRescindidos($_REQUEST);
}

/*
 * MÉTODO PARA LANÇAR VALOR INFORMADO PELA EMPRESA
 * CADASTRO FEITO POR CLT
 */
if (isset($_REQUEST['method']) && !empty($_REQUEST['method'])) {
    if($_REQUEST['method'] == "lanca_valor"){
        $retorno = array("status" => "0");
        
        if($_REQUEST['valor'] > 0){
            $insere = $objGRRF->insereGRRF($_REQUEST);
            
            if($insere){
                $retorno = array(
                    "status" => "1",
                    "clt"    => $_REQUEST['id']
                );
            }else{
                $retorno = array(
                    "status" => "2",
                    "clt"    => $_REQUEST['id']
                );
            }
        }
        
        if($_REQUEST['valor'] == 0){
            $verifica_valor = $objGRRF->consultaValorInformado($_REQUEST['id']);
            
            if($verifica_valor['total'] > 0){
                echo $delete = "DELETE FROM import_grrf_lote WHERE id_clt = '{$_REQUEST['id']}'";
                $alt = mysql_query($delete) or die(mysql_error());
            }
        }
        
        echo json_encode($retorno);
        exit;
    }
}
?>
<html>
    <head>
        <title>:: Intranet :: GRRF</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" type="text/css">
    </head>
    <body>        
        <?php include("../../template/navbar_default.php"); ?>        
        <div class="container">
            <form  name="form" action="" method="post" id="form" enctype="multipart/form-data" class="form-horizontal">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>- GRRF</small></h2></div>
                    
                        <div class="panel panel-default">
                            <div class="panel-heading">Consulta Funcionários</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Região</label>
                                    <div class="col-lg-4 selectpicher">
                                        <?= montaSelect(getRegioes(), $regiaoSel, 'name="regiao" id="regiao" class="form-control "'); ?>
                                    </div>
                                    <label class="col-lg-2 control-label">Projeto</label>
                                    <div class="col-lg-4 selectpicher">
                                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?= $projetoSel; ?>" />
                                        <?= montaSelect(getProjetos($regiaoSel), $projetoSel, 'name="projeto" id="projeto" class="form-control "'); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Competêcia</label>
                                    <div class="col-lg-2 selectpicher">
                                        <?= montaSelect(mesesArray(), $mesSel, 'name="mes" class="form-control "'); ?>
                                    </div>
                                    <div class="col-lg-2 selectpicher">
                                        <?= montaSelect(anosArray(null, date('Y')+1), $anoSel, 'name="ano" class="form-control "'); ?>
                                    </div>                                
                                    <label class="col-lg-2 control-label">Data do Recolhimento</label>
                                    <div class="col-lg-4 selectpicher">
                                        <input type="text" name="data" class="form-control data" value="<?= date("d/m/Y") ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">                                
                                <input type="submit" name="gerar" value="Consultar" class="btn btn-primary" id="gerar" />
                            </div>
                        </div>
                        
                        <?php 
                        if (isset($_REQUEST['gerar'])){
                            if($list['total'] > 0) {
                        ?>                       
                            <div class="panel panel-default">
                                <div id="relatorio_exp">
                                    <table class="table table-striped text-sm tablesorter" id="tbRelatorio">
                                        <thead>
                                            <tr>
                                                <th class="text-center <?php echo $tblImport; ?>">
                                                    <input type="checkbox" id="checkAll" data-name="ativo" checked>
                                                </th>
                                                <th>ID</th>
                                                <th>Nome</th>
                                                <th>PIS</th>
                                                <th>Função</th>
                                                <th>Tipo de demissão</th>
                                                <th>Data de admissão</th>
                                                <th>Data de demissão</th>
                                                <th>Valor da rescisão</th>
                                                <th>Valor informado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $tot_valor = 0;
                                            $tot_clts = 0;
                                            
                                            foreach($list['list'] as $key => $rows){
                                                $tot_valor += $rows['total_liquido'];
                                                $tot_clts++;
                                                
                                                $sqlVerif = $objGRRF->consultaValorInformado($rows['id_clt']);
                                                
                                                if($sqlVerif['total'] > 0){
                                                    $valor = formataMoeda($sqlVerif['dados']['valor'], 1);
                                                }else{
                                                    $valor = "";
                                                }
                                                
                                                if($valor > 0){
                                                    $cor = 'style="background-color: #E3ECE3"';
                                                } else {
                                                    $cor = '';
                                                }
                                            ?>
                                            <tr>
                                                <td class="text-center <?php echo $tblImport; ?>">
                                                    <?php if (!$msgYes) { ?>
                                                        <input type="checkbox" name="ativo[]" value="<?= $rows['id_clt'] ?>" checked class="chk">
                                                    <?php } ?>
                                                </td>
                                                <td><?= $rows['id_clt'] ?></td>
                                                <td><?= $rows['nome'] ?></td>
                                                <td><?= $rows['pis'] ?></td>
                                                <td><?= $rows['nome_funcao'] ?></td>
                                                <td><?= $rows['especifica'] ?></td>
                                                <td><?= converteData($rows['data_entrada'], 'd/m/Y') ?></td>                                                
                                                <td><?= converteData($rows['data_demi'], 'd/m/Y') ?></td>                                                
                                                <td><?= number_format($rows['total_liquido'], 2, ',', '.') ?></td>
                                                <td class="action_val" data-idclt="<?= $rows['id_clt'] ?>">
                                                    <input type="text" <?= $cor ?> name="valor[<?= $rows['id_clt'] ?>]" size="9" class="valor_msk form-control val_inf" value="<?= $valor ?>" id="<?= $rows['id_clt'] ?>_valor" data-idclt="<?= $rows['id_clt'] ?>">                                                    
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="panel-footer text-right">
                                    <div class="pull-left">
                                        Total de Funcionários: <strong><?php echo $tot_clts; ?></strong> |
                                        Valor total das rescisões: <strong><?php echo number_format($tot_valor, 2, ',', '.'); ?></strong>
                                    </div>
                                    <?php if (!$msgYes) { ?>                                        
                                        <input type="submit" name="gerar_grrf_lote" class="btn btn-success <?php echo $gera_disable; ?>" value="Gerar GRRF">
                                    <?php } ?>
                                </div>
                            </div>
                        
                        <?php } else { ?>
                            <div class="alert alert-danger">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <p>Nenhum registro encontrado</p>
                            </div>
                        <?php } 
                        } ?>
                    </div>
                </div>
            </form>
            
            <?php include_once '../../template/footer.php'; ?>
        </div>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.maskedinput.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(function() {
                $('#regiao').ajaxGetJson("../../methods.php", {method: "carregaProjetos", id_projeto: <?=$projetoSel?>}, null, "projeto");
                
                $("#checkAll").click(function () {
                    if ($("#checkAll").prop("checked")) {
                        $(".chk").prop("checked", true);
                    } else {
                        $(".chk").prop("checked", false);
                    }
                });
                
                $(".valor_msk").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});
                
                $(".val_inf").blur(function(){
                    var id_clt = $(this).data("idclt");
                    var valor = $("#"+id_clt+"_valor").val();
                    
                    $.ajax({
                        type: "post",
                        url: "new_grrf_lote.php",
                        dataType: "json",
                        data: {
                            id: id_clt,
                            valor: valor,
                            method: "lanca_valor"
                        },
                        success: function(data) {
                            if(data.status == "1"){
                                $("#"+data.clt+"_valor").css("background-color", "rgba(41, 253, 15, 0.26)");
                            }else if(data.status == "2"){
                                $("#"+data.clt+"_valor").css("background-color", "#F00");
                            }
                        }
                    });
                });
            });
        </script>
    </body>
</html>