<?php
/**
 * Relatório 
 * 
 * @file                rateio.php
 * @license		F71
 * @link		http://des.f71iabassp.com/intranet/relatorios/rateio.php
 * @copyright           2016 F71
 * @author		Não Definido
 * @package             
 * @access              public  
 * 
 * @version: 3.0.0000 - ??/??/???? - Não Definido - Versão Inicial 
 * @version: 3.0.0000 - 12/01/2017 - Jacques      - Tunning com refatoramento do relatório 
 * 
 */

if(empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../wfunction.php');
include("../classes/global.php");

$usuario = carregaUsuario();

if($_COOKIE['logado'] == 179){
    print_r($usuario);
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$global = new GlobalClass();

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>"Relatório de Desconto de FGTS");
$breadcrumb_pages = array("Gestão de RH"=>"/intranet/rh/principalrh.php");

$ano = date('Y');
$filtro = $_REQUEST['filtrar'];

if(isset($filtro)){	
    $projeto = $_REQUEST['projeto'];
    $mes = sprintf("%02d", $_REQUEST['mes']);
    $ano = $_REQUEST['ano'];
        
    if($projeto > '0'){
        $projeto_sql = "AND folha.id_projeto = {$projeto}";
        $projeto_ferias = "AND projeto = '{$projeto}'";
        $projeto_aux = "AND A.id_projeto = '{$projeto}'";
    }else{
        $projeto_sql = null;
    }
    
    $qry_fp = "SELECT * FROM rh_folha WHERE mes = '{$mes}' AND ano = '{$ano}' {$projeto_ferias}";
    $sql_fp = mysql_query($qry_fp) or die(mysql_error());    
            
    while($res_fp = mysql_fetch_assoc($sql_fp)){
        $array_fp[] = $res_fp['ids_movimentos_estatisticas'];
    }
    
    $all_mov_estatisticas = implode(",", $array_fp);
    
//    if($_COOKIE['logado'] == 353){
//        print_array($all_mov_estatisticas);
//    }
    
    $sql_descontados = "
    	SELECT 
            folha.id_clt,
            folha.nome AS nome,
            unidade.id_unidade,
            unidade.unidade AS unidade, 
            folha.id_folha, 
            funcao.nome AS nome_curso, 
            folha.base_inss, 
            folha.inss AS inss, 
            P.nome AS nome_projeto, 
            EMP.aliquotaRat, 
            EMP.aliquota_rat,
            CAST((folha.base_inss*0.20) AS DECIMAL(12,2)) as inss_em,
            CAST((folha.base_inss*EMP.aliquota_rat) AS DECIMAL(12,2)) as inss_rat,
            CAST((folha.base_inss*0.058) AS DECIMAL(12,2)) as inss_ter,(folha.base_inss * 0.08) as fgts,
                
            CAST(IFNULL(ferias.valor,0) AS DECIMAL(12,2)) AS fgts_ferias,
            CAST(IFNULL(creche.valor,0) AS DECIMAL(12,2)) AS aux_creche
		
        FROM rh_folha_proc AS folha INNER JOIN rh_clt AS clt ON clt.id_clt = folha.id_clt
                                    INNER JOIN curso AS funcao ON clt.id_curso = funcao.id_curso
                                    INNER JOIN unidade AS unidade ON unidade.id_unidade = clt.id_unidade
                                    INNER JOIN projeto AS P ON P.id_projeto = folha.id_projeto
                                    INNER JOIN rhempresa AS EMP ON EMP.id_regiao = clt.id_regiao
                                    LEFT JOIN (
                                                SELECT id_clt,(valor_movimento * 0.08) valor
                                                FROM rh_movimentos_clt
                                                WHERE cod_movimento = 90016 AND mes_mov != 16 AND mes_mov = {$mes} AND ano_mov = {$ano} AND id_projeto = '3' AND id_movimento IN ({$all_mov_estatisticas})
                                               ) creche ON creche.id_clt = folha.id_clt
                                    LEFT JOIN (           
                                                SELECT id_clt,(base_inss * 0.08) valor
                                                FROM rh_ferias
                                                WHERE '{$ano}-{$mes}' BETWEEN DATE_FORMAT(data_ini, '%Y-%m') AND DATE_FORMAT(data_fim, '%Y-%m') AND STATUS = 1 AND MONTH(data_ini) = {$mes} AND projeto = '3'
                                                ORDER BY id_ferias DESC
                                               ) ferias ON ferias.id_clt = folha.id_clt
	WHERE folha.mes = {$mes}
		AND folha.ano = {$ano}
                AND folha.status = 3 /*AND folha.status_clt NOT IN(61,63,64,66)*/
                {$projeto_sql}
                    
        ORDER BY unidade,nome";
                
    $rs = mysql_query($sql_descontados) or die(mysql_error());

    $total_descontados = mysql_num_rows($rs);

    while ($row = mysql_fetch_assoc($rs)) {

        $array_clt[$row['id_unidade']][$row['id_clt']] = $row;

        $array_total_unidade[$row['id_unidade']]['id_folha'] = $row['id_folha'];
        $array_total_unidade[$row['id_unidade']]['nome'] = $row['unidade'];

        $total['total_base_inss'] += $array_total_unidade[$row['id_unidade']]['total_base_inss'] += $row['base_inss'];
        $total['total_fgts'] += $array_total_unidade[$row['id_unidade']]['total_fgts'] += $row['base_inss']*0.08;
        $total['total_fgts_ferias'] += $array_total_unidade[$row['id_unidade']]['total_fgts_ferias'] += $row['fgts_ferias'];
        $total['total_aux_creche'] += $array_total_unidade[$row['id_unidade']]['total_aux_creche'] += $row['aux_creche'];
        $total['total_fgts_43'] += $array_total_unidade[$row['id_unidade']]['total_fgts_43'] += ($row['base_inss']*0.08) * 0.43;
        $total['total_fgts_57'] += $array_total_unidade[$row['id_unidade']]['total_fgts_57'] += ($row['base_inss']*0.08) - (($row['base_inss']*0.08)*0.43);
        
        $total['total_inss_em'] += $array_total_unidade[$row['id_unidade']]['total_inss_em'] += $row['inss_em'];
        $total['total_inss_rat'] += $array_total_unidade[$row['id_unidade']]['total_inss_rat'] += $row['inss_rat'];
        $total['total_inss_ter'] += $array_total_unidade[$row['id_unidade']]['total_inss_ter'] += $row['inss_ter'];

    }
    
    //echo "<pre>$sql_descontados</pre>";
    
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
	<title>Relatório de Rateio</title>
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
    <style>
        .esconde100{ display:none !important; }
        .esconde43{ display:none !important; }
        .esconde57{ display:none !important; }
    </style>
</head>
<body>
	<?php include("../template/navbar_default.php"); ?>
        
        <div class="<?=($container_full) ? 'container-full' : 'container'?>">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Relatório de Desconto de FGTS</small></h2></div>
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
                        <?php if(!empty($total_descontados) && (isset($_POST['filtrar']))) { ?>
                            <button type="button" value="exportar para excel" onclick="tableToExcel('table_excel', 'Inss')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            <button type="button" form="formPdf" name="pdf" data-title="Relatório de Desconto de FGTS" data-id="table_excel" id="pdf" value="Gerar PDF" class="btn btn-danger"><span class="fa fa-file-pdf-o"></span> Gerar PDF</button>
                        <?php } ?>
                            <button type="submit" name="filtrar" id="filt" value="Filtrar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                    </div>
                </div>
            
            <?php
            if ($filtro) {
                if (count($array_clt) > 0) {
            ?>
            <table class="table table-bordered table-hover table-condensed text-sm valign-middle" id="table_excel">
                <thead>                    
                    <tr class="bg-primary">
                        <th>Nome</th>
                        <th>Função</th>
                        <th>Unidade</th>
                        <th class="<?php echo $esconde; ?>">FGTS 100%</th>
                        <th class="<?php echo $esconde; ?>">FGTS (FÉRIAS)</th>
                        <th class="<?php echo $esconde; ?>">AUX CRECHE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($array_clt as $key_id_unidade => $value_total_unidade) {

                            foreach ($value_total_unidade as $key_id_clt => $value) {

                                ?>
                                <tr class="linhasParticipantes">
                                    <td><?php echo $value['nome']; ?></td>
                                    <td><?php echo $value['nome_curso']; ?></td>
                                    <td><?php echo $value['unidade']; ?></td>
                                    <td class='text-right'><?php echo number_format($value['fgts'], 2, ',', '.'); ?></td>
                                    <td class='text-right'><?php echo number_format($value['fgts_ferias'] * 0.08, 2, ',', '.'); ?></td>
                                    <td class='text-right'><?php echo number_format($value['aux_creche'] * 0.08, 2, ',', '.'); ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                            <tr class='warning'><td class='text-right' colspan='3'> <?=$array_total_unidade[$key_id_unidade]['nome']?>: </td> 
                            <td class='text-right'><?=number_format($array_total_unidade[$key_id_unidade]['total_fgts'], 2, ',', '.')?></td> 
                            <td class='text-right'><?=number_format($array_total_unidade[$key_id_unidade]['total_fgts_ferias'], 2, ',', '.')?></td>
                            <td class='text-right'><?=number_format($array_total_unidade[$key_id_unidade]['total_aux_creche'], 2, ',', '.')?></td>
                            <?php
                        }    
                    ?>
                </tbody>
                <tfoot>
                    <tr class='danger'>
                        <td class='text-right' colspan="3">Total Geral(FGTS(100%) + FGTS(FÉRIAS) - AUX. CRECHE):</td>
                        <td class="<?php echo $esconde; ?>" colspan="3"><?php echo number_format($total['total_fgts'] + $total['total_fgts_ferias'] - $total['total_aux_creche'], 2, ',', '.');  ?></td>
                         
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
