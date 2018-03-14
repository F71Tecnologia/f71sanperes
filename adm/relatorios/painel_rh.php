<?php
session_start();
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include('../../classes/global.php');
include('../../classes/FolhaClass.php');
include('../../classes/RpaAutonomoClass.php');
include('../../classes/RpeEstagiarioClass.php');
include('../../classes/RescisaoClass.php');

$usuario = carregaUsuario();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"2", "area"=>"Administrativo", "id_form"=>"form1", "ativo"=>"Painel do RH");
$breadcrumb_pages = array('Relatórios de Gestão' => '/intranet/adm/relatorios/');
$filtro = false;

if(isset($_REQUEST['gerar'])){
    $filtro = true;
    $objFolha = new Folha();
    $objRpa = new RpaAutonomo();
    $objRpe = new RpeEstagiario();
    $objRescisao = new Rescisao();
    
    $criterio = new stdClass();
    $criterio->id_regiao = $_REQUEST['regiao'];
    $criterio->id_projeto = $_REQUEST['projeto'];
    $criterio->mes = str_pad($_REQUEST['mes'],2,"0",STR_PAD_LEFT);
    $criterio->ano = $_REQUEST['ano'];
    $criterio->status = 3;
    $compMesAno = $criterio->mes.'/'.$criterio->ano;
    
    $dadosFolhas = $objFolha->getListaFolhas($criterio);
    
    $dadosRpa = $objRpa->getTotalRpaCompetencia($_REQUEST['mes'], $_REQUEST['ano'], $_REQUEST['projeto']);
    $dadosRpa = current($dadosRpa);
    
    $dadosRpe = $objRpe->getTotalRpeCompetencia($_REQUEST['mes'], $_REQUEST['ano'], $_REQUEST['projeto']);
    $dadosRpe = current($dadosRpe);
    
    $dadosRescisao = $objRescisao->getTotalizadorRescisaoByFolha($_REQUEST['projeto'],$compMesAno);
    
    $totalRescisaoInss = $dadosRescisao['DEBITO']['previdencia_social']['valor'] + $dadosRescisao['DEBITO']['previdencia_social_dt']['valor'];
    $totalRescisaoIrrf = $dadosRescisao['DEBITO']['ir_ss']['valor'] + $dadosRescisao['DEBITO']['ir_dt']['valor'];
}

$mesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$regiaoR = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : null;

