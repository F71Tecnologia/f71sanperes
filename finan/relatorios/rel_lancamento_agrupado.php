<?php // session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../funcoes.php");
include("../../classes/BotoesClass.php");
include("../../classes/SaidaClass.php");
include("../../classes/global.php");
//print_array($_REQUEST);
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$saida = new Saida();
$global = new GlobalClass();

if(isset($_REQUEST['filtrar'])){
    $id_projeto = $_REQUEST['projeto'];
    $limite = $_REQUEST['limit'];
    $id_regiao = $usuario['id_regiao'];
    $filtro = true;
    $result = $saida->getBuscarLancamentoAgrupado();
    $total = mysql_num_rows($result);
}

//$arraySaidaRh = array(168, 167, 169, 170, 156, 154, 29, 30, 31, 32, 260, 171); //array(29, 30, 31, 32, 51, 76, 154, 156, 167, 168, 169, 170, 171, 175, 260);

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$projetoR = $_REQUEST['projeto'];
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$codigoR = $_REQUEST['id_saida'];
$nomeR = $_REQUEST['nome'];
$grupoR = $_REQUEST['grupo'];
$subgrupoR = isset($_REQUEST['subgrupo']) ? $_REQUEST['subgrupo'] : '';
$tipoR = $_REQUEST['tipo'];
$statusR = $_REQUEST['status'];
$limitR = $_REQUEST['limit'];
$count = 0;

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Buscar Lançamentos Agrupados");
$breadcrumb_pages = array("Principal" => "../index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Buscar Lançamentos Agrupados</title>
        
        <link href="../../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/datepicker.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - Buscar Lançamentos Agrupados</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
                
                <?php if(isset($_SESSION['regiao'])){ ?>                
                <!--resposta de algum metodo realizado-->
                <div class="alert alert-<?php echo $_SESSION['MESSAGE_TYPE']; ?> msg_cadsuporte">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <p><?php echo $_SESSION['MESSAGE'];
                    session_destroy(); ?></p>
                </div>
                <?php } ?>
                <div class="panel panel-default">
                    <div class="panel-heading text-bold">Buscar Lançamentos</div>
                    <div class="panel-body">
                        <div class="form-group">
<!--                            <label for="select" class="col-sm-offset-1 col-sm-1 control-label">Mês</label>
                            <div class="col-sm-4">
                                <div class="input-daterange input-group" id="bs-datepicker-range">
                                    <?=montaSelect(mesesArray(null,$key='todos',$opcao='Todos os Meses'),$mesR, "id='mes' name='mes' class='required[custom[select]] form-control'")?>
                                    <span class="input-group-addon">Ano</span>
                                    <?=montaSelect(AnosArray(2008,null),$anoR, "id='ano' name='ano' class='required[custom[select]] form-control'")?>
                                </div>
                            </div>-->
                            <label for="select" class="col-sm-offset-1 col-sm-1 control-label">Vencimento</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                <input type="text" class="form-control data" name="data_vencimento_ini" value="<?php echo ($_REQUEST['data_vencimento_ini']) ? $_REQUEST['data_vencimento_ini'] : '01' . date('/m/Y') ?>">
                                <div class="input-group-addon"> até </div>
                                <input type="text" class="form-control data" name="data_vencimento_fim" value="<?php echo ($_REQUEST['data_vencimento_fim']) ? $_REQUEST['data_vencimento_fim'] : date('t/m/Y') ?>">
                                </div>
                            </div>
                            <label for="select" class="col-sm-1 control-label">Grupo</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($saida->getGrupo(), $grupoR, " name='grupo' id='select_grupo' class='form-control'"); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-sm-offset-1 col-sm-1 control-label">Subgrupo</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect(array('todos' => 'Todos os Subgrupos'), $subgrupoR, " name='subgrupo' id='subgrupo' class='form-control'"); ?>
                            </div>
                            <label for="select" class="col-sm-1 control-label">Tipo</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect(array('todos' => 'Todos os Tipos'), $tipoR, " name='tipo' id='tipo' class='form-control''"); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-sm-offset-1 col-sm-1 control-label no-padding-l">Status</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect(['t' => 'Todos os status', 1 => "À pagar", 2 => "Pagas"], $statusR, " name='status' id='status' class='form-control'"); ?>
                            </div>
                            <label for="select" class="col-sm-1 control-label no-padding-l">Banco</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($global->carregaBancosByMaster($usuario['id_master'], []), $_REQUEST['id_banco'], " name='id_banco' id='id_banco' class='form-control'"); ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <button type="button" id="imprimir" value="Imprimir " class="btn btn-default"><i class="fa fa-print"></i> Imprimir</button>
                        <button type="button" id="filt" value="Gerar" class="btn btn-primary"><i class="fa fa-filter"></i> Gerar</button>
                        <input type="hidden" name="filtrar" id="filtrar" value="" />
                        <input type="hidden" name="hide_regiao" id="hide_regiao" value="<?php echo $projetoR; ?>" />
                        <input type="hidden" name="pausa" id="pausa" value="<?php echo $_SESSION['pausa']; ?>" />
                        <input type="hidden" name="volta" id="volta" value="<?php echo $_SESSION['regiao_select']; ?>" />
                        <input type="hidden" name="id_saida_edit" id="id_saida_edit" value="" />
                    </div>
                </div>
            </form>
            <?php
            if ($filtro) {
                if ($total > 0) { ?>
                    <form action="../solicitacao_pagamento.php" method="post" target="_blank" id="printForm">
                        <table class='table table-hover table-striped table-condensed table-bordered text-sm valign-middle'>
                        <thead>
                            <tr class="bg-primary">
                                <th><input type="checkbox" class="sel_todos" name=""></th>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Descrição</th>
                                <th>Banco</th>
                                <th>Nº Nota</th>
                                <th>Data de vencimento</th>
                                <th>Valor</th>                        
                                <th>Anexos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysql_fetch_assoc($result)) { 
                                if($auxProjeto != $row['projeto_id']) { 
                                    if($count > 0) {
                                        echo 
                                        "<tr>
                                            <td colspan='9' class='text-right'>Total Unidade: ".number_format($totalProjeto, 2, ',', '.')."</td>
                                        </tr>";
                                    }
                                    echo 
                                    "<tr>
                                        <td colspan='9'>{$row['projeto_id']} - {$row['projeto_nome']}</td>
                                    </tr>";
                                    $auxProjeto = $row['projeto_id'];
                                    $totalProjeto = 0;
                                }
                                $count++;
                                $totalProjeto += $row['saida_valor']; 
                                ?>
                                <tr>
                                    <td class="text-center"><input type="checkbox" class="saidas_check" name="saidas[]" value="<?php echo $row['saida_id']; ?>"></td>
                                    <td class="text-center"><?php echo $row['saida_id']; ?></td>
                                    <td><?php echo $row['saida_nome']; ?></td>
                                    <td><?php echo $row['saida_especifica']; ?></td>
                                    <td><?php echo $row['banco_id'] . " - " . $row['banco_nome']; ?></td>
                                    <td><?php echo $row['n_documento']; ?></td>
                                    <td class="text-center"><?php echo $row['saida_vencimento']; ?></td>
                                    <td><?php echo formataMoeda($row['saida_valor']); ?></td>

                                    <?php
                                    $comprovante = '';
                                    if($row['tipo'] == 170){
                                        //SAIDA DO TIPO RESCISÃO
                                        $rescisao = $saida->verificaSaidaRescisao($row['saida_id']);
                                        if(!empty($rescisao)){
                                            $comprovante .= "<a target=\"_blank\" title=\"Rescisão\" class=\"btn btn-xs btn-danger\" href=\"../../rh/recisao/{$rescisao}\"> <i class=\"fa fa-file-pdf-o\"></i></a>";
                                            $tot_file = 1;
                                        }
                                    }else{
                                        //SAIDA NORMAL
                                        $res_file = $saida->getSaidaFile($row['saida_id']);
                                        $tot_file = mysql_num_rows($res_file);
                                        $comprovante = '';

                                        while($row_file = mysql_fetch_assoc($res_file)){
                                            $nome_arquivo = '';
                                            $nome_arquivo = $row_file['id_saida_file'].'.'.$row_file['id_saida'].$row_file['tipo_saida_file'];
                                            if(file_exists("../../comprovantes/$nome_arquivo")){
                                                $comprovante .= '<a target="_blank" title="Comprovante" class="margin_r5" data-type="editar" data-key="'.$row['saida_id'].'" href="../../comprovantes/'.$nome_arquivo.'"><i class="bt-image fa fa-paperclip"></i></a>';
                                            }
                                        }
                                    }

                                    $res_file_pg = $saida->getSaidaFilePg($row['saida_id']);
                                    $tot_file_pg = mysql_num_rows($res_file_pg);
                                    $comprovante_pg = '';

                                    while($row_file_pg = mysql_fetch_assoc($res_file_pg)){
                                        $nome_arquivo_pg = '';                            
                                        $nome_arquivo_pg = $row_file_pg['id_pg'].'.'.$row_file_pg['id_saida'].'_pg'.$row_file_pg['tipo_pg'];
                                        if(file_exists("../../comprovantes/$nome_arquivo_pg")){
                                            $comprovante_pg .= '<a target="_blank" title="Comprovante de Pagamento" class="" data-type="editar" data-key="'.$row['saida_id'].'" href="../../comprovantes/'.$nome_arquivo_pg.'"><i class="bt-image fa fa-paperclip" ></i></a>';
                                        }
                                    } ?>

                                    <td class="text-center">                            
                                        <?php 
                                        if($tot_file > 0 OR $row['saida_comprovante'] == 1)
                                            echo $comprovante; 
                                        
                                        if($tot_file_pg > 0)
                                            echo $comprovante_pg; ?>
                                    </td>
                                </tr>
                                <?php
                                $valor_soma = str_replace(",",".",$row['saida_valor']);
                                $adicional = str_replace(",",".",$row['saida_adicional']);

                                $valor_total1 = $valor_total1 + $valor_soma + $adicional; 
                            } ?>
                            <tr>
                                <td colspan='9' class='text-right'>Total Unidade: <?php echo number_format($totalProjeto, 2, ',', '.')?></td>
                            </tr>
                        </tbody>
                    </table>
                        <div class="alert alert-dismissable alert-warning col-sm-6 text-right pull-right">                
                            TOTAL: <?php echo "<strong> " . formataMoeda($valor_total1) . "</strong>"; ?>
                        </div>
                        <div class="clear"></div>
                    </form>
            <?php } else { ?>
                <div class="alert alert-danger top30">
                    Nenhum registro encontrado
                </div>
            <?php }
            } ?>
            
            <?php include('../../template/footer.php'); ?>
        </div>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-datepicker.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <!--<script src="../../resources/js/financeiro/saida.js"></script>-->
        <script src="../../js/global.js"></script>        
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(function() {
                $("#form1").validationEngine({promptPosition : "topRight"});
                
                $("#filt").click(function(){
                    $("#filtrar").val('filtrar');
                    $("#form1").submit();
                });
                
                $('body').on('click', '#imprimir', function(){
                    $('#printForm').submit();
                });
                $('body').on('click', '.sel_todos', function(){
                    $('.saidas_check').prop('checked', $(this).prop('checked'));
                });
                
                $("#select_grupo").ajaxGetJson("../actions/action.saida.php", {action: "load_subgrupo"}, null, "subgrupo");                
                $("#subgrupo").ajaxGetJson("../actions/action.saida.php", {action: "load_tipo"}, null, "tipo");
                
                //datepicker
                var options = new Array();
                options['language'] = 'pt-BR';
                $('.datepicker').datepicker(options);
                
                $("#nome").removeClass('validate[required,custom[select]]');
                
                $(".bt-image").on("click", function() {
                    var action = $(this).data("type");
                    var key = $(this).data("key");
                    var periodo = $(this).data("periodo");
                    var emp = $(this).parents("tr").find("td:first").next().html();
                    var clt = $(this).data("clt");
                    var target = $(this).data("target");
                    if(action === "visualizar") {
                        $("#banco").val(key);
                        $("#form1").attr('action','detalhes_banco.php');
                        $("#form1").prop('target',target);
                        $("#form1").submit();
                    }else if(action === "editar"){
                        $("#banco").val(key);
                        $("#form1").attr('action','form_banco.php');
                        $("#form1").prop('target',target);
                        $("#form1").submit();
                    }else if(action === "editar_saida"){
                        $("#id_saida").val(key);
                        $("#form1").attr('action','../form_saida.php');
                        $("#form1").prop('target',target);
                        $("#form1").submit();
                    }else if(action === "editar_saida_rh"){
                        $("#id_saida").val(key);
                        $("#form1").attr('action','../form_saida_rh.php');
                        $("#form1").prop('target',target);
                        $("#form1").submit();
                    }
                    $("#form1").attr('action','');
                    $("#form1").prop('target','');
                    $("#id_saida").val('');
                });
            });
        </script>
    </body>
</html>