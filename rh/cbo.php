<?php
// Verificando se o usuário está logado
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="../../login.php">Logar</a>';
	exit;
}

// Incluindo Arquivos
require('../conn.php');
include('../classes/abreviacao.php');
include('../classes/formato_valor.php');
include('../classes/formato_data.php');
include('../wfunction.php');

$usuario = carregaUsuario();

// Consulta da Região
$regiao     = $usuario['id_regiao'];
$qr_regiao  = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

// Consulta dos Cursos de CLT
$qr_cbo_clt    = mysql_query("SELECT * FROM curso WHERE id_regiao = '$regiao' AND tipo = '2' AND status = '1' AND status_reg = '1' ORDER BY nome ASC");
$total_cbo_clt = mysql_num_rows($qr_cbo_clt);

// Consulta dos Cursos de Cooperado
$qr_cbo_cooperado    = mysql_query("SELECT * FROM curso WHERE id_regiao = '$regiao' AND tipo = '3' AND status = '1' AND status_reg = '1' ORDER BY nome ASC");
$total_cbo_cooperado = mysql_num_rows($qr_cbo_cooperado);

// Consulta do Curso
$qr_curso  = mysql_query("SELECT * FROM curso WHERE id_curso = '$_GET[id]' AND status = '1' AND status_reg = '1'");
$row_curso = mysql_fetch_assoc($qr_curso);

// Update do Curso
if(isset($_POST['id_cbo'])) {

	$qr_cbo  = mysql_query("SELECT * FROM rh_cbo WHERE id_cbo = '$_POST[id_cbo]'");
	$row_cbo = mysql_fetch_assoc($qr_cbo);
	
	mysql_query("UPDATE curso SET cbo_nome = '$row_cbo[nome]', cbo_codigo = '$row_cbo[id_cbo]' WHERE id_curso = '$_POST[id_curso]' LIMIT 1") or die (mysql_error()); ?>
    
	<script type="text/javascript">
	parent.window.location.reload();
	</script>
    
<?php } ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: Intranet :: CBO</title>
<link href="../favicon.ico" rel="shortcut icon">
<link href="folha/sintetica/folha.css" rel="stylesheet" type="text/css">
<link href="../js/highslide.css" type="text/css" rel="stylesheet">
<script src="../js/ajax_cbo.js" type="text/javascript"></script>
<script src="../js/ramon.js" type="text/javascript"></script>
<script src="../js/highslide-with-html.js" type="text/javascript"></script>
<script type="text/javascript">
	hs.graphicsDir = '../images-box/graphics/'; 
	hs.outlineType = 'rounded-white';
</script>
<style type="text/css">
	li {list-style:none !important;}
	.highslide-html-content { width:600px; padding:0px; }
	#ajax{ visibility:hidden; border:2px solid #CCC; width:300px; position:absolute; background:#FFF; font:8pt Tahoma, "Trebuchet MS", Arial; padding-bottom:35px; top:164px; left:192px; }
	#ajax h3{font:bold 10pt "Trebuchet MS", Arial;margin:5px 10px 0}
	#ajax small{margin:0 10px;position:relative;top:-3px;color:#666;display:block}
	#ajax li a{display:block;padding:5px 4px 4px 22px;color:#000;text-decoration:none;background:#fff url('/img/topic_default.gif') 2px 2px no-repeat}
	#ajax a:hover{color:#333333;text-decoration:none;background-color:#F5F5F5}
	#ajax ul{margin:0 5px;padding:0;list-style:none}
	#ajax #info{position:absolute;bottom:0;background:#ffe;padding:5px;text-align:center;font-size:7.5pt;border-top:1px solid #fc0;width:290px;*width:296px;}
