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

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>"Relatório de Desconto de PIS");
$breadcrumb_pages = array("Gestão de RH"=>"/intranet/rh/principalrh.php");

$ano = date('Y');
$filtro = $_REQUEST['filtrar'];

if(isset($filtro)){
	
    $projeto = $_REQUEST['projeto'];
    $mes = $_REQUEST['mes'];
    $ano = $_REQUEST['ano'];
    $tipoFolha = $_REQUEST['tipoFolha'];
    
    /*
     * CRIEI ESSAS VARIAVEIS,
     * POIS VAI TER QUE IMPLEMENTAR
     * ESSE RELATÓRIO PARA 13º
     * 
     * @author Lucas Praxedes Serra (10/01/2016)
     * ADAPTANDO PARA 13º
     */
    
    $_and = "AND D.terceiro = 2";
    $_and2 = "AND fp.terceiro = 2";
    $_join = "LEFT JOIN rh_folha D ON (A.id_folha = D.id_folha)";
    switch ($tipoFolha) {
        case '1':
            $folhaTerceiro = 'AND folha.terceiro = 2';
            $_and = "AND D.terceiro = 2";
            $_and2 = "AND fp.terceiro = 2";
            break;
        case '2':
            $folhaTerceiro = 'AND folha.terceiro = 1 AND folha.tipo_terceiro = 3';
            $_and = "AND D.terceiro = 1 AND D.tipo_terceiro = 3";
            $_and2 = "AND fp.terceiro = 1 AND fp.tipo_terceiro = 3";
            break;
        case '3':
            $folhaTerceiro = 'AND folha.terceiro = 1 AND folha.tipo_terceiro = 1';
            $_and = "AND D.terceiro = 1 AND D.tipo_terceiro = 1";
            $_and2 = "AND fp.terceiro = 1 AND fp.tipo_terceiro = 1";
            break;
        case '4':
            $folhaTerceiro = 'AND folha.terceiro = 1 AND folha.tipo_terceiro = 2';
            $_and = "AND D.terceiro = 1 AND D.tipo_terceiro = 2";
            $_and2 = "AND fp.terceiro = 1 AND fp.tipo_terceiro = 2";
            break;
    }
    
    if($projeto > '0') { 
        $projeto_sql = "AND folha.id_projeto = {$projeto}"; 
        $folha_sql = "AND projeto = {$projeto}"; 
    }
    
    $qry_fp = "SELECT ids_movimentos_estatisticas FROM rh_folha folha WHERE mes = {$mes} AND ano = {$ano} {$folha_sql} {$folhaTerceiro} AND status = 3";
    $sql_fp = mysql_query($qry_fp) or die(mysql_error());    
    
    $array_fp = array();
    
    while($res_fp = mysql_fetch_assoc($sql_fp)){
//        print_array($res_fp);
        $array_fp[] = $res_fp['ids_movimentos_estatisticas'];
    }
    
    
    
    $sql_descontados = "
    	SELECT 
            folha.nome AS nome,unidade.id_unidade ,unidade.unidade AS unidade, funcao.nome AS nome_curso, folha.base_inss , folha.id_folha,
            folha.inss AS inss, P.nome AS nome_projeto, EMP.aliquotaRat, EMP.aliquota_rat,
            /*CAST((folha.base_inss*0.20) AS DECIMAL(12,2)) as inss_em,
            CAST((folha.base_inss*EMP.aliquota_rat) AS DECIMAL(12,2)) as inss_rat,
            CAST((folha.base_inss*0.058) AS DECIMAL(12,2)) as inss_ter,(folha.base_inss * 0.08) as fgts, folha.base_irrf, folha.imprenda, folha.d_imprenda,*/
            IF(RMC.valor_movimento IS NULL,0,RMC.valor_movimento) AS auxilio_creche, (folha.base_inss/* - IF(RMC.valor_movimento IS NULL,0,RMC.valor_movimento)*/) AS base_pis, ((folha.base_inss - IF(RMC.valor_movimento IS NULL,0,RMC.valor_movimento)) * 0.01) as pis,
            
            CAST((SELECT (A.base_inss) base_n_incide
            FROM rh_folha_proc A
            LEFT JOIN rh_clt B ON (A.id_clt = B.id_clt)
            LEFT JOIN rh_recisao C ON (A.id_clt = C.id_clt)
            {$_join}
            WHERE A.status = 3 AND A.mes = {$mes} AND A.ano = {$ano} AND MONTH(C.data_demi) = {$mes} AND YEAR(C.data_demi) = {$ano} AND C.status = 1 AND C.rescisao_complementar != 1 AND B.status IN (61,64,66) AND A.id_clt = folha.id_clt {$projeto_aux} {$_and}) AS DECIMAL(12,2)) AS base_n_incide

        FROM rh_folha_proc AS folha 
        LEFT JOIN rh_clt AS clt ON clt.id_clt = folha.id_clt
        LEFT JOIN rh_folha AS fp ON folha.id_folha = fp.id_folha
        LEFT JOIN curso AS funcao ON clt.id_curso = funcao.id_curso
        LEFT JOIN unidade AS unidade ON unidade.id_unidade = clt.id_unidade
        LEFT JOIN projeto AS P ON P.id_projeto = folha.id_projeto
        LEFT JOIN rhempresa AS EMP ON EMP.id_regiao = clt.id_regiao
        LEFT JOIN (SELECT * FROM rh_movimentos_clt WHERE cod_movimento = '90016' AND mes_mov != 16 AND ano_mov = {$ano} AND mes_mov = {$mes} AND id_movimento IN(".implode(',',$array_fp).")) AS RMC ON (RMC.id_clt = clt.id_clt)
        WHERE 
            folha.mes = {$mes}
            AND folha.ano = {$ano}
            AND folha.status = 3 
            {$projeto_sql}
            {$_and2}
        ORDER BY unidade,nome";
            
    if($_COOKIE[debug] == 666){
        echo '<br>////////////////////////$sql_descontados////////////////////////<br>';
        echo $sql_descontados;
    }
    $qr_descontados = mysql_query($sql_descontados) or die(mysql_error());
    $total_descontados = mysql_num_rows($qr_descontados);
    
    $qr_totalizador = mysql_query("
        SELECT SUM(base_pis) as total_base_pis, SUM(base_n_incide) as total_base_n_incide, SUM(base_pis * 0.01) as total_pis,  unidade, id_unidade FROM (
                {$sql_descontados}
        )AS temp 
        GROUP BY id_unidade
    ");
    
    $matrizTotal = array();
    $matrizTotalGeral = array();
    
    $valor_base_folha_47 = 1065.5;
//    if($_GET['debug'] == '666'){ echo "$sql_descontados ******** {$row_totalizador['id_folha']} == 47 && $valor_base_folha_47 > 0"; }
    while($row_totalizador = mysql_fetch_assoc($qr_totalizador)){
        $matrizTotal[$row_totalizador['id_unidade']] = $row_totalizador;
        $matrizTotalGeral['base_pis'] += $row_totalizador['total_base_pis'] - $row_totalizador['total_base_n_incide'];
//        $matrizTotalGeral['pis'] += $row_totalizador['total_pis'];
    }
    
    $unidade = "";
    $unidade_nome = "";
    $toInss = 0;
    $toInss_emp = 0;
    $toInss_rat = 0;
}

$arrTipoFolha = array(1 => "Normal", 2 => "13º Integral", 3 => "13º Primeira Parcela", 4 => "13º Segunda Parcela");
$tipoFolhaSel = (isset($tipoFolha)) ? $tipoFolha : null;
?>
<!doctype html>
<html>
<head>
	<meta charset="iso-8859-1">
	<title>Relatório de Desconto de PIS</title>
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
    <script src="../resources/js/main.js"></script>
    <script src="../resources/js/print.js" type="text/javascript"></script>
    <script type="text/javascript" src="/intranet/js/bootstrap-datepicker-1.4.0-dist/js/bootstrap-datepicker.js?8181"></script>
    <script type="text/javascript" src="/intranet/js/bootstrap-datepicker-1.4.0-dist/js/bootstrap-datepicker.min.js?8181"></script>
    <script type="text/javascript" src="/intranet/js/bootstrap-datepicker-1.4.0-dist/locales/bootstrap-datepicker.pt-BR.min.js?8181"></script>
</head>
<body>
	<?php include("../template/navbar_default.php"); ?>
        
        <div class="<?=($container_full) ? 'container-full' : 'container'?>">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Relatório de Desconto de PIS</small></h2></div>
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
                                    <?php echo montaSelect($global->carregaProjetos($usuario['id_master'], $default = array("-1" => "« Todos os Projetos »")), $projeto, "id='projeto' name='projeto' class='required[custom[select]] form-control'"); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="select" class="col-lg-2 control-label">Mês:</label>
                                <div class="col-lg-8">
                                    <div class="input-daterange input-group" id="bs-datepicker-range">
                                        <?php echo montaSelect(mesesArray(), $mes, "id='mes' name='mes' class='required[custom[select]] form-control'"); ?>
                                        <span class="input-group-addon">Ano</span>
                                        <?php echo montaSelect(AnosArray(null, null), $ano, "id='ano' name='ano' class='required[custom[select]] form-control'"); ?>                                
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="select" class="col-lg-2 control-label">Folha:</label>
                                <div class="col-lg-8">
                                    <?php echo montaSelect($arrTipoFolha, $tipoFolhaSel, "id='tipoFolha' name='tipoFolha' class='required[custom[select]] form-control'"); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label"></label>
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
                                        <label class="form-control pointer" for="filtroTipo"> Unidade</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                    <div class="panel-footer text-right">
                         <?php if(!empty($total_descontados) && (isset($_POST['filtrar']))) { ?>
                            <button type="button" value="exportar para excel" onclick="tableToExcel('table_excel', 'Inss')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            <button type="button" form="formPdf" name="pdf" data-title="Relatório de Desconto de PIS" data-id="table_excel" id="pdf" value="Gerar PDF" class="btn btn-danger"><span class="fa fa-file-pdf-o"></span> Gerar PDF</button>
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
                    <tr class="bg-primary">
                        <th>Nome</th>
                        <th>Função</th>
                        <th>Unidade</th>
                        <th>Base PIS</th>
                        <th>PIS</th>                        
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while($row_descontado = mysql_fetch_assoc($qr_descontados)) { 
                        if($row_descontado['id_folha'] == 47 && ($valor_base_folha_47-0.8429588607594937) > 0){
                            $count++;
                            $row_descontado['total_base_pis'] -= 0.8429588607594937;
                            $matrizTotalGeral['base_pis'] -= 0.8429588607594937;
                            $matrizTotal[$row_descontado['id_unidade']]['total_base_pis'] -= 0.8429588607594937;
                            $valor_base_folha_47 -= 0.8429588607594937;
                        }
                        
                    if($unidade != $row_descontado['id_unidade']){
                        //CABEÇALHO, PRINTADO LINHA TOTALIZADOR
                        if(!empty($unidade)){
                            echo "<tr class='warning'><td class='text-right' colspan='3'> {$unidade_nome}: </td> 
                                <td>".number_format($matrizTotal[$unidade]['total_base_pis']-$matrizTotal[$unidade]['total_base_n_incide'], 2, ',', '.')."</td> 
                                <td>".number_format(($matrizTotal[$unidade]['total_base_pis']-$matrizTotal[$unidade]['total_base_n_incide'])*0.01, 2, ',', '.')."</td> 
                                ";
                        }
                         
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
                        <td><?php echo number_format($row_descontado['base_pis'] - $row_descontado['base_n_incide'], 2, ',', '.');  ?></td>
                        <td><?php echo number_format(($row_descontado['base_pis'] - $row_descontado['base_n_incide'])*0.01, 2, ',', '.');  ?></td>
                    </tr>
                    <?php }
                    
                   echo "<tr class='warning'><td class='text-right' colspan='3'> {$unidade_nome}: </td> 
                                <td>".number_format($matrizTotal[$unidade]['total_base_pis'] - $matrizTotal[$unidade]['total_base_n_incide'], 2, ',', '.')."</td> 
                                <td>".number_format(($matrizTotal[$unidade]['total_base_pis'] - $matrizTotal[$unidade]['total_base_n_incide']) * 0.01, 2, ',', '.')."</td> 
                                ";
                    ?>
                    
                    
                </tbody>
                <tfoot>
                    <tr class='danger'>
                        <td class='text-right' colspan='3'>Total Geral:</td>
                        <td><?php echo number_format($matrizTotalGeral['base_pis'], 2, ',', '.');  ?></td>
                        <td><?php echo number_format($matrizTotalGeral['base_pis']*0.01, 2, ',', '.');  ?></td>
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
