<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../funcoes.php');
//include('../../adm/include/criptografia.php');
include('../../classes_permissoes/projeto.class.php');
include("../../wfunction.php");

$usuario = carregaUsuario();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"2", "area"=>"Administrativo", "id_form"=>"form1", "ativo"=>"Obrigações");
$breadcrumb_pages = array("Principal" => "../../admin/index.php");

$PROJETO = new Projeto();
$usuario = mysql_fetch_assoc(mysql_query("SELECT * FROM funcionario WHERE id_funcionario = {$_COOKIE['logado']} LIMIT 1;"));

$sqlRespostaEnv = "SELECT id_oscip, numero_oscip FROM obrigacoes_oscip WHERE  status='1' AND tipo_oscip = 'Ofícios Enviados' AND id_master='{$usuario['id_master']}' GROUP BY descricao ORDER BY tipo_oscip DESC;";
$sqlRespostaEnv = mysql_query($sqlRespostaEnv);

$respostaEnv = '<select name="respostaEnv" id="respostaEnv" style="width: 164px; display: none;">';
$respostaEnv .= '<option value="">Selecione</option>';
while ($rowRespostaEnv = mysql_fetch_assoc($sqlRespostaEnv)) {
    $respostaEnv .= '<option value="' . $rowRespostaEnv['id_oscip'] . '">' . $rowRespostaEnv['numero_oscip'] . '</option>';
}
$respostaEnv .= '</select>';

$sqlRespostaRec = "SELECT id_oscip, numero_oscip FROM obrigacoes_oscip WHERE  status='1' AND tipo_oscip = 'Ofícios Recebidos' AND id_master='{$usuario['id_master']}' GROUP BY descricao ORDER BY tipo_oscip DESC;";
$sqlRespostaRec = mysql_query($sqlRespostaRec);

$respostaRec = '<select name="respostaRec" id="respostaRec" style="width: 164px; display: none;">';
$respostaRec .= '<option value="">Selecione</option>';
while ($rowRespostaRec = mysql_fetch_assoc($sqlRespostaRec)) {
    $respostaRec .= '<option value="' . $rowRespostaRec['id_oscip'] . '">' . $rowRespostaRec['numero_oscip'] . '</option>';
}
$respostaRec .= '</select>';

