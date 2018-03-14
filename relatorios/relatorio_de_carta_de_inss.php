<?php
if(empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../wfunction.php');
include("../classes/global.php");

//retorna uma array associativa com os dados do usuario logado
$usuario = carregaUsuario();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$global = new GlobalClass();

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>"Relatório de Carta de INSS");
$breadcrumb_pages = array("Gestão de RH"=>"/intranet/rh/principalrh.php");

$ano = 2016;
$filtro = $_REQUEST['filtrar'];

if(isset($filtro)){
	
	$projeto = $_REQUEST['projeto'];
	$mes = $_REQUEST['mes'];
    $ano = $_REQUEST['ano'];
    $cdata= $ano.'-'.$mes.'-01';
   

    $qrcarta = mysql_query("   SELECT A.nome, B.desconto, DATE_FORMAT(B.inicio, '%d/%m/%Y') AS inicio, DATE_FORMAT(B.fim, '%d/%m/%Y') AS fim
        FROM rh_clt A
        INNER JOIN (
        SELECT id_clt, desconto, inicio, fim
        FROM rh_inss_outras_empresas
        WHERE '{$cdata}' BETWEEN inicio AND fim AND STATUS = 1) AS B ON (A.id_clt = B.id_clt)
        WHERE A.id_projeto = {$projeto} AND A.status NOT IN (
        SELECT codigo
        FROM rhstatus
        WHERE tipo != 'recisao')
        ORDER BY nome	
            ");
    $total_cartas = mysql_num_rows($qrcarta);
		
}
?>
<!doctype html>
<html>
<head>
	<meta charset="iso-8859-1">
	<title>Relatório de Carta de INSS</title>
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
    
</head>
<body>
	<?php include("../template/navbar_default.php"); ?>
        
        <div class="<?=($container_full) ? 'container-full' : 'container'?>">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Relatório de Carta de INSS</small></h2></div>
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
                    <div class="panel-heading">Relataróio Detalhado</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Projeto:  </label>
                            <div class="col-lg-8">
                                <?php //cria o select da regiao                                
                                echo montaSelect($global->carregaProjetosByRegiao($usuario['id_regiao']), $projeto, "id='projeto' name='projeto' class='required[custom[select]] form-control'"); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Competência</label>
                            <div class="col-lg-8">
                                <div class="input-daterange input-group" id="bs-datepicker-range">
                                    <?php //select mes 
                                    echo montaSelect(mesesArray(), $mes, "id='mes' name='mes' class='required[custom[select]] form-control'"); ?>
                                    <span class="input-group-addon">Ano</span>
                                    <?php //select ano 
                                    echo montaSelect(AnosArray(null,null), $ano, "id='ano' name='ano' class='required[custom[select]] form-control'"); ?>                                
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right hidden-print controls">
                        <?php if(!empty($total_cartas) && (isset($_POST['filtrar']))) { ?>
                            <button type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                        <?php } ?>
                            <button type="submit" name="filtrar" id="filt" value="Filtrar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                    </div>
                </div>
            
            <?php
            if($filtro) {
                if($total_cartas > 0) {
            ?>
            
            <table id="tbRelatorio" class="table table-bordered table-hover table-condensed text-sm valign-middle">
                <thead>                    
                    <tr class="bg-primary">
                        <th>Nome</th>
                        <th>Desconto</th>
                        <th>Início</th>
                        <th>Fim</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($row_carta = mysql_fetch_assoc($qrcarta)) { ?>
                <tr class="active">
                	<td><?php echo $row_carta['nome']; ?></td>
                	<td><?php echo $row_carta['desconto']; ?></td>
                	<td><?php echo $row_carta['inicio']; ?></td>
                        <td><?php echo $row_carta['fim']; ?></td>                	
                </tr>
				<?php } ?>
                <tfoot>
                    <tr class="info">
                        <td colspan="4"><strong>Total: </strong><?php echo $total_cartas; ?></td>
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
        <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.ui.datepicker-pt-BR.js" type="text/javascript"></script>
        <script src="../js/jquery.form.js" type="text/javascript"></script>
</body>
</html>
