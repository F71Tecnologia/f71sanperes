<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$ACOES = new Acoes();

$id_regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];

//$projeto = $_REQUEST['pro'];
//$regiao  = $_REQUEST['regiao'];

$sql = "select t.id_terceirizado, t.nome from terceirizado t where t.id_projeto = '$projeto' and t.id_regiao = '$regiao';";
$result = mysql_query($sql);
?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../rh/css/estrutura_projeto.css" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="../favicon.ico">
</head>
<body>
<div id="corpo" style="width:95%;">

<div>
    <form action="listaterceiro_horario.php" method="post" id="frm_list" name="frm_list">
	<input type="hidden" name="exec" value="S" />
	<input type="hidden" name="pro" value="<?=$projeto?>" />
	<input type="hidden" name="regiao" value="<?=$regiao?>" /><br/>
	Selecione o colaborador: 
	<select name="terceiros" id="terceiros">
		<?php while ($lin = mysql_fetch_array($result)): ?>
			<option <?=($_REQUEST['terceiros']===$lin['id_terceirizado']?'selected':'')?> value="<?=$lin['id_terceirizado']?>"><?=$lin['nome']?></option>
		<? endwhile;?>	    
	</select>
	<input type="submit" value="Consultar" />
    </form>
</div>
   
<?php if($_REQUEST['exec'] == 'S'):
    
    $sql = "select 
		p.id_terceirizado, t.nome, c.nome as curso, 
		p.data, min(hora) as entrada, max(hora) as saida, timediff(max(hora), min(hora)) as total,
		min(p.imagem) as img1, max(p.imagem) as img2, (c.valor/160) as valor_hora, c.valor
	from 
		terceirizado t inner join 
		terceiro_ponto p on t.id_terceirizado = p.id_terceirizado inner join 
		curso c on c.id_curso = t.id_curso
 	where 
		t.id_terceirizado = {$_REQUEST['terceiros']}
 	group 
		by p.id_terceirizado, t.nome, c.nome, p.data
 	order 
		by p.data desc;";
	
    $result = mysql_query($sql);
?>
    
    <div id="conteudo">
    <table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
    <tr>
	    <td align="right"><?php include('reportar_erro.php');?></td>
      </tr>
      <tr>
	<td>
	  <div style="border-bottom:2px solid #F3F3F3; margin-top:10px;">
	       <h2 style="float:left; font-size:18px;">VISUALIZAÇÃO DE PONTO DE TERCEIRO</h2>
	       <p style="float:right;"><a href="ver.php?projeto=<?=$projeto?>&regiao=<?=$regiao?>">&laquo; Voltar</a></p>
	       <div class="clear"></div>
	  </div>
	</td>
      </tr>
      <tr>
	<td>

    <table cellpadding="8" cellspacing="0" style="border:0px; background-color:#f5f5f5; margin:0px auto; margin-top:1px; width:100%;">
	<tr>
	    <td class="show" colspan="9">
		<span style="color:#F90; font-size:32px;">></span>
		TERCEIRIZADOS
	    </td>
	</tr>
       <tr class="novo_tr">
	   <td>NOME</td>
	   <td>FUNÇÃO</td>
	   <td style="text-align: center;">DATA</td>
	   <td style="text-align: center;">FOTO REGISTRO INICIAL</td>
	   <td style="text-align: center;">REGISTRO INICIAL</td>
	   <td style="text-align: center;">FOTO REGISTRO FINAL</td>
	   <td style="text-align: center;">REGISTRO FINAL</td>
	   <td style="text-align: center;">TOTAL DE REGISTROS. (HORAS)</td>
	   <td style="text-align: center;">VALOR HORA</td>
	   <td style="text-align: center;">VALOR ESTIMADO</td>
       </tr>

       <?php while ($row = mysql_fetch_array($result)): ?>

	<tr class="linha_<?php echo ($alternateColor++%2==0)?"um":"dois"; ?>" style="font-size:12px;">
	    <td><?=$row['nome']?></td>
	    <td><?=$row['curso']?></td>
	    <td style="text-align: center;"><?=date("d/m/Y", strtotime($row['data']))?></td>
	    <td style="text-align: center;"><a href="../fotos/<?=$row['img1']?>" target="_blank"><img src="./img/cam.png"/></a></td>
	    <td style="text-align: center;"><?=$row['entrada']?></td>
	    <td style="text-align: center;"><a href="../fotos/<?=$row['img2']?>" target="_blank"><img src="./img/cam.png"/></a></td>
	    <td style="text-align: center;"><?=$row['saida']?></td>
	    <td style="text-align: center;"><?=$row['total']?></td>
	    <td style="text-align: center;"><?=$row['valor_hora']?></td>
	    <td style="text-align: center;"><?=$row['valor']?></td>
        </tr>

	<?php endwhile; ?>

    </table>

    <div style="width:95%; margin:0px auto; font-size:13px; padding-bottom:4px; margin-top:15px; text-align:right;">
	<a href="#corpo" title="Subir navegação">Subir ao topo</a>
    </div>
    </td>
    </tr>
    </table>
    </div>
<?php endif;?>
</div>
</body>
</html>