if (isset($_POST['enviar'])) {
    $tipo = $_POST['tipo'];
    $numero = trim($_POST['numero']);
    $descricao = trim(mysql_real_escape_string($_POST['descricao']));
    $data_publicacao = implode('-', array_reverse(explode('/', $_POST['data_publicacao'])));
    $numero_periodo = trim($_POST['numero_periodo']);
    $periodo = $_POST['periodo'];
    $usuario = $_COOKIE['logado'];
    $id_projeto = $_POST['projeto'];
    $inicio = implode('-', array_reverse(explode('/', $_POST['inicio'])));
    $termino = implode('-', array_reverse(explode('/', $_POST['termino'])));
    $endereco = trim($_POST['endereco']);
    $respostaEnviada = trim($_POST['respostaEnv']);
    $respostaRecebida = trim($_POST['respostaRec']);

    if ($respostaEnviada != "") {
        $resposta = $respostaEnviada;
    } else if ($respostaRecebida != "") {
        $resposta = $respostaRecebida;
    }

    if ($periodo == 'Indeterminado') {
        $numero_periodo = null;
    }


    $qr_inserir = mysql_query("INSERT INTO obrigacoes_oscip (id_oscip,tipo_oscip,numero_oscip,descricao,data_publicacao,numero_periodo,periodo,usuario,data_usuario,status,id_master,id_projeto,oscip_data_inicio,oscip_data_termino,oscip_endereco,resp_env_rec)  
                                VALUES
                                ('','$tipo','$numero','$descricao','$data_publicacao','$numero_periodo','$periodo','$usuario',NOW(),'1','$Master','$id_projeto','$inicio','$termino','$endereco','$resposta')") or die("erro");


    $ultimo_id = (int) @mysql_insert_id();

    $nome_tipo = mysql_result(mysql_query("SELECT tipo_nome FROM tipo_doc_oscip WHERE tipo_nome = '$tipo'"), 0) or die(mysql_error());


    $nome_funcionario = mysql_result(mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'"), 0);
    registrar_log('ADMINISTRAÇÃO - CADASTRO DE OBRIGAÇÕES DA EMPRESA', $nome_funcionario . ' cadastrou a obrigação: ' . '(' . $ultimo_id . ') - ' . $nome_tipo);

    header("Location: cadastro_oscip2.php?m=$link_master&id=$ultimo_id");
}
?>

<html>
    <head>
        <title>:: Intranet :: Cadastro de obrigações da Instituição</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link rel="shortcut icon" href="../favicon.ico">
        <script type="text/javascript" src="../../../js/ramon.js"></script>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="http://code.jquery.com/jquery-migrate-1.2.1.js"></script>
        <script type="application/javascript" src="../../jquery/validationEngine/jquery.validationEngine-pt.js" ></script>
        <script type="application/javascript" src="../../jquery/validationEngine/jquery.validationEngine.js" ></script>
        <link href="../../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css">

        <script type="application/javascript" src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>
        <script type="application/javascript" src="../../jquery/priceFormat.js" ></script>

        <script type="application/javascript" src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.min.js" ></script>
        <script type="application/javascript" src="../../uploadfy/scripts/swfobject.js" ></script>
        <link href="../../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css">
        <link href="../../js/highslide.css" rel="stylesheet" type="text/css"  /> 
        <script type="text/javascript" src="../../js/highslide-with-html.js"></script>
		<!-- Bootstrap -->
	<script src="../../resources/js/bootstrap.min.js"></script>
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">

        <script type="text/javascript">
            hs.graphicsDir = '../../images-box/graphics/';
            hs.outlineType = 'rounded-white';

            $(function () {

                $("#anexo_publicacao").uploadify({
                    'uploader': '../../uploadfy/scripts/uploadify.swf',
                    'script': 'upload.php',
                    'buttonText': 'Enviar',
                    'queueID': 'barra_processo',
                    'cancelImg': '../../uploadfy/cancel.png',
                    'auto': true,
                    'method': 'post',
                    'multi': true,
                    'fileDesc': 'gif jpg pdf PDF',
                    'fileExt': '*.gif;*.jpg;*.pdf;*.PDF;',
                    'onComplete': function (event, ID, fileObj, response, data) {
                        eval('var resposta = ' + response);
                        console.log(resposta);
                        if (resposta.erro) {
                            alert('Ocorreu um erro no envio do arquivo');
                        } else {
                            $("#anexo_publicacao").hide();

                            $('#img_publicacao').html('<img src="' + resposta.img + '" width="200" height="250" />');

                        }
                    }, 'onError': function (event, ID, fileObj, errorObj) {
                        alert(errorObj.type + ' Error: ' + errorObj.info);
                    }
                });


                $("#anexo").uploadify({
                    'uploader': '../../uploadfy/scripts/uploadify.swf',
                    'script': 'upload_2.php',
                    'buttonText': 'Enviar',
                    'queueID': 'barra_processo2',
                    'cancelImg': '../../uploadfy/cancel.png',
                    'auto': true,
                    'method': 'post',
                    'multi': true,
                    'fileDesc': 'gif jpg pdf PDF',
                    'fileExt': '*.gif;*.jpg;*.pdf;*.PDF;',
                    'onComplete': function (event, ID, fileObj, response, data) {
                        eval('var resposta = ' + response);
                        if (resposta.erro) {
                            alert('Ocorreu um erro no envio do arquivo');
                        } else {
                            $("#anexo").hide();

                            $('#img_anexo').html('<img src="' + resposta.img + '" width="200" height="250" />');

                        }
                    }, 'onError': function (event, ID, fileObj, errorObj) {
                        alert(errorObj.type + ' Error: ' + errorObj.info);
                    }
                });

                $("#form1").validationEngine();
                $('#data_publicacao').mask('99/99/9999');
                $('#inicio').mask('99/99/9999');
                $('#termino').mask('99/99/9999');


                $('.anexo').change(function () {


                    if ($(this).attr('checked') == true)
                    {
                        $('#projeto').fadeIn();

                    }

                });


                $('.alvara').change(function () {
                    if ($(this).val() == 'Alvará de Funcionamento')
                    {
                        $('#projeto').hide();
                        $('#endereco').fadeIn();
                    }


                });


                $('.tipo').change(function () {
                    if (($(this).val() != 'Publicação Anexo 1 em Jornal'))
                    {
                        $('#projeto').fadeOut();

                    }

                    if (($(this).val() != 'Alvará de Funcionamento'))
                    {
                        $('#endereco').fadeOut();

                    }



                });

                $('#periodo').change(function () {

                    if ($(this).val() == 'Indeterminado') {

                        $('#validade').fadeOut();
                        $('#periodo_data').fadeOut();

                    }
                    else
                    if ($(this).val() == 'Período')
                    {
                        $('#periodo_data').fadeIn();
                        $('#validade').hide();

                    } else {
                        $('#validade').fadeIn();
                        $('#periodo_data').hide();
                    }

                    if ($(this).val() == 'Dias') {

                        $('#menssagem').empty();
                        $('#menssagem').append(' <i>Digite o número de dias</i>');
                    }
                    else
                    if ($(this).val() == 'Meses') {
                        $('#menssagem').empty();
                        $('#menssagem').append(' <i>Digite o número de meses</i>');
                    }
                    else
                    if ($(this).val() == 'Anos') {
                        $('#menssagem').empty();
                        $('#menssagem').append(' <i>Digite o número de anos</i>');
                    }


                });

                $("input[type=radio]").click(function () {
                    var tipo = $(this).val();
                    if (tipo == 'Ofícios Enviados') {
                        $('#linhaResposta,#respostaRec').show();
                        $('#respostaEnv').hide();
                        $('#respostaEnv').val("");
                    } else if (tipo == 'Ofícios Recebidos') {
                        $('#linhaResposta,#respostaEnv').show();
                        $('#respostaRec').hide();
                        $('#respostaRec').val("");
                    } else {
                        $('#linhaResposta,#respostaEnv,#respostaRec').hide();
                        $('#respostaRec,#respostaEnv').val("");
                    }
                });
            });
        </script>
    </head>
    <body>
    	<?php include("../../template/navbar_default.php"); ?>
        <div class="container">
        	<div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-admin-header"><h2><span class="glyphicon glyphicon-cog"></span> - ADMINISTRATIVO<small> - Cadastrar obrigações da empresa</small></h2></div>
                </div>
            </div>
            <div id="alert" style="background-color:#F30;color:#FFF;font-weight:bold; padding-left:3px;"></div>
            <form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF']; ?>?m=<?= $link_master ?>" method="post" name="form1" id="form1" enctype="multipart/form-data" >
                <div class="panel panel-default">
                    <div class="panel-body">
                	<div class="form-group">
	                    <label class="control-label col-sm-2" for="tipo">Tipo</label>
	                    <div class="col-sm-8">
	                    	<select name="tipo" id="tipo" class="form-control">
	                    		<option value="">Selecione uma opção</option>
		                        <?php
		                            $qr_tipo_oscip = mysql_query("SELECT * FROM tipo_doc_oscip WHERE 1 ORDER BY tipo_nome");
		                            while($row_tipo = mysql_fetch_assoc($qr_tipo_oscip)):
		                                switch ($row_tipo['tipo_id']) {

		                                    case 10: $checked = ($_GET['tp'] == 'Publicação Anexo 1 em Jornal') ? 'checked="checked"' : '';
		                                        $classe = 'class="anexo" ' . $checked;
		                                        break;

		                                    case 19: $classe = 'class="anexo" ' . $checked;
		                                        break;

		                                    case 12: $classe = 'class="alvara"';
		                                        break;

		                                    default: $classe = 'class="tipo"';
		                                        break;
		                                }
		                                ?>
										<option <?php echo $classe; ?> value="<?php echo $row_tipo['tipo_nome']; ?>"><?php echo $row_tipo['tipo_nome']; ?></option>
		                        <?php endwhile; ?>
                            </select>
	                    </div>
	                </div>
	               	<div class="form-group">
	                	<label class="control-label col-sm-2" for="numero">N&ordm; do documento:</label>
	                	<div class="col-sm-8">
	                		<input type="text" name="numero" id="numero" class="validate[required]">
	                	</div>
	                </div>
	                <div class="form-group" id="projeto" style="display:none;">
	                	<label class="control-label col-sm-2">Projeto:</label>
	                	<div class="col-sm-8">
	                		<select name="projeto">
                                <option value="">Selecione um projeto...</option>
                                <?php $PROJETO->Preenhe_select_por_master($Master); ?>
                            </select>
	                	</div>
	                </div>
                    <div class="form-group" id="endereco" style="display:none;">
                        <label class="control-label col-sm-2" for="campo-endereco">Endereço:</label>
                        <div class="col-sm-8">
                            <input name="endereco" type="text" id="campo-endereco" size="50"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="descricao">Descri&ccedil;&atilde;o:</label>
                        <div class="col-sm-8">
                            <textarea name="descricao" id="descricao" cols="45" rows="5"></textarea></td>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="data_publicacao">Data da publica&ccedil;&atilde;o:</label>
                        <div class="col-sm-8">
                            <input type="text" name="data_publicacao" id="data_publicacao" class="validate[required]">
                            <div id="barra_processo"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="data_publicacao">Período:</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="periodo" id="periodo" class="validate[required]">
								<option value="">Selecione um per&iacute;odo..</option>
								<option value="Dias">Dias</option>
								<option value="Meses">Meses</option>
								<option value="Anos" >Anos</option>
								<option value="Indeterminado" >Indeterminado</option>
								<option value="Período" >Período</option>
							</select>
                        </div>
                    </div>
                    <div class="form-group" id="linhaResposta" style="display:none;">
                    	<label class="control-label col-sm-2" for="data_publicacao">Resposta De:</label>
                    	<div class="col-sm-8">
                    		<?php echo $respostaEnv . $respostaRec; ?>
                    	</div>
                    </div>
                    <div class="form-group" id="validade" style="display:none;">
                    	<label class="control-label col-sm-2" for="numero_periodo">Validade:</label>
                    	<div class="col-sm-8">
                    		<input type="text" name="numero_periodo" id="numero_periodo">
                    		<div id="menssagem"></div>
                    	</div>
                    </div>
                    <div class="form-group" id="periodo_data" style="display:none;">
                    	<label class="control-label col-sm-2" for="inicio">Data de início:</label>
                    	<div class="col-sm-3">
                    		<input name="inicio" type="text" id="inicio">
                    	</div>
                    	<label class="control-label col-sm-2" for="termino">Data de termino:</label>
                    	<div class="col-sm-3">
                    		<input name="termino" type="text" id="termino">
                    	</div>
                    </div>
                    <div class="form-group">
                    	<label class="control-label col-sm-2"></label>
                    	<div class="col-sm-2">
		                    <input type="submit" name="enviar" id="enviar" class="btn btn-success" value="Enviar documento"/>
		                    <input type="hidden" name="usuario" value="<?= $id_user ?>" />
		                    <input type="hidden" name="master" value="<?= $Master ?>" />
		                    <input type="hidden" name="update" value="1" />
		                </div>
		                <div style="display:none;">Enviando...</div>
                    </div>
            	</div>
            </div>
        </form>
        <div style="border-bottom:2px solid #F3F3F3; margin:10px 0 18px 0;">
            <p style="float:right;">
                <?php $pagina = $_SERVER['PHP_SELF']; ?>
                    <span style="position:relative; margin-right:10px;"> <a href="../../box_suporte.php?&regiao=<?php echo $regiao; ?>&pagina=<?php echo $pagina; ?>" onClick="return hs.htmlExpand(this, {objectType: 'iframe'})" ><img src="../../imagens/suporte.gif"  width="55" height="55"/></a></span>	
            </p>
            <div class="clear"></div>
	</div>
            <center><div id="rodape"><?php include('include/rodape.php'); ?></div></center>
        </div>
    </body>
</html>
