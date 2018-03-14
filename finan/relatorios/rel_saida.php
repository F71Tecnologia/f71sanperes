<?php // session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/SaidaClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE�ALHO (TROCA DE MASTER E DE REGI�ES)

$saida = new Saida();
$global = new GlobalClass();

if(isset($_REQUEST['filtrar'])){
    $id_projeto = $_REQUEST['projeto'];
    $filtro = true;
    $result = $saida->getSaida();
    $total_entrada = mysql_num_rows($result);
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMUL�RIO SELECIONADO */
if(isset($_REQUEST['projeto'])){
    $projetoR = $_REQUEST['projeto'];
    $bancoR = $_REQUEST['banco'];
    $dataIni = $_REQUEST['data_ini'];
    $dataFim = $_REQUEST['data_fim'];
}

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Relat�rio de Sa�das");
$breadcrumb_pages = array("Principal" => "../index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Relat�rio de Sa�das</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - Relat�rio de Sa�das</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
                
                <?php if(isset($_SESSION['regiao'])){ ?>                
                <!--resposta de algum metodo realizado-->
                <div class="alert alert-<?php echo $_SESSION['MESSAGE_TYPE']; ?> msg_cadsuporte">
                    <button type="button" class="close" data-dismiss="alert">�</button>
                    <p><?php echo $_SESSION['MESSAGE'];
                    session_destroy(); ?></p>
                </div>
                <?php } ?>
                
                <div class="panel panel-default">
                    <div class="panel-heading">Relat�rio de Sa�das</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Projeto</label>
                            <div class="col-lg-4">
                                <?php echo montaSelect($global->carregaProjetosByMaster($usuario['id_master']), $projetoR, "id='projeto' name='projeto' class='required[custom[select]] form-control'"); ?>
                            </div>
                            <label for="select" class="col-lg-1 control-label">Banco</label>
                            <div class="col-lg-4">
                                <?php echo montaSelect($global->carregaBancosByMaster($usuario['id_master'], array("todos" => "Todos"), 1), $bancoR, "id='banco' name='banco' class='required[custom[select]] form-control'"); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Visualizar Sa�das de</label>
                            <div class="col-lg-4">
                                <div class="input-group">
                                    <input type="text" class="input form-control data" name="data_ini" id="data_ini" placeholder="Data Inicial" value="<?php echo $dataIni; ?>">
                                    <span class="input-group-addon">at�</span>
                                    <input type="text" class="input form-control data" name="data_fim" id="data_fim" placeholder="Data Final" value="<?php echo $dataFim; ?>">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="submit" name="filtrar" id="filt" value="Gerar" class="btn btn-primary" />                            
                        <input type="hidden" name="hide_regiao" id="hide_regiao" value="<?php echo $projetoR; ?>" />
                        <input type="hidden" name="pausa" id="pausa" value="<?php echo $_SESSION['pausa']; ?>" />
                        <input type="hidden" name="volta" id="volta" value="<?php echo $_SESSION['regiao_select']; ?>" />                            
                    </div>
                </div>
            </form>
            
            <?php
            if ($filtro) {
                if ($total_entrada > 0) {
            ?>                                                
            
            <table class='table table-bordered table-hover table-striped table-condensed text-sm valign-middle'>
                <thead>
                    <tr class="bg-primary">
                        <th>C�digo Sa�da</th>
                        <th>Data de Recebimento</th>
                        <th>Nome do Cr�dito</th>
                        <th>Especifica��o</th>
                        <th>Conta Creditada</th>
                        <th>Tipo de Sa�da</th>                        
                        <th>Cadastrada por</th>
                        <th>Confirmada por</th>
                        <th>Valor Adicional</th>
                        <th>Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysql_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['id_saida']; ?></td>
                        <td><?php echo $row['data2']; ?></td>
                        <td><?php echo $row['saida_nome']; ?></td>
                        <td><?php echo $row['especifica']; ?></td>
                        <td><?php echo $row['banco_nome'] . " <br />  AG: " . $row['banco_agencia'] . " <br /> C: " . $row['banco_conta']; ?></td>
                        <td><?php echo $row['tipo_saida']; ?></td>                        
                        <td><?php echo $row['nomeCadastrou']; ?></td>
                        <td><?php echo $row['nomePagou']; ?></td>
                        <td><?php echo ($row['adicional'] == "") ? "" : $row['adicional']; ?></td>
                        <td><?php echo $row['valor']; ?></td>
                    </tr>
                    
                    <?php
                    $valor_soma = str_replace(",",".",$row['valor']);
                    $adicional = str_replace(",",".",$row['adicional']);

                    $valor_total1 = $valor_total1 + $valor_soma + $adicional;
                    ?>
                    
                    <?php } ?>
                </tbody>
            </table>
            
            <div class="alert alert-dismissable alert-warning col-lg-6">                
                TOTAL DE SA�DAS: <?php echo "{$dataIni} a {$dataFim}: <strong> " . formataMoeda($valor_total1) . "</strong>"; ?>
            </div>
            
            <div class="clear"></div>
            
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
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(function() {                
                $("#form1").validationEngine({promptPosition : "topRight"});
                
                //datepicker
                var options = new Array();
                options['language'] = 'pt-BR';
                $('.datepicker').datepicker(options);
            });
        </script>
    </body>
</html>