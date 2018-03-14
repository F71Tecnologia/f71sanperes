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

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>"Relatório de Desconto de FOLHA, FERIAS E RESCISÃO");
$breadcrumb_pages = array("Gestão de RH"=>"/intranet/rh/principalrh.php");

$ano = date('Y');
$filtro = $_REQUEST['filtrar'];

if(isset($filtro)){
	
    $projeto = $_REQUEST['projeto'];
    $mes = $_REQUEST['mes'];
    $ano = $_REQUEST['ano'];
        
    $projeto_sql = ($projeto > '0') ? "AND folha.id_projeto = {$projeto}" : null;
    
    /**
     * INICIO FOLHA
     */
    
    /*
     * CRIEI ESSAS VARIAVEIS,
     * POIS VAI TER QUE IMPLEMENTAR
     * ESSE RELATÓRIO PARA 13º
     */    
    $_and = "AND fp.terceiro = 2";
    
    $sql_descontados = "
    	SELECT folha.nome AS nome,unidade.id_unidade ,unidade.unidade AS unidade, funcao.nome AS nome_curso, 
        
                folha.base_inss, folha.inss AS inss_clt, 
                folha.base_irrf, folha.imprenda AS irrf_clt,
                folha.base_inss as base_fgts, if(folha.status_clt NOT IN(61,63,64,66),(folha.base_inss * 0.08),0) AS fgts_clt,
                
                folha.inss_ferias
                
                
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
        SELECT SUM(inss_clt) as total_inss, SUM(irrf_clt) as total_irrf, SUM(fgts_clt) AS total_fgts, SUM(inss_ferias) as total_inss_ferias, unidade,id_unidade FROM (
                {$sql_descontados}
        )AS temp 
        GROUP BY id_unidade
    ");
    
    $matrizTotal = array();
    $matrizTotalGeral = array();
    while($row_totalizador = mysql_fetch_assoc($qr_totalizador)){
        $matrizTotal[$row_totalizador['id_unidade']] = $row_totalizador;
        $matrizTotalGeral['inss'] += $row_totalizador['total_inss'];
        $matrizTotalGeral['irrf'] += $row_totalizador['total_irrf'];
        $matrizTotalGeral['fgts'] += $row_totalizador['total_fgts'];
    }
    
    /**
     * FINAL FOLHA
     */
    
    
    /**
     * INICIO FERIAS
     */
    $sql_descontados_ferias = "SELECT folha.id_folha, folha.inss_ferias, folha.ir_ferias, folha.fgts_ferias, folha.nome AS nome, unidade.id_unidade ,unidade.unidade AS unidade  FROM rh_folha_proc AS folha
                INNER JOIN rh_clt AS clt
		ON clt.id_clt = folha.id_clt
		INNER JOIN unidade AS unidade
		ON unidade.id_unidade = clt.id_unidade
                WHERE folha.mes = {$mes}
                AND folha.ano = {$ano}
                AND folha.status = 3  AND folha.inss_ferias > 0 {$projeto_sql} ORDER BY unidade.id_unidade";
                
    $qr_descontados_ferias = mysql_query($sql_descontados_ferias);
    $total_descontados_ferias = mysql_num_rows($qr_descontados_ferias);
    
    $qr_totalizador_ferias = mysql_query("
        SELECT SUM(inss_ferias) as total_inss_ferias, SUM(ir_ferias) AS total_ir_ferias, SUM(fgts_ferias) AS total_fgts_ferias, unidade, id_unidade FROM (
                {$sql_descontados_ferias}
        )AS temp 
        GROUP BY id_unidade 
    ") or die("" . mysql_error());
    
    while($row_totalizador_ferias = mysql_fetch_assoc($qr_totalizador_ferias)){
        $matrizTotal[$row_totalizador['id_unidade']] = $row_totalizador_ferias;
        $matrizTotalGeral['inss_ferias'] += $row_totalizador_ferias['total_inss_ferias'];
        $matrizTotalGeral['ir_ferias'] += $row_totalizador_ferias['total_ir_ferias'];
        $matrizTotalGeral['fgts_ferias'] += $row_totalizador_ferias['total_fgts_ferias'];
    }
    
    /**
     * FINAL FERIAS
     */
    
    /**
     * INICIO RESCISÃO
     */
    $sql_descontados_rescisao = "SELECT folha.id_folha, folha.inss_rescisao,folha.inss_dt,folha.ir_rescisao,folha.ir_dt,folha.fgts_rescisao,folha.fgts_dt, folha.nome AS nome, unidade.id_unidade ,unidade.unidade AS unidade  FROM rh_folha_proc AS folha
                INNER JOIN rh_clt AS clt
		ON clt.id_clt = folha.id_clt
		INNER JOIN unidade AS unidade
		ON unidade.id_unidade = clt.id_unidade
                WHERE folha.mes = {$mes}
                AND folha.ano = {$ano}
                AND folha.status = 3  AND folha.inss_rescisao > 0 {$projeto_sql} ORDER BY unidade.id_unidade";
                
    $qr_descontados_rescisao = mysql_query($sql_descontados_rescisao);
    $total_descontados_rescisao = mysql_num_rows($qr_descontados_rescisao);
    
    $qr_totalizador_rescisao = mysql_query("
        SELECT 
            SUM(inss_rescisao) as total_inss_rescisao, 
            SUM(inss_dt) as total_inss_dt, 
            SUM(ir_rescisao) AS total_ir_rescisao,
            SUM(ir_dt) AS total_ir_dt,
            SUM(fgts_rescisao) AS total_fgts_rescisao, 
            SUM(fgts_dt) AS total_fgts_dt, 
            unidade, id_unidade FROM (
                {$sql_descontados_rescisao}
        )AS temp 
        GROUP BY id_unidade 
    ") or die("erro" . mysql_error());
                
//    echo "<pre>";
//        print_r("SELECT 
//            SUM(inss_rescisao) as total_inss_rescisao, 
//            SUM(inss_dt) as total_inss_dt, 
//            SUM(ir_rescisao) AS total_ir_rescisao,
//            SUM(ir_dt) AS total_ir_dt,
//            SUM(fgts_rescisao) AS total_fgts_rescisao, 
//            SUM(fgts_dt) AS total_fgts_dt, 
//            unidade, id_unidade FROM (
//                {$sql_descontados_rescisao}
//        )AS temp 
//        GROUP BY id_unidade ");
//    echo "</pre>";            
    
    while($row_totalizador_rescisao = mysql_fetch_assoc($qr_totalizador_rescisao)){
        
        $matrizTotal[$row_totalizador_rescisao['id_unidade']] = $row_totalizador_rescisao;
        $matrizTotalGeral['inss_rescisao'] += $row_totalizador_rescisao['total_inss_rescisao'];
        $matrizTotalGeral['inss_dt'] += $row_totalizador_rescisao['total_inss_dt'];
        $matrizTotalGeral['ir_rescisao'] += $row_totalizador_rescisao['total_ir_rescisao'];
        $matrizTotalGeral['ir_dt'] += $row_totalizador_rescisao['total_ir_dt'];
        $matrizTotalGeral['fgts_rescisao'] += $row_totalizador_rescisao['total_fgts_rescisao'];
        $matrizTotalGeral['fgts_dt'] += $row_totalizador_rescisao['total_fgts_dt'];
    }
    
    /**
     * FINAL RESCISÃO
     */
    
    
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
	<title>Relatório de Desconto de FOLHA, FERIAS E RESCISÃO</title>
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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Relatório de Desconto de FOLHA, FERIAS E RESCISÃO</small></h2></div>
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
                                    <label class="input-group-addon" for="filtroTipo">
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
                        
                    <div class="panel-footer text-right controls">
                        <?php if(!empty($total_descontados) && (isset($_POST['filtrar']))) { ?>
                        <button type="button" value="exportar para excel" onclick="tableToExcel('table_excel', 'Inss')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                        <button type="button" form="formPdf" name="pdf" data-title="Relatório de Desconto de FOLHA, FERIAS E RESCISÃO" data-id="table_export_pdf" id="pdf" value="Gerar PDF" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Gerar PDF</button>
                        <?php } ?>
                        <button type="submit" name="filtrar" id="filt" value="Filtrar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                    </div>
                </div>
            
            
            <?php
            if($filtro) {
                if($total_descontados > 0) {
            ?>
                
           <table class="table table-bordered table-condensed text-sm valign-middle table_export_pdf" id="table_excel">
                <thead>                    
                    <tr class="bg-primary">
                        <td colspan="6">Folha</td>
                    </tr>
                </thead>
                <thead>                    
                    <tr>
                        <th>Nome</th>
                        <th>Função</th>
                        <th>Unidade</th>
                        <th>INSS</th>
                        <th>IRRF</th>                        
                        <th>FGTS</th>                        
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while($row_descontado = mysql_fetch_assoc($qr_descontados)) { 
                    if($unidade != $row_descontado['id_unidade']){
                        //CABEÇALHO, PRINTADO LINHA TOTALIZADOR
                        if(!empty($unidade)){
                            echo "<tr class='warning'><td class='text-right' colspan='3'> {$unidade_nome}: </td> 
                                <td>".number_format($matrizTotal[$unidade]['total_inss'], 2, ',', '.')."</td> 
                                <td>".number_format($matrizTotal[$unidade]['total_irrf'], 2, ',', '.')."</td> 
                                <td>".number_format($matrizTotal[$unidade]['total_fgts'], 2, ',', '.')."</td> 
                                ";
                        }
                         
                        //CABEÇALHO, MUDANÇA DE UNIDADE
                        $unidade = $row_descontado['id_unidade'];
                        $unidade_nome = $row_descontado['unidade'];
                    }

                    ?>
                    <tr class="linhasParticipantes">
                        <td><?php echo $row_descontado['nome']; ?></td>
                        <td><?php echo $row_descontado['nome_curso']; ?></td>
                        <td><?php echo $unidade_nome; ?></td>
                        <td><?php echo number_format($row_descontado['inss_clt'], 2, ',', '.');  ?></td>
                        <td><?php echo number_format($row_descontado['irrf_clt'], 2, ',', '.');  ?></td>
                        <td><?php echo number_format($row_descontado['fgts_clt'], 2, ',', '.');  ?></td>
                         
                    </tr>
                    <?php }
                    
                    echo "<tr class='warning'><td class='text-right'  colspan='3'> {$unidade_nome}: </td> 
                            <td>".number_format($matrizTotal[$unidade]['total_inss'], 2, ',', '.')."</td> 
                            <td>".number_format($matrizTotal[$unidade]['total_irrf'], 2, ',', '.')."</td> 
                            <td>".number_format($matrizTotal[$unidade]['total_fgts'], 2, ',', '.')."</td> 
                            ";
                     
                    ?>
                    
                    
                </tbody>
                <tfoot>
                    <tr class='danger'>
                        <td class='text-right'  colspan='3'>Total Geral:</td>
                        <td><?php echo number_format($matrizTotalGeral['inss'], 2, ',', '.');  ?></td>
                        <td><?php echo number_format($matrizTotalGeral['irrf'], 2, ',', '.');  ?></td>
                        <td><?php echo number_format($matrizTotalGeral['fgts'], 2, ',', '.');  ?></td>
                         
                    </tr>
                </tfoot>
                
            </table>
            
            <!-- FERIAS -->
            <table class="table table-bordered table-hover table-condensed text-sm valign-middle table_export_pdf">
                <thead>                    
                    <tr class="bg-primary">
                        <td colspan="4">Férias</td>
                    </tr>
                </thead>
                <thead>                    
                    <tr>
                        <th style="width: 812px">Nome</th>
                        <th>INSS FÉRIAS</th>
                        <th>IRRF FÉRIAS</th>                        
                        <th>FGTS FÉRIAS</th>                        
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $unidade = "";
                    while($row_descontado_ferias = mysql_fetch_assoc($qr_descontados_ferias)) { 
                        
                    if($unidade != $row_descontado_ferias['id_unidade']){
                        //CABEÇALHO, PRINTADO LINHA TOTALIZADOR
                        if(!empty($unidade)){
                            echo "<tr class='warning'><td class='text-right'> {$unidade_nome}: </td> 
                                <td>".number_format($matrizTotal[$unidade]['total_inss_ferias'], 2, ',', '.')."</td> 
                                <td>".number_format($matrizTotal[$unidade]['total_ir_ferias'], 2, ',', '.')."</td> 
                                <td>".number_format($matrizTotal[$unidade]['total_fgts_ferias'], 2, ',', '.')."</td> 
                                ";
                        }
                         
                        //CABEÇALHO, MUDANÇA DE UNIDADE
                        $unidade = $row_descontado_ferias['id_unidade'];
                        $unidade_nome = $row_descontado_ferias['unidade'];
                    }

                    ?>
                    <tr class="linhasParticipantes">
                        <td><?php echo $row_descontado_ferias['nome']; ?></td>
                        <td><?php echo number_format($row_descontado_ferias['inss_ferias'], 2, ',', '.');  ?></td>
                        <td><?php echo number_format($row_descontado_ferias['ir_ferias'], 2, ',', '.');  ?></td>
                        <td><?php echo number_format($row_descontado_ferias['fgts_ferias'], 2, ',', '.');  ?></td>                         
                    </tr>
                    <?php }
                    
                    echo "<tr class='warning'><td class='text-right'> {$unidade_nome}: </td> 
                            <td>".number_format($matrizTotal[$unidade]['total_inss_ferias'], 2, ',', '.')."</td> 
                            <td>".number_format($matrizTotal[$unidade]['total_ir_ferias'], 2, ',', '.')."</td> 
                            <td>".number_format($matrizTotal[$unidade]['total_fgts_ferias'], 2, ',', '.')."</td> 
                            ";
                     
                    ?>
                    
                    
                </tbody>
                <tfoot>
                    <tr class='danger'>
                        <td class='text-right'>Total Geral:</td>
                        <td><?php echo number_format($matrizTotalGeral['inss_ferias'], 2, ',', '.');  ?></td>
                        <td><?php echo number_format($matrizTotalGeral['ir_ferias'], 2, ',', '.');  ?></td>
                        <td><?php echo number_format($matrizTotalGeral['fgts_ferias'], 2, ',', '.');  ?></td>
                    </tr>
                </tfoot>
                
            </table>
            
            <!-- RESCISAO -->
            <table class="table table-bordered table-hover table-condensed text-sm valign-middle table_export_pdf">
                <thead>                    
                    <tr class="bg-primary">
                        <td colspan="7">Rescisão</td>
                    </tr>
                </thead>
                <thead>                    
                    <tr>
                        <th style="width: 812px">Nome</th>
                        <th>INSS RESCISÃO</th>
                        <th>INSS 13° RESCISÃO</th>
                        <th>IRRF RESCISÃO</th>                        
                        <th>IRRF 13° RESCISÃO</th>                        
                        <th>FGTS RESCISÃO</th>                        
                        <th>FGTS 13° RESCISÃO</th>                        
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $unidade = "";
                    while($row_descontado_rescisao = mysql_fetch_assoc($qr_descontados_rescisao)) { 
                        
                    if($unidade != $row_descontado_rescisao['id_unidade']){
                        
                        
                        //CABEÇALHO, PRINTADO LINHA TOTALIZADOR
                        if(!empty($unidade)){
                            echo "<tr class='warning'><td class='text-right'> {$unidade_nome}: </td> 
                                <td>".number_format($matrizTotal[$unidade]['total_inss_rescisao'], 2, ',', '.')."</td> 
                                <td>".number_format($matrizTotal[$unidade]['total_inss_dt'], 2, ',', '.')."</td> 
                                <td>".number_format($matrizTotal[$unidade]['total_ir_rescisao'], 2, ',', '.')."</td> 
                                <td>".number_format($matrizTotal[$unidade]['total_ir_dt'], 2, ',', '.')."</td> 
                                <td>".number_format($matrizTotal[$unidade]['total_fgts_rescisao'], 2, ',', '.')."</td> 
                                <td>".number_format($matrizTotal[$unidade]['total_fgts_dt'], 2, ',', '.')."</td> 
                                ";
                        }
                         
                        //CABEÇALHO, MUDANÇA DE UNIDADE
                        $unidade = $row_descontado_rescisao['id_unidade'];
                        $unidade_nome = $row_descontado_rescisao['unidade'];
                    }

                    ?>
                    <tr class="linhasParticipantes">
                        <td><?php echo $row_descontado_rescisao['nome']; ?></td>
                        <td><?php echo number_format($row_descontado_rescisao['inss_rescisao'], 2, ',', '.');  ?></td>
                        <td><?php echo number_format($row_descontado_rescisao['inss_dt'], 2, ',', '.');  ?></td>
                        <td><?php echo number_format($row_descontado_rescisao['ir_rescisao'], 2, ',', '.');  ?></td>
                        <td><?php echo number_format($row_descontado_rescisao['ir_dt'], 2, ',', '.');  ?></td>
                        <td><?php echo number_format($row_descontado_rescisao['fgts_rescisao'], 2, ',', '.');  ?></td>                         
                        <td><?php echo number_format($row_descontado_rescisao['fgts_dt'], 2, ',', '.');  ?></td>                         
                    </tr>
                    <?php }
                    
                     echo "<tr class='warning'><td class='text-right'> {$unidade_nome}: </td> 
                                <td>".number_format($matrizTotal[$unidade]['total_inss_rescisao'], 2, ',', '.')."</td> 
                                <td>".number_format($matrizTotal[$unidade]['total_inss_dt'], 2, ',', '.')."</td> 
                                <td>".number_format($matrizTotal[$unidade]['total_ir_rescisao'], 2, ',', '.')."</td> 
                                <td>".number_format($matrizTotal[$unidade]['total_ir_dt'], 2, ',', '.')."</td> 
                                <td>".number_format($matrizTotal[$unidade]['total_fgts_rescisao'], 2, ',', '.')."</td> 
                                <td>".number_format($matrizTotal[$unidade]['total_fgts_dt'], 2, ',', '.')."</td> 
                                ";
                     
                    ?>
                                        
                </tbody>
                <tfoot>
                    <tr class='danger'>
                        <td class='text-right'>Total Geral:</td>
                        <td><?php echo number_format($matrizTotalGeral['inss_rescisao'], 2, ',', '.');  ?></td>
                        <td><?php echo number_format($matrizTotalGeral['inss_dt'], 2, ',', '.');  ?></td>
                        <td><?php echo number_format($matrizTotalGeral['ir_rescisao'], 2, ',', '.');  ?></td>
                        <td><?php echo number_format($matrizTotalGeral['ir_dt'], 2, ',', '.');  ?></td>
                        <td><?php echo number_format($matrizTotalGeral['fgts_rescisao'], 2, ',', '.');  ?></td>
                        <td><?php echo number_format($matrizTotalGeral['fgts_dt'], 2, ',', '.');  ?></td>
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