$totalPessoasFolhas = 0;
$totalInssFolha = 0;
$totalIrrfFolha = 0;
$totalValorFolha = 0;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Gestão de Funções</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-note.css.css" rel="stylesheet" type="text/css">
    </head>
    <body>
    <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-header box-admin-header"><h2><span class="fa fa-users"></span> - ADMINISTRAÇÃO <small> - Painel do RH</small></h2></div>
                </div>
            </div>
            <!--resposta de algum metodo realizado-->
            <?php if(!empty($_SESSION['MESSAGE'])){ ?>
                <div id="message-box" class="alert alert-dismissable alert-warning <?=$_SESSION['MESSAGE_COLOR']; ?> alinha2">
                    <?=$_SESSION['MESSAGE']; session_destroy(); ?>
                </div>
            <?php } ?>
            <div class="row">
                <div class="col-xs-12">
                    <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" class="form-horizontal" >
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?=$projetoR; ?>" />
                                <input type="hidden" name="hide_regiao" id="hide_regiao" value="<?=$regiaoR; ?>" />
                                <div class="form-group">
                                    <label class="col-xs-2 control-label">Região: </label>
                                    <div class="col-xs-4">
                                        <?=montaSelect(GlobalClass::carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' class='required[custom[select]] form-control'"); ?>
                                    </div>
                                    <label class="col-xs-1 control-label">Projeto: </label>
                                    <div class="col-xs-5">
                                        <?=montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto' name='projeto' class='required[custom[select]] form-control' ") ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-2 control-label">Competência: </label>
                                    <div class="col-xs-3">
                                        <?=montaSelect(mesesArray(), $mesSel, "id='mes' name='mes' class='required[custom[select]] form-control' ") ?>
                                    </div>
                                    <div class="col-xs-2">
                                        <?=montaSelect(anosArray(), $anoSel, "id='ano' name='ano' class='required[custom[select]] form-control' ") ?>
                                    </div>
                                </div>
                            </div><!-- /.panel-body -->
                            <div class="panel-footer text-right">
                                <button type="submit" class="btn btn-primary" name="gerar" value="gerar"><i class="fa fa-filter"></i> Filtrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php
            if ($filtro) {
                if (count($dadosFolhas) > 0) { 
                    foreach($dadosFolhas as $row){ 
                        
                        $objFolha->setFolha($row['id_folha']);
                        $objFolha->setProjetoFolha($row['id_projeto']);
    
                        $folhaTotalizador = $objFolha->totalizadorBaseFolha('totalizador');
                        
                        $terceiro = ($row['terceiro']==1)?"de decimo terceiro":"";
                        $totalPessoasFolhas += $row['quant_clt'];
                        $totalInssFolha += $folhaTotalizador['total_inss']['valor'];
                        $totalIrrfFolha += $folhaTotalizador['total_irrf']['valor'];
                        $totalValorFolha += $folhaTotalizador['liquido']['valor'];
                        
                        ?>
            
                        <div class="panel panel-info">
                            <div class="panel-heading"><i class="fa fa-money"></i> Dados da Folha <?php echo $terceiro?></div>
                            <div class="panel-body">
                                <form class="form-horizontal" >
                                    
                                    <div class="form-group">
                                        <strong class="col-md-2 text-right">Nome:</strong>
                                        <div class="col-md-2"><?=$row['nome_projeto']; ?></div>

                                        <strong class="col-md-2 text-right">ID Folha:</strong>
                                        <div class="col-md-2"><?=$row['id_folha']; ?></div>

                                        <strong class="col-md-2 text-right">Competencia:</strong>
                                        <div class="col-md-2"><?=$row['mes'].'/'.$row['ano']; ?></div>
                                    </div>

                                    <div class="form-group">
                                        <strong class="col-md-2 text-right">Qtd Clts:</strong>
                                        <div class="col-md-2"><?=$row['quant_clt']; ?></div>

                                        <strong class="col-md-2 text-right">Liquido da folha:</strong>
                                        <div class="col-md-2"><?=formataMoeda($folhaTotalizador['liquido']['valor']); ?></div>

                                        <!--strong class="col-md-2 text-right">Qtd Clts:</strong>
                                        <div class="col-md-2"><?=$row['quant_clt']; ?></div-->
                                    </div>
                                    
                                    <div class="form-group">
                                        <strong class="col-md-2 text-right">INSS Recolhido:</strong>
                                        <div class="col-md-2"><?=formataMoeda($folhaTotalizador['total_inss']['valor']); ?></div>
                                        
                                        <strong class="col-md-2 text-right">IRRF Recolhido:</strong>
                                        <div class="col-md-2"><?=formataMoeda($folhaTotalizador['total_irrf']['valor']); ?></div>
                                        
                                    </div>
                                
                                </form>
                            </div>
                            
                            
                        </div>
                    <?php } ?>
                    <!-- RPA -->
                    <div class="panel panel-success">
                        <div class="panel-heading"><i class="fa fa-money"></i> Dados de Autonomo e Estagiário</div>
                        <div class="panel-body">
                            <form class="form-horizontal" >
                                <div class="form-group">
                                    <strong class="col-md-2 text-right">Qtd RPAs:</strong>
                                    <div class="col-md-2"><?=$dadosRpa['totalQntRpa']; ?></div>
                                    
                                    <strong class="col-md-2 text-right">Valor Total RPAs:</strong>
                                    <div class="col-md-2"><?=formataMoeda($dadosRpa['totalValorLiquido']); ?></div>
                                    
                                </div>
                                
                                <div class="form-group">
                                    <strong class="col-md-2 text-right">Inss RPAs:</strong>
                                    <div class="col-md-2"><?=formataMoeda($dadosRpa['totalValorInss']); ?></div>
                                    
                                    <strong class="col-md-2 text-right">IRRF RPAs:</strong>
                                    <div class="col-md-2"><?=formataMoeda($dadosRpa['totalValorIr']); ?></div>
                                    
                                </div>
                                
                                <hr/>
                                
                                <div class="form-group">
                                    <strong class="col-md-2 text-right">Qtd RPEs:</strong>
                                    <div class="col-md-2"><?=$dadosRpe['totalQntRpa']; ?></div>
                                    
                                    <strong class="col-md-2 text-right">Valor Total RPEs:</strong>
                                    <div class="col-md-2"><?=formataMoeda($dadosRpe['totalValorLiquido']); ?></div>
                                    
                                </div>
                                
                                <div class="form-group">
                                    <strong class="col-md-2 text-right">Inss RPEs:</strong>
                                    <div class="col-md-2"><?=formataMoeda($dadosRpe['totalValorInss']); ?></div>
                                    
                                    <strong class="col-md-2 text-right">IRRF RPEs:</strong>
                                    <div class="col-md-2"><?=formataMoeda($dadosRpe['totalValorIr']); ?></div>
                                    
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- FÉRIAS -->
                    <div class="panel panel-warning">
                        <div class="panel-heading"><i class="fa fa-plane"></i> Dados de Férias</div>
                        <div class="panel-body">
                            <form class="form-horizontal" >
                                <div class="form-group">
                                    <strong class="col-md-2 text-right">Qtd de Férias:</strong>
                                    <div class="col-md-2"><?=0; ?></div>
                                    
                                    <strong class="col-md-2 text-right">Valor Total:</strong>
                                    <div class="col-md-2"><?=formataMoeda(0); ?></div>
                                </div>
                                
                                <div class="form-group">
                                    <strong class="col-md-2 text-right">INSS Férias:</strong>
                                    <div class="col-md-2"><?=0; ?></div>
                                    
                                    <strong class="col-md-2 text-right">IRRF Férias:</strong>
                                    <div class="col-md-2"><?=formataMoeda(0); ?></div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- RESCISÕES -->
                    <div class="panel panel-danger">
                        <div class="panel-heading"><i class="fa fa-trash"></i> Dados de Rescisões</div>
                        <div class="panel-body">
                            <form class="form-horizontal" >
                                <div class="form-group">
                                    <strong class="col-md-2 text-right">Qtd de Rescisões:</strong>
                                    <div class="col-md-2"><?=$dadosRescisao['RESUMO']['qnt']; ?></div>
                                    
                                    <strong class="col-md-2 text-right">Valor Total:</strong>
                                    <div class="col-md-2"><?=formataMoeda($dadosRescisao['RESUMO']['valor']); ?></div>
                                </div>
                                
                                <div class="form-group">
                                    <strong class="col-md-2 text-right">IR Saldo de Salário:</strong>
                                    <div class="col-md-2"><?=formataMoeda($dadosRescisao['DEBITO']['ir_ss']['valor']); ?></div>
                                    
                                    <strong class="col-md-2 text-right">IR 13º Salario:</strong>
                                    <div class="col-md-2"><?=formataMoeda($dadosRescisao['DEBITO']['ir_dt']['valor']); ?></div>
                                    
                                    <strong class="col-md-2 text-right">Total IRRF:</strong>
                                    <div class="col-md-2"><?=formataMoeda($totalRescisaoIrrf); ?></div>
                                </div>
                                
                                <div class="form-group">
                                    <strong class="col-md-2 text-right">INSS Saldo de Salário:</strong>
                                    <div class="col-md-2"><?=formataMoeda($dadosRescisao['DEBITO']['previdencia_social']['valor']); ?></div>
                                    
                                    <strong class="col-md-2 text-right">INSS 13º Salario:</strong>
                                    <div class="col-md-2"><?=formataMoeda($dadosRescisao['DEBITO']['previdencia_social_dt']['valor']); ?></div>
                                    
                                    <strong class="col-md-2 text-right">Total de INSS:</strong>
                                    <div class="col-md-2"><?=formataMoeda($totalRescisaoInss); ?></div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- TOTALIZADORES -->
                    <div class="panel panel-default">
                        <div class="panel-heading"><i class="fa fa-check"></i> Totalizador</div>
                        <div class="panel-body">
                            <form class="form-horizontal" >
                                <div class="form-group">
                                    <strong class="col-md-6 text-right">TOTAL DE PESSOAS ENVOLVIDAS (FOLHA + RPA + RPE) :</strong>
                                    <div class="col-md-6"><?=$totalPessoasFolhas+$dadosRpa['totalQntRpa']+$dadosRpe['totalQntRpa']; ?></div>
                                </div>
                                
                                <div class="form-group">
                                    <strong class="col-md-6 text-right">TOTAL DE INSS RECOLHIDO:</strong>
                                    <div class="col-md-6"><?= formataMoeda($totalInssFolha + $totalRescisaoInss + $dadosRpa['totalValorInss'] + $dadosRpe['totalValorInss']); ?></div>
                                </div>
                                
                                <div class="form-group">
                                    <strong class="col-md-6 text-right">TOTAL DE IRRF RECOLHIDO:</strong>
                                    <div class="col-md-6"><?= formataMoeda($totalIrrfFolha + $totalRescisaoIrrf + $dadosRpa['totalValorIr'] + $dadosRpe['totalValorIr']); ?></div>
                                </div>
                                
                                <div class="form-group">
                                    <strong class="col-md-6 text-right">CUSTO TOTAL LIQUIDO:</strong>
                                    <div class="col-md-6"><?= formataMoeda($totalValorFolha + $dadosRpa['totalValorLiquido'] + $dadosRpe['totalValorLiquido'] + $dadosRescisao['RESUMO']['valor'])?></div>
                                </div>
                                
                            </form>
                        </div>
                    </div>
                    
                <?php } else { ?>
                    <div class="alert alert-dismissable alert-warning">
                        <p>Nenhum registro encontrado</p>
                    </div>
                <?php }
            } ?>

            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(function() {
                $("#regiao").ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, null, "projeto");
            });
        </script>
    </body>
</html>