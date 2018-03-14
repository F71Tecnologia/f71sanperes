<?php

include('../../conn.php');
include('../../funcoes.php');


if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'renovar') {
    $id_oscip = $_REQUEST['id'];
    $id_master = $_REQUEST['id_master'];
    $qr_oscip = mysql_query("SELECT * FROM obrigacoes_oscip WHERE id_oscip='$id_oscip'");
    $row_oscip = mysql_fetch_assoc($qr_oscip);
}

if(isset($_REQUEST['renovarObrigacoes']) && $_REQUEST['renovarObrigacoes'] == 'Renovar') {
    
    $data_publicacao = implode('-', array_reverse(explode('/', $_REQUEST['data_publicacao'])));
    $numero_periodo = $_REQUEST['numero_periodo'];
    $periodo = $_REQUEST['periodo'];
    $inicio = implode('-', array_reverse(explode('/', $_REQUEST['inicio'])));
    $termino = implode('-', array_reverse(explode('/', $_REQUEST['termino'])));
    $usuario = $_COOKIE['logado'];
    $id_oscip = $_REQUEST['id_oscip'];
    $id_master = $_REQUEST['id_master'];

    if ($periodo == 'Indeterminado') {
        $numero_periodo = NULL;
        $inicio = NULL;
        $termino = NULL;
    } else if ($periodo == 'Período') {
        $numero_periodo = NULL;
    } else if ($periodo == 'Dias' or $periodo == 'Meses' or $periodo == 'Anos') {
        $inicio = NULL;
        $termino = NULL;
    }
    
//    var_dump($link_master); exit;
    $qr = mysql_query("INSERT INTO obrigacoes_oscip (tipo_oscip, numero_oscip, descricao, data_publicacao, numero_periodo, periodo, usuario, data_usuario, status, id_master,id_projeto,oscip_data_inicio,oscip_data_termino, oscip_endereco, resp_env_rec)
          SELECT tipo_oscip, numero_oscip, descricao,'$data_publicacao' AS data_publicacao, '$numero_periodo' AS numero_periodo, '$periodo' AS periodo, '$usuario' AS usuario, NOW() AS data_usuario, status, id_master,id_projeto,'$inicio' AS oscip_data_inicio, '$termino' AS oscip_data_termino,oscip_endereco, resp_env_rec FROM obrigacoes_oscip  WHERE id_oscip = '$id_oscip' LIMIT 1;") or die ("Erro ao duplicar o contrato");
    $ultimo_id = mysql_insert_id();

    $nome_funcionario = mysql_result(mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$usuario'"),0);	
    registrar_log('ADMINISTRAÇÃO - RENOVAÇÃO DE OBRIGAÇÕES DA EMPRESA', $nome_funcionario.' cadastrou a obrigação: '.'('.$ultimo_id.') - '.$nome_tipo);	
    
    header("Location: cadastro_oscip2.php?r=renovar&id=$ultimo_id&master=$id_master");
}
?>

<html>
    <head>
        <title>:: Intranet :: RENOVAR OSCIP</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link rel="shortcut icon" href="../favicon.ico">
        <link href="../../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
        <!--<script type="text/javascript" src="../../../js/ramon.js"></script>-->
        <!--<script type="text/javascript" src="../../jquery-1.3.2.js"></script>-->
        <script type="text/javascript" src="../../js/jquery.validationEngine-pt_BR-2.6.js" ></script>
        <script type="text/javascript" src="../../js/jquery.validationEngine-2.6.js" ></script>
        <link href="../../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <!--<script type="text/javascript" src="../../jquery/priceFormat.js" ></script>-->
        <!--<script type="text/javascript" src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.min.js" ></script>-->
        <!--<script type="text/javascript" src="../../uploadfy/scripts/swfobject.js" ></script>-->
        <!--<link href="../../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css">-->

        <!--<link href="../../js/highslide.css" rel="stylesheet" type="text/css"  />--> 
        <!--<script type="text/javascript" src="../../js/highslide-with-html.js"></script>--> 

        <script>
            hs.graphicsDir = '../../images-box/graphics/';
            hs.outlineType = 'rounded-white';

            $(function() {
                $("#form1").validationEngine();
                $('#data_publicacao').mask('99/99/9999');
                $('#inicio').mask('99/99/9999');
                $('#termino').mask('99/99/9999');
                $('#validade').css("display", "none");

                $('#periodo').change(function() {
                    if ($(this).val() === 'Indeterminado') {
                        $('#validade').fadeOut();
                        $('#periodo_data').fadeOut();
                    } else if ($(this).val() === 'Período') {
                        $('#periodo_data').fadeIn();
                        $('#validade').hide();
                    } else if ($(this).val() === 'Dias' || $(this).val() === 'Meses' || $(this).val() === 'Anos') {
                        $('#validade').fadeIn();
                        $('#periodo_data').hide();
                    } else {
                        $('#validade').fadeOut();
                        $('#periodo_data').fadeOut();
                    }

                    if ($(this).val() == 'Dias') {
                        $('#mensagem').show();
                        $('#mensagem').empty();
                        $('#mensagem').append(' <i>Digite o número de dias </i>');
                    } else if ($(this).val() == 'Meses') {
                        $('#mensagem').show();
                        $('#mensagem').empty();
                        $('#mensagem').append(' <i>Digite o número de meses </i>');
                    } else if ($(this).val() == 'Anos') {
                        $('#mensagem').show();
                        $('#mensagem').empty();
                        $('#mensagem').append(' <i>Digite o número de anos </i>');
                    }
                });
            });

        </script>
    </head>
    <body>
        <div id="corpo">
            <form action="renovar_oscip.php" method="post" name="form1" id="form1" class="secao" >
                <table cellpadding="0" cellspacing="1" align="center" >
                    <tr class="secao_pai">
                        <td colspan="4" class="secao_pai">Tipo: <?php echo $row_oscip['tipo_oscip']; ?></td>
                    </tr>
                    <tr>
                        <td align="center" class="first">N&ordm; do documento:</td>
                        <td colspan="2" ><input type="text" readonly="readonly" name="numero" id="numero" value="<?= $row_oscip['numero_oscip'] ?>"/></td>
                    </tr>
                    <tr>
                        <td align="center" class="first">Descri&ccedil;&atilde;o:</td>
                        <td colspan="2" ><textarea name="descricao" readonly="readonly" id="descricao" cols="45" rows="5"><?= $row_oscip['descricao'] ?></textarea></td>
                    </tr>
                    <tr>
                        <td align="center" valign="top" class="first">Data da publica&ccedil;&atilde;o:</td>
                        <td colspan="2" valign="top" ><input type="text" name="data_publicacao" id="data_publicacao" value="" class="validate[required,custom[dateBr]]" /></td>
                    </tr>
                    <tr>
                        <td align="center" class="first">Período:</td> 
                        <td colspan="2">
                            <select name="periodo" id="periodo" class="validate[required]" >
                                <option value="">Selecione um per&iacute;odo..</option>
                                <option value="Dias">Dias</option>
                                <option value="Meses">Meses </option>
                                <option value="Anos">Anos</option>
                                <option value="Período">Período</option>
                                <option value="Indeterminado">Indeterminado</option>
                            </select>
                        </td>
                    </tr>
                    <tr id="validade" style="display:none;">
                        <td align="center" class="first">Validade:</td>
                        <td colspan="2"><label id="mensagem"></label><input type="text" name="numero_periodo" id="numero_periodo"  value="" class="validate[required]"/></td>
                    </tr> 
                    <tr id="periodo_data" style="display:none;">
                        <td align="center" class="first">Data de início:</td>
                        <td><input name="inicio" type="text" id="inicio"  value="" class="validate[required,custom[dateBr]]"/></td>
                        <td align="center">Data de termino:</td>
                        <td><input name="termino" type="text" id="termino" value="" class="validate[required,custom[dateBr]]"/></td>
                    </tr>
                </table>
                <br>
                <p class="controls">
                    <input type="submit" name="renovarObrigacoes" id="renovarObrigacoes" value="Renovar" />
                    <input type="hidden" name="id_master" id="id_master" value="<?= $id_master; ?>" />
                    <input type="hidden" name="id_oscip" id="id_oscip" value="<?= $row_oscip['id_oscip']; ?>" />
                </p>

            </form>
        </div>
    </body>
</html>