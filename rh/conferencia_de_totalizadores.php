<?php
 
error_reporting(E_NOTICE);

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include '../classes/FolhaClass.php';
include '../classes/RescisaoClass.php';
include '../classes/FeriasClass.php';
include("../wfunction.php");
include("../classes/global.php");

$usuario = carregaUsuario(); 
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$global = new GlobalClass();

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>"Relatório de Rateio de Folha Detalhado");
$breadcrumb_pages = array("Gestão de RH"=>"/intranet/rh/principalrh.php");

$ano = date('Y');
$filtro = $_REQUEST['filtrar'];

if(isset($filtro)){	
    $projeto = $_REQUEST['projeto'];
    $unidade = $_REQUEST['unidade'];
    $mes     = sprintf("%02d", $_REQUEST['mes']);
    $ano     = $_REQUEST['ano'];
        
    /**
     * VERIFICANDO FOLHAS 
     * INSTITUCIONAL SEMPRE VAI TER
     */
    $queryFolhaInstitucional = mysql_query("SELECT A.id_folha
                                    FROM rh_folha AS A
                                    WHERE A.mes = '{$mes}' AND A.ano = '{$ano}' AND A.regiao = 1");
    $itens_folha_institucional = mysql_fetch_assoc($queryFolhaInstitucional);
    $folha_institucuional  = $itens_folha_institucional['id_folha'];
        
    /**
     * VERIFICANDO FOLHAS 
     * PROJETO SELECIONADO NO SELECT
     */
    $queryFolhaBoxProjeto = mysql_query("SELECT A.id_folha
                                    FROM rh_folha AS A
                                    WHERE A.mes = '{$mes}' AND A.ano = '{$ano}' AND A.regiao = '{$projeto}'");
    $itens_folha_BoxProjeto = mysql_fetch_assoc($queryFolhaBoxProjeto);
    $folha_BoxProjeto  = $itens_folha_BoxProjeto['id_folha'];
    
    /**
     * CRITERIA DO PROJETO
     */    
    $projeto_sql = ($projeto > '0') ? "AND folha.id_projeto = {$projeto}" : null;
    
    
    /**
     * QUERY PRINCIPAL
     */
    $sql_descontados = "SELECT *, 
                            CAST(((inss +  inss_ferias + inss_13_rescisao) * porcentagem) AS DECIMAL(13,4)) AS inss_rateio,
                            CAST(((imprenda) * porcentagem) AS DECIMAL(13,4)) AS irrf_rateio
                            FROM (
                            SELECT A.id_clt, A.id_projeto, E.nome as nome_projeto, A.nome, A.mes, A.ano,
                                   A.inss, A.base_inss, A.imprenda, A.base_irrf, A.ir_rescisao, A.ir_dt, A.ir_ferias_novo_campo, F.id_unidade, G.unidade as nome_unidade,
                                        IF((B.porcentagem = 0 || B.id_unidade IS NULL) AND A.id_projeto != 1,1,(B.porcentagem/100)) AS porcentagem,
                                        IF(C.inss IS NULL,0,C.inss) AS inss_ferias, 
                                        IF(D.inss_dt IS NULL,0,D.inss_dt) AS inss_13_rescisao
                            FROM rh_folha_proc AS A
                                    LEFT JOIN rh_clt_unidades_assoc AS B ON(A.id_clt = B.id_clt AND B.id_unidade = '{$unidade}')
                                    LEFT JOIN rh_ferias AS C ON(A.id_clt = C.id_clt AND A.mes = C.mes AND A.ano = C.ano AND C.`status` = 1)
                                    LEFT JOIN rh_recisao AS D ON(A.id_clt = D.id_clt AND A.mes = MONTH(data_demi) AND A.ano = YEAR(data_demi) AND D.`status` = 1)
                                    LEFT JOIN projeto AS E ON(A.id_projeto = E.id_projeto)
                                    LEFT JOIN rh_clt AS F ON(A.id_clt = F.id_clt)
                                    LEFT JOIN unidade AS G ON(F.id_unidade = G.id_unidade)
                            WHERE A.id_folha IN ({$folha_BoxProjeto},{$folha_institucuional}) 
                            ORDER BY F.id_unidade, A.nome
                            ) AS tmp";
    $qr_descontados = mysql_query($sql_descontados);
    $total_descontados = mysql_num_rows($qr_descontados);
    
    
    $qr_totalizador = "SELECT  
             id_unidade, nome_unidade, 
             SUM(inss_rateio) as total_inss, 
             SUM(irrf_rateio) as total_irrf 
        FROM ( {$sql_descontados} ) as total GROUP BY id_unidade";
    $sql_totalizador = mysql_query($qr_totalizador) or die('Erro ao selecionar totalizadores');
    
    $matrizSubTotal = array();
    $matrizTotalGeral = array();
    while($row_totalizador = mysql_fetch_assoc($sql_totalizador)){        
        /**
         * TOTALIZADORES DE INSS
         */
        $matrizSubTotal[$row_totalizador['id_unidade']]['inss'] += $row_totalizador['total_inss'];
        $matrizTotalGeral['inss'] += $row_totalizador['total_inss'];
        
        /**
         * TOTALIZADORES DE IRRF
         */
        $matrizSubTotal[$row_totalizador['id_unidade']]['irrf'] += $row_totalizador['total_irrf'];
        $matrizTotalGeral['irrf'] += $row_totalizador['total_irrf'];
    }
      
}
?>
<!doctype html>
<html>
<head>
	<meta charset="iso-8859-1">
	<title>Relatório de Rateio de Folha Detalhado </title>
	<!-- Bootstrap -->
    <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
    <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
    <link href="../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
    <link href="../resources/css/main.css" rel="stylesheet" media="all">
    <link href="../resources/css/font-awesome.css" rel="stylesheet" media="all">
    <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
    <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
    <link href="../css/progress.css" rel="stylesheet" type="text/css">
    <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
    <script src="../js/jquery-1.10.2.min.js"></script>
    <script>
        $(function(){
            $("select[name='projeto']").change(function(){
                var projeto = $(this).val();
                if(projeto == 2){
                    $("select[name='unidade']").val(69); 
                } 
                if(projeto == 3){
                    $("select[name='unidade']").val(70); 
                } 
            })
        });
    </script>
    <style>
        .esconde100{ display:none !important; }
        .esconde43{ display:none !important; }
        .esconde57{ display:none !important; }
    </style>
</head>
<body>
	<?php include("../template/navbar_default.php"); ?>
        
        <div class="<?=($container_full) ? 'container-full' : 'container'?>">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Relatório de Rateio de Folha Detalhado</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
                
                <?php if(isset($_SESSION['regiao'])){ ?>                
                <!--resposta de algum metodo realizado-->
                <div class="alert alert-<?php echo $_SESSION['MESSAGE_TYPE']; ?> msg_cadsuporte">
                    <button type="button" class="close" data-dismiss="alert">Ã—</button>
                    <p><?php echo $_SESSION['MESSAGE'];
                    session_destroy(); ?></p>
                </div>
                <?php } ?>
                
                <div class="panel panel-default hidden-print">
                    <div class="panel-heading">Relatório Detalhado</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Projeto:</label>
                            <div class="col-lg-8">
                                <select name="projeto" class="required[custom[select]] form-control">
                                    <option value="2">2 - CONTRATO DE GESTÃO NORTE</option>
                                    <option value="3">3 - CONTRATO DE GESTÃO CENTRO</option>
                                </select>
                                <?php //echo montaSelect($global->carregaProjetosByRegiao($usuario['id_regiao']), $projeto, "id='projeto' name='projeto' class='required[custom[select]] form-control'"); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Unidade de Rateio:</label>
                            <div class="col-lg-8">
                                <select name="unidade" class="required[custom[select]] form-control">
                                    <option value="69">COORDENAÇÃO TECNICO ADMINISTRATIVA OS  CG021/2016  REDE ASSISTENCIAL STS - ADM</option>
                                    <option value="70">COORDENAÇÃO TECNICA ADM OS-CG-023/2016- REDE ASSISTENCIAL STS SE-CENTRO</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Mês:</label>
                            <div class="col-lg-8">
                                <div class="input-daterange input-group" id="bs-datepicker-range">
                                    <?php echo montaSelect(mesesArray(), $mes, "id='mes' name='mes' class='required[custom[select]] form-control'"); ?>
                                    <span class="input-group-addon">Ano</span>
                                    <?php echo montaSelect(AnosArray(null,null), $ano, "id='ano' name='ano' class='required[custom[select]] form-control'"); ?>                                
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="box" class="col-lg-2 control-label"></label>
                            <div class="col-lg-2">
                                <input type="radio" name="filtroTipo" id="filtroTipo" value="1" checked="checked" /> Participantes
                            </div>
                            <div class="col-lg-2">
                                <input type="radio" name="filtroTipo" id="filtroTipo" value="2" /> Unidade
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="submit" name="filtrar" id="filt" value="Filtrar" class="btn btn-primary" />
                    </div>
                </div>
            
            <?php
            if($filtro) {
                if($total_descontados > 0) {
            ?>
                
            <p class="pull-right">
                <button type="button" value="exportar para excel" onclick="tableToExcel('table_excel', 'Inss')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
            </p>
            
            <table class="table table-bordered table-hover table-condensed text-sm valign-middle" id="table_excel">
                <thead>                    
                    <tr class="bg-primary">
                        <th>ID</th>
                        <th>NOME</th>
                        <th>UNIDADE</th>
                        <th>PROJETO</th>
                        <th>PORCENTAGEM</th>
                        <th>INSS</th> 
                        <th>IRRF</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php $unidade = ""; ?>
                    <?php while($row_descontado = mysql_fetch_assoc($qr_descontados)) { ?>
                        
                        <?php if($unidade['id'] != $row_descontado['id_unidade']){ ?>
                            <?php if(!empty($unidade['id'])){ ?>
                                <tr class="danger">
                                    <td colspan="5" style="text-align: right; "><?php echo $unidade['nome']; ?></td>
                                    <td colspan="1" style="text-align: right; "><?php echo number_format($matrizSubTotal[$unidade['id']]['inss'], 2, ',', '.');  ?></td>
                                    <td colspan="1" style="text-align: right; "><?php echo number_format($matrizSubTotal[$unidade['id']]['irrf'], 2, ',', '.');  ?></td>
                                </tr>
                            <?php } ?>
                            <?php 
                            $unidade['id'] = $row_descontado['id_unidade'];  
                            $unidade['nome'] = $row_descontado['nome_unidade'];  
                            ?> 
                        <?php } ?>
                    
                        <tr class="linhasParticipantes">
                            <td><?php echo $row_descontado['id_clt']; ?></td>
                            <td><?php echo $row_descontado['nome']; ?></td>
                            <td><?php echo $row_descontado['nome_unidade']; ?></td>
                            <td><?php echo $row_descontado['nome_projeto']; ?></td>
                            <td><?php echo ($row_descontado['porcentagem'] * 100) . " %" ; ?></td>
                            <td style="text-align: right; "><?php echo number_format($row_descontado['inss_rateio'], 2, ',', '.');  ?></td>
                            <td style="text-align: right; "><?php echo number_format($row_descontado['irrf_rateio'], 2, ',', '.');  ?></td>
                        </tr>
                    <?php } ?>
                    <tr class="danger">
                        <td colspan="5" style="text-align: right; "><?php echo $unidade['nome']; ?></td>
                        <td colspan="1" style="text-align: right; "><?php echo number_format($matrizSubTotal[$unidade['id']]['inss'], 2, ',', '.');  ?></td>
                        <td colspan="1" style="text-align: right; "><?php echo number_format($matrizSubTotal[$unidade['id']]['irrf'], 2, ',', '.');  ?></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class='danger'>
                        <td class='text-right' colspan="5">TOTAL GERAL:</td>
                        <td colspan="1" style="text-align: right; "><?php echo number_format($matrizTotalGeral['inss'], 2, ',', '.'); ?></td>
                        <td colspan="1" style="text-align: right; "><?php echo number_format($matrizTotalGeral['irrf'], 2, ',', '.'); ?></td>
                    </tr>
                </tfoot>
                
            </table>
            
            <?php } else { ?>
                <div class="alert alert-danger top30">                    
                    Nenhum registro encontrado
                </div>
            <?php }
            } ?>
        
        </form>
            
            <?php include('../template/footer.php'); ?>
        </div>
    
    <script src="../js/jquery-1.10.2.min.js"></script>
    <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
    <script src="../resources/js/bootstrap.min.js"></script>
    <script src="../resources/js/tooltip.js"></script>
    <script src="../resources/js/main.js"></script>
    <script src="../js/global.js"></script>
    <script>
        $(function(){
           $("body").on("click","input[name='filtroTipo']",function(){
                var valor = $(this).val();
                if(valor == 2){
                    $(".linhasParticipantes").hide();
                }else{
                    $(".linhasParticipantes").show();
                }
           });
        });
    </script>
    </body>
</html>
