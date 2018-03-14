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
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$saida = new Saida();
$global = new GlobalClass();

$meses          = mesesArray(null);
$ano            = anosArray(null, null, array('' => "<< Ano >>"));
$mesSelectI     = (isset($_REQUEST['mesI'])) ? $_REQUEST['mesI'] : null;
$mesSelectF     = (isset($_REQUEST['mesF'])) ? $_REQUEST['mesF'] : null;
$anoSelectI     = (isset($_REQUEST['anoI'])) ? $_REQUEST['anoI'] : date('Y');
$anoSelectF     = (isset($_REQUEST['anoF'])) ? $_REQUEST['anoF'] : date('Y');
$inicio         = $_REQUEST['anoI'] ."-". sprintf("%02d",$_REQUEST['mesI']) ."-". "01";
$final          = $_REQUEST['anoF'] ."-". sprintf("%02d",$_REQUEST['mesF']) ."-". "31"; 



/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
if(isset($_REQUEST['projeto']) && isset($_REQUEST['regiao'])){
    $regiaoR = $_REQUEST['regiao'];
    $projetoR = $_REQUEST['projeto'];
}

if (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])){
    $filtro = true;
    if ($projetoR == -1) {
        
        $sqlProjAll = "SELECT id_projeto FROM projeto WHERE id_regiao = {$regiaoR}";
        $queryProjAll = mysql_query($sqlProjAll);
        
        while($arrayProjAll = mysql_fetch_assoc($queryProjAll)) {
            $ArrProjAll[] = $arrayProjAll['id_projeto'];
        }
        //print_r($arrayProjAll);
//        print_r(implode(',',$ArrProjAll));
        
        $sql = "SELECT A.id_folha, A.mes, A.ano, A.terceiro, A.rendi_final, A.total_liqui, B.nome AS nome_projeto, C.regiao AS nome_regiao
            FROM rh_folha AS A
            LEFT JOIN projeto AS B ON (A.projeto = B.id_projeto)
            LEFT JOIN regioes AS C ON (A.regiao = C.id_regiao)
            WHERE A.ano BETWEEN {$anoSelectI } AND {$anoSelectF} AND A.mes BETWEEN {$mesSelectI} AND {$mesSelectF} AND A.regiao = {$regiaoR} AND A.projeto IN (".implode(',',$ArrProjAll).")
            ORDER BY A.ano, A.mes ASC";
        $query = mysql_query($sql);
        $num_rendimento = mysql_num_rows($query);
        
    } else {
        
        $sql = "SELECT A.id_folha, A.mes, A.ano, A.terceiro, A.rendi_final, A.total_liqui, B.nome AS nome_projeto, C.regiao AS nome_regiao
            FROM rh_folha AS A
            LEFT JOIN projeto AS B ON (A.projeto = B.id_projeto)
            LEFT JOIN regioes AS C ON (A.regiao = C.id_regiao)
            WHERE A.ano BETWEEN {$anoSelectI } AND {$anoSelectF} AND A.mes BETWEEN {$mesSelectI} AND {$mesSelectF} AND A.regiao = {$regiaoR} AND A.projeto IN ({$projetoR})
            ORDER BY A.ano, A.mes ASC";
        $query = mysql_query($sql);
        $num_rendimento = mysql_num_rows($query);
        
    }
    
}

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Relatório de Rendimento Bruto");
$breadcrumb_pages = array("Principal" => "../index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Relatório de Rendimento Bruto/Liquido</title>
        
        <link href="../../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        
        <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
            <div class="container">
                <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - Relatório de Rendimento Bruto/Liquido</small></h2></div>

                <div class="panel panel-default">
                    <div class="panel-heading">Relatório de Rendimento Bruto/Liquido</div>
                    <div class="panel-body">
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoR ?>" />
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Região</label>
                            <div class="col-lg-4">                                                        
                                <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' class='validate[required,custom[select]] form-control'");  ?>
                            </div>
                            <label for="select" class="col-lg-1 control-label">Projeto</label>
                            <div class="col-lg-4">
                                <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto' name='projeto' class='form-control'"); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Início</label>
                            <div class="col-sm-2">
                                <?php echo montaSelect($meses, $mesSelectI, "id='mesI' name='mesI' class='validate[required] form-control'") ?><span class="loader"></span>
                            </div>
                            <div class="col-sm-2">
                                <?php echo montaSelect($ano, $anoSelectI, "id='anoI' name='anoI' class='validate[required] form-control'") ?><span class="loader"></span>
                            </div>
                            <label for="select" class="col-lg-1 control-label">Fim</label>
                            <div class="col-sm-2">
                              <?php echo montaSelect($meses, $mesSelectF, "id='mesF' name='mesF' class='validate[required] form-control'") ?> <span class="loader"></span> 
                            </div>
                            <div class="col-sm-2">
                              <?php echo montaSelect($ano, $anoSelectF, "id='anoF' name='anoF' class='validate[required] form-control'") ?> <span class="loader"></span> 
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="submit" name="filtrar" id="filt" value="Gerar" class="btn btn-primary" />
                    </div>
                </div>

                <?php
                if ($filtro) {
                    if ($num_rendimento > 0) {
                ?>
                <div style="border-top: none" class=" panel-footer text-right hidden-print controls">
                    <button type="button" onclick="tableToExcel('tbRelatorio', 'Exporta??o T?cnica')" value="Exportar para Excel" class="btn btn-success" ><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                </div>
                <table id="tbRelatorio" class='table table-bordered table-hover table-striped table-condensed text-sm valign-middle'>
                    <thead>
                        <tr class="bg-primary">
                            <th class="text-center">Região</th>
                            <th class="text-center">Projeto</th>
                            <th class="text-center">Folha</th>
                            <th class="text-center">Mês</th>
                            <th class="text-center">Ano</th>
                            <th class="text-center">Rendimento Final Bruto</th>
                            <th class="text-center">Rendimento Final Liquido</th>
                        </tr>
                    </thead>
                    <tbody>                    
                        <?php while ($row_rendimento = mysql_fetch_assoc($query)) { 
                            if($row_rendimento['terceiro'] == 1){
                                $terceiro = " (Parcela 13º)";
                            } else {
                                $terceiro = "";
                            }
                            
                            switch($row_rendimento['mes']) {
                                case '1': $mesRend = "Janeiro"; break;
                                case '2': $mesRend = "Fevereiro"; break;
                                case '3': $mesRend = "Março"; break;
                                case '4': $mesRend = "Abril"; break;
                                case '5': $mesRend = "Maio"; break;
                                case '6': $mesRend = "Junho"; break;
                                case '7': $mesRend = "Julho"; break;
                                case '8': $mesRend = "Agosto"; break;
                                case '9': $mesRend = "Setembro"; break;
                                case '10': $mesRend = "Outubro"; break;
                                case '11': $mesRend = "Novembro"; break;
                                case '12': $mesRend = "Dezembro"; break;
                                
                            }
                        ?>
                        <tr>
                            <td class="text-left"><?php echo $row_rendimento['nome_regiao']; ?></td>
                            <td class="text-left"><?php echo $row_rendimento['nome_projeto']; ?></td>
                            <td class="text-center"><?php echo $row_rendimento['id_folha']; ?></td>
                            <td class="text-left"><?php echo $mesRend.$terceiro; ?></td>
                            <td class="text-center"><?php echo $row_rendimento['ano']; ?></td>
                            <td class="text-left"><?php echo formataMoeda($row_rendimento['rendi_final']) ?></td>
                            <td class="text-left"><?php echo formataMoeda($row_rendimento['total_liqui']) ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <?php } else { ?>
                    <div class="alert alert-danger top30">                    
                        Nenhum registro encontrado
                    </div>
                <?php }
                } ?>

                <?php include('../../template/footer.php'); ?>
            </div>        
        </form>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>       
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(function() {
                $("#regiao").ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, null, "projeto");
                
                $("#form1").validationEngine({promptPosition : "topRight"});                
            });
        </script>
    </body>
</html>