<?php
session_start();
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
    $filtro = true;
    $result_mes = $saida->getMesFechamentoAnual();
    $total_mes = mysql_num_rows($result_mes);
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMUL�RIO SELECIONADO */
$projetoR = $_REQUEST['projeto'];
$bancoR = $_REQUEST['banco'];
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$tipoR = $_REQUEST['tipo'];
$tipodataR = $_REQUEST['tipodata'];

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Relat�rio Anual Descritivo");
$breadcrumb_pages = array("Principal" => "../index.php", "Relat�rios de Fechamento" => "rel_fechamento.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relat�rio Anual Descritivo</title>

        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - Relat�rio Anual Descritivo</small></h2></div>
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
                    <div class="panel-heading">Relat�rio Anual Descritivo</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Projeto</label>
                            <div class="col-lg-4">
                                <?=montaSelect($global->carregaProjetosByRegiao($usuario['id_regiao']), $projetoR, "id='projeto' name='projeto' class='validate[required,custom[select]] form-control'")?>
                            </div>
                            <label for="select" class="col-lg-1 control-label">Conta</label>
                            <div class="col-lg-4">
                                <?=montaSelect($global->carregaBancosByRegiao($usuario['id_regiao'], array("-1" => "� Selecione �"), $agencia_conta = 1), $bancoR, "id='banco' name='banco' class='validate[required,custom[select]] form-control'"); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Ano</label>
                            <div class="col-lg-4">
                                <?=montaSelect(AnosArray(null,null),$anoR, "id='ano' name='ano' class='validate[required,custom[select]] form-control'"); ?>
                            </div>
                            <label for="select" class="col-lg-1 control-label">Tipo</label>
                            <div class="col-lg-4">
                                <select name="tipo" id="tipo" class="form-control">
                                    <option value="entrada" <?=selected("entrada", $tipoR); ?>>Entrada</option>
                                    <option value="saida" <?=selected("saida", $tipoR); ?>>Sa�da</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Data para refer�ncia</label>
                            <div class="col-lg-4">
                                <select name="tipodata" class="form-control">
                                    <option value="data_proc" <?=selected("data_proc", $tipodataR); ?>>Processamento</option>
                                    <option value="data_vencimento" <?=selected("data_vencimento", $tipodataR); ?>>Vencimento</option>
                                    <option value="data_pg" <?=selected("data_pg", $tipodataR); ?>>Pagamento</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="submit" name="filtrar" id="filt" value="Gerar" class="btn btn-primary" />
                    </div>
                </div>
            
            <?php
            if ($filtro) {
                if ($total_mes > 0) { ?>
                                
                <?php while ($row_mes = mysql_fetch_assoc($result_mes)) { 
                    $totalizador = 0;
                ?>
                <table class='table table-bordered table-hover table-striped table-condensed text-sm valign-middle'>
                    <thead>
                        <tr class="active">
                            <th class="text-center" colspan="3">
                                <?php echo mesesArray($row_mes['mes_desc']); ?><br />                                
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $result_tipo = $saida->getFechamentoAnual($row_mes['mes_desc']);
                        
                        while ($row_tipo = mysql_fetch_assoc($result_tipo)) {
                        ?>                                                
                        <tr>                            
                            <td><?php echo $row_tipo['tipo']." - ".$row_tipo['nome_tipo']; ?></td>
                            <td><?php echo formataMoeda($row_tipo['valor_tot'] + $row_tipo['tot_adicional']); ?></td>
                        </tr>
                        <?php
                            $totalizador += $row_tipo['valor_tot'] + $row_tipo['tot_adicional'];
                        }
                        $totalizador_geral += $totalizador;
                        ?>
                        <tr class="warning text-right">
                            <td colspan="2">Total em <?php echo mesesArray($row_mes['mes_desc']); ?>: <strong><?php echo formataMoeda($totalizador); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
                <?php } ?>                                
                
                <div class="alert alert-dismissable alert-info col-lg-6 pull-right text-right">
                    Total gasto em <?php echo $anoR.":<strong> " . formataMoeda($totalizador_geral) . "</strong>"; ?>
                </div>
                
                <div class="clear"></div>
            
            <?php } else { ?>
                <div class="alert alert-danger top30">                    
                    Nenhum registro encontrado
                </div>
            <?php }
            } ?>
        
        </form>
            
            <?php include("../../template/footer.php"); ?>
        </div>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/js/financeiro/detalhado.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(function() {
                $("#form1").validationEngine({promptPosition : "topRight"});
            });
        </script>
    </body>
</html>