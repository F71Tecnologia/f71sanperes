<?php // session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/EntradaClass.php");
include("../../classes/BancoClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$objEntrada = new Entrada();

$container_full = true;
$objBanco = new Banco();
$dadosBanco = $objBanco->getControleSaldo();
$arrayBancos[''] = "Todos o Bancos";
while($row_bancos = mysql_fetch_assoc($dadosBanco)){ 
    $arrayBancos[$row_bancos['id_banco']] = "{$row_bancos['id_banco']} - {$row_bancos['nome_banco']} (CC: {$row_bancos['conta']} / Ag: {$row_bancos['agencia']})";
}

$global = new GlobalClass();

if(isset($_REQUEST['filtrar']) || isset($_REQUEST['excel'])){
    $id_projeto = $_REQUEST['projeto'];
    $id_regiao = $usuario['id_regiao'];
    $filtro = true;
    $result = $objEntrada->getBuscaEntrada();
    $total = mysql_num_rows($result);
    
    if(isset($_REQUEST['excel'])){
        $arquivo = 'Entradas.xls';
        header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
        header ("Cache-Control: no-cache, must-revalidate");
        header ("Pragma: no-cache");
        header ("Content-type: application/x-msexcel");
        header ("Content-Disposition: attachment; filename={$arquivo}" );
        header ("Content-Description: PHP Generated Data" );
    }
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$projetoR = $_REQUEST['projeto'];
$data_ini = (isset($_REQUEST['data_ini'])) ? $_REQUEST['data_ini'] : date('01/m/Y');
$data_fim = (isset($_REQUEST['data_fim'])) ? $_REQUEST['data_fim'] : date('t/m/Y');
$codigoR = $_REQUEST['id_entrada'];
$nomeR = $_REQUEST['nome'];
$tipoR = $_REQUEST['tipo'];

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Buscar Entradas");
$breadcrumb_pages = array("Principal" => "../../index.php");
?>
<?php if(!isset($_REQUEST['excel'])) { ?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Buscar Entradas</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->        
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        
        <div class="<?=($container_full) ? 'container-full' : 'container'?>">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - Buscar Entradas</small></h2></div>
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
                    <div class="panel-heading text-bold">Buscar Entradas</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-sm-offset-1 col-sm-1 control-label">Banco</label>
                            <div class="col-sm-9">
                                <?=montaSelect($arrayBancos, $_REQUEST['banco'], "id='banco' name='banco' class='form-control'")?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-sm-offset-1 col-sm-1 control-label">Projeto</label>
                            <div class="col-sm-4">
                                <?=montaSelect($global->carregaProjetosByMaster($usuario['id_master'], array("" => "Todos os Projetos")), $projetoR, "id='projeto' name='projeto' class='validate[required,custom[select]] form-control'")?>
                            </div>
                            <label for="select" class="col-sm-1 control-label">Período</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <input type="text" id='data_ini' name='data_ini' class='data required[custom[select]] form-control' value="<?=$data_ini?>">
                                    <span class="input-group-addon">até</span>
                                    <input type="text" id='data_fim' name='data_fim' class='data required[custom[select]] form-control' value="<?=$data_fim?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-sm-offset-1 col-sm-1 control-label">Código</label>
                            <div class="col-sm-4">
                                <input type="text" id="id_entrada" name="id_entrada" class="form-control" placeholder="Ex.: 123456 ou 123456,654321" value="<?php echo $_REQUEST['id_entrada']; ?>" />
                            </div>
                            <label for="select" class="col-sm-1 control-label">Tipo</label>
                            <div class="col-sm-4">
                                <?= montaSelect($objEntrada->getTipo(),$_REQUEST['tipo'], "id='tipo' name='tipo' class='form-control validate[required,custom[select]]'"); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-sm-offset-1 col-sm-1 control-label">Nome</label>
                            <div class="col-sm-9">
                                <input type="text" id="nome" name="nome" class="form-control" placeholder="Nome ou Descrição" value="<?php echo $_REQUEST['nome']; ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <?php if ($total > 0) { ?><button type="submit" name="excel" value="Excel" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Exportar Excel</button><?php } ?>
                        <button type="submit" name="filtrar" id="filt" value="Filtrar" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
                    </div>
                </div>
            </form>
<?php } ?>
            <?php
            if ($filtro) {
                if ($total > 0) { ?>
                    <table class='table table-hover table-condensed table-bordered text-sm valign-middle'>
                        <thead>
                            <tr class="bg-primary">
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Descrição</th>
                                <th>Tipo</th>
                                <th>Banco</th>
                                <!--<th>Projeto</th>-->
                                <th>Data de vencimento</th>
                                <th>Valor</th>                        
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysql_fetch_assoc($result)) { 
                                if($auxProjeto != $row['id_projeto']){ ?>
                                    <?php if($totProjeto > 0){ ?>
                                    <tr class="tr-bg-warning">
                                        <th colspan="6" class="text-right">TOTAL PROJETO</th>
                                        <th class=""><?= number_format($totProjeto, 2, ',', '.') ?></th>
                                    </tr>
                                    <?php } ?>
                                <tr class="tr-bg-info">
                                    <th colspan="7"><?=$row['nome_projeto']?></th>
                                </tr>
                                <?php $auxProjeto = $row['id_projeto']; $totProjeto = 0; ?>
                                <?php } ?>
                                <?php $totProjeto += $row['valor']; ?>
                                <tr>
                                    <td class="text-center"><?php echo $row['id_entrada']; ?></td>
                                    <td><?php echo $row['nome_entrada']; ?></td>
                                    <td><?php echo $row['especifica']; ?></td>
                                    <td><?php echo $row['tipo_entrada']; ?></td>
                                    <td><?php echo $row['id_banco'] . " - " . $row['nome_banco']; ?></td>
                                    <!--<td><?php echo $row['id_projeto'] . " - " . $row['nome_projeto']; ?></td>-->
                                    <td class="text-center"><?php echo $row['data2']; ?></td>
                                    <td><?php echo formataMoeda(str_replace(',', '.', $row['valor'])); $valorTotal += str_replace(',', '.', $row['valor']); ?></td>
                                </tr>
                            <?php } ?>
                            <tr class="tr-bg-warning">
                                <th colspan="6" class="text-right">TOTAL PROJETO</th>
                                <th class=""><?= number_format($totProjeto, 2, ',', '.') ?></th>
                            </tr>
                        </tbody>
                    </table>
                    <div class="alert alert-dismissable alert-warning col-sm-6 text-right pull-right">                
                        TOTAL: <?php echo "<strong> " . formataMoeda($valorTotal) . "</strong>"; ?>
                    </div>
                    <div class="clear"></div>
            <?php } else { ?>
                <div class="alert alert-danger top30">
                    Nenhum registro encontrado
                </div>
            <?php } ?>
            <?php } ?>
<?php if(!isset($_REQUEST['excel'])) { ?>
            <?php include('../../template/footer.php'); ?>
        </div>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>        
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../resources/js/main_bts.js"></script><!--TIRAR _BTS-->
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../resources/js/financeiro/entrada.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskMoney.js"></script>
        <script>
            $(function() {                
                //$("#form1").validationEngine({promptPosition : "topRight"});
                
                $("body").on('click', ".editar_entrada", function(){
                    $("#form1").append($('input', {value:$(this).data('key'), name:'id_enrada'}));
                    $("#form1").prop('action', '../../novoFinanceiro/view/editar.entrada.php');
                    $("#form1").submit();
                });
                $("body").on('click', ".anexarEntrada", function(){
                    var id = $(this).data("key");
                    //cria_carregando_modal();
                    $.post("../actions/action_entrada.php", {bugger:Math.random(), id:id, gerenciar_anexo_rel:'gerenciar_anexo_rel'}, function(resultado){
                        console.log(resultado);
                        bootDialog(
                            resultado, 
                            'Gerenciar Anexo Entrada '+id, 
                            [{
                                label: 'Fechar',
                                action: function (dialog) {
                                    dialog.close();
                                }
                            }],
                            'primary'
                        );
                        //remove_carregando_modal();
                    });
                });
            });
        </script>
    </body>
</html>
<?php } ?>