</style>
</head>
<body>
<div id="corpo">
	<div style="float:right; margin-right:10px;"> <?php include('../reportar_erro.php'); ?></div>
    <div style="clear:right"></div>
    <table cellspacing="4" cellpadding="0" id="topo">
    
    
      <tr>
        <td width="18%" rowspan="2" valign="middle" align="center">
          <img src="imagensrh/logo-cbo.gif">
        </td>
        <?php if(!isset($_GET['id'])) { ?>
        <td width="82%"><b>Região:</b> <?php echo $regiao.' - '.$row_regiao['regiao']; ?></td>
      </tr>
      <tr>
        <td><b>Total de Atividades:</b> <?php echo ($total_cbo_clt + $total_cbo_cooperado); ?></td>
        <?php } else { ?>
        <td width="82%"><?php echo $row_curso['id_curso'].' - '.$row_curso['nome']; ?></td>
      </tr>
      <tr>
        <td><b>CBO:</b> <?php echo $row_curso['cbo_codigo'].' - '.$row_curso['cbo_nome']; ?></td>
        <?php } ?>
      </tr>
    </table>

   <?php if(!isset($_GET['id'])) { ?>
     
    <table cellpadding="0" cellspacing="0" id="folha">
      <tr>
        <td colspan="2">
          <a href="../principalrh.php?regiao=<?php echo $regiao; ?>" class="voltar">Voltar</a>
        </td>
      </tr>
      <tr class="secao">
        <td width="50%">Atividades de CLT (<?php echo $total_cbo_clt; ?>)</td>
        <td width="50%">Atividades de Cooperado (<?php echo $total_cbo_cooperado; ?>)</td>
      </tr>
	  <tr>
        <td valign="top">
        
            <table cellpadding="0" cellspacing="1" id="folha" style="width:100%;">
              <?php while($row_cbo_clt = mysql_fetch_assoc($qr_cbo_clt)) { ?>
                <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?> destaque">
                  <td><a href="cbo.php?id=<?php echo $row_cbo_clt['id_curso']; ?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )" class="participante" title="Editar CBO desta atividade"><?php echo $row_cbo_clt['nome']; ?></a></td>
                </tr>
              <?php } ?>
            </table>
        
        </td>
        <td valign="top">
        
            <table cellpadding="0" cellspacing="1" id="folha" style="width:100%;">
              <?php while($row_cbo_cooperado = mysql_fetch_assoc($qr_cbo_cooperado)) { ?>
                <tr class="linha_<?php if($linha2++%2==0) { echo 'um'; } else { echo 'dois'; } ?> destaque">
                  <td><a href="cbo.php?id=<?php echo $row_cbo_cooperado['id_curso']; ?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )" class="participante" title="Editar CBO desta atividade"><?php echo $row_cbo_cooperado['nome']; ?></a></td>
                </tr>
              <?php } ?>
            </table>
        
        </td>
	  </tr>
      <tr>
        <td colspan="2"><a href="#corpo" class="ancora">Subir ao topo</a></td>
      </tr>
    </table>
    
    <?php } else { ?>
    
    <table cellpadding="4" cellspacing="0" id="folha" style="line-height:22px;">
      <tr style="background-color:#ddd; font-weight:bold;">
        <td colspan="2">Alterar CBO</td>
	  </tr>
      <tr>
        <td>
          <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
              Digite o nome da profiss&atilde;o no campo ao <br>
            lado e selecione uma das op&ccedil;&otilde;es abaixo:
<input type="text" name="pesquisa_usuario" onKeyUp="searchSuggest();" size="30" id="pesquisa_usuario" autocomplete="off">
            <input type="submit" value="Concluir" style="display:block; width:65px; height:25px; padding:3px; background-color:#eee; font-size:11px; border:1px solid #eee; cursor:pointer; float:right;">
         	  <input type="hidden" name="id_cbo" id="id_cbo" maxlength="6">
              <input type="hidden" name="id_curso" id="id_curso" value="<?php echo $_GET['id']; ?>">
          </form>
        </td>
        <td><div id="ajax"></div></td>
	  </tr>
    </table>
    
    <?php } ?>
</div>
</body>
</html>