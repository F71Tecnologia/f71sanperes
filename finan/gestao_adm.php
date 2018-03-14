<?php // session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
}

include("../conn.php");
include("../wfunction.php");
include("../classes/BotoesClass.php");
include("../classes/SaidaClass.php");
include("../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$saida = new Saida();
$global = new GlobalClass();

if(isset($_REQUEST['filtrar'])){
    $id_projeto = $_REQUEST['projeto'];
    $limite = $_REQUEST['limit'];
    $lancamento = $_REQUEST['lancamento'];
    $id_regiao = $usuario['id_regiao'];
    $filtro = true;
    $result = $saida->getEntradaSaida();
    $total = mysql_num_rows($result);
}

$arraySaidaRh = array(171, 168, 167, 169, 156, 76, 51, 170, 154, 260, 175, 260);

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$projetoR = $_REQUEST['projeto'];
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$codigoR = (isset($_REQUEST['id_saida'])) ? $_REQUEST['id_saida'] : $_REQUEST['id_entrada'];
$nomeR = $_REQUEST['nome_'];
$grupoR = $_REQUEST['grupo'];
$subgrupoR = isset($_REQUEST['subgrupo']) ? $_REQUEST['subgrupo'] : '';
$tipoR = $_REQUEST['tipo'];
$limitR = $_REQUEST['limit'];
$lancamentoR = $_REQUEST['lancamento'];

$tipo_lancamento = ($lancamentoR == 1) ? 'entrada' : 'saida';

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Gestão Administrativa");
$breadcrumb_pages = array("Principal" => "index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Gestão ADM</title>
        
        <link href="../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/datepicker.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - Gestão Administrativa</small></h2></div>
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
                    <div class="panel-heading text-bold">Buscar Saida/Entrada</div>
                    <div class="panel-body">
                        <div class="form-group no-margin-b">
                            <div class="col-sm-3">
                                <label for="select" class="control-label">Projeto</label>
                                <?=montaSelect($global->carregaProjetosByMaster($usuario['id_master'], array("0" => "Todos os Projetos")), $projetoR, "id='projeto' name='projeto' class='validate[required,custom[select]] form-control input-sm'")?>
                            </div>
                            <div class="col-sm-3">
                                <label for="select" class="control-label">Periodo</label>
                                <div class="input-daterange input-group" id="bs-datepicker-range">
                                    <input type="text" id="data_ini" name="data_ini" class="data form-control input-sm" placeholder="Data Inicio" value="<?php echo ($_REQUEST['data_ini']) ? $_REQUEST['data_ini'] : date('01/m/Y'); ?>" />
                                    <span class="input-group-addon">até</span>
                                    <input type="text" id="data_fim" name="data_fim" class="data form-control input-sm" placeholder="Data Final" value="<?php echo ($_REQUEST['data_fim']) ? $_REQUEST['data_fim'] : date('t/m/Y'); ?>" />
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label for="select" class="control-label">Código</label>
                                <input type="text" id="id_saida" name="id_saida" class="form-control input-sm" placeholder="Ex.: 123456 ou 123456,654321" value="<?php echo $codigoR; ?>" />
                            </div>
                            <div class="col-sm-3">
                                <label for="select" class="control-label">Nome</label>
                                <input type="text" id="nome" name="nome" class="form-control input-sm" value="<?php echo $_REQUEST['nome']; ?>" />
                            </div>
                        </div>
                        <div class="form-group no-margin-b">
                            <div class="col-sm-3">
                                <label for="select" class="control-label">Grupo</label>
                                <?php echo montaSelect($saida->getGrupo(), $grupoR, " name='grupo' id='select_grupo' class='form-control input-sm'"); ?>
                            </div>
                            <div class="col-sm-3">
                                <label for="select" class="control-label">Subgrupo</label>
                                <?php echo montaSelect(array('todos' => 'Todos os Subgrupos'), $subgrupoR, " name='subgrupo' id='subgrupo' class='form-control input-sm'"); ?>
                            </div>
                            <div class="col-sm-3">
                                <label for="select" class="control-label">Tipo</label>
                                <?php echo montaSelect(array('todos' => 'Todos os Tipos'), $tipoR, " name='tipo' id='tipo' class='form-control input-sm''"); ?>
                            </div>
                            <div class="col-sm-3">
                                <label for="n_documento" class="control-label">Nº Bordero</label>
                                <input type="text" id="bordero" name="bordero" class="form-control input-sm" value="<?php echo $_REQUEST['bordero'] ?>" />
                            </div>
                        </div>
                        <div class="form-group no-margin-b">
                            <div class="col-sm-3">
                                <label for="n_documento" class="control-label">Nº Documento</label>
                                <input type="text" id="n_cheque" name="n_documento" class="form-control input-sm" value="<?php echo $_REQUEST['n_documento'] ?>" />
                            </div>
<!--                            <div class="col-sm-3">
                                <label for="n_bordero" class="control-label">Nº Borderô</label>
                                <input type="text" id="n_bordero" name="n_bordero" class="form-control input-sm" value="<?php echo $_REQUEST['n_bordero'] ?>" />
                            </div>-->
                            <div class="col-sm-3">
                                <label for="select" class="control-label no-padding-l">Status</label>
                                <?php echo montaSelect(['t' => 'Todos os status', 1 => "À pagar", 2 => "Pagas"], $statusR, " name='status' id='status' class='form-control input-sm''"); ?>
                            </div>
                            <div class="col-sm-3">
                                <label for="select" class="control-label no-padding-l">Lançamento</label>
                                <div class="input-group">
                                    <label class="input-group-addon"><input type="radio" id="lancamento1" name="lancamento" class="validate[required] lanc" value="1" <?php if($lancamentoR == '1'){ echo "checked"; } ?> /></label>
                                    <label class="form-control pointer" for="lancamento1">Entrada</label>
                                    <label class="input-group-addon"><input type="radio" id="lancamento2" name="lancamento" class="validate[required] lanc" value="2" <?php if($lancamentoR == '2'){ echo "checked"; } ?> /></label>
                                    <label class="form-control pointer" for="lancamento2">Saida</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="submit" name="filtrar" id="filt" value="Gerar" class="btn btn-primary" />
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
                    <table class='table table-hover table-striped table-condensed table-bordered text-sm valign-middle'>
                        <thead>
                            <tr class="bg-primary">
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Descrição</th>
                                <th>Banco</th>
                                <!--<th>Região</th>-->
                                <th>Projeto</th>
                                <th>N Nota</th>
                                <th>Bordero</th>
                                <th>Data de vencimento</th>
                                <th>Valor</th>
                                <!--th>Voltar</th>
                                <th>Editar</th-->
                                <th class="text-center" colspan="4">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysql_fetch_assoc($result)) { ?>
                                <tr>
                                    <td class="text-center"><?php echo $row['saida_id']; ?></td>
                                    <td><?php echo $row['saida_nome']; ?></td>
                                    <td><?php echo $row['saida_especifica']; ?></td>
                                    <td><?php echo $row['banco_id'] . " - " . $row['banco_nome']; ?></td>
                                    <!--<td><?php echo $row['regiao_id'] . " - " . $row['regiao_nome']; ?></td>-->
                                    <td><?php echo $row['projeto_id'] . " - " . $row['projeto_nome']; ?></td>
                                    <td><?php echo $row['n_documento']; ?></td>
                                    <td><?php echo $row['id_bordero']; ?></td>
                                    <td class="text-center"><?php echo $row['saida_vencimento']; ?></td>
                                    <td><?php echo formataMoeda($row['saida_valor']); ?></td>                                                           
                                    <td class="text-center">
                                        <?php if(array_key_exists('estorno',$row) && !$row['estorno'] && $row['status'] == 2) { ?>
                                        <a class="btn btn-xs btn-success bt-image" href="javascript:;" data-action="estorno" data-key="<?php echo $row['saida_id']; ?>" data-tipo="<?php echo $tipo_lancamento; ?>" title="Estornar">
                                            <i class="bt-image fa fa-usd"></i>
                                        </a>
                                        <?php } ?>
                                    </td>
                                    <td class="text-center">
                                        <a class="btn btn-xs btn-info bt-image" href="javascript:;" data-action="voltar" data-key="<?php echo $row['saida_id']; ?>" data-tipo="<?php echo $tipo_lancamento; ?>" title="Voltar">
                                            <i class="bt-image fa fa-history"></i>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a class="btn btn-xs btn-warning bt-image" href="javascript:;" data-action="editar" data-key="<?php echo $row['saida_id']; ?>" data-tipo="<?php echo $tipo_lancamento; ?>"  title="Editar">
                                            <i class="bt-image fa fa-pencil"></i>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a class="btn btn-xs btn-danger bt-image" href="javascript:;" data-action="excluir" data-key="<?php echo $row['saida_id']; ?>" data-tipo="<?php echo $tipo_lancamento; ?>" title="Excluir">
                                            <i class="bt-image fa fa-trash-o"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                $valor_soma = str_replace(",",".",$row['saida_valor']);
                                $adicional = str_replace(",",".",$row['saida_adicional']);
                                
                                $valor_total1 = $valor_total1 + $valor_soma + $adicional; 
                            } ?>
                        </tbody>
                    </table>
                    <div class="alert alert-dismissable alert-warning col-sm-6 text-right pull-right">                
                        TOTAL: <?php echo "<strong> " . formataMoeda($valor_total1) . "</strong>"; ?>
                    </div>
                    <div class="clear"></div>
            <?php } else { ?>
                <div class="alert alert-danger top30">
                    Nenhum registro encontrado
                </div>
            <?php }
            } ?>
            
            <?php include('../template/footer.php'); ?>
        </div>
        
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>        
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <!--<script src="../resources/js/financeiro/saida.js"></script>-->
        <script src="../js/global.js"></script>        
        <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(function() {
                $("#form1").validationEngine({promptPosition : "topRight"});
                
                $("#select_grupo").ajaxGetJson("actions/action.saida.php", {action: "load_subgrupo"}, null, "subgrupo");
                $("#subgrupo").ajaxGetJson("actions/action.saida.php", {action: "load_tipo"}, null, "tipo");
                
//                $(".bt-image").on("click", function() {
//                    var action = $(this).data("action");
//                    var id = $(this).data("key");
//                    var url = 'gestao_adm_edit.php';
//                    
//                    if(action === "editar") {
//                        $.post(url, {id: id}, function(data){
//                            bootDialog(data,'Edição de Entrada/Saida');
//                        });
//                    }
//                });
                
                $("table").on('click', ".bt-image", function(){
                    var action = $(this).data("action");
                    var id = $(this).data("key");
                    var tipo = $(this).data("tipo");
                    
                    if(action === "editar") {
                        $.post("gestao_adm_edit.php", {id:id, tipo:tipo}, function(data){
                            bootDialog(data, 'Edição de valor de ' +tipo);
                        });
                    }else if(action === "excluir"){
                        $.post("gestao_adm_delete.php", {id:id, tipo:tipo}, function(data){
                            bootDialog(data, 'Exclusão de ' +tipo);
                        });
                    }else if(action === "voltar"){
                        $.post("gestao_adm_voltar.php", {id:id, tipo:tipo}, function(data){
                            bootDialog(data, 'Voltar valor de ' +tipo);
                        });
                    }else if(action === "estorno"){
                        bootConfirm('Confirmar estorno da saída', 'Confirmação', function( confirmar ){ 
                            if(confirmar) {
                                $.post("actions/action.saida.php", { action:'estornar', id:id, tipo:tipo }, function(data){
                                    console.log(data);
                                    if(data.status){
                                        bootAlert(data.msg, 'Estono de ' +tipo, function(){window.location.reload();}, 'success');
                                    } else {
                                        bootAlert(data.msg, 'Erro', null, 'danger');
                                    }
                                }, 'json');
                            }
                        });
                    }
                });
                
                $(".lanc").click(function(){
                   var tipo = $(this).val();
                   
                   if(tipo == 1){
                       $("#id_saida").attr('name', 'id_entrada');
                   }else if(tipo == 2){
                       $("#id_saida").attr('name', 'id_saida');
                   }
                });
            });
        </script>
    </body>
</html>