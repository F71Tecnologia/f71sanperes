<?php // session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/AbastecimentoClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$abastecimento = new Abastecimento();

$id_regiao = $usuario['id_regiao'];
$id_usuario = $usuario['id_funcionario'];
$reg = $abastecimento->getRegiaoAb($id_regiao);
$nome_regiao = $reg['regiao'];

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO PREENCHIDOS */
$dataIni = $_REQUEST['data_ini'];
$dataFim = $_REQUEST['data_fim'];
$anual = $_REQUEST['anual'];

if(isset($_REQUEST['filtrar'])){
    if($anual != 1 && $dataIni == "" && $dataFim == ""){
        $filtro = false;
        $alerta = 'show';
    }else{
        $filtro = true;
        $alerta = 'hide';
        $result = $abastecimento->getAbastecimento($id_regiao);
        $total_abastecimento = mysql_num_rows($result);    
        $result_f = $abastecimento->getAbastecimentoIndividual($id_regiao); 
    }
}

if($anual == 1){
    $periodo = "em ".date('Y');
}else{
    $periodo = "de {$dataIni} a {$dataFim}";
}

$ids_acesso = array('64','65','68','9','27','5','1','77','80','85','87');

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Relatório de Abastecimento");
$breadcrumb_pages = array("Principal" => "../index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Relatório de Abastecimento</title>
        
        <link href="../../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">        
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">                
        
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - Relatório de Abastecimento</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">                                
                
                <div class="panel panel-default">
                    <div class="panel-heading">Relatório de Abastecimento</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-lg-4">
                                <input type="checkbox" id="anual" name="anual" value="1"
                                <?php
                                if($anual == 1){
                                    echo "checked";
                                }
                                ?>
                                />
                                Marque para ver o relatório anual
                            </div>
                        </div>
                        <div class="form-group datas">
                            <div class="col-lg-4">
                                <div class="input-daterange input-group" id="bs-datepicker-range">
                                    <input type="text" class="input form-control data" name="data_ini" id="data_ini" readonly="true" placeholder="Inicio" value="<?php echo $dataIni; ?>">
                                    <span class="input-group-addon ate">até</span>
                                    <input type="text" class="input form-control data" name="data_fim" id="data_fim" readonly="true" placeholder="Fim" value="<?php echo $dataFim; ?>">
                                    <span class="input-group-addon ate"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="submit" name="filtrar" id="filt" value="Gerar" class="btn btn-primary" />
                    </div>
                </div>
            </form>
            
            <?php
            if ($filtro) {
                if ($total_abastecimento > 0) {
            ?>                                                
            
            <table class='table table-bordered table-hover table-striped table-condensed text-sm valign-middle'>
                <thead>
                    <tr class="warning">
                        <td colspan="7" class="text-center">ABASTECIMENTOS EM <?php echo $nome_regiao; ?></td>
                    </tr>
                    <tr class="bg-primary">
                        <th>Código</th>
                        <th>Autorizado Para:</th>
                        <th>Placa</th>                                                
                        <th>Autorizado Em:</th>                                                
                        <th>Autorizado Por:</th>
                        <th>Numero da Nota</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysql_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['id_combustivel']; ?></td>
                        <td><?php echo $row['autorizado_para']; ?></td>
                        <td><?php echo $row['placa']; ?></td>
                        <td><?php echo $row['autorizado_em']; ?></td>                        
                        <td><?php echo $row['autorizado_por']; ?></td>
                        <td><?php echo $row['num_nota']; ?></td>
                        <td><?php echo formataMoeda($row['valor']); ?></td>
                    </tr>
                    
                    <?php
                    $valor_soma = str_replace(",",".",$row['valor']);
                    $valor_total = $valor_total + $valor_soma;
                    ?>
                    
                    <?php } ?>
                </tbody>
            </table>
            
            <?php if(in_array($id_usuario, $ids_acesso)){ ?>
            <table class='table table-bordered table-hover table-striped table-condensed text-sm valign-middle'>
                <thead>
                    <tr class="warning">
                        <td colspan="7" class="text-center">RELATÓRIO INDIVIDUAL</td>
                    </tr>
                    <tr class="bg-primary">
                        <th>Funcionário</th>
                        <th>Valor:</th>                        
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row_f = mysql_fetch_assoc($result_f)) { ?>
                    <tr>
                        <?php if($row_f['nome'] == ""){ ?>
                        <td class="text-danger">SEM NOME</td>
                        <td class="text-danger"><?php echo formataMoeda($row_f['tot_individual']); ?></td>
                        
                        <?php }else{ ?>
                        <td><?php echo acentoMaiusculo($row_f['nome']); ?></td>
                        <td><?php echo formataMoeda($row_f['tot_individual']); ?></td>
                        <?php } ?>                        
                    </tr>
                    
                    <?php } ?>
                </tbody>
            </table>
            <?php } ?>
            
            <div class="alert alert-dismissable alert-info col-lg-6">
                Total de Abastecimentos <?php echo $periodo . ":<strong> " . formataMoeda($valor_total) . "</strong>"; ?>
            </div>
            
            <div class="clear"></div>
            
            <?php } else { ?>
                <div class="alert alert-danger top30">
                    Nenhum registro encontrado
                </div>
            <?php }
            } ?>
            
            <?php if(isset($_REQUEST['filtrar'])){ ?>
            <div class="alert alert-danger top30 <?php echo $alerta; ?>">
                Selecione o período desejado
            </div>
            <?php } ?>
            
            <?php include('../../template/footer.php'); ?>
        </div>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/js/financeiro/abastecimento.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.ui.datepicker-pt-BR.js" type="text/javascript"></script>
        <script src="../../js/jquery.form.js" type="text/javascript"></script>
        <script>
            $('.data').datepicker({
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                yearRange: '2005:c+1'
            });
        </script>
    </body>
</html>