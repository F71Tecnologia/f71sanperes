<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include("../../conn.php");
include("../../classes/global.php");
include("../../classes/ComprasChamados.php");
include("../../wfunction.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$projeto1 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto1' name='projeto' class='form-control validate[required,custom[select]]'");
$global = new GlobalClass();

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "35", "area" => "Gestão de Compras e Contratos", "ativo" => "Chamados a Prestadores", "id_form" => "form-pedido");

$objChamado = new ComprasChamados();
$abertos = $objChamado->convertToArray($objChamado->getChamados(1));
$fechados = $objChamado->convertToArray($objChamado->getChamados(2));
$pendentes = $objChamado->convertToArray($objChamado->getChamados(3));
$alertas = $objChamado->convertToArray($objChamado->getAlertas(1));

?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet ::</title>

        <link rel="shortcut icon" href="../../favicon.png">

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-compras.css" rel="stylesheet" type="text/css">

    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <form action="ver.php" method="post" name="form1" id="form1">
                <input type="hidden" name="id_chamado" id="id_chamado" value="" />
                <div class="row">
                    <div class="col-lg-12">
                        <div class="page-header box-compras-header">
                            <h2><span class="glyphicon glyphicon-shopping-cart"></span> - Gestão de Compras e Contratos <small>- Chamados a Prestadores</small></h2>
                        </div>

                        <div role="tabpanel">
                            <ul class="nav nav-tabs nav-justified compras" style="margin-bottom: 15px;">
                                <li class="active"><a class="compras" href="#abertos" data-toggle="tab">Chamados Abertos</a></li> 
                                <li><a class="compras" href="#fechados" data-toggle="tab">Chamados Fechados</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="abertos">
                                    
                                    <h3>Alertas Abertos</h3>
                                    <?php if(count($alertas['dados']) > 0){ ?>
                                    <table class="table table-bordered table-striped table-header">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Número</th>
                                                <th class="text-center">Data e hora</th>
                                                <th class="text-center">Aberto Por</th>
                                                <th class="text-center">Unidade</th>
                                                <th class="text-center">Prestador</th>
                                                <th class="text-center">Ver</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($alertas['dados'] as $k => $val){ ?>
                                            <tr class="warning">
                                                <td><?php echo $val['id_chamado'];?></td>
                                                <td><?php echo $val['alertado_emBR'];?></td>
                                                <td><?php echo utf8_decode($val['nome']);?></td>
                                                <td><?php echo utf8_decode($val['nome_projeto']);?></td>
                                                <td><?php echo utf8_decode($val['nome_prestador']);?></td>
                                                <td><a href="javascript:;" class="btn btn-warning bt-ver" data-key="<?php echo $val['id_chamado'];?>" ><i class="fa fa-search"></i> Ver</a></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <?php }else{ ?>
                                    <div class="alert alert-dismissable alert-success">
                                        <strong>Que bom!</strong> Nenhum Alerta Aberto.
                                    </div>
                                    <?php } ?>
                                    
                                    <hr/>
                                    
                                    <h3>Solicitações em andamento</h3>
                                    <table class="table table-bordered table-striped table-header">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Número</th>
                                                <th class="text-center">Data e hora</th>
                                                <th class="text-center">Aberto Por</th>
                                                <th class="text-center">Unidade</th>
                                                <th class="text-center">Prestador</th>
                                                <th class="text-center">Ver</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($abertos['dados'] as $k => $val){ ?>
                                            <tr>
                                                <td><?php echo $val['id_chamado'];?></td>
                                                <td><?php echo $val['aberto_emBR'];?></td>
                                                <td><?php echo utf8_decode($val['nome']);?></td>
                                                <td><?php echo utf8_decode($val['nome_projeto']);?></td>
                                                <td><?php echo utf8_decode($val['nome_prestador']);?></td>
                                                <td><a href="javascript:;" class="btn btn-primary bt-ver" data-key="<?php echo $val['id_chamado'];?>" ><i class="fa fa-search"></i> Ver</a></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    
                                    <hr/>
                                    
                                    <h3>Solicitações Pendentes</h3>
                                    <?php if(count($pendentes['dados']) > 0){ ?>
                                    <table class="table table-bordered table-striped table-header">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Número</th>
                                                <th class="text-center">Data e hora</th>
                                                <th class="text-center">Aberto Por</th>
                                                <th class="text-center">Unidade</th>
                                                <th class="text-center">Prestador</th>
                                                <th class="text-center">Ver</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($pendentes['dados'] as $k => $val){ ?>
                                            <tr>
                                                <td><?php echo $val['id_chamado'];?></td>
                                                <td><?php echo $val['aberto_emBR'];?></td>
                                                <td><?php echo utf8_decode($val['nome']);?></td>
                                                <td><?php echo utf8_decode($val['nome_projeto']);?></td>
                                                <td><?php echo utf8_decode($val['nome_prestador']);?></td>
                                                <td><a href="javascript:;" class="btn btn-primary bt-ver" data-key="<?php echo $val['id_chamado'];?>" ><i class="fa fa-search"></i> Ver</a></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <?php }else{ ?>
                                    <div class="alert alert-dismissable alert-success">
                                        <strong>Que bom!</strong> Nenhuma Solicitação Pendente.
                                    </div>
                                    <?php } ?>
                                </div>

                                <div class="tab-pane fade in" id="fechados">
                                    <h3>Solicitações finalizadas</h3>
                                    <table class="table table-bordered table-striped table-header">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Número</th>
                                                <th class="text-center">Aberto Em</th>
                                                <th class="text-center">Fechado Em</th>
                                                <th class="text-center">Aberto Por</th>
                                                <th class="text-center">Fechado Por</th>
                                                <th class="text-center">Unidade</th>
                                                <th class="text-center">Prestador</th>
                                                <th class="text-center">Ver</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($fechados['dados'] as $k => $val){ ?>
                                            <tr>
                                                <td><?php echo $val['id_chamado'];?></td>
                                                <td><?php echo $val['aberto_emBR'];?></td>
                                                <td><?php echo $val['fechado_emBR'];?></td>
                                                <td><?php echo utf8_decode($val['nome']);?></td>
                                                <td><?php echo utf8_decode($val['fechado_por_nome']);?></td>
                                                <td><?php echo utf8_decode($val['nome_projeto']);?></td>
                                                <td><?php echo utf8_decode($val['nome_prestador']);?></td>
                                                <td><a href="javascript:;" class="btn btn-primary bt-ver" data-key="<?php echo $val['id_chamado'];?>" ><i class="fa fa-search"></i> Ver</a></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="clear"></div>
                    </div>
                </div>
            </form>
            <?php include_once '../../template/footer.php'; ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery.form.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        
        <script>
            $(function(){
                
                $(".bt-ver").click(function(){
                    var id = $(this).data('key');
                    $("#id_chamado").val(id);
                    $("#form1").submit();
                });
                
            });
        </script>
        
    </body>
</html>