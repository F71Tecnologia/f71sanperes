<?php
session_start();

if(!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include('../../classes/global.php');
include('../../classes/FuncoesClass.php');
include("../../classes/CltClass.php");
include "../../classes/LogClass.php";
$log = new Log();

$usuario = carregaUsuario();
$master = $usuario['id_master'];
$id_regiao = $usuario['id_regiao'];
$id_usuario = $_COOKIE['logado'];

$regiao_selecionada = $_REQUEST['hide_regiao'];
$projeto_selecionado = $_REQUEST['hide_projeto'];

$_SESSION['regiao_select'] = $regiao_selecionada;
$_SESSION['projeto_select'] = $projeto_selecionado;
session_write_close();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form-lista", "ativo" => "Negar Solicitação de Demissão");
$breadcrumb_pages = array("Rescisão" => "/intranet/rh_novaintra/recisao/nova_rescisao.php");


$clt = $_REQUEST['clt'];
$justificativa = $_POST['justificativa'];

$qr_solicitacao = mysql_query("SELECT * FROM portal_rescisao_solicitacoes WHERE id_clt = '{$clt}' AND status_id = 1");
$row_solicitacao = mysql_fetch_assoc($qr_solicitacao);
$total_solicitacao = mysql_num_rows($qr_solicitacao);
 
if(isset($_REQUEST['cadastrar'])) {
	if($total_solicitacao) {
		mysql_query("UPDATE portal_rescisao_solicitacoes SET status_id = 2 WHERE id_clt = '{$clt}' LIMIT 1");
		if($justificativa) {
			mysql_query("INSERT INTO portal_rescisao_mensagens (solicitacao_id, status_id, texto, data) VALUES ({$row_solicitacao[id]}, 2, '{$justificativa}', NOW())");
		}
	}
        $log->gravaLog('Rescisão', "Rescisão ID{$row_solicitacao[id]}, negada");
	header('Location: nova_rescisao.php');
}

$objClt = new CltClass();
$clt = $objClt->carregaClt($_REQUEST['clt']);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Negar Solicitação de Demissão</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="jquery.autocomplete.css" rel="stylesheet" type="text/css" />  
    </head>
    <body>
    <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Negar Solicitação de Demissão</small></h2></div>
                </div>
            </div>
            <div class="row">
                <form action="" class="form-horizontal" method="post" name="form1" id="form1" autocomplete="off">
                	<div class="bs-callout bs-callout-danger" id="callout-helper-pull-navbar"> 
		                <input type="hidden" name="id_clt" value="<?php echo $clt->id_clt ?>" />
		                <h4><?php echo $clt->nome ?></h4> 
		                <p>Data de Admissão: <code><?php echo $clt->data_entrada ?></code></p>
		                <p>Fim do Período de Experiencia: <code><?php echo date("d/m/Y", strtotime($clt->data_fim_experiencia)) ?></code> <?php if($clt->data_fim_experiencia > date('Y-m-d')){ ?><span class="label label-warning">Em esperiência</span> <?php } ?></p>
		                <p>Função e Salário: <code><?php echo $clt->nome_curso." ".$clt->letranumero." - R$ ".$clt->salario ?></code></p>
		                <p>Unidade: <code><?php echo $clt->unidade ?></code></p>
		            </div>
                    <div class="col-xs-12 form_funcoes">
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="justificativa" class="col-xs-1 control-label">Justificativa:</label>
                                <div class="col-xs-11">
                                    <textarea name="justificativa" id="justificativa" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                        	<input type="hidden" name="clt" id="clt" value="<?php echo $clt->id_clt; ?>">
                            <input type="submit" class="btn btn-danger" name="cadastrar" id="cadastrar" value="Concluir" />
                        </div>
                    </div>
                </form>
            </div>
        <?php include_once '../../template/footer.php'; ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.price_format.2.0.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../js/jquery.autocomplete.js" type="text/javascript"></script>
    </body>
</html>
