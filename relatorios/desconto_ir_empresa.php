<?php
if(empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../wfunction.php');
include("../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$global = new GlobalClass();

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>"Relatório de Desconto de IRRF");
$breadcrumb_pages = array("Gestão de RH"=>"/intranet/rh/principalrh.php");

$ano = 2016;
$filtro = $_REQUEST['filtrar'];

if(isset($filtro)){
	
    $projeto = $_REQUEST['projeto'];
    $mes = $_REQUEST['mes'];
    $ano = $_REQUEST['ano'];
        
    $projeto_sql = ($projeto > '0') ? "AND folha.id_projeto = {$projeto}" : null;
    
    /*
     * CRIEI ESSAS VARIAVEIS,
     * POIS VAI TER QUE IMPLEMENTAR
     * ESSE RELATÓRIO PARA 13º
     */    
    $_and = "AND fp.terceiro = 2";
    
    $sql_descontados = "
    	SELECT folha.nome AS nome,unidade.id_unidade ,unidade.unidade AS unidade, funcao.nome AS nome_curso, folha.base_inss , folha.inss AS inss, P.nome AS nome_projeto, EMP.aliquotaRat, EMP.aliquota_rat, 
                CAST((folha.base_inss*0.20) AS DECIMAL(15,4)) as inss_em,
                CAST((folha.base_inss*EMP.aliquota_rat) AS DECIMAL(15,4)) as inss_rat,
                CAST((folha.base_inss*0.058) AS DECIMAL(15,4)) as inss_ter,(folha.base_inss * 0.08) as fgts, folha.base_irrf, folha.imprenda, folha.d_imprenda, folha.ir_ferias, folha.ir_rescisao
                
		FROM rh_folha_proc AS folha
		INNER JOIN rh_clt AS clt
		ON clt.id_clt = folha.id_clt
                INNER JOIN rh_folha AS fp 
                ON folha.id_folha = fp.id_folha
		INNER JOIN curso AS funcao
		ON clt.id_curso = funcao.id_curso
		INNER JOIN unidade AS unidade
		ON unidade.id_unidade = clt.id_unidade
                INNER JOIN projeto AS P
                ON P.id_projeto = folha.id_projeto
                INNER JOIN rhempresa AS EMP
                ON EMP.id_regiao = clt.id_regiao
		WHERE folha.mes = {$mes}
		AND folha.ano = {$ano}
                AND folha.status = 3 
		{$projeto_sql}
                {$_and}
                ORDER BY unidade,nome";
    $qr_descontados = mysql_query($sql_descontados);
    $total_descontados = mysql_num_rows($qr_descontados);
    
    $qr_totalizador = mysql_query("
        SELECT SUM(ir_ferias) as total_ir_ferias, SUM(ir_rescisao) as total_ir_rescisao, SUM(base_irrf) as total_base_irrf, SUM(imprenda) as total_imprenda, SUM(d_imprenda) AS total_d_imprenda, SUM(inss_rat) as total_inss_rat,SUM(inss_ter) as total_inss_ter,unidade,id_unidade FROM (
                {$sql_descontados}
        )AS temp 
        GROUP BY id_unidade
    ");
    
    $matrizTotal = array();
    $matrizTotalGeral = array();
    while($row_totalizador = mysql_fetch_assoc($qr_totalizador)){
        $matrizTotal[$row_totalizador['id_unidade']] = $row_totalizador;
        $matrizTotalGeral['base_irrf'] += $row_totalizador['total_base_irrf'];
        $matrizTotalGeral['imprenda'] += $row_totalizador['total_imprenda'];
        $matrizTotalGeral['ir_ferias'] += $row_totalizador['total_ir_ferias'];
        $matrizTotalGeral['ir_rescisao'] += $row_totalizador['total_ir_rescisao'];
        $matrizTotalGeral['d_imprenda'] += $row_totalizador['total_d_imprenda'];
    }
    
    /*if($_COOKIE['logado']==158){
        echo "<pre>";
        print_r($matrizTotal);
        echo "</pre>";
        exit;
    }*/
    
    $unidade = "";
    $unidade_nome = "";
    $toInss = 0;
    $toInss_emp = 0;
    $toInss_rat = 0;
}
?>
<!doctype html>
<html>
<head>
	<meta charset="iso-8859-1">
	<title>Relatório de Desconto de IRRF</title>
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
    <script src="../resources/js/bootstrap.min.js"></script>
    <script src="../resources/js/bootstrap-dialog.min.js"></script>
    <script src="../js/jquery.validationEngine-2.6.js"></script>
    <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
    <script src="../resources/js/print.js" type="text/javascript"></script>
    <script type="text/javascript" src="/intranet/js/bootstrap-datepicker-1.4.0-dist/js/bootstrap-datepicker.js?8181"></script>
    <script type="text/javascript" src="/intranet/js/bootstrap-datepicker-1.4.0-dist/js/bootstrap-datepicker.min.js?8181"></script>
    <script type="text/javascript" src="/intranet/js/bootstrap-datepicker-1.4.0-dist/locales/bootstrap-datepicker.pt-BR.min.js?8181"></script>
</head>
<body>
	<?php include("../template/navbar_default.php"); ?>
        
        <div class="<?=($container_full) ? 'container-full' : 'container'?>">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Relatório de Desconto de IRRF</small></h2></div>
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
                                <?php echo montaSelect($global->carregaProjetosByRegiao($usuario['id_regiao']), $projeto, "id='projeto' name='projeto' class='required[custom[select]] form-control'"); ?>
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
                                <div class="input-group">
                                    <label class="input-group-addon pointer" for="filtroTipo">
                                        <input type="radio" name="filtroTipo" id="filtroTipo" value="1" checked="checked" /> 
                                    </label>
                                    <label class="form-control pointer" for="filtroTipo">Participantes</label>
                                </div>
                            </div>
                  
                            <div class="col-lg-2">
                                <div class="input-group">
                                    <label class="input-group-addon pointer" for="filtroTipo">
                                        <input type="radio" name="filtroTipo" id="filtroTipo" value="2" /> 
                                    </label>
                                    <label class="form-control pointer" for="filtroTipo">Unidade</label>
                                </div>
                            </div>
                        </div>
                    </div>
                        
                        <div class="panel-footer text-right">
                            <?php if (!empty($total_descontados) && (isset($_POST['filtrar']))) { ?>
                            <button type="button" value="exportar para excel" onclick="tableToExcel('table_excel', 'Inss')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            <button type="button" form="formPdf" name="pdf" data-title="Relatório de Desconto de IRRF" data-id="table_excel" id="pdf" value="Gerar PDF" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Gerar PDF</button>
                            <?php } ?>
                            <button type="submit" name="filtrar" id="filt" value="Filtrar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                        </div>
                    </div>
            
            
            <?php
            if($filtro) {
                if($total_descontados > 0) {
            ?>
            
            <table class="table table-bordered table-hover table-condensed text-sm valign-middle" id="table_excel">
                <thead>                    
                    <tr>
                        <th>Nome</th>
                        <th>Função</th>
                        <th>Unidade</th>
                        <th>Base IRRF</th>
                        <th>IRRF</th>
                        <td>IRRF FERIAS</td>
                        <td>IRRF RESCISAO</td>
                        <!--<th>DDIR</th>-->                        
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while($row_descontado = mysql_fetch_assoc($qr_descontados)) { 
                    if($unidade != $row_descontado['id_unidade']){
                        //CABEÇALHO, PRINTADO LINHA TOTALIZADOR
                        if(!empty($unidade)){
                            echo "<tr class='warning'><td class='text-right' colspan='3'> {$unidade_nome}: </td> 
                                <td>".number_format($matrizTotal[$unidade]['total_base_irrf'], 2, ',', '.')."</td> 
                                <td>".number_format($matrizTotal[$unidade]['total_imprenda'], 2, ',', '.')."</td> 
                                <td>".number_format($matrizTotal[$unidade]['total_ir_ferias'], 2, ',', '.')."</td> 
                                <td>".number_format($matrizTotal[$unidade]['total_ir_rescisao'], 2, ',', '.')."</td> 
                                ";
                        }
                        //<td>".number_format($matrizTotal[$unidade]['total_d_imprenda'], 2, ',', '.')."</td>
                        
                        //CABEÇALHO, MUDANÇA DE UNIDADE
                        //echo "<tr class='active'><td colspan='6' class='text-center'> <i class='fa fa-home'></i>  {$row_descontado['unidade']}</td></tr>";
                        $unidade = $row_descontado['id_unidade'];
                        $unidade_nome = $row_descontado['unidade'];
                    }

                    ?>
                    <tr class="linhasParticipantes">
                        <td><?php echo $row_descontado['nome']; ?></td>
                        <td><?php echo $row_descontado['nome_curso']; ?></td>
                        <td><?php echo $unidade_nome; ?></td>
                        <td><?php echo number_format($row_descontado['base_irrf'], 2, ',', '.');  ?></td>
                        <td><?php echo number_format($row_descontado['imprenda'], 2, ',', '.');  ?></td>
                        <td><?php echo number_format($row_descontado['ir_ferias'], 2, ',', '.');  ?></td>
                        <td><?php echo number_format($row_descontado['ir_rescisao'], 2, ',', '.');  ?></td>
                        <!--<td><?php //echo number_format($row_descontado['d_imprenda'], 2, ',', '.');  ?></td>-->
                    </tr>
                    <?php }
                    
                    echo "<tr class='warning'><td class='text-right' colspan='3'> {$unidade_nome}: </td> 
                                <td>".number_format($matrizTotal[$unidade]['total_base_irrf'], 2, ',', '.')."</td>
                                <td>".number_format($matrizTotal[$unidade]['total_imprenda'], 2, ',', '.')."</td>
                                <td>".number_format($matrizTotal[$unidade]['total_ir_ferias'], 2, ',', '.')."</td> 
                                <td>".number_format($matrizTotal[$unidade]['total_ir_rescisao'], 2, ',', '.')."</td> 
                                ";
                    //<td>".number_format($matrizTotal[$unidade]['total_d_imprenda'], 2, ',', '.')."</td>
                    ?>
                    
                    
                </tbody>
                <tfoot>
                    <tr class='danger'>
                        <td class='text-right' colspan='3'>Total Geral:</td>
                        <td><?php echo number_format($matrizTotalGeral['base_irrf'], 2, ',', '.');  ?></td>
                        <td><?php echo number_format($matrizTotalGeral['imprenda'], 2, ',', '.');  ?></td>
                        <td><?php echo number_format($matrizTotalGeral['ir_ferias'], 2, ',', '.');  ?></td>
                        <td><?php echo number_format($matrizTotalGeral['ir_rescisao'], 2, ',', '.');  ?></td>
                        <!--<td><?php //echo number_format($matrizTotalGeral['d_imprenda'], 2, ',', '.');  ?></td>-->
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
