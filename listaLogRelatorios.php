<?php // session_start();
	if (empty($_COOKIE['logado'])) {
		print "<script>location.href = 'login.php?entre=true';</script>";
                exit;
	} else {
	
         header("Content-Type:text/html; charset=ISO-8859-1", true);
	include("conn.php");
	include("wfunction.php");
        include('classes/funcionario.php');
	
	$usuario = carregaUsuario();
	$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
		
	$breadcrumb_config = array("nivel" => "../intranet", "key_btn" => "6", "area" =>"Sistema", "id_form"=>"form1", "ativo"=>"Relatórios de Logs");
	$breadcrumb_pages = array();
	
		
        if (isset($_REQUEST['par']) && $_REQUEST['par'] == TRUE) {
        $url = explode("?", $_REQUEST['url']);
        $date = date("Y-m-d H:i:s");
        $idUsuario = (isset($_REQUEST['id'])) ? $_REQUEST['id'] : $usuario['id_funcionario'];
        $query = "INSERT INTO relatorios_log (nome_arquivo,data_acesso,id_usuario) VALUES ('$url[0]','$date','$idUsuario');";
        echo $query;
        $result = mysql_query($query);
        echo ($result) ? TRUE : FALSE;
        } else {

        $Fun = new funcionario();
        $Fun->MostraUser(0);
        $Master = $Fun->id_master;
        $Id = $Fun->id_funcionario;

        $qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$Id'");
        $funcionario = mysql_fetch_array($qr_funcionario);

        $projeto = $_REQUEST['projeto'];
        $regiao = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
        
        function relatorioNovo($data){
            $arr_date = explode('/', $data);
            $data = mktime(0,0,0,$arr_date[1],$arr_date[0],$arr_date[2]);
            $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $qtd_dias = $hoje - $data;
            $qtd_dias = (int)floor( $qtd_dias / (60 * 60 * 24));
            return ($qtd_dias <= 14) ? '<span class="rel-novo">Novo!</span>' : '';
        }
        
        
        
	
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Relatório de Logs</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="resources/css/main.css" rel="stylesheet" media="screen">
        <link href="resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
             
        
	</head>
    <body>
        <?php include("template/navbar_default.php"); ?>
        
        <div class="container">
            <form action="" id="form1" method="post"></form>
            <div class="page-header box-sistema-header"><h2><span class="glyphicon glyphicon-phone"></span> - Sistema<small> - Relatórios de Logs</small></h2></div>
            
            <table class='table table-hover table-striped text-sm valign-middle'>
                <thead>
                    <tr>
                        <th width="85%">NOME DO RELATÓRIO</th>
                        <th width="15%">GERAR DOCUMENTO</th>
                    </tr>
                </thead>
                <tbody>
                   <tr class="linha_<?= ($classe++ % 2 == 0) ? "um" : "dois"; ?>">
                        <td>Relatório de Log de Acesso aos Relatórios <?= relatorioNovo('11/06/2014') ?></td>
                        <td align="center"><a class="btn btn-success btn-xs" href='relatorios/relatorio_log_rel_acesso_1.php?reg=<?= $regiao ?>&pro=<?= $projeto ?>' target='_blank'><span class="fa fa-file-text-o"></span> Ver Relatório</a></td>
                    </tr>
                    <tr class="linha_<?= ($classe++ % 2 == 0) ? "um" : "dois"; ?>">
                        <td>Relatório de Quantidade de Acessos aos Relatórios <?= relatorioNovo('11/06/2014') ?></td>
                        <td align="center"><a class="btn btn-success btn-xs" href='relatorios/relatorio_log_rel_acesso_2.php?reg=<?= $regiao ?>&pro=<?= $projeto ?>' target='_blank'><span class="fa fa-file-text-o"></span> Ver Relatório </a></td>
                    </tr>
                    <tr class="linha_<?= ($classe++ % 2 == 0) ? "um" : "dois"; ?>">
                        <td>Relatório de Desprocessamento de Férias <?= relatorioNovo('30/06/2014') ?></td>
                        <td align="center"><a class="btn btn-success btn-xs" href='relatorios/relatorio_log_ferias_desprocessar.php?reg=<?= $regiao ?>&pro=<?= $projeto ?>' target='_blank'> <span class="fa fa-file-text-o"></span> Ver Relatório </a></td>
                    </tr>
                    <tr class="linha_<?= ($classe++ % 2 == 0) ? "um" : "dois"; ?>">
                        <td>Relatório de Desprocessamento de Transferências <?= relatorioNovo('01/07/2014') ?></td>
                        <td align="center"><a class="btn btn-success btn-xs" href='relatorios/relatorio_log_transf_desprocessar.php?reg=<?= $regiao ?>&pro=<?= $projeto ?>' target='_blank'> <span class="fa fa-file-text-o"></span> Ver Relatório</a></td>
                    </tr>
                    <tr class="linha_<?= ($classe++ % 2 == 0) ? "um" : "dois"; ?>">
                        <td>Relatório de Log Eventos <?= relatorioNovo('15/07/2014') ?></td>
                        <td align="center"><a class="btn btn-success btn-xs" href='relatorios/relatorio_log_eventos.php?reg=<?= $regiao ?>&pro=<?= $projeto ?>' target='_blank'> <span class="fa fa-file-text-o"></span> Ver Relatório</a></td>
                    </tr>
                </tbody>
	</table>
			
            <?php include('template/footer.php'); ?>
        </div>
        
        <script src="js/jquery-1.10.2.min.js"></script>
        <script src="js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="resources/js/bootstrap.min.js"></script>
        <!--<script src="resources/js/bootstrap-dialog.min.js"></script>-->
        <!--<script src="js/jquery.validationEngine-2.6.js"></script>-->
        <!--<script src="js/jquery.validationEngine-pt_BR-2.6.js"></script>-->
        <!--<script src="js/jquery.maskedinput-1.3.1.js"></script>-->
        <!--<script src="js/jquery.maskMoney.js" type="text/javascript" ></script>-->
        <!--<script src="resources/dropzone/dropzone.js"></script>-->
        <!--<script src="js/jquery.form.js"></script>-->
        <script src="resources/js/main.js"></script>
        <script src="js/global.js"></script>
        <!--<script src="js/jquery.validationEngine-2.6.js" type="text/javascript"></script>-->
        <!--<script src="js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>-->
        <script type="text/javascript">
            $(document).ready(function() {
                $(".tb-relatorios a").click(function() {
                    var url = $(this).attr('href');
                    $.post('<?= $_SERVER['PHP_SELF'] ?>', {url: url, id:<?= $Id ?>, par: true}, function(data) {
                        if (data === true) {
                            windows.open(url);
                        }
                    });
                });
            });

        </script>
    </body>
</html>
<?php
        }
    }
?>