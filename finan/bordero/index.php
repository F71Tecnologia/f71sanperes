<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../funcoes.php");
include("../../wfunction.php");
include("../../classes/global.php");
include("../../classes/SaidaClass.php");
include("../../classes/BorderoClass.php");
include("../../classes_permissoes/acoes.class.php");

$objAcoes = new Acoes();
$global = new GlobalClass();
$objBordero = new BorderoClass();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)


$condicao[] = ($_REQUEST['codigo']) ? "A.id = {$_REQUEST['codigo']}" : "";
$condicao[] = ($_REQUEST['data_ini']) ? "A.data_criacao >= '".implode('-', array_reverse(explode('/', $_REQUEST['data_ini'])))."'" : "";
$condicao[] = ($_REQUEST['data_fin']) ? "A.data_criacao <= '".implode('-', array_reverse(explode('/', $_REQUEST['data_fin'])))."'" : "";
$condicao[] = "A.pago = 1";
$condicao[] = "A.status = 1";



$objBordero->debug = true;
$arrayBordero = $objBordero->getBoredero($condicao);
//print_array($arrayBordero);
$nome_pagina = "Gestão de Borderô";
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>$nome_pagina);
$breadcrumb_pages = array("Principal" => "../");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: FINANCEIRO - <?php echo $nome_pagina ?></title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <!--<link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">-->
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - FINANCEIRO<small> - <?php echo $nome_pagina ?></small></h2></div>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <form action="" method="post">
                                <div class="form-group">
                                    <div class="col-md-3">
                                        <input type="text" name="codigo" class="form-control input-sm" value="<?php echo $_REQUEST['codigo'] ?>" placeholder="Nº Borderô" />
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <input type="text" name="data_ini" class="form-control data input-sm" value="<?php echo $_REQUEST['data_ini'] ?>" placeholder="Data Inicio" />
                                            <div class="input-group-addon"> até </div>
                                            <input type="text" name="data_fim" class="form-control data input-sm" value="<?php echo $_REQUEST['data_fim'] ?>" placeholder="Data Fim" />
                                        </div>
                                    </div>
                                    <button type="submit" name="" class="btn btn-sm btn-primary"><i class="fa fa-filter"></i></button>
                                </div>
                            </form>
                        </div>
                        <div class="panel-body">
                            <table id="table" class="table table-condensed table-bordered table-hover table-striped valign-middle text-sm">
                                <tr>
                                    <th>Descrição</th>
                                    <th class="text-center">Emissão</th>
                                    <th colspan="2" class="text-center"></th>
                                </tr>
                                <?php foreach($arrayBordero AS $k => $v) { ?>
                                    <tr class="borderos pointer <?php echo ($v['pago']) ? 'success' : '' ?>" data-id="<?php echo $v['id'] ?>">
                                        <td>BORDERO DE REMESSA DE DE DOCUMENTOS (<?php echo str_pad($v['id'], 6, "0", STR_PAD_LEFT) ?>)</td>
                                        <td class="text-center"><?php echo date('d/m/Y', strtotime($v['data_criacao'])) ?></td>
                                        <td class="text-center"><a href="/intranet/finan/solicitacao_pagamento.php?id=<?php echo $v['id'] ?>" target="_blank" class="btn btn-xs btn-default"><i class="fa fa-print"></i></a></td>
                                        <td class="text-center"><?php if($objAcoes->verifica_permissoes(134)) { ?><button type="button" class="btn btn-xs btn-danger deletarBordero" data-id="<?php echo $v['id'] ?>"><i class="fa fa-trash-o"></i></button><?php } ?></td>
                                    </tr>
                                    <?php if(count($v['saidas']) > 0) { ?>
                                        <?php foreach($v['saidas'] AS $k1 => $v1) { ?>
                                            <tr class="saidas hide b<?php echo $v['id'] ?>">
                                                <td colspan="2"><?php echo implode(' / ', $v1) ?></td>
                                                <td colspan="2" class="text-center"><?php if($objAcoes->verifica_permissoes(134)) { ?><button type="button" class="btn btn-xs btn-warning removerSaida" data-id_bordero="<?php echo $v['id'] ?>" data-id_saida="<?php echo $v1['id_saida'] ?>"><i class="fa fa-scissors"></i></button><?php } ?></td>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr class="saidas hide b<?php echo $v['id'] ?>">
                                            <td colspan="4">Não há mais saidas neste borderô!</td>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php include('../../template/footer.php'); ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/js/date.js"></script>
        <script src="../../resources/js/dataTables.1.10.16.min.js"></script>
        <script src="../../resources/js/dataTables.bootstrap.1.10.16.min.js"></script>
        <script>
            $(function(){
                $('body').on('click', '.deletarBordero', function(){
                    var $this = $(this);
                    bootConfirm('Confirmar a exclusão do borderô?', 'Confirmação', function(conf){
                        if(conf) {
                            $.post('actions.php', { method: 'deletar', id: $this.data('id') }, function(result){
                                bootAlert(result.msg, '', function(){
                                    if(result.status){ 
                                        location.reload();
                                    }
                                }, result.color);
                            }, 'json');
                        }
                    }, 'warning');
                });
                
                $('body').on('click', '.removerSaida', function(){
                    var $this = $(this);
                    bootConfirm('Confirmar a remoção da saída do borderô?', 'Confirmação', function(conf){
                        if(conf) {
                            $.post('actions.php', { method: 'removerSaida', id_bordero: $this.data('id_bordero'), id_saida: $this.data('id_saida') }, function(result){
                                bootAlert(result.msg, '', function(){
                                    if(result.status){ 
                                        location.reload();
                                    }
                                }, result.color);
                            }, 'json');
                        }
                    }, 'warning');
                });
                
                $('body').on('click', '.borderos', function(){
                    var $this = $(this);
                    $('.saidas').addClass('hide');
                    $('.b'+$this.data('id')).removeClass('hide');
                });
            });
        </script>
    </body>
</html>
