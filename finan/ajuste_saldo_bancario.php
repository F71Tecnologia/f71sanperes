<?php
session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
}

include("../conn.php");
include("../wfunction.php");
include("../classes/BancoClass.php");
include("../classes/global.php");
include("../classes/LogClass.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$global = new GlobalClass();
$banco = new Banco();
$log = new Log();

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'salvar'){ 
//    print_array($_REQUEST);
    $novo_saldo = str_replace('.', '', $_REQUEST['novo_saldo']);
    $qry = mysql_query("UPDATE bancos SET saldo = '{$novo_saldo}' WHERE id_banco = '{$_REQUEST['id_banco']}' LIMIT 1;");
    if($qry){
        $log->gravaLog("Financeiro: Saldo Bancario", "Ajuste de saldo bancário: De: {$_REQUEST['saldo_atual']} para {$novo_saldo}, Motivo: ". addslashes($_REQUEST['motivo']) );
        echo 1;
    } else {
        echo mysql_errno();
    }
    exit;
}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'dadosBanco'){ 
    $sqlBanco = mysql_fetch_assoc(mysql_query("SELECT * FROM bancos WHERE id_banco = '{$_REQUEST['id_banco']}' LIMIT 1;")); ?>
    <form id="form_saldo" action="">
        <div class="form-group">
            <div class="col-sm-12">
                <label for="" class="control-label">Saldo Atual: </label>
                <label class="text-primary">R$ <?= $sqlBanco['saldo'] ?></label>
                <input type="hidden" name="saldo_atual" value="<?= $sqlBanco['saldo'] ?>">
                <input type="hidden" name="id_banco" value="<?= $sqlBanco['id_banco'] ?>">
                <input type="hidden" name="method" value="salvar">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label for="banco" class="control-label">Novo Saldo:</label>
                <input type="text" id="novo_saldo" name="novo_saldo" class="form-control valor" value="<?= $sqlBanco['saldo'] ?>">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label for="banco" class="control-label">Motivo:</label>
                <textarea id="motivo" name="motivo" class="form-control"></textarea>
            </div>
        </div>
        <div class="clear"></div>
    </form>
    <script>$(function(){$('.valor').maskMoney({prefix: 'R$ ', allowNegative: true, thousands: '.', decimal: ','});})</script>
<?php exit; }

$sqlBancos = "SELECT id_banco, nome, saldo FROM bancos WHERE id_regiao = '{$usuario['id_regiao']}' ORDER BY nome";
$qryBancos = mysql_query($sqlBancos) or die(mysql_error());
$numBancos = mysql_num_rows($qryBancos);

$nome_pagina = "Ajuste Saldo Bancário";
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>$nome_pagina);
//$breadcrumb_pages = array("Principal" => "index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link href="../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->        
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>

        <div class="container">
            <div class="col-md-12">
                <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - <?= $nome_pagina ?></small></h2></div>
                
                <!--resposta de algum metodo realizado-->
<!--                <form action="" method="post" id="formAnexo" class="form-horizontal top-margin1" enctype="multipart/form-data" autocomplete="off">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="banco" class="col-md-2 control-label">Banco</label>
                                <div class="col-md-9">
                                    <?=montaSelect($global->carregaBancosByRegiao($usuario['id_regiao']), $bancoR, 'id="id_banco" name="id_banco" class="validate[required,custom[select]] form-control"'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <button type="button" id="filtrar" class="btn btn-primary" name="filtrar" value="filtrar"><i class="fa fa-filter"></i> Selecionar</button>
                        </div>
                    </div>
                </form>-->
<?php if($numBancos > 0){ ?>
<table class="table table-condensed table-bordered table-hover text-sm valign-middle">
    <tr class="bg-primary text-bold">
        <td>ID</td>
        <td>NOME</td>
        <td>SALDO</td>
        <td></td>
    </tr>
    <?php while($rowBancos = mysql_fetch_assoc($qryBancos)){ ?>
    <tr>
        <td><?= $rowBancos['id_banco'] ?></td>
        <td><?= $rowBancos['nome'] ?></td>
        <td class="text-right"><?= $rowBancos['saldo'] ?></td>
        <td class="text-center"><button class="btn btn-xs btn-primary btn-saldo" type="button" data-key="<?= $rowBancos['id_banco'] ?>"><i class="fa fa-edit"></i></button></td>
    </tr>
    <?php } ?>
</table>
<?php } else { ?>
<div class="alert alert-info text-bold">Nenhuma informação encontrada!</div>
<?php } ?>
            </div>
            <div class="clear"></div>
            <?php include("../template/footer.php"); ?>
        </div>
        
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/dropzone/dropzone.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.maskMoney.js"></script>
        <script src="../resources/js/financeiro/index.js"></script>
        <script>
            $(function() { 
                $("body").on('click', '.btn-saldo', function() { 
                    $.post('', { method: 'dadosBanco', id_banco: $(this).data('key')}, function(data){
                        bootConfirm(data, 'Ajustar Saldo', function(data2){
                           var parametros = $('#form_saldo').serialize();
                            if(data2){
                                bootConfirm('Confirmar Alteração?', 'Confirmação', function(data3){
                                    if(data3){
                                        $.post('', parametros, function(data4){
                                            console.log(data4);
                                            if(data4 == 1){
                                                bootAlert('Alteração efetuada com sucesso!','',function(){window.location.reload();},'success');
                                            } else {
                                                bootAlert("Erro: " + data4,'Erro',null,'danger');
                                            }
                                        });
                                    }
                                }, 'warning');
                            }
                        }, 'info');
                    });
                });
            });
        </script>
    </body>
</html>